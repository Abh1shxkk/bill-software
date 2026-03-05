# Quantity Validation Logic - Context Document

## Overview
This document describes the validation logic implemented for Qty and Free Qty fields in transaction forms.

## Business Rule
- Both **Qty** and **Free Qty** fields can accept decimal values
- However, the **sum** of Qty + Free Qty must always be a **whole number (integer)**
- This ensures proper batch stock management

## Validation Behavior

### When Validation Triggers
- Validation runs on `onchange` event (when user leaves the field)
- Does NOT run on every keystroke (no `oninput` to avoid interruption while typing)

### Valid Cases (No Error)
| Qty | Free Qty | Total | Result |
|-----|----------|-------|--------|
| 5.5 | 0.5 | 6 | ✓ Valid |
| 5 | 0 | 5 | ✓ Valid |
| 2.25 | 2.75 | 5 | ✓ Valid |
| 10.5 | 1.5 | 12 | ✓ Valid |

### Invalid Cases (Error Shown)
| Qty | Free Qty | Total | Result |
|-----|----------|-------|--------|
| 5.5 | 0 | 5.5 | ✗ Error - Not whole number |
| 5 | 0.5 | 5.5 | ✗ Error - Not whole number |
| 2.3 | 2 | 4.3 | ✗ Error - Not whole number |

### Error Message
```
Invalid quantity: Total (X + Y = Z) must be a whole number. Please adjust quantities.
```

### User Experience on Error
1. Error toast notification appears
2. Focus automatically returns to Free Qty field
3. User must correct the values to make total a whole number

## Technical Implementation

### Frontend (Blade Files)
**File:** `resources/views/admin/sale/transaction.blade.php` and `modification.blade.php`

```javascript
function calculateRowAmount(rowIndex) {
    const qty = parseFloat(document.getElementById(`qty_${rowIndex}`)?.value) || 0;
    const freeQty = parseFloat(document.getElementById(`free_qty_${rowIndex}`)?.value) || 0;
    
    // Validate: Total quantity (qty + free_qty) must be a whole number
    const totalQty = qty + freeQty;
    if (totalQty > 0 && !Number.isInteger(totalQty)) {
        showToast(`Invalid quantity: Total (${qty} + ${freeQty} = ${totalQty}) must be a whole number. Please adjust quantities.`, 'error', 'Invalid Quantity');
        // Focus on free_qty field for correction
        setTimeout(() => {
            const freeQtyInput = document.getElementById(`free_qty_${rowIndex}`);
            if (freeQtyInput) {
                freeQtyInput.focus();
                freeQtyInput.select();
            }
        }, 100);
        return; // Stop further calculation
    }
    
    // Continue with amount calculation...
}
```

**HTML Input Fields:**
```html
<!-- Only onchange, no oninput -->
<input type="number" 
       name="items[${itemIndex}][qty]" 
       onchange="calculateRowAmount(${itemIndex})">
       
<input type="number" 
       name="items[${itemIndex}][free_qty]" 
       onchange="calculateRowAmount(${itemIndex})">
```

### Backend (Controller)
**File:** `app/Http/Controllers/Admin/SaleTransactionController.php`

**Store Method:**
```php
// Calculate total sold quantity including free quantity
$soldQty = floatval($qty) + floatval($freeQty);
```

**Update Method:**
```php
// When updating, adjust batch stock for both old and new quantities
$oldTotalQty = floatval($oldItem->qty) + floatval($oldItem->free_qty);
$newTotalQty = floatval($qty) + floatval($freeQty);
// Adjust stock: restore old, deduct new
$batch->current_stock += $oldTotalQty;
$batch->current_stock -= $newTotalQty;
```

## Modules Currently Implemented
- [x] Sale Transaction (`transaction.blade.php`)
- [x] Sale Modification (`modification.blade.php`)

## Pending Modules for Implementation
(To be filled by user as needed)
- [ ] Module Name - File Path

## How to Implement in New Module

### Step 1: Update calculateRowAmount() function
Add the validation logic at the beginning of the function:
```javascript
const totalQty = qty + freeQty;
if (totalQty > 0 && !Number.isInteger(totalQty)) {
    showToast(`Invalid quantity: Total (${qty} + ${freeQty} = ${totalQty}) must be a whole number. Please adjust quantities.`, 'error', 'Invalid Quantity');
    setTimeout(() => {
        const freeQtyInput = document.getElementById(`free_qty_${rowIndex}`);
        if (freeQtyInput) {
            freeQtyInput.focus();
            freeQtyInput.select();
        }
    }, 100);
    return;
}
```

### Step 2: Update HTML input fields
Ensure both qty and free_qty inputs have:
```html
onchange="calculateRowAmount(${itemIndex})"
```
And do NOT have:
```html
oninput="calculateRowAmount(${itemIndex})"  <!-- Remove this -->
```

### Step 3: Update Controller (Store & Update methods)
Change batch stock calculations to include both qty and free_qty:
```php
$soldQty = floatval($qty) + floatval($freeQty);
```

## Notes
- This validation is critical for maintaining accurate batch stock levels
- The whole number rule ensures batches are tracked correctly in the system
- Both decimal and whole number inputs are allowed as long as their sum is whole
