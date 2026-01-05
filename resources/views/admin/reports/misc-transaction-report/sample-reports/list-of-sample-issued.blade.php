@extends('layouts.admin')
@section('title', 'List of Sample Issue')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">List of Sample Issue</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.misc-transaction.sample-reports.list-of-sample-issued') }}">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0"><u>F</u>rom :</label></div>
                    <div class="col-auto"><input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;"></div>
                    <div class="col-auto"><label class="fw-bold mb-0">To :</label></div>
                    <div class="col-auto"><input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;"></div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0">Selective :</label></div>
                    <div class="col-auto">
                        <input type="text" name="selective" class="form-control form-control-sm text-uppercase" value="{{ request('selective', 'Y') }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0">Select</label></div>
                    <div class="col-auto">
                        <select name="select_type" id="select_type" class="form-select form-select-sm" style="width: 180px; height: 160px;" size="7">
                            <option value="Customer" {{ request('select_type', 'Customer') == 'Customer' ? 'selected' : '' }}>Customer</option>
                            <option value="Doctor" {{ request('select_type') == 'Doctor' ? 'selected' : '' }}>Doctor</option>
                            <option value="SALES MAN" {{ request('select_type') == 'SALES MAN' ? 'selected' : '' }}>SALES MAN</option>
                            <option value="AREA MGR." {{ request('select_type') == 'AREA MGR.' ? 'selected' : '' }}>AREA MGR.</option>
                            <option value="REG.MGR." {{ request('select_type') == 'REG.MGR.' ? 'selected' : '' }}>REG.MGR.</option>
                            <option value="MKT.MGR." {{ request('select_type') == 'MKT.MGR.' ? 'selected' : '' }}>MKT.MGR.</option>
                            <option value="GEN.MGR." {{ request('select_type') == 'GEN.MGR.' ? 'selected' : '' }}>GEN.MGR.</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0">Name</label></div>
                    <div class="col">
                        <select name="name_id" id="name_select" class="form-select form-select-sm">
                            <option value="">Select an option</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto"><label class="fw-bold mb-0">With Details :</label></div>
                    <div class="col-auto">
                        <input type="text" name="with_details" class="form-control form-control-sm text-uppercase" value="{{ request('with_details', 'N') }}" style="width: 40px;" maxlength="1">
                    </div>
                    <div class="col-auto ms-4"><label class="fw-bold mb-0">Location :</label></div>
                    <div class="col-auto">
                        <input type="text" name="location" class="form-control form-control-sm" value="{{ request('location') }}" style="width: 120px;">
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-12">
                        <label class="fw-bold mb-0">1. Bill-Party-Item / 2. Item-Party-Bill / 3.Item BillWise / 4.Party BillWise :</label>
                        <input type="text" name="report_type" class="form-control form-control-sm text-uppercase d-inline-block ms-2" value="{{ request('report_type', '1') }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-12">
                        <label class="fw-bold mb-0">1. Sale / 2. W.Sale / 3. Spl. Rate / 4. Pur.Rate / 5.Cost / 6. MRP :</label>
                        <input type="text" name="rate_type" class="form-control form-control-sm text-uppercase d-inline-block ms-2" value="{{ request('rate_type', '1') }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()"><u>P</u>rint</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportExcel()"><u>E</u>xcel</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && $reportData->count() > 0)
    <div class="card mt-2">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr><th class="text-center">S.No</th><th>Date</th><th>Voucher No</th><th>Issued To</th><th>Item Name</th><th class="text-end">Qty</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->date ? date('d-M-y', strtotime($item->date)) : '' }}</td>
                            <td>{{ $item->voucher_no ?? '' }}</td>
                            <td>{{ $item->issued_to ?? '' }}</td>
                            <td>{{ $item->item_name ?? '' }}</td>
                            <td class="text-end">{{ number_format($item->qty ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="card mt-2">
        <div class="card-body p-3 text-center">
            <p class="mb-0 text-muted">No records found for the selected criteria.</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
var masterData = {
    'Customer': @json($customers->map(fn($c) => ['id' => $c->customer_id, 'name' => $c->name])),
    'Doctor': @json($doctors->map(fn($d) => ['id' => $d->id, 'name' => $d->name])),
    'SALES MAN': @json($salesmen->map(fn($s) => ['id' => $s->salesman_id, 'name' => $s->name])),
    'AREA MGR.': @json($areaManagers->map(fn($a) => ['id' => $a->id, 'name' => $a->name])),
    'REG.MGR.': @json($regionalManagers->map(fn($r) => ['id' => $r->id, 'name' => $r->name])),
    'MKT.MGR.': @json($marketingManagers->map(fn($m) => ['id' => $m->id, 'name' => $m->name])),
    'GEN.MGR.': @json($generalManagers->map(fn($g) => ['id' => $g->id, 'name' => $g->name]))
};

function loadNameOptions(type) {
    var select = $('#name_select');
    select.empty().append('<option value="">All</option>');
    if (masterData[type]) {
        masterData[type].forEach(function(item) {
            select.append('<option value="' + item.id + '">' + item.name + '</option>');
        });
    }
}

$(document).ready(function() {
    loadNameOptions($('#select_type').val() || 'Customer');
    @if(request('name_id'))
    $('#name_select').val('{{ request('name_id') }}');
    @endif
});

$('#select_type').on('change', function() {
    loadNameOptions($(this).val());
});

function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }
function printReport() { window.open('{{ route("admin.reports.misc-transaction.sample-reports.list-of-sample-issued") }}?print=1&' + $('#filterForm').serialize(), '_blank'); }
function exportExcel() { window.location.href = '{{ route("admin.reports.misc-transaction.sample-reports.list-of-sample-issued") }}?excel=1&' + $('#filterForm').serialize(); }
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
</style>
@endpush
