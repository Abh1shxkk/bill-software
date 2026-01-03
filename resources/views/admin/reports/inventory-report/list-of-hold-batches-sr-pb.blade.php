@extends('layouts.admin')

@section('title', 'List of Hold Batches (SR,PB)')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">LIST OF HOLD BATCHES (SR,PB)</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="">
                <!-- From / To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">From :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="{{ $fromDate ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" value="{{ $toDate ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Manual Hold -->
                <div class="row g-2 mb-2 align-items-center justify-content-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Manual Hold :</label>
                    </div>
                    <div class="col-auto">
                        <select name="manual_hold" id="manual_hold" class="form-select form-select-sm" style="width: 60px;">
                            <option value="N" {{ ($manualHold ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                            <option value="Y" {{ ($manualHold ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                        </select>
                    </div>
                </div>

                <!-- 1.Sale Return / 2. Purchase -->
                <div class="row g-2 mb-2 align-items-center justify-content-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">1.Sale Return / 2. Purchase :</label>
                    </div>
                    <div class="col-auto">
                        <select name="sr_pb_type" id="sr_pb_type" class="form-select form-select-sm" style="width: 60px;">
                            <option value="1" {{ ($srPbType ?? '1') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($srPbType ?? '') == '2' ? 'selected' : '' }}>2</option>
                        </select>
                    </div>
                </div>

                <!-- (C)ustomer / (S)upplier -->
                <div class="row g-2 mb-2 align-items-center justify-content-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">(<u>C</u>)ustomer / (<u>S</u>)upplier :</label>
                    </div>
                    <div class="col-auto">
                        <select name="cs_type" id="cs_type" class="form-select form-select-sm" style="width: 60px;">
                            <option value="C" {{ ($csType ?? 'C') == 'C' ? 'selected' : '' }}>C</option>
                            <option value="S" {{ ($csType ?? '') == 'S' ? 'selected' : '' }}>S</option>
                        </select>
                    </div>
                </div>

                <!-- Party Name -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Party Name :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="party_code" class="form-control form-control-sm" value="{{ $partyCode ?? '00' }}" style="width: 80px;">
                    </div>
                    <div class="col-md-6">
                        <select name="party_id" id="party_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($parties ?? [] as $party)
                                <option value="{{ $party->id }}" {{ ($partyId ?? '') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">E<u>x</u>cel</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData) && count($reportData) > 0)
    <div class="card mt-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center">Sr.</th>
                            <th>Party Name</th>
                            <th>Item Name</th>
                            <th>Batch No</th>
                            <th class="text-center">Expiry</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">MRP</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['party_name'] ?? '' }}</td>
                            <td>{{ $row['item_name'] ?? '' }}</td>
                            <td>{{ $row['batch_no'] ?? '' }}</td>
                            <td class="text-center">{{ $row['expiry'] ?? '' }}</td>
                            <td class="text-end">{{ $row['qty'] ?? 0 }}</td>
                            <td class="text-end">{{ number_format($row['mrp'] ?? 0, 2) }}</td>
                            <td>{{ $row['type'] ?? '' }}</td>
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
function exportToExcel() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.set('export', 'excel');
    window.location.href = '{{ url()->current() }}?' + params.toString();
}

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
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.8rem; }
</style>
@endpush
