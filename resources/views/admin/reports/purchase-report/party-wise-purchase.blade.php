@extends('layouts.admin')

@section('title', 'Party Wise Purchase')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">PARTY WISE PURCHASE</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.party-wise-purchase') }}">
                <div class="row g-2">
                    <!-- Row 1: Date Range -->
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

                    <!-- Row 2: Selective Parties & Party Selection -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Selective Parties [Y/N]:</span>
                            <select name="selective_parties" class="form-select">
                                <option value="Y" {{ ($selectiveParties ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($selectiveParties ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party:</span>
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="" style="max-width: 60px;">
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
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Print Address [Y/N]:</span>
                            <select name="print_address" class="form-select">
                                <option value="N" {{ ($printAddress ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($printAddress ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Print Options & Sorting -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Print S.Tax No. [Y/N]:</span>
                            <select name="print_stax_no" class="form-select">
                                <option value="N" {{ ($printStaxNo ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($printStaxNo ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sort By P(arty)/A(mount):</span>
                            <select name="sort_by" class="form-select">
                                <option value="P" {{ ($sortBy ?? 'P') == 'P' ? 'selected' : '' }}>P</option>
                                <option value="A" {{ ($sortBy ?? '') == 'A' ? 'selected' : '' }}>A</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">A(sc)/D(sc):</span>
                            <select name="sort_order" class="form-select">
                                <option value="A" {{ ($sortOrder ?? 'A') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="D" {{ ($sortOrder ?? '') == 'D' ? 'selected' : '' }}>D</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">With Br./Expiry [Y/N]:</span>
                            <select name="with_br_expiry" class="form-select">
                                <option value="N" {{ ($withBrExpiry ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($withBrExpiry ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Amount Filters & Checkboxes -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="checkbox" id="amountGreaterCheck" {{ $amountGreater ? 'checked' : '' }}>
                            </div>
                            <span class="input-group-text">Amount &gt;</span>
                            <input type="number" name="amount_greater" class="form-control" value="{{ $amountGreater ?? '' }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="checkbox" id="amountLessCheck" {{ $amountLessEqual ? 'checked' : '' }}>
                            </div>
                            <span class="input-group-text">Amount &lt;=</span>
                            <input type="number" name="amount_less_equal" class="form-control" value="{{ $amountLessEqual ?? '' }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="with_tax" id="withTax" value="1" {{ ($withTax ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="withTax">With Tax</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="bill_amount" id="billAmount" value="1" {{ ($billAmount ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="billAmount">Bill Amount</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
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
    @if(isset($partyWise) && $partyWise->count() > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Parties</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Bills</small>
                    <h6 class="mb-0">{{ number_format($totals['bills'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Gross Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['gross_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Discount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['discount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Tax</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tax_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Net Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 80px;">Code</th>
                            <th>Supplier Name</th>
                            @if(($printAddress ?? 'N') == 'Y')
                            <th>Address</th>
                            @endif
                            @if(($printStaxNo ?? 'N') == 'Y')
                            <th style="width: 140px;">GST No</th>
                            @endif
                            <th style="width: 100px;">Mobile</th>
                            <th class="text-center" style="width: 60px;">Bills</th>
                            <th class="text-end" style="width: 100px;">Gross Amt</th>
                            <th class="text-end" style="width: 90px;">Discount</th>
                            <th class="text-end" style="width: 90px;">Tax</th>
                            <th class="text-end" style="width: 110px;">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partyWise ?? [] as $index => $party)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $party->code ?? '-' }}</td>
                            <td>{{ $party->name ?? 'N/A' }}</td>
                            @if(($printAddress ?? 'N') == 'Y')
                            <td class="small">{{ $party->address ?? '' }}</td>
                            @endif
                            @if(($printStaxNo ?? 'N') == 'Y')
                            <td class="small">{{ $party->gst_no ?? '' }}</td>
                            @endif
                            <td>{{ $party->mobile ?? '-' }}</td>
                            <td class="text-center">{{ $party->bill_count ?? 0 }}</td>
                            <td class="text-end">{{ number_format($party->gross_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($party->discount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($party->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($party->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 11 + (($printAddress ?? 'N') == 'Y' ? 1 : 0) + (($printStaxNo ?? 'N') == 'Y' ? 1 : 0) }}" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($partyWise) && $partyWise->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="{{ 5 + (($printAddress ?? 'N') == 'Y' ? 1 : 0) + (($printStaxNo ?? 'N') == 'Y' ? 1 : 0) }}" class="text-end">
                                Grand Total: {{ number_format($totals['count'] ?? 0) }} Parties
                            </td>
                            <td class="text-center">{{ number_format($totals['bills'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($totals['gross_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['discount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
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
.input-group-text { font-size: 0.7rem; padding: 0.25rem 0.4rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
