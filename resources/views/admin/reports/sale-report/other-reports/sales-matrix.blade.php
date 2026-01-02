@extends('layouts.admin')

@section('title', 'Sales Matrix')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.sales.other.sales-matrix') }}">
                <!-- Date Filters -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">From :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">To :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Company -->
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0 text-danger">Company :</label>
                    </div>
                    <div class="col-md-6">
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">-- Select Company --</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ ($companyId ?? '') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Division -->
                <div class="row g-0 mb-1 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="division_code" id="division_code" class="form-control form-control-sm" value="{{ $divisionCode ?? '00' }}">
                    </div>
                </div>

                <!-- Status -->
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Status :</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="status_code" class="form-control form-control-sm" value="{{ $statusCode ?? '' }}">
                    </div>
                </div>

                <!-- Show For -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Show For :</label>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="show_for" id="showable_party" value="Party" {{ ($showFor ?? 'Party') == 'Party' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="showable_party">Party</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="show_for" id="showable_area" value="Area" {{ ($showFor ?? '') == 'Area' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="showable_area">Area</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="show_for" id="showable_salesman" value="Salesman" {{ ($showFor ?? '') == 'Salesman' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="showable_salesman">Salesman</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="show_for" id="showable_route" value="Route" {{ ($showFor ?? '') == 'Route' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="showable_route">Route</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Salesman -->
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Salesman :</label>
                    </div>
                    <div class="col-md-6">
                        <select name="salesman_id" id="salesman_id" class="form-select form-select-sm">
                            <option value="">-- All Salesmen --</option>
                            @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ ($salesmanId ?? '') == $salesman->id ? 'selected' : '' }}>{{ $salesman->code }} - {{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Area -->
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Area :</label>
                    </div>
                    <div class="col-md-6">
                        <select name="area_id" id="area_id" class="form-select form-select-sm">
                            <option value="">-- All Areas --</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ ($areaId ?? '') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Route -->
                <div class="row g-0 mb-3 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Route :</label>
                    </div>
                    <div class="col-md-6">
                        <select name="route_id" id="route_id" class="form-select form-select-sm">
                            <option value="">-- All Routes --</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ ($routeId ?? '') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Value on -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-2 text-end pe-2">
                        <label class="fw-bold mb-0">Value on :</label>
                    </div>
                    <div class="col-md-10">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="value_on" id="val_net_sale" value="NetSale" {{ ($valueOn ?? 'NetSale') == 'NetSale' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="val_net_sale">Net Sale Rate</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="value_on" id="val_sale" value="Sale" {{ ($valueOn ?? '') == 'Sale' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="val_sale">Sale Rate</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="value_on" id="val_ws" value="WS" {{ ($valueOn ?? '') == 'WS' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="val_ws">WS Rate</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="value_on" id="val_spl" value="Spl" {{ ($valueOn ?? '') == 'Spl' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="val_spl">Spl Rate</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="value_on" id="val_cost" value="Cost" {{ ($valueOn ?? '') == 'Cost' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="val_cost">Cost Rate</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Checkboxes and Add Free Qty -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-md-4 offset-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="print_sales_return" id="printSalesReturn" {{ ($printSalesReturn ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="printSalesReturn">Print Sales Return value</label>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <label class="fw-bold mb-0 me-2">Add Free Qty [ Y / N ] :</label>
                            <input type="text" name="add_free_qty" id="add_free_qty" class="form-control form-control-sm text-center fw-bold" style="width: 40px;" value="{{ $addFreeQty ?? 'Y' }}" maxlength="1">
                        </div>
                    </div>
                </div>

                <hr style="border-top: 2px solid #000;">

                <!-- Footer Text and Buttons -->
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <label class="fw-bold mb-0 me-2" style="font-size: 0.9rem;">1. X-> Party Y-> Item / 2. X->Item Y->Party :</label>
                            <input type="text" name="matrix_type" id="matrix_type" class="form-control form-control-sm text-center fw-bold" style="width: 40px;" value="{{ $matrixType ?? '1' }}" maxlength="1">
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="submit" class="btn btn-primary border px-4 fw-bold shadow-sm me-2">Show</button>
                        <button type="submit" form="filterForm" class="btn btn-light border px-4 fw-bold shadow-sm me-2">Ok</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Matrix Report Table -->
    @if(isset($matrixData) && count($matrixData) > 0)
    <div class="card mt-3">
        <div class="card-header bg-primary text-white py-2">
            <strong>Sales Matrix ({{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }})</strong>
            <span class="float-end">{{ $showFor ?? 'Party' }} vs Items | {{ $valueOn ?? 'Net Sale' }} Rate</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px; overflow: auto;">
                <table class="table table-bordered table-sm mb-0" id="matrixTable">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="sticky-col bg-dark text-white" style="min-width: 150px;">
                                @if($matrixType == '1') {{ $showFor ?? 'Party' }} @else Item @endif
                            </th>
                            @if($matrixType == '1')
                                @foreach($itemsList as $itemId => $itemName)
                                <th class="text-center text-nowrap" style="min-width: 100px;">{{ Str::limit($itemName, 15) }}</th>
                                @endforeach
                            @else
                                @foreach($entitiesList as $entityId => $entityName)
                                <th class="text-center text-nowrap" style="min-width: 100px;">{{ Str::limit($entityName, 15) }}</th>
                                @endforeach
                            @endif
                            <th class="text-end bg-warning text-dark" style="min-width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($matrixType == '1')
                            @foreach($entitiesList as $entityId => $entityName)
                            <tr>
                                <td class="sticky-col bg-light fw-bold">{{ Str::limit($entityName, 25) }}</td>
                                @php $rowTotal = 0; @endphp
                                @foreach($itemsList as $itemId => $itemName)
                                    @php 
                                        $value = $matrixData[$entityId][$itemId] ?? 0;
                                        $rowTotal += $value;
                                    @endphp
                                <td class="text-end {{ $value > 0 ? '' : 'text-muted' }}">{{ $value > 0 ? number_format($value, 0) : '-' }}</td>
                                @endforeach
                                <td class="text-end bg-warning fw-bold">{{ number_format($rowTotal, 0) }}</td>
                            </tr>
                            @endforeach
                        @else
                            @foreach($itemsList as $itemId => $itemName)
                            <tr>
                                <td class="sticky-col bg-light fw-bold">{{ Str::limit($itemName, 25) }}</td>
                                @php $rowTotal = 0; @endphp
                                @foreach($entitiesList as $entityId => $entityName)
                                    @php 
                                        $value = $matrixData[$itemId][$entityId] ?? 0;
                                        $rowTotal += $value;
                                    @endphp
                                <td class="text-end {{ $value > 0 ? '' : 'text-muted' }}">{{ $value > 0 ? number_format($value, 0) : '-' }}</td>
                                @endforeach
                                <td class="text-end bg-warning fw-bold">{{ number_format($rowTotal, 0) }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot class="table-warning fw-bold sticky-bottom">
                        <tr>
                            <td class="sticky-col bg-warning">TOTAL</td>
                            @if($matrixType == '1')
                                @foreach($itemsList as $itemId => $itemName)
                                <td class="text-end">{{ number_format($totals['col_totals'][$itemId] ?? 0, 0) }}</td>
                                @endforeach
                            @else
                                @foreach($entitiesList as $entityId => $entityName)
                                <td class="text-end">{{ number_format($totals['col_totals'][$entityId] ?? 0, 0) }}</td>
                                @endforeach
                            @endif
                            <td class="text-end bg-success text-white">{{ number_format($totals['grand_total'] ?? 0, 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-4"><small class="text-muted">Items: {{ $totals['items_count'] ?? 0 }}</small></div>
                <div class="col-md-4"><small class="text-muted">{{ $showFor ?? 'Parties' }}: {{ $totals['entities_count'] ?? 0 }}</small></div>
                <div class="col-md-4 text-end"><small class="text-primary fw-bold">Grand Total: ₹{{ number_format($totals['grand_total'] ?? 0, 2) }}</small></div>
            </div>
        </div>
    </div>
    @elseif(request()->has('date_from'))
    <div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No records found for the selected filters. Please select a company and try again.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function viewReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    params.set('view_type', 'print');
    window.open('{{ route("admin.reports.sales.other.sales-matrix") }}?' + params.toString(), 'SalesMatrix', 'width=1200,height=800,scrollbars=yes,resizable=yes');
}
function closeWindow() { window.location.href = '{{ route("admin.reports.sales") }}'; }
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !['BUTTON', 'TEXTAREA'].includes(document.activeElement.tagName)) document.getElementById('filterForm').submit();
    if (e.key === 'Escape') closeWindow();
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.3rem 0.4rem; font-size: 0.75rem; vertical-align: middle; }
.sticky-col { position: sticky; left: 0; z-index: 1; }
.sticky-top { position: sticky; top: 0; z-index: 2; }
.sticky-bottom { position: sticky; bottom: 0; z-index: 2; }
#matrixTable th, #matrixTable td { white-space: nowrap; }
</style>
@endpush
