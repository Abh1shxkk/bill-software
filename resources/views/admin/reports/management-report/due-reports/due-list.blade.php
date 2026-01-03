@extends('layouts.admin')

@section('title', 'Due List')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Due List</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.due-list') }}">
                <!-- From & As On Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', '2000-04-01') }}" style="width: 140px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">As On :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="as_on_date" class="form-control form-control-sm" value="{{ request('as_on_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Party Total Required [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="party_total" class="form-control form-control-sm text-center text-uppercase" value="{{ request('party_total', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Customer/Supplier -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">C(ustomer) / S(upplier) :</label>
                    </div>
                    <div class="col-auto">
                        <select name="party_type" id="party_type" class="form-select form-select-sm" style="width: 120px;">
                            <option value="C" {{ request('party_type', 'C') == 'C' ? 'selected' : '' }}>Customer</option>
                            <option value="S" {{ request('party_type') == 'S' ? 'selected' : '' }}>Supplier</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0" id="party_label">Customer :</label>
                    </div>
                    <div class="col-md-4">
                        <select name="party_code" id="party_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                        </select>
                    </div>
                </div>

                <!-- Bill Date / Due Date / Credit Parties -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">B(ill) Date / D(ue) Date / C(redit Parties Only) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="date_type" class="form-control form-control-sm text-center text-uppercase" value="{{ request('date_type', 'B') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">D(ebit / C(redit) / A (ll) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="debit_credit" class="form-control form-control-sm text-center text-uppercase" value="{{ request('debit_credit', 'D') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Condensed Report & Radio Options -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Condenced Report [ Y/N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="condensed" class="form-control form-control-sm text-center text-uppercase" value="{{ request('condensed', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-5">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="hold_status" id="all" value="A" {{ request('hold_status', 'A') == 'A' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="all">All</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="hold_status" id="hold" value="H" {{ request('hold_status') == 'H' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="hold">Hold</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="hold_status" id="unhold" value="U" {{ request('hold_status') == 'U' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="unhold">Unhold</label>
                        </div>
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

                <!-- Day & Filter Options -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Day :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="day" class="form-control form-control-sm" value="{{ request('day') }}" style="width: 60px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Filter From Master / Duelist :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="filter_from" class="form-control form-control-sm text-center text-uppercase" value="{{ request('filter_from', 'D') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">L(ocal) / C(entral) / B(oth) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="local_central" class="form-control form-control-sm text-center text-uppercase" value="{{ request('local_central', 'B') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Date Wise / Party Wise -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">D(ate Wise) / P(arty Wise) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="date_party_wise" class="form-control form-control-sm text-center text-uppercase" value="{{ request('date_party_wise', 'P') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">N(ame) / S(equence) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="name_sequence" class="form-control form-control-sm text-center text-uppercase" value="{{ request('name_sequence', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Del.Man & Flag -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Del.Man :</label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="del_man" class="form-control form-control-sm" value="{{ request('del_man') }}" style="background-color: #e9ecef;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Flag :</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="flag" class="form-control form-control-sm" value="{{ request('flag') }}" style="background-color: #e9ecef;">
                    </div>
                </div>

                <hr class="my-2">

                <!-- Tagged / Untagged / All -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">T(agged) / U(tagged) / A(ll) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tagged_status" class="form-control form-control-sm text-center text-uppercase" value="{{ request('tagged_status', 'A') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Tag :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tag" class="form-control form-control-sm" value="{{ request('tag') }}" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Series :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="series" class="form-control form-control-sm" value="{{ request('series') }}" style="width: 50px;">
                    </div>
                </div>

                <!-- Tagged Parties & Remove Tags -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Tagged Parties Only [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tagged_parties" class="form-control form-control-sm text-center text-uppercase" value="{{ request('tagged_parties', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Remove Tags [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="remove_tags" class="form-control form-control-sm text-center text-uppercase" value="{{ request('remove_tags', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="gst_no" id="gst_no" {{ request('gst_no') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="gst_no">GSTVno</label>
                        </div>
                    </div>
                </div>

                <!-- Wholesale / Retail / Institution -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">W(hole Sale) / R(etail) / I(nstitution) / D(ept. Store) / O(thers) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="party_category" class="form-control form-control-sm text-center text-uppercase" value="{{ request('party_category') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="city" id="city" {{ request('city') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="city">City</label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-md-12 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()"><u>P</u>rint</button>
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
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Bill No</th>
                            <th>Party Name</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->transaction_date ? date('d-M-y', strtotime($item->transaction_date)) : '' }}</td>
                            <td>{{ $item->trans_no ?? '' }}</td>
                            <td>{{ $item->customer->name ?? '' }}</td>
                            <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->running_balance ?? 0, 2) }}</td>
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
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.due-reports.due-list") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).ready(function() {
    // Customer and Supplier data from controller
    var customers = @json($customers ?? []);
    var suppliers = @json($suppliers ?? []);
    var selectedPartyCode = '{{ request('party_code') }}';

    // Populate party dropdown based on party type
    function populatePartyDropdown() {
        var type = $('#party_type').val();
        var $partyDropdown = $('#party_code');
        
        // Clear existing options
        $partyDropdown.empty();
        $partyDropdown.append('<option value="">-- Select --</option>');
        
        if (type == 'C') {
            $('#party_label').text('Customer :');
            // Add customers
            customers.forEach(function(customer) {
                var selected = (selectedPartyCode == customer.id) ? 'selected' : '';
                $partyDropdown.append('<option value="' + customer.id + '" ' + selected + '>' + customer.name + '</option>');
            });
        } else if (type == 'S') {
            $('#party_label').text('Supplier :');
            // Add suppliers
            suppliers.forEach(function(supplier) {
                var selected = (selectedPartyCode == supplier.id) ? 'selected' : '';
                $partyDropdown.append('<option value="' + supplier.id + '" ' + selected + '>' + supplier.name + '</option>');
            });
        }
    }

    // Initial populate
    populatePartyDropdown();

    // On party type change
    $('#party_type').on('change', function() {
        selectedPartyCode = ''; // Reset selection when type changes
        populatePartyDropdown();
    });

    // Keyboard shortcuts
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
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
</style>
@endpush
