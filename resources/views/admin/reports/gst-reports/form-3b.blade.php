@extends('layouts.admin')

@section('title', 'GSTR-3B Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic" style="font-family: 'Times New Roman', serif; color: #800000;">G S T R 3 B</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.gst.form-3b') }}">
                <div class="row g-2 align-items-center">
                    <!-- Month -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Month</label>
                    </div>
                    <div class="col-auto">
                        <select name="month" class="form-select form-select-sm" style="width: 120px;" {{ $useDateRange ? 'disabled' : '' }}>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Year</label>
                    </div>
                    <div class="col-auto">
                        <select name="year" class="form-select form-select-sm" style="width: 90px;" {{ $useDateRange ? 'disabled' : '' }}>
                            @foreach(range(date('Y') - 5, date('Y') + 1) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Checkbox -->
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="date_range" id="dateRange" value="1" {{ $useDateRange ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="dateRange">Date Range</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-2">
                    <!-- From Date -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">From</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $fromDate }}" style="width: 140px;" {{ !$useDateRange ? 'disabled' : '' }}>
                    </div>

                    <!-- To Date -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">To</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ $toDate }}" style="width: 140px;" {{ !$useDateRange ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="row g-2 mt-2">
                    <!-- Reduce Cust. Expiry Same As Sales Return -->
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="reduce_cust_expiry" id="reduceCustExpiry" value="1" {{ $reduceCustExpiry ? 'checked' : '' }}>
                            <label class="form-check-label" for="reduceCustExpiry">Reduce Cust. Expiry Same As Sales Return</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- With Unregister Supplier Purchase -->
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="with_unregister_supplier" id="withUnregisterSupplier" value="1" {{ $withUnregisterSupplier ? 'checked' : '' }}>
                            <label class="form-check-label" for="withUnregisterSupplier">With Unregister Supplier Purchase</label>
                        </div>
                    </div>
                </div>

                <hr class="my-2">

                <div class="row g-2">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="printReport()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Results -->
    @if(!empty($gstr))
    <div class="card shadow-sm mb-2">
        <div class="card-header bg-warning text-dark py-2">
            <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>GSTR-3B Summary - {{ $reportData['period'] ?? '' }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="2">Description</th>
                            <th class="text-end" style="width: 110px;">Integrated Tax</th>
                            <th class="text-end" style="width: 110px;">Central Tax</th>
                            <th class="text-end" style="width: 110px;">State/UT Tax</th>
                            <th class="text-end" style="width: 90px;">Cess</th>
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
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
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
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
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
                        <tr class="table-info">
                            <td>(A)</td>
                            <td class="fw-bold">ITC Available (whether in full or part)</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['itc_igst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['itc_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['itc_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($gstr['itc_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="ps-4">(1) Import of goods</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">0.00</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="ps-4">(2) Import of services</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="ps-4">(3) Inward supplies liable to reverse charge</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
                            <td class="text-end">0.00</td>
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
                            <td class="text-end">{{ number_format($gstr['itc_igst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['itc_cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['itc_sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($gstr['itc_cess'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="table-warning">
                            <td>(B)</td>
                            <td class="fw-bold">ITC Reversed</td>
                            <td class="text-end text-danger">0.00</td>
                            <td class="text-end text-danger">0.00</td>
                            <td class="text-end text-danger">0.00</td>
                            <td class="text-end text-danger">0.00</td>
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
                            <td class="text-end text-success">{{ number_format(min($gstr['net_itc_igst'] ?? 0, $gstr['payable_igst'] ?? 0), 2) }}</td>
                            <td class="text-end text-success">{{ number_format(min($gstr['net_itc_cgst'] ?? 0, $gstr['payable_cgst'] ?? 0), 2) }}</td>
                            <td class="text-end text-success">{{ number_format(min($gstr['net_itc_sgst'] ?? 0, $gstr['payable_sgst'] ?? 0), 2) }}</td>
                            <td class="text-end text-success">{{ number_format(min($gstr['net_itc_cess'] ?? 0, $gstr['payable_cess'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="table-danger">
                            <td colspan="2" class="fw-bold">Tax/Cess paid in Cash</td>
                            <td class="text-end fw-bold">{{ number_format(max(0, ($gstr['payable_igst'] ?? 0) - ($gstr['net_itc_igst'] ?? 0)), 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format(max(0, ($gstr['payable_cgst'] ?? 0) - ($gstr['net_itc_cgst'] ?? 0)), 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format(max(0, ($gstr['payable_sgst'] ?? 0) - ($gstr['net_itc_sgst'] ?? 0)), 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format(max(0, ($gstr['payable_cess'] ?? 0) - ($gstr['net_itc_cess'] ?? 0)), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white py-2">
            <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Transaction Summary</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <tbody>
                        <tr>
                            <td class="fw-bold">Total Sales</td>
                            <td class="text-end">{{ number_format($gstr['total_sales'] ?? 0, 2) }}</td>
                            <td class="fw-bold">Total Sales Return</td>
                            <td class="text-end text-danger">{{ number_format($gstr['total_sales_return'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Total Purchase</td>
                            <td class="text-end">{{ number_format($gstr['total_purchase'] ?? 0, 2) }}</td>
                            <td class="fw-bold">Total Purchase Return</td>
                            <td class="text-end text-danger">{{ number_format($gstr['total_purchase_return'] ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRangeCheckbox = document.getElementById('dateRange');
    const monthSelect = document.querySelector('select[name="month"]');
    const yearSelect = document.querySelector('select[name="year"]');
    const fromDate = document.querySelector('input[name="from_date"]');
    const toDate = document.querySelector('input[name="to_date"]');

    function toggleDateInputs() {
        const useDateRange = dateRangeCheckbox.checked;
        monthSelect.disabled = useDateRange;
        yearSelect.disabled = useDateRange;
        fromDate.disabled = !useDateRange;
        toDate.disabled = !useDateRange;
    }

    dateRangeCheckbox.addEventListener('change', toggleDateInputs);
    toggleDateInputs();
});

function printReport() {
    window.open('{{ route("admin.reports.gst.form-3b") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
</style>
@endpush
