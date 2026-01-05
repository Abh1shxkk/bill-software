@extends('layouts.admin')

@section('title', 'User Work Summary')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">USER WORK SUMMARY</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.others.user-work-summary') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Date : <u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0"><u>T</u>o :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">E<u>x</u>it</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center" style="background-color: #ffc4d0;">
            <span class="fw-bold">User Work Summary - {{ \Carbon\Carbon::parse(request('from_date'))->format('d-M-Y') }} to {{ \Carbon\Carbon::parse(request('to_date'))->format('d-M-Y') }}</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background-color: #e0e0e0;">
                        <tr>
                            <th style="width: 40px;">S.No</th>
                            <th>User Name</th>
                            <th class="text-center" style="width: 90px;">Sales</th>
                            <th class="text-center" style="width: 90px;">Purchases</th>
                            <th class="text-center" style="width: 90px;">Sale Returns</th>
                            <th class="text-center" style="width: 90px;">Pur. Returns</th>
                            <th class="text-center" style="width: 90px;">Receipts</th>
                            <th class="text-center" style="width: 90px;">Payments</th>
                            <th class="text-center" style="width: 80px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $totalSales = 0; $totalPurchases = 0; $totalSaleReturns = 0;
                            $totalPurchaseReturns = 0; $totalReceipts = 0; $totalPayments = 0; $grandTotal = 0;
                        @endphp
                        @foreach($reportData as $index => $row)
                        @php 
                            $totalSales += $row['sales'];
                            $totalPurchases += $row['purchases'];
                            $totalSaleReturns += $row['sale_returns'];
                            $totalPurchaseReturns += $row['purchase_returns'];
                            $totalReceipts += $row['receipts'];
                            $totalPayments += $row['payments'];
                            $grandTotal += $row['total'];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['user_name'] }}</td>
                            <td class="text-center">{{ $row['sales'] }}</td>
                            <td class="text-center">{{ $row['purchases'] }}</td>
                            <td class="text-center">{{ $row['sale_returns'] }}</td>
                            <td class="text-center">{{ $row['purchase_returns'] }}</td>
                            <td class="text-center">{{ $row['receipts'] }}</td>
                            <td class="text-center">{{ $row['payments'] }}</td>
                            <td class="text-center fw-bold">{{ $row['total'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background-color: #e0e0e0;">
                        <tr class="fw-bold">
                            <td colspan="2" class="text-end">Totals:</td>
                            <td class="text-center">{{ $totalSales }}</td>
                            <td class="text-center">{{ $totalPurchases }}</td>
                            <td class="text-center">{{ $totalSaleReturns }}</td>
                            <td class="text-center">{{ $totalPurchaseReturns }}</td>
                            <td class="text-center">{{ $totalReceipts }}</td>
                            <td class="text-center">{{ $totalPayments }}</td>
                            <td class="text-center">{{ $grandTotal }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No work records found for the selected period.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.others.user-work-summary") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'f') {
        e.preventDefault();
        $('input[name="from_date"]').focus();
    }
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'x') {
        e.preventDefault();
        closeWindow();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.3rem 0.5rem; font-size: 0.85rem; border: 1px solid #999; }
</style>
@endpush
