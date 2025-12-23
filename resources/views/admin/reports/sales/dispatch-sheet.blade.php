@extends('layouts.admin')

@section('title', 'Dispatch Sheet')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Dispatch Sheet</h5>
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
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
                        <label class="form-label small mb-1">Area</label>
                        <select name="area_id" class="form-select form-select-sm">
                            <option value="">All Areas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ $areaId == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Route</label>
                        <select name="route_id" class="form-select form-select-sm">
                            <option value="">All Routes</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ $routeId == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
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
        <div class="col-md-4"><div class="card bg-primary text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Invoices</small><h5 class="mb-0">{{ number_format($totals['invoices']) }}</h5>
        </div></div></div>
        <div class="col-md-4"><div class="card bg-success text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Amount</small><h5 class="mb-0">₹{{ number_format($totals['net_amount'], 2) }}</h5>
        </div></div></div>
        <div class="col-md-4"><div class="card bg-info text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Total Items</small><h5 class="mb-0">{{ number_format($totals['items_count']) }}</h5>
        </div></div></div>
    </div>

    <!-- Area Wise Dispatch -->
    @foreach($groupedSales as $areaName => $areaSales)
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-dark text-white py-2">
            <div class="d-flex justify-content-between">
                <strong><i class="bi bi-geo-alt me-2"></i>{{ $areaName }}</strong>
                <span>{{ $areaSales->count() }} Bills | ₹{{ number_format($areaSales->sum('net_amount'), 2) }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th>Mobile</th>
                        <th>Route</th>
                        <th class="text-end">Items</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">Delivered</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($areaSales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                        <td class="small">{{ $sale->customer->address ?? '-' }}</td>
                        <td>{{ $sale->customer->mobile ?? '-' }}</td>
                        <td>{{ $sale->customer->route_name ?? '-' }}</td>
                        <td class="text-end">{{ $sale->items->count() }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($sale->net_amount, 2) }}</td>
                        <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    @if($groupedSales->isEmpty())
    <div class="alert alert-info text-center">No dispatch data found for selected period</div>
    @endif
</div>
@endsection
