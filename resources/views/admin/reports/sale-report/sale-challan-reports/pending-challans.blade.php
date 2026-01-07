@extends('layouts.admin')

@section('title', 'Pending Challans')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">LIST OF PENDING CHALLANS</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.pending-challans') }}">
                <div class="row g-2 align-items-end">
                    <!-- Row 1: Date Range -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party</span>
                            <select name="customer_id" class="form-select">
                                <option value="">All</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Salesman</span>
                            <select name="salesman_id" class="form-select">
                                <option value="">All</option>
                                @foreach($salesmen ?? [] as $salesman)
                                    <option value="{{ $salesman->id }}" {{ ($salesmanId ?? '') == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Route</span>
                            <select name="route_id" class="form-select">
                                <option value="">All</option>
                                @foreach($routes ?? [] as $route)
                                    <option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>
                                        {{ $route->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2: More Filters -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Area</span>
                            <select name="area_id" class="form-select">
                                <option value="">All</option>
                                @foreach($areas ?? [] as $area)
                                    <option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Flag</span>
                            <select name="flag" class="form-select text-uppercase">
                                <option value="">All</option>
                                <option value="C" {{ ($flag ?? '') == 'C' ? 'selected' : '' }}>Cash</option>
                                <option value="R" {{ ($flag ?? '') == 'R' ? 'selected' : '' }}>Credit</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <u>V</u>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">
                            <u>P</u>rint
                        </button>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-light border px-4 fw-bold shadow-sm">
                            <u>C</u>lose
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table - Only show when view is clicked -->
    @if(request()->has('view') && isset($challans) && $challans->count() > 0)
    <!-- Summary Cards -->
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2 text-center">
                    <small>Pending Challans</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Total Amount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 55vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 80px;">Chaln.Date</th>
                            <th style="width: 80px;">Chln.No</th>
                            <th>Party Name</th>
                            <th class="text-end" style="width: 100px;">Amount</th>
                            <th style="width: 80px;">Inv.Date</th>
                            <th style="width: 80px;">Inv.No</th>
                            <th class="text-center" style="width: 80px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($challans ?? [] as $index => $challan)
                        <tr data-id="{{ $challan->id }}" class="challan-row" style="cursor: pointer;">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $challan->challan_date ? $challan->challan_date->format('d-m-Y') : '' }}</td>
                            <td>
                                <a href="{{ route('admin.sale-challan.show', $challan->id) }}" class="text-primary">
                                    {{ $challan->challan_no }}
                                </a>
                            </td>
                            <td>{{ Str::limit($challan->customer->name ?? 'N/A', 35) }}</td>
                            <td class="text-end fw-bold">{{ number_format($challan->net_amount, 2) }}</td>
                            <td>{{ $challan->saleTransaction ? $challan->saleTransaction->sale_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $challan->saleTransaction->invoice_no ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark">Pending</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No pending challans found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(($totals['count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Total ({{ number_format($totals['count'] ?? 0) }} Challans):</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Additional Action Buttons -->
    <div class="card mt-2">
        <div class="card-body py-2">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="modifyChallan()" id="modifyBtn" disabled>
                    <i class="bi bi-pencil me-1"></i>Modify (Enter)
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="showBillDetails()" id="billDetailsBtn" disabled>
                    <i class="bi bi-file-text me-1"></i>Bill Details (F11)
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Bill Details Modal -->
<div class="modal fade" id="billDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2 bg-primary text-white">
                <h6 class="modal-title">Challan Details</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="billDetailsContent">
                Loading...
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedChallanId = null;

// Row selection
document.querySelectorAll('.challan-row').forEach(row => {
    row.addEventListener('click', function() {
        document.querySelectorAll('.challan-row').forEach(r => r.classList.remove('table-primary'));
        this.classList.add('table-primary');
        selectedChallanId = this.dataset.id;
        document.getElementById('modifyBtn').disabled = false;
        document.getElementById('billDetailsBtn').disabled = false;
    });
    
    row.addEventListener('dblclick', function() {
        selectedChallanId = this.dataset.id;
        modifyChallan();
    });
});

function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.pending-challans") }}?' + params.toString(), '_blank');
}

function printReport() {
    window.open('{{ route("admin.reports.sales.pending-challans") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

function modifyChallan() {
    if (selectedChallanId) {
        window.location.href = '{{ url("admin/sale-challan/modification") }}/' + selectedChallanId;
    }
}

function showBillDetails() {
    if (selectedChallanId) {
        const modal = new bootstrap.Modal(document.getElementById('billDetailsModal'));
        document.getElementById('billDetailsContent').innerHTML = '<div class="text-center py-4"><div class="spinner-border"></div></div>';
        modal.show();
        
        fetch('{{ url("admin/sale-challan") }}/' + selectedChallanId)
            .then(response => response.text())
            .then(html => {
                document.getElementById('billDetailsContent').innerHTML = `
                    <iframe src="{{ url("admin/sale-challan") }}/${selectedChallanId}" style="width:100%;height:500px;border:none;"></iframe>
                `;
            })
            .catch(err => {
                document.getElementById('billDetailsContent').innerHTML = '<div class="alert alert-danger">Error loading details</div>';
            });
    }
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        printReport();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
    if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();
        exportToExcel();
    }
    if (e.key === 'F7') {
        e.preventDefault();
        printReport();
    }
    if (e.key === 'F11') {
        e.preventDefault();
        showBillDetails();
    }
    if (e.key === 'Enter' && selectedChallanId) {
        e.preventDefault();
        modifyChallan();
    }
    if (e.key === 'Escape') {
        window.location.href = '{{ route("admin.reports.sales") }}';
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.8rem; border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
.challan-row:hover { background-color: #e3f2fd !important; }
</style>
@endpush
