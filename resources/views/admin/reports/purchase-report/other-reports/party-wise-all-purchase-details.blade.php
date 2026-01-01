@extends('layouts.admin')

@section('title', 'Party Wise All Purchase Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-info fst-italic fw-bold">PARTY WISE ALL PURCHASE DETAILS</h4>
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
                            <span class="input-group-text">Format</span>
                            <select name="format" class="form-select">
                                <option value="detailed">Detailed</option>
                                <option value="summary">Summary</option>
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
                            <th>City</th>
                            <th class="text-center">Bills</th>
                            <th class="text-end">Gross Amt</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amount</th>
                            <th class="text-end">Returns</th>
                            <th class="text-end">Net Purchase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partyDetails ?? [] as $index => $party)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $party->supplier_code ?? '-' }}</td>
                            <td>{{ $party->supplier_name ?? 'N/A' }}</td>
                            <td>{{ $party->city ?? '-' }}</td>
                            <td class="text-center">{{ number_format($party->bill_count ?? 0) }}</td>
                            <td class="text-end">{{ number_format($party->gross_amount ?? 0, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($party->discount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($party->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($party->net_amount ?? 0, 2) }}</td>
                            <td class="text-end text-warning">{{ number_format($party->returns ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($party->net_purchase ?? 0, 2) }}</td>
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
