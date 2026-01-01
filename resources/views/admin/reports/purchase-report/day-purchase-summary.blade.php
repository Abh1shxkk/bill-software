@extends('layouts.admin')

@section('title', 'Day Purchase Summary - Item Wise')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #cce5ff 0%, #e6f2ff 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold">DAY PURCHASE SUMMARY - ITEM WISE</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Date</span>
                            <input type="date" name="date" class="form-control" value="{{ $date ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier</span>
                            <select name="supplier_id" class="form-select">
                                <option value="">All</option>
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
                                <option value="">All</option>
                                @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()"><i class="bi bi-file-excel"></i></button>
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
            <div class="table-responsive" style="max-height: 65vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th>Pack</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Free</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items ?? [] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->item_code ?? '-' }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->company_name ?? '-' }}</td>
                            <td>{{ $item->pack ?? '-' }}</td>
                            <td class="text-center">{{ $item->qty ?? 0 }}</td>
                            <td class="text-center">{{ $item->free_qty ?? 0 }}</td>
                            <td class="text-end">{{ number_format($item->rate ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select date and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($items) && count($items) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Total:</td>
                            <td class="text-center">{{ $totals['qty'] ?? 0 }}</td>
                            <td class="text-center">{{ $totals['free'] ?? 0 }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
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
