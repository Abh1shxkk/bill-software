@extends('layouts.admin')

@section('title', 'Batch Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-eye me-2"></i> Batch Details</h4>
        <div class="text-muted small">View complete batch information</div>
    </div>
    <div>
        <a href="{{ route('admin.batches.edit', $batch->id) }}" class="btn btn-primary btn-sm me-2">
            <i class="bi bi-pencil"></i> Edit Batch
        </a>
        <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-md-6">
        <!-- Basic Information Card -->
        <div class="card shadow-sm border-0 rounded mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Item Name:</td>
                        <td><strong>{{ $batch->item_name ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Item Code:</td>
                        <td>{{ $batch->item_code ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Batch Number:</td>
                        <td><span class="badge bg-info">{{ $batch->batch_no ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Company:</td>
                        <td>{{ $batch->company_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Packing:</td>
                        <td>{{ $batch->packing ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Unit:</td>
                        <td>{{ $batch->unit ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Dates Card -->
        <div class="card shadow-sm border-0 rounded mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Dates</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Expiry Date:</td>
                        <td>
                            @if($batch->expiry_date)
                                <span class="badge {{ $batch->isExpired() ? 'bg-danger' : ($batch->isExpiringsoon() ? 'bg-warning' : 'bg-success') }}">
                                    {{ $batch->expiry_date->format('d M, Y') }} ({{ $batch->expiry_date->format('m/y') }})
                                </span>
                                @if($batch->isExpired())
                                    <small class="text-danger ms-2">Expired</small>
                                @elseif($batch->isExpiringsoon())
                                    <small class="text-warning ms-2">Expiring Soon ({{ $batch->daysUntilExpiry() }} days)</small>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Manufacturing Date:</td>
                        <td>{{ $batch->manufacturing_date ? $batch->manufacturing_date->format('d M, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">H/B/E Status:</td>
                        <td>
                            @if($batch->hold_breakage_expiry)
                                @if($batch->hold_breakage_expiry == 'H')
                                    <span class="badge bg-warning">Hold</span>
                                @elseif($batch->hold_breakage_expiry == 'B')
                                    <span class="badge bg-danger">Breakage</span>
                                @elseif($batch->hold_breakage_expiry == 'E')
                                    <span class="badge bg-dark">Expiry</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Created At:</td>
                        <td>{{ $batch->created_at->format('d M, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Last Updated:</td>
                        <td>{{ $batch->updated_at->format('d M, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Quantities Card -->
        <div class="card shadow-sm border-0 rounded mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-boxes me-2"></i>Quantities</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Quantity:</td>
                        <td><strong class="text-primary">{{ number_format($batch->qty, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Free Quantity:</td>
                        <td>{{ number_format($batch->free_qty, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Quantity:</td>
                        <td><strong class="text-success">{{ number_format($batch->total_qty, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-md-6">
        <!-- Pricing Card -->
        <div class="card shadow-sm border-0 rounded mb-3">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>Pricing Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Purchase Rate:</td>
                        <td><strong class="text-primary">₹{{ number_format($batch->pur_rate, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sale Rate:</td>
                        <td>₹{{ number_format($batch->s_rate, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">MRP:</td>
                        <td>₹{{ number_format($batch->mrp, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Wholesale Rate:</td>
                        <td>₹{{ number_format($batch->ws_rate, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Special Rate:</td>
                        <td>₹{{ number_format($batch->spl_rate, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">N.Rate:</td>
                        <td>₹{{ number_format($batch->n_rate, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sale Scheme:</td>
                        <td><span class="badge bg-info">{{ $batch->sale_scheme ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Inc. Flag:</td>
                        <td><span class="badge {{ $batch->inc == 'Y' ? 'bg-success' : 'bg-secondary' }}">{{ $batch->inc ?? 'Y' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">BC Flag:</td>
                        <td><span class="badge {{ $batch->bc == 'Y' ? 'bg-success' : 'bg-secondary' }}">{{ $batch->bc ?? 'N' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cost Price:</td>
                        <td>₹{{ number_format($batch->cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cost with GST:</td>
                        <td>₹{{ number_format($batch->cost_gst, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cost WFQ:</td>
                        <td>₹{{ number_format($batch->cost_wfq, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Rate Difference:</td>
                        <td><strong class="{{ $batch->rate_diff >= 0 ? 'text-success' : 'text-danger' }}">₹{{ number_format($batch->rate_diff, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Discount & Amounts Card -->
        <div class="card shadow-sm border-0 rounded mb-3">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>Discount & Amounts</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Discount %:</td>
                        <td>{{ number_format($batch->dis_percent, 2) }}%</td>
                    </tr>
                    <tr>
                        <td class="text-muted">S.C. Amount (Rs):</td>
                        <td>₹{{ number_format($batch->sc_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Amount (Before Tax):</td>
                        <td>₹{{ number_format($batch->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tax Amount:</td>
                        <td>₹{{ number_format($batch->tax_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Net Amount:</td>
                        <td><strong class="text-success">₹{{ number_format($batch->net_amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- GST Details Card -->
        <div class="card shadow-sm border-0 rounded mb-3">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>GST Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">CGST %:</td>
                        <td>{{ number_format($batch->cgst_percent, 2) }}%</td>
                    </tr>
                    <tr>
                        <td class="text-muted">CGST Amount:</td>
                        <td>₹{{ number_format($batch->cgst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">SGST %:</td>
                        <td>{{ number_format($batch->sgst_percent, 2) }}%</td>
                    </tr>
                    <tr>
                        <td class="text-muted">SGST Amount:</td>
                        <td>₹{{ number_format($batch->sgst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">CESS %:</td>
                        <td>{{ number_format($batch->cess_percent, 2) }}%</td>
                    </tr>
                    <tr>
                        <td class="text-muted">CESS Amount:</td>
                        <td>₹{{ number_format($batch->cess_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Total GST %:</strong></td>
                        <td><strong>{{ number_format($batch->cgst_percent + $batch->sgst_percent, 2) }}%</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>GST PTS:</strong></td>
                        <td><strong class="text-danger">₹{{ number_format($batch->gst_pts, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Transaction Details -->
<div class="card shadow-sm border-0 rounded mb-3">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="bi bi-cart-check me-2"></i>Purchase Transaction Details</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <p class="text-muted mb-1">Invoice Number:</p>
                <p class="mb-0"><strong>{{ $batch->transaction->bill_no ?? 'N/A' }}</strong></p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1">Invoice Date:</p>
                <p class="mb-0">{{ $batch->transaction->bill_date ? $batch->transaction->bill_date->format('d M, Y') : 'N/A' }}</p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1">Supplier:</p>
                <p class="mb-0">{{ $batch->transaction->supplier->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1">Transaction ID:</p>
                <p class="mb-0">
                    @if($batch->transaction)
                        <a href="{{ route('admin.purchase.transactions.show', $batch->transaction->id) }}" class="btn btn-sm btn-outline-primary">
                            View Transaction #{{ $batch->transaction->trn_no }}
                        </a>
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Additional Information -->
<div class="card shadow-sm border-0 rounded mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-info-square me-2"></i>Additional Information</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p class="text-muted mb-1">Godown/Location:</p>
                <p class="mb-0">{{ $batch->godown ?? 'N/A' }}</p>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1">Status:</p>
                <p class="mb-0">
                    <span class="badge {{ $batch->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($batch->status) }}
                    </span>
                </p>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1">Batch ID:</p>
                <p class="mb-0">#{{ $batch->id }}</p>
            </div>
        </div>
        @if($batch->remarks)
            <div class="mt-3">
                <p class="text-muted mb-1">Remarks:</p>
                <p class="mb-0">{{ $batch->remarks }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('admin.batches.edit', $batch->id) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit Batch
    </a>
    <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

@endsection

@push('styles')
<style>
.card-header {
    font-weight: 600;
}
.table-borderless td {
    padding: 0.5rem 0;
}
</style>
@endpush

