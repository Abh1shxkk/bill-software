@extends('superadmin.layouts.app')

@section('title', $organization->name)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('superadmin.organizations.index') }}">Organizations</a></li>
        <li class="breadcrumb-item active">{{ $organization->name }}</li>
    </ol>
</nav>
<h1 class="page-title">{{ $organization->name }}</h1>
@endsection

@section('content')
<div class="row g-4">
    <!-- Organization Info -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Organization Details
                </h5>
                <div>
                    <a href="{{ route('superadmin.organizations.edit', $organization) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    @if($organization->status !== 'suspended')
                        <form action="{{ route('superadmin.organizations.suspend', $organization) }}" 
                              method="POST" class="d-inline" onsubmit="return confirm('Suspend this organization?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-ban me-1"></i>Suspend
                            </button>
                        </form>
                    @else
                        <form action="{{ route('superadmin.organizations.activate', $organization) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-check me-1"></i>Activate
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Organization Code</label>
                        <div class="fw-bold">{{ $organization->code }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Status</label>
                        <div>
                            <span class="badge badge-status badge-{{ $organization->status }}">
                                {{ ucfirst($organization->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <div>{{ $organization->email ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Phone</label>
                        <div>{{ $organization->phone ?: '-' }}</div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Address</label>
                        <div>
                            {{ $organization->address ?: '-' }}
                            @if($organization->city || $organization->state || $organization->pin_code)
                                <br>{{ collect([$organization->city, $organization->state, $organization->pin_code])->filter()->join(', ') }}
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">GST Number</label>
                        <div>{{ $organization->gst_no ?: '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">PAN Number</label>
                        <div>{{ $organization->pan_no ?: '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Drug License</label>
                        <div>{{ $organization->dl_no ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-value">{{ $stats['users_count'] }}</div>
                    <div class="stats-label">Users</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-value">{{ $stats['customers_count'] }}</div>
                    <div class="stats-label">Customers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-value">{{ $stats['suppliers_count'] }}</div>
                    <div class="stats-label">Suppliers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-value">{{ $stats['items_count'] }}</div>
                    <div class="stats-label">Items</div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>Users
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($organization->users as $user)
                            <tr>
                                <td>
                                    {{ $user->full_name }}
                                    @if($user->is_organization_owner)
                                        <span class="badge bg-primary ms-1">Owner</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->role_badge_class }}">
                                        {{ $user->role_display_name }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge badge-status badge-active">Active</span>
                                    @else
                                        <span class="badge badge-status badge-suspended">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No users found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Licenses -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-key me-2"></i>Licenses
                </h5>
                <a href="{{ route('superadmin.licenses.create', ['organization_id' => $organization->id]) }}" 
                   class="btn btn-sm btn-outline-success">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
            <div class="card-body">
                @forelse($organization->licenses as $license)
                <div class="border rounded p-3 mb-3 {{ $license->isValid() ? 'border-success' : 'border-danger' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge badge-status {{ $license->isValid() ? 'badge-active' : 'badge-expired' }}">
                            {{ $license->isValid() ? 'Active' : ($license->isExpired() ? 'Expired' : 'Suspended') }}
                        </span>
                        <span class="badge bg-secondary">{{ ucfirst($license->plan_type) }}</span>
                    </div>
                    
                    <div class="license-key small mb-2">{{ $license->license_key }}</div>
                    
                    <div class="row small text-muted">
                        <div class="col-6">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $license->starts_at?->format('d M Y') ?? 'N/A' }}
                        </div>
                        <div class="col-6 text-end">
                            <i class="fas fa-clock me-1"></i>
                            {{ $license->expires_at?->format('d M Y') ?? 'N/A' }}
                        </div>
                    </div>

                    @if($license->isValid() && $license->isExpiringSoon())
                        <div class="alert alert-warning small py-1 px-2 mt-2 mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Expires in {{ $license->daysUntilExpiry() }} days
                        </div>
                    @endif

                    <div class="mt-2">
                        <a href="{{ route('superadmin.licenses.show', $license) }}" 
                           class="btn btn-sm btn-outline-light w-100">
                            <i class="fas fa-eye me-1"></i>View Details
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-key fa-2x mb-2 opacity-50 d-block"></i>
                    No licenses yet
                    <br>
                    <a href="{{ route('superadmin.licenses.create', ['organization_id' => $organization->id]) }}" 
                       class="btn btn-sm btn-primary mt-2">
                        Generate License
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Quick Info
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Created</span>
                    <span>{{ $organization->created_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Updated</span>
                    <span>{{ $organization->updated_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Owner</span>
                    <span>{{ $organization->owner?->full_name ?? 'Not Set' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
