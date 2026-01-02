@extends('layouts.admin')
@section('title', 'Sale/Stock Detail')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">-: Sale/Stock Detail :-</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.sale-stock-detail') }}"><div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">From</span><input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->startOfMonth()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}"></div></div>
            <div class="col-md-3"><div class="input-group input-group-sm"><span class="input-group-text">Company</span><select name="company_id" class="form-select"><option value="">All</option>@foreach($companies ?? [] as $c)<option value="{{ $c->id }}" {{ ($companyId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div></div>
            <div class="col-md-1"><button type="submit" class="btn btn-primary btn-sm w-100">Ok</button></div>
        </div></form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 55vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>#</th><th>Item Code</th><th>Item Name</th><th>Company</th><th class="text-end">Stock</th><th class="text-end">Sold</th><th class="text-end">Balance</th><th class="text-end">Sale Value</th></tr></thead>
            <tbody>
                @forelse($items ?? [] as $index => $item)
                <tr><td>{{ $index + 1 }}</td><td>{{ $item['item_code'] ?? '' }}</td><td>{{ $item['item_name'] ?? '' }}</td><td>{{ $item['company_name'] ?? '' }}</td><td class="text-end">{{ number_format($item['stock'] ?? 0) }}</td><td class="text-end text-danger">{{ number_format($item['sold'] ?? 0) }}</td><td class="text-end {{ ($item['balance'] ?? 0) < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($item['balance'] ?? 0) }}</td><td class="text-end fw-bold">{{ number_format($item['sale_value'] ?? 0, 2) }}</td></tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Select filters and click "Ok"</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals))<tfoot class="table-dark fw-bold"><tr><td colspan="4" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['stock'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['sold'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['balance'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['sale_value'] ?? 0, 2) }}</td></tr></tfoot>@endif
        </table>
    </div></div></div>
    <div class="card mt-2"><div class="card-body py-2"><div class="d-flex justify-content-between">
        <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()"><i class="bi bi-file-excel me-1"></i>Excel</button>
        <div class="d-flex gap-2"><button type="submit" form="filterForm" class="btn btn-info btn-sm"><i class="bi bi-eye me-1"></i>View</button><a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">Close</a></div>
    </div></div></div>
</div>
@endsection
@push('scripts')
<script>
function exportToExcel() { window.open('{{ route("admin.reports.sales.other.sale-stock-detail") }}?' + new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString() + '&export=excel', '_blank'); }
function viewReport() { window.open('{{ route("admin.reports.sales.other.sale-stock-detail") }}?' + new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString() + '&view_type=print', 'SaleStockDetail', 'width=1100,height=800,scrollbars=yes,resizable=yes'); }
</script>
@endpush
@push('styles')<style>.input-group-text { font-size: 0.7rem; }.form-control, .form-select { font-size: 0.75rem; }.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; }.btn-sm { font-size: 0.75rem; }.sticky-top { position: sticky; top: 0; z-index: 10; }</style>@endpush
