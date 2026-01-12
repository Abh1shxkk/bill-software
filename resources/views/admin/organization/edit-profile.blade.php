@extends('layouts.admin')

@section('title', 'Edit Organization Profile')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('admin.organization.update-profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-building me-2"></i>Edit Organization Profile
                        </h5>
                        <a href="{{ route('admin.organization.settings') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <label class="form-label">Logo</label>
                                <div class="mb-2">
                                    @if($organization->logo_path)
                                        <img src="{{ Storage::url($organization->logo_path) }}" 
                                             alt="Logo" class="img-fluid rounded" style="max-height: 100px;" id="logoPreview">
                                    @else
                                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center mx-auto"
                                             style="width: 100px; height: 100px; font-size: 2rem;" id="logoPlaceholder">
                                            {{ strtoupper(substr($organization->name, 0, 2)) }}
                                        </div>
                                        <img src="" alt="Logo" class="img-fluid rounded d-none" style="max-height: 100px;" id="logoPreview">
                                    @endif
                                </div>
                                <input type="file" name="logo" id="logoInput" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted">Max 2MB, JPG/PNG</small>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label class="form-label">Organization Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $organization->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" 
                                               value="{{ old('email', $organization->email) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" 
                                               value="{{ old('phone', $organization->phone) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Address -->
                        <h6 class="mb-3">Address</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Street Address</label>
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
                        </div>

                        <hr class="my-4">

                        <!-- Tax & Licenses -->
                        <h6 class="mb-3">Tax & Licenses</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">GST Number</label>
                                <input type="text" name="gst_no" class="form-control" 
                                       value="{{ old('gst_no', $organization->gst_no) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PAN Number</label>
                                <input type="text" name="pan_no" class="form-control" 
                                       value="{{ old('pan_no', $organization->pan_no) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Drug License No. 1</label>
                                <input type="text" name="dl_no" class="form-control" 
                                       value="{{ old('dl_no', $organization->dl_no) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Drug License No. 2</label>
                                <input type="text" name="dl_no_1" class="form-control" 
                                       value="{{ old('dl_no_1', $organization->dl_no_1) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Food License</label>
                                <input type="text" name="food_license" class="form-control" 
                                       value="{{ old('food_license', $organization->food_license) }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check me-1"></i>Save Changes
                        </button>
                        <a href="{{ route('admin.organization.settings') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('logoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
            document.getElementById('logoPreview').classList.remove('d-none');
            const placeholder = document.getElementById('logoPlaceholder');
            if (placeholder) placeholder.classList.add('d-none');
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
