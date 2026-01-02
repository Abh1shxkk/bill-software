@extends('layouts.admin')
@section('title', 'Sale Book Summarised')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">Sale Book Summarised</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" action="{{ route('admin.reports.sales.other.sale-book-summarised') }}" id="filterForm">
        <div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">From</span><input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}"></div></div>
        </div>
        <div class="row g-2 align-items-end mt-2">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">Selective [Y/N]</span><select name="selective" class="form-select"><option value="N" {{ ($selective ?? 'N') == 'N' ? 'selected' : '' }}>N</option><option value="Y" {{ ($selective ?? '') == 'Y' ? 'selected' : '' }}>Y</option></select></div></div>
        </div>
        <div class="row g-2 align-items-end mt-2">
            <div class="col-md-1"><label class="form-label mb-0 small">Customer</label></div>
            <div class="col-md-2"><input type="text" name="customer_code" class="form-control form-control-sm" value="{{ $customerCode ?? '00' }}" placeholder="00"></div>
            <div class="col-md-4"><select name="customer_id" class="form-select form-select-sm"><option value="">All Customers</option>@foreach($customers ?? [] as $cust)<option value="{{ $cust->id }}" {{ ($customerId ?? '') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>@endforeach</select></div>
        </div>
        </form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 50vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>#</th><th>Customer Code</th><th>Customer Name</th><th class="text-end">Bills</th><th class="text-end">Gross Amt</th><th class="text-end">Discount</th><th class="text-end">Tax</th><th class="text-end">Net Amount</th></tr></thead>
            <tbody>
                @forelse($summary ?? [] as $index => $row)
                <tr><td>{{ $index + 1 }}</td><td>{{ $row['customer_code'] ?? '' }}</td><td>{{ $row['customer_name'] ?? '' }}</td><td class="text-end">{{ number_format($row['bill_count'] ?? 0) }}</td><td class="text-end">{{ number_format($row['gross'] ?? 0, 2) }}</td><td class="text-end text-danger">{{ number_format($row['disc'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($row['tax'] ?? 0, 2) }}</td><td class="text-end fw-bold">{{ number_format($row['net'] ?? 0, 2) }}</td></tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Select filters and click "Excel" to export</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals) && count($summary ?? []) > 0)<tfoot class="table-dark fw-bold"><tr><td colspan="3" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['bills'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['gross'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['disc'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['net'] ?? 0, 2) }}</td></tr></tfoot>@endif
        </table>
    </div></div></div>
    <div class="card mt-2"><div class="card-body py-2"><div class="d-flex justify-content-end gap-2">
        <button type="submit" form="filterForm" class="btn btn-outline-primary btn-sm">E<u>x</u>cel</button><a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary btn-sm">Close</a>
    </div></div></div>
</div>
@endsection
@push('styles')<style>.input-group-text{font-size:.7rem}.form-control,.form-select{font-size:.75rem}.table th,.table td{padding:.3rem .4rem;font-size:.75rem}.btn-sm{font-size:.75rem}.sticky-top{position:sticky;top:0;z-index:10}</style>@endpush
