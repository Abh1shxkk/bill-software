@extends('layouts.admin')

@section('title', 'Discount On Sale - Bill Wise')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">DISCOUNT ON SALE - BILL WISE</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.sale-bill-wise-discount') }}">
                <!-- Date Range -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">From :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Discount Option -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">1. With Dis. / 2. W/o Dis / 3. All :-</label>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="discount_option" id="discount_option" class="form-control form-control-sm text-center" value="{{ $discountOption ?? '1' }}" maxlength="1">
                    </div>
                </div>

                <!-- Sales Man -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Sales Man:</label>
                    </div>
                    <div class="col-md-4">
                        <select name="salesman_id" id="salesman_id" class="form-select form-select-sm">
                            <option value="">-- All Salesmen --</option>
                            @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ ($salesmanId ?? '') == $salesman->id ? 'selected' : '' }}>
                                    {{ $salesman->code }} - {{ $salesman->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Area -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Area:</label>
                    </div>
                    <div class="col-md-4">
                        <select name="area_id" id="area_id" class="form-select form-select-sm">
                            <option value="">-- All Areas --</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Route -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Route:</label>
                    </div>
                    <div class="col-md-4">
                        <select name="route_id" id="route_id" class="form-select form-select-sm">
                            <option value="">-- All Routes --</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>
                                    {{ $route->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- State -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">State:</label>
                    </div>
                    <div class="col-md-4">
                        <select name="state_id" id="state_id" class="form-select form-select-sm">
                            <option value="">-- All States --</option>
                            @foreach($states ?? [] as $state)
                                <option value="{{ $state->id }}" {{ ($stateId ?? '') == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Party -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Party:</label>
                    </div>
                    <div class="col-md-4">
                        <select name="customer_id" id="customer_id" class="form-select form-select-sm">
                            <option value="">-- All Parties --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->code }} - {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Series -->
                <div class="row g-0 mb-3 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Series:</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="series" id="series" class="form-control form-control-sm" value="{{ $series ?? '' }}" placeholder="All">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border w-100 fw-bold shadow-sm" onclick="exportToExcel()">
                            <span class="text-decoration-underline">E</span>xcel
                        </button>
                    </div>
                    <div class="col-md-6 offset-md-4 text-end">
                        <button type="submit" class="btn btn-primary border px-4 fw-bold shadow-sm me-2">
                            Show
                        </button>
                        <button type="submit" form="filterForm" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <span class="text-decoration-underline">V</span>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">
                            Close
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Data Table -->
    @if(isset($sales) && $sales->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2">
            <strong>Discount On Sale - Bill Wise ({{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }})</strong>
            <span class="float-end">
                @if($discountOption == '1') With Discount @elseif($discountOption == '2') Without Discount @else All Bills @endif
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th class="text-center" style="width: 90px;">Date</th>
                            <th class="text-center" style="width: 90px;">Bill No</th>
                            <th style="width: 70px;">Code</th>
                            <th>Party Name</th>
                            <th>Area</th>
                            <th>Salesman</th>
                            <th class="text-end" style="width: 100px;">Gross Amt</th>
                            <th class="text-end" style="width: 90px;">Discount</th>
                            <th class="text-center" style="width: 60px;">Dis%</th>
                            <th class="text-end" style="width: 80px;">Scheme</th>
                            <th class="text-end" style="width: 80px;">Tax</th>
                            <th class="text-end" style="width: 100px;">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $index => $sale)
                        @php
                            $disPercent = $sale->nt_amount > 0 ? ($sale->dis_amount / $sale->nt_amount) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td class="text-center">{{ ($sale->series ?? '') . $sale->invoice_no }}</td>
                            <td>{{ $sale->customer->code ?? '' }}</td>
                            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td>{{ $sale->customer->area_name ?? '' }}</td>
                            <td>{{ $sale->salesman->name ?? '' }}</td>
                            <td class="text-end">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end {{ $sale->dis_amount > 0 ? 'text-danger fw-bold' : '' }}">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($disPercent, 1) }}%</td>
                            <td class="text-end">{{ number_format($sale->scm_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-warning fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">TOTAL:</td>
                            <td class="text-end">{{ number_format($totals['gross_amount'] ?? 0, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($totals['dis_percent'] ?? 0, 1) }}%</td>
                            <td class="text-end">{{ number_format($totals['scm_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <small class="text-muted">Total Bills: {{ $totals['count'] ?? 0 }} | Total Discount: ₹{{ number_format($totals['dis_amount'] ?? 0, 2) }}</small>
        </div>
    </div>
    @elseif(request()->has('date_from'))
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle"></i> No records found for the selected filters.
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.sales.other.sale-bill-wise-discount") }}?' + params.toString();
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.other.sale-bill-wise-discount") }}?' + params.toString(), 'SaleBillWiseDiscount', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}

function closeWindow() {
    window.location.href = '{{ route("admin.reports.sales") }}';
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'e' || e.key === 'E') {
        if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
            exportToExcel();
        }
    }
    if (e.key === 'v' || e.key === 'V') {
        if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
            viewReport();
        }
    }
    if (e.key === 'Escape') {
        closeWindow();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm {
    border: 1px solid #aaa;
    border-radius: 0;
}
.card {
    border-radius: 0;
    border: 1px solid #ccc;
}
.btn {
    border-radius: 0;
}
.table th, .table td {
    padding: 0.35rem 0.4rem;
    font-size: 0.8rem;
    vertical-align: middle;
}
</style>
@endpush
