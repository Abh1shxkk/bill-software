@extends('layouts.admin')

@section('title', 'Sales Stock Summary')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Sales Stock Summary</h5>
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

    <!-- Summary -->
    <div class="row g-2 mb-3">
        <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Items</small><h5 class="mb-0">{{ number_format($totals['items']) }}</h5>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Qty Sold</small><h5 class="mb-0">{{ number_format($totals['qty']) }}</h5>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Free Qty</small><h5 class="mb-0">{{ number_format($totals['free']) }}</h5>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body py-2 text-center">
            <small>Total Amount</small><h5 class="mb-0">₹{{ number_format($totals['amount'], 2) }}</h5>
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
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th>HSN</th>
                            <th class="text-end">Qty Sold</th>
                            <th class="text-end">Free</th>
                            <th class="text-end">Avg Rate</th>
                            <th class="text-end">Bills</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->company_name }}</td>
                            <td>{{ $item->hsn_code }}</td>
                            <td class="text-end">{{ number_format($item->total_qty) }}</td>
                            <td class="text-end">{{ number_format($item->total_free) }}</td>
                            <td class="text-end">{{ number_format($item->avg_rate, 2) }}</td>
                            <td class="text-end">{{ $item->bill_count }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No data found</td></tr>
                        @endforelse
                    </tbody>
                    @if($items->count() > 0)
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totals['qty']) }}</td>
                            <td class="text-end">{{ number_format($totals['free']) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">₹{{ number_format($totals['amount'], 2) }}</td>
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
    params.set('report_type', 'sales-stock-summary');
    window.open('{{ route("admin.reports.sales.export-csv") }}?' + params.toString(), '_blank');
}
</script>
@endpush
