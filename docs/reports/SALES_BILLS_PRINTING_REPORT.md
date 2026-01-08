# Sales Bills Printing Report Documentation

## Overview
Sales Bills Printing report ek utility report hai jo multiple invoices ko ek jagah se print karne ki facility provide karta hai. Ye report batch printing ke liye useful hai - jaise end of day sabhi bills print karna ya salesman-wise bills print karna.

---

## Report Details

### Purpose
- Batch invoice printing
- Salesman-wise bill grouping for printing
- Quick access to individual bill print
- Customer contact details for delivery
- End of day bill printing

### Route
`admin.reports.sales.sales-bills-printing`

---

## Filter Options

### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Report period start date |
| To Date | Report period end date |

### Print Options
| Filter | Options | Description |
|--------|---------|-------------|
| Print Grid [Y/N] | Y/N | Grid format printing (default: N) |
| B(ill)/S(alesman) Wise | B/S | Grouping option (default: S) |

### Other Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Sales Man | Dropdown | Filter by specific salesman |
| Remarks | Text | Add custom remarks |

---

## Grouping Options (B/S)

| Code | Option | Description |
|------|--------|-------------|
| S | Salesman Wise | Group bills by salesman with subtotals |
| B | Bill Wise | Flat list without grouping |

---

## Output Columns

| Column | Description |
|--------|-------------|
| # | Serial number |
| Date | Invoice date |
| Bill No | Invoice number (clickable link) |
| Code | Customer code |
| Party Name | Customer name |
| Address | Customer address |
| Mobile | Customer mobile number |
| Discount | Discount amount |
| Tax | Tax amount |
| Net Amount | Final invoice amount |
| Print | Individual print button |

---

## Report Structure (Salesman Wise)

```
┌─────────────────────────────────────────────────────────────┐
│ 👤 Salesman A                              [X Bills]        │  ← Cyan header
├─────────────────────────────────────────────────────────────┤
│ Bill 1... Customer... Address... Mobile... Amt    [🖨️]     │
│ Bill 2... Customer... Address... Mobile... Amt    [🖨️]     │
├─────────────────────────────────────────────────────────────┤
│ Salesman A Total:                    Disc   Tax   Net Amt   │  ← Gray subtotal
├─────────────────────────────────────────────────────────────┤
│ 👤 Salesman B                              [X Bills]        │
│ ...                                                         │
├─────────────────────────────────────────────────────────────┤
│ Grand Total (XX Bills):              Disc   Tax   Net Amt   │  ← Dark footer
└─────────────────────────────────────────────────────────────┘
```

---

## Interactive Features

### Bill No Link
- Bill No column mein clickable link hai
- Click karne par sale transaction detail page open hota hai

### Individual Print Button
- Har row mein printer icon button hai
- Click karne par us specific bill ka print preview open hota hai (new tab)

---

## Action Buttons

| Button | Shortcut | Description |
|--------|----------|-------------|
| Excel | Alt+E | Export to Excel |
| View | Alt+V | Generate report |
| Print | Alt+P | Print all bills |
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

### 1. End of Day Bill Printing
- Set From/To date to current day
- View all bills
- Print all at once or individually

### 2. Salesman-wise Bill Distribution
- B/S = "S" (Salesman Wise)
- Group bills by salesman
- Print salesman-wise for distribution

### 3. Delivery Preparation
- View customer Address and Mobile
- Prepare delivery route
- Print bills for delivery person

### 4. Reprint Missing Bills
- Search by date range
- Find specific bill
- Click individual print button

### 5. Bulk Export for Records
- Generate report
- Export to Excel
- Maintain records

---

## Customer Information Display

Report mein customer details bhi show hote hain jo delivery ke liye useful hain:

| Field | Purpose |
|-------|---------|
| Party Name | Customer identification |
| Address | Delivery location |
| Mobile | Contact for delivery |

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- SaleTransaction Model
- Customer Model
- Salesman Model

### Grouping Logic
- When B/S = "S": Bills grouped by Salesman name
- When B/S = "B": Flat list, no grouping
- Subtotals shown only in Salesman-wise mode

### UI Components
- Pink header card with report title
- Gray filter section
- Responsive data table with sticky header
- Salesman-wise grouped rows (when S mode)
- Individual print buttons per row
- Maximum table height: 65vh with scroll

### Print Functionality
- Individual print: Opens bill in new tab with print=1 parameter
- Bulk print: Opens print view of entire report

### Performance
- Report generates on "View" button click
- Large date ranges may include many bills
- Use Salesman filter to narrow results
