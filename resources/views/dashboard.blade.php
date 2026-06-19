@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Total Leads</span>
                <span class="stat-value">{{ $totalLeads }}</span>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Today's Follow-ups</span>
                <span class="stat-value">{{ $todayFollowUps }}</span>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Pending Follow-ups</span>
                <span class="stat-value">{{ $pendingFollowUps }}</span>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Won Leads</span>
                <span class="stat-value">{{ $statusCounts['won'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Lead Status Breakdown -->
    <div class="card mb-4">
        <div class="card-header">
            <h2><i class="fas fa-chart-bar" style="margin-right: 8px; color: var(--accent);"></i>Lead Status Overview</h2>
        </div>
        <div class="card-body">
            <div class="status-bar-chart">
                @php
                    $allStatuses = ['new', 'contacted', 'interested', 'follow_up', 'won', 'lost'];
                    $maxCount = max(array_values($statusCounts) ?: [1]);
                @endphp
                @foreach($allStatuses as $status)
                    @php $count = $statusCounts[$status] ?? 0; @endphp
                    <div class="status-bar-row">
                        <span class="status-bar-label">
                            <span class="badge badge-{{ str_replace('_', '-', $status) }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                        </span>
                        <div class="status-bar-track">
                            <div class="status-bar-fill badge-{{ str_replace('_', '-', $status) }}-bg" style="width: {{ $maxCount > 0 ? ($count / $maxCount * 100) : 0 }}%"></div>
                        </div>
                        <span class="status-bar-count">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Two Column Grid -->
    <div class="dashboard-grid">
        <!-- Today's Follow-ups -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-bell" style="margin-right: 8px; color: var(--status-follow-up);"></i>Today's Follow-ups</h2>
            </div>
            <div class="card-body">
                @if($todayFollowUpList->count() > 0)
                    @foreach($todayFollowUpList as $fu)
                        <div class="followup-item">
                            <span class="followup-date">
                                {{ $fu->follow_up_time ? \Carbon\Carbon::parse($fu->follow_up_time)->format('h:i A') : 'No time set' }}
                            </span>
                            <div class="followup-content">
                                <span class="followup-lead-name">{{ $fu->lead->name }}</span>
                                @if($fu->notes)
                                    <p class="followup-notes">{{ Str::limit($fu->notes, 60) }}</p>
                                @endif
                            </div>
                            <span class="badge badge-{{ $fu->status }}">{{ ucfirst($fu->status) }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-check"></i>
                        <p>No follow-ups scheduled for today</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Leads -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-user-clock" style="margin-right: 8px; color: var(--accent);"></i>Recent Leads</h2>
                <a href="{{ route('leads.index') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body">
                @if($recentLeads->count() > 0)
                    @foreach($recentLeads as $lead)
                        <div class="followup-item">
                            <div class="followup-content">
                                <span class="followup-lead-name">{{ $lead->name }}</span>
                                <p class="followup-notes">{{ $lead->mobile_number }} {{ $lead->email ? '· ' . $lead->email : '' }}</p>
                            </div>
                            <span class="badge badge-{{ str_replace('_', '-', $lead->status) }}">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-user-plus"></i>
                        <p>No leads yet. <a href="{{ route('leads.create') }}" style="color: var(--accent);">Add your first lead</a></p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Follow-ups -->
    @if($upcomingFollowUps->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h2><i class="fas fa-list-check" style="margin-right: 8px; color: var(--status-interested);"></i>Upcoming Pending Follow-ups</h2>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Lead</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Notes</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingFollowUps as $fu)
                                <tr>
                                    <td><strong>{{ $fu->lead->name }}</strong></td>
                                    <td>{{ $fu->follow_up_date->format('d M Y') }}</td>
                                    <td>{{ $fu->follow_up_time ? \Carbon\Carbon::parse($fu->follow_up_time)->format('h:i A') : '-' }}</td>
                                    <td>{{ Str::limit($fu->notes, 40) ?: '-' }}</td>
                                    <td><span class="badge badge-{{ $fu->status }}">{{ ucfirst($fu->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('styles')
<style>
    .status-bar-chart {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .status-bar-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .status-bar-label {
        width: 110px;
        flex-shrink: 0;
    }
    .status-bar-track {
        flex: 1;
        height: 8px;
        background: var(--bg);
        border-radius: 4px;
        overflow: hidden;
    }
    .status-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.6s ease;
        min-width: 2px;
    }
    .status-bar-count {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-primary);
        width: 32px;
        text-align: right;
    }
    .badge-new-bg { background: var(--status-new); }
    .badge-contacted-bg { background: var(--status-contacted); }
    .badge-interested-bg { background: var(--status-interested); }
    .badge-follow-up-bg { background: var(--status-follow-up); }
    .badge-won-bg { background: var(--status-won); }
    .badge-lost-bg { background: var(--status-lost); }
</style>
@endpush
