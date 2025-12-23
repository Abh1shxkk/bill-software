@extends('layouts.admin')

@section('title', 'Sale Challan Book')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Sale Challan Book</h5>
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
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="invoiced" {{ $status == 'invoiced' ? 'selected' : '' }}>Invoiced</option>
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
            <small class="text-white-50">Total Challans</small><h5 class="mb-0">{{ number_format($totals['count']) }}</h5>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body py-2 text-center">
            <small>Pending</small><h5 class="mb-0">{{ number_format($totals['pending']) }}</h5>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Invoiced</small><h5 class="mb-0">{{ number_format($totals['invoiced']) }}</h5>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Amount</small><h5 class="mb-0">₹{{ number_format($totals['net_amount'], 2) }}</h5>
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
                            <th>Challan No</th>
                            <th>Customer</th>
                            <th>Salesman</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Net Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($challans as $index => $challan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $challan->challan_date->format('d-m-Y') }}</td>
                            <td><a href="{{ route('admin.sale-challan.show', $challan->id) }}">{{ $challan->challan_no }}</a></td>
                            <td>{{ $challan->customer->name ?? 'N/A' }}</td>
                            <td>{{ $challan->salesman->name ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $challan->is_invoiced ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $challan->is_invoiced ? 'Invoiced' : 'Pending' }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">₹{{ number_format($challan->net_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.sale-challan.show', $challan->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No challans found</td></tr>
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
    params.set('report_type', 'sale-challan-book');
    window.open('{{ route("admin.reports.sales.export-csv") }}?' + params.toString(), '_blank');
}
</script>
@endpush
