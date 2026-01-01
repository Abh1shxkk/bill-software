@extends('layouts.admin')

@section('title', 'Central Purchase with Local Value')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #7b1fa2;">CENTRAL PURCHASE WITH LOCAL VALUE</h4>
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
                            <span class="input-group-text">Type</span>
                            <select name="purchase_type" class="form-select">
                                <option value="">All</option>
                                <option value="central">Central Only</option>
                                <option value="local">Local Only</option>
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
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Central Purchase</small>
                    <h5 class="mb-0">₹{{ number_format($totals['central'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Local Value</small>
                    <h5 class="mb-0">₹{{ number_format($totals['local'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Difference</small>
                    <h5 class="mb-0">₹{{ number_format($totals['difference'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Savings %</small>
                    <h5 class="mb-0">{{ number_format($totals['savings_percent'] ?? 0, 2) }}%</h5>
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
                            <th class="text-end">Qty</th>
                            <th class="text-end">Central Rate</th>
                            <th class="text-end">Local Rate</th>
                            <th class="text-end">Central Value</th>
                            <th class="text-end">Local Value</th>
                            <th class="text-end">Difference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items ?? [] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->bill_date->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $item->bill_no ?? '-' }}</td>
                            <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $item->item_name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->quantity ?? 0) }}</td>
                            <td class="text-end">{{ number_format($item->central_rate ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->local_rate ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->central_value ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->local_value ?? 0, 2) }}</td>
                            <td class="text-end fw-bold {{ ($item->difference ?? 0) > 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($item->difference ?? 0, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
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
