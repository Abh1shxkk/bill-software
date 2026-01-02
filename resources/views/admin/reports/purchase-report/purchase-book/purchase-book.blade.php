@extends('layouts.admin')

@section('title', 'Purchase Book')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #cce5ff 0%, #e6f2ff 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold">PURCHASE BOOK</h4>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="fw-bold small">Report Type:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="report_type" id="type_purchase" value="1" {{ ($reportType ?? '1') == '1' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_purchase">1. Purchase</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_return" value="2" {{ ($reportType ?? '') == '2' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_return">2. Purchase Return</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_debit" value="3" {{ ($reportType ?? '') == '3' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_debit">3. D.Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_credit" value="4" {{ ($reportType ?? '') == '4' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_credit">4. C.Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_consolidated" value="5" {{ ($reportType ?? '') == '5' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_consolidated">5. Consolidated Pur. Book</label>
                    
                    <input type="radio" class="btn-check" name="report_type" id="type_all_cn_dn" value="6" {{ ($reportType ?? '') == '6' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="type_all_cn_dn">6. All CN_DN</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.purchase-book') }}">
                <input type="hidden" name="report_type" id="hidden_report_type" value="{{ $reportType ?? '1' }}">
                
                <div class="row g-2">
                    <!-- Row 1: Date Range & Basic Options -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">with Br.Exp</span>
                            <select name="with_br_exp" class="form-select">
                                <option value="Y" {{ ($withBrExp ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($withBrExp ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">with Customer Exp</span>
                            <select name="with_customer_exp" class="form-select">
                                <option value="Y" {{ ($withCustomerExp ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($withCustomerExp ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">with RCM</span>
                            <select name="with_rcm" class="form-select">
                                <option value="Y" {{ ($withRcm ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($withRcm ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">From</span>
                                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">To</span>
                                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Format & Options -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">D/S/M/G</span>
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
                            <span class="input-group-text">T(ax)/R(etail)</span>
                            <select name="tax_retail" class="form-select">
                                <option value="">All</option>
                                <option value="T">Tax</option>
                                <option value="R">Retail</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">P/T/B</span>
                            <select name="purchase_transfer" class="form-select">
                                <option value="P">P(urchase)</option>
                                <option value="T">T(ransfer)</option>
                                <option value="B">B(oth)</option>
                            </select>
                        </div>
                    </div>
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

                    <!-- Row 3: GSTN & Stock Options -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1.With GSTN / 2.Without GSTN / 3.All</span>
                            <select name="gstn_filter" class="form-select">
                                <option value="3" {{ ($gstnFilter ?? '3') == '3' ? 'selected' : '' }}>3</option>
                                <option value="1" {{ ($gstnFilter ?? '') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($gstnFilter ?? '') == '2' ? 'selected' : '' }}>2</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-check-inline mt-1">
                            <input class="form-check-input" type="checkbox" name="without_stock" id="withoutStock" value="1" {{ ($withoutStock ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-bold" for="withoutStock">Without Stock</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check form-check-inline mt-1">
                                <input class="form-check-input" type="checkbox" name="gr_details" id="grDetails" value="1" {{ ($grDetails ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="grDetails">Gr Details</label>
                            </div>
                            <div class="form-check form-check-inline mt-1">
                                <input class="form-check-input" type="checkbox" name="order_by_supplier" id="orderBySupplier" value="1" {{ ($orderBySupplier ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="orderBySupplier">Order by Supplier</label>
                            </div>
                            <div class="form-check form-check-inline mt-1">
                                <input class="form-check-input" type="checkbox" name="party_wise_total" id="partyWiseTotal" value="1" {{ ($partyWiseTotal ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="partyWiseTotal">Party Wise Total</label>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Supplier Filters -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier</span>
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="00">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <select name="supplier_id" class="form-select" id="supplierSelect">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->supplier_id }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                        {{ $supplier->code ?? '' }} - {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-check-inline mt-1">
                            <input class="form-check-input" type="checkbox" name="with_address" id="withAddress" value="1" {{ ($withAddress ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-bold" for="withAddress">With Address</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Day Wise Total</span>
                            <select name="day_wise_total" class="form-select">
                                <option value="N" {{ ($dayWiseTotal ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($dayWiseTotal ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 5: Location Filters -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L/C/B/Import</span>
                            <select name="local_central" class="form-select">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B(oth)</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L(ocal)</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C(entral)</option>
                                <option value="I" {{ ($localCentral ?? '') == 'I' ? 'selected' : '' }}>I(mport)</option>
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
                            <span class="input-group-text">State</span>
                            <select name="state_id" class="form-select">
                                <option value="">All</option>
                                @foreach($states ?? [] as $state)
                                    <option value="{{ $state->id }}" {{ ($stateId ?? '') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                </div>

                <!-- Display Options -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center border rounded p-2 bg-light">
                            <span class="fw-bold small">Display Options:</span>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_gst_details" id="showGstDetails" value="1" {{ ($showGstDetails ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showGstDetails">GST Details</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="show_area" id="showArea" value="1" {{ ($showArea ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showArea">Show Area</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-excel me-1"></i>Excel
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="stateWisePurchase()">
                                <i class="bi bi-geo-alt me-1"></i>State Wise Pur.
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="btnView">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="btnPrint">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Summary Cards -->
    @if(isset($purchases) && $purchases->count() > 0)
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
                    <small class="text-white-50">TCS Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tcs_amount'] ?? 0, 2) }}</h6>
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
                <table class="table table-sm table-hover table-striped table-bordered mb-0" id="purchaseTable">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 100px;">Bill No</th>
                            <th>Supplier Name</th>
                            @if($showArea ?? false)
                            <th>Area</th>
                            @endif
                            <th class="text-end">Gross Amt</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amount</th>
                            @if($withAddress ?? false)
                            <th>Address</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentDate = null; $dayTotal = 0; $dayCount = 0; @endphp
                        @forelse($purchases ?? [] as $index => $purchase)
                            @if(($dayWiseTotal ?? 'N') == 'Y' && $currentDate !== null && $currentDate != $purchase->bill_date->format('Y-m-d'))
                                <tr class="table-warning fw-bold">
                                    <td colspan="{{ 4 + (($showArea ?? false) ? 1 : 0) }}" class="text-end">
                                        Day Total ({{ \Carbon\Carbon::parse($currentDate)->format('d-m-Y') }}): {{ $dayCount }} Bills
                                    </td>
                                    <td class="text-end" colspan="3"></td>
                                    <td class="text-end">₹{{ number_format($dayTotal, 2) }}</td>
                                    @if($withAddress ?? false)<td></td>@endif
                                </tr>
                                @php $dayTotal = 0; $dayCount = 0; @endphp
                            @endif
                            @php 
                                $currentDate = $purchase->bill_date->format('Y-m-d'); 
                                $dayTotal += $purchase->net_amount ?? 0;
                                $dayCount++;
                            @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $purchase->bill_date->format('d-m-Y') }}</td>
                            <td>
                                @if(in_array($reportType ?? '1', ['1', '5']))
                                <a href="{{ route('admin.purchase.show', $purchase->id) }}" class="text-decoration-none fw-bold">
                                    {{ $purchase->voucher_type ?? '' }}{{ $purchase->bill_no }}
                                </a>
                                @elseif(($reportType ?? '1') == '2')
                                <span class="fw-bold text-danger">{{ $purchase->bill_no }}</span>
                                @elseif(($reportType ?? '1') == '3')
                                <span class="fw-bold text-info">DN-{{ $purchase->bill_no }}</span>
                                @elseif(($reportType ?? '1') == '4')
                                <span class="fw-bold text-success">CN-{{ $purchase->bill_no }}</span>
                                @elseif(($reportType ?? '1') == '6')
                                <span class="fw-bold {{ ($purchase->note_type ?? '') == 'DN' ? 'text-info' : 'text-success' }}">
                                    {{ $purchase->note_type ?? '' }}-{{ $purchase->bill_no }}
                                </span>
                                @else
                                <span class="fw-bold">{{ $purchase->bill_no }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">{{ $purchase->supplier->code ?? '' }}</span>
                                {{ $purchase->supplier->name ?? $purchase->debit_party_name ?? $purchase->credit_party_name ?? 'N/A' }}
                            </td>
                            @if($showArea ?? false)
                            <td>{{ $purchase->supplier->area_name ?? '-' }}</td>
                            @endif
                            <td class="text-end">{{ number_format($purchase->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->dis_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
                            @if($withAddress ?? false)
                            <td class="small">{{ $purchase->supplier->address ?? '' }}</td>
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

                        @if(($dayWiseTotal ?? 'N') == 'Y' && isset($purchases) && $purchases->count() > 0)
                            <tr class="table-warning fw-bold">
                                <td colspan="{{ 4 + (($showArea ?? false) ? 1 : 0) }}" class="text-end">
                                    Day Total ({{ \Carbon\Carbon::parse($currentDate)->format('d-m-Y') }}): {{ $dayCount }} Bills
                                </td>
                                <td class="text-end" colspan="3"></td>
                                <td class="text-end">₹{{ number_format($dayTotal, 2) }}</td>
                                @if($withAddress ?? false)<td></td>@endif
                            </tr>
                        @endif
                    </tbody>
                    @if(isset($purchases) && $purchases->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="{{ 4 + (($showArea ?? false) ? 1 : 0) }}" class="text-end">
                                Grand Total: {{ number_format($totals['count'] ?? 0) }} Bills
                            </td>
                            <td class="text-end">{{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    
    // Sync report type radio buttons with hidden field
    document.querySelectorAll('input[name="report_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('hidden_report_type').value = this.value;
        });
    });

    // View button - submits form to load data on current page
    document.getElementById('btnView').addEventListener('click', function() {
        // Remove any view_type or export params to load data normally
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        let exportInput = form.querySelector('input[name="export"]');
        if (exportInput) exportInput.value = '';
        
        form.target = '_self';
        form.submit();
    });

    // Excel button - exports to Excel
    document.getElementById('btnExcel').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (!exportInput) {
            exportInput = document.createElement('input');
            exportInput.type = 'hidden';
            exportInput.name = 'export';
            form.appendChild(exportInput);
        }
        exportInput.value = 'excel';
        form.submit();
        exportInput.value = '';
    });

    // Print button - opens print view in new tab
    const btnPrint = document.getElementById('btnPrint');
    if (btnPrint) {
        btnPrint.addEventListener('click', function() {
            let viewTypeInput = form.querySelector('input[name="view_type"]');
            if (!viewTypeInput) {
                viewTypeInput = document.createElement('input');
                viewTypeInput.type = 'hidden';
                viewTypeInput.name = 'view_type';
                form.appendChild(viewTypeInput);
            }
            viewTypeInput.value = 'print';
            form.target = '_blank';
            form.submit();
            form.target = '_self';
            viewTypeInput.value = '';
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
        if (e.key === 'F7') {
            e.preventDefault();
            document.getElementById('btnView').click();
        }
    });
});

function stateWisePurchase() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    // Redirect to state wise purchase report with current filters
    alert('State Wise Purchase Report - Feature coming soon');
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
</style>
@endpush
