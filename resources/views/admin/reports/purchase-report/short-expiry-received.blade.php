@extends('layouts.admin')

@section('title', 'Short Expiry Received')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f4a460 0%, #ffa07a 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-white fst-italic fw-bold">SHORT EXPIRY RECEIVED</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.short-expiry-received') }}">
                <div class="row g-2">
                    <!-- Row 1: Date Range -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From:</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To:</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 2: No of Months, Date Type -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">No of Months:</span>
                            <input type="number" name="no_of_months" class="form-control" value="{{ $noOfMonths ?? 6 }}" min="1" max="24" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">(B)ill Date / (R)eceive Date:</span>
                            <select name="date_type" class="form-select" style="max-width: 50px;">
                                <option value="B" {{ ($dateType ?? 'B') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="R" {{ ($dateType ?? '') == 'R' ? 'selected' : '' }}>R</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7"></div>

                    <!-- Row 3: Supplier -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier:</span>
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="00" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->code ?? '' }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 4: Available Batch Qty checkbox and buttons -->
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="available_batch_qty" class="form-check-input" id="availableBatchQty" value="1" {{ ($availableBatchQty ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="availableBatchQty">Available Batch Qty</label>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" id="btnExcel">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="btnView">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnPrint">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-dark btn-sm">
                                <i class="bi bi-x-lg me-1"></i>Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($shortExpiry) && count($shortExpiry) > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Items</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Total Qty</small>
                    <h6 class="mb-0">{{ number_format($totals['qty'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 40px;">Sr.</th>
                            <th style="width: 90px;">Recv Date</th>
                            <th style="width: 90px;">Bill No</th>
                            <th>Supplier</th>
                            <th>Item Name</th>
                            <th style="width: 80px;">Batch</th>
                            <th style="width: 80px;">Expiry</th>
                            <th class="text-center" style="width: 80px;">Days Left</th>
                            <th class="text-end" style="width: 70px;">Qty</th>
                            <th class="text-end" style="width: 80px;">Rate</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shortExpiry ?? [] as $index => $item)
                        <tr class="{{ $item->days_left <= 30 ? 'table-danger' : ($item->days_left <= 90 ? 'table-warning' : '') }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->received_date ? $item->received_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->bill_no }}</td>
                            <td>{{ $item->supplier_name }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->batch_no ?? '-' }}</td>
                            <td>{{ $item->expiry_date ? $item->expiry_date->format('M-Y') : '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $item->days_left <= 30 ? 'bg-danger' : ($item->days_left <= 90 ? 'bg-warning text-dark' : 'bg-info') }}">
                                    {{ $item->days_left }} days
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($item->qty, 2) }}</td>
                            <td class="text-end">{{ number_format($item->pur_rate ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View in Excel" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($shortExpiry) && count($shortExpiry) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="8">Grand Total</td>
                            <td class="text-end">{{ number_format($totals['qty'] ?? 0, 2) }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');

    // View button
    document.getElementById('btnView').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        let exportInput = form.querySelector('input[name="export"]');
        if (exportInput) exportInput.value = '';
        form.target = '_self';
        form.submit();
    });

    // Print button
    document.getElementById('btnPrint').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (!viewTypeInput) {
            viewTypeInput = document.createElement('input');
            viewTypeInput.type = 'hidden';
            viewTypeInput.name = 'view_type';
            form.appendChild(viewTypeInput);
        }
        viewTypeInput.value = 'print';
        form.target = '_blank';
        form.submit();
        viewTypeInput.value = '';
        form.target = '_self';
    });

    // Excel button
    document.getElementById('btnExcel').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (!exportInput) {
            exportInput = document.createElement('input');
            exportInput.type = 'hidden';
            exportInput.name = 'export';
            form.appendChild(exportInput);
        }
        exportInput.value = 'excel';
        form.target = '_self';
        form.submit();
        exportInput.value = '';
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.history.back();
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnView').click();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.4rem 0.5rem; font-size: 0.8rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
