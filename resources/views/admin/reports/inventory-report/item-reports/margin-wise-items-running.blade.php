@extends('layouts.admin')

@section('title', 'Item Margin Report - Running Items')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">Item Margin Report Running Items</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.item.margin-wise-running') }}">
                <!-- Date Range -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from', date('Y-m-d')) }}">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to', date('Y-m-d')) }}">
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tagged Items -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Tagged Items :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tagged_items" id="tagged_y" value="Y" {{ request('tagged_items', 'N') == 'Y' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tagged_y">Y</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tagged_items" id="tagged_n" value="N" {{ request('tagged_items', 'N') == 'N' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tagged_n">N</label>
                        </div>
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">Remove Tags :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="remove_tags" id="remove_y" value="Y" {{ request('remove_tags', 'N') == 'Y' ? 'checked' : '' }}>
                            <label class="form-check-label" for="remove_y">Y</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="remove_tags" id="remove_n" value="N" {{ request('remove_tags', 'N') == 'N' ? 'checked' : '' }}>
                            <label class="form-check-label" for="remove_n">N</label>
                        </div>
                    </div>
                </div>

                <!-- Margin Range -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Margin From % :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="margin_from" class="form-control form-control-sm text-center" value="{{ request('margin_from', '0.00') }}" step="0.01">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">To % :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="margin_to" class="form-control form-control-sm text-center" value="{{ request('margin_to', '100.00') }}" step="0.01">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border w-100 fw-bold shadow-sm" onclick="exportToExcel()"><u>E</u>xcel</button>
                    </div>
                    <div class="col-md-6 offset-md-4 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-primary border px-4 fw-bold shadow-sm me-2">Show</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && $reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between">
            <strong>Margin-Wise Items - Running ({{ request('margin_from', 0) }}% - {{ request('margin_to', 100) }}%)</strong>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="printReport()"><i class="fas fa-print"></i> Print</button>
                <button type="button" class="btn btn-sm btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>
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
                            <th class="text-end">Pur. Rate</th>
                            <th class="text-end">Sale Rate</th>
                            <th class="text-end">MRP</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Margin %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td>{{ $row['packing'] }}</td>
                            <td class="text-end">{{ number_format($row['pur_rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['s_rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['mrp'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['current_stock'], 2) }}</td>
                            <td class="text-end fw-bold {{ $row['margin'] < 10 ? 'text-danger' : ($row['margin'] > 20 ? 'text-success' : '') }}">
                                {{ number_format($row['margin'], 2) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <small class="text-muted">Total Records: {{ $reportData->count() }}</small>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No records found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.inventory.item.margin-wise-running") }}?' + params.toString();
}

function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.item.margin-wise-running") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
}

function closeWindow() {
    window.location.href = '{{ route("admin.reports.inventory") }}';
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
