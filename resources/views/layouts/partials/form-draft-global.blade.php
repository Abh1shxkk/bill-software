{{-- =====================================================================
     GLOBAL FORM DRAFT  –  Auto-save & restore on refresh
     Activates on any URL containing "/transaction" or "/modification"
     ===================================================================== --}}
<script>
(function () {
    'use strict';

    // ── Activation guard ─────────────────────────────────────────
    const path = window.location.pathname;
    if (!path.includes('/transaction') && !path.includes('/modification')) return;

    // ── Constants ────────────────────────────────────────────────
    const DRAFT_KEY  = 'form_draft_{{ auth()->id() }}_' + path;
    const CLEAR_FLAG = 'draft_clear_' + DRAFT_KEY;   // sessionStorage key (unique per page)
    let _timer       = null;
    let _hasData     = false;   // true once user has made any change

    console.log('[FormDraft] Active on:', path, '| key:', DRAFT_KEY);

    // ── DOM helpers ──────────────────────────────────────────────
    function getForm() {
        return document.querySelector(
            'form#saleTransactionForm, form#saleChallanForm, form#saleReturnTransactionForm,' +
            'form#saleReturnReplacementForm, form#purchaseForm, form#purchaseChallanForm,' +
            'form#purchaseReturnForm, form[data-draft], form'
        ) || null;
    }
    function getTbody() {
        return document.getElementById('itemsTableBody')
            || document.getElementById('hsnTableBody')
            || null;
    }

    // ── Safe attribute escape ─────────────────────────────────────
    function escAttr(str) {
        return String(str).replace(/([^\w-])/g, '\\$1');
    }

    // ── Collect current state ────────────────────────────────────
    function collectDraft() {
        const form  = getForm();
        const tbody = getTbody();
        const headerInputs = {};
        const tableInputs  = {};

        // Header fields — everything inside the form but NOT in the table body,
        // and NOT server-generated read-only fields (invoice no, day name, etc.)
        if (form) {
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (tbody && tbody.contains(el)) return;
                if (el.classList.contains('readonly-field')) return;
                const key = el.name || el.id;
                if (!key) return;
                headerInputs[key] = el.value;
            });
        }

        // Table — innerHTML captures <tr data-*> + HTML-attribute values.
        // tableInputs captures live .value (user-typed values NOT in HTML attr).
        let tableHTML = '';
        if (tbody) {
            tableHTML = tbody.innerHTML;
            tbody.querySelectorAll('input, select, textarea').forEach(el => {
                const key = el.id || el.name;
                if (key) tableInputs[key] = el.value;
            });
        }

        return {
            savedAt: new Date().toISOString(),
            headerInputs,
            tableHTML,
            tableInputs,
        };
    }

    // ── Save (called on debounce AND on beforeunload) ─────────────
    function saveDraft() {
        clearTimeout(_timer);   // cancel any pending debounce
        try {
            const d = collectDraft();

            // Determine whether there is any meaningful user-entered data
            const hasHeader = Object.values(d.headerInputs).some(v => v && String(v).trim() !== '');
            const hasTable  = d.tableHTML.trim().length > 30;

            if (hasHeader || hasTable) {
                localStorage.setItem(DRAFT_KEY, JSON.stringify(d));
                _hasData = true;
                console.log('[FormDraft] Saved. items:', (d.tableHTML.match(/<tr[\s>]/g)||[]).length,
                            '| header keys:', Object.keys(d.headerInputs).length);
            }
        } catch (e) {
            console.warn('[FormDraft] Save error:', e);
        }
    }

    function scheduleSave() {
        _hasData = true;
        clearTimeout(_timer);
        _timer = setTimeout(saveDraft, 500);
    }

    // ── Public API ───────────────────────────────────────────────
    window.FormDraft = {
        clear: function () {
            localStorage.removeItem(DRAFT_KEY);
            sessionStorage.removeItem(CLEAR_FLAG);
            _hasData = false;
            console.log('[FormDraft] Draft cleared.');
        },
        markSaved: function () {
            sessionStorage.setItem(CLEAR_FLAG, '1');
        },
    };

    // ── Widget restore helpers ────────────────────────────────────

    // <select> with Select2 — update Select2 UI after setting .value
    function refreshSelect2(el) {
        try {
            if (window.jQuery && window.jQuery.fn.select2 && jQuery(el).data('select2')) {
                jQuery(el).trigger('change.select2');
            }
        } catch(e) { /* Select2 not initialised on this element */ }
    }

    // Custom searchable dropdown restore
    // Pattern: .searchable-dropdown-input (visible text) + hidden input (saved id)
    // Strategy: dispatch a synthetic click on the matching list item so the page's
    // own selectItem() handler fires — this sets text, hidden value, marks selected,
    // AND fires all side effects (fetchCustomerDue, checkChooseItemsButtonState, etc.)
    function restoreSearchableDropdown(wrapper, savedId) {
        if (!wrapper || !savedId) return;

        const list = wrapper.querySelector('.searchable-dropdown-list');
        if (!list) return;

        const matchingItem = Array.prototype.find.call(
            list.querySelectorAll('.dropdown-item'),
            item => item.getAttribute('data-value') === String(savedId)
        );
        if (!matchingItem) return;

        // Synthetic click bubbles up to the list's click listener → page's selectItem() runs
        try {
            matchingItem.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
        } catch(e) {
            // Fallback: cosmetic-only mark
            list.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('selected'));
            matchingItem.classList.add('selected');
        }
    }

    // ── Restore ──────────────────────────────────────────────────
    function restoreDraft(draft) {
        const form  = getForm();
        const tbody = getTbody();

        // 1. Plain inputs & selects (skip readonly-field — server-generated values)
        Object.entries(draft.headerInputs || {}).forEach(([key, value]) => {
            let el = null;
            try { el = form ? form.querySelector(`[name="${escAttr(key)}"]`) : null; } catch(e) {}
            if (!el) el = document.getElementById(key);
            if (!el) { try { el = document.querySelector(`[name="${escAttr(key)}"]`); } catch(e) {} }
            if (!el || el.classList.contains('readonly-field')) return;

            // Skip visible text inputs of custom searchable dropdowns —
            // those are handled via selectItem() click simulation below
            if (el.classList.contains('searchable-dropdown-input')) return;

            el.value = value;

            // <select>: fire change so onchange handlers (updateInvoiceType etc.) run
            if (el.tagName === 'SELECT') {
                el.dispatchEvent(new Event('change', { bubbles: true }));
                refreshSelect2(el);
            }
        });

        // 2. Custom searchable dropdowns — simulate item click so ALL side effects fire
        document.querySelectorAll('.searchable-dropdown').forEach(wrapper => {
            const hiddenEl = wrapper.querySelector('input[type="hidden"]');
            if (!hiddenEl) return;
            const savedId = draft.headerInputs[hiddenEl.name || hiddenEl.id];
            if (savedId) restoreSearchableDropdown(wrapper, savedId);
        });

        // 2. Table body: restore HTML structure (preserves data-* attrs + inline onchange handlers)
        if (tbody && draft.tableHTML) {
            tbody.innerHTML = draft.tableHTML;

            // Override with live-typed values (these are NOT in the HTML attribute)
            Object.entries(draft.tableInputs || {}).forEach(([key, value]) => {
                let el = null;
                try { el = tbody.querySelector('#' + escAttr(key)); } catch(e) {}
                if (!el) { try { el = tbody.querySelector(`[name="${escAttr(key)}"]`); } catch(e) {} }
                if (el) el.value = value;
            });

            // Sync the global itemIndex counter so new rows get correct indices
            const rows = tbody.querySelectorAll('tr');
            if (rows.length) {
                const lastIdx = parseInt(
                    rows[rows.length - 1].getAttribute('data-row-index') || rows.length
                );
                if (!isNaN(lastIdx) && typeof itemIndex !== 'undefined') {
                    itemIndex = lastIdx;
                }

                // Re-attach programmatic row listeners (keyboard nav, click-to-select)
                rows.forEach(row => {
                    const idx = parseInt(row.getAttribute('data-row-index'));
                    row.style.cursor = 'pointer';
                    row.addEventListener('click', function (e) {
                        const i = parseInt(e.currentTarget.getAttribute('data-row-index'));
                        if (typeof selectRow === 'function') selectRow(i);
                    });
                    if (!isNaN(idx) && typeof addRowEventListeners === 'function') {
                        try { addRowEventListeners(row, idx); } catch (e) { /* non-critical */ }
                    }
                });
            }
        }

        // 3. Trigger display-update helpers present on this page
        const tryCall = fn => { if (typeof fn === 'function') { try { fn(); } catch(e) {} } };
        tryCall(window.updateInvoiceType);
        tryCall(window.updateDayName);
        tryCall(window.updateSalesmanName);

        // 4. Recalculate totals after DOM settles
        setTimeout(() => {
            tryCall(window.calculateTotal);
            tryCall(window.recalculateAll);
            tryCall(window.updateTotals);
            tryCall(window.calculateGrandTotal);
        }, 150);

        console.log('[FormDraft] Restored successfully.');
    }

    // ── Banner ───────────────────────────────────────────────────
    function showBanner(draft) {
        if (document.getElementById('globalDraftBanner')) return;

        const when     = new Date(draft.savedAt).toLocaleString('en-IN', { dateStyle: 'short', timeStyle: 'short' });
        const rowCount = (draft.tableHTML.match(/<tr[\s>]/g) || []).length;
        const rowLabel = rowCount
            ? `<span style="color:#94a3b8;font-size:11px;margin-left:6px">${rowCount} item${rowCount !== 1 ? 's' : ''}</span>`
            : '';

        const el = document.createElement('div');
        el.id = 'globalDraftBanner';
        el.style.cssText = [
            'position:fixed', 'top:62px', 'left:50%', 'transform:translateX(-50%)',
            'background:#1e293b', 'color:#f8fafc', 'border-radius:10px',
            'padding:11px 18px', 'z-index:99999', 'display:flex', 'align-items:center',
            'gap:12px', 'box-shadow:0 8px 28px rgba(0,0,0,.4)', 'font-size:13px',
            'border-left:4px solid #f59e0b', 'max-width:94vw', 'flex-wrap:wrap',
        ].join(';');

        el.innerHTML = `
            <span style="font-size:18px;color:#f59e0b">&#9998;</span>
            <span><strong>Unsaved draft found</strong> &mdash; ${when}${rowLabel}</span>
            <button id="draftRestoreBtn"
                style="background:#22c55e;color:#fff;border:none;border-radius:6px;
                       padding:5px 15px;font-size:12px;cursor:pointer;font-weight:700;">
                &#10003;&nbsp;Restore
            </button>
            <button id="draftDiscardBtn"
                style="background:#ef4444;color:#fff;border:none;border-radius:6px;
                       padding:5px 15px;font-size:12px;cursor:pointer;font-weight:700;">
                &#10007;&nbsp;Discard
            </button>`;

        document.body.appendChild(el);

        document.getElementById('draftRestoreBtn').addEventListener('click', () => {
            restoreDraft(draft);
            el.remove();
        });
        document.getElementById('draftDiscardBtn').addEventListener('click', () => {
            window.FormDraft.clear();
            el.remove();
        });
    }

    // ── Patch success functions to auto-clear draft on save ───────
    function patchSuccessFunctions() {
        ['showSuccessModalWithReload', 'showSaveOptionsModal'].forEach(name => {
            const orig = window[name];
            window[name] = function () {
                window.FormDraft.clear();
                if (typeof orig === 'function') orig.apply(this, arguments);
            };
        });
    }

    // ── Attach change listeners ───────────────────────────────────
    function attachListeners() {
        const form  = getForm();
        const tbody = getTbody();

        if (form) {
            form.addEventListener('input',  scheduleSave, { passive: true });
            form.addEventListener('change', scheduleSave, { passive: true });
            console.log('[FormDraft] Listening on form:', form.id || '(no id)');
        } else {
            console.warn('[FormDraft] No form found on this page.');
        }

        if (tbody) {
            tbody.addEventListener('input',  scheduleSave, { passive: true });
            tbody.addEventListener('change', scheduleSave, { passive: true });
            new MutationObserver(scheduleSave).observe(tbody, { childList: true, subtree: true });
            console.log('[FormDraft] Listening on table body:', tbody.id);
        }

        // ── KEY FIX: save immediately on page unload ──────────────
        // This catches the case where user refreshes within the 500ms debounce window
        window.addEventListener('beforeunload', function () {
            if (_hasData) {
                saveDraft();
                console.log('[FormDraft] beforeunload save triggered.');
            }
        });
    }

    // ── Boot ─────────────────────────────────────────────────────
    // Patch success functions immediately (page scripts are already loaded at this point)
    patchSuccessFunctions();

    document.addEventListener('DOMContentLoaded', function () {
        attachListeners();

        // If a successful save just reloaded the page, clear the stale draft
        if (sessionStorage.getItem(CLEAR_FLAG) === '1') {
            window.FormDraft.clear();
            console.log('[FormDraft] Post-save reload detected — draft cleared.');
            return;
        }

        // Check for an existing draft
        try {
            const raw = localStorage.getItem(DRAFT_KEY);
            if (!raw) {
                console.log('[FormDraft] No draft found in localStorage.');
                return;
            }
            const draft = JSON.parse(raw);
            const hasHeader = Object.values(draft.headerInputs || {}).some(v => v && String(v).trim() !== '');
            const hasTable  = (draft.tableHTML || '').trim().length > 30;
            console.log('[FormDraft] Draft found — hasHeader:', hasHeader, '| hasTable:', hasTable,
                        '| savedAt:', draft.savedAt);
            if (hasHeader || hasTable) {
                showBanner(draft);
            }
        } catch (e) {
            console.warn('[FormDraft] Draft parse error — clearing:', e);
            localStorage.removeItem(DRAFT_KEY);
        }
    });

})();
</script>
