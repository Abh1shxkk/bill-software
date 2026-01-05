@extends('layouts.admin')

@section('title', 'List of Master Modifications')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">List of Master Modifications</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.others.list-of-master-modifications') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="
                        {{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
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
                        <label class="fw-bold mb-0"><u>T</u>ype :</label>
                    </div>
                    <div class="col-auto">
                        <select name="type" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">-- Select Type --</option>
                            <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Customer</option>
                            <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Supplier</option>
                            <option value="3" {{ request('type') == '3' ? 'selected' : '' }}>Item</option>
                            <option value="4" {{ request('type') == '4' ? 'selected' : '' }}>Company</option>
                            <option value="5" {{ request('type') == '5' ? 'selected' : '' }}>Salesman</option>
                            <option value="6" {{ request('type', '6') == '6' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">1. Customer / 2. Supplier / 3. Item / 4. Company / 5. Salesman / 6. All :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="type_code" class="form-control form-control-sm text-center" value="{{ request('type_code', '6') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportExcel()">Excel</button>
                        <span class="float-end">
                            <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                            <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center" style="background-color: #ffc4d0;">
            <span class="fw-bold">List of Master Modifications - {{ count($reportData) }} records</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead style="background-color: #e0e0e0;">
                        <tr>
                            <th style="width: 40px;">S.No</th>
                            <th style="width: 80px;">Type</th>
                            <th style="width: 80px;">Code</th>
                            <th>Name</th>
                            <th style="width: 80px;">Action</th>
                            <th style="width: 100px;">Modified By</th>
                            <th style="width: 130px;">Modified At</th>
                            <th>Changed Fields</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['type'] }}</td>
                            <td>{{ $row['code'] }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td class="{{ $row['action'] == 'Deleted' ? 'text-danger' : ($row['action'] == 'Created' ? 'text-success' : 'text-warning') }} fw-bold">{{ $row['action'] }}</td>
                            <td>{{ $row['modified_by'] }}</td>
                            <td>{{ $row['modified_at'] }}</td>
                            <td>{{ $row['changed_fields'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No master modifications found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.others.list-of-master-modifications") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

function exportExcel() {
    window.location.href = '{{ route("admin.reports.management.others.list-of-master-modifications") }}?excel=1&' + $('#filterForm').serialize();
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

// Sync type dropdown with type_code input
$('select[name="type"]').on('change', function() {
    $('input[name="type_code"]').val($(this).val());
});

$('input[name="type_code"]').on('change keyup', function() {
    var val = $(this).val();
    if (val >= 1 && val <= 6) {
        $('select[name="type"]').val(val);
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
