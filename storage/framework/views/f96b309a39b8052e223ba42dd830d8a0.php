<?php $__env->startSection('title', 'Payment Modification'); ?>

<?php $__env->startSection('content'); ?>
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
    
    /* Load Payment Modal row highlight */
    #loadPaymentsList tr.load-row-selected,
    #loadPaymentsList tr.load-row-selected:hover,
    .table-hover #loadPaymentsList tr.load-row-selected,
    .table-hover tbody tr.load-row-selected:hover { 
        background-color: #007bff !important; 
        color: white !important; 
    }
    #loadPaymentsList tr.load-row-selected td,
    #loadPaymentsList tr.load-row-selected:hover td { 
        color: white !important; 
        font-weight: 600; 
        background-color: #007bff !important;
    }
    #loadPaymentsList tr { transition: background-color 0.15s ease; }
    
    .supplier-modal, .adjustment-modal, .bank-modal, .load-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7); z-index: 10003; opacity: 0; transition: all 0.3s ease; }
    .supplier-modal.show, .adjustment-modal.show, .bank-modal.show, .load-modal.show { display: block; transform: translate(-50%, -50%) scale(1); opacity: 1; }
    .supplier-modal { width: 90%; max-width: 700px; }
    .adjustment-modal { width: 80%; max-width: 750px; }
    .bank-modal { width: 500px; }
    .load-modal { width: 600px; }
    .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 10002; }
    .modal-backdrop.show { display: block; }
    .modal-content-box { background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); overflow: hidden; }
    .modal-header-box { padding: 1rem 1.5rem; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-header-box.orange { background: #ff6b35; }
    .modal-header-box.blue { background: #0d6efd; }
    .modal-header-box.gray { background: #6c757d; }
    .modal-header-box.dark { background: #343a40; }
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
    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Payment Modification</h5>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.supplier-payment.index')); ?>" class="btn btn-secondary btn-sm"><i class="bi bi-list"></i> All Payments</a>
        <a href="<?php echo e(route('admin.supplier-payment.transaction')); ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> New Payment</a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-2">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">TRN NO.</span>
                    <input type="text" class="form-control" id="searchTrnNo" placeholder="Enter Trn No">
                    <button class="btn btn-primary" id="searchBtn" type="button" onclick="loadPayment();"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-info btn-sm" id="loadPaymentBtn" type="button" onclick="openLoadModal()"><i class="bi bi-folder-open me-1"></i> Load Payment</button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form">
        <form id="paymentForm" method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>
            <div class="header-section">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="field-group mb-2">
                            <label style="width: 60px;">Date :</label>
                            <input type="date" class="form-control" id="paymentDate" style="width: 130px;">
                            <input type="text" class="form-control readonly-field" id="dayName" readonly style="width: 80px;">
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 60px;">TRN NO. :</label>
                            <input type="text" class="form-control readonly-field" id="trnNo" readonly style="width: 80px;">
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
                                        <?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="dropdown-item" 
                                                 data-id="<?php echo e($bank->alter_code); ?>" 
                                                 data-name="<?php echo e($bank->name); ?>"
                                                 style="padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f0f0f0;">
                                                <?php echo e($bank->name); ?>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <button type="button" class="btn btn-success" onclick="updatePayment()" id="btnUpdate" disabled>Save (End)</button>
                <button type="button" class="btn btn-danger" onclick="deletePayment()" id="btnDelete" disabled>Delete</button>
                <a href="<?php echo e(route('admin.supplier-payment.index')); ?>" class="btn btn-secondary">Exit (Esc)</a>
            </div>
        </form>
    </div>
</div>

<!-- Load Payment Modal -->
<div class="modal-backdrop" id="loadModalBackdrop" onclick="closeLoadModal()"></div>
<div class="load-modal" id="loadModal">
    <div class="modal-content-box">
        <div class="modal-header-box dark">
            <h6 class="mb-0"><i class="bi bi-folder-open me-2"></i>Load Payment</h6>
            <button type="button" class="btn-close-modal" onclick="closeLoadModal()">&times;</button>
        </div>
        <div class="modal-body-box">
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-bordered table-hover" style="font-size: 11px;">
                    <thead class="table-light" style="position: sticky; top: 0;"><tr><th>Trn No</th><th>Date</th><th>Bank</th><th>Amount</th></tr></thead>
                    <tbody id="loadPaymentsList"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer-box"><button class="btn btn-secondary btn-sm" onclick="closeLoadModal()">Close</button></div>
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

<!-- Bank Details Modal -->
<div class="modal-backdrop" id="bankModalBackdrop"></div>
<div class="bank-modal" id="bankModal">
    <div class="modal-content-box">
        <div class="modal-header-box gray">
            <h6 class="mb-0"><i class="bi bi-bank me-2"></i>Cheque Bank Details</h6>
            <button type="button" class="btn-close-modal" onclick="closeBankModal()">&times;</button>
        </div>
        <div class="modal-body-box" style="background: #f8f9fa;">
            <div class="bank-field-group"><label>Bank Name :</label><select class="form-control" id="chequeBankName"><option value="">Select Bank</option><?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($bank->name); ?>"><?php echo e($bank->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="bank-field-group"><label>Bank Area :</label><input type="text" class="form-control" id="chequeBankArea" placeholder="Enter bank area"></div>
            <div class="bank-field-group"><label>Closed On :</label><input type="date" class="form-control" id="chequeClosedOn"></div>
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
                        <tr><th style="width: 50px; text-align: center;">SR.NO.</th><th style="width: 120px; text-align: center;">BILL NO.</th><th style="width: 100px; text-align: center;">DATE</th><th style="width: 100px; text-align: right;">BILL AMT.</th><th style="width: 100px; text-align: center;">ADJUSTED</th><th style="width: 100px; text-align: right;">BALANCE</th></tr>
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
let suppliers = <?php echo json_encode($suppliers, 15, 512) ?>;
let itemRowCount = 0;
let currentRowIndex = null;
let selectedSupplier = null;
let currentPayment = null;
let adjustmentData = [];
let currentOutstandingSupplierId = null;
let currentBankRow = null;
let rowBankDetails = {};

document.addEventListener('DOMContentLoaded', function() {
    buildSupplierList();
    
    const paymentDate = document.getElementById('paymentDate');
    if (paymentDate) {
        paymentDate.addEventListener('change', function() {
            if (!this.value) return;
            const date = new Date(this.value);
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            document.getElementById('dayName').value = days[date.getDay()];
        });
        
        // Enter key navigation: Date -> Bank, Shift+Enter: Date -> TRN NO
        paymentDate.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.shiftKey) {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('searchTrnNo').focus();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                const bankSearchInput = document.getElementById('bankSearchInput');
                if (bankSearchInput) bankSearchInput.focus();
            }
        });
    }

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
        
        // Auto-focus next field (Ledger)
        setTimeout(() => {
            $('#ledger').focus();
            $('#ledger').select();
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
            const visibleItems = getVisibleBankItems();
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (bankActiveIndex < visibleItems.length - 1) setActiveBankItem(bankActiveIndex + 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (bankActiveIndex > 0) setActiveBankItem(bankActiveIndex - 1);
            } else if (e.key === 'Enter' && e.shiftKey) {
                // Shift+Enter: Bank -> Date
                e.preventDefault();
                e.stopPropagation();
                bankDropdown.style.display = 'none';
                document.getElementById('paymentDate').focus();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                if (bankActiveIndex >= 0 && visibleItems[bankActiveIndex]) {
                    selectBankItem(visibleItems[bankActiveIndex]);
                } else if (visibleItems.length > 0) {
                    selectBankItem(visibleItems[0]);
                } else if (bankSearchInput.value === '') {
                    bankDropdown.style.display = 'none';
                    setTimeout(() => {
                        $('#ledger').focus();
                        $('#ledger').select();
                    }, 50);
                }
            } else if (e.key === 'Escape') {
                bankDropdown.style.display = 'none';
            }
        });
        
        bankList.addEventListener('click', function(e) {
            const item = e.target.closest('.dropdown-item');
            if (item) selectBankItem(item);
        });

        document.addEventListener('click', function(e) {
            if (!bankSearchInput.contains(e.target) && !bankDropdown.contains(e.target)) {
                bankDropdown.style.display = 'none';
            }
        });
    }

    // Enter key navigation: Ledger -> Add Party, Shift+Enter: Ledger -> Bank
    const ledger = document.getElementById('ledger');
    if (ledger) {
        ledger.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.shiftKey) {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('bankSearchInput').focus();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                openSupplierModal();
            }
        });
    }
    
    // Fallback form prevent submission
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            return false;
        });
    }
    
    setTimeout(() => {
        if(document.getElementById('searchTrnNo')) document.getElementById('searchTrnNo').focus();
    }, 100);

    const urlParams = new URLSearchParams(window.location.search);
    const trnNo = urlParams.get('trn_no');
    if (trnNo) { document.getElementById('searchTrnNo').value = trnNo; loadPayment(); }
});

function buildSupplierList() {
    const container = document.getElementById('supplierList');
    container.innerHTML = suppliers.map(s => `<div class="supplier-list-item" data-id="${s.supplier_id}" data-code="${s.code || ''}" data-name="${s.name}" onclick="selectSupplierItem(this)"><strong>${s.code || '-'}</strong> - ${s.name}</div>`).join('');
}

function filterSuppliers() {
    const search = document.getElementById('supplierSearch').value.toLowerCase();
    document.querySelectorAll('#supplierList .supplier-list-item').forEach(item => {
        item.style.display = (item.dataset.code.toLowerCase().includes(search) || item.dataset.name.toLowerCase().includes(search)) ? '' : 'none';
    });
}

function handleSupplierModalKeydown(e) {
    const modal = document.getElementById('supplierModal');
    if (!modal || !modal.classList.contains('show')) return;

    const MANAGED = ['ArrowDown', 'ArrowUp', 'Enter', 'Escape'];
    if (!MANAGED.includes(e.key)) return;

    // Block ALL other handlers
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    if (e.key === 'Escape') {
        closeSupplierModal();
        return;
    }

    const items = Array.from(document.querySelectorAll('#supplierList .supplier-list-item'))
        .filter(el => el.style.display !== 'none');
    if (items.length === 0) return;

    let selectedIndex = items.findIndex(item => item.classList.contains('selected'));

    if (e.key === 'ArrowDown') {
        const nextIdx = selectedIndex < items.length - 1 ? selectedIndex + 1 : 0;
        selectSupplierItem(items[nextIdx], true);
        return;
    }

    if (e.key === 'ArrowUp') {
        if (selectedIndex > 0) {
            selectSupplierItem(items[selectedIndex - 1], true);
        } else {
            selectSupplierItem(items[items.length - 1], true);
        }
        return;
    }

    if (e.key === 'Enter') {
        if (selectedIndex !== -1) {
            confirmSupplierSelection();
        } else if (items.length > 0) {
            selectSupplierItem(items[0], true);
            confirmSupplierSelection();
        }
        return;
    }
}

function selectSupplierItem(el, scroll = false) {
    document.querySelectorAll('#supplierList .supplier-list-item').forEach(item => item.classList.remove('selected'));
    if (!el) return;
    el.classList.add('selected');
    selectedSupplier = { id: el.dataset.id, code: el.dataset.code, name: el.dataset.name };
    if (scroll) el.scrollIntoView({ block: 'nearest' });
}

function openSupplierModal() {
    selectedSupplier = null;
    document.getElementById('supplierSearch').value = '';

    // Remove any stale listeners
    window.removeEventListener('keydown', handleSupplierModalKeydown, true);
    const srch = document.getElementById('supplierSearch');
    if (srch) srch.removeEventListener('input', filterSuppliers);

    filterSuppliers();
    document.querySelectorAll('#supplierList .supplier-list-item').forEach(item => item.classList.remove('selected'));
    document.getElementById('supplierModalBackdrop').classList.add('show');
    document.getElementById('supplierModal').classList.add('show');

    // window CAPTURE — fires before every other handler
    window.addEventListener('keydown', handleSupplierModalKeydown, true);
    if (srch) srch.addEventListener('input', filterSuppliers);

    setTimeout(() => {
        if (srch) srch.focus();
        const visibleItems = Array.from(document.querySelectorAll('#supplierList .supplier-list-item'))
            .filter(el => el.style.display !== 'none');
        if (visibleItems.length > 0) selectSupplierItem(visibleItems[0], true);
    }, 80);
}

function closeSupplierModal() {
    window.removeEventListener('keydown', handleSupplierModalKeydown, true);
    const srch = document.getElementById('supplierSearch');
    if (srch) srch.removeEventListener('input', filterSuppliers);
    document.getElementById('supplierModalBackdrop').classList.remove('show');
    document.getElementById('supplierModal').classList.remove('show');
}

function confirmSupplierSelection() {
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

function loadPayment() {
    const trnNo = document.getElementById('searchTrnNo').value.trim();
    if (!trnNo) {
        // Empty TRN → focus Load Payment button
        const lpBtn = document.getElementById('loadPaymentBtn');
        if (lpBtn) lpBtn.focus();
        return;
    }
    
    fetch(`<?php echo e(url('admin/supplier-payment/get-by-trn')); ?>/${trnNo}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.payment) { 
                populatePaymentData(data.payment);
                // After load → cursor on first row cheque-no
                setTimeout(() => {
                    const firstRow = document.querySelector('#itemsTableBody tr');
                    if (firstRow) {
                        const chequeNo = firstRow.querySelector('.cheque-no');
                        if (chequeNo) { chequeNo.focus(); chequeNo.select(); return; }
                    }
                    // No rows → focus paymentDate
                    const pd = document.getElementById('paymentDate');
                    if (pd) pd.focus();
                }, 150);
            }
            else { alert(data.message || 'Payment not found'); }
        })
        .catch(err => { console.error('Error:', err); alert('Error loading payment'); });
}

function populatePaymentData(payment) {
    currentPayment = payment;
    document.getElementById('paymentDate').value = payment.payment_date.split('T')[0];
    document.getElementById('dayName').value = payment.day_name || '';
    document.getElementById('trnNo').value = payment.trn_no;
    document.getElementById('ledger').value = payment.ledger || 'SL';
    
    // Set bank value
    const bankSelect = document.getElementById('bankSelect');
    const bankSearchInput = document.getElementById('bankSearchInput');
    bankSelect.value = payment.bank_code || '';
    if (bankSearchInput) {
        if (payment.bank_code) {
            const item = document.querySelector(`.dropdown-item[data-id="${payment.bank_code}"]`);
            if (item) {
                bankSearchInput.value = item.getAttribute('data-name') || '';
            }
        } else {
            bankSearchInput.value = '';
        }
    }
    
    document.getElementById('currencyDetail').checked = payment.currency_detail;
    
    document.getElementById('itemsTableBody').innerHTML = '';
    itemRowCount = 0;
    rowBankDetails = {};
    
    if (payment.items && payment.items.length > 0) {
        payment.items.forEach(item => addItemRowWithData(item));
    }
    
    adjustmentData = [];
    if (payment.adjustments && payment.adjustments.length > 0) {
        payment.adjustments.forEach(adj => {
            adjustmentData.push({
                purchase_transaction_id: null,
                reference_no: adj.reference_no,
                reference_date: adj.reference_date,
                reference_amount: parseFloat(adj.reference_amount || 0),
                adjusted_amount: parseFloat(adj.adjusted_amount || 0),
                balance_amount: parseFloat(adj.balance_amount || 0),
                adjustment_type: adj.adjustment_type
            });
        });
        updateAdjustedTable();
    }
    
    updateTotals();
    document.getElementById('btnUpdate').disabled = false;
    document.getElementById('btnDelete').disabled = false;
}

function addItemRowWithData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = itemRowCount++;
    
    rowBankDetails[rowIndex] = {
        bankName: item.cheque_bank_name || '',
        bankArea: item.cheque_bank_area || '',
        closedOn: item.cheque_closed_on ? item.cheque_closed_on.split('T')[0] : ''
    };
    
    const tr = document.createElement('tr');
    tr.id = `itemRow_${rowIndex}`;
    tr.dataset.rowIndex = rowIndex;
    tr.dataset.supplierId = item.supplier_id || '';
    tr.dataset.paymentType = item.payment_type || 'cash';
    tr.onclick = function(e) { if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'BUTTON') selectRow(rowIndex); };
    
    tr.innerHTML = `
        <td><input type="text" class="form-control" value="${item.party_code || ''}" readonly></td>
        <td><input type="text" class="form-control" value="${item.party_name || ''}" readonly></td>
        <td><input type="text" class="form-control cheque-no" data-row="${rowIndex}" value="${item.cheque_no || ''}"></td>
        <td><input type="date" class="form-control cheque-date" data-row="${rowIndex}" value="${item.cheque_date ? item.cheque_date.split('T')[0] : ''}"></td>
        <td><input type="number" class="form-control amount-input" data-row="${rowIndex}" step="0.01" value="${parseFloat(item.amount || 0).toFixed(2)}"></td>
        <td><input type="number" class="form-control unadjusted-input" data-row="${rowIndex}" step="0.01" value="${parseFloat(item.unadjusted || 0).toFixed(2)}" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button></td>
    `;
    
    tbody.appendChild(tr);
    setupRowEventListeners(tr, rowIndex);
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
    setupRowEventListeners(tr, rowIndex);
}

function setupRowEventListeners(tr, rowIndex) {
    const chequeNoInput = tr.querySelector('.cheque-no');
    
    function updateCashHighlight() {
        const val = chequeNoInput.value.trim().toLowerCase();
        if (val === 'cash') chequeNoInput.classList.add('cash-highlighted');
        else chequeNoInput.classList.remove('cash-highlighted');
    }
    
    // Initial check just in case
    updateCashHighlight();

    chequeNoInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.shiftKey) {
            // Shift+Enter: Cheque No -> Ledger (or previous row's Amount)
            e.preventDefault();
            e.stopPropagation();
            const allRows = Array.from(document.querySelectorAll('#itemsTableBody tr'));
            const currentIdx = allRows.indexOf(tr);
            if (currentIdx > 0) {
                const prevRow = allRows[currentIdx - 1];
                const prevAmount = prevRow.querySelector('.amount-input');
                if (prevAmount) prevAmount.focus();
            } else {
                document.getElementById('ledger').focus();
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value.trim().toLowerCase();
            if (val && val !== 'cash') {
                tr.dataset.paymentType = 'cheque';
                openBankModal(rowIndex);
            } else {
                if(val === 'cash') this.value = 'CASH';
                updateCashHighlight();
                tr.querySelector('.cheque-date').focus();
            }
        }
    });
    chequeNoInput.addEventListener('change', function() {
        const val = this.value.trim().toLowerCase();
        if(val === 'cash') this.value = 'CASH';
        updateCashHighlight();
        tr.dataset.paymentType = (val && val !== 'cash') ? 'cheque' : 'cash';
        updateTotals();
    });
    
    const chequeDateInput = tr.querySelector('.cheque-date');
    chequeDateInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.shiftKey) {
            // Shift+Enter: Cheque Date -> Cheque No
            e.preventDefault();
            e.stopPropagation();
            chequeNoInput.focus();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            tr.querySelector('.amount-input').focus();
        }
    });
    
    // Amount - Enter → next row cheque-no OR Add Party if no next row
    const amountInput = tr.querySelector('.amount-input');
    // Store row ref on the element so window handler can find it
    amountInput._parentTr = tr;
    amountInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.shiftKey) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            tr.querySelector('.cheque-date').focus();
        }
    });
    amountInput.addEventListener('change', function() { updateTotals(); });
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(tr => tr.classList.remove('row-selected'));
    const row = document.getElementById(`itemRow_${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        currentRowIndex = rowIndex;
        const supplierId = row.dataset.supplierId;
        if (supplierId && supplierId != currentOutstandingSupplierId) { fetchSupplierOutstanding(supplierId); }
    }
}

function removeRow(rowIndex) {
    const row = document.getElementById(`itemRow_${rowIndex}`);
    if (row) { row.remove(); updateTotals(); }
}

function fetchSupplierOutstanding(supplierId) {
    if (!supplierId) { document.getElementById('outstandingTableBody').innerHTML = ''; return; }
    currentOutstandingSupplierId = supplierId;
    document.getElementById('outstandingTableBody').innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
    
    fetch(`<?php echo e(url('admin/supplier-payment/supplier-outstanding')); ?>/${supplierId}?page=1&per_page=50`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.outstanding) {
                displayOutstandingInvoices(data.outstanding);
                document.getElementById('outstandingTotal').textContent = parseFloat(data.total_amount || 0).toFixed(2);
            } else {
                document.getElementById('outstandingTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-muted">No outstanding invoices</td></tr>';
            }
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

// Bank Modal
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
    if (currentBankRow !== null) {
        const row = document.getElementById(`itemRow_${currentBankRow}`);
        if (row) row.querySelector('.cheque-date').focus();
    }
}

function saveBankDetails() {
    if (currentBankRow !== null) {
        rowBankDetails[currentBankRow] = {
            bankName: document.getElementById('chequeBankName').value,
            bankArea: document.getElementById('chequeBankArea').value,
            closedOn: document.getElementById('chequeClosedOn').value
        };
    }
    closeBankModal();
}

// Load Modal
let loadModalSelectedIndex = -1;

function handleLoadModalKeydown(e) {
    const rows = Array.from(document.querySelectorAll('#loadPaymentsList tr[data-trn-no]'));
    if (rows.length === 0) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (loadModalSelectedIndex < rows.length - 1) {
            loadModalSelectedIndex++;
        } else {
            loadModalSelectedIndex = 0;
        }
        highlightLoadRow(rows, loadModalSelectedIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (loadModalSelectedIndex > 0) {
            loadModalSelectedIndex--;
        } else {
            loadModalSelectedIndex = rows.length - 1;
        }
        highlightLoadRow(rows, loadModalSelectedIndex);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (loadModalSelectedIndex >= 0 && rows[loadModalSelectedIndex]) {
            rows[loadModalSelectedIndex].click();
        }
    } else if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        closeLoadModal();
    }
}

function highlightLoadRow(rows, index) {
    rows.forEach(r => r.classList.remove('load-row-selected'));
    if (index >= 0 && rows[index]) {
        rows[index].classList.add('load-row-selected');
        rows[index].scrollIntoView({ block: 'nearest' });
    }
}

function openLoadModal() {
    loadModalSelectedIndex = -1;
    document.removeEventListener('keydown', handleLoadModalKeydown, true);
    document.getElementById('loadModalBackdrop').classList.add('show');
    document.getElementById('loadModal').classList.add('show');
    
    // Load all payments automatically
    document.getElementById('loadPaymentsList').innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
    fetch('<?php echo e(url("admin/supplier-payment/get-payments")); ?>')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.payments) {
                displayPaymentsList(data.payments);
                // Auto-select first row
                setTimeout(() => {
                    const rows = Array.from(document.querySelectorAll('#loadPaymentsList tr[data-trn-no]'));
                    if (rows.length > 0) {
                        loadModalSelectedIndex = 0;
                        highlightLoadRow(rows, 0);
                    }
                }, 50);
            }
            else document.getElementById('loadPaymentsList').innerHTML = '<tr><td colspan="4" class="text-center text-muted">No payments found</td></tr>';
        })
        .catch(() => {
            document.getElementById('loadPaymentsList').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading payments</td></tr>';
        });
    
    // Add keyboard listener in CAPTURE phase (before global index shortcuts)
    document.addEventListener('keydown', handleLoadModalKeydown, true);
}

function closeLoadModal() {
    document.removeEventListener('keydown', handleLoadModalKeydown, true);
    document.getElementById('loadModalBackdrop').classList.remove('show');
    document.getElementById('loadModal').classList.remove('show');
}

function displayPaymentsList(payments) {
    const tbody = document.getElementById('loadPaymentsList');
    tbody.innerHTML = '';
    if (!payments || payments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No payments found</td></tr>';
        return;
    }
    payments.forEach(p => {
        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.dataset.trnNo = p.trn_no;
        tr.onclick = function() { document.getElementById('searchTrnNo').value = p.trn_no; closeLoadModal(); loadPayment(); };
        tr.innerHTML = `<td>${p.trn_no}</td><td>${p.payment_date ? new Date(p.payment_date).toLocaleDateString('en-GB') : '-'}</td><td>${p.bank_name || '-'}</td><td class="text-end">${(parseFloat(p.total_cash || 0) + parseFloat(p.total_cheque || 0)).toFixed(2)}</td>`;
        tbody.appendChild(tr);
    });
}

// Adjustment Modal
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
    
    // Build URL with payment_id if in modification mode
    let url = `<?php echo e(url('admin/supplier-payment/supplier-outstanding')); ?>/${supplierId}?page=1&per_page=100`;
    if (currentPayment && currentPayment.id) {
        url += `&payment_id=${currentPayment.id}`;
    }
    
    fetch(url)
        .then(r => r.json())
        .then(data => { if (data.success && data.outstanding) populateAdjustmentTable(data.outstanding); });
    
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
        // Available amount = balance + existing adjustment (what's actually available)
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
            balanceCell.innerHTML = `<span style="color: ${newBalance === 0 ? '#28a745' : '#28a745'};">₹ ${newBalance.toFixed(2)}</span>`;
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
        tr.innerHTML = `<td style="padding: 4px;">${adj.reference_no || '-'}</td><td style="padding: 4px;">${adj.reference_date ? new Date(adj.reference_date).toLocaleDateString('en-GB') : '-'}</td><td style="padding: 4px; text-align: right;">${adj.adjusted_amount.toFixed(2)}</td>`;
        tbody.appendChild(tr);
        total += adj.adjusted_amount;
    });
    document.getElementById('adjustedTotal').textContent = total.toFixed(2);
}

function closeAdjustmentModal() {
    document.getElementById('adjustmentModalBackdrop').classList.remove('show');
    document.getElementById('adjustmentModal').classList.remove('show');
}

function updatePayment() {
    if (!currentPayment) { alert('No payment loaded'); return; }
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
            items.push({ party_code: code, party_name: name, cheque_no: chequeNo, cheque_date: chequeDate || null, cheque_bank_name: bankDetails.bankName || null, cheque_bank_area: bankDetails.bankArea || null, cheque_closed_on: bankDetails.closedOn || null, amount: amount, unadjusted: unadjusted, payment_type: chequeNo ? 'cheque' : 'cash' });
        }
    });
    if (items.length === 0) { alert('Please add at least one party'); return; }
    
    const payload = { payment_date: document.getElementById('paymentDate').value, ledger: document.getElementById('ledger').value, bank_code: document.getElementById('bankSelect').value, tds_amount: 0, currency_detail: document.getElementById('currencyDetail').checked, items: items, adjustments: adjustmentData };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`<?php echo e(url('admin/supplier-payment')); ?>/${currentPayment.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(result => { if (result.success) alert('Payment updated successfully!'); else alert(result.message || 'Failed to update payment'); })
    .catch(err => { console.error('Error:', err); alert('An error occurred while updating'); });
}

function deletePayment() {
    if (!currentPayment) { alert('No payment loaded'); return; }
    if (!confirm(`Delete Payment #${currentPayment.trn_no}?`)) return;
    
    fetch(`<?php echo e(url('admin/supplier-payment')); ?>/${currentPayment.id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(result => { if (result.success) { alert(result.message); window.location.href = '<?php echo e(route("admin.supplier-payment.index")); ?>'; } else alert(result.message || 'Failed to delete'); })
    .catch(err => alert('Error deleting payment'));
}

// ═══════════════════════════════════════════════════════════════════
// GLOBAL KEYBOARD HANDLERS — window capture phase (fires before ALL
// layout/global handlers — no conflicts possible)
// ═══════════════════════════════════════════════════════════════════

/* ── Helper: which modal is open right now ── */
function _activeModal() {
    const ids = ['loadModal','supplierModal','bankModal','adjustmentModal'];
    return ids.find(id => {
        const el = document.getElementById(id);
        return el && el.classList.contains('show');
    }) || null;
}

/* ── Amount field: Enter → next row cheque-no OR Add Party ─────── */
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter' || e.shiftKey) return;
    const active = document.activeElement;
    if (!active || !active.classList.contains('amount-input')) return;
    if (_activeModal()) return;

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    updateTotals();

    const tr = active._parentTr || active.closest('tr');
    if (!tr) return;

    const allRows = Array.from(document.querySelectorAll('#itemsTableBody tr'));
    const myIdx   = allRows.indexOf(tr);
    const nextRow = allRows[myIdx + 1];

    if (nextRow) {
        const nextCheque = nextRow.querySelector('.cheque-no');
        if (nextCheque) { nextCheque.focus(); nextCheque.select(); }
        selectRow(parseInt(nextRow.dataset.rowIndex));
    } else {
        openSupplierModal();
    }
}, true);

/* ── TRN NO field: Enter → load OR focus loadPaymentBtn ── */
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    if (document.activeElement?.id !== 'searchTrnNo') return;
    if (_activeModal()) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    const val = document.getElementById('searchTrnNo').value.trim();
    if (val) {
        loadPayment();           // has TRN → fetch & load
    } else {
        // No TRN → move focus to Load Payment button
        const btn = document.getElementById('loadPaymentBtn');
        if (btn) btn.focus();
    }
}, true);

/* ── Load Payment button: Enter → open modal ── */
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    if (document.activeElement?.id !== 'loadPaymentBtn') return;
    if (_activeModal()) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    openLoadModal();
}, true);

/* ── Load Payment modal: ArrowUp/Down/Enter/Escape ── */
/* Already handled inside handleLoadModalKeydown added in document capture.
   That handler is registered/removed by openLoadModal/closeLoadModal — keep it. */

/* ── Ctrl+S → direct save ── */
window.addEventListener('keydown', function(e) {
    if (!(e.ctrlKey || e.metaKey) || e.key !== 's') return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    if (_activeModal()) return;
    const saveBtn = document.getElementById('btnUpdate');
    if (saveBtn && !saveBtn.disabled) saveBtn.click();
}, true);

/* ── Escape: close any open modal ── */
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;
    const m = _activeModal();
    if (!m) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    if (m === 'loadModal')       closeLoadModal();
    else if (m === 'supplierModal')    closeSupplierModal();
    else if (m === 'bankModal')        closeBankModal();
    else if (m === 'adjustmentModal')  closeAdjustmentModal();
}, true);

/* ── Shift+Tab: copy party ── */
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Tab' || !e.shiftKey) return;
    if (_activeModal()) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    copyParty();
}, true);

/* ── Bank modal: Enter navigates Bank→Area→ClosedOn→Save ── */
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    const modal = document.getElementById('bankModal');
    if (!modal || !modal.classList.contains('show')) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    const order = ['chequeBankName','chequeBankArea','chequeClosedOn'];
    const idx = order.indexOf(document.activeElement?.id || '');
    if (idx >= 0 && idx < order.length - 1) {
        document.getElementById(order[idx + 1]).focus();
    } else {
        saveBankDetails();
    }
}, true);


</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/supplier-payment/modification.blade.php ENDPATH**/ ?>