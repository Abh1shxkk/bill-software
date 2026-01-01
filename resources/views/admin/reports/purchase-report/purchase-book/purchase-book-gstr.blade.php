@extends('layouts.admin')

@section('title', 'Purchase Book GSTR')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-success fst-italic fw-bold">PURCHASE BOOK GSTR</h4>
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
                            <span class="input-group-text">GSTR Type</span>
                            <select name="gstr_type" class="form-select">
                                <option value="GSTR2">GSTR-2</option>
                                <option value="GSTR2A">GSTR-2A</option>
                                <option value="GSTR2B">GSTR-2B</option>
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
                <!-- Row 2 -->
                <div class="row g-2 mt-1">
                     <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1.With GSTN / 2.Without GSTN / 3.All</span>
                            <select name="gstn_filter" class="form-select">
                                <option value="3" {{ ($gstnFilter ?? '3') == '3' ? 'selected' : '' }}>3</option>
                                <option value="1" {{ ($gstnFilter ?? '') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($gstnFilter ?? '') == '2' ? 'selected' : '' }}>2</option>
                            </select>
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L(ocal) / C(entral) / B(oth)</span>
                            <select name="local_central" class="form-select">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C</option>
                            </select>
                        </div>
                    </div>
                     <div class="col-md-5">
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="order_by_supplier" id="orderBySupplier" value="1" {{ ($orderBySupplier ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="orderBySupplier">Order by Supplier</label>
                            </div>
                             <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="party_wise_total" id="partyWiseTotal" value="1" {{ ($partyWiseTotal ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="partyWiseTotal">Party Wise Total</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Invoices</small>
                    <h5 class="mb-0">{{ number_format($totals['invoices'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Taxable Value</small>
                    <h5 class="mb-0">₹{{ number_format($totals['taxable'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">CGST</small>
                    <h5 class="mb-0">₹{{ number_format($totals['cgst'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">SGST</small>
                    <h5 class="mb-0">₹{{ number_format($totals['sgst'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small class="text-dark">IGST</small>
                    <h5 class="mb-0">₹{{ number_format($totals['igst'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Tax</small>
                    <h5 class="mb-0">₹{{ number_format($totals['tax'] ?? 0, 2) }}</h5>
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
                            <th>GSTN</th>
                            <th>Supplier Name</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th class="text-end">Taxable</th>
                            <th class="text-end">CGST</th>
                            <th class="text-end">SGST</th>
                            <th class="text-end">IGST</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $index => $purchase)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="small">{{ $purchase->supplier->gstn ?? '-' }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->bill_no }}</td>
                            <td>{{ $purchase->bill_date->format('d-m-Y') ?? '-' }}</td>
                            <td class="text-end">{{ number_format($purchase->taxable_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->cgst_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->sgst_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->igst_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
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
