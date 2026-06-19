@extends('layouts.app')

@section('title', 'Add Lead')
@section('page-title', 'Add New Lead')

@section('content')
    <div class="card" style="max-width: 680px;">
        <div class="card-header">
            <h2><i class="fas fa-user-plus" style="margin-right: 8px; color: var(--accent);"></i>Lead Information</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('leads.store') }}" method="POST" id="leadForm">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name" required>
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="mobile_number">Mobile Number *</label>
                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="+91 98765 43210" required>
                        @error('mobile_number') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                        @error('email') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="source">Source</label>
                        <select class="form-control" id="source" name="source">
                            <option value="">Select Source</option>
                            <option value="Website" {{ old('source') == 'Website' ? 'selected' : '' }}>Website</option>
                            <option value="Referral" {{ old('source') == 'Referral' ? 'selected' : '' }}>Referral</option>
                            <option value="Social Media" {{ old('source') == 'Social Media' ? 'selected' : '' }}>Social Media</option>
                            <option value="Google Ads" {{ old('source') == 'Google Ads' ? 'selected' : '' }}>Google Ads</option>
                            <option value="Cold Call" {{ old('source') == 'Cold Call' ? 'selected' : '' }}>Cold Call</option>
                            <option value="Email Campaign" {{ old('source') == 'Email Campaign' ? 'selected' : '' }}>Email Campaign</option>
                            <option value="Walk In" {{ old('source') == 'Walk In' ? 'selected' : '' }}>Walk In</option>
                            <option value="Other" {{ old('source') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('source') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="status">Status *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="new" {{ old('status', 'new') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="interested" {{ old('status') == 'interested' ? 'selected' : '' }}>Interested</option>
                            <option value="follow_up" {{ old('status') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                            <option value="won" {{ old('status') == 'won' ? 'selected' : '' }}>Won</option>
                            <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                        @error('status') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 8px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Lead
                    </button>
                    <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
