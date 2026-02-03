# Stock Transfer Outgoing Return - Implementation Plan

## Overview
Update Stock Transfer Outgoing Return module (transaction and modification) to use descriptive modal IDs.

## Target Modal IDs

### Transaction Blade
```
Item Modal: stockTransferOutgoingReturnItemModal
Batch Modal: stockTransferOutgoingReturnBatchModal
```

### Modification Blade
```
Item Modal: stockTransferOutgoingReturnModItemModal
Batch Modal: stockTransferOutgoingReturnModBatchModal
```

## Implementation Steps

### Step 1: Update Transaction Blade

#### 1.1 Update Modal Includes
**Find**:
```php
'id' => 'chooseItemsModal',
'batchModalId' => 'batchSelectionModal',
```

**Replace with**:
```php
'id' => 'stockTransferOutgoingReturnItemModal',
'batchModalId' => 'stockTransferOutgoingReturnBatchModal',
```

#### 1.2 Update openInsertItemsModal Function
**Find**:
```javascript
function openInsertItemsModal() {
    if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    }
}
```

**Replace with**:
```javascript
function openInsertItemsModal() {
    console.log('📦 Opening stock transfer outgoing return item modal');
    if (typeof openItemModal_stockTransferOutgoingReturnItemModal === 'function') {
        openItemModal_stockTransferOutgoingReturnItemModal();
    } else {
        console.error('❌ Item modal function not found');
    }
}
```

#### 1.3 Enhance Main Callback
Add enhanced logging to `window.onItemBatchSelectedFromModal`

### Step 2: Update Modification Blade

#### 2.1 Update Modal Includes
**Find**:
```php
'id' => 'chooseItemsModal',
'batchModalId' => 'batchSelectionModal',
```

**Replace with**:
```php
'id' => 'stockTransferOutgoingReturnModItemModal',
'batchModalId' => 'stockTransferOutgoingReturnModBatchModal',
```

#### 2.2 Update Functions
- Update `openInsertItemsModal()`
- Update `openItemModal(rowIndex)`
- Add enhanced logging

## Testing Checklist

- [ ] Transaction page loads without errors
- [ ] Modification page loads without errors
- [ ] Insert Items button works on both pages
- [ ] Item selection opens batch modal
- [ ] Batch selection creates/populates rows
- [ ] No modal conflicts between pages
- [ ] Console logging shows correct messages

## Success Criteria

- ✅ Descriptive modal IDs in use
- ✅ No JavaScript errors
- ✅ All modals function correctly
- ✅ Enhanced logging visible
- ✅ No conflicts with other modules
