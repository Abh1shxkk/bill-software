@extends('layouts.admin')

@section('title', 'Category Wise Stock Status')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffcc80;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">Category Wise Stock Status</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #ffffcc;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.stock.category-wise-stock-status') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="company_code" class="form-control form-control-sm text-center" value="{{ request('company_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-md-3">
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Value On</label>
                    </div>
                    <div class="col-md-2">
                        <select name="value_on" class="form-select form-select-sm">
                            <option value="cost" {{ request('value_on') == 'cost' ? 'selected' : '' }}>Cost Rate</option>
                            <option value="sale" {{ request('value_on') == 'sale' ? 'selected' : '' }}>Sale Rate</option>
                            <option value="mrp" {{ request('value_on') == 'mrp' ? 'selected' : '' }}>MRP</option>
                            <option value="purchase" {{ request('value_on') == 'purchase' ? 'selected' : '' }}>Pur. Rate</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Item Category :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="category_code" class="form-control form-control-sm text-center" value="{{ request('category_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-info border px-4 fw-bold shadow-sm me-2">OK</button>
                        <button type="button" class="btn btn-info border px-4 fw-bold shadow-sm" onclick="window.history.back()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card mt-2">
        @if(isset($reportData) && $reportData->count() > 0)
        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between">
            <strong>Category Wise Stock Status</strong>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="printReport()"><i class="fas fa-print"></i> Print</button>
                <button type="button" class="btn btn-sm btn-success ms-1" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>
            </div>
        </div>
        @endif
        <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0" style="background-color: #fff;">
                <thead style="background-color: #ccccff;">
                    <tr>
                        <th class="text-center" style="width: 40px; background-color: #90EE90;">Sr.</th>
                        <th style="background-color: #ccccff;">Category Name</th>
                        <th class="text-end" style="width: 100px; background-color: #ccccff;">Qty</th>
                        <th class="text-end" style="width: 140px; background-color: #ccccff;">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($reportData) && $reportData->count() > 0)
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center" style="background-color: #90EE90;">{{ $index + 1 }}</td>
                            <td>{{ $row['category_name'] }}</td>
                            <td class="text-end">{{ number_format($row['qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['value'], 2) }}</td>
                        </tr>
                        @endforeach
                    @else
                        @for($i = 0; $i < 10; $i++)
                        <tr>
                            <td class="text-center" style="background-color: #90EE90;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endfor
                    @endif
                </tbody>
                @if(isset($reportData) && $reportData->count() > 0)
                <tfoot style="background-color: #ffff99; font-weight: bold;">
                    <tr>
                        <td colspan="2" class="text-end">Total:</td>
                        <td class="text-end">{{ number_format($totals['total_qty'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($totals['total_value'] ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @if(isset($reportData) && $reportData->count() > 0)
        <div class="card-footer">
            <small class="text-muted">Total Records: {{ $reportData->count() }}</small>
        </div>
        @endif
    </div>

    @if(request()->has('view') && (!isset($reportData) || $reportData->count() == 0))
    <div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No records found for the selected filters.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.inventory.stock.category-wise-stock-status") }}?' + params.toString();
}
function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.stock.category-wise-stock-status") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
}
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.4rem; font-size: 0.85rem; vertical-align: middle; border: 1px solid #000; }
</style>
@endpush
