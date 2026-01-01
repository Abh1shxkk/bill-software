@extends('layouts.admin')

@section('title', 'Purchase with Item Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e2d4f0 0%, #f3e8ff 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-purple fst-italic fw-bold" style="color: #6f42c1;">PURCHASE WITH ITEM DETAILS</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier</span>
                            <select name="supplier_id" class="form-select">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Item</span>
                            <input type="text" name="item_search" class="form-control" placeholder="Search Item...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <button type="button" class="btn btn-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Bills</small>
                    <h5 class="mb-0">{{ number_format($totals['bills'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Items</small>
                    <h5 class="mb-0">{{ number_format($totals['items'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Total Quantity</small>
                    <h5 class="mb-0">{{ number_format($totals['quantity'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Amount</small>
                    <h5 class="mb-0">₹{{ number_format($totals['amount'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Date</th>
                            <th>Bill No</th>
                            <th>Supplier</th>
                            <th>Item Name</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Free</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Disc %</th>
                            <th class="text-end">GST %</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $index => $purchase)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $purchase->bill_date->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $purchase->bill_no }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->item_name ?? '-' }}</td>
                            <td>{{ $purchase->batch_no ?? '-' }}</td>
                            <td>{{ $purchase->expiry_date ?? '-' }}</td>
                            <td class="text-end">{{ number_format($purchase->quantity ?? 0) }}</td>
                            <td class="text-end">{{ number_format($purchase->free_qty ?? 0) }}</td>
                            <td class="text-end">{{ number_format($purchase->rate ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->discount_percent ?? 0, 2) }}%</td>
                            <td class="text-end">{{ number_format($purchase->gst_percent ?? 0, 2) }}%</td>
                            <td class="text-end fw-bold">{{ number_format($purchase->amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
