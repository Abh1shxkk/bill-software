@extends('layouts.admin')

@section('title', 'Pay-In-Slip Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-dark fst-italic fw-bold" style="color: #383d41 !important;">PAY - IN - SLIP REPORT</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.receipt-payment.pay-in-slip') }}">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Bank:</span>
                            <select name="bank_id" class="form-select">
                                <option value="">All Banks</option>
                                @foreach($banks ?? [] as $bank)
                                <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Type:</span>
                            <select name="report_type" class="form-select">
                                <option value="D" {{ request('report_type', 'D') == 'D' ? 'selected' : '' }}>Detailed</option>
                                <option value="S" {{ request('report_type') == 'S' ? 'selected' : '' }}>Summary</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="printReport()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 100px;">Date</th>
                            <th style="width: 120px;">Slip No</th>
                            <th>Bank Name</th>
                            <th style="width: 100px;">A/C No</th>
                            <th class="text-end" style="width: 120px;">Cash Amt</th>
                            <th class="text-end" style="width: 120px;">Cheque Amt</th>
                            <th class="text-end" style="width: 120px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalCash = 0; $totalCheque = 0; @endphp
                        @forelse($reportData ?? [] as $index => $row)
                        @php 
                            $totalCash += $row['cash_amount']; 
                            $totalCheque += $row['cheque_amount']; 
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['slip_no'] }}</td>
                            <td>{{ $row['bank_name'] }}</td>
                            <td>{{ $row['account_no'] }}</td>
                            <td class="text-end">{{ number_format($row['cash_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['cheque_amount'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($row['cash_amount'] + $row['cheque_amount'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($reportData) && count($reportData) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Grand Total ({{ count($reportData) }} records):</td>
                            <td class="text-end">{{ number_format($totalCash, 2) }}</td>
                            <td class="text-end">{{ number_format($totalCheque, 2) }}</td>
                            <td class="text-end">{{ number_format($totalCash + $totalCheque, 2) }}</td>
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
function printReport() { 
    window.open('{{ route("admin.reports.receipt-payment.pay-in-slip") }}?print=1&' + $('#filterForm').serialize(), '_blank'); 
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') window.history.back();
    if (e.key === 'Enter') {
        e.preventDefault();
        document.querySelector('button[name="view"]').click();
    }
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.25rem 0.4rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
