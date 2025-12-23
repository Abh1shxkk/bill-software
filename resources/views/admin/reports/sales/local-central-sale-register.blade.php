@extends('layouts.admin')

@section('title', 'Local / Central Sale Register')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Local / Central Sale Register</h5>
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
                        <label class="form-label small mb-1">Sale Type</label>
                        <select name="sale_type" class="form-select form-select-sm">
                            <option value="all" {{ $saleType == 'all' ? 'selected' : '' }}>All Sales</option>
                            <option value="local" {{ $saleType == 'local' ? 'selected' : '' }}>Local (CGST/SGST)</option>
                            <option value="central" {{ $saleType == 'central' ? 'selected' : '' }}>Central (IGST)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-white-50">Local Sales (CGST/SGST)</small>
                            <h5 class="mb-0">₹{{ number_format($totals['local']['net_amount'], 2) }}</h5>
                            <small>{{ $totals['local']['count'] }} Invoices | Tax: ₹{{ number_format($totals['local']['tax_amount'], 2) }}</small>
                        </div>
                        <i class="bi bi-house fs-2 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-white-50">Central Sales (IGST)</small>
                            <h5 class="mb-0">₹{{ number_format($totals['central']['net_amount'], 2) }}</h5>
                            <small>{{ $totals['central']['count'] }} Invoices | Tax: ₹{{ number_format($totals['central']['tax_amount'], 2) }}</small>
                        </div>
                        <i class="bi bi-globe fs-2 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-white-50">Total Sales</small>
                            <h5 class="mb-0">₹{{ number_format($totals['total']['net_amount'], 2) }}</h5>
                            <small>{{ $totals['total']['count'] }} Invoices | Tax: ₹{{ number_format($totals['total']['tax_amount'], 2) }}</small>
                        </div>
                        <i class="bi bi-currency-rupee fs-2 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 500px;">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>GST No</th>
                            <th>State</th>
                            <th class="text-center">Type</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $index => $sale)
                        @php $isLocal = ($sale->customer->local_central ?? 'L') === 'L'; @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td><a href="{{ route('admin.sale.show', $sale->id) }}">{{ $sale->invoice_no }}</a></td>
                            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td class="small">{{ $sale->customer->gst_number ?? '-' }}</td>
                            <td>{{ $sale->customer->state_name ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $isLocal ? 'bg-success' : 'bg-info' }}">
                                    {{ $isLocal ? 'Local' : 'Central' }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($sale->tax_amount, 2) }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($sale->net_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No sales found</td></tr>
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
    params.set('report_type', 'local-central-register');
    window.open('{{ route("admin.reports.sales.export-csv") }}?' + params.toString(), '_blank');
}
</script>
@endpush
