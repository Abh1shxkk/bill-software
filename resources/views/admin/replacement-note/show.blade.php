@extends('layouts.admin')

@section('title', 'Replacement Note Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i> Replacement Note Details</h4>
        <div class="text-muted small">RN No: {{ $transaction->rn_no }}</div>
    </div>
    <div>
        <a href="{{ route('admin.replacement-note.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('admin.replacement-note.modification') }}?id={{ $transaction->id }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Transaction Details -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white py-2">
                <strong>Transaction Information</strong>
            </div>
            <div class="card-body">
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-4 mb-2">
                        <strong>RN No:</strong> {{ $transaction->rn_no }}
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Date:</strong> {{ $transaction->transaction_date ? $transaction->transaction_date->format('d-M-Y') : '' }}
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Day:</strong> {{ $transaction->day_name }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Supplier:</strong> {{ $transaction->supplier ? $transaction->supplier->name : $transaction->supplier_name }}
                    </div>
                    <div class="col-md-3 mb-2">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $transaction->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($transaction->status ?? 'active') }}
                        </span>
                    </div>
                    <div class="col-md-3 mb-2">
                        <strong>Net Amount:</strong> <span class="text-primary fw-bold">₹{{ number_format($transaction->net_amount ?? 0, 2) }}</span>
                    </div>
                </div>
                @if($transaction->remarks)
                <div class="mt-2 p-2 bg-light rounded">
                    <strong>Remarks:</strong> {{ $transaction->remarks }}
                </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="card">
            <div class="card-header bg-success text-white py-2">
                <strong>Items ({{ $transaction->items->count() }})</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0" style="font-size: 12px;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Batch</th>
                                <th>Expiry</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">MRP</th>
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
                                <td class="text-end">{{ $item->qty }}</td>
                                <td class="text-end">₹{{ number_format($item->mrp ?? 0, 2) }}</td>
                                <td class="text-end">₹{{ number_format($item->amount ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Total:</th>
                                <th class="text-end">{{ $transaction->items->sum('qty') }}</th>
                                <th></th>
                                <th class="text-end">₹{{ number_format($transaction->items->sum('amount'), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Summary Card -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white py-2">
                <strong>Summary</strong>
            </div>
            <div class="card-body" style="font-size: 13px;">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Items:</span>
                    <strong>{{ $transaction->items->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Quantity:</span>
                    <strong>{{ $transaction->items->sum('qty') }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Pending Br./Expiry:</span>
                    <strong>₹{{ number_format($transaction->pending_br_expiry ?? 0, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Balance Amount:</span>
                    <strong>₹{{ number_format($transaction->balance_amount ?? 0, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>SCM %:</span>
                    <strong>{{ $transaction->scm_percent ?? 0 }}%</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>SCM Amount:</span>
                    <strong>₹{{ number_format($transaction->scm_amount ?? 0, 2) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Net Amount:</span>
                    <strong class="text-primary fs-5">₹{{ number_format($transaction->net_amount ?? 0, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="card">
            <div class="card-header bg-secondary text-white py-2">
                <strong>Additional Info</strong>
            </div>
            <div class="card-body" style="font-size: 12px;">
                <div class="row">
                    <div class="col-6 mb-2"><strong>Pack:</strong> {{ $transaction->pack }}</div>
                    <div class="col-6 mb-2"><strong>Unit:</strong> {{ $transaction->unit }}</div>
                    <div class="col-6 mb-2"><strong>Cl. Qty:</strong> {{ $transaction->cl_qty }}</div>
                    <div class="col-6 mb-2"><strong>Comp:</strong> {{ $transaction->comp }}</div>
                    <div class="col-6 mb-2"><strong>Lctn:</strong> {{ $transaction->lctn }}</div>
                    <div class="col-6 mb-2"><strong>Srlno:</strong> {{ $transaction->srlno }}</div>
                    <div class="col-6 mb-2"><strong>Case:</strong> {{ $transaction->case_no }}</div>
                    <div class="col-6 mb-2"><strong>Box:</strong> {{ $transaction->box }}</div>
                </div>
                <hr>
                <div class="text-muted small">
                    <div>Created: {{ $transaction->created_at ? $transaction->created_at->format('d-M-Y H:i') : '' }}</div>
                    <div>Updated: {{ $transaction->updated_at ? $transaction->updated_at->format('d-M-Y H:i') : '' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
