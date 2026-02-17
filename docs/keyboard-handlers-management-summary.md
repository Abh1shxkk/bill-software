# Keyboard Handlers Rollout: Management Summary

## Current Situation
- Multiple transaction/modification modules me keyboard handlers pending hain.
- Standard flow already define ho chuka hai (header navigation, dropdown handling, date picker flow, table loop, modal controls, save/update behavior).
- Objective: all pending modules me same UX standard achieve karna.

## Scope Snapshot
- Total pending screens: **60+** (transaction + modification + utility finance screens).
- High-priority clusters:
  - Challan (Sale/Purchase)
  - Credit/Debit Note
  - Receipt/Payment
  - Stock Transfer variants
  - Replacement variants

## Delivery Strategy
1. Common reusable pattern apply karna (same architecture across modules).
2. Module rollout in batches (high-usage first).
3. Mandatory QA checklist per module before marking complete.
4. Regression risk reduce via conflict-safe keyboard capture and modal-specific handlers.

## Risks
- Global keydown handler conflicts.
- `select2` remnants causing duplicate dropdown behavior.
- Modification screens me incorrect create/save flow (`POST` instead of `PUT`).
- Mixed API payload shapes causing calculation mismatch.

## Controls
- Scoped keyboard listeners only on target inputs/modals.
- Standard save routing rule:
  - Transaction screen -> `POST`
  - Modification screen with loaded ID -> `PUT`
- Module-wise smoke checklist (header, table loop, modals, Ctrl+S, Esc, totals).

## Tracking Recommendation
- Use one sheet with columns:
  - Module
  - Screen
  - Owner
  - Status (`Not Completed / In Progress / Completed`)
  - QA Status
  - Known Issues
  - Release Date

## Reference
- Detailed technical SOP:
  - `docs/keyboard-handlers-sop-pending-modules.md`
