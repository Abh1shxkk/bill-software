@extends('layouts.admin')

@section('title', 'GSTR-1 Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2">
            <h4 class="mb-0 fst-italic" style="font-family: 'Times New Roman', serif; color: #800000;">G S T R 1</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.gst.gstr-1') }}">
                <!-- Row 1 -->
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Month</label>
                    </div>
                    <div class="col-auto">
                        <select name="month" class="form-select form-select-sm" style="width: 100px;" {{ ($useDateRange ?? false) ? 'disabled' : '' }}>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ ($month ?? date('n')) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Year</label>
                    </div>
                    <div class="col-auto">
                        <select name="year" class="form-select form-select-sm" style="width: 75px;" {{ ($useDateRange ?? false) ? 'disabled' : '' }}>
                            @foreach(range(date('Y') - 5, date('Y') + 1) as $y)
                                <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="date_range" id="dateRange" value="1" {{ ($useDateRange ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="dateRange">Date Range</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Date</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" id="fromDate" class="form-control form-control-sm" 
                               value="{{ $fromDate ?? date('Y-m-01') }}" style="width: 125px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">To</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" id="toDate" class="form-control form-control-sm" 
                               value="{{ $toDate ?? date('Y-m-d') }}" style="width: 125px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">HSN</label>
                    </div>
                    <div class="col-auto">
                        <select name="hsn" class="form-select form-select-sm" style="width: 70px;">
                            <option value="Full" {{ ($hsn ?? 'Full') == 'Full' ? 'selected' : '' }}>Full</option>
                            <option value="4" {{ ($hsn ?? '') == '4' ? 'selected' : '' }}>4</option>
                            <option value="6" {{ ($hsn ?? '') == '6' ? 'selected' : '' }}>6</option>
                            <option value="8" {{ ($hsn ?? '') == '8' ? 'selected' : '' }}>8</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-info btn-sm" id="btnInvalidGstn">Invalid GSTN List</button>
                    </div>
                </div>

                <!-- Row 2: Checkboxes -->
                <div class="row g-2 align-items-center mt-1">
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="reduce_unregistered_b2c" id="reduceUnregisteredB2C" value="1" {{ ($reduceUnregisteredB2C ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="reduceUnregisteredB2C">Reduce Unregistered CN/DN from B2C (Small)</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="zero_rated_remove_b2b" id="zeroRatedRemoveB2B" value="1" {{ ($zeroRatedRemoveB2B ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="zeroRatedRemoveB2B">Zero Rated Sale Remove in B2B</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-1">
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="reduce_cust_expiry" id="reduceCustExpiry" value="1" {{ ($reduceCustExpiry ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="reduceCustExpiry">Reduce Cust. Expiry Same As Sales Return</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="add_supplier_expiry" id="addSupplierExpiry" value="1" {{ ($addSupplierExpiry ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="addSupplierExpiry">Add Supplier Expiry Same As Sales Bill</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-1">
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="amendment_row" id="amendmentRow" value="1" {{ ($amendmentRow ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="amendmentRow">Amendment Row</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="new_hsn_summary" id="newHsnSummary" value="1" checked {{ ($newHsnSummary ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="newHsnSummary">New HSN Summary</label>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row g-2 mt-2">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg me-1"></i>OK
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Exit
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Results Table -->
    <div class="card shadow-sm mb-2">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="background-color: #ffffcc;">
                    <thead style="background-color: #cc9966;">
                        <tr>
                            <th style="width: 200px;">Description</th>
                            <th class="text-center" style="width: 70px;">Count</th>
                            <th class="text-end" style="width: 110px;">Taxable</th>
                            <th class="text-end" style="width: 90px;">IGST</th>
                            <th class="text-end" style="width: 90px;">CGST</th>
                            <th class="text-end" style="width: 90px;">SGST</th>
                            <th class="text-end" style="width: 70px;">CESS</th>
                            <th class="text-end" style="width: 110px;">Total Tax</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($reportData))
                        <tr>
                            <td class="fw-bold">B2B (Registered)</td>
                            <td class="text-center">{{ $reportData['b2b_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_cess'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_total_tax'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>B2C Large</td>
                            <td class="text-center">0</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">B2C Small (Unregistered)</td>
                            <td class="text-center">{{ $reportData['b2c_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['b2c_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2c_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2c_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2c_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2c_cess'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2c_total_tax'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Credit/Debit Notes (Registered)</td>
                            <td class="text-center">0</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                        </tr>
                        <tr>
                            <td>Credit/Debit Notes (Unregistered)</td>
                            <td class="text-center">0</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                        </tr>
                        <tr>
                            <td>Exports</td>
                            <td class="text-center">0</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                        </tr>
                        <tr>
                            <td>Nil Rated/Exempted</td>
                            <td class="text-center">0</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td>Advances Received</td>
                            <td class="text-center">0</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                        </tr>
                        <tr>
                            <td>Adjustment of Advances</td>
                            <td class="text-center">0</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                        </tr>
                        <tr>
                            <td>HSN Summary</td>
                            <td class="text-center">{{ isset($hsnSummary) ? $hsnSummary->count() : 0 }}</td>
                            <td class="text-end">{{ number_format(isset($hsnSummary) ? $hsnSummary->sum('taxable_value') : 0, 2) }}</td>
                            <td class="text-end">{{ number_format(isset($hsnSummary) ? $hsnSummary->sum('igst') : 0, 2) }}</td>
                            <td class="text-end">{{ number_format(isset($hsnSummary) ? $hsnSummary->sum('cgst') : 0, 2) }}</td>
                            <td class="text-end">{{ number_format(isset($hsnSummary) ? $hsnSummary->sum('sgst') : 0, 2) }}</td>
                            <td class="text-end">{{ number_format(isset($hsnSummary) ? $hsnSummary->sum('cess') : 0, 2) }}</td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td>Documents Issued</td>
                            <td class="text-center">{{ ($reportData['b2b_count'] ?? 0) + ($reportData['b2c_count'] ?? 0) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                        @else
                        <tr><td colspan="8" class="text-center py-3">Click OK to generate report</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-auto">
                    <small class="text-success"><i class="bi bi-info-circle me-1"></i>Excel Allow Only For Less Then 20000 Record in B2B</small>
                </div>
                <div class="col-auto">
                    <small class="text-primary"><i class="bi bi-upload me-1"></i>Upload FOR REGISTRATION : http://gst.easysol.in/</small>
                </div>
                <div class="col-auto ms-auto">
                    <span class="me-2">Download File : GSTR1.xls</span>
                    <span>File Path : C:\ExTemp\GSTR</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnDocs">Docs</button>
                        <button type="button" class="btn btn-success btn-sm" id="btnExcelGstr">Excel Gstr</button>
                        <button type="button" class="btn btn-success btn-sm" id="btnExcelAnxI">Excel Anx-I</button>
                        <div class="form-check form-check-inline align-self-center">
                            <input type="checkbox" class="form-check-input" id="absCheckbox">
                            <label class="form-check-label" for="absCheckbox">Abs</label>
                        </div>
                        <button type="button" class="btn btn-warning btn-sm" id="btnCsvGstr">CSV Gstr</button>
                        <button type="button" class="btn btn-warning btn-sm" id="btnCsvAnxI">CSV Anx-I</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const dateRangeCheckbox = $('#dateRange');
    const monthSelect = $('select[name="month"]');
    const yearSelect = $('select[name="year"]');
    const fromDateInput = $('#fromDate');
    const toDateInput = $('#toDate');

    function toggleDateInputs() {
        const useDateRange = dateRangeCheckbox.is(':checked');
        
        // Toggle month/year
        monthSelect.prop('disabled', useDateRange);
        yearSelect.prop('disabled', useDateRange);
        
        // Toggle date inputs visibility/interactivity
        if (useDateRange) {
            fromDateInput.removeClass('bg-light');
            toDateInput.removeClass('bg-light');
            fromDateInput.css('pointer-events', 'auto');
            toDateInput.css('pointer-events', 'auto');
        } else {
            fromDateInput.addClass('bg-light');
            toDateInput.addClass('bg-light');
            fromDateInput.css('pointer-events', 'none');
            toDateInput.css('pointer-events', 'none');
        }
    }

    dateRangeCheckbox.on('change', toggleDateInputs);
    
    // Initial state
    toggleDateInputs();

    // Excel Export
    $('#btnExcelGstr').on('click', function() {
        window.location.href = '{{ route("admin.reports.gst.gstr-1") }}?export=excel&type=gstr&' + $('#filterForm').serialize();
    });

    $('#btnExcelAnxI').on('click', function() {
        window.location.href = '{{ route("admin.reports.gst.gstr-1") }}?export=excel&type=anx&' + $('#filterForm').serialize();
    });

    // CSV Export
    $('#btnCsvGstr').on('click', function() {
        window.location.href = '{{ route("admin.reports.gst.gstr-1") }}?export=csv&type=gstr&' + $('#filterForm').serialize();
    });

    $('#btnCsvAnxI').on('click', function() {
        window.location.href = '{{ route("admin.reports.gst.gstr-1") }}?export=csv&type=anx&' + $('#filterForm').serialize();
    });

    // Invalid GSTN List
    $('#btnInvalidGstn').on('click', function() {
        window.open('{{ route("admin.reports.gst.gstr-1") }}?invalid_gstn_list=1&view=1&' + $('#filterForm').serialize(), '_blank');
    });
});
</script>
@endpush

@push('styles')
<style>
.form-control, .form-select { font-size: 0.8rem; }
.form-check-label { font-size: 0.8rem; }
.table th, .table td { padding: 0.3rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
</style>
@endpush
