@extends('layouts.admin')

@section('title', 'Receipt from Customer - Month Wise')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold">Receipt from Customer - Month Wise</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.receipt-payment.receipt-customer-month-wise') }}">
                <div class="row g-2">
                    <!-- Row 1: Year Range & Mode -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="number" name="from_year" class="form-control" value="{{ request('from_year', date('Y')) }}" min="2000" max="2099">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="number" name="to_year" class="form-control" value="{{ request('to_year', date('Y')) }}" min="2000" max="2099">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Mode:</span>
                            <select name="payment_mode" class="form-select">
                                <option value="5" {{ request('payment_mode', '5') == '5' ? 'selected' : '' }}>All</option>
                                <option value="1" {{ request('payment_mode') == '1' ? 'selected' : '' }}>Cash</option>
                                <option value="2" {{ request('payment_mode') == '2' ? 'selected' : '' }}>Cheque</option>
                                <option value="3" {{ request('payment_mode') == '3' ? 'selected' : '' }}>RTGS</option>
                                <option value="4" {{ request('payment_mode') == '4' ? 'selected' : '' }}>NEFT</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Flag:</span>
                            <input type="text" name="flag" class="form-control" value="{{ request('flag') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Tagged Customers:</span>
                            <select name="tagged_customers" class="form-select">
                                <option value="N" {{ request('tagged_customers', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ request('tagged_customers') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Customer & Salesman -->
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Customer:</span>
                            <select name="customer_id" class="form-select">
                                <option value="">All Customers</option>
                                @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sman:</span>
                            <select name="salesman_id" class="form-select">
                                <option value="">All Salesmen</option>
                                @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ request('salesman_id') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Area, Route, Collection Boy -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Area:</span>
                            <select name="area_id" class="form-select">
                                <option value="">All Areas</option>
                                @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Route:</span>
                            <select name="route_id" class="form-select">
                                <option value="">All Routes</option>
                                @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Coll. Boy:</span>
                            <select name="coll_boy_id" class="form-select">
                                <option value="">All</option>
                                @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ request('coll_boy_id') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Actions -->
                    <div class="col-md-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="bi bi-file-excel me-1"></i>E<u>x</u>cel
                            </button>
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="printReport()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>Customer Name</th>
                            <th class="text-end" style="width: 90px;">Apr</th>
                            <th class="text-end" style="width: 90px;">May</th>
                            <th class="text-end" style="width: 90px;">Jun</th>
                            <th class="text-end" style="width: 90px;">Jul</th>
                            <th class="text-end" style="width: 90px;">Aug</th>
                            <th class="text-end" style="width: 90px;">Sep</th>
                            <th class="text-end" style="width: 90px;">Oct</th>
                            <th class="text-end" style="width: 90px;">Nov</th>
                            <th class="text-end" style="width: 90px;">Dec</th>
                            <th class="text-end" style="width: 90px;">Jan</th>
                            <th class="text-end" style="width: 90px;">Feb</th>
                            <th class="text-end" style="width: 90px;">Mar</th>
                            <th class="text-end" style="width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; $monthTotals = array_fill(0, 12, 0); @endphp
                        @forelse($reportData ?? [] as $index => $row)
                        @php 
                            $rowTotal = array_sum($row['months'] ?? []);
                            $grandTotal += $rowTotal;
                            foreach($row['months'] ?? [] as $mIdx => $mAmt) {
                                $monthTotals[$mIdx] += $mAmt;
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['customer_name'] }}</td>
                            @foreach($row['months'] ?? array_fill(0, 12, 0) as $amount)
                            <td class="text-end">{{ number_format($amount, 2) }}</td>
                            @endforeach
                            <td class="text-end fw-bold">{{ number_format($rowTotal, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($reportData) && count($reportData) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Grand Total:</td>
                            @foreach($monthTotals as $mTotal)
                            <td class="text-end">{{ number_format($mTotal, 2) }}</td>
                            @endforeach
                            <td class="text-end">{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printReport() { 
    window.open('{{ route("admin.reports.receipt-payment.receipt-customer-month-wise") }}?print=1&' + $('#filterForm').serialize(), '_blank'); 
}

function exportExcel() {
    window.location.href = '{{ route("admin.reports.receipt-payment.receipt-customer-month-wise") }}?excel=1&' + $('#filterForm').serialize();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') window.history.back();
    if (e.key === 'Enter') {
        e.preventDefault();
        document.querySelector('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'x') {
        e.preventDefault();
        exportExcel();
    }
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.25rem 0.4rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.75rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
