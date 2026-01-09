@extends('layouts.admin')

@section('title', 'Sale Book GSTR')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">SALE BOOK GSTR</h4>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <div class="d-flex align-items-center flex-wrap gap-1">
                <span class="fw-bold small me-2">Type:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_sale" value="1" {{ ($reportType ?? '8') == '1' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary btn-sm" for="type_sale">1.Sale</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_return" value="2" {{ ($reportType ?? '') == '2' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary btn-sm" for="type_return">2.Sale Ret</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_dnote" value="3" {{ ($reportType ?? '') == '3' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary btn-sm" for="type_dnote">3.D.Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_cnote" value="4" {{ ($reportType ?? '') == '4' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary btn-sm" for="type_cnote">4.C.Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_consolidated" value="5" {{ ($reportType ?? '') == '5' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary btn-sm" for="type_consolidated">5.Consolidated</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_all_cn_dn" value="6" {{ ($reportType ?? '') == '6' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary btn-sm" for="type_all_cn_dn">6.All CN_DN</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_expiry" value="7" {{ ($reportType ?? '') == '7' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary btn-sm" for="type_expiry">7.Expiry Sale</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_voucher" value="8" {{ ($reportType ?? '8') == '8' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary btn-sm" for="type_voucher">8.Voucher Sale</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.sales-book-gstr') }}">
                <input type="hidden" name="report_type" id="hidden_report_type" value="{{ $reportType ?? '8' }}">
                
                <div class="row g-2">
                    <!-- Row 1: Date Range & Format -->
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
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Format</span>
                            <select name="report_format" class="form-select">
                                <option value="D" {{ ($reportFormat ?? 'D') == 'D' ? 'selected' : '' }}>D(etailed)</option>
                                <option value="S" {{ ($reportFormat ?? '') == 'S' ? 'selected' : '' }}>S(ummarised-Day wise)</option>
                                <option value="M" {{ ($reportFormat ?? '') == 'M' ? 'selected' : '' }}>M(onthly)</option>
                                <option value="G" {{ ($reportFormat ?? '') == 'G' ? 'selected' : '' }}>G(roup)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Series</span>
                            <select name="series" class="form-select">
                                <option value="">All</option>
                                @foreach($seriesList ?? [] as $s)
                                    <option value="{{ $s }}" {{ ($series ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
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

                    <!-- Row 2: Supplier/Customer Exp & WOST -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supp.Exp</span>
                            <select name="with_supp_exp" class="form-select">
                                <option value="Y" {{ ($withSuppExp ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($withSuppExp ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Cust.Exp</span>
                            <select name="with_cust_exp" class="form-select">
                                <option value="Y" {{ ($withCustExp ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($withCustExp ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">WOST</span>
                            <input type="text" name="wost" class="form-control text-uppercase" value="{{ $wost ?? '' }}" placeholder="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party Code</span>
                            <input type="text" name="party_code" class="form-control text-uppercase" value="{{ $partyCode ?? '' }}" placeholder="00">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">GSTN</span>
                            <select name="gstn_filter" class="form-select">
                                <option value="3" {{ ($gstnFilter ?? '3') == '3' ? 'selected' : '' }}>3.All</option>
                                <option value="1" {{ ($gstnFilter ?? '') == '1' ? 'selected' : '' }}>1.With GSTN</option>
                                <option value="2" {{ ($gstnFilter ?? '') == '2' ? 'selected' : '' }}>2.Without GSTN</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Party Name -->
                    <div class="col-md-12">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party Name</span>
                            <select name="customer_id" class="form-select" id="customerSelect">
                                <option value="">All Customers</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}" data-code="{{ $customer->code }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Sales Man, Area, Route, State -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sales Man</span>
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

                <!-- Row 5: Display Options -->
                <div class="row mt-2">
                    <div class="col-md-8">
                        <div class="d-flex flex-wrap gap-3 align-items-center border rounded p-2 bg-light">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <span class="input-group-text">S.Man Master</span>
                                <select name="sman_from_master" class="form-select">
                                    <option value="N" {{ ($smanFromMaster ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ ($smanFromMaster ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_salesman" id="showSalesman" value="1" {{ ($showSalesman ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showSalesman">Show Sales Man</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_area" id="showArea" value="1" {{ ($showArea ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showArea">Show AREA</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="deduct_add_less" id="deductAddLess" value="1" {{ ($deductAddLess ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="deductAddLess">Deduct Add Less Bill Amt</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-2" onclick="formatTwo()">
                            Format-2
                        </button>
                        <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-2" onclick="stateWiseSale()">
                            State Wise
                        </button>
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
                    <small>CGST</small>
                    <h6 class="mb-0">₹{{ number_format($totals['cgst_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2 text-center">
                    <small>SGST</small>
                    <h6 class="mb-0">₹{{ number_format($totals['sgst_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">IGST</small>
                    <h6 class="mb-0">₹{{ number_format($totals['igst_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Total Tax</small>
                    <h6 class="mb-0">₹{{ number_format($totals['total_tax'] ?? 0, 2) }}</h6>
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
                            <th style="width: 80px;">Date</th>
                            <th style="width: 70px;">Bill No</th>
                            <th>Party Name</th>
                            <th style="width: 120px;">GSTIN</th>
                            <th style="width: 60px;">State</th>
                            @if($showArea ?? false)
                            <th>Area</th>
                            @endif
                            @if($showSalesman ?? false)
                            <th>Salesman</th>
                            @endif
                            <th class="text-end" style="width: 85px;">Taxable</th>
                            <th class="text-end" style="width: 70px;">CGST</th>
                            <th class="text-end" style="width: 70px;">SGST</th>
                            <th class="text-end" style="width: 70px;">IGST</th>
                            <th class="text-end" style="width: 85px;">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $index => $sale)
                        @php
                            $taxable = $sale->items->sum('taxable_amount') ?: $sale->items->sum('amount') ?: $sale->nt_amount;
                            $cgst = $sale->items->sum('cgst_amount') ?: 0;
                            $sgst = $sale->items->sum('sgst_amount') ?: 0;
                            $igst = $sale->items->sum('igst_amount') ?: 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none fw-bold">
                                    {{ $sale->series }}{{ $sale->invoice_no }}
                                </a>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $sale->customer->code ?? '' }}</span>
                                {{ Str::limit($sale->customer->name ?? 'N/A', 20) }}
                            </td>
                            <td class="small">{{ $sale->customer->gst_number ?? '-' }}</td>
                            <td>{{ $sale->customer->state_code ?? '' }}</td>
                            @if($showArea ?? false)
                            <td>{{ $sale->customer->area_name ?? '-' }}</td>
                            @endif
                            @if($showSalesman ?? false)
                            <td>{{ $sale->salesman->name ?? '-' }}</td>
                            @endif
                            <td class="text-end">{{ number_format($taxable, 2) }}</td>
                            <td class="text-end">{{ number_format($cgst, 2) }}</td>
                            <td class="text-end">{{ number_format($sgst, 2) }}</td>
                            <td class="text-end">{{ number_format($igst, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="{{ 6 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0) }}" class="text-end">
                                Grand Total ({{ number_format($totals['count'] ?? 0) }} Bills):
                            </td>
                            <td class="text-end">{{ number_format($totals['taxable_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['cgst_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['sgst_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['igst_amount'] ?? 0, 2) }}</td>
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
// Sync report type radio buttons with hidden field
document.querySelectorAll('input[name="report_type_radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden_report_type').value = this.value;
    });
});

function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sales-book-gstr") }}?' + params.toString(), '_blank');
}

function stateWiseSale() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('group_by', 'state');
    window.location.href = '{{ route("admin.reports.sales.sales-book-gstr") }}?' + params.toString();
}

function formatTwo() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('format', '2');
    window.location.href = '{{ route("admin.reports.sales.sales-book-gstr") }}?' + params.toString();
}

function printReport() {
    window.open('{{ route("admin.reports.sales.sales-book-gstr") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
