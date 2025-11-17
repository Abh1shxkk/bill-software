@extends('layouts.admin')

@section('title', 'View Stock Transfer Incoming Return')

@section('content')
<section class="py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-box-arrow-up-right me-2"></i> Stock Transfer Incoming Return Details</h4>
                <div class="text-muted small">Transaction #{{ $transaction->trn_no }}</div>
            </div>
            <div>
                <a href="{{ route('admin.stock-transfer-incoming-return.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
                <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded mb-3">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Transaction Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p><strong>Trn No.:</strong> {{ $transaction->trn_no }}</p>
                        <p><strong>Date:</strong> {{ $transaction->transaction_date->format('d-M-Y') }}</p>
                        <p><strong>Day:</strong> {{ $transaction->day_name }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Name:</strong> {{ $transaction->name ?? '-' }}</p>
                        <p><strong>Remarks:</strong> {{ $transaction->remarks ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>GR No.:</strong> {{ $transaction->gr_no ?? '-' }}</p>
                        <p><strong>GR Date:</strong> {{ $transaction->gr_date ? $transaction->gr_date->format('d-M-Y') : '-' }}</p>
                        <p><strong>Cases:</strong> {{ $transaction->cases ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Transport:</strong> {{ $transaction->transport ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0" style="font-size: 11px;">
                        <thead class="table-success">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th style="width: 70px;">Code</th>
                                <th style="width: 200px;">Item Name</th>
                                <th>Batch</th>
                                <th>Expiry</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->item_code }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->batch_no }}</td>
                                <td>{{ $item->expiry }}</td>
                                <td class="text-center">{{ number_format($item->qty, 0) }}</td>
                                <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                                <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-warning">
                            <tr>
                                <td colspan="7" class="text-end"><strong>Net Amount:</strong></td>
                                <td class="text-end"><strong>₹{{ number_format($transaction->net_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p><strong>Packing:</strong> {{ $transaction->packing ?? '-' }}</p>
                        <p><strong>Unit:</strong> {{ $transaction->unit ?? '-' }}</p>
                        <p><strong>Cl. Qty:</strong> {{ $transaction->cl_qty ?? 0 }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Comp:</strong> {{ $transaction->comp ?? '-' }}</p>
                        <p><strong>Location:</strong> {{ $transaction->lctn ?? '-' }}</p>
                        <p><strong>Srl.No.:</strong> {{ $transaction->srlno ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@media print {
    .btn, nav, .sidebar, header, footer { display: none !important; }
    .card { border: 1px solid #000 !important; }
}
</style>
@endsection
