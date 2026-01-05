@extends('layouts.admin')

@section('title', 'Currency Detail')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e2d5f7 0%, #d4c4f0 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #6f42c1 !important;">Currency Detail</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.receipt-payment.currency-detail') }}">
                <div class="row g-2 justify-content-center">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Date:</span>
                            <input type="date" name="report_date" class="form-control" value="{{ request('report_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>OK
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

    <!-- Currency Denominations Table -->
    @if(request()->has('view'))
    <div class="card shadow-sm">
        <div class="card-header py-1" style="background: linear-gradient(135deg, #e2d5f7 0%, #d4c4f0 100%);">
            <span class="fw-bold" style="color: #6f42c1;">Currency Detail for {{ \Carbon\Carbon::parse(request('report_date', date('Y-m-d')))->format('d-M-Y') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Denomination</th>
                            <th class="text-center" style="width: 120px;">Count</th>
                            <th class="text-end" style="width: 150px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $denominations = [
                                ['value' => 2000, 'label' => '₹2000 Notes'],
                                ['value' => 500, 'label' => '₹500 Notes'],
                                ['value' => 200, 'label' => '₹200 Notes'],
                                ['value' => 100, 'label' => '₹100 Notes'],
                                ['value' => 50, 'label' => '₹50 Notes'],
                                ['value' => 20, 'label' => '₹20 Notes'],
                                ['value' => 10, 'label' => '₹10 Notes'],
                                ['value' => 5, 'label' => '₹5 Coins'],
                                ['value' => 2, 'label' => '₹2 Coins'],
                                ['value' => 1, 'label' => '₹1 Coins'],
                            ];
                            $totalAmount = 0;
                        @endphp
                        @foreach($denominations as $index => $denom)
                        @php 
                            $count = $reportData[$denom['value']] ?? 0;
                            $amount = $denom['value'] * $count;
                            $totalAmount += $amount;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $denom['label'] }}</td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm text-center" 
                                       value="{{ $count }}" readonly style="width: 80px; margin: auto;">
                            </td>
                            <td class="text-end fw-bold">{{ number_format($amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Grand Total:</td>
                            <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="card shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-cash-coin fs-1 d-block mb-2"></i>
            Select a date and click "OK" to view currency details
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function printReport() { 
    window.open('{{ route("admin.reports.receipt-payment.currency-detail") }}?print=1&' + $('#filterForm').serialize(), '_blank'); 
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
</style>
@endpush
