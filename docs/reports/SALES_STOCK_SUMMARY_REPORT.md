# Stock Sale Summary Report Documentation

## Overview
Stock Sale Summary report ek operational report hai jo sales transactions ko different groupings (Salesman/Area/Route) ke saath display karta hai. Ye report tagging feature ke saath aata hai jo selective transactions ko mark karne ki facility deta hai - useful for delivery tracking, collection follow-up aur batch processing.

---

## Report Details

### Purpose
- Sales transactions summary with grouping
- Transaction tagging for batch operations
- Salesman/Area/Route wise sales tracking
- Delivery and collection management
- Never printed bills identification

### Route
`admin.reports.sales.sales-stock-summary`

---

## Filter Options

### Report Type (S/R/C)
| Code | Type | Description |
|------|------|-------------|
| S | Sale | Normal sales |
| R | Return | Sale returns |
| C | Consolidated | Combined view |

### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Report period start date |
| To Date | Report period end date |

### Display Options
| Filter | Options | Description |
|--------|---------|-------------|
| Show Total | Y/N | Show totals in report |
| Never Printed | Y/N | Show only never printed bills |
| Across/Down | Y/N | Layout orientation |

### Grouping (S/A/R)
| Code | Group By | Description |
|------|----------|-------------|
| S | Salesman | Group by salesman |
| A | Area | Group by area |
| R | Route | Group by route |

### Other Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Vou.Type | Code | Voucher type filter (default: 00) |
| Sales Man | Dropdown | Filter by specific salesman |
| Area | Dropdown | Filter by area |
| Route | Dropdown | Filter by route |

---

## Output Columns

| Column | Description |
|--------|-------------|
| Date | Transaction date |
| TRN. No. | Transaction/Invoice number (clickable link) |
| Party Name | Customer name |
| Sales Man | Salesman name |
| Amount | Net amount |
| Tag | Checkbox for tagging |

---

## Report Structure

```
┌─────────────────────────────────────────────────────────────┐
│ Date │ TRN. No. │ Party Name │ Sales Man │ Amount │ Tag    │  ← Header
├─────────────────────────────────────────────────────────────┤
│ 👤 Salesman A                              [X Bills]        │  ← Yellow group header
├─────────────────────────────────────────────────────────────┤
│ 01-01│ A-001   │ Customer 1 │ Salesman A│ 10000  │ ☐      │
│ 02-01│ A-002   │ Customer 2 │ Salesman A│ 15000  │ ☐      │
├─────────────────────────────────────────────────────────────┤
│ Salesman A Total:                          25000            │  ← Gray subtotal
├─────────────────────────────────────────────────────────────┤
│ 👤 Salesman B                              [X Bills]        │
│ ...                                                         │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ TAG (+) / UNTAG (-)    TOTAL: XX  XXXXX   TAGGED: XX  XXXXX │  ← Footer
└─────────────────────────────────────────────────────────────┘
```

---

## Tagging Feature

### Purpose
- Mark transactions for batch processing
- Track delivered/collected bills
- Selective printing
- Follow-up management

### Footer Totals
| Metric | Description |
|--------|-------------|
| TOTAL | Total bills count and amount |
| TAGGED | Tagged bills count and amount |

### Tag Operations
- **TAG (+)**: Check checkbox to tag
- **UNTAG (-)**: Uncheck to remove tag

---

## Interactive Features

### TRN. No. Link
- Transaction number clickable hai
- Click karne par sale transaction detail page open hota hai

### Tag Checkbox
- Har row mein checkbox hai
- Real-time tagging for batch operations

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

### 1. Salesman-wise Daily Sales
- S/A/R = "S" (Salesman)
- Set date to current day
- View salesman-wise sales summary

### 2. Area-wise Sales Tracking
- S/A/R = "A" (Area)
- Analyze area-wise sales distribution

### 3. Route-wise Delivery Planning
- S/A/R = "R" (Route)
- Plan deliveries by route
- Tag delivered bills

### 4. Never Printed Bills
- Never Printed = "Y"
- Find bills that were never printed
- Ensure all bills are printed

### 5. Collection Follow-up
- Generate report
- Tag collected bills
- Track pending collections

### 6. Batch Processing
- Select multiple bills using tags
- Process tagged bills together
- Track progress via tagged count

---

## Grouping Examples

### Salesman Wise (S)
```
Salesman A [5 Bills]
  - Bill 1, Bill 2, Bill 3...
  Salesman A Total: ₹50,000

Salesman B [3 Bills]
  - Bill 1, Bill 2, Bill 3...
  Salesman B Total: ₹30,000
```

### Area Wise (A)
```
Area North [4 Bills]
  - Bill 1, Bill 2...
  Area North Total: ₹40,000

Area South [6 Bills]
  - Bill 1, Bill 2...
  Area South Total: ₹60,000
```

### Route Wise (R)
```
Route 1 [5 Bills]
  - Bill 1, Bill 2...
  Route 1 Total: ₹45,000

Route 2 [4 Bills]
  - Bill 1, Bill 2...
  Route 2 Total: ₹35,000
```

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- SaleTransaction Model
- Customer Model
- Salesman Model
- Area Model
- Route Model

### Grouping Logic
- Dynamic grouping based on S/A/R selection
- Subtotals calculated per group
- Grand totals in footer card

### UI Components
- Pink header card with report title
- Gray filter section with 2 rows
- Responsive data table with sticky header
- Group-wise rows with subtotals
- Tag checkboxes per row
- Footer card with total/tagged counts
- Maximum table height: 55vh with scroll

### Performance
- Report generates on "View" button click
- Large date ranges may include many transactions
- Use filters to narrow results
