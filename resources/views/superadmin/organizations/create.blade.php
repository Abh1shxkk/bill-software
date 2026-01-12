@extends('superadmin.layouts.app')

@section('title', 'Create Organization')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('superadmin.organizations.index') }}">Organizations</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>
<h1 class="page-title">Create Organization</h1>
@endsection

@section('content')
<form action="{{ route('superadmin.organizations.store') }}" method="POST">
    @csrf
    
    <div class="row g-4">
        <!-- Organization Details -->
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
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PIN Code</label>
                            <input type="text" name="pin_code" class="form-control" value="{{ old('pin_code') }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_no" class="form-control" value="{{ old('gst_no') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan_no" class="form-control" value="{{ old('pan_no') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Drug License No.</label>
                            <input type="text" name="dl_no" class="form-control" value="{{ old('dl_no') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin User -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-user-shield me-2"></i>Admin User
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Create the administrator account for this organization</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror" 
                                   value="{{ old('admin_name') }}" required>
                            @error('admin_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" 
                                   value="{{ old('admin_email') }}" required>
                            @error('admin_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror" 
                                   required minlength="8">
                            @error('admin_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Details -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-key me-2"></i>License
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Plan Type <span class="text-danger">*</span></label>
                        <select name="plan_type" class="form-select @error('plan_type') is-invalid @enderror" required>
                            <option value="trial" {{ old('plan_type') == 'trial' ? 'selected' : '' }}>Trial (14 days)</option>
                            <option value="basic" {{ old('plan_type') == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="standard" {{ old('plan_type', 'standard') == 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="premium" {{ old('plan_type') == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="enterprise" {{ old('plan_type') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                        @error('plan_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">License Validity (Days) <span class="text-danger">*</span></label>
                        <input type="number" name="license_days" class="form-control @error('license_days') is-invalid @enderror" 
                               value="{{ old('license_days', 30) }}" min="1" max="3650" required>
                        @error('license_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Max 10 years (3650 days)</small>
                    </div>

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-2"></i>
                        A license key will be automatically generated and can be shared with the organization admin.
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-check me-2"></i>Create Organization
                    </button>
                    <a href="{{ route('superadmin.organizations.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
