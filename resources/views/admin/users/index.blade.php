@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-people-fill me-2"></i>User Management
        </h4>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Add User
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

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span>All Users</span>
            <button type="button" class="btn btn-danger btn-sm" id="deleteSelectedBtn" style="display: none;">
                <i class="bi bi-trash me-1"></i>Delete Selected (<span id="selectedCount">0</span>)
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $user->user_id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://i.pravatar.cc/32?u=' . $user->user_id }}" 
                                             class="rounded-circle me-2" width="32" height="32" alt="">
                                        {{ $user->full_name }}
                                    </div>
                                </td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm status-toggle {{ $user->is_active ? 'btn-success' : 'btn-secondary' }}"
                                            data-user-id="{{ $user->user_id }}"
                                            title="Click to toggle status">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.permissions', $user) }}" 
                                           class="btn btn-outline-primary" title="Manage Permissions">
                                            <i class="bi bi-shield-lock"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="btn btn-outline-secondary" title="Edit User">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger delete-btn"
                                                data-delete-url="{{ route('admin.users.destroy', $user) }}"
                                                data-delete-message="Are you sure you want to delete user '{{ $user->full_name }}'?"
                                                title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    No users found. <a href="{{ route('admin.users.create') }}">Add your first user</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-toggle {
        min-width: 80px;
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Select all functionality
    selectAll?.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
        updateDeleteButton();
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteButton);
    });

    function updateDeleteButton() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        selectedCountSpan.textContent = checkedCount;
        deleteSelectedBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
    }

    // Delete selected
    deleteSelectedBtn?.addEventListener('click', function() {
        const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
        if (ids.length === 0) return;

        if (confirm(`Are you sure you want to delete ${ids.length} user(s)?`)) {
            fetch('{{ route("admin.users.multiple-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to delete users');
                }
            })
            .catch(() => alert('An error occurred'));
        }
    });

    // Status toggle
    document.querySelectorAll('.status-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            fetch(`/admin/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.textContent = data.is_active ? 'Active' : 'Inactive';
                    this.classList.toggle('btn-success', data.is_active);
                    this.classList.toggle('btn-secondary', !data.is_active);
                } else {
                    alert(data.message || 'Failed to update status');
                }
            })
            .catch(() => alert('An error occurred'));
        });
    });
});
</script>
@endpush
