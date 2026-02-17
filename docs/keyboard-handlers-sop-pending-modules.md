# SOP: Keyboard Handler Rollout (Pending Modules + Approach)

## Objective
Is SOP ka purpose hai:
- Kaunse modules me keyboard handlers abhi pending hain, clear list dena
- Har module me same standardized approach follow karna
- Rework aur regression kam karna

## Status Legend
- `Not Completed`: keyboard handler implementation pending
- `In Progress`: implementation chal rahi hai
- `Completed`: implementation + smoke test done

## Pending Module List

### Breakage/Expiry to Supplier
| Module | Screen | Status |
|---|---|---|
| Breakage/Expiry to Supplier | Transaction 2 | Not Completed |
| Breakage/Expiry to Supplier | Transaction 3 | Not Completed |
| Breakage/Expiry to Supplier | Modification 1 | Not Completed |
| Breakage/Expiry to Supplier | Modification 2 | Not Completed |
| Breakage/Expiry to Supplier | Modification 3 | Not Completed |

### Sales / Purchase Challan
| Module | Screen | Status |
|---|---|---|
| Sale Challan | Transaction | Not Completed |
| Sale Challan | Modification | Not Completed |
| Purchase Challan | Transaction | Not Completed |
| Purchase Challan | Modification | Not Completed |

### Credit / Debit Note
| Module | Screen | Status |
|---|---|---|
| Credit Note | Transaction | Not Completed |
| Credit Note | Modification | Not Completed |
| Debit Note | Transaction | Not Completed |
| Debit Note | Modification | Not Completed |

### Receipt / Payment
| Module | Screen | Status |
|---|---|---|
| Receipt from Customer | Transaction | Not Completed |
| Receipt from Customer | Modification | Not Completed |
| Payment to Supplier | Transaction | Not Completed |
| Payment to Supplier | Modification | Not Completed |

### Voucher / Quotation
| Module | Screen | Status |
|---|---|---|
| Voucher Entry | Transaction | Not Completed |
| Voucher Entry | Modification | Not Completed |
| Quotation | Transaction | Not Completed |
| Quotation | Modification | Not Completed |

### Replacement Flow
| Module | Screen | Status |
|---|---|---|
| Replacement Note | Transaction | Not Completed |
| Replacement Note | Modification | Not Completed |
| Replacement Received | Transaction | Not Completed |
| Replacement Received | Modification | Not Completed |
| Sale Return Replacement | Transaction | Not Completed |
| Sale Return Replacement | Modification | Not Completed |

### Stock Transfer / Adjustment
| Module | Screen | Status |
|---|---|---|
| Stock Adjustment | Transaction | Not Completed |
| Stock Adjustment | Modification | Not Completed |
| Stock Transfer Outgoing | Transaction | Not Completed |
| Stock Transfer Outgoing | Modification | Not Completed |
| Stock Transfer Outgoing Return | Transaction | Not Completed |
| Stock Transfer Outgoing Return | Modification | Not Completed |
| Stock Transfer Incoming | Transaction | Not Completed |
| Stock Transfer Incoming | Modification | Not Completed |
| Stock Transfer Incoming Return | Transaction | Not Completed |
| Stock Transfer Incoming Return | Modification | Not Completed |

### Sample / Claim / Godown
| Module | Screen | Status |
|---|---|---|
| Sample Issued | Transaction | Not Completed |
| Sample Issued | Modification | Not Completed |
| Sample Received | Transaction | Not Completed |
| Sample Received | Modification | Not Completed |
| Godown Breakage/Expiry | Transaction | Not Completed |
| Godown Breakage/Expiry | Modification | Not Completed |
| Claim to Supplier | Transaction | Not Completed |
| Claim to Supplier | Modification | Not Completed |

### HSN Voucher Group
| Module | Screen | Status |
|---|---|---|
| Sale Voucher (HSN) | Transaction | Not Completed |
| Sale Voucher (HSN) | Modification | Not Completed |
| Purchase Voucher (HSN) | Transaction | Not Completed |
| Purchase Voucher (HSN) | Modification | Not Completed |
| Sale Return Voucher (HSN) | Transaction | Not Completed |
| Sale Return Voucher (HSN) | Modification | Not Completed |
| Purchase Return Voucher (HSN) | Transaction | Not Completed |
| Purchase Return Voucher (HSN) | Modification | Not Completed |

### Other Finance/Utility Screens
| Module | Screen | Status |
|---|---|---|
| Cheque Returned Unpaid | Cheque Returned Unpaid | Not Completed |
| Deposit Slip | Deposit Slip | Not Completed |
| Voucher Purchase (Input GST) | Voucher Purchase (Input GST) | Not Completed |
| Voucher Income (Output GST) | Voucher Income (Output GST) | Not Completed |
| Multi Voucher Entry | Multi Voucher Entry | Not Completed |
| Cash Deposited / Withdrawn | Cash Deposited / Withdrawn | Not Completed |

---

## Standard Implementation Approach (Same for all modules)

### Phase 1: Baseline Cleanup
1. `select2` dependency remove/disable karo jahan custom searchable dropdown use karna hai.
2. Existing global key handlers map karo (`document/window capture`, inline `onkeydown`, modal handlers).
3. Focus CSS standardize karo: active field blue border.

### Phase 2: Header Keyboard Flow
1. Fixed header order define karo (module-specific sequence).
2. `Enter` pe next field focus.
3. Date fields:
   - `Enter/Tab` -> date picker open
   - `Enter` confirm -> next target field
4. Dropdown fields:
   - `ArrowUp/ArrowDown` navigate
   - `Enter` select
   - `Esc` close without selection

### Phase 3: Table Loop Flow
1. Row add behavior standard:
   - `Add Row` button should only add row
   - Item modal open only from code field `Enter`
2. `Dis% Enter`:
   - current row complete + calc update
   - `Add Row` trigger
   - new row code field focus
3. Code field `Enter`:
   - item modal open
   - modal selection ke baad batch modal
   - table row populate + next editable field focus

### Phase 4: Modal Keyboard Handling
1. Item modal:
   - `ArrowUp/Down` row navigation
   - `Enter` select row
   - `Esc` close + fallback focus restore
2. Batch modal:
   - same as item modal
3. Confirmation modals (Yes/No, Save dialogs):
   - `Left/Right` button switch
   - `Enter` trigger active button
   - `Esc` cancel/close
4. Credit adjustment modals:
   - input rows navigation (`Up/Down/Enter`)
   - action buttons navigation (`Left/Right`)
   - `Ctrl+S` submit (modal context aware)

### Phase 5: Conflict-Proofing
1. Capture-phase handlers only where required (`true` flag), but scoped selectors ke saath.
2. `preventDefault + stopPropagation + stopImmediatePropagation` only conflict points pe.
3. Re-entry locks add karo jahan duplicate triggers ka risk ho (`*_lock` flags).
4. Console logs temporary rakho (`[KB-*]`) for debugging; final cleanup me reduce karo.

### Phase 6: Save Behavior
1. Transaction screen: `POST` (create)
2. Modification screen: loaded transaction ID ho to `PUT` (update), else fallback `POST`
3. `Ctrl+S` mapping context-aware:
   - normal screen -> save
   - confirm modal open -> confirm action
   - adjustment modal open -> save adjustment action

### Phase 7: QA Checklist (Mandatory per module)
1. Header full Enter flow pass
2. Dropdown keyboard select pass
3. Date picker Enter confirm pass
4. Item+Batch modal keyboard select pass
5. Dis% -> Add row loop pass
6. Code Enter -> item modal pass
7. `Ctrl+S` correct action pass
8. `Esc` modal close + focus restore pass
9. Totals/calculation multi-row scenario pass
10. Modification update flow (not create) pass

---

## Execution Order Recommendation
1. High-volume modules first: Challan, Credit/Debit, Receipt/Payment
2. Complex inventory flows: Stock Transfer variants, Replacement variants
3. HSN voucher cluster (reuse same template strategy)
4. Utility/finance screens at end

---

## Reusable Technical Pattern (Do Not Break)
- Header nav initializer
- Scoped capture handlers for code/dis% enter
- Modal keyboard bind/unbind pairs
- Focus restore strategy after modal close
- Save endpoint switch (`POST` vs `PUT`) based on current transaction ID

---

## Notes
- Is SOP ko module-wise progress tracker ke saath weekly update karna.
- Har completed module ke against short regression note add karo (known edge cases + fix commit reference).
