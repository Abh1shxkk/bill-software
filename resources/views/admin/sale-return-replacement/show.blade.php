@extends('layouts.admin')

@section('title', 'View Sale Return Replacement')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-eye me-2"></i> Transaction Details</h4>
        <div class="text-muted small">View transaction #{{ $transaction->trn_no }}</div>
    </div>
    <div>
        <a href="{{ route('admin.sale-return-replacement.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <button onclick="window.print()" class="btn btn-info ms-2"><i class="bi bi-printer"></i> Print</button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body p-4">
        <!-- Header Info -->
        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="text-muted text-uppercase fw-bold">Customer</h6>
                <h5 class="mb-1">{{ $transaction->customer_name }}</h5>
                <p class="mb-0">ID: {{ $transaction->customer_id }}</p>
                <p>Cash: <span class="badge bg-secondary">{{ $transaction->is_cash }}</span></p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="text-muted text-uppercase fw-bold">Transaction Details</h6>
                <p class="mb-1"><strong>Series:</strong> <span class="text-danger fw-bold">{{ $transaction->series }}</span></p>
                <p class="mb-1"><strong>Trn No:</strong> #{{ $transaction->trn_no }}</p>
                <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($transaction->trn_date)->format('d M, Y') }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">Code</th>
                        <th style="width: 30%">Item Name</th>
                        <th style="width: 10%">Batch</th>
                        <th style="width: 10%">Exp.</th>
                        <th style="width: 5%" class="text-end">Qty</th>
                        <th style="width: 5%" class="text-end">Free</th>
                        <th style="width: 10%" class="text-end">Rate</th>
                        <th style="width: 10%" class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_code }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->batch_no }}</td>
                        <td>{{ $item->expiry_date }}</td>
                        <td class="text-end">{{ $item->qty }}</td>
                        <td class="text-end">{{ $item->free_qty }}</td>
                        <td class="text-end">{{ number_format($item->sale_rate, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="row justify-content-end">
            <div class="col-md-5">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-end"><strong>N.T. Amt:</strong></td>
                        <td class="text-end">{{ number_format($transaction->items->sum('amount'), 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>SC ({{ $transaction->sc_percent }}%):</strong></td>
                        <td class="text-end">{{ number_format($transaction->sc_amt, 2) }}</td>
                    </tr>
                    <tr>
                         <td class="text-end"><strong>Discount:</strong></td>
                         <td class="text-end text-danger">- {{ number_format($transaction->dis_amt, 2) }}</td>
                    </tr>
                    <tr>
                         <td class="text-end"><strong>SCM:</strong></td>
                         <td class="text-end text-danger">- {{ number_format($transaction->scm_amt, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>Tax ({{ $transaction->tax_percent }}%):</strong></td>
                        <td class="text-end">{{ number_format($transaction->tax_amt, 2) }}</td>
                    </tr>
                    <tr class="border-top border-dark">
                        <td class="text-end"><h5 class="mb-0">Net Amount:</h5></td>
                        <td class="text-end"><h5 class="mb-0">{{ number_format($transaction->net_amt, 2) }}</h5></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
