@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-cash-stack me-2"></i> Payment #{{ $payment->trn_no }}</h4>
        <small class="text-muted">Payment details and adjustments</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.supplier-payment.modification') }}?trn_no={{ $payment->trn_no }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.supplier-payment.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-info-circle me-2"></i> Payment Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th style="width: 150px;">Trn No:</th><td>{{ $payment->trn_no }}</td></tr>
                    <tr><th>Date:</th><td>{{ $payment->payment_date->format('d/m/Y') }} ({{ $payment->day_name }})</td></tr>
                    <tr><th>Ledger:</th><td>{{ $payment->ledger }}</td></tr>
                    <tr><th>Bank:</th><td>{{ $payment->bank_name ?? '-' }}</td></tr>
                    <tr><th>Total Cash:</th><td class="text-success fw-bold">₹ {{ number_format($payment->total_cash, 2) }}</td></tr>
                    <tr><th>Total Cheque:</th><td class="text-info fw-bold">₹ {{ number_format($payment->total_cheque, 2) }}</td></tr>
                    <tr><th>TDS Amount:</th><td>₹ {{ number_format($payment->tds_amount, 2) }}</td></tr>
                    <tr><th>Remarks:</th><td>{{ $payment->remarks ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-success text-white">
                <i class="bi bi-people me-2"></i> Payment Items ({{ $payment->items->count() }})
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Party Name</th>
                            <th>Type</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payment->items as $item)
                        <tr>
                            <td>{{ $item->party_code }}</td>
                            <td>{{ $item->party_name }}</td>
                            <td>
                                @if($item->payment_type == 'cheque')
                                <span class="badge bg-info">Cheque: {{ $item->cheque_no }}</span>
                                @else
                                <span class="badge bg-success">Cash</span>
                                @endif
                            </td>
                            <td class="text-end">₹ {{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-end">₹ {{ number_format($payment->total_cash + $payment->total_cheque, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@if($payment->adjustments->count() > 0)
<div class="card shadow-sm border-0">
    <div class="card-header bg-warning">
        <i class="bi bi-receipt-cutoff me-2"></i> Adjustments ({{ $payment->adjustments->count() }})
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-bordered mb-0" style="font-size: 12px;">
            <thead class="table-light">
                <tr>
                    <th>Reference No</th>
                    <th>Date</th>
                    <th class="text-end">Bill Amount</th>
                    <th class="text-end">Adjusted</th>
                    <th class="text-end">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->adjustments as $adj)
                <tr>
                    <td>{{ $adj->reference_no }}</td>
                    <td>{{ $adj->reference_date ? $adj->reference_date->format('d/m/Y') : '-' }}</td>
                    <td class="text-end">₹ {{ number_format($adj->reference_amount, 2) }}</td>
                    <td class="text-end text-success">₹ {{ number_format($adj->adjusted_amount, 2) }}</td>
                    <td class="text-end">₹ {{ number_format($adj->balance_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
