@extends('layouts.admin')

@section('title', 'Manage Permissions - ' . $user->full_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-shield-lock me-2"></i>Manage Permissions
            </h4>
            <p class="text-muted mb-0">
                User: <strong>{{ $user->full_name }}</strong> ({{ $user->username }})
            </p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Users
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.users.permissions.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span>Module Access Permissions</span>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success" id="selectAllPerms">
                        <i class="bi bi-check-all me-1"></i>Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPerms">
                        <i class="bi bi-x-lg me-1"></i>Deselect All
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light py-2">
                                    <strong><i class="bi bi-folder me-1"></i>{{ $group }}</strong>
                                    <button type="button" class="btn btn-sm btn-link float-end group-select-all" data-group="{{ Str::slug($group) }}">
                                        Select All
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Module</th>
                                                <th width="55" class="text-center">View</th>
                                                <th width="55" class="text-center">Add</th>
                                                <th width="55" class="text-center">Edit</th>
                                                <th width="55" class="text-center">Del</th>
                                                <th width="45" class="text-center">All</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groupPermissions as $permission)
                                                @php
                                                    $userPerm = $userPermissions->get($permission->id);
                                                @endphp
                                                <tr data-group="{{ Str::slug($group) }}">
                                                    <td class="small">
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
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer bg-white">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Save Permissions
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
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

    // Group select all
    document.querySelectorAll('.group-select-all').forEach(btn => {
        btn.addEventListener('click', function() {
            const group = this.dataset.group;
            const rows = document.querySelectorAll(`tr[data-group="${group}"]`);
            rows.forEach(row => {
                row.querySelectorAll('.perm-checkbox, .row-select-all').forEach(cb => cb.checked = true);
            });
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
