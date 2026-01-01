@extends('layouts.admin')

@section('title', 'GST SET OFF GSTR')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-success fst-italic fw-bold">GST SET OFF GSTR</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
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
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">GSTR Type</span>
                            <select name="gstr_type" class="form-select">
                                <option value="GSTR3B">GSTR-3B</option>
                                <option value="GSTR1">GSTR-1</option>
                                <option value="GSTR2A">GSTR-2A</option>
                                <option value="GSTR2B">GSTR-2B</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Format</span>
                            <select name="format" class="form-select">
                                <option value="summary">Summary</option>
                                <option value="detailed">Detailed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <button type="button" class="btn btn-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</button>
                            <button type="button" class="btn btn-info btn-sm text-white"><i class="bi bi-download me-1"></i>JSON</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- GSTR-3B Summary -->
    <div class="card shadow-sm mb-2">
        <div class="card-header bg-success text-white py-2">
            <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>GSTR-3B Summary - {{ date('F Y') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="2">Description</th>
                            <th class="text-end">Integrated Tax</th>
                            <th class="text-end">Central Tax</th>
                            <th class="text-end">State/UT Tax</th>
                            <th class="text-end">Cess</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 3.1 Outward Supplies -->
                        <tr class="table-light">
                            <td colspan="6" class="fw-bold">3.1 Details of Outward Supplies and inward supplies liable to reverse charge</td>
                        </tr>
                        <tr>
                            <td width="30">(a)</td>
                            <td>Outward taxable supplies (other than zero rated, nil rated and exempted)</td>
                            <td class="text-end">{{ number_format($gstr['outward_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['outward_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['outward_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['outward_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>(b)</td>
                            <td>Outward taxable supplies (zero rated)</td>
                            <td class="text-end">{{ number_format($gstr['zero_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['zero_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['zero_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['zero_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>(c)</td>
                            <td>Other outward supplies (Nil rated, exempted)</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td>(d)</td>
                            <td>Inward supplies (liable to reverse charge)</td>
                            <td class="text-end">{{ number_format($gstr['rcm_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['rcm_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['rcm_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['rcm_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>(e)</td>
                            <td>Non-GST outward supplies</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>

                        <!-- 4. Eligible ITC -->
                        <tr class="table-light">
                            <td colspan="6" class="fw-bold">4. Eligible ITC</td>
                        </tr>
                        <tr>
                            <td>(A)</td>
                            <td>ITC Available (whether in full or part)</td>
                            <td class="text-end">{{ number_format($gstr['itc_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['itc_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['itc_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['itc_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="ps-4">(1) Import of goods</td>
                            <td class="text-end">{{ number_format($gstr['import_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">{{ number_format($gstr['import_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="ps-4">(2) Import of services</td>
                            <td class="text-end">{{ number_format($gstr['import_svc_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="ps-4">(3) Inward supplies liable to reverse charge</td>
                            <td class="text-end">{{ number_format($gstr['rcm_input_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['rcm_input_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['rcm_input_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['rcm_input_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="ps-4">(4) Inward supplies from ISD</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="ps-4">(5) All other ITC</td>
                            <td class="text-end">{{ number_format($gstr['other_itc_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['other_itc_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['other_itc_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['other_itc_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="table-warning">
                            <td>(B)</td>
                            <td class="fw-bold">ITC Reversed</td>
                            <td class="text-end text-danger">{{ number_format($gstr['reversed_igst'] ?? 0, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($gstr['reversed_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($gstr['reversed_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($gstr['reversed_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td>(C)</td>
                            <td class="fw-bold">Net ITC Available (A) - (B)</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['net_itc_igst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['net_itc_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['net_itc_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['net_itc_cess'] ?? 0, 2) }}</td>
                        </tr>

                        <!-- 6. Payment of Tax -->
                        <tr class="table-light">
                            <td colspan="6" class="fw-bold">6. Payment of Tax</td>
                        </tr>
                        <tr class="table-primary">
                            <td colspan="2" class="fw-bold">Tax Payable</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['payable_igst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['payable_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['payable_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['payable_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Paid through ITC</td>
                            <td class="text-end">{{ number_format($gstr['paid_itc_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['paid_itc_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['paid_itc_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['paid_itc_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="table-danger">
                            <td colspan="2" class="fw-bold">Tax/Cess paid in Cash</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['cash_igst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['cash_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['cash_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['cash_cess'] ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ITC Reconciliation -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white py-2">
            <h6 class="mb-0"><i class="bi bi-check2-square me-2"></i>ITC Reconciliation with GSTR-2B</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Particulars</th>
                            <th class="text-end">As Per Books</th>
                            <th class="text-end">As Per GSTR-2B</th>
                            <th class="text-end">Difference</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>IGST</td>
                            <td class="text-end">{{ number_format($recon['book_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($recon['gstr_igst'] ?? 0, 2) }}</td>
                            <td class="text-end {{ ($recon['diff_igst'] ?? 0) != 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($recon['diff_igst'] ?? 0, 2) }}
                            </td>
                            <td class="text-center">
                                @if(($recon['diff_igst'] ?? 0) == 0)
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> Matched</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-lg"></i> Mismatch</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>CGST</td>
                            <td class="text-end">{{ number_format($recon['book_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($recon['gstr_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end {{ ($recon['diff_cgst'] ?? 0) != 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($recon['diff_cgst'] ?? 0, 2) }}
                            </td>
                            <td class="text-center">
                                @if(($recon['diff_cgst'] ?? 0) == 0)
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> Matched</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-lg"></i> Mismatch</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>SGST</td>
                            <td class="text-end">{{ number_format($recon['book_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($recon['gstr_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end {{ ($recon['diff_sgst'] ?? 0) != 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($recon['diff_sgst'] ?? 0, 2) }}
                            </td>
                            <td class="text-center">
                                @if(($recon['diff_sgst'] ?? 0) == 0)
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> Matched</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-lg"></i> Mismatch</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td class="fw-bold">Total</td>
                            <td class="text-end fw-bold">{{ number_format($recon['book_total'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($recon['gstr_total'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($recon['diff_total'] ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
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
