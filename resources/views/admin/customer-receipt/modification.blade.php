@extends('layouts.admin')

@section('title', 'Receipt Modification')

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
    .adjustment-modal-backdrop.show { display: block; opacity: 1; }
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
    .adjustment-modal.show { display: block; transform: translate(-50%, -50%) scale(1); opacity: 1; }
    .adjustment-modal-content { background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); overflow: hidden; }
    .adjustment-modal-header { padding: 1rem 1.5rem; background: #0d6efd; color: white; display: flex; justify-content: space-between; align-items: center; }
    .adjustment-modal-title { margin: 0; font-size: 1.2rem; font-weight: 600; }
    .adjustment-modal-body { padding: 1rem; background: #fff; }
    .adjustment-modal-footer { padding: 1rem 1.5rem; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px; }
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
    .bank-modal-backdrop.show { display: block; opacity: 1; }
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
    .bank-modal.show { display: block; transform: translate(-50%, -50%) scale(1); opacity: 1; }
    .bank-modal-content { background: white; border-radius: 4px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); overflow: hidden; }
    .bank-modal-header { padding: 8px 15px; background: #e9ecef; border-bottom: 1px solid #dee2e6; }
    .bank-modal-title { margin: 0; font-size: 14px; font-weight: 600; }
    .bank-modal-body { padding: 15px; background: #f8f9fa; }
    .bank-modal-footer { padding: 10px 15px; background: #e9ecef; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px; }
    .bank-field-group { display: flex; align-items: center; margin-bottom: 10px; }
    .bank-field-group label { width: 80px; font-weight: 600; font-size: 12px; }
    .bank-field-group input, .bank-field-group select { flex: 1; font-size: 12px; padding: 4px 8px; height: 28px; border: 1px solid #ced4da; border-radius: 0; }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Receipt Modification</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customer-receipt.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-list"></i> All Receipts</a>
        <a href="{{ route('admin.customer-receipt.transaction') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> New Receipt</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <form id="receiptForm" method="POST" autocomplete="off">
            @csrf
            <input type="hidden" id="receiptId" value="">

            <!-- Search Section -->
            <div class="header-section mb-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="field-group">
                            <input type="text" class="form-control" id="searchTrnNo" placeholder="Enter Trn No" style="width: 100px; background: #fff8dc;">
                            <button type="button" class="btn btn-sm btn-primary" onclick="searchReceipt()"><i class="bi bi-search"></i> Load</button>
                            <button type="button" class="btn btn-sm btn-info" onclick="openLoadInvoiceModal()"><i class="bi bi-file-earmark-text"></i> Load Invoice</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Section -->
            <div class="header-section">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="field-group mb-2">
                            <label style="width: 60px;">Date :</label>
                            <input type="date" class="form-control" name="receipt_date" id="receiptDate" style="width: 130px;">
                            <input type="text" class="form-control readonly-field" id="dayName" readonly style="width: 80px;">
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 60px;">Trn No. :</label>
                            <input type="text" class="form-control readonly-field" name="trn_no" id="trnNo" readonly style="width: 80px;">
                        </div>
                        <div class="field-group">
                            <label style="width: 60px;">Ledger :</label>
                            <input type="text" class="form-control" name="ledger" id="ledger" style="width: 50px;">
                        </div>
                    </div>
                    
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
                        <tbody id="itemsTableBody"></tbody>
                    </table>
                </div>
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openCustomerModal()">
                        <i class="bi bi-plus-circle me-1"></i> Add Party
                    </button>
                </div>
            </div>

            <div class="tds-display">TDS Amt. : <span id="tdsAmtDisplay">0.00</span></div>

            <div class="total-section mb-2">
                <div class="row">
                    <div class="col-md-6"><span class="total-label">Total:</span> <span class="ms-3">Cash: ( ) <strong id="totalCash">0.00</strong></span></div>
                    <div class="col-md-6 text-end"><span>Cheque: ( ) <strong id="totalCheque">0.00</strong></span></div>
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

            <div class="d-flex justify-content-end gap-2 mt-3">
                <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" name="currency_detail" id="currencyDetail">
                    <label class="form-check-label" for="currencyDetail">Currency Detail</label>
                </div>
                <button type="button" class="btn btn-success" onclick="updateReceipt()" id="btnUpdate" disabled>Save (End)</button>
                <button type="button" class="btn btn-danger" onclick="deleteReceipt()" id="btnDelete" disabled>Delete</button>
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
            <div id="customerList" style="max-height: 300px; overflow-y: auto;"></div>
        </div>
        <div class="customer-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeCustomerModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="confirmCustomerSelection()">Select</button>
        </div>
    </div>
</div>

<!-- Load Invoice Modal -->
<div class="modal-backdrop" id="loadInvoiceModalBackdrop" onclick="closeLoadInvoiceModal()"></div>
<div class="customer-modal" id="loadInvoiceModal">
    <div class="customer-modal-content">
        <div class="customer-modal-header" style="background: #17a2b8;">
            <h5 class="customer-modal-title"><i class="bi bi-file-earmark-text me-2"></i>Load Invoice</h5>
            <button type="button" class="btn-close-modal" onclick="closeLoadInvoiceModal()">&times;</button>
        </div>
        <div class="customer-modal-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="invoiceSearch" placeholder="Search by Trn No or Date..." onkeyup="filterInvoices()">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="invoiceFromDate" placeholder="From Date">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="invoiceToDate" placeholder="To Date">
                </div>
            </div>
            <div style="max-height: 350px; overflow-y: auto;">
                <table class="table table-bordered table-sm" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 5;">
                        <tr>
                            <th style="width: 30px; text-align: center;"><input type="checkbox" id="selectAllInvoices" onchange="toggleSelectAllInvoices()"></th>
                            <th style="width: 80px;">Trn No</th>
                            <th style="width: 100px;">Date</th>
                            <th style="width: 100px;">Cash</th>
                            <th style="width: 100px;">Cheque</th>
                            <th>Salesman</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceListBody"></tbody>
                </table>
            </div>
        </div>
        <div class="customer-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeLoadInvoiceModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="loadSelectedInvoice()">Load Selected</button>
        </div>
    </div>
</div>

<script>
let customers = @json($customers);
let itemRowCount = 0;
let currentRowIndex = null;
let selectedCustomer = null;
let currentReceipt = null;
let selectedRowCustomerId = null;

// Outstanding pagination state
let outstandingPage = 1;
let outstandingHasMore = false;
let outstandingLoading = false;
let currentOutstandingCustomerId = null;

// Adjustment Modal Variables
let currentAdjustmentRow = null;
let currentAdjustmentAmount = 0;
let adjustmentInvoices = [];
let rowAdjustments = {};

// Bank Details Modal Variables
let currentBankRow = null;

document.addEventListener('DOMContentLoaded', function() {
    buildCustomerList();
    
    // Initialize Select2 for dropdowns
    $('#salesmanSelect').select2({ placeholder: 'Select Salesman', allowClear: true, width: '230px' });
    $('#areaSelect').select2({ placeholder: 'Select Area', allowClear: true, width: '230px' });
    $('#routeSelect').select2({ placeholder: 'Select Route', allowClear: true, width: '230px' });
    $('#bankSelect').select2({ placeholder: 'Select Bank', allowClear: true, width: '150px' });
    $('#collBoySelect').select2({ placeholder: 'Select', allowClear: true, width: '150px' });
    
    document.getElementById('receiptDate').addEventListener('change', function() {
        const date = new Date(this.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    });
    
    // Use jQuery for Select2 change events
    $('#salesmanSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        document.getElementById('salesmanCode').value = selectedOption.data('code') || '';
    });
    $('#areaSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        document.getElementById('areaCode').value = selectedOption.data('code') || '';
    });
    $('#routeSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        document.getElementById('routeCode').value = selectedOption.data('code') || '';
    });
    $('#collBoySelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        document.getElementById('collBoyCode').value = selectedOption.data('code') || '';
    });
    
    // Setup infinite scroll for outstanding table
    const outstandingContainer = document.getElementById('outstandingScrollContainer');
    if (outstandingContainer) {
        outstandingContainer.addEventListener('scroll', function() {
            if (outstandingLoading || !outstandingHasMore || !currentOutstandingCustomerId) return;
            const scrollTop = this.scrollTop;
            const scrollHeight = this.scrollHeight;
            const clientHeight = this.clientHeight;
            if (scrollTop + clientHeight >= scrollHeight - 10) {
                fetchCustomerOutstanding(currentOutstandingCustomerId, outstandingPage + 1, true);
            }
        });
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    const trnNo = urlParams.get('trn_no');
    if (trnNo) {
        document.getElementById('searchTrnNo').value = trnNo;
        searchReceipt();
    }
});

function buildCustomerList() {
    document.getElementById('customerList').innerHTML = customers.map(c => `
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
    if (!selectedCustomer) { alert('Please select a customer'); return; }
    addItemRow(selectedCustomer);
    fetchCustomerOutstanding(selectedCustomer.id);
    closeCustomerModal();
}

function searchReceipt() {
    const trnNo = document.getElementById('searchTrnNo').value;
    if (!trnNo) { alert('Please enter a transaction number'); return; }
    
    fetch(`{{ url('admin/customer-receipt/get-by-trn') }}/${trnNo}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) { loadReceipt(data.receipt); }
            else { alert(data.message || 'Receipt not found'); }
        })
        .catch(e => { console.error(e); alert('Error loading receipt'); });
}

function loadReceipt(receipt) {
    currentReceipt = receipt;
    rowAdjustments = {};
    
    document.getElementById('receiptId').value = receipt.id;
    document.getElementById('trnNo').value = receipt.trn_no;
    document.getElementById('receiptDate').value = receipt.receipt_date.split('T')[0];
    document.getElementById('dayName').value = receipt.day_name || '';
    document.getElementById('ledger').value = receipt.ledger || 'CL';
    document.getElementById('salesmanCode').value = receipt.salesman_code || '';
    document.getElementById('areaCode').value = receipt.area_code || '';
    document.getElementById('routeCode').value = receipt.route_code || '';
    document.getElementById('collBoyCode').value = receipt.coll_boy_code || '';
    document.getElementById('dayValue').value = receipt.day_value || '';
    document.getElementById('tag').value = receipt.tag || '';
    document.getElementById('tdsAmtDisplay').textContent = parseFloat(receipt.tds_amount || 0).toFixed(2);
    document.getElementById('totalCash').textContent = parseFloat(receipt.total_cash || 0).toFixed(2);
    document.getElementById('totalCheque').textContent = parseFloat(receipt.total_cheque || 0).toFixed(2);
    
    // Set Select2 values properly
    setSelect2ValueByCode('salesmanSelect', receipt.salesman_code);
    setSelect2ValueByCode('areaSelect', receipt.area_code);
    setSelect2ValueByCode('routeSelect', receipt.route_code);
    setSelect2ValueByCode('collBoySelect', receipt.coll_boy_code);
    
    // Set bank select
    if (receipt.bank_code) {
        $('#bankSelect').val(receipt.bank_code).trigger('change');
    }
    
    document.getElementById('itemsTableBody').innerHTML = '';
    document.getElementById('outstandingTableBody').innerHTML = '';
    document.getElementById('adjustedTableBody').innerHTML = '';
    document.getElementById('outstandingTotal').textContent = '0.00';
    document.getElementById('adjustedTotal').textContent = '0.00';
    itemRowCount = 0;
    
    if (receipt.items && receipt.items.length > 0) {
        receipt.items.forEach(item => addItemRowFromData(item));
    }
    
    document.getElementById('btnUpdate').disabled = false;
    document.getElementById('btnDelete').disabled = false;
}

// Helper function to set Select2 value by data-code attribute
function setSelect2ValueByCode(selectId, code) {
    if (!code) return;
    const $select = $('#' + selectId);
    const $option = $select.find('option').filter(function() {
        return $(this).data('code') === code;
    });
    if ($option.length) {
        $select.val($option.val()).trigger('change');
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
    const chequeClosedOn = item.cheque_closed_on ? item.cheque_closed_on.split('T')[0] : '';
    
    row.innerHTML = `
        <td><input type="text" class="form-control party-code readonly-field" value="${item.party_code || ''}" readonly></td>
        <td><input type="text" class="form-control party-name readonly-field" value="${item.party_name || ''}" readonly>
            <input type="hidden" class="customer-id" name="items[${itemRowCount}][customer_id]" value="${item.customer_id || ''}">
        </td>
        <td><input type="text" class="form-control cheque-no" name="items[${itemRowCount}][cheque_no]" value="${item.cheque_no || ''}" onchange="onChequeNoChange(this)">
            <input type="hidden" class="cheque-bank-name" name="items[${itemRowCount}][cheque_bank_name]" value="${item.cheque_bank_name || ''}">
            <input type="hidden" class="cheque-bank-area" name="items[${itemRowCount}][cheque_bank_area]" value="${item.cheque_bank_area || ''}">
            <input type="hidden" class="cheque-closed-on" name="items[${itemRowCount}][cheque_closed_on]" value="${chequeClosedOn}">
        </td>
        <td><input type="date" class="form-control cheque-date" name="items[${itemRowCount}][cheque_date]" value="${chequeDate}"></td>
        <td><input type="number" class="form-control text-end amount" name="items[${itemRowCount}][amount]" step="0.01" value="${item.amount || ''}" onchange="calculateTotals(); updateRowStatus(this.closest('tr')); openAdjustmentModalForRow(this.closest('tr'))"></td>
        <td><input type="number" class="form-control text-end unadjusted" name="items[${itemRowCount}][unadjusted]" step="0.01" value="${item.unadjusted || ''}"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Remove"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(row);
    if (item.party_code && parseFloat(item.amount) > 0) row.classList.add('row-complete');
}

function addItemRow(customer = null) {
    itemRowCount++;
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', itemRowCount);
    row.setAttribute('data-customer-id', customer?.id || '');
    row.onclick = function(e) {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I' && e.target.tagName !== 'INPUT') {
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
    selectRow(row);
}

function selectRow(row) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
    row.classList.add('row-selected');
    const customerId = row.getAttribute('data-customer-id');
    if (customerId && customerId !== selectedRowCustomerId) {
        selectedRowCustomerId = customerId;
        document.getElementById('outstandingTableBody').innerHTML = '';
        document.getElementById('outstandingTotal').textContent = '0.00';
        fetchCustomerOutstanding(customerId);
    }
}

function removeRow(btn) { btn.closest('tr').remove(); calculateTotals(); }

function updateRowStatus(row) {
    const code = row.querySelector('.party-code').value;
    const amount = parseFloat(row.querySelector('.amount').value) || 0;
    row.classList.toggle('row-complete', code && amount > 0);
}

function calculateTotals() {
    let totalCash = 0, totalCheque = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('.amount')?.value) || 0;
        const chequeNo = row.querySelector('.cheque-no')?.value;
        if (chequeNo && chequeNo.trim() !== '') { totalCheque += amount; }
        else { totalCash += amount; }
    });
    document.getElementById('totalCash').textContent = totalCash.toFixed(2);
    document.getElementById('totalCheque').textContent = totalCheque.toFixed(2);
}

// Outstanding functions
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
                if (data.total_amount !== undefined) {
                    document.getElementById('outstandingTotal').textContent = parseFloat(data.total_amount).toFixed(2);
                }
            }
        })
        .catch(e => { outstandingLoading = false; console.error('Error fetching outstanding:', e); });
}

function displayOutstandingInvoices(invoices, append = false) {
    const tbody = document.getElementById('outstandingTableBody');
    if (!append) tbody.innerHTML = '';
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

// Adjustment Modal functions
function openAdjustmentModalForRow(row) {
    const amount = parseFloat(row.querySelector('.amount').value) || 0;
    const customerId = row.getAttribute('data-customer-id');
    if (amount <= 0 || !customerId) return;
    currentAdjustmentRow = row;
    currentAdjustmentAmount = amount;
    fetch(`{{ url('admin/customer-receipt/customer-outstanding') }}/${customerId}?page=1&per_page=100`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.outstanding && data.outstanding.length > 0) {
                adjustmentInvoices = data.outstanding;
                showAdjustmentModal(data.outstanding, amount, row.getAttribute('data-row'));
            }
        })
        .catch(e => console.error('Error fetching invoices:', e));
}

function showAdjustmentModal(invoices, receiptAmount, rowIndex) {
    const tbody = document.getElementById('adjustmentTableBody');
    const existingAdjustments = rowAdjustments[rowIndex] || {};
    tbody.innerHTML = invoices.map((inv, index) => {
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
                    <input type="number" class="form-control form-control-sm adj-input" id="adj_${inv.id}" data-invoice-id="${inv.id}" data-balance="${balance}" value="${existingAdj > 0 ? existingAdj.toFixed(2) : ''}" min="0" max="${balance}" step="0.01" onchange="updateAdjustmentBalances()" oninput="updateAdjustmentBalances()" style="width: 90px; height: 24px; padding: 2px 4px; font-size: 11px; text-align: right; border-radius: 0;">
                </td>
                <td style="text-align: right;" id="bal_${inv.id}"><span style="color: #28a745;">₹ ${currentBalance.toFixed(2)}</span></td>
            </tr>
        `;
    }).join('');
    document.getElementById('adjustmentAmountDisplay').textContent = `₹ ${receiptAmount.toFixed(2)}`;
    document.getElementById('autoAdjustAmount').value = receiptAmount.toFixed(2);
    updateAdjustmentBalances();
    document.getElementById('adjustmentModalBackdrop').classList.add('show');
    document.getElementById('adjustmentModal').classList.add('show');
    document.addEventListener('keydown', handleAdjustmentEsc);
}

function handleAdjustmentEsc(e) { if (e.key === 'Escape') closeAdjustmentModal(); }

function updateAdjustmentBalances() {
    const inputs = document.querySelectorAll('.adj-input');
    let totalAdjusted = 0;
    inputs.forEach(input => {
        let adjusted = parseFloat(input.value || 0);
        const invoiceId = input.getAttribute('data-invoice-id');
        const balance = parseFloat(input.getAttribute('data-balance'));
        if (adjusted > balance) { input.value = balance.toFixed(2); adjusted = balance; }
        totalAdjusted += adjusted;
        const newBalance = balance - adjusted;
        const balanceCell = document.getElementById(`bal_${invoiceId}`);
        if (balanceCell) {
            balanceCell.innerHTML = newBalance === 0 
                ? `<span style="color: #28a745; font-weight: bold;">₹ ${newBalance.toFixed(2)}</span>`
                : `<span style="color: #28a745;">₹ ${newBalance.toFixed(2)}</span>`;
        }
    });
    const remaining = currentAdjustmentAmount - totalAdjusted;
    const remainingEl = document.getElementById('adjustmentRemainingDisplay');
    remainingEl.textContent = `₹ ${remaining.toFixed(2)}`;
    remainingEl.style.color = remaining < 0 ? '#dc3545' : (remaining === 0 ? '#28a745' : '#ffc107');
}

function autoDistributeAmount() {
    const totalAmount = parseFloat(document.getElementById('autoAdjustAmount').value || 0);
    if (totalAmount <= 0) { alert('Please enter a valid amount to distribute'); return; }
    document.querySelectorAll('.adj-input').forEach(input => { input.value = ''; });
    const inputs = Array.from(document.querySelectorAll('.adj-input'));
    const transactions = inputs.map(input => ({ input: input, balance: parseFloat(input.getAttribute('data-balance')) })).filter(t => t.balance > 0);
    let remainingAmount = totalAmount;
    transactions.forEach(transaction => {
        if (remainingAmount <= 0) return;
        const adjustAmount = Math.min(remainingAmount, transaction.balance);
        transaction.input.value = adjustAmount.toFixed(2);
        remainingAmount -= adjustAmount;
    });
    updateAdjustmentBalances();
}

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
    rowAdjustments[rowIndex] = adjustments;
    const unadjustedInput = currentAdjustmentRow.querySelector('.unadjusted');
    const amountInput = currentAdjustmentRow.querySelector('.amount');
    const amount = parseFloat(amountInput.value) || 0;
    unadjustedInput.value = (amount - totalAdjusted).toFixed(2);
    updateAdjustedTable();
    closeAdjustmentModal();
}

function updateAdjustedTable() {
    const tbody = document.getElementById('adjustedTableBody');
    tbody.innerHTML = '';
    let totalAdjusted = 0;
    Object.keys(rowAdjustments).forEach(rowIndex => {
        const rowAdj = rowAdjustments[rowIndex];
        Object.keys(rowAdj).forEach(invoiceId => {
            const adjusted = rowAdj[invoiceId];
            if (adjusted > 0) {
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

function closeAdjustmentModal() {
    document.getElementById('adjustmentModalBackdrop').classList.remove('show');
    document.getElementById('adjustmentModal').classList.remove('show');
    document.removeEventListener('keydown', handleAdjustmentEsc);
}

// Bank Details Modal functions
function onChequeNoChange(input) {
    const chequeNo = input.value.trim();
    if (chequeNo) {
        currentBankRow = input.closest('tr');
        const bankName = currentBankRow.querySelector('.cheque-bank-name')?.value || '';
        const bankArea = currentBankRow.querySelector('.cheque-bank-area')?.value || '';
        const closedOn = currentBankRow.querySelector('.cheque-closed-on')?.value || '';
        document.getElementById('chequeBankName').value = bankName;
        document.getElementById('chequeBankArea').value = bankArea;
        document.getElementById('chequeClosedOn').value = closedOn;
        openBankModal();
    }
}

function openBankModal() {
    document.getElementById('bankModalBackdrop').classList.add('show');
    document.getElementById('bankModal').classList.add('show');
    document.getElementById('chequeBankName').focus();
    document.addEventListener('keydown', handleBankEsc);
}

function handleBankEsc(e) { if (e.key === 'Escape') closeBankModal(); }

function closeBankModal() {
    document.getElementById('bankModalBackdrop').classList.remove('show');
    document.getElementById('bankModal').classList.remove('show');
    document.removeEventListener('keydown', handleBankEsc);
}

function saveBankDetails() {
    if (!currentBankRow) return;
    const bankName = document.getElementById('chequeBankName').value;
    const bankArea = document.getElementById('chequeBankArea').value;
    const closedOn = document.getElementById('chequeClosedOn').value;
    currentBankRow.querySelector('.cheque-bank-name').value = bankName;
    currentBankRow.querySelector('.cheque-bank-area').value = bankArea;
    currentBankRow.querySelector('.cheque-closed-on').value = closedOn;
    closeBankModal();
}

// Update Receipt
function updateReceipt() {
    if (!currentReceipt) { alert('No receipt loaded'); return; }
    
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const code = row.querySelector('.party-code')?.value;
        const name = row.querySelector('.party-name')?.value;
        const customerId = row.querySelector('.customer-id')?.value;
        const chequeNo = row.querySelector('.cheque-no')?.value;
        const chequeDate = row.querySelector('.cheque-date')?.value;
        const amount = row.querySelector('.amount')?.value;
        const unadjusted = row.querySelector('.unadjusted')?.value;
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
        salesman_code: document.getElementById('salesmanCode').value,
        area_code: document.getElementById('areaCode').value,
        route_code: document.getElementById('routeCode').value,
        bank_code: document.getElementById('bankSelect').value,
        coll_boy_code: document.getElementById('collBoyCode').value,
        day_value: document.getElementById('dayValue').value,
        tag: document.getElementById('tag').value,
        currency_detail: document.getElementById('currencyDetail').checked,
        items: items,
        adjustments: adjustments
    };
    
    fetch(`{{ url('admin/customer-receipt') }}/${currentReceipt.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) { alert('Receipt updated successfully!'); }
        else { alert(result.message || 'Failed to update'); }
    })
    .catch(e => { console.error(e); alert('An error occurred'); });
}

function deleteReceipt() {
    if (!currentReceipt) { alert('No receipt loaded'); return; }
    if (!confirm(`Delete Receipt #${currentReceipt.trn_no}?`)) return;
    
    fetch(`{{ url('admin/customer-receipt') }}/${currentReceipt.id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) { alert(result.message); window.location.href = '{{ route("admin.customer-receipt.index") }}'; }
        else { alert(result.message || 'Failed to delete'); }
    })
    .catch(e => { console.error(e); alert('An error occurred'); });
}

function openReceiptsList() { window.location.href = '{{ route("admin.customer-receipt.index") }}'; }

// Load Invoice Modal functions
let allReceipts = [];

function openLoadInvoiceModal() {
    document.getElementById('loadInvoiceModalBackdrop').classList.add('show');
    document.getElementById('loadInvoiceModal').classList.add('show');
    document.getElementById('invoiceSearch').value = '';
    document.getElementById('invoiceFromDate').value = '';
    document.getElementById('invoiceToDate').value = '';
    document.getElementById('selectAllInvoices').checked = false;
    
    // Fetch receipts from server
    fetchReceiptsList();
    
    document.addEventListener('keydown', handleLoadInvoiceEsc);
}

function handleLoadInvoiceEsc(e) { if (e.key === 'Escape') closeLoadInvoiceModal(); }

function closeLoadInvoiceModal() {
    document.getElementById('loadInvoiceModalBackdrop').classList.remove('show');
    document.getElementById('loadInvoiceModal').classList.remove('show');
    document.removeEventListener('keydown', handleLoadInvoiceEsc);
}

function fetchReceiptsList() {
    const fromDate = document.getElementById('invoiceFromDate').value;
    const toDate = document.getElementById('invoiceToDate').value;
    
    let url = '{{ url("admin/customer-receipt/get-receipts") }}';
    const params = new URLSearchParams();
    if (fromDate) params.append('from_date', fromDate);
    if (toDate) params.append('to_date', toDate);
    if (params.toString()) url += '?' + params.toString();
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.receipts) {
                allReceipts = data.receipts;
                displayReceiptsList(data.receipts);
            }
        })
        .catch(e => {
            console.error('Error fetching receipts:', e);
            document.getElementById('invoiceListBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading receipts</td></tr>';
        });
}

function displayReceiptsList(receipts) {
    const tbody = document.getElementById('invoiceListBody');
    if (!receipts || receipts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No receipts found</td></tr>';
        return;
    }
    
    tbody.innerHTML = receipts.map(receipt => {
        const receiptDate = receipt.receipt_date ? new Date(receipt.receipt_date).toLocaleDateString('en-GB') : '-';
        return `
            <tr data-receipt-id="${receipt.id}" data-trn-no="${receipt.trn_no}">
                <td style="text-align: center;"><input type="radio" name="selectedReceipt" value="${receipt.id}" class="receipt-radio"></td>
                <td>${receipt.trn_no || '-'}</td>
                <td>${receiptDate}</td>
                <td class="text-end">${parseFloat(receipt.total_cash || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(receipt.total_cheque || 0).toFixed(2)}</td>
                <td>${receipt.salesman_name || '-'}</td>
            </tr>
        `;
    }).join('');
}

function filterInvoices() {
    const searchText = document.getElementById('invoiceSearch').value.toLowerCase();
    const fromDate = document.getElementById('invoiceFromDate').value;
    const toDate = document.getElementById('invoiceToDate').value;
    
    let filtered = allReceipts;
    
    // Filter by search text
    if (searchText) {
        filtered = filtered.filter(receipt => {
            const trnNo = (receipt.trn_no || '').toString().toLowerCase();
            const salesmanName = (receipt.salesman_name || '').toLowerCase();
            return trnNo.includes(searchText) || salesmanName.includes(searchText);
        });
    }
    
    // Filter by date range
    if (fromDate) {
        filtered = filtered.filter(receipt => {
            const receiptDate = receipt.receipt_date ? receipt.receipt_date.split('T')[0] : '';
            return receiptDate >= fromDate;
        });
    }
    if (toDate) {
        filtered = filtered.filter(receipt => {
            const receiptDate = receipt.receipt_date ? receipt.receipt_date.split('T')[0] : '';
            return receiptDate <= toDate;
        });
    }
    
    displayReceiptsList(filtered);
}

function toggleSelectAllInvoices() {
    const selectAll = document.getElementById('selectAllInvoices').checked;
    document.querySelectorAll('.receipt-radio').forEach(radio => {
        radio.checked = selectAll;
    });
}

function loadSelectedInvoice() {
    const selectedRadio = document.querySelector('.receipt-radio:checked');
    if (!selectedRadio) {
        alert('Please select a receipt to load');
        return;
    }
    
    const receiptId = selectedRadio.value;
    const selectedReceipt = allReceipts.find(r => r.id == receiptId);
    
    if (selectedReceipt) {
        // Set the transaction number in search field and load it
        document.getElementById('searchTrnNo').value = selectedReceipt.trn_no;
        closeLoadInvoiceModal();
        searchReceipt();
    }
}

// Add event listeners for date filters
document.getElementById('invoiceFromDate')?.addEventListener('change', filterInvoices);
document.getElementById('invoiceToDate')?.addEventListener('change', filterInvoices);
</script>
@endsection
