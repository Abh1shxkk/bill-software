# Inventory Report - Others

## Overview
Miscellaneous inventory reports - FIFO Ledger, Bank Reports.

---

## 1. FIFO Ledger

### Purpose
FIFO (First In First Out) basis pe item ledger - batch-wise stock movement.

### Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Selective Item | Y, N | Filter specific item |
| Company | Dropdown | Select company |
| Item | Dropdown | Select item |

### Output Columns
| Column | Description |
|--------|-------------|
| Item Name | Item name |
| Batch No | Batch number |
| Expiry | Expiry date |
| Opening | Opening qty |
| Purchase | Purchase qty |
| Sale | Sale qty |
| Closing | Closing qty |
| MRP | Maximum retail price |
| Value | Stock value |

---

## 2. Stock & O/S Report for Bank

### Purpose
Bank ke liye stock aur outstanding report - loan/CC limit ke liye.

### Filters
| Filter | Type | Description |
|--------|------|-------------|
| Closing Stock % | Number | Stock margin % |
| D/L Type | D, L | Due List/Ledger |
| Due List % | Number | Due list margin % |
| PDC % | Number | Post dated cheque % |
| As On Date | Date | Report date |
| With Creditors | Checkbox | Include creditors |

### Output Columns
| Column | Description |
|--------|-------------|
| Particulars | Description |
| Amount | Total amount |
| % | Percentage |
| Value | Calculated value |

---

## Flow Diagram
```
┌─────────────────────────────────────────────────────────────┐
│                    OTHERS                                   │
└─────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────┴───────────────┐
              ▼                               ▼
      ┌───────────────┐               ┌───────────────┐
      │  FIFO Ledger  │               │ Stock & O/S   │
      │  Batch-wise   │               │ Report Bank   │
      └───────────────┘               └───────────────┘
```

## Use Cases
- FIFO Ledger: Batch-wise stock tracking, expiry management
- Bank Report: Loan applications, CC limit calculations
