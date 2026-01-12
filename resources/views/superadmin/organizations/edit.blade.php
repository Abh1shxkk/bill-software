@extends('superadmin.layouts.app')

@section('title', 'Edit ' . $organization->name)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('superadmin.organizations.index') }}">Organizations</a></li>
        <li class="breadcrumb-item"><a href="{{ route('superadmin.organizations.show', $organization) }}">{{ $organization->name }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>
<h1 class="page-title">Edit Organization</h1>
@endsection

@section('content')
<form action="{{ route('superadmin.organizations.update', $organization) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-building me-2"></i>Organization Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Organization Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $organization->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $organization->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $organization->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $organization->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="{{ old('email', $organization->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="{{ old('phone', $organization->phone) }}">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $organization->address) }}</textarea>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" 
                                   value="{{ old('city', $organization->city) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" 
                                   value="{{ old('state', $organization->state) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PIN Code</label>
                            <input type="text" name="pin_code" class="form-control" 
                                   value="{{ old('pin_code', $organization->pin_code) }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_no" class="form-control" 
                                   value="{{ old('gst_no', $organization->gst_no) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan_no" class="form-control" 
                                   value="{{ old('pan_no', $organization->pan_no) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Drug License No.</label>
                            <input type="text" name="dl_no" class="form-control" 
                                   value="{{ old('dl_no', $organization->dl_no) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>Organization Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Organization Code</label>
                        <div class="fw-bold">{{ $organization->code }}</div>
                        <small class="text-muted">Cannot be changed</small>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Created</label>
                        <div>{{ $organization->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Last Updated</label>
                        <div>{{ $organization->updated_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('superadmin.organizations.show', $organization) }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>

            @if($organization->code !== 'DEFAULT')
            <div class="card mt-4 border-danger">
                <div class="card-header bg-danger bg-opacity-10">
                    <h5 class="card-title text-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Deleting an organization will permanently remove all associated data including users, licenses, and transactions.</p>
                    <form action="{{ route('superadmin.organizations.destroy', $organization) }}" 
                          method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete Organization
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</form>
@endsection
