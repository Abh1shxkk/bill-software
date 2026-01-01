@extends('layouts.admin')

@section('title', 'Company - Customer Wise Sales')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">COMPANY - CUSTOMER WISE SALES</h4>
    </div>

    <div class="border p-3" style="background: #f0e8f0;">
        <form method="GET" id="filterForm">
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

            {{-- Transaction Type & With MRP Value --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <span class="small">1. Sale / 2. Sale Return / 3. Both :</span>
                    <input type="text" name="transaction_type" class="form-control form-control-sm d-inline-block" value="3" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">With MRP Value</label>
                    <input type="checkbox" name="with_mrp_value" class="form-check-input ms-2">
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

            {{-- S(elective) / A(ll) & With Address --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">S (elective) / A (ll) :</label>
                    <input type="text" name="selective_all" class="form-control form-control-sm d-inline-block" value="S" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">With Address [ Y / N ] :</label>
                    <input type="text" name="with_address" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Company --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small">Company :</label></div>
                <div class="col-md-5"><select name="company_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            </div>

            {{-- Division --}}
            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 120px;"><label class="small">Division :</label></div>
                <div class="col-auto"><input type="text" name="division_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
            </div>

            {{-- Tagged Customers & Remove Tags --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Tagged Customers [ Y / N ] :</label>
                    <input type="text" name="tagged_customers" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">Remove Tags [ Y / N ] :</label>
                    <input type="text" name="remove_customer_tags" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Filters Section --}}
            <fieldset class="border p-2 mb-3" style="background: #e0d0e0;">
                <legend class="small text-muted fw-bold w-auto px-2" style="font-size: 11px;">Filters</legend>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Sales Man:</label></div>
                    <div class="col-auto"><input type="text" name="salesman_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="salesman_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($salesmen ?? [] as $sm)<option value="{{ $sm->id }}">{{ $sm->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Area:</label></div>
                    <div class="col-auto"><input type="text" name="area_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="area_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($areas ?? [] as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Route:</label></div>
                    <div class="col-auto"><input type="text" name="route_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="route_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($routes ?? [] as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-2">
                    <div class="col-auto" style="width: 100px;"><label class="small">Customer :</label></div>
                    <div class="col-auto"><input type="text" name="customer_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="customer_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($customers ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                </div>
            </fieldset>

            {{-- With Br./Expiry & Wholesale/Retail --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small">With Br. / Expiry [ Y / N ] :</label>
                    <input type="text" name="with_br_expiry" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">W(hole Sale) / R(etail) :</label>
                    <input type="text" name="wholesale_retail" class="form-control form-control-sm d-inline-block" value="" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col">
                    <button type="button" class="btn btn-secondary btn-sm border px-3">Excel</button>
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
