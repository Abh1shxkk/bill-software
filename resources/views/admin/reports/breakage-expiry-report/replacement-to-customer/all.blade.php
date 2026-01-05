@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', Times, serif;">
                    <h5 class="mb-0">Replacement To Customer - All</h5>
                </div>
                <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
                    <form method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-auto">
                                <label class="form-label mb-0">Party:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="party_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('party_code', '00') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="party_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($customers ?? [] as $customer)
                                        <option value="{{ $customer->id }}" {{ request('party_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0">From:</label>
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
                        <div class="row g-2 align-items-end mt-1">
                            <div class="col-auto">
                                <label class="form-label mb-0">Company:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="company_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('company_code', '00') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="company_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 align-items-end mt-1">
                            <div class="col-auto">
                                <label class="form-label mb-0">Sales Man:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="salesman_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('salesman_code', '00') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="salesman_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($salesmen ?? [] as $salesman)
                                        <option value="{{ $salesman->id }}" {{ request('salesman_id') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0">Area:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="area_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('area_code', '00') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="area_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($areas ?? [] as $area)
                                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 align-items-end mt-1">
                            <div class="col-auto">
                                <label class="form-label mb-0">Route:</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="route_code" class="form-control form-control-sm" style="width: 50px;" value="{{ request('route_code', '00') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="route_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($routes ?? [] as $route)
                                        <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0">Sort By:</label>
                            </div>
                            <div class="col-auto">
                                <select name="sort_by" class="form-select form-select-sm" style="width: 100px;">
                                    <option value="date" {{ request('sort_by', 'date') == 'date' ? 'selected' : '' }}>Date</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="code" {{ request('sort_by') == 'code' ? 'selected' : '' }}>Code</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="ok" value="1" class="btn btn-secondary btn-sm">Ok</button>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="view" value="1" class="btn btn-secondary btn-sm">View</button>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.reports.breakage-expiry.replacement-to-customer.all') }}" class="btn btn-secondary btn-sm">Close</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0" style="font-size: 12px;">
                            <thead>
                                <tr style="background-color: #000080; color: white;">
                                    <th style="width: 40px;">SNo</th>
                                    <th>Date</th>
                                    <th>Trn No</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Inv No.</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData ?? [] as $index => $row)
                                <tr style="{{ $index == 0 ? 'background-color: #000080; color: white;' : '' }}">
                                    <td>{{ $index + 1 }}.</td>
                                    <td>{{ $row->transaction_date ? $row->transaction_date->format('d/m/Y') : '' }}</td>
                                    <td>{{ $row->sr_no ?? '' }}</td>
                                    <td>{{ $row->customer_id ?? '' }}</td>
                                    <td>{{ $row->customer_name ?? '' }}</td>
                                    <td>{{ number_format($row->net_amount ?? 0, 2) }}</td>
                                    <td>{{ $row->gst_vno ?? '' }}</td>
                                    <td>{{ $row->end_date ? $row->end_date->format('d/m/Y') : '' }}</td>
                                </tr>
                                @empty
                                @for($i = 1; $i <= 17; $i++)
                                <tr style="{{ $i == 2 ? 'background-color: #000080; color: white;' : '' }}">
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
                    <div class="d-flex justify-content-between p-2" style="font-family: 'Times New Roman', Times, serif; font-style: italic;">
                        <div><strong>Total Records :</strong> <span style="color: red;">{{ isset($reportData) ? count($reportData) : 0 }}</span></div>
                        <div><strong>Total :</strong> <span>{{ number_format($totalAmount ?? 0, 2) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printReport() {
    window.open('{{ route("admin.reports.breakage-expiry.replacement-to-customer.all") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endsection
