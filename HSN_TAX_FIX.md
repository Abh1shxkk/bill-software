# HSN Code and Tax Calculation Fix - Summary

## Changes Made

### 1. **Updated selectItemBatch() Function**
Changed from accepting individual parameters to accepting the full item object:
```javascript
// OLD
function selectItemBatch(itemId, itemName, mrp, saleRate) {
    window.selectedItem = {
        item_id: itemId,
        item_name: itemName,
        mrp: mrp,
        sale_rate: saleRate
    };
}

// NEW
function selectItemBatch(item) {
    window.selectedItem = {
        item_id: item.id || item.code,
        item_code: item.code,
        item_name: item.name,
        mrp: parseFloat(item.mrp || 0),
        sale_rate: parseFloat(item.s_rate || item.srate || 0),
        hsn_code: item.hsn_code || '',        // ✅ Added
        cgst_percent: parseFloat(item.cgst || 6),   // ✅ Added
        sgst_percent: parseFloat(item.sgst || 6),   // ✅ Added
        cess_percent: parseFloat(item.gst_cess || 0), // ✅ Added
        packing: item.packing || '',
        company_name: item.company_name || '',
        unit: item.unit || 'PCS'
    };
}
```

### 2. **Updated Button Click Handler**
Changed the onclick to pass the complete item object:
```javascript
// OLD
onclick="selectItemBatch(${item.code}, '${item.name}', ${item.mrp}, ${item.s_rate})"

// NEW  
onclick='selectItemBatch(${JSON.stringify(item).replace(/'/g, "\\\'")})'
```

### 3. **Updated addItemToReturn() Function**
Now prioritizes selectedItem data for HSN and tax percentages:
```javascript
const newItem = {
    item_id: selectedItem.item_id,
    item_code: selectedItem.item_code || selectedItem.item_id,
    item_name: selectedItem.item_name,
    batch_id: batch.id,
    batch_no: batch.batch_no,
    expiry_date: batch.expiry_date,
    packing: selectedItem.packing || batch.packing || '',
    unit: selectedItem.unit || batch.unit || 'PCS',
    company_name: selectedItem.company_name || batch.company_name || '',
    hsn_code: selectedItem.hsn_code || batch.hsn_code || '',  // ✅ From item first
    sale_rate: parseFloat(batch.s_rate || selectedItem.sale_rate || 0),
    mrp: parseFloat(batch.mrp || selectedItem.mrp || 0),
    discount_percent: 0,
    cgst_percent: parseFloat(selectedItem.cgst_percent || batch.cgst_percent || 6),  // ✅
    sgst_percent: parseFloat(selectedItem.sgst_percent || batch.sgst_percent || 6),  // ✅
    cess_percent: parseFloat(selectedItem.cess_percent || batch.cess_percent || 0),   // ✅
    return_qty: 1,
    return_fqty: 0
};
```

## API Requirements

The `/admin/items/all` API (route: `admin.items.all`) should return items with these fields:

```json
{
  "success": true,
  "items": [
    {
      "id": 1,
      "code": "ITM001",
      "name": "ABAXIS 2.5 MG TAB.",
      "mrp": 210.00,
      "s_rate": 200.00,
      "hsn_code": "30049099",        // ✅ Required for HSN display
      "cgst": 6,                      // ✅ Required for CGST %
      "sgst": 6,                      // ✅ Required for SGST %
      "gst_cess": 0,                  // ✅ Required for Cess %
      "packing": "1*10",
      "company_name": "ABC Pharma",
      "unit": "PCS"
    }
  ]
}
```

## Data Flow

```
User clicks "Add Row"
    ↓
openAllItemsModal() fetches items via admin.items.all
    ↓
Items displayed with "Batch" button
    ↓
User clicks Batch button
    ↓
selectItemBatch(fullItemObject) called
    ↓
Stores: item_id, code, name, mrp, rate, hsn_code, cgst%, sgst%, cess%
    ↓
Fetches batches for item
    ↓
User selects/creates batch
    ↓
addItemToReturn(batch) called
    ↓
Creates newItem with HSN and tax data from selectedItem
    ↓
populateItemsTable() adds row
    ↓
calculateSummary() uses item.hsn_code, cgst_percent, sgst_percent
    ↓
Calculation section displays: HSN Code, CGST%, SGST%, amounts ✅
```

## Calculation Section Fields Updated

The `calculateSummary()` function now properly updates:

1. **HSN Code** (calc_hsn_code)
2. **CGST(%)** (calc_cgst_percent) 
3. **SGST(%)** (calc_sgst_percent)
4. **Cess(%)** (calc_cess_percent)
5. **CGST Amount** (calc_cgst_amount)
6. **SGST Amount** (calc_sgst_amount)
7. **Cess Amount** (calc_cess_amount)

## Expected Behavior

Before fix:
- HSN Code: `---`
- CGST(%): `0.0(`
- SGST(%): `0.0(`
- Amounts: `0.00`

After fix:
- HSN Code: `30049099` (from item)
- CGST(%): `6.0` (from item)
- SGST(%): `6.0` (from item) 
- CGST Amount: `54.00` (calculated: 900 × 6%)
- SGST Amount: `54.00` (calculated: 900 × 6%)

## Testing

1. Clear browser cache / Hard refresh (Ctrl+F5)
2. Go to Sale Return Transaction
3. Click "Add Row"
4. Select an item
5. Select/Create batch
6. Enter Qty: 10, Rate: 100, Dis%: 10
7. Check Calculation Section:
   - HSN Code should show item's HSN
   - CGST% should show 6.0 (or item's CGST)
   - SGST% should show 6.0 (or item's SGST)
   - CGST Amount should show 54.00
   - SGST Amount should show 54.00
   - Total Tax should show 108.00

## Console Debugging

Added console.log statements:
- `console.log('Selected Item Data:', window.selectedItem)` - Check if HSN and tax% are stored  
- `console.log('Adding item to return:', { batch, selectedItem })` - Verify data before adding
- `console.log('New item object:', newItem)` - Check final item object

Open browser DevTools (F12) → Console tab to see these logs.
