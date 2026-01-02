@extends('layouts.admin')
@section('title', 'Customer Consistency Report')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">Customer Consistency Report</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" action="{{ route('admin.reports.sales.other.customer-consistency') }}" id="filterForm"><div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">Period1 From</span><input type="date" name="period1_from" class="form-control" value="{{ $period1From ?? now()->subMonth()->startOfMonth()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="date" name="period1_to" class="form-control" value="{{ $period1To ?? now()->subMonth()->endOfMonth()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">Period2 From</span><input type="date" name="period2_from" class="form-control" value="{{ $period2From ?? now()->startOfMonth()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="date" name="period2_to" class="form-control" value="{{ $period2To ?? now()->format('Y-m-d') }}"></div></div>
        </div>
        <div class="row g-2 align-items-end mt-1">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">Report Type</span><select name="report_type" class="form-select"><option value="1" {{ ($reportType ?? '3') == '1' ? 'selected' : '' }}>Consistent</option><option value="2" {{ ($reportType ?? '') == '2' ? 'selected' : '' }}>Others</option><option value="3" {{ ($reportType ?? '3') == '3' ? 'selected' : '' }}>All</option></select></div></div>
            <div class="col-md-3"><div class="input-group input-group-sm"><span class="input-group-text">Items</span><select name="item_id" class="form-select"><option value="">All Items</option>@foreach($itemsList ?? [] as $item)<option value="{{ $item->id }}" {{ ($itemId ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>@endforeach</select></div></div>
        </div></form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 55vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>#</th><th>Code</th><th>Customer Name</th><th>Area</th><th class="text-end">P1 Bills</th><th class="text-end">P1 Value</th><th class="text-end">P2 Bills</th><th class="text-end">P2 Value</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($customers ?? [] as $index => $cust)
                <tr><td>{{ $index + 1 }}</td><td>{{ $cust['code'] ?? '' }}</td><td>{{ $cust['name'] ?? '' }}</td><td>{{ $cust['area'] ?? '' }}</td><td class="text-end">{{ number_format($cust['period1_bills'] ?? 0) }}</td><td class="text-end">{{ number_format($cust['period1_value'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($cust['period2_bills'] ?? 0) }}</td><td class="text-end">{{ number_format($cust['period2_value'] ?? 0, 2) }}</td><td><span class="badge {{ ($cust['period1_bills'] ?? 0) > 0 && ($cust['period2_bills'] ?? 0) > 0 ? 'bg-success' : 'bg-warning' }}">{{ ($cust['period1_bills'] ?? 0) > 0 && ($cust['period2_bills'] ?? 0) > 0 ? 'Consistent' : 'Others' }}</span></td></tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No records found</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals) && count($customers ?? []) > 0)<tfoot class="table-dark fw-bold"><tr><td colspan="4" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['period1_bills'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['period1_value'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['period2_bills'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['period2_value'] ?? 0, 2) }}</td><td></td></tr></tfoot>@endif
        </table>
    </div></div></div>
    <div class="card mt-2"><div class="card-body py-2"><div class="d-flex justify-content-end gap-2">
        <button type="submit" form="filterForm" class="btn btn-outline-primary btn-sm"><u>V</u>iew</button><a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary btn-sm">Close</a>
    </div></div></div>
</div>
@endsection
@push('styles')<style>.input-group-text{font-size:.7rem}.form-control,.form-select{font-size:.75rem}.table th,.table td{padding:.3rem .4rem;font-size:.75rem}.btn-sm{font-size:.75rem}.sticky-top{position:sticky;top:0;z-index:10}</style>@endpush
