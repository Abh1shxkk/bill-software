@extends('layouts.admin')

@section('title', 'Sales Man Wise - Sale Book')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">SALES MAN WISE - SALE BOOK</h4>
    </div>

    <div class="border p-3" style="background: #f0f0f0;">
        <form method="GET" id="filterForm">
            <div class="mb-2">
                <span class="small">1. Sale / 2. Sale Return / 3. Debit Note / 4. Credit Note :</span>
                <input type="text" name="transaction_type" class="form-control form-control-sm d-inline-block" value="{{ $transactionType ?? '1' }}" style="width: 35px;" maxlength="1">
            </div>

            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small fw-bold"><u>F</u>rom :</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" style="width: 140px;">
                </div>
                <div class="col-auto">
                    <label class="small fw-bold">To :</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}" style="width: 140px;">
                </div>
            </div>

            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small">Selective Y/N :</label></div>
                <div class="col-auto">
                    <input type="text" name="selective" class="form-control form-control-sm" value="{{ $selective ?? 'N' }}" style="width: 35px;" maxlength="1">
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 120px;"><label class="small">Sales Man :</label></div>
                <div class="col-md-5">
                    <select name="salesman_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($salesmen as $s)
                            <option value="{{ $s->id }}" {{ ($salesmanId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->code }} - {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filters Section -->
            <fieldset class="border p-2 mb-2" style="background: #e8e8e8;">
                <legend class="small text-primary fw-bold w-auto px-2" style="font-size: 11px;">Filters</legend>
                
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Sales Man :</label></div>
                    <div class="col-md-4">
                        <select name="filter_salesman_id" class="form-select form-select-sm">
                            <option value="">00</option>
                            @foreach($salesmen as $s)
                                <option value="{{ $s->id }}" {{ ($filterSalesmanId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->code }} - {{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Area :</label></div>
                    <div class="col-md-4">
                        <select name="area_id" class="form-select form-select-sm">
                            <option value="">00</option>
                            @foreach($areas as $a)
                                <option value="{{ $a->id }}" {{ ($areaId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 100px;"><label class="small">Route :</label></div>
                    <div class="col-md-4">
                        <select name="route_id" class="form-select form-select-sm">
                            <option value="">00</option>
                            @foreach($routes as $r)
                                <option value="{{ $r->id }}" {{ ($routeId ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </fieldset>

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
