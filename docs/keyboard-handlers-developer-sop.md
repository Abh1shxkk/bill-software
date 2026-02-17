# Keyboard Handlers: Developer SOP

## 1) Goal
Har module me consistent keyboard UX implement karna:
- Enter-based header progression
- Dropdown keyboard selection
- Date picker keyboard confirm
- Table row loop automation
- Modal full keyboard navigation
- Context-aware save behavior

## 2) Implementation Standard

### 2.1 Header Flow
- Fixed sequence define karo (`fieldOrder` array).
- `Enter` => next field.
- Date field:
  - `Enter/Tab` => picker open
  - `Enter` => confirm and move target
- Supplier/customer dropdown:
  - `ArrowUp/ArrowDown` => option navigate
  - `Enter` => select
  - `Esc` => close

### 2.2 Table Flow
- `Add Row` button ka single role: **new row add**.
- Code field `Enter`: item modal open.
- Dis% field `Enter`:
  - current row complete
  - totals recalculate
  - add row trigger
  - focus new row code field

### 2.3 Modal Flow
- Item modal:
  - Up/Down navigate
  - Enter select
  - Esc close + focus restore
- Batch modal:
  - same behavior
- Confirm modal (Yes/No):
  - Left/Right switch
  - Enter click active
  - Esc close
- Credit adjustment modal:
  - input navigation + action buttons navigation
  - Ctrl+S submit
  - Esc close

### 2.4 Save Logic
- Transaction page => `POST` create
- Modification page => if `currentTransactionId` exists then `PUT` update, else fallback `POST`
- Ctrl+S modal-aware hona chahiye:
  - confirm modal open -> confirm action
  - adjustment modal open -> save adjustment
  - otherwise normal save flow

## 3) Conflict Rules (Important)
- Global `keydown` listeners scoped selectors ke saath rakho.
- Sirf conflict points pe:
  - `preventDefault()`
  - `stopPropagation()`
  - `stopImmediatePropagation()`
- Double trigger avoid karne ke liye lock flags use karo (example: `codeEnterModalLock`, `disPercentEnterLoopLock`).
- Modal open/close pe keyboard handlers bind/unbind mandatory.

## 4) select2 Migration Rule
- Jahan custom dropdown target hai, `select2` remove/disable.
- Duplicate placeholders ya double UI remove karo.
- Custom dropdown me same keyboard parity ensure karo.

## 5) Calculation Integrity Rules
- Payload normalization required:
  - `purchase_rate` fallback chain (`purchase_rate/pur_rate/avg_pur_rate`)
  - tax fields fallback (`cgst/sgst/cess`)
  - item/batch mixed API keys map consistently
- Multi-row totals on each edit recalculate karo.
- Row dataset (`row.dataset.itemData`) always complete and numeric-safe ho.

## 6) QA Checklist (Per Screen)
1. Header Enter flow pass
2. Dropdown arrows + enter pass
3. Date picker keyboard confirm pass
4. Item modal selection pass
5. Batch modal selection pass
6. Dis% Enter -> Add Row -> new code focus pass
7. Code Enter -> item modal pass
8. Ctrl+S context-aware pass
9. Esc close + focus restore pass
10. Multi-row totals correct pass
11. Modification save updates existing transaction (not create) pass

## 7) Debug Logging Convention
- Temporary logs prefix:
  - `[KB-<MODULE>]`
  - `[KB-<MODULE>][Capture]`
  - `[KB-<MODULE>][Modal]`
- Final cleanup me noisy logs remove/reduce.

## 8) Delivery Workflow
1. Header complete
2. Table loop complete
3. Modals complete
4. Save/update correctness
5. QA checklist sign-off
6. Mark status as Completed

## 9) Related Docs
- Pending modules list + master SOP:
  - `docs/keyboard-handlers-sop-pending-modules.md`
- Management summary:
  - `docs/keyboard-handlers-management-summary.md`
