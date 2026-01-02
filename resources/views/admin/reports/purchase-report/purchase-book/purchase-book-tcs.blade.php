@extends('layouts.admin')

@section('title', 'Purchase Book With TCS')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #fff3cd 0%, #fff9e6 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-warning fst-italic fw-bold">PURCHASE BOOK WITH TCS</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.purchase-book-tcs') }}">
                <div class="row g-2">
                    <!-- Row 1 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1. Detailed / 2.</span>
                            <select name="report_format" class="form-select">
                                <option value="1" {{ ($reportFormat ?? '1') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($reportFormat ?? '') == '2' ? 'selected' : '' }}>2</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Pan No</span>
                            <input type="text" name="pan_no" class="form-control" value="{{ $panNo ?? '' }}" placeholder="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">GST No</span>
                            <input type="text" name="gst_no" class="form-control" value="{{ $gstNo ?? '' }}" placeholder="">
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party Name</span>
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
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1. With TCS / 2. Without TCS / 3. All</span>
                            <select name="tcs_filter" class="form-select">
                                <option value="1" {{ ($tcsFilter ?? '1') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($tcsFilter ?? '') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ ($tcsFilter ?? '') == '3' ? 'selected' : '' }}>3</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1. From Transaction / 2. From</span>
                            <select name="from_type" class="form-select">
                                <option value="1" {{ ($fromType ?? '1') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($fromType ?? '') == '2' ? 'selected' : '' }}>2</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1.Purchase / 2. Purchase Return / 3. Both</span>
                            <select name="transaction_type" class="form-select">
                                <option value="1" {{ ($transactionType ?? '1') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($transactionType ?? '') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ ($transactionType ?? '') == '3' ? 'selected' : '' }}>3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Order By</span>
                            <select name="order_by" class="form-select">
                                <option value="bill_wise" {{ ($orderBy ?? 'bill_wise') == 'bill_wise' ? 'selected' : '' }}>Bill Wise</option>
                                <option value="supplier_wise" {{ ($orderBy ?? '') == 'supplier_wise' ? 'selected' : '' }}>Supplier Wise</option>
                                <option value="date_wise" {{ ($orderBy ?? '') == 'date_wise' ? 'selected' : '' }}>Date Wise</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm" id="btnView">
                                <i class="bi bi-eye me-1"></i>OK
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

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 100px;">Trn No</th>
                            <th style="width: 80px;">Party Code</th>
                            <th>Party Name</th>
                            <th style="width: 120px;">Pan No.</th>
                            <th class="text-end" style="width: 100px;">Taxable</th>
                            <th class="text-end" style="width: 90px;">Tax Amt</th>
                            <th class="text-center" style="width: 70px;">TCS %</th>
                            <th class="text-end" style="width: 90px;">TCS Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $index => $purchase)
                        <tr>
                            <td>{{ $purchase->bill_date->format('d-m-Y') }}</td>
                            <td>{{ $purchase->voucher_type ?? '' }}{{ $purchase->bill_no }}</td>
                            <td>{{ $purchase->supplier->code ?? '' }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td class="small">{{ $purchase->supplier->pan ?? '-' }}</td>
                            <td class="text-end">{{ number_format($purchase->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->tax_amount ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($purchase->tcs_rate ?? 0, 3) }}</td>
                            <td class="text-end text-warning fw-bold">{{ number_format($purchase->tcs_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "OK" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Footer Totals -->
        <div class="card-footer bg-light py-2">
            <div class="row">
                <div class="col-md-6">
                    <span class="fw-bold">Total:</span>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-primary fw-bold me-4">{{ number_format($totals['taxable'] ?? 0, 2) }}</span>
                    <span class="text-danger fw-bold">{{ number_format($totals['tcs'] ?? 0, 3) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');

    // View/OK button - submits form to load data on current page
    document.getElementById('btnView').addEventListener('click', function() {
        let exportInput = form.querySelector('input[name="export"]');
        if (exportInput) exportInput.value = '';
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        form.target = '_self';
        form.submit();
    });

    // Print button - opens print view in new tab
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
        form.target = '_self';
        viewTypeInput.value = '';
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
.input-group-text { font-size: 0.7rem; padding: 0.25rem 0.4rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
