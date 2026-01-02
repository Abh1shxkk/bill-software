@extends('layouts.admin')

@section('title', 'List of Schemes')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">List of Schemes</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.item.list-of-schemes') }}">
                <!-- S/A Selector -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Type :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_s" value="S" {{ request('type', 'S') == 'S' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_s">S (Scheme)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_a" value="A" {{ request('type') == 'A' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_a">A (All)</label>
                        </div>
                    </div>
                </div>

                <!-- Scheme +/- -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Scheme :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scheme_type" id="scheme_plus" value="+" {{ request('scheme_type', '+') == '+' ? 'checked' : '' }}>
                            <label class="form-check-label" for="scheme_plus">+ (With Scheme)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scheme_type" id="scheme_minus" value="-" {{ request('scheme_type') == '-' ? 'checked' : '' }}>
                            <label class="form-check-label" for="scheme_minus">- (Without Scheme)</label>
                        </div>
                    </div>
                </div>

                <!-- S/P Selector -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">S/P :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sp_type" id="sp_s" value="S" {{ request('sp_type', 'S') == 'S' ? 'checked' : '' }}>
                            <label class="form-check-label" for="sp_s">S (Sale)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sp_type" id="sp_p" value="P" {{ request('sp_type') == 'P' ? 'checked' : '' }}>
                            <label class="form-check-label" for="sp_p">P (Purchase)</label>
                        </div>
                    </div>
                </div>

                <!-- From M/L -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">From :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="from_type" id="from_m" value="M" {{ request('from_type', 'M') == 'M' ? 'checked' : '' }}>
                            <label class="form-check-label" for="from_m">M (Master)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="from_type" id="from_l" value="L" {{ request('from_type') == 'L' ? 'checked' : '' }}>
                            <label class="form-check-label" for="from_l">L (Ledger)</label>
                        </div>
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
            <strong>List of Schemes Report</strong>
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
                            <th class="text-center">Scheme Qty</th>
                            <th class="text-center">Scheme Free</th>
                            <th class="text-end">Sale Rate</th>
                            <th class="text-end">MRP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td>{{ $row['packing'] }}</td>
                            <td class="text-center">{{ $row['scheme_qty'] }}</td>
                            <td class="text-center">{{ $row['scheme_free'] }}</td>
                            <td class="text-end">{{ number_format($row['s_rate'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['mrp'], 2) }}</td>
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
    window.location.href = '{{ route("admin.reports.inventory.item.list-of-schemes") }}?' + params.toString();
}

function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.item.list-of-schemes") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
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
