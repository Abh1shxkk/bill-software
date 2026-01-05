@extends('layouts.admin')

@section('title', 'Day Check List')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.others.day-check-list') }}">
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
                        <input type="checkbox" name="day_wise" value="1" class="form-check-input" id="dayWise" {{ request('day_wise') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold ms-1" for="dayWise">Day Wise</label>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="submit" name="print" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>P</u>rint</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData))
    <div class="card">
        <div class="card-header py-1 text-center" style="background-color: #ffffcc;">
            <span class="fw-bold">Day Check List</span>
            <br>
            <small>{{ request('from_date', date('Y-m-d')) }} to {{ request('to_date', date('Y-m-d')) }}</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background-color: #ffffcc;">
                        <tr>
                            <th style="width: 50%;">Transaction</th>
                            <th class="text-end" style="width: 25%;">Value</th>
                            <th class="text-end" style="width: 25%;">No. Of Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $row)
                        <tr>
                            <td class="fw-bold" style="background-color: {{ $row['is_header'] ?? false ? '#3399ff' : '#f0f0f0' }}; color: {{ $row['is_header'] ?? false ? '#fff' : '#000' }};">
                                {{ $row['transaction'] }}
                            </td>
                            <td class="text-end" style="background-color: #f0f0f0;">{{ number_format($row['value'], 2) }}</td>
                            <td class="text-end" style="background-color: #f0f0f0;">{{ $row['count'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background-color: #e0e0e0;">
                        <tr class="fw-bold">
                            <td>Grand Total</td>
                            <td class="text-end">{{ number_format(collect($reportData)->sum('value'), 2) }}</td>
                            <td class="text-end">{{ collect($reportData)->sum('count') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No transactions found for the selected date range.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
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
