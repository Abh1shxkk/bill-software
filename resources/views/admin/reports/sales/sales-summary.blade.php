@extends('layouts.admin')

@section('title', 'Sales Summary')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Sales Summary</h5>
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
                        <label class="form-label small mb-1">Group By</label>
                        <select name="group_by" class="form-select form-select-sm">
                            <option value="date" {{ $groupBy == 'date' ? 'selected' : '' }}>Date Wise</option>
                            <option value="customer" {{ $groupBy == 'customer' ? 'selected' : '' }}>Customer Wise</option>
                            <option value="salesman" {{ $groupBy == 'salesman' ? 'selected' : '' }}>Salesman Wise</option>
                            <option value="company" {{ $groupBy == 'company' ? 'selected' : '' }}>Company Wise</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Grand Totals -->
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white"><div class="card-body py-2">
                <small class="text-white-50">Total Invoices</small>
                <h5 class="mb-0">{{ number_format($grandTotals['invoices']) }}</h5>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white"><div class="card-body py-2">
                <small class="text-white-50">Net Amount</small>
                <h5 class="mb-0">₹{{ number_format($grandTotals['net_amount'], 2) }}</h5>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white"><div class="card-body py-2">
                <small class="text-white-50">Total Tax</small>
                <h5 class="mb-0">₹{{ number_format($grandTotals['tax_amount'], 2) }}</h5>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark"><div class="card-body py-2">
                <small>Total Discount</small>
                <h5 class="mb-0">₹{{ number_format($grandTotals['dis_amount'], 2) }}</h5>
            </div></div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>#</th>
                            @if($groupBy == 'date')
                                <th>Date</th>
                            @elseif($groupBy == 'customer')
                                <th>Customer</th>
                            @elseif($groupBy == 'salesman')
                                <th>Salesman</th>
                            @else
                                <th>Company</th>
                            @endif
                            <th class="text-end">Invoices</th>
                            @if($groupBy != 'company')
                            <th class="text-end">NT Amount</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Tax</th>
                            @else
                            <th class="text-end">Qty</th>
                            @endif
                            <th class="text-end">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if($groupBy == 'date')
                                <td>{{ \Carbon\Carbon::parse($row->sale_date)->format('d-m-Y') }}</td>
                            @elseif($groupBy == 'customer')
                                <td>{{ $row->customer->name ?? 'Unknown' }}</td>
                            @elseif($groupBy == 'salesman')
                                <td>{{ $row->salesman->name ?? 'Unknown' }}</td>
                            @else
                                <td>{{ $row->company_name ?? 'Unknown' }}</td>
                            @endif
                            <td class="text-end">{{ number_format($row->invoice_count) }}</td>
                            @if($groupBy != 'company')
                            <td class="text-end">{{ number_format($row->total_nt, 2) }}</td>
                            <td class="text-end">{{ number_format($row->total_dis, 2) }}</td>
                            <td class="text-end">{{ number_format($row->total_tax, 2) }}</td>
                            @else
                            <td class="text-end">{{ number_format($row->total_qty) }}</td>
                            @endif
                            <td class="text-end fw-bold">₹{{ number_format($row->total_net, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No data found</td></tr>
                        @endforelse
                    </tbody>
                    @if($summary->count() > 0)
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($grandTotals['invoices']) }}</td>
                            @if($groupBy != 'company')
                            <td class="text-end">{{ number_format($grandTotals['nt_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($grandTotals['dis_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($grandTotals['tax_amount'], 2) }}</td>
                            @else
                            <td class="text-end">-</td>
                            @endif
                            <td class="text-end">₹{{ number_format($grandTotals['net_amount'], 2) }}</td>
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
    params.set('report_type', 'sales-summary');
    window.open('{{ route("admin.reports.sales.export-csv") }}?' + params.toString(), '_blank');
}
</script>
@endpush
