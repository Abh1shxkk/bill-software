@extends('layouts.admin')

@section('title', 'Bill History')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm" style="background-color: #f0f0f0; max-width: 450px;">
        <div class="card-header py-2" style="background-color: #e8e8e8; border-bottom: 1px solid #ccc;">
            <span class="fw-bold">Bill History</span>
        </div>
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.due-reports.bill-history') }}">
                <!-- Series & Financial Year -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Series :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="series" class="form-control form-control-sm text-center" value="{{ request('series', 'SB') }}" style="width: 50px; background-color: #ffffcc; border: 2px solid #0066cc;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Financial Year :</label>
                    </div>
                    <div class="col-auto">
                        <select name="financial_year" class="form-select form-select-sm" style="width: 120px;">
                            @php
                                $currentYear = date('Y');
                                $currentMonth = date('n');
                                $startYear = $currentMonth >= 4 ? $currentYear : $currentYear - 1;
                            @endphp
                            @for($i = $startYear; $i >= $startYear - 5; $i--)
                                <option value="{{ $i }}-{{ $i + 1 }}" {{ request('financial_year', $startYear . '-' . ($startYear + 1)) == $i . '-' . ($i + 1) ? 'selected' : '' }}>
                                    {{ $i }}-{{ $i + 1 }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- No. -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">No.</label>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0">:</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="bill_no" class="form-control form-control-sm" value="{{ request('bill_no') }}" style="width: 150px; background-color: #e9ecef;">
                    </div>
                </div>

                <hr class="my-2" style="border-top: 2px solid #000;">

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="submit" name="ok" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>O</u>k</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="printReport()"><u>P</u>rint</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('ok'))
    <div class="card mt-2" style="max-width: 600px;">
        <div class="card-body p-2">
            @if(isset($reportData) && $reportData->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Bill No</th>
                            <th>Date</th>
                            <th>Party Name</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->invoice_no ?? '' }}</td>
                            <td>{{ $item->sale_date ? date('d-M-y', strtotime($item->sale_date)) : '' }}</td>
                            <td>{{ $item->customer->name ?? '' }}</td>
                            <td class="text-end">{{ number_format($item->net_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->paid_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format(($item->net_amount ?? 0) - ($item->paid_amount ?? 0), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2 text-muted">Total Records: {{ $reportData->count() }}</div>
            @else
            <div class="alert alert-info mb-0">No records found for the given bill number.</div>
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
    window.open('{{ route("admin.reports.management.due-reports.bill-history") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
        if (e.key === 'Escape') {
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
