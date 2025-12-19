@extends('layouts.admin')

@section('title', 'Voucher Income #' . $voucher->voucher_no)

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Voucher Income #{{ $voucher->voucher_no }}</h5>
        <a href="{{ route('admin.voucher-income.index') }}" class="btn btn-light btn-sm">Back to List</a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Date:</strong> {{ $voucher->voucher_date?->format('d-M-Y') }}</p>
                <p><strong>Customer:</strong> {{ $voucher->customer_name }}</p>
                <p><strong>GST No:</strong> {{ $voucher->gst_no }}</p>
                <p><strong>City:</strong> {{ $voucher->city }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Local/Inter:</strong> {{ $voucher->local_inter == 'L' ? 'Local' : 'Interstate' }}</p>
                <p><strong>Status:</strong> <span class="badge bg-{{ $voucher->status == 'active' ? 'success' : 'warning' }}">{{ ucfirst($voucher->status) }}</span></p>
                <p><strong>Description:</strong> {{ $voucher->description }}</p>
            </div>
        </div>
        <h6>Accounts</h6>
        <table class="table table-sm table-bordered mb-3">
            <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Type</th></tr></thead>
            <tbody>
                @foreach($voucher->accounts as $acc)
                <tr><td>{{ $acc->account_code }}</td><td>{{ $acc->account_name }}</td><td>{{ $acc->account_type }}</td></tr>
                @endforeach
            </tbody>
        </table>
        <h6>HSN Items</h6>
        <table class="table table-sm table-bordered mb-3">
            <thead class="table-light"><tr><th>HSN</th><th>Amount</th><th>GST%</th><th>CGST%</th><th>CGST Amt</th><th>SGST%</th><th>SGST Amt</th></tr></thead>
            <tbody>
                @foreach($voucher->items as $item)
                <tr>
                    <td>{{ $item->hsn_code }}</td><td class="text-end">{{ number_format($item->amount, 2) }}</td>
                    <td class="text-end">{{ $item->gst_percent }}</td><td class="text-end">{{ $item->cgst_percent }}</td>
                    <td class="text-end">{{ number_format($item->cgst_amount, 2) }}</td><td class="text-end">{{ $item->sgst_percent }}</td>
                    <td class="text-end">{{ number_format($item->sgst_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm"><tr><td>Gross Amount:</td><td class="text-end">{{ number_format($voucher->amount, 2) }}</td></tr>
                    <tr><td>Total GST:</td><td class="text-end">{{ number_format($voucher->total_gst, 2) }}</td></tr>
                    <tr><td>Net Amount:</td><td class="text-end">{{ number_format($voucher->net_amount, 2) }}</td></tr>
                    <tr><td><strong>Total Credit:</strong></td><td class="text-end"><strong>{{ number_format($voucher->total_credit, 2) }}</strong></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm"><tr><td>TDS %:</td><td class="text-end">{{ $voucher->tds_percent }}</td></tr>
                    <tr><td>TDS Amount:</td><td class="text-end">{{ number_format($voucher->tds_amount, 2) }}</td></tr>
                    <tr><td><strong>Total Debit:</strong></td><td class="text-end"><strong>{{ number_format($voucher->total_debit, 2) }}</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
