@extends('layouts.admin')

@section('title', 'Dispatch Sheet')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">DISPATCH SHEET</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.dispatch-sheet') }}">
                <div class="row g-2 align-items-end">
                    <!-- Row 1: Date Range -->
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
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Company</span>
                            <select name="company_id" class="form-select">
                                <option value="">All</option>
                                @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
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
    @if(request()->has('view') && isset($groupedItems) && count($groupedItems) > 0)
    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Companies</small>
                    <h6 class="mb-0">{{ number_format($totals['companies'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Items</small>
                    <h6 class="mb-0">{{ number_format($totals['items_count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2 text-center">
                    <small>Qty</small>
                    <h6 class="mb-0">{{ number_format($totals['qty'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Free</small>
                    <h6 class="mb-0">{{ number_format($totals['free_qty'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 70px;">Item Code</th>
                            <th>Item Name</th>
                            <th style="width: 80px;">Packing</th>
                            <th style="width: 80px;">Batch</th>
                            <th class="text-end" style="width: 60px;">Qty</th>
                            <th class="text-end" style="width: 60px;">Free</th>
                            <th class="text-end" style="width: 80px;">Rate</th>
                            <th class="text-end" style="width: 90px;">Amount</th>
                            <th style="width: 80px;">Bill No</th>
                            <th>Customer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $srNo = 0; @endphp
                        @forelse($groupedItems ?? [] as $companyName => $items)
                            <tr class="table-warning">
                                <td colspan="11" class="fw-bold">
                                    <i class="bi bi-building me-1"></i>{{ $companyName ?: 'No Company' }}
                                    <span class="badge bg-primary ms-2">{{ $items->count() }} Items</span>
                                    <span class="badge bg-success ms-1">Qty: {{ number_format($items->sum('qty')) }}</span>
                                </td>
                            </tr>
                            @foreach($items as $item)
                            @php $srNo++; @endphp
                            <tr>
                                <td class="text-center">{{ $srNo }}</td>
                                <td>{{ $item->item_code }}</td>
                                <td>{{ Str::limit($item->item_name, 30) }}</td>
                                <td>{{ $item->packing ?? '' }}</td>
                                <td>{{ $item->batch_no ?? '' }}</td>
                                <td class="text-end">{{ number_format($item->qty) }}</td>
                                <td class="text-end">{{ number_format($item->free_qty ?? 0) }}</td>
                                <td class="text-end">{{ number_format((float)($item->sale_rate ?? 0), 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format((float)($item->net_amount ?? 0), 2) }}</td>
                                <td>
                                    <a href="{{ route('admin.sale.show', $item->sale_transaction_id) }}" class="text-decoration-none">
                                        {{ $item->saleTransaction->series ?? '' }}{{ $item->saleTransaction->invoice_no ?? '' }}
                                    </a>
                                </td>
                                <td class="small">{{ Str::limit($item->saleTransaction->customer->name ?? 'N/A', 20) }}</td>
                            </tr>
                            @endforeach
                            <tr class="table-secondary">
                                <td colspan="5" class="text-end fw-bold">{{ $companyName ?: 'No Company' }} Total:</td>
                                <td class="text-end fw-bold">{{ number_format($items->sum('qty')) }}</td>
                                <td class="text-end fw-bold">{{ number_format($items->sum('free_qty')) }}</td>
                                <td></td>
                                <td class="text-end fw-bold">{{ number_format($items->sum('net_amount'), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate Dispatch Sheet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($totals) && ($totals['items_count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Grand Total ({{ number_format($totals['items_count'] ?? 0) }} Items):</td>
                            <td class="text-end">{{ number_format($totals['qty'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($totals['free_qty'] ?? 0) }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                            <td colspan="2"></td>
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
    window.open('{{ route("admin.reports.sales.dispatch-sheet") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.dispatch-sheet") }}?print=1&' + $('#filterForm').serialize(), '_blank');
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
