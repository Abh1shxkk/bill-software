@extends('layouts.admin')
@section('title','View Cash/Bank Book')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <div class="text-muted small">Complete cash/bank book information</div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-cash-stack me-2"></i> {{ $cashBankBook->name }}</h4>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.cash-bank-books.edit', $cashBankBook) }}" class="btn btn-primary">
      <i class="bi bi-pencil me-1"></i>Edit Cash Book
    </a>
    <a href="{{ route('admin.cash-bank-books.index') }}" class="btn btn-light">
      <i class="bi bi-arrow-left me-1"></i>Back to Cash Books
    </a>
  </div>
</div>

<div class="row">
  <div class="col-12">

    <!-- Basic Information Section -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
      <div class="card-body p-4">
        <div class="section-card mb-5">
          <div class="section-header mb-4">
            <i class="bi bi-info-circle text-primary me-2"></i>
            <h5 class="mb-0">Basic Information</h5>
          </div>
          <div class="row g-4">
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Name:</label>
                <div class="fw-semibold text-dark">{{ $cashBankBook->name ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Alter Code:</label>
                <div class="fw-semibold">{{ $cashBankBook->alter_code ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Under:</label>
                <div class="fw-semibold">{{ $cashBankBook->under ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Opening Balance:</label>
                <div class="fw-semibold">₹{{ number_format($cashBankBook->opening_balance ?? 0, 2) }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Balance Type:</label>
                <div>
                  <span class="badge bg-info px-2 py-1 rounded-pill">{{ $cashBankBook->opening_balance_type ?? 'D' }}</span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Flag:</label>
                <div class="fw-semibold">{{ $cashBankBook->flag ?? '-' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Address & Contact Details Section -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
      <div class="card-body p-4">
        <div class="section-card mb-5">
          <div class="section-header mb-4">
            <i class="bi bi-geo-alt text-success me-2"></i>
            <h5 class="mb-0">Address & Contact Details</h5>
          </div>
          <div class="row g-4">
            <div class="col-12">
              <div class="detail-item">
                <label class="text-muted small mb-1">Address:</label>
                <div class="fw-semibold" style="white-space: pre-wrap;">{{ $cashBankBook->address ?? '-' }}</div>
              </div>
            </div>
            <div class="col-12">
              <div class="detail-item">
                <label class="text-muted small mb-1">Address 1:</label>
                <div class="fw-semibold" style="white-space: pre-wrap;">{{ $cashBankBook->address1 ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Telephone:</label>
                <div class="fw-semibold">{{ $cashBankBook->telephone ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">E-Mail:</label>
                <div class="fw-semibold">{{ $cashBankBook->email ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Fax:</label>
                <div class="fw-semibold">{{ $cashBankBook->fax ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="detail-item">
                <label class="text-muted small mb-1">B' Day:</label>
                <div class="fw-semibold">{{ $cashBankBook->birth_day ? $cashBankBook->birth_day->format('M d, Y') : '-' }}</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="detail-item">
                <label class="text-muted small mb-1">A' Day:</label>
                <div class="fw-semibold">{{ $cashBankBook->anniversary_day ? $cashBankBook->anniversary_day->format('M d, Y') : '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Contact Person I:</label>
                <div class="fw-semibold">{{ $cashBankBook->contact_person_1 ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Mobile 1:</label>
                <div class="fw-semibold">{{ $cashBankBook->mobile_1 ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Contact Person II:</label>
                <div class="fw-semibold">{{ $cashBankBook->contact_person_2 ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Mobile 2:</label>
                <div class="fw-semibold">{{ $cashBankBook->mobile_2 ?? '-' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Account Details Section -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
      <div class="card-body p-4">
        <div class="section-card mb-5">
          <div class="section-header mb-4">
            <i class="bi bi-bank text-info me-2"></i>
            <h5 class="mb-0">Account Details</h5>
          </div>
          <div class="row g-4">
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Account No:</label>
                <div class="fw-semibold">{{ $cashBankBook->account_no ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-item">
                <label class="text-muted small mb-1">Report No:</label>
                <div class="fw-semibold">{{ $cashBankBook->report_no ?? '-' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- GST & Cheque Settings Section -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
      <div class="card-body p-4">
        <div class="section-card">
          <div class="section-header mb-4">
            <i class="bi bi-receipt text-primary me-2"></i>
            <h5 class="mb-0">GST & Cheque Settings</h5>
          </div>
          <div class="detail-grid">
            <div class="detail-item">
              <label class="text-muted small mb-1">Input GST (Purchase):</label>
              <div>
                <span class="badge {{ $cashBankBook->input_gst_purchase ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                  {{ $cashBankBook->input_gst_purchase ? 'Yes' : 'No' }}
                </span>
              </div>
            </div>
            <div class="detail-item">
              <label class="text-muted small mb-1">Output GST (Income):</label>
              <div>
                <span class="badge {{ $cashBankBook->output_gst_income ? 'bg-success' : 'bg-secondary' }} px-2 py-1 rounded-pill">
                  {{ $cashBankBook->output_gst_income ? 'Yes' : 'No' }}
                </span>
              </div>
            </div>
            <div class="detail-item">
              <label class="text-muted small mb-1">Cheque Clearance Method:</label>
              <div class="fw-semibold">{{ $cashBankBook->cheque_clearance_method == 'P' ? 'Pis. No.' : 'Individual Cheques' }}</div>
            </div>
            <div class="detail-item">
              <label class="text-muted small mb-1">Receipts:</label>
              <div>
                <span class="badge {{ $cashBankBook->receipts == 'S' ? 'bg-primary' : 'bg-info' }} px-2 py-1 rounded-pill">
                  {{ $cashBankBook->receipts == 'S' ? 'Summary' : 'Individual' }}
                </span>
              </div>
            </div>
            <div class="detail-item">
              <label class="text-muted small mb-1">Credit Card:</label>
              <div class="fw-semibold">{{ $cashBankBook->credit_card ?? '-' }}</div>
            </div>
            <div class="detail-item">
              <label class="text-muted small mb-1">Bank Charges:</label>
              <div class="fw-semibold">₹{{ number_format($cashBankBook->bank_charges ?? 0, 2) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- System Information Section -->
    <div class="card shadow-sm border-0 rounded-lg">
      <div class="card-body p-4">
        <div class="section-header mb-4">
          <i class="bi bi-gear text-primary me-2"></i>
          <h5 class="mb-0">System Information</h5>
        </div>
        <div class="detail-grid">
          <div class="detail-item">
            <label class="text-muted small mb-1">Created Date:</label>
            <div class="fw-semibold">{{ $cashBankBook->created_at->format('M d, Y H:i:s') }}</div>
          </div>
          <div class="detail-item">
            <label class="text-muted small mb-1">Modified Date:</label>
            <div class="fw-semibold">{{ $cashBankBook->updated_at->format('M d, Y H:i:s') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .section-card {
    position: relative;
  }

  .section-header {
    display: flex;
    align-items: center;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e9ecef;
  }

  .detail-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f9fa;
  }

  .detail-item:last-child {
    border-bottom: none;
  }

  .detail-item label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    color: #6c757d;
  }

  .detail-item .fw-semibold {
    color: #2c3e50;
    font-size: 0.95rem;
  }

  .detail-grid .detail-item {
    padding: 0.5rem 0;
  }

  .rounded-lg {
    border-radius: 0.75rem !important;
  }
</style>
@endsection
