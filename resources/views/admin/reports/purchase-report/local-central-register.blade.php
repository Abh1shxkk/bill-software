@extends('layouts.admin')

@section('title', 'Purchase Book Local Central')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">PURCHASE BOOK LOCAL CENTRAL</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.local-central-register') }}">
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

                    <!-- Row 2: Report Type -->
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1.Purchase / 2.Purchase Return / 3.Debit Note / 4.Credit Note / 5.Consolidated:</span>
                            <select name="report_type" class="form-select" style="max-width: 60px;">
                                <option value="5" {{ ($reportType ?? '5') == '5' ? 'selected' : '' }}>5</option>
                                <option value="1" {{ ($reportType ?? '') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($reportType ?? '') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ ($reportType ?? '') == '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ ($reportType ?? '') == '4' ? 'selected' : '' }}>4</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4"></div>

                    <!-- Row 3: Party -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party:</span>
                            <input type="text" name="party_code" class="form-control" value="{{ $partyCode ?? '' }}" placeholder="00" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ ($supplierId ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->code ?? '' }} - {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5"></div>

                    <!-- Row 4: Local/Central, Selective/All, Tax -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L(ocal)/C(entral)/B(oth):</span>
                            <select name="local_central" class="form-select" style="max-width: 50px;">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">S(elective)/A(ll):</span>
                            <select name="selective_all" class="form-select" style="max-width: 50px;">
                                <option value="A" {{ ($selectiveAll ?? 'A') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="S" {{ ($selectiveAll ?? '') == 'S' ? 'selected' : '' }}>S</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Tax (%):</span>
                            <input type="text" name="tax_percent" class="form-control" value="{{ $taxPercent ?? '0.00' }}" style="max-width: 70px;">
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
    @if(isset($purchases) && count($purchases) > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Bills</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Local</small>
                    <h6 class="mb-0">{{ number_format($totals['local_count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Central</small>
                    <h6 class="mb-0">{{ number_format($totals['central_count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>Taxable Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['taxable'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Amount</small>
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
                            <th style="width: 90px;">Bill No</th>
                            <th>Supplier</th>
                            <th style="width: 120px;">GSTN</th>
                            <th class="text-center" style="width: 70px;">Type</th>
                            <th class="text-end" style="width: 100px;">Taxable</th>
                            <th class="text-end" style="width: 80px;">CGST</th>
                            <th class="text-end" style="width: 80px;">SGST</th>
                            <th class="text-end" style="width: 80px;">IGST</th>
                            <th class="text-end" style="width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $index => $purchase)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchase->bill_date->format('d-m-Y') }}</td>
                            <td>{{ $purchase->bill_no }}</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                            <td class="small">{{ $purchase->supplier->gst_no ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $purchase->is_local ? 'bg-primary' : 'bg-success' }}">
                                    {{ $purchase->is_local ? 'L' : 'C' }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($purchase->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->cgst_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->sgst_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->igst_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
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
                    @if(isset($purchases) && count($purchases) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="6">Grand Total</td>
                            <td class="text-end">{{ number_format($totals['taxable'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['cgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['sgst'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['igst'] ?? 0, 2) }}</td>
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
