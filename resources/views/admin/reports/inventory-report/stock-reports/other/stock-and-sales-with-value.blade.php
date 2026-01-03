@extends('layouts.admin')

@section('title', 'Stock and Sales with Value')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">SALES STATEMENT</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.stock.stock-and-sales-with-value') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-1 text-end pe-2">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Selective Comp :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="company_code" class="form-control form-control-sm text-center" value="{{ request('company_code', '00') }}" style="width: 50px;">
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
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Value On :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="value_on" class="form-select form-select-sm">
                            <option value="P" {{ request('value_on') == 'P' ? 'selected' : '' }}>P (Pur. Rate)</option>
                            <option value="C" {{ request('value_on', 'C') == 'C' ? 'selected' : '' }}>C (Cost Rate)</option>
                            <option value="M" {{ request('value_on') == 'M' ? 'selected' : '' }}>M (MRP)</option>
                        </select>
                    </div>
                    <div class="col-md-1 text-end pe-2">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="division_code" class="form-control form-control-sm text-center" value="{{ request('division_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-md-3">
                        <select name="division_id" class="form-select form-select-sm">
                            <option value="">All</option>
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
            <strong>Sales Statement</strong>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="printReport()"><i class="fas fa-print"></i> Print</button>
                <button type="button" class="btn btn-sm btn-success ms-1" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end">Stock Qty</th>
                            <th class="text-end">Stock Value</th>
                            <th class="text-end">Sale Qty</th>
                            <th class="text-end">Sale Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] ?? $row['name'] ?? '' }}</td>
                            <td>{{ $row['company_name'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($row['stock_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['stock_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_value'] ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totals['total_stock_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_stock_value'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_sale_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total_sale_value'] ?? 0, 2) }}</td>
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
    <div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No records found.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.inventory.stock.stock-and-sales-with-value") }}?' + params.toString();
}
function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.stock.stock-and-sales-with-value") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
}
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.4rem; font-size: 0.8rem; vertical-align: middle; }
</style>
@endpush
