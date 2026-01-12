@extends('superadmin.layouts.app')

@section('title', 'License Details')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('superadmin.licenses.index') }}">Licenses</a></li>
        <li class="breadcrumb-item active">{{ Str::limit($license->license_key, 20) }}</li>
    </ol>
</nav>
<h1 class="page-title">License Details</h1>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <!-- License Info -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-key me-2"></i>License Information
                </h5>
                <div>
                    @if($license->isValid())
                        <span class="badge badge-status badge-active">Active</span>
                    @elseif($license->isExpired())
                        <span class="badge badge-status badge-expired">Expired</span>
                    @else
                        <span class="badge badge-status badge-suspended">Suspended</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="text-muted small">License Key</label>
                        <div class="license-key d-flex align-items-center justify-content-between">
                            <span id="licenseKey">{{ $license->license_key }}</span>
                            <button type="button" class="btn btn-sm btn-outline-light ms-2" onclick="copyLicenseKey()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="text-muted small">Organization</label>
                        <div>
                            @if($license->organization)
                            <a href="{{ route('superadmin.organizations.show', $license->organization) }}" 
                               class="text-decoration-none text-light">
                                <strong>{{ $license->organization->name }}</strong>
                            </a>
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Plan Type</label>
                        <div>
                            <span class="badge bg-secondary">{{ ucfirst($license->plan_type) }}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="text-muted small">Issued Date</label>
                        <div>{{ $license->issued_at?->format('d M Y, h:i A') ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Start Date</label>
                        <div>{{ $license->starts_at?->format('d M Y, h:i A') ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Expiry Date</label>
                        <div class="{{ $license->isExpired() ? 'text-danger' : '' }}">
                            {{ $license->expires_at?->format('d M Y, h:i A') ?? 'N/A' }}
                            @if($license->isExpiringSoon() && !$license->isExpired())
                                <br><small class="text-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $license->daysUntilExpiry() }} days remaining
                                </small>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="text-muted small">Activated At</label>
                        <div>{{ $license->activated_at?->format('d M Y, h:i A') ?? 'Not activated' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Activation IP</label>
                        <div>{{ $license->activation_ip ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Activation Domain</label>
                        <div>{{ $license->activation_domain ?? '-' }}</div>
                    </div>
                    
                    @if($license->notes)
                    <div class="col-12">
                        <label class="text-muted small">Notes</label>
                        <div>{{ $license->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Usage Limits -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Usage & Limits
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small">Users</label>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                @php 
                                    $userPercent = $license->max_users > 0 
                                        ? min(100, ($usageLimits['users']['current'] / $license->max_users) * 100) 
                                        : 0;
                                @endphp
                                <div class="progress-bar {{ $userPercent > 90 ? 'bg-danger' : 'bg-primary' }}" 
                                     style="width: {{ $userPercent }}%"></div>
                            </div>
                            <span class="ms-3 {{ $usageLimits['users']['exceeded'] ? 'text-danger' : '' }}">
                                {{ $usageLimits['users']['current'] }} / {{ $license->max_users }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Items</label>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                @php 
                                    $itemPercent = $license->max_items > 0 
                                        ? min(100, ($usageLimits['items']['current'] / $license->max_items) * 100) 
                                        : 0;
                                @endphp
                                <div class="progress-bar {{ $itemPercent > 90 ? 'bg-danger' : 'bg-success' }}" 
                                     style="width: {{ $itemPercent }}%"></div>
                            </div>
                            <span class="ms-3 {{ $usageLimits['items']['exceeded'] ? 'text-danger' : '' }}">
                                {{ $usageLimits['items']['current'] }} / {{ $license->max_items }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <div class="stats-card text-center py-3">
                            <div class="text-muted small">Max Users</div>
                            <div class="fs-4 fw-bold">{{ $license->max_users }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card text-center py-3">
                            <div class="text-muted small">Max Items</div>
                            <div class="fs-4 fw-bold">{{ number_format($license->max_items) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card text-center py-3">
                            <div class="text-muted small">Max Transactions/Month</div>
                            <div class="fs-4 fw-bold">{{ number_format($license->max_transactions_per_month) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Logs -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Activity Log
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Performed By</th>
                                <th>IP Address</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($license->logs as $log)
                            <tr>
                                <td>
                                    <span class="badge {{ $log->action_badge_class }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                    @if($log->notes)
                                        <br><small class="text-muted">{{ $log->notes }}</small>
                                    @endif
                                </td>
                                <td>{{ $log->performer?->full_name ?? 'System' }}</td>
                                <td><small class="text-muted">{{ $log->ip_address ?? '-' }}</small></td>
                                <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No activity logs</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>Actions
                </h5>
            </div>
            <div class="card-body">
                <!-- Extend License -->
                <form action="{{ route('superadmin.licenses.extend', $license) }}" method="POST" class="mb-3">
                    @csrf
                    <label class="form-label">Extend License</label>
                    <div class="input-group">
                        <input type="number" name="days" class="form-control" placeholder="Days" 
                               value="30" min="1" max="3650">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-calendar-plus"></i>
                        </button>
                    </div>
                </form>

                <hr class="border-secondary">

                @if($license->isValid())
                    <form action="{{ route('superadmin.licenses.suspend', $license) }}" method="POST" class="mb-3">
                        @csrf
                        <label class="form-label">Suspend License</label>
                        <textarea name="reason" class="form-control mb-2" rows="2" 
                                  placeholder="Reason (optional)"></textarea>
                        <button type="submit" class="btn btn-warning w-100"
                                onclick="return confirm('Suspend this license?')">
                            <i class="fas fa-ban me-2"></i>Suspend
                        </button>
                    </form>
                @else
                    <form action="{{ route('superadmin.licenses.reactivate', $license) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-success w-100"
                                {{ $license->isExpired() ? 'disabled' : '' }}>
                            <i class="fas fa-check me-2"></i>Reactivate
                        </button>
                        @if($license->isExpired())
                            <small class="text-muted">Extend first to reactivate</small>
                        @endif
                    </form>
                @endif

                <hr class="border-secondary">

                <!-- Renew License -->
                <form action="{{ route('superadmin.licenses.renew', $license) }}" method="POST">
                    @csrf
                    <label class="form-label">Renew License</label>
                    <select name="plan_type" class="form-select mb-2">
                        <option value="trial" {{ $license->plan_type == 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="basic" {{ $license->plan_type == 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="standard" {{ $license->plan_type == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="premium" {{ $license->plan_type == 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="enterprise" {{ $license->plan_type == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                    <div class="input-group mb-2">
                        <input type="number" name="validity_days" class="form-control" 
                               placeholder="Days" value="30" min="1" max="3650">
                        <span class="input-group-text">days</span>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sync me-2"></i>Renew
                    </button>
                </form>

                <hr class="border-secondary">

                <!-- Regenerate Key -->
                <form action="{{ route('superadmin.licenses.regenerate', $license) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100"
                            onclick="return confirm('Generate a new key and revoke the current one?')">
                        <i class="fas fa-redo me-2"></i>Regenerate Key
                    </button>
                    <small class="text-muted">Current key will be revoked</small>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <a href="{{ route('superadmin.licenses.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left me-2"></i>Back to Licenses
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyLicenseKey() {
    const key = document.getElementById('licenseKey').textContent;
    navigator.clipboard.writeText(key).then(() => {
        alert('License key copied to clipboard!');
    });
}
</script>
@endpush
