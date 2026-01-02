@extends('layouts.admin')

@section('title', 'Purchase Book - Item Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #ffcdd2 0%, #f8bbd9 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #1565c0;">Purchase Book - Item Details</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2" style="background: #e0f7fa;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.other.purchase-book-item-details') }}">
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

                    <!-- Row 2: P/T/B, Tagged Parties -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">P(urchase) / T(ransfer) / B(oth):</span>
                            <select name="purchase_transfer" class="form-select" style="max-width: 50px;">
                                <option value="P" {{ ($purchaseTransfer ?? 'P') == 'P' ? 'selected' : '' }}>P</option>
                                <option value="T" {{ ($purchaseTransfer ?? '') == 'T' ? 'selected' : '' }}>T</option>
                                <option value="B" {{ ($purchaseTransfer ?? '') == 'B' ? 'selected' : '' }}>B</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8"></div>

                    <!-- Row 3: Tagged Parties Only -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Tagged Parties Only [ Y / N ]:</span>
                            <select name="tagged_parties_only" class="form-select" style="max-width: 50px;">
                                <option value="N" {{ ($taggedPartiesOnly ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($taggedPartiesOnly ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8"></div>

                    <!-- Row 4: Supplier -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Supplier:</span>
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="00" style="max-width: 50px;">
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

                    <!-- Row 5: Replacement Received and Buttons -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Replacement Received:</span>
                            <select name="replacement_received" class="form-select" style="max-width: 50px;">
                                <option value="N" {{ ($replacementReceived ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($replacementReceived ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
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
    @if(isset($items) && count($items) > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Items</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Qty</small>
                    <h6 class="mb-0">{{ number_format($totals['qty'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Free Qty</small>
                    <h6 class="mb-0">{{ number_format($totals['free_qty'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Tax</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tax'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Net Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh;">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 40px;">Sr.</th>
                            <th style="width: 80px;">Date</th>
                            <th style="width: 80px;">Bill No</th>
                            <th>Supplier</th>
                            <th>Item Name</th>
                            <th style="width: 60px;">Pack</th>
                            <th style="width: 70px;">Batch</th>
                            <th style="width: 70px;">Expiry</th>
                            <th class="text-end" style="width: 70px;">MRP</th>
                            <th class="text-end" style="width: 70px;">Rate</th>
                            <th class="text-end" style="width: 60px;">Qty</th>
                            <th class="text-end" style="width: 50px;">Free</th>
                            <th class="text-end" style="width: 80px;">Amount</th>
                            <th class="text-end" style="width: 70px;">Tax</th>
                            <th class="text-end" style="width: 90px;">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items ?? [] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->transaction->bill_date ? $item->transaction->bill_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->transaction->bill_no ?? '-' }}</td>
                            <td>{{ $item->transaction->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $item->item_name ?? '-' }}</td>
                            <td>{{ $item->packing ?? '-' }}</td>
                            <td>{{ $item->batch_no ?? '-' }}</td>
                            <td>{{ $item->expiry_date ? $item->expiry_date->format('M-Y') : '-' }}</td>
                            <td class="text-end">{{ number_format($item->mrp ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->pur_rate ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->qty ?? 0, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($item->free_qty ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($items) && count($items) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="10">Grand Total</td>
                            <td class="text-end">{{ number_format($totals['qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['free_qty'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
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
