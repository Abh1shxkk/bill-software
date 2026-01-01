@extends('layouts.admin')

@section('title', 'Discount Wise - Item Wise Invoice Wise')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #000080;">Item Wise</h4>
    </div>

    <div class="border p-3" style="background: #f0e8f0;">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.discount-wise-sales.item-wise-invoice-wise') }}">
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

            {{-- Selective Discount & Discount % --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">Selective Discount [ Y / N ] :</label>
                    <input type="text" name="selective_discount" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">Discount :</label>
                    <input type="text" name="discount_percent" class="form-control form-control-sm d-inline-block" value="" style="width: 50px;">
                    <span class="small">%</span>
                </div>
            </div>

            {{-- Comparison Type & DPC Item --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">1. ( >= ) / 2. ( <= ) / 3. ( = ) :</label>
                    <input type="text" name="comparison_type" class="form-control form-control-sm d-inline-block" value="1" style="width: 35px;" maxlength="1">
                </div>
                <div class="col-auto ms-4">
                    <label class="small">DPC Item [ Y / N ] :</label>
                    <input type="text" name="dpc_item" class="form-control form-control-sm d-inline-block" value="N" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- Company --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Company :</label></div>
                <div class="col-auto"><input type="text" name="company_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="company_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            </div>

            {{-- Item --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Item :</label></div>
                <div class="col-auto"><input type="text" name="item_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="item_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($items ?? [] as $i)<option value="{{ $i->id }}">{{ $i->name }}</option>@endforeach</select></div>
            </div>

            {{-- Customer --}}
            <div class="row g-2 mb-3">
                <div class="col-auto" style="width: 100px;"><label class="small">Customer :</label></div>
                <div class="col-auto"><input type="text" name="customer_code" class="form-control form-control-sm" value="00" style="width: 60px;"></div>
                <div class="col-md-4"><select name="customer_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($customers ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col">
                    <button type="button" class="btn btn-secondary btn-sm border px-3" id="btnExcel"><u>E</u>xcel</button>
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
    document.getElementById('btnExcel').addEventListener('click', function() {
        let vi = form.querySelector('input[name="export"]');
        if (!vi) { vi = document.createElement('input'); vi.type = 'hidden'; vi.name = 'export'; form.appendChild(vi); }
        vi.value = 'excel'; form.submit(); vi.value = '';
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') window.history.back(); });
});
</script>
@endsection
