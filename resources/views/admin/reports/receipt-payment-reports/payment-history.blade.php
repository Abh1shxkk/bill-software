@extends('layouts.admin')

@section('title', 'Payment History')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-dark fst-italic fw-bold" style="color: #155724 !important;">Payment History</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.receipt-payment.payment-history') }}">
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
                            <span class="input-group-text">Mode:</span>
                            <select name="payment_mode" class="form-select">
                                <option value="5" {{ request('payment_mode', '5') == '5' ? 'selected' : '' }}>All</option>
                                <option value="1" {{ request('payment_mode') == '1' ? 'selected' : '' }}>Cash</option>
                                <option value="2" {{ request('payment_mode') == '2' ? 'selected' : '' }}>Cheque</option>
                                <option value="3" {{ request('payment_mode') == '3' ? 'selected' : '' }}>RTGS</option>
                                <option value="4" {{ request('payment_mode') == '4' ? 'selected' : '' }}>NEFT</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Selection:</span>
                            <select name="selection_by" class="form-select">
                                <option value="payment" {{ request('selection_by', 'payment') == 'payment' ? 'selected' : '' }}>Payment History</option>
                                <option value="bill" {{ request('selection_by') == 'bill' ? 'selected' : '' }}>Bill History</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sort By:</span>
                            <select name="sort_by" class="form-select">
                                <option value="party" {{ request('sort_by', 'party') == 'party' ? 'selected' : '' }}>Party Name</option>
                                <option value="date" {{ request('sort_by') == 'date' ? 'selected' : '' }}>Date</option>
                                <option value="amount" {{ request('sort_by') == 'amount' ? 'selected' : '' }}>Amount</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">OK</button>
                            <button type="button" class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="bi bi-file-excel"></i>
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm">Exit</a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier:</span>
                            <select name="supplier_id" class="form-select">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ request('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 60px;">Code</th>
                            <th>Party Name</th>
                            <th style="width: 90px;">Trn. Date</th>
                            <th style="width: 80px;">Trn.No</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                            <th style="width: 70px;">P.Mode</th>
                            <th class="text-center" style="width: 50px;">Days</th>
                            <th style="width: 90px;">Bill Date</th>
                            <th style="width: 80px;">Bill No</th>
                            <th class="text-end" style="width: 100px;">Bill Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $totalAmount = 0; 
                            $totalCash = 0; 
                            $totalCheque = 0; 
                            $totalRTGS = 0; 
                            $totalNEFT = 0; 
                        @endphp
                        @forelse($reportData ?? [] as $index => $row)
                        @php 
                            $totalAmount += $row['amount'];
                            if($row['mode'] == 'Cash') $totalCash += $row['amount'];
                            if($row['mode'] == 'Cheque') $totalCheque += $row['amount'];
                            if($row['mode'] == 'RTGS') $totalRTGS += $row['amount'];
                            if($row['mode'] == 'NEFT') $totalNEFT += $row['amount'];
                        @endphp
                        <tr>
                            <td>{{ $row['code'] }}</td>
                            <td>{{ $row['party_name'] }}</td>
                            <td>{{ $row['trn_date'] }}</td>
                            <td>{{ $row['trn_no'] }}</td>
                            <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            <td>{{ $row['mode'] }}</td>
                            <td class="text-center">{{ $row['days'] ?? '' }}</td>
                            <td>{{ $row['bill_date'] ?? '' }}</td>
                            <td>{{ $row['bill_no'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($row['bill_amount'] ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "OK" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Footer Totals -->
        <div class="card-footer py-2" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
            <div class="row text-center">
                <div class="col-md-2">
                    <strong>Total:</strong> <span class="text-danger">{{ number_format($totalAmount ?? 0, 2) }}</span>
                </div>
                <div class="col-md-2">
                    <strong>Total Cash Amt:</strong> <span class="text-danger">{{ number_format($totalCash ?? 0, 2) }}</span>
                </div>
                <div class="col-md-2">
                    <strong>Total Chq. Amt:</strong> <span class="text-danger">{{ number_format($totalCheque ?? 0, 2) }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Total RTGS Amt:</strong> <span class="text-danger">{{ number_format($totalRTGS ?? 0, 2) }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Total NEFT Amt:</strong> <span class="text-danger">{{ number_format($totalNEFT ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportExcel() {
    window.location.href = '{{ route("admin.reports.receipt-payment.payment-history") }}?excel=1&' + $('#filterForm').serialize();
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
.card-footer strong { font-size: 0.75rem; }
.card-footer span { font-size: 0.8rem; }
</style>
@endpush
