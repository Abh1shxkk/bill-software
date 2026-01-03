@extends('layouts.admin')

@section('title', 'FiFo Alteration Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: serif; letter-spacing: 1px;">FIFO ALTERATION REPORT</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="">
                <!-- From Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="{{ $fromDate ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" value="{{ $toDate ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Sort Order -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Sort Order :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="sort_order" id="sort_order" class="form-select form-select-sm">
                            <option value="1" {{ ($sortOrder ?? '1') == '1' ? 'selected' : '' }}>1.Date</option>
                            <option value="2" {{ ($sortOrder ?? '') == '2' ? 'selected' : '' }}>2.Item Name</option>
                        </select>
                    </div>
                </div>

                <!-- User -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">User :</label>
                    </div>
                    <div class="col-md-3">
                        <select name="user_id" id="user_id" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->user_id }}" {{ ($userId ?? '') == $user->user_id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 text-center">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && count($reportData) > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2">
            <strong>FiFo Alteration Report</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">Sr.</th>
                            <th>Item Name</th>
                            <th>Batch No</th>
                            <th class="text-center">Expiry</th>
                            <th class="text-end">Old Qty</th>
                            <th class="text-end">New Qty</th>
                            <th class="text-end">Difference</th>
                            <th class="text-center">Date</th>
                            <th>User</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData ?? [] as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] ?? '' }}</td>
                            <td>{{ $row['batch_no'] ?? '' }}</td>
                            <td class="text-center">{{ $row['expiry'] ?? '' }}</td>
                            <td class="text-end">{{ number_format($row['old_qty'] ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($row['new_qty'] ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($row['difference'] ?? 0, 0) }}</td>
                            <td class="text-center">{{ $row['date'] ?? '' }}</td>
                            <td>{{ $row['user_name'] ?? '' }}</td>
                            <td>{{ $row['remarks'] ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
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
    window.location.href = '{{ route("admin.reports.inventory") ?? "#" }}';
}
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.4rem; font-size: 0.8rem; vertical-align: middle; }
</style>
@endpush
