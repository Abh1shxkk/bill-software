@extends('layouts.admin')
@section('title', 'Tax Percentage Wise Sale')
@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);"><div class="card-body py-2 text-center"><h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">-: Tax Percentage Wise Sale :-</h4></div></div>
    <div class="card shadow-sm mb-2"><div class="card-body py-2">
        <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.tax-percentage-wise-sale') }}"><div class="row g-2 align-items-end">
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">From</span><input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->startOfMonth()->format('Y-m-d') }}"></div></div>
            <div class="col-md-2"><div class="input-group input-group-sm"><span class="input-group-text">To</span><input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}"></div></div>
            <div class="col-md-1"><button type="submit" class="btn btn-primary btn-sm w-100">Ok</button></div>
        </div></form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive" style="max-height: 55vh;">
        <table class="table table-sm table-hover table-striped table-bordered mb-0">
            <thead class="table-dark sticky-top"><tr><th>#</th><th>GST %</th><th class="text-end">Taxable Value</th><th class="text-end">CGST</th><th class="text-end">SGST</th><th class="text-end">IGST</th><th class="text-end">Total Tax</th><th class="text-end">Invoice Value</th></tr></thead>
            <tbody>
                @forelse($taxData ?? [] as $index => $tax)
                <tr><td>{{ $index + 1 }}</td><td>{{ $tax['gst_percent'] ?? 0 }}%</td><td class="text-end">{{ number_format($tax['taxable_value'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($tax['cgst'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($tax['sgst'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($tax['igst'] ?? 0, 2) }}</td><td class="text-end text-info">{{ number_format($tax['total_tax'] ?? 0, 2) }}</td><td class="text-end fw-bold">{{ number_format($tax['invoice_value'] ?? 0, 2) }}</td></tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Select filters and click "Ok"</td></tr>
                @endforelse
            </tbody>
            @if(isset($totals))<tfoot class="table-dark fw-bold"><tr><td colspan="2" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['taxable_value'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['cgst'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['sgst'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['igst'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['total_tax'] ?? 0, 2) }}</td><td class="text-end">{{ number_format($totals['invoice_value'] ?? 0, 2) }}</td></tr></tfoot>@endif
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
function exportToExcel() { window.open('{{ route("admin.reports.sales.other.tax-percentage-wise-sale") }}?' + new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString() + '&export=excel', '_blank'); }
function viewReport() { window.open('{{ route("admin.reports.sales.other.tax-percentage-wise-sale") }}?' + new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString() + '&view_type=print', 'TaxPercentWiseSale', 'width=1100,height=800,scrollbars=yes,resizable=yes'); }
</script>
@endpush
@push('styles')<style>.input-group-text { font-size: 0.7rem; }.form-control, .form-select { font-size: 0.75rem; }.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; }.btn-sm { font-size: 0.75rem; }.sticky-top { position: sticky; top: 0; z-index: 10; }</style>@endpush
