@extends('layouts.admin')

@section('title', 'Due List Summary')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">DUE LIST SUMMARY</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.due-list-summary') }}">
                <input type="hidden" name="view" value="1">
                <!-- Due List As On & Customer/Supplier -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Due List As On :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="as_on_date" class="form-control form-control-sm" value="{{ request('as_on_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">C/S :</label>
                    </div>
                    <div class="col-auto">
                        <select name="party_type" id="party_type" class="form-select form-select-sm" style="width: 110px;">
                            <option value="C" {{ request('party_type', 'C') == 'C' ? 'selected' : '' }}>Customer</option>
                            <option value="S" {{ request('party_type') == 'S' ? 'selected' : '' }}>Supplier</option>
                        </select>
                    </div>
                </div>

                <!-- Sales Man -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Sales Man :</label>
                    </div>
                    <div class="col-md-5">
                        <select name="salesman_code" id="salesman_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ request('salesman_code') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Area -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Area :</label>
                    </div>
                    <div class="col-md-5">
                        <select name="area_code" id="area_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ request('area_code') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Route -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Route :</label>
                    </div>
                    <div class="col-md-5">
                        <select name="route_code" id="route_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ request('route_code') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- State -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">State :</label>
                    </div>
                    <div class="col-md-5">
                        <select name="state_code" id="state_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($states ?? [] as $state)
                                <option value="{{ $state->id }}" {{ request('state_code') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Day, Series, Status -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Day</label>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">:</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="day" class="form-control form-control-sm" value="{{ request('day') }}" style="width: 60px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Series :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="series" class="form-control form-control-sm" value="{{ request('series') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Status :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="status" class="form-control form-control-sm" value="{{ request('status') }}" style="width: 60px;">
                    </div>
                </div>

                <!-- Group By -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Group By S(man) / A(rea) / R(oute) / P(arty):</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="group_by" class="form-control form-control-sm text-center text-uppercase" value="{{ request('group_by', 'P') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Balance & Bill Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Balance D(ebit) / A(ll) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="balance_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('balance_type', 'D') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">B(ill Date) / D(ue Date) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="date_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('date_type', 'B') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Tagged Party Only -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Tagged Party Only</label>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">:</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tagged_party" class="form-control form-control-sm text-center text-uppercase" value="{{ request('tagged_party', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <hr class="my-2" style="border-top: 2px solid #000;">

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()"><u>P</u>rint</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view'))
    <div class="card mt-2">
        <div class="card-body p-2">
            @if(isset($reportData) && $reportData->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Code</th>
                            <th>Party Name</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalBalance = 0; @endphp
                        @foreach($reportData as $index => $item)
                        @php $totalBalance += $item->balance ?? 0; @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->code ?? $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td class="text-end">{{ number_format($item->balance ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-end">{{ number_format($totalBalance, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-2 text-muted">Total Records: {{ $reportData->count() }}</div>
            @else
            <div class="alert alert-info mb-0">No records found.</div>
            @endif
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
    window.open('{{ route("admin.reports.management.due-reports.due-list-summary") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).ready(function() {
    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.altKey && e.key.toLowerCase() === 'v') {
            e.preventDefault();
            $('button[name="view"]').click();
        }
        if (e.altKey && e.key.toLowerCase() === 'p') {
            e.preventDefault();
            printReport();
        }
        if (e.altKey && e.key.toLowerCase() === 'c') {
            e.preventDefault();
            closeWindow();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
</style>
@endpush
