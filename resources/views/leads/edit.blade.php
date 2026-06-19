@extends('layouts.app')

@section('title', 'Edit Lead')
@section('page-title', 'Edit Lead')

@section('content')
    <div class="card" style="max-width: 680px;">
        <div class="card-header">
            <h2><i class="fas fa-pen" style="margin-right: 8px; color: var(--accent);"></i>Edit Lead</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('leads.update', $lead) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="name">Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $lead->name) }}" placeholder="Enter full name" required>
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="mobile_number">Mobile Number *</label>
                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $lead->mobile_number) }}" placeholder="+91 98765 43210" required>
                        @error('mobile_number') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $lead->email) }}" placeholder="email@example.com">
                        @error('email') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="source">Source</label>
                        <select class="form-control" id="source" name="source">
                            <option value="">Select Source</option>
                            @foreach(['Website', 'Referral', 'Social Media', 'Google Ads', 'Cold Call', 'Email Campaign', 'Walk In', 'Other'] as $src)
                                <option value="{{ $src }}" {{ old('source', $lead->source) == $src ? 'selected' : '' }}>{{ $src }}</option>
                            @endforeach
                        </select>
                        @error('source') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="status">Status *</label>
                        <select class="form-control" id="status" name="status" required>
                            @foreach(['new', 'contacted', 'interested', 'follow_up', 'won', 'lost'] as $st)
                                <option value="{{ $st }}" {{ old('status', $lead->status) == $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 8px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Lead
                    </button>
                    <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
