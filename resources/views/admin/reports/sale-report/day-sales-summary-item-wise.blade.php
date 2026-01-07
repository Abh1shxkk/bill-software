@extends('layouts.admin')

@section('title', 'Day Sales Summary - Item Wise')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">DAY SALES SUMMARY - ITEM WISE</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.day-sales-summary-item-wise') }}">
                <div class="row g-2">
                    <!-- Row 1 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Inv From</span>
                            <input type="number" name="invoice_from" class="form-control" value="{{ $invoiceFrom ?? 0 }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="number" name="invoice_to" class="form-control" value="{{ $invoiceTo ?? 9999999 }}" placeholder="9999999">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L/C/B</span>
                            <select name="local_central" class="form-select text-uppercase">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B(oth)</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L(ocal)</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C(entral)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Category</span>
                            <select name="category_id" class="form-select">
                                <option value="">All</option>
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Show Val</span>
                            <select name="show_value" class="form-select text-uppercase">
                                <option value="N" {{ ($showValue ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($showValue ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">With VAT</span>
                            <select name="with_vat" class="form-select text-uppercase">
                                <option value="B" {{ ($withVat ?? 'B') == 'B' ? 'selected' : '' }}>B(oth)</option>
                                <option value="Y" {{ ($withVat ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                                <option value="N" {{ ($withVat ?? '') == 'N' ? 'selected' : '' }}>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small fw-bold">Type:</span>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="sale_type" id="type_sale" value="1" {{ ($saleType ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="type_sale">1.Sale</label>
                                
                                <input type="radio" class="btn-check" name="sale_type" id="type_return" value="2" {{ ($saleType ?? '') == '2' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="type_return">2.Return</label>
                                
                                <input type="radio" class="btn-check" name="sale_type" id="type_both" value="3" {{ ($saleType ?? '') == '3' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="type_both">3.Both</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Order By</span>
                            <select name="order_by" class="form-select">
                                <option value="company" {{ ($orderBy ?? 'company') == 'company' ? 'selected' : '' }}>Company</option>
                                <option value="item" {{ ($orderBy ?? '') == 'item' ? 'selected' : '' }}>Item</option>
                                <option value="qty" {{ ($orderBy ?? '') == 'qty' ? 'selected' : '' }}>Qty</option>
                                <option value="value" {{ ($orderBy ?? '') == 'value' ? 'selected' : '' }}>Value</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">A/D</span>
                            <select name="asc_desc" class="form-select text-uppercase">
                                <option value="A" {{ ($ascDesc ?? 'A') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="D" {{ ($ascDesc ?? '') == 'D' ? 'selected' : '' }}>D</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="add_free_qty" id="addFreeQty" value="Y" {{ ($addFreeQty ?? '') == 'Y' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="addFreeQty">Add Free Qty</label>
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
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>COMPANY</th>
                            <th>ITEM NAME</th>
                            <th>PACK</th>
                            <th class="text-end">SALE</th>
                            <th class="text-end">VALUE</th>
                            <th class="text-end">BAL.</th>
                            <th class="text-end">PO</th>
                            <th class="text-end">PO.XX</th>
                            <th class="text-end">MRP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items ?? [] as $item)
                        <tr>
                            <td>{{ $item->company_name ?? '-' }}</td>
                            <td>{{ $item->item_name ?? '-' }}</td>
                            <td>{{ $item->packing ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->total_qty ?? 0) }}</td>
                            <td class="text-end">{{ number_format($item->total_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->balance ?? 0) }}</td>
                            <td class="text-end">{{ number_format($item->po ?? 0) }}</td>
                            <td class="text-end">{{ number_format($item->po_xx ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->mrp ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($items) && $items->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">TOTAL SALE VALUE:</td>
                            <td class="text-end">{{ number_format($totals['qty'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom Buttons -->
    <div class="mt-2 d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-info btn-sm" onclick="stockLedger()">Stock Ledger</button>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.day-sales-summary-item-wise") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.day-sales-summary-item-wise") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

function stockLedger() {
    alert('Stock Ledger feature - select an item first');
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
