@extends('layouts.admin')

@section('title', 'Due List With PDC')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Due List With PDC</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.due-list-with-pdc') }}">
                <!-- AsOn Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2"><label class="fw-bold mb-0">AsOn :</label></div>
                    <div class="col-auto">
                        <input type="date" name="as_on_date" class="form-control form-control-sm" value="{{ request('as_on_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>

                <!-- Customer -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2"><label class="fw-bold mb-0">Customer :</label></div>
                    <div class="col-md-6">
                        <select name="customer_code" id="customer_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_code') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Sales Man -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2"><label class="fw-bold mb-0">Sales Man :</label></div>
                    <div class="col-md-6">
                        <select name="salesman_code" id="salesman_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ request('salesman_code') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Area -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2"><label class="fw-bold mb-0">Area :</label></div>
                    <div class="col-md-6">
                        <select name="area_code" id="area_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ request('area_code') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Route -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2"><label class="fw-bold mb-0">Route :</label></div>
                    <div class="col-md-6">
                        <select name="route_code" id="route_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ request('route_code') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Day -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2"><label class="fw-bold mb-0">Day :</label></div>
                    <div class="col-auto">
                        <select name="day" class="form-select form-select-sm" style="width: 120px;">
                            <option value="">Select</option>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d)
                            <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Series -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2"><label class="fw-bold mb-0">Series :</label></div>
                    <div class="col-auto">
                        <input type="text" name="series" class="form-control form-control-sm" value="{{ request('series') }}" style="width: 60px;">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportExcel()">E<u>x</u>cel</button>
                            <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="printReport()"><u>P</u>rint</button>
                        </div>
                        <div>
                            <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                            <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">E<u>x</u>it</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view'))
    <div class="card mt-2">
        <div class="card-body p-2">
            @if(isset($reportData) && $reportData->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Bill No</th>
                            <th>Customer</th>
                            <th class="text-end">Bill Amt</th>
                            <th class="text-end">Due Amt</th>
                            <th>PDC Date</th>
                            <th class="text-end">PDC Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalBill = 0; $totalDue = 0; @endphp
                        @foreach($reportData as $index => $item)
                        @php 
                            $billAmt = $item->net_amount ?? 0;
                            $dueAmt = $billAmt - ($item->paid_amount ?? 0);
                            $totalBill += $billAmt;
                            $totalDue += $dueAmt;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->sale_date ? date('d-M-y', strtotime($item->sale_date)) : '' }}</td>
                            <td>{{ $item->invoice_no ?? '' }}</td>
                            <td>{{ $item->customer->name ?? '' }}</td>
                            <td class="text-end">{{ number_format($billAmt, 2) }}</td>
                            <td class="text-end">{{ number_format($dueAmt, 2) }}</td>
                            <td></td>
                            <td class="text-end"></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totalBill, 2) }}</td>
                            <td class="text-end">{{ number_format($totalDue, 2) }}</td>
                            <td></td>
                            <td class="text-end"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-2 text-muted">Total Records: {{ $reportData->count() }}</div>
            @else
            <div class="alert alert-info mb-0">No records found for the selected criteria.</div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportExcel() {
    window.location.href = '{{ route("admin.reports.management.due-reports.due-list-with-pdc") }}?export=excel&' + $('#filterForm').serialize();
}
function printReport() {
    window.open('{{ route("admin.reports.management.due-reports.due-list-with-pdc") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') { e.preventDefault(); $('button[name="view"]').click(); }
    if (e.altKey && e.key.toLowerCase() === 'p') { e.preventDefault(); printReport(); }
    if (e.altKey && e.key.toLowerCase() === 'x') { e.preventDefault(); closeWindow(); }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
</style>
@endpush
