@extends('layouts.admin')

@section('title', "Supplier's Pending Orders")

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Supplier's Pending Orders</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.suppliers-pending-order') }}">
                <!-- From & To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 120px;">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>T</u>o :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>

                <!-- Selective -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 120px;">
                        <label class="fw-bold mb-0">Selective [ Y/N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="selective" class="form-control form-control-sm text-center text-uppercase" value="{{ request('selective', 'Y') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Supplier -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 120px;">
                        <label class="fw-bold mb-0">Supplier :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="supplier_code" class="form-control form-control-sm text-uppercase" value="{{ request('supplier_code') }}" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <select name="supplier_id" class="form-select form-select-sm" style="width: 300px;">
                            <option value="">-- All Suppliers --</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ request('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 120px;">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="company_code" class="form-control form-control-sm text-uppercase" value="{{ request('company_code', '00') }}" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <select name="company_id" class="form-select form-select-sm" style="width: 300px;">
                            <option value="">-- All Companies --</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Division -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 120px;">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="division" class="form-control form-control-sm text-uppercase" value="{{ request('division', '00') }}" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <input type="text" name="division_name" class="form-control form-control-sm" value="{{ request('division_name') }}" style="width: 300px;" placeholder="Division name">
                    </div>
                </div>

                <!-- Item -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 120px;">
                        <label class="fw-bold mb-0">Item :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="item_code" class="form-control form-control-sm text-uppercase" value="{{ request('item_code', '00') }}" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <select name="item_id" class="form-select form-select-sm" style="width: 300px;">
                            <option value="">-- All Items --</option>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row g-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px; margin-top: 150px;">
                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center">
            <span class="fw-bold">Pending Orders ({{ count($reportData) }} items)</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">S.No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Order No</th>
                            <th>Supplier</th>
                            <th>Item Name</th>
                            <th class="text-end">Order Qty</th>
                            <th class="text-end">Received Qty</th>
                            <th class="text-end">Pending Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalPending = 0; $totalAmount = 0; @endphp
                        @foreach($reportData as $index => $row)
                        @php 
                            $totalPending += $row['pending_qty']; 
                            $totalAmount += $row['amount'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $row['order_date'] }}</td>
                            <td class="text-center">{{ $row['order_no'] }}</td>
                            <td>{{ $row['supplier_name'] }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td class="text-end">{{ number_format($row['order_qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['received_qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['pending_qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totalPending, 2) }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No pending orders found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.suppliers-pending-order") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
</style>
@endpush
