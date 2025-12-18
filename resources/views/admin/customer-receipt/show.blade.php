@extends('layouts.admin')

@section('title', 'Receipt Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-receipt me-2"></i> Receipt #{{ $receipt->trn_no }}</h4>
        <small class="text-muted">{{ $receipt->receipt_date->format('d/m/Y') }} - {{ $receipt->day_name }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customer-receipt.modification') }}?trn_no={{ $receipt->trn_no }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.customer-receipt.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Receipt Details -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-primary text-white">
                <strong>Receipt Information</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th width="120">Trn No:</th><td>{{ $receipt->trn_no }}</td></tr>
                            <tr><th>Date:</th><td>{{ $receipt->receipt_date->format('d/m/Y') }}</td></tr>
                            <tr><th>Day:</th><td>{{ $receipt->day_name }}</td></tr>
                            <tr><th>Ledger:</th><td>{{ $receipt->ledger }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th width="120">Bank:</th><td>{{ $receipt->bank_name ?? '-' }}</td></tr>
                            <tr><th>Salesman:</th><td>{{ $receipt->salesman_name ?? '-' }}</td></tr>
                            <tr><th>Area:</th><td>{{ $receipt->area_name ?? '-' }}</td></tr>
                            <tr><th>Route:</th><td>{{ $receipt->route_name ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-info text-white">
                <strong>Receipt Items</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Party Name</th>
                            <th>Cheque No</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Unadjusted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipt->items as $item)
                        <tr>
                            <td>{{ $item->party_code }}</td>
                            <td>{{ $item->party_name }}</td>
                            <td>{{ $item->cheque_no ?? '-' }}</td>
                            <td>{{ $item->cheque_date ? $item->cheque_date->format('d/m/Y') : '-' }}</td>
                            <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                            <td class="text-end">₹{{ number_format($item->unadjusted, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No items</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Summary -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <strong>Summary</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th>Total Cash:</th>
                        <td class="text-end"><strong class="text-success">₹{{ number_format($receipt->total_cash, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Total Cheque:</th>
                        <td class="text-end"><strong class="text-info">₹{{ number_format($receipt->total_cheque, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>TDS Amount:</th>
                        <td class="text-end">₹{{ number_format($receipt->tds_amount, 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <th>Grand Total:</th>
                        <td class="text-end"><strong class="text-primary fs-5">₹{{ number_format($receipt->total_cash + $receipt->total_cheque, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        @if($receipt->tag || $receipt->day_value)
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-secondary text-white">
                <strong>Additional Info</strong>
            </div>
            <div class="card-body">
                @if($receipt->day_value)
                <p class="mb-1"><strong>Day:</strong> {{ $receipt->day_value }}</p>
                @endif
                @if($receipt->tag)
                <p class="mb-0"><strong>Tag:</strong> {{ $receipt->tag }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
