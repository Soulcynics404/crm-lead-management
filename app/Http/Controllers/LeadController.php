<?php

/**
 * HK CRM Lead Management System
 *
 * @author    Harsshh (@Soulcynics404)
 * @github    https://github.com/Soulcynics404/crm-lead-management
 * @quote     "Breaking systems to make them secure."
 * @copyright 2026 Harsshh. All rights reserved.
 *
 * NOTICE: This code is proprietary. Do not copy, modify, or redistribute
 * without proper attribution to the original author.
 */

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    /**
     * Display a listing of leads with search & filter.
     */
    public function index(Request $request)
    {
        $query = Lead::where('user_id', Auth::id())->withCount('followUps');

        // Search by name, email, or mobile
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $leads = $query->latest()->paginate(15);

        // For AJAX requests, return JSON
        if ($request->ajax()) {
            return response()->json($leads);
        }

        return view('leads.index', compact('leads'));
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create()
    {
        return view('leads.create');
    }

    /**
     * Store a newly created lead.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'required|in:new,contacted,interested,follow_up,won,lost',
        ]);

        $validated['user_id'] = Auth::id();
        $lead = Lead::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'lead' => $lead], 201);
        }

        return redirect()->route('leads.index')->with('success', 'Lead created successfully!');
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        if ($lead->user_id !== Auth::id()) {
            abort(403);
        }
        $lead->load('followUps');
        return view('leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified lead.
     */
    public function edit(Lead $lead)
    {
        if ($lead->user_id !== Auth::id()) {
            abort(403);
        }
        return view('leads.edit', compact('lead'));
    }

    /**
     * Update the specified lead.
     */
    public function update(Request $request, Lead $lead)
    {
        if ($lead->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'required|in:new,contacted,interested,follow_up,won,lost',
        ]);

        $lead->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'lead' => $lead]);
        }

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully!');
    }

    /**
     * Remove the specified lead.
     */
    public function destroy(Request $request, Lead $lead)
    {
        if ($lead->user_id !== Auth::id()) {
            abort(403);
        }

        $lead->delete(); // Soft delete

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Lead deleted successfully']);
        }

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully!');
    }

    /**
     * Export leads as CSV file.
     */
    public function export(Request $request)
    {
        $query = Lead::where('user_id', Auth::id());

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        $leads = $query->latest()->get();

        $filename = 'leads_export_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');

            // CSV Header row
            fputcsv($file, ['Name', 'Mobile Number', 'Email', 'Source', 'Status', 'Created At']);

            // Data rows
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->name,
                    $lead->mobile_number,
                    $lead->email ?? '',
                    $lead->source ?? '',
                    $lead->status,
                    $lead->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import leads from CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        // Read the file
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Unable to read the file.');
        }

        // Get header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File is empty or invalid format.');
        }

        // Clean headers (trim whitespace, lowercase for matching)
        $header = array_map(function ($col) {
            return strtolower(trim(str_replace(["\xEF\xBB\xBF", '"'], '', $col)));
        }, $header);

        // Map CSV columns to database fields (flexible matching)
        $columnMap = [];
        $fieldMappings = [
            'name' => ['name', 'full name', 'lead name', 'customer name', 'contact name', 'first name'],
            'mobile_number' => ['mobile number', 'mobile', 'phone', 'phone number', 'contact number', 'mobile_number', 'tel', 'telephone'],
            'email' => ['email', 'email address', 'e-mail', 'mail'],
            'source' => ['source', 'lead source', 'channel', 'origin'],
            'status' => ['status', 'lead status', 'stage'],
        ];

        foreach ($fieldMappings as $dbField => $possibleHeaders) {
            foreach ($possibleHeaders as $possibleHeader) {
                $index = array_search($possibleHeader, $header);
                if ($index !== false) {
                    $columnMap[$dbField] = $index;
                    break;
                }
            }
        }

        // Must have at least name and mobile
        if (!isset($columnMap['name'])) {
            fclose($handle);
            return back()->with('error', 'CSV must have a "Name" column. Found columns: ' . implode(', ', $header));
        }
        if (!isset($columnMap['mobile_number'])) {
            fclose($handle);
            return back()->with('error', 'CSV must have a "Mobile Number" or "Phone" column. Found columns: ' . implode(', ', $header));
        }

        $validStatuses = ['new', 'contacted', 'interested', 'follow_up', 'won', 'lost'];
        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            $name = isset($columnMap['name']) ? trim($row[$columnMap['name']] ?? '') : '';
            $mobile = isset($columnMap['mobile_number']) ? trim($row[$columnMap['mobile_number']] ?? '') : '';

            // Skip rows without required fields
            if (empty($name) || empty($mobile)) {
                $skipped++;
                $errors[] = "Row {$rowNumber}: Missing name or mobile number";
                continue;
            }

            $email = isset($columnMap['email']) ? trim($row[$columnMap['email']] ?? '') : null;
            $source = isset($columnMap['source']) ? trim($row[$columnMap['source']] ?? '') : null;
            $status = isset($columnMap['status']) ? strtolower(trim($row[$columnMap['status']] ?? '')) : 'new';

            // Normalize status
            $status = str_replace(' ', '_', $status);
            if (!in_array($status, $validStatuses)) {
                $status = 'new';
            }

            // Validate email format
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = null;
            }

            try {
                Lead::create([
                    'user_id' => Auth::id(),
                    'name' => $name,
                    'mobile_number' => $mobile,
                    'email' => $email ?: null,
                    'source' => $source ?: null,
                    'status' => $status,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "{$imported} leads imported successfully!";
        if ($skipped > 0) {
            $message .= " ({$skipped} rows skipped)";
        }

        return back()->with('success', $message);
    }

    /**
     * Download a sample CSV template for import.
     */
    public function sampleCsv()
    {
        $filename = 'leads_import_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Mobile Number', 'Email', 'Source', 'Status']);
            fputcsv($file, ['John Doe', '9876543210', 'john@example.com', 'Website', 'new']);
            fputcsv($file, ['Jane Smith', '8765432109', 'jane@example.com', 'Referral', 'contacted']);
            fputcsv($file, ['Raj Kumar', '7654321098', '', 'Social Media', 'interested']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
