@extends('layouts.admin')
@section('title', 'Purchase Return Voucher Details')
@section('content')
<style>
    .detail-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .detail-header { background: linear-gradient(135deg, #d69e2e, #b7791f); color: white; padding: 15px 20px; border-radius: 8px 8px 0 0; }
    .detail-body { padding: 20px; }
    .info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
    .info-item { background: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 3px solid #d69e2e; }
    .info-label { font-size: 11px; color: #666; text-transform: uppercase; margin-bottom: 4px; }
    .info-value { font-size: 14px; font-weight: 600; color: #333; }
    .items-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .items-table th { background: #d69e2e; color: white; padding: 10px; text-align: center; }
    .items-table td { padding: 8px 10px; border-bottom: 1px solid #eee; }
    .items-table tr:hover { background: #f5f5f5; }
    .totals-box { background: #fffff0; padding: 15px; border-radius: 6px; border: 1px solid #f6e05e; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-eye me-2"></i>Purchase Return Voucher Details</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.purchase-return-voucher.modification') }}?invoice_no={{ $voucher->pr_no }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.purchase-return-voucher.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="detail-card">
    <div class="detail-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>PR No #{{ $voucher->pr_no }}</h6>
            <span class="badge bg-light text-dark">{{ $voucher->return_date ? $voucher->return_date->format('d/m/Y') : '' }}</span>
        </div>
    </div>
    <div class="detail-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">PR No</div>
                <div class="info-value">{{ $voucher->pr_no }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Return Date</div>
                <div class="info-value">{{ $voucher->return_date ? $voucher->return_date->format('d/m/Y') : '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Supplier</div>
                <div class="info-value">{{ $voucher->supplier?->name ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value"><span class="badge bg-success">{{ ucfirst($voucher->status ?? 'Completed') }}</span></div>
            </div>
        </div>

        <h6 class="mb-3"><i class="bi bi-list-ul me-2"></i>Items</h6>
        <table class="items-table mb-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>HSN Code</th>
                    <th>Amount</th>
                    <th>GST %</th>
                    <th>CGST %</th>
                    <th>CGST Amt</th>
                    <th>SGST %</th>
                    <th>SGST Amt</th>
                    <th>Qty</th>
                    <th>Net Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($voucher->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->hsn_code ?? '-' }}</td>
                    <td class="text-end">₹{{ number_format($item->amount ?? 0, 2) }}</td>
                    <td class="text-center">{{ $item->gst_percent ?? 0 }}%</td>
                    <td class="text-center">{{ $item->cgst_percent ?? 0 }}%</td>
                    <td class="text-end">₹{{ number_format($item->cgst_amount ?? 0, 2) }}</td>
                    <td class="text-center">{{ $item->sgst_percent ?? 0 }}%</td>
                    <td class="text-end">₹{{ number_format($item->sgst_amount ?? 0, 2) }}</td>
                    <td class="text-center">{{ $item->qty ?? 0 }}</td>
                    <td class="text-end fw-bold">₹{{ number_format(($item->amount ?? 0) + ($item->cgst_amount ?? 0) + ($item->sgst_amount ?? 0), 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted">No items found</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="row">
            <div class="col-md-6 offset-md-6">
                <div class="totals-box">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Gross Amount:</span>
                        <strong>₹{{ number_format($voucher->nt_amount ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Tax:</span>
                        <strong>₹{{ number_format($voucher->tax_amount ?? 0, 2) }}</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="fs-5">Net Amount:</span>
                        <strong class="fs-5 text-warning">₹{{ number_format($voucher->net_amount ?? 0, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        @if($voucher->remarks)
        <div class="mt-3 p-3 bg-light rounded">
            <strong>Remarks:</strong> {{ $voucher->remarks }}
        </div>
        @endif
    </div>
</div>
@endsection
