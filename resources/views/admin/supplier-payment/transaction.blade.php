@extends('layouts.admin')

@section('title', 'Payment to Supplier')

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
    
    .row-selected td { border-top: 2px solid #007bff !important; border-bottom: 2px solid #007bff !important; }
    .row-selected td:first-child { border-left: 2px solid #007bff !important; }
    .row-selected td:last-child { border-right: 2px solid #007bff !important; }
    #itemsTableBody tr { cursor: pointer; }
    #itemsTableBody tr:hover { background-color: #f0f7ff; }
    
    .supplier-modal, .adjustment-modal, .bank-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7); z-index: 10003; opacity: 0; transition: all 0.3s ease; }
    .supplier-modal.show, .adjustment-modal.show, .bank-modal.show { display: block; transform: translate(-50%, -50%) scale(1); opacity: 1; }
    .supplier-modal { width: 90%; max-width: 700px; }
    .adjustment-modal { width: 80%; max-width: 750px; }
    .bank-modal { width: 500px; }
    .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 10002; }
    .modal-backdrop.show { display: block; }
    .modal-content-box { background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); overflow: hidden; }
    .modal-header-box { padding: 1rem 1.5rem; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-header-box.orange { background: #ff6b35; }
    .modal-header-box.blue { background: #0d6efd; }
    .modal-header-box.gray { background: #6c757d; }
    .modal-body-box { padding: 1rem; }
    .modal-footer-box { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px; }
    .btn-close-modal { background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
    .supplier-list-item { padding: 8px 12px; border-bottom: 1px solid #eee; cursor: pointer; }
    .supplier-list-item:hover { background: #f0f7ff; }
    .supplier-list-item.selected { background: #007bff; color: white; }
    .bank-field-group { display: flex; align-items: center; margin-bottom: 10px; }
    .bank-field-group label { width: 100px; font-weight: 600; font-size: 12px; }
    .bank-field-group input, .bank-field-group select { flex: 1; font-size: 12px; padding: 4px 8px; height: 28px; border: 1px solid #ced4da; }
    
    /* Cash Highlighted Style */
    .cheque-no.cash-highlighted {
        font-weight: bold !important;
        text-transform: uppercase !important;
        background-color: #d4edda !important;
        color: #155724 !important;
        border-color: #28a745 !important;
    }
    
    /* Custom searchable dropdown */
    .custom-dropdown-menu .dropdown-item:hover {
        background-color: #f1f5ff;
    }
    .custom-dropdown-menu .dropdown-item:active {
        background-color: #e3ebff;
    }
    .custom-dropdown-menu .dropdown-item.active {
        background-color: #e3ebff;
    }
    .custom-dropdown-menu::-webkit-scrollbar {
        width: 6px;
    }
    .custom-dropdown-menu::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-dropdown-menu::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    .custom-dropdown-menu::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i> Payment to Supplier</h5>
    <a href="{{ route('admin.supplier-payment.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-list"></i> All Payments</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <form id="paymentForm" method="POST" autocomplete="off">
            @csrf
            <div class="header-section">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="field-group mb-2">
                            <label style="width: 60px;">Date :</label>
                            <input type="date" class="form-control" id="paymentDate" value="{{ date('Y-m-d') }}" style="width: 130px;">
                            <input type="text" class="form-control readonly-field" id="dayName" value="{{ date('l') }}" readonly style="width: 80px;">
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 60px;">TRN NO. :</label>
                            <input type="text" class="form-control readonly-field" id="trnNo" value="{{ $nextTrnNo }}" readonly style="width: 80px;">
                        </div>
                        <div class="field-group">
                            <label style="width: 60px;">Ledger :</label>
                            <input type="text" class="form-control" id="ledger" value="SL" style="width: 50px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Bank :</label>
                            <div class="custom-dropdown-wrapper" style="width: 230px; position: relative;">
                                <input type="text" 
                                       class="form-control" 
                                       id="bankSearchInput" 
                                       placeholder="Select Bank"
                                       autocomplete="off"
                                       style="width: 100%;">
                                <input type="hidden" id="bankSelect" name="bank_code">
                                <i class="bi bi-chevron-down" style="position: absolute; right: 10px; top: 5px; cursor: pointer;" onclick="document.getElementById('bankSearchInput').focus()"></i>
                                
                                <div id="bankDropdown" class="custom-dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; max-height: 250px; overflow-y: auto; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;">
                                    <div id="bankList" class="dropdown-list">
                                        @foreach($banks as $bank)
                                            <div class="dropdown-item" 
                                                 data-id="{{ $bank->alter_code }}" 
                                                 data-name="{{ $bank->name }}"
                                                 style="padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f0f0f0;">
                                                {{ $bank->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="copyParty()">Copy Party (Tab)</button>
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="max-height: 310px; overflow-y: auto;">
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
                    <button type="button" class="btn btn-sm btn-primary" onclick="openSupplierModal()"><i class="bi bi-plus-circle me-1"></i> Add Party</button>
                </div>
            </div>

            <div class="tds-display">TDS Amt. : <span id="tdsAmtDisplay">0.00</span></div>

            <div class="total-section mb-2">
                <div class="row">
                    <div class="col-md-6"><span class="total-label">Total:</span> <span class="ms-3">Cash: ( ) <strong id="totalCash">0.00</strong></span></div>
                    <div class="col-md-6 text-end"><span>Cheque: ( ) <strong id="totalCheque">0.00</strong></span></div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="bg-white border rounded p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2" style="background: #ff9800; padding: 5px; color: white;">
                            <span>1) Amt. Outstanding</span>
                            <span>Total: <strong id="outstandingTotal">0.00</strong></span>
                        </div>
                        <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                            <table class="table table-bordered table-sm mb-0" style="font-size: 10px;">
                                <thead style="position: sticky; top: 0; background: #ffe0b2; z-index: 5;">
                                    <tr><th style="padding: 4px;">Inv. No</th><th style="padding: 4px;">Date</th><th style="padding: 4px;">Amount</th><th style="padding: 4px;">Balance</th></tr>
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
                                    <tr><th style="padding: 4px;">Inv. No</th><th style="padding: 4px;">Date</th><th style="padding: 4px;">Adjusted</th></tr>
                                </thead>
                                <tbody id="adjustedTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="currencyDetail">
                    <label class="form-check-label" for="currencyDetail">Currency Detail</label>
                </div>
                <button type="button" class="btn btn-success" onclick="savePayment()">Save (End)</button>
                <button type="button" class="btn btn-danger" onclick="deletePayment()">Delete</button>
                <a href="{{ route('admin.supplier-payment.index') }}" class="btn btn-secondary">Exit (Esc)</a>
            </div>
        </form>
    </div>
</div>

<!-- Supplier Selection Modal -->
<div class="modal-backdrop" id="supplierModalBackdrop" onclick="closeSupplierModal()"></div>
<div class="supplier-modal" id="supplierModal">
    <div class="modal-content-box">
        <div class="modal-header-box orange">
            <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Select Supplier</h5>
            <button type="button" class="btn-close-modal" onclick="closeSupplierModal()">&times;</button>
        </div>
        <div class="modal-body-box">
            <input type="text" class="form-control mb-3" id="supplierSearch" placeholder="Search by code or name..." oninput="filterSuppliers()">
            <div id="supplierList" style="max-height: 300px; overflow-y: auto;"></div>
        </div>
        <div class="modal-footer-box">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeSupplierModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="confirmSupplierSelection()">Select</button>
        </div>
    </div>
</div>

<!-- Bank Details Modal (Cheque) -->
<div class="modal-backdrop" id="bankModalBackdrop"></div>
<div class="bank-modal" id="bankModal">
    <div class="modal-content-box">
        <div class="modal-header-box gray">
            <h6 class="mb-0"><i class="bi bi-bank me-2"></i>Cheque Bank Details</h6>
            <button type="button" class="btn-close-modal" onclick="closeBankModal()">&times;</button>
        </div>
        <div class="modal-body-box" style="background: #f8f9fa;">
            <div class="bank-field-group">
                <label>Bank Name :</label>
                <select class="form-control" id="chequeBankName">
                    <option value="">Select Bank</option>
                    @foreach($banks as $bank)
                    <option value="{{ $bank->name }}">{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="bank-field-group">
                <label>Bank Area :</label>
                <input type="text" class="form-control" id="chequeBankArea" placeholder="Enter bank area">
            </div>
            <div class="bank-field-group">
                <label>Closed On :</label>
                <input type="date" class="form-control" id="chequeClosedOn">
            </div>
        </div>
        <div class="modal-footer-box">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeBankModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="saveBankDetails()">OK</button>
        </div>
    </div>
</div>

<!-- Adjustment Modal -->
<div class="modal-backdrop" id="adjustmentModalBackdrop"></div>
<div class="adjustment-modal" id="adjustmentModal">
    <div class="modal-content-box">
        <div class="modal-header-box blue">
            <h5 class="mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Payment Adjustment</h5>
            <button type="button" class="btn-close-modal" onclick="closeAdjustmentModal()">&times;</button>
        </div>
        <div class="modal-body-box">
            <div style="max-height: 350px; overflow-y: auto;">
                <table class="table table-bordered" style="font-size: 11px; margin-bottom: 0;">
                    <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                        <tr>
                            <th style="width: 50px; text-align: center;">SR.NO.</th>
                            <th style="width: 120px; text-align: center;">BILL NO.</th>
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
                    <span style="font-weight: bold; font-size: 14px; color: #0d6efd;">AMOUNT TO ADJUST (Rs) : <span id="adjustmentAmountDisplay">₹ 0.00</span></span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="font-weight: bold;">Auto Adjust:</label>
                        <input type="number" id="autoAdjustAmount" class="form-control form-control-sm" style="width: 120px;" step="0.01">
                        <button type="button" class="btn btn-info btn-sm" onclick="autoDistributeAmount()"><i class="bi bi-magic me-1"></i>Auto Distribute</button>
                    </div>
                    <span style="font-weight: bold; font-size: 14px;">REMAINING : <span id="adjustmentRemainingDisplay" style="color: #28a745;">₹ 0.00</span></span>
                </div>
            </div>
        </div>
        <div class="modal-footer-box">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustmentModal()">Cancel</button>
            <button type="button" class="btn btn-success btn-sm" onclick="saveAdjustmentData()"><i class="bi bi-check-circle me-1"></i>Save Adjustment</button>
        </div>
    </div>
</div>


<script>
let suppliers = @json($suppliers);
let itemRowCount = 0;
let currentRowIndex = null;
let selectedSupplier = null;
let currentOutstandingSupplierId = null;
let adjustmentData = [];
let currentBankRow = null;
let rowBankDetails = {};
let supplierModalEnterLock = false;

document.addEventListener('DOMContentLoaded', function() {
    buildSupplierList();
    
    const paymentDate = document.getElementById('paymentDate');
    paymentDate.addEventListener('change', function() {
        const date = new Date(this.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    });
    
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            return false;
        });
    }

    // Auto-focus on first field (Date)
    setTimeout(() => {
        if(paymentDate) paymentDate.focus();
    }, 100);

    // Custom Bank Dropdown functionality
    const bankSearchInput = document.getElementById('bankSearchInput');
    const bankDropdown = document.getElementById('bankDropdown');
    const bankSelect = document.getElementById('bankSelect');
    const bankList = document.getElementById('bankList');
    
    let bankActiveIndex = -1;

    function getVisibleBankItems() {
        return Array.from(bankList.querySelectorAll('.dropdown-item:not([style*="display: none"])'));
    }

    function setActiveBankItem(index) {
        const items = getVisibleBankItems();
        items.forEach(item => item.classList.remove('active'));
        if (index < 0 || index >= items.length) {
            bankActiveIndex = -1;
            return;
        }
        bankActiveIndex = index;
        const activeItem = items[index];
        activeItem.classList.add('active');
        activeItem.scrollIntoView({ block: 'nearest' });
    }

    function filterBanks(searchTerm) {
        const items = bankList.querySelectorAll('.dropdown-item');
        let visibleCount = 0;
        const normalized = (searchTerm || '').toLowerCase();
        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            if (name.includes(normalized)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        setActiveBankItem(-1);
    }

    function selectBankItem(item) {
        if (!item) return false;
        const bankId = item.getAttribute('data-id');
        const name = item.getAttribute('data-name') || '';
        bankSearchInput.value = name;
        bankSelect.value = bankId || '';
        bankDropdown.style.display = 'none';

        // After bank selection, directly continue with Add Party flow
        setTimeout(() => {
            if (typeof openSupplierModal === 'function') {
                openSupplierModal();
            }
        }, 50);
        return true;
    }

    if (bankSearchInput) {
        bankSearchInput.addEventListener('focus', function() {
            bankDropdown.style.display = 'block';
            filterBanks(this.value || '');
        });

        bankSearchInput.addEventListener('input', function() {
            filterBanks(this.value);
            bankDropdown.style.display = 'block';
        });

        bankSearchInput.addEventListener('keydown', function(e) {
            if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) {
                // Block any page/global key handlers while bank dropdown is active
                e.stopPropagation();
                e.stopImmediatePropagation();
            }

            const visibleItems = getVisibleBankItems();
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (bankActiveIndex < visibleItems.length - 1) {
                    setActiveBankItem(bankActiveIndex + 1);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (bankActiveIndex > 0) {
                    setActiveBankItem(bankActiveIndex - 1);
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                if (bankActiveIndex >= 0 && visibleItems[bankActiveIndex]) {
                    selectBankItem(visibleItems[bankActiveIndex]);
                } else if (visibleItems.length > 0) {
                    selectBankItem(visibleItems[0]);
                } else if (bankSearchInput.value === '') {
                    // Optional: allow tabbing/entering past without selection if empty
                    bankDropdown.style.display = 'none';
                    setTimeout(() => {
                        $('#ledger').focus();
                        $('#ledger').select();
                    }, 50);
                }
            } else if (e.key === 'Escape') {
                e.preventDefault();
                bankDropdown.style.display = 'none';
            }
        });
        
        // Click on item
        bankList.addEventListener('click', function(e) {
            const item = e.target.closest('.dropdown-item');
            if (!item) return;
            selectBankItem(item);
        });

        // Click outside closes dropdown
        document.addEventListener('click', function(e) {
            if (!bankSearchInput.contains(e.target) && !bankDropdown.contains(e.target)) {
                bankDropdown.style.display = 'none';
            }
        });

        // Capture-level handler: bank dropdown must own Enter/Arrow flow
        // (prevents global keyboard handlers from stealing Enter and moving focus elsewhere)
        document.addEventListener('keydown', function(e) {
            if (!bankDropdown || bankDropdown.style.display !== 'block') return;
            if (document.activeElement !== bankSearchInput) return;
            if (!['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;

            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            const visibleItems = getVisibleBankItems();
            if (e.key === 'ArrowDown') {
                if (visibleItems.length === 0) return;
                if (bankActiveIndex < visibleItems.length - 1) {
                    setActiveBankItem(bankActiveIndex + 1);
                } else {
                    setActiveBankItem(0);
                }
                return;
            }

            if (e.key === 'ArrowUp') {
                if (visibleItems.length === 0) return;
                if (bankActiveIndex > 0) {
                    setActiveBankItem(bankActiveIndex - 1);
                } else {
                    setActiveBankItem(visibleItems.length - 1);
                }
                return;
            }

            if (e.key === 'Enter') {
                if (bankActiveIndex >= 0 && visibleItems[bankActiveIndex]) {
                    selectBankItem(visibleItems[bankActiveIndex]);
                } else if (visibleItems.length > 0) {
                    selectBankItem(visibleItems[0]);
                } else {
                    bankDropdown.style.display = 'none';
                }
                return;
            }

            if (e.key === 'Escape') {
                bankDropdown.style.display = 'none';
            }
        }, true);
    }

    // Enter key navigation: Date -> Bank
    if (paymentDate) {
        paymentDate.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                if (bankSearchInput) {
                    bankSearchInput.focus();
                }
            }
        });
    }

    // Enter key navigation: Ledger -> Add Party
    const ledger = document.getElementById('ledger');
    if (ledger) {
        ledger.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                openSupplierModal();
            }
        });
    }
});

function buildSupplierList() {
    const container = document.getElementById('supplierList');
    container.innerHTML = suppliers.map(s => `
        <div class="supplier-list-item" data-id="${s.supplier_id || s.id || ''}" data-code="${s.code || ''}" data-name="${s.name}" onclick="selectSupplierItem(this)">
            <strong>${s.code || '-'}</strong> - ${s.name}
        </div>
    `).join('');
}

function filterSuppliers() {
    const search = document.getElementById('supplierSearch').value.toLowerCase();
    let hasVisible = false;
    let firstVisibleObj = null;

    document.querySelectorAll('#supplierList .supplier-list-item').forEach(item => {
        const code = item.dataset.code.toLowerCase();
        const name = item.dataset.name.toLowerCase();
        
        if (code.includes(search) || name.includes(search)) {
            item.style.display = '';
            if (!hasVisible) {
                firstVisibleObj = item;
                hasVisible = true;
            }
        } else {
            item.style.display = 'none';
            item.classList.remove('selected');
        }
    });

    if (firstVisibleObj) {
        selectSupplierItem(firstVisibleObj, false);
    }
}

function selectSupplierItem(el, scrollTo = true) {
    document.querySelectorAll('#supplierList .supplier-list-item').forEach(item => item.classList.remove('selected'));
    if(el) {
        el.classList.add('selected');
        selectedSupplier = { id: el.dataset.id || '', code: el.dataset.code || '', name: el.dataset.name || '' };
        if(scrollTo) {
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
}

function openSupplierModal() {
    selectedSupplier = null;
    document.getElementById('supplierSearch').value = '';

    // Remove any stale listeners (both phases)
    window.removeEventListener('keydown', handleSupplierModalKeydown, true);
    document.removeEventListener('keydown', handleSupplierModalKeydown);

    filterSuppliers();
    document.querySelectorAll('#supplierList .supplier-list-item').forEach(item => item.classList.remove('selected'));
    document.getElementById('supplierModalBackdrop').classList.add('show');
    document.getElementById('supplierModal').classList.add('show');

    // CAPTURE phase — fires before ALL other handlers
    window.addEventListener('keydown', handleSupplierModalKeydown, true);

    setTimeout(() => {
        document.getElementById('supplierSearch').focus();
        const visibleItems = Array.from(document.querySelectorAll('#supplierList .supplier-list-item'))
            .filter(el => el.style.display !== 'none');
        if (visibleItems.length > 0) selectSupplierItem(visibleItems[0], false);
    }, 80);
}

function handleSupplierModalKeydown(e) {
    const modal = document.getElementById('supplierModal');
    if (!modal.classList.contains('show')) return;
    
    // Allow typing in the search box
    const searchInput = document.getElementById('supplierSearch');
    const isSearchFocused = document.activeElement === searchInput;
    
    // Exit if it's not a navigation key
    if (!['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) return;
    
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    
    const items = Array.from(document.querySelectorAll('#supplierList .supplier-list-item')).filter(el => el.style.display !== 'none');
    if (items.length === 0) return;
    
    let currentIndex = items.findIndex(el => el.classList.contains('selected'));
    
    if (e.key === 'ArrowDown') {
        let nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
        selectSupplierItem(items[nextIndex], true);
    } 
    else if (e.key === 'ArrowUp') {
        let prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
        selectSupplierItem(items[prevIndex], true);
    } 
    else if (e.key === 'Enter') {
        if (supplierModalEnterLock) return;
        supplierModalEnterLock = true;

        // Fallback: if no row is marked selected, pick first visible row
        if (currentIndex < 0 && items.length > 0) {
            selectSupplierItem(items[0], true);
            currentIndex = 0;
        }

        if (currentIndex >= 0 && items[currentIndex]) {
            // Ensure selectedSupplier object is synced with highlighted row
            selectSupplierItem(items[currentIndex], false);
            confirmSupplierSelection();
        }

        setTimeout(() => { supplierModalEnterLock = false; }, 120);
    }
}

function closeSupplierModal() {
    window.removeEventListener('keydown', handleSupplierModalKeydown, true);
    document.removeEventListener('keydown', handleSupplierModalKeydown);
    document.getElementById('supplierModalBackdrop').classList.remove('show');
    document.getElementById('supplierModal').classList.remove('show');
    supplierModalEnterLock = false;
}

function confirmSupplierSelection() {
    if (!selectedSupplier) {
        const selectedEl = document.querySelector('#supplierList .supplier-list-item.selected');
        if (selectedEl) {
            selectedSupplier = {
                id: selectedEl.dataset.id || '',
                code: selectedEl.dataset.code || '',
                name: selectedEl.dataset.name || ''
            };
        }
    }

    if (!selectedSupplier) { alert('Please select a supplier'); return; }
    const newRowIndex = itemRowCount; // Capturing the index before addItemRow increments it
    addItemRow(selectedSupplier);
    fetchSupplierOutstanding(selectedSupplier.id);
    closeSupplierModal();
    
    // Auto-focus on the Cheque No field of the newly added row
    setTimeout(() => {
        const tr = document.getElementById(`itemRow_${newRowIndex}`);
        if (tr) {
            const chequeNoInput = tr.querySelector('.cheque-no');
            if (chequeNoInput) {
                chequeNoInput.focus();
                chequeNoInput.select();
            }
        }
    }, 150);
}

function fetchSupplierOutstanding(supplierId) {
    currentOutstandingSupplierId = supplierId;
    document.getElementById('outstandingTableBody').innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
    
    fetch(`{{ url('admin/supplier-payment/supplier-outstanding') }}/${supplierId}?page=1&per_page=50`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.outstanding) {
                displayOutstandingInvoices(data.outstanding);
                document.getElementById('outstandingTotal').textContent = parseFloat(data.total_amount || 0).toFixed(2);
            } else {
                document.getElementById('outstandingTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-muted">No outstanding invoices</td></tr>';
                document.getElementById('outstandingTotal').textContent = '0.00';
            }
        })
        .catch(err => {
            console.error('Error:', err);
            document.getElementById('outstandingTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading data</td></tr>';
        });
}

function displayOutstandingInvoices(invoices) {
    const tbody = document.getElementById('outstandingTableBody');
    tbody.innerHTML = '';
    
    if (!invoices || invoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No outstanding invoices</td></tr>';
        return;
    }
    
    invoices.forEach(inv => {
        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.onclick = function() { openAdjustmentModalDirect(); };
        tr.innerHTML = `
            <td style="padding: 4px;">${inv.invoice_no || '-'}</td>
            <td style="padding: 4px;">${inv.invoice_date ? new Date(inv.invoice_date).toLocaleDateString('en-GB') : '-'}</td>
            <td style="padding: 4px; text-align: right;">${parseFloat(inv.net_amount || 0).toFixed(2)}</td>
            <td style="padding: 4px; text-align: right;">${parseFloat(inv.balance_amount || 0).toFixed(2)}</td>
        `;
        tbody.appendChild(tr);
    });
}

function addItemRow(supplier) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = itemRowCount++;
    
    const tr = document.createElement('tr');
    tr.id = `itemRow_${rowIndex}`;
    tr.dataset.rowIndex = rowIndex;
    tr.dataset.supplierId = supplier.id;
    tr.dataset.paymentType = 'cash';
    tr.onclick = function(e) { if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'BUTTON') selectRow(rowIndex); };
    
    tr.innerHTML = `
        <td><input type="text" class="form-control" value="${supplier.code || ''}" readonly></td>
        <td><input type="text" class="form-control" value="${supplier.name}" readonly></td>
        <td><input type="text" class="form-control cheque-no" data-row="${rowIndex}" placeholder="Cheque No"></td>
        <td><input type="date" class="form-control cheque-date" data-row="${rowIndex}"></td>
        <td><input type="number" class="form-control amount-input" data-row="${rowIndex}" step="0.01" value="0.00"></td>
        <td><input type="number" class="form-control unadjusted-input" data-row="${rowIndex}" step="0.01" value="0.00" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button></td>
    `;
    
    tbody.appendChild(tr);
    selectRow(rowIndex);
    
    // Cheque No - Press Enter to open Bank Modal (skip if 'cash')
    const chequeNoInput = tr.querySelector('.cheque-no');
    chequeNoInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = this.value.trim().toLowerCase();
            if (value && value !== 'cash') {
                // Open bank modal only for actual cheque numbers
                const row = document.getElementById(`itemRow_${rowIndex}`);
                row.dataset.paymentType = 'cheque';
                openBankModal(rowIndex);
            } else {
                // If empty or 'cash', skip to date field
                tr.querySelector('.cheque-date').focus();
            }
        }
    });
    chequeNoInput.addEventListener('change', function() {
        const row = document.getElementById(`itemRow_${rowIndex}`);
        const value = this.value.trim().toLowerCase();
        
        // If cash, capitalize and highlight
        if (value === 'cash') {
            this.value = 'CASH';
            this.classList.add('cash-highlighted');
            row.dataset.paymentType = 'cash';
        } else {
            this.classList.remove('cash-highlighted');
            row.dataset.paymentType = value ? 'cheque' : 'cash';
        }
        updateTotals();
    });
    
    // Cheque Date - Press Enter to move to Amount
    const chequeDateInput = tr.querySelector('.cheque-date');
    chequeDateInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            tr.querySelector('.amount-input').focus();
        }
    });
    
    // Amount - Press Enter to open Add Party modal
    const amountInput = tr.querySelector('.amount-input');
    amountInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            triggerAddPartyFromAmountInput(this);
        }
    });
    amountInput.addEventListener('change', function() {
        const amount = parseFloat(this.value || 0);
        setUnadjustedAmount(tr, amount);
        updateTotals();
    });
}

function triggerAddPartyFromAmountInput(inputEl) {
    if (!inputEl) return;
    const tr = inputEl.closest('tr');
    if (!tr) return;

    const amount = parseFloat(inputEl.value || 0);
    if (amount > 0) {
        setUnadjustedAmount(tr, amount);
        updateTotals();
    }

    if (isAnyPaymentModalOpen()) return;
    openSupplierModal();
}

function isAnyPaymentModalOpen() {
    const supplierModal = document.getElementById('supplierModal');
    const bankModal = document.getElementById('bankModal');
    const adjustmentModal = document.getElementById('adjustmentModal');
    return !!(
        (supplierModal && supplierModal.classList.contains('show')) ||
        (bankModal && bankModal.classList.contains('show')) ||
        (adjustmentModal && adjustmentModal.classList.contains('show'))
    );
}

// Set unadjusted amount = entered amount (before adjustment)
function setUnadjustedAmount(row, amount) {
    const unadjustedInput = row.querySelector('.unadjusted-input');
    if (unadjustedInput) {
        unadjustedInput.value = amount.toFixed(2);
    }
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(tr => tr.classList.remove('row-selected'));
    const row = document.getElementById(`itemRow_${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        currentRowIndex = rowIndex;
        const supplierId = row.dataset.supplierId;
        if (supplierId && supplierId != currentOutstandingSupplierId) {
            fetchSupplierOutstanding(supplierId);
        }
    }
}

function removeRow(rowIndex) {
    const row = document.getElementById(`itemRow_${rowIndex}`);
    if (row) { row.remove(); updateTotals(); }
}

function updateTotals() {
    let totalCash = 0, totalCheque = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(tr => {
        const amount = parseFloat(tr.querySelector('.amount-input')?.value || 0);
        if (tr.dataset.paymentType === 'cheque') totalCheque += amount;
        else totalCash += amount;
    });
    document.getElementById('totalCash').textContent = totalCash.toFixed(2);
    document.getElementById('totalCheque').textContent = totalCheque.toFixed(2);
}

function copyParty() {
    if (currentRowIndex === null) return;
    const currentRow = document.getElementById(`itemRow_${currentRowIndex}`);
    if (!currentRow) return;
    addItemRow({ id: currentRow.dataset.supplierId, code: currentRow.querySelector('td:first-child input').value, name: currentRow.querySelector('td:nth-child(2) input').value });
}

// Bank Modal Functions
function openBankModal(rowIndex) {
    currentBankRow = rowIndex;
    const existing = rowBankDetails[rowIndex] || {};
    
    // Set cheque bank name - handle Select2
    const chequeBankNameSelect = document.getElementById('chequeBankName');
    chequeBankNameSelect.value = existing.bankName || '';
    if (typeof $ !== 'undefined' && $(chequeBankNameSelect).data('select2')) {
        $(chequeBankNameSelect).val(existing.bankName || '').trigger('change');
    }
    
    document.getElementById('chequeBankArea').value = existing.bankArea || '';
    document.getElementById('chequeClosedOn').value = existing.closedOn || '';
    document.getElementById('bankModalBackdrop').classList.add('show');
    document.getElementById('bankModal').classList.add('show');
    setTimeout(() => {
        // Focus on Select2 or regular select
        if (typeof $ !== 'undefined' && $(chequeBankNameSelect).data('select2')) {
            $(chequeBankNameSelect).select2('open');
        } else {
            chequeBankNameSelect.focus();
        }
    }, 100);
}

function closeBankModal() {
    document.getElementById('bankModalBackdrop').classList.remove('show');
    document.getElementById('bankModal').classList.remove('show');
    // Focus back to cheque date after closing bank modal
    if (currentBankRow !== null) {
        const row = document.getElementById(`itemRow_${currentBankRow}`);
        if (row) {
            row.querySelector('.cheque-date').focus();
        }
    }
}

function saveBankDetails() {
    if (currentBankRow !== null) {
        const closedOn = document.getElementById('chequeClosedOn').value;
        rowBankDetails[currentBankRow] = {
            bankName: document.getElementById('chequeBankName').value,
            bankArea: document.getElementById('chequeBankArea').value,
            closedOn: closedOn
        };
        
        // Also set the cheque date in the row
        const row = document.getElementById(`itemRow_${currentBankRow}`);
        if (row && closedOn) {
            row.querySelector('.cheque-date').value = closedOn;
        }
    }
    closeBankModal();
}

// Adjustment Modal Functions
let currentAdjustmentRow = null;
let currentAdjustmentAmount = 0;

function openAdjustmentModalDirect() {
    if (currentRowIndex === null) { alert('Please select a row first'); return; }
    
    const row = document.getElementById(`itemRow_${currentRowIndex}`);
    const amount = parseFloat(row.querySelector('.amount-input').value || 0);
    
    if (amount <= 0) {
        alert('Please enter an amount first');
        row.querySelector('.amount-input').focus();
        return;
    }
    
    currentAdjustmentRow = currentRowIndex;
    currentAdjustmentAmount = amount;
    
    document.getElementById('adjustmentAmountDisplay').textContent = '₹ ' + amount.toFixed(2);
    document.getElementById('autoAdjustAmount').value = amount.toFixed(2);
    
    const supplierId = row.dataset.supplierId;
    
    fetch(`{{ url('admin/supplier-payment/supplier-outstanding') }}/${supplierId}?page=1&per_page=100`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.outstanding) {
                populateAdjustmentTable(data.outstanding);
            }
        });
    
    document.getElementById('adjustmentModalBackdrop').classList.add('show');
    document.getElementById('adjustmentModal').classList.add('show');
}

function populateAdjustmentTable(invoices) {
    const tbody = document.getElementById('adjustmentTableBody');
    tbody.innerHTML = '';
    
    if (!invoices || invoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No outstanding invoices</td></tr>';
        return;
    }
    
    invoices.forEach((inv, idx) => {
        // Available amount = balance + existing adjustment (for consistency)
        const availableAmount = parseFloat(inv.available_amount || inv.balance_amount || 0);
        const existingAdj = parseFloat(inv.existing_adjustment || 0);
        const currentBalance = availableAmount - existingAdj;
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="text-align: center;">${idx + 1}</td>
            <td style="text-align: center;">${inv.invoice_no || '-'}</td>
            <td style="text-align: center;">${inv.invoice_date ? new Date(inv.invoice_date).toLocaleDateString('en-GB') : '-'}</td>
            <td style="text-align: right; font-weight: bold; color: #0d6efd;">₹ ${availableAmount.toFixed(2)}</td>
            <td><input type="number" class="form-control form-control-sm adj-amount" 
                data-id="${inv.id}" 
                data-invoice="${inv.invoice_no}" 
                data-date="${inv.invoice_date}" 
                data-bill="${inv.net_amount}" 
                data-available="${availableAmount}" 
                step="0.01" 
                value="${existingAdj > 0 ? existingAdj.toFixed(2) : '0.00'}" 
                max="${availableAmount}"
                style="text-align: right;" 
                onchange="updateAdjustmentRemaining()" 
                oninput="updateAdjustmentRemaining()"></td>
            <td style="text-align: right;" id="adj_bal_${inv.id}"><span style="color: #28a745;">₹ ${currentBalance.toFixed(2)}</span></td>
        `;
        tbody.appendChild(tr);
    });
    updateAdjustmentRemaining();
}

function updateAdjustmentRemaining() {
    let totalAdjusted = 0;
    document.querySelectorAll('.adj-amount').forEach(input => {
        let adjusted = parseFloat(input.value || 0);
        const invoiceId = input.dataset.id;
        const available = parseFloat(input.dataset.available || input.dataset.balance || 0);
        
        // Prevent adjusting more than available
        if (adjusted > available) {
            input.value = available.toFixed(2);
            adjusted = available;
        }
        
        totalAdjusted += adjusted;
        
        // Update balance display
        const newBalance = available - adjusted;
        const balanceCell = document.getElementById(`adj_bal_${invoiceId}`);
        if (balanceCell) {
            balanceCell.innerHTML = `<span style="color: #28a745;">₹ ${newBalance.toFixed(2)}</span>`;
        }
    });
    const remaining = currentAdjustmentAmount - totalAdjusted;
    const remainingEl = document.getElementById('adjustmentRemainingDisplay');
    if (remainingEl) {
        remainingEl.textContent = '₹ ' + remaining.toFixed(2);
        remainingEl.style.color = remaining >= 0 ? '#28a745' : '#dc3545';
    }
}

function autoDistributeAmount() {
    let remaining = parseFloat(document.getElementById('autoAdjustAmount').value || 0);
    document.querySelectorAll('.adj-amount').forEach(input => {
        if (remaining <= 0) { input.value = '0.00'; return; }
        const available = parseFloat(input.dataset.available || input.dataset.balance || 0);
        const toAdjust = Math.min(remaining, available);
        input.value = toAdjust.toFixed(2);
        remaining -= toAdjust;
    });
    updateAdjustmentRemaining();
}

function saveAdjustmentData() {
    adjustmentData = [];
    let totalAdjusted = 0;
    
    document.querySelectorAll('.adj-amount').forEach(input => {
        const adjAmount = parseFloat(input.value || 0);
        if (adjAmount > 0) {
            const available = parseFloat(input.dataset.available || input.dataset.balance || 0);
            adjustmentData.push({
                purchase_transaction_id: input.dataset.id,
                reference_no: input.dataset.invoice,
                reference_date: input.dataset.date,
                reference_amount: parseFloat(input.dataset.bill || 0),
                adjusted_amount: adjAmount,
                balance_amount: available - adjAmount,
                adjustment_type: 'outstanding'
            });
            totalAdjusted += adjAmount;
        }
    });
    
    if (currentAdjustmentRow !== null) {
        const row = document.getElementById(`itemRow_${currentAdjustmentRow}`);
        const amount = parseFloat(row.querySelector('.amount-input').value || 0);
        row.querySelector('.unadjusted-input').value = (amount - totalAdjusted).toFixed(2);
    }
    
    updateAdjustedTable();
    closeAdjustmentModal();
}

function updateAdjustedTable() {
    const tbody = document.getElementById('adjustedTableBody');
    tbody.innerHTML = '';
    let total = 0;
    
    adjustmentData.forEach(adj => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 4px;">${adj.reference_no || '-'}</td>
            <td style="padding: 4px;">${adj.reference_date ? new Date(adj.reference_date).toLocaleDateString('en-GB') : '-'}</td>
            <td style="padding: 4px; text-align: right;">${adj.adjusted_amount.toFixed(2)}</td>
        `;
        tbody.appendChild(tr);
        total += adj.adjusted_amount;
    });
    document.getElementById('adjustedTotal').textContent = total.toFixed(2);
}

function closeAdjustmentModal() {
    document.getElementById('adjustmentModalBackdrop').classList.remove('show');
    document.getElementById('adjustmentModal').classList.remove('show');
}

function savePayment() {
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(tr => {
        const rowIdx = tr.dataset.rowIndex;
        const code = tr.querySelector('td:first-child input').value;
        const name = tr.querySelector('td:nth-child(2) input').value;
        const chequeNo = tr.querySelector('.cheque-no').value;
        const chequeDate = tr.querySelector('.cheque-date').value;
        const amount = parseFloat(tr.querySelector('.amount-input').value || 0);
        const unadjusted = parseFloat(tr.querySelector('.unadjusted-input').value || 0);
        const bankDetails = rowBankDetails[rowIdx] || {};
        
        if (code || name) {
            items.push({
                party_code: code,
                party_name: name,
                cheque_no: chequeNo,
                cheque_date: chequeDate || null,
                cheque_bank_name: bankDetails.bankName || null,
                cheque_bank_area: bankDetails.bankArea || null,
                cheque_closed_on: bankDetails.closedOn || null,
                amount: amount,
                unadjusted: unadjusted,
                payment_type: chequeNo ? 'cheque' : 'cash'
            });
        }
    });
    
    if (items.length === 0) { alert('Please add at least one party'); return; }
    
    const payload = {
        payment_date: document.getElementById('paymentDate').value,
        ledger: document.getElementById('ledger').value,
        bank_code: document.getElementById('bankSelect').value,
        tds_amount: 0,
        currency_detail: document.getElementById('currencyDetail').checked,
        items: items,
        adjustments: adjustmentData
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('{{ route("admin.supplier-payment.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Payment saved successfully! Trn No: ' + result.trn_no);
            window.location.href = '{{ route("admin.supplier-payment.index") }}';
        } else {
            alert(result.message || 'Failed to save payment');
        }
    })
    .catch(err => { console.error('Error:', err); alert('An error occurred while saving'); });
}

function deletePayment() { alert('This is a new payment. Nothing to delete.'); }

// Capture-phase fallback:
// Ensure Enter on amount field always triggers Add Party flow
document.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    const activeEl = document.activeElement;
    if (!activeEl || !activeEl.classList || !activeEl.classList.contains('amount-input')) return;

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    triggerAddPartyFromAmountInput(activeEl);
}, true);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSupplierModal();
        closeAdjustmentModal();
        closeBankModal();
    }
    if (e.key === 'Tab' && e.shiftKey) {
        const activeEl = document.activeElement;
        const bankSearchInput = document.getElementById('bankSearchInput');
        const bankDropdown = document.getElementById('bankDropdown');
        const isBankContext =
            (bankSearchInput && activeEl === bankSearchInput) ||
            (bankDropdown && bankDropdown.style.display === 'block');
        if (isBankContext) {
            return;
        }
        e.preventDefault();
        copyParty();
    }
    
    // Ctrl+S to save transaction
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        const saveBtn = document.querySelector('.btn-primary[onclick*="savePayment"]');
        if (saveBtn) {
            saveBtn.click();
        } else if (typeof savePayment === 'function') {
            savePayment();
        }
        return false;
    }
});

// Capture fallback: when supplier modal is open, Enter must confirm highlighted supplier
document.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;

    const modal = document.getElementById('supplierModal');
    if (!modal || !modal.classList.contains('show')) return;
    if (!modal.contains(document.activeElement)) return;
    if (document.activeElement && document.activeElement.tagName === 'BUTTON') return;
    if (supplierModalEnterLock) return;

    e.preventDefault();
    e.stopPropagation();
    if (typeof e.stopImmediatePropagation === 'function') {
        e.stopImmediatePropagation();
    }

    const items = Array.from(document.querySelectorAll('#supplierList .supplier-list-item'))
        .filter(el => el.style.display !== 'none');
    if (items.length === 0) return;

    let selectedEl = document.querySelector('#supplierList .supplier-list-item.selected');
    if (!selectedEl || selectedEl.style.display === 'none') {
        selectedEl = items[0];
    }

    supplierModalEnterLock = true;
    selectSupplierItem(selectedEl, false);
    confirmSupplierSelection();
    setTimeout(() => { supplierModalEnterLock = false; }, 120);
}, true);

// Bank modal Enter key to save
document.getElementById('bankModal').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        saveBankDetails();
    }
});
</script>
@endsection
