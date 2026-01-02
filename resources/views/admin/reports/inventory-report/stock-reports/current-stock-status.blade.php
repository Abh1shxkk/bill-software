@extends('layouts.admin')

@section('title', 'Current Stock Status')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px; text-decoration: underline;">STOCK STATUS</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.stock.current-stock-status') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0"><u>C</u>( ompany ) / A ( ll ) :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="filter_type" class="form-control form-control-sm text-center" value="{{ request('filter_type', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-1 text-end pe-1">
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
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Latest Position [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="latest_position" class="form-control form-control-sm text-center" value="{{ request('latest_position', 'Y') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0">Date :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="as_on_date" class="form-control form-control-sm" value="{{ request('as_on_date', date('Y-m-d')) }}">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Batch Wise Status [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="batch_wise" class="form-control form-control-sm text-center" value="{{ request('batch_wise', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0">Qty From :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="qty_from" class="form-control form-control-sm" value="{{ request('qty_from', '') }}" style="width: 70px;">
                    </div>
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="qty_to" class="form-control form-control-sm" value="{{ request('qty_to', '') }}" style="width: 70px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-5 text-end pe-1">
                        <label class="fw-bold mb-0">1. All Items / 2. With Stock / 3. W/o Stock / 4. Negative Stock Only :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="stock_filter" class="form-control form-control-sm text-center" value="{{ request('stock_filter', '2') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">With Value [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="with_value" class="form-control form-control-sm text-center" value="{{ request('with_value', 'Y') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0">Value From :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="value_from" class="form-control form-control-sm" value="{{ request('value_from', '') }}" style="width: 70px;">
                    </div>
                    <div class="col-md-1 text-end pe-1">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="value_to" class="form-control form-control-sm" value="{{ request('value_to', '') }}" style="width: 70px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-5 text-end pe-1">
                        <label class="fw-bold mb-0">Value on :- 1 Cost Rate / 2 Sale Rate / 3 Pur.Rate / 4 MRP / 5 Cost+Tax :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="value_on" class="form-control form-control-sm text-center" value="{{ request('value_on', '1') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="division" class="form-control form-control-sm text-center" value="{{ request('division', '00') }}" style="width: 50px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Status :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">DPC Item [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="dpc_item" class="form-control form-control-sm text-center" value="{{ request('dpc_item', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Item Category :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="item_category_code" class="form-control form-control-sm text-center" value="{{ request('item_category_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-md-4">
                        <select name="item_category" class="form-select form-select-sm">
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Item Location :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="item_location" class="form-select form-select-sm">
                            <option value="">All</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <div class="form-check">
                            <input type="checkbox" name="h1" class="form-check-input" id="h1Check" {{ request('h1') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="h1Check">H1</label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-check">
                            <input type="checkbox" name="food" class="form-check-input" id="foodCheck" {{ request('food') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="foodCheck">Food</label>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Tagged Items [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="tagged_items" class="form-control form-control-sm text-center" value="{{ request('tagged_items', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-md-2 text-end pe-1">
                        <label class="fw-bold mb-0">Remove Tags [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="remove_tags" class="form-control form-control-sm text-center" value="{{ request('remove_tags', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border fw-bold shadow-sm me-2" onclick="exportToExcel()"><u>E</u>xcel</button>
                        <button type="button" class="btn btn-light border fw-bold shadow-sm">Format-2</button>
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
            <strong>Current Stock Status</strong>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="printReport()"><i class="fas fa-print"></i> Print</button>
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
                            <th>Packing</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td>{{ $row['packing'] }}</td>
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
    window.location.href = '{{ route("admin.reports.inventory.stock.current-stock-status") }}?' + params.toString();
}
function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.stock.current-stock-status") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
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
