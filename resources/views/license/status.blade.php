@extends('layouts.app')

@section('title', 'License Status')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-key me-2 text-primary"></i>License Status
                    </h5>
                    @if($license && $license->isValid())
                        <span class="badge bg-success">Active</span>
                    @elseif($license && $license->isExpired())
                        <span class="badge bg-danger">Expired</span>
                    @else
                        <span class="badge bg-warning">Inactive</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($license)
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="text-muted small">License Key</label>
                                <div class="font-monospace bg-light p-2 rounded">
                                    {{ $license->license_key }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Plan</label>
                                <div class="fw-bold text-capitalize">
                                    {{ $license->plan_type }}
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="text-muted small">Start Date</label>
                                <div>{{ $license->starts_at?->format('d M Y') ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Expiry Date</label>
                                <div class="{{ $license->isExpiringSoon() ? 'text-warning' : '' }} {{ $license->isExpired() ? 'text-danger' : '' }}">
                                    {{ $license->expires_at?->format('d M Y') ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Days Remaining</label>
                                <div class="{{ $license->daysUntilExpiry() <= 7 ? 'text-warning fw-bold' : '' }}">
                                    {{ $license->daysUntilExpiry() }} days
                                </div>
                            </div>
                        </div>

                        @if($license->isExpiringSoon())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>License Expiring Soon!</strong>
                            Your license will expire in {{ $license->daysUntilExpiry() }} days. 
                            Please contact support to renew.
                        </div>
                        @endif

                        @if($usageLimits)
                        <h6 class="mb-3">Usage Limits</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">Users</div>
                                        <div class="fs-4 fw-bold {{ $usageLimits['users']['exceeded'] ? 'text-danger' : '' }}">
                                            {{ $usageLimits['users']['current'] }} / {{ $license->max_users }}
                                        </div>
                                        <div class="progress" style="height: 5px;">
                                            @php $userPct = min(100, ($usageLimits['users']['current'] / $license->max_users) * 100); @endphp
                                            <div class="progress-bar {{ $userPct > 90 ? 'bg-danger' : 'bg-primary' }}" 
                                                 style="width: {{ $userPct }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">Items</div>
                                        <div class="fs-4 fw-bold {{ $usageLimits['items']['exceeded'] ? 'text-danger' : '' }}">
                                            {{ number_format($usageLimits['items']['current']) }} / {{ number_format($license->max_items) }}
                                        </div>
                                        <div class="progress" style="height: 5px;">
                                            @php $itemPct = min(100, ($usageLimits['items']['current'] / $license->max_items) * 100); @endphp
                                            <div class="progress-bar {{ $itemPct > 90 ? 'bg-danger' : 'bg-success' }}" 
                                                 style="width: {{ $itemPct }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">Transactions/Month</div>
                                        <div class="fs-4 fw-bold">
                                            {{ number_format($license->max_transactions_per_month) }}
                                        </div>
                                        <div class="text-muted small">Limit</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-key fa-3x text-muted mb-3"></i>
                            <h5>No Active License</h5>
                            <p class="text-muted">
                                Your organization doesn't have an active license.
                            </p>
                            <a href="{{ route('license.required') }}" class="btn btn-primary">
                                <i class="fas fa-unlock me-2"></i>Activate License
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($organization)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Organization</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $organization->name }}</h6>
                    <p class="text-muted small mb-2">Code: {{ $organization->code }}</p>
                    
                    @if($organization->email)
                        <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $organization->email }}</p>
                    @endif
                    @if($organization->phone)
                        <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $organization->phone }}</p>
                    @endif
                    @if($organization->gst_no)
                        <p class="mb-0"><i class="fas fa-file-alt me-2"></i>GST: {{ $organization->gst_no }}</p>
                    @endif
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-headset me-2"></i>Need Help?</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        For license renewal or support inquiries:
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:support@medibill.com">support@medibill.com</a>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:+911234567890">+91 1234-567-890</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
