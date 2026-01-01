@extends('layouts.admin')

@section('title', 'All Item Sale')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #4169E1, #1E90FF); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold text-white">ALL ITEM SALE</h4>
    </div>

    <div class="border p-3" style="background: #d8e8f0;">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.item-wise-sales.all-item-sale') }}">
            {{-- Transaction Type --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <span class="small">1. Sale / 2. Sale Return / 3. Both / 4.Only Challan :</span>
                </div>
                <div class="col-auto">
                    <input type="text" name="transaction_type" class="form-control form-control-sm" value="3" style="width: 35px; background: #4169E1; color: white;" maxlength="1">
                </div>
            </div>

            {{-- Date Range --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small fw-bold"><u>F</u>rom :</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
                <div class="col-auto ms-5">
                    <label class="small fw-bold">To :</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
            </div>

            {{-- Series & Bill No --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Series :</label>
                    <input type="text" name="series_from" class="form-control form-control-sm d-inline-block" value="00" style="width: 50px;">
                </div>
                <div class="col-auto">
                    <label class="small">Bill No. :</label>
                    <input type="text" name="bill_no_from" class="form-control form-control-sm d-inline-block" value="0" style="width: 80px;">
                </div>
                <div class="col-auto">
                    <label class="small">To :</label>
                    <input type="text" name="bill_no_to" class="form-control form-control-sm d-inline-block" value="0" style="width: 80px;">
                </div>
            </div>

            {{-- Tagged Companies & Remove Tags --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Tagged Companies [ Y / N ] :</label>
                    <input type="text" name="tagged_companies" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-5">
                    <label class="small">Remove Tags [ Y / N ] :</label>
                    <input type="text" name="remove_tags" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Company --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Company :</label></div>
                <div class="col-auto"><input type="text" name="company_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="company_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            </div>

            {{-- Division --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Division :</label></div>
                <div class="col-auto"><input type="text" name="division_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
            </div>

            {{-- Item --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Item :</label></div>
                <div class="col-auto"><input type="text" name="item_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="item_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($items ?? [] as $i)<option value="{{ $i->id }}">{{ $i->name }}</option>@endforeach</select></div>
            </div>

            {{-- Tagged Categories & Remove Tags --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Tagged Categories [ Y / N ] :</label>
                    <input type="text" name="tagged_categories" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-5">
                    <label class="small">Remove Tags [ Y / N ] :</label>
                    <input type="text" name="remove_category_tags" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Category --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Category :</label></div>
                <div class="col-auto"><input type="text" name="category_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="category_id" class="form-select form-select-sm"><option value="">-- All --</option></select></div>
            </div>

            {{-- Commodity --}}
            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 100px;"><label class="small">Commodity :</label></div>
                <div class="col-auto"><input type="text" name="commodity_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
            </div>

            {{-- Range, Add Gst, Value --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Range :</label>
                    <input type="text" name="range" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-2">
                    <input type="checkbox" name="add_gst" class="form-check-input" id="addGst">
                    <label class="form-check-label small" for="addGst">Add Gst</label>
                </div>
                <div class="col-auto ms-3">
                    <label class="small">Value :</label>
                    <input type="text" name="value_from" class="form-control form-control-sm d-inline-block" value="-999999999" style="width: 100px;">
                    <label class="small">To :</label>
                    <input type="text" name="value_to" class="form-control form-control-sm d-inline-block" value="999999999" style="width: 100px;">
                </div>
            </div>

            {{-- Order By & Asc/Desc --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Order By Q(ty) / V(alue) / N(ame) :</label>
                    <input type="text" name="order_by" class="form-control form-control-sm d-inline-block" value="V" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">A(sc) / D(esc) :</label>
                    <input type="text" name="order_direction" class="form-control form-control-sm d-inline-block" value="D" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- No. of Top Items & Batch Wise --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">No. of Top Items :</label>
                    <input type="text" name="top_items" class="form-control form-control-sm d-inline-block" value="0" style="width: 50px;">
                </div>
                <div class="col-auto ms-5">
                    <label class="small">Batch Wise</label>
                    <input type="checkbox" name="batch_wise" class="form-check-input ms-2">
                </div>
            </div>

            {{-- With Br./Expiry & With Return Det. --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">With Br. / Expiry [ Y / N ] :</label>
                    <input type="text" name="with_br_expiry" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">With Return Det. [ Y / N ] :</label>
                    <input type="text" name="with_return_det" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- DPC Item & Item Type --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">DPC Item [ Y / N ] :</label>
                    <input type="text" name="dpc_item" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">Item Type :</label>
                    <input type="text" name="item_type" class="form-control form-control-sm d-inline-block" value="" style="width: 80px;">
                </div>
            </div>

            {{-- Item Status --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small">Item Status :</label>
                    <input type="text" name="item_status" class="form-control form-control-sm d-inline-block" value="" style="width: 150px;">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col">
                    <button type="button" class="btn btn-secondary btn-sm border px-3">Tax Wise</button>
                    <button type="button" class="btn btn-secondary btn-sm border px-3 ms-2">Excel</button>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-secondary btn-sm border px-4 me-2" id="btnView"><u>V</u>iew</button>
                    <button type="button" class="btn btn-secondary btn-sm border px-4" onclick="window.history.back()">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    document.getElementById('btnView').addEventListener('click', function() {
        let vi = form.querySelector('input[name="view_type"]');
        if (!vi) { vi = document.createElement('input'); vi.type = 'hidden'; vi.name = 'view_type'; form.appendChild(vi); }
        vi.value = 'print'; form.target = '_blank'; form.submit(); form.target = '_self'; vi.value = '';
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') window.history.back(); });
});
</script>
@endsection
