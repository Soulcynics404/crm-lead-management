@extends('layouts.app')

@section('title', $lead->name)
@section('page-title', 'Lead Details')

@section('header-actions')
    <a href="{{ route('leads.edit', $lead) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-pen"></i> Edit
    </a>
@endsection

@section('content')
    <!-- Lead Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="lead-detail-header">
                <div class="lead-avatar-large">
                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                </div>
                <div class="lead-info">
                    <h2>{{ $lead->name }}</h2>
                    <p>
                        <span class="badge badge-{{ str_replace('_', '-', $lead->status) }}">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span>
                        @if($lead->source) · {{ $lead->source }} @endif
                        · Added {{ $lead->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <label><i class="fas fa-phone" style="margin-right: 4px;"></i> Mobile Number</label>
                    <span>{{ $lead->mobile_number }}</span>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-envelope" style="margin-right: 4px;"></i> Email</label>
                    <span>{{ $lead->email ?: 'Not provided' }}</span>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-tag" style="margin-right: 4px;"></i> Source</label>
                    <span>{{ $lead->source ?: 'Not specified' }}</span>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-calendar" style="margin-right: 4px;"></i> Created</label>
                    <span>{{ $lead->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Follow-ups Section -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-tasks" style="margin-right: 8px; color: var(--status-follow-up);"></i>Follow-ups ({{ $lead->followUps->count() }})</h2>
            <button class="btn btn-primary btn-sm" onclick="openFollowUpModal()">
                <i class="fas fa-plus"></i> Add Follow-up
            </button>
        </div>
        <div class="card-body">
            @if($lead->followUps->count() > 0)
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="followUpTableBody">
                            @foreach($lead->followUps->sortByDesc('follow_up_date') as $fu)
                                <tr id="followup-row-{{ $fu->id }}">
                                    <td>{{ $fu->follow_up_date->format('d M Y') }}</td>
                                    <td>{{ $fu->follow_up_time ? \Carbon\Carbon::parse($fu->follow_up_time)->format('h:i A') : '-' }}</td>
                                    <td>{{ $fu->notes ?: '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $fu->status }}">{{ ucfirst($fu->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            @if($fu->status === 'pending')
                                                <button class="btn btn-success btn-sm" onclick="markCompleted({{ $fu->id }})" title="Mark Completed">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button class="action-btn delete" onclick="deleteFollowUp({{ $fu->id }})" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <p>No follow-ups yet. Click "Add Follow-up" to create one.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Follow-up Modal -->
    <div class="modal-overlay" id="followUpModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Add Follow-up</h3>
                <button class="modal-close" onclick="closeFollowUpModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="followUpForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="fu_date">Date *</label>
                            <input type="date" class="form-control" id="fu_date" name="follow_up_date" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="fu_time">Time</label>
                            <input type="time" class="form-control" id="fu_time" name="follow_up_time">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="fu_notes">Notes</label>
                        <textarea class="form-control" id="fu_notes" name="notes" rows="3" placeholder="Add follow-up notes..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="fu_status">Status</label>
                        <select class="form-control" id="fu_status" name="status">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeFollowUpModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveFollowUp()">
                    <i class="fas fa-save"></i> Save Follow-up
                </button>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <a href="{{ route('leads.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Leads
        </a>
    </div>
@endsection

@push('scripts')
<script>
    const leadId = {{ $lead->id }};

    function openFollowUpModal() {
        // Set default date to today
        document.getElementById('fu_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('followUpModal').classList.add('active');
    }

    function closeFollowUpModal() {
        document.getElementById('followUpModal').classList.remove('active');
        document.getElementById('followUpForm').reset();
    }

    function saveFollowUp() {
        const form = document.getElementById('followUpForm');
        const date = document.getElementById('fu_date').value;

        if (!date) {
            alert('Please select a date');
            return;
        }

        const data = {
            follow_up_date: date,
            follow_up_time: document.getElementById('fu_time').value || null,
            notes: document.getElementById('fu_notes').value,
            status: document.getElementById('fu_status').value,
        };

        fetch(`/leads/${leadId}/follow-ups`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(data),
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                closeFollowUpModal();
                window.location.reload();
            }
        })
        .catch(err => alert('Failed to save follow-up'));
    }

    function markCompleted(id) {
        fetch(`/follow-ups/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ status: 'completed' }),
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                window.location.reload();
            }
        })
        .catch(err => alert('Failed to update follow-up'));
    }

    function deleteFollowUp(id) {
        if (!confirm('Delete this follow-up?')) return;

        fetch(`/follow-ups/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                const row = document.getElementById(`followup-row-${id}`);
                row.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => row.remove(), 300);
            }
        })
        .catch(err => alert('Failed to delete follow-up'));
    }

    // Close modal on overlay click
    document.getElementById('followUpModal').addEventListener('click', function(e) {
        if (e.target === this) closeFollowUpModal();
    });
</script>
@endpush
