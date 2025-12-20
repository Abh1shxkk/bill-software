@extends('layouts.admin')
@section('title','Profile Settings')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Profile Settings</h2>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php($user = auth()->user())

            <div class="row g-3">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profile</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12 mb-3 text-center">
                                        <div class="position-relative d-inline-block">
                                            <img id="profilePreview" src="{{ $user->profile_picture ? (str_starts_with($user->profile_picture, 'storage/') ? asset($user->profile_picture) : asset('storage/' . $user->profile_picture)) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name ?? $user->name ?? 'User') . '&background=0D8ABC&color=fff&size=160' }}" alt="Profile" class="rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->full_name ?? $user->name ?? 'User') }}&background=0D8ABC&color=fff&size=160'">
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label">Profile Photo</label>
                                            <input class="form-control" type="file" name="profile_picture" id="profile_picture" accept="image/*">
                                            <div class="form-text">Max 2MB. JPG, PNG, GIF.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('full_name', $user->full_name ?? $user->name) }}">
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $user->username) }}">
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Telephone</label>
                                        <input type="text" class="form-control @error('telephone') is-invalid @enderror" name="telephone" value="{{ old('telephone', $user->telephone) }}" placeholder="Enter phone number">
                                        @error('telephone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="2" placeholder="Enter address">{{ old('address', $user->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <h6 class="text-muted border-bottom pb-2"><i class="bi bi-building me-2"></i>Business Information</h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">TIN No.</label>
                                        <input type="text" class="form-control @error('tin_no') is-invalid @enderror" name="tin_no" value="{{ old('tin_no', $user->tin_no) }}" placeholder="Enter TIN number">
                                        @error('tin_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GST No.</label>
                                        <input type="text" class="form-control @error('gst_no') is-invalid @enderror" name="gst_no" value="{{ old('gst_no', $user->gst_no) }}" placeholder="Enter GST number">
                                        @error('gst_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">DL No.</label>
                                        <input type="text" class="form-control @error('dl_no') is-invalid @enderror" name="dl_no" value="{{ old('dl_no', $user->dl_no) }}" placeholder="Enter Drug License number">
                                        @error('dl_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">DL No. 1</label>
                                        <input type="text" class="form-control @error('dl_no_1') is-invalid @enderror" name="dl_no_1" value="{{ old('dl_no_1', $user->dl_no_1) }}" placeholder="Enter second Drug License number">
                                        @error('dl_no_1')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Licensed To</label>
                                        <input type="text" class="form-control @error('licensed_to') is-invalid @enderror" name="licensed_to" value="{{ old('licensed_to', $user->licensed_to) }}" placeholder="Enter licensed to name (e.g., Business/Person name)">
                                        @error('licensed_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('password.change') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" autocomplete="current-password">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-key me-2"></i>Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('profile_picture')?.addEventListener('change', function(e) {
        const [file] = e.target.files;
        if (file) {
            const img = document.getElementById('profilePreview');
            img.src = URL.createObjectURL(file);
        }
    });
</script>
@endpush
@endsection
