@extends('layouts.admin')

@section('title', 'Category Wise Stock Status')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">Category Wise Stock Status</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.stock.category-wise-stock-status') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Date :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">With Value :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="with_value" class="form-select form-select-sm">
                            <option value="Y" {{ request('with_value', 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                            <option value="N" {{ request('with_value') == 'N' ? 'selected' : '' }}>N</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Value On :</label>
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
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-6 offset-md-6 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="window.history.back()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && $reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between">
            <strong>Category Wise Stock Status</strong>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="printReport()"><i class="fas fa-print"></i> Print</button>
                <button type="button" class="btn btn-sm btn-success ms-1" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>
            </div>
        </div>
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
                    @foreach($reportData as $index => $row)
                    <tr>
                        <td class="text-center" style="background-color: #90EE90;">{{ $index + 1 }}</td>
                        <td>{{ $row['category_name'] }}</td>
                        <td class="text-end">{{ number_format($row['qty'], 2) }}</td>
                        <td class="text-end">{{ number_format($row['value'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background-color: #ffff99; font-weight: bold;">
                    <tr>
                        <td colspan="2" class="text-end">Total:</td>
                        <td class="text-end">{{ number_format($totals['total_qty'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($totals['total_value'] ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <small class="text-muted">Total Records: {{ $reportData->count() }}</small>
        </div>
    </div>
    @elseif(request()->has('view'))
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
