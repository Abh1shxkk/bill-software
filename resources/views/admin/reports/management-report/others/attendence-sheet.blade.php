@extends('layouts.admin')

@section('title', 'Attendance Sheet')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">ATTENDENCE SHEET</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.others.attendence-sheet') }}">
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
                        <label class="fw-bold mb-0">S(elective) / A (ll) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="selection_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('selection_type', 'A') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Select User</label>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">:</label>
                    </div>
                    <div class="col-auto">
                        <select name="user_id" class="form-select form-select-sm" style="width: 200px;" {{ request('selection_type', 'A') == 'A' ? 'disabled' : '' }}>
                            <option value="">-- All Users --</option>
                            @foreach($users ?? [] as $user)
                            <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                            @endforeach
                        </select>
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
            <span class="fw-bold">Attendance Sheet - {{ \Carbon\Carbon::parse(request('from_date'))->format('d-M-Y') }} to {{ \Carbon\Carbon::parse(request('to_date'))->format('d-M-Y') }}</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background-color: #e0e0e0;">
                        <tr>
                            <th style="width: 50px;">S.No</th>
                            <th>User Name</th>
                            <th style="width: 100px;">Date</th>
                            <th style="width: 100px;">In Time</th>
                            <th style="width: 100px;">Out Time</th>
                            <th style="width: 80px;">Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['user_name'] }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['in_time'] }}</td>
                            <td>{{ $row['out_time'] }}</td>
                            <td class="{{ $row['status'] == 'Present' ? 'text-success' : ($row['status'] == 'Absent' ? 'text-danger' : '') }} fw-bold">{{ $row['status'] }}</td>
                            <td>{{ $row['remarks'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Summary Section --}}
    @if(isset($summary) && count($summary) > 0)
    <div class="card mt-2">
        <div class="card-header py-1" style="background-color: #e0e0e0;">
            <span class="fw-bold">Attendance Summary</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th class="text-center">Total Days</th>
                            <th class="text-center text-success">Present</th>
                            <th class="text-center text-danger">Absent</th>
                            <th class="text-center">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summary as $row)
                        <tr>
                            <td>{{ $row['user_name'] }}</td>
                            <td class="text-center">{{ $row['total_days'] }}</td>
                            <td class="text-center text-success">{{ $row['present'] }}</td>
                            <td class="text-center text-danger">{{ $row['absent'] }}</td>
                            <td class="text-center">{{ number_format($row['percentage'], 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No attendance records found for the selected period.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.others.attendence-sheet") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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

// Toggle user dropdown based on selection type
$('input[name="selection_type"]').on('change keyup', function() {
    var val = $(this).val().toUpperCase();
    if (val === 'S') {
        $('select[name="user_id"]').prop('disabled', false);
    } else {
        $('select[name="user_id"]').prop('disabled', true).val('');
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
