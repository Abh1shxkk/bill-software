@extends('layouts.admin')

@section('title', 'Purchase Return List')

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="card shadow-sm mb-2" style="background: #ffe4c4;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.purchase-return-list') }}">
                <div class="row g-2">
                    <!-- Row 1: Date Range, Supplier, Adjusted Filter -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier:</span>
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="00" style="max-width: 45px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->code ?? '' }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline">
                                <input type="radio" name="adjusted_filter" class="form-check-input" id="adjY" value="Y" {{ ($adjustedFilter ?? '') == 'Y' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="adjY">Adjusted</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="adjusted_filter" class="form-check-input" id="adjN" value="N" {{ ($adjustedFilter ?? '') == 'N' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="adjN">Unadjusted</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="adjusted_filter" class="form-check-input" id="adjA" value="A" {{ ($adjustedFilter ?? 'A') == 'A' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="adjA">All</label>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Salesman, Route, R(epl.)/C(redit) Note -->
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">SalesMan:</span>
                            <input type="text" name="salesman_code" class="form-control" value="{{ $salesmanCode ?? '' }}" placeholder="00" style="max-width: 45px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="salesman_id" class="form-select form-select-sm">
                            <option value="">All Salesmen</option>
                            @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ ($salesmanId ?? '') == $salesman->id ? 'selected' : '' }}>
                                    {{ $salesman->code ?? '' }} - {{ $salesman->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Route:</span>
                            <input type="text" name="route_code" class="form-control" value="{{ $routeCode ?? '' }}" placeholder="00" style="max-width: 45px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="route_id" class="form-select form-select-sm">
                            <option value="">All Routes</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>
                                    {{ $route->code ?? '' }} - {{ $route->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">R(epl.) / C(redit) Note:</span>
                            <input type="text" name="repl_credit_note" class="form-control" value="{{ $replCreditNote ?? '' }}" style="max-width: 50px;">
                        </div>
                    </div>

                    <!-- Row 3: Area, Flag, Buttons -->
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Area:</span>
                            <input type="text" name="area_code" class="form-control" value="{{ $areaCode ?? '' }}" placeholder="00" style="max-width: 45px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="area_id" class="form-select form-select-sm">
                            <option value="">All Areas</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>
                                    {{ $area->area_code ?? '' }} - {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Flag:</span>
                            <input type="text" name="flag" class="form-control" value="{{ $flag ?? '5' }}" style="max-width: 40px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">1.PR / 2.CN / 3.DN / 4.PE / 5.ALL</small>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-danger btn-sm" id="btnView">
                                <i class="bi bi-eye me-1"></i>Ok
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
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

    <!-- Main Data Table -->
    <div class="card shadow-sm mb-2" style="background: #ffe4c4;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 35vh;">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="sticky-top" style="background: #d3d3d3;">
                        <tr>
                            <th style="width: 80px;">Date</th>
                            <th style="width: 80px;">Bill No.</th>
                            <th style="width: 60px;">Code</th>
                            <th>Party Name</th>
                            <th class="text-end" style="width: 90px;">Amount</th>
                            <th class="text-end" style="width: 90px;">Taxable</th>
                            <th class="text-end" style="width: 70px;">Tax</th>
                            <th class="text-end" style="width: 90px;">Due Amt</th>
                            <th class="text-end" style="width: 90px;">Adj. Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns ?? [] as $return)
                        @php $adjAmt = $return->net_amount - ($return->balance_amount ?? 0); @endphp
                        <tr>
                            <td>{{ $return->return_date->format('d-m-Y') }}</td>
                            <td>{{ $return->pr_no }}</td>
                            <td>{{ $return->supplier->code ?? '' }}</td>
                            <td>{{ $return->supplier->name ?? 'N/A' }}</td>
                            <td class="text-end">{{ number_format($return->net_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($return->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($return->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($return->balance_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($adjAmt, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                Select filters and click "Ok" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($returns) && count($returns) > 0)
                    <tfoot class="fw-bold" style="background: #d3d3d3;">
                        <tr>
                            <td colspan="4" class="text-danger">TOTAL :</td>
                            <td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['due_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['adj_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Adjustment Detail & Reference Detail -->
    <div class="row g-2">
        <!-- Adjustment Detail -->
        <div class="col-md-6">
            <div class="card shadow-sm" style="background: #ffe4c4;">
                <div class="card-header py-1 text-center" style="background: #ffa07a;">
                    <span class="text-danger fw-bold">------: Adjustment Detail :------</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 20vh;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead style="background: #ffa07a;">
                                <tr>
                                    <th style="width: 100px;">Trans.No.</th>
                                    <th style="width: 100px;">Date</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adjustments ?? [] as $adj)
                                <tr>
                                    <td>{{ $adj->transaction_no ?? '-' }}</td>
                                    <td>{{ $adj->adjustment_date ? $adj->adjustment_date->format('d-m-Y') : '-' }}</td>
                                    <td class="text-end">{{ number_format($adj->amount ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-2">No adjustments</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="fw-bold" style="background: #d3d3d3;">
                                <tr>
                                    <td class="text-danger">TOTAL :</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($adjustmentTotal ?? 0, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reference Detail -->
        <div class="col-md-6">
            <div class="card shadow-sm" style="background: #ffe4c4;">
                <div class="card-header py-1 text-center" style="background: #ffa07a;">
                    <span class="text-primary fw-bold">------: Reference Detail :------</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 20vh;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead style="background: #ffa07a;">
                                <tr>
                                    <th style="width: 80px;">Trn. No.</th>
                                    <th style="width: 90px;">PBill No.</th>
                                    <th style="width: 90px;">PBill Date</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($references ?? [] as $ref)
                                <tr>
                                    <td>{{ $ref->trn_no ?? '-' }}</td>
                                    <td>{{ $ref->pbill_no ?? '-' }}</td>
                                    <td>{{ $ref->pbill_date ? $ref->pbill_date->format('d-m-Y') : '-' }}</td>
                                    <td class="text-end">{{ number_format($ref->amount ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-2">No references</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
.form-check-label { font-size: 0.75rem; }
</style>
@endpush
