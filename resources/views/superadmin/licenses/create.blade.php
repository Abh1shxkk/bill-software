@extends('superadmin.layouts.app')

@section('title', 'Generate License')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Super Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('superadmin.licenses.index') }}">Licenses</a></li>
        <li class="breadcrumb-item active">Generate</li>
    </ol>
</nav>
<h1 class="page-title">Generate License</h1>
@endsection

@section('content')
<form action="{{ route('superadmin.licenses.store') }}" method="POST">
    @csrf
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-key me-2"></i>License Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Organization <span class="text-danger">*</span></label>
                            <select name="organization_id" class="form-select @error('organization_id') is-invalid @enderror" required>
                                <option value="">Select Organization</option>
                                @foreach($organizations as $org)
                                <option value="{{ $org->id }}" 
                                    {{ old('organization_id', $selectedOrg?->id) == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }} ({{ $org->code }})
                                </option>
                                @endforeach
                            </select>
                            @error('organization_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Plan Type <span class="text-danger">*</span></label>
                            <select name="plan_type" class="form-select @error('plan_type') is-invalid @enderror" 
                                    required id="planType">
                                @foreach($plans as $plan)
                                <option value="{{ $plan->code }}" 
                                        data-users="{{ $plan->max_users }}"
                                        data-items="{{ $plan->max_items }}"
                                        data-days="{{ $plan->validity_days }}"
                                    {{ old('plan_type') == $plan->code ? 'selected' : '' }}>
                                    {{ $plan->name }} ({{ $plan->formatted_monthly_price }}/mo)
                                </option>
                                @endforeach
                            </select>
                            @error('plan_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Validity (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="validity_days" id="validityDays"
                                   class="form-control @error('validity_days') is-invalid @enderror" 
                                   value="{{ old('validity_days', 30) }}" min="1" max="3650" required>
                            @error('validity_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Max Users</label>
                            <input type="number" name="max_users" id="maxUsers" class="form-control" 
                                   value="{{ old('max_users', 5) }}" min="1">
                            <small class="text-muted">Leave empty to use plan default</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Max Items</label>
                            <input type="number" name="max_items" id="maxItems" class="form-control" 
                                   value="{{ old('max_items', 1000) }}" min="1">
                            <small class="text-muted">Leave empty to use plan default</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" 
                                      placeholder="Optional notes about this license...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>License Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info small mb-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        A unique license key will be automatically generated upon creation.
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">License Start</label>
                        <div class="fw-bold">Today ({{ now()->format('d M Y') }})</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">License Expiry</label>
                        <div class="fw-bold" id="expiryDate">
                            {{ now()->addDays(30)->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>

            @if($selectedOrg)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-building me-2"></i>Selected Organization
                    </h5>
                </div>
                <div class="card-body">
                    <h6>{{ $selectedOrg->name }}</h6>
                    <p class="text-muted small mb-1">Code: {{ $selectedOrg->code }}</p>
                    <p class="text-muted small mb-0">
                        Status: 
                        <span class="badge badge-status badge-{{ $selectedOrg->status }}">
                            {{ ucfirst($selectedOrg->status) }}
                        </span>
                    </p>
                </div>
            </div>
            @endif

            <div class="card mt-4">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-key me-2"></i>Generate License
                    </button>
                    <a href="{{ route('superadmin.licenses.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('planType');
    const validityInput = document.getElementById('validityDays');
    const maxUsersInput = document.getElementById('maxUsers');
    const maxItemsInput = document.getElementById('maxItems');
    const expiryDateEl = document.getElementById('expiryDate');

    function updateExpiry() {
        const days = parseInt(validityInput.value) || 30;
        const expiry = new Date();
        expiry.setDate(expiry.getDate() + days);
        expiryDateEl.textContent = expiry.toLocaleDateString('en-GB', { 
            day: 'numeric', 
            month: 'short', 
            year: 'numeric' 
        });
    }

    planSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        validityInput.value = option.dataset.days || 30;
        maxUsersInput.value = option.dataset.users || 5;
        maxItemsInput.value = option.dataset.items || 1000;
        updateExpiry();
    });

    validityInput.addEventListener('input', updateExpiry);
});
</script>
@endpush
