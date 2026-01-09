@extends('layouts.admin')

@section('title', 'GSTR-2 Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2">
            <h4 class="mb-0 fst-italic" style="font-family: 'Times New Roman', serif; color: #800000;">G S T R 2</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.gst.gstr-2') }}">
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
                </div>

                <!-- Row 2: Checkboxes -->
                <div class="row g-2 align-items-center mt-1">
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="remove_expiry_b2b" id="removeExpiryB2B" value="1" {{ ($removeExpiryB2B ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="removeExpiryB2B">Remove Expiry in B2B</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="remove_rcm" id="removeRCM" value="1" {{ ($removeRCM ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="removeRCM">Remove RCM</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="add_expiry_return" id="addExpiryReturn" value="1" {{ ($addExpiryReturn ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="addExpiryReturn">Add Expiry as Return</label>
                        </div>
                    </div>
                    <div class="col-auto ms-3">
                        <small class="text-muted">Download File : <span class="text-danger fw-bold">GSTR2.xls</span></small>
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
                            <th style="width: 200px;">Particulars</th>
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
                            <td class="fw-bold">B2B (Registered Suppliers)</td>
                            <td class="text-center">{{ $reportData['b2b_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_cess'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_total_tax'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>B2B (Unregistered Suppliers)</td>
                            <td class="text-center">{{ $reportData['b2b_unreg_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_unreg_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_unreg_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_unreg_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_unreg_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_unreg_cess'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['b2b_unreg_total_tax'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Credit/Debit Notes (Registered)</td>
                            <td class="text-center">{{ $reportData['cdn_reg_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['cdn_reg_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['cdn_reg_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['cdn_reg_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['cdn_reg_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['cdn_reg_cess'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['cdn_reg_total_tax'] ?? 0, 2) }}</td>
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
                            <td>Imports</td>
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
                            <td class="text-center">{{ $reportData['nil_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['nil_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td>ISD Credits</td>
                            <td class="text-center">0</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                        </tr>
                        <tr>
                            <td>TDS/TCS Credits</td>
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
                    <small class="text-primary"><i class="bi bi-folder me-1"></i>File Path : C:\EsTemp\GSTR</small>
                </div>
                <div class="col-auto ms-auto">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning btn-sm" id="btnCsv">
                            <i class="bi bi-filetype-csv me-1"></i>CSV
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                            <i class="bi bi-file-earmark-excel me-1"></i>Excel
                        </button>
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
    toggleDateInputs();

    // Excel Export
    $('#btnExcel').on('click', function() {
        window.location.href = '{{ route("admin.reports.gst.gstr-2") }}?export=excel&' + $('#filterForm').serialize();
    });

    // CSV Export
    $('#btnCsv').on('click', function() {
        window.location.href = '{{ route("admin.reports.gst.gstr-2") }}?export=csv&' + $('#filterForm').serialize();
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
