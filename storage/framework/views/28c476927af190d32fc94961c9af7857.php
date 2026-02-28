<?php $__env->startSection('title', 'Voucher Income (Output GST)'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .voucher-form { font-size: 11px; background: #f0f0f0; }
    .voucher-form label { font-weight: 600; font-size: 10px; margin-bottom: 0; }
    .voucher-form input, .voucher-form select { font-size: 11px; padding: 2px 5px; height: 24px; }
    .header-section { background: #e8e8e8; border: 1px solid #ccc; padding: 8px; }
    .field-label { color: #800080; font-weight: 600; font-size: 10px; }
    .field-label-green { color: #008000; font-weight: 600; font-size: 10px; }
    .table-grid { font-size: 10px; margin-bottom: 0; }
    .table-grid th { background: #008000; color: #fff; font-weight: 600; text-align: center; padding: 3px 5px; border: 1px solid #006600; }
    .table-grid td { padding: 2px 3px; border: 1px solid #ccc; background: #fff; }
    .table-grid input { font-size: 10px; padding: 1px 3px; height: 20px; border: 1px solid #ccc; width: 100%; }
    .table-hsn th { background: #800000; color: #fff; }
    .table-hsn td { background: #ffffcc; }
    .table-gst th { background: #606060; color: #fff; }
    .credit-section { background: #e0ffe0; border: 1px solid #008000; padding: 5px; }
    .credit-title { color: #008000; font-weight: bold; font-size: 11px; font-style: italic; }
    .debit-section { background: #ffe0e0; border: 1px solid #cc0000; padding: 5px; }
    .debit-title { color: #cc0000; font-weight: bold; font-size: 11px; font-style: italic; }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 2px 5px; }
    .summary-label { font-size: 10px; }
    .summary-value { font-size: 11px; color: #0000ff; font-weight: bold; text-align: right; min-width: 80px; }
    .btn-action { font-size: 10px; padding: 3px 10px; }
    .footer-section { background: #d0d0d0; padding: 8px; border: 1px solid #999; }
    .custom-modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999999; }
    .custom-modal-overlay.show { display: flex; align-items: center; justify-content: center; }
    .custom-modal { background: #fff; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); width: 90%; max-width: 700px; max-height: 80vh; display: flex; flex-direction: column; }
    .custom-modal-header { background: #008000; color: #fff; padding: 10px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .custom-modal-header h6 { margin: 0; font-size: 14px; }
    .custom-modal-close { background: none; border: none; color: #fff; font-size: 20px; cursor: pointer; }
    .custom-modal-body { padding: 15px; overflow-y: auto; flex: 1; }
    .custom-modal-footer { padding: 10px 15px; border-top: 1px solid #ddd; display: flex; justify-content: flex-end; gap: 10px; }
    /* Keyboard-active row in account modal */
    #accountListBody tr.kb-active { background: #008000 !important; color: #fff !important; }
    #accountListBody tr.kb-active td { background: #008000 !important; color: #fff !important; }
    /* Custom Customer Dropdown */
    .customer-dropdown-wrap { position: relative; display: inline-block; width: 150px; }
    .customer-dropdown-wrap input { width: 100%; }
    .customer-dropdown-list { display: none; position: absolute; top: 100%; left: 0; width: 280px; max-height: 200px; overflow-y: auto; background: #fff; border: 1px solid #008000; border-radius: 0 0 6px 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 99999; }
    .customer-dropdown-list.show { display: block; }
    .customer-dropdown-item { padding: 4px 8px; cursor: pointer; font-size: 11px; border-bottom: 1px solid #eee; }
    .customer-dropdown-item:hover { background: #e0ffe0; }
    .customer-dropdown-item.kb-active { background: #008000 !important; color: #fff !important; }
    /* Custom HSN Dropdown */
    .hsn-dropdown-list { display: none; position: fixed; width: 320px; max-height: 220px; overflow-y: auto; background: #fff; border: 1px solid #800000; border-radius: 0 0 6px 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.25); z-index: 99999; }
    .hsn-dropdown-list.show { display: block; }
    .hsn-dropdown-item { padding: 4px 8px; cursor: pointer; font-size: 10px; border-bottom: 1px solid #eee; }
    .hsn-dropdown-item:hover { background: #ffffcc; }
    .hsn-dropdown-item.kb-active { background: #800000 !important; color: #fff !important; }
    .hsn-dropdown-item .hsn-code-label { font-weight: bold; }
    .hsn-dropdown-item .hsn-name-label { color: #666; margin-left: 6px; }
    .hsn-dropdown-item .hsn-gst-label { color: #008000; font-weight: bold; margin-left: auto; white-space: nowrap; }
    .hsn-dropdown-item.kb-active .hsn-name-label { color: #ffc; }
    .hsn-dropdown-item.kb-active .hsn-gst-label { color: #afa; }
    .hsn-dropdown-item { display: flex; align-items: center; }
    /* GST Rate Selector */
    .gst-rate-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 999999; }
    .gst-rate-overlay.show { display: flex; align-items: center; justify-content: center; }
    .gst-rate-box { background: #fff; border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.3); padding: 15px; min-width: 250px; }
    .gst-rate-box h6 { margin: 0 0 10px; font-size: 12px; color: #800000; font-weight: bold; }
    .gst-rate-item { padding: 6px 10px; cursor: pointer; font-size: 11px; border: 1px solid #eee; margin-bottom: 4px; border-radius: 4px; }
    .gst-rate-item:hover { background: #ffffcc; }
    .gst-rate-item.kb-active { background: #800000 !important; color: #fff !important; border-color: #800000; }
    /* Debit Account Dropdown */
    .debit-acct-wrap { position: relative; width: 100%; }
    .debit-acct-wrap input { width: 100%; }
    .debit-acct-list { display: none; position: absolute; bottom: 100%; left: 0; width: 100%; max-height: 180px; overflow-y: auto; background: #fff; border: 1px solid #cc0000; border-radius: 6px 6px 0 0; box-shadow: 0 -4px 12px rgba(0,0,0,0.2); z-index: 99999; }
    .debit-acct-list.show { display: block; }
    .debit-acct-item { padding: 4px 8px; cursor: pointer; font-size: 11px; border-bottom: 1px solid #eee; }
    .debit-acct-item:hover { background: #ffe0e0; }
    .debit-acct-item.kb-active { background: #cc0000 !important; color: #fff !important; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body voucher-form p-0">
        <form id="voucherForm" method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>
            <div class="header-section">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <span class="field-label">Voucher</span>
                        <input type="date" class="form-control" name="voucher_date" id="voucherDate" value="<?php echo e(date('Y-m-d')); ?>" style="width: 120px;">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">Voucher No :</span>
                        <input type="text" class="form-control bg-light" id="voucherNoDisplay" value="<?php echo e($nextVoucherNo); ?>" readonly style="width: 60px;">
                        <input type="hidden" name="voucher_no" id="voucherNo" value="<?php echo e($nextVoucherNo); ?>">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">L(ocal) / I(nter</span>
                        <input type="text" class="form-control" name="local_inter" id="localInter" value="L" maxlength="1" style="width: 30px; text-transform: uppercase;">
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-12">
                        <span class="field-label">Description :</span>
                        <input type="text" class="form-control" name="description" id="description" style="width: 100%;">
                    </div>
                </div>
            </div>

            <div class="row g-0">
                <div class="col-md-8 p-2">
                    <div class="table-responsive mb-2" style="max-height: 100px; overflow-y: auto;">
                        <table class="table table-grid mb-0">
                            <thead><tr><th style="width: 100px;">Code</th><th>Name</th></tr></thead>
                            <tbody id="accountsTableBody"></tbody>
                        </table>
                    </div>
                    <div class="row g-1 mb-2 p-1" style="background: #f8f8f8; border: 1px solid #ddd;">
                        <div class="col-auto">
                            <span class="field-label-green">Customer</span>
                            <div class="customer-dropdown-wrap">
                                <input type="text" class="form-control" id="customerNameInput" placeholder="Search..." autocomplete="off">
                                <input type="hidden" name="customer_id" id="customerId">
                                <input type="hidden" name="customer_name_input" id="customerNameHidden">
                                <div class="customer-dropdown-list" id="customerDropdownList"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-action" onclick="openAccountModal()">Add Account (F9)</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="deleteAccount()">Delete Account</button>
                        </div>
                    </div>
                    <div class="row g-1 mb-2">
                        <div class="col-auto"><span class="field-label-green">GST No.</span><input type="text" class="form-control bg-light" name="gst_no" id="gstNo" readonly style="width: 150px;"></div>
                        <div class="col-auto"><span class="field-label-green">PAN No.</span><input type="text" class="form-control bg-light" name="pan_no" id="panNo" readonly style="width: 120px;"></div>
                    </div>
                    <div class="row g-1 mb-2">
                        <div class="col-auto"><span class="field-label-green">City</span><input type="text" class="form-control bg-light" name="city" id="city" readonly style="width: 120px;"></div>
                        <div class="col-auto"><span class="field-label-green">PIN</span><input type="text" class="form-control bg-light" name="pin" id="pin" readonly style="width: 80px;"></div>
                    </div>

                    <div class="table-responsive mb-2" style="max-height: 150px; overflow-y: auto;">
                        <table class="table table-grid table-hsn mb-0">
                            <thead><tr><th style="width: 80px;">HSN Code</th><th style="width: 80px;">Amount</th><th style="width: 50px;">GST%</th><th style="width: 50px;">CGST%</th><th style="width: 70px;">Amount</th><th style="width: 50px;">SGST%</th><th style="width: 70px;">Amount</th></tr></thead>
                            <tbody id="hsnTableBody"></tbody>
                        </table>
                    </div>
                    <div class="row g-1 mb-2 p-1" style="background: #e0e0e0;">
                        <div class="col-auto"><span class="field-label">Gross</span><input type="text" class="form-control bg-light text-end" id="grossDisplay" readonly style="width: 100px;"></div>
                        <div class="col-auto ms-auto"><button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="deleteHsnRow()">Delete Row</button></div>
                    </div>
                    <div class="row g-1 mb-1"><div class="col-auto"><span class="field-label-green">Total GST</span><span class="summary-value" id="totalGstDisplay">0.00</span></div></div>
                    <div class="row g-1 mb-2"><div class="col-auto"><span class="field-label-green">Net Amt.</span><span class="summary-value" id="netAmtDisplay">0.00</span></div></div>
                    <div class="table-responsive" style="max-height: 120px; overflow-y: auto;">
                        <table class="table table-grid table-gst mb-0">
                            <thead><tr><th style="width: 60px;">CGST(%)</th><th style="width: 60px;">SGST(%)</th><th style="width: 80px;">Total Amt.</th><th style="width: 80px;">CGST Amt.</th><th style="width: 80px;">SGST Amt.</th></tr></thead>
                            <tbody id="gstSummaryBody"></tbody>
                            <tfoot><tr style="background: #d0d0d0;"><td colspan="3" class="text-end"><strong>CGSTAMT</strong></td><td class="text-end"><span id="totalCgstAmt">0.00</span></td><td class="text-end"><strong>SGSTAMT</strong> <span id="totalSgstAmt">0.00</span></td></tr></tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-4 p-2">
                    <div class="credit-section mb-2">
                        <div class="credit-title mb-2">Credit</div>
                        <div class="summary-row"><span class="summary-label">Amount</span><span class="summary-value" id="creditAmount">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">Total GST</span><span class="summary-value" id="creditTotalGst">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">Net Amt.</span><span class="summary-value" id="creditNetAmt">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">R/Off</span><span class="summary-value" id="creditRoff">0.00</span></div>
                        <div class="summary-row" style="border-top: 1px solid #008000; margin-top: 5px; padding-top: 5px;"><span class="summary-label"><strong>Total Db. Amt.</strong></span><span class="summary-value" id="totalCreditAmt" style="color: #008000;">0.00</span></div>
                    </div>
                    <div class="debit-section">
                        <div class="debit-title mb-2">Debit</div>
                        <div class="row g-1 mb-1">
                            <div class="col-auto"><span class="field-label">TDS @</span><input type="number" class="form-control text-end" name="tds_percent" id="tdsPercent" value="0" step="0.01" style="width: 50px;" onchange="calculateTds()"></div>
                            <div class="col-auto"><span class="field-label">%</span><input type="text" class="form-control bg-light text-end" id="tdsAmount" readonly style="width: 80px;"></div>
                            <div class="col-auto"><span class="field-label">TDS</span></div>
                        </div>
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label">Account</span>
                            <div class="debit-acct-wrap">
                                <input type="text" class="form-control" id="debitAccountInput" placeholder="Search account..." autocomplete="off">
                                <input type="hidden" name="debit_account_id" id="debitAccountId">
                                <input type="hidden" name="debit_account_type" id="debitAccountType">
                                <input type="hidden" name="debit_account_name" id="debitAccountName">
                                <div class="debit-acct-list" id="debitAcctList"></div>
                            </div>
                        </div></div>
                        <div class="summary-row" style="border-top: 1px solid #cc0000; margin-top: 5px; padding-top: 5px;"><span class="summary-label"><strong>Total Cr</strong></span><span class="summary-value" id="totalDebitAmt" style="color: #cc0000;">0.00</span></div>
                    </div>
                    <div class="mt-2 p-2" style="background: #f0f0f0; border: 1px solid #ccc;">
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label-green">Customer</span><input type="text" class="form-control bg-light" id="customerDisplay" readonly></div></div>
                        <div class="row g-1"><div class="col-12"><span class="field-label-green">Address</span><textarea class="form-control bg-light" id="addressDisplay" rows="2" readonly></textarea></div></div>
                    </div>
                </div>
            </div>
            <div class="footer-section d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger btn-action" onclick="deleteVoucher()">Delete Voucher</button>
                <button type="button" class="btn btn-secondary btn-action" onclick="printVoucher()">Print</button>
                <button type="button" class="btn btn-warning btn-action" onclick="reverseVoucher()">Reverse</button>
                <button type="button" class="btn btn-success btn-action" onclick="saveVoucher()">Save</button>
                <a href="<?php echo e(route('admin.voucher-income.index')); ?>" class="btn btn-secondary btn-action">Exit</a>
            </div>
        </form>
    </div>
</div>

<datalist id="hsnDatalist">
    <?php $__currentLoopData = $hsnCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hsn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($hsn['hsn_code']); ?>"><?php echo e($hsn['name']); ?> (<?php echo e($hsn['total_gst_percent'] ?: ($hsn['cgst_percent'] + $hsn['sgst_percent'])); ?>%)</option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</datalist>

<div class="hsn-dropdown-list" id="hsnDropdownList"></div>
<div class="gst-rate-overlay" id="gstRateOverlay">
    <div class="gst-rate-box">
        <h6 id="gstRateTitle">Select GST Rate</h6>
        <div id="gstRateList"></div>
    </div>
</div>

<div class="custom-modal-overlay" id="accountModalOverlay">
    <div class="custom-modal">
        <div class="custom-modal-header"><h6>Select Account</h6><button type="button" class="custom-modal-close" onclick="closeAccountModal()">&times;</button></div>
        <div class="custom-modal-body">
            <div class="row mb-2">
                <div class="col-md-6"><input type="text" class="form-control form-control-sm" id="accountSearch" placeholder="Search..." oninput="filterAccountList()"></div>
                <div class="col-md-6"><select class="form-select form-select-sm" id="accountTypeFilter" onchange="loadAccountList()"><option value="SL">Sales Ledger</option><option value="GL">General Ledger</option></select></div>
            </div>
            <div style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-hover" id="accountListTable"><thead><tr><th>Code</th><th>Name</th></tr></thead><tbody id="accountListBody"></tbody></table>
            </div>
        </div>
        <div class="custom-modal-footer"><button type="button" class="btn btn-secondary btn-sm" onclick="closeAccountModal()">Cancel</button><button type="button" class="btn btn-primary btn-sm" onclick="selectAccount()">Select</button></div>
    </div>
</div>

<script>
const salesLedgers = <?php echo json_encode($salesLedgers, 15, 512) ?>;
const generalLedgers = <?php echo json_encode($generalLedgers, 15, 512) ?>;
const cashBankBooks = <?php echo json_encode($cashBankBooks, 15, 512) ?>;
const hsnCodes = <?php echo json_encode($hsnCodes, 15, 512) ?>;
const customers = <?php echo json_encode($customers, 15, 512) ?>;

let accountRowCount = 0, hsnRowCount = 0, selectedAccountRow = null, selectedHsnRow = null;
let kbFocusIndex = -1;  // keyboard cursor inside account modal
let _currentAccountTargetRow = null; // which row will receive the next account selection

document.addEventListener('DOMContentLoaded', function() {
    for (let i = 0; i < 3; i++) addAccountRow();
    for (let i = 0; i < 5; i++) addHsnRow();

    /* ── Customer dropdown: filter on typing ── */
    document.getElementById('customerNameInput').addEventListener('input', function() {
        filterCustomerDropdown(this.value);
        openCustomerDropdown();
    });
    document.getElementById('customerNameInput').addEventListener('focus', function() {
        filterCustomerDropdown(this.value);
        openCustomerDropdown();
    });
    
    loadDebitAccounts();

    /* ══════════════════════════════════════════════════════════
       KEYBOARD NAVIGATION FLOW
       voucherDate → localInter → description → Add Account (F9)
    ══════════════════════════════════════════════════════════ */

    /* ── 1. voucherDate: Enter → localInter ──
       Capture-phase on window so it fires BEFORE any browser default
       (date input's native Enter moves focus to next tabindex). */
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        var vd = document.getElementById('voucherDate');
        if (document.activeElement !== vd) return;
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        vd.blur();
        setTimeout(function() {
            var li = document.getElementById('localInter');
            li.focus();
            li.select();
        }, 80);
    }, true);

    /* ── 2. localInter: keyboard handling ──
       - Only allow typing L or I (auto-uppercase)
       - Up/Down arrow toggles between L and I
       - Enter → move to description */
    var localInterEl = document.getElementById('localInter');
    localInterEl.addEventListener('keydown', function(e) {
        // Enter → go to description
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            document.getElementById('description').focus();
            return;
        }
        // Up/Down → toggle L ↔ I
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            e.preventDefault();
            this.value = (this.value.toUpperCase() === 'L') ? 'I' : 'L';
            return;
        }
        // Allow only L or I typing
        if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
            e.preventDefault();
            var ch = e.key.toUpperCase();
            if (ch === 'L' || ch === 'I') {
                this.value = ch;
            }
            return;
        }
        // Allow Tab, Escape, Backspace, Delete, etc.
    });

    /* ── 3. description: Enter → trigger Add Account button ──
       Capture-phase on window so it fires BEFORE any global handler
       or browser default that moves focus to the Code column. */
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        var desc = document.getElementById('description');
        if (document.activeElement !== desc) return;
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        desc.blur();
        setTimeout(function() {
            openAccountModal();
        }, 80);
    }, true);

    /* ── 4. Account Code/Name Enter → open modal for that row ──
       Capture-phase on window so it fires BEFORE transaction-shortcuts
       global Enter handler. Handles both .account-code and .account-name */
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        if (_isAccountModalOpen()) return; // modal handler will handle it
        var el = document.activeElement;
        if (!el) return;
        var isCode = el.classList.contains('account-code');
        var isName = el.classList.contains('account-name');
        if (!isCode && !isName) return;
        var row = el.closest('tr');
        if (!row || !row.closest('#accountsTableBody')) return;

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        // Open modal for THIS row (will fill this row)
        selectAccountRowEl(row);
        openAccountModal(row);
    }, true);

    /* ── Global shortcuts (F9, Escape) ── */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F9') { e.preventDefault(); openAccountModal(); }
        if (e.key === 'Escape') {
            if (_isDebitAcctOpen()) { closeDebitAcctDropdown(); return; }
            if (_isHsnDropdownOpen()) { closeHsnDropdown(); return; }
            if (_isCustomerDropdownOpen()) { closeCustomerDropdown(); return; }
            closeAccountModal();
        }
    });

    /* ── 5. Ctrl+Enter → smart navigation ──
       If Customer not selected → open Customer dropdown
       If Customer already selected → focus TDS @ in Debit section */
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            if (_isAccountModalOpen() || _isGstRateOpen()) return;
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            var customerId = document.getElementById('customerId').value;
            if (!customerId) {
                // Customer not selected → open Customer dropdown
                var inp = document.getElementById('customerNameInput');
                inp.value = '';
                inp.focus();
                filterCustomerDropdown('');
                openCustomerDropdown();
            } else {
                // Customer selected → go to TDS @
                var tdsEl = document.getElementById('tdsPercent');
                tdsEl.focus();
                tdsEl.select();
            }
        }
    }, true);

    /* ── 5b. Enter navigation: TDS@ → %TDS → Debit Account dropdown ── */
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' || e.ctrlKey) return;
        if (_isAccountModalOpen() || _isCustomerDropdownOpen() || _isHsnDropdownOpen() || _isGstRateOpen() || _isDebitAcctOpen()) return;
        var el = document.activeElement;
        if (!el) return;
        if (el.id === 'tdsPercent') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            calculateTds();
            document.getElementById('tdsAmount').focus();
            return;
        }
        if (el.id === 'tdsAmount') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var inp = document.getElementById('debitAccountInput');
            inp.value = '';
            inp.focus();
            openDebitAcctDropdown();
            return;
        }
    }, true);

    /* ── 5c. Debit Account dropdown keyboard handler ── */
    window.addEventListener('keydown', function(e) {
        if (!_isDebitAcctOpen()) return;
        var el = document.activeElement;
        if (!el || el.id !== 'debitAccountInput') return;
        var items = _getVisibleDebitAcctItems();

        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!items.length) return;
            _debitAcctKbIdx = Math.min(_debitAcctKbIdx + 1, items.length - 1);
            _setDebitAcctHighlight(_debitAcctKbIdx);
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!items.length) return;
            _debitAcctKbIdx = Math.max(_debitAcctKbIdx - 1, 0);
            _setDebitAcctHighlight(_debitAcctKbIdx);
            return;
        }
        if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (_debitAcctKbIdx < 0 && items.length > 0) { _debitAcctKbIdx = 0; _setDebitAcctHighlight(0); }
            if (_debitAcctKbIdx >= 0 && _debitAcctKbIdx < items.length) {
                selectDebitAcctItem(items[_debitAcctKbIdx]);
            }
            return;
        }
        if (e.key === 'Escape') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            closeDebitAcctDropdown();
            return;
        }
        _debitAcctKbIdx = -1;
    }, true);

    /* Debit Account input filter */
    document.getElementById('debitAccountInput').addEventListener('input', function() {
        filterDebitAcctDropdown(this.value);
        if (!_isDebitAcctOpen()) openDebitAcctDropdown();
    });
    document.getElementById('debitAccountInput').addEventListener('focus', function() {
        filterDebitAcctDropdown(this.value);
        openDebitAcctDropdown();
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.debit-acct-wrap')) closeDebitAcctDropdown();
    });

    /* ── Ctrl+S → Save transaction ── */
    window.addEventListener('keydown', function(e) {
        if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            saveVoucher();
        }
    }, true);

    /* ── 6. Customer dropdown keyboard handler ──
       Capture-phase so arrow keys/enter work inside dropdown */
    window.addEventListener('keydown', function(e) {
        if (!_isCustomerDropdownOpen()) return;
        var el = document.activeElement;
        if (!el || el.id !== 'customerNameInput') return;

        var items = _getVisibleCustomerItems();

        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!items.length) return;
            _custKbIdx = Math.min(_custKbIdx + 1, items.length - 1);
            _setCustKbHighlight(_custKbIdx);
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!items.length) return;
            _custKbIdx = Math.max(_custKbIdx - 1, 0);
            _setCustKbHighlight(_custKbIdx);
            return;
        }
        if (e.key === 'Enter' && !e.ctrlKey) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (_custKbIdx < 0 && items.length > 0) {
                _custKbIdx = 0;
                _setCustKbHighlight(0);
            }
            if (_custKbIdx >= 0 && _custKbIdx < items.length) {
                selectCustomerItem(items[_custKbIdx]);
            }
            return;
        }
        if (e.key === 'Escape') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            closeCustomerDropdown();
            return;
        }
        if (e.key === 'Tab') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            closeCustomerDropdown();
            return;
        }
        // reset index on typing
        _custKbIdx = -1;
    }, true);

    /* Click outside to close customer dropdown */
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.customer-dropdown-wrap')) {
            closeCustomerDropdown();
        }
    });

    /* ── 8. HSN Code Enter → open HSN dropdown ──
       Capture-phase so it fires before global handlers */
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        if (_isAccountModalOpen() || _isCustomerDropdownOpen() || _isGstRateOpen()) return;

        // If HSN dropdown is open, handle Enter inside it
        if (_isHsnDropdownOpen()) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var items = _getVisibleHsnItems();
            if (_hsnKbIdx < 0 && items.length > 0) { _hsnKbIdx = 0; _setHsnKbHighlight(0); }
            if (_hsnKbIdx >= 0 && _hsnKbIdx < items.length) {
                selectHsnItem(items[_hsnKbIdx]);
            }
            return;
        }

        // If focused on hsn-code field, open dropdown
        var el = document.activeElement;
        if (!el || !el.classList.contains('hsn-code')) return;
        var row = el.closest('tr');
        if (!row || !row.closest('#hsnTableBody')) return;

        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        openHsnDropdown(el);
    }, true);

    /* ── 9. HSN dropdown keyboard navigation ──
       Capture-phase: ArrowDown/Up navigate, Escape closes */
    window.addEventListener('keydown', function(e) {
        if (!_isHsnDropdownOpen()) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var items = _getVisibleHsnItems();
            if (!items.length) return;
            _hsnKbIdx = Math.min(_hsnKbIdx + 1, items.length - 1);
            _setHsnKbHighlight(_hsnKbIdx);
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var items = _getVisibleHsnItems();
            if (!items.length) return;
            _hsnKbIdx = Math.max(_hsnKbIdx - 1, 0);
            _setHsnKbHighlight(_hsnKbIdx);
            return;
        }
        if (e.key === 'Escape') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            closeHsnDropdown();
            return;
        }
        if (e.key === 'Tab') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            closeHsnDropdown();
            return;
        }
    }, true);

    /* ── HSN code input event → filter dropdown as user types ── */
    document.getElementById('hsnTableBody').addEventListener('input', function(e) {
        if (!e.target.classList.contains('hsn-code')) return;
        if (!_isHsnDropdownOpen()) {
            openHsnDropdown(e.target);
        } else {
            filterHsnDropdown(e.target.value);
        }
    });

    /* Click outside to close HSN dropdown */
    document.addEventListener('click', function(e) {
        if (_isHsnDropdownOpen() && !e.target.classList.contains('hsn-code') && !e.target.closest('#hsnDropdownList')) {
            closeHsnDropdown();
        }
    });

    /* ── 7. Auto-focus voucherDate on page load ──
       Use setTimeout to ensure it fires AFTER any Select2 or
       other scripts that might steal focus. */
    setTimeout(function() {
        var vd = document.getElementById('voucherDate');
        vd.focus();
    }, 300);
});

/* ══════════════════════════════════════════════════════════
   CUSTOMER CUSTOM DROPDOWN
══════════════════════════════════════════════════════════ */
let _custKbIdx = -1;

function _isCustomerDropdownOpen() {
    return document.getElementById('customerDropdownList').classList.contains('show');
}

function _getVisibleCustomerItems() {
    return Array.from(document.querySelectorAll('#customerDropdownList .customer-dropdown-item'))
                .filter(function(el) { return el.style.display !== 'none'; });
}

function _setCustKbHighlight(idx) {
    var items = _getVisibleCustomerItems();
    document.querySelectorAll('#customerDropdownList .customer-dropdown-item').forEach(function(el) {
        el.classList.remove('kb-active');
    });
    if (idx < 0 || idx >= items.length) return;
    _custKbIdx = idx;
    items[idx].classList.add('kb-active');
    items[idx].scrollIntoView({ block: 'nearest' });
}

function openCustomerDropdown() {
    var list = document.getElementById('customerDropdownList');
    if (!list.classList.contains('show')) {
        buildCustomerDropdown();
        filterCustomerDropdown(document.getElementById('customerNameInput').value);
        list.classList.add('show');
    }
    _custKbIdx = -1;
}

function closeCustomerDropdown() {
    document.getElementById('customerDropdownList').classList.remove('show');
    _custKbIdx = -1;
}

function buildCustomerDropdown() {
    var list = document.getElementById('customerDropdownList');
    list.innerHTML = '';
    customers.forEach(function(c) {
        var item = document.createElement('div');
        item.className = 'customer-dropdown-item';
        item.dataset.id = c.id;
        item.dataset.name = c.name;
        item.dataset.gst = c.gst_number || '';
        item.dataset.pan = c.pan_number || '';
        item.dataset.city = c.city || '';
        item.dataset.address = c.address || '';
        item.textContent = c.name;
        item.onclick = function() { selectCustomerItem(item); };
        list.appendChild(item);
    });
}

function filterCustomerDropdown(search) {
    search = (search || '').toLowerCase();
    document.querySelectorAll('#customerDropdownList .customer-dropdown-item').forEach(function(el) {
        el.style.display = el.dataset.name.toLowerCase().includes(search) ? '' : 'none';
    });
    _custKbIdx = -1;
    document.querySelectorAll('#customerDropdownList .customer-dropdown-item').forEach(function(el) {
        el.classList.remove('kb-active');
    });
}

function selectCustomerItem(item) {
    document.getElementById('customerNameInput').value = item.dataset.name;
    document.getElementById('customerNameHidden').value = item.dataset.name;
    document.getElementById('customerId').value = item.dataset.id;
    document.getElementById('gstNo').value = item.dataset.gst;
    document.getElementById('panNo').value = item.dataset.pan;
    document.getElementById('city').value = item.dataset.city;
    document.getElementById('customerDisplay').value = item.dataset.name;
    document.getElementById('addressDisplay').value = item.dataset.address;
    closeCustomerDropdown();
    /* Focus HSN Code field (first row) after customer selection */
    setTimeout(function() {
        var firstHsn = document.querySelector('#hsnTableBody tr:first-child .hsn-code');
        if (firstHsn) firstHsn.focus();
    }, 80);
}

/* ══════════════════════════════════════════════════════════
   HSN CODE CUSTOM DROPDOWN
══════════════════════════════════════════════════════════ */
let _hsnKbIdx = -1;
let _hsnActiveInput = null;

function _isHsnDropdownOpen() {
    return document.getElementById('hsnDropdownList').classList.contains('show');
}

function _getVisibleHsnItems() {
    return Array.from(document.querySelectorAll('#hsnDropdownList .hsn-dropdown-item'))
                .filter(function(el) { return el.style.display !== 'none'; });
}

function _setHsnKbHighlight(idx) {
    var items = _getVisibleHsnItems();
    document.querySelectorAll('#hsnDropdownList .hsn-dropdown-item').forEach(function(el) {
        el.classList.remove('kb-active');
    });
    if (idx < 0 || idx >= items.length) return;
    _hsnKbIdx = idx;
    items[idx].classList.add('kb-active');
    items[idx].scrollIntoView({ block: 'nearest' });
}

function buildHsnDropdown() {
    var list = document.getElementById('hsnDropdownList');
    list.innerHTML = '';
    hsnCodes.forEach(function(h, idx) {
        if (!h.hsn_code) return;
        var code = h.hsn_code.toString();
        var gst = parseFloat(h.total_gst_percent) || (parseFloat(h.cgst_percent || 0) + parseFloat(h.sgst_percent || 0));
        var item = document.createElement('div');
        item.className = 'hsn-dropdown-item';
        item.dataset.code = code;
        item.dataset.name = h.name || '';
        item.dataset.idx = idx;  // index into hsnCodes array
        item.innerHTML = '<span class="hsn-code-label">' + code + '</span>' +
                         '<span class="hsn-name-label">' + (h.name || '') + '</span>' +
                         '<span class="hsn-gst-label">' + gst + '%</span>';
        item.onclick = function() { selectHsnItem(item); };
        list.appendChild(item);
    });
}

function openHsnDropdown(inputEl) {
    _hsnActiveInput = inputEl;
    var list = document.getElementById('hsnDropdownList');
    buildHsnDropdown();
    filterHsnDropdown(inputEl.value);
    var rect = inputEl.getBoundingClientRect();
    list.style.left = rect.left + 'px';
    list.style.top = (rect.bottom) + 'px';
    list.classList.add('show');
    _hsnKbIdx = -1;
}

function closeHsnDropdown() {
    document.getElementById('hsnDropdownList').classList.remove('show');
    _hsnKbIdx = -1;
    _hsnActiveInput = null;
}

function filterHsnDropdown(search) {
    search = (search || '').toLowerCase();
    document.querySelectorAll('#hsnDropdownList .hsn-dropdown-item').forEach(function(el) {
        var code = el.dataset.code.toLowerCase();
        var name = el.dataset.name.toLowerCase();
        el.style.display = (code.includes(search) || name.includes(search)) ? '' : 'none';
    });
    _hsnKbIdx = -1;
    document.querySelectorAll('#hsnDropdownList .hsn-dropdown-item').forEach(function(el) {
        el.classList.remove('kb-active');
    });
}

function selectHsnItem(item) {
    if (_hsnActiveInput) {
        _hsnActiveInput.value = item.dataset.code;
        var row = _hsnActiveInput.closest('tr');
        var hsnIdx = parseInt(item.dataset.idx);
        var hsn = hsnCodes[hsnIdx];
        closeHsnDropdown();
        // Directly apply the selected HSN+GST combo (no need for rate picker)
        applyHsnToRow(row, hsn);
    } else {
        closeHsnDropdown();
    }
}

function addAccountRow(data = null) {
    accountRowCount++;
    const tbody = document.getElementById('accountsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', accountRowCount);
    row.innerHTML = `<td><input type="text" class="account-code" name="accounts[${accountRowCount}][account_code]" value="${data?.account_code || ''}" readonly onclick="selectAccountRowEl(this.closest('tr'))"></td>
        <td><input type="text" class="account-name" name="accounts[${accountRowCount}][account_name]" value="${data?.account_name || ''}" readonly onclick="selectAccountRowEl(this.closest('tr')); openAccountModal(this.closest('tr'));">
            <input type="hidden" class="account-type" name="accounts[${accountRowCount}][account_type]" value="${data?.account_type || ''}">
            <input type="hidden" class="account-id" name="accounts[${accountRowCount}][account_id]" value="${data?.account_id || ''}"></td>`;
    row.onclick = () => selectAccountRowEl(row);
    tbody.appendChild(row);
    wireAccountRowKeys(row);
}

function wireAccountRowKeys(row) {
    var nameEl = row.querySelector('.account-name');

    /* Focus handler: click on name → select this row */
    nameEl.addEventListener('focus', function() {
        if (this._noModal) { this._noModal = false; return; }
        selectAccountRowEl(row);
    });
}

function selectAccountRowEl(row) {
    document.querySelectorAll('#accountsTableBody tr').forEach(r => r.classList.remove('table-primary'));
    row.classList.add('table-primary');
    selectedAccountRow = row;
}

function addHsnRow(data = null) {
    hsnRowCount++;
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', hsnRowCount);
    row.innerHTML = `<td><input type="text" class="hsn-code" name="items[${hsnRowCount}][hsn_code]" value="${data?.hsn_code || ''}" onclick="selectHsnRowEl(this.closest('tr'))" autocomplete="off"></td>
        <td><input type="number" class="hsn-amount text-end" name="items[${hsnRowCount}][amount]" value="${data?.amount || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr')); checkAddHsnRow();" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-gst text-end" name="items[${hsnRowCount}][gst_percent]" value="${data?.gst_percent || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr'))" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-cgst-pct text-end bg-light" name="items[${hsnRowCount}][cgst_percent]" value="${data?.cgst_percent || ''}" readonly></td>
        <td><input type="number" class="hsn-cgst-amt text-end bg-light" name="items[${hsnRowCount}][cgst_amount]" value="${data?.cgst_amount || ''}" readonly></td>
        <td><input type="number" class="hsn-sgst-pct text-end bg-light" name="items[${hsnRowCount}][sgst_percent]" value="${data?.sgst_percent || ''}" readonly></td>
        <td><input type="number" class="hsn-sgst-amt text-end bg-light" name="items[${hsnRowCount}][sgst_amount]" value="${data?.sgst_amount || ''}" readonly></td>`;
    row.onclick = () => selectHsnRowEl(row);
    tbody.appendChild(row);
}

function selectHsnRowEl(row) {
    document.querySelectorAll('#hsnTableBody tr').forEach(r => r.classList.remove('table-warning'));
    row.classList.add('table-warning');
    selectedHsnRow = row;
}

function checkAddHsnRow() {
    const rows = document.querySelectorAll('#hsnTableBody tr');
    const lastRow = rows[rows.length - 1];
    if (lastRow) {
        const amount = lastRow.querySelector('.hsn-amount')?.value;
        if (amount && parseFloat(amount) > 0) addHsnRow();
    }
}

function fetchHsnDetails(input) {
    const hsnCode = input.value.trim();
    if (!hsnCode) return;
    const matchingHsns = hsnCodes.filter(h => h.hsn_code && h.hsn_code.toString() === hsnCode.toString());
    if (matchingHsns.length === 0) return;
    const uniqueRates = [];
    const seenRates = new Set();
    matchingHsns.forEach(hsn => {
        const gst = parseFloat(hsn.total_gst_percent) || 0;
        if (!seenRates.has(gst)) { seenRates.add(gst); uniqueRates.push(hsn); }
    });
    uniqueRates.sort((a, b) => {
        const gstA = parseFloat(a.total_gst_percent) || 0;
        const gstB = parseFloat(b.total_gst_percent) || 0;
        if (gstA === 0 && gstB !== 0) return 1;
        if (gstA !== 0 && gstB === 0) return -1;
        return gstA - gstB;
    });
    const row = input.closest('tr');
    if (uniqueRates.length > 1) showGstRateSelector(row, uniqueRates, hsnCode);
    else applyHsnToRow(row, uniqueRates[0]);
}

function showGstRateSelector(row, hsnOptions, hsnCode) {
    _gstRateRow = row;
    _gstRateOptions = hsnOptions;
    _gstRateKbIdx = -1;
    var list = document.getElementById('gstRateList');
    document.getElementById('gstRateTitle').textContent = 'HSN ' + hsnCode + ' - Select GST Rate';
    list.innerHTML = '';
    hsnOptions.forEach(function(hsn, idx) {
        var item = document.createElement('div');
        item.className = 'gst-rate-item';
        item.textContent = (idx + 1) + '. ' + (parseFloat(hsn.total_gst_percent) || 0) + '% GST';
        item.dataset.idx = idx;
        item.onclick = function() { _selectGstRate(idx); };
        list.appendChild(item);
    });
    document.getElementById('gstRateOverlay').classList.add('show');
    /* Register capture-phase handler */
    window.removeEventListener('keydown', _handleGstRateKey, true);
    window.addEventListener('keydown', _handleGstRateKey, true);
}

var _gstRateRow = null, _gstRateOptions = [], _gstRateKbIdx = -1;

function _isGstRateOpen() {
    return document.getElementById('gstRateOverlay').classList.contains('show');
}

function _handleGstRateKey(e) {
    if (!_isGstRateOpen()) return;
    var items = document.querySelectorAll('#gstRateList .gst-rate-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _gstRateKbIdx = Math.min(_gstRateKbIdx + 1, items.length - 1);
        _highlightGstRate(_gstRateKbIdx);
        return;
    }
    if (e.key === 'ArrowUp') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _gstRateKbIdx = Math.max(_gstRateKbIdx - 1, 0);
        _highlightGstRate(_gstRateKbIdx);
        return;
    }
    if (e.key === 'Enter') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (_gstRateKbIdx < 0 && items.length > 0) { _gstRateKbIdx = 0; _highlightGstRate(0); }
        _selectGstRate(_gstRateKbIdx);
        return;
    }
    if (e.key === 'Escape') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _closeGstRate();
        return;
    }
    // Number keys 1-9 for quick selection
    if (e.key >= '1' && e.key <= '9') {
        var idx = parseInt(e.key) - 1;
        if (idx < _gstRateOptions.length) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            _selectGstRate(idx);
        }
        return;
    }
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
}

function _highlightGstRate(idx) {
    document.querySelectorAll('#gstRateList .gst-rate-item').forEach(function(el) { el.classList.remove('kb-active'); });
    var items = document.querySelectorAll('#gstRateList .gst-rate-item');
    if (idx >= 0 && idx < items.length) items[idx].classList.add('kb-active');
}

function _selectGstRate(idx) {
    if (idx >= 0 && idx < _gstRateOptions.length) {
        applyHsnToRow(_gstRateRow, _gstRateOptions[idx]);
    }
    _closeGstRate();
}

function _closeGstRate() {
    window.removeEventListener('keydown', _handleGstRateKey, true);
    document.getElementById('gstRateOverlay').classList.remove('show');
}

function applyHsnToRow(row, hsn) {
    let cgstPct = parseFloat(hsn.cgst_percent) || 0;
    let sgstPct = parseFloat(hsn.sgst_percent) || 0;
    let totalGst = parseFloat(hsn.total_gst_percent) || 0;
    if (totalGst > 0 && cgstPct === 0 && sgstPct === 0) { cgstPct = totalGst / 2; sgstPct = totalGst / 2; }
    if (totalGst === 0 && (cgstPct > 0 || sgstPct > 0)) totalGst = cgstPct + sgstPct;
    row.querySelector('.hsn-gst').value = totalGst.toFixed(2);
    row.querySelector('.hsn-cgst-pct').value = cgstPct.toFixed(2);
    row.querySelector('.hsn-sgst-pct').value = sgstPct.toFixed(2);
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    if (amount > 0) {
        row.querySelector('.hsn-cgst-amt').value = (amount * cgstPct / 100).toFixed(2);
        row.querySelector('.hsn-sgst-amt').value = (amount * sgstPct / 100).toFixed(2);
    }
    calculateTotals();
    /* Focus Amount field after HSN selection */
    setTimeout(function() {
        var amtEl = row.querySelector('.hsn-amount');
        if (amtEl) { amtEl.focus(); amtEl.select(); }
    }, 80);
}

function calculateHsnRow(row) {
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    const gstPct = parseFloat(row.querySelector('.hsn-gst').value) || 0;
    let cgstPct = parseFloat(row.querySelector('.hsn-cgst-pct').value) || 0;
    let sgstPct = parseFloat(row.querySelector('.hsn-sgst-pct').value) || 0;
    if (cgstPct === 0 && sgstPct === 0 && gstPct > 0) {
        cgstPct = gstPct / 2; sgstPct = gstPct / 2;
        row.querySelector('.hsn-cgst-pct').value = cgstPct.toFixed(2);
        row.querySelector('.hsn-sgst-pct').value = sgstPct.toFixed(2);
    }
    row.querySelector('.hsn-cgst-amt').value = (amount * cgstPct / 100).toFixed(2);
    row.querySelector('.hsn-sgst-amt').value = (amount * sgstPct / 100).toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let totalAmount = 0, totalCgst = 0, totalSgst = 0;
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        totalAmount += parseFloat(row.querySelector('.hsn-amount')?.value) || 0;
        totalCgst += parseFloat(row.querySelector('.hsn-cgst-amt')?.value) || 0;
        totalSgst += parseFloat(row.querySelector('.hsn-sgst-amt')?.value) || 0;
    });
    const totalGst = totalCgst + totalSgst;
    const netAmt = totalAmount + totalGst;
    const roundOff = Math.round(netAmt) - netAmt;
    const totalCredit = Math.round(netAmt);
    document.getElementById('grossDisplay').value = totalAmount.toFixed(2);
    document.getElementById('totalGstDisplay').textContent = totalGst.toFixed(2);
    document.getElementById('netAmtDisplay').textContent = netAmt.toFixed(2);
    document.getElementById('creditAmount').textContent = totalAmount.toFixed(2);
    document.getElementById('creditTotalGst').textContent = totalGst.toFixed(2);
    document.getElementById('creditNetAmt').textContent = netAmt.toFixed(2);
    document.getElementById('creditRoff').textContent = roundOff.toFixed(2);
    document.getElementById('totalCreditAmt').textContent = totalCredit.toFixed(2);
    document.getElementById('totalCgstAmt').textContent = totalCgst.toFixed(2);
    document.getElementById('totalSgstAmt').textContent = totalSgst.toFixed(2);
    calculateTds();
    updateGstSummary();
}

function calculateTds() {
    const netAmt = parseFloat(document.getElementById('creditNetAmt').textContent) || 0;
    const tdsPct = parseFloat(document.getElementById('tdsPercent').value) || 0;
    const tdsAmt = netAmt * tdsPct / 100;
    const totalCredit = parseFloat(document.getElementById('totalCreditAmt').textContent) || 0;
    document.getElementById('tdsAmount').value = tdsAmt.toFixed(2);
    document.getElementById('totalDebitAmt').textContent = (totalCredit - tdsAmt).toFixed(2);
}

function updateGstSummary() {
    const gstMap = {};
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const cgstPct = parseFloat(row.querySelector('.hsn-cgst-pct')?.value) || 0;
        const sgstPct = parseFloat(row.querySelector('.hsn-sgst-pct')?.value) || 0;
        const amount = parseFloat(row.querySelector('.hsn-amount')?.value) || 0;
        const cgstAmt = parseFloat(row.querySelector('.hsn-cgst-amt')?.value) || 0;
        const sgstAmt = parseFloat(row.querySelector('.hsn-sgst-amt')?.value) || 0;
        if (cgstPct > 0 || sgstPct > 0) {
            const key = `${cgstPct}-${sgstPct}`;
            if (!gstMap[key]) gstMap[key] = { cgstPct, sgstPct, totalAmt: 0, cgstAmt: 0, sgstAmt: 0 };
            gstMap[key].totalAmt += amount; gstMap[key].cgstAmt += cgstAmt; gstMap[key].sgstAmt += sgstAmt;
        }
    });
    const tbody = document.getElementById('gstSummaryBody');
    tbody.innerHTML = '';
    Object.values(gstMap).forEach(g => {
        const row = document.createElement('tr');
        row.innerHTML = `<td class="text-end">${g.cgstPct.toFixed(2)}</td><td class="text-end">${g.sgstPct.toFixed(2)}</td><td class="text-end">${g.totalAmt.toFixed(2)}</td><td class="text-end">${g.cgstAmt.toFixed(2)}</td><td class="text-end">${g.sgstAmt.toFixed(2)}</td>`;
        tbody.appendChild(row);
    });
}

function loadDebitAccounts() {
    buildDebitAcctDropdown();
}

function updateDebitAccount() {}

/* ══════════════════════════════════════════════════════════
   DEBIT ACCOUNT CUSTOM DROPDOWN
══════════════════════════════════════════════════════════ */
let _debitAcctKbIdx = -1;

function _isDebitAcctOpen() {
    return document.getElementById('debitAcctList').classList.contains('show');
}

function _getVisibleDebitAcctItems() {
    return Array.from(document.querySelectorAll('#debitAcctList .debit-acct-item'))
                .filter(function(el) { return el.style.display !== 'none'; });
}

function _setDebitAcctHighlight(idx) {
    var items = _getVisibleDebitAcctItems();
    document.querySelectorAll('#debitAcctList .debit-acct-item').forEach(function(el) {
        el.classList.remove('kb-active');
    });
    if (idx < 0 || idx >= items.length) return;
    _debitAcctKbIdx = idx;
    items[idx].classList.add('kb-active');
    items[idx].scrollIntoView({ block: 'nearest' });
}

function buildDebitAcctDropdown() {
    var list = document.getElementById('debitAcctList');
    list.innerHTML = '';
    cashBankBooks.forEach(function(acc) {
        var item = document.createElement('div');
        item.className = 'debit-acct-item';
        item.dataset.id = acc.id;
        item.dataset.name = acc.name;
        item.dataset.type = 'CB';
        item.textContent = acc.name;
        item.onclick = function() { selectDebitAcctItem(item); };
        list.appendChild(item);
    });
}

function openDebitAcctDropdown() {
    var list = document.getElementById('debitAcctList');
    if (!list.classList.contains('show')) {
        buildDebitAcctDropdown();
        filterDebitAcctDropdown(document.getElementById('debitAccountInput').value);
        list.classList.add('show');
    }
    _debitAcctKbIdx = -1;
}

function closeDebitAcctDropdown() {
    document.getElementById('debitAcctList').classList.remove('show');
    _debitAcctKbIdx = -1;
}

function filterDebitAcctDropdown(search) {
    search = (search || '').toLowerCase();
    document.querySelectorAll('#debitAcctList .debit-acct-item').forEach(function(el) {
        el.style.display = el.dataset.name.toLowerCase().includes(search) ? '' : 'none';
    });
    _debitAcctKbIdx = -1;
    document.querySelectorAll('#debitAcctList .debit-acct-item').forEach(function(el) {
        el.classList.remove('kb-active');
    });
}

function selectDebitAcctItem(item) {
    document.getElementById('debitAccountInput').value = item.dataset.name;
    document.getElementById('debitAccountId').value = item.dataset.id;
    document.getElementById('debitAccountType').value = item.dataset.type;
    document.getElementById('debitAccountName').value = item.dataset.name;
    closeDebitAcctDropdown();
}

function _isAccountModalOpen() {
    return document.getElementById('accountModalOverlay').classList.contains('show');
}

function openAccountModal(targetRow) {
    _currentAccountTargetRow = targetRow || null;
    loadAccountList();
    kbFocusIndex = -1;
    document.querySelectorAll('#accountListBody tr').forEach(r => r.classList.remove('table-primary', 'kb-active'));
    document.getElementById('accountModalOverlay').classList.add('show');
    document.getElementById('accountSearch').value = '';
    /* Register capture-phase keyboard handler */
    window.removeEventListener('keydown', _handleAccountModalKey, true);
    window.addEventListener('keydown', _handleAccountModalKey, true);
    setTimeout(function() { document.getElementById('accountSearch').focus(); }, 60);
}

function closeAccountModal() {
    /* Unregister capture-phase keyboard handler */
    window.removeEventListener('keydown', _handleAccountModalKey, true);
    document.getElementById('accountModalOverlay').classList.remove('show');
}

/* ══════════════════════════════════════════════════════════
   MODAL KEYBOARD – CAPTURE-PHASE
   ↑↓ navigate, Enter select, Esc close, typing → search
══════════════════════════════════════════════════════════ */
function _getVisibleAccountRows() {
    return Array.from(document.querySelectorAll('#accountListBody tr'))
                .filter(function(r) { return r.style.display !== 'none'; });
}

function _setKbHighlight(idx) {
    var rows = _getVisibleAccountRows();
    document.querySelectorAll('#accountListBody tr').forEach(r => r.classList.remove('table-primary', 'kb-active'));
    if (idx < 0 || idx >= rows.length) return;
    kbFocusIndex = idx;
    rows[idx].classList.add('table-primary', 'kb-active');
    rows[idx].scrollIntoView({ block: 'nearest' });
}

function _handleAccountModalKey(e) {
    if (!_isAccountModalOpen()) return;

    var MANAGED = ['ArrowDown', 'ArrowUp', 'Enter', 'Escape', 'Tab'];
    var isTyping = (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey);

    if (!MANAGED.includes(e.key) && !isTyping && e.key !== 'Backspace' && e.key !== 'Delete') return;

    var rows = _getVisibleAccountRows();

    if (e.key === 'ArrowDown') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (!rows.length) return;
        kbFocusIndex = Math.min(kbFocusIndex + 1, rows.length - 1);
        _setKbHighlight(kbFocusIndex);
        return;
    }
    if (e.key === 'ArrowUp') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (!rows.length) return;
        kbFocusIndex = Math.max(kbFocusIndex - 1, 0);
        _setKbHighlight(kbFocusIndex);
        return;
    }
    if (e.key === 'Enter') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        /* If nothing highlighted, highlight first */
        if (kbFocusIndex < 0 && rows.length > 0) {
            kbFocusIndex = 0;
            _setKbHighlight(0);
        }
        selectAccount();
        return;
    }
    if (e.key === 'Escape') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        closeAccountModal();
        return;
    }
    if (e.key === 'Tab') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        return;
    }
    /* Typing → redirect to search input */
    if (isTyping || e.key === 'Backspace' || e.key === 'Delete') {
        var searchInput = document.getElementById('accountSearch');
        if (document.activeElement !== searchInput) {
            searchInput.focus();
        }
        /* Reset keyboard index when user types */
        kbFocusIndex = -1;
    }
}

function loadAccountList() {
    const type = document.getElementById('accountTypeFilter').value;
    const accounts = type === 'SL' ? salesLedgers : generalLedgers;
    const tbody = document.getElementById('accountListBody');
    tbody.innerHTML = '';
    accounts.forEach(acc => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        row.innerHTML = `<td>${acc.alter_code || acc.account_code || ''}</td><td>${acc.ledger_name || acc.account_name}</td>`;
        row.dataset.id = acc.id;
        row.dataset.code = acc.alter_code || acc.account_code || '';
        row.dataset.name = acc.ledger_name || acc.account_name;
        row.dataset.type = type;
        row.onclick = () => { document.querySelectorAll('#accountListBody tr').forEach(r => r.classList.remove('table-primary')); row.classList.add('table-primary'); };
        row.ondblclick = () => selectAccount();
        tbody.appendChild(row);
    });
}

function filterAccountList() {
    const search = document.getElementById('accountSearch').value.toLowerCase();
    document.querySelectorAll('#accountListBody tr').forEach(row => { row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none'; });
    /* Reset keyboard focus after filtering */
    kbFocusIndex = -1;
    document.querySelectorAll('#accountListBody tr').forEach(r => r.classList.remove('kb-active'));
}

function selectAccount() {
    const selected = document.querySelector('#accountListBody tr.table-primary');
    if (!selected) { alert('Please select an account'); return; }

    /* Determine target row: use _currentAccountTargetRow if set, else find empty row */
    let targetRow = _currentAccountTargetRow || selectedAccountRow;
    if (!targetRow || targetRow.querySelector('.account-name').value) {
        const rows = document.querySelectorAll('#accountsTableBody tr');
        for (let row of rows) { if (!row.querySelector('.account-name').value) { targetRow = row; break; } }
        if (!targetRow || targetRow.querySelector('.account-name').value) { addAccountRow(); targetRow = document.querySelector('#accountsTableBody tr:last-child'); }
    }

    targetRow.querySelector('.account-code').value = selected.dataset.code;
    targetRow.querySelector('.account-name').value = selected.dataset.name;
    targetRow.querySelector('.account-type').value = selected.dataset.type;
    targetRow.querySelector('.account-id').value = selected.dataset.id;
    selectAccountRowEl(targetRow);

    /* Ensure a blank row exists below for next entry */
    var rows = Array.from(document.querySelectorAll('#accountsTableBody tr'));
    var lastRow = rows[rows.length - 1];
    if (lastRow && lastRow.querySelector('.account-name').value) {
        addAccountRow();
    }

    closeAccountModal();

    /* Focus the NEXT row's Code field so user can press Enter to add more */
    var allRows = Array.from(document.querySelectorAll('#accountsTableBody tr'));
    var filledIdx = allRows.indexOf(targetRow);
    var nextRow = allRows[filledIdx + 1];
    if (!nextRow) {
        addAccountRow();
        nextRow = document.querySelector('#accountsTableBody tr:last-child');
    }
    var codeEl = nextRow.querySelector('.account-code');
    setTimeout(function() {
        codeEl.focus();
    }, 100);

    _currentAccountTargetRow = null;
    selectedAccountRow = null;
}

function deleteAccount() { if (selectedAccountRow) { selectedAccountRow.remove(); selectedAccountRow = null; } else alert('Please select an account row'); }
function deleteHsnRow() { if (selectedHsnRow) { selectedHsnRow.remove(); selectedHsnRow = null; calculateTotals(); } else alert('Please select an HSN row'); }

function saveVoucher() {
    const items = [], accounts = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value;
        const amount = row.querySelector('.hsn-amount')?.value;
        if (hsnCode || amount) {
            items.push({ hsn_code: hsnCode, amount: amount || 0, gst_percent: row.querySelector('.hsn-gst')?.value || 0,
                cgst_percent: row.querySelector('.hsn-cgst-pct')?.value || 0, cgst_amount: row.querySelector('.hsn-cgst-amt')?.value || 0,
                sgst_percent: row.querySelector('.hsn-sgst-pct')?.value || 0, sgst_amount: row.querySelector('.hsn-sgst-amt')?.value || 0 });
        }
    });
    document.querySelectorAll('#accountsTableBody tr').forEach(row => {
        const name = row.querySelector('.account-name')?.value;
        if (name) accounts.push({ account_type: row.querySelector('.account-type')?.value, account_id: row.querySelector('.account-id')?.value,
            account_code: row.querySelector('.account-code')?.value, account_name: name });
    });
    const formData = {
        voucher_date: document.getElementById('voucherDate').value, local_inter: document.getElementById('localInter').value,
        description: document.getElementById('description').value, customer_id: document.getElementById('customerId').value,
        customer_name: document.getElementById('customerNameInput').value, gst_no: document.getElementById('gstNo').value,
        pan_no: document.getElementById('panNo').value, city: document.getElementById('city').value, address: document.getElementById('addressDisplay').value,
        amount: parseFloat(document.getElementById('grossDisplay').value) || 0, total_gst: parseFloat(document.getElementById('totalGstDisplay').textContent) || 0,
        net_amount: parseFloat(document.getElementById('netAmtDisplay').textContent) || 0, round_off: parseFloat(document.getElementById('creditRoff').textContent) || 0,
        total_credit: parseFloat(document.getElementById('totalCreditAmt').textContent) || 0, tds_percent: parseFloat(document.getElementById('tdsPercent').value) || 0,
        tds_amount: parseFloat(document.getElementById('tdsAmount').value) || 0, debit_account_id: document.getElementById('debitAccountId').value,
        debit_account_type: document.getElementById('debitAccountType').value, debit_account_name: document.getElementById('debitAccountName').value,
        total_debit: parseFloat(document.getElementById('totalDebitAmt').textContent) || 0,
        total_cgst_amount: parseFloat(document.getElementById('totalCgstAmt').textContent) || 0,
        total_sgst_amount: parseFloat(document.getElementById('totalSgstAmt').textContent) || 0,
        items: items, accounts: accounts, _token: '<?php echo e(csrf_token()); ?>'
    };
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    fetch('<?php echo e(route("admin.voucher-income.store")); ?>', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }, body: JSON.stringify(formData) })
    .then(r => r.json()).then(data => { if (data.success) { alert('Voucher #' + data.voucher_no + ' saved!'); window.location.reload(); } else alert('Error: ' + data.message); })
    .catch(e => { console.error('Error:', e); alert('Failed to save'); });
}

function deleteVoucher() { alert('Delete available in modification mode'); }
function printVoucher() { window.print(); }
function reverseVoucher() { alert('Reverse available in modification mode'); }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/voucher-income/transaction.blade.php ENDPATH**/ ?>