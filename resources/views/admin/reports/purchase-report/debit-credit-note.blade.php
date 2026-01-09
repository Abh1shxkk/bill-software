@extends('layouts.admin')

@section('title', 'Debit/Credit Note Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-danger fst-italic fw-bold">DEBIT / CREDIT NOTE - REPORT</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.purchase.debit-credit-note') }}">
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

                    <!-- Row 2: Party Type & Note Type -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">S(ale)/P(urchase)/G(eneral)/A(ll):</span>
                            <select name="party_type" class="form-select" style="max-width: 60px;">
                                <option value="A" {{ ($partyType ?? 'A') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="S" {{ ($partyType ?? '') == 'S' ? 'selected' : '' }}>S</option>
                                <option value="P" {{ ($partyType ?? '') == 'P' ? 'selected' : '' }}>P</option>
                                <option value="G" {{ ($partyType ?? '') == 'G' ? 'selected' : '' }}>G</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">D(ebit Note)/C(redit Note)/A(ll):</span>
                            <select name="note_type" class="form-select" style="max-width: 60px;">
                                <option value="A" {{ ($noteType ?? 'A') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="D" {{ ($noteType ?? '') == 'D' ? 'selected' : '' }}>D</option>
                                <option value="C" {{ ($noteType ?? '') == 'C' ? 'selected' : '' }}>C</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4"></div>

                    <!-- Row 3: Customer -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Customer:</span>
                            <input type="text" name="customer_code" class="form-control" value="{{ $customerCode ?? '' }}" placeholder="00" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All Customers</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->code ?? '' }} - {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6"></div>

                    <!-- Row 4: Supplier -->
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

                    <!-- Row 5: G.Ledger -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">G.Ledger:</span>
                            <input type="text" name="ledger_code" class="form-control text-uppercase" value="{{ $ledgerCode ?? '' }}" placeholder="00" style="max-width: 60px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="ledger_id" class="form-select form-select-sm">
                            <option value="">Select an option</option>
                            @foreach($generalLedgers ?? [] as $ledger)
                                <option value="{{ $ledger->id }}" {{ ($ledgerId ?? '') == $ledger->id ? 'selected' : '' }}>
                                    {{ $ledger->account_code ?? '' }} - {{ $ledger->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
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
    @if(isset($notes) && count($notes) > 0)
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Total Notes</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Debit Notes</small>
                    <h6 class="mb-0">{{ number_format($totals['dn_count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">Credit Notes</small>
                    <h6 class="mb-0">{{ number_format($totals['cn_count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <small class="text-white-50">DN Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['dn_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <small>CN Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['cn_amount'] ?? 0, 2) }}</h6>
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
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th style="width: 50px;">Sr.</th>
                            <th style="width: 60px;">Type</th>
                            <th style="width: 100px;">Note No</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 80px;">Party Type</th>
                            <th>Party Name</th>
                            <th>Reason</th>
                            <th class="text-end" style="width: 100px;">Gross Amt</th>
                            <th class="text-end" style="width: 80px;">GST</th>
                            <th class="text-end" style="width: 110px;">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notes ?? [] as $index => $note)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge {{ $note->note_type == 'DN' ? 'bg-danger' : 'bg-success' }}">
                                    {{ $note->note_type }}
                                </span>
                            </td>
                            <td>{{ $note->note_no }}</td>
                            <td>{{ $note->note_date->format('d-m-Y') }}</td>
                            <td>{{ $note->party_type_label ?? '' }}</td>
                            <td>{{ $note->party_name ?? '' }}</td>
                            <td>{{ $note->reason ?? '' }}</td>
                            <td class="text-end">{{ number_format($note->gross_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($note->total_gst ?? 0, 2) }}</td>
                            <td class="text-end fw-bold {{ $note->note_type == 'DN' ? 'text-danger' : 'text-success' }}">
                                {{ number_format($note->net_amount ?? 0, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($notes) && count($notes) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="7">Grand Total</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
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

    // View button - submits form to load data on current page
    document.getElementById('btnView').addEventListener('click', function() {
        let viewTypeInput = form.querySelector('input[name="view_type"]');
        if (viewTypeInput) viewTypeInput.value = '';
        let exportInput = form.querySelector('input[name="export"]');
        if (exportInput) exportInput.value = '';
        form.target = '_self';
        form.submit();
    });

    // Excel button - exports to Excel
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
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { padding: 0.4rem 0.5rem; font-size: 0.8rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
