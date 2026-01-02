@extends('layouts.admin')

@section('title', 'State Wise - Company Wise Sale')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">STATE WISE - COMPANY WISE SALE</h4>
    </div>

    <div class="border p-3" style="background: #f0f0f0;">
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

            {{-- Selective --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Selective Y/N :</label>
                    <input type="text" name="selective" class="form-control form-control-sm d-inline-block" value="{{ $selective ?? 'Y' }}" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- State --}}
            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 100px;"><label class="small">State:</label></div>
                <div class="col-auto"><input type="text" name="state_code" class="form-control form-control-sm" value="" style="width: 60px;"></div>
                <div class="col-md-4"><select name="state_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($states ?? [] as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
            </div>

            {{-- Tagged Companies --}}
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

            {{-- Company & Division --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Company:</label></div>
                <div class="col-auto"><input type="text" name="company_code" class="form-control form-control-sm" value="" style="width: 60px;"></div>
                <div class="col-md-4"><select name="company_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-auto" style="width: 100px;"><label class="small">Division:</label></div>
                <div class="col-auto"><input type="text" name="division_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
            </div>

            {{-- Filters Section --}}
            <fieldset class="border p-2 mb-3" style="background: #e8e8e8;">
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
                    <div class="col-auto" style="width: 100px;"><label class="small">State:</label></div>
                    <div class="col-auto"><input type="text" name="filter_state_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                    <div class="col-md-4"><select name="filter_state_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($states ?? [] as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
                </div>
            </fieldset>

            {{-- With Br./Expiry --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small">With Br. / Expiry [ Y / N ] :</label>
                    <input type="text" name="with_br_expiry" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-3 pt-2 border-top">
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
