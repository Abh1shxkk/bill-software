@extends('layouts.admin')

@section('title', 'Dispatch Sheet')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-success fst-italic fw-bold">DISPATCH SHEET</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
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
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Ok</button>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2: Company & Remarks -->
                    <div class="col-md-2">
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
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Remarks</span>
                            <input type="text" name="remarks" class="form-control" value="{{ $remarks ?? '' }}" placeholder="Enter remarks...">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($totals) && ($totals['items_count'] ?? 0) > 0)
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
    @endif

    <!-- Data Table -->
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
                                Select filters and click "Ok" to generate Dispatch Sheet
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

    <!-- Action Buttons -->
    <div class="card mt-2">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                    <i class="bi bi-file-excel me-1"></i>Grid To Excel
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info btn-sm" onclick="viewReport()">
                        <i class="bi bi-eye me-1"></i>View
                    </button>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">Close (Esc)</a>
                </div>
            </div>
        </div>
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
    window.open('{{ route("admin.reports.sales.dispatch-sheet") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.dispatch-sheet") }}?' + params.toString(), 'DispatchSheet', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}

// Keyboard shortcut for close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; min-width: auto; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
