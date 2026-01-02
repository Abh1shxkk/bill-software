@extends('layouts.admin')

@section('title', 'Purchase Voucher Detail')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #6495ed 0%, #87ceeb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-white fst-italic fw-bold">Purchase Voucher Detail</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.purchase-voucher-detail') }}">
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

                    <!-- Row 2: Voucher No, Bill No -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Voucher No:</span>
                            <input type="text" name="voucher_no" class="form-control" value="{{ $voucherNo ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Bill No.:</span>
                            <input type="text" name="bill_no" class="form-control" value="{{ $billNo ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 3: Local/Inter State, RCM -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L(ocal)/I(nter State)/B(oth):</span>
                            <select name="local_inter_state" class="form-select" style="max-width: 50px;">
                                <option value="L" {{ ($localInterState ?? 'L') == 'L' ? 'selected' : '' }}>L</option>
                                <option value="I" {{ ($localInterState ?? '') == 'I' ? 'selected' : '' }}>I</option>
                                <option value="B" {{ ($localInterState ?? '') == 'B' ? 'selected' : '' }}>B</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">RCM (Y/N):</span>
                            <select name="rcm" class="form-select" style="max-width: 50px;">
                                <option value="N" {{ ($rcm ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                                <option value="Y" {{ ($rcm ?? '') == 'Y' ? 'selected' : '' }}>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7"></div>

                    <!-- Row 4: Dr. Account -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Dr. Account:</span>
                            <input type="text" name="dr_account_code" class="form-control" value="{{ $drAccountCode ?? '' }}" placeholder="00" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="dr_account_id" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($drAccountId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->code ?? '' }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 5: Cr. Account -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Cr. Account:</span>
                            <input type="text" name="cr_account_code" class="form-control" value="{{ $crAccountCode ?? '' }}" placeholder="00" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="cr_account_id" class="form-control form-control-sm" value="{{ $crAccountId ?? '' }}" placeholder="Credit Account Name">
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 6: HSN Code, Voucher Type -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1.With / 2.W/o HSNCODE:</span>
                            <select name="hsn_code" class="form-select" style="max-width: 50px;">
                                <option value="1" {{ ($hsnCode ?? '1') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($hsnCode ?? '') == '2' ? 'selected' : '' }}>2</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1.Voucher Sale 2.Voucher Purchase 3.All:</span>
                            <select name="voucher_type" class="form-select" style="max-width: 50px;">
                                <option value="3" {{ ($voucherType ?? '3') == '3' ? 'selected' : '' }}>3</option>
                                <option value="1" {{ ($voucherType ?? '') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($voucherType ?? '') == '2' ? 'selected' : '' }}>2</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
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
    @if(isset($vouchers) && count($vouchers) > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Vouchers</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Tax</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tax'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total</small>
                    <h6 class="mb-0">₹{{ number_format($totals['total'] ?? 0, 2) }}</h6>
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
                            <th style="width: 90px;">Date</th>
                            <th style="width: 90px;">Voucher No</th>
                            <th style="width: 90px;">Bill No</th>
                            <th>Supplier</th>
                            <th style="width: 120px;">GSTN</th>
                            <th class="text-center" style="width: 60px;">Type</th>
                            <th class="text-center" style="width: 60px;">Items</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                            <th class="text-end" style="width: 80px;">Tax</th>
                            <th class="text-end" style="width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers ?? [] as $index => $voucher)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $voucher->bill_date->format('d-m-Y') }}</td>
                            <td>{{ $voucher->trn_no ?? '-' }}</td>
                            <td>{{ $voucher->bill_no ?? '-' }}</td>
                            <td>{{ $voucher->supplier->name ?? 'N/A' }}</td>
                            <td class="small">{{ $voucher->supplier->gst_no ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $voucher->is_local ? 'bg-primary' : 'bg-success' }}">
                                    {{ $voucher->is_local ? 'L' : 'I' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $voucher->item_count ?? 0 }}</td>
                            <td class="text-end">{{ number_format($voucher->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($voucher->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($voucher->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($vouchers) && count($vouchers) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="8">Grand Total</td>
                            <td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['total'] ?? 0, 2) }}</td>
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
