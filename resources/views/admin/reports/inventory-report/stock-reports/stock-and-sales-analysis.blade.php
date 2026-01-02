@extends('layouts.admin')

@section('title', 'Stock and Sales Analysis')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">STOCK AND SALES</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.stock.stock-and-sales-analysis') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from', date('Y-m-01')) }}">
                    </div>
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Tagged Companies [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="tagged_companies" class="form-control form-control-sm text-center" value="{{ request('tagged_companies', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Remove Tags [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="remove_company_tags" class="form-control form-control-sm text-center" value="{{ request('remove_company_tags', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Selective Comp. [Y / N] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="selective_company" class="form-control form-control-sm text-center" value="{{ request('selective_company', 'Y') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Company Status :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="company_status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="active" {{ request('company_status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('company_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-4 text-end pe-1">
                        <label class="fw-bold mb-0">Value on P(urchase) / C(ost) / M(rp) / S(rate) / (Cost) + G(ST) :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="value_on" class="form-control form-control-sm text-center" value="{{ request('value_on', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0">All Items :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="checkbox" name="all_items" class="form-check-input" {{ request('all_items') ? 'checked' : '' }}>
                    </div>
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="division" class="form-control form-control-sm text-center" value="{{ request('division', '00') }}" style="width: 50px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-4 text-end pe-1">
                        <label class="fw-bold mb-0">1. Detailed / 2. Summarised / 3. Re-order on Sale Basis :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="report_format" class="form-control form-control-sm text-center" value="{{ request('report_format', '3') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Show Free Qty :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="show_free_qty" class="form-control form-control-sm text-center" value="{{ request('show_free_qty', 'Y') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Re-Order Formula : Sale X</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="reorder_formula" class="form-control form-control-sm text-center" value="{{ request('reorder_formula', '1.00') }}" style="width: 60px;">
                    </div>
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Division Wise [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="division_wise" class="form-control form-control-sm text-center" value="{{ request('division_wise', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Add Challan Qty. in Sale</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="add_challan_qty" class="form-control form-control-sm text-center" value="{{ request('add_challan_qty', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Actual Sale / Pur. Value :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="actual_sale_pur" class="form-control form-control-sm text-center" value="{{ request('actual_sale_pur', 'Y') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Tagged Categories [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="tagged_categories" class="form-control form-control-sm text-center" value="{{ request('tagged_categories', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Remove Tags [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="remove_category_tags" class="form-control form-control-sm text-center" value="{{ request('remove_category_tags', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Item Category :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="item_category_code" class="form-control form-control-sm text-center" value="{{ request('item_category_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-md-3">
                        <select name="item_category" class="form-select form-select-sm">
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Item Location :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="item_location" class="form-select form-select-sm">
                            <option value="">All</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input type="checkbox" name="merge_trf" class="form-check-input" id="mergeTrf" {{ request('merge_trf') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="mergeTrf">Merge Trf. (PX-SC)</label>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Tagged Items [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="tagged_items" class="form-control form-control-sm text-center" value="{{ request('tagged_items', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Remove Tags [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="remove_item_tags" class="form-control form-control-sm text-center" value="{{ request('remove_item_tags', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input type="checkbox" name="show_barcode" class="form-check-input" id="showBarcode" {{ request('show_barcode') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="showBarcode">Show Barcode in Excel</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-6 offset-md-6 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="window.history.back()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
</style>
@endpush
