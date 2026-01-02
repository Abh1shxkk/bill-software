@extends('layouts.admin')

@section('title', 'Route - Item Wise Sale')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">ROUTE - ITEM WISE SALE</h4>
    </div>

    <div class="border p-3" style="background: #f0f0f0;">
        <form method="GET" id="filterForm">
            <div class="row g-2 mb-3">
                <div class="col-auto"><label class="small fw-bold"><u>F</u>rom :</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}" style="width: 140px;"></div>
                <div class="col-auto ms-5"><label class="small fw-bold">To :</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}" style="width: 140px;"></div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-auto" style="width: 130px;"><label class="small text-danger fw-bold">Route :</label></div>
                <div class="col-auto"><input type="text" name="route_code" class="form-control form-control-sm" value="{{ $routeCode ?? '' }}" style="width: 120px;"></div>
                <div class="col-md-4"><select name="route_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($routes ?? [] as $r)<option value="{{ $r->id }}" {{ ($routeId ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>@endforeach</select></div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-auto"><label class="small">Selective Item [ Y / N ] :</label></div>
                <div class="col-auto"><input type="text" name="selective_item" class="form-control form-control-sm d-inline-block" value="{{ $selectiveItem ?? 'Y' }}" style="width: 35px;" maxlength="1"></div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-auto" style="width: 130px;"><label class="small text-danger fw-bold">Item :</label></div>
                <div class="col-auto"><input type="text" name="item_code" class="form-control form-control-sm" value="{{ $itemCode ?? '' }}" style="width: 120px;"></div>
                <div class="col-md-4"><select name="item_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($items ?? [] as $item)<option value="{{ $item->id }}" {{ ($itemId ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>@endforeach</select></div>
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
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') window.history.back(); if (e.key === 'F7') { e.preventDefault(); document.getElementById('btnView').click(); } });
});
</script>
@endsection