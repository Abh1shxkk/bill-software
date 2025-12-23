@extends('layouts.admin')

@section('title', 'Customer Visit Status')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Customer Visit Status</h5>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-success" onclick="exportReport('csv')"><i class="bi bi-file-excel me-1"></i>CSV</button>
            <button type="button" class="btn btn-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Salesman</label>
                        <select name="salesman_id" class="form-select form-select-sm">
                            <option value="">All Salesmen</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" {{ $salesmanId == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Area</label>
                        <select name="area_id" class="form-select form-select-sm">
                            <option value="">All Areas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ $areaId == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="row g-2 mb-3">
        <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Customers</small><h5 class="mb-0">{{ number_format($totals['total_customers']) }}</h5>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Visited</small><h5 class="mb-0">{{ number_format($totals['visited']) }}</h5>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Not Visited</small><h5 class="mb-0">{{ number_format($totals['not_visited']) }}</h5>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Sales</small><h5 class="mb-0">₹{{ number_format($totals['total_amount'], 2) }}</h5>
        </div></div></div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 500px;">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Area</th>
                            <th>Route</th>
                            <th>Salesman</th>
                            <th>Mobile</th>
                            <th class="text-center">Visits</th>
                            <th class="text-end">Amount</th>
                            <th>Last Visit</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report as $index => $row)
                        <tr class="{{ $row['visit_count'] == 0 ? 'table-danger' : '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['customer']->name }}</td>
                            <td>{{ $row['customer']->area_name ?? '-' }}</td>
                            <td>{{ $row['customer']->route_name ?? '-' }}</td>
                            <td>{{ $row['customer']->sales_man_name ?? '-' }}</td>
                            <td>{{ $row['customer']->mobile ?? '-' }}</td>
                            <td class="text-center">{{ $row['visit_count'] }}</td>
                            <td class="text-end">₹{{ number_format($row['total_amount'], 2) }}</td>
                            <td>{{ $row['last_visit'] ? \Carbon\Carbon::parse($row['last_visit'])->format('d-m-Y') : '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $row['visit_count'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No customers found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportReport(type) {
    const params = new URLSearchParams(window.location.search);
    params.set('report_type', 'customer-visit-status');
    window.open('{{ route("admin.reports.sales.export-csv") }}?' + params.toString(), '_blank');
}
</script>
@endpush
