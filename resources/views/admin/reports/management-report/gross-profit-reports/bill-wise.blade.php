@extends('layouts.admin')

@section('title', 'Gross Profit - Bill Wise')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Gross Profit - Bill Wise</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.gross-profit.bill-wise') }}">
                <!-- From & To Date, Series, GP on -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Series :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="series" class="form-control form-control-sm text-uppercase" value="{{ request('series', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">GP on - S(rate) / P(rate) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="gp_on" class="form-control form-control-sm text-center text-uppercase" value="{{ request('gp_on', 'S') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- W/R/I/D/O & Tax/Retail -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">W(hole Sale) / R(etail) / I(nstitution) / D(ept. Store) / O(ther) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="party_category" class="form-control form-control-sm text-center text-uppercase" value="{{ request('party_category') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">T(ax) / R(etail) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tax_retail" class="form-control form-control-sm text-center text-uppercase" value="{{ request('tax_retail') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Detailed/Summarised -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">D(etailed) / S(ummerised) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="report_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('report_type', 'D') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- With BE, Negative, Tagged Parties, Remove Tags -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Tagged Parties [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tagged_parties" class="form-control form-control-sm text-center text-uppercase" value="{{ request('tagged_parties', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-2">
                        <label class="fw-bold mb-0">Remove Tags [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="remove_tags" class="form-control form-control-sm text-center text-uppercase" value="{{ request('remove_tags', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">With BE [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="with_be" class="form-control form-control-sm text-center text-uppercase" value="{{ request('with_be', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Negative [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="negative" class="form-control form-control-sm text-center text-uppercase" value="{{ request('negative', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Sales Man -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Sales Man</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="salesman_code" class="form-control form-control-sm" value="{{ request('salesman_code', '00') }}" style="width: 60px;">
                    </div>
                    <div class="col-md-5">
                        <select name="salesman_id" id="salesman_id" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ request('salesman_id') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Area -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Area</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="area_code" class="form-control form-control-sm" value="{{ request('area_code', '00') }}" style="width: 60px;">
                    </div>
                    <div class="col-md-5">
                        <select name="area_id" id="area_id" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Route -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Route</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="route_code" class="form-control form-control-sm" value="{{ request('route_code', '00') }}" style="width: 60px;">
                    </div>
                    <div class="col-md-5">
                        <select name="route_id" id="route_id" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Day & GP % -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Day :</label>
                    </div>
                    <div class="col-md-2">
                        <select name="day" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            <option value="Monday" {{ request('day') == 'Monday' ? 'selected' : '' }}>Monday</option>
                            <option value="Tuesday" {{ request('day') == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                            <option value="Wednesday" {{ request('day') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                            <option value="Thursday" {{ request('day') == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                            <option value="Friday" {{ request('day') == 'Friday' ? 'selected' : '' }}>Friday</option>
                            <option value="Saturday" {{ request('day') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                            <option value="Sunday" {{ request('day') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                        </select>
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">GP (%) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="gp_percent" class="form-control form-control-sm" value="{{ request('gp_percent') }}" style="width: 100px;">
                    </div>
                </div>

                <!-- Sort By & Order -->
                <div class="row g-2 mb-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Sort By :</label>
                    </div>
                    <div class="col-auto">
                        <select name="sort_by" class="form-select form-select-sm" style="width: 150px;">
                            <option value="date" {{ request('sort_by', 'date') == 'date' ? 'selected' : '' }}>Date</option>
                            <option value="invoice_no" {{ request('sort_by') == 'invoice_no' ? 'selected' : '' }}>Invoice No</option>
                            <option value="customer" {{ request('sort_by') == 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="gp_amount" {{ request('sort_by') == 'gp_amount' ? 'selected' : '' }}>GP Amount</option>
                            <option value="gp_percent" {{ request('sort_by') == 'gp_percent' ? 'selected' : '' }}>GP %</option>
                        </select>
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Order :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="order" id="asc" value="asc" {{ request('order', 'asc') == 'asc' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="asc">Asc</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="order" id="desc" value="desc" {{ request('order') == 'desc' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="desc">Desc</label>
                        </div>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center">
            <span class="fw-bold">Report Results</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">S.No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Inv No</th>
                            <th>Customer Name</th>
                            <th class="text-end">Sale Amt</th>
                            <th class="text-end">Pur Amt</th>
                            <th class="text-end">GP Amt</th>
                            <th class="text-end">GP %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalSale = 0;
                            $totalPurchase = 0;
                            $totalGP = 0;
                        @endphp
                        @foreach($reportData as $index => $row)
                        @php
                            $totalSale += $row['sale_amount'];
                            $totalPurchase += $row['purchase_amount'];
                            $totalGP += $row['gp_amount'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($row['sale_date'])->format('d-M-y') }}</td>
                            <td class="text-center">{{ $row['invoice_no'] }}</td>
                            <td>{{ $row['customer_name'] }}</td>
                            <td class="text-end">{{ number_format($row['sale_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['purchase_amount'], 2) }}</td>
                            <td class="text-end {{ $row['gp_amount'] < 0 ? 'text-danger' : '' }}">{{ number_format($row['gp_amount'], 2) }}</td>
                            <td class="text-end {{ $row['gp_percent'] < 0 ? 'text-danger' : '' }}">{{ number_format($row['gp_percent'], 2) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totalSale, 2) }}</td>
                            <td class="text-end">{{ number_format($totalPurchase, 2) }}</td>
                            <td class="text-end {{ $totalGP < 0 ? 'text-danger' : '' }}">{{ number_format($totalGP, 2) }}</td>
                            <td class="text-end {{ ($totalSale > 0 ? ($totalGP / $totalSale * 100) : 0) < 0 ? 'text-danger' : '' }}">{{ $totalSale > 0 ? number_format($totalGP / $totalSale * 100, 2) : '0.00' }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No records found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.gross-profit.bill-wise") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
</style>
@endpush
