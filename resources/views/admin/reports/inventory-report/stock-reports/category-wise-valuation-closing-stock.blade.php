@extends('layouts.admin')

@section('title', 'Category Wise Valuation of Closing Stock')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">CATEGORY WISE VALUATION OF CLOSING STOCK</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.stock.category-wise-valuation-closing-stock') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Closing stock as on :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="closing_date" class="form-control form-control-sm" value="{{ request('closing_date', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Statement Date :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="statement_date" class="form-control form-control-sm" value="{{ request('statement_date', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Value on C(ost) / S(rate) / M(RP) / P(rate) :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="value_on" class="form-control form-control-sm text-center" value="{{ request('value_on', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-6 offset-md-6 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="window.history.back()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
</style>
@endpush
