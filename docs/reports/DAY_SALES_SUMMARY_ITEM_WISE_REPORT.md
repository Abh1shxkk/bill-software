# Day Sales Summary - Item Wise Report Documentation

## Overview
Day Sales Summary - Item Wise report ek inventory-focused sales report hai jo item-wise daily sales quantity aur value summarize karta hai. Ye report stock management, reorder planning aur product performance analysis ke liye useful hai.

---

## Report Details

### Purpose
- Daily item-wise sales tracking
- Stock balance monitoring
- Purchase order planning (PO)
- Product performance analysis
- Company/Brand-wise sales summary

### Route
`admin.reports.sales.day-sales-summary-item-wise`

---

## Filter Options

### Date & Invoice Filters
| Filter | Description |
|--------|-------------|
| From Date | Report period start date |
| To Date | Report period end date |
| Inv From | Starting invoice number (default: 0) |
| Inv To | Ending invoice number (default: 9999999) |

### Location Filter (L/C/B)
| Code | Option | Description |
|------|--------|-------------|
| L | Local | Local sales only |
| C | Central | Central/Interstate sales only |
| B | Both | All sales (default) |

### Category Filter
| Filter | Options | Description |
|--------|---------|-------------|
| Category | Dropdown | Filter by item category |

### Display Options
| Filter | Options | Description |
|--------|---------|-------------|
| Show Val | Y/N | Show value column (default: N) |
| With VAT | Y/N/B | VAT filter - Yes/No/Both |
| Add Free Qty | Checkbox | Include free quantity in totals |

### Transaction Type
| Code | Type | Description |
|------|------|-------------|
| 1 | Sale | Only sales transactions |
| 2 | Return | Only return transactions |
| 3 | Both | Sales and returns combined |

### Sorting Options
| Filter | Options | Description |
|--------|---------|-------------|
| Order By | Company/Item/Qty/Value | Sort criteria |
| A/D | A(scending)/D(escending) | Sort direction |

---

## Output Columns

| Column | Description |
|--------|-------------|
| COMPANY | Manufacturer/Brand name |
| ITEM NAME | Product name |
| PACK | Packing size |
| SALE | Total quantity sold |
| VALUE | Total sale value |
| BAL. | Current stock balance |
| PO | Purchase order quantity |
| PO.XX | Purchase order value |
| MRP | Maximum retail price |

---

## Summary Footer

| Metric | Description |
|--------|-------------|
| Total Qty | Sum of all quantities sold |
| Total Amount | Sum of all sale values |

---

## Action Buttons

| Button | Shortcut | Description |
|--------|----------|-------------|
| Excel | Alt+E | Export to Excel |
| View | Alt+V | Generate report |
| Print | Alt+P | Print report |
| Close | Alt+C | Return to reports menu |

### Additional Actions
| Button | Description |
|--------|-------------|
| Stock Ledger | View stock ledger for selected item |

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

### 1. Daily Sales Review
- Set From/To date to same day
- View all items sold today
- Check quantities and values

### 2. Stock Reorder Planning
- Check BAL. (Balance) column
- Identify low stock items
- Plan purchase orders using PO column

### 3. Top Selling Products
- Order By = "Qty" or "Value"
- A/D = "D" (Descending)
- See best performing products

### 4. Company/Brand Performance
- Order By = "Company"
- Analyze brand-wise sales
- Compare manufacturer performance

### 5. Category Analysis
- Select specific Category
- View category-wise item sales
- Compare product performance within category

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- Item Model
- Company Model
- Category Model
- Sale Model
- Stock Model

### UI Components
- Pink header card with report title
- Gray filter section with 2-row layout
- Radio button group for transaction type
- Responsive data table with sticky header
- Dark footer with totals
- Maximum table height: 55vh with scroll

### Performance
- Report generates on "View" button click
- Large item catalogs may take time
- Use Category filter to narrow results
- Invoice range filter helps with large datasets
