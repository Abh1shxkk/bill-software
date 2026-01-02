@extends('layouts.admin')

@section('title', 'Stock Sale Summary')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">-: Stock Sale Summary :-</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">
                    <!-- Row 1 -->
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">S/R/C</span>
                            <select name="report_type" class="form-select">
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
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Show Total</span>
                            <select name="show_total" class="form-select">
                                <option value="Y" {{ ($showTotal ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($showTotal ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Never Printed [Y/N]</span>
                            <select name="never_printed" class="form-select">
                                <option value="Y" {{ ($neverPrinted ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($neverPrinted ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2 -->
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Vou.Type</span>
                            <input type="text" name="vou_type" class="form-control" value="{{ $vouType ?? '00' }}" style="width: 40px;">
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
                    <div class="col-md-2">
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
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 3 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">S/A/R</span>
                            <select name="group_by" class="form-select">
                                <option value="S" {{ ($groupBy ?? 'S') == 'S' ? 'selected' : '' }}>S</option>
                                <option value="A" {{ ($groupBy ?? '') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="R" {{ ($groupBy ?? '') == 'R' ? 'selected' : '' }}>R</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Across/Down [Y/N]</span>
                            <select name="across_down" class="form-select">
                                <option value="Y" {{ ($acrossDown ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($acrossDown ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Ok</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
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
                                Select filters and click "Ok" to generate Stock Sale Summary
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons & Totals -->
    <div class="card mt-2">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info btn-sm" onclick="viewReport()">View (F7)</button>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">Close</a>
                    <button type="button" class="btn btn-warning btn-sm">Modify Batch (F3)</button>
                    <span class="text-primary fst-italic ms-2">TAG (+) / UNTAG (-)</span>
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
</div>
@endsection

@push('scripts')
<script>
function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.sales-stock-summary") }}?' + params.toString(), 'StockSaleSummary', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'F7') { e.preventDefault(); viewReport(); }
    if (e.key === 'Escape') { window.location.href = '{{ route("admin.reports.sales") }}'; }
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.65rem; padding: 0.15rem 0.3rem; min-width: auto; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.25rem 0.4rem; font-size: 0.75rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.2rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
