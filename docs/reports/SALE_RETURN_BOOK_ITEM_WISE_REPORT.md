# Sale / Return Book Item Wise Report Documentation

## Overview
Sale / Return Book Item Wise report ek comparative analysis report hai jo item-wise sales aur returns ko side-by-side compare karta hai. Ye report net sales calculation, return analysis aur product performance tracking ke liye useful hai.

---

## Report Details

### Purpose
- Item-wise sale vs return comparison
- Net sales calculation (Sale - Return)
- Return rate analysis per item
- Product performance evaluation
- Company/Brand-wise return tracking

### Route
`admin.reports.sales.sale-return-book-item-wise`

---

## Filter Options

### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Report period start date |
| To Date | Report period end date |

### Product Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Item | Dropdown | Filter by specific item |
| Company | Dropdown | Filter by manufacturer/brand |

---

## Summary Cards

Report ke top par 6 summary cards display hote hain:

| Card | Color | Description |
|------|-------|-------------|
| Sale Qty | Green (Success) | Total sale quantity |
| Sale Amt | Green (Success) | Total sale amount |
| Return Qty | Red (Danger) | Total return quantity |
| Return Amt | Red (Danger) | Total return amount |
| Net Qty | Blue (Primary) | Net quantity (Sale - Return) |
| Net Amt | Blue (Primary) | Net amount (Sale - Return) |

---

## Output Columns

| Column | Color | Description |
|--------|-------|-------------|
| # | - | Serial number |
| Item Code | - | Product code |
| Item Name | - | Product name |
| Company | - | Manufacturer/Brand name |
| Sale Qty | Green | Quantity sold |
| Sale Amt | Green | Sale amount |
| Ret Qty | Red | Quantity returned |
| Ret Amt | Red | Return amount |
| Net Qty | Bold | Net quantity (Sale - Return) |
| Net Amt | Bold | Net amount (Sale - Return) |

---

## Calculations

### Net Calculations
```
Net Qty = Sale Qty - Return Qty
Net Amt = Sale Amt - Return Amt
```

### Return Rate (for analysis)
```
Return Rate % = (Return Qty / Sale Qty) × 100
```

---

## Color Coding

| Element | Color | Meaning |
|---------|-------|---------|
| Sale columns | Green (text-success) | Positive/Revenue |
| Return columns | Red (text-danger) | Negative/Loss |
| Net columns | Bold | Final calculated values |

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

### 1. High Return Items Identification
- Generate report for period
- Sort by Return Qty (descending)
- Identify items with high returns
- Investigate quality issues

### 2. Net Sales Analysis
- View Net Qty and Net Amt columns
- Identify profitable items
- Compare actual vs gross sales

### 3. Company-wise Return Analysis
- Filter by specific Company
- Analyze return patterns per brand
- Negotiate with suppliers for quality

### 4. Product Performance Review
- Compare Sale vs Return ratios
- Identify underperforming products
- Make stocking decisions

### 5. Period Comparison
- Run report for different periods
- Compare return trends
- Track improvement over time

---

## Report Insights

### Healthy Product Indicators
- Low Return Qty relative to Sale Qty
- Positive Net Qty and Net Amt
- Return Rate < 5%

### Problem Product Indicators
- High Return Qty
- Negative or low Net values
- Return Rate > 10%

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- Item Model
- Company Model
- SaleTransaction Model
- SaleReturnTransaction Model

### Data Aggregation
- Sales and Returns aggregated by Item
- Grouped by Item Code
- Company name included for reference

### UI Components
- Pink header card with report title
- Gray filter section
- 6 color-coded summary cards (3 pairs)
- Responsive data table with sticky header
- Color-coded columns (Green/Red)
- Maximum table height: 55vh with scroll

### Performance
- Report generates on "View" button click
- Large item catalogs may take time
- Use Item/Company filter to narrow results
