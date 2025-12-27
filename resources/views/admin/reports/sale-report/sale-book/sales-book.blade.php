@extends('layouts.admin')

@section('title', 'Sale Book')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #ffcccc 0%, #ffe6e6 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">SALE BOOK</h4>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="fw-bold small">Report Type:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="report_type" id="type_sale" value="1" {{ ($reportType ?? '1') == '1' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_sale">1. Sale</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_return" value="2" {{ ($reportType ?? '') == '2' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_return">2. Sale Return</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_debit" value="3" {{ ($reportType ?? '') == '3' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_debit">3. Debit Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_credit" value="4" {{ ($reportType ?? '') == '4' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_credit">4. Credit Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_consolidated" value="5" {{ ($reportType ?? '') == '5' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_consolidated">5. Consolidated</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_all_cn_dn" value="6" {{ ($reportType ?? '') == '6' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_all_cn_dn">6. All CN_DN</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <input type="hidden" name="report_type" id="hidden_report_type" value="{{ $reportType ?? '1' }}">
                
                <div class="row g-2">
                    <!-- Row 1: Date Range & Basic Options -->
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">From</span>
                                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">To</span>
                                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">VAT ROff [DN/CN]</span>
                            <select name="vat_roff" class="form-select">
                                <option value="Y" {{ ($vatRoff ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($vatRoff ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">T(ax)/R(etail)</span>
                            <select name="tax_retail" class="form-select">
                                <option value="">All</option>
                                <option value="T" {{ ($taxRetail ?? '') == 'T' ? 'selected' : '' }}>Tax</option>
                                <option value="R" {{ ($taxRetail ?? '') == 'R' ? 'selected' : '' }}>Retail</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Report Format & Options -->
                    <div class="col-md-4">
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
                            <span class="input-group-text">Cancelled</span>
                            <select name="cancelled" class="form-select">
                                <option value="N" {{ ($cancelled ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($cancelled ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Br.Exp</span>
                            <select name="with_br_exp" class="form-select">
                                <option value="N" {{ ($withBrExp ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($withBrExp ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="A" {{ ($withBrExp ?? '') == 'A' ? 'selected' : '' }}>A(ll)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Day Total</span>
                            <select name="day_wise_total" class="form-select">
                                <option value="N" {{ ($dayWiseTotal ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($dayWiseTotal ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
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

                    <!-- Row 3: User & Party Filters -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">User</span>
                            <select name="user_id" class="form-select">
                                <option value="">All</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->user_id }}" {{ ($userId ?? '') == $user->user_id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">F/L User</span>
                            <select name="first_last_user" class="form-select">
                                <option value="F" {{ ($firstLastUser ?? 'F') == 'F' ? 'selected' : '' }}>F</option>
                                <option value="L" {{ ($firstLastUser ?? '') == 'L' ? 'selected' : '' }}>L</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party Code</span>
                            <input type="text" name="party_code" class="form-control" value="{{ $partyCode ?? '' }}" placeholder="00">
                        </div>
                    </div>
                    <div class="col-md-5">
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

                    <!-- Row 4: Location & Type Filters -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L/C/B/E</span>
                            <select name="local_central" class="form-select">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B(oth)</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L(ocal)</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C(entral)</option>
                                <option value="E" {{ ($localCentral ?? '') == 'E' ? 'selected' : '' }}>E(xport)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sale Type</span>
                            <select name="business_type" class="form-select">
                                <option value="">All</option>
                                <option value="W" {{ ($businessType ?? '') == 'W' ? 'selected' : '' }}>W(holesale)</option>
                                <option value="R" {{ ($businessType ?? '') == 'R' ? 'selected' : '' }}>R(etail)</option>
                                <option value="I" {{ ($businessType ?? '') == 'I' ? 'selected' : '' }}>I(nstitution)</option>
                                <option value="D" {{ ($businessType ?? '') == 'D' ? 'selected' : '' }}>D(ept. Store)</option>
                                <option value="O" {{ ($businessType ?? '') == 'O' ? 'selected' : '' }}>O(thers)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">GSTN</span>
                            <select name="gstn_filter" class="form-select">
                                <option value="3" {{ ($gstnFilter ?? '3') == '3' ? 'selected' : '' }}>All</option>
                                <option value="1" {{ ($gstnFilter ?? '') == '1' ? 'selected' : '' }}>With</option>
                                <option value="2" {{ ($gstnFilter ?? '') == '2' ? 'selected' : '' }}>Without</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Credit Card</span>
                            <select name="credit_card" class="form-select">
                                <option value="Y" {{ ($creditCard ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($creditCard ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">S.Man Master</span>
                            <select name="sman_from_master" class="form-select">
                                <option value="N" {{ ($smanFromMaster ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($smanFromMaster ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 5: Sales Man, Area, Route, State -->
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

                <!-- Row 6: Display Options (Checkboxes) -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center border rounded p-2 bg-light">
                            <span class="fw-bold small">Display Options:</span>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_gst_details" id="showGstDetails" value="1" {{ ($showGstDetails ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showGstDetails">GST Details</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_gr_details" id="showGrDetails" value="1" {{ ($showGrDetails ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showGrDetails">GR Details</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_cash_credit" id="showCashCredit" value="1" {{ ($showCashCredit ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showCashCredit">Cash/Credit Card</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_salesman" id="showSalesman" value="1" {{ ($showSalesman ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showSalesman">Show Sales Man</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="order_by_customer" id="orderByCustomer" value="1" {{ ($orderByCustomer ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="orderByCustomer">Order by Customer</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="deduct_add_less" id="deductAddLess" value="1" {{ ($deductAddLess ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="deductAddLess">Deduct Add Less Bill Amt</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_area" id="showArea" value="1" {{ ($showArea ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showArea">Show AREA</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="with_address" id="withAddress" value="1" {{ ($withAddress ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="withAddress">With Address</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                                <i class="bi bi-file-excel me-1"></i>Excel
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="stateWiseSale()">
                                <i class="bi bi-geo-alt me-1"></i>State Wise Sale
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="viewReport()">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($sales) && $sales->count() > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Total Bills</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Gross Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['nt_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2">
                    <small>Discount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['dis_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Tax Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tax_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Sch. Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['scm_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2">
                    <small class="text-white-50">Net Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0" id="salesTable">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 80px;">Bill No</th>
                            <th>Party Name</th>
                            @if($showArea ?? false)
                            <th>Area</th>
                            @endif
                            @if($showSalesman ?? false)
                            <th>Salesman</th>
                            @endif
                            <th class="text-end">Gross Amt</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Sch Amt</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amount</th>
                            @if($withAddress ?? false)
                            <th>Address</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentDate = null; $dayTotal = 0; $dayCount = 0; @endphp
                        @forelse($sales ?? [] as $index => $sale)
                            @if(($dayWiseTotal ?? 'N') == 'Y' && $currentDate !== null && $currentDate != $sale->sale_date->format('Y-m-d'))
                                <tr class="table-warning fw-bold">
                                    <td colspan="{{ 4 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0) }}" class="text-end">
                                        Day Total ({{ \Carbon\Carbon::parse($currentDate)->format('d-m-Y') }}): {{ $dayCount }} Bills
                                    </td>
                                    <td class="text-end" colspan="4"></td>
                                    <td class="text-end">₹{{ number_format($dayTotal, 2) }}</td>
                                    @if($withAddress ?? false)<td></td>@endif
                                </tr>
                                @php $dayTotal = 0; $dayCount = 0; @endphp
                            @endif
                            @php 
                                $currentDate = $sale->sale_date->format('Y-m-d'); 
                                $dayTotal += $sale->net_amount;
                                $dayCount++;
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
                                {{ $sale->customer->name ?? 'N/A' }}
                            </td>
                            @if($showArea ?? false)
                            <td>{{ $sale->customer->area_name ?? '-' }}</td>
                            @endif
                            @if($showSalesman ?? false)
                            <td>{{ $sale->salesman->name ?? '-' }}</td>
                            @endif
                            <td class="text-end">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->scm_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                            @if($withAddress ?? false)
                            <td class="small">{{ $sale->customer->address ?? '' }}</td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse

                        @if(($dayWiseTotal ?? 'N') == 'Y' && isset($sales) && $sales->count() > 0)
                            <tr class="table-warning fw-bold">
                                <td colspan="{{ 4 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0) }}" class="text-end">
                                    Day Total ({{ \Carbon\Carbon::parse($currentDate)->format('d-m-Y') }}): {{ $dayCount }} Bills
                                </td>
                                <td class="text-end" colspan="4"></td>
                                <td class="text-end">₹{{ number_format($dayTotal, 2) }}</td>
                                @if($withAddress ?? false)<td></td>@endif
                            </tr>
                        @endif
                    </tbody>
                    @if(isset($sales) && $sales->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="{{ 3 + (($showArea ?? false) ? 1 : 0) + (($showSalesman ?? false) ? 1 : 0) }}" class="text-end">
                                Grand Total: {{ number_format($totals['count'] ?? 0) }} Bills
                            </td>
                            <td class="text-end">{{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['scm_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                            @if($withAddress ?? false)<td></td>@endif
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
// Sync report type radio buttons with hidden field
document.querySelectorAll('input[name="report_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden_report_type').value = this.value;
    });
});

// Party code to customer select sync
document.querySelector('input[name="party_code"]').addEventListener('change', function() {
    const code = this.value;
    const select = document.getElementById('customerSelect');
    for (let option of select.options) {
        if (option.dataset.code === code) {
            select.value = option.value;
            break;
        }
    }
});

// Customer select to party code sync
document.getElementById('customerSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    document.querySelector('input[name="party_code"]').value = selectedOption.dataset.code || '';
});

function exportToExcel() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sales-book") }}?' + params.toString(), '_blank');
}

function stateWiseSale() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('group_by', 'state');
    window.location.href = '{{ route("admin.reports.sales.sales-book") }}?' + params.toString();
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    
    // Open in new window with print-friendly size
    const printWindow = window.open(
        '{{ route("admin.reports.sales.sales-book") }}?' + params.toString(),
        'SaleBookReport',
        'width=1200,height=800,scrollbars=yes,resizable=yes'
    );
    
    if (printWindow) {
        printWindow.focus();
    }
}
</script>
@endpush

@push('styles')
<style>
.input-group-text {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    min-width: fit-content;
}
.form-control, .form-select {
    font-size: 0.8rem;
}
.table th, .table td {
    padding: 0.35rem 0.5rem;
    font-size: 0.8rem;
    vertical-align: middle;
}
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
@media print {
    .btn, form, .card-header, .input-group { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    .table { font-size: 10px !important; }
}
</style>
@endpush
