@extends('layouts.admin')

@section('title', 'Valuation of Closing Stock')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">VALUATION OF CLOSING STOCK</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.stock.valuation-of-closing-stock') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Closing stock as on :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="closing_date" class="form-control form-control-sm" value="{{ request('closing_date', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">D(etailed) / S(ummerized) :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="report_type" class="form-control form-control-sm text-center" value="{{ request('report_type', 'S') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input type="checkbox" name="hsn_wise" class="form-check-input" id="hsnWise" {{ request('hsn_wise') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="hsnWise">HSN Wise</label>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Selective Company [ Y/N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="selective_company" class="form-control form-control-sm text-center" value="{{ request('selective_company', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="division" class="form-control form-control-sm text-center" value="{{ request('division', '00') }}" style="width: 50px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Batch Wise [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="batch_wise" class="form-control form-control-sm text-center" value="{{ request('batch_wise', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Statement Date :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="statement_date" class="form-control form-control-sm" value="{{ request('statement_date', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Value on C(ost) / S(rate) / M(rp) / P(rate) / G(Cost+GST) :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="value_on" class="form-control form-control-sm text-center" value="{{ request('value_on', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Item Category :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="item_category_code" class="form-control form-control-sm text-center" value="{{ request('item_category_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-md-3">
                        <select name="item_category" class="form-select form-select-sm">
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Item Location :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="item_location" class="form-select form-select-sm">
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Tagged Items [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="tagged_items" class="form-control form-control-sm text-center" value="{{ request('tagged_items', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Remove Tags [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="remove_tags" class="form-control form-control-sm text-center" value="{{ request('remove_tags', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Select GST [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="select_gst" class="form-control form-control-sm text-center" value="{{ request('select_gst', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="gst_percent" class="form-control form-control-sm text-center" value="{{ request('gst_percent', '') }}" style="width: 60px;">
                    </div>
                    <div class="col-md-1">
                        <label class="fw-bold mb-0">%</label>
                    </div>
                    <div class="col-md-3">
                        <span class="text-primary fw-bold">FifoSameBatch - Yes</span>
                    </div>
                </div>
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border w-100 fw-bold shadow-sm" onclick="exportToExcel()"><u>E</u>xcel</button>
                    </div>
                    <div class="col-md-6 offset-md-4 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="window.history.back()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.inventory.stock.valuation-of-closing-stock") }}?' + params.toString();
}
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
</style>
@endpush
