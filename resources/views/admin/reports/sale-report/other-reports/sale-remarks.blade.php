@extends('layouts.admin')
@section('title', 'Sale Remarks Report')
@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 fst-italic fw-bold" style="color: #1a0dab; font-family: 'Times New Roman', serif;">-: Sale Remarks Report :-</h4>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2" style="background-color: #e9ecef;">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.sale-remarks') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">P/N/A</span>
                            <select name="pending_filter" class="form-select">
                                <option value="A" {{ ($pendingFilter ?? 'A') == 'A' ? 'selected' : '' }}>All</option>
                                <option value="P" {{ ($pendingFilter ?? '') == 'P' ? 'selected' : '' }}>Pending</option>
                                <option value="N" {{ ($pendingFilter ?? '') == 'N' ? 'selected' : '' }}>Non-Pending</option>
                            </select>
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
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Stock</span>
                            <select name="stock_filter" class="form-select">
                                <option value="3" {{ ($stockFilter ?? '3') == '3' ? 'selected' : '' }}>All</option>
                                <option value="1" {{ ($stockFilter ?? '') == '1' ? 'selected' : '' }}>With Stock</option>
                                <option value="2" {{ ($stockFilter ?? '') == '2' ? 'selected' : '' }}>Without Stock</option>
                            </select>
                        </div>
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
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 90px;">Date</th>
                            <th style="width: 100px;">Bill No</th>
                            <th style="width: 80px;">Code</th>
                            <th>Party Name</th>
                            <th>Salesman</th>
                            <th>Remarks</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                            <th class="text-end" style="width: 90px;">Balance</th>
                            <th class="text-center" style="width: 70px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remarks ?? [] as $index => $r)
                        <tr class="{{ $r['status'] == 'Pending' ? 'table-warning' : '' }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $r['date'] }}</td>
                            <td>{{ $r['bill_no'] }}</td>
                            <td>{{ $r['party_code'] }}</td>
                            <td>{{ $r['party_name'] }}</td>
                            <td>{{ $r['salesman'] }}</td>
                            <td class="text-truncate" style="max-width: 200px;" title="{{ $r['remarks'] }}">{{ $r['remarks'] }}</td>
                            <td class="text-end fw-bold">{{ number_format($r['amount'], 2) }}</td>
                            <td class="text-end {{ $r['balance'] > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($r['balance'], 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $r['status'] == 'Pending' ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ $r['status'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No sales with remarks found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($totals) && ($totals['count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">Total ({{ $totals['count'] }} bills | Pending: {{ $totals['pending'] }} | Paid: {{ $totals['paid'] }}):</td>
                            <td class="text-end">{{ number_format($totals['amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['balance'], 2) }}</td>
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
                <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                    <i class="bi bi-file-excel me-1"></i>Excel
                </button>
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
    window.open('{{ route("admin.reports.sales.other.sale-remarks") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.other.sale-remarks") }}?' + params.toString(), 'SaleRemarks', 'width=1100,height=800,scrollbars=yes,resizable=yes');
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
