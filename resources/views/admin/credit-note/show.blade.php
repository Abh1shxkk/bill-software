@extends('layouts.admin')

@section('title', 'View Credit Note')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-file-earmark-minus me-2"></i> Credit Note Details</h4>
        <div class="text-muted small">CN No: {{ $creditNote->credit_note_no }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.credit-note.invoices') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('admin.credit-note.modification') }}?credit_note_no={{ $creditNote->credit_note_no }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print
        </button>
    </div>
</div>

<div class="row">
    <!-- Credit Note Info -->
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Credit Note Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">CN No:</td>
                        <td><strong>{{ $creditNote->credit_note_no }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Date:</td>
                        <td>{{ $creditNote->credit_note_date ? $creditNote->credit_note_date->format('d-m-Y') : 'N/A' }} ({{ $creditNote->day_name ?? '' }})</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Reason:</td>
                        <td>{{ $creditNote->reason ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status:</td>
                        <td>
                            @if($creditNote->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($creditNote->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Inv. Ref. No:</td>
                        <td>{{ $creditNote->inv_ref_no ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Invoice Date:</td>
                        <td>{{ $creditNote->invoice_date ? $creditNote->invoice_date->format('d-m-Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">GST Vno:</td>
                        <td>{{ $creditNote->gst_vno ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Party Info -->
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i> Party Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Party Type:</td>
                        <td>
                            @if($creditNote->credit_party_type == 'S')
                                <span class="badge bg-info">Supplier</span>
                            @else
                                <span class="badge bg-warning text-dark">Customer</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Party Name:</td>
                        <td><strong>{{ $creditNote->credit_party_name ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Salesman:</td>
                        <td>{{ $creditNote->salesman->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Debit Account:</td>
                        <td>
                            @if($creditNote->debit_account_type == 'P')
                                Purchase
                            @elseif($creditNote->debit_account_type == 'S')
                                Sale
                            @else
                                General
                            @endif
                            {{ $creditNote->debit_account_no ? '('.$creditNote->debit_account_no.')' : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Party Trn. No:</td>
                        <td>{{ $creditNote->party_trn_no ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Party Trn. Date:</td>
                        <td>{{ $creditNote->party_trn_date ? $creditNote->party_trn_date->format('d-m-Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- HSN Items Table -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0"><i class="bi bi-table me-2"></i> HSN Details ({{ $creditNote->items->count() }} items)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0" style="font-size: 12px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>HSN Code</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">GST%</th>
                        <th class="text-center">CGST%</th>
                        <th class="text-end">CGST Amt</th>
                        <th class="text-center">SGST%</th>
                        <th class="text-end">SGST Amt</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($creditNote->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->hsn_code ?? '-' }}</td>
                        <td class="text-end">₹ {{ number_format($item->amount, 2) }}</td>
                        <td class="text-center">{{ number_format($item->gst_percent, 2) }}%</td>
                        <td class="text-center">{{ number_format($item->cgst_percent, 2) }}%</td>
                        <td class="text-end">₹ {{ number_format($item->cgst_amount, 2) }}</td>
                        <td class="text-center">{{ number_format($item->sgst_percent, 2) }}%</td>
                        <td class="text-end">₹ {{ number_format($item->sgst_amount, 2) }}</td>
                        <td class="text-end"><strong>₹ {{ number_format($item->amount + $item->cgst_amount + $item->sgst_amount, 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No items found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Summary & Narration -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="bi bi-chat-text me-2"></i> Narration</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $creditNote->narration ?? 'No narration provided' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="bi bi-calculator me-2"></i> Summary</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Gross Amount:</td>
                        <td class="text-end">₹ {{ number_format($creditNote->gross_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total GST:</td>
                        <td class="text-end">₹ {{ number_format($creditNote->total_gst ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Net Amount:</td>
                        <td class="text-end">₹ {{ number_format($creditNote->net_amount ?? 0, 2) }}</td>
                    </tr>
                    @if($creditNote->tcs_amount > 0)
                    <tr>
                        <td class="text-muted">TCS:</td>
                        <td class="text-end">₹ {{ number_format($creditNote->tcs_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($creditNote->round_off != 0)
                    <tr>
                        <td class="text-muted">Round Off:</td>
                        <td class="text-end">₹ {{ number_format($creditNote->round_off, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-top">
                        <td><strong>CN Amount:</strong></td>
                        <td class="text-end"><strong class="text-success fs-5">₹ {{ number_format($creditNote->cn_amount ?? 0, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
@media print {
    .btn, .d-flex.gap-2 { display: none !important; }
    .card { border: 1px solid #ddd !important; }
}
</style>
@endpush
