@extends('layouts.admin')

@section('title', 'Cash Deposite / Withdrawn Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic" style="font-family: 'Times New Roman', serif; color: #000;">Cash Deposite / Withdrawn Report</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.financial.cash-deposite-withdrawn') }}">
                <div class="row g-3 align-items-center">
                    <!-- Date From -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">From :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $fromDate }}" style="width: 150px;">
                    </div>

                    <!-- Date To -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">To :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ $toDate }}" style="width: 150px;">
                    </div>
                </div>

                <div class="row g-3 align-items-center mt-2">
                    <!-- Transaction Type -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">D(eposited) / W(ithdrawn) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="transaction_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ $transactionType }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row g-3 align-items-center mt-2">
                    <!-- Bank -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Bank :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="bank" class="form-control form-control-sm" 
                               value="{{ $bankCode }}" style="width: 60px;" placeholder="---">
                    </div>
                    <div class="col-3">
                        <select class="form-select form-select-sm" onchange="document.querySelector('input[name=bank]').value = this.value; this.nextElementSibling.value = this.options[this.selectedIndex].text;">
                            <option value="">-- All Banks --</option>
                            @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ $bankCode == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-3" style="border-color: #000;">

                <div class="row">
                    <div class="col-12 text-end">
                        <button type="submit" name="view" value="1" class="btn btn-primary px-4">
                            View
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-4 ms-2">
                            Close
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    @if($reportData->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr style="background-color: #d0d0d0;">
                            <th style="width: 50px;">S.No</th>
                            <th style="width: 100px;">Date</th>
                            <th style="width: 80px;">Trn No</th>
                            <th>Bank Name</th>
                            <th style="width: 100px;">Cheque No</th>
                            <th style="width: 120px;" class="text-end">Amount</th>
                            <th>Narration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-M-Y') }}</td>
                            <td>{{ $row['transaction_no'] }}</td>
                            <td>{{ $row['bank_name'] }}</td>
                            <td>{{ $row['cheque_no'] }}</td>
                            <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            <td>{{ $row['narration'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #d0d0d0; font-weight: bold;">
                            <td colspan="5" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totals['amount'], 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 text-center">
        <button type="button" class="btn btn-secondary" onclick="printReport()">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function printReport() {
    window.open('{{ route("admin.reports.financial.cash-deposite-withdrawn") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    // Uppercase for single char inputs
    document.querySelectorAll('.text-uppercase').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.table th, .table td { 
    padding: 0.3rem 0.5rem; 
    font-size: 0.85rem; 
    vertical-align: middle; 
    border-color: #999;
}
</style>
@endpush
