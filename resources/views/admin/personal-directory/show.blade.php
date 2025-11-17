@extends('layouts.admin')

@section('title', 'Personal Directory Entry - ' . ($personalDirectory->name ?? 'View'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-person-lines-fill me-2"></i> {{ $personalDirectory->name ?? 'Personal Directory Entry' }}</h4>
    <div class="text-muted small">View complete entry details</div>
  </div>
  <div class="gap-2 d-flex">
    <a href="{{ route('admin.personal-directory.edit', $personalDirectory) }}" class="btn btn-warning">
      <i class="bi bi-pencil"></i> Edit
    </a>
    <a href="{{ route('admin.personal-directory.index') }}" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>
</div>

<!-- Basic Information -->
<div class="card mb-3 shadow-sm">
  <div class="card-header bg-primary text-white py-2">
    <h6 class="mb-0"><i class="bi bi-person-fill me-2"></i>Basic Information</h6>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Name</small>
        <div class="fw-semibold">{{ $personalDirectory->name ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Alt. Code</small>
        <div class="fw-semibold">{{ $personalDirectory->alt_code ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Status</small>
        <div class="fw-semibold">{{ $personalDirectory->status ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Contact Person</small>
        <div class="fw-semibold">{{ $personalDirectory->contact_person ?? '-' }}</div>
      </div>
    </div>
  </div>
</div>

<!-- Contact Information -->
<div class="card mb-3 shadow-sm">
  <div class="card-header bg-success text-white py-2">
    <h6 class="mb-0"><i class="bi bi-telephone-fill me-2"></i>Contact Information</h6>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Mobile</small>
        <div class="fw-semibold">{{ $personalDirectory->mobile ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Tel (O)</small>
        <div class="fw-semibold">{{ $personalDirectory->tel_office ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Tel (R)</small>
        <div class="fw-semibold">{{ $personalDirectory->tel_residence ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Fax</small>
        <div class="fw-semibold">{{ $personalDirectory->fax ?? '-' }}</div>
      </div>
      <div class="col-12">
        <small class="text-muted text-uppercase fw-semibold">Email</small>
        <div class="fw-semibold">{{ $personalDirectory->email ?? '-' }}</div>
      </div>
    </div>
  </div>
</div>

<!-- Address Information -->
<div class="card mb-3 shadow-sm">
  <div class="card-header bg-info text-white py-2">
    <h6 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Address Information</h6>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Office Address</small>
        <div class="fw-semibold" style="white-space: pre-wrap;">{{ $personalDirectory->address_office ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Residence Address</small>
        <div class="fw-semibold" style="white-space: pre-wrap;">{{ $personalDirectory->address_residence ?? '-' }}</div>
      </div>
    </div>
  </div>
</div>

<!-- Personal & Family Details -->
<div class="card mb-3 shadow-sm">
  <div class="card-header bg-warning text-dark py-2">
    <h6 class="mb-0"><i class="bi bi-heart-fill me-2"></i>Personal & Family Details</h6>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Birthday</small>
        <div class="fw-semibold">{{ $personalDirectory->birthday ? \Carbon\Carbon::parse($personalDirectory->birthday)->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Anniversary</small>
        <div class="fw-semibold">{{ $personalDirectory->anniversary ? \Carbon\Carbon::parse($personalDirectory->anniversary)->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Spouse</small>
        <div class="fw-semibold">{{ $personalDirectory->spouse ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Spouse DOB</small>
        <div class="fw-semibold">{{ $personalDirectory->spouse_dob ? \Carbon\Carbon::parse($personalDirectory->spouse_dob)->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Child I</small>
        <div class="fw-semibold">{{ $personalDirectory->child_1 ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Child I DOB</small>
        <div class="fw-semibold">{{ $personalDirectory->child_1_dob ? \Carbon\Carbon::parse($personalDirectory->child_1_dob)->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Child II</small>
        <div class="fw-semibold">{{ $personalDirectory->child_2 ?? '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Child II DOB</small>
        <div class="fw-semibold">{{ $personalDirectory->child_2_dob ? \Carbon\Carbon::parse($personalDirectory->child_2_dob)->format('d-M-Y') : '-' }}</div>
      </div>
    </div>
  </div>
</div>

<!-- Timestamps -->
<div class="card shadow-sm">
  <div class="card-header bg-secondary text-white py-2">
    <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Timestamps</h6>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Created At</small>
        <div class="fw-semibold">{{ $personalDirectory->created_at ? $personalDirectory->created_at->format('d-M-Y H:i:s') : '-' }}</div>
      </div>
      <div class="col-md-6">
        <small class="text-muted text-uppercase fw-semibold">Last Updated</small>
        <div class="fw-semibold">{{ $personalDirectory->updated_at ? $personalDirectory->updated_at->format('d-M-Y H:i:s') : '-' }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
