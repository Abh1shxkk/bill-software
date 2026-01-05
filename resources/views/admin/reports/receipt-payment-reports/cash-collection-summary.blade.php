@extends('layouts.admin')

@section('title', 'Cash Collection Summary')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">CASH COLLECTION SUMMARY</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.receipt-payment.cash-collection-summary') }}">
                <div class="row g-2">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date', date('Y-m-01')) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Collection Boy:</span>
                            <select name="coll_boy_id" class="form-select">
                                <option value="">All</option>
                                @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ request('coll_boy_id') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">With Details:</span>
                            <select name="with_details" class="form-select">
                                <option value="Y" {{ request('with_details', 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ request('with_details') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">User:</span>
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach($users ?? [] as $user)
                                <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
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
                            <th>Collection Boy</th>
                            <th class="text-end" style="width: 120px;">Cash Amt</th>
                            <th class="text-end" style="width: 120px;">Cheque Amt</th>
                            <th class="text-end" style="width: 120px;">Total</th>
                            <th class="text-center" style="width: 80px;">Receipts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalCash = 0; $totalCheque = 0; $totalReceipts = 0; @endphp
                        @forelse($reportData ?? [] as $index => $row)
                        @php 
                            $totalCash += $row['cash_amount']; 
                            $totalCheque += $row['cheque_amount']; 
                            $totalReceipts += $row['receipt_count'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['collection_boy'] }}</td>
                            <td class="text-end">{{ number_format($row['cash_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['cheque_amount'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($row['cash_amount'] + $row['cheque_amount'], 2) }}</td>
                            <td class="text-center">{{ $row['receipt_count'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($reportData) && count($reportData) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($totalCash, 2) }}</td>
                            <td class="text-end">{{ number_format($totalCheque, 2) }}</td>
                            <td class="text-end">{{ number_format($totalCash + $totalCheque, 2) }}</td>
                            <td class="text-center">{{ $totalReceipts }}</td>
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
    window.open('{{ route("admin.reports.receipt-payment.cash-collection-summary") }}?print=1&' + $('#filterForm').serialize(), '_blank'); 
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
