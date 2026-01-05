@extends('layouts.admin')

@section('title', 'Ledger Due List Mismatch Report')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.others.ledger-due-list-mismatch-report') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Ledger Code :</label>
                    </div>
                    <div class="col-auto">
                        <select name="ledger_code" class="form-select form-select-sm" style="width: 120px;">
                            <option value="CL" {{ request('ledger_code', 'CL') == 'CL' ? 'selected' : '' }}>CL - Customer</option>
                            <option value="SL" {{ request('ledger_code') == 'SL' ? 'selected' : '' }}>SL - Supplier</option>
                        </select>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">OK</button>
                        <button type="submit" name="print" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>P</u>rint</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData))
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center" style="background-color: #3399ff; color: #fff;">
            <span class="fw-bold">Ledger Due List Mismatch Report - {{ request('ledger_code', 'CL') == 'CL' ? 'Customer' : 'Supplier' }}</span>
            <span>Total Mismatches: {{ count($reportData) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background-color: #e0e0e0;">
                        <tr>
                            <th style="width: 50px;">S.No</th>
                            <th style="width: 80px;">CODE</th>
                            <th>PARTY NAME</th>
                            <th class="text-end" style="width: 130px;">LEDGER AMT.</th>
                            <th class="text-end" style="width: 130px;">DUE LIST AMT.</th>
                            <th class="text-end" style="width: 100px;">DIFF.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}.</td>
                            <td>{{ $row['code'] }}</td>
                            <td>{{ $row['party_name'] }}</td>
                            <td class="text-end">{{ number_format($row['ledger_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['due_list_amount'], 2) }}</td>
                            <td class="text-end {{ $row['difference'] != 0 ? 'text-danger fw-bold' : '' }}">{{ number_format($row['difference'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No mismatches found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($reportData) > 0)
                    <tfoot style="background-color: #e0e0e0;">
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">Totals:</td>
                            <td class="text-end">{{ number_format(collect($reportData)->sum('ledger_amount'), 2) }}</td>
                            <td class="text-end">{{ number_format(collect($reportData)->sum('due_list_amount'), 2) }}</td>
                            <td class="text-end text-danger">{{ number_format(collect($reportData)->sum('difference'), 2) }}</td>
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

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        $('button[name="print"]').click();
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
