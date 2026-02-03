# Stock Transfer Incoming - Implementation Plan

## Overview
Update Stock Transfer Incoming module (transaction and modification) to use descriptive modal IDs.

## Target Modal IDs

### Transaction Blade
```
Item Modal: stockTransferIncomingItemModal
Batch Modal: stockTransferIncomingBatchModal
```

### Modification Blade
```
Item Modal: stockTransferIncomingModItemModal
Batch Modal: stockTransferIncomingModBatchModal
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
'id' => 'stockTransferIncomingItemModal',
'batchModalId' => 'stockTransferIncomingBatchModal',
```

#### 1.2 Update showItemSelectionModal Function
**Find**:
```javascript
function showItemSelectionModal() {
    if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    }
}
```

**Replace with**:
```javascript
function showItemSelectionModal() {
    console.log('📦 Opening stock transfer incoming item modal');
    if (typeof openItemModal_stockTransferIncomingItemModal === 'function') {
        openItemModal_stockTransferIncomingItemModal();
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
'id' => 'stockTransferIncomingModItemModal',
'batchModalId' => 'stockTransferIncomingModBatchModal',
```

#### 2.2 Update Functions
- Update `showItemSelectionModal()`
- Add enhanced logging

## Key Configuration Notes

### Rate Type: `pur_rate` ✅
- Incoming transfers are valued at purchase rate
- This is DIFFERENT from outgoing transfers (which use `s_rate`)
- Configuration is correct and should NOT be changed

### Show Only Available: `false` ✅
- Incoming transfers can create new batches
- This is DIFFERENT from outgoing transfers (which use `true`)
- Configuration is correct and should NOT be changed

### Show Cost Details: `true` ✅
- Cost information is relevant for incoming transfers
- This is DIFFERENT from outgoing transfers (which use `false`)
- Configuration is correct and should NOT be changed

## Testing Checklist

- [ ] Transaction page loads without errors
- [ ] Modification page loads without errors
- [ ] Insert Items button works on both pages
- [ ] Item selection opens batch modal
- [ ] Batch selection creates/populates rows
- [ ] No modal conflicts between pages
- [ ] Console logging shows correct messages
- [ ] Rate type remains `pur_rate`
- [ ] Show only available remains `false`
- [ ] Show cost details remains `true`

## Success Criteria

- ✅ Descriptive modal IDs in use
- ✅ No JavaScript errors
- ✅ All modals function correctly
- ✅ Enhanced logging visible
- ✅ No conflicts with other modules
- ✅ Configuration settings unchanged (pur_rate, showOnlyAvailable: false, showCostDetails: true)
