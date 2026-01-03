@extends('layouts.admin')

@section('title', 'Ageing Analysis (Account Ledger)')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">AGEING ANALYSIS (ACCOUNT LEDGER)</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.ageing-analysis-account-ledger') }}">
                <input type="hidden" name="view" value="1">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-7">
                        <!-- Enter Date -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto">
                                <label class="fw-bold mb-0">Enter Date :</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="enter_date" class="form-control form-control-sm" value="{{ request('enter_date', date('Y-m-d')) }}" style="width: 140px;">
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

                        <!-- C/S and Party Code -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto">
                                <label class="fw-bold mb-0">C/S :</label>
                            </div>
                            <div class="col-auto">
                                <select name="party_type" id="party_type" class="form-select form-select-sm" style="width: 110px;">
                                    <option value="C" {{ request('party_type', 'C') == 'C' ? 'selected' : '' }}>Customer</option>
                                    <option value="S" {{ request('party_type') == 'S' ? 'selected' : '' }}>Supplier</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="fw-bold mb-0" id="party_label">Customer :</label>
                            </div>
                            <div class="col-md-5">
                                <select name="party_code" id="party_code" class="form-select form-select-sm">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Flag -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-md-2">
                                <label class="fw-bold mb-0">Flag :</label>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="flag" class="form-control form-control-sm" value="{{ request('flag') }}" style="background-color: #e9ecef;">
                            </div>
                        </div>

                        <!-- Show Credit Balances -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto">
                                <label class="fw-bold mb-0">Show Credit Balances :</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="show_credit" class="form-control form-control-sm text-center text-uppercase" value="{{ request('show_credit', 'N') }}" maxlength="1" style="width: 40px;">
                            </div>
                        </div>

                        <!-- Bill Wise Checkbox -->
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto ms-auto">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="bill_wise" id="bill_wise" {{ request('bill_wise') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="bill_wise">Bill Wise</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Day Slabs -->
                    <div class="col-md-5">
                        <div class="mb-2">
                            <label class="fw-bold mb-0">Day Slabs :</label>
                        </div>
                        <div class="row g-1 mb-1 align-items-center">
                            <div class="col-auto text-end" style="width: 50px;">0-</div>
                            <div class="col-auto">
                                <input type="number" name="slab1" class="form-control form-control-sm text-end" value="{{ request('slab1', '30') }}" style="width: 70px;">
                            </div>
                        </div>
                        <div class="row g-1 mb-1 align-items-center">
                            <div class="col-auto text-end" style="width: 50px;">31-</div>
                            <div class="col-auto">
                                <input type="number" name="slab2" class="form-control form-control-sm text-end" value="{{ request('slab2', '60') }}" style="width: 70px;">
                            </div>
                        </div>
                        <div class="row g-1 mb-1 align-items-center">
                            <div class="col-auto text-end" style="width: 50px;">61-</div>
                            <div class="col-auto">
                                <input type="number" name="slab3" class="form-control form-control-sm text-end" value="{{ request('slab3', '90') }}" style="width: 70px;">
                            </div>
                        </div>
                        <div class="row g-1 mb-1 align-items-center">
                            <div class="col-auto text-end" style="width: 50px;">91-</div>
                            <div class="col-auto">
                                <input type="number" name="slab4" class="form-control form-control-sm text-end" value="{{ request('slab4', '120') }}" style="width: 70px;">
                            </div>
                        </div>
                        <div class="row g-1 mb-1 align-items-center">
                            <div class="col-auto text-end" style="width: 50px;">121-</div>
                            <div class="col-auto">
                                <input type="number" name="slab5" class="form-control form-control-sm text-end" value="{{ request('slab5', '9999') }}" style="width: 70px;">
                            </div>
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

    @if(request()->has('view'))
    <div class="card mt-2">
        <div class="card-body p-2">
            @if(isset($reportData) && $reportData->count() > 0)
            @php
                $slab1 = request('slab1', 30);
                $slab2 = request('slab2', 60);
                $slab3 = request('slab3', 90);
                $slab4 = request('slab4', 120);
            @endphp
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Code</th>
                            <th>Party Name</th>
                            <th class="text-end">0-{{ $slab1 }}</th>
                            <th class="text-end">{{ $slab1+1 }}-{{ $slab2 }}</th>
                            <th class="text-end">{{ $slab2+1 }}-{{ $slab3 }}</th>
                            <th class="text-end">{{ $slab3+1 }}-{{ $slab4 }}</th>
                            <th class="text-end">{{ $slab4+1 }}+</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totals = [0, 0, 0, 0, 0, 0]; @endphp
                        @foreach($reportData as $index => $item)
                        @php
                            $balance = $item->balance ?? 0;
                            $totals[5] += $balance;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->code ?? $item->id }}</td>
                            <td>{{ $item->name ?? '' }}</td>
                            <td class="text-end">{{ number_format($balance, 2) }}</td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end">{{ number_format($balance, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-end">{{ number_format($totals[5], 2) }}</th>
                            <th class="text-end"></th>
                            <th class="text-end"></th>
                            <th class="text-end"></th>
                            <th class="text-end"></th>
                            <th class="text-end">{{ number_format($totals[5], 2) }}</th>
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
function closeWindow() { window.location.href = '{{ route("admin.dashboard") }}'; }

function printReport() {
    window.open('{{ route("admin.reports.management.due-reports.ageing-analysis-account-ledger") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
        
        $partyDropdown.empty();
        $partyDropdown.append('<option value="">-- Select --</option>');
        
        if (type == 'C') {
            $('#party_label').text('Customer :');
            customers.forEach(function(customer) {
                var selected = (selectedPartyCode == customer.id) ? 'selected' : '';
                $partyDropdown.append('<option value="' + customer.id + '" ' + selected + '>' + customer.name + '</option>');
            });
        } else if (type == 'S') {
            $('#party_label').text('Supplier :');
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
        selectedPartyCode = '';
        populatePartyDropdown();
    });

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
