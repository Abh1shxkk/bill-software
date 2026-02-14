# Purchase Return Keyboard Handlers: Challenges and Reusable Fixes

## Scope
This note captures what failed and what worked while implementing keyboard handlers in:
- `resources/views/admin/purchase-return/transaction.blade.php`

Use this as a direct blueprint for `purchase-return/modification.blade.php`.

## Main Challenges Faced
1. Global key handlers were swallowing `Enter` before field-level handlers.
2. `keyup` fallback for `Dis.%` caused accidental trigger when focus moved into `Dis.%`.
3. Add Row flow conflicted with item modal flow.
4. Multiple row builders and duplicate callbacks caused inconsistent behavior.
5. Modal keyboard behavior was inconsistent across:
- confirm modal
- adjustment modal
- success modal
6. Global `Ctrl+S` was firing page save while modal was open.

## Proven Fix Patterns
1. Use capture-phase handlers for critical fields:
- `window.addEventListener('keydown', handler, true)` for strongest precedence.
- Then `document` capture as secondary fallback.

2. For `Dis.%` Enter behavior:
- Trigger Add Row only on `keydown Enter`.
- Remove `keyup` fallback for `Dis.%` to avoid carry-over Enter bugs.
- Guard with lock (`disPercentEnterLoopLock`) to prevent double execution.

3. Decouple Add Row and item modal:
- Add Row should only create blank row and focus `Code`.
- `Code` `Enter` opens item modal for that row.

4. Keep row context while selecting from modal:
- Store pending row index (`pendingItemSelectionRowIndex`).
- On item+batch selection, fill that same row.

5. Add guards around shortcut handlers:
- Skip page-level `Ctrl+S` when modal is open.
- Let modal-specific `Ctrl+S` handle submit.

6. Standardize modal keyboard engine:
- Maintain active index.
- Arrow keys move active button/input.
- Enter triggers active control.
- Esc closes modal.
- Bind on modal open, unbind on close.

## Keyboard Contracts Implemented
1. Table loop:
- `Dis.%` `Enter` -> complete row -> add new row -> focus new row `Code`.
- `Code` `Enter` -> open item selection modal.

2. Confirm modal (Credit Note Adjustment question):
- Left/Right/Up/Down switch between `No` and `Yes`.
- Enter triggers selected button.

3. Credit adjustment modal:
- Up/Down navigate adjustment inputs.
- Right from input moves to footer buttons.
- Left/Right on buttons toggles `Save`/`Cancel`.
- Enter triggers active input/button behavior.
- Esc closes modal.
- Ctrl+S saves from inside modal.

4. Success modal:
- Left/Right/Up/Down switch `New Return` and `OK`.
- Enter triggers selected button.
- Esc closes via `OK` flow.

## Implementation Checklist for Modification Blade
1. Reuse same state flags:
- `pendingItemSelectionRowIndex`
- locks for key loops and modal open
- active indexes for modal keyboard focus

2. Reuse same event strategy:
- capture-phase keydown for critical fields
- avoid keyup fallback unless truly needed

3. Keep one source of truth per function:
- one `addNewRow`
- one `onItemBatchSelectedFromModal`
- one keyboard binder per modal

4. Add explicit IDs for modal buttons:
- avoids brittle selector logic

5. Add active-focus CSS:
- `.kb-active-choice` for visual clarity during keyboard navigation

6. Verify no duplicate global listeners:
- especially `Ctrl+S`, `Enter`, and `Escape`

## Regression Test Set (Must Pass)
1. `Dis.%` focus only: no auto add row.
2. `Dis.%` + `Enter`: add row and focus `Code`.
3. `Code` + `Enter`: item modal opens.
4. Item modal: arrow navigation + Enter selection.
5. Batch modal: arrow navigation + Enter selection.
6. Confirm modal: arrows + Enter on both options.
7. Adjustment modal: input navigation, footer button navigation, Enter, Esc, Ctrl+S.
8. Success modal: left/right + Enter on `New Return` and `OK`.

