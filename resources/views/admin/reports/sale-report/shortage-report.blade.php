@extends('layouts.admin')

@section('title', 'Shortage Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">Shortage Report</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">
                    <!-- Row 1: Date Range -->
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
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2: Company -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Company</span>
                            <select name="company_id" class="form-select">
                                <option value="">All</option>
                                @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 3: Format -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">(D)etailed / (S)ummerized</span>
                            <select name="report_format" class="form-select" style="width: 50px;">
                                <option value="D" {{ ($reportFormat ?? 'D') == 'D' ? 'selected' : '' }}>D</option>
                                <option value="S" {{ ($reportFormat ?? '') == 'S' ? 'selected' : '' }}>S</option>
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

    <!-- Summary Cards -->
    @if(isset($totals) && ($totals['items'] ?? 0) > 0)
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Items</small>
                    <h6 class="mb-0">{{ number_format($totals['items'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Out of Stock</small>
                    <h6 class="mb-0">{{ number_format($totals['out_of_stock'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2 text-center">
                    <small>Low Stock</small>
                    <h6 class="mb-0">{{ number_format($totals['low_stock'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Sold Qty</small>
                    <h6 class="mb-0">{{ number_format($totals['sold_qty'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Shortage</small>
                    <h6 class="mb-0">{{ number_format($totals['shortage_qty'] ?? 0) }}</h6>
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
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 70px;">Item Code</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th style="width: 70px;">Packing</th>
                            <th class="text-end" style="width: 70px;">Sold Qty</th>
                            <th class="text-end" style="width: 70px;">Stock</th>
                            <th class="text-end" style="width: 70px;">Shortage</th>
                            <th class="text-center" style="width: 90px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shortageItems ?? [] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item['item_code'] }}</td>
                            <td>{{ Str::limit($item['item_name'], 30) }}</td>
                            <td>{{ Str::limit($item['company_name'] ?? '', 20) }}</td>
                            <td>{{ $item['packing'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($item['sold_qty']) }}</td>
                            <td class="text-end">{{ number_format($item['current_stock']) }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($item['shortage_qty']) }}</td>
                            <td class="text-center">
                                @if($item['status'] === 'Out of Stock')
                                    <span class="badge bg-danger">Out of Stock</span>
                                @else
                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No shortage items found. All items have sufficient stock!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($totals) && ($totals['items'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Total ({{ number_format($totals['items'] ?? 0) }} Items):</td>
                            <td class="text-end">{{ number_format($totals['sold_qty'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($totals['current_stock'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($totals['shortage_qty'] ?? 0) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card mt-2">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between">
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
    window.open('{{ route("admin.reports.sales.shortage-report") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.shortage-report") }}?' + params.toString(), 'ShortageReport', 'width=1100,height=800,scrollbars=yes,resizable=yes');
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
