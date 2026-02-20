@extends('layouts.admin')

@section('title', 'Receipt from Customer')

@section('content')
<style>
    .compact-form { font-size: 11px; padding: 8px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .field-group label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; color: #c00; }
    .field-group input, .field-group select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #e9ecef; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; border-radius: 0 !important; }

    /* Searchable custom dropdown (Select2 replacement) */
    .custom-select-hidden { display: none !important; }
    .searchable-select-wrapper { position: relative; }
    .searchable-select-input { width: 100%; }
    .searchable-select-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 220px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ced4da;
        border-top: none;
        z-index: 1050;
        display: none;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }
    .searchable-select-item {
        padding: 6px 8px;
        font-size: 11px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        line-height: 1.3;
    }
    .searchable-select-item:last-child { border-bottom: none; }
    .searchable-select-item:hover { background: #f5f9ff; }
    .searchable-select-item.highlighted { background: #0d6efd; color: #fff; }
    .searchable-select-item.selected { background: #e7f1ff; font-weight: 600; }
    .customer-list-item.customer-highlighted { background: #0d6efd !important; color: #fff !important; }
    .searchable-select-empty {
        padding: 6px 8px;
        font-size: 11px;
        color: #6c757d;
    }
    .kb-focus-ring {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.12rem rgba(13, 110, 253, 0.25) !important;
    }
    
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .total-section { background: #e0f7fa; border: 1px solid #00acc1; padding: 8px; border-radius: 4px; }
    .total-label { color: #c00; font-weight: bold; font-size: 12px; }
    .tds-display { color: #0000ff; font-size: 12px; text-align: center; margin: 10px 0; }
    
    .row-complete { background-color: #e8f5e9 !important; }
    .row-complete td { background-color: #e8f5e9 !important; }
    .row-complete input { background-color: #e8f5e9 !important; }
    
    /* Row selection - blue border on all sides */
    .row-selected td { border-top: 2px solid #007bff !important; border-bottom: 2px solid #007bff !important; }
    .row-selected td:first-child { border-left: 2px solid #007bff !important; }
    .row-selected td:last-child { border-right: 2px solid #007bff !important; }
    #itemsTableBody tr { cursor: pointer; }
    #itemsTableBody tr:hover { background-color: #f0f7ff; }
    
    /* Customer Select Modal */
    .customer-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7); width: 90%; max-width: 700px; z-index: 9999; opacity: 0; transition: all 0.3s ease-in-out; }
    .customer-modal.show { display: block; transform: translate(-50%, -50%) scale(1); opacity: 1; }
    .customer-modal-content { background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); overflow: hidden; }
    .customer-modal-header { padding: 1rem 1.5rem; background: #ff6b35; color: white; display: flex; justify-content: space-between; align-items: center; }
    .customer-modal-title { margin: 0; font-size: 1.2rem; font-weight: 600; }
    .customer-modal-body { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .customer-modal-footer { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px; }
    .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 9998; }
    .modal-backdrop.show { display: block; }
    .btn-close-modal { background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
    .customer-list-item { padding: 8px 12px; border-bottom: 1px solid #eee; cursor: pointer; }
    .customer-list-item:hover { background: #f0f7ff; }
    .customer-list-item.selected { background: #007bff; color: white; }
    
    /* Adjustment Modal Styles */
    .adjustment-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10002;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    .adjustment-modal-backdrop.show {
        display: block;
        opacity: 1;
    }
    .adjustment-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        width: 80%;
        max-width: 750px;
        z-index: 10003;
        opacity: 0;
        transition: all 0.3s ease;
    }
    .adjustment-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    .adjustment-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }
    .adjustment-modal-header {
        padding: 1rem 1.5rem;
        background: #0d6efd;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .adjustment-modal-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }
    .adjustment-modal-body {
        padding: 1rem;
        background: #fff;
    }
    .adjustment-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-close-adj { background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
    
    /* Bank Details Modal Styles */
    .bank-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10004;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .bank-modal-backdrop.show {
        display: block;
        opacity: 1;
    }
    .bank-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        width: 500px;
        z-index: 10005;
        opacity: 0;
        transition: all 0.3s ease;
    }
    .bank-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    .bank-modal-content {
        background: white;
        border-radius: 4px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }
    .bank-modal-header {
        padding: 8px 15px;
        background: #e9ecef;
        border-bottom: 1px solid #dee2e6;
    }
    .bank-modal-title {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }
    .bank-modal-body {
        padding: 15px;
        background: #f8f9fa;
    }
    .bank-modal-footer {
        padding: 10px 15px;
        background: #e9ecef;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .bank-field-group {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .bank-field-group label {
        width: 80px;
        font-weight: 600;
        font-size: 12px;
    }
    .bank-field-group input, .bank-field-group select {
        flex: 1;
        font-size: 12px;
        padding: 4px 8px;
        height: 28px;
        border: 1px solid #ced4da;
        border-radius: 0;
    }
    
    /* Cash Highlighted Style */
    .cheque-no.cash-highlighted {
        font-weight: bold !important;
        text-transform: uppercase !important;
        background-color: #d4edda !important;
        color: #155724 !important;
        border-color: #28a745 !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i> Receipt from Customer</h5>
    <a href="{{ route('admin.customer-receipt.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-list"></i> All Receipts
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <form id="receiptForm" method="POST" autocomplete="off">
            @csrf

            <!-- Header Section -->
            <div class="header-section">
                <div class="row g-2">
                    <!-- Left Column -->
                    <div class="col-md-4">
                        <div class="field-group mb-2">
                            <label style="width: 60px;">Date :</label>
                            <input type="date" class="form-control" name="receipt_date" id="receiptDate" value="{{ date('Y-m-d') }}" style="width: 130px;">
                            <input type="text" class="form-control readonly-field" id="dayName" value="{{ date('l') }}" readonly style="width: 80px;">
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 60px;">Trn No. :</label>
                            <input type="text" class="form-control readonly-field" name="trn_no" id="trnNo" value="{{ $nextTrnNo }}" readonly style="width: 80px;">
                        </div>
                        <div class="field-group">
                            <label style="width: 60px;">Ledger :</label>
                            <input type="text" class="form-control" name="ledger" id="ledger" value="CL" style="width: 50px;">
                        </div>
                    </div>
                    
                    <!-- Middle Column -->
                    <div class="col-md-4">
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Sales Man</label>
                            <input type="hidden" name="salesman_code" id="salesmanCode">
                            <select class="form-control no-select2 custom-select-hidden" name="salesman_id" id="salesmanSelect" style="width: 230px;">
                                <option value="">Select Salesman</option>
                                @foreach($salesmen as $sm)
                                <option value="{{ $sm->id }}" data-code="{{ $sm->code }}">{{ $sm->name }}</option>
                                @endforeach
                            </select>
                            <div class="searchable-select-wrapper" id="salesmanSearchWrapper" style="width: 230px;">
                                <input type="text"
                                       class="form-control searchable-select-input"
                                       id="salesmanSearchInput"
                                       placeholder="Type to search salesman..."
                                       autocomplete="off">
                                <div class="searchable-select-list" id="salesmanDropdownList"></div>
                            </div>
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Area</label>
                            <input type="hidden" name="area_code" id="areaCode">
                            <select class="form-control no-select2 custom-select-hidden" name="area_id" id="areaSelect" style="width: 230px;">
                                <option value="">Select Area</option>
                                @foreach($areas as $area)
                                <option value="{{ $area->id }}" data-code="{{ $area->alter_code }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            <div class="searchable-select-wrapper" id="areaSearchWrapper" style="width: 230px;">
                                <input type="text"
                                       class="form-control searchable-select-input"
                                       id="areaSearchInput"
                                       placeholder="Type to search area..."
                                       autocomplete="off">
                                <div class="searchable-select-list" id="areaDropdownList"></div>
                            </div>
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Route</label>
                            <input type="hidden" name="route_code" id="routeCode">
                            <select class="form-control no-select2 custom-select-hidden" name="route_id" id="routeSelect" style="width: 230px;">
                                <option value="">Select Route</option>
                                @foreach($routes as $route)
                                <option value="{{ $route->id }}" data-code="{{ $route->alter_code }}">{{ $route->name }}</option>
                                @endforeach
                            </select>
                            <div class="searchable-select-wrapper" id="routeSearchWrapper" style="width: 230px;">
                                <input type="text"
                                       class="form-control searchable-select-input"
                                       id="routeSearchInput"
                                       placeholder="Type to search route..."
                                       autocomplete="off">
                                <div class="searchable-select-list" id="routeDropdownList"></div>
                            </div>
                        </div>
                        <div class="field-group">
                            <label style="width: 70px;">Bank :</label>
                            <select class="form-control no-select2 custom-select-hidden" name="bank_code" id="bankSelect" style="width: 150px;">
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                <option value="{{ $bank->alter_code }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                            <div class="searchable-select-wrapper" id="bankSearchWrapper" style="width: 150px;">
                                <input type="text"
                                       class="form-control searchable-select-input"
                                       id="bankSearchInput"
                                       placeholder="Select bank..."
                                       autocomplete="off">
                                <div class="searchable-select-list" id="bankDropdownList"></div>
                            </div>
                            <label style="width: 60px;">Coll. Boy :</label>
                            <input type="hidden" name="coll_boy_code" id="collBoyCode">
                            <select class="form-control no-select2 custom-select-hidden" name="coll_boy_id" id="collBoySelect" style="width: 150px;">
                                <option value="">Select</option>
                                @foreach($salesmen as $sm)
                                <option value="{{ $sm->id }}" data-code="{{ $sm->code }}">{{ $sm->name }}</option>
                                @endforeach
                            </select>
                            <div class="searchable-select-wrapper" id="collBoySearchWrapper" style="width: 150px;">
                                <input type="text"
                                       class="form-control searchable-select-input"
                                       id="collBoySearchInput"
                                       placeholder="Select..."
                                       autocomplete="off">
                                <div class="searchable-select-list" id="collBoyDropdownList"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-md-4">
                        <div class="d-flex justify-content-end gap-3 mb-2">
                            <div class="field-group">
                                <label>DAY :</label>
                                <input type="text" class="form-control" name="day_value" id="dayValue" style="width: 80px;">
                            </div>
                            <div class="field-group">
                                <label>TAG :</label>
                                <input type="text" class="form-control" name="tag" id="tag" style="width: 100px;">
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="copyPartyBtn" onclick="copyParty()">Copy Party (Tab)</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto; max-height: 310px;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; background: #9999cc; color: #000; z-index: 10;">
                            <tr>
                                <th style="width: 60px;">Code</th>
                                <th style="width: 200px;">Party Name</th>
                                <th style="width: 100px;">Cheque No</th>
                                <th style="width: 100px;">Date</th>
                                <th style="width: 80px;">Amount</th>
                                <th style="width: 80px;">Unadjusted</th>
                                <th style="width: 50px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-primary" id="addPartyBtn" onclick="openCustomerModal()">
                        <i class="bi bi-plus-circle me-1"></i> Add Party
                    </button>
                </div>
            </div>

            <!-- TDS Display -->
            <div class="tds-display">TDS Amt. : <span id="tdsAmtDisplay">0.00</span></div>

            <!-- Totals Row -->
            <div class="total-section mb-2">
                <div class="row">
                    <div class="col-md-6">
                        <span class="total-label">Total:</span>
                        <span class="ms-3">Cash: ( ) <strong id="totalCash">0.00</strong></span>
                    </div>
                    <div class="col-md-6 text-end">
                        <span>Cheque: ( ) <strong id="totalCheque">0.00</strong></span>
                    </div>
                </div>
            </div>

            <!-- Adjustment Tables -->
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="bg-white border rounded p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2" style="background: #ff9800; padding: 5px; color: white;">
                            <span>1) Amt. Outstanding</span>
                            <span>Total: <strong id="outstandingTotal">0.00</strong></span>
                        </div>
                        <div class="table-responsive" id="outstandingScrollContainer" style="max-height: 180px; overflow-y: auto;">
                            <table class="table table-bordered table-sm mb-0" style="font-size: 10px;">
                                <thead style="position: sticky; top: 0; background: #ffe0b2; z-index: 5;">
                                    <tr>
                                        <th style="padding: 4px;">Inv. No</th>
                                        <th style="padding: 4px;">Date</th>
                                        <th style="padding: 4px;">Amount</th>
                                        <th style="padding: 4px;">Balance</th>
                                    </tr>
                                </thead>
                                <tbody id="outstandingTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white border rounded p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2" style="background: #00bcd4; padding: 5px; color: white;">
                            <span>2) Amt. Adjusted</span>
                            <span>Total: <strong id="adjustedTotal">0.00</strong></span>
                        </div>
                        <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                            <table class="table table-bordered table-sm mb-0" style="font-size: 10px;">
                                <thead style="position: sticky; top: 0; background: #b2ebf2; z-index: 5;">
                                    <tr>
                                        <th style="padding: 4px;">Inv. No</th>
                                        <th style="padding: 4px;">Date</th>
                                        <th style="padding: 4px;">Adjusted</th>
                                    </tr>
                                </thead>
                                <tbody id="adjustedTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-3">
                <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" name="currency_detail" id="currencyDetail">
                    <label class="form-check-label" for="currencyDetail">Currency Detail</label>
                </div>
                <button type="button" class="btn btn-success" id="saveReceiptBtn" onclick="handleSave()">Save (End)</button>
                <button type="button" class="btn btn-danger" onclick="deleteReceipt()">Delete</button>
                <a href="{{ route('admin.customer-receipt.index') }}" class="btn btn-secondary">Exit (Esc)</a>
            </div>
        </form>
    </div>
</div>

<!-- Adjustment Modal -->
<div class="adjustment-modal-backdrop" id="adjustmentModalBackdrop" onclick="closeAdjustmentModal()"></div>
<div class="adjustment-modal" id="adjustmentModal">
    <div class="adjustment-modal-content">
        <div class="adjustment-modal-header">
            <h5 class="adjustment-modal-title"><i class="bi bi-receipt-cutoff me-2"></i>Receipt Adjustment</h5>
            <button type="button" class="btn-close-adj" onclick="closeAdjustmentModal()">&times;</button>
        </div>
        <div class="adjustment-modal-body">
            <div style="max-height: 350px; overflow-y: auto;">
                <table class="table table-bordered" style="font-size: 11px; margin-bottom: 0;">
                    <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                        <tr>
                            <th style="width: 50px; text-align: center;">SR.NO.</th>
                            <th style="width: 120px; text-align: center;">TRANS NO.</th>
                            <th style="width: 100px; text-align: center;">DATE</th>
                            <th style="width: 100px; text-align: right;">BILL AMT.</th>
                            <th style="width: 100px; text-align: center;">ADJUSTED</th>
                            <th style="width: 100px; text-align: right;">BALANCE</th>
                        </tr>
                    </thead>
                    <tbody id="adjustmentTableBody"></tbody>
                </table>
            </div>
            <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: bold; color: #dc3545;">EXIT : &lt;ESC&gt;</span>
                    <span style="font-weight: bold; font-size: 14px; color: #0d6efd;">
                        AMOUNT TO ADJUST (Rs) : <span id="adjustmentAmountDisplay">₹ 0.00</span>
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="font-weight: bold; color: #495057;">Auto Adjust:</label>
                        <input type="number" id="autoAdjustAmount" class="form-control form-control-sm" style="width: 120px; border-radius: 0;" step="0.01" placeholder="Enter amount">
                        <button type="button" class="btn btn-info btn-sm" onclick="autoDistributeAmount()">
                            <i class="bi bi-magic me-1"></i>Auto Distribute
                        </button>
                    </div>
                    <span style="font-weight: bold; font-size: 14px;">
                        REMAINING : <span id="adjustmentRemainingDisplay" style="color: #28a745;">₹ 0.00</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="adjustment-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustmentModal()">Cancel</button>
            <button type="button" class="btn btn-success btn-sm" onclick="saveAdjustmentData()">
                <i class="bi bi-check-circle me-1"></i>Save Adjustment
            </button>
        </div>
    </div>
</div>

<!-- Bank Details Modal -->
<div class="bank-modal-backdrop" id="bankModalBackdrop"></div>
<div class="bank-modal" id="bankModal">
    <div class="bank-modal-content">
        <div class="bank-modal-header">
            <span class="bank-modal-title">Bank Details</span>
        </div>
        <div class="bank-modal-body">
            <div class="bank-field-group">
                <label>Bank :</label>
                <!-- hidden native select -->
                <select class="custom-select-hidden no-select2" id="chequeBankName" style="display:none;">
                    <option value="">Select Bank</option>
                    @foreach($banks as $bank)
                    <option value="{{ $bank->name }}">{{ $bank->name }}</option>
                    @endforeach
                </select>
                <!-- custom searchable wrapper -->
                <div class="searchable-select-wrapper" id="bankModalSearchWrapper" style="flex:1; position:relative;">
                    <input type="text" class="form-control searchable-select-input"
                           id="bankModalSearchInput"
                           placeholder="Search bank..."
                           autocomplete="off">
                    <div class="searchable-select-list" id="bankModalDropdownList" style="display:none;"></div>
                </div>
            </div>
            <div class="bank-field-group">
                <label>Area :</label>
                <input type="text" class="form-control" id="chequeBankArea" placeholder="Enter area">
            </div>
            <div class="bank-field-group">
                <label>Closed On :</label>
                <input type="date" class="form-control" id="chequeClosedOn" style="width: 150px;">
            </div>
        </div>
        <div class="bank-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" id="bankCancelBtn" onclick="closeBankModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" id="bankOkBtn" onclick="saveBankDetails()">OK</button>
        </div>
    </div>
</div>

<!-- Customer Selection Modal -->
<div class="modal-backdrop" id="customerModalBackdrop" onclick="closeCustomerModal()"></div>
<div class="customer-modal" id="customerModal">
    <div class="customer-modal-content">
        <div class="customer-modal-header">
            <h5 class="customer-modal-title"><i class="bi bi-people me-2"></i>Select Customer</h5>
            <button type="button" class="btn-close-modal" onclick="closeCustomerModal()">&times;</button>
        </div>
        <div class="customer-modal-body">
            <input type="text" class="form-control mb-3" id="customerSearch" placeholder="Search by code or name...">
            <div id="customerList" style="max-height: 300px; overflow-y: auto;">
            </div>
        </div>
        <div class="customer-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeCustomerModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="confirmCustomerSelection()">Select</button>
        </div>
    </div>
</div>

<script>
let customers = @json($customers);
let itemRowCount = 0;
let currentRowIndex = null;
let selectedCustomer = null;
let customerModalHighlightIndex = -1;

document.addEventListener('DOMContentLoaded', function() {
    // Build customer list for modal
    buildCustomerList();
    
    // Update day name on date change
    document.getElementById('receiptDate').addEventListener('change', function() {
        const date = new Date(this.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    });
    
    // Dropdown change handlers
    document.getElementById('salesmanSelect').addEventListener('change', function() {
        document.getElementById('salesmanCode').value = this.options[this.selectedIndex].dataset.code || '';
    });
    document.getElementById('areaSelect').addEventListener('change', function() {
        document.getElementById('areaCode').value = this.options[this.selectedIndex].dataset.code || '';
    });
    document.getElementById('routeSelect').addEventListener('change', function() {
        document.getElementById('routeCode').value = this.options[this.selectedIndex].dataset.code || '';
    });
    document.getElementById('collBoySelect').addEventListener('change', function() {
        document.getElementById('collBoyCode').value = this.options[this.selectedIndex].dataset.code || '';
    });

    // Replace native selects with searchable dropdown inputs (Select2-free)
    initializeSearchableHeaderDropdowns();

    // Header keyboard handlers and focus flow
    initializeHeaderKeyboardHandlers();
});

function buildCustomerList() {
    const container = document.getElementById('customerList');
    container.innerHTML = customers.map(c => `
        <div class="customer-list-item" data-id="${c.id}" data-code="${c.code || ''}" data-name="${c.name}" onclick="selectCustomerItem(this)">
            <strong>${c.code || '-'}</strong> - ${c.name}
        </div>
    `).join('');
}

function filterCustomers() {
    const search = document.getElementById('customerSearch').value.toLowerCase();
    document.querySelectorAll('#customerList .customer-list-item').forEach(item => {
        const code = item.dataset.code.toLowerCase();
        const name = item.dataset.name.toLowerCase();
        item.style.display = (code.includes(search) || name.includes(search)) ? '' : 'none';
    });
    // Reset highlight on filter change
    customerModalHighlightIndex = -1;
    document.querySelectorAll('#customerList .customer-list-item').forEach(item => item.classList.remove('customer-highlighted'));
}

function getVisibleCustomerItems() {
    return Array.from(document.querySelectorAll('#customerList .customer-list-item'))
        .filter(item => item.style.display !== 'none');
}

function highlightCustomerAt(index) {
    const items = getVisibleCustomerItems();
    if (!items.length) return;
    if (index < 0) index = items.length - 1;
    if (index >= items.length) index = 0;
    customerModalHighlightIndex = index;
    document.querySelectorAll('#customerList .customer-list-item').forEach(item => item.classList.remove('customer-highlighted'));
    items[customerModalHighlightIndex].classList.add('customer-highlighted');
    items[customerModalHighlightIndex].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
}

function selectCustomerItem(el) {
    document.querySelectorAll('#customerList .customer-list-item').forEach(item => {
        item.classList.remove('selected', 'customer-highlighted');
    });
    el.classList.add('selected');
    selectedCustomer = { id: el.dataset.id, code: el.dataset.code, name: el.dataset.name };
}

function openCustomerModal() {
    selectedCustomer = null;
    customerModalHighlightIndex = -1;
    document.getElementById('customerSearch').value = '';
    filterCustomers();
    document.querySelectorAll('#customerList .customer-list-item').forEach(item => item.classList.remove('selected', 'customer-highlighted'));
    document.getElementById('customerModalBackdrop').classList.add('show');
    document.getElementById('customerModal').classList.add('show');
    setTimeout(() => {
        const s = document.getElementById('customerSearch');
        if (s) s.focus();
    }, 50);
    // Attach keyboard handler on window capture — beats ALL layout global handlers
    window.addEventListener('keydown', handleCustomerModalKeydown, true);
    // Attach search input listener
    const searchEl = document.getElementById('customerSearch');
    if (searchEl) searchEl.addEventListener('input', filterCustomers);
}

function handleCustomerModalKeydown(e) {
    const modal = document.getElementById('customerModal');
    if (!modal || !modal.classList.contains('show')) return;

    const MANAGED = ['ArrowDown', 'ArrowUp', 'Enter', 'Escape'];
    if (!MANAGED.includes(e.key)) return;

    // Block ALL other handlers from seeing these keys while modal is open
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    if (e.key === 'ArrowDown') {
        highlightCustomerAt(customerModalHighlightIndex + 1);
        return;
    }
    if (e.key === 'ArrowUp') {
        highlightCustomerAt(customerModalHighlightIndex - 1);
        return;
    }
    if (e.key === 'Enter') {
        const items = getVisibleCustomerItems();
        if (customerModalHighlightIndex < 0) {
            // Highlight first item on first Enter
            if (items.length > 0) highlightCustomerAt(0);
            return;
        }
        // Second Enter (or Enter after arrow) → select
        const highlighted = items[customerModalHighlightIndex];
        if (highlighted) {
            selectCustomerItem(highlighted);
            confirmCustomerSelection();
        }
        return;
    }
    if (e.key === 'Escape') {
        closeCustomerModal();
        return;
    }
}

function closeCustomerModal() {
    document.getElementById('customerModalBackdrop').classList.remove('show');
    document.getElementById('customerModal').classList.remove('show');
    window.removeEventListener('keydown', handleCustomerModalKeydown, true);
    const searchEl = document.getElementById('customerSearch');
    if (searchEl) searchEl.removeEventListener('input', filterCustomers);
    customerModalHighlightIndex = -1;
}

function confirmCustomerSelection() {
    if (!selectedCustomer) {
        alert('Please select a customer');
        return;
    }
    
    // Add new row and get a reference to it
    const newRow = addItemRow(selectedCustomer);
    
    // Fetch outstanding invoices for this customer
    fetchCustomerOutstanding(selectedCustomer.id);
    
    closeCustomerModal();
    
    // Focus cheque-no field of the new row (slight delay to let modal close)
    if (newRow) {
        setTimeout(() => {
            const chequeInput = newRow.querySelector('.cheque-no');
            if (chequeInput) {
                chequeInput.focus();
                chequeInput.select();
                // Scroll new row into view so user can see it
                newRow.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        }, 100);
    }
}

// Outstanding pagination state
let outstandingPage = 1;
let outstandingHasMore = false;
let outstandingLoading = false;
let currentOutstandingCustomerId = null;

function fetchCustomerOutstanding(customerId, page = 1, append = false) {
    if (outstandingLoading) return;
    
    outstandingLoading = true;
    currentOutstandingCustomerId = customerId;
    
    if (!append) {
        outstandingPage = 1;
        document.getElementById('outstandingTableBody').innerHTML = '';
    }
    
    fetch(`{{ url('admin/customer-receipt/customer-outstanding') }}/${customerId}?page=${page}`)
        .then(r => r.json())
        .then(data => {
            outstandingLoading = false;
            if (data.success && data.outstanding) {
                displayOutstandingInvoices(data.outstanding, append);
                outstandingHasMore = data.has_more;
                outstandingPage = data.current_page;
                
                // Update total from server (full total, not just displayed)
                if (data.total_amount !== undefined) {
                    document.getElementById('outstandingTotal').textContent = parseFloat(data.total_amount).toFixed(2);
                }
            }
        })
        .catch(e => {
            outstandingLoading = false;
            console.error('Error fetching outstanding:', e);
        });
}

function displayOutstandingInvoices(invoices, append = false) {
    const tbody = document.getElementById('outstandingTableBody');
    
    if (!append) {
        tbody.innerHTML = '';
    }
    
    invoices.forEach(inv => {
        const balance = parseFloat(inv.balance_amount) || 0;
        
        const row = document.createElement('tr');
        row.style.height = '28px';
        row.innerHTML = `
            <td style="padding: 3px 5px;">${inv.invoice_no || '-'}</td>
            <td style="padding: 3px 5px;">${inv.invoice_date ? new Date(inv.invoice_date).toLocaleDateString('en-GB') : '-'}</td>
            <td class="text-end" style="padding: 3px 5px;">${parseFloat(inv.net_amount || 0).toFixed(2)}</td>
            <td class="text-end" style="padding: 3px 5px;">${balance.toFixed(2)}</td>
        `;
        tbody.appendChild(row);
    });
}

// Setup infinite scroll for outstanding table
document.addEventListener('DOMContentLoaded', function() {
    const outstandingContainer = document.getElementById('outstandingScrollContainer');
    
    if (outstandingContainer) {
        outstandingContainer.addEventListener('scroll', function() {
            if (outstandingLoading || !outstandingHasMore || !currentOutstandingCustomerId) return;
            
            const scrollTop = this.scrollTop;
            const scrollHeight = this.scrollHeight;
            const clientHeight = this.clientHeight;
            
            // Load more when scrolled to bottom (with 10px threshold)
            if (scrollTop + clientHeight >= scrollHeight - 10) {
                fetchCustomerOutstanding(currentOutstandingCustomerId, outstandingPage + 1, true);
            }
        });
    }
});

let selectedRowCustomerId = null;

function addItemRow(customer = null) {
    itemRowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', itemRowCount);
    row.setAttribute('data-customer-id', customer?.id || '');
    row.onclick = function(e) {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I') {
            selectRow(this);
        }
    };
    row.innerHTML = `
        <td><input type="text" class="form-control party-code readonly-field" value="${customer?.code || ''}" readonly></td>
        <td><input type="text" class="form-control party-name readonly-field" value="${customer?.name || ''}" readonly>
            <input type="hidden" class="customer-id" name="items[${itemRowCount}][customer_id]" value="${customer?.id || ''}">
        </td>
        <td><input type="text" class="form-control cheque-no" name="items[${itemRowCount}][cheque_no]" onchange="onChequeNoChange(this)">
            <input type="hidden" class="cheque-bank-name" name="items[${itemRowCount}][cheque_bank_name]">
            <input type="hidden" class="cheque-bank-area" name="items[${itemRowCount}][cheque_bank_area]">
            <input type="hidden" class="cheque-closed-on" name="items[${itemRowCount}][cheque_closed_on]">
        </td>
        <td><input type="date" class="form-control cheque-date" name="items[${itemRowCount}][cheque_date]"></td>
        <td><input type="number" class="form-control text-end amount" name="items[${itemRowCount}][amount]" step="0.01" value="" onchange="setUnadjustedAmount(this); calculateTotals(); updateRowStatus(this.closest('tr'))"></td>
        <td><input type="number" class="form-control text-end unadjusted readonly-field" name="items[${itemRowCount}][unadjusted]" step="0.01" value="" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Remove"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(row);
    
    // Auto-select the newly added row
    selectRow(row);

    // Add table-row keyboard navigation (Enter moves: cheque-no → cheque-date → amount)
    setupTableRowKeyNav(row);

    return row; // caller can focus it
}

function setupTableRowKeyNav(row) {
    const chequeNo = row.querySelector('.cheque-no');
    const chequeDate = row.querySelector('.cheque-date');
    const amountInput = row.querySelector('.amount');

    // cheque-no: Enter → go to cheque-date
    if (chequeNo) {
        chequeNo.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            if (isAnyKeyHandlingModalOpen()) return;
            e.preventDefault();
            e.stopPropagation();
            if (chequeDate) { chequeDate.focus(); }
        });
    }

    // cheque-date: Enter → go to amount
    if (chequeDate) {
        chequeDate.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            if (isAnyKeyHandlingModalOpen()) return;
            e.preventDefault();
            e.stopPropagation();
            if (amountInput) { amountInput.focus(); amountInput.select(); }
        });
    }

    // amount: Enter is now handled by the global window capture handler below
    // (see: AMOUNT FIELD GLOBAL ENTER HANDLER)
    // We only need to mark this input as an amount field — the class 'amount' is already set.
}

function selectRow(row) {
    // Remove selection from all rows
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
    
    // Add selection to clicked row
    row.classList.add('row-selected');
    
    // Get customer ID from the row
    const customerId = row.getAttribute('data-customer-id');
    
    // Fetch outstanding only if customer changed
    if (customerId && customerId !== selectedRowCustomerId) {
        selectedRowCustomerId = customerId;
        
        // Clear existing outstanding data
        document.getElementById('outstandingTableBody').innerHTML = '';
        document.getElementById('outstandingTotal').textContent = '0.00';
        
        // Fetch new outstanding data
        fetchCustomerOutstanding(customerId);
    }
}

function removeRow(btn) {
    const row = btn.closest('tr');
    row.remove();
    calculateTotals();
}

function updateRowStatus(row) {
    const code = row.querySelector('.party-code').value;
    const amount = parseFloat(row.querySelector('.amount').value) || 0;
    if (code && amount > 0) {
        row.classList.add('row-complete');
    } else {
        row.classList.remove('row-complete');
    }
}

// Set unadjusted amount = entered amount (before adjustment modal opens)
function setUnadjustedAmount(amountInput) {
    const row = amountInput.closest('tr');
    const unadjustedInput = row.querySelector('.unadjusted');
    const amount = parseFloat(amountInput.value) || 0;
    unadjustedInput.value = amount.toFixed(2);
}

function calculateTotals() {
    let totalCash = 0, totalCheque = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('.amount')?.value) || 0;
        const chequeNo = row.querySelector('.cheque-no')?.value;
        if (chequeNo && chequeNo.trim() !== '') {
            totalCheque += amount;
        } else {
            totalCash += amount;
        }
    });
    document.getElementById('totalCash').textContent = totalCash.toFixed(2);
    document.getElementById('totalCheque').textContent = totalCheque.toFixed(2);
}

function saveReceipt() {
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const code = row.querySelector('.party-code')?.value;
        const name = row.querySelector('.party-name')?.value;
        const customerId = row.querySelector('.customer-id')?.value;
        const chequeNo = row.querySelector('.cheque-no')?.value;
        const chequeDate = row.querySelector('.cheque-date')?.value;
        const amount = row.querySelector('.amount')?.value;
        const unadjusted = row.querySelector('.unadjusted')?.value;
        
        // Get bank details
        const chequeBankName = row.querySelector('.cheque-bank-name')?.value;
        const chequeBankArea = row.querySelector('.cheque-bank-area')?.value;
        const chequeClosedOn = row.querySelector('.cheque-closed-on')?.value;
        
        if (code || name || parseFloat(amount) > 0) {
            items.push({
                party_code: code, party_name: name, customer_id: customerId,
                cheque_no: chequeNo, cheque_date: chequeDate || null,
                cheque_bank_name: chequeBankName || null,
                cheque_bank_area: chequeBankArea || null,
                cheque_closed_on: chequeClosedOn || null,
                amount: amount || 0, unadjusted: unadjusted || amount || 0,
                payment_type: (chequeNo && chequeNo.trim() !== '') ? 'cheque' : 'cash'
            });
        }
    });
    
    if (items.length === 0) { alert('Please add at least one item'); return; }
    
    // Collect all adjustments
    const adjustments = [];
    Object.keys(rowAdjustments).forEach(rowIndex => {
        const rowAdj = rowAdjustments[rowIndex];
        Object.keys(rowAdj).forEach(invoiceId => {
            const adjusted = rowAdj[invoiceId];
            if (adjusted > 0) {
                const invoice = adjustmentInvoices.find(inv => inv.id == invoiceId);
                adjustments.push({
                    adjustment_type: 'outstanding',
                    reference_no: invoice?.invoice_no || '',
                    reference_date: invoice?.invoice_date || null,
                    reference_amount: parseFloat(invoice?.balance_amount || 0),
                    adjusted_amount: adjusted,
                    balance_amount: parseFloat(invoice?.balance_amount || 0) - adjusted,
                    sale_transaction_id: invoiceId
                });
            }
        });
    });
    
    const data = {
        receipt_date: document.getElementById('receiptDate').value,
        ledger: document.getElementById('ledger').value,
        salesman_id: document.getElementById('salesmanSelect').value || null,
        salesman_code: document.getElementById('salesmanCode').value,
        area_id: document.getElementById('areaSelect').value || null,
        area_code: document.getElementById('areaCode').value,
        route_id: document.getElementById('routeSelect').value || null,
        route_code: document.getElementById('routeCode').value,
        bank_code: document.getElementById('bankSelect').value,
        coll_boy_id: document.getElementById('collBoySelect').value || null,
        coll_boy_code: document.getElementById('collBoyCode').value,
        day_value: document.getElementById('dayValue').value,
        tag: document.getElementById('tag').value,
        currency_detail: document.getElementById('currencyDetail').checked,
        items: items,
        adjustments: adjustments
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('{{ route("admin.customer-receipt.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Receipt saved successfully! Trn No: ' + result.trn_no);
            window.location.href = '{{ route("admin.customer-receipt.index") }}';
        } else {
            alert(result.message || 'Failed to save receipt');
        }
    })
    .catch(e => { console.error(e); alert('An error occurred'); });
}

function deleteReceipt() { alert('No receipt loaded to delete'); }
function copyParty() { alert('Copy Party feature'); }

// Adjustment Modal Variables
let currentAdjustmentRow = null;
let currentAdjustmentAmount = 0;
let adjustmentInvoices = [];
let rowAdjustments = {}; // Store adjustments per row

// Open Adjustment Modal when amount is entered
function openAdjustmentModalForRow(row) {
    const amount = parseFloat(row.querySelector('.amount').value) || 0;
    const customerId = row.getAttribute('data-customer-id');
    
    if (amount <= 0 || !customerId) {
        // No valid data — if save was pending, save directly
        if (_pendingSaveAfterAdjustment) {
            _pendingSaveAfterAdjustment = false;
            _saveInProgress = false;
            saveReceipt();
        }
        return;
    }
    
    currentAdjustmentRow = row;
    currentAdjustmentAmount = amount;
    
    // Fetch customer's outstanding invoices
    fetch(`{{ url('admin/customer-receipt/customer-outstanding') }}/${customerId}?page=1&per_page=100`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.outstanding && data.outstanding.length > 0) {
                adjustmentInvoices = data.outstanding;
                showAdjustmentModal(data.outstanding, amount, row.getAttribute('data-row'));
            } else {
                // No outstanding invoices — if save was pending, save directly
                console.log('[openAdjustmentModalForRow] No outstanding invoices — saving directly');
                if (_pendingSaveAfterAdjustment) {
                    _pendingSaveAfterAdjustment = false;
                    _saveInProgress = false;
                    saveReceipt();
                }
            }
        })
        .catch(e => {
            console.error('Error fetching invoices:', e);
            // On error, if save was pending, save directly anyway
            if (_pendingSaveAfterAdjustment) {
                _pendingSaveAfterAdjustment = false;
                _saveInProgress = false;
                saveReceipt();
            }
        });
}

// Show Adjustment Modal
function showAdjustmentModal(invoices, receiptAmount, rowIndex) {
    const tbody = document.getElementById('adjustmentTableBody');
    const existingAdjustments = rowAdjustments[rowIndex] || {};
    
    tbody.innerHTML = invoices.map((inv, index) => {
        const billAmount = parseFloat(inv.net_amount || 0);
        const balance = parseFloat(inv.balance_amount || 0);
        const existingAdj = existingAdjustments[inv.id] || 0;
        const currentBalance = balance - existingAdj;
        
        return `
            <tr>
                <td style="text-align: center;">${index + 1}</td>
                <td style="text-align: center;">${inv.invoice_no || '-'}</td>
                <td style="text-align: center;">${inv.invoice_date ? new Date(inv.invoice_date).toLocaleDateString('en-GB') : '-'}</td>
                <td style="text-align: right; font-weight: bold; color: #0d6efd;">₹ ${balance.toFixed(2)}</td>
                <td style="text-align: center;">
                    <input type="number" class="form-control form-control-sm adj-input" 
                           id="adj_${inv.id}" 
                           data-invoice-id="${inv.id}"
                           data-balance="${balance}"
                           value="${existingAdj > 0 ? existingAdj.toFixed(2) : ''}" 
                           min="0" 
                           max="${balance}"
                           step="0.01"
                           onchange="updateAdjustmentBalances()"
                           oninput="updateAdjustmentBalances()"
                           style="width: 90px; height: 24px; padding: 2px 4px; font-size: 11px; text-align: right; border-radius: 0;">
                </td>
                <td style="text-align: right;" id="bal_${inv.id}"><span style="color: #28a745;">₹ ${currentBalance.toFixed(2)}</span></td>
            </tr>
        `;
    }).join('');
    
    // Set amount display
    document.getElementById('adjustmentAmountDisplay').textContent = `₹ ${receiptAmount.toFixed(2)}`;
    document.getElementById('autoAdjustAmount').value = receiptAmount.toFixed(2);
    
    // Calculate initial remaining
    updateAdjustmentBalances();
    
    // Show modal
    document.getElementById('adjustmentModalBackdrop').classList.add('show');
    document.getElementById('adjustmentModal').classList.add('show');
    
    // Add ESC key listener
    document.addEventListener('keydown', handleAdjustmentEsc);
}

// Handle ESC key
function handleAdjustmentEsc(e) {
    if (e.key === 'Escape') {
        closeAdjustmentModal();
    }
}

// Update adjustment balances
function updateAdjustmentBalances() {
    const inputs = document.querySelectorAll('.adj-input');
    let totalAdjusted = 0;
    
    inputs.forEach(input => {
        let adjusted = parseFloat(input.value || 0);
        const invoiceId = input.getAttribute('data-invoice-id');
        const balance = parseFloat(input.getAttribute('data-balance'));
        
        // Prevent adjusting more than balance
        if (adjusted > balance) {
            input.value = balance.toFixed(2);
            adjusted = balance;
        }
        
        totalAdjusted += adjusted;
        
        // Calculate new balance
        const newBalance = balance - adjusted;
        const balanceCell = document.getElementById(`bal_${invoiceId}`);
        if (balanceCell) {
            if (newBalance === 0) {
                balanceCell.innerHTML = `<span style="color: #28a745; font-weight: bold;">₹ ${newBalance.toFixed(2)}</span>`;
            } else {
                balanceCell.innerHTML = `<span style="color: #28a745;">₹ ${newBalance.toFixed(2)}</span>`;
            }
        }
    });
    
    // Update remaining
    const remaining = currentAdjustmentAmount - totalAdjusted;
    const remainingEl = document.getElementById('adjustmentRemainingDisplay');
    remainingEl.textContent = `₹ ${remaining.toFixed(2)}`;
    
    if (remaining < 0) {
        remainingEl.style.color = '#dc3545';
    } else if (remaining === 0) {
        remainingEl.style.color = '#28a745';
    } else {
        remainingEl.style.color = '#ffc107';
    }
}

// Auto distribute amount
function autoDistributeAmount() {
    const totalAmount = parseFloat(document.getElementById('autoAdjustAmount').value || 0);
    
    if (totalAmount <= 0) {
        alert('Please enter a valid amount to distribute');
        return;
    }
    
    // Clear all existing adjustments
    document.querySelectorAll('.adj-input').forEach(input => {
        input.value = '';
    });
    
    // Get all inputs sorted by balance
    const inputs = Array.from(document.querySelectorAll('.adj-input'));
    const transactions = inputs.map(input => ({
        input: input,
        balance: parseFloat(input.getAttribute('data-balance'))
    })).filter(t => t.balance > 0);
    
    let remainingAmount = totalAmount;
    
    // Distribute amount
    transactions.forEach(transaction => {
        if (remainingAmount <= 0) return;
        
        const adjustAmount = Math.min(remainingAmount, transaction.balance);
        transaction.input.value = adjustAmount.toFixed(2);
        remainingAmount -= adjustAmount;
    });
    
    // Update balances
    updateAdjustmentBalances();
}

// Save adjustment data
function saveAdjustmentData() {
    if (!currentAdjustmentRow) return;
    
    const rowIndex = currentAdjustmentRow.getAttribute('data-row');
    const inputs = document.querySelectorAll('.adj-input');
    let totalAdjusted = 0;
    const adjustments = {};
    
    inputs.forEach(input => {
        const adjusted = parseFloat(input.value || 0);
        if (adjusted > 0) {
            const invoiceId = input.getAttribute('data-invoice-id');
            adjustments[invoiceId] = adjusted;
            totalAdjusted += adjusted;
        }
    });
    
    // Store adjustments for this row
    rowAdjustments[rowIndex] = adjustments;
    
    // Update unadjusted amount in the row
    const unadjustedInput = currentAdjustmentRow.querySelector('.unadjusted');
    const amountInput = currentAdjustmentRow.querySelector('.amount');
    const amount = parseFloat(amountInput.value) || 0;
    unadjustedInput.value = (amount - totalAdjusted).toFixed(2);
    
    // Update adjusted table display
    updateAdjustedTable();
    
    closeAdjustmentModal();

    // If save was pending (triggered via Save button or Ctrl+S), auto-save receipt now
    if (_pendingSaveAfterAdjustment) {
        _pendingSaveAfterAdjustment = false;
        _saveInProgress = false;
        setTimeout(() => saveReceipt(), 100);
    } else {
        _saveInProgress = false;
    }
}

// Update the Amt. Adjusted table
function updateAdjustedTable() {
    const tbody = document.getElementById('adjustedTableBody');
    tbody.innerHTML = '';
    let totalAdjusted = 0;
    
    // Collect all adjustments from all rows
    Object.keys(rowAdjustments).forEach(rowIndex => {
        const rowAdj = rowAdjustments[rowIndex];
        Object.keys(rowAdj).forEach(invoiceId => {
            const adjusted = rowAdj[invoiceId];
            if (adjusted > 0) {
                // Find invoice details
                const invoice = adjustmentInvoices.find(inv => inv.id == invoiceId);
                if (invoice) {
                    totalAdjusted += adjusted;
                    const row = document.createElement('tr');
                    row.style.height = '28px';
                    row.innerHTML = `
                        <td style="padding: 3px 5px;">${invoice.invoice_no || '-'}</td>
                        <td style="padding: 3px 5px;">${invoice.invoice_date ? new Date(invoice.invoice_date).toLocaleDateString('en-GB') : '-'}</td>
                        <td class="text-end" style="padding: 3px 5px;">${adjusted.toFixed(2)}</td>
                    `;
                    tbody.appendChild(row);
                }
            }
        });
    });
    
    document.getElementById('adjustedTotal').textContent = totalAdjusted.toFixed(2);
}

// Close adjustment modal
function closeAdjustmentModal() {
    document.getElementById('adjustmentModalBackdrop').classList.remove('show');
    document.getElementById('adjustmentModal').classList.remove('show');
    document.removeEventListener('keydown', handleAdjustmentEsc);
    // Reset save guard so user can try again
    _saveInProgress = false;
}

// Bank Details Modal Variables
let currentBankRow = null;

// Open Bank Details Modal when cheque number is entered (skip if 'cash')
function onChequeNoChange(input) {
    const chequeNo = input.value.trim();
    const chequeNoLower = chequeNo.toLowerCase();
    
    // If cash, capitalize and highlight
    if (chequeNoLower === 'cash') {
        input.value = 'CASH';
        input.classList.add('cash-highlighted');
        return; // Skip bank modal
    } else {
        input.classList.remove('cash-highlighted');
    }
    
    // Skip bank modal if empty
    if (chequeNo) {
        currentBankRow = input.closest('tr');
        
        // Pre-fill existing values if any
        const bankName = currentBankRow.querySelector('.cheque-bank-name')?.value || '';
        const bankArea = currentBankRow.querySelector('.cheque-bank-area')?.value || '';
        const closedOn = currentBankRow.querySelector('.cheque-closed-on')?.value || '';
        
        document.getElementById('chequeBankName').value = bankName;
        document.getElementById('chequeBankArea').value = bankArea;
        document.getElementById('chequeClosedOn').value = closedOn;
        // Pre-fill the visible search input with existing bank name
        const bankSearchInp = document.getElementById('bankModalSearchInput');
        if (bankSearchInp) bankSearchInp.value = bankName;
        
        openBankModal();
    }
}

// ── Bank Modal Custom Searchable Dropdown ─────────────────────────
let _bankModalDropdown = null;  // holds { highlightedIndex, visibleItems, hide, isOpen }

function initBankModalDropdown() {
    const selectEl  = document.getElementById('chequeBankName');
    const inputEl   = document.getElementById('bankModalSearchInput');
    const listEl    = document.getElementById('bankModalDropdownList');
    const wrapperEl = document.getElementById('bankModalSearchWrapper');
    if (!selectEl || !inputEl || !listEl) return;

    let highlightedIndex = -1;
    let visibleItems     = [];

    function syncInput() {
        const opt = selectEl.options[selectEl.selectedIndex];
        inputEl.value = (opt && opt.value) ? opt.textContent.trim() : '';
    }

    function buildList(filter) {
        const term = (filter || '').toLowerCase().trim();
        listEl.innerHTML = '';
        highlightedIndex = -1;
        visibleItems     = [];
        const frag = document.createDocumentFragment();
        let count  = 0;
        Array.from(selectEl.options).forEach(opt => {
            if (!opt.value) return;
            const text = opt.textContent.trim();
            if (term && !text.toLowerCase().includes(term)) return;
            const div         = document.createElement('div');
            div.className     = 'searchable-select-item';
            div.dataset.value = opt.value;
            div.textContent   = text;
            if (opt.value === selectEl.value) div.classList.add('selected');
            div.addEventListener('mousedown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                doSelect(this);
                // move to Area after mouse click
                setTimeout(() => {
                    const area = document.getElementById('chequeBankArea');
                    if (area) area.focus();
                }, 0);
            });
            frag.appendChild(div);
            count++;
        });
        if (count === 0) {
            const empty      = document.createElement('div');
            empty.className  = 'searchable-select-empty';
            empty.textContent = 'No results found';
            listEl.appendChild(empty);
        } else {
            listEl.appendChild(frag);
            visibleItems = Array.from(listEl.querySelectorAll('.searchable-select-item'));
        }
    }

    function openList(filter) {
        buildList(filter !== undefined ? filter : '');
        listEl.style.display = 'block';
        if (visibleItems.length > 0) {
            const selIdx = visibleItems.findIndex(it => it.dataset.value === selectEl.value);
            highlightAt(selIdx >= 0 ? selIdx : 0);
        }
    }

    function hideList() {
        listEl.style.display = 'none';
        highlightedIndex = -1;
        visibleItems.forEach(it => it.classList.remove('highlighted'));
    }

    function isOpen() { return listEl.style.display === 'block'; }

    function highlightAt(idx) {
        if (!visibleItems.length) return;
        visibleItems.forEach(it => it.classList.remove('highlighted'));
        if (idx < 0)                    idx = visibleItems.length - 1;
        if (idx >= visibleItems.length) idx = 0;
        highlightedIndex = idx;
        visibleItems[idx].classList.add('highlighted');
        visibleItems[idx].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    function doSelect(el) {
        if (!el || !el.dataset.value) return false;
        selectEl.value = el.dataset.value;
        inputEl.value  = el.textContent.trim();
        selectEl.dispatchEvent(new Event('change', { bubbles: true }));
        hideList();
        return true;
    }

    inputEl.addEventListener('focus', function() { openList(''); });
    inputEl.addEventListener('input', function() {
        buildList(inputEl.value);
        listEl.style.display = 'block';
        if (visibleItems.length === 1) highlightAt(0);
    });
    // Close on outside click
    document.addEventListener('mousedown', function(e) {
        if (wrapperEl && !wrapperEl.contains(e.target) && isOpen()) {
            syncInput(); hideList();
        }
    });

    // Expose to modal keyboard handler
    _bankModalDropdown = {
        isOpen, hideList, openList, syncInput,
        highlightUp:   () => highlightAt(highlightedIndex - 1),
        highlightDown: () => highlightAt(highlightedIndex + 1),
        selectHighlighted: () => {
            if (highlightedIndex >= 0 && visibleItems[highlightedIndex]) {
                return doSelect(visibleItems[highlightedIndex]);
            }
            return false;
        }
    };

    syncInput();
}

// Open Bank Modal
function openBankModal() {
    document.getElementById('bankModalBackdrop').classList.add('show');
    document.getElementById('bankModal').classList.add('show');
    // Reset dropdown state
    const inp = document.getElementById('bankModalSearchInput');
    if (inp) inp.value = '';
    setTimeout(() => {
        initBankModalDropdown();
        const inp2 = document.getElementById('bankModalSearchInput');
        if (inp2) inp2.focus();
    }, 30);
    window.addEventListener('keydown', handleBankModalKey, true);
}

// Bank modal keyboard — window capture, beats ALL handlers
function handleBankModalKey(e) {
    const modal = document.getElementById('bankModal');
    if (!modal || !modal.classList.contains('show')) return;

    const MANAGED = ['Enter', 'Escape', 'ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight', 'Tab'];
    if (!MANAGED.includes(e.key)) return;

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    const active = document.activeElement;

    if (e.key === 'Escape') {
        if (_bankModalDropdown && _bankModalDropdown.isOpen()) {
            _bankModalDropdown.syncInput();
            _bankModalDropdown.hideList();
        } else {
            closeBankModal();
        }
        return;
    }

    if (e.key === 'ArrowDown') {
        if (_bankModalDropdown) {
            if (!_bankModalDropdown.isOpen()) _bankModalDropdown.openList('');
            else _bankModalDropdown.highlightDown();
        }
        return;
    }

    if (e.key === 'ArrowUp') {
        if (_bankModalDropdown && _bankModalDropdown.isOpen()) {
            _bankModalDropdown.highlightUp();
        }
        return;
    }

    // Left/Right arrow — toggle between Cancel & OK buttons
    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
        const cancelBtn = document.getElementById('bankCancelBtn');
        const okBtn     = document.getElementById('bankOkBtn');
        if (!cancelBtn || !okBtn) return;

        if (active && active.id === 'bankOkBtn') {
            cancelBtn.focus();
        } else {
            okBtn.focus();
        }
        return;
    }

    // Tab — cycle through fields → Cancel → OK
    if (e.key === 'Tab') {
        const fieldOrder = ['bankModalSearchInput', 'chequeBankArea', 'chequeClosedOn', 'bankCancelBtn', 'bankOkBtn'];
        const idx = fieldOrder.indexOf(active ? active.id : '');
        if (idx >= 0 && idx < fieldOrder.length - 1) {
            document.getElementById(fieldOrder[idx + 1]).focus();
        } else {
            document.getElementById(fieldOrder[0]).focus();
        }
        return;
    }

    if (e.key === 'Enter') {
        // If bank dropdown is open → select highlighted item
        if (_bankModalDropdown && _bankModalDropdown.isOpen()) {
            const selected = _bankModalDropdown.selectHighlighted();
            if (selected) {
                setTimeout(() => {
                    const area = document.getElementById('chequeBankArea');
                    if (area) area.focus();
                }, 0);
            }
            return;
        }

        // Enter on Cancel button → close modal
        if (active && active.id === 'bankCancelBtn') {
            closeBankModal();
            return;
        }

        // Enter on OK button → save
        if (active && active.id === 'bankOkBtn') {
            saveBankDetails();
            return;
        }

        // Normal field-to-field Enter navigation: Bank → Area → Date → focus OK button
        const fieldOrder = ['bankModalSearchInput', 'chequeBankArea', 'chequeClosedOn'];
        const idx = fieldOrder.indexOf(active ? active.id : '');
        if (idx >= 0 && idx < fieldOrder.length - 1) {
            document.getElementById(fieldOrder[idx + 1]).focus();
        } else if (idx === fieldOrder.length - 1) {
            // Last field (Closed On) → move to OK button
            const okBtn = document.getElementById('bankOkBtn');
            if (okBtn) okBtn.focus();
        } else {
            saveBankDetails();
        }
        return;
    }
}

// Close Bank Modal
function closeBankModal() {
    document.getElementById('bankModalBackdrop').classList.remove('show');
    document.getElementById('bankModal').classList.remove('show');
    window.removeEventListener('keydown', handleBankModalKey, true);
    _bankModalDropdown = null;
}

// Save Bank Details
function saveBankDetails() {
    if (!currentBankRow) return;
    
    const bankName = document.getElementById('chequeBankName').value;
    const bankArea = document.getElementById('chequeBankArea').value;
    const closedOn = document.getElementById('chequeClosedOn').value;
    
    // Save to hidden fields in the row
    currentBankRow.querySelector('.cheque-bank-name').value = bankName;
    currentBankRow.querySelector('.cheque-bank-area').value = bankArea;
    currentBankRow.querySelector('.cheque-closed-on').value = closedOn;
    
    // Also set the cheque date in the row from 'Closed On'
    if (closedOn) {
        currentBankRow.querySelector('.cheque-date').value = closedOn;
    }

    // Keep reference to row before closing modal clears it
    const savedRow = currentBankRow;
    
    closeBankModal();

    // After closing, focus the cheque-date field of the saved row
    setTimeout(() => {
        const chequeDateInput = savedRow.querySelector('.cheque-date');
        if (chequeDateInput) {
            chequeDateInput.focus();
        }
    }, 60);
}

// -----------------------------
// Custom dropdown + Keyboard nav
// -----------------------------
function closeAllHeaderDropdowns(exceptWrapperId = null) {
    document.querySelectorAll('.searchable-select-wrapper').forEach((wrapper) => {
        if (exceptWrapperId && wrapper.id === exceptWrapperId) return;
        if (typeof wrapper.__hideSearchableSelect === 'function') {
            wrapper.__hideSearchableSelect();
        } else {
            const list = wrapper.querySelector('.searchable-select-list');
            if (list) list.style.display = 'none';
        }
    });
}

function isAnyHeaderDropdownOpen() {
    return Array.from(document.querySelectorAll('.searchable-select-list')).some((el) => el.style.display === 'block');
}

function initializeSearchableHeaderDropdowns() {
    const dropdownConfigs = [
        {
            selectId: 'salesmanSelect',
            inputId: 'salesmanSearchInput',
            listId: 'salesmanDropdownList',
            wrapperId: 'salesmanSearchWrapper'
        },
        {
            selectId: 'areaSelect',
            inputId: 'areaSearchInput',
            listId: 'areaDropdownList',
            wrapperId: 'areaSearchWrapper'
        },
        {
            selectId: 'routeSelect',
            inputId: 'routeSearchInput',
            listId: 'routeDropdownList',
            wrapperId: 'routeSearchWrapper'
        },
        {
            selectId: 'bankSelect',
            inputId: 'bankSearchInput',
            listId: 'bankDropdownList',
            wrapperId: 'bankSearchWrapper'
        },
        {
            selectId: 'collBoySelect',
            inputId: 'collBoySearchInput',
            listId: 'collBoyDropdownList',
            wrapperId: 'collBoySearchWrapper'
        }
    ];

    dropdownConfigs.forEach(config => setupSearchableSelect(config));
}


/* ══════════════════════════════════════════════════════════════════
   AMOUNT FIELD GLOBAL ENTER HANDLER
   Registered on `window` in capture phase — fires FIRST before
   ANY other handler (including layout, dropdown, form handlers).
   When Enter is pressed inside an amount field:
     1. Blocks browser default (prevents focus jumping to action btn)
     2. Updates totals
     3. Moves focus to Add Party button
     4. Opens customer modal
═══════════════════════════════════════════════════════════════════ */
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;

    const active = document.activeElement;
    if (!active || !active.classList.contains('amount')) return;
    if (isAnyKeyHandlingModalOpen()) return;

    console.log('[AmountField] Enter pressed — blocking all other handlers');

    // KILL the event completely — nothing else will see it
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Update totals
    setUnadjustedAmount(active);
    calculateTotals();
    updateRowStatus(active.closest('tr'));

    console.log('[AmountField] Totals updated, focusing Add Party btn');

    // Focus Add Party button first (visible feedback), then open modal
    const addPartyBtn = document.getElementById('addPartyBtn');
    if (addPartyBtn) {
        addPartyBtn.focus();
    }
    setTimeout(function() {
        console.log('[AmountField] Opening customer modal');
        openCustomerModal();
    }, 60);

}, true /* capture phase = fires absolutely first */);

/* Block keypress on amount field too (some browsers use keypress for Enter default action) */
window.addEventListener('keypress', function(e) {
    if (e.key !== 'Enter' && e.keyCode !== 13) return;
    const active = document.activeElement;
    if (!active || !active.classList.contains('amount')) return;
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    console.log('[AmountField] keypress Enter blocked');
}, true);

/* Block keyup on amount field too (belt and suspenders) */
window.addEventListener('keyup', function(e) {
    if (e.key !== 'Enter') return;
    const active = document.activeElement;
    if (!active || !active.classList.contains('amount')) return;
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
}, true);

/* ══════════════════════════════════════════════════════════════════
   GLOBAL DROPDOWN KEYBOARD TRAP
   Registered on `window` in capture phase — fires BEFORE any
   `document` capture handler (including layout global handlers).
   This is the ONLY place where ArrowDown/Up/Enter/Esc are handled
   when a searchable dropdown is open.
═══════════════════════════════════════════════════════════════════ */
const _dropdownRegistry = new Map(); // inputId → { isOpen, handleKey }

window.addEventListener('keydown', function(e) {
    const active = document.activeElement;
    if (!active) return;
    const reg = _dropdownRegistry.get(active.id);
    if (!reg || !reg.isOpen()) return;
    if (isAnyKeyHandlingModalOpen()) return;

    const MANAGED = ['ArrowDown', 'ArrowUp', 'Enter', 'Escape', 'Tab'];
    if (!MANAGED.includes(e.key)) return;

    /* Stop EVERYTHING — no layout handler will see this event */
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    reg.handleKey(e.key);
}, true /* capture = fires before document handlers */);

/* ── TAG FIELD: Enter → open Add Party modal ────────────────────────
   Registered on window capture so it fires before any layout handler.
   Only acts when #tag is the active element.
─────────────────────────────────────────────────────────────────── */
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    if (document.activeElement && document.activeElement.id !== 'tag') return;
    if (isAnyKeyHandlingModalOpen()) return;

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    openCustomerModal();
}, true);

// Flag: after adjustment modal closes, auto-save the receipt
let _pendingSaveAfterAdjustment = false;
// Guard flag: prevent duplicate save calls while one is in progress
let _saveInProgress = false;

/**
 * handleSave() — called by Save button and Ctrl+S
 * Flow: Open adjustment modal for selected row first → user saves adjustment → receipt auto-saves
 * If no row with amount, saves directly.
 */
function handleSave() {
    console.log('[handleSave] ✅ Function called');
    
    // Guard: prevent duplicate calls
    if (_saveInProgress) {
        console.log('[handleSave] ⏳ Save already in progress — skipping');
        return;
    }
    
    try {
        _saveInProgress = true;
        
        // Find selected row (or first row with amount)
        let targetRow = document.querySelector('#itemsTableBody tr.row-selected');
        console.log('[handleSave] Selected row:', targetRow);
        
        // If no row selected, try the first row that has an amount
        if (!targetRow) {
            const allRows = document.querySelectorAll('#itemsTableBody tr');
            console.log('[handleSave] No selected row, checking', allRows.length, 'rows');
            for (const row of allRows) {
                const amt = parseFloat(row.querySelector('.amount')?.value) || 0;
                if (amt > 0) { targetRow = row; break; }
            }
        }
        
        if (targetRow) {
            const amount = parseFloat(targetRow.querySelector('.amount')?.value) || 0;
            const customerId = targetRow.getAttribute('data-customer-id');
            console.log('[handleSave] Target row found — amount:', amount, 'customerId:', customerId);
            if (amount > 0 && customerId) {
                // Open adjustment modal first; saving will happen after user saves adjustment
                _pendingSaveAfterAdjustment = true;
                selectRow(targetRow);
                openAdjustmentModalForRow(targetRow);
                return;
            }
        }
        // No valid row — save directly
        console.log('[handleSave] No adjustment needed — calling saveReceipt()');
        _saveInProgress = false;
        saveReceipt();
    } catch (err) {
        _saveInProgress = false;
        console.error('[handleSave] ❌ ERROR:', err);
        alert('handleSave error: ' + err.message);
    }
}

/* ── CTRL+S → Trigger Save flow ── */
window.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        console.log('[Ctrl+S] Triggered');
        
        // If adjustment modal is open, trigger save adjustment (which will then auto-save receipt)
        const adjModal = document.getElementById('adjustmentModal');
        if (adjModal && adjModal.classList.contains('show')) {
            console.log('[Ctrl+S] Adjustment modal open — saving adjustment + receipt');
            _pendingSaveAfterAdjustment = true;
            saveAdjustmentData();
            return;
        }
        
        // If any other modal is open, skip
        if (isAnyKeyHandlingModalOpen()) {
            console.log('[Ctrl+S] Modal open — skipping');
            return;
        }

        handleSave();
    }
}, true);



function setupSearchableSelect(config) {
    const selectEl  = document.getElementById(config.selectId);
    const inputEl   = document.getElementById(config.inputId);
    const listEl    = document.getElementById(config.listId);
    const wrapperEl = document.getElementById(config.wrapperId);
    if (!selectEl || !inputEl || !listEl || !wrapperEl) return;

    selectEl.classList.add('custom-select-hidden', 'no-select2');

    let highlightedIndex = -1;
    let visibleItems     = [];

    /* ── helpers ─────────────────────────────────────── */
    function syncInput() {
        const opt = selectEl.options[selectEl.selectedIndex];
        inputEl.value = (opt && opt.value) ? opt.textContent.trim() : '';
    }

    function buildList(filter) {
        const term = (filter || '').toLowerCase().trim();
        listEl.innerHTML = '';
        highlightedIndex = -1;
        visibleItems     = [];
        const frag = document.createDocumentFragment();
        let count  = 0;

        Array.from(selectEl.options).forEach(opt => {
            if (!opt.value) return;
            const text = opt.textContent.trim();
            const code = (opt.dataset.code || '').toLowerCase();
            if (term && !(text.toLowerCase().includes(term) || code.includes(term))) return;

            const div         = document.createElement('div');
            div.className     = 'searchable-select-item';
            div.dataset.value = opt.value;
            div.textContent   = text;
            if (opt.value === selectEl.value) div.classList.add('selected');

            /* mousedown = fires before blur, most reliable mouse selection */
            div.addEventListener('mousedown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                doSelect(this);
                setTimeout(() => focusNextHeaderField(inputEl.id, true), 0);
            });

            frag.appendChild(div);
            count++;
        });

        if (count === 0) {
            const empty      = document.createElement('div');
            empty.className  = 'searchable-select-empty';
            empty.textContent = 'No results found';
            listEl.appendChild(empty);
        } else {
            listEl.appendChild(frag);
            visibleItems = Array.from(listEl.querySelectorAll('.searchable-select-item'));
        }
    }

    function openList(filter) {
        closeAllHeaderDropdowns(wrapperEl.id);
        buildList(filter !== undefined ? filter : '');
        listEl.style.display = 'block';
        if (visibleItems.length > 0) {
            const selIdx = visibleItems.findIndex(it => it.dataset.value === selectEl.value);
            highlightAt(selIdx >= 0 ? selIdx : 0);
        }
    }

    function hideList() {
        listEl.style.display = 'none';
        highlightedIndex = -1;
        visibleItems.forEach(it => it.classList.remove('highlighted'));
    }

    function isOpen() { return listEl.style.display === 'block'; }

    function highlightAt(idx) {
        if (!visibleItems.length) return;
        visibleItems.forEach(it => it.classList.remove('highlighted'));
        if (idx < 0)                    idx = visibleItems.length - 1;
        if (idx >= visibleItems.length) idx = 0;
        highlightedIndex = idx;
        visibleItems[idx].classList.add('highlighted');
        visibleItems[idx].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    function doSelect(el) {
        if (!el || !el.dataset.value) return false;
        const value = el.dataset.value.trim();
        const text  = el.textContent.trim();
        if (!value) return false;
        /* set the hidden native select */
        selectEl.value = value;
        /* directly set visible input (don't wait for syncInput via change event) */
        inputEl.value  = text;
        /* fire change so other code (code/area sync) still works */
        selectEl.dispatchEvent(new Event('change', { bubbles: true }));
        hideList();
        closeAllHeaderDropdowns();
        return true;
    }

    /* ── KEY HANDLER — called by the window-level trap ── */
    function handleKey(key) {
        if (key === 'ArrowDown') {
            if (!isOpen()) openList('');
            else           highlightAt(highlightedIndex + 1);
            return;
        }
        if (key === 'ArrowUp') {
            if (!isOpen()) openList('');
            else           highlightAt(highlightedIndex - 1);
            return;
        }
        if (key === 'Enter') {
            if (!isOpen()) { openList(''); return; }
            /* directly use highlightedIndex — most reliable, no DOM query needed */
            if (highlightedIndex < 0 || !visibleItems[highlightedIndex]) {
                /* nothing highlighted → highlight first item */
                highlightAt(0);
                return;
            }
            const item = visibleItems[highlightedIndex];
            if (doSelect(item)) {
                /* move to next field after current event cycle */
                setTimeout(() => focusNextHeaderField(inputEl.id, true), 0);
            }
            return;
        }
        if (key === 'Escape') {
            syncInput();
            hideList();
            return;
        }
        if (key === 'Tab') {
            if (highlightedIndex >= 0 && visibleItems[highlightedIndex]) {
                doSelect(visibleItems[highlightedIndex]);
            } else {
                hideList();
            }
            setTimeout(() => focusNextHeaderField(inputEl.id, true), 0);
            return;
        }
    }

    /* expose hideList for closeAllHeaderDropdowns */
    wrapperEl.__hideSearchableSelect = hideList;

    /* register in global registry so window handler can find us */
    _dropdownRegistry.set(inputEl.id, { isOpen, handleKey });

    /* ── FOCUS: open dropdown ──────────────────────────── */
    inputEl.addEventListener('focus', function() {
        if (isAnyKeyHandlingModalOpen()) return;
        openList('');
    });

    /* ── INPUT: filter while typing ────────────────────── */
    inputEl.addEventListener('input', function() {
        if (isAnyKeyHandlingModalOpen()) return;
        buildList(inputEl.value);
        listEl.style.display = 'block';
        if (visibleItems.length === 1) highlightAt(0);
    });

    /* ── click outside → close ─────────────────────────── */
    document.addEventListener('click', function(e) {
        if (!wrapperEl.contains(e.target) && isOpen()) {
            syncInput();
            hideList();
        }
    });

    selectEl.addEventListener('change', syncInput);
    syncInput();
}


function isAnyKeyHandlingModalOpen() {
    const modalIds = ['customerModal', 'adjustmentModal', 'bankModal'];
    return modalIds.some((id) => {
        const el = document.getElementById(id);
        return el && el.classList.contains('show');
    });
}

function getHeaderFocusOrder() {
    return [
        'receiptDate',
        'ledger',
        'salesmanSearchInput',
        'areaSearchInput',
        'routeSearchInput',
        'bankSearchInput',
        'collBoySearchInput',
        'dayValue',
        'tag',
        'saveReceiptBtn'
    ];
}

function focusNextHeaderField(currentId, force = false) {
    const headerOrder = getHeaderFocusOrder();
    const currentIndex = headerOrder.indexOf(currentId);
    if (currentIndex < 0) return false;
    if (!force && isAnyHeaderDropdownOpen()) {
        return false;
    }

    for (let i = currentIndex + 1; i < headerOrder.length; i++) {
        const nextEl = document.getElementById(headerOrder[i]);
        if (!nextEl) continue;
        if (nextEl.disabled) continue;
        if (nextEl.offsetParent === null) continue;

        closeAllHeaderDropdowns();
        nextEl.focus();
        if (typeof nextEl.select === 'function' && nextEl.tagName === 'INPUT') {
            nextEl.select();
        }
        return true;
    }
    return false;
}

function initializeHeaderKeyboardHandlers() {
    const headerOrder = getHeaderFocusOrder();

    // Add blue focus border tracking
    document.addEventListener(
        'focusin',
        function(e) {
            const el = e.target;
            if (el && el.matches('input, select, textarea, button, a.btn')) {
                el.classList.add('kb-focus-ring');
            }
        },
        true
    );

    document.addEventListener(
        'focusout',
        function(e) {
            const el = e.target;
            if (el) {
                el.classList.remove('kb-focus-ring');
            }
        },
        true
    );

    headerOrder.forEach((fieldId) => {
        const el = document.getElementById(fieldId);
        if (!el) return;

        // Searchable dropdown inputs have their own keydown handler
        if (fieldId.endsWith('SearchInput')) return;

        el.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            if (isAnyKeyHandlingModalOpen()) return;
            if (isAnyHeaderDropdownOpen()) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return;
            }

            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            if (fieldId === 'copyPartyBtn') {
                copyParty();
                return;
            }

            if (fieldId === 'addPartyBtn') {
                openCustomerModal();
                return;
            }

            if (fieldId === 'saveReceiptBtn') {
                handleSave();
                return;
            }

            focusNextHeaderField(fieldId);
        }, true);   /* capture:true — fires before layout global handlers */
    });

    setTimeout(() => {
        const startEl = document.getElementById('receiptDate');
        if (startEl) {
            startEl.focus();
            if (typeof startEl.select === 'function') startEl.select();
        }
    }, 80);

    // addPartyBtn: Enter → open customer modal (separate from header flow)
    const addPartyBtnEl = document.getElementById('addPartyBtn');
    if (addPartyBtnEl) {
        addPartyBtnEl.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            if (isAnyKeyHandlingModalOpen()) return;
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            openCustomerModal();
        }, true);
    }
}
</script>
@endsection