@extends('layouts.admin')

@section('title', 'Due List Analysis Report')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #7fffd4;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="font-family: 'Times New Roman', serif; color: #000080;">Due List Analysis Report</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.due-list-adjustment-analysis') }}">
                <!-- From & To Date -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>

                <!-- C/S and Party -->
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
                    <div class="col-md-4">
                        <select name="customer_code" id="customer_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                        </select>
                    </div>
                </div>

                <!-- Voucher Type -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Voucher Type :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="voucher_type" class="form-control form-control-sm" value="{{ request('voucher_type') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto ms-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="dn_party_no" id="dn_party_no" {{ request('dn_party_no') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary" for="dn_party_no">DN Party No</label>
                        </div>
                    </div>
                    <div class="col-auto ms-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="without_adjustment" id="without_adjustment" {{ request('without_adjustment') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="without_adjustment">Without Adjustment</label>
                        </div>
                    </div>
                </div>

                <hr class="my-2" style="border-top: 2px solid #000;">

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()"><u>P</u>rint</button>
                        <button type="submit" name="excel" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">Excel</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') || request()->has('excel'))
    <div class="card mt-2">
        <div class="card-body p-2">
            @if(isset($reportData) && $reportData->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Receipt No</th>
                            <th>Party Name</th>
                            <th class="text-end">Amount</th>
                            <th>Voucher Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAmount = 0; @endphp
                        @foreach($reportData as $index => $item)
                        @php $totalAmount += $item->amount ?? 0; @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->date ? date('d-M-y', strtotime($item->date)) : '' }}</td>
                            <td>{{ $item->receipt_no ?? '' }}</td>
                            <td>{{ $item->customer->name ?? '' }}</td>
                            <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                            <td>{{ $item->voucher_type ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th class="text-end">{{ number_format($totalAmount, 2) }}</th>
                            <th></th>
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
    window.open('{{ route("admin.reports.management.due-reports.due-list-adjustment-analysis") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).ready(function() {
    // Customer and Supplier data from controller
    var customers = @json($customers ?? []);
    var suppliers = @json($suppliers ?? []);
    var selectedPartyCode = '{{ request('customer_code') }}';

    // Populate party dropdown based on party type
    function populatePartyDropdown() {
        var type = $('#party_type').val();
        var $partyDropdown = $('#customer_code');
        
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
</style>
@endpush
