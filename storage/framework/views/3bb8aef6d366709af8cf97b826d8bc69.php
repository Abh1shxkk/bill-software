<?php $__env->startSection('title', 'Cash Deposited / Withdrawn from Bank'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .bank-form { background: #e0e0e0; padding: 20px; }
    .bank-form-inner { background: #f5f5f5; border: 1px solid #999; padding: 20px; max-width: 600px; margin: 0 auto; }
    .form-title { text-align: center; font-weight: bold; font-size: 16px; color: #000; margin-bottom: 20px; }
    .form-row { display: flex; align-items: center; margin-bottom: 12px; }
    .form-label { width: 140px; font-weight: 600; font-size: 13px; }
    .form-input { flex: 1; }
    .form-input input, .form-input select { font-size: 13px; padding: 5px 10px; height: 30px; border: 1px solid #999; }
    .form-input input:focus, .form-input select:focus { outline: none; border-color: #666; }
    .day-display { margin-left: 15px; font-size: 13px; font-weight: 600; }
    .footer-section { text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #ccc; }
    .btn-action { font-size: 13px; padding: 8px 30px; min-width: 100px; }

    /* Focus ring for fields */
    #transactionDate:focus, #transactionType:focus, #chequeNo:focus, #amount:focus, #narration:focus,
    #bankDisplay:focus {
        outline: none !important;
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13,110,253,.20) !important;
    }

    /* Custom Bank Dropdown */
    #bankDisplay {
        background: #fff; cursor: pointer; caret-color: transparent;
        width: 350px; font-size: 13px; padding: 5px 10px; height: 30px; border: 1px solid #999;
    }
    #bankMenu {
        display: none; position: absolute; top: 100%; left: 0; width: 380px;
        background: #fff; border: 2px solid #0d6efd; border-radius: 4px;
        z-index: 99999; box-shadow: 0 4px 16px rgba(0,0,0,.25);
        max-height: 220px; overflow-y: auto;
    }
    #bankSearchInput {
        width: 100%; padding: 6px 8px; border: none; border-bottom: 1px solid #ccc;
        font-size: 12px; outline: none;
    }
    .bank-opt { padding: 6px 10px; cursor: pointer; font-size: 12px; }
    .bank-opt:hover, .bank-opt.bank-hi { background: #0d6efd !important; color: #fff !important; }

    /* Button focus */
    .btn-action:focus { outline: 2px solid #0d6efd !important; box-shadow: 0 0 0 3px rgba(13,110,253,.25) !important; }
</style>

<div class="bank-form">
    <div class="bank-form-inner">
        <div class="form-title">-: CASH DEPOSITED / WITHDRAWN FROM BANK :-</div>
        
        <form id="transactionForm" autocomplete="off">
            <?php echo csrf_field(); ?>
            <div class="form-row">
                <div class="form-label">Date :</div>
                <div class="form-input">
                    <input type="date" name="transaction_date" id="transactionDate" value="<?php echo e(date('Y-m-d')); ?>" style="width: 150px;" onchange="updateDayName()" data-custom-enter>
                    <span class="day-display" id="dayName"><?php echo e(date('l')); ?></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-label">D(eposited) / W(ithdrawn) :</div>
                <div class="form-input">
                    <input type="text" name="transaction_type" id="transactionType" value="D" maxlength="1" style="width: 40px; text-transform: uppercase; text-align: center;" readonly data-custom-enter>
                    <span id="typeLabel" style="margin-left: 10px; font-size: 12px; color: #666;">(↑↓ to toggle)</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-label">Bank :</div>
                <div class="form-input" style="position: relative;">
                    
                    <input type="text" id="bankDisplay" placeholder="Select an option" readonly autocomplete="off" data-custom-enter>
                    <input type="hidden" name="bank_id" id="bankId" value="">
                    <input type="hidden" id="bankNameHidden" value="">
                    <div id="bankMenu">
                        <input type="text" id="bankSearchInput" placeholder="Type to search..." data-custom-enter>
                        <div id="bankOpts">
                            <div class="bank-opt" data-val="" data-name="" style="color:#888;">--- Select Bank ---</div>
                            <?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bank-opt"
                                 data-val="<?php echo e($bank->id); ?>"
                                 data-name="<?php echo e($bank->name); ?>"><?php echo e($bank->alter_code ?? $bank->id); ?> - <?php echo e($bank->name); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-label">Cheque No. :</div>
                <div class="form-input">
                    <input type="text" name="cheque_no" id="chequeNo" style="width: 200px;" data-custom-enter>
                </div>
            </div>

            <div class="form-row">
                <div class="form-label">Amount :</div>
                <div class="form-input">
                    <input type="number" name="amount" id="amount" step="0.01" style="width: 200px;" data-custom-enter>
                </div>
            </div>

            <div class="form-row" style="margin-top: 20px;">
                <div class="form-label">Narration :</div>
                <div class="form-input">
                    <input type="text" name="narration" id="narration" style="width: 100%;" data-custom-enter>
                </div>
            </div>

            <div class="footer-section">
                <button type="button" class="btn btn-primary btn-action" id="btnSave" onclick="saveTransaction()">Save</button>
                <a href="<?php echo e(route('admin.bank-transaction.index')); ?>" class="btn btn-secondary btn-action ms-2" id="btnExit">Exit (Esc)</a>
            </div>
        </form>
    </div>
</div>

<script>
const banks = <?php echo json_encode($banks, 15, 512) ?>;

document.addEventListener('DOMContentLoaded', function() {
    updateDayName();

    /* ══════════════════════════════════════════════════════
       BANK CUSTOM DROPDOWN
    ══════════════════════════════════════════════════════ */
    const bankDisp   = document.getElementById('bankDisplay');
    const bankHid    = document.getElementById('bankId');
    const bankNameH  = document.getElementById('bankNameHidden');
    const bankMenu   = document.getElementById('bankMenu');
    const bankSearch = document.getElementById('bankSearchInput');
    const bankOptsW  = document.getElementById('bankOpts');
    let bankIdx = 0;

    function bankOpts() { return Array.from(bankOptsW.querySelectorAll('.bank-opt')).filter(o => o.style.display !== 'none'); }
    function bankIsOpen() { return bankMenu.style.display !== 'none'; }

    function bankOpen() {
        bankMenu.style.display = 'block';
        bankSearch.value = '';
        filterBankOpts('');
        bankIdx = 0;
        bankHilight(0);
        setTimeout(() => bankSearch.focus(), 0);
    }
    function bankClose() { bankMenu.style.display = 'none'; }

    function filterBankOpts(term) {
        Array.from(bankOptsW.querySelectorAll('.bank-opt')).forEach(o => {
            o.style.display = (!term || o.textContent.toLowerCase().includes(term.toLowerCase())) ? '' : 'none';
        });
        bankIdx = 0; bankHilight(0);
    }

    function bankHilight(idx) {
        const opts = bankOpts();
        opts.forEach((o, i) => o.classList.toggle('bank-hi', i === idx));
        if (opts[idx]) opts[idx].scrollIntoView({ block: 'nearest' });
        bankIdx = idx;
    }

    function bankSelectAndNext(val, label, name) {
        bankHid.value  = val;
        bankDisp.value = label;
        bankNameH.value = name;
        bankClose();
        /* Move to Cheque No */
        setTimeout(() => { document.getElementById('chequeNo').focus(); }, 50);
    }

    bankDisp.addEventListener('click', function() { bankIsOpen() ? bankClose() : bankOpen(); });
    bankDisp.addEventListener('focus', function() {
        if (!bankIsOpen()) setTimeout(() => bankOpen(), 0);
    });
    bankDisp.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); bankClose(); return; }
        if (e.key === 'Enter' || e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!bankIsOpen()) bankOpen();
        }
    }, true);

    /* Search input inside dropdown */
    bankSearch.addEventListener('input', function() { filterBankOpts(this.value); });
    bankSearch.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const opts = bankOpts(); bankIdx = Math.min(bankIdx + 1, opts.length - 1); bankHilight(bankIdx);
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            bankIdx = Math.max(bankIdx - 1, 0); bankHilight(bankIdx);
            return;
        }
        if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const opts = bankOpts(); const opt = opts[bankIdx];
            if (opt) bankSelectAndNext(opt.dataset.val, opt.textContent.trim(), opt.dataset.name);
            return;
        }
        if (e.key === 'Escape') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            bankClose(); bankDisp.focus();
            return;
        }
    }, true);

    /* Mouse interactions for bank options */
    bankOptsW.addEventListener('mouseover', function(e) {
        const opt = e.target.closest('.bank-opt');
        if (opt) { bankIdx = bankOpts().indexOf(opt); bankHilight(bankIdx); }
    });
    bankOptsW.addEventListener('click', function(e) {
        const opt = e.target.closest('.bank-opt');
        if (opt) bankSelectAndNext(opt.dataset.val, opt.textContent.trim(), opt.dataset.name);
    });

    /* Close dropdown on outside click */
    document.addEventListener('click', e => {
        if (!e.target.closest('#bankDisplay') && !e.target.closest('#bankMenu')) bankClose();
    });

    /* ══════════════════════════════════════════════════════
       KEYBOARD NAVIGATION CHAIN  (all capture phase)
    ══════════════════════════════════════════════════════ */

    /* 1. Date → Enter → D/W type */
    document.getElementById('transactionDate').addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        document.getElementById('transactionType').focus();
    }, true);

    /* 2. D/W type: Up/Down toggles, Enter → Bank dropdown */
    document.getElementById('transactionType').addEventListener('keydown', function(e) {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            this.value = this.value === 'D' ? 'W' : 'D';
            return;
        }
        if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            bankDisp.focus();
            setTimeout(() => bankOpen(), 50);
            return;
        }
        /* Also allow typing D or W directly */
        if (e.key === 'd' || e.key === 'D') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            this.value = 'D';
            return;
        }
        if (e.key === 'w' || e.key === 'W') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            this.value = 'W';
            return;
        }
    }, true);

    /* 3. Cheque No → Enter → Amount */
    document.getElementById('chequeNo').addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        document.getElementById('amount').focus();
        document.getElementById('amount').select();
    }, true);

    /* 4. Amount → Enter → Narration */
    document.getElementById('amount').addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        document.getElementById('narration').focus();
    }, true);

    /* 5. Narration → Enter → Save button */
    document.getElementById('narration').addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        document.getElementById('btnSave').focus();
    }, true);

    /* 6. Footer buttons: Left/Right to navigate, Enter to trigger */
    const footerBtns = [document.getElementById('btnSave'), document.getElementById('btnExit')];

    footerBtns.forEach(btn => {
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                const idx = footerBtns.indexOf(this);
                const next = footerBtns[Math.min(idx + 1, footerBtns.length - 1)];
                if (next) next.focus();
                return;
            }
            if (e.key === 'ArrowLeft') {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                const idx = footerBtns.indexOf(this);
                const prev = footerBtns[Math.max(idx - 1, 0)];
                if (prev) prev.focus();
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                this.click();
                return;
            }
        }, true);
    });

    /* ══════════════════════════════════════════════════════
       GLOBAL SHORTCUTS
    ══════════════════════════════════════════════════════ */
    document.addEventListener('keydown', function(e) {
        /* Escape: close bank dropdown if open, otherwise exit */
        if (e.key === 'Escape') {
            if (bankIsOpen()) {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                bankClose(); bankDisp.focus();
                return;
            }
            /* Don't navigate away if handled by layout */
        }
    }, true);

    /* Auto-focus first field */
    setTimeout(() => {
        const dt = document.getElementById('transactionDate');
        dt.focus();
    }, 150);
});

function updateDayName() {
    const date = new Date(document.getElementById('transactionDate').value);
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    document.getElementById('dayName').textContent = days[date.getDay()];
}

function toggleType() {
    const input = document.getElementById('transactionType');
    input.value = input.value === 'D' ? 'W' : 'D';
}

function validateType() {
    const input = document.getElementById('transactionType');
    const val = input.value.toUpperCase();
    if (val !== 'D' && val !== 'W') {
        alert('Please enter D for Deposit or W for Withdrawal');
        input.value = 'D';
    } else {
        input.value = val;
    }
}

function saveTransaction() {
    const bankId = document.getElementById('bankId').value;
    const amount = document.getElementById('amount').value;
    const type = document.getElementById('transactionType').value.toUpperCase();
    
    if (!bankId) { alert('Please select a bank'); document.getElementById('bankDisplay').focus(); return; }
    if (!amount || parseFloat(amount) <= 0) { alert('Please enter a valid amount'); document.getElementById('amount').focus(); return; }
    if (type !== 'D' && type !== 'W') { alert('Please enter D or W for transaction type'); return; }
    
    const formData = {
        transaction_date: document.getElementById('transactionDate').value,
        transaction_type: type,
        bank_id: bankId,
        cheque_no: document.getElementById('chequeNo').value,
        amount: parseFloat(amount),
        narration: document.getElementById('narration').value,
        _token: '<?php echo e(csrf_token()); ?>'
    };
    
    // Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('<?php echo e(route("admin.bank-transaction.store")); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Transaction #' + data.transaction_no + ' saved successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(e => alert('Failed to save transaction'));
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/bank-transaction/transaction.blade.php ENDPATH**/ ?>