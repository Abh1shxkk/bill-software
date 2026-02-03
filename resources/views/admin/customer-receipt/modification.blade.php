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
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Receipt Modification</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-info btn-sm" onclick="openLoadInvoicesModal()">
            <i class="bi bi-file-earmark-text me-1"></i> Load Invoices
        </button>
        <a href="{{ route('admin.customer-receipt.transaction') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Receipt
        </a>
        <a href="{{ route('admin.customer-receipt.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-list"></i> All Receipts
        </a>
    </div>
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
                            <select class="form-control" name="salesman_id" id="salesmanSelect" style="width: 230px;">
                                <option value="">Select Salesman</option>
                                @foreach($salesmen as $sm)
                                <option value="{{ $sm->id }}" data-code="{{ $sm->code }}">{{ $sm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Area</label>
                            <input type="hidden" name="area_code" id="areaCode">
                            <select class="form-control" name="area_id" id="areaSelect" style="width: 230px;">
                                <option value="">Select Area</option>
                                @foreach($areas as $area)
                                <option value="{{ $area->id }}" data-code="{{ $area->alter_code }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Route</label>
                            <input type="hidden" name="route_code" id="routeCode">
                            <select class="form-control" name="route_id" id="routeSelect" style="width: 230px;">
                                <option value="">Select Route</option>
                                @foreach($routes as $route)
                                <option value="{{ $route->id }}" data-code="{{ $route->alter_code }}">{{ $route->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label style="width: 70px;">Bank :</label>
                            <select class="form-control" name="bank_code" id="bankSelect" style="width: 150px;">
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                <option value="{{ $bank->alter_code }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                            <label style="width: 60px;">Coll. Boy :</label>
                            <input type="hidden" name="coll_boy_code" id="collBoyCode">
                            <select class="form-control" name="coll_boy_id" id="collBoySelect" style="width: 150px;">
                                <option value="">Select</option>
                                @foreach($salesmen as $sm)
                                <option value="{{ $sm->id }}" data-code="{{ $sm->code }}">{{ $sm->name }}</option>
                                @endforeach
                            </select>
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
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="copyParty()">Copy Party (Tab)</button>
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
                    <button type="button" class="btn btn-sm btn-primary" onclick="openCustomerModal()">
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
                <button type="button" class="btn btn-success" id="btnSave" onclick="saveReceipt()">Save (End)</button>
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
                <select class="form-control" id="chequeBankName">
                    <option value="">Select Bank</option>
                    @foreach($banks as $bank)
                    <option value="{{ $bank->name }}">{{ $bank->name }}</option>
                    @endforeach
                </select>
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
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeBankModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="saveBankDetails()">OK</button>
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
            <input type="text" class="form-control mb-3" id="customerSearch" placeholder="Search by code or name..." onkeyup="filterCustomers()">
            <div id="customerList" style="max-height: 300px; overflow-y: auto;">
            </div>
        </div>
        <div class="customer-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeCustomerModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="confirmCustomerSelection()">Select</button>
        </div>
    </div>
</div>

<!-- Load Invoices Modal -->
<div class="modal-backdrop" id="loadInvoicesModalBackdrop" onclick="closeLoadInvoicesModal()"></div>
<div class="customer-modal" id="loadInvoicesModal" style="max-width: 900px;">
    <div class="customer-modal-content">
        <div class="customer-modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h5 class="customer-modal-title"><i class="bi bi-file-earmark-text me-2"></i>Load Past Receipts</h5>
            <button type="button" class="btn-close-modal" onclick="closeLoadInvoicesModal()">&times;</button>
        </div>
        <div class="customer-modal-body" style="max-height: 500px;">
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 5;">
                        <tr>
                            <th style="width: 80px;">Trn No</th>
                            <th style="width: 100px;">Date</th>
                            <th>Salesman</th>
                            <th style="width: 100px;">Cash</th>
                            <th style="width: 100px;">Cheque</th>
                            <th style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesListBody">
                        <tr><td colspan="6" class="text-center text-muted py-3">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="customer-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeLoadInvoicesModal()">Close</button>
        </div>
    </div>
</div>

<script>
let customers = @json($customers);
let itemRowCount = 0;
let currentRowIndex = null;
let selectedCustomer = null;

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
}

function selectCustomerItem(el) {
    document.querySelectorAll('#customerList .customer-list-item').forEach(item => item.classList.remove('selected'));
    el.classList.add('selected');
    selectedCustomer = { id: el.dataset.id, code: el.dataset.code, name: el.dataset.name };
}

function openCustomerModal() {
    selectedCustomer = null;
    document.getElementById('customerSearch').value = '';
    filterCustomers();
    document.querySelectorAll('#customerList .customer-list-item').forEach(item => item.classList.remove('selected'));
    document.getElementById('customerModalBackdrop').classList.add('show');
    document.getElementById('customerModal').classList.add('show');
    document.getElementById('customerSearch').focus();
}

function closeCustomerModal() {
    document.getElementById('customerModalBackdrop').classList.remove('show');
    document.getElementById('customerModal').classList.remove('show');
}

function confirmCustomerSelection() {
    if (!selectedCustomer) {
        alert('Please select a customer');
        return;
    }
    
    // Add new row with selected customer
    addItemRow(selectedCustomer);
    
    // Fetch outstanding invoices for this customer
    fetchCustomerOutstanding(selectedCustomer.id);
    
    closeCustomerModal();
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
        <td><input type="number" class="form-control text-end amount" name="items[${itemRowCount}][amount]" step="0.01" value="" onchange="calculateTotals(); updateRowStatus(this.closest('tr')); openAdjustmentModalForRow(this.closest('tr'))"></td>
        <td><input type="number" class="form-control text-end unadjusted" name="items[${itemRowCount}][unadjusted]" step="0.01" value=""></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Remove"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(row);
    
    // Auto-select the newly added row
    selectRow(row);
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
    
    // Determine if this is an update or new save
    let url, method;
    if (currentReceiptId) {
        // Update existing receipt
        url = `{{ url('admin/customer-receipt') }}/${currentReceiptId}`;
        method = 'PUT';
    } else {
        // Create new receipt
        url = '{{ route("admin.customer-receipt.store") }}';
        method = 'POST';
    }
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            if (currentReceiptId) {
                alert('Receipt updated successfully!');
            } else {
                alert('Receipt saved successfully! Trn No: ' + result.trn_no);
            }
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
    
    if (amount <= 0 || !customerId) return;
    
    currentAdjustmentRow = row;
    currentAdjustmentAmount = amount;
    
    // Build URL with receipt_id if in modification mode
    let url = `{{ url('admin/customer-receipt/customer-outstanding') }}/${customerId}?page=1&per_page=100`;
    if (currentReceiptId) {
        url += `&receipt_id=${currentReceiptId}`;
    }
    
    // Fetch customer's outstanding invoices
    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.outstanding && data.outstanding.length > 0) {
                adjustmentInvoices = data.outstanding;
                showAdjustmentModal(data.outstanding, amount, row.getAttribute('data-row'));
            }
        })
        .catch(e => console.error('Error fetching invoices:', e));
}

// Show Adjustment Modal
function showAdjustmentModal(invoices, receiptAmount, rowIndex) {
    const tbody = document.getElementById('adjustmentTableBody');
    const existingRowAdjustments = rowAdjustments[rowIndex] || {};
    
    tbody.innerHTML = invoices.map((inv, index) => {
        // Available amount = balance + existing adjustment (what's actually available for this receipt)
        const availableAmount = parseFloat(inv.available_amount || inv.balance_amount || 0);
        const existingAdj = parseFloat(inv.existing_adjustment || 0);
        
        // Check if we have a local adjustment stored (user modified in current session)
        const localAdj = existingRowAdjustments[inv.id];
        const displayAdj = localAdj !== undefined ? localAdj : existingAdj;
        const currentBalance = availableAmount - displayAdj;
        
        return `
            <tr>
                <td style="text-align: center;">${index + 1}</td>
                <td style="text-align: center;">${inv.invoice_no || '-'}</td>
                <td style="text-align: center;">${inv.invoice_date ? new Date(inv.invoice_date).toLocaleDateString('en-GB') : '-'}</td>
                <td style="text-align: right; font-weight: bold; color: #0d6efd;">₹ ${availableAmount.toFixed(2)}</td>
                <td style="text-align: center;">
                    <input type="number" class="form-control form-control-sm adj-input" 
                           id="adj_${inv.id}" 
                           data-invoice-id="${inv.id}"
                           data-invoice-no="${inv.invoice_no || ''}"
                           data-available="${availableAmount}"
                           value="${displayAdj > 0 ? displayAdj.toFixed(2) : ''}" 
                           min="0" 
                           max="${availableAmount}"
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
        // Use data-available (available amount = balance + existing adjustment)
        const available = parseFloat(input.getAttribute('data-available') || input.getAttribute('data-balance') || 0);
        
        // Prevent adjusting more than available
        if (adjusted > available) {
            input.value = available.toFixed(2);
            adjusted = available;
        }
        
        totalAdjusted += adjusted;
        
        // Calculate new balance
        const newBalance = available - adjusted;
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
    if (remainingEl) {
        remainingEl.textContent = `₹ ${remaining.toFixed(2)}`;
        
        if (remaining < 0) {
            remainingEl.style.color = '#dc3545';
        } else if (remaining === 0) {
            remainingEl.style.color = '#28a745';
        } else {
            remainingEl.style.color = '#ffc107';
        }
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
    
    // Get all inputs sorted by available amount
    const inputs = Array.from(document.querySelectorAll('.adj-input'));
    const transactions = inputs.map(input => ({
        input: input,
        available: parseFloat(input.getAttribute('data-available') || input.getAttribute('data-balance') || 0)
    })).filter(t => t.available > 0);
    
    let remainingAmount = totalAmount;
    
    // Distribute amount
    transactions.forEach(transaction => {
        if (remainingAmount <= 0) return;
        
        const adjustAmount = Math.min(remainingAmount, transaction.available);
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
}

// Bank Details Modal Variables
let currentBankRow = null;

// Open Bank Details Modal when cheque number is entered
function onChequeNoChange(input) {
    const chequeNo = input.value.trim();
    if (chequeNo) {
        currentBankRow = input.closest('tr');
        
        // Pre-fill existing values if any
        const bankName = currentBankRow.querySelector('.cheque-bank-name')?.value || '';
        const bankArea = currentBankRow.querySelector('.cheque-bank-area')?.value || '';
        const closedOn = currentBankRow.querySelector('.cheque-closed-on')?.value || '';
        
        document.getElementById('chequeBankName').value = bankName;
        document.getElementById('chequeBankArea').value = bankArea;
        document.getElementById('chequeClosedOn').value = closedOn;
        
        openBankModal();
    }
}

// Open Bank Modal
function openBankModal() {
    document.getElementById('bankModalBackdrop').classList.add('show');
    document.getElementById('bankModal').classList.add('show');
    document.getElementById('chequeBankName').focus();
    
    // Add ESC key listener
    document.addEventListener('keydown', handleBankEsc);
}

// Handle ESC key for bank modal
function handleBankEsc(e) {
    if (e.key === 'Escape') {
        closeBankModal();
    }
}

// Close Bank Modal
function closeBankModal() {
    document.getElementById('bankModalBackdrop').classList.remove('show');
    document.getElementById('bankModal').classList.remove('show');
    document.removeEventListener('keydown', handleBankEsc);
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
    
    closeBankModal();
}

// ==================== LOAD INVOICES MODAL ====================
let currentReceiptId = null;

function openLoadInvoicesModal() {
    document.getElementById('loadInvoicesModalBackdrop').classList.add('show');
    document.getElementById('loadInvoicesModal').classList.add('show');
    // Auto-load all past invoices
    loadPastInvoices();
}

function closeLoadInvoicesModal() {
    document.getElementById('loadInvoicesModalBackdrop').classList.remove('show');
    document.getElementById('loadInvoicesModal').classList.remove('show');
}

function loadPastInvoices() {
    const tbody = document.getElementById('invoicesListBody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</td></tr>';
    
    fetch(`{{ url('admin/customer-receipt/get-receipts') }}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })
        .then(data => {
            if (data.success && data.receipts && data.receipts.length > 0) {
                tbody.innerHTML = data.receipts.map(r => `
                    <tr${r.has_returned_cheque ? ' style="background-color: #ffe6e6;"' : ''}>
                        <td><strong>${r.trn_no || '-'}</strong></td>
                        <td>${r.receipt_date || '-'}</td>
                        <td>${r.salesman_name || '-'}</td>
                        <td class="text-end">₹${parseFloat(r.total_cash || 0).toFixed(2)}</td>
                        <td class="text-end">₹${parseFloat(r.total_cheque || 0).toFixed(2)}</td>
                        <td class="text-center">
                            ${r.has_returned_cheque 
                                ? '<span class="badge bg-danger" title="Cheque Returned - Cannot Modify"><i class="bi bi-exclamation-triangle me-1"></i>Returned</span>'
                                : `<button type="button" class="btn btn-sm btn-primary" onclick="loadReceiptById(${r.id})"><i class="bi bi-download"></i> Load</button>`
                            }
                        </td>
                    </tr>
                `).join('');
            } else if (data.success && (!data.receipts || data.receipts.length === 0)) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No receipts found in database.</td></tr>';
            } else {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-3">${data.message || 'Error loading receipts'}</td></tr>`;
            }
        })
        .catch(e => {
            console.error('Error:', e);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-3">Error loading receipts: ' + e.message + '</td></tr>';
        });
}

function loadReceiptById(id) {
    fetch(`{{ url('admin/customer-receipt/details') }}/${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeLoadInvoicesModal();
                populateFormWithReceipt(data.receipt);
            } else if (data.is_returned) {
                // Show special warning for returned cheques
                closeLoadInvoicesModal();
                showReturnedChequeWarning(data.message);
            } else {
                alert(data.message || 'Error loading receipt');
            }
        })
        .catch(e => {
            console.error(e);
            alert('Error loading receipt');
        });
}

// Show warning for returned cheque
function showReturnedChequeWarning(message) {
    // Create modal HTML
    const modalHTML = `
        <div id="returnedWarningBackdrop" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 99998;"></div>
        <div id="returnedWarningModal" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 450px; background: white; border-radius: 8px; box-shadow: 0 5px 30px rgba(0,0,0,0.4); z-index: 99999; overflow: hidden;">
            <div style="background: #dc3545; color: white; padding: 15px 20px; display: flex; align-items: center; gap: 10px;">
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 24px;"></i>
                <h5 style="margin: 0; font-size: 16px;">Cannot Modify Receipt</h5>
            </div>
            <div style="padding: 20px;">
                <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                    <i class="bi bi-info-circle" style="color: #856404; margin-right: 8px;"></i>
                    <strong style="color: #856404;">Cheque Returned!</strong>
                </div>
                <p style="font-size: 14px; color: #333; margin: 0;">${message}</p>
            </div>
            <div style="background: #f8f9fa; padding: 12px 20px; display: flex; justify-content: flex-end;">
                <button onclick="closeReturnedWarning()" style="padding: 8px 25px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">OK</button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeReturnedWarning() {
    document.getElementById('returnedWarningBackdrop')?.remove();
    document.getElementById('returnedWarningModal')?.remove();
}

function populateFormWithReceipt(receipt) {
    currentReceiptId = receipt.id;
    
    // Populate header fields
    document.getElementById('trnNo').value = receipt.trn_no;
    document.getElementById('receiptDate').value = receipt.receipt_date ? receipt.receipt_date.split('T')[0] : '';
    document.getElementById('dayName').value = receipt.day_name || '';
    document.getElementById('ledger').value = receipt.ledger || 'CL';
    document.getElementById('salesmanCode').value = receipt.salesman_code || '';
    document.getElementById('areaCode').value = receipt.area_code || '';
    document.getElementById('routeCode').value = receipt.route_code || '';
    document.getElementById('collBoyCode').value = receipt.coll_boy_code || '';
    
    // Set dropdown values - Use IDs if available, otherwise match by code/name
    const salesmanSelect = document.getElementById('salesmanSelect');
    const areaSelect = document.getElementById('areaSelect');
    const routeSelect = document.getElementById('routeSelect');
    const collBoySelect = document.getElementById('collBoySelect');
    const bankSelect = document.getElementById('bankSelect');
    
    // Salesman
    if (receipt.salesman_id) {
        salesmanSelect.value = receipt.salesman_id;
    } else if (receipt.salesman_code) {
        // Find by code
        const option = Array.from(salesmanSelect.options).find(opt => opt.dataset.code === receipt.salesman_code);
        if (option) salesmanSelect.value = option.value;
    }
    salesmanSelect.dispatchEvent(new Event('change'));
    
    // Area
    if (receipt.area_id) {
        areaSelect.value = receipt.area_id;
    } else if (receipt.area_code) {
        const option = Array.from(areaSelect.options).find(opt => opt.dataset.code === receipt.area_code);
        if (option) areaSelect.value = option.value;
    }
    areaSelect.dispatchEvent(new Event('change'));
    
    // Route
    if (receipt.route_id) {
        routeSelect.value = receipt.route_id;
    } else if (receipt.route_code) {
        const option = Array.from(routeSelect.options).find(opt => opt.dataset.code === receipt.route_code);
        if (option) routeSelect.value = option.value;
    }
    routeSelect.dispatchEvent(new Event('change'));
    
    // Collection Boy
    if (receipt.coll_boy_id) {
        collBoySelect.value = receipt.coll_boy_id;
    } else if (receipt.coll_boy_code) {
        const option = Array.from(collBoySelect.options).find(opt => opt.dataset.code === receipt.coll_boy_code);
        if (option) collBoySelect.value = option.value;
    }
    collBoySelect.dispatchEvent(new Event('change'));
    
    // Bank (uses alter_code directly)
    if (receipt.bank_code) {
        bankSelect.value = receipt.bank_code;
    }
    bankSelect.dispatchEvent(new Event('change'));
    
    // Clear and populate items
    document.getElementById('itemsTableBody').innerHTML = '';
    itemRowCount = 0;
    
    // Clear outstanding table first
    const outstandingTbody = document.getElementById('outstandingTableBody');
    if (outstandingTbody) {
        outstandingTbody.innerHTML = '';
    }
    const outstandingTotalEl = document.getElementById('outstandingTotal');
    if (outstandingTotalEl) {
        outstandingTotalEl.textContent = '0.00';
    }
    
    if (receipt.items && receipt.items.length > 0) {
        receipt.items.forEach(item => {
            addItemRowFromData(item);
        });
        
        // Fetch outstanding invoices for the first customer
        const firstCustomerId = receipt.items[0]?.customer_id;
        if (firstCustomerId) {
            fetchCustomerOutstanding(firstCustomerId);
        }
    }
    
    // Clear and populate adjustments
    const adjustedTbody = document.getElementById('adjustedTableBody');
    if (adjustedTbody) {
        adjustedTbody.innerHTML = '';
    }
    let totalAdjusted = 0;
    
    if (receipt.adjustments && receipt.adjustments.length > 0 && adjustedTbody) {
        receipt.adjustments.forEach(adj => {
            const adjustedAmt = parseFloat(adj.adjusted_amount || 0);
            totalAdjusted += adjustedAmt;
            
            const row = document.createElement('tr');
            row.style.height = '28px';
            row.innerHTML = `
                <td style="padding: 3px 5px;">${adj.reference_no || '-'}</td>
                <td style="padding: 3px 5px;">${adj.reference_date ? new Date(adj.reference_date).toLocaleDateString('en-GB') : '-'}</td>
                <td class="text-end" style="padding: 3px 5px;">${adjustedAmt.toFixed(2)}</td>
            `;
            adjustedTbody.appendChild(row);
        });
    }
    
    const adjustedTotalEl = document.getElementById('adjustedTotal');
    if (adjustedTotalEl) {
        adjustedTotalEl.textContent = totalAdjusted.toFixed(2);
    }
    
    // Update totals
    calculateTotals();
    
    // Enable update button
    const btnSave = document.getElementById('btnSave');
    if (btnSave) {
        btnSave.textContent = 'Update';
    }
}

function addItemRowFromData(item) {
    itemRowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', itemRowCount);
    row.setAttribute('data-customer-id', item.customer_id || '');
    row.onclick = function(e) {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I' && e.target.tagName !== 'INPUT') {
            selectRow(this);
        }
    };
    
    const chequeDate = item.cheque_date ? item.cheque_date.split('T')[0] : '';
    
    row.innerHTML = `
        <td><input type="text" class="form-control party-code readonly-field" value="${item.party_code || ''}" readonly></td>
        <td><input type="text" class="form-control party-name readonly-field" value="${item.party_name || ''}" readonly>
            <input type="hidden" class="customer-id" name="items[${itemRowCount}][customer_id]" value="${item.customer_id || ''}">
        </td>
        <td><input type="text" class="form-control cheque-no" name="items[${itemRowCount}][cheque_no]" value="${item.cheque_no || ''}" onchange="onChequeNoChange(this)">
            <input type="hidden" class="cheque-bank-name" name="items[${itemRowCount}][cheque_bank_name]" value="${item.cheque_bank_name || ''}">
            <input type="hidden" class="cheque-bank-area" name="items[${itemRowCount}][cheque_bank_area]" value="${item.cheque_bank_area || ''}">
            <input type="hidden" class="cheque-closed-on" name="items[${itemRowCount}][cheque_closed_on]" value="${item.cheque_closed_on || ''}">
        </td>
        <td><input type="date" class="form-control cheque-date" name="items[${itemRowCount}][cheque_date]" value="${chequeDate}"></td>
        <td><input type="number" class="form-control text-end amount" name="items[${itemRowCount}][amount]" step="0.01" value="${item.amount || ''}" onchange="calculateTotals(); updateRowStatus(this.closest('tr')); openAdjustmentModalForRow(this.closest('tr'))"></td>
        <td><input type="number" class="form-control text-end unadjusted readonly-field" name="items[${itemRowCount}][unadjusted]" step="0.01" value="${item.unadjusted || ''}" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Remove"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(row);
    
    // Mark row as complete if has data
    if (item.party_code && parseFloat(item.amount) > 0) {
        row.classList.add('row-complete');
    }
}
</script>
@endsection
