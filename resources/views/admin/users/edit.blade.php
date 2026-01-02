@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-pencil-square me-2"></i>Edit User: {{ $user->full_name }}
        </h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Users
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- User Details -->
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <i class="bi bi-person me-2"></i>User Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                                   value="{{ old('full_name', $user->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                   value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telephone</label>
                            <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $user->telephone) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" 
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Active User</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-shield-lock me-2"></i>Module Permissions</span>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPerms">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPerms">Deselect All</button>
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="mb-4">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-folder me-1"></i>{{ $group }}
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Module</th>
                                                <th width="70" class="text-center">View</th>
                                                <th width="70" class="text-center">Create</th>
                                                <th width="70" class="text-center">Edit</th>
                                                <th width="70" class="text-center">Delete</th>
                                                <th width="60" class="text-center">All</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groupPermissions as $permission)
                                                @php
                                                    $userPerm = $userPermissions->get($permission->id);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <i class="{{ $permission->icon }} me-1 text-muted"></i>
                                                        {{ $permission->display_name }}
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input perm-checkbox" 
                                                               name="permissions[{{ $permission->id }}][view]" value="1"
                                                               data-perm-id="{{ $permission->id }}"
                                                               {{ $userPerm && $userPerm->can_view ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input perm-checkbox" 
                                                               name="permissions[{{ $permission->id }}][create]" value="1"
                                                               data-perm-id="{{ $permission->id }}"
                                                               {{ $userPerm && $userPerm->can_create ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input perm-checkbox" 
                                                               name="permissions[{{ $permission->id }}][edit]" value="1"
                                                               data-perm-id="{{ $permission->id }}"
                                                               {{ $userPerm && $userPerm->can_edit ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input perm-checkbox" 
                                                               name="permissions[{{ $permission->id }}][delete]" value="1"
                                                               data-perm-id="{{ $permission->id }}"
                                                               {{ $userPerm && $userPerm->can_delete ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $allChecked = $userPerm && $userPerm->can_view && $userPerm->can_create && $userPerm->can_edit && $userPerm->can_delete;
                                                        @endphp
                                                        <input type="checkbox" class="form-check-input row-select-all" 
                                                               data-perm-id="{{ $permission->id }}"
                                                               {{ $allChecked ? 'checked' : '' }}>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Update User
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Row select all
    document.querySelectorAll('.row-select-all').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const permId = this.dataset.permId;
            document.querySelectorAll(`.perm-checkbox[data-perm-id="${permId}"]`).forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    // Update row select all when individual checkboxes change
    document.querySelectorAll('.perm-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const permId = this.dataset.permId;
            const allChecked = document.querySelectorAll(`.perm-checkbox[data-perm-id="${permId}"]`);
            const checkedCount = document.querySelectorAll(`.perm-checkbox[data-perm-id="${permId}"]:checked`).length;
            const rowSelectAll = document.querySelector(`.row-select-all[data-perm-id="${permId}"]`);
            if (rowSelectAll) {
                rowSelectAll.checked = checkedCount === allChecked.length;
            }
        });
    });

    // Select all permissions
    document.getElementById('selectAllPerms')?.addEventListener('click', function() {
        document.querySelectorAll('.perm-checkbox, .row-select-all').forEach(cb => cb.checked = true);
    });

    // Deselect all permissions
    document.getElementById('deselectAllPerms')?.addEventListener('click', function() {
        document.querySelectorAll('.perm-checkbox, .row-select-all').forEach(cb => cb.checked = false);
    });
});
</script>
@endpush
