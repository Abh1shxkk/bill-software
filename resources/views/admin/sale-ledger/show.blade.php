@extends('layouts.admin')
@section('title','View Sale Ledger Entry')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-cart-check me-2"></i> Sale Ledger Entry Details</h4>
    <div class="text-muted small">View sale ledger entry information</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.sale-ledger.edit', $saleLedger) }}" class="btn btn-primary">
      <i class="bi bi-pencil me-1"></i> Edit
    </a>
    <a href="{{ route('admin.sale-ledger.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>
</div>

<!-- Ledger Information -->
<div class="card shadow-sm mb-3">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Ledger Information</h5>
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Ledger Name</label>
        <div class="fw-semibold">{{ $saleLedger->ledger_name ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Form Type</label>
        <div class="fw-semibold">{{ $saleLedger->form_type ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label class="text-muted small">Sale Tax</label>
        <div class="fw-semibold">₹{{ number_format($saleLedger->sale_tax ?? 0, 2) }}</div>
      </div>
      <div class="col-md-4">
        <label class="text-muted small">Type</label>
        <div class="fw-semibold">{{ $saleLedger->type ?? '-' }}</div>
      </div>
      <div class="col-md-4">
        <label class="text-muted small">Status</label>
        <div class="fw-semibold">{{ $saleLedger->status ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Alter Code</label>
        <div class="fw-semibold">{{ $saleLedger->alter_code ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Under</label>
        <div class="fw-semibold">{{ $saleLedger->under ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label class="text-muted small">Opening Balance</label>
        <div class="fw-semibold">₹{{ number_format($saleLedger->opening_balance ?? 0, 2) }}</div>
      </div>
      <div class="col-md-4">
        <label class="text-muted small">Form Required</label>
        <div class="fw-semibold">{{ $saleLedger->form_required ?? '-' }}</div>
      </div>
      <div class="col-md-4">
        <label class="text-muted small">Charges</label>
        <div class="fw-semibold">₹{{ number_format($saleLedger->charges ?? 0, 2) }}</div>
      </div>
    </div>

    @if($saleLedger->desc)
    <div class="row">
      <div class="col-12">
        <label class="text-muted small">Description</label>
        <div class="p-2 bg-light rounded" style="white-space: pre-wrap;">{{ $saleLedger->desc }}</div>
      </div>
    </div>
    @endif
  </div>
</div>

<!-- Contact Information -->
<div class="card shadow-sm mb-3">
  <div class="card-header bg-success text-white">
    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Contact Information</h5>
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Address</label>
        <div class="p-2 bg-light rounded" style="white-space: pre-wrap;">{{ $saleLedger->address ?? '-' }}</div>
      </div>
      <div class="col-md-3">
        <label class="text-muted small">Birth Day</label>
        <div class="fw-semibold">{{ $saleLedger->birth_day?->format('d M Y') ?? '-' }}</div>
      </div>
      <div class="col-md-3">
        <label class="text-muted small">Anniversary</label>
        <div class="fw-semibold">{{ $saleLedger->anniversary?->format('d M Y') ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3">
        <label class="text-muted small">Telephone</label>
        <div class="fw-semibold">{{ $saleLedger->telephone ?? '-' }}</div>
      </div>
      <div class="col-md-3">
        <label class="text-muted small">Fax</label>
        <div class="fw-semibold">{{ $saleLedger->fax ?? '-' }}</div>
      </div>
      <div class="col-md-3">
        <label class="text-muted small">Email</label>
        <div class="fw-semibold">{{ $saleLedger->email ?? '-' }}</div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="text-muted small">Contact 1</label>
        <div class="fw-semibold">{{ $saleLedger->contact_1 ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Mobile 1</label>
        <div class="fw-semibold">{{ $saleLedger->mobile_1 ?? '-' }}</div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label class="text-muted small">Contact 2</label>
        <div class="fw-semibold">{{ $saleLedger->contact_2 ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Mobile 2</label>
        <div class="fw-semibold">{{ $saleLedger->mobile_2 ?? '-' }}</div>
      </div>
    </div>
  </div>
</div>

<!-- Timestamps -->
<div class="card shadow-sm">
  <div class="card-header bg-secondary text-white">
    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Timestamps</h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="text-muted small">Created At</label>
        <div>{{ $saleLedger->created_at->format('d M Y, h:i A') }}</div>
      </div>
      <div class="col-md-6">
        <label class="text-muted small">Last Updated</label>
        <div>{{ $saleLedger->updated_at->format('d M Y, h:i A') }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
