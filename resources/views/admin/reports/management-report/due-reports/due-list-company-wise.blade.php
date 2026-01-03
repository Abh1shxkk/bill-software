@extends('layouts.admin')

@section('title', 'Company wise Due List')

@section('content')
<div class="container-fluid">
    <div class="card mb-2">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-purple fst-italic fw-bold" style="font-family: 'Times New Roman', serif; color: #800080;">Company wise Due List</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #e8e8e8;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.due-list-company-wise') }}">
                <!-- From & To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', '2000-04-01') }}" style="width: 140px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>

                <!-- Tagged Company -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Tagged Cpmpany :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tagged_company" class="form-control form-control-sm text-center text-uppercase" value="{{ request('tagged_company', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0" style="color: #800080;">Company :</label>
                    </div>
                    <div class="col-md-6">
                        <select name="company_code" id="company_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_code') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- SalesMan -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">SalesMan:</label>
                    </div>
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
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Area :</label>
                    </div>
                    <div class="col-md-6">
                        <select name="area_code" id="area_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ request('area_code') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Party -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Party :</label>
                    </div>
                    <div class="col-md-6">
                        <select name="party_code" id="party_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('party_code') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Division -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="division_code" class="form-control form-control-sm" value="{{ request('division_code') }}" style="width: 60px;">
                    </div>
                </div>

                <!-- BillWise / Partywise & With Address -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">1. BillWise / 2. Partywise :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="report_type" class="form-control form-control-sm text-center" value="{{ request('report_type', '1') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-5">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="with_address" id="with_address" {{ request('with_address') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="with_address">With Address</label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportExcel()">E<u>x</u>cel</button>
                            <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="printReport()"><u>P</u>rint</button>
                        </div>
                        <div>
                            <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                            <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
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
                            <th>Company</th>
                            <th class="text-end">Bill Amt</th>
                            <th class="text-end">Due Amt</th>
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
                            <td>{{ $item->items->first()->item->company->name ?? '' }}</td>
                            <td class="text-end">{{ number_format($billAmt, 2) }}</td>
                            <td class="text-end">{{ number_format($dueAmt, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totalBill, 2) }}</td>
                            <td class="text-end">{{ number_format($totalDue, 2) }}</td>
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
    window.location.href = '{{ route("admin.reports.management.due-reports.due-list-company-wise") }}?export=excel&' + $('#filterForm').serialize();
}
function printReport() {
    window.open('{{ route("admin.reports.management.due-reports.due-list-company-wise") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') { e.preventDefault(); $('button[name="view"]').click(); }
    if (e.altKey && e.key.toLowerCase() === 'p') { e.preventDefault(); printReport(); }
    if (e.altKey && e.key.toLowerCase() === 'c') { e.preventDefault(); closeWindow(); }
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
