@extends('superadmin.layouts.app')

@section('title', 'Licenses')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
        <li class="breadcrumb-item active">Licenses</li>
    </ol>
</nav>
<h1 class="page-title">Licenses</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-key me-2"></i>All Licenses
        </h5>
        <a href="{{ route('superadmin.licenses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Generate License
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('superadmin.licenses.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by key or organization..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="expiring" {{ request('status') == 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="plan_type" class="form-select">
                    <option value="">All Plans</option>
                    @foreach($plans as $plan)
                    <option value="{{ $plan->code }}" {{ request('plan_type') == $plan->code ? 'selected' : '' }}>
                        {{ $plan->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-light w-100">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
            </div>
            @if(request()->hasAny(['search', 'status', 'plan_type']))
            <div class="col-md-2">
                <a href="{{ route('superadmin.licenses.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
            @endif
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>License Key</th>
                        <th>Organization</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Expires</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licenses as $license)
                    <tr>
                        <td>
                            <code class="license-key small">{{ $license->license_key }}</code>
                            <br>
                            <small class="text-muted">
                                Created: {{ $license->created_at->format('d M Y') }}
                            </small>
                        </td>
                        <td>
                            @if($license->organization)
                            <a href="{{ route('superadmin.organizations.show', $license->organization) }}" 
                               class="text-decoration-none text-light">
                                {{ $license->organization->name }}
                            </a>
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ ucfirst($license->plan_type) }}
                            </span>
                        </td>
                        <td>
                            @if($license->isValid())
                                <span class="badge badge-status badge-active">Active</span>
                                @if($license->isExpiringSoon())
                                    <br><small class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $license->daysUntilExpiry() }} days left
                                    </small>
                                @endif
                            @elseif($license->isExpired())
                                <span class="badge badge-status badge-expired">Expired</span>
                            @else
                                <span class="badge badge-status badge-suspended">Suspended</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $license->isExpired() ? 'text-danger' : '' }}">
                                {{ $license->expires_at?->format('d M Y') ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('superadmin.licenses.show', $license) }}" 
                                   class="btn btn-outline-light" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($license->isValid())
                                    <button type="button" class="btn btn-outline-warning" title="Suspend"
                                            data-bs-toggle="modal" data-bs-target="#suspendModal{{ $license->id }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <form action="{{ route('superadmin.licenses.reactivate', $license) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Reactivate"
                                                {{ $license->isExpired() ? 'disabled' : '' }}>
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <button type="button" class="btn btn-outline-info" title="Extend"
                                        data-bs-toggle="modal" data-bs-target="#extendModal{{ $license->id }}">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                            </div>

                            <!-- Suspend Modal -->
                            <div class="modal fade" id="suspendModal{{ $license->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content bg-dark">
                                        <form action="{{ route('superadmin.licenses.suspend', $license) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title">Suspend License</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Suspend license for <strong>{{ $license->organization?->name }}</strong>?</p>
                                                <div class="mb-3">
                                                    <label class="form-label">Reason (optional)</label>
                                                    <textarea name="reason" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning">Suspend</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Extend Modal -->
                            <div class="modal fade" id="extendModal{{ $license->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content bg-dark">
                                        <form action="{{ route('superadmin.licenses.extend', $license) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title">Extend License</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Extend license for <strong>{{ $license->organization?->name }}</strong></p>
                                                <p class="small text-muted">
                                                    Current expiry: {{ $license->expires_at?->format('d M Y') ?? 'N/A' }}
                                                </p>
                                                <div class="mb-3">
                                                    <label class="form-label">Extend by (days)</label>
                                                    <input type="number" name="days" class="form-control" 
                                                           value="30" min="1" max="3650" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-info">Extend</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-key fa-3x mb-3 d-block opacity-50"></i>
                            No licenses found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $licenses->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
