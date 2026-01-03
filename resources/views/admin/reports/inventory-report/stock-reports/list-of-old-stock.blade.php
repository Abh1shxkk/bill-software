@extends('layouts.admin')

@section('title', 'List of Old Stock')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px; text-decoration: underline;">List of Old Stock</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.stock.list-of-old-stock') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Selective Company / All :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="filter_type" class="form-select form-select-sm" style="background-color: #0066cc; color: #fff;">
                            <option value="selective" {{ request('filter_type', 'selective') == 'selective' ? 'selected' : '' }}>Selective</option>
                            <option value="all" {{ request('filter_type') == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="company_code" class="form-control form-control-sm" value="{{ request('company_code', '') }}" style="width: 50px;">
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
                    <div class="col-md-2">
                        <input type="text" name="division" class="form-control form-control-sm" value="{{ request('division', '00') }}" style="width: 80px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Stock Purchased Before :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="before_date" class="form-control form-control-sm" value="{{ request('before_date', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">Value on :- 1 Cost Rate / 2 Sale Rate / 3 Pur.Rate / 4 MRP :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="value_on" class="form-control form-control-sm text-center" value="{{ request('value_on', '1') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-3 text-end pe-2">
                        <label class="fw-bold mb-0">D(etailed) / S(ummarised) :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="report_type" class="form-control form-control-sm text-center" value="{{ request('report_type', 'D') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border fw-bold shadow-sm" onclick="exportToExcel()"><u>E</u>xcel</button>
                    </div>
                    <div class="col-md-6 offset-md-4 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="window.history.back()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && $reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between">
            <strong>List of Old Stock</strong>
            <button type="button" class="btn btn-sm btn-light" onclick="printReport()"><i class="fas fa-print"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th>Batch</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td>{{ $row['batch'] }}</td>
                            <td class="text-end">{{ number_format($row['qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['value'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totals['total_qty'] ?? 0, 2) }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['total_value'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
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
    window.location.href = '{{ route("admin.reports.inventory.stock.list-of-old-stock") }}?' + params.toString();
}
function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.stock.list-of-old-stock") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
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
