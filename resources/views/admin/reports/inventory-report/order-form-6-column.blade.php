@extends('layouts.admin')

@section('title', 'Order Form Six Column')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Order Form Six Column</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="">
                <!-- S(elective) / A(ll) Company & D(irect) / I(ndirect) -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>S</u>(elective) / <u>A</u>(ll) Company :</label>
                    </div>
                    <div class="col-auto">
                        <select name="sa_company" id="sa_company" class="form-select form-select-sm" style="width: 60px;">
                            <option value="A" {{ ($saCompany ?? 'A') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="S" {{ ($saCompany ?? '') == 'S' ? 'selected' : '' }}>S</option>
                        </select>
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0"><u>D</u>(irect) / <u>I</u>(ndirect) :</label>
                    </div>
                    <div class="col-auto">
                        <select name="di_type" id="di_type" class="form-select form-select-sm" style="width: 60px;">
                            <option value="D" {{ ($diType ?? 'D') == 'D' ? 'selected' : '' }}>D</option>
                            <option value="I" {{ ($diType ?? '') == 'I' ? 'selected' : '' }}>I</option>
                        </select>
                    </div>
                </div>

                <!-- Tag Company & With/Without/All Stock -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Tag Company [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <select name="tag_company" id="tag_company" class="form-select form-select-sm" style="width: 60px;">
                            <option value="N" {{ ($tagCompany ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                            <option value="Y" {{ ($tagCompany ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                        </select>
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">1. With / 2. Without / 3. All Stock :</label>
                    </div>
                    <div class="col-auto">
                        <select name="stock_type" id="stock_type" class="form-select form-select-sm" style="width: 60px;">
                            <option value="3" {{ ($stockType ?? '3') == '3' ? 'selected' : '' }}>3</option>
                            <option value="1" {{ ($stockType ?? '') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($stockType ?? '') == '2' ? 'selected' : '' }}>2</option>
                        </select>
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="company_code" class="form-control form-control-sm" value="{{ $companyCode ?? '' }}" style="width: 80px;">
                    </div>
                    <div class="col-md-6">
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Supplier -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Supplier :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="supplier_code" class="form-control form-control-sm" value="{{ $supplierCode ?? '00' }}" style="width: 80px;">
                    </div>
                    <div class="col-md-6">
                        <select name="supplier_id" id="supplier_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->id }}" {{ ($supplierId ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Category -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Category :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="category_code" class="form-control form-control-sm" value="{{ $categoryCode ?? '00' }}" style="width: 80px;">
                    </div>
                    <div class="col-md-6">
                        <select name="category_id" id="category_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Company Status -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Company Status :</label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="company_status" class="form-control form-control-sm" value="{{ $companyStatus ?? '' }}">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">E<u>x</u>cel</button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
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
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.set('export', 'excel');
    window.location.href = '{{ url()->current() }}?' + params.toString();
}

function closeWindow() {
    window.location.href = '{{ route("admin.reports.inventory") ?? "#" }}';
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
