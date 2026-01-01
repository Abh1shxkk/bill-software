@extends('layouts.admin')

@section('title', 'Purchase Book With TCS')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #fff3cd 0%, #fff9e6 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-warning fst-italic fw-bold">PURCHASE BOOK WITH TCS</h4>
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
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">TCS Rate</span>
                            <select name="tcs_rate" class="form-select">
                                <option value="">All</option>
                                <option value="0.1">0.1%</option>
                                <option value="0.075">0.075%</option>
                                <option value="1">1%</option>
                            </select>
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
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <button type="button" class="btn btn-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-md-6">
                         <div class="d-flex flex-wrap gap-3 mt-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="order_by_supplier" id="orderBySupplier" value="1" {{ ($orderBySupplier ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="orderBySupplier">Order by Supplier</label>
                            </div>
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
                    <small class="text-white-50">Total Amount</small>
                    <h5 class="mb-0">₹{{ number_format($totals['amount'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small class="text-dark">TCS Amount</small>
                    <h5 class="mb-0">₹{{ number_format($totals['tcs'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Net Amount</small>
                    <h5 class="mb-0">₹{{ number_format($totals['net'] ?? 0, 2) }}</h5>
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
                            <th>PAN</th>
                            <th class="text-end">Taxable Amt</th>
                            <th class="text-end">GST</th>
                            <th class="text-center">TCS %</th>
                            <th class="text-end">TCS Amt</th>
                            <th class="text-end">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $index => $purchase)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $purchase->bill_date->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $purchase->bill_no }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td class="small">{{ $purchase->supplier->pan ?? '-' }}</td>
                            <td class="text-end">{{ number_format($purchase->taxable_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->tax_amount ?? 0, 2) }}</td>
                            <td class="text-center">{{ $purchase->tcs_rate ?? 0 }}%</td>
                            <td class="text-end text-warning fw-bold">{{ number_format($purchase->tcs_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No TCS records found
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
