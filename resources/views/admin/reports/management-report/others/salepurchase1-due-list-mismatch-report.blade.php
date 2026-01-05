@extends('layouts.admin')

@section('title', 'Sale/Purchase Due List Mismatch Report')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.others.salepurchase1-due-list-mismatch-report') }}">
                <div class="row g-2 align-items-center">
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
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">C(ustomer) / S(upplier) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('type', 'C') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">OK</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData))
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center" style="background-color: #e0e0e0;">
            <span class="fw-bold">{{ request('type', 'C') == 'C' ? 'Customer' : 'Supplier' }} Due List - {{ count($reportData) }} records</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background-color: #e0e0e0;">
                        <tr>
                            <th style="width: 40px;">S.No</th>
                            <th style="width: 90px;">DATE</th>
                            <th style="width: 90px;">TRN.NO</th>
                            <th style="width: 70px;">CODE</th>
                            <th>PARTY NAME</th>
                            <th class="text-end" style="width: 100px;">TRN. AMT.</th>
                            <th class="text-end" style="width: 100px;">ADJ. AMT.</th>
                            <th class="text-end" style="width: 100px;">O/S AMT.</th>
                            <th class="text-end" style="width: 100px;">DUE AMT.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $totalTrn = 0; $totalAdj = 0; $totalOs = 0; $totalDue = 0;
                        @endphp
                        @forelse($reportData as $index => $row)
                        @php 
                            $totalTrn += $row['trn_amount']; 
                            $totalAdj += $row['adj_amount'];
                            $totalOs += $row['os_amount'];
                            $totalDue += $row['due_amount'];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}.</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['trn_no'] }}</td>
                            <td>{{ $row['code'] }}</td>
                            <td>{{ $row['party_name'] }}</td>
                            <td class="text-end">{{ number_format($row['trn_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['adj_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['os_amount'], 2) }}</td>
                            <td class="text-end {{ $row['due_amount'] != $row['os_amount'] ? 'text-danger fw-bold' : '' }}">{{ number_format($row['due_amount'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($reportData) > 0)
                    <tfoot style="background-color: #e0e0e0;">
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end">Totals:</td>
                            <td class="text-end">{{ number_format($totalTrn, 2) }}</td>
                            <td class="text-end">{{ number_format($totalAdj, 2) }}</td>
                            <td class="text-end">{{ number_format($totalOs, 2) }}</td>
                            <td class="text-end">{{ number_format($totalDue, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.others.salepurchase1-due-list-mismatch-report") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'f') {
        e.preventDefault();
        $('input[name="from_date"]').focus();
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
