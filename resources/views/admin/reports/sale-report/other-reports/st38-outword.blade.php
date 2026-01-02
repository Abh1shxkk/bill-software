@extends('layouts.admin')
@section('title', 'List Of ST - 38 OutWord')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">List Of ST - 38 OutWord</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" action="{{ route('admin.reports.sales.other.st38-outword') }}" id="filterForm"><div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">Date: - From</span><input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}"></div></div>
        </div></form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 55vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>#</th><th>Date</th><th>ST-38 No</th><th>Party</th><th>State</th><th class="text-end">Taxable</th><th class="text-end">Tax</th><th class="text-end">Total</th></tr></thead>
            <tbody>
                @forelse($transfers ?? [] as $index => $txn)
                <tr><td>{{ $index + 1 }}</td><td>{{ $txn['date'] ?? '' }}</td><td>{{ $txn['st38_no'] ?? '' }}</td><td>{{ $txn['party_name'] ?? '' }}</td><td>{{ $txn['state'] ?? '' }}</td><td class="text-end">{{ number_format($txn['taxable'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($txn['tax'] ?? 0, 2) }}</td><td class="text-end fw-bold">{{ number_format($txn['total'] ?? 0, 2) }}</td></tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No records found</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals) && count($transfers ?? []) > 0)<tfoot class="table-dark fw-bold"><tr><td colspan="5" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['taxable'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['total'] ?? 0, 2) }}</td></tr></tfoot>@endif
        </table>
    </div></div></div>
    <div class="card mt-2"><div class="card-body py-2"><div class="d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportToExcel()"><u>E</u>xcel</button>
        <div class="d-flex gap-2"><button type="submit" form="filterForm" class="btn btn-outline-primary btn-sm"><u>V</u>iew</button><a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary btn-sm">Exit</a></div>
    </div></div></div>
</div>
@endsection
@push('scripts')
<script>
function exportToExcel() { window.location.href = '{{ route("admin.reports.sales.other.st38-outword") }}?' + new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString() + '&export=excel'; }
</script>
@endpush
@push('styles')<style>.input-group-text{font-size:.7rem}.form-control,.form-select{font-size:.75rem}.table th,.table td{padding:.3rem .4rem;font-size:.75rem}.btn-sm{font-size:.75rem}.sticky-top{position:sticky;top:0;z-index:10}</style>@endpush
