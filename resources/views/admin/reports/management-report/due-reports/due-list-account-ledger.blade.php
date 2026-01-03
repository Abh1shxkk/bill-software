@extends('layouts.admin')

@section('title', 'Due List Account Ledger')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm" style="background-color: #d3d3d3;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.due-list-account-ledger') }}">
            <!-- Filter Row 1 -->
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <label class="fw-bold mb-0">Due List as on :</label>
                </div>
                <div class="col-auto">
                    <input type="date" name="as_on_date" class="form-control form-control-sm" value="{{ request('as_on_date', date('Y-m-d')) }}" style="width: 140px;">
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
                <div class="col-auto ms-auto">
                    <label class="fw-bold mb-0">Flag :</label>
                </div>
                <div class="col-auto">
                    <input type="text" name="flag" class="form-control form-control-sm" value="{{ request('flag') }}" style="width: 100px; background-color: #e9ecef;">
                </div>
            </div>

            <!-- Filter Row 2 -->
            <div class="row g-2 mb-2 align-items-center">
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
            </div>

            <!-- Filter Row 3 -->
            <div class="row g-2 mb-3 align-items-center">
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
                    <label class="fw-bold mb-0">D(etailed) / S(ummerised) :</label>
                </div>
                <div class="col-auto">
                    <input type="text" name="report_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('report_type', 'D') }}" maxlength="1" style="width: 40px;">
                </div>
                <div class="col-auto ms-auto">
                    <button type="submit" name="view" value="1" class="btn btn-light border px-3 fw-bold shadow-sm"><u>O</u>k</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm ms-1" onclick="printReport()">Print (F7)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm ms-1" onclick="closeWindow()">Close</button>
                </div>
            </div>
            </form>

            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" style="background-color: #fff;">
                    <thead style="background-color: #0000ff; color: #fff;">
                        <tr>
                            <th style="width: 80px;">CODE</th>
                            <th>PARTY NAME</th>
                            <th style="width: 100px;">INVOICE NO.</th>
                            <th style="width: 100px;">DATE</th>
                            <th class="text-end" style="width: 120px;">INVOICE AMT.</th>
                            <th class="text-end" style="width: 120px;">DUE AMOUNT</th>
                            <th class="text-end" style="width: 100px;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        @if(isset($reportData) && $reportData->count() > 0)
                            @foreach($reportData as $item)
                            <tr>
                                <td>{{ $item->code ?? $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td></td>
                                <td></td>
                                <td class="text-end"></td>
                                <td class="text-end"></td>
                                <td class="text-end">{{ number_format($item->balance ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        @else
                            @for($i = 1; $i <= 15; $i++)
                            <tr><td></td><td></td><td></td><td></td><td class="text-end"></td><td class="text-end"></td><td class="text-end"></td></tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="row mt-2">
                <div class="col-auto">
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="exportExcel()">E<u>x</u>cel</button>
                </div>
                <div class="col text-end">
                    <span class="fst-italic" style="color: #800080;">Total O/S :</span>
                    <span class="ms-3" style="color: #800080;">{{ isset($reportData) ? number_format($reportData->sum('balance'), 2) : '0.00' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printReport() { 
    window.open('{{ route("admin.reports.management.due-reports.due-list-account-ledger") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
function exportExcel() { 
    window.location.href = '{{ route("admin.reports.management.due-reports.due-list-account-ledger") }}?export=excel&' + $('#filterForm').serialize();
}
function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }

$(document).ready(function() {
    $(document).on('keydown', function(e) {
        if (e.key === 'F7') { e.preventDefault(); printReport(); }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.4rem; font-size: 0.8rem; vertical-align: middle; }
.table thead th { font-weight: bold; }
</style>
@endpush
