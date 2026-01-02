@extends('layouts.admin')

@section('title', 'Area Wise - Month Wise Sale')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">AREA WISE - MONTH WISE SALE</h4>
    </div>

    <div class="border p-3" style="background: #f0f0f0;">
        <form method="GET" id="filterForm">
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small fw-bold"><u>F</u>rom :</label>
                    <input type="number" name="year_from" class="form-control form-control-sm" value="{{ $yearFrom ?? date('Y') }}" style="width: 80px;" min="2000" max="2099">
                </div>
                <div class="col-auto ms-3">
                    <label class="small fw-bold">To :</label>
                    <input type="number" name="year_to" class="form-control form-control-sm" value="{{ $yearTo ?? (date('Y') + 1) }}" style="width: 80px;" min="2000" max="2099">
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small">Sales in: 1. (Thousand) / 2. (Ten Thousand) / 3. (Lacs) / 4. (Actual) :</label>
                    <input type="text" name="sales_in" class="form-control form-control-sm d-inline-block" value="{{ $salesIn ?? '4' }}" style="width: 35px;" maxlength="1">
                </div>
            </div>

            <fieldset class="border p-2 mb-3" style="background: #e8e8e8;">
                <legend class="small text-muted fw-bold w-auto px-2" style="font-size: 11px;">Filters</legend>
                
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 80px;"><label class="small">Sales Man :</label></div>
                    <div class="col-auto">
                        <input type="text" name="salesman_code" class="form-control form-control-sm" value="{{ $salesmanCode ?? '00' }}" style="width: 50px;">
                    </div>
                    <div class="col-md-4">
                        <select name="salesman_id" class="form-select form-select-sm">
                            <option value="">-- All --</option>
                            @foreach($salesmen ?? [] as $s)
                                <option value="{{ $s->id }}" {{ ($salesmanId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 80px;"><label class="small">Area :</label></div>
                    <div class="col-auto">
                        <input type="text" name="area_code" class="form-control form-control-sm" value="{{ $areaCode ?? '00' }}" style="width: 50px;">
                    </div>
                    <div class="col-md-4">
                        <select name="area_id" class="form-select form-select-sm">
                            <option value="">-- All --</option>
                            @foreach($areas ?? [] as $a)
                                <option value="{{ $a->id }}" {{ ($areaId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 80px;"><label class="small">Route :</label></div>
                    <div class="col-auto">
                        <input type="text" name="route_code" class="form-control form-control-sm" value="{{ $routeCode ?? '00' }}" style="width: 50px;">
                    </div>
                    <div class="col-md-4">
                        <select name="route_id" class="form-select form-select-sm">
                            <option value="">-- All --</option>
                            @foreach($routes ?? [] as $r)
                                <option value="{{ $r->id }}" {{ ($routeId ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-auto" style="width: 80px;"><label class="small">State :</label></div>
                    <div class="col-auto">
                        <input type="text" name="state_code" class="form-control form-control-sm" value="{{ $stateCode ?? '00' }}" style="width: 50px;">
                    </div>
                    <div class="col-md-4">
                        <select name="state_id" class="form-select form-select-sm">
                            <option value="">-- All --</option>
                            @foreach($states ?? [] as $st)
                                <option value="{{ $st->id }}" {{ ($stateId ?? '') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </fieldset>

            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">With Br. / Expiry [ Y / N ] :</label>
                    <input type="text" name="with_br_expiry" class="form-control form-control-sm d-inline-block" value="{{ $withBrExpiry ?? 'N' }}" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">With DN / CN [ Y / N ] :</label>
                    <input type="text" name="with_dn_cn" class="form-control form-control-sm d-inline-block" value="{{ $withDnCn ?? 'Y' }}" style="width: 35px;" maxlength="1">
                </div>
            </div>

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
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
        if (e.key === 'F7') { e.preventDefault(); document.getElementById('btnView').click(); }
    });
});
</script>
@endsection
