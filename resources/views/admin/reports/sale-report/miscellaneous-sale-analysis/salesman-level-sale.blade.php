@extends('layouts.admin')

@section('title', 'Marketing Levels Report')

@section('content')
<div class="container-fluid py-2">
    <div class="text-center py-2 mb-2" style="background: linear-gradient(to bottom, #f8d7da, #f5c6cb); border: 1px solid #ccc;">
        <h4 class="mb-0 fst-italic fw-bold" style="color: #000080;">Marketing Levels Report</h4>
    </div>

    <div class="border p-3" style="background: #f0e8f0;">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.salesman-level-sale') }}">
            {{-- Transaction Type --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <label class="small">1.Sale / 2.Sale Return / 3.Br./Exp. / 4.Consolidated :</label>
                    <input type="text" name="transaction_type" class="form-control form-control-sm d-inline-block" value="4" style="width: 35px;" maxlength="1">
                </div>
            </div>

            {{-- From Master Checkbox --}}
            <div class="row g-2 mb-2">
                <div class="col-auto">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="from_master" id="fromMaster" value="1">
                        <label class="form-check-label small" for="fromMaster">From Master</label>
                    </div>
                </div>
            </div>

            {{-- Select Level --}}
            <div class="row g-2 mb-2">
                <div class="col-auto" style="width: 100px;"><label class="small">Select <u>L</u>evel :</label></div>
                <div class="col-md-3">
                    <select name="level" id="levelSelect" class="form-select form-select-sm" size="5" style="height: auto;">
                        <option value="salesman" selected>SALES MAN</option>
                        <option value="area_mgr">AREA MGR.</option>
                        <option value="reg_mgr">REG.MGR.</option>
                        <option value="mkt_mgr">MKT.MGR.</option>
                        <option value="gen_mgr">GEN.MGR.</option>
                        <option value="dc_mgr">D.C.MGR</option>
                        <option value="c_mgr">C.MGR</option>
                    </select>
                </div>
            </div>

            {{-- Date Range --}}
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <label class="small fw-bold"><u>F</u>rom :</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
                <div class="col-auto ms-3">
                    <label class="small fw-bold"><u>T</u>o :</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}" style="width: 140px;">
                </div>
                <div class="col-auto ms-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="with_sman_only" id="withSManOnly" value="1">
                        <label class="form-check-label small" for="withSManOnly">With SMan Only</label>
                    </div>
                </div>
            </div>

            {{-- Name --}}
            <div class="row g-2 mb-1">
                <div class="col-auto" style="width: 100px;"><label class="small">Name :</label></div>
                <div class="col-auto"><input type="text" name="name_code" class="form-control form-control-sm" value="" style="width: 60px;"></div>
                <div class="col-md-4"><select name="salesman_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($salesmen ?? [] as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
            </div>

            {{-- Company --}}
            <div class="row g-2 mb-3">
                <div class="col-auto" style="width: 100px;"><label class="small">Company :</label></div>
                <div class="col-auto"><input type="text" name="company_code" class="form-control form-control-sm" value="" style="width: 60px;"></div>
                <div class="col-md-4"><select name="company_id" class="form-select form-select-sm"><option value="">-- All --</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            </div>

            {{-- Selective Item Section --}}
            <fieldset class="border p-2 mb-3">
                <legend class="w-auto px-2 small">
                    <input class="form-check-input" type="checkbox" name="selective_item" id="selectiveItem" value="1">
                    <label class="form-check-label small" for="selectiveItem">Selective Item</label>
                </legend>
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-bordered mb-0" style="font-size: 11px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">S.No</th>
                                <th style="width: 100px;">Code</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody id="selectiveItemsTable">
                            @for($i = 0; $i < 8; $i++)
                            <tr>
                                <td></td>
                                <td><input type="text" name="item_codes[]" class="form-control form-control-sm border-0 p-0" style="background: {{ $i == 0 ? '#ffffcc' : 'transparent' }};"></td>
                                <td></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </fieldset>

            {{-- Action Buttons --}}
            <div class="row g-2 mt-3 pt-2 border-top">
                <div class="col">
                    <a href="#" class="text-primary text-decoration-underline fw-bold" id="btnSave">SAVE (END)</a>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-secondary btn-sm border px-3 me-2" id="btnRemove">Remove</button>
                    <button type="button" class="btn btn-secondary btn-sm border px-4 me-2" id="btnPrint">Print</button>
                    <button type="button" class="btn btn-secondary btn-sm border px-4" onclick="window.history.back()">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    
    // Print button
    document.getElementById('btnPrint').addEventListener('click', function() {
        let vi = form.querySelector('input[name="view_type"]');
        if (!vi) { vi = document.createElement('input'); vi.type = 'hidden'; vi.name = 'view_type'; form.appendChild(vi); }
        vi.value = 'print'; form.target = '_blank'; form.submit(); form.target = '_self'; vi.value = '';
    });
    
    // Remove button - clear form
    document.getElementById('btnRemove').addEventListener('click', function() {
        form.reset();
    });
    
    // Save button
    document.getElementById('btnSave').addEventListener('click', function(e) {
        e.preventDefault();
        alert('Settings Saved!');
    });
    
    // Escape key to close
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') window.history.back(); });
    
    // Level select change - update name dropdown based on level
    document.getElementById('levelSelect').addEventListener('change', function() {
        // Could fetch different personnel based on level - for now just show alert
        console.log('Level changed to: ' + this.value);
    });
});
</script>
@endsection
