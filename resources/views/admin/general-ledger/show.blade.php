@extends('layouts.admin')
@section('title','View General Ledger Account')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-journal-text me-2"></i> General Ledger Account Details</h4>
    <div class="text-muted small">View ledger account information</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.general-ledger.edit', $generalLedger) }}" class="btn btn-primary">
      <i class="bi bi-pencil me-1"></i> Edit
    </a>
    <a href="{{ route('admin.general-ledger.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="card shadow-sm mb-3">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Account Information</h5>
      </div>
      <div class="card-body">
        <!-- Row 1: Name, Alter Code -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Name</label>
            <div class="fw-semibold">{{ $generalLedger->account_name }}</div>
          </div>
          <div class="col-md-6">
            <label class="text-muted small">Alter Code</label>
            <div class="fw-semibold">{{ $generalLedger->alter_code ?? '-' }}</div>
          </div>
        </div>

        <!-- Row 2: Under, Opening Balance, Dr/Cr -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Under</label>
            <div class="fw-semibold">{{ $generalLedger->under ?? '-' }}</div>
          </div>
          <div class="col-md-3">
            <label class="text-muted small">Opening Bal.</label>
            <div class="fw-semibold fs-5 text-primary">₹{{ number_format($generalLedger->opening_balance ?? 0, 2) }}</div>
          </div>
          <div class="col-md-3">
            <label class="text-muted small">Dr / Cr</label>
            <div><span class="badge {{ $generalLedger->balance_type == 'D' ? 'bg-danger' : 'bg-success' }}">
              {{ $generalLedger->balance_type == 'D' ? 'Dr' : 'Cr' }}
            </span></div>
          </div>
        </div>

        <!-- Row 3: GST Flags -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Input GST(Purchase)</label>
            <div>
              @if($generalLedger->input_gst_purchase)
                <span class="badge bg-success">Yes</span>
              @else
                <span class="badge bg-secondary">No</span>
              @endif
            </div>
          </div>
          <div class="col-md-6">
            <label class="text-muted small">Output GST (Income)</label>
            <div>
              @if($generalLedger->output_gst_income)
                <span class="badge bg-success">Yes</span>
              @else
                <span class="badge bg-secondary">No</span>
              @endif
            </div>
          </div>
        </div>

        <!-- Row 4: Flag -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Flag</label>
            <div class="fw-semibold">{{ $generalLedger->flag ?? '-' }}</div>
          </div>
        </div>

        <!-- Row 5: Address -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Address</label>
            <div style="white-space: pre-wrap;">{{ $generalLedger->address ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <label class="text-muted small">Address Line 2</label>
            <div class="fw-semibold">{{ $generalLedger->address_line2 ?? '-' }}</div>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Address Line 3</label>
            <div class="fw-semibold">{{ $generalLedger->address_line3 ?? '-' }}</div>
          </div>
          <div class="col-md-3">
            <label class="text-muted small">B' Day</label>
            <div class="fw-semibold">{{ $generalLedger->birth_day ?? '-' }}</div>
          </div>
          <div class="col-md-3">
            <label class="text-muted small">A' Day</label>
            <div class="fw-semibold">{{ $generalLedger->anniversary_day ?? '-' }}</div>
          </div>
        </div>

        <!-- Row 6: Contact Persons & Mobiles -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Contact Person I</label>
            <div class="fw-semibold">{{ $generalLedger->contact_person_1 ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <label class="text-muted small">Mobile 1</label>
            <div class="fw-semibold">{{ $generalLedger->mobile_1 ?? '-' }}</div>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Contact Person II</label>
            <div class="fw-semibold">{{ $generalLedger->contact_person_2 ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <label class="text-muted small">Mobile 2</label>
            <div class="fw-semibold">{{ $generalLedger->mobile_2 ?? '-' }}</div>
          </div>
        </div>

        <!-- Row 7: Contact Details -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Telephone</label>
            <div class="fw-semibold">{{ $generalLedger->telephone ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <label class="text-muted small">E-Mail</label>
            <div class="fw-semibold">{{ $generalLedger->email ?? '-' }}</div>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Fax</label>
            <div class="fw-semibold">{{ $generalLedger->fax ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <label class="text-muted small">Mobile</label>
            <div class="fw-semibold">{{ $generalLedger->mobile ?? '-' }}</div>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="text-muted small">Mobile (Additional)</label>
            <div class="fw-semibold">{{ $generalLedger->mobile_additional ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Timestamps</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="text-muted small">Created At</label>
          <div>{{ $generalLedger->created_at->format('d M Y, h:i A') }}</div>
        </div>
        <div>
          <label class="text-muted small">Last Updated</label>
          <div>{{ $generalLedger->updated_at->format('d M Y, h:i A') }}</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
