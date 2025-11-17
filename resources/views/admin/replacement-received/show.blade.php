@extends('layouts.admin')
@section('title', 'Replacement Received Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center">
            <i class="bi bi-box-arrow-in-down me-2"></i> Replacement Received Details
        </h4>
        <div class="text-muted small">Complete details of replacement received transaction</div>
    </div>
    <div>
        <a href="{{ route('admin.replacement-received.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <a href="{{ route('admin.replacement-received.modification') }}?id={{ $transaction->id }}" class="btn btn-outline-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
    </div>
</div>

<!-- Transaction Header -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Transaction Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold">RR No:</label>
                        <div>{{ $transaction->rr_no }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Date:</label>
                        <div>{{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Supplier:</label>
                        <div>{{ $transaction->supplier ? $transaction->supplier->name : ($transaction->supplier_name ?? '-') }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Series:</label>
                        <div>{{ $transaction->series ?? 'RR' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Status:</label>
                        <div>
                            <span class="badge bg-{{ $transaction->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($transaction->status ?? 'active') }}
                            </span>
                        </div>
                    </div>
                    @if($transaction->remarks)
                    <div class="col-md-12">
                        <label class="fw-bold">Remarks:</label>
                        <div>{{ $transaction->remarks }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>Amount Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Amount:</span>
                    <strong class="text-success fs-5">₹{{ number_format($transaction->total_amount ?? 0, 2) }}</strong>
                </div>
                @if(isset($transaction->adjustments) && $transaction->adjustments->count() > 0)
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Adjusted:</span>
                    <strong class="text-primary">₹{{ number_format($transaction->adjustments->sum('adjusted_amount'), 2) }}</strong>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Free</th>
                        <th class="text-end">MRP</th>
                        <th class="text-end">Dis%</th>
                        <th class="text-end">FT Rate</th>
                        <th class="text-end">FT Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaction->items ?? [] as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->batch_no ?? '-' }}</td>
                        <td>{{ $item->expiry_date ? $item->expiry_date->format('m/Y') : ($item->expiry ?? '-') }}</td>
                        <td class="text-end">{{ $item->qty }}</td>
                        <td class="text-end">{{ $item->free_qty ?? 0 }}</td>
                        <td class="text-end">₹{{ number_format($item->mrp ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($item->discount_percent ?? 0, 2) }}%</td>
                        <td class="text-end">₹{{ number_format($item->ft_rate ?? 0, 2) }}</td>
                        <td class="text-end">₹{{ number_format($item->ft_amount ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-3">No items found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Adjustments Table -->
@if(isset($transaction->adjustments) && $transaction->adjustments->count() > 0)
<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Purchase Return Adjustments</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>PR No.</th>
                        <th>Return Date</th>
                        <th class="text-end">Adjusted Amount</th>
                        <th>Adjustment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->adjustments as $adjustment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($adjustment->purchaseReturn)
                                <a href="{{ route('admin.purchase-return.show', $adjustment->purchaseReturn->id) }}" class="text-primary">
                                    {{ $adjustment->purchaseReturn->pr_no }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($adjustment->purchaseReturn && $adjustment->purchaseReturn->return_date)
                                {{ $adjustment->purchaseReturn->return_date->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">₹{{ number_format($adjustment->adjusted_amount ?? 0, 2) }}</td>
                        <td>{{ $adjustment->adjustment_date ? $adjustment->adjustment_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total Adjusted:</td>
                        <td class="text-end fw-bold">₹{{ number_format($transaction->adjustments->sum('adjusted_amount'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
