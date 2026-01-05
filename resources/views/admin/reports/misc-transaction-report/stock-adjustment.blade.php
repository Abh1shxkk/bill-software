@extends('layouts.admin')
@section('title', 'Stock Adjustment Report')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Stock Adjustment Report</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.misc-transaction.stock-adjustment') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0"><u>F</u>rom :</label></div>
                    <div class="col-auto"><input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;"></div>
                    <div class="col-auto"><label class="fw-bold mb-0">To :</label></div>
                    <div class="col-auto"><input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;"></div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0"><u>D</u>(etailed) / <u>S</u>(ummerised) :</label></div>
                    <div class="col-auto">
                        <input type="text" name="report_type" class="form-control form-control-sm text-uppercase" value="{{ request('report_type', 'D') }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0">Order By : <u>C</u>(ompany) / <u>I</u>(tem) :</label></div>
                    <div class="col-auto">
                        <input type="text" name="order_by" class="form-control form-control-sm text-uppercase" value="{{ request('order_by', 'C') }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 80px;"><label class="fw-bold mb-0">Company :</label></div>
                    <div class="col">
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">-- All Companies --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->code }} - {{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 80px;"><label class="fw-bold mb-0">Item :</label></div>
                    <div class="col">
                        <select name="item_id" id="item_id" class="form-select form-select-sm">
                            <option value="">-- All Items --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->code }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="exportExcel()">E<u>x</u>cel</button>
                    </div>
                    <div class="col-md-8 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && $reportData->count() > 0)
    <div class="card mt-2">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">S.No</th>
                            <th>Date</th>
                            <th>Voucher No</th>
                            <th>Item Name</th>
                            <th>Batch</th>
                            <th class="text-end">Adj. Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sno = 1; @endphp
                        @foreach($reportData as $adjustment)
                            @foreach($adjustment->items as $item)
                            <tr>
                                <td class="text-center">{{ $sno++ }}</td>
                                <td>{{ $adjustment->adjustment_date->format('d-M-y') }}</td>
                                <td>{{ $adjustment->trn_no }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->batch_no ?? '-' }}</td>
                                <td class="text-end">{{ $item->adjustment_type == 'S' ? '-' : '+' }}{{ number_format($item->qty, 2) }}</td>
                                <td class="text-end">{{ number_format($item->cost, 2) }}</td>
                                <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                                <td>{{ $adjustment->remarks ?? '-' }}</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }
function printReport() { window.open('{{ route("admin.reports.misc-transaction.stock-adjustment") }}?print=1&' + $('#filterForm').serialize(), '_blank'); }
function exportExcel() { window.location.href = '{{ route("admin.reports.misc-transaction.stock-adjustment") }}?excel=1&' + $('#filterForm').serialize(); }

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key === 'f') { e.preventDefault(); $('input[name="from_date"]').focus(); }
    if (e.altKey && e.key === 'd') { e.preventDefault(); $('input[name="report_type"]').focus(); }
    if (e.altKey && e.key === 's') { e.preventDefault(); $('input[name="report_type"]').val('S').focus(); }
    if (e.altKey && e.key === 'c') { e.preventDefault(); $('input[name="order_by"]').val('C').focus(); }
    if (e.altKey && e.key === 'i') { e.preventDefault(); $('input[name="order_by"]').val('I').focus(); }
    if (e.altKey && e.key === 'x') { e.preventDefault(); exportExcel(); }
    if (e.altKey && e.key === 'v') { e.preventDefault(); $('#filterForm').submit(); }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
</style>
@endpush
