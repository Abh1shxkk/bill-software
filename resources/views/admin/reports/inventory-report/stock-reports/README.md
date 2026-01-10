# Stock Reports Module

## Overview
Stock related saari reports - Current Stock, Batch-wise, Location-wise, Valuation, etc.

---

## Reports List

### 1. Current Stock Status
Current stock position with filters for company, date, value calculation.

| Filter | Description |
|--------|-------------|
| Company | C(ompany)/A(ll) |
| Latest Position | Y/N |
| Batch Wise | Y/N |
| Stock Filter | 1=All, 2=With Stock, 3=W/o Stock, 4=Negative |
| Value On | 1=Cost, 2=Sale, 3=Pur, 4=MRP, 5=Cost+Tax |

### 2. Batch Wise Stock
Stock details batch-wise with expiry dates.

| Filter | Description |
|--------|-------------|
| Date Range | From/To |
| Company | Select company |
| Division | Select division |
| Item Range | From/To item code |

### 3. Location Wise Stock
Stock by godown/location.

### 4. Category Wise Stock Status
Stock grouped by item category.

### 5. Valuation of Closing Stock
Closing stock valuation for accounting.

### 6. Category Wise Valuation
Closing stock valuation category-wise.

### 7. Company Wise Stock Value
Stock value grouped by company.

### 8. Stock Register
Complete stock register with all movements.

### 9. Stock Register IT Return
Stock register format for IT returns.

### 10. Stock and Sales Analysis
Stock vs Sales comparison.

### 11. Stock and Sales with Value
Stock and sales with value calculations.

### 12. Annual Stock Ledger Summary
Yearly stock summary.

### 13. Current Stock Status Supplier Wise
Stock grouped by supplier.

### 14. Sales and Stock Variation
Variation analysis between sales and stock.

### 15. List of Old Stock
Items with old/slow moving stock.

---

## Flow Diagram
```
┌─────────────────────────────────────────────────────────────┐
│                    STOCK REPORTS                            │
└─────────────────────────────────────────────────────────────┘
                              │
    ┌─────────────┬───────────┼───────────┬─────────────┐
    ▼             ▼           ▼           ▼             ▼
┌────────┐  ┌──────────┐  ┌────────┐  ┌────────┐  ┌──────────┐
│Current │  │ Batch/   │  │Valua-  │  │Register│  │ Analysis │
│ Stock  │  │ Location │  │ tion   │  │        │  │          │
└────────┘  └──────────┘  └────────┘  └────────┘  └──────────┘
```

## Common Actions
- View - Generate report
- Print (F7) - Print
- Excel - Export
- Close - Exit
