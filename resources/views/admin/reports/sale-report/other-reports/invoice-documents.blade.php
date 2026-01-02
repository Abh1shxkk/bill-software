@extends('layouts.admin')
@section('title', 'Invoice Documents')
@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">-: Invoice Documents :-</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2" style="background-color: #e9ecef;">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.invoice-documents') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Advice Date</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Fin.Year</span>
                            <input type="text" name="fin_year" class="form-control" value="{{ $finYear ?? date('Y') . '-' . (date('Y') + 1) }}" style="width: 80px;">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <select name="series" class="form-select form-select-sm">
                            <option value="">Series</option>
                            @foreach($seriesList ?? [] as $s)
                            <option value="{{ $s }}" {{ ($series ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="bill_no_from" class="form-control form-control-sm" placeholder="Bill From" value="{{ $billNoFrom ?? '' }}">
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="bill_no_to" class="form-control form-control-sm" placeholder="Bill To" value="{{ $billNoTo ?? '' }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search me-1"></i>Ok
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 50px;">Sr.No</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 100px;">Invoice No</th>
                            <th style="width: 80px;">Code</th>
                            <th>Party Name</th>
                            <th style="width: 130px;">GST No</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                            <th style="width: 120px;">E-Way Bill</th>
                            <th style="width: 150px;">IRN No</th>
                            <th class="text-center" style="width: 80px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents ?? [] as $doc)
                        <tr>
                            <td class="text-center">{{ $doc['sr_no'] }}</td>
                            <td>{{ $doc['date'] }}</td>
                            <td>{{ $doc['invoice_no'] }}</td>
                            <td>{{ $doc['party_code'] }}</td>
                            <td>{{ $doc['party_name'] }}</td>
                            <td>{{ $doc['gst_number'] }}</td>
                            <td class="text-end fw-bold">{{ number_format($doc['amount'], 2) }}</td>
                            <td>{{ $doc['eway_bill'] ?: '-' }}</td>
                            <td class="text-truncate" style="max-width: 150px;" title="{{ $doc['irn_no'] }}">{{ $doc['irn_no'] ?: '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $doc['status'] == 'Generated' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $doc['status'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "Ok"
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($totals) && ($totals['count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Total ({{ $totals['count'] }} invoices):</td>
                            <td class="text-end">{{ number_format($totals['amount'], 2) }}</td>
                            <td colspan="2" class="text-center">Generated: {{ $totals['generated'] }} | Pending: {{ $totals['pending'] }}</td>
                            <td></td>
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
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                        <i class="bi bi-file-excel me-1"></i>Excel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="printBankAdvice()">
                        <i class="bi bi-bank me-1"></i>Print Bank Advice
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="printForm()">
                        <i class="bi bi-printer me-1"></i>Print Form
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" form="filterForm" class="btn btn-info btn-sm">
                        <i class="bi bi-eye me-1"></i>View
                    </button>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i>Close
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.other.invoice-documents") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.other.invoice-documents") }}?' + params.toString(), 'InvoiceDocuments', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}

function printBankAdvice() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('view_type', 'print');
    params.set('print_type', 'bank_advice');
    window.open('{{ route("admin.reports.sales.other.invoice-documents") }}?' + params.toString(), 'BankAdvice', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}

function printForm() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('view_type', 'print');
    params.set('print_type', 'form');
    window.open('{{ route("admin.reports.sales.other.invoice-documents") }}?' + params.toString(), 'PrintForm', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; }
.btn-sm { font-size: 0.75rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
