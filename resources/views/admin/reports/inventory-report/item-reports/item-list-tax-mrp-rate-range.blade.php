@extends('layouts.admin')

@section('title', 'Tax / Mrp / Rate Range')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">Tax / Mrp / Rate Range</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.inventory.item.tax-mrp-rate-range') }}">
                <!-- Rate Type -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-8">
                        <label class="fw-bold mb-0">1. Sale Rate / 2. MRP / 3. Pur.Rate / 4. Cost / 5. TAX :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="rate_type" id="rate_type" class="form-control form-control-sm text-center" value="{{ request('rate_type', 1) }}" min="1" max="5" style="width: 50px;">
                    </div>
                </div>

                <!-- Range Type -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-8">
                        <label class="fw-bold mb-0">1. ( >= ) / 2. ( <= ) / 3. ( = ) / 4 . Range :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="range_type" id="range_type" class="form-control form-control-sm text-center" value="{{ request('range_type', 4) }}" min="1" max="4" style="width: 50px;">
                    </div>
                </div>

                <!-- Enter Value -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Enter Value :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="enter_value" id="enter_value" class="form-control form-control-sm" value="{{ request('enter_value', '') }}" step="0.01">
                    </div>
                </div>

                <!-- With Stock -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-md-3">
                        <label class="fw-bold mb-0">With Stock [ Y / N ] :</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="with_stock" id="with_stock" class="form-control form-control-sm text-center text-uppercase" value="{{ request('with_stock', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border w-100 fw-bold shadow-sm" onclick="exportToExcel()"><u>E</u>xcel</button>
                    </div>
                    <div class="col-md-6 offset-md-4 text-end">
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
            <strong>Item List - Tax / MRP / Rate Range</strong>
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
                            <th>Packing</th>
                            <th class="text-end">Sale Rate</th>
                            <th class="text-end">MRP</th>
                            <th class="text-end">Pur. Rate</th>
                            <th class="text-end">Cost</th>
                            <th class="text-end">VAT %</th>
                            @if(request('with_stock') == 'Y')
                            <th class="text-end">Stock</th>
                            @endif
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
                            <td class="text-end">{{ number_format($row['cost'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['vat_percent'], 2) }}%</td>
                            @if(request('with_stock') == 'Y')
                            <td class="text-end">{{ number_format($row['current_stock'] ?? 0, 0) }}</td>
                            @endif
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
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.inventory.item.tax-mrp-rate-range") }}?' + params.toString();
}

function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.item.tax-mrp-rate-range") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
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
