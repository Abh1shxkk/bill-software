@extends('layouts.admin')

@section('title', 'List of Modifications')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">List of Modifications</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.others.list-of-modifications') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0"><u>T</u>o :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">B(ack Date) / S(ame Date) / A(LL) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="date_filter" class="form-control form-control-sm text-center text-uppercase" value="{{ request('date_filter', 'A') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>T</u>ype :</label>
                    </div>
                    <div class="col-auto">
                        <select name="type" class="form-select form-select-sm" style="width: 140px;">
                            <option value="ALL" {{ request('type', 'ALL') == 'ALL' ? 'selected' : '' }}>ALL</option>
                            <option value="SALE" {{ request('type') == 'SALE' ? 'selected' : '' }}>SALE</option>
                            <option value="PURCHASE" {{ request('type') == 'PURCHASE' ? 'selected' : '' }}>PURCHASE</option>
                            <option value="SALE_RETURN" {{ request('type') == 'SALE_RETURN' ? 'selected' : '' }}>SALE RETURN</option>
                            <option value="PURCHASE_RETURN" {{ request('type') == 'PURCHASE_RETURN' ? 'selected' : '' }}>PURCHASE RETURN</option>
                        </select>
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0"><u>S</u>tatus :</label>
                    </div>
                    <div class="col-auto">
                        <select name="status" class="form-select form-select-sm" style="width: 140px;">
                            <option value="ALL" {{ request('status', 'ALL') == 'ALL' ? 'selected' : '' }}>ALL</option>
                            <option value="Modified" {{ request('status') == 'Modified' ? 'selected' : '' }}>Modified</option>
                            <option value="Deleted" {{ request('status') == 'Deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Series :</label>
                    </div>
                    <div class="col-auto">
                        <select name="series" class="form-select form-select-sm" style="width: 80px;">
                            <option value="ALL">ALL</option>
                            @foreach($seriesList ?? [] as $s)
                            <option value="{{ $s }}" {{ request('series') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Invoice No. :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="invoice_no" class="form-control form-control-sm" value="{{ request('invoice_no') }}" style="width: 100px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0"><u>U</u>ser :</label>
                    </div>
                    <div class="col-auto">
                        <select name="user_id" class="form-select form-select-sm" style="width: 140px;">
                            <option value="ALL">ALL</option>
                            @foreach($users ?? [] as $user)
                            <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Item Name :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="item_name" class="form-control form-control-sm" value="{{ request('item_name') }}" style="width: 200px;" placeholder="Search item...">
                    </div>
                </div>
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Customer :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="customer_name" class="form-control form-control-sm" value="{{ request('customer_name') }}" style="width: 200px;" placeholder="Search customer...">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center" style="background-color: #ffc4d0;">
            <span class="fw-bold">List of Modifications - {{ count($reportData) }} records</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background-color: #e0e0e0;">
                        <tr>
                            <th style="width: 40px;">S.No</th>
                            <th style="width: 80px;">Type</th>
                            <th style="width: 100px;">Invoice No</th>
                            <th style="width: 90px;">Date</th>
                            <th>Party Name</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                            <th style="width: 80px;">Status</th>
                            <th style="width: 100px;">Modified By</th>
                            <th style="width: 130px;">Modified At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['type'] }}</td>
                            <td>{{ $row['invoice_no'] }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['party_name'] }}</td>
                            <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            <td class="{{ $row['status'] == 'Deleted' ? 'text-danger' : 'text-warning' }} fw-bold">{{ $row['status'] }}</td>
                            <td>{{ $row['modified_by'] }}</td>
                            <td>{{ $row['modified_at'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No modifications found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.others.list-of-modifications") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'f') {
        e.preventDefault();
        $('input[name="from_date"]').focus();
    }
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        closeWindow();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.3rem 0.5rem; font-size: 0.85rem; border: 1px solid #999; }
</style>
@endpush
