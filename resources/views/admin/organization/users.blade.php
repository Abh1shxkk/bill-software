@extends('layouts.admin')

@section('title', 'Organization Users')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>Organization Users
                </h5>
                @if($license)
                <small class="text-muted">
                    {{ $users->total() }} of {{ $license->max_users }} users
                </small>
                @endif
            </div>
            <div>
                <a href="{{ route('admin.organization.settings') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
                @if(!$license || $users->total() < $license->max_users)
                <a href="{{ route('admin.organization.create-user') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus me-1"></i>Add User
                </a>
                @else
                <button class="btn btn-sm btn-secondary" disabled title="User limit reached">
                    <i class="bi bi-plus me-1"></i>Add User (Limit Reached)
                </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($license && $users->total() >= $license->max_users)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>User Limit Reached!</strong> 
                You have reached the maximum number of users ({{ $license->max_users }}) for your plan.
                <a href="{{ route('admin.organization.license') }}">Upgrade your license</a> to add more users.
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                         style="width: 36px; height: 36px; font-size: 0.85rem;">
                                        {{ strtoupper(substr($user->full_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $user->full_name }}</strong>
                                        @if($user->is_organization_owner)
                                            <span class="badge bg-warning ms-1">Owner</span>
                                        @endif
                                        @if($user->user_id === auth()->id())
                                            <span class="badge bg-info ms-1">You</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $user->username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'manager' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                @if($user->user_id !== auth()->id() && !$user->is_organization_owner)
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('admin.organization.toggle-user', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.organization.remove-user', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Remove this user from the organization?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-people" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">No users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
