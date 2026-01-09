@extends('layouts.admin')

@section('title', 'HSN Wise Sale Purchase Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #d4edda;">
        <div class="card-body py-2">
            <h4 class="mb-0 text-dark fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">HSN Wise Sale Purchase Report</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f8f9fa;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.others.hsn-wise-sale-purchase-report') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Date</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 130px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">To</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 130px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">1.Sale / 2. Purchase / 3.Both</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="report_type" class="form-control form-control-sm text-center" value="{{ request('report_type', '3') }}" maxlength="1" style="width: 35px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">C(ustomer)/S(upplier)</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="party_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('party_type', 'C') }}" maxlength="1" style="width: 35px;">
                    </div>
                </div>
                <div class="row g-2 align-items-center mt-1">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Customer</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="customer" class="form-control form-control-sm" value="{{ request('customer') }}" style="width: 60px;" placeholder="Code">
                    </div>
                    <div class="col-auto">
                        <select name="customer_id" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">-- Select --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Hsn</label>
                    </div>
                    <div class="col-auto">
                        <select name="hsn" class="form-select form-select-sm" style="width: 150px;">
                            <option value="">-- All HSN --</option>
                            @foreach($hsnCodes ?? [] as $hsnCode)
                                <option value="{{ $hsnCode->hsn_code }}" {{ request('hsn') == $hsnCode->hsn_code ? 'selected' : '' }}>{{ $hsnCode->hsn_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Tax</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tax" class="form-control form-control-sm text-center" value="{{ request('tax', '0') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Len.</label>
                    </div>
                    <div class="col-auto">
                        <select name="len" class="form-select form-select-sm" style="width: 70px;">
                            <option value="Full" {{ request('len', 'Full') == 'Full' ? 'selected' : '' }}>Full</option>
                            <option value="4" {{ request('len') == '4' ? 'selected' : '' }}>4</option>
                            <option value="6" {{ request('len') == '6' ? 'selected' : '' }}>6</option>
                            <option value="8" {{ request('len') == '8' ? 'selected' : '' }}>8</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 align-items-center mt-1">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Item</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="item" class="form-control form-control-sm" value="{{ request('item') }}" style="width: 100px;" placeholder="Code">
                    </div>
                    <div class="col-auto">
                        <input type="text" name="item_name" class="form-control form-control-sm" value="{{ request('item_name') }}" style="width: 200px;" placeholder="Name" readonly>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Order By</label>
                    </div>
                    <div class="col-auto">
                        <select name="order_by" class="form-select form-select-sm" style="width: 180px;">
                            <option value="Item,Inv.Date,Inv.No" {{ request('order_by', 'Item,Inv.Date,Inv.No') == 'Item,Inv.Date,Inv.No' ? 'selected' : '' }}>Item,Inv.Date,Inv.No</option>
                            <option value="HSN,Item" {{ request('order_by') == 'HSN,Item' ? 'selected' : '' }}>HSN,Item</option>
                            <option value="Inv.Date,Inv.No" {{ request('order_by') == 'Inv.Date,Inv.No' ? 'selected' : '' }}>Inv.Date,Inv.No</option>
                        </select>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-secondary px-4 fw-bold shadow-sm me-2">OK</button>
                        <button type="button" class="btn btn-secondary px-4 fw-bold shadow-sm" onclick="closeWindow()">Exit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-2" style="background-color: #d4edda;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background-color: #c3e6cb; position: sticky; top: 0;">
                        <tr>
                            <th style="width: 150px;">GSTIN of the TaxPayer Submitting Data</th>
                            <th style="width: 90px;">Product HSN</th>
                            <th>Product Name</th>
                            <th style="width: 150px;">Whether Product in column 4 is hand Sanitizer(Alcohol Based)-Yes/No</th>
                            <th style="width: 100px;">Nature of Transaction (Sale/purchase)</th>
                            <th style="width: 90px;">Inv. Date</th>
                            <th style="width: 90px;">Invoice No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
                            @foreach($reportData as $row)
                            <tr>
                                <td>{{ $row['gstin'] }}</td>
                                <td>{{ $row['hsn'] }}</td>
                                <td>{{ $row['product_name'] }}</td>
                                <td class="text-center">{{ $row['is_sanitizer'] }}</td>
                                <td class="text-center">{{ $row['nature'] }}</td>
                                <td>{{ $row['inv_date'] }}</td>
                                <td>{{ $row['inv_no'] }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr><td colspan="7" class="text-center text-muted">No data</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-1" style="background-color: #c3e6cb;">
            <div class="row">
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="printReport()">Find [F1]</button>
                    <button type="button" class="btn btn-sm btn-outline-dark">Find Next</button>
                </div>
            </div>
            <div class="mt-1">
                <span class="text-danger fw-bold">No. Of Records : {{ isset($reportData) ? count($reportData) : 0 }}</span>
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
    window.open('{{ route("admin.reports.management.others.hsn-wise-sale-purchase-report") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).on('keydown', function(e) {
    if (e.key === 'F1') {
        e.preventDefault();
        printReport();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #999; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.3rem 0.5rem; font-size: 0.8rem; border: 1px solid #999; vertical-align: middle; }
.table thead th { font-size: 0.75rem; }
</style>
@endpush
