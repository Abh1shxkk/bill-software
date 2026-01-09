@extends('layouts.admin')

@section('title', 'Sales Bills Printing')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">SALES BILLS PRINTING</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.sales-bills-printing') }}">
                <div class="row g-2 align-items-end">
                    <!-- Row 1: Date Range & Options -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Print Grid [Y/N]</span>
                            <select name="print_grid_format" class="form-select text-uppercase">
                                <option value="N" {{ ($printGridFormat ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($printGridFormat ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">B(ill)/S(alesman) Wise</span>
                            <select name="bill_salesman_wise" class="form-select text-uppercase">
                                <option value="S" {{ ($billSalesmanWise ?? 'S') == 'S' ? 'selected' : '' }}>S</option>
                                <option value="B" {{ ($billSalesmanWise ?? '') == 'B' ? 'selected' : '' }}>B</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2: Salesman & Remarks -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sales Man</span>
                            <select name="salesman_id" class="form-select">
                                <option value="">All</option>
                                @foreach($salesmen ?? [] as $salesman)
                                    <option value="{{ $salesman->id }}" {{ ($salesmanId ?? '') == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->code ?? '' }} - {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Remarks</span>
                            <input type="text" name="remarks" class="form-control" value="{{ $remarks ?? '' }}" placeholder="Enter remarks...">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <u>V</u>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">
                            <u>P</u>rint
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-light border px-4 fw-bold shadow-sm">
                            <u>C</u>lose
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table - Only show when view is clicked -->
    @if(request()->has('view') && isset($groupedSales) && count($groupedSales) > 0)
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 65vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 85px;">Date</th>
                            <th style="width: 80px;">Bill No</th>
                            <th style="width: 60px;">Code</th>
                            <th>Party Name</th>
                            <th>Address</th>
                            <th style="width: 100px;">Mobile</th>
                            <th class="text-end" style="width: 90px;">Discount</th>
                            <th class="text-end" style="width: 90px;">Tax</th>
                            <th class="text-end" style="width: 100px;">Net Amount</th>
                            <th class="text-center" style="width: 60px;">Print</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $srNo = 0; @endphp
                        @forelse($groupedSales ?? [] as $groupName => $sales)
                            @if(($billSalesmanWise ?? 'S') === 'S')
                            <tr class="table-info">
                                <td colspan="11" class="fw-bold">
                                    <i class="bi bi-person-badge me-1"></i>{{ $groupName }}
                                    <span class="badge bg-primary ms-2">{{ $sales->count() }} Bills</span>
                                </td>
                            </tr>
                            @endif
                            @foreach($sales as $sale)
                            @php $srNo++; @endphp
                            <tr>
                                <td class="text-center">{{ $srNo }}</td>
                                <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none fw-bold">
                                        {{ $sale->series }}{{ $sale->invoice_no }}
                                    </a>
                                </td>
                                <td>{{ $sale->customer->code ?? '' }}</td>
                                <td>{{ Str::limit($sale->customer->name ?? 'N/A', 25) }}</td>
                                <td class="small">{{ Str::limit($sale->customer->address ?? '', 30) }}</td>
                                <td>{{ $sale->customer->mobile ?? '' }}</td>
                                <td class="text-end">{{ number_format((float)($sale->dis_amount ?? 0), 2) }}</td>
                                <td class="text-end">{{ number_format((float)($sale->tax_amount ?? 0), 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format((float)($sale->net_amount ?? 0), 2) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.sale.show', $sale->id) }}?print=1" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-1">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @if(($billSalesmanWise ?? 'S') === 'S')
                            <tr class="table-secondary">
                                <td colspan="7" class="text-end fw-bold">{{ $groupName }} Total:</td>
                                <td class="text-end fw-bold">{{ number_format($sales->sum('dis_amount'), 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($sales->sum('tax_amount'), 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($sales->sum('net_amount'), 2) }}</td>
                                <td></td>
                            </tr>
                            @endif
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to load bills for printing
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($totals) && ($totals['count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">Grand Total ({{ number_format($totals['count'] ?? 0) }} Bills):</td>
                            <td class="text-end">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sales-bills-printing") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.sales-bills-printing") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        printReport();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
    if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();
        exportToExcel();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.8rem; border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
