@extends('layouts.admin')

@section('title', 'Ledger Summary')

@section('content')
<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.financial.ledger-summary') }}">
                <div class="row g-2 align-items-center">
                    <!-- Date From -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">From :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $fromDate }}" style="width: 140px;">
                    </div>

                    <!-- Date To -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">To :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ $toDate }}" style="width: 140px;">
                    </div>

                    <!-- Ledger Type -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">C(ustomer) / S(upplier) / G(eneral) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="ledger_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ $ledgerType }}" style="width: 40px;" maxlength="1">
                    </div>

                    <!-- Group Head -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Group Head</label>
                    </div>
                    <div class="col-2">
                        <select name="group_head" class="form-select form-select-sm">
                            @foreach($groupHeads as $head)
                            <option value="{{ $head }}" {{ $groupHead == $head ? 'selected' : '' }}>{{ $head }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-2">
                    <!-- Flag -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Flag :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="flag" class="form-control form-control-sm" 
                               value="{{ $flag }}" style="width: 80px;">
                    </div>

                    <div class="col-auto ms-auto">
                        <div class="d-flex gap-2">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                                Ok
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="printReport()">
                                Print (F7)
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm">
                                Exit
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="background-color: #e8e8e8;">
                    <thead>
                        <tr style="background-color: #d0d0d0;">
                            <th class="text-primary" style="width: 80px;">CODE</th>
                            <th class="text-primary">NAME</th>
                            <th class="text-primary text-end" style="width: 100px;">OPENING</th>
                            <th class="text-primary text-center" style="width: 40px;">DrCr</th>
                            <th class="text-primary text-end" style="width: 100px;">DEBIT</th>
                            <th class="text-primary text-end" style="width: 100px;">CREDIT</th>
                            <th class="text-primary text-end" style="width: 100px;">CLOSING</th>
                            <th class="text-primary text-center" style="width: 40px;">DrCr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                        <tr>
                            <td>{{ $row['code'] }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                            <td class="text-center">{{ $row['opening_type'] }}</td>
                            <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                            <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                            <td class="text-end">{{ number_format($row['closing'], 2) }}</td>
                            <td class="text-center">{{ $row['closing_type'] }}</td>
                        </tr>
                        @empty
                        @for($i = 0; $i < 15; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endfor
                        @endforelse
                        @if($reportData->count() > 0 && $reportData->count() < 15)
                        @for($i = $reportData->count(); $i < 15; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endfor
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #d0d0d0; font-weight: bold;">
                            <td class="text-purple" style="color: #800080;">Total Records : {{ $reportData->count() }}</td>
                            <td class="text-end text-purple" style="color: #800080;">Total :</td>
                            <td class="text-end text-purple" style="color: #800080;">{{ number_format($totals['opening'], 2) }}</td>
                            <td class="text-center text-purple" style="color: #800080;">{{ $totals['opening_type'] }}</td>
                            <td class="text-end text-purple" style="color: #800080;">{{ number_format($totals['debit'], 2) }}</td>
                            <td class="text-end text-purple" style="color: #800080;">{{ number_format($totals['credit'], 2) }}</td>
                            <td class="text-end text-purple" style="color: #800080;">{{ number_format($totals['closing'], 2) }}</td>
                            <td class="text-center text-purple" style="color: #800080;">{{ $totals['closing_type'] }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printReport() {
    window.open('{{ route("admin.reports.financial.ledger-summary") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    // Uppercase for single char inputs
    document.querySelectorAll('.text-uppercase').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });

    // F7 for print
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F7') {
            e.preventDefault();
            printReport();
        }
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
