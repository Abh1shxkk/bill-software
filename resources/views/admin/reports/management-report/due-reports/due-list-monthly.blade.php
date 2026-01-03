@extends('layouts.admin')

@section('title', 'Monthly Due List')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="font-family: 'Times New Roman', serif; color: #cc0066;">Monthly Due List</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.due-list-monthly') }}">
                <!-- As On -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">As On :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="as_on_date" class="form-control form-control-sm" value="{{ request('as_on_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                </div>

                <!-- Tagged Parties -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Tagged Parties [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="tagged_parties" class="form-control form-control-sm text-center text-uppercase" value="{{ request('tagged_parties', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <!-- Party Name -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Party Name :</label>
                    </div>
                    <div class="col-md-5">
                        <select name="party_code" id="party_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('party_code') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-md-2">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-md-5">
                        <select name="company_code" id="company_code" class="form-select form-select-sm">
                            <option value="">-- Select --</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_code') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Checkboxes -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="address" id="address" {{ request('address') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="address">Address</label>
                        </div>
                    </div>
                    <div class="col-auto ms-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tel_no" id="tel_no" {{ request('tel_no') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="tel_no">Tel. No.</label>
                        </div>
                    </div>
                </div>

                <hr class="my-2" style="border-top: 2px solid #000;">

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="submit" name="ok" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>O</u>k</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()"><u>P</u>rint</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>ancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('ok'))
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
                            @if(request('address'))
                            <th>Address</th>
                            @endif
                            @if(request('tel_no'))
                            <th>Tel. No.</th>
                            @endif
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
                            @if(request('address'))
                            <td>{{ $item->address ?? '' }}</td>
                            @endif
                            @if(request('tel_no'))
                            <td>{{ $item->phone ?? $item->mobile ?? '' }}</td>
                            @endif
                            <td class="text-end">{{ number_format($item->balance ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="{{ 3 + (request('address') ? 1 : 0) + (request('tel_no') ? 1 : 0) }}" class="text-end">Total:</th>
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
    window.open('{{ route("admin.reports.management.due-reports.due-list-monthly") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

$(document).ready(function() {
    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.altKey && e.key.toLowerCase() === 'o') {
            e.preventDefault();
            $('button[name="ok"]').click();
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
