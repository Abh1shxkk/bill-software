@extends('layouts.admin')

@section('title', 'GSTR-4 Annual Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2">
            <h4 class="mb-0 fst-italic" style="font-family: 'Times New Roman', serif; color: #800000;">GSTR-4 (Annual)</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.gst.gstr-4-annual') }}">
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
                               value="{{ $fromDate ?? date('Y-01-01') }}" style="width: 125px;">
                    </div>
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">To</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" id="toDate" class="form-control form-control-sm" 
                               value="{{ $toDate ?? date('Y-12-31') }}" style="width: 125px;">
                    </div>
                    <div class="col-auto ms-3">
                        <small class="text-muted">Download File : <span class="text-danger fw-bold">GST4.xls</span></small>
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
                            <td class="fw-bold">Annual Turnover</td>
                            <td class="text-center">{{ $reportData['annual_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['annual_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['annual_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['annual_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['annual_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['annual_cess'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['annual_total_tax'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Outward Supplies (Taxable)</td>
                            <td class="text-center">{{ $reportData['outward_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['outward_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['outward_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['outward_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['outward_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['outward_cess'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['outward_total_tax'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Inward Supplies (RCM)</td>
                            <td class="text-center">{{ $reportData['inward_rcm_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['inward_rcm_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['inward_rcm_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['inward_rcm_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['inward_rcm_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['inward_rcm_cess'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData['inward_rcm_total_tax'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Exempt Supplies</td>
                            <td class="text-center">{{ $reportData['exempt_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['exempt_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td>Nil Rated Supplies</td>
                            <td class="text-center">{{ $reportData['nil_count'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($reportData['nil_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr class="table-warning">
                            <td class="fw-bold">Total Tax Payable</td>
                            <td class="text-center">-</td>
                            <td class="text-end fw-bold">{{ number_format($reportData['total_taxable'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($reportData['total_igst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($reportData['total_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($reportData['total_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($reportData['total_cess'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($reportData['total_tax'] ?? 0, 2) }}</td>
                        </tr>
                        @else
                        <tr><td colspan="8" class="text-center py-3">Click OK to generate annual report</td></tr>
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
                        <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                            <i class="bi bi-file-earmark-excel me-1"></i>Excel
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="btnPreview">
                            <i class="bi bi-eye me-1"></i>Preview
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
        
        monthSelect.prop('disabled', useDateRange);
        yearSelect.prop('disabled', useDateRange);
        
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
        window.location.href = '{{ route("admin.reports.gst.gstr-4-annual") }}?export=excel&' + $('#filterForm').serialize();
    });

    // Preview
    $('#btnPreview').on('click', function() {
        window.open('{{ route("admin.reports.gst.gstr-4-annual") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
