@extends('layouts.admin')

@section('title', 'Register of Schedule H1 Drugs')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">REGISTER OF SCHEDULE H1 DRUGS</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-0">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-01') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-0">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-0">Item</label>
                        <select name="item_id" class="form-select form-select-sm" id="itemSelect">
                            <option value="">All Items</option>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item->id }}" {{ ($itemId ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }} {{ $item->packing ? '('.$item->packing.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-0">Supplier</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="supplier_code" class="form-control" value="{{ request('supplier_code') }}" placeholder="Code" style="max-width: 60px;" id="supplierCode">
                            <select name="supplier_id" class="form-select" id="supplierSelect">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->supplier_id }}" data-code="{{ $supplier->code }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small mb-0">Item Type</label>
                        <select name="item_type" class="form-select form-select-sm">
                            <option value="H1" {{ ($itemType ?? 'H1') == 'H1' ? 'selected' : '' }}>H1</option>
                            <option value="H" {{ ($itemType ?? '') == 'H' ? 'selected' : '' }}>H</option>
                            <option value="G" {{ ($itemType ?? '') == 'G' ? 'selected' : '' }}>G</option>
                            <option value="X" {{ ($itemType ?? '') == 'X' ? 'selected' : '' }}>X</option>
                            <option value="ALL" {{ ($itemType ?? '') == 'ALL' ? 'selected' : '' }}>ALL</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small mb-0">Sup. Flag</label>
                        <select name="supplier_flag" class="form-select form-select-sm">
                            <option value="5" {{ ($supplierFlag ?? '5') == '5' ? 'selected' : '' }}>ALL</option>
                            <option value="1" {{ ($supplierFlag ?? '') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($supplierFlag ?? '') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ ($supplierFlag ?? '') == '3' ? 'selected' : '' }}>3</option>
                            <option value="4" {{ ($supplierFlag ?? '') == '4' ? 'selected' : '' }}>4</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small mb-0">Item Status</label>
                        <select name="item_status" class="form-select form-select-sm">
                            <option value="A" {{ ($itemStatus ?? 'A') == 'A' ? 'selected' : '' }}>Active</option>
                            <option value="D" {{ ($itemStatus ?? '') == 'D' ? 'selected' : '' }}>Discontinued</option>
                            <option value="B" {{ ($itemStatus ?? '') == 'B' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-md-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye me-1"></i>View</button>
                            <button type="submit" name="export" value="excel" class="btn btn-success btn-sm"><i class="bi bi-file-excel me-1"></i>Excel</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="printReport()"><i class="bi bi-printer me-1"></i>Print</button>
                            <a href="{{ route('admin.reports.purchase') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg me-1"></i>Close</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Important Notice -->
    <div class="alert alert-danger mb-2 py-2">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Schedule H1 Drugs:</strong> These are drugs that require special record-keeping as per Drug and Cosmetics Act.
        <span class="float-end">Total Records: <strong>{{ $totals['count'] ?? 0 }}</strong> | Total Qty: <strong>{{ number_format($totals['total_qty'] ?? 0) }}</strong></span>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-danger sticky-top">
                        <tr>
                            <th class="text-center" style="width: 50px;">S.No</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 100px;">Invoice No</th>
                            <th>Name of Drug</th>
                            <th style="width: 100px;">Batch No</th>
                            <th style="width: 70px;">Expiry</th>
                            <th class="text-end" style="width: 80px;">Qty Received</th>
                            <th>Manufacturer</th>
                            <th>Supplier Name</th>
                            <th style="width: 120px;">D.L. No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drugs ?? [] as $index => $drug)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $drug->bill_date ? $drug->bill_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $drug->bill_no ?? '-' }}</td>
                            <td class="fw-bold text-danger">{{ $drug->drug_name ?? '-' }}</td>
                            <td>{{ $drug->batch_no ?? '-' }}</td>
                            <td>{{ $drug->expiry_date ?? '-' }}</td>
                            <td class="text-end">{{ number_format($drug->quantity ?? 0) }}</td>
                            <td class="small">{{ $drug->manufacturer ?? '-' }}</td>
                            <td>{{ $drug->supplier->name ?? 'N/A' }}</td>
                            <td class="small">{{ $drug->supplier->dl_no ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                @if(!request()->has('date_from'))
                                    Click "View" to load Schedule H1 drug records
                                @else
                                    No Schedule H1 drug records found for the selected criteria
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($drugs ?? []) > 0)
                    <tfoot class="table-danger">
                        <tr class="fw-bold">
                            <td colspan="6" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($totals['total_qty'] ?? 0) }}</td>
                            <td colspan="3">{{ $totals['count'] ?? 0 }} Records</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="card mt-2">
        <div class="card-body py-2 small text-muted">
            <strong>Note:</strong> This register is maintained as per Rule 65(10) of Drugs and Cosmetics Rules, 1945 for Schedule H1 drugs including Antibiotics, Anti-TB drugs, Habit forming drugs, etc.
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
</script>
@endpush
