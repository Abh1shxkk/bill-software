@extends('layouts.admin')

@section('title', 'Purchase Book GSTR')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-success fst-italic fw-bold">PURCHASE BOOK GSTR</h4>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="fw-bold small">Report Type:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="report_type_btn" id="type_purchase" value="1" {{ ($reportType ?? '1') == '1' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success btn-sm" for="type_purchase">1. Purchase</label>
                    
                    <input type="radio" class="btn-check" name="report_type_btn" id="type_return" value="2" {{ ($reportType ?? '') == '2' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success btn-sm" for="type_return">2. Purchase Return</label>
                    
                    <input type="radio" class="btn-check" name="report_type_btn" id="type_debit" value="3" {{ ($reportType ?? '') == '3' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success btn-sm" for="type_debit">3. D.Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_btn" id="type_credit" value="4" {{ ($reportType ?? '') == '4' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success btn-sm" for="type_credit">4. C.Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_btn" id="type_consolidated" value="5" {{ ($reportType ?? '') == '5' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success btn-sm" for="type_consolidated">5. Consolidated</label>
                    
                    <input type="radio" class="btn-check" name="report_type_btn" id="type_all_cn_dn" value="6" {{ ($reportType ?? '') == '6' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success btn-sm" for="type_all_cn_dn">6. All CN_DN</label>

                    <input type="radio" class="btn-check" name="report_type_btn" id="type_vou_pur" value="7" {{ ($reportType ?? '') == '7' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success btn-sm" for="type_vou_pur">7. Vou.Pur.</label>

                    <input type="radio" class="btn-check" name="report_type_btn" id="type_cust_exp" value="8" {{ ($reportType ?? '') == '8' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success btn-sm" for="type_cust_exp">8. Cust Exp</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.purchase-book-gstr') }}">
                <input type="hidden" name="report_type" id="hidden_report_type" value="{{ $reportType ?? '1' }}">
                
                <div class="row g-2">
                    <!-- Row 1 -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">with Customer Exp [Y/N]:</span>
                            <select name="with_customer_exp" class="form-select">
                                <option value="Y" {{ ($withCustomerExp ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($withCustomerExp ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">RCM [Y/N/B]:</span>
                            <select name="with_rcm" class="form-select">
                                <option value="B" {{ ($withRcm ?? 'B') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="Y" {{ ($withRcm ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($withRcm ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">D(etailed)/S(ummarized)/M(onthly)/G(roup):</span>
                            <select name="report_format" class="form-select">
                                <option value="D" {{ ($reportFormat ?? 'D') == 'D' ? 'selected' : '' }}>D</option>
                                <option value="S" {{ ($reportFormat ?? '') == 'S' ? 'selected' : '' }}>S</option>
                                <option value="M" {{ ($reportFormat ?? '') == 'M' ? 'selected' : '' }}>M</option>
                                <option value="G" {{ ($reportFormat ?? '') == 'G' ? 'selected' : '' }}>G</option>
                            </select>
                        </div>
                    </div>
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
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L(ocal) / C(entral) / B(oth):</span>
                            <select name="local_central" class="form-select">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier</span>
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="00" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->code ?? '' }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-check-inline mt-1">
                            <input class="form-check-input" type="checkbox" name="order_by_supplier" id="orderBySupplier" value="1" {{ ($orderBySupplier ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-bold" for="orderBySupplier">Order by Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-check-inline mt-1">
                            <input class="form-check-input" type="checkbox" name="party_wise_total" id="partyWiseTotal" value="1" {{ ($partyWiseTotal ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-bold" for="partyWiseTotal">Party Wise Total</label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-info btn-sm" id="btnStateWise">
                                <i class="bi bi-geo-alt me-1"></i>State Wise Pur.
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="btnGstWise">
                                <i class="bi bi-percent me-1"></i>GST Wise
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-excel me-1"></i>Excel
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="btnView">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnPrint">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-dark btn-sm">
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
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Invoices</small>
                    <h6 class="mb-0">{{ number_format($totals['invoices'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Taxable Value</small>
                    <h6 class="mb-0">₹{{ number_format($totals['taxable'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">CGST</small>
                    <h6 class="mb-0">₹{{ number_format($totals['cgst'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">SGST</small>
                    <h6 class="mb-0">₹{{ number_format($totals['sgst'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small class="text-dark">IGST</small>
                    <h6 class="mb-0">₹{{ number_format($totals['igst'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Tax</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tax'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>GSTN</th>
                            <th>Supplier Name</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th class="text-end">Taxable</th>
                            <th class="text-end">CGST</th>
                            <th class="text-end">SGST</th>
                            <th class="text-end">IGST</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $index => $purchase)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="small">{{ $purchase->supplier->gst_no ?? '-' }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->voucher_type ?? '' }}{{ $purchase->bill_no }}</td>
                            <td>{{ $purchase->bill_date->format('d-m-Y') }}</td>
                            <td class="text-end">{{ number_format($purchase->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->cgst_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->sgst_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->igst_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($purchases) && $purchases->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Grand Total: {{ number_format($totals['invoices'] ?? 0) }} Invoices</td>
                            <td class="text-end">{{ number_format($totals['taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
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
    document.querySelectorAll('input[name="report_type_btn"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('hidden_report_type').value = this.value;
        });
    });

    // View button - submits form to load data on current page
    document.getElementById('btnView').addEventListener('click', function() {
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

    // State Wise button
    document.getElementById('btnStateWise').addEventListener('click', function() {
        alert('State Wise Purchase Report - Feature coming soon');
    });

    // GST Wise button
    document.getElementById('btnGstWise').addEventListener('click', function() {
        alert('GST Wise Report - Feature coming soon');
    });

    // Print button - opens print view in new tab
    document.getElementById('btnPrint').addEventListener('click', function() {
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

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
    });
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
