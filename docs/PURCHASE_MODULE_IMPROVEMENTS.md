# Purchase Module Improvements Plan

This document outlines the steps required to implement the new Item/Batch selection flow and navigation improvements in the Purchase Transaction and Purchase Modification modules. This mirrors the changes recently applied to the Sales module.

## Objectives

1.  **Integrate New Modal Components**: Replace disparate modal logic with the reusable `item-selection` and `batch-selection` components.
2.  **Enhance Keyboard Navigation**:
    *   **Code Field**:
        *   `Enter` (Empty): Open Item Selection Modal.
        *   `Enter` (Barcode): Fetch item -> Open Batch Modal -> Populate Row.
    *   **Discount Field** (`dis_percent`): `Enter` should move focus to the **Code field of the next row** (creating a new row if necessary).
3.  **UI/UX Improvements**:
    *   Make **Item Name** field (`[name]`) **readonly** after it is populated to prevent accidental edits.

---

## 1. Files to Modify

*   `resources/views/admin/purchase/transaction.blade.php`
*   `resources/views/admin/purchase/modification.blade.php`

## 2. Implementation Steps

### Step A: Include Modal Components

Add the following includes at the bottom of the content section (before the scripts), ensuring `showPurchaseRate` is enabled if applicable.

```blade
<!-- Reusable Item Selection Modal Component -->
@include('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'purchase',
    'showStock' => true,
    'rateType' => 'pur_rate', // Use purchase rate
    'showCompany' => true,
    'showHsn' => true,
    'batchModalId' => 'batchSelectionModal',
])

<!-- Reusable Batch Selection Modal Component -->
@include('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'purchase',
    'showOnlyAvailable' => false, // Allow all batches or new batches for purchase
    'rateType' => 'pur_rate',
    'showCostDetails' => true,
    'showSupplier' => true,
    'showPurchaseRate' => true
])
```

### Step B: Add Bridge Script

Add the bridge logic to connect the modals with the table. This must handle `pendingBarcodeRowIndex` to decide whether to populate an existing row (barcode scan) or add a new one.

**Key Logic:**
```javascript
// Track which row barcode was entered for
if (typeof window.pendingBarcodeRowIndex === 'undefined') {
    window.pendingBarcodeRowIndex = null;
}

// Override openChooseItemsModal (if button exists)
window.openChooseItemsModal = function() {
    if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    }
};

// Callback when item and batch are selected
window.onItemBatchSelectedFromModal = function(item, batch) {
    // Check if this is from barcode entry (existing row) or generic add (new row)
    if (window.pendingBarcodeRowIndex !== null) {
        // Populate existing row
        populateRowWithItemAndBatch(window.pendingBarcodeRowIndex, item, batch);
        window.pendingBarcodeRowIndex = null;
    } else {
        // Add new row
        // Note: Check existing addRow/addItem function in purchase module
        addItemToTable(item, batch); 
    }
    
    // Cleanup
    window.selectedBatch = null;
    pendingItemSelection = null;
};

// Reset on close
window.closeBatchSelectionModal = function() {
    if (typeof closeBatchModal_batchSelectionModal === 'function') {
        closeBatchModal_batchSelectionModal();
    }
    window.pendingBarcodeRowIndex = null;
};
```

### Step C: Implement Helper Functions

Add these functions to handle the specific logic for Purchase (Note field names differ from Sales):

1.  **`moveToNextRowCodeField(currentRowIndex)`**:
    *   Find the next row.
    *   If not found, call `addNewRow()` (or equivalent).
    *   Focus on the `[code]` input of the next row.

2.  **`fetchItemByBarcodeAndOpenBatchModal(barcode, rowIndex)`**:
    *   Set `window.pendingBarcodeRowIndex = rowIndex`.
    *   Fetch item via API (`/admin/api/items/search?search=...&exact=1`).
    *   On success: Open Batch Modal (`openBatchSelectionModal(item)`).
    *   On fail: Alert and reset `pendingBarcodeRowIndex`.

3.  **`populateRowWithItemAndBatch(rowIndex, item, batch)`**:
    *   Target specific row by index.
    *   Fill fields: `[code]`, `[name]`, `[batch]`, `[qty]`, `[pur_rate]`, `[mrp]`, etc.
    *   **Critical**: Set `[name]` field to `readonly`.
    *   Trigger necessary calculation updates (e.g., `calculateAndSaveGstForRow`).

### Step D: Update Event Listeners

In `addRowNavigationWithMrpModal` (or `addRowEventListeners`):

1.  **Code Input (`[code]`) Keydown**:
    ```javascript
    if (e.key === 'Enter') {
        e.preventDefault();
        const code = this.value.trim();
        if (!code) {
            // Empty -> Open Item Modal
            openChooseItemsModal(); 
        } else {
            // Barcode -> Fetch & Batch Modal
            fetchItemByBarcodeAndOpenBatchModal(code, rowIndex);
        }
    }
    ```

2.  **Discount Input (`[dis_percent]`) Keydown**:
    ```javascript
    if (e.key === 'Enter') {
        e.preventDefault();
        // ... existing change detection logic ...
        
        // Instead of moving to S.Rate, move to NEXT ROW Code
        moveToNextRowCodeField(rowIndex);
    }
    ```

## 3. Specific Considerations for Purchase Module

*   **Field Names**:
    *   Item Name: `items[i][name]` (not `item_name`)
    *   Discount: `items[i][dis_percent]` (not `discount`)
    *   Rate: `items[i][pur_rate]`
*   **Calculations**: Purchase module involves complex GST/HSN calculations (`fetchItemDetailsForCalculation`). Ensure `populateRowWithItemAndBatch` triggers these updates so functionality isn't broken.
