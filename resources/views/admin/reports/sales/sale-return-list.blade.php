@extends('layouts.admin')

@section('title', 'Sale Return List')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i>Sale Return List</h5>
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
        <div class="col-md-2"><div class="card bg-danger text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Returns</small><h5 class="mb-0">{{ number_format($totals['count']) }}</h5>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-secondary text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Items Qty</small><h5 class="mb-0">{{ number_format($totals['items_count']) }}</h5>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-info text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">NT Amount</small><h5 class="mb-0">₹{{ number_format($totals['nt_amount'], 2) }}</h5>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-warning text-dark"><div class="card-body py-2 text-center">
            <small>Discount</small><h5 class="mb-0">₹{{ number_format($totals['dis_amount'], 2) }}</h5>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-primary text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Tax</small><h5 class="mb-0">₹{{ number_format($totals['tax_amount'], 2) }}</h5>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-success text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Net Amount</small><h5 class="mb-0">₹{{ number_format($totals['net_amount'], 2) }}</h5>
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
                            <th>SR No</th>
                            <th>Orig. Invoice</th>
                            <th>Customer</th>
                            <th>Salesman</th>
                            <th class="text-end">Items</th>
                            <th class="text-end">NT Amt</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amt</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $index => $return)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $return->return_date->format('d-m-Y') }}</td>
                            <td>{{ $return->sr_no }}</td>
                            <td>{{ $return->original_invoice_no ?? '-' }}</td>
                            <td>{{ $return->customer->name ?? 'N/A' }}</td>
                            <td>{{ $return->salesman->name ?? '-' }}</td>
                            <td class="text-end">{{ $return->items->sum('qty') }}</td>
                            <td class="text-end">{{ number_format($return->nt_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($return->tax_amount, 2) }}</td>
                            <td class="text-end fw-bold text-danger">₹{{ number_format($return->net_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.sale-return.show', $return->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="text-center text-muted py-4">No sale returns found</td></tr>
                        @endforelse
                    </tbody>
                    @if($returns->count() > 0)
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totals['items_count']) }}</td>
                            <td class="text-end">{{ number_format($totals['nt_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'], 2) }}</td>
                            <td class="text-end text-danger">₹{{ number_format($totals['net_amount'], 2) }}</td>
                            <td></td>
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
    params.set('report_type', 'sale-return-list');
    window.open('{{ route("admin.reports.sales.export-csv") }}?' + params.toString(), '_blank');
}
</script>
@endpush
