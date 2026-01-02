@extends('layouts.admin')
@section('title', 'Customer / Supplier Pending Orders')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">CUSTOMER / SUPPLIER PENDING ORDERS</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" action="{{ route('admin.reports.sales.other.pending-orders') }}" id="filterForm"><div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">C/S</span><select name="order_type" class="form-select"><option value="C" {{ ($orderType ?? 'C') == 'C' ? 'selected' : '' }}>Customer</option><option value="S" {{ ($orderType ?? '') == 'S' ? 'selected' : '' }}>Supplier</option></select></div></div>
        </div>
        <fieldset class="border rounded p-2 mt-2"><legend class="float-none w-auto px-2 fs-6 fst-italic">Filters</legend>
        <div class="row g-2 align-items-end">
            <div class="col-md-1"><label class="form-label mb-0 small">Sales Man:</label></div>
            <div class="col-md-3"><select name="salesman_id" class="form-select form-select-sm"><option value="">All Salesmen</option>@foreach($salesmen ?? [] as $sm)<option value="{{ $sm->id }}" {{ ($salesmanId ?? '') == $sm->id ? 'selected' : '' }}>{{ $sm->name }}</option>@endforeach</select></div>
        </div>
        <div class="row g-2 align-items-end mt-1">
            <div class="col-md-1"><label class="form-label mb-0 small">Area:</label></div>
            <div class="col-md-3"><select name="area_id" class="form-select form-select-sm"><option value="">All Areas</option>@foreach($areas ?? [] as $area)<option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>@endforeach</select></div>
        </div>
        <div class="row g-2 align-items-end mt-1">
            <div class="col-md-1"><label class="form-label mb-0 small">Route:</label></div>
            <div class="col-md-3"><select name="route_id" class="form-select form-select-sm"><option value="">All Routes</option>@foreach($routes ?? [] as $route)<option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>@endforeach</select></div>
        </div>
        <div class="row g-2 align-items-end mt-1">
            <div class="col-md-1"><label class="form-label mb-0 small">State:</label></div>
            <div class="col-md-3"><select name="state_id" class="form-select form-select-sm"><option value="">All States</option>@foreach($states ?? [] as $state)<option value="{{ $state->id }}" {{ ($stateId ?? '') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>@endforeach</select></div>
        </div>
        </fieldset></form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 45vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>#</th><th>Order Date</th><th>Order No</th><th>Party</th><th>Item</th><th class="text-end">Ordered</th><th class="text-end">Delivered</th><th class="text-end">Pending</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($orders ?? [] as $index => $order)
                <tr><td>{{ $index + 1 }}</td><td>{{ $order['date'] ?? '' }}</td><td>{{ $order['order_no'] ?? '' }}</td><td>{{ $order['party_name'] ?? '' }}</td><td>{{ $order['item_name'] ?? '' }}</td><td class="text-end">{{ number_format($order['ordered'] ?? 0) }}</td><td class="text-end text-success">{{ number_format($order['delivered'] ?? 0) }}</td><td class="text-end text-danger">{{ number_format($order['pending'] ?? 0) }}</td><td><span class="badge {{ ($order['status'] ?? '') == 'Complete' ? 'bg-success' : 'bg-warning' }}">{{ $order['status'] ?? 'Pending' }}</span></td></tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No pending orders found</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals) && count($orders ?? []) > 0)<tfoot class="table-dark fw-bold"><tr><td colspan="5" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['ordered'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['delivered'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['pending'] ?? 0) }}</td><td></td></tr></tfoot>@endif
        </table>
    </div></div></div>
    <div class="card mt-2"><div class="card-body py-2"><div class="d-flex justify-content-end gap-2">
        <button type="submit" form="filterForm" class="btn btn-outline-primary btn-sm"><u>V</u>iew</button><a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary btn-sm">Close</a>
    </div></div></div>
</div>
@endsection
@push('styles')<style>.input-group-text{font-size:.7rem}.form-control,.form-select{font-size:.75rem}.table th,.table td{padding:.3rem .4rem;font-size:.75rem}.btn-sm{font-size:.75rem}.sticky-top{position:sticky;top:0;z-index:10}legend{font-size:.85rem}</style>@endpush
