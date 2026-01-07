@extends('layouts.admin')

@section('title', 'Day Book')

@section('content')
<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0; border: 2px solid #999;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.financial.day-book') }}">
                <div class="row g-2 align-items-center">
                    <!-- From Date -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">From :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" 
                               value="{{ $fromDate }}" style="width: 140px;">
                    </div>

                    <!-- To Date -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">To :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" 
                               value="{{ $toDate }}" style="width: 140px;">
                    </div>

                    <!-- Single/Double Entry -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">S(ingle) / D(ouble) Entry :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="entry_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ $entryType }}" style="width: 40px;" maxlength="1" placeholder="S">
                    </div>

                    <!-- Type (J/P/R) -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold">Type (J/P/R) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="voucher_type" class="form-control form-control-sm text-uppercase" 
                               value="{{ $voucherType }}" style="width: 40px;" maxlength="1" placeholder="">
                    </div>

                    <div class="col-auto ms-auto">
                        <div class="d-flex gap-2">
                            <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg me-1"></i>Ok
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card shadow-sm" style="border: 2px solid #999; border-radius: 0;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr style="background-color: #e0e0e0;">
                            <th style="width: 80px;">Vou. No.</th>
                            <th style="width: 100px;">Date</th>
                            <th>Account Name</th>
                            <th class="text-end" style="width: 110px;">Debit</th>
                            <th class="text-end" style="width: 110px;">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                        <tr style="background-color: #e8e8e8;">
                            <td>{{ $row['voucher_no'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-M-y') }}</td>
                            <td>{{ $row['account_name'] }}</td>
                            <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                            <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                        </tr>
                        @empty
                        @for($i = 0; $i < 15; $i++)
                        <tr style="background-color: #e8e8e8;">
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
                        <tr style="background-color: #e8e8e8;">
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
                            <td>Narration :</td>
                            <td colspan="2">{{ $reportData->isNotEmpty() ? ($reportData->last()['narration'] ?? 'Narration') : 'Narration' }}</td>
                            <td class="text-end">{{ number_format($totals['debit'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['credit'], 2) }}</td>
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
    window.open('{{ route("admin.reports.financial.day-book") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

document.addEventListener('DOMContentLoaded', function() {
    // Uppercase for single char inputs
    document.querySelectorAll('.text-uppercase').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.location.href = '{{ route("admin.dashboard") }}';
        }
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.querySelector('button[name="view"]').click();
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
.form-control-sm {
    border: 1px solid #999;
}
</style>
@endpush
