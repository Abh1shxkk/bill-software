@extends('layouts.admin')

@section('title', 'Day Sales Summary - Item Wise')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">DAY SALES SUMMARY - ITEM WISE</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
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
                            <select name="local_central" class="form-select">
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
                            <select name="show_value" class="form-select">
                                <option value="N" {{ ($showValue ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($showValue ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">With VAT</span>
                            <select name="with_vat" class="form-select">
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
                                <label class="btn btn-outline-primary btn-sm" for="type_sale">1.Sale</label>
                                
                                <input type="radio" class="btn-check" name="sale_type" id="type_return" value="2" {{ ($saleType ?? '') == '2' ? 'checked' : '' }}>
                                <label class="btn btn-outline-danger btn-sm" for="type_return">2.Return</label>
                                
                                <input type="radio" class="btn-check" name="sale_type" id="type_both" value="3" {{ ($saleType ?? '') == '3' ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary btn-sm" for="type_both">3.Both</label>
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
                            <select name="asc_desc" class="form-select">
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
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm">Ok</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="viewReport()">Print (F7)</button>
                            <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">Close</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
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
                                Select filters and click "Ok" to generate report
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
        <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">Excel</button>
        <button type="button" class="btn btn-info btn-sm" onclick="stockLedger()">Stock Ledger</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.day-sales-summary-item-wise") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.day-sales-summary-item-wise") }}?' + params.toString(), 'DaySalesSummary', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}

function stockLedger() {
    alert('Stock Ledger feature - select an item first');
}
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
