@extends('layouts.admin')
@section('title', 'Sale Return Adjustment')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">SALE RETURN ADJUSTMENT</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" action="{{ route('admin.reports.sales.other.sale-return-adjustment') }}" id="filterForm"><div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">From</span><input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}"></div></div>
            <div class="col-md-1"><button type="submit" class="btn btn-outline-primary btn-sm w-100">OK</button></div>
            <div class="col-md-1"><a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary btn-sm w-100">Exit</a></div>
            <div class="col-md-1"><button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="printReport()">Print (F7)</button></div>
        </div></form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 55vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>SL.NO.</th><th>Date</th><th>Trn.No</th><th>PartyName</th><th class="text-end">Amount</th><th>Adj.Bill</th><th class="text-end">Bal.Amt</th></tr></thead>
            <tbody>
                @forelse($adjustments ?? [] as $index => $adj)
                <tr><td>{{ $index + 1 }}.</td><td>{{ $adj['date'] ?? '' }}</td><td>{{ $adj['trn_no'] ?? '' }}</td><td>{{ $adj['party_name'] ?? '' }}</td><td class="text-end">{{ number_format($adj['amount'] ?? 0, 2) }}</td><td>{{ $adj['adj_bill'] ?? '' }}</td><td class="text-end">{{ number_format($adj['bal_amt'] ?? 0, 2) }}</td></tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No records found</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals) && count($adjustments ?? []) > 0)<tfoot class="table-dark fw-bold"><tr><td colspan="4" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td><td></td><td class="text-end">{{ number_format($totals['bal_amt'] ?? 0, 2) }}</td></tr></tfoot>@endif
        </table>
    </div></div></div>
</div>
@endsection
@push('scripts')
<script>
function printReport() { 
    var url = '{{ route("admin.reports.sales.other.sale-return-adjustment") }}?' + new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString() + '&view_type=print';
    window.open(url, 'SaleReturnAdj', 'width=1100,height=800,scrollbars=yes,resizable=yes'); 
}
</script>
@endpush
@push('styles')<style>.input-group-text{font-size:.7rem}.form-control,.form-select{font-size:.75rem}.table th,.table td{padding:.3rem .4rem;font-size:.75rem}.btn-sm{font-size:.75rem}.sticky-top{position:sticky;top:0;z-index:10}</style>@endpush
