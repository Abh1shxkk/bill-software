@extends('layouts.admin')

@section('title', 'View Purchase Challan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-receipt me-2"></i> Purchase Challan Details</h4>
        <div class="text-muted small">Challan No: {{ $challan->challan_no }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.purchase-challan.invoices') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
        @if(!$challan->is_invoiced)
        <a href="{{ route('admin.purchase-challan.modification') }}?challan_no={{ $challan->challan_no }}" class="btn btn-primary btn-sm">
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
                        <td><strong>{{ $challan->challan_no }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Supplier Invoice No:</td>
                        <td>{{ $challan->supplier_invoice_no ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Challan Date:</td>
                        <td>{{ $challan->challan_date ? $challan->challan_date->format('d-m-Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Supplier Invoice Date:</td>
                        <td>{{ $challan->supplier_invoice_date ? $challan->supplier_invoice_date->format('d-m-Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Due Date:</td>
                        <td>{{ $challan->due_date ? $challan->due_date->format('d-m-Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status:</td>
                        <td>
                            @if($challan->is_invoiced)
                                <span class="badge bg-success">Invoiced</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Payment Mode:</td>
                        <td>
                            @if($challan->cash_flag == 'Y')
                                <span class="badge bg-success">Cash</span>
                            @elseif($challan->transfer_flag == 'Y')
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

    <!-- Supplier Info -->
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-building me-2"></i> Supplier Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Supplier:</td>
                        <td><strong>{{ $challan->supplier->name ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address:</td>
                        <td>{{ $challan->supplier->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Phone:</td>
                        <td>{{ $challan->supplier->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">GSTIN:</td>
                        <td>{{ $challan->supplier->gstin ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Remarks:</td>
                        <td>{{ $challan->remarks ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-box me-2"></i> Items ({{ $challan->items->count() }})</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0" style="font-size: 12px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Company</th>
                        <th>Batch No</th>
                        <th>Expiry</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Free</th>
                        <th class="text-end">P.Rate</th>
                        <th class="text-end">MRP</th>
                        <th class="text-center">Dis%</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($challan->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item->bar_code ?? $item->item->id ?? 'N/A' }}</td>
                        <td>{{ $item->item->name ?? 'N/A' }}</td>
                        <td>{{ $item->item->company->short_name ?? $item->item->company->name ?? 'N/A' }}</td>
                        <td>{{ $item->batch_no ?? 'N/A' }}</td>
                        <td>{{ $item->expiry_date ? date('m/Y', strtotime($item->expiry_date)) : 'N/A' }}</td>
                        <td class="text-center">{{ number_format($item->qty, 0) }}</td>
                        <td class="text-center">{{ number_format($item->free_qty, 0) }}</td>
                        <td class="text-end">₹ {{ number_format($item->purchase_rate, 2) }}</td>
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
                        <td class="text-end">₹ {{ number_format($challan->nt_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Scheme Amount:</td>
                        <td class="text-end">₹ {{ number_format($challan->sc_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Discount:</td>
                        <td class="text-end text-danger">- ₹ {{ number_format($challan->dis_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tax Amount:</td>
                        <td class="text-end">₹ {{ number_format($challan->tax_amount ?? 0, 2) }}</td>
                    </tr>
                    @if($challan->tcs_amount > 0)
                    <tr>
                        <td class="text-muted">TCS Amount:</td>
                        <td class="text-end">₹ {{ number_format($challan->tcs_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-top">
                        <td><strong>Net Amount:</strong></td>
                        <td class="text-end"><strong class="text-success fs-5">₹ {{ number_format($challan->net_amount ?? 0, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if($challan->is_invoiced && $challan->purchase_transaction_id)
<div class="alert alert-success mt-3">
    <i class="bi bi-check-circle me-2"></i>
    This challan has been converted to Invoice. 
    <a href="{{ route('admin.purchase.show', $challan->purchase_transaction_id) }}" class="alert-link">View Invoice</a>
</div>
@endif
@endsection
