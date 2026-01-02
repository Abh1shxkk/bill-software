@extends('layouts.admin')

@section('title', 'Minimum / Maximum Level Items')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">Minimum / Maximum Level Items</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.item.min-max-level') }}">
                <!-- Filter Type -->
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-md-3">
                        <label class="fw-bold mb-0">C (ompany) / A (ll) :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="filter_type" id="filter_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('filter_type', 'A') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">Select</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="company_name_display" class="form-control form-control-sm" readonly 
                            value="{{ $companies->where('id', request('company_id'))->first()->name ?? '' }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Status Filter :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="status_filter" id="status_filter" class="form-select form-select-sm" style="width: 80px;">
                            <option value="">All</option>
                            <option value="min" {{ request('status_filter') == 'min' ? 'selected' : '' }}>Min</option>
                            <option value="max" {{ request('status_filter') == 'max' ? 'selected' : '' }}>Max</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-6 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && $reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between">
            <strong>Minimum / Maximum Level Items Report</strong>
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
                            <th class="text-end">Min Level</th>
                            <th class="text-end">Max Level</th>
                            <th class="text-end">Current Stock</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td>{{ $row['packing'] }}</td>
                            <td class="text-end">{{ number_format($row['min_level'], 0) }}</td>
                            <td class="text-end">{{ number_format($row['max_level'], 0) }}</td>
                            <td class="text-end {{ $row['current_stock'] < $row['min_level'] ? 'text-danger fw-bold' : ($row['current_stock'] > $row['max_level'] ? 'text-warning fw-bold' : '') }}">
                                {{ number_format($row['current_stock'], 0) }}
                            </td>
                            <td class="text-center">
                                @if($row['current_stock'] < $row['min_level'])
                                    <span class="badge bg-danger">Below Min</span>
                                @elseif($row['current_stock'] > $row['max_level'] && $row['max_level'] > 0)
                                    <span class="badge bg-warning">Above Max</span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
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
    <div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No records found.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.getElementById('company_id').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    @if(isset($companies))
    var companies = @json($companies);
    var company = companies.find(c => c.id == this.value);
    document.getElementById('company_name_display').value = company ? company.name : '';
    @endif
});

function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.inventory.item.min-max-level") }}?' + params.toString();
}

function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.item.min-max-level") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
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
