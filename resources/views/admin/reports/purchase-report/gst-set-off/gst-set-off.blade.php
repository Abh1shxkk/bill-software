@extends('layouts.admin')

@section('title', 'GST SET OFF')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e2e3e5 0%, #f5f5f5 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-secondary fst-italic fw-bold">GST SET OFF</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Month</span>
                            <select name="month" class="form-select">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ (date('n') == $i) ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Year</span>
                            <select name="year" class="form-select">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ (date('Y') == $y) ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <button type="button" class="btn btn-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</button>
                            <button type="button" class="btn btn-danger btn-sm"><i class="bi bi-file-pdf me-1"></i>PDF</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- GST Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Input GST (Purchase)</small>
                    <h5 class="mb-0">₹{{ number_format($totals['input_gst'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Output GST (Sales)</small>
                    <h5 class="mb-0">₹{{ number_format($totals['output_gst'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Set Off Amount</small>
                    <h5 class="mb-0">₹{{ number_format($totals['set_off'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Net Payable/Refundable</small>
                    <h5 class="mb-0">₹{{ number_format($totals['net'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- GST Breakup -->
    <div class="row g-2 mb-2">
        <!-- Input GST -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-arrow-down-circle me-2"></i>Input GST (ITC Available)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Particulars</th>
                                <th class="text-end">CGST</th>
                                <th class="text-end">SGST</th>
                                <th class="text-end">IGST</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Purchase B2B</td>
                                <td class="text-end">{{ number_format($input['purchase_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['purchase_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['purchase_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($input['purchase_total'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Debit Note</td>
                                <td class="text-end">{{ number_format($input['dn_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['dn_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['dn_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($input['dn_total'] ?? 0, 2) }}</td>
                            </tr>
                            <tr class="table-success">
                                <td class="fw-bold">Less: Credit Note</td>
                                <td class="text-end text-danger">{{ number_format($input['cn_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($input['cn_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($input['cn_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold text-danger">{{ number_format($input['cn_total'] ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td class="fw-bold">Net Input ITC</td>
                                <td class="text-end">{{ number_format($input['net_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['net_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($input['net_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($input['net_total'] ?? 0, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Output GST -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-arrow-up-circle me-2"></i>Output GST (Liability)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Particulars</th>
                                <th class="text-end">CGST</th>
                                <th class="text-end">SGST</th>
                                <th class="text-end">IGST</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sales B2B/B2C</td>
                                <td class="text-end">{{ number_format($output['sales_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['sales_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['sales_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($output['sales_total'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Credit Note</td>
                                <td class="text-end">{{ number_format($output['cn_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['cn_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['cn_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($output['cn_total'] ?? 0, 2) }}</td>
                            </tr>
                            <tr class="table-danger">
                                <td class="fw-bold">Less: Debit Note</td>
                                <td class="text-end text-success">{{ number_format($output['dn_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($output['dn_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($output['dn_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold text-success">{{ number_format($output['dn_total'] ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td class="fw-bold">Net Output Liability</td>
                                <td class="text-end">{{ number_format($output['net_cgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['net_sgst'] ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($output['net_igst'] ?? 0, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($output['net_total'] ?? 0, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Set Off Computation -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white py-2">
            <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>GST Set Off Computation</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">CGST</th>
                        <th class="text-end">SGST</th>
                        <th class="text-end">IGST</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Output Liability</td>
                        <td class="text-end">{{ number_format($setoff['liability_cgst'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($setoff['liability_sgst'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($setoff['liability_igst'] ?? 0, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($setoff['liability_total'] ?? 0, 2) }}</td>
                    </tr>
                    <tr class="table-success">
                        <td>Less: Input ITC Set Off</td>
                        <td class="text-end">{{ number_format($setoff['itc_cgst'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($setoff['itc_sgst'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($setoff['itc_igst'] ?? 0, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($setoff['itc_total'] ?? 0, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot class="table-warning">
                    <tr>
                        <td class="fw-bold">Net GST Payable / (Refundable)</td>
                        <td class="text-end fw-bold">{{ number_format($setoff['net_cgst'] ?? 0, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($setoff['net_sgst'] ?? 0, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($setoff['net_igst'] ?? 0, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($setoff['net_total'] ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
</style>
@endpush
