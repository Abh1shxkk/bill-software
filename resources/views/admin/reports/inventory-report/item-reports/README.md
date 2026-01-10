# Item Reports Module

## Overview
Item master related reports - Item List, Ledger, Rates, Margins, Schemes, etc.

---

## Reports List

### 1. Display Item List
Items ki list with sale/transaction details.

| Filter | Description |
|--------|-------------|
| Date Range | From/To |
| Item | Select item |

### 2. Item Ledger Printing
Individual item ka complete ledger - all transactions.

| Filter | Description |
|--------|-------------|
| Date Range | From/To |
| Company | Select company |
| Division | Select division |
| Item Range | From/To item |
| Tagged Item | Y/N |
| Category | Select category |

### 3. Item Search by Batch
Search items by batch number.

### 4. Rate List
Items ki rate list - MRP, Sale Rate, Purchase Rate.

### 5. Item List with Salts
Items with salt/composition details.

### 6. VAT Wise Items
Items grouped by VAT/Tax percentage.

### 7. Margin Wise Items
Items sorted by profit margin.

### 8. Margin Wise Items (Running)
Running items with margin analysis.

### 9. Multi Rate Items
Items with multiple rates.

### 10. Minimum Maximum Level Items
Items with min/max stock levels defined.

### 11. Item List Tax MRP Rate Range
Items filtered by tax/MRP/rate range.

### 12. New Items Customers Suppliers
Newly added items, customers, suppliers.

### 13. List of Schemes
Active schemes on items.

---

## Flow Diagram
```
┌─────────────────────────────────────────────────────────────┐
│                    ITEM REPORTS                             │
└─────────────────────────────────────────────────────────────┘
                              │
    ┌─────────────┬───────────┼───────────┬─────────────┐
    ▼             ▼           ▼           ▼             ▼
┌────────┐  ┌──────────┐  ┌────────┐  ┌────────┐  ┌──────────┐
│ Item   │  │  Ledger  │  │ Rates  │  │ Margin │  │ Schemes  │
│ List   │  │ Printing │  │  List  │  │  Wise  │  │          │
└────────┘  └──────────┘  └────────┘  └────────┘  └──────────┘
```

## Common Actions
- View/Show - Generate report
- Print - Print report
- Excel - Export
- Exit/Close - Close window
