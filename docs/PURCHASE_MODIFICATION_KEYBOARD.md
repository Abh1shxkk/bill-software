# Purchase Modification Keyboard Support (Analysis + Plan)

## Goal
Implement consistent keyboard navigation in purchase modification so users can complete the header, table, and modal flows without the mouse, matching purchase transaction behavior.

## Current Page Structure (key inputs)
Header fields
- billDate, dayName (readonly), supplierSearchInput + supplierSelect (hidden), receiveDate
- cash, transfer, remarks, dueDate
- billNo, trnNo
- Save button uses savePurchase()

Items table (row inputs)
- code, name, batch, exp, qty, free_qty, pur_rate, dis_percent, mrp, amount (readonly)
- row action buttons: insert item, delete row

Calculation section
- calc_* readonly fields plus calc_s_rate (editable)

Modals present
- Pending Orders modal (pendingOrdersModal)
- MRP details modal (mrpDetailsModal)
- Insert Item modal (insertItemModal)
- Invoice List modal (invoiceListModal)
- Discount Options modal (discountOptionsModal)
- Alert / Success modal (alertModal)
- Item + Batch selection components at bottom (components.modals.*)

## Existing Keyboard Behavior (already in file)
- Supplier dropdown search with Enter selection and Escape close.
- Bill No and Trn No Enter triggers fetchBillByTrnNo().
- S.Rate Enter calculates GST and selects next row (row selection mode).
- Row selection via Arrow Up/Down when row is selected.
- Basic Enter handling in row navigation (addRowNavigationWithMrpModal).
- Pending Orders and MRP modals open/close functions exist, but keyboard navigation is minimal.

## Gaps vs desired behavior
- No consistent header keyboard order (Enter/Up) like purchase transaction.
- No Ctrl+S save shortcut.
- Missing keyboard focus style and modal button highlight in this file.
- Modals lack full keyboard navigation (rows with arrows, actions with left/right, Enter to select).
- Insert Item and Invoice List modals need arrow/Enter selection.
- Discount Options modal needs left/right/Enter navigation.
- Alert success OK should respond to Enter.
- Table flow should match purchase transaction (batch -> exp -> qty -> fqty -> pur_rate -> dis% -> mrp -> sale rate -> add row -> code).

## Target Keyboard Flow (proposal)
Header flow
- billDate -> supplierSearchInput -> receiveDate -> cash -> transfer -> remarks -> dueDate -> billNo -> trnNo
- Enter moves forward, ArrowUp moves back.

Table flow (row)
- batch -> exp -> qty -> fqty -> pur_rate -> dis% -> mrp
- Enter on MRP moves to calc_s_rate.
- Enter on calc_s_rate triggers add row and focuses code of new row.

Modal flow
- Pending orders: Up/Down to select row, Enter to focus actions, Left/Right to toggle Exit/Generate, Enter to activate, Esc to close.
- MRP modal: Enter advances fields in order; after Excise -> actions (Cancel/Save), Left/Right toggles, Enter triggers.
- Insert item modal: Up/Down highlight item row, Enter selects, Esc closes; search input stays active for typing.
- Invoice list modal: Up/Down highlight invoice, Enter loads, Esc closes.
- Discount options modal: Left/Right choose Temporary/Company/Item, Enter triggers, Esc closes.
- Alert success: Enter triggers OK.

## Implementation Steps (high-level)
1. Add focus/highlight CSS for keyboard focus and modal buttons (kbd-highlight).
2. Add data-kb-order to header fields and header key handler (Enter/Up).
3. Add Ctrl+S (and Cmd+S) to call savePurchase().
4. Align table Enter flow with purchase transaction.
5. Add modal keyboard handlers (pending orders, MRP, insert item, invoice list, discount options, alert).
6. Add isBlockingModalOpen guard + stopImmediatePropagation where needed to avoid conflicts with global shortcuts.
7. Add small console logs for critical transitions to debug focus/flow.

## Conflicts to Guard Against
- Global keyboard shortcuts from keyboard-shortcuts-inline.blade.php.
- Multiple document-level keydown listeners in this file. Use capture listeners for modal handlers and for calc_s_rate/MRP to avoid focus leaks (example: Unit field grabbing focus).

## Next Steps
- Implement the plan in purchase modification.
- Validate flow against purchase transaction behavior.
- Remove or reduce logs once stable.
