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
use App\Models\FollowUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with stats.
     */
    public function index()
    {
        $userId = Auth::id();

        $totalLeads = Lead::where('user_id', $userId)->count();

        $todayFollowUps = FollowUp::whereHas('lead', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->whereDate('follow_up_date', Carbon::today())->count();

        $pendingFollowUps = FollowUp::whereHas('lead', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'pending')->count();

        // Lead status breakdown
        $statusCounts = Lead::where('user_id', $userId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent leads
        $recentLeads = Lead::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Today's follow-ups with lead details
        $todayFollowUpList = FollowUp::with('lead')
            ->whereHas('lead', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereDate('follow_up_date', Carbon::today())
            ->orderBy('follow_up_time')
            ->get();

        // Upcoming pending follow-ups
        $upcomingFollowUps = FollowUp::with('lead')
            ->whereHas('lead', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'pending')
            ->where('follow_up_date', '>=', Carbon::today())
            ->orderBy('follow_up_date')
            ->orderBy('follow_up_time')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalLeads',
            'todayFollowUps',
            'pendingFollowUps',
            'statusCounts',
            'recentLeads',
            'todayFollowUpList',
            'upcomingFollowUps'
        ));
    }

    /**
     * Dashboard stats API endpoint.
     */
    public function stats()
    {
        $userId = Auth::id();

        return response()->json([
            'total_leads' => Lead::where('user_id', $userId)->count(),
            'today_follow_ups' => FollowUp::whereHas('lead', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->whereDate('follow_up_date', Carbon::today())->count(),
            'pending_follow_ups' => FollowUp::whereHas('lead', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', 'pending')->count(),
            'status_counts' => Lead::where('user_id', $userId)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
        ]);
    }
}
