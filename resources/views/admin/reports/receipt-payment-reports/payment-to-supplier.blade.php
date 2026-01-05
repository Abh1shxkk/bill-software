@extends('layouts.admin')

@section('title', 'Payment To Supplier')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-success fst-italic fw-bold">PAYMENT TO SUPPLIER</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.receipt-payment.payment-to-supplier') }}">
                <div class="row g-2">
                    <!-- Row 1: Date Range & Mode -->
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
                            <span class="input-group-text">Mode:</span>
                            <select name="payment_mode" class="form-select">
                                <option value="3" {{ request('payment_mode', '3') == '3' ? 'selected' : '' }}>Both</option>
                                <option value="1" {{ request('payment_mode') == '1' ? 'selected' : '' }}>Cash</option>
                                <option value="2" {{ request('payment_mode') == '2' ? 'selected' : '' }}>Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Group By:</span>
                            <select name="group_by" class="form-select">
                                <option value="Date" {{ request('group_by', 'Date') == 'Date' ? 'selected' : '' }}>Date</option>
                                <option value="Supplier" {{ request('group_by') == 'Supplier' ? 'selected' : '' }}>Supplier</option>
                                <option value="Amount" {{ request('group_by') == 'Amount' ? 'selected' : '' }}>Amount</option>
                            </select>
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
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Cheque No:</span>
                            <input type="text" name="cheque_no" class="form-control" value="{{ request('cheque_no') }}">
                        </div>
                    </div>

                    <!-- Row 2: Supplier -->
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
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="bi bi-file-excel me-1"></i>Excel
                            </button>
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
                            <th style="width: 100px;">Payment No</th>
                            <th>Supplier Name</th>
                            <th style="width: 80px;">Mode</th>
                            <th style="width: 120px;">Cheque No</th>
                            <th class="text-end" style="width: 120px;">Amount</th>
                            <th>Narration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAmount = 0; @endphp
                        @forelse($reportData ?? [] as $index => $row)
                        @php $totalAmount += $row['amount']; @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['payment_no'] }}</td>
                            <td>{{ $row['supplier_name'] }}</td>
                            <td>{{ $row['mode'] }}</td>
                            <td>{{ $row['cheque_no'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            <td>{{ $row['narration'] }}</td>
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
                            <td colspan="6" class="text-end">Grand Total ({{ count($reportData) }} records):</td>
                            <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
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
    window.open('{{ route("admin.reports.receipt-payment.payment-to-supplier") }}?print=1&' + $('#filterForm').serialize(), '_blank'); 
}

function exportExcel() {
    window.location.href = '{{ route("admin.reports.receipt-payment.payment-to-supplier") }}?excel=1&' + $('#filterForm').serialize();
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
