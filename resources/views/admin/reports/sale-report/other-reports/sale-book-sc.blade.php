@extends('layouts.admin')
@section('title', 'Sale Book SC')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">SALE BOOK SC</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" action="{{ route('admin.reports.sales.other.sale-book-sc') }}" id="filterForm"><div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">From</span><input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->startOfMonth()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">Series</span><select name="series" class="form-select"><option value="">All</option>@foreach($seriesList ?? [] as $s)<option value="{{ $s }}" {{ ($series ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach</select></div></div>
        </div></form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 55vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>#</th><th>Date</th><th>Bill No</th><th>Party Name</th><th class="text-end">Gross</th><th class="text-end">Disc</th><th class="text-end">SC</th><th class="text-end">Tax</th><th class="text-end">Net</th></tr></thead>
            <tbody>
                @forelse($sales ?? [] as $index => $sale)
                <tr><td>{{ $index + 1 }}</td><td>{{ $sale->sale_date->format('d-m-Y') ?? '' }}</td><td>{{ $sale->series ?? '' }}{{ $sale->invoice_no ?? '' }}</td><td>{{ Str::limit($sale->customer->name ?? 'N/A', 20) }}</td><td class="text-end">{{ number_format($sale->gross_amount ?? 0, 2) }}</td><td class="text-end text-danger">{{ number_format($sale->dis_amount ?? 0, 2) }}</td><td class="text-end text-info">{{ number_format($sale->sc_amount ?? 0, 2) }}</td><td class="text-end">{{ number_format($sale->tax_amount ?? 0, 2) }}</td><td class="text-end fw-bold">{{ number_format($sale->net_amount ?? 0, 2) }}</td></tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Select filters and click "View"</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals) && count($sales ?? []) > 0)<tfoot class="table-dark fw-bold"><tr><td colspan="4" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['gross'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['disc'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['sc'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['net'] ?? 0, 2) }}</td></tr></tfoot>@endif
        </table>
    </div></div></div>
    <div class="card mt-2"><div class="card-body py-2"><div class="d-flex justify-content-end gap-2">
        <button type="submit" form="filterForm" class="btn btn-outline-primary btn-sm"><u>V</u>iew</button><a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary btn-sm">Close</a>
    </div></div></div>
</div>
@endsection
@push('styles')<style>.input-group-text { font-size: 0.7rem; }.form-control, .form-select { font-size: 0.75rem; }.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; }.btn-sm { font-size: 0.75rem; }.sticky-top { position: sticky; top: 0; z-index: 10; }</style>@endpush
