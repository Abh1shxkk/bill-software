# Godown Breakage Expiry Module - Implementation Plan

## Overview
Migrate the Godown Breakage Expiry module (both transaction and modification blades) from custom modal implementation to reusable modal components, while preserving the unique `targetRowIndex` pattern that allows both creating new rows and updating existing rows.

## Current State Analysis

### Transaction Blade
- **File**: `resources/views/admin/godown-breakage-expiry/transaction.blade.php`
- **Status**: Has reusable components included but uses legacy functions
- **Modal IDs**: Generic (`chooseItemsModal`, `batchSelectionModal`)
- **Unique Feature**: `showItemSelectionModal(targetRowIndex)` parameter
  - `targetRowIndex === null` → Create new row
  - `targetRowIndex` provided → Update existing row at that index
- **Rate Type**: `s_rate` (correct for breakage/expiry valuation)
- **Show Only Available**: `true` (correct - can only mark existing stock)

### Modification Blade
- **File**: `resources/views/admin/godown-breakage-expiry/modification.blade.php`
- **Status**: MISSING reusable components, uses legacy custom modals
- **Needs**: Complete migration to reusable components

## Implementation Steps

### Phase 1: Transaction Blade Migration

#### Step 1.1: Update Modal IDs
```php
// Change from:
@include('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    ...
])

// To:
@include('components.modals.item-selection', [
    'id' => 'godownBreakageExpiryItemModal',
    'module' => 'godown-breakage-expiry',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'godownBreakageExpiryBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'godownBreakageExpiryBatchModal',
    'module' => 'godown-breakage-expiry',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])
```

#### Step 1.2: Add Bridge Function with targetRowIndex Support
```javascript
// Add BEFORE legacy functions
function onItemBatchSelectedFromModal(itemData, batchData) {
    console.log('🎯 Godown Breakage Expiry: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Godown Breakage Expiry: Invalid item data received');
        return;
    }
    
    const tbody = document.getElementById('itemsTableBody');
    
    // Check if we should update existing row or create new one
    const targetRowIndex = window.targetRowIndexForModal;
    const shouldCreateNewRow = (targetRowIndex === null || targetRowIndex === undefined);
    
    if (shouldCreateNewRow) {
        // CREATE NEW ROW
        const rowIndex = currentRowIndex++;
        const row = document.createElement('tr');
        row.id = `row-${rowIndex}`;
        row.dataset.rowIndex = rowIndex;
        row.dataset.itemId = itemData.id;
        row.onclick = function() { selectRow(rowIndex); };
        
        // Store item and batch data
        row.dataset.itemData = JSON.stringify({
            packing: itemData.packing || '',
            unit: itemData.unit || '1',
            mrp: itemData.mrp || 0,
            s_rate: itemData.s_rate || 0,
            p_rate: itemData.p_rate || itemData.pur_rate || 0,
            company_name: itemData.company_name || ''
        });
        
        if (batchData && batchData.id) {
            row.dataset.batchId = batchData.id;
            row.dataset.batchData = JSON.stringify({
                qty: batchData.qty || batchData.available_qty || 0,
                location: batchData.location || ''
            });
        }
        
        const cost = batchData?.pur_rate || batchData?.cost || itemData.p_rate || itemData.pur_rate || 0;
        const qty = 1;
        const amount = (qty * cost).toFixed(2);
        
        row.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemData.id || ''}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemData.name || ''}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchData?.batch_no || ''}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${batchData?.expiry_display || batchData?.expiry || ''}" readonly></td>
            <td>
                <select class="form-select form-select-sm" name="items[${rowIndex}][br_ex_type]">
                    <option value="BREAKAGE">Brk</option>
                    <option value="EXPIRY">Exp</option>
                </select>
            </td>
            <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${qty}" onchange="calculateRowAmount(${rowIndex})"></td>
            <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][cost]" value="${cost}" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
            <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="${amount}" step="0.01" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${itemData.id || ''}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchData?.id || ''}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${itemData.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${itemData.unit || '1'}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${itemData.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="${itemData.mrp || 0}">
            <input type="hidden" name="items[${rowIndex}][s_rate]" value="${itemData.s_rate || 0}">
            <input type="hidden" name="items[${rowIndex}][p_rate]" value="${cost}">
        `;
        
        tbody.appendChild(row);
        row.classList.add('row-complete');
        selectRow(rowIndex);
        calculateRowAmount(rowIndex);
        
        console.log('✅ Godown Breakage Expiry: New row created successfully', rowIndex);
        
        // Focus qty field
        setTimeout(() => {
            row.querySelector('input[name*="[qty]"]')?.focus();
        }, 100);
        
    } else {
        // UPDATE EXISTING ROW
        const row = document.getElementById(`row-${targetRowIndex}`);
        if (!row) {
            console.error('❌ Godown Breakage Expiry: Target row not found', targetRowIndex);
            return;
        }
        
        row.dataset.itemId = itemData.id;
        row.dataset.itemData = JSON.stringify({
            packing: itemData.packing || '',
            unit: itemData.unit || '1',
            mrp: itemData.mrp || 0,
            s_rate: itemData.s_rate || 0,
            p_rate: itemData.p_rate || itemData.pur_rate || 0,
            company_name: itemData.company_name || ''
        });
        
        if (batchData && batchData.id) {
            row.dataset.batchId = batchData.id;
            row.dataset.batchData = JSON.stringify({
                qty: batchData.qty || batchData.available_qty || 0,
                location: batchData.location || ''
            });
        }
        
        const cost = batchData?.pur_rate || batchData?.cost || itemData.p_rate || itemData.pur_rate || 0;
        
        // Update row fields
        row.querySelector('input[name*="[code]"]').value = itemData.id || '';
        row.querySelector('input[name*="[name]"]').value = itemData.name || '';
        row.querySelector('input[name*="[batch]"]').value = batchData?.batch_no || '';
        row.querySelector('input[name*="[expiry]"]').value = batchData?.expiry_display || batchData?.expiry || '';
        row.querySelector('input[name*="[cost]"]').value = cost;
        row.querySelector('input[name*="[item_id]"]').value = itemData.id || '';
        row.querySelector('input[name*="[batch_id]"]').value = batchData?.id || '';
        row.querySelector('input[name*="[packing]"]').value = itemData.packing || '';
        row.querySelector('input[name*="[unit]"]').value = itemData.unit || '1';
        row.querySelector('input[name*="[company_name]"]').value = itemData.company_name || '';
        row.querySelector('input[name*="[mrp]"]').value = itemData.mrp || 0;
        row.querySelector('input[name*="[s_rate]"]').value = itemData.s_rate || 0;
        row.querySelector('input[name*="[p_rate]"]').value = cost;
        
        row.classList.add('row-complete');
        calculateRowAmount(targetRowIndex);
        
        console.log('✅ Godown Breakage Expiry: Existing row updated successfully', targetRowIndex);
    }
    
    // Update footer
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '1';
    document.getElementById('p_rate').value = itemData.p_rate || itemData.pur_rate || '0';
    document.getElementById('s_rate').value = itemData.s_rate || '0';
    document.getElementById('mrp').value = itemData.mrp || '0';
    document.getElementById('company_name').value = itemData.company_name || '';
    document.getElementById('cl_qty').value = batchData?.qty || batchData?.available_qty || '0';
    document.getElementById('location').value = batchData?.location || '';
    
    calculateTotalAmount();
    
    // Clear target row index
    window.targetRowIndexForModal = null;
    
    console.log('✅ Godown Breakage Expiry: Footer updated, targetRowIndex cleared');
}
```

#### Step 1.3: Add showItemSelectionModal Bridge Function
```javascript
// Add BEFORE legacy functions
function showItemSelectionModal(targetRowIndex = null) {
    console.log('🎯 Godown Breakage Expiry: showItemSelectionModal called', {targetRowIndex});
    
    // Store targetRowIndex for later use
    window.targetRowIndexForModal = targetRowIndex;
    
    // Check if modal component function exists
    if (typeof window.openItemModal_godownBreakageExpiryItemModal === 'function') {
        console.log('✅ Godown Breakage Expiry: Opening reusable item modal');
        window.openItemModal_godownBreakageExpiryItemModal();
    } else {
        console.error('❌ Godown Breakage Expiry: openItemModal_godownBreakageExpiryItemModal function not found. Modal component may not be loaded.');
        alert('Error: Item selection modal not available. Please refresh the page.');
    }
}
```

#### Step 1.4: Rename Legacy Functions
```javascript
// Rename all legacy functions with _legacy_ prefix:
function _legacy_showItemSelectionModal(targetRowIndex = null) { ... }
function _legacy_renderItemsList(filter = '') { ... }
function _legacy_filterItems() { ... }
function _legacy_selectItem(itemId) { ... }
function _legacy_showBatchModalForItem(item) { ... }
function _legacy_selectBatchAndCreateRow(...) { ... }
function _legacy_closeBatchModalAndClear() { ... }
function _legacy_closeItemModal() { ... }
function _legacy_showBatchModal(rowIndex) { ... }
// ... etc
```

#### Step 1.5: Update onclick Calls in Legacy Modal HTML
- Search for any onclick calls in legacy modal HTML
- Update them to call `_legacy_` prefixed functions

#### Step 1.6: Add Enhanced Logging
- Already included in bridge functions above

### Phase 2: Modification Blade Migration

#### Step 2.1: Add Modal Component Includes
```php
// Add AFTER @endsection, BEFORE @push('scripts')
@include('components.modals.item-selection', [
    'id' => 'godownBreakageExpiryModItemModal',
    'module' => 'godown-breakage-expiry-mod',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'godownBreakageExpiryModBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'godownBreakageExpiryModBatchModal',
    'module' => 'godown-breakage-expiry-mod',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])
```

#### Step 2.2: Add Bridge Functions
- Same as transaction blade but with `godownBreakageExpiryModItemModal` and `godownBreakageExpiryModBatchModal`
- Same targetRowIndex support
- Update console.log messages to say "Godown Breakage Expiry Mod"

#### Step 2.3: Rename Legacy Functions
- Same pattern as transaction blade

#### Step 2.4: Check for Duplicate Functions
- Search for any duplicate function definitions
- Rename duplicates with `_legacy_` prefix

## Testing Checklist

### Transaction Blade
- [ ] Click "Add Items" button (targetRowIndex = null)
- [ ] Select item from new modal
- [ ] Select batch from new modal
- [ ] Verify new row created with all fields
- [ ] Click on existing row's code field (targetRowIndex provided)
- [ ] Select different item from modal
- [ ] Verify existing row updated with new item/batch
- [ ] Verify footer updates correctly
- [ ] Verify calculations work
- [ ] No old green modal appears
- [ ] Clear browser cache (Ctrl+Shift+R)

### Modification Blade
- [ ] Load Invoice button works
- [ ] Click "Add Items" button
- [ ] Select item and batch
- [ ] Verify new row created
- [ ] Click on existing row code field
- [ ] Verify row updates
- [ ] All fields populated correctly
- [ ] No old modal appears
- [ ] Clear browser cache

## Key Differences from Other Modules

| Feature | Other Modules | Godown Breakage Expiry |
|---------|---------------|------------------------|
| Row Creation | Always create new | Create OR update existing |
| targetRowIndex | Not used | Used to determine create vs update |
| Modal Call | `showItemSelectionModal()` | `showItemSelectionModal(targetRowIndex)` |
| Bridge Function | Simple create | Conditional create/update |
| Use Case | Add items only | Add OR modify items inline |

## Implementation Order

1. ✅ Create implementation plan (this document)
2. ⏳ Implement transaction blade changes
3. ⏳ Test transaction blade thoroughly
4. ⏳ Implement modification blade changes
5. ⏳ Test modification blade thoroughly
6. ⏳ Create completion document

## Notes

- The `targetRowIndex` pattern is unique to this module
- Must preserve this functionality during migration
- Bridge function must handle both create and update scenarios
- Legacy functions kept as fallback (with `_legacy_` prefix)
- Enhanced logging helps debug issues
- User must clear browser cache to see changes

