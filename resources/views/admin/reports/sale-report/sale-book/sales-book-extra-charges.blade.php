@extends('layouts.admin')

@section('title', 'Sale Book Extra Charges')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background: linear-gradient(135deg, #e2d5f1 0%, #d4c4e8 100%);">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-purple fst-italic fw-bold" style="color: #6f42c1;">SALE BOOK EXTRA CHARGES</h4>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <form method="GET" id="filterForm">
                <div class="row g-2">
                    <!-- Row 1: Date Range & Format -->
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
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">D/S</span>
                            <select name="report_format" class="form-select">
                                <option value="D" {{ ($reportFormat ?? 'D') == 'D' ? 'selected' : '' }}>D(etailed)</option>
                                <option value="S" {{ ($reportFormat ?? '') == 'S' ? 'selected' : '' }}>S(ummarised)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">L/C/B</span>
                            <select name="local_central" class="form-select">
                                <option value="B" {{ ($localCentral ?? 'B') == 'B' ? 'selected' : '' }}>B(oth)</option>
                                <option value="L" {{ ($localCentral ?? '') == 'L' ? 'selected' : '' }}>L(ocal)</option>
                                <option value="C" {{ ($localCentral ?? '') == 'C' ? 'selected' : '' }}>C(entral)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">GSTN</span>
                            <select name="gstn_filter" class="form-select">
                                <option value="3" {{ ($gstnFilter ?? '3') == '3' ? 'selected' : '' }}>3.All</option>
                                <option value="1" {{ ($gstnFilter ?? '') == '1' ? 'selected' : '' }}>1.With GSTN</option>
                                <option value="2" {{ ($gstnFilter ?? '') == '2' ? 'selected' : '' }}>2.Without GSTN</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Customer -->
                    <div class="col-md-12">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Customer</span>
                            <select name="customer_id" class="form-select">
                                <option value="">All Customers</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}" {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Location Filters -->
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Salesman</span>
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

                <!-- Row 4: Display Options -->
                <div class="row mt-2">
                    <div class="col-md-8">
                        <div class="d-flex flex-wrap gap-3 align-items-center border rounded p-2 bg-light">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="tag_customer" id="tagCustomer" value="1" {{ ($tagCustomer ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="tagCustomer">Tag Customer</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="order_by_customer" id="orderByCustomer" value="1" {{ ($orderByCustomer ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="orderByCustomer">Order by Customer</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                                <i class="bi bi-file-excel"></i> Excel
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="viewReport()">
                                <i class="bi bi-printer"></i> View
                            </button>
                            <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary btn-sm">Close</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($sales) && $sales->count() > 0)
    <div class="row g-2 mb-2">
        <div class="col">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Bills</small>
                    <h6 class="mb-0">{{ number_format($totals['count'] ?? 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">NT Amt</small>
                    <h6 class="mb-0">₹{{ number_format($totals['nt_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Discount</small>
                    <h6 class="mb-0">₹{{ number_format($totals['dis_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-2 text-center">
                    <small>Scheme</small>
                    <h6 class="mb-0">₹{{ number_format($totals['scm_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-dark text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">SC/FT</small>
                    <h6 class="mb-0">₹{{ number_format(($totals['sc_amount'] ?? 0) + ($totals['ft_amount'] ?? 0), 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Tax</small>
                    <h6 class="mb-0">₹{{ number_format($totals['tax_amount'] ?? 0, 2) }}</h6>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-2 text-center">
                    <small class="text-white-50">Net Amt</small>
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
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 30px;">#</th>
                            <th style="width: 80px;">Date</th>
                            <th style="width: 70px;">Bill No</th>
                            <th style="width: 50px;">Code</th>
                            <th>Party Name</th>
                            <th class="text-end" style="width: 80px;">NT Amt</th>
                            <th class="text-end" style="width: 70px;">Disc</th>
                            <th class="text-end" style="width: 70px;">Scheme</th>
                            <th class="text-end" style="width: 60px;">SC</th>
                            <th class="text-end" style="width: 60px;">FT</th>
                            <th class="text-end" style="width: 70px;">Tax</th>
                            <th class="text-end" style="width: 60px;">TCS</th>
                            <th class="text-end" style="width: 85px;">Net Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales ?? [] as $index => $sale)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none fw-bold">
                                    {{ $sale->series }}{{ $sale->invoice_no }}
                                </a>
                            </td>
                            <td>{{ $sale->customer->code ?? '' }}</td>
                            <td>{{ Str::limit($sale->customer->name ?? 'N/A', 18) }}</td>
                            <td class="text-end">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                            <td class="text-end text-warning">{{ number_format($sale->scm_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->sc_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->ft_amount ?? 0, 2) }}</td>
                            <td class="text-end text-info">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($sale->tcs_amount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "Search" to generate Extra Charges report
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(isset($sales) && $sales->count() > 0)
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Grand Total ({{ number_format($totals['count'] ?? 0) }} Bills):</td>
                            <td class="text-end">{{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['scm_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['sc_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['ft_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($totals['tcs_amount'] ?? 0, 2) }}</td>
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

@push('scripts')
<script>
function exportToExcel() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('export', 'excel');
    window.open('{{ route("admin.reports.sales.sales-book-extra-charges") }}?' + params.toString(), '_blank');
}

function viewReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.sales-book-extra-charges") }}?' + params.toString(), 'ExtraCharges', 'width=1200,height=800,scrollbars=yes,resizable=yes');
}
</script>
@endpush

@push('styles')
<style>
.input-group-text { font-size: 0.7rem; padding: 0.2rem 0.4rem; }
.form-control, .form-select { font-size: 0.75rem; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.72rem; vertical-align: middle; }
.btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
@endpush
