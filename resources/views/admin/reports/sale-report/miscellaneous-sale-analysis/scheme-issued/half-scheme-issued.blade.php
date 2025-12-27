@extends('layouts.admin')

@section('title', 'Half Scheme Issued')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">Half Scheme Issued</h4>
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

            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 90px;"><label class="small">Company :</label></div>
                <div class="col-md-5">
                    <select name="company_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($companies ?? [] as $c)
                            <option value="{{ $c->id }}" {{ ($companyId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 90px;"><label class="small">Item :</label></div>
                <div class="col-md-5">
                    <select name="item_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($items ?? [] as $item)
                            <option value="{{ $item->id }}" {{ ($itemId ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 90px;"><label class="small">Sales Man :</label></div>
                <div class="col-md-5">
                    <select name="salesman_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($salesmen ?? [] as $s)
                            <option value="{{ $s->id }}" {{ ($salesmanId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
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
                    <label class="small">Order By C(ompany) / I(tem) :</label>
                    <input type="text" name="order_by" class="form-control form-control-sm d-inline-block" value="{{ $orderBy ?? 'C' }}" style="width: 35px;">
                </div>
                <div class="col-auto">
                    <label class="small">With Batch [ Y / N ] :</label>
                    <input type="text" name="with_batch" class="form-control form-control-sm d-inline-block" value="{{ $withBatch ?? 'N' }}" style="width: 35px;">
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
