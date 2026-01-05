@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', Times, serif;">
                    <h5 class="mb-0">Customer Wise Expiry Return</h5>
                </div>
                <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
                    <form method="GET" id="filterForm">
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-auto">
                                <label class="form-label mb-0"><u>F</u>rom:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0">To:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-3">
                                <label class="form-label mb-0">Sales Man:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="salesman_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('salesman_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="salesman_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($salesmen ?? [] as $salesman)
                                        <option value="{{ $salesman->id }}" {{ request('salesman_id') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-3">
                                <label class="form-label mb-0">Area:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="area_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('area_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="area_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($areas ?? [] as $area)
                                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-3">
                                <label class="form-label mb-0">Route:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="route_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('route_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="route_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($routes ?? [] as $route)
                                        <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-3">
                                <label class="form-label mb-0">State:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="state_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('state_code', '00') }}">
                            </div>
                            <div class="col">
                                <select name="state_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($states ?? [] as $state)
                                        <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-3">
                            <div class="col-auto">
                                <label class="form-label mb-0">Order By <u>N</u>(ame) / <u>V</u>(alue):</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="order_by" class="form-control form-control-sm text-uppercase" style="width: 40px;" maxlength="1" value="{{ request('order_by', 'N') }}">
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0"><u>A</u>(sending) / <u>D</u>(esending):</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="order_dir" class="form-control form-control-sm text-uppercase" style="width: 40px;" maxlength="1" value="{{ request('order_dir', 'A') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col text-center">
                                <button type="submit" name="view" value="1" class="btn btn-secondary btn-sm">View</button>
                                <a href="{{ route('admin.reports.breakage-expiry.customer-wise-expiry-return') }}" class="btn btn-secondary btn-sm">Close</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
