@extends('superadmin.layouts.app')

@section('title', 'Organizations')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
        <li class="breadcrumb-item active">Organizations</li>
    </ol>
</nav>
<h1 class="page-title">Organizations</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-building me-2"></i>All Organizations
        </h5>
        <a href="{{ route('superadmin.organizations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Organization
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('superadmin.organizations.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by name, code or email..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="license_status" class="form-select">
                    <option value="">All Licenses</option>
                    <option value="active" {{ request('license_status') == 'active' ? 'selected' : '' }}>Has Active License</option>
                    <option value="expired" {{ request('license_status') == 'expired' ? 'selected' : '' }}>No Active License</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-light w-100">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
            </div>
            @if(request()->hasAny(['search', 'status', 'license_status']))
            <div class="col-md-2">
                <a href="{{ route('superadmin.organizations.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th>Organization</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>License</th>
                        <th>Users</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organizations as $org)
                    <tr>
                        <td>
                            <a href="{{ route('superadmin.organizations.show', $org) }}" 
                               class="text-decoration-none text-light">
                                <strong>{{ $org->name }}</strong>
                            </a>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-hashtag me-1"></i>{{ $org->code }}
                            </small>
                        </td>
                        <td>
                            @if($org->email)
                                <small><i class="fas fa-envelope me-1"></i>{{ $org->email }}</small><br>
                            @endif
                            @if($org->phone)
                                <small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $org->phone }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-status badge-{{ $org->status }}">
                                {{ ucfirst($org->status) }}
                            </span>
                        </td>
                        <td>
                            @if($org->activeLicense)
                                <span class="badge badge-status badge-active">
                                    {{ ucfirst($org->activeLicense->plan_type) }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    Expires: {{ $org->activeLicense->expires_at->format('d M Y') }}
                                </small>
                            @else
                                <span class="badge badge-status badge-expired">No License</span>
                            @endif
                        </td>
                        <td>
                            @php $usersCount = $org->users()->count(); @endphp
                            <span class="badge bg-secondary">{{ $usersCount }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $org->created_at->format('d M Y') }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('superadmin.organizations.show', $org) }}" 
                                   class="btn btn-outline-light" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('superadmin.organizations.edit', $org) }}" 
                                   class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($org->status !== 'suspended')
                                    <form action="{{ route('superadmin.organizations.suspend', $org) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Suspend this organization?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning" title="Suspend">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('superadmin.organizations.activate', $org) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Activate">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-building fa-3x mb-3 d-block opacity-50"></i>
                            No organizations found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $organizations->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
