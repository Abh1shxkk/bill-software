@extends('layouts.admin')

@section('title', 'Rate List')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">Rate List</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.item.rate-list') }}">
                <!-- Tagged Companies -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Tagged Companies :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tagged_companies" id="tagged_y" value="Y" {{ request('tagged_companies', 'N') == 'Y' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tagged_y">Y</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tagged_companies" id="tagged_n" value="N" {{ request('tagged_companies', 'N') == 'N' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tagged_n">N</label>
                        </div>
                    </div>
                </div>

                <!-- Category -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Category :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="category_id" id="category_id" class="form-select form-select-sm">
                            <option value="">All</option>
                        </select>
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

                <!-- Division -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="division_id" id="division_id" class="form-select form-select-sm">
                            <option value="">All</option>
                        </select>
                    </div>
                </div>

                <!-- Rate Checkboxes -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Show Rates :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="show_mrp" id="show_mrp" value="1" {{ request('show_mrp', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_mrp">MRP</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="show_sale_rate" id="show_sale_rate" value="1" {{ request('show_sale_rate', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_sale_rate">Sale Rate</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="show_pur_rate" id="show_pur_rate" value="1" {{ request('show_pur_rate') ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_pur_rate">Pur. Rate</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="show_cost" id="show_cost" value="1" {{ request('show_cost') ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_cost">Cost</label>
                        </div>
                    </div>
                </div>

                <!-- Additional Columns -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Additional Columns :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="show_packing" id="show_packing" value="1" {{ request('show_packing', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_packing">Packing</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="show_stock" id="show_stock" value="1" {{ request('show_stock') ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_stock">Stock</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="show_vat" id="show_vat" value="1" {{ request('show_vat') ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_vat">VAT %</label>
                        </div>
                    </div>
                </div>

                <!-- Order By -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Order By :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="order_by" id="order_by" class="form-select form-select-sm">
                            <option value="name" {{ request('order_by', 'name') == 'name' ? 'selected' : '' }}>Item Name</option>
                            <option value="company" {{ request('order_by') == 'company' ? 'selected' : '' }}>Company</option>
                            <option value="mrp" {{ request('order_by') == 'mrp' ? 'selected' : '' }}>MRP</option>
                            <option value="s_rate" {{ request('order_by') == 's_rate' ? 'selected' : '' }}>Sale Rate</option>
                        </select>
                    </div>
                </div>

                <!-- Group By -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Group By :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="group_by" id="group_by" class="form-select form-select-sm">
                            <option value="">None</option>
                            <option value="company" {{ request('group_by') == 'company' ? 'selected' : '' }}>Company</option>
                            <option value="category" {{ request('group_by') == 'category' ? 'selected' : '' }}>Category</option>
                        </select>
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
            <strong>Rate List Report</strong>
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
                            <th class="text-end">Sale Rate</th>
                            <th class="text-end">MRP</th>
                            <th class="text-end">Pur. Rate</th>
                            <th class="text-end">VAT %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td>{{ $row['packing'] }}</td>
                            <td class="text-end">{{ number_format($row['s_rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['mrp'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['pur_rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['vat_percent'], 2) }}%</td>
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
    window.location.href = '{{ route("admin.reports.inventory.item.rate-list") }}?' + params.toString();
}

function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.item.rate-list") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
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
