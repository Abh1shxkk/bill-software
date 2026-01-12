@extends('superadmin.layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</nav>
<h1 class="page-title">Dashboard</h1>
@endsection

@section('content')
<!-- Stats Row -->
<div class="row g-4 mb-4">
    <!-- Organizations Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-label">Total Organizations</div>
                    <div class="stats-value">{{ $organizationStats['total'] }}</div>
                </div>
                <div class="stats-icon bg-primary bg-opacity-25 text-primary">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="badge badge-active badge-status">{{ $organizationStats['active'] }} Active</span>
                @if($organizationStats['suspended'] > 0)
                    <span class="badge badge-suspended badge-status">{{ $organizationStats['suspended'] }} Suspended</span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Licenses Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-label">Active Licenses</div>
                    <div class="stats-value">{{ $licenseStats['active'] }}</div>
                </div>
                <div class="stats-icon bg-success bg-opacity-25 text-success">
                    <i class="fas fa-key"></i>
                </div>
            </div>
            <div class="mt-3">
                @if($licenseStats['expiring_soon'] > 0)
                    <span class="badge badge-expiring badge-status">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        {{ $licenseStats['expiring_soon'] }} Expiring Soon
                    </span>
                @else
                    <span class="text-muted small">All licenses valid</span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Users Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-label">Total Users</div>
                    <div class="stats-value">{{ $userStats['total'] }}</div>
                </div>
                <div class="stats-icon bg-info bg-opacity-25 text-info">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-muted small">
                    {{ $userStats['admins'] }} Admins, {{ $userStats['staff'] }} Staff
                </span>
            </div>
        </div>
    </div>
    
    <!-- Expired Licenses -->
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stats-label">Expired Licenses</div>
                    <div class="stats-value text-danger">{{ $licenseStats['expired'] }}</div>
                </div>
                <div class="stats-icon bg-danger bg-opacity-25 text-danger">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('superadmin.licenses.index', ['status' => 'expired']) }}" class="text-danger small">
                    View expired licenses <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Organizations -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="fas fa-building me-2 text-primary"></i>Recent Organizations
                </h5>
                <a href="{{ route('superadmin.organizations.index') }}" class="btn btn-sm btn-outline-light">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Organization</th>
                                <th>Status</th>
                                <th>License</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrganizations as $org)
                            <tr>
                                <td>
                                    <a href="{{ route('superadmin.organizations.show', $org) }}" class="text-decoration-none text-light">
                                        <strong>{{ $org->name }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $org->code }}</small>
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
                                    @else
                                        <span class="badge badge-status badge-expired">No License</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $org->created_at->format('d M Y') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No organizations yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Expiring Licenses -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Licenses Expiring Soon
                </h5>
                <a href="{{ route('superadmin.licenses.index', ['status' => 'expiring']) }}" class="btn btn-sm btn-outline-light">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Organization</th>
                                <th>Plan</th>
                                <th>Expires</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expiringLicenses as $license)
                            <tr>
                                <td>
                                    <strong>{{ $license->organization->name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-status badge-active">
                                        {{ ucfirst($license->plan_type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-warning">
                                        {{ $license->expires_at->format('d M Y') }}
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        {{ $license->daysUntilExpiry() }} days left
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('superadmin.licenses.show', $license) }}" 
                                       class="btn btn-sm btn-outline-light">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    No licenses expiring in the next 30 days
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Plan Distribution -->
<div class="row g-4 mt-2">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-pie me-2 text-info"></i>License Distribution by Plan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $planColors = [
                            'trial' => 'secondary',
                            'basic' => 'info',
                            'standard' => 'primary',
                            'premium' => 'warning',
                            'enterprise' => 'success',
                        ];
                    @endphp
                    @foreach(['trial', 'basic', 'standard', 'premium', 'enterprise'] as $plan)
                        <div class="col-6 col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="rounded-circle bg-{{ $planColors[$plan] ?? 'secondary' }}" 
                                         style="width: 12px; height: 12px;"></div>
                                </div>
                                <div>
                                    <div class="text-muted small text-capitalize">{{ $plan }}</div>
                                    <div class="fw-bold">{{ $planDistribution[$plan] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('superadmin.organizations.create') }}" 
                           class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-building me-2"></i>
                            <br>New Organization
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('superadmin.licenses.create') }}" 
                           class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-key me-2"></i>
                            <br>Generate License
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('superadmin.organizations.index') }}" 
                           class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-list me-2"></i>
                            <br>All Organizations
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('superadmin.licenses.index', ['status' => 'expired']) }}" 
                           class="btn btn-outline-danger w-100 py-3">
                            <i class="fas fa-clock me-2"></i>
                            <br>Expired Licenses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
