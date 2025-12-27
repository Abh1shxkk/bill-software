@extends('layouts.admin')

@section('title', 'Sales Man - Item Invoice Wise Sales')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #8B0000;">Sales Man - Item Invoice Wise Sales</h4>
    </div>

    <div class="border p-3" style="background: #f0f0f0;">
        <form method="GET" id="filterForm">
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
                <div class="col-auto" style="width: 120px;"><label class="small text-primary fw-bold">Sales Man :</label></div>
                <div class="col-md-4">
                    <select name="salesman_id" class="form-select form-select-sm">
                        <option value="">-- All --</option>
                        @foreach($salesmen as $s)
                            <option value="{{ $s->id }}" {{ ($salesmanId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->code }} - {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small">Selective Item [ Y/ N ] :</label></div>
                <div class="col-auto">
                    <input type="text" name="selective_item" class="form-control form-control-sm" value="{{ $selectiveItem ?? 'Y' }}" style="width: 35px;" maxlength="1">
                </div>
            </div>

            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small text-primary fw-bold">Item :</label></div>
                <div class="col-md-4">
                    <select name="item_id" class="form-select form-select-sm" id="itemSelect">
                        <option value="">-- All --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ ($itemId ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 120px;"><label class="small text-primary fw-bold">Division :</label></div>
                <div class="col-md-4">
                    <select name="division_id" class="form-select form-select-sm">
                        <option value="00">00</option>
                        @foreach($divisions as $d)
                            <option value="{{ $d->id }}" {{ ($divisionId ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 120px;"><label class="small">With Br. / Expiry [ Y / N ] :</label></div>
                <div class="col-auto">
                    <input type="text" name="with_br_expiry" class="form-control form-control-sm" value="{{ $withBrExpiry ?? 'N' }}" style="width: 35px;" maxlength="1">
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
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (!viewTypeInput) {
            viewTypeInput = document.createElement('input');
            viewTypeInput.type = 'hidden';
            viewTypeInput.name = 'view_type';
            form.appendChild(viewTypeInput);
        }
        viewTypeInput.value = 'print';
        form.target = '_blank';
        form.submit();
        form.target = '_self';
        viewTypeInput.value = '';
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
        if (e.key === 'F7') { e.preventDefault(); document.getElementById('btnView').click(); }
    });

    // Toggle item select based on selective_item value
    const selectiveInput = document.querySelector('input[name="selective_item"]');
    const itemSelect = document.getElementById('itemSelect');
    
    function toggleItemSelect() {
        itemSelect.disabled = selectiveInput.value.toUpperCase() !== 'Y';
    }
    
    selectiveInput.addEventListener('input', toggleItemSelect);
    toggleItemSelect();
});
</script>
@endsection
