# Dispatch Sheet Report Documentation

## Overview
Dispatch Sheet report ek logistics-focused report hai jo warehouse/godown se dispatch hone wale items ki detailed list provide karta hai. Ye report company-wise grouped hota hai aur delivery/packing team ke liye essential hai.

---

## Report Details

### Purpose
- Warehouse dispatch planning
- Packing list generation
- Company-wise item grouping for efficient picking
- Delivery verification document
- Stock movement tracking

### Route
`admin.reports.sales.dispatch-sheet`

---

## Filter Options

### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Dispatch period start date |
| To Date | Dispatch period end date |

### Other Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Company | Dropdown | Filter by manufacturer/brand |
| Remarks | Text | Add custom remarks to dispatch sheet |

---

## Summary Cards

Report ke top par 5 summary cards display hote hain:

| Card | Color | Description |
|------|-------|-------------|
| Companies | Blue (Primary) | Number of companies in dispatch |
| Items | Cyan (Info) | Total item count |
| Qty | Yellow (Warning) | Total quantity to dispatch |
| Free | Green (Success) | Total free quantity |
| Amount | Red (Danger) | Total dispatch value |

---

## Output Columns

| Column | Description |
|--------|-------------|
| # | Serial number |
| Item Code | Product code |
| Item Name | Product name (truncated to 30 chars) |
| Packing | Pack size |
| Batch | Batch number |
| Qty | Dispatch quantity |
| Free | Free quantity |
| Rate | Sale rate |
| Amount | Net amount |
| Bill No | Invoice number (clickable link) |
| Customer | Customer name |

---

## Report Structure

### Company-wise Grouping
Report is grouped by Company/Manufacturer:

```
┌─────────────────────────────────────────────┐
│ 🏢 Company Name A    [X Items] [Qty: XXX]   │  ← Yellow header row
├─────────────────────────────────────────────┤
│ Item 1 details...                           │
│ Item 2 details...                           │
├─────────────────────────────────────────────┤
│ Company A Total:     XXX    XXX    XXX.XX   │  ← Gray subtotal row
├─────────────────────────────────────────────┤
│ 🏢 Company Name B    [X Items] [Qty: XXX]   │
│ ...                                         │
├─────────────────────────────────────────────┤
│ Grand Total:         XXX    XXX    XXX.XX   │  ← Dark footer row
└─────────────────────────────────────────────┘
```

### Row Types
| Row Type | Color | Description |
|----------|-------|-------------|
| Company Header | Yellow (table-warning) | Company name with item count & qty badges |
| Item Row | Striped | Individual item details |
| Company Subtotal | Gray (table-secondary) | Company-wise totals |
| Grand Total | Dark (table-dark) | Overall totals |

---

## Action Buttons

| Button | Shortcut | Description |
|--------|----------|-------------|
| Excel | Alt+E | Export to Excel |
| View | Alt+V | Generate report |
| Print | Alt+P | Print dispatch sheet |
| Close | Alt+C | Return to reports menu |

---

## Keyboard Shortcuts

| Key | Action |
|-----|--------|
| Alt+V | View report |
| Alt+P | Print report |
| Alt+E | Export to Excel |
| Alt+C | Close/Back |

---

## Interactive Features

### Bill Number Link
- Bill No column mein clickable link hai
- Click karne par sale transaction detail page open hota hai

---

## Use Cases

### 1. Daily Dispatch Planning
- Set From/To date to current day
- Generate list of items to be dispatched
- Use for warehouse picking

### 2. Company-wise Packing
- Items are grouped by company
- Efficient for packing same-company items together
- Reduces picking time

### 3. Delivery Verification
- Print dispatch sheet
- Use as checklist during loading
- Verify quantities before dispatch

### 4. Stock Movement Record
- Track what was dispatched
- Maintain dispatch history
- Audit trail for inventory

### 5. Customer Delivery Preparation
- See customer names for each item
- Plan route-wise delivery
- Prepare delivery documents

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- SaleTransaction Model
- SaleTransactionItem Model
- Company Model
- Customer Model
- Item Model

### Data Grouping
- Items grouped by `company_name`
- Each group shows item count and total quantity badges
- Subtotals calculated per company

### UI Components
- Pink header card with report title
- Gray filter section
- 5 color-coded summary cards
- Responsive data table with sticky header
- Company-wise grouped rows with subtotals
- Maximum table height: 60vh with scroll

### Performance
- Report generates on "View" button click
- Large date ranges may include many items
- Use Company filter to narrow results
