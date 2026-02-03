# Sample Received Module - Implementation Plan

## Overview
Migrate Sample Received transaction and modification blades from legacy custom modals to reusable modal components, following the same pattern as Sample Issued.

## Phase 1: Transaction Blade Updates

### Step 1.1: Update Modal IDs
**File**: `resources/views/admin/sample-received/transaction.blade.php`

**Change**:
```php
<!-- FROM -->
@include('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    ...
    'batchModalId' => 'batchSelectionModal',
])

@include('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    ...
])

<!-- TO -->
@include('components.modals.item-selection', [
    'id' => 'sampleReceivedItemModal',
    ...
    'batchModalId' => 'sampleReceivedBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'sampleReceivedBatchModal',
    ...
])
```

### Step 1.2: Add Bridge Functions
**Location**: After `updatePartyName()` function, before legacy modal functions

**Add**:
```javascript
// ============ REUSABLE MODAL BRIDGE FUNCTION ============
function onItemBatchSelectedFromModal(itemData, batchData) {
    console.log('🎯 Sample Received: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Sample Received: Invalid item data received');
        return;
    }
    
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = itemData.id;
    row.dataset.itemData = JSON.stringify(itemData);
    if (batchData && batchData.id) {
        row.dataset.batchId = batchData.id;
        row.dataset.batchData = JSON.stringify(batchData);
    }
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemData.id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemData.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchData?.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${batchData?.expiry_formatted || ''}" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" value="${parseFloat(batchData?.s_rate || itemData.s_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${itemData.id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchData?.id || ''}">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${itemData.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][unit]" value="${itemData.unit || '1'}">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="${itemData.company_name || ''}">
        <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${itemData.hsn_code || ''}">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="${itemData.mrp || 0}">
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '1';
    document.getElementById('cl_qty').value = batchData?.qty || 0;
    
    console.log('✅ Sample Received: Row created successfully', {rowIndex, itemId: itemData.id, batchId: batchData?.id});
    
    row.querySelector('input[name*="[qty]"]')?.focus();
}

// ============ SHOW ITEM SELECTION MODAL (BRIDGE TO REUSABLE COMPONENT) ============
function showItemSelectionModal() {
    console.log('🔗 Sample Received: showItemSelectionModal called - opening reusable modal');
    
    if (typeof window.openItemModal_sampleReceivedItemModal === 'function') {
        window.openItemModal_sampleReceivedItemModal();
    } else {
        console.error('❌ Sample Received: openItemModal_sampleReceivedItemModal function not found.');
        alert('Error: Modal component not loaded. Please refresh the page.');
    }
}
```

### Step 1.3: Rename Legacy Functions
**Change all legacy function names**:
- `showItemSelectionModal()` → `_legacy_showItemSelectionModal()`
- `selectItemFromModal()` → `_legacy_selectItemFromModal()`
- `showBatchSelectionForItem()` → `_legacy_showBatchSelectionForItem()`
- `showBatchSelectionModal()` → `_legacy_showBatchSelectionModal()`
- `skipBatchSelection()` → `_legacy_skipBatchSelection()`
- `selectBatchFromModal()` → `_legacy_selectBatchFromModal()`

### Step 1.4: Update Legacy Modal HTML Calls
Update all onclick calls in legacy modal HTML to use `_legacy_` prefix:
- `onclick="selectItemFromModal(...)` → `onclick="_legacy_selectItemFromModal(...)`
- `onclick="skipBatchSelection(...)` → `onclick="_legacy_skipBatchSelection(...)`
- `onclick="selectBatchFromModal(...)` → `onclick="_legacy_selectBatchFromModal(...)`

Update function calls:
- `showBatchSelectionForItem(...)` → `_legacy_showBatchSelectionForItem(...)`
- `showBatchSelectionModal(...)` → `_legacy_showBatchSelectionModal(...)`

## Phase 2: Modification Blade Updates

### Step 2.1: Add Modal Component Includes
**File**: `resources/views/admin/sample-received/modification.blade.php`
**Location**: After `@endsection`, before `@push('scripts')`

**Add**:
```php
<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'sampleReceivedModItemModal',
    'module' => 'sample-received-mod',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'sampleReceivedModBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'sampleReceivedModBatchModal',
    'module' => 'sample-received-mod',
    'showOnlyAvailable' => false,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])
```

### Step 2.2: Add Bridge Functions
**Location**: After `updatePartyName()` function, before legacy modal functions

**Add**:
```javascript
// ============ REUSABLE MODAL BRIDGE FUNCTION ============
function onItemBatchSelectedFromModal(itemData, batchData) {
    console.log('🎯 Sample Received Mod: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Sample Received Mod: Invalid item data received');
        return;
    }
    
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = itemData.id;
    row.dataset.itemData = JSON.stringify(itemData);
    if (batchData && batchData.id) {
        row.dataset.batchId = batchData.id;
        row.dataset.batchData = JSON.stringify(batchData);
    }
    row.onclick = function() { selectRow(rowIndex); };
    row.className = 'row-complete';
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemData.id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemData.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchData?.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${batchData?.expiry_formatted || ''}" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${batchData?.qty || 0}" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" value="${parseFloat(batchData?.s_rate || itemData.s_rate || 0).toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="0.00" step="0.01" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${itemData.id}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchData?.id || ''}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${itemData.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${itemData.unit || ''}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${itemData.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${itemData.hsn_code || ''}">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="${itemData.mrp || 0}">
        </td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '';
    document.getElementById('cl_qty').value = batchData?.qty || 0;
    
    calculateRowAmount(rowIndex);
    
    console.log('✅ Sample Received Mod: Row created successfully', {rowIndex, itemId: itemData.id, batchId: batchData?.id});
}

// ============ SHOW ITEM SELECTION MODAL (BRIDGE TO REUSABLE COMPONENT) ============
function showItemSelectionModal() {
    console.log('🔗 Sample Received Mod: showItemSelectionModal called - opening reusable modal');
    
    if (typeof window.openItemModal_sampleReceivedModItemModal === 'function') {
        window.openItemModal_sampleReceivedModItemModal();
    } else {
        console.error('❌ Sample Received Mod: openItemModal_sampleReceivedModItemModal function not found.');
        alert('Error: Modal component not loaded. Please refresh the page.');
    }
}
```

### Step 2.3: Rename Legacy Functions
Same as transaction blade - rename all legacy functions with `_legacy_` prefix.

### Step 2.4: Update Legacy Modal HTML Calls
Same as transaction blade - update all onclick calls to use `_legacy_` prefix.

## Phase 3: Testing

### Test Transaction Blade
1. Clear browser cache (Ctrl+Shift+R)
2. Navigate to Sample Received transaction page
3. Click "Add Items" button
4. Verify new reusable modal opens (not old green modal)
5. Select an item
6. Verify batch modal opens
7. Select a batch (or skip)
8. Verify row created with all fields
9. Verify footer updated (packing, unit, cl_qty)
10. Verify console shows correct logging messages

### Test Modification Blade
1. Clear browser cache (Ctrl+Shift+R)
2. Navigate to Sample Received modification page
3. Click "Load Invoice" button
4. Load an existing transaction
5. Verify data populated correctly
6. Click "Add Items" button
7. Verify new reusable modal opens
8. Select item and batch
9. Verify row created correctly
10. Update transaction
11. Verify no errors

## Success Criteria

### Transaction Blade
- ✅ Modal IDs updated to descriptive names
- ✅ Bridge functions implemented
- ✅ Legacy functions renamed
- ✅ Enhanced logging added
- ✅ No old green modal appears
- ✅ All fields populated correctly
- ✅ Footer updates correctly

### Modification Blade
- ✅ Modal components added
- ✅ Bridge functions implemented
- ✅ Legacy functions renamed
- ✅ Enhanced logging added
- ✅ Load Invoice works
- ✅ No old green modal appears
- ✅ All fields populated correctly
- ✅ Update transaction works

## Rollback Plan
If issues occur:
1. Revert changes to transaction.blade.php
2. Revert changes to modification.blade.php
3. Clear browser cache
4. Test with legacy modals

## Notes
- Sample Received uses `showOnlyAvailable: false` because it can receive new batches
- Rate type is `s_rate` for consistency with Sample Issued
- Both incoming and outgoing samples valued at sale rate
- Enhanced logging uses "Sample Received" and "Sample Received Mod" prefixes
