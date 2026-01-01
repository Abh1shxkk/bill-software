@extends('layouts.admin')

@section('title', 'Sale Below Cost')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #800080;">Sale Below Cost</h4>
    </div>

    <div class="border p-3" style="background: #f0e8f0;">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.item-wise-sales.below-cost-item-sale') }}">
            {{-- Date Range --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small fw-bold">From :</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
                <div class="col-auto ms-5">
                    <label class="small fw-bold">To :</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
            </div>

            {{-- Selective Company --}}
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label class="small">Selective Company [Y/ N] :</label>
                </div>
                <div class="col-auto ms-auto">
                    <input type="text" name="selective_company" class="form-control form-control-sm" value="Y" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Company --}}
            <div class="row g-2 mb-3">
                <div class="col-auto" style="width: 100px;"><label class="small">Company :</label></div>
                <div class="col-md-5"><select name="company_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-4 pt-2 border-top">
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
