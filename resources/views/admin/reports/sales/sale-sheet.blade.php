@extends('layouts.admin')

@section('title', 'Sale Sheet')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-file-spreadsheet me-2"></i>Sale Sheet</h5>
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
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Item</label>
                        <select name="item_id" class="form-select form-select-sm">
                            <option value="">All Items</option>
                            @foreach($itemsList as $item)
                                <option value="{{ $item->id }}" {{ $itemId == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Company</label>
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">All</option>
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
        <div class="col-md-2"><div class="card bg-primary text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Qty</small><h6 class="mb-0">{{ number_format($totals['qty']) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-info text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Free</small><h6 class="mb-0">{{ number_format($totals['free_qty']) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-secondary text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Amount</small><h6 class="mb-0">₹{{ number_format($totals['amount'], 2) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-warning text-dark"><div class="card-body py-2 text-center">
            <small>Discount</small><h6 class="mb-0">₹{{ number_format($totals['discount'], 2) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-danger text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Tax</small><h6 class="mb-0">₹{{ number_format($totals['tax'], 2) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-success text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Net</small><h6 class="mb-0">₹{{ number_format($totals['net_amount'], 2) }}</h6>
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
                            <th>Date</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Item</th>
                            <th>Batch</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Free</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Disc</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->saleTransaction->sale_date->format('d-m-Y') }}</td>
                            <td><a href="{{ route('admin.sale.show', $item->sale_transaction_id) }}">{{ $item->saleTransaction->invoice_no }}</a></td>
                            <td>{{ $item->saleTransaction->customer->name ?? 'N/A' }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->batch_no }}</td>
                            <td class="text-end">{{ number_format($item->qty) }}</td>
                            <td class="text-end">{{ number_format($item->free_qty) }}</td>
                            <td class="text-end">{{ number_format($item->sale_rate, 2) }}</td>
                            <td class="text-end">{{ number_format($item->discount_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($item->tax_amount, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->net_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="12" class="text-center text-muted py-4">No data found</td></tr>
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
    params.set('report_type', 'sale-sheet');
    window.open('{{ route("admin.reports.sales.export-csv") }}?' + params.toString(), '_blank');
}
</script>
@endpush
