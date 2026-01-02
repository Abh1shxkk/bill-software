@extends('layouts.admin')

@section('title', 'Sales Man - Customer Wise Sales')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">Sales Man - Customer Wise Sales</h4>
    </div>

    <div class="border p-3" style="background: #f0f0f0;">
        <form method="GET" id="filterForm">
            <div class="mb-2">
                <span class="small">1. Sale / 2. Sale Return / 3. Debit Note / 4. Credit Note / 5. Consolidated Sale :</span>
                <input type="text" name="transaction_type" class="form-control form-control-sm d-inline-block" value="{{ $transactionType ?? '5' }}" style="width: 35px;">
            </div>

            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small fw-bold">From :</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" style="width: 140px;">
                </div>
                <div class="col-auto">
                    <label class="small fw-bold">To :</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}" style="width: 140px;">
                </div>
            </div>

            <div class="mb-2">
                <label class="small">S(elective) / A(ll) :</label>
                <input type="text" name="selective" class="form-control form-control-sm d-inline-block" value="{{ $selective ?? 'S' }}" style="width: 35px;">
            </div>

            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 90px;"><label class="small">SALES MAN :</label></div>
                <div class="col-md-5">
                    <select name="salesman_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($salesmen ?? [] as $s)
                            <option value="{{ $s->id }}" {{ ($salesmanId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <fieldset class="border rounded p-2 mb-2">
                <legend class="float-none w-auto px-2 fs-6 text-primary small">Filters</legend>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 90px;"><label class="small">Sales Man:</label></div>
                    <div class="col-md-5">
                        <select name="filter_salesman_id" class="form-select form-select-sm">
                            <option value="">-- All --</option>
                            @foreach($salesmen ?? [] as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 90px;"><label class="small">Area:</label></div>
                    <div class="col-md-5">
                        <select name="area_id" class="form-select form-select-sm">
                            <option value="">-- All --</option>
                            @foreach($areas ?? [] as $a)
                                <option value="{{ $a->id }}" {{ ($areaId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 90px;"><label class="small">Route:</label></div>
                    <div class="col-md-5">
                        <select name="route_id" class="form-select form-select-sm">
                            <option value="">-- All --</option>
                            @foreach($routes ?? [] as $r)
                                <option value="{{ $r->id }}" {{ ($routeId ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-1">
                    <div class="col-auto" style="width: 90px;"><label class="small">State:</label></div>
                    <div class="col-md-5">
                        <select name="state_id" class="form-select form-select-sm">
                            <option value="">-- All --</option>
                            @foreach($states ?? [] as $st)
                                <option value="{{ $st->id }}" {{ ($stateId ?? '') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </fieldset>

            <div class="mb-2">
                <label class="small">Tagged Customers [ Y / N ] :</label>
                <input type="text" name="tagged_customers" class="form-control form-control-sm d-inline-block" value="{{ $taggedCustomers ?? 'N' }}" style="width: 35px;">
                <label class="small ms-3">Remove Tags [ Y / N ] :</label>
                <input type="text" name="remove_tags" class="form-control form-control-sm d-inline-block" value="{{ $removeTags ?? 'N' }}" style="width: 35px;">
            </div>

            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 90px;"><label class="small">Customer :</label></div>
                <div class="col-md-5">
                    <select name="customer_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($customers ?? [] as $c)
                            <option value="{{ $c->id }}" {{ ($customerId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">With Br. / Expiry [ Y / N ] :</label>
                    <input type="text" name="with_br_expiry" class="form-control form-control-sm d-inline-block" value="{{ $withBrExpiry ?? 'N' }}" style="width: 35px;">
                </div>
                <div class="col-auto">
                    <label class="small">Flag :</label>
                    <input type="text" name="flag" class="form-control form-control-sm d-inline-block" value="{{ $flag ?? '' }}" style="width: 60px;">
                </div>
            </div>

            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col-auto">
                    <button type="button" class="btn btn-light btn-sm border px-4" id="btnExcel"><u>E</u>xcel</button>
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
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (!viewTypeInput) { viewTypeInput = document.createElement('input'); viewTypeInput.type = 'hidden'; viewTypeInput.name = 'view_type'; form.appendChild(viewTypeInput); }
        viewTypeInput.value = 'print'; form.target = '_blank'; form.submit(); form.target = '_self'; viewTypeInput.value = '';
    });
    document.getElementById('btnExcel').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (!exportInput) { exportInput = document.createElement('input'); exportInput.type = 'hidden'; exportInput.name = 'export'; form.appendChild(exportInput); }
        exportInput.value = 'excel'; form.submit(); exportInput.value = '';
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') window.history.back(); if (e.key === 'F7') { e.preventDefault(); document.getElementById('btnView').click(); } });
});
</script>
@endsection
