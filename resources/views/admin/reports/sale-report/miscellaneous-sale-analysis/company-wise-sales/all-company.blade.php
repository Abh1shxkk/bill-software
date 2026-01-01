@extends('layouts.admin')

@section('title', 'Company Wise Sales - All Company')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">Company Wise Sales</h4>
    </div>

    <div class="border p-3" style="background: #f0e8f0;">
        <form method="GET" id="filterForm">
            {{-- Transaction Type --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <span class="small">1. Sale / 2. Sale Return / 3. Consolidated Sale :</span>
                </div>
                <div class="col-auto">
                    <input type="text" name="transaction_type" class="form-control form-control-sm" value="3" style="width: 35px;" maxlength="1">
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

            {{-- Tagged Companies & Remove Tags --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Tagged Compnies [ Y / N ] :</label>
                    <input type="text" name="tagged_companies" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">Remove Tags [ Y / N ] :</label>
                    <input type="text" name="remove_tags" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- S(elective) / A(ll) & With Tax --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">S (elective) / A (ll) :</label>
                    <input type="text" name="selective_all" class="form-control form-control-sm d-inline-block" value="A" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">1. With Tax / 2. W/O Tax :</label>
                    <input type="text" name="with_tax" class="form-control form-control-sm d-inline-block" value="1" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Company Code --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small">Company Code :</label></div>
                <div class="col-auto"><input type="text" name="company_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="company_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            </div>

            {{-- Division --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small">Division :</label></div>
                <div class="col-auto"><input type="text" name="division_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="division_id" class="form-select form-select-sm"><option value="">-- All --</option></select></div>
            </div>

            {{-- Item Category --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small">Item Category :</label></div>
                <div class="col-auto"><input type="text" name="item_category_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="item_category_id" class="form-select form-select-sm"><option value="">-- All --</option></select></div>
            </div>

            {{-- Party Code --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small">Party Code :</label></div>
                <div class="col-auto"><input type="text" name="party_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="customer_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($customers ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            </div>

            {{-- Sales Man --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small">Sales Man :</label></div>
                <div class="col-auto"><input type="text" name="salesman_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="salesman_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($salesmen ?? [] as $sm)<option value="{{ $sm->id }}">{{ $sm->name }}</option>@endforeach</select></div>
            </div>

            {{-- Area --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small">Area :</label></div>
                <div class="col-auto"><input type="text" name="area_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="area_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($areas ?? [] as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
            </div>

            {{-- Route --}}
            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 120px;"><label class="small">Route :</label></div>
                <div class="col-auto"><input type="text" name="route_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="route_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($routes ?? [] as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
            </div>

            {{-- With Br./Expiry & Sort --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small">With Br. / Expiry [ Y / N ] :</label>
                    <input type="text" name="with_br_expiry" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-2">
                    <label class="small">Sort :</label>
                    <select name="sort_by" class="form-select form-select-sm d-inline-block" style="width: 100px;">
                        <option value="company">Company</option>
                        <option value="amount">Amount</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="small">By :</label>
                    <select name="sort_order" class="form-select form-select-sm d-inline-block" style="width: 100px;">
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col">
                    <button type="button" class="btn btn-secondary btn-sm border px-3">View in Excel</button>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-light btn-sm border px-4 me-2" id="btnView"><u>V</u>iew</button>
                    <button type="button" class="btn btn-light btn-sm border px-4" onclick="window.history.back()">Close</button>
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
