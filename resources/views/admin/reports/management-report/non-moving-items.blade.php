@extends('layouts.admin')

@section('title', 'Non-Moving Items')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Non-Moving Items</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.non-moving-items') }}">
                <!-- From & To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 150px;">
                        <label class="fw-bold mb-0">Items not sold <u>F</u>rom :</label>
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

                <!-- Tag Company -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 150px;">
                        <label class="fw-bold mb-0">Tag Company [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tag_company" class="form-control form-control-sm text-center text-uppercase" value="{{ request('tag_company', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 150px;">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="company_code" class="form-control form-control-sm text-uppercase" value="{{ request('company_code', '00') }}" style="width: 80px;">
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

                <!-- Supplier -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 150px;">
                        <label class="fw-bold mb-0">Supplier :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="supplier_code" class="form-control form-control-sm text-uppercase" value="{{ request('supplier_code', '00') }}" style="width: 80px;">
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

                <!-- Checkboxes -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 150px;">
                        <label class="fw-bold mb-0">With Stock</label>
                    </div>
                    <div class="col-auto">
                        <input type="checkbox" name="with_stock" value="1" class="form-check-input" {{ request('with_stock') ? 'checked' : '' }}>
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">With Batch Detail</label>
                    </div>
                    <div class="col-auto">
                        <input type="checkbox" name="with_batch_detail" value="1" class="form-check-input" {{ request('with_batch_detail') ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row g-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px; margin-top: 50px;">
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
            <span class="fw-bold">Non-Moving Items ({{ count($reportData) }} items)</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">S.No</th>
                            <th>Item Name</th>
                            @if(request('tag_company') == 'Y')
                            <th>Company</th>
                            @endif
                            <th class="text-center">Last Sale Date</th>
                            <th class="text-end">Days Since Sale</th>
                            @if(request('with_stock'))
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Stock Value</th>
                            @endif
                            @if(request('with_batch_detail'))
                            <th>Batch No</th>
                            <th class="text-center">Expiry</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            @if(request('tag_company') == 'Y')
                            <td>{{ $row['company_name'] }}</td>
                            @endif
                            <td class="text-center">{{ $row['last_sale_date'] }}</td>
                            <td class="text-end">{{ $row['days_since_sale'] }}</td>
                            @if(request('with_stock'))
                            <td class="text-end">{{ number_format($row['current_stock'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['stock_value'], 2) }}</td>
                            @endif
                            @if(request('with_batch_detail'))
                            <td>{{ $row['batch_no'] ?? '' }}</td>
                            <td class="text-center">{{ $row['expiry_date'] ?? '' }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    @if(request('with_stock'))
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="{{ request('tag_company') == 'Y' ? 5 : 4 }}" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format(collect($reportData)->sum('current_stock'), 2) }}</td>
                            <td class="text-end">{{ number_format(collect($reportData)->sum('stock_value'), 2) }}</td>
                            @if(request('with_batch_detail'))
                            <td colspan="2"></td>
                            @endif
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No non-moving items found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.non-moving-items") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
