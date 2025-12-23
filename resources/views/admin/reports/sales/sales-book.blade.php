@extends('layouts.admin')

@section('title', 'Sales Book')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Sales Book</h5>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-success" onclick="exportReport('csv')">
                <i class="bi bi-file-excel me-1"></i>CSV
            </button>
            <button type="button" class="btn btn-danger" onclick="exportReport('pdf')">
                <i class="bi bi-file-pdf me-1"></i>PDF
            </button>
            <button type="button" class="btn btn-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Salesman</label>
                        <select name="salesman_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" {{ $salesmanId == $salesman->id ? 'selected' : '' }}>
                                    {{ $salesman->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Series</label>
                        <select name="series" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($seriesList as $s)
                                <option value="{{ $s }}" {{ $series == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-white-50">Total Invoices</small>
                            <h5 class="mb-0">{{ number_format($totals['count']) }}</h5>
                        </div>
                        <i class="bi bi-receipt fs-3 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-white-50">Net Amount</small>
                            <h5 class="mb-0">₹{{ number_format($totals['net_amount'], 2) }}</h5>
                        </div>
                        <i class="bi bi-currency-rupee fs-3 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-white-50">Total Tax</small>
                            <h5 class="mb-0">₹{{ number_format($totals['tax_amount'], 2) }}</h5>
                        </div>
                        <i class="bi bi-percent fs-3 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small>Total Discount</small>
                            <h5 class="mb-0">₹{{ number_format($totals['dis_amount'], 2) }}</h5>
                        </div>
                        <i class="bi bi-tag fs-3 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0" id="salesTable">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Series</th>
                            <th>Customer</th>
                            <th>Salesman</th>
                            <th class="text-end">NT Amt</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $index => $sale)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none">
                                    {{ $sale->invoice_no }}
                                </a>
                            </td>
                            <td>{{ $sale->series }}</td>
                            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td>{{ $sale->salesman->name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($sale->nt_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->dis_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->tax_amount, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($sale->net_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No sales found for selected period</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($sales->count() > 0)
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totals['nt_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['dis_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'], 2) }}</td>
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
function exportReport(type) {
    const params = new URLSearchParams(window.location.search);
    params.set('report_type', 'sales-book');
    const url = type === 'csv' 
        ? '{{ route("admin.reports.sales.export-csv") }}?' + params.toString()
        : '{{ route("admin.reports.sales.export-pdf") }}?' + params.toString();
    window.open(url, '_blank');
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn-group, form, .card-header { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
