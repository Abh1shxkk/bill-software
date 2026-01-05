@extends('layouts.admin')

@section('title', 'CL/SL Date Wise Ledger Summary')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">CUSTOMER / SUPPLIER - DATE WISE LEDGER SUMMARY</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.others.cl-sl-date-wise-ledger-summary') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
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
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">C(ustomer) / S(upplier) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('type', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center" style="background-color: #ffc4d0;">
            <span class="fw-bold">{{ request('type', 'C') == 'C' ? 'Customer' : 'Supplier' }} Date Wise Ledger Summary - {{ count($reportData) }} records</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background-color: #e0e0e0;">
                        <tr>
                            <th style="width: 40px;">S.No</th>
                            <th style="width: 100px;">Date</th>
                            <th class="text-end" style="width: 120px;">Opening</th>
                            <th class="text-end" style="width: 120px;">Debit</th>
                            <th class="text-end" style="width: 120px;">Credit</th>
                            <th class="text-end" style="width: 120px;">Closing</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalDebit = 0; $totalCredit = 0; @endphp
                        @foreach($reportData as $index => $row)
                        @php 
                            $totalDebit += $row['debit']; 
                            $totalCredit += $row['credit'];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($row['closing'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background-color: #e0e0e0;">
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">Totals:</td>
                            <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                            <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                            <td class="text-end">{{ number_format($reportData[count($reportData) - 1]['closing'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No ledger transactions found for the selected period.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.others.cl-sl-date-wise-ledger-summary") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
    if (e.altKey && e.key.toLowerCase() === 'c') {
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
