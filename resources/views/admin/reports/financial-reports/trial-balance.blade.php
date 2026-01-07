@extends('layouts.admin')

@section('title', 'Trial Balance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic" style="font-family: 'Times New Roman', serif; color: #800080;">TRIAL BALANCE</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.financial.trial-balance') }}">
                <div class="row g-2 align-items-center">
                    <!-- As On Date -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold text-primary">TRIAL BALANCE AS ON :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="as_on_date" class="form-control form-control-sm" 
                               value="{{ $asOnDate }}" style="width: 140px;">
                    </div>

                    <!-- Trial Head -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Trial Head</label>
                    </div>
                    <div class="col-2">
                        <select name="trial_head" class="form-select form-select-sm">
                            <option value="All" {{ $trialHead == 'All' ? 'selected' : '' }}>All</option>
                            <option value="Assets" {{ $trialHead == 'Assets' ? 'selected' : '' }}>Assets</option>
                            <option value="Liabilities" {{ $trialHead == 'Liabilities' ? 'selected' : '' }}>Liabilities</option>
                            <option value="Purchases" {{ $trialHead == 'Purchases' ? 'selected' : '' }}>Purchases</option>
                            <option value="Sales" {{ $trialHead == 'Sales' ? 'selected' : '' }}>Sales</option>
                            <option value="Expenses" {{ $trialHead == 'Expenses' ? 'selected' : '' }}>Expenses</option>
                            <option value="Income" {{ $trialHead == 'Income' ? 'selected' : '' }}>Income</option>
                        </select>
                    </div>

                    <!-- Opening Y/N -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Opening [ Y / N ]</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="show_opening" class="form-control form-control-sm text-uppercase" 
                               value="{{ $showOpening ? 'Y' : 'N' }}" style="width: 40px;" maxlength="1">
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-2">
                    <!-- From Date -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">From :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $fromDate }}" style="width: 140px;">
                    </div>

                    <div class="col-auto ms-auto">
                        <div class="d-flex gap-2">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg me-1"></i>Ok
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="printReport()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
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
                <table class="table table-sm table-bordered mb-0" style="background-color: #c8f7c8;">
                    <thead>
                        <tr style="background-color: #c8f7c8;">
                            <th rowspan="2" class="text-primary" style="width: 80px;">Code</th>
                            <th rowspan="2" class="text-primary">Name</th>
                            <th colspan="2" class="text-center text-purple" style="color: #800080;">Opening Balance</th>
                            <th colspan="2" class="text-center text-purple" style="color: #800080;">Closing Balance</th>
                        </tr>
                        <tr style="background-color: #c8f7c8;">
                            <th class="text-center text-purple" style="width: 100px; color: #800080;">Debit</th>
                            <th class="text-center text-purple" style="width: 100px; color: #800080;">Credit</th>
                            <th class="text-center text-purple" style="width: 100px; color: #800080;">Debit</th>
                            <th class="text-center text-purple" style="width: 100px; color: #800080;">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                        <tr style="background-color: #c8f7c8;">
                            <td>{{ $row['code'] }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-end">{{ $row['opening_debit'] > 0 ? number_format($row['opening_debit'], 2) : '' }}</td>
                            <td class="text-end">{{ $row['opening_credit'] > 0 ? number_format($row['opening_credit'], 2) : '' }}</td>
                            <td class="text-end">{{ $row['closing_debit'] > 0 ? number_format($row['closing_debit'], 2) : '' }}</td>
                            <td class="text-end">{{ $row['closing_credit'] > 0 ? number_format($row['closing_credit'], 2) : '' }}</td>
                        </tr>
                        @empty
                        @for($i = 0; $i < 15; $i++)
                        <tr style="background-color: #c8f7c8;">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endfor
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #c8f7c8; font-weight: bold;">
                            <td class="text-primary">Total Records : {{ $reportData->count() }}</td>
                            <td class="text-end text-primary">Total</td>
                            <td class="text-end">{{ number_format($totals['opening_debit'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['opening_credit'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['closing_debit'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['closing_credit'], 2) }}</td>
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
    window.open('{{ route("admin.reports.financial.trial-balance") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    // Excel export
    document.getElementById('btnExcel').addEventListener('click', function() {
        let form = document.getElementById('filterForm');
        let exportInput = form.querySelector('input[name="export"]');
        if (!exportInput) {
            exportInput = document.createElement('input');
            exportInput.type = 'hidden';
            exportInput.name = 'export';
            form.appendChild(exportInput);
        }
        exportInput.value = 'excel';
        form.submit();
        exportInput.value = '';
    });

    // Uppercase for Y/N input
    document.querySelector('input[name="show_opening"]').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endpush

@push('styles')
<style>
.table th, .table td { 
    padding: 0.35rem 0.5rem; 
    font-size: 0.85rem; 
    vertical-align: middle; 
    border-color: #999;
}
.text-purple { color: #800080 !important; }
</style>
@endpush
