@extends('layouts.admin')

@section('title', 'Supplier Wise Companies')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold">SUPPLIER WISE COMPANIES</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-4">
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
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Company</span>
                            <select name="company_id" class="form-select">
                                <option value="">All Companies</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Status</span>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Supplier Code</th>
                            <th>Supplier Name</th>
                            <th>Company Name</th>
                            <th>Division</th>
                            <th class="text-center">Items Count</th>
                            <th class="text-end">Last Purchase</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplierCompanies ?? [] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->supplier->code ?? '-' }}</td>
                            <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $item->company->name ?? '-' }}</td>
                            <td>{{ $item->division ?? '-' }}</td>
                            <td class="text-center">{{ number_format($item->items_count ?? 0) }}</td>
                            <td class="text-end">{{ $item->last_purchase_date ?? '-' }}</td>
                            <td class="text-center">
                                @if($item->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
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
