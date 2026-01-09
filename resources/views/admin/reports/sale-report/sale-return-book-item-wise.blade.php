@extends('layouts.admin')

@section('title', 'Sale / Return Book Item Wise')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">SALE / RETURN BOOK ITEM WISE</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.sale-return-book-item-wise') }}">
                <div class="row g-2 align-items-end">
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
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Item</span>
                            <select name="item_id" class="form-select">
                                <option value="">All Items</option>
                                @foreach($itemsList as $item)
                                    <option value="{{ $item->id }}" {{ $itemId == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Company</span>
                            <select name="company_id" class="form-select">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ $companyId == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
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
    @if(request()->has('view') && isset($items) && $items->count() > 0)
    <!-- Summary -->
    <div class="row g-2 mb-2">
        <div class="col-md-2"><div class="card bg-success text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Sale Qty</small><h6 class="mb-0">{{ number_format($totals['sale_qty']) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-success text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Sale Amt</small><h6 class="mb-0">₹{{ number_format($totals['sale_amount'], 2) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-danger text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Return Qty</small><h6 class="mb-0">{{ number_format($totals['return_qty']) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-danger text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Return Amt</small><h6 class="mb-0">₹{{ number_format($totals['return_amount'], 2) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-primary text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Net Qty</small><h6 class="mb-0">{{ number_format($totals['net_qty']) }}</h6>
        </div></div></div>
        <div class="col-md-2"><div class="card bg-primary text-white"><div class="card-body py-2 text-center">
            <small class="text-white-50">Net Amt</small><h6 class="mb-0">₹{{ number_format($totals['net_amount'], 2) }}</h6>
        </div></div></div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 80px;">Item Code</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-end text-success" style="width: 80px;">Sale Qty</th>
                            <th class="text-end text-success" style="width: 100px;">Sale Amt</th>
                            <th class="text-end text-danger" style="width: 80px;">Ret Qty</th>
                            <th class="text-end text-danger" style="width: 100px;">Ret Amt</th>
                            <th class="text-end" style="width: 80px;">Net Qty</th>
                            <th class="text-end" style="width: 100px;">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item['item_code'] }}</td>
                            <td>{{ $item['item_name'] }}</td>
                            <td>{{ $item['company_name'] }}</td>
                            <td class="text-end text-success">{{ number_format($item['sale_qty']) }}</td>
                            <td class="text-end text-success">{{ number_format($item['sale_amount'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($item['return_qty']) }}</td>
                            <td class="text-end text-danger">{{ number_format($item['return_amount'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item['net_qty']) }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($item['net_amount'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($items->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Total:</td>
                            <td class="text-end text-success">{{ number_format($totals['sale_qty']) }}</td>
                            <td class="text-end text-success">{{ number_format($totals['sale_amount'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($totals['return_qty']) }}</td>
                            <td class="text-end text-danger">{{ number_format($totals['return_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_qty']) }}</td>
                            <td class="text-end">₹{{ number_format($totals['net_amount'], 2) }}</td>
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
    window.open('{{ route("admin.reports.sales.sale-return-book-item-wise") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.sale-return-book-item-wise") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
