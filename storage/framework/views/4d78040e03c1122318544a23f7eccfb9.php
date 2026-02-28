<?php $__env->startSection('title', 'Voucher Purchase (Input GST)'); ?>

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
    
    .debit-section { background: #ffe0e0; border: 1px solid #cc0000; padding: 5px; }
    .debit-title { color: #cc0000; font-weight: bold; font-size: 11px; font-style: italic; }
    .credit-section { background: #e0ffe0; border: 1px solid #008000; padding: 5px; }
    .credit-title { color: #008000; font-weight: bold; font-size: 11px; font-style: italic; }
    
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 2px 5px; }
    .summary-label { font-size: 10px; }
    .summary-value { font-size: 11px; color: #0000ff; font-weight: bold; text-align: right; min-width: 80px; }
    
    .btn-action { font-size: 10px; padding: 3px 10px; }
    .footer-section { background: #d0d0d0; padding: 8px; border: 1px solid #999; }

    /* Focus rings on header fields */
    #voucherDate:focus, #billNo:focus, #billDate:focus,
    #localInter:focus, #rcm:focus, #description:focus {
        outline: none !important;
        border: 2px solid #008000 !important;
        box-shadow: 0 0 0 3px rgba(0,128,0,.20) !important;
    }
    /* Supplier custom dropdown */
    #supplierDisplay:focus { outline:none !important; border:2px solid #008000 !important; box-shadow:0 0 0 3px rgba(0,128,0,.20) !important; }
    .sup-opt:hover, .sup-opt.sup-hi { background:#008000 !important; color:#fff !important; }
    /* Account Type Filter dropdown */
    #atfDisplay:focus { outline:none !important; border:2px solid #008000 !important; box-shadow:0 0 0 3px rgba(0,128,0,.20) !important; }
    .atf-opt:hover, .atf-opt.atf-hi { background:#008000 !important; color:#fff !important; }
    /* Account list keyboard highlight */
    #accountListBody tr.kb-hi { background:#d4edda !important; }
    #accountListBody tr.kb-hi td { color:#000 !important; }
    /* Account row in top table — keyboard highlight */
    #accountsTableBody tr.kb-row { background:#e8f5e9 !important; outline:2px solid #008000; }

    /* Custom HSN Dropdown */
    .hsn-dropdown-list { display: none; position: fixed; width: 320px; max-height: 220px; overflow-y: auto; background: #fff; border: 1px solid #800000; border-radius: 0 0 6px 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.25); z-index: 99999; }
    .hsn-dropdown-list.show { display: block; }
    .hsn-dropdown-item { padding: 4px 8px; cursor: pointer; font-size: 10px; border-bottom: 1px solid #eee; display: flex; align-items: center; }
    .hsn-dropdown-item:hover { background: #ffffcc; }
    .hsn-dropdown-item.kb-active { background: #800000 !important; color: #fff !important; }
    .hsn-dropdown-item .hsn-code-label { font-weight: bold; }
    .hsn-dropdown-item .hsn-name-label { color: #666; margin-left: 6px; }
    .hsn-dropdown-item .hsn-gst-label { color: #008000; font-weight: bold; margin-left: auto; white-space: nowrap; }
    .hsn-dropdown-item.kb-active .hsn-name-label { color: #ffc; }
    .hsn-dropdown-item.kb-active .hsn-gst-label { color: #afa; }
    /* GST Rate Selector */
    .gst-rate-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 999999; }
    .gst-rate-overlay.show { display: flex; align-items: center; justify-content: center; }
    .gst-rate-box { background: #fff; border-radius: 8px; box-shadow: 0 8px 30px rgba(0,0,0,0.3); padding: 15px; min-width: 250px; }
    .gst-rate-box h6 { margin: 0 0 10px; font-size: 12px; color: #800000; font-weight: bold; }
    .gst-rate-item { padding: 6px 10px; cursor: pointer; font-size: 11px; border: 1px solid #eee; margin-bottom: 4px; border-radius: 4px; }
    .gst-rate-item:hover { background: #ffffcc; }
    .gst-rate-item.kb-active { background: #800000 !important; color: #fff !important; border-color: #800000; }
    /* Credit Account Dropdown */
    .credit-acct-wrap { position: relative; width: 100%; }
    .credit-acct-wrap input { width: 100%; }
    .credit-acct-list { display: none; position: absolute; bottom: 100%; left: 0; width: 100%; max-height: 180px; overflow-y: auto; background: #fff; border: 1px solid #008000; border-radius: 6px 6px 0 0; box-shadow: 0 -4px 12px rgba(0,0,0,0.2); z-index: 99999; }
    .credit-acct-list.show { display: block; }
    .credit-acct-item { padding: 4px 8px; cursor: pointer; font-size: 11px; border-bottom: 1px solid #eee; }
    .credit-acct-item:hover { background: #e0ffe0; }
    .credit-acct-item.kb-active { background: #008000 !important; color: #fff !important; }

    /* Custom Modal Styles */
    .custom-modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999999; }
    .custom-modal-overlay.show { display: flex; align-items: center; justify-content: center; }
    .custom-modal { background: #fff; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); width: 90%; max-width: 700px; max-height: 80vh; display: flex; flex-direction: column; }
    .custom-modal-header { background: #008000; color: #fff; padding: 10px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .custom-modal-header h6 { margin: 0; font-size: 14px; }
    .custom-modal-close { background: none; border: none; color: #fff; font-size: 20px; cursor: pointer; line-height: 1; }
    .custom-modal-body { padding: 15px; overflow-y: auto; flex: 1; }
    .custom-modal-footer { padding: 10px 15px; border-top: 1px solid #ddd; display: flex; justify-content: flex-end; gap: 10px; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body voucher-form p-0">
        <form id="voucherForm" method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>
            <div class="header-section">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <span class="field-label">Voucher</span>
                        <input type="date" class="form-control" name="voucher_date" id="voucherDate" value="<?php echo e(date('Y-m-d')); ?>" style="width: 120px;" tabindex="1">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">Voucher No :</span>
                        <input type="text" class="form-control bg-light" id="voucherNoDisplay" value="<?php echo e($nextVoucherNo); ?>" readonly style="width: 60px;">
                        <input type="hidden" name="voucher_no" id="voucherNo" value="<?php echo e($nextVoucherNo); ?>">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">Bill No. :</span>
                        <input type="text" class="form-control" name="bill_no" id="billNo" style="width: 100px;" tabindex="2">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">Bill Date :</span>
                        <input type="date" class="form-control" name="bill_date" id="billDate" value="<?php echo e(date('Y-m-d')); ?>" style="width: 120px;" tabindex="3">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">L(ocal) / I(nter</span>
                        <input type="text" class="form-control" name="local_inter" id="localInter" value="L" maxlength="1" style="width: 30px; text-transform: uppercase;" tabindex="4">
                    </div>
                    <div class="col-auto">
                        <span class="field-label">RCM (Y/N) :</span>
                        <input type="text" class="form-control" name="rcm" id="rcm" value="N" maxlength="1" style="width: 30px; text-transform: uppercase;" tabindex="5">
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-12">
                        <span class="field-label">Description :</span>
                        <input type="text" class="form-control" name="description" id="description" style="width: 100%;" tabindex="6">
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
                        <div class="col-auto" style="position:relative;">
                            <span class="field-label-green">Supplier</span><br>
                            <input type="text" id="supplierDisplay"
                                   class="form-control"
                                   placeholder="Select Supplier" tabindex="7"
                                   style="width:220px;cursor:pointer;caret-color:transparent;background:#fff;"
                                   autocomplete="off">
                            <input type="hidden" name="supplier_id" id="supplierId" value="">
                            <div id="supplierMenu" style="
                                display:none;position:absolute;top:100%;left:0;width:300px;
                                background:#fff;border:2px solid #008000;border-radius:4px;
                                z-index:99999;box-shadow:0 4px 16px rgba(0,0,0,.25);
                                max-height:200px;overflow-y:auto;">
                                <input type="text" id="supplierSearch"
                                       style="width:100%;padding:6px 8px;border:none;border-bottom:1px solid #ccc;font-size:11px;outline:none;"
                                       placeholder="Type to search...">
                                <div id="supplierOpts">
                                    <div class="sup-opt" data-val="" data-gst="" data-pan="" data-city="" data-pin=""
                                         style="padding:5px 10px;cursor:pointer;font-size:11px;color:#888;">— Select Supplier —</div>
                                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="sup-opt"
                                         data-val="<?php echo e($supplier->supplier_id); ?>"
                                         data-gst="<?php echo e($supplier->gst_no); ?>"
                                         data-pan="<?php echo e($supplier->pan); ?>"
                                         data-city="<?php echo e($supplier->address); ?>"
                                         data-pin=""
                                         style="padding:5px 10px;cursor:pointer;font-size:11px;"><?php echo e($supplier->name); ?></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <span class="field-label-green">Add Account (F9)</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-action" id="addAccountBtn" onclick="openAccountModal()">Add</button>
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
                    <div class="debit-section mb-2">
                        <div class="debit-title mb-2">Debit</div>
                        <div class="summary-row"><span class="summary-label">Amount</span><span class="summary-value" id="debitAmount">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">Total GST</span><span class="summary-value" id="debitTotalGst">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">Net Amt.</span><span class="summary-value" id="debitNetAmt">0.00</span></div>
                        <div class="summary-row"><span class="summary-label">R/Off</span><span class="summary-value" id="debitRoff">0.00</span></div>
                        <div class="summary-row" style="border-top: 1px solid #cc0000; margin-top: 5px; padding-top: 5px;"><span class="summary-label"><strong>Total Db. Amt.</strong></span><span class="summary-value" id="totalDebitAmt" style="color: #cc0000;">0.00</span></div>
                    </div>
                    <div class="credit-section">
                        <div class="credit-title mb-2">Credit</div>
                        <div class="row g-1 mb-1">
                            <div class="col-auto"><span class="field-label">TDS @</span><input type="number" class="form-control text-end" name="tds_percent" id="tdsPercent" value="0" step="0.01" style="width: 50px;" onchange="calculateTds()"></div>
                            <div class="col-auto"><span class="field-label">%</span><input type="text" class="form-control bg-light text-end" id="tdsAmount" readonly style="width: 80px;"></div>
                            <div class="col-auto"><span class="field-label">TDS</span></div>
                        </div>
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label">1. Cash & Bank / 2. General</span><input type="text" class="form-control" name="payment_type" id="paymentType" value="1" maxlength="1" style="width: 30px;"></div></div>
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label">Account</span>
                            <div class="credit-acct-wrap">
                                <input type="text" class="form-control" id="creditAccountInput" placeholder="Search account..." autocomplete="off">
                                <input type="hidden" name="credit_account_id" id="creditAccountId">
                                <input type="hidden" name="credit_account_type" id="creditAccountType">
                                <input type="hidden" name="credit_account_name" id="creditAccountName">
                                <div class="credit-acct-list" id="creditAcctList"></div>
                            </div>
                        </div></div>
                        <div class="row g-1 mb-1"><div class="col-12"><span class="field-label">Cheque No.</span><input type="text" class="form-control" name="cheque_no" id="chequeNo" style="width: 100%;"></div></div>
                        <div class="summary-row" style="border-top: 1px solid #008000; margin-top: 5px; padding-top: 5px;"><span class="summary-label"><strong>Total Cr</strong></span><span class="summary-value" id="totalCreditAmt" style="color: #008000;">0.00</span></div>
                    </div>
                </div>
            </div>
            <div class="footer-section d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger btn-action" onclick="deleteVoucher()">Delete Voucher</button>
                <button type="button" class="btn btn-secondary btn-action" onclick="printVoucher()">Print</button>
                <button type="button" class="btn btn-warning btn-action" onclick="reverseVoucher()">Reverse</button>
                <button type="button" class="btn btn-success btn-action" onclick="saveVoucher()">Save</button>
                <a href="<?php echo e(route('admin.voucher-purchase.index')); ?>" class="btn btn-secondary btn-action">Exit</a>
            </div>
        </form>
    </div>
</div>

<!-- HSN Datalist for autocomplete -->
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

<!-- Custom Account Selection Modal -->
<div class="custom-modal-overlay" id="accountModalOverlay">
    <div class="custom-modal">
        <div class="custom-modal-header">
            <h6>Select Account</h6>
            <button type="button" class="custom-modal-close" onclick="closeAccountModal()">&times;</button>
        </div>
        <div class="custom-modal-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm" id="accountSearch" placeholder="Search..." oninput="filterAccountList()">
                </div>
                <div class="col-md-6" style="position:relative;">
                    <input type="text" id="atfDisplay"
                           class="form-control form-control-sm"
                           value="Purchase Ledger" readonly
                           style="cursor:pointer;background:#fff;caret-color:transparent;"
                           autocomplete="off">
                    <input type="hidden" id="accountTypeFilter" value="PL">
                    <div id="atfMenu" style="
                        display:none;position:absolute;top:100%;left:0;right:0;
                        background:#fff;border:2px solid #008000;border-radius:4px;
                        z-index:999999;box-shadow:0 4px 12px rgba(0,0,0,.2);overflow:hidden;">
                        <div class="atf-opt" data-val="PL" style="padding:7px 10px;cursor:pointer;font-size:12px;">Purchase Ledger</div>
                        <div class="atf-opt" data-val="GL" style="padding:7px 10px;cursor:pointer;font-size:12px;">General Ledger</div>
                    </div>
                </div>
            </div>
            <div style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-hover" id="accountListTable">
                    <thead><tr><th>Code</th><th>Name</th></tr></thead>
                    <tbody id="accountListBody"></tbody>
                </table>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeAccountModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="selectAccount()">Select</button>
        </div>
    </div>
</div>

<script>
const purchaseLedgers = <?php echo json_encode($purchaseLedgers, 15, 512) ?>;
const generalLedgers = <?php echo json_encode($generalLedgers, 15, 512) ?>;
const cashBankBooks = <?php echo json_encode($cashBankBooks, 15, 512) ?>;
const hsnCodes = <?php echo json_encode($hsnCodes, 15, 512) ?>;

let accountRowCount = 0, hsnRowCount = 0, selectedAccountRow = null, selectedHsnRow = null;
let modalKbIdx = -1;

document.addEventListener('DOMContentLoaded', function() {
    for (let i = 0; i < 3; i++) addAccountRow();
    for (let i = 0; i < 5; i++) addHsnRow();

    document.getElementById('paymentType').addEventListener('change', loadCreditAccounts);

    /* Kill Select2 if it was applied to any of our custom fields */
    setTimeout(function() {
        document.querySelectorAll('.select2-container').forEach(el => el.style.display = 'none');
        if (window.jQuery && $.fn && $.fn.select2) {
            try { $('#supplierId').select2('destroy'); } catch(e) {}
            try { $('#accountTypeFilter').select2('destroy'); } catch(e) {}
        }
    }, 300);
    loadCreditAccounts();

    /* ══════════════════════════════════════════════════════
       TOP-PRIORITY: Supplier dropdown keyboard (window capture)
       Must be registered FIRST so it fires before all other handlers
    ══════════════════════════════════════════════════════ */
    window.addEventListener('keydown', function(e) {
        var supMenu = document.getElementById('supplierMenu');
        if (!supMenu || supMenu.style.display === 'none') return;
        // Supplier dropdown is OPEN — handle all keys here
        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var opts = Array.from(document.querySelectorAll('#supplierOpts .sup-opt')).filter(function(o) { return o.style.display !== 'none'; });
            supIdx = Math.min(supIdx + 1, opts.length - 1);
            opts.forEach(function(o, i) { o.classList.toggle('sup-hi', i === supIdx); });
            if (opts[supIdx]) opts[supIdx].scrollIntoView({ block: 'nearest' });
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var opts = Array.from(document.querySelectorAll('#supplierOpts .sup-opt')).filter(function(o) { return o.style.display !== 'none'; });
            supIdx = Math.max(supIdx - 1, 0);
            opts.forEach(function(o, i) { o.classList.toggle('sup-hi', i === supIdx); });
            if (opts[supIdx]) opts[supIdx].scrollIntoView({ block: 'nearest' });
            return;
        }
        if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var opts = Array.from(document.querySelectorAll('#supplierOpts .sup-opt')).filter(function(o) { return o.style.display !== 'none'; });
            var opt = opts[supIdx];
            if (opt) {
                document.getElementById('supplierId').value = opt.dataset.val;
                document.getElementById('supplierDisplay').value = opt.textContent.trim();
                document.getElementById('gstNo').value = opt.dataset.gst || '';
                document.getElementById('panNo').value = opt.dataset.pan || '';
                document.getElementById('city').value = opt.dataset.city || '';
                document.getElementById('pin').value = opt.dataset.pin || '';
                supMenu.style.display = 'none';
                setTimeout(function() {
                    var hsn = document.querySelector('#hsnTableBody .hsn-code');
                    if (hsn) { hsn.focus(); hsn.select(); }
                }, 50);
            }
            return;
        }
        if (e.key === 'Escape') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            supMenu.style.display = 'none';
            document.getElementById('supplierDisplay').focus();
            return;
        }
    }, true);

    /* ══════════════════════════════════════════════════════
       SUPPLIER CUSTOM DROPDOWN
    ══════════════════════════════════════════════════════ */
    const supDisp   = document.getElementById('supplierDisplay');
    const supHid    = document.getElementById('supplierId');
    const supMenu   = document.getElementById('supplierMenu');
    const supSearch = document.getElementById('supplierSearch');
    const supOptsWrap = document.getElementById('supplierOpts');
    let supIdx = 0, _supSkipFocus = false;

    function supOpts() { return Array.from(supOptsWrap.querySelectorAll('.sup-opt')).filter(o=>o.style.display!=='none'); }
    function supIsOpen() { return supMenu.style.display !== 'none'; }

    function supOpen() {
        supMenu.style.display = 'block';
        supSearch.value = '';
        filterSupplierOpts('');
        supIdx = 0;
        supHilight(supIdx);
        setTimeout(()=>supSearch.focus(), 0);
    }
    function supClose() { supMenu.style.display = 'none'; }

    function filterSupplierOpts(term) {
        Array.from(supOptsWrap.querySelectorAll('.sup-opt')).forEach(o => {
            o.style.display = (!term || o.textContent.toLowerCase().includes(term.toLowerCase())) ? '' : 'none';
        });
        supIdx = 0; supHilight(0);
    }

    function supHilight(idx) {
        const opts = supOpts();
        opts.forEach((o,i)=>o.classList.toggle('sup-hi', i===idx));
        if (opts[idx]) opts[idx].scrollIntoView({ block:'nearest' });
    }

    function supSelectAndNext(val, label, gst, pan, city, pin) {
        supHid.value  = val;
        supDisp.value = label;
        document.getElementById('gstNo').value  = gst  || '';
        document.getElementById('panNo').value  = pan  || '';
        document.getElementById('city').value   = city || '';
        document.getElementById('pin').value    = pin  || '';
        _supSkipFocus = true;
        supClose();
        /* After supplier → move to first HSN code row */
        setTimeout(()=>{ const hsn = document.querySelector('#hsnTableBody .hsn-code'); if(hsn) { hsn.focus(); hsn.select(); } }, 50);
    }

    supDisp.addEventListener('focus', function() {
        if (_supSkipFocus) { _supSkipFocus = false; return; }
        setTimeout(()=>{ if(!supIsOpen()) supOpen(); }, 0);
    });
    supDisp.addEventListener('click', function() { supIsOpen() ? supClose() : supOpen(); });
    supDisp.addEventListener('keydown', function(e) {
        if (e.key==='Escape') { e.preventDefault(); supClose(); return; }
        if (!supIsOpen()) { supOpen(); return; }
    });

    supSearch.addEventListener('input', function() { filterSupplierOpts(this.value); });
    /* Capture-phase so it fires before global Enter handlers */
    supSearch.addEventListener('keydown', function(e) {
        if (e.key==='ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const opts=supOpts(); supIdx=Math.min(supIdx+1,opts.length-1); supHilight(supIdx); return;
        }
        if (e.key==='ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const opts=supOpts(); supIdx=Math.max(supIdx-1,0); supHilight(supIdx); return;
        }
        if (e.key==='Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const opts=supOpts(); const opt=opts[supIdx];
            if (opt) supSelectAndNext(opt.dataset.val, opt.textContent.trim(), opt.dataset.gst, opt.dataset.pan, opt.dataset.city, opt.dataset.pin);
            return;
        }
        if (e.key==='Escape') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); supClose(); supDisp.focus(); return; }
    }, true);

    Array.from(supOptsWrap.querySelectorAll('.sup-opt')).forEach((opt,i)=>{
        opt.addEventListener('mouseenter', ()=>{ supIdx=supOpts().indexOf(opt); supHilight(supIdx); });
        opt.addEventListener('click', ()=>{
            supSelectAndNext(opt.dataset.val, opt.textContent.trim(), opt.dataset.gst, opt.dataset.pan, opt.dataset.city, opt.dataset.pin);
        });
    });

    document.addEventListener('click', e=>{
        if (!e.target.closest('#supplierDisplay') && !e.target.closest('#supplierMenu')) supClose();
    });

    /* ══════════════════════════════════════════════════════
       ACCOUNT TYPE FILTER CUSTOM DROPDOWN (in modal)
    ══════════════════════════════════════════════════════ */
    const atfDisp = document.getElementById('atfDisplay');
    const atfHid  = document.getElementById('accountTypeFilter');
    const atfMenu = document.getElementById('atfMenu');
    const atfOpts = Array.from(atfMenu.querySelectorAll('.atf-opt'));
    let atfIdx = 0;

    function atfIsOpen() { return atfMenu.style.display !== 'none'; }
    function atfOpen()  { atfMenu.style.display='block'; atfHilight(atfOpts.findIndex(o=>o.dataset.val===atfHid.value)||0); }
    function atfClose() { atfMenu.style.display='none'; }
    function atfHilight(idx) { atfOpts.forEach((o,i)=>o.classList.toggle('atf-hi',i===idx)); atfIdx=idx; }

    function atfSelect(val, label) {
        atfHid.value  = val;
        atfDisp.value = label;
        atfClose();
        loadAccountList();
        /* After filter choose → move to first visible row */
        const rows = modalVisibleRows();
        if (rows.length) {
            modalKbIdx = 0;
            modalHilight(0);
            rows[0].focus();
        } else {
            document.getElementById('accountSearch').focus();
        }
    }

    atfDisp.addEventListener('click', ()=> atfIsOpen() ? atfClose() : atfOpen());
    atfDisp.addEventListener('keydown', function(e) {
        if (e.key==='ArrowDown') { e.preventDefault(); if(!atfIsOpen()){atfOpen();return;} atfHilight(Math.min(atfIdx+1,atfOpts.length-1)); return; }
        if (e.key==='ArrowUp')   { e.preventDefault(); if(!atfIsOpen()){atfOpen();return;} atfHilight(Math.max(atfIdx-1,0)); return; }
        if (e.key==='Enter'||e.key===' ') {
            e.preventDefault(); e.stopPropagation();
            if (atfIsOpen()) { atfSelect(atfOpts[atfIdx].dataset.val, atfOpts[atfIdx].textContent.trim()); }
            else atfOpen(); return;
        }
        if (e.key==='Escape') { e.preventDefault(); atfClose(); return; }
        if (e.key==='Tab') { e.preventDefault(); atfClose(); document.getElementById('accountSearch').focus(); return; }
    });
    atfOpts.forEach((opt,i)=>{
        opt.addEventListener('mouseenter',()=>atfHilight(i));
        opt.addEventListener('click',()=>atfSelect(opt.dataset.val, opt.textContent.trim()));
    });
    document.addEventListener('click', e=>{
        if (!e.target.closest('#atfDisplay') && !e.target.closest('#atfMenu')) atfClose();
    });

    /* ══════════════════════════════════════════════════════
       HEADER ENTER CHAIN  (capture phase on date inputs)
    ══════════════════════════════════════════════════════ */
    function chainTo(nextId) {
        const el = document.getElementById(nextId);
        if (el) { el.focus(); if (el.select) el.select(); }
    }

    document.getElementById('voucherDate').addEventListener('keydown', function(e) {
        if (e.key!=='Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        setTimeout(()=>chainTo('billNo'), 30);
    }, true);

    document.getElementById('billNo').addEventListener('keydown', function(e) {
        if (e.key!=='Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        setTimeout(()=>chainTo('billDate'), 30);
    }, true);

    document.getElementById('billDate').addEventListener('keydown', function(e) {
        if (e.key!=='Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        setTimeout(()=>chainTo('localInter'), 30);
    }, true);

    document.getElementById('localInter').addEventListener('keydown', function(e) {
        if (e.key!=='Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        chainTo('rcm');
    });

    document.getElementById('rcm').addEventListener('keydown', function(e) {
        if (e.key!=='Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        chainTo('description');
    });

    function triggerAddFromDescriptionEnter() {
        _supSkipFocus = true;
        supClose();
        
        // Find or create first empty row for the account entry
        let targetRow = null;
        const rows = document.querySelectorAll('#accountsTableBody tr');
        
        // Look for first empty row
        for (let row of rows) {
            if (!row.querySelector('.account-name').value) {
                targetRow = row;
                break;
            }
        }
        
        // If no empty row, add a new one
        if (!targetRow) {
            addAccountRow();
            const newRows = document.querySelectorAll('#accountsTableBody tr');
            targetRow = newRows[newRows.length - 1];
        }
        
        // Select this row and open modal
        if (targetRow) {
            selectAccountRowEl(targetRow);
            setTimeout(function () {
                openAccountModal();
            }, 50);
        }
    }

    document.getElementById('description').addEventListener('keydown', function(e) {
        if (e.key!=='Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        triggerAddFromDescriptionEnter();
    });

    /* Capture fallback: block any global Enter-chain from moving focus to supplier */
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        if (!e.target || e.target.id !== 'description') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        triggerAddFromDescriptionEnter();
    }, true);

    /* ── Capture-phase Enter on account-code / account-name ──
       Opens the account modal for the next empty row */
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' || e.ctrlKey) return;
        if (isAccountModalOpen()) return;
        if (_isHsnDropdownOpen() || _isGstRateOpen() || _isCreditAcctOpen()) return;
        if (supIsOpen()) return;

        var el = document.activeElement;
        if (!el) return;
        var isCode = el.classList.contains('account-code');
        var isName = el.classList.contains('account-name');
        if (!isCode && !isName) return;
        var row = el.closest('tr');
        if (!row || !row.closest('#accountsTableBody')) return;

        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();

        // Find next empty row or create one
        var targetRow = null;
        var allRows = document.querySelectorAll('#accountsTableBody tr');
        var foundCurrent = false;
        for (var r of allRows) {
            if (r === row) { foundCurrent = true; continue; }
            if (foundCurrent && !r.querySelector('.account-name').value) {
                targetRow = r;
                break;
            }
        }
        if (!targetRow) {
            addAccountRow();
            targetRow = document.querySelector('#accountsTableBody tr:last-child');
        }
        selectAccountRowEl(targetRow);
        setTimeout(function() { openAccountModal(); }, 50);
    }, true);

    /* ══════════════════════════════════════════════════════
       MODAL KEYBOARD NAVIGATION
    ══════════════════════════════════════════════════════ */
    function modalVisibleRows() {
        return Array.from(document.querySelectorAll('#accountListBody tr'))
                    .filter(r=>r.style.display!=='none');
    }
    function modalHilight(idx) {
        const rows = modalVisibleRows();
        rows.forEach((r,i)=>{ r.classList.remove('table-primary','kb-hi'); if(i===idx) r.classList.add('table-primary','kb-hi'); });
        if (rows[idx]) rows[idx].scrollIntoView({ block:'nearest' });
        modalKbIdx = idx;
    }

    /* ══════════════════════════════════════════════════════
       MODAL KEYBOARD NAVIGATION - FIXED IMPLEMENTATION
    ══════════════════════════════════════════════════════ */
    
    function isAccountModalOpen() {
        const modal = document.getElementById('accountModalOverlay');
        return modal && modal.classList.contains('show');
    }
    
    /* accountSearch: ↑↓ navigates list, Enter selects */
    const accountSearch = document.getElementById('accountSearch');
    
    accountSearch.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const rows = modalVisibleRows();
            if (rows.length === 0) return;
            modalKbIdx = modalKbIdx < 0 ? 0 : Math.min(modalKbIdx + 1, rows.length - 1);
            modalHilight(modalKbIdx);
            // Ensure focus stays on search box
            setTimeout(() => this.focus(), 0);
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            const rows = modalVisibleRows();
            if (rows.length === 0) return;
            modalKbIdx = Math.max(modalKbIdx - 1, 0);
            modalHilight(modalKbIdx);
            // Ensure focus stays on search box
            setTimeout(() => this.focus(), 0);
            return;
        }
        if (e.key === 'Enter') {
            e.preventDefault();
            const rows = modalVisibleRows();
            if (rows.length === 0) return;
            if (modalKbIdx < 0) {
                modalKbIdx = 0;
                modalHilight(0);
                return;
            }
            selectAccount();
            return;
        }
        if (e.key === 'Escape') {
            e.preventDefault();
            closeAccountModal();
            return;
        }
        if (e.key === 'Tab') {
            e.preventDefault();
            atfDisp.focus();
            return;
        }
    });

    /* Reset highlight when filtering */
    accountSearch.addEventListener('input', function() {
        filterAccountList();
        modalKbIdx = -1;
        // Clear all highlights
        document.querySelectorAll('#accountListBody tr').forEach(r => {
            r.classList.remove('table-primary', 'kb-hi');
        });
    });

    /* ── WINDOW CAPTURE-PHASE HANDLER for modal ─────────────
       This ensures modal navigation works even if focus is lost */
    window.addEventListener('keydown', function(e) {
        if (!isAccountModalOpen()) return;
        
        const managedKeys = ['ArrowDown', 'ArrowUp', 'Enter', 'Escape'];
        if (managedKeys.indexOf(e.key) === -1) return;
        
        // Don't interfere if typing in search (already handled above)
        if (e.target.id === 'accountSearch') return;
        // Don't interfere with filter dropdown
        if (e.target.id === 'atfDisplay' || e.target.closest('#atfMenu')) return;
        
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        const rows = modalVisibleRows();
        if (rows.length === 0) return;
        
        if (e.key === 'ArrowDown') {
            modalKbIdx = modalKbIdx < 0 ? 0 : Math.min(modalKbIdx + 1, rows.length - 1);
            modalHilight(modalKbIdx);
            // Return focus to search box
            setTimeout(() => accountSearch.focus(), 0);
            return;
        }
        if (e.key === 'ArrowUp') {
            modalKbIdx = Math.max(modalKbIdx - 1, 0);
            modalHilight(modalKbIdx);
            // Return focus to search box
            setTimeout(() => accountSearch.focus(), 0);
            return;
        }
        if (e.key === 'Enter') {
            if (modalKbIdx < 0) {
                modalKbIdx = 0;
                modalHilight(0);
                return;
            }
            selectAccount();
            return;
        }
        if (e.key === 'Escape') {
            closeAccountModal();
            return;
        }
    }, true);

    /* Global F9 + Escape handler */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F9') { e.preventDefault(); openAccountModal(); return; }
        if (e.key === 'Escape' && !isAccountModalOpen()) {
            if (_isCreditAcctOpen()) { closeCreditAcctDropdown(); return; }
            if (_isHsnDropdownOpen()) { closeHsnDropdown(); return; }
            if (supIsOpen()) { supClose(); return; }
            if (atfIsOpen()) { atfClose(); return; }
        }
    });

    /* ── Ctrl+Enter → smart navigation ──
       If Supplier not selected → open Supplier dropdown
       If Supplier selected → focus TDS @ */
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            if (isAccountModalOpen() || _isGstRateOpen()) return;
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var suppId = document.getElementById('supplierId').value;
            if (!suppId) {
                supOpen();
            } else {
                var tdsEl = document.getElementById('tdsPercent');
                tdsEl.focus(); tdsEl.select();
            }
        }
    }, true);

    /* ── Enter navigation: TDS@ → %TDS → Payment Type → Credit Account → Cheque ── */
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' || e.ctrlKey) return;
        if (isAccountModalOpen() || _isHsnDropdownOpen() || _isGstRateOpen() || _isCreditAcctOpen()) return;
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
            document.getElementById('paymentType').focus();
            return;
        }
        if (el.id === 'paymentType') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            loadCreditAccounts();
            var inp = document.getElementById('creditAccountInput');
            inp.value = '';
            inp.focus();
            openCreditAcctDropdown();
            return;
        }
        if (el.id === 'chequeNo') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            return;
        }
    }, true);

    /* ── Credit Account dropdown keyboard handler ── */
    window.addEventListener('keydown', function(e) {
        if (!_isCreditAcctOpen()) return;
        var el = document.activeElement;
        if (!el || el.id !== 'creditAccountInput') return;
        var items = _getVisibleCreditAcctItems();
        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!items.length) return;
            _creditAcctKbIdx = Math.min(_creditAcctKbIdx + 1, items.length - 1);
            _setCreditAcctHighlight(_creditAcctKbIdx); return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!items.length) return;
            _creditAcctKbIdx = Math.max(_creditAcctKbIdx - 1, 0);
            _setCreditAcctHighlight(_creditAcctKbIdx); return;
        }
        if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (_creditAcctKbIdx < 0 && items.length > 0) { _creditAcctKbIdx = 0; _setCreditAcctHighlight(0); }
            if (_creditAcctKbIdx >= 0 && _creditAcctKbIdx < items.length) selectCreditAcctItem(items[_creditAcctKbIdx]);
            return;
        }
        if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); closeCreditAcctDropdown(); return; }
        _creditAcctKbIdx = -1;
    }, true);

    /* Credit Account input filter */
    document.getElementById('creditAccountInput').addEventListener('input', function() {
        filterCreditAcctDropdown(this.value);
        if (!_isCreditAcctOpen()) openCreditAcctDropdown();
    });
    document.getElementById('creditAccountInput').addEventListener('focus', function() {
        filterCreditAcctDropdown(this.value);
        openCreditAcctDropdown();
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.credit-acct-wrap')) closeCreditAcctDropdown();
    });

    /* ── HSN Code Enter → open HSN dropdown ── */
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        if (isAccountModalOpen() || _isGstRateOpen() || _isCreditAcctOpen()) return;
        if (supIsOpen()) return;
        if (_isHsnDropdownOpen()) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var items = _getVisibleHsnItems();
            if (_hsnKbIdx < 0 && items.length > 0) { _hsnKbIdx = 0; _setHsnKbHighlight(0); }
            if (_hsnKbIdx >= 0 && _hsnKbIdx < items.length) selectHsnItem(items[_hsnKbIdx]);
            return;
        }
        var el = document.activeElement;
        if (!el || !el.classList.contains('hsn-code')) return;
        var row = el.closest('tr');
        if (!row || !row.closest('#hsnTableBody')) return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        openHsnDropdown(el);
    }, true);

    /* ── HSN dropdown keyboard navigation ── */
    window.addEventListener('keydown', function(e) {
        if (!_isHsnDropdownOpen()) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var items = _getVisibleHsnItems();
            if (!items.length) return;
            _hsnKbIdx = Math.min(_hsnKbIdx + 1, items.length - 1);
            _setHsnKbHighlight(_hsnKbIdx); return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var items = _getVisibleHsnItems();
            if (!items.length) return;
            _hsnKbIdx = Math.max(_hsnKbIdx - 1, 0);
            _setHsnKbHighlight(_hsnKbIdx); return;
        }
        if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); closeHsnDropdown(); return; }
        if (e.key === 'Tab') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); closeHsnDropdown(); return; }
    }, true);

    /* HSN code input → filter dropdown */
    document.getElementById('hsnTableBody').addEventListener('input', function(e) {
        if (!e.target.classList.contains('hsn-code')) return;
        if (!_isHsnDropdownOpen()) openHsnDropdown(e.target);
        else filterHsnDropdown(e.target.value);
    });
    document.addEventListener('click', function(e) {
        if (_isHsnDropdownOpen() && !e.target.classList.contains('hsn-code') && !e.target.closest('#hsnDropdownList')) closeHsnDropdown();
    });

    /* ── Ctrl+S → Save ── */
    window.addEventListener('keydown', function(e) {
        if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            saveVoucher();
        }
    }, true);

    /* Auto-focus first field */
    setTimeout(()=>chainTo('voucherDate'), 100);
});

function addAccountRow(data = null) {
    accountRowCount++;
    const tbody = document.getElementById('accountsTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', accountRowCount);
    row.innerHTML = `
        <td><input type="text" class="account-code" name="accounts[${accountRowCount}][account_code]" value="${data?.account_code || ''}" tabindex="-1"
                style="cursor:pointer;" readonly
                onclick="selectAccountRowEl(this.closest('tr')); openAccountModal();"
                onfocus="selectAccountRowEl(this.closest('tr'));"
                onkeydown="if(event.key==='Enter'){event.preventDefault();selectAccountRowEl(this.closest('tr'));openAccountModal();}"></td>
        <td><input type="text" class="account-name" name="accounts[${accountRowCount}][account_name]" value="${data?.account_name || ''}" readonly
                onclick="selectAccountRowEl(this.closest('tr')); openAccountModal();"
                onkeydown="if(event.key==='Enter'){event.preventDefault();selectAccountRowEl(this.closest('tr'));openAccountModal();}"
                tabindex="-1">
            <input type="hidden" class="account-type" name="accounts[${accountRowCount}][account_type]" value="${data?.account_type || ''}">
            <input type="hidden" class="account-id" name="accounts[${accountRowCount}][account_id]" value="${data?.account_id || ''}"></td>
    `;
    row.onclick = () => selectAccountRowEl(row);
    tbody.appendChild(row);
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
    row.innerHTML = `
        <td><input type="text" class="hsn-code" name="items[${hsnRowCount}][hsn_code]" value="${data?.hsn_code || ''}" onclick="selectHsnRowEl(this.closest('tr'))" autocomplete="off"></td>
        <td><input type="number" class="hsn-amount text-end" name="items[${hsnRowCount}][amount]" value="${data?.amount || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr')); checkAddHsnRow();" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-gst text-end" name="items[${hsnRowCount}][gst_percent]" value="${data?.gst_percent || ''}" step="0.01" onchange="calculateHsnRow(this.closest('tr'))" onclick="selectHsnRowEl(this.closest('tr'))"></td>
        <td><input type="number" class="hsn-cgst-pct text-end bg-light" name="items[${hsnRowCount}][cgst_percent]" value="${data?.cgst_percent || ''}" readonly></td>
        <td><input type="number" class="hsn-cgst-amt text-end bg-light" name="items[${hsnRowCount}][cgst_amount]" value="${data?.cgst_amount || ''}" readonly></td>
        <td><input type="number" class="hsn-sgst-pct text-end bg-light" name="items[${hsnRowCount}][sgst_percent]" value="${data?.sgst_percent || ''}" readonly></td>
        <td><input type="number" class="hsn-sgst-amt text-end bg-light" name="items[${hsnRowCount}][sgst_amount]" value="${data?.sgst_amount || ''}" readonly></td>
    `;
    row.onclick = () => selectHsnRowEl(row);
    tbody.appendChild(row);
}

function selectHsnRowEl(row) {
    document.querySelectorAll('#hsnTableBody tr').forEach(r => r.classList.remove('table-warning'));
    row.classList.add('table-warning');
    selectedHsnRow = row;
}

// Auto-complete HSN code
function autoCompleteHsn(input) {
    const val = input.value.toLowerCase();
    if (val.length < 1) return;
    const hsn = hsnCodes.find(h => h.hsn_code && h.hsn_code.toLowerCase().startsWith(val));
    if (hsn) {
        // Show suggestion but don't auto-fill yet
    }
}

// Check if we need to add more HSN rows
function checkAddHsnRow() {
    const rows = document.querySelectorAll('#hsnTableBody tr');
    const lastRow = rows[rows.length - 1];
    if (lastRow) {
        const amount = lastRow.querySelector('.hsn-amount')?.value;
        if (amount && parseFloat(amount) > 0) {
            addHsnRow();
        }
    }
}

function fetchHsnDetails(input) {
    const hsnCode = input.value.trim();
    if (!hsnCode) return;
    
    // Find ALL matching HSN codes (same code can have different GST rates)
    const matchingHsns = hsnCodes.filter(h => h.hsn_code && h.hsn_code.toString() === hsnCode.toString());
    
    if (matchingHsns.length === 0) {
        console.log('HSN not found:', hsnCode);
        return;
    }
    
    // Get unique GST rates (filter out duplicates and 0% if other rates exist)
    const uniqueRates = [];
    const seenRates = new Set();
    matchingHsns.forEach(hsn => {
        const gst = parseFloat(hsn.total_gst_percent) || 0;
        if (!seenRates.has(gst)) {
            seenRates.add(gst);
            uniqueRates.push(hsn);
        }
    });
    
    // Sort by GST rate (non-zero first, then by rate)
    uniqueRates.sort((a, b) => {
        const gstA = parseFloat(a.total_gst_percent) || 0;
        const gstB = parseFloat(b.total_gst_percent) || 0;
        if (gstA === 0 && gstB !== 0) return 1;
        if (gstA !== 0 && gstB === 0) return -1;
        return gstA - gstB;
    });
    
    const row = input.closest('tr');
    
    // If multiple GST rates exist, show selection
    if (uniqueRates.length > 1) {
        showGstRateSelector(row, uniqueRates, hsnCode);
    } else {
        // Only one rate, apply it directly
        applyHsnToRow(row, uniqueRates[0]);
    }
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
    window.removeEventListener('keydown', _handleGstRateKey, true);
    window.addEventListener('keydown', _handleGstRateKey, true);
}
var _gstRateRow = null, _gstRateOptions = [], _gstRateKbIdx = -1;
function _isGstRateOpen() { return document.getElementById('gstRateOverlay').classList.contains('show'); }
function _handleGstRateKey(e) {
    if (!_isGstRateOpen()) return;
    var items = document.querySelectorAll('#gstRateList .gst-rate-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); _gstRateKbIdx = Math.min(_gstRateKbIdx + 1, items.length - 1); _highlightGstRate(_gstRateKbIdx); return; }
    if (e.key === 'ArrowUp') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); _gstRateKbIdx = Math.max(_gstRateKbIdx - 1, 0); _highlightGstRate(_gstRateKbIdx); return; }
    if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); if (_gstRateKbIdx < 0 && items.length > 0) { _gstRateKbIdx = 0; _highlightGstRate(0); } _selectGstRate(_gstRateKbIdx); return; }
    if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); _closeGstRate(); return; }
    if (e.key >= '1' && e.key <= '9') { var idx = parseInt(e.key) - 1; if (idx < _gstRateOptions.length) { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); _selectGstRate(idx); } return; }
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
}
function _highlightGstRate(idx) { document.querySelectorAll('#gstRateList .gst-rate-item').forEach(function(el) { el.classList.remove('kb-active'); }); var items = document.querySelectorAll('#gstRateList .gst-rate-item'); if (idx >= 0 && idx < items.length) items[idx].classList.add('kb-active'); }
function _selectGstRate(idx) { if (idx >= 0 && idx < _gstRateOptions.length) applyHsnToRow(_gstRateRow, _gstRateOptions[idx]); _closeGstRate(); }
function _closeGstRate() { window.removeEventListener('keydown', _handleGstRateKey, true); document.getElementById('gstRateOverlay').classList.remove('show'); }

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
    setTimeout(function() { var amtEl = row.querySelector('.hsn-amount'); if (amtEl) { amtEl.focus(); amtEl.select(); } }, 80);
}

function calculateHsnRow(row) {
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    const gstPct = parseFloat(row.querySelector('.hsn-gst').value) || 0;
    const localInter = document.getElementById('localInter').value.toUpperCase();
    
    // Check if CGST/SGST percentages are already set (from HSN lookup)
    let cgstPct = parseFloat(row.querySelector('.hsn-cgst-pct').value) || 0;
    let sgstPct = parseFloat(row.querySelector('.hsn-sgst-pct').value) || 0;
    
    // Only recalculate percentages if they're not already set or if GST% changed manually
    if (cgstPct === 0 && sgstPct === 0 && gstPct > 0) {
        if (localInter === 'L') {
            cgstPct = gstPct / 2;
            sgstPct = gstPct / 2;
        } else {
            // Interstate - IGST (for now treating as CGST+SGST split, can be modified)
            cgstPct = gstPct / 2;
            sgstPct = gstPct / 2;
        }
        row.querySelector('.hsn-cgst-pct').value = cgstPct.toFixed(2);
        row.querySelector('.hsn-sgst-pct').value = sgstPct.toFixed(2);
    }
    
    // Calculate amounts
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
    const totalDebit = Math.round(netAmt);
    
    document.getElementById('grossDisplay').value = totalAmount.toFixed(2);
    document.getElementById('totalGstDisplay').textContent = totalGst.toFixed(2);
    document.getElementById('netAmtDisplay').textContent = netAmt.toFixed(2);
    document.getElementById('debitAmount').textContent = totalAmount.toFixed(2);
    document.getElementById('debitTotalGst').textContent = totalGst.toFixed(2);
    document.getElementById('debitNetAmt').textContent = netAmt.toFixed(2);
    document.getElementById('debitRoff').textContent = roundOff.toFixed(2);
    document.getElementById('totalDebitAmt').textContent = totalDebit.toFixed(2);
    document.getElementById('totalCgstAmt').textContent = totalCgst.toFixed(2);
    document.getElementById('totalSgstAmt').textContent = totalSgst.toFixed(2);
    calculateTds();
    updateGstSummary();
}

function calculateTds() {
    const netAmt = parseFloat(document.getElementById('debitNetAmt').textContent) || 0;
    const tdsPct = parseFloat(document.getElementById('tdsPercent').value) || 0;
    const tdsAmt = netAmt * tdsPct / 100;
    const totalDebit = parseFloat(document.getElementById('totalDebitAmt').textContent) || 0;
    document.getElementById('tdsAmount').value = tdsAmt.toFixed(2);
    document.getElementById('totalCreditAmt').textContent = (totalDebit - tdsAmt).toFixed(2);
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
            gstMap[key].totalAmt += amount;
            gstMap[key].cgstAmt += cgstAmt;
            gstMap[key].sgstAmt += sgstAmt;
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

function loadCreditAccounts() {
    buildCreditAcctDropdown();
}

function updateCreditAccount() {}

/* ══════════════════════════════════════════════════════════
   HSN CODE CUSTOM DROPDOWN
══════════════════════════════════════════════════════════ */
let _hsnKbIdx = -1;
let _hsnActiveInput = null;
function _isHsnDropdownOpen() { return document.getElementById('hsnDropdownList').classList.contains('show'); }
function _getVisibleHsnItems() { return Array.from(document.querySelectorAll('#hsnDropdownList .hsn-dropdown-item')).filter(function(el) { return el.style.display !== 'none'; }); }
function _setHsnKbHighlight(idx) {
    var items = _getVisibleHsnItems();
    document.querySelectorAll('#hsnDropdownList .hsn-dropdown-item').forEach(function(el) { el.classList.remove('kb-active'); });
    if (idx < 0 || idx >= items.length) return;
    _hsnKbIdx = idx; items[idx].classList.add('kb-active'); items[idx].scrollIntoView({ block: 'nearest' });
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
        item.dataset.idx = idx;
        item.innerHTML = '<span class="hsn-code-label">' + code + '</span><span class="hsn-name-label">' + (h.name || '') + '</span><span class="hsn-gst-label">' + gst + '%</span>';
        item.onclick = function() { selectHsnItem(item); };
        list.appendChild(item);
    });
}
function openHsnDropdown(inputEl) {
    _hsnActiveInput = inputEl;
    var list = document.getElementById('hsnDropdownList');
    buildHsnDropdown(); filterHsnDropdown(inputEl.value);
    var rect = inputEl.getBoundingClientRect();
    list.style.left = rect.left + 'px'; list.style.top = (rect.bottom) + 'px';
    list.classList.add('show'); _hsnKbIdx = -1;
}
function closeHsnDropdown() { document.getElementById('hsnDropdownList').classList.remove('show'); _hsnKbIdx = -1; _hsnActiveInput = null; }
function filterHsnDropdown(search) {
    search = (search || '').toLowerCase();
    document.querySelectorAll('#hsnDropdownList .hsn-dropdown-item').forEach(function(el) {
        var code = el.dataset.code.toLowerCase(); var name = el.dataset.name.toLowerCase();
        el.style.display = (code.includes(search) || name.includes(search)) ? '' : 'none';
    });
    _hsnKbIdx = -1;
    document.querySelectorAll('#hsnDropdownList .hsn-dropdown-item').forEach(function(el) { el.classList.remove('kb-active'); });
}
function selectHsnItem(item) {
    if (_hsnActiveInput) {
        _hsnActiveInput.value = item.dataset.code;
        var row = _hsnActiveInput.closest('tr');
        var hsnIdx = parseInt(item.dataset.idx);
        var hsn = hsnCodes[hsnIdx];
        closeHsnDropdown();
        applyHsnToRow(row, hsn);
    } else { closeHsnDropdown(); }
}

/* ══════════════════════════════════════════════════════════
   CREDIT ACCOUNT CUSTOM DROPDOWN
══════════════════════════════════════════════════════════ */
let _creditAcctKbIdx = -1;
function _isCreditAcctOpen() { return document.getElementById('creditAcctList').classList.contains('show'); }
function _getVisibleCreditAcctItems() { return Array.from(document.querySelectorAll('#creditAcctList .credit-acct-item')).filter(function(el) { return el.style.display !== 'none'; }); }
function _setCreditAcctHighlight(idx) {
    var items = _getVisibleCreditAcctItems();
    document.querySelectorAll('#creditAcctList .credit-acct-item').forEach(function(el) { el.classList.remove('kb-active'); });
    if (idx < 0 || idx >= items.length) return;
    _creditAcctKbIdx = idx; items[idx].classList.add('kb-active'); items[idx].scrollIntoView({ block: 'nearest' });
}
function buildCreditAcctDropdown() {
    var type = document.getElementById('paymentType').value;
    var accounts = type === '1' ? cashBankBooks : generalLedgers;
    var accountType = type === '1' ? 'CB' : 'GL';
    var list = document.getElementById('creditAcctList');
    list.innerHTML = '';
    accounts.forEach(function(acc) {
        var item = document.createElement('div');
        item.className = 'credit-acct-item';
        item.dataset.id = acc.id;
        item.dataset.name = acc.name || acc.account_name;
        item.dataset.type = accountType;
        item.textContent = acc.name || acc.account_name;
        item.onclick = function() { selectCreditAcctItem(item); };
        list.appendChild(item);
    });
}
function openCreditAcctDropdown() {
    var list = document.getElementById('creditAcctList');
    if (!list.classList.contains('show')) {
        buildCreditAcctDropdown();
        filterCreditAcctDropdown(document.getElementById('creditAccountInput').value);
        list.classList.add('show');
    }
    _creditAcctKbIdx = -1;
}
function closeCreditAcctDropdown() { document.getElementById('creditAcctList').classList.remove('show'); _creditAcctKbIdx = -1; }
function filterCreditAcctDropdown(search) {
    search = (search || '').toLowerCase();
    document.querySelectorAll('#creditAcctList .credit-acct-item').forEach(function(el) {
        el.style.display = el.dataset.name.toLowerCase().includes(search) ? '' : 'none';
    });
    _creditAcctKbIdx = -1;
    document.querySelectorAll('#creditAcctList .credit-acct-item').forEach(function(el) { el.classList.remove('kb-active'); });
}
function selectCreditAcctItem(item) {
    document.getElementById('creditAccountInput').value = item.dataset.name;
    document.getElementById('creditAccountId').value = item.dataset.id;
    document.getElementById('creditAccountType').value = item.dataset.type;
    document.getElementById('creditAccountName').value = item.dataset.name;
    closeCreditAcctDropdown();
    setTimeout(function() { document.getElementById('chequeNo').focus(); }, 80);
}

function openAccountModal() {
    loadAccountList();
    document.getElementById('accountModalOverlay').classList.add('show');
    document.getElementById('accountSearch').value = '';
    document.getElementById('accountSearch').focus();
    /* Reset keyboard index */
    if (typeof modalKbIdx !== 'undefined') modalKbIdx = -1;
}

function closeAccountModal() {
    document.getElementById('accountModalOverlay').classList.remove('show');
}

function loadAccountList() {
    const type = document.getElementById('accountTypeFilter').value;
    const accounts = type === 'PL' ? purchaseLedgers : generalLedgers;
    const tbody = document.getElementById('accountListBody');
    tbody.innerHTML = '';
    accounts.forEach(acc => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        /* NO tabIndex — focus must stay on search box */
        row.innerHTML = `<td>${acc.alter_code || acc.account_code || ''}</td><td>${acc.ledger_name || acc.name || acc.account_name}</td>`;
        row.dataset.id = acc.id;
        row.dataset.code = acc.alter_code || acc.account_code || '';
        row.dataset.name = acc.ledger_name || acc.name || acc.account_name;
        row.dataset.type = type;
        row.onclick = () => {
            const visRows = modalVisibleRows();
            document.querySelectorAll('#accountListBody tr').forEach(r => r.classList.remove('table-primary','kb-hi'));
            row.classList.add('table-primary','kb-hi');
            modalKbIdx = visRows.indexOf(row);
            /* Return focus to search box after click */
            document.getElementById('accountSearch').focus();
        };
        row.ondblclick = () => selectAccount();
        tbody.appendChild(row);
    });
}

function filterAccountList() {
    const search = document.getElementById('accountSearch').value.toLowerCase();
    document.querySelectorAll('#accountListBody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
    /* Reset keyboard highlight */
    if (typeof modalKbIdx !== 'undefined') {
        modalKbIdx = -1;
        document.querySelectorAll('#accountListBody tr').forEach(r=>r.classList.remove('table-primary','kb-hi'));
    }
}

function selectAccount() {
    const selected = document.querySelector('#accountListBody tr.table-primary');
    if (!selected) { alert('Please select an account'); return; }
    
    // Use the pre-selected target row
    let targetRow = selectedAccountRow;
    if (!targetRow || targetRow.querySelector('.account-name').value) {
        // Find first empty row
        const rows = document.querySelectorAll('#accountsTableBody tr');
        for (let row of rows) {
            if (!row.querySelector('.account-name').value) {
                targetRow = row;
                break;
            }
        }
        // If no empty row, add new one
        if (!targetRow || targetRow.querySelector('.account-name').value) {
            addAccountRow();
            targetRow = document.querySelector('#accountsTableBody tr:last-child');
        }
    }
    
    targetRow.querySelector('.account-code').value = selected.dataset.code;
    targetRow.querySelector('.account-name').value = selected.dataset.name;
    targetRow.querySelector('.account-type').value = selected.dataset.type;
    targetRow.querySelector('.account-id').value = selected.dataset.id;

    closeAccountModal();
    selectedAccountRow = null;

    /* Focus the next empty row's code field */
    setTimeout(function() {
        var nextEmpty = null;
        var allRows = document.querySelectorAll('#accountsTableBody tr');
        var passedTarget = false;
        for (var r of allRows) {
            if (r === targetRow) { passedTarget = true; continue; }
            if (passedTarget && !r.querySelector('.account-name').value) {
                nextEmpty = r;
                break;
            }
        }
        // If no next empty row exists, check from beginning (unlikely) or use existing empty row
        if (!nextEmpty) {
            for (var r of allRows) {
                if (!r.querySelector('.account-name').value) {
                    nextEmpty = r;
                    break;
                }
            }
        }
        if (nextEmpty) {
            var codeInput = nextEmpty.querySelector('.account-code');
            if (codeInput) { codeInput.focus(); selectAccountRowEl(nextEmpty); }
        }
    }, 50);
}

function deleteAccount() {
    if (selectedAccountRow) { selectedAccountRow.remove(); selectedAccountRow = null; }
    else alert('Please select an account row to delete');
}

function deleteHsnRow() {
    if (selectedHsnRow) { selectedHsnRow.remove(); selectedHsnRow = null; calculateTotals(); }
    else alert('Please select an HSN row to delete');
}

function saveVoucher() {
    const items = [], accounts = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value;
        const amount = row.querySelector('.hsn-amount')?.value;
        if (hsnCode || amount) {
            items.push({
                hsn_code: hsnCode, amount: amount || 0,
                gst_percent: row.querySelector('.hsn-gst')?.value || 0,
                cgst_percent: row.querySelector('.hsn-cgst-pct')?.value || 0,
                cgst_amount: row.querySelector('.hsn-cgst-amt')?.value || 0,
                sgst_percent: row.querySelector('.hsn-sgst-pct')?.value || 0,
                sgst_amount: row.querySelector('.hsn-sgst-amt')?.value || 0,
            });
        }
    });
    document.querySelectorAll('#accountsTableBody tr').forEach(row => {
        const name = row.querySelector('.account-name')?.value;
        if (name) {
            accounts.push({
                account_type: row.querySelector('.account-type')?.value,
                account_id: row.querySelector('.account-id')?.value,
                account_code: row.querySelector('.account-code')?.value,
                account_name: name,
            });
        }
    });
    const supplierIdVal = document.getElementById('supplierId').value;
    const supplierNameVal = document.getElementById('supplierDisplay').value;

    const formData = {
        voucher_date: document.getElementById('voucherDate').value,
        bill_no: document.getElementById('billNo').value,
        bill_date: document.getElementById('billDate').value,
        local_inter: document.getElementById('localInter').value,
        rcm: document.getElementById('rcm').value,
        description: document.getElementById('description').value,
        supplier_id: supplierIdVal,
        supplier_name: supplierNameVal,
        gst_no: document.getElementById('gstNo').value,
        pan_no: document.getElementById('panNo').value,
        city: document.getElementById('city').value,
        pin: document.getElementById('pin').value,
        amount: parseFloat(document.getElementById('grossDisplay').value) || 0,
        total_gst: parseFloat(document.getElementById('totalGstDisplay').textContent) || 0,
        net_amount: parseFloat(document.getElementById('netAmtDisplay').textContent) || 0,
        round_off: parseFloat(document.getElementById('debitRoff').textContent) || 0,
        total_debit: parseFloat(document.getElementById('totalDebitAmt').textContent) || 0,
        tds_percent: parseFloat(document.getElementById('tdsPercent').value) || 0,
        tds_amount: parseFloat(document.getElementById('tdsAmount').value) || 0,
        payment_type: document.getElementById('paymentType').value,
        credit_account_id: document.getElementById('creditAccountId').value,
        credit_account_type: document.getElementById('creditAccountType').value,
        credit_account_name: document.getElementById('creditAccountName').value,
        cheque_no: document.getElementById('chequeNo').value,
        total_credit: parseFloat(document.getElementById('totalCreditAmt').textContent) || 0,
        total_cgst_amount: parseFloat(document.getElementById('totalCgstAmt').textContent) || 0,
        total_sgst_amount: parseFloat(document.getElementById('totalSgstAmt').textContent) || 0,
        items: items, accounts: accounts, _token: '<?php echo e(csrf_token()); ?>'
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('<?php echo e(route("admin.voucher-purchase.store")); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { alert('Voucher #' + data.voucher_no + ' saved successfully!'); window.location.reload(); }
        else alert('Error: ' + data.message);
    })
    .catch(e => { console.error('Error:', e); alert('Failed to save voucher'); });
}

function deleteVoucher() { alert('Delete functionality available in modification mode'); }
function printVoucher() { window.print(); }
function reverseVoucher() { alert('Reverse functionality available in modification mode'); }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/voucher-purchase/transaction.blade.php ENDPATH**/ ?>