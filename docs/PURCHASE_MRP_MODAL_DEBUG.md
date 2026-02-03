# Purchase Transaction - MRP Modal Debugging Guide

## Current Status

The "Add Item" functionality is adding rows correctly with all attributes, but the MRP modal is not opening when pressing Enter on the Free Qty field.

## Debug Logs Added

Added comprehensive console logging to track the issue:

### In `addItemToTableWithoutBatch()`:
```javascript
console.log('🔍 Row appended to tbody, checking elements...');
console.log('🔍 F.Qty input:', row.querySelector('.item-fqty'));
console.log('🔍 Row index:', newIndex);
console.log('🔍 About to call addRowNavigationWithMrpModal...');
console.log('✅ addRowNavigationWithMrpModal called');
console.log('✅ addAmountCalculation called');
```

### In `addRowNavigationWithMrpModal()`:
```javascript
console.log('🎯 addRowNavigationWithMrpModal called for row:', rowIndex);
console.log('🎯 Found inputs:', inputs.length);
console.log('✅ Found F.Qty field, attaching listeners...');
```

## Testing Steps

### Step 1: Clear Everything
```
1. Clear browser cache (Ctrl+Shift+Delete)
2. Hard refresh (Ctrl+F5)
3. Open browser console (F12)
```

### Step 2: Add Item
```
1. Click "Add Item" button
2. Select an item
3. Watch console for logs
```

### Step 3: Expected Console Output
```
🎯 Opening Add Item Flow (No Batch Modal)
✅ Item selected from modal (Purchase - No Batch): [ITEM NAME]
➕ Adding item to new row X: [ITEM NAME]
🔍 Row appended to tbody, checking elements...
🔍 F.Qty input: <input...>  (should show the input element)
🔍 Row index: X
🔍 About to call addRowNavigationWithMrpModal...
🎯 addRowNavigationWithMrpModal called for row: X
🎯 Found inputs: 9  (should be 9 non-readonly inputs)
✅ Found F.Qty field, attaching listeners...
✅ addRowNavigationWithMrpModal called
✅ addAmountCalculation called
✅ Row X added - Purchase rate: XXX.XX, User will enter batch/qty
```

### Step 4: Test MRP Modal
```
1. Enter batch number
2. Enter expiry date
3. Enter quantity
4. Enter Free Qty (e.g., 10)
5. Press Enter on Free Qty field
6. Watch console for:
   "F.Qty Enter pressed, rowIndex: X"
7. MRP modal should open
```

## Troubleshooting

### Issue 1: F.Qty input is null
**Symptom**: Console shows `🔍 F.Qty input: null`

**Cause**: The `.item-fqty` class is missing from the Free Qty input

**Fix**: Check the row HTML, ensure:
```javascript
<input type="number" class="form-control item-fqty" ...>
```

### Issue 2: No inputs found
**Symptom**: Console shows `🎯 Found inputs: 0`

**Cause**: Row not properly appended to DOM before calling function

**Fix**: Ensure `tbody.appendChild(row)` is called before `addRowNavigationWithMrpModal()`

### Issue 3: F.Qty listener not attached
**Symptom**: Console doesn't show "✅ Found F.Qty field, attaching listeners..."

**Cause**: The input doesn't have the `.item-fqty` class

**Fix**: Verify the HTML has the correct class

### Issue 4: Enter key not detected
**Symptom**: Console shows listener attached but no "F.Qty Enter pressed" message

**Cause**: Event listener might be overridden by another listener

**Fix**: Check if there are duplicate event listeners being added

### Issue 5: Modal function not found
**Symptom**: Console shows error "populateMrpModal is not defined"

**Cause**: Function doesn't exist or not in scope

**Fix**: Check if `populateMrpModal()` function exists in the file

## Manual Test Checklist

### Test 1: Basic Row Creation
- [ ] Click "Add Item"
- [ ] Select item
- [ ] Row appears
- [ ] Item code filled
- [ ] Item name filled
- [ ] Purchase rate filled
- [ ] Batch field empty with focus

### Test 2: Field Navigation
- [ ] Tab from Batch → Expiry
- [ ] Tab from Expiry → Qty
- [ ] Tab from Qty → F.Qty
- [ ] Tab from F.Qty → Pur.Rate
- [ ] Tab order is correct

### Test 3: MRP Modal Trigger
- [ ] Enter batch: "TEST123"
- [ ] Enter expiry: "12/26"
- [ ] Enter qty: "100"
- [ ] Enter F.Qty: "10"
- [ ] Press Enter on F.Qty
- [ ] Console shows: "F.Qty Enter pressed, rowIndex: X"
- [ ] MRP modal opens
- [ ] Modal shows item details

### Test 4: Compare with Pending Orders
- [ ] Load a pending order
- [ ] Check console logs for that row
- [ ] Enter F.Qty and press Enter
- [ ] MRP modal opens
- [ ] Compare behavior with Add Item row

## Code Comparison

### Pending Orders Row HTML
```javascript
<td><input type="number" class="form-control item-fqty" name="items[${index}][free_qty]" value="${item.free_qty || ''}" tabindex="${index * 10 + 6}" autocomplete="off" data-row="${index}"></td>
```

### Add Item Row HTML
```javascript
<td><input type="number" class="form-control item-fqty" name="items[${newIndex}][free_qty]" value="0" tabindex="${newIndex * 10 + 6}" autocomplete="off" data-row="${newIndex}"></td>
```

**Differences:**
- Pending orders: `value="${item.free_qty || ''}"`
- Add Item: `value="0"`

This should not affect the modal trigger, but worth noting.

## Event Listener Check

### How to verify listeners are attached:

1. Open browser console
2. After adding item, run:
```javascript
const fqtyInput = document.querySelector('#itemsTableBody tr:last-child .item-fqty');
console.log('F.Qty input:', fqtyInput);
console.log('Has keydown listener:', getEventListeners(fqtyInput).keydown);
```

3. Should show keydown listeners attached

## Common Issues

### Issue: Row index is 1 instead of 0
**Explanation**: This means there's already a row 0 in the table. Check if:
- You clicked "Add Row" before "Add Item"
- There's an initial row being added somewhere
- Previous row wasn't deleted

**Not a problem**: The modal should still work regardless of row index.

### Issue: Multiple rows with same index
**Symptom**: Two rows both have `data-row="1"`

**Cause**: Row count calculation is wrong

**Fix**: Check `const rowCount = tbody.querySelectorAll('tr').length;`

## Next Steps

1. **Test with console open** - Watch for all the debug logs
2. **Check if listeners are attached** - Use the event listener check above
3. **Compare with pending orders** - Load a pending order and test F.Qty
4. **Report findings** - Share console output to identify the exact issue

## Expected Behavior

When everything works correctly:

```
User Flow:
1. Click "Add Item" → Item modal opens
2. Select item → Row created with item data
3. Enter batch → Cursor moves to expiry
4. Enter expiry → Cursor moves to qty
5. Enter qty → Cursor moves to F.Qty
6. Enter F.Qty value → Value entered
7. Press Enter → MRP modal opens ✅
8. Update MRP → Click Save
9. Modal closes → MRP updated in row
10. Continue to next field
```

## Files Modified

1. `resources/views/admin/purchase/transaction.blade.php`
   - Added debug logging in `addItemToTableWithoutBatch()`
   - Added debug logging in `addRowNavigationWithMrpModal()`
   - All attributes match pending orders exactly

## Success Criteria

✅ Console shows all debug logs  
✅ F.Qty input element is found  
✅ Event listeners are attached  
✅ Enter key is detected  
✅ MRP modal opens  
✅ Modal shows correct data  
✅ Modal updates work  
✅ Behavior matches pending orders exactly  
