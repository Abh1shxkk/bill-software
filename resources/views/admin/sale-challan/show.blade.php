@extends('layouts.admin')

@section('title', 'View Sale Challan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Sale Challan Details</h4>
        <div class="text-muted small">Challan No: {{ $transaction->challan_no }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.sale-challan.invoices') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
        @if(!$transaction->is_invoiced)
        <a href="{{ route('admin.sale-challan.modification') }}?challan_no={{ $transaction->challan_no }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @endif
    </div>
</div>

<div class="row">
    <!-- Challan Info -->
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Challan Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Challan No:</td>
                        <td><strong>{{ $transaction->challan_no }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Series:</td>
                        <td>{{ $transaction->series }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Challan Date:</td>
                        <td>{{ $transaction->challan_date->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Due Date:</td>
                        <td>{{ $transaction->due_date ? $transaction->due_date->format('d-m-Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status:</td>
                        <td>
                            @if($transaction->is_invoiced)
                                <span class="badge bg-success">Invoiced</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Payment Mode:</td>
                        <td>
                            @if($transaction->cash_flag == 'Y')
                                <span class="badge bg-success">Cash</span>
                            @elseif($transaction->transfer_flag == 'Y')
                                <span class="badge bg-info">Transfer</span>
                            @else
                                <span class="badge bg-secondary">Credit</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i> Customer Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Customer:</td>
                        <td><strong>{{ $transaction->customer->name ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address:</td>
                        <td>{{ $transaction->customer->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Salesman:</td>
                        <td>{{ $transaction->salesman->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Remarks:</td>
                        <td>{{ $transaction->remarks ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-box me-2"></i> Items ({{ $transaction->items->count() }})</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0" style="font-size: 12px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Batch No</th>
                        <th>Expiry</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Free</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">MRP</th>
                        <th class="text-center">Dis%</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item->name ?? 'N/A' }}</td>
                        <td>{{ $item->batch_no ?? 'N/A' }}</td>
                        <td>{{ $item->expiry_date ? $item->expiry_date->format('m/Y') : 'N/A' }}</td>
                        <td class="text-center">{{ number_format($item->qty, 0) }}</td>
                        <td class="text-center">{{ number_format($item->free_qty, 0) }}</td>
                        <td class="text-end">₹ {{ number_format($item->sale_rate, 2) }}</td>
                        <td class="text-end">₹ {{ number_format($item->mrp, 2) }}</td>
                        <td class="text-center">{{ number_format($item->discount_percent, 2) }}%</td>
                        <td class="text-end">₹ {{ number_format($item->net_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Summary -->
<div class="row">
    <div class="col-md-6 offset-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-calculator me-2"></i> Summary</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Gross Amount:</td>
                        <td class="text-end">₹ {{ number_format($transaction->nt_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Discount:</td>
                        <td class="text-end text-danger">- ₹ {{ number_format($transaction->dis_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tax Amount:</td>
                        <td class="text-end">₹ {{ number_format($transaction->tax_amount, 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Net Amount:</strong></td>
                        <td class="text-end"><strong class="text-success fs-5">₹ {{ number_format($transaction->net_amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if($transaction->is_invoiced && $transaction->sale_transaction_id)
<div class="alert alert-success mt-3">
    <i class="bi bi-check-circle me-2"></i>
    This challan has been converted to Invoice. 
    <a href="{{ route('admin.sale.show', $transaction->sale_transaction_id) }}" class="alert-link">View Invoice</a>
</div>
@endif
@endsection
