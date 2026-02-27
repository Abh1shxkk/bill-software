<?php $__env->startSection('title', 'Deposit Slip'); ?>

<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-receipt me-2"></i> Deposit Slip</h4>
        <div class="text-muted small">Manage cheque deposits to bank</div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card-body border-bottom" style="background-color: #f0f0f0;">
        <form class="row g-2 align-items-end" id="filterForm" onsubmit="return false;">
            <div class="col-md-2">
                <label class="form-label small mb-1">Deposit / Clearing Date</label>
                <input type="date" class="form-control form-control-sm hdr-field" id="deposit_date" name="deposit_date"
                       value="<?php echo e(date('Y-m-d')); ?>" tabindex="1" autocomplete="off">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Cheques Upto</label>
                <input type="date" class="form-control form-control-sm hdr-field" id="cheques_upto" name="cheques_upto"
                       value="<?php echo e(date('Y-m-d')); ?>" tabindex="2" autocomplete="off">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Payin Slip Date</label>
                <input type="date" class="form-control form-control-sm hdr-field" id="payin_slip_date" name="payin_slip_date"
                       value="<?php echo e(date('Y-m-d')); ?>" tabindex="3" autocomplete="off">
            </div>
            <div class="col-md-3" style="position:relative;">
                <label class="form-label small mb-1">Bank</label>
                <input type="text" id="bank_display"
                       class="form-control form-control-sm hdr-field"
                       placeholder="Select Bank" tabindex="4"
                       style="cursor:pointer;background:#fff;caret-color:transparent;"
                       autocomplete="off">
                <input type="hidden" id="bank_id" name="bank_id" value="">
                <div id="bank_menu" style="
                    display:none;position:absolute;top:100%;left:0;right:0;
                    background:#fff;border:2px solid #0d6efd;border-radius:4px;
                    z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,.25);
                    max-height:180px;overflow-y:auto;">
                    <div class="bank-opt" data-val="" style="padding:6px 10px;cursor:pointer;font-size:12px;color:#888;">— Select Bank —</div>
                    <?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bank-opt" data-val="<?php echo e($bank->id); ?>"
                         style="padding:6px 10px;cursor:pointer;font-size:12px;"><?php echo e($bank->name); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <!-- D/N Wise — custom keyboard dropdown -->
            <div class="col-md-1" style="position:relative;">
                <label class="form-label small mb-1">D/N Wise</label>
                <input type="text" id="dn_display"
                       class="form-control form-control-sm text-center fw-bold hdr-field"
                       value="N" readonly tabindex="5"
                       style="cursor:pointer;background:#fff;caret-color:transparent;"
                       autocomplete="off">
                <input type="hidden" id="search_type" name="search_type" value="N">
                <!-- dropdown menu -->
                <div id="dn_menu" style="
                    display:none;position:absolute;top:100%;left:0;right:0;
                    background:#fff;border:2px solid #0d6efd;border-radius:4px;
                    z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,.2);overflow:hidden;">
                    <div class="dn-opt" data-val="N"
                         style="padding:7px 0;text-align:center;font-weight:700;font-size:13px;cursor:pointer;">N</div>
                    <div class="dn-opt" data-val="D"
                         style="padding:7px 0;text-align:center;font-weight:700;font-size:13px;cursor:pointer;">D</div>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Pay In Slip No</label>
                <input type="number" class="form-control form-control-sm" id="slip_no" name="slip_no"
                       value="<?php echo e($nextSlipNo); ?>" readonly tabindex="-1">
            </div>
        </form>
    </div>

    <!-- Cheque Table -->
    <div class="table-responsive" id="tableScroll" style="max-height: 300px; overflow-y: auto;">
        <table class="table table-sm table-bordered table-hover mb-0" id="chequeTable">
            <thead class="table-primary sticky-top">
                <tr>
                    <th style="width: 60px;">Code</th>
                    <th>Party Name</th>
                    <th style="width: 100px;">Cheque No</th>
                    <th style="width: 90px;">Date</th>
                    <th style="width: 100px;" class="text-end">Amount</th>
                    <th style="width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody id="cheque-table-body">
                <?php $__empty_1 = true; $__currentLoopData = $chequeData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cheque): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr data-id="<?php echo e($cheque['id']); ?>" data-cheque='<?php echo json_encode($cheque, 15, 512) ?>'
                    class="cheque-row <?php echo e($cheque['status'] === 'posted' ? 'table-success' : ''); ?>"
                    style="cursor: pointer;">
                    <td><?php echo e($cheque['customer_code'] ?? '---'); ?></td>
                    <td class="text-primary fw-bold"><?php echo e($cheque['customer_name'] ?? '-'); ?></td>
                    <td><?php echo e($cheque['cheque_no']); ?></td>
                    <td><?php echo e($cheque['cheque_date']); ?></td>
                    <td class="text-end"><?php echo e(number_format($cheque['amount'], 2)); ?></td>
                    <td>
                        <?php if($cheque['status'] === 'posted'): ?>
                            <span class="badge bg-success">POSTED</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">PENDING</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">No cheques found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary Row -->
    <div class="card-body border-top border-bottom py-2" style="background-color: #e0e0e0;">
        <div class="row align-items-center">
            <div class="col-md-4">
                <span class="fw-bold">TRN : </span>
                <span id="selected_trn">-</span>
                <span class="ms-3" id="selected_date">-</span>
                <span class="ms-3" id="selected_bank">-</span>
            </div>
            <div class="col-md-4 text-center">
                <span class="fw-bold text-success">TOTAL : </span>
                <span id="total_amount" class="fw-bold">0.00</span>
                <span class="ms-4 text-primary">No. : (<span id="total_count">0</span>)</span>
                <span class="ms-2 text-warning">Un-Posted : <span id="unposted_count">0</span></span>
                <span class="ms-2 text-success">Posted : (<span id="posted_count">0</span>)</span>
            </div>
            <div class="col-md-4 text-end">
                <span class="fw-bold" id="summary_total">0.00</span>
            </div>
        </div>
    </div>

    <!-- Bottom Sections -->
    <div class="row g-0">
        <div class="col-md-6 border-end">
            <div class="p-2" style="background-color: #00ffff;">
                <strong>1 ) Amt. Outstanding &nbsp;&nbsp; Total :</strong>
                <span id="outstanding_total" class="float-end">0.00</span>
            </div>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr><th>Type</th><th>Ref No</th><th>Date</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody id="outstanding-body">
                        <tr><td colspan="4" class="text-center text-muted py-3">No outstanding items</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-2" style="background-color: #00ffff;">
                <strong>2 ) Amt. Adjusted &nbsp;&nbsp; Total :</strong>
                <span id="adjusted_total" class="float-end">0.00</span>
            </div>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr><th>Type</th><th>Ref No</th><th>Date</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody id="adjusted-body">
                        <tr><td colspan="4" class="text-center text-muted py-3">No adjusted items</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button type="button" class="btn btn-success btn-sm action-btn" id="btn-post">
                    <i class="bi bi-check-circle me-1"></i> Post Selected
                </button>
                <button type="button" class="btn btn-warning btn-sm ms-2 action-btn" id="btn-unpost">
                    <i class="bi bi-x-circle me-1"></i> Unpost Selected
                </button>
            </div>
            <div>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-refresh" tabindex="-1">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ── Selected row ── */
    .cheque-row.selected {
        background-color: #cfe2ff !important;
        box-shadow: 0 0 0 3px rgba(13,110,253,.5);
        position: relative; z-index: 1;
    }
    .cheque-row.selected td                { border-top: 2px solid #0d6efd !important; border-bottom: 2px solid #0d6efd !important; }
    .cheque-row.selected td:first-child    { border-left:  2px solid #0d6efd !important; }
    .cheque-row.selected td:last-child     { border-right: 2px solid #0d6efd !important; }
    .table-success { background-color: #d1e7dd !important; }
    .table-success.selected { background-color: #b6d4fe !important; }
    .cheque-row:hover { background-color: #e9ecef; }

    /* ── Keyboard highlight on rows ── */
    .cheque-row.kb-hi { background-color: #fff3cd !important; outline: 2px solid #ffc107; }
    .cheque-row.kb-hi td { border-top: 2px solid #ffc107 !important; border-bottom: 2px solid #ffc107 !important; }

    /* ── Blue focus rings on header fields ── */
    .hdr-field:focus {
        outline: none !important;
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13,110,253,.22) !important;
        border-radius: 4px;
    }
    #dn_display.dn-open {
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13,110,253,.22) !important;
    }

    /* ── D/N dropdown options ── */
    .dn-opt:hover, .dn-opt.dn-hi { background: #0d6efd !important; color: #fff !important; }
    .bank-opt:hover, .bank-opt.bank-hi { background: #0d6efd !important; color: #fff !important; }
    #bank_display:focus { outline:none !important; border:2px solid #0d6efd !important; box-shadow:0 0 0 3px rgba(13,110,253,.22) !important; }
    #bank_display.bank-open { border:2px solid #0d6efd !important; box-shadow:0 0 0 3px rgba(13,110,253,.22) !important; }

    /* ── Action button keyboard focus ── */
    .action-btn.kb-btn { outline: 3px solid #0d6efd; outline-offset: 2px; box-shadow: 0 0 0 4px rgba(13,110,253,.25); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ══════════════════════════════════════════════
       STATE
    ══════════════════════════════════════════════ */
    var selectedCheque = null;
    var chequeData     = <?php echo json_encode($chequeData, 15, 512) ?>;
    var kbRow          = -1;   // keyboard-highlighted row index
    var kbBtn          = -1;   // 0=Post, 1=Unpost
    var ACTION_IDS     = ['btn-post', 'btn-unpost'];
    var inTableMode    = false; // true when arrow keys navigate table

    /* ══════════════════════════════════════════════
       TOTALS
    ══════════════════════════════════════════════ */
    function calcTotals() {
        var t = 0, p = 0, u = 0;
        chequeData.forEach(function (c) {
            t += parseFloat(c.amount) || 0;
            c.status === 'posted' ? p++ : u++;
        });
        document.getElementById('total_amount').textContent   = t.toFixed(2);
        document.getElementById('total_count').textContent    = chequeData.length;
        document.getElementById('posted_count').textContent   = p;
        document.getElementById('unposted_count').textContent = u;
        document.getElementById('summary_total').textContent  = t.toFixed(2);
    }
    calcTotals();

    /* ══════════════════════════════════════════════
       ROW HELPERS
    ══════════════════════════════════════════════ */
    function visibleRows() {
        return Array.from(document.querySelectorAll('.cheque-row'))
                    .filter(function (r) { return r.style.display !== 'none'; });
    }

    function clearRowHighlights() {
        document.querySelectorAll('.cheque-row').forEach(function (r) {
            r.classList.remove('selected', 'kb-hi');
        });
    }

    /* Highlight (yellow) without selecting */
    function kbHighlight(idx) {
        var rows = visibleRows();
        document.querySelectorAll('.cheque-row.kb-hi').forEach(function (r) { r.classList.remove('kb-hi'); });
        if (idx < 0 || idx >= rows.length) return;
        rows[idx].classList.add('kb-hi');
        rows[idx].scrollIntoView({ block: 'nearest' });
    }

    /* Select (blue) — confirms the row */
    function selectRow(idx) {
        var rows = visibleRows();
        if (idx < 0 || idx >= rows.length) return;
        clearRowHighlights();
        rows[idx].classList.add('selected', 'kb-hi');
        rows[idx].scrollIntoView({ block: 'nearest' });
        selectedCheque = JSON.parse(rows[idx].dataset.cheque);
        document.getElementById('selected_trn').textContent  = selectedCheque.trn_no     || '-';
        document.getElementById('selected_date').textContent = selectedCheque.cheque_date || '-';
        document.getElementById('selected_bank').textContent = selectedCheque.bank_name   || '-';
        displayAdjustments(selectedCheque);
        kbRow = idx;
    }

    /* Mouse click */
    document.querySelectorAll('.cheque-row').forEach(function (row) {
        row.addEventListener('click', function () {
            var rows = visibleRows();
            kbRow = rows.indexOf(this);
            selectRow(kbRow);
            inTableMode = true;
        });
    });

    /* ══════════════════════════════════════════════
       ACTION BUTTON HELPERS
    ══════════════════════════════════════════════ */
    function clearBtnFocus() {
        ACTION_IDS.forEach(function (id) {
            document.getElementById(id).classList.remove('kb-btn');
        });
        kbBtn = -1;
    }

    function focusBtn(idx) {
        clearBtnFocus();
        if (idx < 0 || idx >= ACTION_IDS.length) return;
        kbBtn = idx;
        var btn = document.getElementById(ACTION_IDS[idx]);
        btn.classList.add('kb-btn');
        btn.focus();
    }

    /* ══════════════════════════════════════════════
       D/N CUSTOM DROPDOWN
    ══════════════════════════════════════════════ */
    var dnDisp = document.getElementById('dn_display');
    var dnHid  = document.getElementById('search_type');
    var dnMenu = document.getElementById('dn_menu');
    var dnOpts = Array.from(dnMenu.querySelectorAll('.dn-opt'));
    var dnIdx  = 0;  /* 0=N, 1=D */

    function dnIsOpen() { return dnMenu.style.display !== 'none'; }

    function dnOpen() {
        dnMenu.style.display = 'block';
        dnDisp.classList.add('dn-open');
        dnIdx = dnOpts.findIndex(function (o) { return o.dataset.val === dnHid.value; });
        if (dnIdx < 0) dnIdx = 0;
        dnOpts.forEach(function (o, i) { o.classList.toggle('dn-hi', i === dnIdx); });
    }

    function dnClose() { dnMenu.style.display = 'none'; dnDisp.classList.remove('dn-open'); }

    function dnSelect(val) {
        dnHid.value  = val;
        dnDisp.value = val;
        dnClose();
    }

    dnDisp.addEventListener('click', function () { dnIsOpen() ? dnClose() : dnOpen(); });

    dnDisp.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation();
            if (!dnIsOpen()) { dnOpen(); return; }
            dnIdx = Math.min(dnIdx + 1, dnOpts.length - 1);
            dnOpts.forEach(function (o, i) { o.classList.toggle('dn-hi', i === dnIdx); });
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation();
            if (!dnIsOpen()) { dnOpen(); return; }
            dnIdx = Math.max(dnIdx - 1, 0);
            dnOpts.forEach(function (o, i) { o.classList.toggle('dn-hi', i === dnIdx); });
            return;
        }
        if (e.key === 'Escape') { e.preventDefault(); dnClose(); return; }
        if (e.key === 'n' || e.key === 'N') { e.preventDefault(); dnSelect('N'); return; }
        if (e.key === 'd' || e.key === 'D') { e.preventDefault(); dnSelect('D'); return; }
        if (e.key === 'Enter' || e.code === 'NumpadEnter' || e.key === ' ') {
            e.preventDefault(); e.stopPropagation();
            if (dnIsOpen()) {
                /* Confirm selected option then go to table */
                dnSelect(dnOpts[dnIdx].dataset.val);
                enterTableMode();
            } else {
                dnOpen();
            }
            return;
        }
        if (e.key === 'Tab') {
            dnClose();
            /* Tab → go to table */
            e.preventDefault();
            enterTableMode();
        }
    });

    /* Guard: when D/N menu is open, trap keys at capture phase.
       Prevents page scroll/global handlers from stealing Enter. */
    window.addEventListener('keydown', function (e) {
        if (!dnIsOpen()) return;
        var k = e.key;
        var isEnter = (k === 'Enter' || e.code === 'NumpadEnter');
        var managed = ['ArrowDown', 'ArrowUp', 'Escape', 'Tab', ' '];
        if (!isEnter && managed.indexOf(k) === -1) return;

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (k === 'ArrowDown') {
            dnIdx = Math.min(dnIdx + 1, dnOpts.length - 1);
            dnOpts.forEach(function (o, i) { o.classList.toggle('dn-hi', i === dnIdx); });
            return;
        }
        if (k === 'ArrowUp') {
            dnIdx = Math.max(dnIdx - 1, 0);
            dnOpts.forEach(function (o, i) { o.classList.toggle('dn-hi', i === dnIdx); });
            return;
        }
        if (k === 'Escape') {
            dnClose();
            return;
        }
        if (isEnter || k === ' ' || k === 'Tab') {
            if (dnOpts[dnIdx]) dnSelect(dnOpts[dnIdx].dataset.val);
            enterTableMode();
            return;
        }
    }, true);

    dnOpts.forEach(function (opt) {
        opt.addEventListener('click', function () { dnSelect(this.dataset.val); });
        opt.addEventListener('mouseenter', function () {
            dnIdx = dnOpts.indexOf(this);
            dnOpts.forEach(function (o, i) { o.classList.toggle('dn-hi', i === dnIdx); });
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#dn_display') && !e.target.closest('#dn_menu')) dnClose();
    });
    /* ══════════════════════════════════════════════
       BANK CUSTOM DROPDOWN
    ══════════════════════════════════════════════ */
    var bankDisp  = document.getElementById('bank_display');
    var bankHid   = document.getElementById('bank_id');
    var bankMenu  = document.getElementById('bank_menu');
    var bankOpts  = Array.from(bankMenu.querySelectorAll('.bank-opt'));
    var bankIdx   = 0;   /* currently highlighted index */
    var bankSearch = ''; /* quick-search typed chars */
    var bankSearchTimer = null;

    function bankIsOpen()  { return bankMenu.style.display !== 'none'; }

    function bankOpen() {
        bankMenu.style.display = 'block';
        bankDisp.classList.add('bank-open');
        /* highlight currently selected */
        bankIdx = bankOpts.findIndex(function(o){ return o.dataset.val === bankHid.value; });
        if (bankIdx < 0) bankIdx = 0;
        bankHilight(bankIdx);
        /* scroll to highlighted */
        var hi = bankOpts[bankIdx]; if(hi) hi.scrollIntoView({ block:'nearest' });
    }

    function bankClose() { bankMenu.style.display = 'none'; bankDisp.classList.remove('bank-open'); }

    function bankHilight(idx) {
        bankOpts.forEach(function(o,i){ o.classList.toggle('bank-hi', i===idx); });
        if (bankOpts[idx]) bankOpts[idx].scrollIntoView({ block:'nearest' });
    }

    function bankSelect(val, label) {
        bankHid.value  = val;
        bankDisp.value = label || '';
        bankClose();
    }

    function bankSelectAndNext(val, label) {
        _bankSkipFocus = true;   /* prevent focus handler from reopening */
        bankSelect(val, label);
        document.getElementById('dn_display').focus();
    }

    /* Block typing — display only, values come from dropdown */

    /* Focus → auto open dropdown (works from Enter chain AND tab) */
    var _bankSkipFocus = false;
    bankDisp.addEventListener('focus', function() {
        if (_bankSkipFocus) { _bankSkipFocus = false; return; }
        setTimeout(function() { if (!bankIsOpen()) bankOpen(); }, 0);
    });

    bankDisp.addEventListener('click', function() { bankIsOpen() ? bankClose() : bankOpen(); });

    /* Single unified keydown handler for bank dropdown */
    bankDisp.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!bankIsOpen()) { bankOpen(); return; }
            bankIdx = Math.min(bankIdx + 1, bankOpts.length - 1);
            bankHilight(bankIdx); return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!bankIsOpen()) { bankOpen(); return; }
            bankIdx = Math.max(bankIdx - 1, 0);
            bankHilight(bankIdx); return;
        }
        if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (bankIsOpen()) {
                var opt = bankOpts[bankIdx];
                if (opt) bankSelectAndNext(opt.dataset.val, opt.textContent.trim());
            } else {
                bankOpen();
            }
            return;
        }
        if (e.key === ' ') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            bankIsOpen() ? (function(){ var opt=bankOpts[bankIdx]; if(opt) bankSelectAndNext(opt.dataset.val, opt.textContent.trim()); })() : bankOpen();
            return;
        }
        if (e.key === 'Escape') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (bankIsOpen()) { bankClose(); return; }
            return;
        }
        if (e.key === 'Tab') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (bankIsOpen()) {
                var opt = bankOpts[bankIdx];
                if (opt) bankSelectAndNext(opt.dataset.val, opt.textContent.trim());
            } else {
                _bankSkipFocus = true;
                document.getElementById('dn_display').focus();
            }
            return;
        }
        if (e.key === 'Backspace' || e.key === 'Delete') { e.preventDefault(); return; }
        /* Quick-search: typing letters jump to matching bank */
        if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
            e.preventDefault();
            if (!bankIsOpen()) bankOpen();
            bankSearch += e.key.toLowerCase();
            clearTimeout(bankSearchTimer);
            bankSearchTimer = setTimeout(function () { bankSearch = ''; }, 800);
            var found = bankOpts.findIndex(function (o) {
                return o.textContent.trim().toLowerCase().startsWith(bankSearch);
            });
            if (found >= 0) { bankIdx = found; bankHilight(bankIdx); }
        }
    });

    /* Guard: when bank menu is open, trap keys at capture phase.
       Prevents page scroll / global handlers from hijacking Enter. */
    window.addEventListener('keydown', function (e) {
        if (!bankIsOpen()) return;
        var k = e.key;
        var managed = ['ArrowDown', 'ArrowUp', 'Enter', ' ', 'Escape', 'Tab'];
        if (managed.indexOf(k) === -1) return;

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (k === 'ArrowDown') {
            bankIdx = Math.min(bankIdx + 1, bankOpts.length - 1);
            bankHilight(bankIdx);
            return;
        }
        if (k === 'ArrowUp') {
            bankIdx = Math.max(bankIdx - 1, 0);
            bankHilight(bankIdx);
            return;
        }
        if (k === 'Escape') {
            bankClose();
            return;
        }
        if (k === 'Enter' || k === ' ' || k === 'Tab') {
            var opt = bankOpts[bankIdx];
            if (opt) bankSelectAndNext(opt.dataset.val, opt.textContent.trim());
            return;
        }
    }, true);

    bankOpts.forEach(function(opt, i) {
        opt.addEventListener('click', function() {
            bankSelect(this.dataset.val, this.textContent.trim());
        });
        opt.addEventListener('mouseenter', function() {
            bankIdx = i; bankHilight(bankIdx);
        });
    });

    /* Close bank menu on outside click */
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#bank_display') && !e.target.closest('#bank_menu')) bankClose();
    });

    /* ══════════════════════════════════════════════
       ENTER TABLE MODE
    ══════════════════════════════════════════════ */
    function enterTableMode() {
        inTableMode = true;
        kbBtn = -1;
        clearBtnFocus();
        if (kbRow < 0) kbRow = 0;
        kbHighlight(kbRow);
        /* Blur current focus so arrow keys go to our global handler */
        if (document.activeElement) document.activeElement.blur();
    }

    /* ══════════════════════════════════════════════
       HEADER ENTER CHAIN (capture phase, reliable on date inputs)
    ══════════════════════════════════════════════ */
    var HDR_CHAIN = ['deposit_date', 'cheques_upto', 'payin_slip_date', 'bank_id', 'dn_display'];

    /* Direct listeners on each field (more reliable than global for date inputs) */
    document.getElementById('deposit_date').addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault(); e.stopImmediatePropagation();
        document.getElementById('cheques_upto').focus();
    });
    document.getElementById('cheques_upto').addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault(); e.stopImmediatePropagation();
        document.getElementById('payin_slip_date').focus();
    });
    /* payin_slip_date → bank_display  (capture + setTimeout beats date-picker's own Enter) */
    document.getElementById('payin_slip_date').addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        setTimeout(function () {
            document.getElementById('bank_display').focus();
        }, 30);
    }, true);
    /* bank Enter handled in bankDisp keydown above */
    /* dn_display Enter is handled inside dnDisp.addEventListener above */

    /* ══════════════════════════════════════════════
       GLOBAL ARROW + ENTER (table & action btns)
    ══════════════════════════════════════════════ */
    /* ═══════════════════════════════════════════════════════
       GLOBAL KEYBOARD — capture phase
       Works purely on flag state, no table.focus() dependency
    ═══════════════════════════════════════════════════════ */
    window.addEventListener('keydown', function (e) {
        var ae  = document.activeElement;
        var onBtn = ae && ACTION_IDS.indexOf(ae.id) >= 0;

        /* Skip if a header date/select/input is focused (they have own handlers) */
        var hdrIds = ['deposit_date','cheques_upto','payin_slip_date','bank_display','dn_display','slip_no'];
        if (ae && hdrIds.indexOf(ae.id) >= 0) return;

        /* Skip if D/N or Bank menu open */
        if (dnIsOpen() || bankIsOpen()) return;

        /* ── ArrowDown / ArrowUp → navigate rows (when inTableMode OR btn not focused) ── */
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            if (onBtn) return;                /* buttons handle their own arrow */
            e.preventDefault(); e.stopPropagation();
            inTableMode = true;
            kbBtn = -1; clearBtnFocus();
            var rows = visibleRows();
            if (!rows.length) return;
            if (e.key === 'ArrowDown') kbRow = (kbRow < 0) ? 0 : Math.min(kbRow + 1, rows.length - 1);
            else                       kbRow = (kbRow < 0) ? 0 : Math.max(kbRow - 1, 0);
            kbHighlight(kbRow);
            return;
        }

        /* ── Enter ── */
        if (e.key === 'Enter') {
            /* On action button → click */
            if (onBtn) {
                e.preventDefault();
                document.getElementById(ae.id).click();
                return;
            }
            /* In table mode → confirm selected row */
            if (inTableMode && kbRow >= 0 && !onBtn) {
                e.preventDefault();
                selectRow(kbRow);
                setTimeout(function () { focusBtn(0); }, 30);
                return;
            }
        }

        /* ── ArrowRight / ArrowLeft → Post ↔ Unpost ── */
        if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
            /* Only when button focused OR kbBtn already active */
            if (onBtn || kbBtn >= 0) {
                e.preventDefault();
                var cur  = (kbBtn >= 0) ? kbBtn : ACTION_IDS.indexOf(ae.id);
                var next = e.key === 'ArrowRight' ? cur + 1 : cur - 1;
                next = Math.max(0, Math.min(next, ACTION_IDS.length - 1));
                focusBtn(next);
                return;
            }
        }

        /* ── Escape → back to table mode ── */
        if (e.key === 'Escape') {
            if (onBtn || kbBtn >= 0) {
                clearBtnFocus();
                inTableMode = true;
                if (kbRow >= 0) kbHighlight(kbRow);
            }
            return;
        }
    }, true);

    /* ══════════════════════════════════════════════
       ADJUSTMENTS DISPLAY
    ══════════════════════════════════════════════ */
    function displayAdjustments(cheque) {
        var outBody = document.getElementById('outstanding-body');
        var adjBody = document.getElementById('adjusted-body');
        var outHtml = '', adjHtml = '', outTotal = 0, adjTotal = 0;
        var adjustments = cheque.adjustments || [];

        adjustments.forEach(function (adj) {
            if (adj.balance_amount > 0) {
                outTotal += adj.balance_amount;
                outHtml  += '<tr><td>' + (adj.adjustment_type||'-') + '</td><td>' + (adj.reference_no||'-') + '</td><td>' + (adj.reference_date||'-') + '</td><td class="text-end">' + parseFloat(adj.balance_amount).toFixed(2) + '</td></tr>';
            }
            if (adj.adjusted_amount > 0) {
                adjTotal += adj.adjusted_amount;
                adjHtml  += '<tr><td>' + (adj.adjustment_type||'-') + '</td><td>' + (adj.reference_no||'-') + '</td><td>' + (adj.reference_date||'-') + '</td><td class="text-end">' + parseFloat(adj.adjusted_amount).toFixed(2) + '</td></tr>';
            }
        });

        if (cheque.unadjusted && cheque.unadjusted > 0) {
            outTotal += cheque.unadjusted;
            outHtml  += '<tr><td>Unadjusted</td><td>-</td><td>-</td><td class="text-end">' + parseFloat(cheque.unadjusted).toFixed(2) + '</td></tr>';
        }

        outBody.innerHTML = outHtml || '<tr><td colspan="4" class="text-center text-muted py-3">No outstanding items</td></tr>';
        adjBody.innerHTML = adjHtml || '<tr><td colspan="4" class="text-center text-muted py-3">No adjusted items</td></tr>';
        document.getElementById('outstanding_total').textContent = outTotal.toFixed(2);
        document.getElementById('adjusted_total').textContent    = adjTotal.toFixed(2);
    }

    /* ══════════════════════════════════════════════
       POST CHEQUE
    ══════════════════════════════════════════════ */
    document.getElementById('btn-post').addEventListener('click', function () {
        if (!selectedCheque) { alert('Please select a cheque to post'); return; }
        if (selectedCheque.status === 'posted') { alert('This cheque is already posted'); return; }
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Posting...';
        var bankId     = (document.getElementById('bank_id').value || '').trim();
        var bankName   = (document.getElementById('bank_display').value || '').trim();
        if (!bankId) {
            alert('Please select bank');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Post Selected';
            return;
        }
        fetch('<?php echo e(route("admin.deposit-slip.store")); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json', 'Accept': 'application/json'
            },
            body: JSON.stringify({
                customer_receipt_item_id: selectedCheque.id,
                slip_no:         document.getElementById('slip_no').value,
                deposit_date:    document.getElementById('deposit_date').value,
                clearing_date:   document.getElementById('deposit_date').value,
                payin_slip_date: document.getElementById('payin_slip_date').value,
                bank_id:         bankId,
                bank_name:       bankName
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) { location.reload(); }
            else { alert('Error: ' + (data.message || 'Unknown error')); btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Post Selected'; }
        })
        .catch(function () { alert('Error processing request'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Post Selected'; });
    });

    /* ══════════════════════════════════════════════
       UNPOST CHEQUE
    ══════════════════════════════════════════════ */
    document.getElementById('btn-unpost').addEventListener('click', function () {
        if (!selectedCheque) { alert('Please select a cheque to unpost'); return; }
        if (selectedCheque.status !== 'posted') { alert('This cheque is not posted'); return; }
        if (!selectedCheque.deposit_slip_id) { alert('Deposit slip not found'); return; }
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Unposting...';
        fetch('<?php echo e(route("admin.deposit-slip.unpost")); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json', 'Accept': 'application/json'
            },
            body: JSON.stringify({ deposit_slip_id: selectedCheque.deposit_slip_id })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) { location.reload(); }
            else { alert('Error: ' + (data.message || 'Unknown error')); btn.disabled = false; btn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Unpost Selected'; }
        })
        .catch(function () { alert('Error processing request'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Unpost Selected'; });
    });

    /* ══════════════════════════════════════════════
       REFRESH & FILTER
    ══════════════════════════════════════════════ */
    document.getElementById('btn-refresh').addEventListener('click', function () { location.reload(); });

    document.getElementById('cheques_upto').addEventListener('change', function () { filterCheques(); });

    function filterCheques() {
        var upto = document.getElementById('cheques_upto').value;
        document.querySelectorAll('.cheque-row').forEach(function (row) {
            var cheque = JSON.parse(row.dataset.cheque);
            var d      = cheque.cheque_date_raw;
            row.style.display = (!upto || !d || d <= upto) ? '' : 'none';
        });
        /* reset kb row if currently highlighted row is now hidden */
        var rows = visibleRows();
        if (kbRow >= rows.length) kbRow = rows.length - 1;
    }

    /* ══════════════════════════════════════════════
       KILL SELECT2 if it was applied anywhere
    ══════════════════════════════════════════════ */
    setTimeout(function () {
        /* Remove any Select2 containers injected next to bank_id or search_type */
        document.querySelectorAll('.select2-container').forEach(function(el){ el.style.display='none'; });
        /* Restore original hidden inputs */
        ['bank_id','search_type'].forEach(function(id){
            var el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('select2-hidden-accessible');
            el.removeAttribute('data-select2-id');
        });
        if (window.jQuery && $.fn && $.fn.select2) {
            try { $('#bank_id').select2('destroy'); } catch(e){}
            try { $('#search_type').select2('destroy'); } catch(e){}
        }
    }, 200);

    /* ══════════════════════════════════════════════
       AUTO-FOCUS first header field on load
    ══════════════════════════════════════════════ */
    setTimeout(function () { document.getElementById('deposit_date').focus(); }, 100);

});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/deposit-slip/index.blade.php ENDPATH**/ ?>