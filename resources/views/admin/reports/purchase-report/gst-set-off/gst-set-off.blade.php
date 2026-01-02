@extends('layouts.admin')

@section('title', 'GST - SET OFF Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f0e68c 0%, #daa520 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-dark fst-italic fw-bold">GST - SET OFF Report</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.gst-set-off') }}">
                <div class="row g-2">
                    <!-- Row 1: Date Range -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 2: Options -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Show DN/CN:</span>
                            <select name="show_dn_cn" class="form-select" style="max-width: 60px;">
                                <option value="Y" {{ ($showDnCn ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($showDnCn ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Show Br. Exp:</span>
                            <select name="show_br_exp" class="form-select" style="max-width: 60px;">
                                <option value="Y" {{ ($showBrExp ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($showBrExp ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-1">
                            <input type="checkbox" class="form-check-input" name="without_hsn" id="withoutHsn" value="1" {{ ($withoutHsn ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="withoutHsn">Without HSN Code</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
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

    <!-- GST Summary Cards -->
    @if(isset($totals) && ($totals['input_gst'] > 0 || $totals['output_gst'] > 0))
    <div class="row g-2 mb-2">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Input GST (ITC)</small>
                    <h6 class="mb-0">₹{{ number_format($totals['input_gst'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Output GST (Liability)</small>
                    <h6 class="mb-0">₹{{ number_format($totals['output_gst'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">ITC Set Off</small>
                    <h6 class="mb-0">₹{{ number_format($totals['set_off'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ ($totals['net'] ?? 0) >= 0 ? 'bg-danger' : 'bg-success' }} text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">{{ ($totals['net'] ?? 0) >= 0 ? 'Net Payable' : 'Net Refundable' }}</small>
                    <h6 class="mb-0">₹{{ number_format(abs($totals['net'] ?? 0), 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- GST Tables -->
    <div class="row g-2">
        <!-- INPUT GST Section -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-arrow-down-circle me-1"></i>INPUT GST (ITC Available)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Particulars</th>
                                <th class="text-end" style="width: 90px;">CGST</th>
                                <th class="text-end" style="width: 90px;">SGST</th>
                                <th class="text-end" style="width: 90px;">IGST</th>
                                <th class="text-end" style="width: 100px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($input))
                            <tr>
                                <td>Purchase B2B</td>
                                <td class="text-end">{{ number_format($input['purchase_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['purchase_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['purchase_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($input['purchase_total'] ?? 0, 2) }}</td>
                            </tr>
                            @if(($showDnCn ?? 'Y') == 'Y')
                            <tr>
                                <td>Add: Debit Note</td>
                                <td class="text-end text-success">{{ number_format($input['dn_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($input['dn_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($input['dn_igst'] ?? 0, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($input['dn_total'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Less: Credit Note</td>
                                <td class="text-end text-danger">{{ number_format($input['cn_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($input['cn_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($input['cn_igst'] ?? 0, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($input['cn_total'] ?? 0, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="table-info fw-bold">
                                <td>Net Input ITC</td>
                                <td class="text-end">{{ number_format($input['net_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['net_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['net_igst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['net_total'] ?? 0, 2) }}</td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    Click "View" to load data
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- OUTPUT GST Section -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark py-2">
                    <h6 class="mb-0"><i class="bi bi-arrow-up-circle me-1"></i>OUTPUT GST (Liability)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Particulars</th>
                                <th class="text-end" style="width: 90px;">CGST</th>
                                <th class="text-end" style="width: 90px;">SGST</th>
                                <th class="text-end" style="width: 90px;">IGST</th>
                                <th class="text-end" style="width: 100px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($output))
                            <tr>
                                <td>Sales B2B/B2C</td>
                                <td class="text-end">{{ number_format($output['sales_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['sales_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['sales_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($output['sales_total'] ?? 0, 2) }}</td>
                            </tr>
                            @if(($showDnCn ?? 'Y') == 'Y')
                            <tr>
                                <td>Add: Credit Note</td>
                                <td class="text-end text-success">{{ number_format($output['cn_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($output['cn_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($output['cn_igst'] ?? 0, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($output['cn_total'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Less: Debit Note</td>
                                <td class="text-end text-danger">{{ number_format($output['dn_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($output['dn_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($output['dn_igst'] ?? 0, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($output['dn_total'] ?? 0, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="table-warning fw-bold">
                                <td>Net Output Liability</td>
                                <td class="text-end">{{ number_format($output['net_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['net_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['net_igst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['net_total'] ?? 0, 2) }}</td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    Click "View" to load data
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- GST Set Off Computation -->
    @if(isset($setoff))
    <div class="card shadow-sm mt-2">
        <div class="card-header bg-dark text-white py-2">
            <h6 class="mb-0"><i class="bi bi-calculator me-1"></i>GST SET OFF COMPUTATION</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-secondary">
                    <tr>
                        <th>Description</th>
                        <th class="text-end" style="width: 120px;">CGST</th>
                        <th class="text-end" style="width: 120px;">SGST</th>
                        <th class="text-end" style="width: 120px;">IGST</th>
                        <th class="text-end" style="width: 130px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Output Liability (A)</td>
                        <td class="text-end">{{ number_format($setoff['liability_cgst'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($setoff['liability_sgst'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($setoff['liability_igst'] ?? 0, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($setoff['liability_total'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Less: Input ITC Set Off (B)</td>
                        <td class="text-end text-success">{{ number_format($setoff['itc_cgst'] ?? 0, 2) }}</td>
                        <td class="text-end text-success">{{ number_format($setoff['itc_sgst'] ?? 0, 2) }}</td>
                        <td class="text-end text-success">{{ number_format($setoff['itc_igst'] ?? 0, 2) }}</td>
                        <td class="text-end text-success">{{ number_format($setoff['itc_total'] ?? 0, 2) }}</td>
                    </tr>
                    <tr class="{{ ($setoff['net_total'] ?? 0) >= 0 ? 'table-danger' : 'table-success' }} fw-bold">
                        <td>Net GST {{ ($setoff['net_total'] ?? 0) >= 0 ? 'Payable' : 'Refundable' }} (A - B)</td>
                        <td class="text-end">{{ number_format($setoff['net_cgst'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($setoff['net_sgst'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($setoff['net_igst'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format(abs($setoff['net_total'] ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');

    // View button
    document.getElementById('btnView').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        let exportInput = form.querySelector('input[name="export"]');
        if (exportInput) exportInput.value = '';
        form.target = '_self';
        form.submit();
    });

    // Excel button
    document.getElementById('btnExcel').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (!exportInput) {
            exportInput = document.createElement('input');
            exportInput.type = 'hidden';
            exportInput.name = 'export';
            form.appendChild(exportInput);
        }
        exportInput.value = 'excel';
        form.target = '_self';
        form.submit();
        exportInput.value = '';
    });

    // Print button
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
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnView').click();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.4rem 0.5rem; font-size: 0.8rem; }
</style>
@endpush
