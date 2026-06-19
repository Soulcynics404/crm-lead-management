@extends('layouts.app')

@section('title', 'Leads')
@section('page-title', 'Lead Management')

@section('header-actions')
    <div class="header-btn-group">
        <button class="btn btn-secondary btn-sm" onclick="openImportModal()" title="Import Leads">
            <i class="fas fa-file-import"></i> Import
        </button>
        <a href="{{ route('leads.export', request()->only(['search', 'status'])) }}" class="btn btn-secondary btn-sm" title="Export Leads">
            <i class="fas fa-file-export"></i> Export
        </a>
        <a href="{{ route('leads.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Lead
        </a>
    </div>
@endsection

@section('content')
    <!-- Search & Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search by name, email, or mobile..." value="{{ request('search') }}">
        </div>
        <select class="filter-select" id="statusFilter">
            <option value="">All Statuses</option>
            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
            <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
            <option value="interested" {{ request('status') == 'interested' ? 'selected' : '' }}>Interested</option>
            <option value="follow_up" {{ request('status') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
            <option value="won" {{ request('status') == 'won' ? 'selected' : '' }}>Won</option>
            <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
        </select>
    </div>

    <!-- Leads Table -->
    <div class="card">
        <div class="table-wrapper">
            <table id="leadsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Follow-ups</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr id="lead-row-{{ $lead->id }}">
                            <td>
                                <strong>{{ $lead->name }}</strong>
                            </td>
                            <td>{{ $lead->mobile_number }}</td>
                            <td>{{ $lead->email ?: '-' }}</td>
                            <td>{{ $lead->source ?: '-' }}</td>
                            <td>
                                <span class="badge badge-{{ str_replace('_', '-', $lead->status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                </span>
                            </td>
                            <td>{{ $lead->follow_ups_count }}</td>
                            <td>{{ $lead->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('leads.show', $lead) }}" class="action-btn" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('leads.edit', $lead) }}" class="action-btn" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button class="action-btn delete" title="Delete" onclick="deleteLead({{ $lead->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No leads found. <a href="{{ route('leads.create') }}" style="color: var(--accent);">Add your first lead</a></p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leads->hasPages())
            <div class="pagination-wrapper">
                @if($leads->onFirstPage())
                    <span class="page-link" style="opacity: 0.5;">← Prev</span>
                @else
                    <a href="{{ $leads->previousPageUrl() }}" class="page-link">← Prev</a>
                @endif

                @foreach($leads->getUrlRange(1, $leads->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="page-link {{ $leads->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
                @endforeach

                @if($leads->hasMorePages())
                    <a href="{{ $leads->nextPageUrl() }}" class="page-link">Next →</a>
                @else
                    <span class="page-link" style="opacity: 0.5;">Next →</span>
                @endif
            </div>
        @endif
    </div>

    <!-- Import Modal -->
    <div class="modal-overlay" id="importModal">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-file-import" style="margin-right: 8px; color: var(--accent);"></i>Import Leads</h3>
                <button class="modal-close" onclick="closeImportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="import-info">
                        <div class="import-info-card">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Supported Formats</strong>
                                <p>CSV (.csv) files — compatible with Excel, Google Sheets, etc.</p>
                            </div>
                        </div>

                        <div class="import-info-card">
                            <i class="fas fa-columns"></i>
                            <div>
                                <strong>Required Columns</strong>
                                <p><code>Name</code> and <code>Mobile Number</code> (or Phone)</p>
                            </div>
                        </div>

                        <div class="import-info-card">
                            <i class="fas fa-magic"></i>
                            <div>
                                <strong>Smart Column Matching</strong>
                                <p>Automatically detects columns like "Phone", "Email Address", "Lead Source", etc.</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label class="form-label">Select CSV File</label>
                        <div class="file-upload-wrapper" id="fileUploadWrapper">
                            <input type="file" name="csv_file" id="csvFileInput" accept=".csv,.txt" required class="file-input">
                            <div class="file-upload-content" id="fileUploadContent">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p><strong>Click to select</strong> or drag & drop</p>
                                <span>CSV file (max 5MB)</span>
                            </div>
                            <div class="file-selected" id="fileSelected" style="display: none;">
                                <i class="fas fa-file-csv"></i>
                                <span id="fileName"></span>
                                <button type="button" class="file-remove" onclick="removeFile()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 8px;">
                        <a href="{{ route('leads.sample-csv') }}" class="sample-download-link">
                            <i class="fas fa-download"></i> Download sample CSV template
                        </a>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeImportModal()">Cancel</button>
                <button class="btn btn-primary" onclick="submitImport()" id="importSubmitBtn">
                    <i class="fas fa-upload"></i> Import Leads
                </button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .header-btn-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .import-info {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .import-info-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 14px;
        background: var(--bg);
        border-radius: var(--radius-sm);
        font-size: 0.85rem;
    }

    .import-info-card i {
        color: var(--accent);
        font-size: 1rem;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .import-info-card strong {
        display: block;
        font-size: 0.8rem;
        color: var(--text-primary);
        margin-bottom: 2px;
    }

    .import-info-card p {
        font-size: 0.78rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .import-info-card code {
        background: rgba(79, 125, 243, 0.1);
        color: var(--accent);
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .file-upload-wrapper {
        position: relative;
        border: 2px dashed var(--border);
        border-radius: var(--radius-sm);
        transition: var(--transition);
        cursor: pointer;
        overflow: hidden;
    }

    .file-upload-wrapper:hover,
    .file-upload-wrapper.dragover {
        border-color: var(--accent);
        background: rgba(79, 125, 243, 0.03);
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .file-upload-content {
        text-align: center;
        padding: 28px 20px;
        color: var(--text-muted);
    }

    .file-upload-content i {
        font-size: 2rem;
        color: var(--accent);
        margin-bottom: 8px;
    }

    .file-upload-content p {
        font-size: 0.85rem;
        margin: 4px 0;
    }

    .file-upload-content span {
        font-size: 0.75rem;
    }

    .file-selected {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        background: rgba(79, 125, 243, 0.05);
    }

    .file-selected i {
        font-size: 1.5rem;
        color: var(--status-won);
    }

    .file-selected span {
        flex: 1;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--text-primary);
    }

    .file-remove {
        color: var(--text-muted);
        padding: 4px 8px;
        border-radius: 4px;
        transition: var(--transition);
        z-index: 3;
        position: relative;
    }

    .file-remove:hover {
        color: var(--status-lost);
        background: rgba(239, 68, 68, 0.1);
    }

    .sample-download-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: var(--accent);
        font-weight: 500;
        transition: var(--transition);
    }

    .sample-download-link:hover {
        color: var(--accent-dark);
        text-decoration: underline;
    }
</style>
@endpush

@push('scripts')
<script>
    // Search with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 400);
    });

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        applyFilters();
    });

    function applyFilters() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const params = new URLSearchParams();
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        window.location.href = '{{ route("leads.index") }}?' + params.toString();
    }

    // Delete lead
    function deleteLead(id) {
        if (!confirm('Are you sure you want to delete this lead?')) return;

        fetch(`/leads/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`lead-row-${id}`);
                row.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => row.remove(), 300);
            }
        })
        .catch(err => alert('Failed to delete lead'));
    }

    // ====== Import Modal ======
    function openImportModal() {
        document.getElementById('importModal').classList.add('active');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.remove('active');
        removeFile();
    }

    // File input handling
    const fileInput = document.getElementById('csvFileInput');
    const fileUploadContent = document.getElementById('fileUploadContent');
    const fileSelected = document.getElementById('fileSelected');
    const fileName = document.getElementById('fileName');
    const fileWrapper = document.getElementById('fileUploadWrapper');

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
            fileUploadContent.style.display = 'none';
            fileSelected.style.display = 'flex';
        }
    });

    // Drag & Drop
    fileWrapper.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    fileWrapper.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
    });

    fileWrapper.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    function removeFile() {
        fileInput.value = '';
        fileUploadContent.style.display = 'block';
        fileSelected.style.display = 'none';
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function submitImport() {
        if (!fileInput.files.length) {
            alert('Please select a CSV file first.');
            return;
        }

        const btn = document.getElementById('importSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';

        document.getElementById('importForm').submit();
    }

    // Close modal on overlay click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
@endpush
