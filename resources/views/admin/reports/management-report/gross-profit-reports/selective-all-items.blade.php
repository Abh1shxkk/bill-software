@extends('layouts.admin')

@section('title', 'Item Wise Gross Profit')

@section('content')
<div class="container-fluid">
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">Item Wise Gross Profit</h4>
        </div>
    </div>

    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="{{ route('admin.reports.management.gross-profit.selective-all-items') }}">
                <!-- From & To Date, View Type -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>F</u>rom :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold mb-0"><u>T</u>o :</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', date('Y-m-d')) }}" style="width: 140px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">View :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="view_type" id="viewAll" value="all" {{ request('view_type', 'all') == 'all' ? 'checked' : '' }} onchange="toggleItemSelection()">
                            <label class="form-check-label fw-bold" for="viewAll"><u>A</u>ll</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="view_type" id="viewSelective" value="selective" {{ request('view_type') == 'selective' ? 'checked' : '' }} onchange="toggleItemSelection()">
                            <label class="form-check-label fw-bold" for="viewSelective"><u>S</u>elective</label>
                        </div>
                    </div>
                </div>

                <!-- Company & Type -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Company :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="company_code" id="companyCode" class="form-control form-control-sm text-uppercase" value="{{ request('company_code', '00') }}" style="width: 50px;" onchange="lookupCompany()">
                    </div>
                    <div class="col-auto">
                        <select name="company_id" id="companySelect" class="form-select form-select-sm" style="width: 200px;" onchange="updateCompanyCode()">
                            <option value="">-- All Companies --</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto ms-4">
                        <label class="fw-bold mb-0">Type :</label>
                    </div>
                    <div class="col-auto">
                        <select name="type" class="form-select form-select-sm" style="width: 120px;">
                            <option value="BOTH" {{ request('type', 'BOTH') == 'BOTH' ? 'selected' : '' }}>BOTH</option>
                            <option value="SALE" {{ request('type') == 'SALE' ? 'selected' : '' }}>SALE</option>
                            <option value="PURCHASE" {{ request('type') == 'PURCHASE' ? 'selected' : '' }}>PURCHASE</option>
                        </select>
                    </div>
                </div>

                <!-- Category -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Category :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="category_code" class="form-control form-control-sm text-uppercase" value="{{ request('category_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <select name="category_id" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">-- All Categories --</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- GP on & Division & With BE & GP Filter -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">GP on - S(rate) / P(rate) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="gp_on" class="form-control form-control-sm text-center text-uppercase" value="{{ request('gp_on', 'S') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Division :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="division" class="form-control form-control-sm text-uppercase" value="{{ request('division', '00') }}" style="width: 50px;">
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">With BE [ Y / N ] :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="with_be" class="form-control form-control-sm text-center text-uppercase" value="{{ request('with_be', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">GP - P(lus) / M(inus) / B(oth) :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="gp_filter" class="form-control form-control-sm text-center text-uppercase" value="{{ request('gp_filter', 'N') }}" maxlength="1" style="width: 40px;">
                    </div>
                    <div class="col-auto ms-2">
                        <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="applyFilters()">Ok</button>
                    </div>
                </div>

                <!-- Item Selection Grid (for Selective mode) -->
                <div id="itemSelectionGrid" class="mb-2" style="display: {{ request('view_type') == 'selective' ? 'block' : 'none' }};">
                    <div class="card">
                        <div class="card-body p-2">
                            <div class="table-responsive" style="max-height: 250px;">
                                <table class="table table-bordered table-sm mb-0" id="itemTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 50px;">SNo</th>
                                            <th style="width: 100px;">Code</th>
                                            <th>Name</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedItems">
                                        @for($i = 1; $i <= 10; $i++)
                                        <tr>
                                            <td class="text-center">{{ $i }}.</td>
                                            <td><input type="text" name="selected_items[]" class="form-control form-control-sm item-code-input" data-row="{{ $i }}" style="border: none; background: transparent;"></td>
                                            <td><span class="item-name-display" data-row="{{ $i }}"></span></td>
                                        </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            <div class="row g-2 mt-2 align-items-center">
                                <div class="col-auto">
                                    <label class="fw-bold mb-0">Total Companies :</label>
                                </div>
                                <div class="col-auto">
                                    <input type="text" id="totalItems" class="form-control form-control-sm" readonly style="width: 80px; background: #fff;" value="0">
                                </div>
                                <div class="col-auto ms-auto">
                                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="confirmSelection()">Ok (End)</button>
                                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="removeLastItem()"><u>R</u>emove</button>
                                    <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="removeAllItems()">Remove All</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Filters -->
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Customer :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="customer_code" class="form-control form-control-sm text-uppercase" value="{{ request('customer_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <select name="customer_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Customers --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Salesman :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="salesman_code" class="form-control form-control-sm text-uppercase" value="{{ request('salesman_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <select name="salesman_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Salesmen --</option>
                            @foreach($salesmen ?? [] as $salesman)
                                <option value="{{ $salesman->id }}" {{ request('salesman_id') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Area :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="area_code" class="form-control form-control-sm text-uppercase" value="{{ request('area_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <select name="area_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Areas --</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Route :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="route_code" class="form-control form-control-sm text-uppercase" value="{{ request('route_code', '00') }}" style="width: 50px;">
                    </div>
                    <div class="col-auto">
                        <select name="route_id" class="form-select form-select-sm" style="width: 250px;">
                            <option value="">-- All Routes --</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Sort By & Order -->
                <div class="row g-2 mb-2 align-items-center" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-auto">
                        <label class="fw-bold mb-0">Sort By :</label>
                    </div>
                    <div class="col-auto">
                        <select name="sort_by" class="form-select form-select-sm" style="width: 150px;">
                            <option value="name" {{ request('sort_by', 'name') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="code" {{ request('sort_by') == 'code' ? 'selected' : '' }}>Code</option>
                            <option value="qty" {{ request('sort_by') == 'qty' ? 'selected' : '' }}>Quantity</option>
                            <option value="sale_amount" {{ request('sort_by') == 'sale_amount' ? 'selected' : '' }}>Sale Amount</option>
                            <option value="gp_amount" {{ request('sort_by') == 'gp_amount' ? 'selected' : '' }}>GP Amount</option>
                            <option value="gp_percent" {{ request('sort_by') == 'gp_percent' ? 'selected' : '' }}>GP %</option>
                        </select>
                    </div>
                    <div class="col-auto ms-3">
                        <label class="fw-bold mb-0">Order :</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="order" id="asc" value="asc" {{ request('order', 'asc') == 'asc' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="asc">Asc</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="order" id="desc" value="desc" {{ request('order') == 'desc' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="desc">Desc</label>
                        </div>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2"><u>V</u>iew</button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm" onclick="closeWindow()"><u>C</u>lose</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('view') && isset($reportData) && count($reportData) > 0)
    <div class="card mt-2">
        <div class="card-header py-1 d-flex justify-content-between align-items-center">
            <span class="fw-bold">Report Results</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()"><i class="bi bi-printer"></i> Print</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">S.No</th>
                            <th>Item Name</th>
                            <th>Company</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Sale Amt</th>
                            <th class="text-end">Pur Amt</th>
                            <th class="text-end">GP Amt</th>
                            <th class="text-end">GP %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQty = 0;
                            $totalSale = 0;
                            $totalPurchase = 0;
                            $totalGP = 0;
                        @endphp
                        @foreach($reportData as $index => $row)
                        @php
                            $totalQty += $row['qty'];
                            $totalSale += $row['sale_amount'];
                            $totalPurchase += $row['purchase_amount'];
                            $totalGP += $row['gp_amount'];
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td>{{ $row['company_name'] }}</td>
                            <td class="text-center">{{ number_format($row['qty'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['sale_amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['purchase_amount'], 2) }}</td>
                            <td class="text-end {{ $row['gp_amount'] < 0 ? 'text-danger' : '' }}">{{ number_format($row['gp_amount'], 2) }}</td>
                            <td class="text-end {{ $row['gp_percent'] < 0 ? 'text-danger' : '' }}">{{ number_format($row['gp_percent'], 2) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-center">{{ number_format($totalQty, 2) }}</td>
                            <td class="text-end">{{ number_format($totalSale, 2) }}</td>
                            <td class="text-end">{{ number_format($totalPurchase, 2) }}</td>
                            <td class="text-end {{ $totalGP < 0 ? 'text-danger' : '' }}">{{ number_format($totalGP, 2) }}</td>
                            <td class="text-end">{{ $totalSale > 0 ? number_format($totalGP / $totalSale * 100, 2) : '0.00' }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-2">No records found for the selected criteria.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
var items = @json($items ?? []);
var companies = @json($companies ?? []);
var selectedItemIds = [];

function toggleItemSelection() {
    var viewType = $('input[name="view_type"]:checked').val();
    if (viewType === 'selective') {
        $('#itemSelectionGrid').show();
    } else {
        $('#itemSelectionGrid').hide();
    }
}

function lookupCompany() {
    var code = $('#companyCode').val();
    var company = companies.find(c => c.id == code || c.code == code);
    if (company) {
        $('#companySelect').val(company.id);
    }
}

function updateCompanyCode() {
    var id = $('#companySelect').val();
    $('#companyCode').val(id || '00');
}

function applyFilters() {
    // Apply current filter settings
}

function removeLastItem() {
    var filledRows = $('.item-code-input').filter(function() {
        return $(this).val();
    });
    
    if (filledRows.length) {
        var lastRow = filledRows.last();
        var rowNum = lastRow.data('row');
        var itemId = lastRow.val();
        
        lastRow.val('');
        $('.item-name-display[data-row="' + rowNum + '"]').text('');
        selectedItemIds = selectedItemIds.filter(id => id != itemId);
        updateTotalItems();
    }
}

function removeAllItems() {
    $('.item-code-input').val('');
    $('.item-name-display').text('');
    selectedItemIds = [];
    updateTotalItems();
}

function confirmSelection() {
    // Selection confirmed
}

function updateTotalItems() {
    $('#totalItems').val(selectedItemIds.length);
}

function closeWindow() {
    window.location.href = '{{ route("admin.dashboard") }}';
}

function printReport() {
    window.open('{{ route("admin.reports.management.gross-profit.selective-all-items") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}

// Item code input handler
$(document).on('change', '.item-code-input', function() {
    var code = $(this).val();
    var rowNum = $(this).data('row');
    var item = items.find(i => i.id == code || i.code == code);
    
    if (item) {
        $(this).val(item.id);
        $('.item-name-display[data-row="' + rowNum + '"]').text(item.name);
        if (!selectedItemIds.includes(item.id)) {
            selectedItemIds.push(item.id);
        }
    } else {
        $('.item-name-display[data-row="' + rowNum + '"]').text('');
    }
    updateTotalItems();
});

$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'f') {
        e.preventDefault();
        $('input[name="from_date"]').focus();
    }
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        closeWindow();
    }
    if (e.altKey && e.key.toLowerCase() === 'a') {
        e.preventDefault();
        $('#viewAll').prop('checked', true).trigger('change');
    }
    if (e.altKey && e.key.toLowerCase() === 's') {
        e.preventDefault();
        $('#viewSelective').prop('checked', true).trigger('change');
    }
    if (e.altKey && e.key.toLowerCase() === 'r') {
        e.preventDefault();
        removeLastItem();
    }
});
</script>
@endpush

@push('styles')
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
#itemTable input { width: 100%; }
</style>
@endpush
