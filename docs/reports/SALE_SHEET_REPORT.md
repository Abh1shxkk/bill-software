# Sale Sheet (Sale Book With Item Details) Report Documentation

## Overview
Sale Sheet report ek detailed item-level sales report hai jo har invoice ke individual items ki complete information provide karta hai. Ye report Sale Book se different hai kyunki ye invoice-level nahi balki item-level data show karta hai - useful for detailed analysis aur audit purposes.

---

## Report Details

### Purpose
- Item-level sales details
- Batch-wise sales tracking
- Detailed discount and tax breakup per item
- Customer-wise item sales analysis
- Expiry sales tracking
- Comprehensive audit trail

### Route
`admin.reports.sales.sale-sheet`

---

## Filter Options

### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Report period start date |
| To Date | Report period end date |

### Primary Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Party | Dropdown | Filter by specific customer |
| Type | 1-Sale/2-Return/3-Expiry | Transaction type |
| Series | Dropdown | Invoice series filter |

### Location Filters (Fieldset)
| Filter | Options | Description |
|--------|---------|-------------|
| Sales Man | Dropdown | Filter by salesman |
| Area | Dropdown | Filter by area |
| Route | Dropdown | Filter by route |
| State | Dropdown | Filter by state |

---

## Transaction Types

| Code | Type | Description |
|------|------|-------------|
| 1 | Sale | Normal sales items |
| 2 | Return | Sale return items |
| 3 | Expiry | Expiry-related sales |

---

## Summary Cards

Report ke top par 5 summary cards display hote hain:

| Card | Color | Description |
|------|-------|-------------|
| Items | Blue (Primary) | Total item line count |
| Qty | Cyan (Info) | Total quantity |
| Free | Green (Success) | Total free quantity |
| Discount | Yellow (Warning) | Total discount amount |
| Net Amount | Red (Danger) | Total net amount |

---

## Output Columns

| Column | Description |
|--------|-------------|
| # | Serial number |
| Date | Transaction date |
| Bill No | Invoice number (clickable link) |
| Customer | Customer name |
| Item Code | Product code |
| Item Name | Product name |
| Batch | Batch number |
| Qty | Sale quantity |
| Free | Free quantity |
| Rate | Sale rate |
| Disc | Discount amount |
| Tax | Tax amount |
| Net Amt | Net amount per item |

---

## Interactive Features

### Bill No Link
- Bill No column mein clickable link hai
- Click karne par sale transaction detail page open hota hai

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

## Sale Sheet vs Sale Book Comparison

| Aspect | Sale Book | Sale Sheet |
|--------|-----------|------------|
| Level | Invoice-level | Item-level |
| Rows | One row per invoice | One row per item |
| Detail | Summary per bill | Full item details |
| Batch Info | No | Yes |
| Item Rate | No | Yes |
| Use Case | Overview | Detailed analysis |

---

## Use Cases

### 1. Batch-wise Sales Tracking
- View Batch column for each item
- Track which batches are selling
- Expiry management

### 2. Item-level Discount Analysis
- See discount per item
- Identify high-discount items
- Margin analysis

### 3. Customer Purchase Pattern
- Filter by specific Party
- See all items purchased
- Customer behavior analysis

### 4. Salesman Performance Detail
- Filter by Salesman
- See item-wise sales
- Product mix analysis

### 5. Expiry Sales Tracking
- Type = 3 (Expiry)
- Track expiry-related sales
- Loss analysis

### 6. Tax Audit Preparation
- Complete item-level details
- Tax breakup per item
- Export to Excel for auditor

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- SaleTransaction Model
- SaleTransactionItem Model
- Customer Model
- Salesman Model
- Area Model
- Route Model
- State Model
- Item Model

### UI Components
- Pink header card with report title
- Gray filter section with fieldset for location filters
- 5 color-coded summary cards
- Responsive data table with sticky header
- Clickable Bill No links
- Maximum table height: 50vh with scroll

### Performance
- Report generates on "View" button click
- Item-level data can be large
- Use filters to narrow results
- Excel export recommended for large datasets
