@extends('layouts.admin')

@section('title', 'Bill Tagging')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm" style="background-color: #d3d3d3;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.bill-tagging') }}">
            <!-- Tag No & Order -->
            <div class="row g-2 mb-3 align-items-center">
                <div class="col-auto"><label class="fw-bold mb-0">Tag No :</label></div>
                <div class="col-auto">
                    <input type="text" name="tag_no" id="tag_no" class="form-control form-control-sm" value="{{ request('tag_no') }}" style="width: 100px;">
                </div>
                <div class="col-auto ms-auto"><label class="fw-bold mb-0">Order : - 1. As Entered / 2. Name Wise :</label></div>
                <div class="col-auto">
                    <input type="text" name="order_type" class="form-control form-control-sm text-center" value="{{ request('order_type', '2') }}" maxlength="1" style="width: 40px;">
                </div>
            </div>

            <!-- Bills Table -->
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm mb-0" style="background-color: #fff;" id="billsTable">
                    <thead style="background-color: #0000ff; color: #fff;">
                        <tr>
                            <th class="text-center" style="width: 50px;">S.No</th>
                            <th style="width: 100px;">Bill No</th>
                            <th style="width: 100px;">Date</th>
                            <th style="width: 80px;">Code</th>
                            <th>Party</th>
                            <th style="width: 80px;">Ref</th>
                            <th class="text-end" style="width: 100px;">O/S Amt</th>
                            <th style="width: 80px;">Tag No</th>
                        </tr>
                    </thead>
                    <tbody id="billsTableBody">
                        @if(isset($bills) && $bills->count() > 0)
                            @foreach($bills as $index => $bill)
                            <tr data-id="{{ $bill->id }}">
                                <td class="text-center">{{ $index + 1 }}.</td>
                                <td>{{ $bill->invoice_no }}</td>
                                <td>{{ $bill->sale_date ? date('d-M-y', strtotime($bill->sale_date)) : '' }}</td>
                                <td>{{ $bill->customer->code ?? $bill->customer_id }}</td>
                                <td>{{ $bill->customer->name ?? '' }}</td>
                                <td>{{ $bill->reference ?? '' }}</td>
                                <td class="text-end">{{ number_format(($bill->net_amount ?? 0) - ($bill->paid_amount ?? 0), 2) }}</td>
                                <td>{{ $bill->tag_no ?? '' }}</td>
                            </tr>
                            @endforeach
                        @else
                            @for($i = 1; $i <= 15; $i++)
                            <tr><td class="text-center">{{ $i }}.</td><td></td><td></td><td></td><td></td><td></td><td class="text-end"></td><td></td></tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Summary Section -->
            <div class="row g-2 mb-2 p-2" style="background-color: #c0c0c0; border: 1px solid #999;">
                <div class="col-auto"><label class="fw-bold mb-0">Total Invoices :</label><span class="ms-2" id="totalInvoices">{{ isset($bills) ? $bills->count() : 0 }}</span></div>
                <div class="col-auto ms-4"><label class="fw-bold mb-0">Tagged Invoices :</label><span class="ms-2" id="taggedInvoices">0</span></div>
                <div class="col-auto ms-4"><label class="fw-bold mb-0">Total :</label><span class="ms-2" id="totalAmount">{{ isset($bills) ? number_format($bills->sum(fn($b) => ($b->net_amount ?? 0) - ($b->paid_amount ?? 0)), 2) : '0.00' }}</span></div>
            </div>

            <!-- Filter Fields -->
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto"><label class="fw-bold mb-0">Sales Man :</label></div>
                <div class="col-md-3">
                    <select name="salesman_code" id="salesman_code" class="form-select form-select-sm">
                        <option value="">-- Select --</option>
                        @foreach($salesmen ?? [] as $salesman)
                            <option value="{{ $salesman->id }}" {{ request('salesman_code') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto"><label class="fw-bold mb-0">Area :</label></div>
                <div class="col-md-3">
                    <select name="area_code" id="area_code" class="form-select form-select-sm">
                        <option value="">-- Select --</option>
                        @foreach($areas ?? [] as $area)
                            <option value="{{ $area->id }}" {{ request('area_code') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto"><label class="fw-bold mb-0">Route :</label></div>
                <div class="col-md-3">
                    <select name="route_code" id="route_code" class="form-select form-select-sm">
                        <option value="">-- Select --</option>
                        @foreach($routes ?? [] as $route)
                            <option value="{{ $route->id }}" {{ request('route_code') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto"><label class="fw-bold mb-0">Day :</label></div>
                <div class="col-auto">
                    <input type="text" name="day" class="form-control form-control-sm" value="{{ request('day') }}" style="width: 80px;">
                </div>
                <div class="col-auto ms-3"><label class="fw-bold mb-0">Bill Amount :</label></div>
                <div class="col-auto">
                    <input type="text" name="bill_amount" class="form-control form-control-sm" value="{{ request('bill_amount') }}" style="width: 100px;">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                <div class="col-md-12">
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="tagBill()">Tag (+)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="tagAll()">Tag All (Ctrl +)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="untagBill()">Un Tag (-)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="untagAll()">Un Tag All (Ctrl -)</button>
                    <button type="submit" name="get_bills" value="1" class="btn btn-light border px-3 fw-bold shadow-sm me-1">Get Bills (F2)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="printBills()">Print (F7)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="dueList()">Due List ( F5)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="pdcList()">PDC (F6)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="ledger()">Ledger (F10)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="editBill()">Edit (F3)</button>
                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="closeWindow()">Close</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var selectedRow = null;

function tagBill() {
    if (!selectedRow) { alert('Please select a bill first'); return; }
    var tagNo = $('#tag_no').val();
    if (!tagNo) { alert('Please enter Tag No'); $('#tag_no').focus(); return; }
    $(selectedRow).find('td:last').text(tagNo);
    updateTaggedCount();
}

function tagAll() {
    var tagNo = $('#tag_no').val();
    if (!tagNo) { alert('Please enter Tag No'); $('#tag_no').focus(); return; }
    $('#billsTableBody tr').each(function() {
        if ($(this).find('td:eq(1)').text()) {
            $(this).find('td:last').text(tagNo);
        }
    });
    updateTaggedCount();
}

function untagBill() {
    if (!selectedRow) { alert('Please select a bill first'); return; }
    $(selectedRow).find('td:last').text('');
    updateTaggedCount();
}

function untagAll() {
    $('#billsTableBody tr').each(function() {
        $(this).find('td:last').text('');
    });
    updateTaggedCount();
}

function updateTaggedCount() {
    var count = 0;
    $('#billsTableBody tr').each(function() {
        if ($(this).find('td:last').text().trim()) count++;
    });
    $('#taggedInvoices').text(count);
}

function printBills() { 
    window.open('{{ route("admin.reports.management.due-reports.bill-tagging") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
function dueList() { window.location.href = '{{ route("admin.reports.management.due-reports.due-list") }}'; }
function pdcList() { window.location.href = '{{ route("admin.reports.management.due-reports.due-list-with-pdc") }}'; }
function ledger() { alert('Ledger functionality'); }
function editBill() { alert('Edit functionality'); }
function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }

$(document).ready(function() {
    // Row selection
    $('#billsTableBody tr').on('click', function() {
        $('#billsTableBody tr').removeClass('table-primary');
        $(this).addClass('table-primary');
        selectedRow = this;
    });

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.key === 'F2') { e.preventDefault(); $('button[name="get_bills"]').click(); }
        if (e.key === 'F5') { e.preventDefault(); dueList(); }
        if (e.key === 'F6') { e.preventDefault(); pdcList(); }
        if (e.key === 'F7') { e.preventDefault(); printBills(); }
        if (e.ctrlKey && e.key === '+') { e.preventDefault(); tagAll(); }
        if (e.ctrlKey && e.key === '-') { e.preventDefault(); untagAll(); }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; font-size: 0.75rem; }
.table th, .table td { padding: 0.2rem 0.3rem; font-size: 0.75rem; vertical-align: middle; }
.table thead th { font-weight: bold; }
#billsTableBody tr { cursor: pointer; }
#billsTableBody tr:hover { background-color: #e0e0e0; }
</style>
@endpush
