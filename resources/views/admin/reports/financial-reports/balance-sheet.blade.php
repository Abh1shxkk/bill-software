@extends('layouts.admin')

@section('title', 'Balance Sheet')

@section('content')
<div class="container-fluid">
    <!-- Header & Filter Form -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0; border-radius: 0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.financial.balance-sheet') }}">
                <div class="row g-2 align-items-center">
                    <!-- Balance Sheet As On -->
                    <div class="col-auto">
                        <label class="col-form-label fw-bold" style="color: #800080;">BALANCE SHEET AS ON</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="as_on_date" class="form-control form-control-sm" 
                               value="{{ $asOnDate }}" style="width: 140px;">
                    </div>

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
                                <i class="bi bi-printer me-1"></i>Print (F7)
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

    <!-- Balance Sheet Tables -->
    <div class="row">
        <!-- Liabilities Side -->
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 0;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr style="background-color: #87ceeb;">
                                    <th style="color: #800080;">Liabilities</th>
                                    <th class="text-end" style="width: 120px; color: #800080;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($liabilities as $item)
                                <tr style="background-color: #87ceeb;">
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @empty
                                @for($i = 0; $i < 15; $i++)
                                <tr style="background-color: #87ceeb;">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                @endfor
                                @endforelse
                                @if($liabilities->count() > 0 && $liabilities->count() < 15)
                                @for($i = $liabilities->count(); $i < 15; $i++)
                                <tr style="background-color: #87ceeb;">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                @endfor
                                @endif
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #87ceeb; font-weight: bold;">
                                    <td>Total</td>
                                    <td class="text-end">{{ number_format($totalLiabilities, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assets Side -->
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 0;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr style="background-color: #ffb6c1;">
                                    <th style="color: #800080;">Assets</th>
                                    <th class="text-end" style="width: 120px; color: #800080;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $item)
                                <tr style="background-color: #ffb6c1;">
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @empty
                                @for($i = 0; $i < 15; $i++)
                                <tr style="background-color: #ffb6c1;">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                @endfor
                                @endforelse
                                @if($assets->count() > 0 && $assets->count() < 15)
                                @for($i = $assets->count(); $i < 15; $i++)
                                <tr style="background-color: #ffb6c1;">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                @endfor
                                @endif
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #ffb6c1; font-weight: bold;">
                                    <td>Total</td>
                                    <td class="text-end">{{ number_format($totalAssets, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printReport() {
    window.open('{{ route("admin.reports.financial.balance-sheet") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

// F7 shortcut for print
document.addEventListener('keydown', function(e) {
    if (e.key === 'F7') {
        e.preventDefault();
        printReport();
    }
    if (e.key === 'Escape') {
        window.location.href = '{{ route("admin.dashboard") }}';
    }
});
</script>
@endpush

@push('styles')
<style>
.table th, .table td { 
    padding: 0.35rem 0.5rem; 
    font-size: 0.85rem; 
    vertical-align: middle; 
    border-color: #666;
}
</style>
@endpush
