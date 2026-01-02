@extends('layouts.admin')

@section('title', 'Sale Book With Item Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold">SALE BOOK WITH ITEM DETAILS</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
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
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 2: Party -->
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party</span>
                            <select name="customer_id" class="form-select">
                                <option value="">All</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 3: Report Type -->
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">1. Sale Book / 2. Sales Return / 3. Expiry</span>
                            <select name="report_type" class="form-select" style="width: 60px;">
                                <option value="1" {{ ($reportType ?? '1') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($reportType ?? '') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ ($reportType ?? '') == '3' ? 'selected' : '' }}>3</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <!-- Row 4: Series -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Series</span>
                            <select name="series" class="form-select">
                                <option value="">All</option>
                                @foreach($seriesList ?? [] as $s)
                                    <option value="{{ $s }}" {{ ($series ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <fieldset class="border rounded p-2 mt-2">
                    <legend class="float-none w-auto px-2 text-primary small">Filters</legend>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Sales Man</span>
                                <select name="salesman_id" class="form-select">
                                    <option value="">All</option>
                                    @foreach($salesmen ?? [] as $salesman)
                                        <option value="{{ $salesman->id }}" {{ ($salesmanId ?? '') == $salesman->id ? 'selected' : '' }}>
                                            {{ $salesman->code ?? '' }} - {{ $salesman->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Area</span>
                                <select name="area_id" class="form-select">
                                    <option value="">All</option>
                                    @foreach($areas ?? [] as $area)
                                        <option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Route</span>
                                <select name="route_id" class="form-select">
                                    <option value="">All</option>
                                    @foreach($routes ?? [] as $route)
                                        <option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">State</span>
                                <select name="state_id" class="form-select">
                                    <option value="">All</option>
                                    @foreach($states ?? [] as $state)
                                        <option value="{{ $state->id }}" {{ ($stateId ?? '') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($totals) && ($totals['items_count'] ?? 0) > 0)
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Items</small>
                    <h6 class="mb-0">{{ number_format($totals['items_count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Qty</small>
                    <h6 class="mb-0">{{ number_format($totals['qty'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Free</small>
                    <h6 class="mb-0">{{ number_format($totals['free_qty'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2 text-center">
                    <small>Discount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['discount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-2 text-center">
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
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th style="width: 80px;">Date</th>
                            <th style="width: 70px;">Bill No</th>
                            <th>Customer</th>
                            <th style="width: 70px;">Item Code</th>
                            <th>Item Name</th>
                            <th style="width: 70px;">Batch</th>
                            <th class="text-end" style="width: 50px;">Qty</th>
                            <th class="text-end" style="width: 50px;">Free</th>
                            <th class="text-end" style="width: 70px;">Rate</th>
                            <th class="text-end" style="width: 70px;">Disc</th>
                            <th class="text-end" style="width: 70px;">Tax</th>
                            <th class="text-end" style="width: 90px;">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items ?? [] as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->saleTransaction->sale_date->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.sale.show', $item->sale_transaction_id) }}" class="text-decoration-none">
                                    {{ $item->saleTransaction->series ?? '' }}{{ $item->saleTransaction->invoice_no }}
                                </a>
                            </td>
                            <td>{{ Str::limit($item->saleTransaction->customer->name ?? 'N/A', 20) }}</td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ Str::limit($item->item_name, 25) }}</td>
                            <td>{{ $item->batch_no ?? '' }}</td>
                            <td class="text-end">{{ number_format($item->qty) }}</td>
                            <td class="text-end">{{ number_format($item->free_qty ?? 0) }}</td>
                            <td class="text-end">{{ number_format((float)($item->sale_rate ?? 0), 2) }}</td>
                            <td class="text-end">{{ number_format((float)($item->discount_amount ?? 0), 2) }}</td>
                            <td class="text-end">{{ number_format((float)($item->tax_amount ?? 0), 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format((float)($item->net_amount ?? 0), 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters to generate Sale Book With Item Details
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($totals) && ($totals['items_count'] ?? 0) > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">Grand Total ({{ number_format($totals['items_count'] ?? 0) }} Items):</td>
                            <td class="text-end">{{ number_format($totals['qty'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($totals['free_qty'] ?? 0) }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($totals['discount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
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
                    <button type="button" class="btn btn-outline-secondary btn-sm">Format2</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm">Format 3</button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info btn-sm" onclick="viewReport()">
                        <i class="bi bi-eye me-1"></i>View
                    </button>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sale-sheet") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.sale-sheet") }}?' + params.toString(), 'SaleSheet', 'width=1100,height=800,scrollbars=yes,resizable=yes');
}
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; min-width: auto; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.25rem 0.4rem; font-size: 0.73rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
fieldset { background: #f8f9fa; }
legend { font-size: 0.8rem; }
</style>
@endpush
