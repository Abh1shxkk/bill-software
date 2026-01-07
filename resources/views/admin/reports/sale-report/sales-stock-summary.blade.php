@extends('layouts.admin')

@section('title', 'Stock Sale Summary')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">STOCK SALE SUMMARY</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.sales-stock-summary') }}">
                <div class="row g-2 align-items-end">
                    <!-- Row 1 -->
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">S/R/C</span>
                            <select name="report_type" class="form-select text-uppercase">
                                <option value="S" {{ ($reportType ?? 'S') == 'S' ? 'selected' : '' }}>S</option>
                                <option value="R" {{ ($reportType ?? '') == 'R' ? 'selected' : '' }}>R</option>
                                <option value="C" {{ ($reportType ?? '') == 'C' ? 'selected' : '' }}>C</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Show Total</span>
                            <select name="show_total" class="form-select text-uppercase">
                                <option value="Y" {{ ($showTotal ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($showTotal ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Never Printed</span>
                            <select name="never_printed" class="form-select text-uppercase">
                                <option value="Y" {{ ($neverPrinted ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($neverPrinted ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">S/A/R</span>
                            <select name="group_by" class="form-select text-uppercase">
                                <option value="S" {{ ($groupBy ?? 'S') == 'S' ? 'selected' : '' }}>S</option>
                                <option value="A" {{ ($groupBy ?? '') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="R" {{ ($groupBy ?? '') == 'R' ? 'selected' : '' }}>R</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Across/Down</span>
                            <select name="across_down" class="form-select text-uppercase">
                                <option value="Y" {{ ($acrossDown ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($acrossDown ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Vou.Type</span>
                            <input type="text" name="vou_type" class="form-control text-uppercase" value="{{ $vouType ?? '00' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sales Man</span>
                            <select name="salesman_id" class="form-select">
                                <option value="">All</option>
                                @foreach($salesmen ?? [] as $salesman)
                                    <option value="{{ $salesman->id }}" {{ ($salesmanId ?? '') == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->code ?? '' }} - {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Area</span>
                            <select name="area_id" class="form-select">
                                <option value="">All</option>
                                @foreach($areas ?? [] as $area)
                                    <option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Route</span>
                            <select name="route_id" class="form-select">
                                <option value="">All</option>
                                @foreach($routes ?? [] as $route)
                                    <option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <u>V</u>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">
                            <u>P</u>rint
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-light border px-4 fw-bold shadow-sm">
                            <u>C</u>lose
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table - Only show when view is clicked -->
    @if(request()->has('view') && isset($groupedSales) && count($groupedSales) > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 80px;">Date</th>
                            <th style="width: 80px;">TRN. No.</th>
                            <th>Party Name</th>
                            <th>Sales Man</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                            <th class="text-center" style="width: 40px;">Tag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedSales ?? [] as $groupName => $sales)
                            <tr class="table-warning">
                                <td colspan="6" class="fw-bold">
                                    <i class="bi bi-person-badge me-1"></i>{{ $groupName }}
                                    <span class="badge bg-primary ms-2">{{ $sales->count() }} Bills</span>
                                </td>
                            </tr>
                            @foreach($sales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none">
                                        {{ $sale->series }}{{ $sale->invoice_no }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($sale->customer->name ?? 'N/A', 30) }}</td>
                                <td>{{ $sale->salesman->name ?? '' }}</td>
                                <td class="text-end">{{ number_format((float)($sale->net_amount ?? 0), 2) }}</td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input tag-checkbox" data-id="{{ $sale->id }}">
                                </td>
                            </tr>
                            @endforeach
                            <tr class="table-secondary">
                                <td colspan="4" class="text-end fw-bold">{{ $groupName }} Total:</td>
                                <td class="text-end fw-bold">{{ number_format($sales->sum('net_amount'), 2) }}</td>
                                <td></td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate Stock Sale Summary
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Totals Footer -->
    <div class="card mt-2">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-primary fst-italic">TAG (+) / UNTAG (-)</span>
                </div>
                <div class="d-flex gap-4">
                    <div>
                        <span class="text-muted">TOTAL:</span>
                        <span class="text-primary fw-bold ms-2">{{ number_format($totals['count'] ?? 0) }}</span>
                        <span class="text-primary fw-bold ms-3">{{ number_format($totals['net_amount'] ?? 0, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-muted">TAGGED:</span>
                        <span class="text-primary fw-bold ms-2">{{ number_format($totals['tagged'] ?? 0) }}</span>
                        <span class="text-primary fw-bold ms-3">{{ number_format($totals['tagged_amount'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sales-stock-summary") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.sales-stock-summary") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        printReport();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
    if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();
        exportToExcel();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.8rem; border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
