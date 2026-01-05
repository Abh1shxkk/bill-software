@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', Times, serif;">
                    <h5 class="mb-0">Breakage/Expiry From Customer - Pending</h5>
                </div>
                <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
                    <form method="GET" id="filterForm">
                        <div class="row g-2 mb-2">
                            <div class="col-md-1">
                                <label class="form-label">Party</label>
                                <input type="text" name="party_code" class="form-control form-control-sm text-uppercase" value="{{ request('party_code', '00') }}" maxlength="2">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <select name="customer_id" class="form-select form-select-sm">
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers ?? [] as $customer)
                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From</label>
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To</label>
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <label class="form-label">View</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="view_type" id="viewAll" value="all" {{ request('view_type', 'all') == 'all' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="viewAll">All</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="view_type" id="viewCredit" value="credit" {{ request('view_type') == 'credit' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="viewCredit">Credit</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="view_type" id="viewReplacement" value="replacement" {{ request('view_type') == 'replacement' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="viewReplacement">Replacement</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sort By</label>
                                <select name="sort_by" class="form-select form-select-sm">
                                    <option value="date" {{ request('sort_by', 'date') == 'date' ? 'selected' : '' }}>Date</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="code" {{ request('sort_by') == 'code' ? 'selected' : '' }}>Code</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">With Adjustment [Y/N]</label>
                                <input type="text" name="with_adjustment" class="form-control form-control-sm text-uppercase" value="{{ request('with_adjustment', 'N') }}" maxlength="1">
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-1">
                                <label class="form-label">Sales Man</label>
                                <input type="text" name="salesman_code" class="form-control form-control-sm text-uppercase" value="{{ request('salesman_code', '00') }}" maxlength="2">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <select name="salesman_id" class="form-select form-select-sm">
                                    <option value="">-- Select --</option>
                                    @foreach($salesmen ?? [] as $salesman)
                                        <option value="{{ $salesman->id }}" {{ request('salesman_id') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Company</label>
                                <input type="text" name="company_code" class="form-control form-control-sm text-uppercase" value="{{ request('company_code', '00') }}" maxlength="2">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <select name="company_id" class="form-select form-select-sm">
                                    <option value="">-- Select --</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-1">
                                <label class="form-label">Area</label>
                                <input type="text" name="area_code" class="form-control form-control-sm text-uppercase" value="{{ request('area_code', '00') }}" maxlength="2">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <select name="area_id" class="form-select form-select-sm">
                                    <option value="">-- Select --</option>
                                    @foreach($areas ?? [] as $area)
                                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Division</label>
                                <input type="text" name="division_code" class="form-control form-control-sm text-uppercase" value="{{ request('division_code', '00') }}" maxlength="2">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <select name="division_id" class="form-select form-select-sm">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Adjustment Amount</label>
                                <input type="text" name="adjustment_amount" class="form-control form-control-sm text-end" value="{{ request('adjustment_amount', '0.00') }}" readonly>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-1">
                                <label class="form-label">Route</label>
                                <input type="text" name="route_code" class="form-control form-control-sm text-uppercase" value="{{ request('route_code', '00') }}" maxlength="2">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <select name="route_id" class="form-select form-select-sm">
                                    <option value="">-- Select --</option>
                                    @foreach($routes ?? [] as $route)
                                        <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end gap-1">
                                <button type="submit" name="ok" value="1" class="btn btn-outline-secondary btn-sm">Ok</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm">History (F5)</button>
                                <button type="submit" name="view" value="1" class="btn btn-outline-secondary btn-sm">View</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportToExcel()">Grid To Excel</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.close()">Close</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm">Adjust</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0" style="font-size: 12px;">
                            <thead style="background-color: #0000ff; color: white;">
                                <tr>
                                    <th style="width: 40px;">SNo</th>
                                    <th>Date</th>
                                    <th>Trn No</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th class="text-end">Amt</th>
                                    <th class="text-end">O/S Amt</th>
                                    <th>Desc</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData ?? [] as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->transaction_date ? $row->transaction_date->format('d/m/Y') : '' }}</td>
                                    <td>{{ $row->sr_no ?? '' }}</td>
                                    <td>{{ $row->customer_id ?? '' }}</td>
                                    <td>{{ $row->customer_name ?? '' }}</td>
                                    <td class="text-end">{{ number_format($row->net_amount ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($row->net_amount ?? 0, 2) }}</td>
                                    <td>{{ $row->remarks ?? '' }}</td>
                                </tr>
                                @empty
                                @for($i = 1; $i <= 16; $i++)
                                <tr>
                                    <td>{{ $i }}.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endfor
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between p-2" style="background-color: #90EE90; font-size: 12px;">
                        <span>No Of Records : <span class="text-danger">{{ count($reportData ?? []) }}</span></span>
                        <span>Total : <span class="text-danger">{{ number_format($reportData->sum('net_amount') ?? 0, 2) }}</span> <span class="text-danger ms-4">{{ number_format($totalAmount ?? 0, 2) }}</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printReport() {
    window.open('{{ route("admin.reports.breakage-expiry.from-customer.pending") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
function exportToExcel() {
    // Excel export logic
}
</script>
@endsection
