@extends('layouts.admin')

@section('title', 'TDS OUTPUT')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">TDS Output</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.tds-output') }}">
                <div class="row g-2">
                    <!-- Row 1 -->
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
                    <div class="col-md-6"></div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm" id="btnView">
                                <i class="bi bi-check-lg me-1"></i>OK
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnPrint">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-dark btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party</span>
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="00" style="max-width: 60px;">
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
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-danger sticky-top">
                        <tr>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 100px;">Bill No</th>
                            <th style="width: 80px;">Code</th>
                            <th>Party Name</th>
                            <th style="width: 120px;">Pan</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                            <th class="text-end" style="width: 100px;">Taxable</th>
                            <th class="text-center" style="width: 70px;">TDS%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tdsRecords ?? [] as $index => $record)
                        <tr>
                            <td>{{ $record->bill_date->format('d-m-Y') }}</td>
                            <td>{{ $record->voucher_type ?? '' }}{{ $record->bill_no }}</td>
                            <td>{{ $record->supplier->code ?? '' }}</td>
                            <td>{{ $record->supplier->name ?? 'N/A' }}</td>
                            <td class="small">{{ $record->supplier->pan ?? '-' }}</td>
                            <td class="text-end">{{ number_format($record->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($record->nt_amount ?? 0, 2) }}</td>
                            <td class="text-center text-danger fw-bold">{{ number_format($record->tds_rate ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "OK" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer with Find buttons -->
    <div class="card mt-2">
        <div class="card-body py-2">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-danger btn-sm" id="btnFind">
                    <i class="bi bi-search me-1"></i>Find [F1]
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" id="btnFindNext">
                    Find Next
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');

    // View/OK button - submits form to load data on current page
    document.getElementById('btnView').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (exportInput) exportInput.value = '';
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        form.target = '_self';
        form.submit();
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

    // Find button
    document.getElementById('btnFind').addEventListener('click', function() {
        alert('Find functionality - Feature coming soon');
    });

    // Find Next button
    document.getElementById('btnFindNext').addEventListener('click', function() {
        alert('Find Next functionality - Feature coming soon');
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnView').click();
        }
        if (e.key === 'F1') {
            e.preventDefault();
            document.getElementById('btnFind').click();
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
.table-danger th { background-color: #f8d7da !important; color: #721c24; }
</style>
@endpush
