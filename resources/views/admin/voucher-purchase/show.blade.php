@extends('layouts.admin')

@section('title', 'View Voucher Purchase #' . $voucher->voucher_no)

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Voucher Purchase #{{ $voucher->voucher_no }}</h5>
        <a href="{{ route('admin.voucher-purchase.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th width="150">Voucher No:</th><td>{{ $voucher->voucher_no }}</td></tr>
                    <tr><th>Voucher Date:</th><td>{{ $voucher->voucher_date?->format('d-M-Y') }}</td></tr>
                    <tr><th>Bill No:</th><td>{{ $voucher->bill_no }}</td></tr>
                    <tr><th>Bill Date:</th><td>{{ $voucher->bill_date?->format('d-M-Y') }}</td></tr>
                    <tr><th>Local/Inter:</th><td>{{ $voucher->local_inter == 'L' ? 'Local' : 'Inter-State' }}</td></tr>
                    <tr><th>RCM:</th><td>{{ $voucher->rcm }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th width="150">Supplier:</th><td>{{ $voucher->supplier_name }}</td></tr>
                    <tr><th>GST No:</th><td>{{ $voucher->gst_no }}</td></tr>
                    <tr><th>PAN No:</th><td>{{ $voucher->pan_no }}</td></tr>
                    <tr><th>City:</th><td>{{ $voucher->city }}</td></tr>
                    <tr><th>PIN:</th><td>{{ $voucher->pin }}</td></tr>
                    <tr><th>Status:</th><td><span class="badge bg-{{ $voucher->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($voucher->status) }}</span></td></tr>
                </table>
            </div>
        </div>

        @if($voucher->description)
        <div class="mb-3">
            <strong>Description:</strong> {{ $voucher->description }}
        </div>
        @endif

        <h6 class="mt-4">HSN Items</h6>
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>HSN Code</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">GST%</th>
                    <th class="text-end">CGST%</th>
                    <th class="text-end">CGST Amt</th>
                    <th class="text-end">SGST%</th>
                    <th class="text-end">SGST Amt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voucher->items as $item)
                <tr>
                    <td>{{ $item->hsn_code }}</td>
                    <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                    <td class="text-end">{{ number_format($item->gst_percent, 2) }}</td>
                    <td class="text-end">{{ number_format($item->cgst_percent, 2) }}</td>
                    <td class="text-end">{{ number_format($item->cgst_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($item->sgst_percent, 2) }}</td>
                    <td class="text-end">{{ number_format($item->sgst_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white py-2">Debit</div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr><td>Amount:</td><td class="text-end">{{ number_format($voucher->amount, 2) }}</td></tr>
                            <tr><td>Total GST:</td><td class="text-end">{{ number_format($voucher->total_gst, 2) }}</td></tr>
                            <tr><td>Net Amount:</td><td class="text-end">{{ number_format($voucher->net_amount, 2) }}</td></tr>
                            <tr><td>Round Off:</td><td class="text-end">{{ number_format($voucher->round_off, 2) }}</td></tr>
                            <tr class="table-danger"><th>Total Debit:</th><th class="text-end">{{ number_format($voucher->total_debit, 2) }}</th></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white py-2">Credit</div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr><td>TDS @ {{ $voucher->tds_percent }}%:</td><td class="text-end">{{ number_format($voucher->tds_amount, 2) }}</td></tr>
                            <tr><td>Account:</td><td class="text-end">{{ $voucher->credit_account_name }}</td></tr>
                            <tr><td>Cheque No:</td><td class="text-end">{{ $voucher->cheque_no }}</td></tr>
                            <tr class="table-success"><th>Total Credit:</th><th class="text-end">{{ number_format($voucher->total_credit, 2) }}</th></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h6>GST Summary</h6>
                <table class="table table-sm table-bordered">
                    <tr><td>Total CGST:</td><td class="text-end">{{ number_format($voucher->total_cgst_amount, 2) }}</td></tr>
                    <tr><td>Total SGST:</td><td class="text-end">{{ number_format($voucher->total_sgst_amount, 2) }}</td></tr>
                    <tr><td>Total IGST:</td><td class="text-end">{{ number_format($voucher->total_igst_amount, 2) }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
