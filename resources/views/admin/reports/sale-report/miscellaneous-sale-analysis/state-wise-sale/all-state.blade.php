@extends('layouts.admin')

@section('title', 'State Wise Sales - All State')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">State Wise Sales</h4>
    </div>

    <div class="border p-3" style="background: #f0f0f0;">
        <form method="GET" id="filterForm">
            {{-- Transaction Type --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <span class="small">1. Sale / 2. Sale Return / 3. Debit Note / 4. Credit Note / 5. Consolidated Sale :</span>
                </div>
                <div class="col-auto">
                    <input type="text" name="transaction_type" class="form-control form-control-sm" value="{{ $transactionType ?? '5' }}" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Date Range --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small fw-bold"><u>F</u>rom :</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
                <div class="col-auto ms-5">
                    <label class="small fw-bold">To :</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
            </div>

            {{-- Sales Man --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Sales Man:</label></div>
                <div class="col-auto"><input type="text" name="salesman_code" class="form-control form-control-sm" value="{{ $salesmanCode ?? '00' }}" style="width: 60px;"></div>
                <div class="col-md-4"><select name="salesman_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($salesmen ?? [] as $sm)<option value="{{ $sm->id }}" {{ ($salesmanId ?? '') == $sm->id ? 'selected' : '' }}>{{ $sm->name }}</option>@endforeach</select></div>
            </div>

            {{-- Area --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Area:</label></div>
                <div class="col-auto"><input type="text" name="area_code" class="form-control form-control-sm" value="{{ $areaCode ?? '00' }}" style="width: 60px;"></div>
                <div class="col-md-4"><select name="area_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($areas ?? [] as $a)<option value="{{ $a->id }}" {{ ($areaId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>@endforeach</select></div>
            </div>

            {{-- Route --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Route:</label></div>
                <div class="col-auto"><input type="text" name="route_code" class="form-control form-control-sm" value="{{ $routeCode ?? '00' }}" style="width: 60px;"></div>
                <div class="col-md-4"><select name="route_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($routes ?? [] as $r)<option value="{{ $r->id }}" {{ ($routeId ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>@endforeach</select></div>
            </div>

            {{-- State --}}
            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 100px;"><label class="small">State:</label></div>
                <div class="col-auto"><input type="text" name="state_code" class="form-control form-control-sm" value="{{ $stateCode ?? '00' }}" style="width: 60px;"></div>
                <div class="col-md-4"><select name="state_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($states ?? [] as $s)<option value="{{ $s->id }}" {{ ($stateId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
            </div>

            {{-- Order By & Ascending/Descending --}}
            <div class="row g-2 mb-1">
                <div class="col-auto">
                    <label class="small">Order By N(ame) / V(alue) :</label>
                    <input type="text" name="order_by" class="form-control form-control-sm d-inline-block" value="{{ $orderBy ?? 'N' }}" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">A(scending) / D(escending) :</label>
                    <input type="text" name="order_dir" class="form-control form-control-sm d-inline-block" value="{{ $orderDir ?? 'A' }}" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- With Br./Expiry & Series --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small">With Br. / Expiry [ Y / N ] :</label>
                    <input type="text" name="with_br_expiry" class="form-control form-control-sm d-inline-block" value="{{ $withBrExpiry ?? 'N' }}" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">Series :</label>
                    <input type="text" name="series" class="form-control form-control-sm d-inline-block" value="{{ $series ?? '00' }}" style="width: 50px;">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col">
                    <button type="button" class="btn btn-light btn-sm border px-4" id="btnExcel">Excel</button>
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
    
    // View Button
    document.getElementById('btnView').addEventListener('click', function() {
        let vi = form.querySelector('input[name="view_type"]');
        if (!vi) { vi = document.createElement('input'); vi.type = 'hidden'; vi.name = 'view_type'; form.appendChild(vi); }
        vi.value = 'print'; 
        form.target = '_blank'; 
        form.submit(); 
        form.target = '_self'; 
        vi.value = '';
    });

    // Excel Button
    document.getElementById('btnExcel').addEventListener('click', function() {
        let exp = form.querySelector('input[name="export"]');
        if (!exp) { exp = document.createElement('input'); exp.type = 'hidden'; exp.name = 'export'; form.appendChild(exp); }
        exp.value = 'excel';
        form.submit();
        exp.value = '';
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) { 
        if (e.key === 'Escape') window.history.back(); 
        if (e.key === 'F7') { e.preventDefault(); document.getElementById('btnView').click(); }
    });
});
</script>
@endsection
