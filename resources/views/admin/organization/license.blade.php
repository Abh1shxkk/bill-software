@extends('layouts.admin')

@section('title', 'License Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Current License -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-key me-2"></i>Current License
                    </h5>
                    <a href="{{ route('admin.organization.settings') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="card-body">
                    @if($license)
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="text-muted small">License Key</label>
                                <div class="input-group">
                                    <input type="text" class="form-control font-monospace" 
                                           value="{{ $license->license_key }}" id="licenseKey" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyKey()">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <label class="text-muted small d-block">Status</label>
                                @if($license->isValid())
                                    <span class="badge bg-success fs-6">Active</span>
                                @elseif($license->isExpired())
                                    <span class="badge bg-danger fs-6">Expired</span>
                                @else
                                    <span class="badge bg-warning fs-6">Suspended</span>
                                @endif
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="text-muted small">Plan</label>
                                <div class="fw-bold text-capitalize">{{ $license->plan_type }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Start Date</label>
                                <div>{{ $license->starts_at?->format('d M Y') ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Expiry Date</label>
                                <div class="{{ $license->isExpiringSoon() ? 'text-warning fw-bold' : '' }} {{ $license->isExpired() ? 'text-danger' : '' }}">
                                    {{ $license->expires_at?->format('d M Y') ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small">Days Remaining</label>
                                <div class="fw-bold {{ $license->daysUntilExpiry() <= 7 ? 'text-warning' : '' }} {{ $license->isExpired() ? 'text-danger' : '' }}">
                                    {{ $license->isExpired() ? 'Expired' : $license->daysUntilExpiry() . ' days' }}
                                </div>
                            </div>
                        </div>

                        @if($license->isExpiringSoon())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Your license is expiring soon!</strong>
                            Please renew your license to continue using MediBill without interruption.
                        </div>
                        @endif

                        @if($license->isExpired())
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle me-2"></i>
                            <strong>Your license has expired!</strong>
                            Some features may be restricted. Please renew your license immediately.
                        </div>
                        @endif

                        <!-- Usage Limits -->
                        @if($usageLimits)
                        <h6 class="mb-3 mt-4">Usage Limits</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="text-muted small">Users</div>
                                        <div class="h4 mb-1 {{ $usageLimits['users']['exceeded'] ? 'text-danger' : '' }}">
                                            {{ $usageLimits['users']['current'] }} / {{ $license->max_users }}
                                        </div>
                                        <div class="progress" style="height: 8px;">
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
                                        <div class="h4 mb-1 {{ $usageLimits['items']['exceeded'] ? 'text-danger' : '' }}">
                                            {{ number_format($usageLimits['items']['current']) }} / {{ number_format($license->max_items) }}
                                        </div>
                                        <div class="progress" style="height: 8px;">
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
                                        <div class="h4 mb-1">{{ number_format($license->max_transactions_per_month) }}</div>
                                        <small class="text-muted">Maximum allowed</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-key text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">No Active License</h5>
                            <p class="text-muted">Your organization doesn't have an active license.</p>
                            <a href="{{ route('license.required') }}" class="btn btn-primary">
                                <i class="bi bi-unlock me-1"></i>Activate License
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- License History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>License History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>License Key</th>
                                    <th>Plan</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($licenseHistory as $lic)
                                <tr class="{{ $lic->id === $license?->id ? 'table-primary' : '' }}">
                                    <td>
                                        <code>{{ Str::limit($lic->license_key, 15, '...') }}</code>
                                        @if($lic->id === $license?->id)
                                            <span class="badge bg-primary ms-1">Current</span>
                                        @endif
                                    </td>
                                    <td class="text-capitalize">{{ $lic->plan_type }}</td>
                                    <td>
                                        {{ $lic->starts_at?->format('d M Y') ?? 'N/A' }} - 
                                        {{ $lic->expires_at?->format('d M Y') ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if($lic->isValid())
                                            <span class="badge bg-success">Active</span>
                                        @elseif($lic->isExpired())
                                            <span class="badge bg-secondary">Expired</span>
                                        @else
                                            <span class="badge bg-warning">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">No license history</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Renewal Request -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i>Request Renewal</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Need to extend your license or upgrade your plan? Submit a renewal request and our team will contact you.
                    </p>

                    <form action="{{ route('admin.organization.request-renewal') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Message (Optional)</label>
                            <textarea name="message" class="form-control" rows="4" 
                                      placeholder="Tell us about your requirements..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send me-1"></i>Submit Request
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-headset me-2"></i>Support</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Need help with your license? Contact our support team.
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-envelope me-2"></i>
                        <a href="mailto:support@medibill.com">support@medibill.com</a>
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-phone me-2"></i>
                        <a href="tel:+911234567890">+91 1234-567-890</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyKey() {
    const keyInput = document.getElementById('licenseKey');
    keyInput.select();
    document.execCommand('copy');
    alert('License key copied to clipboard!');
}
</script>
@endsection
