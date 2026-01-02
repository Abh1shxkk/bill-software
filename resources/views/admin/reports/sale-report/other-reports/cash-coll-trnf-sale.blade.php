@extends('layouts.admin')

@section('title', 'Cash Collection Transfer')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #ffc4d0 0%, #ffb3c6 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="letter-spacing: 2px;">CASH COLLECTION TRANSFER</h4>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="card shadow-sm">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.cash-coll-trnf-sale') }}">
                <!-- Date Filters Row -->
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text fw-bold" style="width: 80px;">From :</span>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text fw-bold" style="width: 80px;">To :</span>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons Row -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 2px solid #000;">
                            <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()" style="min-width: 100px;">
                                <span class="text-decoration-underline">E</span>xcel
                            </button>
                            <div class="d-flex gap-2">
                                <button type="submit" form="filterForm" class="btn btn-info btn-sm" style="min-width: 100px;">
                                    <span class="text-decoration-underline">V</span>iew
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="closeWindow()" style="min-width: 100px;">
                                    Close(Esc)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Data Table -->
    @if(isset($sales) && $sales->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2">
            <strong>Cash Collection Transfer Report ({{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }})</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 50px;">Sr.</th>
                            <th class="text-center" style="width: 100px;">Date</th>
                            <th class="text-center" style="width: 100px;">Bill No</th>
                            <th style="width: 80px;">Party Code</th>
                            <th>Party Name</th>
                            <th>Salesman</th>
                            <th class="text-end" style="width: 120px;">Net Amount</th>
                            <th class="text-end" style="width: 120px;">Paid Amount</th>
                            <th class="text-end" style="width: 120px;">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $index => $sale)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td class="text-center">{{ ($sale->series ?? '') . $sale->invoice_no }}</td>
                            <td>{{ $sale->customer->code ?? '' }}</td>
                            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td>{{ $sale->salesman->name ?? '' }}</td>
                            <td class="text-end">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->paid_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->balance_amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-warning fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">TOTAL:</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['paid_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['balance_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <small class="text-muted">Total Records: {{ $totals['count'] ?? 0 }}</small>
        </div>
    </div>

    <!-- Daily Summary -->
    @if(isset($dailySummary) && $dailySummary->count() > 0)
    <div class="card mt-3">
        <div class="card-header bg-info text-white py-2">
            <strong>Daily Summary</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>Date</th>
                            <th class="text-center">Bills</th>
                            <th class="text-end">Net Amount</th>
                            <th class="text-end">Paid Amount</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailySummary as $date => $summary)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($date)->format('d-M-Y (D)') }}</td>
                            <td class="text-center">{{ $summary['count'] }}</td>
                            <td class="text-end">{{ number_format($summary['net_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($summary['paid_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($summary['balance_amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    @elseif(request()->has('date_from'))
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle"></i> No cash collection records found for the selected date range.
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.sales.other.cash-coll-trnf-sale") }}?' + params.toString();
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.other.cash-coll-trnf-sale") }}?' + params.toString(), 'CashCollTrnfSale', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}

function closeWindow() {
    window.location.href = '{{ route("admin.reports.sales") }}';
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeWindow();
    }
    if ((e.key === 'e' || e.key === 'E') && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
        exportToExcel();
    }
    if ((e.key === 'v' || e.key === 'V') && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
        viewReport();
    }
});

// Auto-submit on date change
document.getElementById('date_from').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});
document.getElementById('date_to').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});
</script>
@endpush

@push('styles')
<style>
.input-group-text {
    font-size: 0.875rem;
    padding: 0.375rem 0.5rem;
    background-color: #f8f9fa;
}
.form-control {
    font-size: 0.875rem;
    padding: 0.375rem 0.5rem;
}
.btn-sm {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    font-weight: 500;
}
.table th, .table td {
    padding: 0.4rem 0.5rem;
    font-size: 0.85rem;
    vertical-align: middle;
}
</style>
@endpush
