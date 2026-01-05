@extends('layouts.admin')

@section('title', 'List of Returned Cheques')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">LIST OF RETURNED CHEQUES</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.receipt-payment.returned-cheques') }}">
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
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Order By:</span>
                            <select name="order_by" class="form-select">
                                <option value="D" {{ request('order_by', 'D') == 'D' ? 'selected' : '' }}>Date</option>
                                <option value="P" {{ request('order_by') == 'P' ? 'selected' : '' }}>Party</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="printReport()">
                                <i class="bi bi-printer me-1"></i>Print (F7)
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
                            <th style="width: 100px;">CHQ.DATE</th>
                            <th style="width: 120px;">CHQ.NO</th>
                            <th style="width: 60px;">CODE</th>
                            <th>PARTY NAME</th>
                            <th class="text-end" style="width: 110px;">CHQ.AMT</th>
                            <th style="width: 100px;">RTD.DATE</th>
                            <th class="text-end" style="width: 90px;">CHARGES</th>
                            <th style="width: 120px;">REASON</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAmount = 0; $totalCharges = 0; @endphp
                        @forelse($reportData ?? [] as $index => $row)
                        @php $totalAmount += $row['amount']; $totalCharges += $row['charges'] ?? 0; @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['cheque_date'] }}</td>
                            <td>{{ $row['cheque_no'] }}</td>
                            <td>{{ $row['code'] }}</td>
                            <td>{{ $row['party_name'] }}</td>
                            <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            <td>{{ $row['return_date'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($row['charges'] ?? 0, 2) }}</td>
                            <td>{{ $row['reason'] ?? '' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
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
                            <td class="text-end text-danger">{{ number_format($totalAmount, 2) }}</td>
                            <td></td>
                            <td class="text-end text-danger">{{ number_format($totalCharges, 2) }}</td>
                            <td></td>
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
    window.open('{{ route("admin.reports.receipt-payment.returned-cheques") }}?print=1&' + $('#filterForm').serialize(), '_blank'); 
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') window.history.back();
    if (e.key === 'F7') {
        e.preventDefault();
        printReport();
    }
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
