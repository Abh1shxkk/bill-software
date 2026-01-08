# Shortage Report Documentation

## Overview
Shortage Report ek inventory management report hai jo stock shortage aur low stock items identify karta hai. Ye report purchase planning, reorder decisions aur stock-out prevention ke liye critical hai - shows items that are running low or out of stock based on sales demand.

---

## Report Details

### Purpose
- Stock shortage identification
- Out of stock items tracking
- Low stock alerts
- Purchase order planning
- Demand vs Stock analysis
- Company-wise shortage tracking

### Route
`admin.reports.sales.shortage-report`

---

## Filter Options

### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Analysis period start date |
| To Date | Analysis period end date |

### Other Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Company | Dropdown | Filter by manufacturer/brand |
| D/S | D(etailed)/S(ummarized) | Report format |

### Report Format (D/S)
| Code | Format | Description |
|------|--------|-------------|
| D | Detailed | Item-wise detailed listing |
| S | Summarized | Condensed summary view |

---

## Summary Cards

Report ke top par 5 summary cards display hote hain:

| Card | Color | Description |
|------|-------|-------------|
| Items | Blue (Primary) | Total items with shortage |
| Out of Stock | Red (Danger) | Items with zero stock |
| Low Stock | Yellow (Warning) | Items with low stock |
| Sold Qty | Cyan (Info) | Total quantity sold |
| Shortage | Green (Success) | Total shortage quantity |

---

## Output Columns

| Column | Description |
|--------|-------------|
| # | Serial number |
| Item Code | Product code |
| Item Name | Product name |
| Company | Manufacturer/Brand |
| Packing | Pack size |
| Sold Qty | Quantity sold in period |
| Stock | Current stock quantity |
| Shortage | Shortage quantity (Red/Bold) |
| Status | Stock status badge |

---

## Status Badges

| Badge | Color | Meaning |
|-------|-------|---------|
| Out of Stock | Red (bg-danger) | Zero stock available |
| Low Stock | Yellow (bg-warning) | Stock below threshold |

---

## Report Structure

```
┌─────────────────────────────────────────────────────────────┐
│ [Items] [Out of Stock] [Low Stock] [Sold Qty] [Shortage]    │  ← Summary Cards
├─────────────────────────────────────────────────────────────┤
│ # │ Code │ Item Name │ Company │ Sold │ Stock │ Short │ Sts │  ← Header
├─────────────────────────────────────────────────────────────┤
│ 1 │ I001 │ Product A │ Comp A  │ 100  │  0    │  100  │ 🔴  │  ← Out of Stock
│ 2 │ I002 │ Product B │ Comp B  │  50  │  10   │   40  │ 🟡  │  ← Low Stock
│ 3 │ I003 │ Product C │ Comp A  │  80  │  20   │   60  │ 🟡  │  ← Low Stock
│ ...                                                         │
├─────────────────────────────────────────────────────────────┤
│ Total (XX Items):              XXX    XXX     XXX           │  ← Footer
└─────────────────────────────────────────────────────────────┘
```

---

## Shortage Calculation

```
Shortage Qty = Sold Qty - Current Stock (if negative, shown as shortage)
```

### Status Logic
- **Out of Stock**: Current Stock = 0
- **Low Stock**: Current Stock > 0 but below demand/threshold

---

## Action Buttons

| Button | Shortcut | Description |
|--------|----------|-------------|
| Excel | Alt+E | Export to Excel |
| View | Alt+V | Generate report |
| Print | Alt+P | Print report |
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

## Use Cases

### 1. Daily Stock Check
- Set date to current day
- Identify items running low
- Plan immediate purchases

### 2. Company-wise Shortage
- Filter by specific Company
- Analyze brand-wise shortages
- Prioritize supplier orders

### 3. Purchase Order Planning
- Generate shortage report
- Export to Excel
- Create purchase orders for shortage items

### 4. Out of Stock Alert
- Focus on "Out of Stock" items
- Immediate action required
- Prevent lost sales

### 5. Demand Analysis
- Compare Sold Qty vs Stock
- Understand demand patterns
- Adjust reorder levels

### 6. Weekly/Monthly Review
- Set date range for week/month
- Analyze shortage trends
- Improve inventory planning

---

## Color Coding

| Element | Color | Meaning |
|---------|-------|---------|
| Shortage Qty | Red (text-danger) | Shortage amount |
| Out of Stock Badge | Red (bg-danger) | Critical - no stock |
| Low Stock Badge | Yellow (bg-warning) | Warning - low stock |

---

## Empty State

Jab koi shortage nahi hoti:
```
"No shortage items found. All items have sufficient stock!"
```

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- Item Model
- Company Model
- Stock Model
- SaleTransactionItem Model

### Calculation Logic
- Aggregates sales for date range
- Compares with current stock
- Identifies items where demand > supply

### UI Components
- Pink header card with report title
- Gray filter section
- 5 color-coded summary cards
- Responsive data table with sticky header
- Status badges per row
- Maximum table height: 55vh with scroll

### Performance
- Report generates on "View" button click
- Analyzes sales vs stock
- Use Company filter for faster results
