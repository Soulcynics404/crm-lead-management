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

use App\Models\FollowUp;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    /**
     * Display follow-ups for a lead.
     */
    public function index(Lead $lead)
    {
        if ($lead->user_id !== Auth::id()) {
            abort(403);
        }

        $followUps = $lead->followUps()->latest('follow_up_date')->get();

        return response()->json($followUps);
    }

    /**
     * Store a new follow-up for a lead.
     */
    public function store(Request $request, Lead $lead)
    {
        if ($lead->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'follow_up_date' => 'required|date',
            'follow_up_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
            'status' => 'in:pending,completed',
        ]);

        $followUp = $lead->followUps()->create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'follow_up' => $followUp], 201);
        }

        return back()->with('success', 'Follow-up added successfully!');
    }

    /**
     * Update the specified follow-up.
     */
    public function update(Request $request, FollowUp $followUp)
    {
        $lead = $followUp->lead;
        if ($lead->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'follow_up_date' => 'sometimes|date',
            'follow_up_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
            'status' => 'in:pending,completed',
        ]);

        $followUp->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'follow_up' => $followUp]);
        }

        return back()->with('success', 'Follow-up updated successfully!');
    }

    /**
     * Remove the specified follow-up.
     */
    public function destroy(Request $request, FollowUp $followUp)
    {
        $lead = $followUp->lead;
        if ($lead->user_id !== Auth::id()) {
            abort(403);
        }

        $followUp->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Follow-up deleted']);
        }

        return back()->with('success', 'Follow-up deleted successfully!');
    }
}
