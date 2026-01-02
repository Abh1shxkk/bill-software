@extends('layouts.admin')

@section('title', 'Purchase with Item Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #fffde7 0%, #fff9c4 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #1565c0;">PURCHASE WITH ITEM DETAILS</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-0">From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-01') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-0">To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small mb-0">S(elective)/A(ll)</label>
                        <select name="selective_all" class="form-select form-select-sm" id="selectiveAll">
                            <option value="A" {{ ($selectiveAll ?? 'A') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="S" {{ ($selectiveAll ?? '') == 'S' ? 'selected' : '' }}>S</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small mb-0">P(urchase)/R(epl)</label>
                        <select name="purchase_replacement" class="form-select form-select-sm">
                            <option value="P" {{ ($purchaseReplacement ?? 'P') == 'P' ? 'selected' : '' }}>P</option>
                            <option value="R" {{ ($purchaseReplacement ?? '') == 'R' ? 'selected' : '' }}>R</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-0">Party</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="supplier_code" class="form-control" value="{{ $supplierCode ?? '' }}" placeholder="Code" style="max-width: 70px;" id="supplierCode">
                            <select name="supplier_id" class="form-select" id="supplierSelect">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->supplier_id }}" data-code="{{ $supplier->code }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-0">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" name="export" value="excel" class="btn btn-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="printReport()"><i class="bi bi-printer me-1"></i>Print</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg me-1"></i>Close</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Bills</small>
                    <h5 class="mb-0">{{ number_format($totals['bills'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Items</small>
                    <h5 class="mb-0">{{ number_format($totals['items'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Total Quantity</small>
                    <h5 class="mb-0">{{ number_format($totals['quantity'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Free Qty</small>
                    <h5 class="mb-0">{{ number_format($totals['free_qty'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Tax Amount</small>
                    <h5 class="mb-0">₹{{ number_format($totals['tax_amount'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Net Amount</small>
                    <h5 class="mb-0">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 80px;">Date</th>
                            <th style="width: 90px;">Bill No</th>
                            <th>Supplier</th>
                            <th>Item Name</th>
                            <th style="width: 70px;">Packing</th>
                            <th style="width: 80px;">Batch</th>
                            <th style="width: 60px;">Expiry</th>
                            <th class="text-end" style="width: 50px;">Qty</th>
                            <th class="text-end" style="width: 45px;">Free</th>
                            <th class="text-end" style="width: 70px;">Rate</th>
                            <th class="text-end" style="width: 70px;">MRP</th>
                            <th class="text-end" style="width: 50px;">Disc%</th>
                            <th class="text-end" style="width: 50px;">GST%</th>
                            <th class="text-end" style="width: 80px;">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $index => $purchase)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $purchase->bill_date ? $purchase->bill_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $purchase->bill_no }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->item_name ?? '-' }}</td>
                            <td>{{ $purchase->packing ?? '-' }}</td>
                            <td>{{ $purchase->batch_no ?? '-' }}</td>
                            <td>{{ $purchase->expiry_date ?? '-' }}</td>
                            <td class="text-end">{{ number_format($purchase->quantity ?? 0) }}</td>
                            <td class="text-end">{{ number_format($purchase->free_qty ?? 0) }}</td>
                            <td class="text-end">{{ number_format($purchase->rate ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->mrp ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->discount_percent ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->gst_percent ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                @if(!request()->has('date_from'))
                                    Click "View" to load purchase item details
                                @else
                                    No records found for the selected criteria
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($purchases ?? []) > 0)
                    <tfoot class="table-dark">
                        <tr class="fw-bold">
                            <td colspan="8" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totals['quantity'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($totals['free_qty'] ?? 0) }}</td>
                            <td colspan="4"></td>
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

@push('styles')
<style>
.form-label { font-size: 0.75rem; }
.input-group-text { font-size: 0.75rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush

@push('scripts')
<script>
function printReport() {
    const form = document.getElementById('filterForm');
    const url = new URL(form.action || window.location.href);
    const formData = new FormData(form);
    formData.forEach((value, key) => {
        if (value) url.searchParams.set(key, value);
    });
    url.searchParams.set('view_type', 'print');
    window.open(url.toString(), '_blank');
}

// Supplier code to dropdown sync
document.getElementById('supplierCode')?.addEventListener('change', function() {
    const code = this.value.toUpperCase();
    const select = document.getElementById('supplierSelect');
    for (let option of select.options) {
        if (option.dataset.code === code) {
            select.value = option.value;
            break;
        }
    }
});

document.getElementById('supplierSelect')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    document.getElementById('supplierCode').value = selected.dataset.code || '';
});

// Show/hide supplier field based on Selective/All
document.getElementById('selectiveAll')?.addEventListener('change', function() {
    const supplierGroup = document.getElementById('supplierSelect').closest('.col-md-3');
    if (this.value === 'A') {
        supplierGroup.style.opacity = '0.5';
    } else {
        supplierGroup.style.opacity = '1';
    }
});
</script>
@endpush
