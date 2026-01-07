@extends('layouts.admin')

@section('title', 'Cash Collection Transfer')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">CASH COLLECTION TRANSFER</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.cash-coll-trnf-sale') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <u>V</u>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">
                            <u>P</u>rint
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-light border px-4 fw-bold shadow-sm">
                            <u>C</u>lose
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table - Only show when view is clicked -->
    @if(request()->has('view') && isset($sales) && $sales->count() > 0)
    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Total Bills</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Net Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Paid Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['paid_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Balance</small>
                    <h6 class="mb-0">₹{{ number_format($totals['balance_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 90px;">Bill No</th>
                            <th style="width: 70px;">Code</th>
                            <th>Party Name</th>
                            <th>Salesman</th>
                            <th class="text-end" style="width: 100px;">Net Amount</th>
                            <th class="text-end" style="width: 100px;">Paid Amount</th>
                            <th class="text-end" style="width: 100px;">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $index => $sale)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td>{{ ($sale->series ?? '') . $sale->invoice_no }}</td>
                            <td>{{ $sale->customer->code ?? '' }}</td>
                            <td>{{ Str::limit($sale->customer->name ?? 'N/A', 25) }}</td>
                            <td>{{ Str::limit($sale->salesman->name ?? '', 15) }}</td>
                            <td class="text-end">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($sale->paid_amount ?? 0, 2) }}</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($sale->balance_amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Grand Total ({{ $totals['count'] }} Bills):</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['paid_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['balance_amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Daily Summary -->
    @if(isset($dailySummary) && $dailySummary->count() > 0)
    <div class="card shadow-sm mt-2">
        <div class="card-header bg-info text-white py-2">
            <strong>Daily Summary</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
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
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle"></i> No cash collection records found for the selected date range.
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.other.cash-coll-trnf-sale") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.other.cash-coll-trnf-sale") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        printReport();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
    if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();
        exportToExcel();
    }
    if (e.key === 'Escape') {
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.8rem; border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
