@extends('layouts.admin')
@section('title','View Purchase Ledger Entry')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-bag-check me-2"></i> Purchase Ledger Entry Details</h4>
    <div class="text-muted small">View purchase ledger entry information</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.purchase-ledger.edit', $purchaseLedger) }}" class="btn btn-primary">
      <i class="bi bi-pencil me-1"></i> Edit
    </a>
    <a href="{{ route('admin.purchase-ledger.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>
</div>

<!-- Ledger Information Section -->
<div class="card shadow-sm mb-3">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Ledger Information</h5>
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Ledger Name</label>
        <div class="fw-semibold">{{ $purchaseLedger->ledger_name ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Form Type</label>
        <div class="fw-semibold">{{ $purchaseLedger->form_type ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Sale Tax</label>
        <div class="fw-semibold">{{ number_format($purchaseLedger->sale_tax ?? 0, 2) }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Description</label>
        <div class="fw-semibold">{{ $purchaseLedger->desc ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3">
        <label class="text-muted small">Type</label>
        <div><span class="badge bg-light text-dark">{{ $purchaseLedger->type ?? '-' }}</span></div>
      </div>
      <div class="col-md-3">
        <label class="text-muted small">Status</label>
        <div class="fw-semibold">{{ $purchaseLedger->status ?? '-' }}</div>
      </div>
      <div class="col-md-3">
        <label class="text-muted small">Alter Code</label>
        <div class="fw-semibold">{{ $purchaseLedger->alter_code ?? '-' }}</div>
      </div>
      <div class="col-md-3">
        <label class="text-muted small">Opening Balance</label>
        <div class="fw-semibold">₹{{ number_format($purchaseLedger->opening_balance ?? 0, 2) }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3">
        <label class="text-muted small">Form Required</label>
        <div><span class="badge bg-info">{{ $purchaseLedger->form_required ?? 'N' }}</span></div>
      </div>
      <div class="col-md-3">
        <label class="text-muted small">Charges</label>
        <div class="fw-semibold">₹{{ number_format($purchaseLedger->charges ?? 0, 2) }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Under</label>
        <div class="fw-semibold">{{ $purchaseLedger->under ?? '-' }}</div>
      </div>
    </div>
  </div>
</div>

<!-- Contact Information Section -->
<div class="card shadow-sm mb-3">
  <div class="card-header bg-success text-white">
    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Contact Information</h5>
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-12">
        <label class="text-muted small">Address</label>
        <div class="p-2 bg-light rounded" style="white-space: pre-wrap;">{{ $purchaseLedger->address ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Birth Day</label>
        <div class="fw-semibold">{{ $purchaseLedger->birth_day ? $purchaseLedger->birth_day->format('d M Y') : '-' }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Anniversary</label>
        <div class="fw-semibold">{{ $purchaseLedger->anniversary ? $purchaseLedger->anniversary->format('d M Y') : '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Telephone</label>
        <div class="fw-semibold">{{ $purchaseLedger->telephone ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Fax</label>
        <div class="fw-semibold">{{ $purchaseLedger->fax ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-12">
        <label class="text-muted small">Email</label>
        <div class="fw-semibold">{{ $purchaseLedger->email ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Contact 1</label>
        <div class="fw-semibold">{{ $purchaseLedger->contact_1 ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Mobile 1</label>
        <div class="fw-semibold">{{ $purchaseLedger->mobile_1 ?? '-' }}</div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label class="text-muted small">Contact 2</label>
        <div class="fw-semibold">{{ $purchaseLedger->contact_2 ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Mobile 2</label>
        <div class="fw-semibold">{{ $purchaseLedger->mobile_2 ?? '-' }}</div>
      </div>
    </div>
  </div>
</div>

<!-- Timestamps Section -->
<div class="card shadow-sm">
  <div class="card-header bg-secondary text-white">
    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Timestamps</h5>
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Created At</label>
        <div>{{ $purchaseLedger->created_at->format('d M Y, h:i A') }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Last Updated</label>
        <div>{{ $purchaseLedger->updated_at->format('d M Y, h:i A') }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
