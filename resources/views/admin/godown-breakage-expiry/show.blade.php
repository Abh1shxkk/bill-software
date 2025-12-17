@extends('layouts.admin')
@section('title', 'Godown Breakage/Expiry - View')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-box-seam me-2"></i> Godown Breakage/Expiry Details</h4>
        <div class="text-muted small">Transaction: {{ $transaction->trn_no }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.godown-breakage-expiry.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list me-1"></i> View All
        </a>
        <a href="{{ route('admin.godown-breakage-expiry.modification') }}?load={{ $transaction->id }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Modify
        </a>
        <a href="{{ route('admin.godown-breakage-expiry.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Transaction
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-header bg-info text-white">
        <div class="row">
            <div class="col-md-4">
                <strong>TRN No:</strong> {{ $transaction->trn_no }}
            </div>
            <div class="col-md-4">
                <strong>Date:</strong> {{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-' }}
                ({{ $transaction->day_name }})
            </div>
            <div class="col-md-4">
                <strong>Status:</strong> 
                <span class="badge {{ $transaction->status == 'completed' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($transaction->status) }}
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($transaction->narration)
        <div class="mb-3">
            <strong>Narration:</strong> {{ $transaction->narration }}
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-sm" style="font-size: 12px;">
                <thead class="table-info">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Item Name</th>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <th>Br/Ex</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Cost</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_code }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->batch_no ?? '-' }}</td>
                        <td>{{ $item->expiry ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $item->br_ex_type == 'BREAKAGE' ? 'bg-warning' : 'bg-danger' }}">
                                {{ $item->br_ex_type == 'BREAKAGE' ? 'Brk' : 'Exp' }}
                            </span>
                        </td>
                        <td class="text-end">{{ number_format($item->qty, 0) }}</td>
                        <td class="text-end">₹{{ number_format($item->cost, 2) }}</td>
                        <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="6" class="text-end">Total:</th>
                        <th class="text-end">{{ number_format($transaction->total_qty, 0) }}</th>
                        <th></th>
                        <th class="text-end">₹{{ number_format($transaction->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection
