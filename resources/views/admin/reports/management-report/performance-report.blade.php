@extends('layouts.admin')

@section('title', 'Performance Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Performance Report</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.performance-report') }}">
                <!-- From & To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0"><u>T</u>o :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>

                <!-- Salesman / Customer Toggle -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 180px;">
                        <label class="fw-bold mb-0">S(alesman) \ C(ustomer) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="report_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('report_type', 'S') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Sales Man -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0">Sales Man :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="salesman_code" class="form-control form-control-sm text-uppercase" value="{{ request('salesman_code', '00') }}" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <select name="salesman_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Salesmen --</option>
                            @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ request('salesman_id') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Area -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0">Area :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="area_code" class="form-control form-control-sm text-uppercase" value="{{ request('area_code', '00') }}" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <select name="area_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Areas --</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Route -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0">Route :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="route_code" class="form-control form-control-sm text-uppercase" value="{{ request('route_code', '00') }}" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <select name="route_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Routes --</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Customer -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0">Customer :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="customer_code" class="form-control form-control-sm text-uppercase" value="{{ request('customer_code', '00') }}" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <select name="customer_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Customers --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Flag -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto" style="width: 100px;">
                        <label class="fw-bold mb-0">Flag :</label>
                    </div>
                    <div class="col-auto">
                        <select name="flag" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">-- All --</option>
                            <option value="active" {{ request('flag') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('flag') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row g-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px; margin-top: 20px;">
                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center">
            <span class="fw-bold">Performance Report - {{ request('report_type', 'S') == 'S' ? 'Salesman Wise' : 'Customer Wise' }} ({{ count($reportData) }} records)</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">S.No</th>
                            <th>{{ request('report_type', 'S') == 'S' ? 'Salesman' : 'Customer' }} Name</th>
                            <th class="text-end">Sale Amount</th>
                            <th class="text-end">Return Amount</th>
                            <th class="text-end">Net Amount</th>
                            <th class="text-end">Bills</th>
                            <th class="text-end">Avg Bill Value</th>
                            <th class="text-end">Collection</th>
                            <th class="text-end">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $totalSale = 0; $totalReturn = 0; $totalNet = 0; 
                            $totalBills = 0; $totalCollection = 0; $totalOutstanding = 0;
                        @endphp
                        @foreach($reportData as $index => $row)
                        @php 
                            $totalSale += $row['sale_amount']; 
                            $totalReturn += $row['return_amount'];
                            $totalNet += $row['net_amount'];
                            $totalBills += $row['bills'];
                            $totalCollection += $row['collection'];
                            $totalOutstanding += $row['outstanding'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-end">{{ number_format($row['sale_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['return_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['net_amount'], 2) }}</td>
                            <td class="text-end">{{ $row['bills'] }}</td>
                            <td class="text-end">{{ number_format($row['avg_bill_value'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['collection'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['outstanding'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totalSale, 2) }}</td>
                            <td class="text-end">{{ number_format($totalReturn, 2) }}</td>
                            <td class="text-end">{{ number_format($totalNet, 2) }}</td>
                            <td class="text-end">{{ $totalBills }}</td>
                            <td class="text-end">{{ $totalBills > 0 ? number_format($totalNet / $totalBills, 2) : '0.00' }}</td>
                            <td class="text-end">{{ number_format($totalCollection, 2) }}</td>
                            <td class="text-end">{{ number_format($totalOutstanding, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No data found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.performance-report") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'f') {
        e.preventDefault();
        $('input[name="from_date"]').focus();
    }
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        closeWindow();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
</style>
@endpush
