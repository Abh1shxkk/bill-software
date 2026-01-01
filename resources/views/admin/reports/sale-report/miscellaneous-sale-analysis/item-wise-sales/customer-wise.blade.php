@extends('layouts.admin')

@section('title', 'Item Wise - Customer Wise Sale')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #800080;">ITEM WISE - CUSTOMER WISE SALE</h4>
    </div>

    <div class="border p-3" style="background: #f0e8f0;">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.item-wise-sales.customer-wise') }}">
            {{-- Transaction Type --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <span class="small">1. Sale / 2. Sale Return / 3. Both :</span>
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
                <div class="col-auto ms-4">
                    <label class="small fw-bold">To :</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
            </div>

            {{-- Tagged Companies & Remove Tags --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Tagged Companies [ Y / N ] :</label>
                    <input type="text" name="tagged_companies" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">Remove Tags [ Y / N ] :</label>
                    <input type="text" name="remove_tags" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- C(ompany) / I(tem) --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">C (ompany) / I (tem) :</label>
                    <input type="text" name="company_item" class="form-control form-control-sm d-inline-block" value="I" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Item --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Item :</label></div>
                <div class="col-md-5"><select name="item_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($items ?? [] as $i)<option value="{{ $i->id }}">{{ $i->name }}</option>@endforeach</select></div>
            </div>

            {{-- Division & Series --}}
            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 100px;"><label class="small">Division :</label></div>
                <div class="col-auto"><input type="text" name="division_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-auto ms-4">
                    <label class="small">Series :</label>
                    <input type="text" name="series" class="form-control form-control-sm d-inline-block" value="00" style="width: 50px;">
                </div>
            </div>

            {{-- Filters Section --}}
            <fieldset class="border p-2 mb-3" style="background: #e0d0e0;">
                <legend class="small text-danger fw-bold w-auto px-2" style="font-size: 11px;">Filters</legend>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Sales Man :</label></div>
                    <div class="col-auto"><input type="text" name="salesman_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="salesman_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($salesmen ?? [] as $sm)<option value="{{ $sm->id }}">{{ $sm->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Area :</label></div>
                    <div class="col-auto"><input type="text" name="area_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="area_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($areas ?? [] as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Route :</label></div>
                    <div class="col-auto"><input type="text" name="route_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="route_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($routes ?? [] as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2">
                    <div class="col-auto" style="width: 100px;"><label class="small">State :</label></div>
                    <div class="col-auto"><input type="text" name="state_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="state_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($states ?? [] as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
                </div>
            </fieldset>

            {{-- Item Type --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small">Item Type :</label>
                    <input type="text" name="item_type" class="form-control form-control-sm d-inline-block" value="" style="width: 100px;">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col text-end">
                    <button type="button" class="btn btn-secondary btn-sm border px-4 me-2" id="btnExcel">Excel</button>
                    <button type="button" class="btn btn-secondary btn-sm border px-4" onclick="window.history.back()">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    document.getElementById('btnExcel').addEventListener('click', function() {
        let vi = form.querySelector('input[name="export"]');
        if (!vi) { vi = document.createElement('input'); vi.type = 'hidden'; vi.name = 'export'; form.appendChild(vi); }
        vi.value = 'excel'; form.submit(); vi.value = '';
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') window.history.back(); });
});
</script>
@endsection
