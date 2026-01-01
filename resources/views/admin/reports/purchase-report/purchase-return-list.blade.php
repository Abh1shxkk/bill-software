@extends('layouts.admin')

@section('title', 'Purchase Return List')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e2e3e5 0%, #f8f9fa 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-secondary fst-italic fw-bold">PURCHASE RETURN LIST</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($returns) && $returns->count() > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Returns</small>
                    <h5 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Return Amount</small>
                    <h5 class="mb-0">₹{{ number_format($totals['amount'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Date</th>
                            <th>Return No</th>
                            <th>Original Bill</th>
                            <th>Supplier</th>
                            <th>Reason</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns ?? [] as $index => $return)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $return->return_date->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $return->return_no }}</td>
                            <td>{{ $return->original_bill_no ?? '-' }}</td>
                            <td>{{ $return->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $return->reason ?? '-' }}</td>
                            <td class="text-end">{{ number_format($return->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($return->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($return->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No purchase returns found
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
