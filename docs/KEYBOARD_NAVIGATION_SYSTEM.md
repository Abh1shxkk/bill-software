# Keyboard Navigation System Documentation

## Overview

This document details the complete keyboard navigation implementation in the **Sale Transaction** blade. Use this as a reference for implementing similar functionality in other module blades (Sale Modification, Purchase, Purchase Return, etc.).

---

## Architecture

The keyboard navigation system consists of several components:

```
┌─────────────────────────────────────────────────────────────────────┐
│                    KEYBOARD NAVIGATION SYSTEM                        │
├─────────────────────────────────────────────────────────────────────┤
│  1. MAIN KEYBOARD EVENT LISTENER (Document level, capture phase)    │
│     ├── Detects key presses                                         │
│     ├── Checks if modals are open (delegates to modal handlers)     │
│     └── Routes to appropriate key handlers                          │
├─────────────────────────────────────────────────────────────────────┤
│  2. KEY HANDLERS                                                     │
│     ├── handleEnterKey()    - Field navigation, special behaviors   │
│     ├── handleArrowKeys()   - Table cell navigation                 │
│     ├── handleEndKey()      - Save transaction                      │
│     ├── handleCtrlS()       - Save transaction                      │
│     ├── handleCtrlI()       - Open Choose Items modal               │
│     └── handleEscapeKey()   - Close modals (priority order)         │
├─────────────────────────────────────────────────────────────────────┤
│  3. MODAL KEYBOARD HANDLERS                                          │
│     ├── handleChooseItemsModalKeyboard()                            │
│     ├── handleBatchModalKeyboard()                                  │
│     └── handleSaveOptionsModalKeyboard()                            │
├─────────────────────────────────────────────────────────────────────┤
│  4. ROW-LEVEL HANDLERS (Per-field in items table)                   │
│     ├── code field    - Enter opens item/batch modal                │
│     ├── qty field     - Enter → Free Qty, ArrowUp/Down navigate     │
│     ├── free_qty      - Enter → Rate                                │
│     ├── rate          - Enter → Discount (with MRP validation)      │
│     ├── discount      - Enter → Next row or Discount Options Modal  │
│     └── batch         - Enter (when empty) → Batch Change Modal     │
├─────────────────────────────────────────────────────────────────────┤
│  5. FOCUS MANAGEMENT                                                 │
│     ├── focusNextElement()  - Smart field-to-field navigation       │
│     ├── focusTableCell()    - Table cell navigation                 │
│     ├── Auto-focus on page load (Series Select)                     │
│     └── Focus return on modal close                                 │
├─────────────────────────────────────────────────────────────────────┤
│  6. STATE TRACKING                                                   │
│     ├── passedRemarksField  - Tracks if user passed remarks         │
│     ├── chooseItemsSelectedIndex - Modal row selection              │
│     ├── batchSelectedIndex  - Batch modal row selection             │
│     └── itemModalOpenedFromRowIndex - Focus return tracking         │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Location in Sale Transaction Blade

```
File: resources/views/admin/sale/transaction.blade.php
Lines: ~5965 - ~6835 (Main Keyboard Navigation System)
Lines: ~3790 - ~3990 (Row-level event listeners in addRowEventListeners function)
Lines: ~5635 - ~5745 (Save Options Modal keyboard handling)
```

---

## 1. Configuration Object

```javascript
const CONFIG = {
    // Selectors for focusable elements in tab order
    focusableSelector: [
        'input:not([type="hidden"]):not([readonly]):not([disabled]):not(.readonly-field)',
        'select:not([disabled])',
        'textarea:not([readonly]):not([disabled])',
        'button:not([disabled]):not(.btn-close-modal)'
    ].join(', '),
    
    // Special selectors for items table
    tableInputSelector: '#itemsTableBody input:not([readonly]):not([disabled])',
    tableRowSelector: '#itemsTableBody tr',
    
    // Modal selectors (all modals that need keyboard handling)
    modalSelectors: '.pending-orders-modal.show, .choose-items-modal.show, .alert-modal.show, .save-options-modal.show'
};
```

---

## 2. Utility Functions

### getFocusableElements()
Returns array of all visible, focusable elements in DOM order.
- Filters out hidden elements (display:none, visibility:hidden)
- Filters out elements in hidden modals
- Useful for Enter-based field navigation

### isModalOpen()
Returns `true` if any modal (matching CONFIG.modalSelectors) is currently open.

### isInItemsTable(element)
Returns `true` if element is inside `#itemsTableBody`.

### getTableCellInfo(element)
Returns object with cell position info:
```javascript
{
    element,      // The focused element
    td,           // Parent TD
    tr,           // Parent TR
    tbody,        // Parent TBODY
    colIndex,     // Current column index
    rowIndex,     // Current row index
    totalCols,    // Total columns in row
    totalRows     // Total rows in table
}
```

### focusNextElement(currentElement, direction, skipButtons)
Moves focus to next/previous element in form.
- `direction`: 1 = forward, -1 = backward
- `skipButtons`: true to skip button elements (default: true)
- Selects text in input fields after focus

### focusTableCell(rowIndex, colIndex)
Focuses input in specific table cell.
- Returns true if successful
- Selects text after focus

---

## 3. Key Handlers

### handleEnterKey(e)

**Priority checks (in order):**

1. **Don't interfere with buttons** - Let native click happen
2. **Don't interfere with textarea** - Allow new lines
3. **Remarks field** - Opens Choose Items modal, sets `passedRemarksField = true`
4. **Fallback after remarks** - If on readonly/body after passing remarks, reopen Choose Items modal
5. **Empty batch field with item** - Opens Batch Change modal
6. **Inside items table** - Navigate to next column, then next row
7. **SELECT elements** - Confirm and move to next field
8. **Regular INPUT** - Move to next/previous field (Shift+Enter = backward)

```javascript
function handleEnterKey(e) {
    const activeEl = document.activeElement;
    const tagName = activeEl?.tagName?.toLowerCase();
    
    // Skip buttons and textareas
    if (tagName === 'button') return;
    if (tagName === 'textarea') return;
    
    // Special: Remarks field opens Choose Items modal
    if (activeEl.id === 'remarks') {
        e.preventDefault();
        passedRemarksField = true;
        openChooseItemsModal();
        return;
    }
    
    // Table navigation
    if (isInItemsTable(activeEl)) {
        e.preventDefault();
        const cellInfo = getTableCellInfo(activeEl);
        // Try next column, then next row, then add new row
        // ...
        return;
    }
    
    // Regular field navigation
    e.preventDefault();
    focusNextElement(activeEl, e.shiftKey ? -1 : 1);
}
```

### handleArrowKeys(e)

Only works inside items table:
- **ArrowDown**: Same column, next row
- **ArrowUp**: Same column, previous row
- **ArrowRight**: Next column (only if cursor at end of text)
- **ArrowLeft**: Previous column (only if cursor at start of text)

### handleEndKey(e)

- In textarea: Allow normal End behavior
- In text input without Ctrl: Allow normal End behavior
- Otherwise: Trigger save function

### handleCtrlS(e)

Always triggers save function.

### handleCtrlI(e)

Always opens Choose Items modal.

### handleEscapeKey(e)

Closes modals in priority order:
1. Alert Modal
2. Save Options Modal
3. Batch Selection Modal
4. Choose Items Modal
5. Pending Challan Modal

---

## 4. Modal Keyboard Navigation

### State Variables
```javascript
let chooseItemsSelectedIndex = -1;  // Currently highlighted row in Choose Items
let batchSelectedIndex = -1;        // Currently highlighted row in Batch Modal
```

### Check Functions
```javascript
function isChooseItemsModalOpen() {
    const modal = document.getElementById('chooseItemsModal');
    return modal && modal.classList.contains('show');
}

function isBatchModalOpen() {
    const modal = document.getElementById('batchSelectionModal');
    return modal && modal.classList.contains('show');
}
```

### Choose Items Modal Keyboard

| Key | Action |
|-----|--------|
| ↓ Arrow Down | Navigate to next item row |
| ↑ Arrow Up | Navigate to previous item row |
| Enter | Select highlighted item (or first if on search) |
| F | Focus search input |
| Escape | Close modal |

```javascript
function handleChooseItemsModalKeyboard(e) {
    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            navigateChooseItemsModal('down');
            break;
        case 'ArrowUp':
            e.preventDefault();
            navigateChooseItemsModal('up');
            break;
        case 'Enter':
            if (document.activeElement.id === 'itemSearchInput') {
                // Select first visible item
                e.preventDefault();
                const visibleRows = document.querySelectorAll('#chooseItemsBody tr:not([style*="display: none"])');
                if (visibleRows.length > 0) {
                    chooseItemsSelectedIndex = 0;
                    visibleRows[0].click();
                }
                return;
            }
            e.preventDefault();
            selectCurrentChooseItem();
            break;
        case 'f':
        case 'F':
            if (!e.ctrlKey && !e.altKey) {
                e.preventDefault();
                document.getElementById('itemSearchInput')?.focus();
            }
            break;
    }
}
```

### Batch Modal Keyboard

Same pattern as Choose Items Modal.

### Save Options Modal Keyboard

| Key | Action |
|-----|--------|
| ←/↑ Arrow | Toggle to previous print format |
| →/↓ Arrow | Toggle to next print format |
| 1 | Select Full Page A4 |
| 2 | Select Half Page A5 |
| Enter / P | Print Invoice |
| D | Done (close and reload) |
| Escape | Close modal only |

---

## 5. Main Event Listener

```javascript
document.addEventListener('keydown', function(e) {
    // 1. Check if modals are open - delegate to modal handlers
    if (isChooseItemsModalOpen()) {
        handleChooseItemsModalKeyboard(e);
        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeChooseItemsModal();
            chooseItemsSelectedIndex = -1;
        }
        return; // Don't process main form keys
    }
    
    if (isBatchModalOpen()) {
        handleBatchModalKeyboard(e);
        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeBatchSelectionModal();
            batchSelectedIndex = -1;
        }
        return;
    }
    
    // 2. Handle main form key combinations
    switch (e.key) {
        case 'Enter':
            handleEnterKey(e);
            break;
        case 'ArrowDown':
        case 'ArrowUp':
        case 'ArrowLeft':
        case 'ArrowRight':
            handleArrowKeys(e);
            break;
        case 'End':
            handleEndKey(e);
            break;
        case 'Escape':
            handleEscapeKey(e);
            break;
        case 's':
        case 'S':
            if (e.ctrlKey) handleCtrlS(e);
            break;
        case 'i':
        case 'I':
            if (e.ctrlKey) handleCtrlI(e);
            break;
    }
}, true); // CAPTURE PHASE - fires before element handlers
```

**CRITICAL**: The `true` parameter enables **capture phase**, meaning this handler fires BEFORE any element-level handlers. This gives us priority control.

---

## 6. Row-Level Event Listeners

Added in `addRowEventListeners(row, rowIndex)` function:

### Code Field (Barcode Entry)
```javascript
codeInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const itemCode = this.value.trim();
        if (!itemCode) {
            // Empty - open Item Modal
            window.itemModalOpenedFromRowIndex = rowIndex; // Track for focus return
            openChooseItemsModal();
        } else {
            // Has barcode - fetch item and open batch modal
            fetchItemByBarcodeAndOpenBatchModal(itemCode, rowIndex);
        }
    }
});
```

### Qty → Free Qty → Rate → Discount Flow
```javascript
// Qty field
qtyInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        calculateRowAmount(rowIndex);
        freeQtyInput.focus();
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        navigateToRow(rowIndex - 1);
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        navigateToRow(rowIndex + 1);
    }
});

// Rate field (with MRP validation)
rateInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const saleRate = parseFloat(this.value) || 0;
        const mrp = parseFloat(mrpInput?.value) || 0;
        
        if (mrp > 0 && saleRate > mrp) {
            showToast('Sale Rate cannot be greater than MRP', 'error');
            this.focus();
            return;
        }
        
        calculateRowAmount(rowIndex);
        discountInput.focus();
    }
});

// Discount field (with options modal)
discountInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const currentValue = parseFloat(this.value) || 0;
        const originalValue = parseFloat(row.dataset.originalDiscount || 0);
        
        if (currentValue !== originalValue) {
            showDiscountOptionsModal(rowIndex, currentValue);
        } else {
            calculateRowAmount(rowIndex);
            moveToNextRowCodeField(rowIndex);
        }
    }
});
```

### Batch Field (Batch Change)
```javascript
batchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const batchValue = this.value.trim();
        const itemId = row.getAttribute('data-item-id');
        
        // If batch field is cleared AND row has an item, open batch modal
        if (!batchValue && itemId) {
            openBatchChangeModal(rowIndex);
        }
    }
});
```

---

## 7. Focus Management

### Auto-Focus on Page Load
```javascript
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const seriesSelect = document.getElementById('seriesSelect');
        if (seriesSelect) seriesSelect.focus();
    }, 200);
});
```

### Focus Return on Modal Close

Track which row opened the modal:
```javascript
window.itemModalOpenedFromRowIndex = null;

// When opening modal from code field
window.itemModalOpenedFromRowIndex = rowIndex;

// When closing modal
function closeChooseItemsModal() {
    // ... close modal ...
    
    if (window.itemModalOpenedFromRowIndex !== null) {
        const rowIndex = window.itemModalOpenedFromRowIndex;
        window.itemModalOpenedFromRowIndex = null;
        
        setTimeout(function() {
            const row = document.querySelector(`#itemsTableBody tr[data-row-index="${rowIndex}"]`);
            const codeInput = row?.querySelector('input[name*="[code]"]');
            if (codeInput) {
                codeInput.focus();
                codeInput.select();
            }
        }, 50);
    }
}
```

### Modal Open Hooks (Focus Search)
```javascript
const originalOpenItemsModal = window.openItemsModalDirectly;
window.openItemsModalDirectly = function() {
    originalOpenItemsModal.apply(this, arguments);
    chooseItemsSelectedIndex = -1;
    setTimeout(() => {
        document.getElementById('itemSearchInput')?.focus();
    }, 100);
};
```

---

## 8. State Tracking

### passedRemarksField
Tracks whether user has navigated past the remarks field (last header field).
- Set to `true` when Enter pressed on remarks field
- Used to enable fallback: if focus is on readonly/body after passing remarks, reopen Choose Items modal
- Reset when user clicks/focuses on any header field

```javascript
let passedRemarksField = false;

// Reset when focusing header fields
const headerFieldsToWatch = ['seriesSelect', 'invoiceNo', 'saleDate', ...];
headerFieldsToWatch.forEach(fieldId => {
    document.getElementById(fieldId)?.addEventListener('focus', function() {
        if (passedRemarksField) {
            passedRemarksField = false;
        }
    });
});

// Expose reset function globally
window.resetRemarksNavigationState = function() {
    passedRemarksField = false;
};
```

---

## 9. Visual Focus Indicator

```javascript
const focusStyle = document.createElement('style');
focusStyle.textContent = `
    /* Enhanced focus styles */
    .form-control:focus,
    select:focus,
    input:focus {
        outline: 2px solid #0d6efd !important;
        outline-offset: 1px;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
    }
    
    /* Items table row highlight */
    #itemsTableBody tr:focus-within {
        background-color: #e7f3ff !important;
    }
    
    /* Modal row selection */
    #chooseItemsBody tr.item-row-selected,
    #batchSelectionBody tr.item-row-selected {
        background-color: #007bff !important;
        color: white !important;
    }
`;
document.head.appendChild(focusStyle);
```

---

## 10. Key Differences for Modification Blades

When implementing for Modification blades (vs Transaction blades), consider:

| Aspect | Transaction | Modification |
|--------|-------------|--------------|
| Entry Point | Series Select | Invoice No field |
| First Action | Create new transaction | Load existing transaction |
| Invoice Field | Auto-generated, readonly | Editable, Enter loads transaction |
| Empty Invoice + Enter | N/A | Open Past Invoices modal |
| Items Table | Start empty | Populated from loaded transaction |
| passedRemarksField | Applicable | May not be needed |
| Save Function | `saveSale()` | Same or similar |

### Modification-Specific Features

1. **Invoice No Enter Handler**
```javascript
invoiceNoInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopPropagation();
        const invoiceNo = this.value.trim();
        if (invoiceNo) {
            loadTransactionByInvoiceNo(invoiceNo);
        } else {
            openAllInvoicesModal(); // Past invoices modal
        }
    }
});
```

2. **Past Invoices Modal Keyboard**
Same pattern as Choose Items modal - arrow navigation, Enter to select, F for search, Escape to close.

---

## 11. Implementation Checklist

When implementing keyboard navigation in a new blade:

- [ ] Create CONFIG object with selectors
- [ ] Implement utility functions (getFocusableElements, focusNextElement, focusTableCell, etc.)
- [ ] Implement key handlers (handleEnterKey, handleArrowKeys, handleEndKey, handleEscapeKey)
- [ ] Implement modal keyboard handlers for each modal
- [ ] Add main document keydown listener with **capture phase** (`true`)
- [ ] Add row-level keydown listeners in `addRowEventListeners`
- [ ] Implement focus return mechanism (track which row opened modal)
- [ ] Add auto-focus on page load
- [ ] Add visual focus styles
- [ ] Add state tracking variables if needed
- [ ] Add modal open hooks to focus search fields
- [ ] Test all keyboard flows end-to-end

---

## 12. Console Logging

For debugging, the system logs:
```javascript
console.log('🎹 Keyboard Navigation System Loaded');
console.log('   Enter → Next field | Shift+Enter → Previous field');
console.log('   Arrow Keys → Navigate dropdown/table');
console.log('   End → Save | Ctrl+S → Save | Ctrl+I → Choose Items');
console.log('   In Modals: ↑↓ Navigate | Enter Select | F → Search | Esc → Close');
```

---

## 13. Event Propagation

**CRITICAL for avoiding double-triggers:**

```javascript
// In modal handlers and escape handlers, use all three:
e.preventDefault();        // Prevent default browser action
e.stopPropagation();       // Stop event from bubbling up
e.stopImmediatePropagation(); // Stop other handlers on same element
```

The main listener uses **capture phase** (`true`) to fire before element-level handlers. When processing modals, we `return` early to prevent main form handlers from running.

---

## Summary

The keyboard navigation system provides:
1. **Field-to-field navigation** with Enter key
2. **Table cell navigation** with Arrow keys
3. **Quick save** with End or Ctrl+S
4. **Modal keyboard support** with arrow navigation, Enter selection, F for search
5. **Focus management** with auto-focus and focus return
6. **Visual feedback** with focus indicators and row highlighting
7. **State tracking** for smart behavior (passedRemarksField, modal row indices)
8. **Proper event handling** to avoid double-triggers and conflicts
