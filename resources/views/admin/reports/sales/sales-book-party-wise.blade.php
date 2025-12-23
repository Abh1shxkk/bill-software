@extends('layouts.admin')

@section('title', 'Sale Book Party Wise')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Sale Book Party Wise</h5>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-success" onclick="exportReport('csv')"><i class="bi bi-file-excel me-1"></i>CSV</button>
            <button type="button" class="btn btn-danger" onclick="exportReport('pdf')"><i class="bi bi-file-pdf me-1"></i>PDF</button>
            <button type="button" class="btn btn-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
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
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
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
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white"><div class="card-body py-2">
                <small class="text-white-50">Total Parties</small>
                <h5 class="mb-0">{{ $groupedSales->count() }}</h5>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white"><div class="card-body py-2">
                <small class="text-white-50">Total Invoices</small>
                <h5 class="mb-0">{{ number_format($totals['count']) }}</h5>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white"><div class="card-body py-2">
                <small class="text-white-50">Net Amount</small>
                <h5 class="mb-0">₹{{ number_format($totals['net_amount'], 2) }}</h5>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark"><div class="card-body py-2">
                <small>Total Tax</small>
                <h5 class="mb-0">₹{{ number_format($totals['tax_amount'], 2) }}</h5>
            </div></div>
        </div>
    </div>

    <!-- Party Wise Data -->
    @foreach($groupedSales as $customerId => $customerSales)
    @php $customer = $customerSales->first()->customer; @endphp
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $customer->name ?? 'Unknown' }}</strong>
                    <small class="text-muted ms-2">({{ $customer->code ?? '' }})</small>
                    @if($customer->area_name)<span class="badge bg-secondary ms-2">{{ $customer->area_name }}</span>@endif
                </div>
                <div>
                    <span class="badge bg-primary">{{ $customerSales->count() }} Bills</span>
                    <span class="badge bg-success">₹{{ number_format($customerSales->sum('net_amount'), 2) }}</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Salesman</th>
                        <th class="text-end">NT Amt</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Tax</th>
                        <th class="text-end">Net Amt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customerSales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                        <td><a href="{{ route('admin.sale.show', $sale->id) }}">{{ $sale->invoice_no }}</a></td>
                        <td>{{ $sale->salesman->name ?? '-' }}</td>
                        <td class="text-end">{{ number_format($sale->nt_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($sale->dis_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($sale->tax_amount, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($sale->net_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">Party Total:</td>
                        <td class="text-end">{{ number_format($customerSales->sum('nt_amount'), 2) }}</td>
                        <td class="text-end">{{ number_format($customerSales->sum('dis_amount'), 2) }}</td>
                        <td class="text-end">{{ number_format($customerSales->sum('tax_amount'), 2) }}</td>
                        <td class="text-end">{{ number_format($customerSales->sum('net_amount'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endforeach

    @if($groupedSales->isEmpty())
    <div class="alert alert-info text-center">No sales found for selected period</div>
    @endif

    <!-- Grand Total -->
    @if($groupedSales->isNotEmpty())
    <div class="card bg-dark text-white">
        <div class="card-body py-2">
            <div class="row text-center">
                <div class="col-md-3"><small class="text-white-50">Total Parties</small><h5>{{ $groupedSales->count() }}</h5></div>
                <div class="col-md-3"><small class="text-white-50">Total Invoices</small><h5>{{ $totals['count'] }}</h5></div>
                <div class="col-md-3"><small class="text-white-50">Total Tax</small><h5>₹{{ number_format($totals['tax_amount'], 2) }}</h5></div>
                <div class="col-md-3"><small class="text-white-50">Grand Total</small><h5>₹{{ number_format($totals['net_amount'], 2) }}</h5></div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportReport(type) {
    const params = new URLSearchParams(window.location.search);
    params.set('report_type', 'sales-book-party-wise');
    window.open((type === 'csv' ? '{{ route("admin.reports.sales.export-csv") }}' : '{{ route("admin.reports.sales.export-pdf") }}') + '?' + params.toString(), '_blank');
}
</script>
@endpush
