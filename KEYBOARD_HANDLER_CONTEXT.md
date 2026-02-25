# Keyboard Handler Context — Medi-BillSuite Laravel Blade Files

## Project Overview
Laravel Blade-based billing application with multiple transaction forms.
Each form has: header fields, item table rows, and modal boxes (supplier/customer select, bank details, load past transactions, HSN codes, adjustment).

---

## THE CORE PROBLEM — Why Keyboard Handlers Break

### Root Cause: Event Phase Conflict
The admin layout template registers **global `document` capture-phase handlers** for keyboard shortcuts (navigation, Escape, etc).

When a blade file adds handlers using:
```javascript
// ❌ WRONG — fires AFTER layout's capture handler has already consumed the event
document.addEventListener('keydown', handler);
element.addEventListener('keydown', handler);
document.addEventListener('keydown', handler, true); // still loses to window capture
```

The layout handler eats the event first → blade handler never fires.

**Result seen by user:** Enter pressed → nothing happens / wrong field gets focus / modal doesn't close.

---

## THE FIX — Always Use `window` Capture Phase

```javascript
// ✅ CORRECT — fires before ALL other handlers including layout's document handlers
window.addEventListener('keydown', handler, true);
```

**Always pair with:**
```javascript
e.preventDefault();
e.stopPropagation();
e.stopImmediatePropagation(); // blocks ALL other handlers on same element
```

**Always remove on modal close:**
```javascript
window.removeEventListener('keydown', handler, true);
```

---

## Standard Handler Pattern Used Across All Blades

```javascript
window.addEventListener('keydown', function(e) {
    // 1. Check key
    if (e.key !== 'Enter') return;

    // 2. Check which element is active
    if (document.activeElement?.id !== 'myFieldId') return;

    // 3. Check no modal is open (optional but recommended)
    if (_anyModalOpen()) return;

    // 4. Block everything
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // 5. Do the thing
    document.getElementById('nextFieldId')?.focus();
}, true); // <-- 'true' = capture phase = fires FIRST
```

---

## Modal Keyboard Handler Pattern

```javascript
// Handler function
function _handleMyModalKey(e) {
    const modal = document.getElementById('myModal');
    if (!modal || !modal.classList.contains('show')) return; // guard

    const MANAGED = ['ArrowDown', 'ArrowUp', 'Enter', 'Escape'];
    if (!MANAGED.includes(e.key)) return;

    // Block all other handlers
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    if (e.key === 'Escape') { closeMyModal(); return; }
    if (e.key === 'ArrowDown') { /* highlight next */ return; }
    if (e.key === 'ArrowUp')   { /* highlight prev */ return; }
    if (e.key === 'Enter')     { /* select highlighted */ return; }
}

// Register on open
function openMyModal() {
    window.removeEventListener('keydown', _handleMyModalKey, true); // remove stale
    document.getElementById('myModal').classList.add('show');
    window.addEventListener('keydown', _handleMyModalKey, true);
    // Auto-highlight first item
    setTimeout(() => highlightItem(0), 50);
}

// Unregister on close
function closeMyModal() {
    window.removeEventListener('keydown', _handleMyModalKey, true);
    document.getElementById('myModal').classList.remove('show');
}
```

---

## Dropdown List Selection Pattern

**Problem:** `onkeyup="filterList()"` fires on ArrowDown → resets highlighted item to first.

**Fix:** Use `oninput` instead of `onkeyup`:
```html
<!-- ❌ WRONG -->
<input oninput="filterList()" onkeyup="filterList()">

<!-- ✅ CORRECT — oninput only fires on actual text change, NOT on arrow keys -->
<input oninput="filterList()">
```

---

## Native `<select>` Dropdown in Modal — Z-Index Problem

**Problem:** Native `<select>` inside a modal opens its OS dropdown *behind* the modal overlay.

**Fix:** Replace native select with a custom searchable dropdown:
```html
<!-- Hidden native select (keeps value/form submission working) -->
<select id="mySelect" style="display:none;">
    @foreach($items as $item)
    <option value="{{ $item->id }}">{{ $item->name }}</option>
    @endforeach
</select>

<!-- Visible custom dropdown -->
<div style="position:relative; flex:1;">
    <input type="text" id="mySearchInput" placeholder="Search...">
    <div id="myDropList" style="display:none; position:absolute; z-index:99999;
         top:100%; left:0; width:100%; max-height:200px; overflow-y:auto;
         background:white; border:1px solid #ccc; box-shadow:0 4px 8px rgba(0,0,0,.15);">
    </div>
</div>
```

---

## Focus After Modal Close

Always move focus to a relevant field when a modal closes:

```javascript
function closeMyModal() {
    window.removeEventListener('keydown', _handleMyModalKey, true);
    document.getElementById('myModal').classList.remove('show');

    // Move cursor to correct next field
    setTimeout(() => {
        const nextField = document.getElementById('nextFieldId');
        if (nextField) { nextField.focus(); nextField.select?.(); }
    }, 50);
}
```

---

## Common Flows Implemented

| Form | Flow |
|------|------|
| Supplier Payment Transaction | Date → Bank → Ledger → Cheque No → Date → Amount → (next row / Add Party modal) |
| Supplier Payment Modification | TRN NO → (Load modal) / Enter on empty → Load Payment button |
| Customer Receipt Modification | Date → Ledger → Load Invoices button → modal |
| Credit Note / Debit Note | Party Name search → Salesman → ... → HSN modal → Amount |
| Quotation Modification | Date → Name → Remarks → Terms → Dis% → Load Quotation button → modal |

---

## Quick Diagnosis Checklist

When a keyboard handler is not working:

1. **Is it using `document.addEventListener` or element-level listener?**
   → Move to `window.addEventListener(..., true)`

2. **Is `stopImmediatePropagation()` missing?**
   → Add it alongside `preventDefault()` and `stopPropagation()`

3. **Is a dropdown reset happening on arrow keys?**
   → Change `onkeyup` to `oninput` on the search input

4. **Is a native `<select>` inside a modal showing behind the backdrop?**
   → Replace with custom searchable dropdown with `z-index: 99999`

5. **Is the modal guard checking the wrong condition?**
   → Use `modal.classList.contains('show')` not `modal.style.display`

6. **Is `selectHsnCode` / `confirmSupplierSelection` being called but then a global Enter handler refocuses wrong field?**
   → The close function changes modal state → global handler sees modal as "closed" and acts on main form. Fix: move modal handler to window capture so it runs first and calls `stopImmediatePropagation()`.

---

## Files Modified in This Session

- `transaction_blade.php` — Supplier Payment Transaction
- `modification_blade.php` (supplier) — Supplier Payment Modification  
- `cust_modification_blade.php` — Customer Receipt Modification
- `credit_modification_blade.php` — Credit Note Modification
- `debit_modification_blade.php` — Debit Note Modification
- `sales_modification_blade.php` — Quotation Modification
