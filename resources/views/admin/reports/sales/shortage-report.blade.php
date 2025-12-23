@extends('layouts.admin')

@section('title', 'Shortage Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Shortage Report</h5>
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
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Company</label>
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">All Companies</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $companyId == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
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

    <!-- Alert -->
    <div class="alert alert-warning py-2 mb-3">
        <i class="bi bi-info-circle me-2"></i>
        Showing items that are <strong>Out of Stock</strong> or have <strong>Low Stock</strong> compared to recent sales.
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 500px;">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end">Sold Qty</th>
                            <th class="text-end">Current Stock</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shortageItems as $index => $item)
                        <tr class="{{ $item['shortage'] == 'Out of Stock' ? 'table-danger' : 'table-warning' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['item_code'] }}</td>
                            <td>{{ $item['item_name'] }}</td>
                            <td>{{ $item['company_name'] }}</td>
                            <td class="text-end">{{ number_format($item['sold_qty']) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item['current_stock']) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $item['shortage'] == 'Out of Stock' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ $item['shortage'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-success py-4">
                            <i class="bi bi-check-circle me-2"></i>No shortage items found. Stock levels are healthy!
                        </td></tr>
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
    params.set('report_type', 'shortage-report');
    window.open('{{ route("admin.reports.sales.export-csv") }}?' + params.toString(), '_blank');
}
</script>
@endpush
