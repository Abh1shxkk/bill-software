@extends('layouts.admin')

@section('title', 'Sale Book With TCS')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">SALE BOOK WITH TCS</h4>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold small">Format:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="report_format_radio" id="format_detailed" value="D" {{ ($reportFormat ?? 'D') == 'D' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="format_detailed">Detailed</label>
                            
                            <input type="radio" class="btn-check" name="report_format_radio" id="format_summarised" value="S" {{ ($reportFormat ?? '') == 'S' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="format_summarised">Summarised</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold small">TCS:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="tcs_filter_radio" id="tcs_with" value="T" {{ ($tcsFilter ?? 'A') == 'T' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="tcs_with">With TCS</label>
                            
                            <input type="radio" class="btn-check" name="tcs_filter_radio" id="tcs_without" value="W" {{ ($tcsFilter ?? '') == 'W' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="tcs_without">Without TCS</label>
                            
                            <input type="radio" class="btn-check" name="tcs_filter_radio" id="tcs_all" value="A" {{ ($tcsFilter ?? 'A') == 'A' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="tcs_all">All</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold small">Sale Type:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="sale_type_radio" id="sale_only" value="S" {{ ($saleType ?? 'B') == 'S' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="sale_only">Sale</label>
                            
                            <input type="radio" class="btn-check" name="sale_type_radio" id="return_only" value="R" {{ ($saleType ?? '') == 'R' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="return_only">Return</label>
                            
                            <input type="radio" class="btn-check" name="sale_type_radio" id="sale_both" value="B" {{ ($saleType ?? 'B') == 'B' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="sale_both">Both</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.sales-book-tcs') }}">
                <input type="hidden" name="report_format" id="hidden_report_format" value="{{ $reportFormat ?? 'D' }}">
                <input type="hidden" name="tcs_filter" id="hidden_tcs_filter" value="{{ $tcsFilter ?? 'A' }}">
                <input type="hidden" name="sale_type" id="hidden_sale_type" value="{{ $saleType ?? 'B' }}">
                
                <div class="row g-2">
                    <!-- Row 1: Date Range & Source -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <select name="from_source" class="form-select">
                                <option value="T" {{ ($fromSource ?? 'T') == 'T' ? 'selected' : '' }}>Transaction</option>
                                <option value="M" {{ ($fromSource ?? '') == 'M' ? 'selected' : '' }}>Master</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L/C/B</span>
                            <select name="local_central" class="form-select">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B(oth)</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L(ocal)</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C(entral)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Customer</span>
                            <select name="customer_id" class="form-select">
                                <option value="">All</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Location Filters -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Salesman</span>
                            <select name="salesman_id" class="form-select">
                                <option value="">All</option>
                                @foreach($salesmen ?? [] as $salesman)
                                    <option value="{{ $salesman->id }}" {{ ($salesmanId ?? '') == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->code ?? '' }} - {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Area</span>
                            <select name="area_id" class="form-select">
                                <option value="">All</option>
                                @foreach($areas ?? [] as $area)
                                    <option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Route</span>
                            <select name="route_id" class="form-select">
                                <option value="">All</option>
                                @foreach($routes ?? [] as $route)
                                    <option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">State</span>
                            <select name="state_id" class="form-select">
                                <option value="">All</option>
                                @foreach($states ?? [] as $state)
                                    <option value="{{ $state->id }}" {{ ($stateId ?? '') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-2" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <u>V</u>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">
                            <u>P</u>rint
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-light border px-4 fw-bold shadow-sm">
                            <u>C</u>lose
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table - Only show when view is clicked -->
    @if(request()->has('view') && isset($sales) && $sales->count() > 0)
    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Bills</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Taxable</small>
                    <h6 class="mb-0">₹{{ number_format($totals['taxable_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2 text-center">
                    <small>Tax Amt</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tax_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">TCS Amt</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tcs_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Net Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 85px;">Date</th>
                            <th style="width: 80px;">Trn No</th>
                            <th style="width: 60px;">Code</th>
                            <th>Party Name</th>
                            <th style="width: 110px;">PAN No</th>
                            <th class="text-end" style="width: 95px;">Taxable</th>
                            <th class="text-end" style="width: 80px;">Tax Amt</th>
                            <th class="text-center" style="width: 55px;">TCS%</th>
                            <th class="text-end" style="width: 80px;">TCS Amt</th>
                            <th class="text-end" style="width: 95px;">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $index => $sale)
                        @php
                            $taxableAmount = $sale->nt_amount - ($sale->dis_amount ?? 0);
                            $tcsPercent = ($fromSource ?? 'T') == 'T' 
                                ? ($sale->tcs_amount > 0 ? round(($sale->tcs_amount / $taxableAmount) * 100, 2) : 0)
                                : ($sale->calculated_tcs_percent ?? 0);
                            $tcsAmount = ($fromSource ?? 'T') == 'T' 
                                ? ($sale->tcs_amount ?? 0)
                                : ($sale->calculated_tcs_amount ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none fw-bold">
                                    {{ $sale->series }}{{ $sale->invoice_no }}
                                </a>
                            </td>
                            <td>{{ $sale->customer->code ?? '' }}</td>
                            <td>{{ Str::limit($sale->customer->name ?? 'N/A', 22) }}</td>
                            <td class="small">{{ $sale->customer->pan_number ?? '-' }}</td>
                            <td class="text-end">{{ number_format($taxableAmount, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($tcsPercent, 2) }}%</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($tcsAmount, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Grand Total ({{ number_format($totals['count'] ?? 0) }} Bills):</td>
                            <td class="text-end">{{ number_format($totals['taxable_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['tcs_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Sync radio buttons with hidden fields
document.querySelectorAll('input[name="report_format_radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden_report_format').value = this.value;
    });
});
document.querySelectorAll('input[name="tcs_filter_radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden_tcs_filter').value = this.value;
    });
});
document.querySelectorAll('input[name="sale_type_radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden_sale_type').value = this.value;
    });
});

function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sales-book-tcs") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.sales-book-tcs") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        printReport();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
    if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();
        exportToExcel();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.75rem; border-radius: 0; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
