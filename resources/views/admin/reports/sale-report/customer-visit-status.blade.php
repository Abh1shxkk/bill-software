@extends('layouts.admin')

@section('title', 'Customer Visit Status')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">-: Customer Visit Status :-</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">
                    <!-- Row 1: Date Range & Salesman -->
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
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2: Visit Filter & Group By -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">V/N/A</span>
                            <select name="visit_filter" class="form-select" style="width: 50px;">
                                <option value="A" {{ ($visitFilter ?? 'A') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="V" {{ ($visitFilter ?? '') == 'V' ? 'selected' : '' }}>V</option>
                                <option value="N" {{ ($visitFilter ?? '') == 'N' ? 'selected' : '' }}>N</option>
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
                    <!-- Row 3: S/A/R/All & Route -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">S/A/R/All</span>
                            <select name="group_by" class="form-select" style="width: 50px;">
                                <option value="S" {{ ($groupBy ?? 'S') == 'S' ? 'selected' : '' }}>S</option>
                                <option value="A" {{ ($groupBy ?? '') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="R" {{ ($groupBy ?? '') == 'R' ? 'selected' : '' }}>R</option>
                                <option value="All" {{ ($groupBy ?? '') == 'All' ? 'selected' : '' }}>All</option>
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
                            <th style="width: 60px;">Code</th>
                            <th>Party Name</th>
                            <th>Sales Man</th>
                            <th class="text-end" style="width: 80px;">No.of Bills</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report ?? [] as $item)
                        <tr class="{{ $item['visit_count'] == 0 ? 'table-warning' : '' }}">
                            <td>{{ $item['code'] }}</td>
                            <td>{{ Str::limit($item['name'], 35) }}</td>
                            <td>{{ $item['salesman'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($item['visit_count']) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item['total_amount'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "Ok" to generate Customer Visit Status
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer with Totals -->
    <div class="card mt-2">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span class="fst-italic text-primary">No. of Records: <span class="fw-bold">{{ number_format($totals['total_customers'] ?? 0) }}</span></span>
                    <span class="text-danger">Tel:</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-bold text-primary">{{ number_format($totals['total_amount'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                    <i class="bi bi-file-excel me-1"></i>Excel
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info btn-sm" onclick="viewReport()">
                        <i class="bi bi-eye me-1"></i>View
                    </button>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.customer-visit-status") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.customer-visit-status") }}?' + params.toString(), 'CustomerVisitStatus', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; min-width: auto; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
