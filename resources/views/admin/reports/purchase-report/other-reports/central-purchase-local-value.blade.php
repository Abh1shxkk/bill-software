@extends('layouts.admin')

@section('title', 'Central Purchase with Local Value')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #fff59d 0%, #ffee58 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fw-bold" style="color: #1565c0;">CENTRAL PURCHASE WITH LOCAL VALUE</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2" style="background: #ffe0b2;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.other.central-purchase-local-value') }}">
                <div class="row g-2">
                    <!-- Row 1: Date Range -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 2: Supplier -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier:</span>
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="00" style="max-width: 50px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->code ?? '' }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
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
    @if(isset($items) && count($items) > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Items</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Qty</small>
                    <h6 class="mb-0">{{ number_format($totals['qty'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Central Value</small>
                    <h6 class="mb-0">₹{{ number_format($totals['central_value'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Local Value</small>
                    <h6 class="mb-0">₹{{ number_format($totals['local_value'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Difference</small>
                    <h6 class="mb-0">₹{{ number_format($totals['difference'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Savings %</small>
                    <h6 class="mb-0">{{ number_format($totals['savings_percent'] ?? 0, 2) }}%</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 40px;">Sr.</th>
                            <th style="width: 80px;">Date</th>
                            <th style="width: 80px;">Bill No</th>
                            <th>Supplier</th>
                            <th>Item Name</th>
                            <th class="text-end" style="width: 70px;">Qty</th>
                            <th class="text-end" style="width: 90px;">Central Rate</th>
                            <th class="text-end" style="width: 90px;">Local Rate</th>
                            <th class="text-end" style="width: 100px;">Central Value</th>
                            <th class="text-end" style="width: 100px;">Local Value</th>
                            <th class="text-end" style="width: 90px;">Difference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items ?? [] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->bill_date ? $item->bill_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->bill_no ?? '-' }}</td>
                            <td>{{ $item->supplier_name ?? 'N/A' }}</td>
                            <td>{{ $item->item_name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->qty ?? 0, 2) }}</td>
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
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($items) && count($items) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5">Grand Total</td>
                            <td class="text-end">{{ number_format($totals['qty'] ?? 0, 2) }}</td>
                            <td></td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['central_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['local_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['difference'] ?? 0, 2) }}</td>
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

    // View button
    document.getElementById('btnView').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        let exportInput = form.querySelector('input[name="export"]');
        if (exportInput) exportInput.value = '';
        form.target = '_self';
        form.submit();
    });

    // Print button
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
        viewTypeInput.value = '';
        form.target = '_self';
    });

    // Excel button
    document.getElementById('btnExcel').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (!exportInput) {
            exportInput = document.createElement('input');
            exportInput.type = 'hidden';
            exportInput.name = 'export';
            form.appendChild(exportInput);
        }
        exportInput.value = 'excel';
        form.target = '_self';
        form.submit();
        exportInput.value = '';
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
.table th, .table td { padding: 0.4rem 0.5rem; font-size: 0.8rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
