@extends('layouts.admin')

@section('title', 'Stock Transfer Outgoing Return - View')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-box-arrow-in-left me-2"></i> Stock Transfer Outgoing Return - {{ $transaction->sr_no }}</h4>
    <a href="{{ route('admin.stock-transfer-outgoing-return.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light"><strong>Transaction Details</strong></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>Sr No:</strong> {{ $transaction->sr_no }}</div>
            <div class="col-md-3"><strong>Date:</strong> {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</div>
            <div class="col-md-3"><strong>Transfer From:</strong> {{ $transaction->transfer_from_name ?? 'N/A' }}</div>
            <div class="col-md-3"><strong>Trf Return No:</strong> {{ $transaction->trf_return_no ?? '-' }}</div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3"><strong>GR No:</strong> {{ $transaction->challan_no ?? '-' }}</div>
            <div class="col-md-3"><strong>GR Date:</strong> {{ $transaction->challan_date ? \Carbon\Carbon::parse($transaction->challan_date)->format('d/m/Y') : '-' }}</div>
            <div class="col-md-3"><strong>Cases:</strong> {{ $transaction->cases ?? 0 }}</div>
            <div class="col-md-3"><strong>Transport:</strong> {{ $transaction->transport ?? '-' }}</div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6"><strong>Remarks:</strong> {{ $transaction->remarks ?? '-' }}</div>
            <div class="col-md-3"><strong>Net Amount:</strong> <span class="text-success fw-bold">{{ number_format($transaction->net_amount ?? 0, 2) }}</span></div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-light"><strong>Items</strong></div>
    <div class="card-body p-0">
        <table class="table table-bordered table-sm mb-0" style="font-size: 12px;">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Code</th><th>Item Name</th><th>Batch</th><th>Expiry</th>
                    <th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item_code ?? $item->item_id }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->batch_no ?? '-' }}</td>
                    <td>{{ $item->expiry ?? '-' }}</td>
                    <td class="text-end">{{ $item->qty }}</td>
                    <td class="text-end">{{ number_format($item->s_rate ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="7" class="text-end"><strong>Total:</strong></td>
                    <td class="text-end"><strong>{{ number_format($transaction->net_amount ?? 0, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
