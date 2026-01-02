@extends('layouts.admin')

@section('title', 'Reorder on Sale Basis')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">REORDER ON SALE BASIS</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="">
                <!-- Date Filters -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>{{ $company->id }} - {{ Str::limit($company->name, 15) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Days for Sale Calculation -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Days :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="days" id="days" class="form-control form-control-sm" value="{{ $days ?? 30 }}" min="1">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border w-100 fw-bold shadow-sm" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                    </div>
                    <div class="col-md-6 offset-md-4 text-end">
                        <button type="submit" class="btn btn-primary border px-4 fw-bold shadow-sm me-2">Show</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="viewReport()"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && count($reportData) > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2">
            <strong>Reorder on Sale Basis Report</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Avg Sale/Day</th>
                            <th class="text-end">Days Stock</th>
                            <th class="text-end">Reorder Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData ?? [] as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] ?? '' }}</td>
                            <td>{{ $row['company_name'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($row['current_stock'] ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($row['avg_sale_per_day'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($row['days_stock'] ?? 0, 0) }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($row['reorder_qty'] ?? 0, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.location.href = window.location.pathname + '?' + params.toString();
}

function viewReport() {
    document.getElementById('filterForm').submit();
}

function closeWindow() {
    window.location.href = '{{ route("admin.reports.inventory") ?? "#" }}';
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
