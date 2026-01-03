@extends('layouts.admin')

@section('title', 'Balance Confirmation Letter')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm" style="background-color: #ffe4c4;">
        <div class="card-body p-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.balance-confirmation-letter') }}">
                <!-- Row 1: As On, Customer, D(ebit)/C(redit)/A(ll), Flag -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">As On:</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="as_on_date" class="form-control form-control-sm" value="{{ request('as_on_date', date('Y-m-d')) }}" style="width: 130px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Customer :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="customer_code" id="customer_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_code') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">D(ebit / C(redit) / A (ll) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="balance_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('balance_type', 'A') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Row 2: Tagged Customer, Sales Man, Flag -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Tagged Customer :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tagged_customer" class="form-control form-control-sm text-center text-uppercase" value="{{ request('tagged_customer', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Sales Man :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="salesman_code" id="salesman_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ request('salesman_code') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Flag :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="flag" class="form-control form-control-sm" value="{{ request('flag') }}" style="width: 80px;">
                    </div>
                </div>

                <!-- Row 3: Area, Status -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="visibility: hidden;">
                        <label class="fw-bold mb-0">Tagged Customer :</label>
                    </div>
                    <div class="col-auto" style="visibility: hidden;">
                        <input type="text" class="form-control form-control-sm text-center" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Area :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="area_code" id="area_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ request('area_code') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Status :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="status" class="form-control form-control-sm" value="{{ request('status') }}" style="width: 80px;">
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="ok" value="1" class="btn btn-light border px-3 fw-bold shadow-sm">OK</button>
                        <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="printReport()">Print</button>
                        <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="closeWindow()">CANCEL</button>
                    </div>
                </div>

                <!-- Row 4: Remove Tag, Route, Letter Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Remove Tag :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="remove_tag" class="form-control form-control-sm text-center text-uppercase" value="{{ request('remove_tag', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Route :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="route_code" id="route_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ request('route_code') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Letter Date :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="letter_date" class="form-control form-control-sm" value="{{ request('letter_date', date('Y-m-d')) }}" style="width: 130px;">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Grid -->
    @if(request()->has('ok'))
    <div class="card mt-2" style="background-color: #ffe4c4;">
        <div class="card-body p-0">
            <div class="row g-0">
                <!-- Left Grid -->
                <div class="col-md-6">
                    <table class="table table-bordered table-sm mb-0" style="background-color: #ffe4c4;">
                        <thead style="background-color: #f4a460;">
                            <tr>
                                <th style="width: 60px;">CODE</th>
                                <th>NAME</th>
                                <th style="width: 120px;">LEDGER BALANCE</th>
                                <th style="width: 50px;">TAG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($reportData) && $reportData->count() > 0)
                                @foreach($reportData as $item)
                                <tr>
                                    <td>{{ $item->code ?? $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-end">{{ number_format($item->balance ?? 0, 2) }}</td>
                                    <td>{{ $item->tag ?? '' }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="4" class="text-center">No records found.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- Right Grid -->
                <div class="col-md-6" style="border-left: 2px solid #000;">
                    <table class="table table-bordered table-sm mb-0" style="background-color: #ffe4c4;">
                        <thead style="background-color: #f4a460;">
                            <tr>
                                <th style="width: 80px;">DATE</th>
                                <th style="width: 100px;">BILLNO.</th>
                                <th>AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($ledgerData) && $ledgerData->count() > 0)
                                @foreach($ledgerData as $ledger)
                                <tr>
                                    <td>{{ $ledger->date ? date('d-M-y', strtotime($ledger->date)) : '' }}</td>
                                    <td>{{ $ledger->bill_no ?? '' }}</td>
                                    <td class="text-end">{{ number_format($ledger->amount ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                @for($i = 0; $i < 5; $i++)
                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                @endfor
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if(isset($reportData) && $reportData->count() > 0)
    <div class="p-2 text-muted">Total Records: {{ $reportData->count() }}</div>
    @endif
    @else
    <div class="card mt-2" style="background-color: #ffe4c4;">
        <div class="card-body p-0">
            <div class="row g-0">
                <!-- Left Grid -->
                <div class="col-md-6">
                    <table class="table table-bordered table-sm mb-0" style="background-color: #ffe4c4;">
                        <thead style="background-color: #f4a460;">
                            <tr>
                                <th style="width: 60px;">CODE</th>
                                <th>NAME</th>
                                <th style="width: 120px;">LEDGER BALANCE</th>
                                <th style="width: 50px;">TAG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < 15; $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <!-- Right Grid -->
                <div class="col-md-6" style="border-left: 2px solid #000;">
                    <table class="table table-bordered table-sm mb-0" style="background-color: #ffe4c4;">
                        <thead style="background-color: #f4a460;">
                            <tr>
                                <th style="width: 80px;">DATE</th>
                                <th style="width: 100px;">BILLNO.</th>
                                <th>AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < 15; $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="card mt-2" style="background-color: #ffe4c4;">
        <div class="card-body p-2 d-flex justify-content-between align-items-center">
            <div>
                <a href="#" class="text-decoration-underline fw-bold">Switch Grid : [ENTER]</a>
            </div>
            <div>
                <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="exportExcel()">Excel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.due-reports.balance-confirmation-letter") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

function exportExcel() {
    window.location.href = '{{ route("admin.reports.management.due-reports.balance-confirmation-letter") }}?export=excel&' + $('#filterForm').serialize();
}
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { border: 1px solid #000; padding: 2px 5px; vertical-align: middle; }
.table th { text-align: center; font-weight: bold; }
</style>
@endpush
