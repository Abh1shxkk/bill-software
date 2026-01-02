@extends('layouts.admin')
@section('title', 'Party Volume Discount')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">PARTY VOLUME DISCOUNT</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" action="{{ route('admin.reports.sales.other.party-volume-discount') }}" id="filterForm">
        <div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">From</span><input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->startOfMonth()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}"></div></div>
        </div>
        <div class="row g-2 align-items-end mt-2">
            <div class="col-md-4"><div class="input-group input-group-sm"><span class="input-group-text">Party</span><select name="customer_id" class="form-select"><option value="">All Parties</option>@foreach($customers ?? [] as $cust)<option value="{{ $cust->id }}" {{ ($customerId ?? '') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>@endforeach</select></div></div>
        </div>
        </form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 55vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>#</th><th>Party Code</th><th>Party Name</th><th class="text-end">Total Sale</th><th class="text-end">Volume Disc</th><th class="text-end">Net Amount</th></tr></thead>
            <tbody>
                @forelse($discounts ?? [] as $index => $disc)
                <tr><td>{{ $index + 1 }}</td><td>{{ $disc['party_code'] ?? '' }}</td><td>{{ $disc['party_name'] ?? '' }}</td><td class="text-end">{{ number_format($disc['total_sale'] ?? 0, 2) }}</td><td class="text-end text-success">{{ number_format($disc['volume_discount'] ?? 0, 2) }}</td><td class="text-end fw-bold">{{ number_format($disc['net_amount'] ?? 0, 2) }}</td></tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No records found</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals) && count($discounts ?? []) > 0)<tfoot class="table-dark fw-bold"><tr><td colspan="3" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['total_sale'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['volume_discount'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td></tr></tfoot>@endif
        </table>
    </div></div></div>
    <div class="card mt-2"><div class="card-body py-2"><div class="d-flex justify-content-end gap-2">
        <button type="submit" form="filterForm" class="btn btn-outline-primary btn-sm"><u>V</u>iew</button><a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary btn-sm">Close</a>
    </div></div></div>
</div>
@endsection
@push('styles')<style>.input-group-text{font-size:.7rem}.form-control,.form-select{font-size:.75rem}.table th,.table td{padding:.3rem .4rem;font-size:.75rem}.btn-sm{font-size:.75rem}.sticky-top{position:sticky;top:0;z-index:10}</style>@endpush
