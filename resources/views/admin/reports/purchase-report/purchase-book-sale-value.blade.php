@extends('layouts.admin')

@section('title', 'Purchase Book With Sale Value')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">PURCHASE BOOK WITH SALE VALUE</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.purchase-book-sale-value') }}">
                <div class="row g-2">
                    <!-- Row 1 -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Tagged Parties [Y/N]:</span>
                            <select name="tagged_parties" class="form-select">
                                <option value="N" {{ ($taggedParties ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($taggedParties ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Remove Tags [Y/N]:</span>
                            <select name="remove_tags" class="form-select">
                                <option value="N" {{ ($removeTags ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($removeTags ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier:</span>
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="00" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->code ?? '' }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-excel me-1"></i>Excel
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="btnView">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnPrint">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-dark btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($purchases) && $purchases->count() > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Bills</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Purchase Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['purchase_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Sale Value</small>
                    <h6 class="mb-0">₹{{ number_format($totals['sale_value'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ ($totals['margin'] ?? 0) >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Margin</small>
                    <h6 class="mb-0">₹{{ number_format($totals['margin'] ?? 0, 2) }}</h6>
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
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 100px;">Bill No</th>
                            <th style="width: 80px;">Code</th>
                            <th>Supplier Name</th>
                            <th class="text-end" style="width: 110px;">Purchase Amt</th>
                            <th class="text-end" style="width: 110px;">Sale Value</th>
                            <th class="text-end" style="width: 100px;">Margin</th>
                            <th class="text-end" style="width: 80px;">Margin %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $index => $purchase)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $purchase->bill_date->format('d-m-Y') }}</td>
                            <td>{{ $purchase->voucher_type ?? '' }}{{ $purchase->bill_no }}</td>
                            <td>{{ $purchase->supplier->code ?? '' }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td class="text-end">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->sale_value ?? 0, 2) }}</td>
                            <td class="text-end {{ ($purchase->margin ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ number_format($purchase->margin ?? 0, 2) }}
                            </td>
                            <td class="text-end {{ ($purchase->margin_percent ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($purchase->margin_percent ?? 0, 2) }}%
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($purchases) && $purchases->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Grand Total: {{ number_format($totals['count'] ?? 0) }} Bills</td>
                            <td class="text-end">{{ number_format($totals['purchase_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['sale_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['margin'] ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');

    // View button - submits form to load data on current page
    document.getElementById('btnView').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (exportInput) exportInput.value = '';
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        form.target = '_self';
        form.submit();
    });

    // Excel button - exports to Excel
    document.getElementById('btnExcel').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (!exportInput) {
            exportInput = document.createElement('input');
            exportInput.type = 'hidden';
            exportInput.name = 'export';
            form.appendChild(exportInput);
        }
        exportInput.value = 'excel';
        form.submit();
        exportInput.value = '';
    });

    // Print button - opens print view in new tab
    document.getElementById('btnPrint').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (!viewTypeInput) {
            viewTypeInput = document.createElement('input');
            viewTypeInput.type = 'hidden';
            viewTypeInput.name = 'view_type';
            form.appendChild(viewTypeInput);
        }
        viewTypeInput.value = 'print';
        form.target = '_blank';
        form.submit();
        form.target = '_self';
        viewTypeInput.value = '';
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnView').click();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
