@extends('layouts.admin')
@section('title', 'Frige Item Report')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">FRIGE ITEM REPORT</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" action="{{ route('admin.reports.sales.other.frige-item') }}" id="filterForm">
        <div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">Date</span><input type="date" name="date" class="form-control" value="{{ $date ?? now()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">Bill No. From</span><input type="text" name="bill_from" class="form-control" value="{{ $billFrom ?? '' }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="text" name="bill_to" class="form-control" value="{{ $billTo ?? '' }}"></div></div>
        </div>
        <div class="row g-2 align-items-end mt-2">
            <div class="col-md-3"><div class="input-group input-group-sm"><span class="input-group-text">Category</span><select name="category_id" class="form-select"><option value="">All</option>@foreach($categories ?? [] as $cat)<option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>@endforeach</select></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">Status</span><input type="text" name="status" class="form-control" value="{{ $status ?? '' }}"></div></div>
        </div>
        <div class="row g-2 align-items-end mt-1">
            <div class="col-md-3"><div class="input-group input-group-sm"><span class="input-group-text">Sales Man</span><select name="salesman_id" class="form-select"><option value="">All</option>@foreach($salesmen ?? [] as $sm)<option value="{{ $sm->id }}" {{ ($salesmanId ?? '') == $sm->id ? 'selected' : '' }}>{{ $sm->name }}</option>@endforeach</select></div></div>
        </div>
        <div class="row g-2 align-items-end mt-1">
            <div class="col-md-3"><div class="input-group input-group-sm"><span class="input-group-text">Area</span><select name="area_id" class="form-select"><option value="">All</option>@foreach($areas ?? [] as $area)<option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>@endforeach</select></div></div>
        </div>
        <div class="row g-2 align-items-end mt-1">
            <div class="col-md-3"><div class="input-group input-group-sm"><span class="input-group-text">Route</span><select name="route_id" class="form-select"><option value="">All</option>@foreach($routes ?? [] as $route)<option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>@endforeach</select></div></div>
        </div>
        </form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 50vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>#</th><th>Invoice</th><th>Date</th><th>Customer</th><th>Item</th><th class="text-end">Qty</th><th class="text-end">Amount</th></tr></thead>
            <tbody>
                @forelse($items ?? [] as $index => $item)
                <tr><td>{{ $index + 1 }}</td><td>{{ $item['invoice_no'] ?? '' }}</td><td>{{ $item['date'] ?? '' }}</td><td>{{ $item['customer'] ?? '' }}</td><td>{{ $item['item_name'] ?? '' }}</td><td class="text-end">{{ number_format($item['qty'] ?? 0) }}</td><td class="text-end">{{ number_format($item['amount'] ?? 0, 2) }}</td></tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No records found</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals) && count($items ?? []) > 0)<tfoot class="table-dark fw-bold"><tr><td colspan="5" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['qty'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td></tr></tfoot>@endif
        </table>
    </div></div></div>
    <div class="card mt-2"><div class="card-body py-2"><div class="d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportExcel()"><u>E</u>xcel</button>
        <div class="d-flex gap-2"><button type="submit" form="filterForm" class="btn btn-outline-primary btn-sm"><u>V</u>iew</button><a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary btn-sm">Close</a></div>
    </div></div></div>
</div>
@endsection
@push('scripts')<script>function exportExcel(){window.location.href='{{ route("admin.reports.sales.other.frige-item") }}?'+new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString()+'&export=excel';}</script>@endpush
@push('styles')<style>.input-group-text{font-size:.7rem}.form-control,.form-select{font-size:.75rem}.table th,.table td{padding:.3rem .4rem;font-size:.75rem}.btn-sm{font-size:.75rem}.sticky-top{position:sticky;top:0;z-index:10}</style>@endpush
