@extends('layouts.admin')

@section('title', 'Voucher Entry')

@section('content')
<style>
    .compact-form { font-size: 11px; padding: 8px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: #d8d8ff; border: 1px solid #9999cc; padding: 10px; margin-bottom: 0; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .field-group label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; color: #800080; }
    .field-group input, .field-group select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    .table-compact { font-size: 11px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px 8px; vertical-align: middle; }
    .table-compact th { background: #9999cc; font-weight: 600; text-align: center; border: 1px solid #7777aa; color: #000; }
    .table-compact td { border: 1px solid #ccc; background: #fff; }
    .table-compact input { font-size: 11px; padding: 2px 4px; height: 24px; border: 1px solid #ced4da; width: 100%; border-radius: 0 !important; }
    
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .narration-section { background: #f0f0f0; padding: 8px; border: 1px solid #ccc; }
    .narration-label { color: #008000; font-weight: bold; font-size: 12px; }
    .total-display { color: #0000ff; font-weight: bold; font-size: 12px; text-align: right; }
    
    .row-complete { background-color: #e8f5e9 !important; }
    .row-complete td { background-color: #e8f5e9 !important; }
    .row-complete input { background-color: #e8f5e9 !important; }
    
    /* Row selection */
    .row-selected td { border-top: 2px solid #007bff !important; border-bottom: 2px solid #007bff !important; }
    .row-selected td:first-child { border-left: 2px solid #007bff !important; }
    .row-selected td:last-child { border-right: 2px solid #007bff !important; }
    #itemsTableBody tr { cursor: pointer; }
    #itemsTableBody tr:hover { background-color: #f0f7ff; }
    
    .voucher-type-btn { padding: 4px 12px; font-size: 11px; border-radius: 0; }
    .voucher-type-btn.active { background: #800080; color: white; border-color: #800080; }
    
    .footer-section { background: #e0e0e0; padding: 8px 15px; border: 1px solid #ccc; border-top: none; }
    .footer-btn { font-size: 11px; padding: 4px 15px; }
    
    /* Account Selection Modal */
    .account-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7); width: 90%; max-width: 800px; z-index: 9999; opacity: 0; transition: all 0.3s ease-in-out; }
    .account-modal.show { display: block; transform: translate(-50%, -50%) scale(1); opacity: 1; }
    .account-modal-content { background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); overflow: hidden; }
    .account-modal-header { padding: 1rem 1.5rem; background: #9999cc; color: #000; display: flex; justify-content: space-between; align-items: center; }
    .account-modal-title { margin: 0; font-size: 1.2rem; font-weight: 600; }
    .account-modal-body { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .account-modal-footer { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px; }
    .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 9998; }
    .modal-backdrop.show { display: block; }
    .btn-close-modal { background: transparent; border: none; color: #000; font-size: 1.5rem; cursor: pointer; }
    .account-list-item { padding: 8px 12px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; justify-content: space-between; }
    .account-list-item:hover { background: #e8e8ff; }
    .account-list-item.selected { background: #9999cc; color: white; }
    .account-type-badge { font-size: 9px; padding: 2px 6px; border-radius: 3px; }
    .account-type-GL { background: #e3f2fd; color: #1565c0; }
    .account-type-CB { background: #e8f5e9; color: #2e7d32; }
    .account-type-CL { background: #fff3e0; color: #ef6c00; }
    .account-type-SU { background: #fce4ec; color: #c2185b; }

    /* Keyboard navigation in account modal */
    .account-list-item.kb-active { background: #9999cc !important; color: #fff !important; }
    .account-list-item.kb-active .account-type-badge { opacity: .85; }
    /* Focus rings */
    /* ── Blue border on every focused field ── */
    #multiNarration:focus,
    #voucherType:focus,
    #voucherDate:focus,
    #narration:focus {
        outline: none !important;
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.20) !important;
        border-radius: 3px;
    }
    .account-name:focus {
        outline: none !important;
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.20) !important;
        background: #f0f6ff !important;
    }
    .debit-amount:focus {
        outline: none !important;
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.20) !important;
        background: #f0f6ff !important;
    }
    .credit-amount:focus {
        outline: none !important;
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.20) !important;
        background: #f0f6ff !important;
    }
    #accountSearch:focus {
        outline: none !important;
        border: 2px solid #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.20) !important;
    }
    /* Force our header selects to always show as native elements */
    select.no-select2,
    select.no-select2.select2-hidden-accessible {
        display: inline-block !important;
        visibility: visible   !important;
        opacity: 1            !important;
        width: auto           !important;
    }
    /* Hide any Select2 widget containers adjacent to our selects */
    #multiNarration    ~ .select2-container,
    #voucherType       ~ .select2-container,
    #accountTypeFilter ~ .select2-container { display: none !important; }
</style>

<div class="card shadow-sm border-0">
    <div class="card-body compact-form p-0">
        <form id="voucherForm" method="POST" autocomplete="off">
            @csrf

            <!-- Header Section -->
            <div class="header-section">
                <div class="row g-2 align-items-center">
                    <!-- Left: Multi Narration & Voucher Type -->
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="field-group">
                                <label>Multi Narration :</label>
                                <select class="form-control no-select2" name="multi_narration" id="multiNarration" style="width: 50px;" tabindex="1">
                                    <option value="0">N</option>
                                    <option value="1">Y</option>
                                </select>
                            </div>
                            <select class="form-control no-select2" name="voucher_type" id="voucherType" style="width: 140px; background: #800080; color: white; font-weight: bold;" tabindex="2">
                                <option value="receipt" {{ $voucherType == 'receipt' ? 'selected' : '' }}>Receipt Voucher</option>
                                <option value="payment" {{ $voucherType == 'payment' ? 'selected' : '' }}>Payment Voucher</option>
                                <option value="contra" {{ $voucherType == 'contra' ? 'selected' : '' }}>Contra Voucher</option>
                                <option value="journal" {{ $voucherType == 'journal' ? 'selected' : '' }}>Journal Voucher</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Center: Date & Day -->
                    <div class="col-md-4 text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <label class="field-group" style="color: #008000;">Voucher Date :</label>
                            <input type="date" class="form-control" name="voucher_date" id="voucherDate" value="{{ date('Y-m-d') }}" style="width: 130px;" tabindex="3">
                            <span id="dayName" style="color: #0000ff; font-weight: bold;">{{ date('l') }}</span>
                        </div>
                    </div>
                    
                    <!-- Right: Voucher No -->
                    <div class="col-md-4 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <label style="color: #800080; font-weight: bold;">Voucher No :</label>
                            <span id="voucherNoDisplay" style="font-weight: bold; font-size: 14px;">{{ $nextVoucherNo }}</span>
                            <input type="hidden" name="voucher_no" id="voucherNo" value="{{ $nextVoucherNo }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white border" style="border-top: none !important;">
                <div class="table-responsive" style="overflow-y: auto; max-height: 350px;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact mb-0">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="width: 50%;">NAME</th>
                                <th style="width: 20%;">DEBIT</th>
                                <th style="width: 20%;">CREDIT</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Narration Section -->
            <div class="narration-section">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2">
                            <label class="narration-label">Narration :</label>
                            <input type="text" class="form-control" name="narration" id="narration" style="flex: 1; height: 28px; font-size: 11px;">
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="total-display">
                            <span id="totalDebitDisplay">0.00</span>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span id="totalCreditDisplay">0.00</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="footer-section d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" class="btn btn-outline-primary footer-btn" onclick="showStandardNarrations()">
                        Standard Narrations [ F1 ]
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-danger footer-btn" onclick="deleteEntry()">Delete Entry</button>
                    <button type="button" class="btn btn-primary footer-btn" onclick="openAccountModal()">New Account (F9)</button>
                    <button type="button" class="btn btn-warning footer-btn" onclick="cancelVoucher()">Cancel Voucher</button>
                    <button type="button" class="btn btn-success footer-btn" onclick="saveVoucher()">Save (Ctrl+S / End)</button>
                    <a href="{{ route('admin.voucher-entry.index') }}" class="btn btn-secondary footer-btn">Exit (Esc)</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Account Selection Modal -->
<div class="modal-backdrop" id="accountModalBackdrop" onclick="closeAccountModal()"></div>
<div class="account-modal" id="accountModal">
    <div class="account-modal-content">
        <div class="account-modal-header">
            <h5 class="account-modal-title"><i class="bi bi-journal-text me-2"></i>Select Account</h5>
            <button type="button" class="btn-close-modal" onclick="closeAccountModal()">&times;</button>
        </div>
        <div class="account-modal-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm" id="accountSearch" placeholder="Search by code or name..." oninput="filterAccounts(); resetKbFocus()">
                </div>
                <div class="col-md-6">
                    <select class="form-control form-control-sm no-select2" id="accountTypeFilter" onchange="filterAccounts()">
                        <option value="">All Types</option>
                        <option value="GL">General Ledger</option>
                        <option value="CB">Cash/Bank</option>
                        <option value="CL">Customer</option>
                        <option value="SU">Supplier</option>
                    </select>
                </div>
            </div>
            <div id="accountList" style="max-height: 300px; overflow-y: auto;">
            </div>
            <div style="font-size:10px;color:#888;margin-top:4px;">↑ ↓ to navigate &nbsp;|&nbsp; Enter to select &nbsp;|&nbsp; Double-click to select &nbsp;|&nbsp; Esc to close</div>
        </div>
        <div class="account-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeAccountModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="confirmAccountSelection()">Select</button>
        </div>
    </div>
</div>

<script>
/* ═══════════════════════════════════════════════════
   STATE
═══════════════════════════════════════════════════ */
let itemRowCount    = 0;
let selectedAccount = null;
let currentEditRow  = null;
let allAccounts     = [];
let kbFocusIndex    = -1;  // keyboard cursor inside account list modal

const generalLedgers = @json($generalLedgers);
const cashBankBooks  = @json($cashBankBooks);
const customers      = @json($customers);
const suppliers      = @json($suppliers);

/* ═══════════════════════════════════════════════════
   BOOT
═══════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {

    buildAccountsList();
    for (var i = 0; i < 10; i++) addItemRow();

    /* Day name update */
    document.getElementById('voucherDate').addEventListener('change', function () {
        var d = new Date(this.value + 'T00:00:00');
        document.getElementById('dayName').textContent =
            ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d.getDay()];
    });

    /* Voucher type → refresh voucher number */
    document.getElementById('voucherType').addEventListener('change', function () {
        fetchNextVoucherNo(this.value);
    });

    /* ── Header Enter-key chain ─────────────────────────────────
       multiNarration [Enter] → voucherType [Enter] → voucherDate [Enter] → row-0
    ──────────────────────────────────────────────────────────── */
    document.getElementById('multiNarration').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('voucherType').focus(); }
    });
    document.getElementById('voucherType').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('voucherDate').focus(); }
    });

    /* ── voucherDate Enter → NAME field (row 0) ──
       CAPTURE-phase on window so it fires BEFORE any other handler.
       The browser's native date-input Enter moves focus to next tabindex,
       so we must (1) capture first, (2) stopImmediatePropagation,
       (3) use setTimeout to re-stamp our focus AFTER the browser's
       native focus-shift completes.
    ──────────────────────────────────────────────────────────── */
    window.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        var vd = document.getElementById('voucherDate');
        if (document.activeElement !== vd) return; /* Only when date field is focused */
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        /* Stage 1: immediate blur to prevent browser from choosing next tabindex */
        vd.blur();

        /* Stage 2: after browser settles, move to row-0 and open modal */
        setTimeout(function () { focusRow(0); }, 80);
    }, true); /* ← capture phase */

    /* ── Global shortcuts ─────────────────────────────────────── */
    /* Ctrl+S / End → save: capture-phase on window so it fires BEFORE
       global keyboard-shortcuts.js which might steal Ctrl+S */
    window.addEventListener('keydown', function (e) {
        if (_isAccountModalOpen()) return; /* modal has its own Ctrl+S handler */
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            saveVoucher(); return;
        }
        if (e.key === 'End') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            saveVoucher(); return;
        }
    }, true);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'F1')  { e.preventDefault(); showStandardNarrations(); }
        if (e.key === 'F9')  { e.preventDefault(); openAccountModal(); }
        if (e.key === 'Escape') {
            if (document.getElementById('accountModal').classList.contains('show'))
                closeAccountModal();
        }
    });

    /* ── Destroy Select2 on our 3 selects ────────────────────────
       Use 300ms so Select2's own init (which runs at ~0ms) is done first.
       After destroy, focus multiNarration so cursor lands there on page load.
    ──────────────────────────────────────────────────────────── */
    function destroySelect2AndFocus() {
        var selIds = ['multiNarration', 'voucherType', 'accountTypeFilter'];
        selIds.forEach(function (id) {
            var el  = document.getElementById(id);
            if (!el) return;

            /* 1. Remove select2-hidden-accessible so the native select shows */
            el.classList.remove('select2-hidden-accessible');
            el.removeAttribute('data-select2-id');
            el.style.display    = '';
            el.style.visibility = '';
            el.style.opacity    = '';

            /* 2. Hide the Select2 widget container that sits next to the element */
            var next = el.nextElementSibling;
            while (next) {
                if (next.classList.contains('select2-container')) {
                    next.style.display = 'none';
                }
                next = next.nextElementSibling;
            }

            /* 3. Also try jQuery destroy if available */
            if (window.jQuery && $.fn && $.fn.select2) {
                try {
                    var $el = $(el);
                    if ($el.hasClass('select2-hidden-accessible')) $el.select2('destroy');
                } catch (e) {}
            }
        });

        /* Focus multiNarration AFTER Select2 is destroyed */
        document.getElementById('multiNarration').focus();
    }

    /* 300ms gives Select2 enough time to init, then we destroy it */
    setTimeout(destroySelect2AndFocus, 300);

    /* ── SAFETY NET ─────────────────────────────────────────────────────────
       If focus EVER lands on a row's delete button (e.g. after credit Enter),
       immediately jump to the NEXT row's NAME field and open the modal.      */
    document.getElementById('itemsTableBody').addEventListener('focusin', function (e) {
        if (!e.target.closest('.btn-outline-danger')) return;
        var row     = e.target.closest('tr');
        var rows    = Array.from(document.querySelectorAll('#itemsTableBody tr'));
        var nextRow = rows[rows.indexOf(row) + 1];
        if (!nextRow) {
            addItemRow();
            nextRow = document.getElementById('itemsTableBody').lastElementChild;
        }
        nextRow.scrollIntoView({ block: 'nearest' });
        var nameEl = nextRow.querySelector('.account-name');
        nameEl._noModal = true;
        nameEl.focus();
        setTimeout(function () { openAccountModal(nextRow); }, 50);
    });
});

/* ═══════════════════════════════════════════════════
   ACCOUNT LIST — build, render, filter
═══════════════════════════════════════════════════ */
function buildAccountsList() {
    allAccounts = [];
    generalLedgers.forEach(function (gl) {
        allAccounts.push({ type:'GL', id:gl.id, code:gl.account_code||gl.alter_code||'', name:gl.account_name, label:'General Ledger' });
    });
    cashBankBooks.forEach(function (cb) {
        allAccounts.push({ type:'CB', id:cb.id, code:cb.alter_code||'', name:cb.name, label:'Cash/Bank' });
    });
    customers.forEach(function (cl) {
        allAccounts.push({ type:'CL', id:cl.id, code:cl.code||'', name:cl.name, label:'Customer' });
    });
    suppliers.forEach(function (su) {
        allAccounts.push({ type:'SU', id:su.id, code:su.code||'', name:su.name, label:'Supplier' });
    });
    renderAccountList();
}

function renderAccountList() {
    var container = document.getElementById('accountList');
    container.innerHTML = allAccounts
        .filter(function (a) { return a.id != null && a.id !== '' && a.id !== undefined; })
        .map(function (a) {
            return '<div class="account-list-item"' +
                ' data-type="'  + a.type                + '"' +
                ' data-id="'    + parseInt(a.id)        + '"' +
                ' data-code="'  + escHtml(a.code)       + '"' +
                ' data-name="'  + escHtml(a.name)       + '"' +
                ' onclick="selectAccountItem(this)"' +
                ' ondblclick="selectAccountItemAndConfirm(this)">' +
                '<div>' +
                '<span class="account-type-badge account-type-' + a.type + '">' + a.label + '</span>' +
                ' <strong class="ms-2">' + (a.code || '–') + '</strong> – ' + a.name +
                '</div></div>';
        }).join('');
}

function filterAccounts() {
    var search = document.getElementById('accountSearch').value.toLowerCase();
    var type   = document.getElementById('accountTypeFilter').value;
    document.querySelectorAll('#accountList .account-list-item').forEach(function (el) {
        var code = el.dataset.code.toLowerCase();
        var name = el.dataset.name.toLowerCase();
        var show = (code.includes(search) || name.includes(search)) && (!type || el.dataset.type === type);
        el.style.display = show ? '' : 'none';
    });
}

function resetKbFocus() {
    kbFocusIndex = -1;
    document.querySelectorAll('#accountList .account-list-item.kb-active')
            .forEach(function (el) { el.classList.remove('kb-active'); });
}

/* ═══════════════════════════════════════════════════
   MODAL KEYBOARD  ↑ ↓ Enter Esc
═══════════════════════════════════════════════════ */
function getVisibleItems() {
    return Array.from(document.querySelectorAll('#accountList .account-list-item'))
                .filter(function (el) { return el.style.display !== 'none'; });
}

function setKbHighlight(idx) {
    var items = getVisibleItems();
    document.querySelectorAll('#accountList .account-list-item.kb-active')
            .forEach(function (el) { el.classList.remove('kb-active', 'selected'); });

    if (idx < 0 || idx >= items.length) { selectedAccount = null; return; }

    var el = items[idx];
    el.classList.add('kb-active', 'selected');
    el.scrollIntoView({ block: 'nearest' });

    var rawId = el.dataset.id;
    selectedAccount = {
        type: el.dataset.type,
        id:   (rawId && rawId !== 'undefined' && !isNaN(rawId)) ? parseInt(rawId, 10) : null,
        code: el.dataset.code,
        name: el.dataset.name
    };
}

/* ═══════════════════════════════════════════════════
   MODAL KEYBOARD – CAPTURE-PHASE
   Registered on open, unregistered on close.
   Fires BEFORE any other handler (keyboard-shortcuts, index-shortcuts etc.).
═══════════════════════════════════════════════════ */
function _isAccountModalOpen() {
    var m = document.getElementById('accountModal');
    return m && m.classList.contains('show');
}

function _handleAccountModalKey(e) {
    if (!_isAccountModalOpen()) return;

    /* ── Ctrl+S → save (even while modal is open) ── */
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        closeAccountModal();
        setTimeout(saveVoucher, 100);
        return;
    }

    var MANAGED = ['ArrowDown', 'ArrowUp', 'Enter', 'Escape'];
    var isTyping = (e.key.length === 1 && !e.ctrlKey && !e.altKey);

    if (!MANAGED.includes(e.key) && !isTyping && e.key !== 'Backspace' && e.key !== 'Delete') return;

    var items = getVisibleItems();

    if (e.key === 'ArrowDown') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (!items.length) return;
        kbFocusIndex = Math.min(kbFocusIndex + 1, items.length - 1);
        setKbHighlight(kbFocusIndex);
        return;
    }

    if (e.key === 'ArrowUp') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (!items.length) return;
        kbFocusIndex = Math.max(kbFocusIndex - 1, 0);
        setKbHighlight(kbFocusIndex);
        return;
    }

    if (e.key === 'Enter') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        /* If nothing highlighted yet, highlight first item */
        if (!selectedAccount && items.length > 0) {
            kbFocusIndex = 0;
            setKbHighlight(0);
        }
        /* Confirm the highlighted account */
        if (selectedAccount) confirmAccountSelection();
        return;
    }

    if (e.key === 'Escape') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        closeAccountModal();
        return;
    }

    /* Typing → redirect focus to search input */
    if (isTyping || e.key === 'Backspace' || e.key === 'Delete') {
        var searchInput = document.getElementById('accountSearch');
        if (document.activeElement !== searchInput) {
            searchInput.focus();
        }
    }
}

/* ═══════════════════════════════════════════════════
   MODAL OPEN / CLOSE / SELECT / CONFIRM
═══════════════════════════════════════════════════ */
var _modalClosingToDebit = false;  // true = after confirm, go to debit; false = go back to name

function openAccountModal(row) {
    if (row === undefined) row = null;
    currentEditRow       = row;
    selectedAccount      = null;
    kbFocusIndex         = -1;
    _modalClosingToDebit = false;

    document.getElementById('accountSearch').value     = '';
    document.getElementById('accountTypeFilter').value = '';
    filterAccounts();
    document.querySelectorAll('#accountList .account-list-item')
            .forEach(function (el) { el.classList.remove('selected', 'kb-active'); });

    document.getElementById('accountModalBackdrop').classList.add('show');
    document.getElementById('accountModal').classList.add('show');

    /* Register capture-phase keyboard handler for the modal */
    window.removeEventListener('keydown', _handleAccountModalKey, true); // remove stale
    window.addEventListener('keydown', _handleAccountModalKey, true);

    setTimeout(function () { document.getElementById('accountSearch').focus(); }, 60);
}

function closeAccountModal() {
    /* Unregister capture-phase keyboard handler */
    window.removeEventListener('keydown', _handleAccountModalKey, true);

    document.getElementById('accountModalBackdrop').classList.remove('show');
    document.getElementById('accountModal').classList.remove('show');
    /* If Esc was pressed (no confirm), return focus to name WITHOUT re-opening modal */
    if (!_modalClosingToDebit && currentEditRow) {
        var nameInput = currentEditRow.querySelector('.account-name');
        nameInput._noModal = true;
        nameInput.focus();
    }
    _modalClosingToDebit = false;   /* reset flag for next open */
}

function selectAccountItem(el) {
    document.querySelectorAll('#accountList .account-list-item')
            .forEach(function (i) { i.classList.remove('selected', 'kb-active'); });
    el.classList.add('selected', 'kb-active');
    var rawId = el.dataset.id;
    selectedAccount = {
        type: el.dataset.type,
        id:   (rawId && rawId !== 'undefined' && !isNaN(rawId)) ? parseInt(rawId, 10) : null,
        code: el.dataset.code,
        name: el.dataset.name
    };
    var items = getVisibleItems();
    kbFocusIndex = items.indexOf(el);
}

function selectAccountItemAndConfirm(el) {
    selectAccountItem(el);
    confirmAccountSelection();
}

function confirmAccountSelection() {
    if (!selectedAccount) { alert('Please select an account'); return; }

    var targetRow = currentEditRow;

    if (!targetRow) {
        document.querySelectorAll('#itemsTableBody tr').forEach(function (r) {
            if (!targetRow && !r.querySelector('.account-name').value) targetRow = r;
        });
        if (!targetRow) {
            addItemRow();
            targetRow = document.querySelector('#itemsTableBody tr:last-child');
        }
    }

    targetRow.querySelector('.account-type').value = selectedAccount.type;
    targetRow.querySelector('.account-id').value   = selectedAccount.id;
    targetRow.querySelector('.account-code').value = selectedAccount.code;
    targetRow.querySelector('.account-name').value = selectedAccount.name;
    updateRowStatus(targetRow);

    /* Tell closeAccountModal to NOT focus nameInput — we will focus debit directly */
    _modalClosingToDebit = true;
    closeAccountModal();

    /* Direct focus to Debit — no setTimeout race condition */
    var debitEl = targetRow.querySelector('.debit-amount');
    debitEl.focus();
    debitEl.select();
}

/* ═══════════════════════════════════════════════════
   TABLE ROWS
═══════════════════════════════════════════════════ */
function addItemRow(data) {
    itemRowCount++;
    var n    = itemRowCount;
    var tbody = document.getElementById('itemsTableBody');
    var row   = document.createElement('tr');
    row.setAttribute('data-row', n);
    row.innerHTML =
        '<td>' +
          '<div class="d-flex align-items-center gap-1">' +
            '<input type="text" class="form-control account-name" name="items[' + n + '][account_name]"' +
                ' value="' + escHtml((data && data.account_name) || '') + '"' +
                ' readonly tabindex="' + (100 + n * 3) + '"' +
                ' placeholder="Enter / click to select"' +
                ' style="cursor:pointer;flex:1;">' +
            '<input type="hidden" class="account-type" name="items[' + n + '][account_type]" value="' + ((data && data.account_type) || '') + '">' +
            '<input type="hidden" class="account-id"   name="items[' + n + '][account_id]"   value="' + ((data && data.account_id)   || '') + '">' +
            '<input type="hidden" class="account-code" name="items[' + n + '][account_code]" value="' + ((data && data.account_code) || '') + '">' +
          '</div>' +
        '</td>' +
        '<td>' +
          '<input type="number" class="form-control text-end debit-amount" name="items[' + n + '][debit_amount]"' +
              ' step="0.01" value="' + ((data && data.debit_amount) || '') + '"' +
              ' tabindex="' + (101 + n * 3) + '"' +
              ' onchange="calculateTotals();updateRowStatus(this.closest(\'tr\'));clearOtherAmount(this,\'credit\')">' +
        '</td>' +
        '<td>' +
          '<input type="number" class="form-control text-end credit-amount" name="items[' + n + '][credit_amount]"' +
              ' step="0.01" value="' + ((data && data.credit_amount) || '') + '"' +
              ' tabindex="' + (102 + n * 3) + '"' +
              ' onchange="calculateTotals();updateRowStatus(this.closest(\'tr\'));clearOtherAmount(this,\'debit\')">' +
        '</td>' +
        '<td class="text-center">' +
          '<button type="button" class="btn btn-sm btn-outline-danger" tabindex="-1" onclick="removeRow(this)" title="Remove">' +
            '<i class="bi bi-trash"></i>' +
          '</button>' +
        '</td>';

    tbody.appendChild(row);
    wireRowKeys(row);
    if (data) updateRowStatus(row);
}

function wireRowKeys(row) {
    var nameEl   = row.querySelector('.account-name');
    var debitEl  = row.querySelector('.debit-amount');
    var creditEl = row.querySelector('.credit-amount');

    /* ── NAME: focus → open modal (for mouse clicks)
       _noModal flag = set before programmatic focus so modal isn't double-opened ── */
    nameEl.addEventListener('focus', function () {
        if (this._noModal) { this._noModal = false; return; }
        /* Only open if modal not already visible */
        if (!document.getElementById('accountModal').classList.contains('show')) {
            openAccountModal(row);
        }
    });
    nameEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            if (!document.getElementById('accountModal').classList.contains('show')) {
                openAccountModal(row);
            }
        }
    });

    /* ── DEBIT: Enter → move to Credit (clear credit first if debit has value) ── */
    debitEl.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if ((parseFloat(this.value) || 0) > 0) {
            creditEl.value = '';
        }
        calculateTotals();
        creditEl.focus();
        creditEl.select();
    });
    debitEl.addEventListener('input', function () { calculateTotals(); updateRowStatus(row); });

    /* ── CREDIT: Enter → go to NEXT row Name + open modal ── */
    creditEl.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if ((parseFloat(this.value) || 0) > 0) {
            debitEl.value = '';
        }
        updateRowStatus(row);
        calculateTotals();
        goToNextRow(row);
    });
    creditEl.addEventListener('input', function () { calculateTotals(); updateRowStatus(row); });
}

function goToNextRow(currentRow) {
    var rows = Array.from(document.querySelectorAll('#itemsTableBody tr'));
    var idx  = rows.indexOf(currentRow);
    var next = rows[idx + 1];

    if (!next) {
        addItemRow();
        /* addItemRow appends to tbody — grab the last child */
        next = document.getElementById('itemsTableBody').lastElementChild;
    }

    next.scrollIntoView({ block: 'nearest' });
    var nxtName = next.querySelector('.account-name');
    nxtName._noModal = true;
    nxtName.focus();
    openAccountModal(next);
}

function focusRow(idx) {
    var rows = document.querySelectorAll('#itemsTableBody tr');
    if (!rows[idx]) return;
    var targetRow = rows[idx];
    var nameEl = targetRow.querySelector('.account-name');
    
    /* 1. Prevent focus handler from double-opening modal */
    nameEl._noModal = true;
    /* 2. Focus the NAME field first */
    nameEl.focus();
    /* 3. Blur any residual focus (date input calendar) */
    document.getElementById('voucherDate').blur();
    /* 4. Open the account selection modal for this row */
    openAccountModal(targetRow);
}

/* ═══════════════════════════════════════════════════
   HELPERS
═══════════════════════════════════════════════════ */
function escHtml(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function clearOtherAmount(input, otherType) {
    var row = input.closest('tr');
    if ((parseFloat(input.value) || 0) > 0)
        row.querySelector('.' + otherType + '-amount').value = '';
}

function removeRow(btn) {
    btn.closest('tr').remove();
    calculateTotals();
    if (document.querySelectorAll('#itemsTableBody tr').length < 5) addItemRow();
}

function updateRowStatus(row) {
    var name   = row.querySelector('.account-name').value;
    var debit  = parseFloat(row.querySelector('.debit-amount').value)  || 0;
    var credit = parseFloat(row.querySelector('.credit-amount').value) || 0;
    if (name && (debit > 0 || credit > 0)) row.classList.add('row-complete');
    else                                    row.classList.remove('row-complete');
}

function calculateTotals() {
    var d = 0, c = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(function (r) {
        d += parseFloat(r.querySelector('.debit-amount')?.value)  || 0;
        c += parseFloat(r.querySelector('.credit-amount')?.value) || 0;
    });
    document.getElementById('totalDebitDisplay').textContent  = d.toFixed(2);
    document.getElementById('totalCreditDisplay').textContent = c.toFixed(2);
}

/* ═══════════════════════════════════════════════════
   VOUCHER NO
═══════════════════════════════════════════════════ */
function fetchNextVoucherNo(type) {
    fetch('{{ route("admin.voucher-entry.get-next-voucher-no") }}?type=' + type)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                document.getElementById('voucherNo').value             = data.voucher_no;
                document.getElementById('voucherNoDisplay').textContent = data.voucher_no;
            }
        });
}

function showStandardNarrations() { alert('Standard Narrations feature coming soon!'); }

/* ═══════════════════════════════════════════════════
   SAVE
═══════════════════════════════════════════════════ */
function saveVoucher() {
    var items    = [];
    var hasItems = false;

    document.querySelectorAll('#itemsTableBody tr').forEach(function (row) {
        var name   = row.querySelector('.account-name')?.value  || null;
        var type   = row.querySelector('.account-type')?.value  || null;
        var rawId  = row.querySelector('.account-id')?.value;
        var code   = row.querySelector('.account-code')?.value  || null;
        var debit  = row.querySelector('.debit-amount')?.value;
        var credit = row.querySelector('.credit-amount')?.value;
        var acctId = (rawId && rawId !== 'undefined' && !isNaN(rawId)) ? parseInt(rawId, 10) : null;

        if (name || parseFloat(debit) > 0 || parseFloat(credit) > 0) {
            hasItems = true;
            items.push({ account_type:type, account_id:acctId, account_code:code,
                         account_name:name, debit_amount:debit||0, credit_amount:credit||0 });
        }
    });

    if (!hasItems) { alert('Please add at least one entry'); return; }

    var td = parseFloat(document.getElementById('totalDebitDisplay').textContent)  || 0;
    var tc = parseFloat(document.getElementById('totalCreditDisplay').textContent) || 0;
    if (Math.abs(td - tc) > 0.01) {
        alert('Debit and Credit totals must be equal!\nDebit: ' + td.toFixed(2) + '\nCredit: ' + tc.toFixed(2));
        return;
    }

    if (typeof window.markAsSaving === 'function') window.markAsSaving();

    fetch('{{ route("admin.voucher-entry.store") }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            voucher_date:    document.getElementById('voucherDate').value,
            voucher_type:    document.getElementById('voucherType').value,
            multi_narration: document.getElementById('multiNarration').value,
            narration:       document.getElementById('narration').value,
            items:           items,
            _token:          '{{ csrf_token() }}'
        })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            alert('Voucher #' + data.voucher_no + ' saved successfully!');
            window.location.href = '{{ route("admin.voucher-entry.index") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(function () { alert('Failed to save voucher'); });
}

function deleteEntry() {
    var sel = document.querySelector('#itemsTableBody tr.row-selected');
    if (sel) { sel.remove(); calculateTotals(); }
    else alert('Please select a row to delete');
}

function cancelVoucher() {
    if (confirm('Are you sure you want to cancel this voucher entry?'))
        window.location.href = '{{ route("admin.voucher-entry.index") }}';
}
</script>
@endsection