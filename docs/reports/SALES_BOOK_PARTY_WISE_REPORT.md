# Sales Book Party Wise Report Documentation

## Overview
Sales Book Party Wise report ek customer-centric sales report hai jo party/customer-wise sales summary provide karta hai. Ye report customer performance analysis, top customers identification aur bill-wise drill-down ke liye useful hai.

---

## Report Details

### Purpose
- Customer-wise sales summary
- Party performance analysis
- Top customers identification
- Bill-wise transaction drill-down
- Area-wise customer sales
- Amount range based filtering

### Route
`admin.reports.sales.sales-book-party-wise`

---

## Report Types (Radio Buttons)

| Code | Type | Description |
|------|------|-------------|
| 1 | Sale | Normal sales |
| 2 | Sale Return | Sale returns |
| 3 | Debit Note | Debit notes |
| 4 | Credit Note | Credit notes |
| 5 | Consolidated Sale | All transactions combined |

---

## Filter Options

### Date & Series Filters
| Filter | Description |
|--------|-------------|
| From Date | Report period start date |
| To Date | Report period end date |
| Series | Invoice series filter |

### Display Options
| Filter | Options | Description |
|--------|---------|-------------|
| Selective | Y/N | Selective party mode |
| Bill Wise | Y/N | Show individual bills under party |
| T/R | Tax/Retail/All | Invoice type filter |

### Tagging Options
| Filter | Options | Description |
|--------|---------|-------------|
| Tagged | Y/N | Show only tagged parties |
| Remove Tags | Y/N | Clear existing tags |
| Flag | Text | Custom flag filter |

### Party Filter
| Filter | Options | Description |
|--------|---------|-------------|
| Party | Dropdown | Filter by specific customer |

### Print Options
| Filter | Options | Description |
|--------|---------|-------------|
| Print Addr | Y/N | Include address in print |
| Print S.Tax | Y/N | Include service tax details |

### Sorting Options
| Filter | Options | Description |
|--------|---------|-------------|
| Sort By | P(arty)/A(mount) | Sort criteria |
| A/D | A(sc)/D(esc) | Sort direction |

### Amount Range Filter
| Filter | Description |
|--------|-------------|
| Amt > | Minimum amount filter |
| Amt < | Maximum amount filter |

### Checkbox Options
| Option | Description |
|--------|-------------|
| With Vat | Include VAT details |
| Bill Amount | Show bill amount |
| GST Summary | Include GST summary |

---

## Output Columns

### Party Summary Row (Cyan Background)
| Column | Description |
|--------|-------------|
| # | Serial number |
| Code | Customer code |
| Party Name | Customer name |
| Area | Customer area |
| Bills | Number of bills |
| NT Amount | Net taxable amount |
| Discount | Discount amount (Red) |
| Tax | Tax amount |
| Net Amount | Final amount (Green) |

### Bill Detail Row (When Bill Wise = Y)
| Column | Description |
|--------|-------------|
| Date | Bill date (dd-mm format) |
| Bill No | Invoice number (clickable link) |
| NT Amount | Net taxable amount |
| Discount | Discount amount |
| Tax | Tax amount |
| Net Amount | Net amount |

---

## Report Structure

```
┌─────────────────────────────────────────────────────────────┐
│ # │ Code │ Party Name    │ Area │ Bills │ NT Amt │ Net Amt │  ← Header
├─────────────────────────────────────────────────────────────┤
│ 1 │ C001 │ Customer A    │ Area1│   5   │ 50000  │ 55000   │  ← Cyan summary
│   │ 01-01│ A-001         │      │       │ 10000  │ 11000   │  ← Bill detail
│   │ 02-01│ A-002         │      │       │ 15000  │ 16500   │  ← Bill detail
│   │ ...  │ ...           │      │       │ ...    │ ...     │
├─────────────────────────────────────────────────────────────┤
│ 2 │ C002 │ Customer B    │ Area2│   3   │ 30000  │ 33000   │  ← Cyan summary
│   │ ...  │ ...           │      │       │ ...    │ ...     │
├─────────────────────────────────────────────────────────────┤
│ Grand Total:                    │  XX   │ XXXXX  │ XXXXX   │  ← Dark footer
└─────────────────────────────────────────────────────────────┘
```

---

## Interactive Features

### Bill No Link
- Bill detail rows mein Bill No clickable hai
- Click karne par sale transaction detail page open hota hai

### Report Type Radio Buttons
- Top section mein radio buttons se report type select kar sakte hain
- Selection automatically hidden field mein sync hota hai

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

### 1. Top Customers Analysis
- Sort By = "A" (Amount)
- A/D = "D" (Descending)
- View top customers by sales value

### 2. Customer Transaction History
- Select specific Party
- Bill Wise = "Y"
- See all transactions for customer

### 3. Area-wise Customer Performance
- View Area column
- Analyze customer distribution by area

### 4. High Value Customers
- Set Amt > (minimum threshold)
- Filter customers above certain sales value

### 5. Low Performing Customers
- Set Amt < (maximum threshold)
- Identify customers with low sales

### 6. Consolidated Customer Statement
- Report Type = 5 (Consolidated)
- See all transaction types combined

---

## Color Coding

| Element | Color | Meaning |
|---------|-------|---------|
| Party Summary Row | Cyan (table-info) | Customer header |
| Discount Column | Red (text-danger) | Discount amount |
| Net Amount | Green (text-success) | Final amount |
| Bill Detail | Small/Muted | Individual transactions |

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- SaleTransaction Model
- Customer Model
- Area Model

### Data Grouping
- Sales grouped by Customer ID
- Each customer shows summary row
- Bill details shown when Bill Wise = Y

### UI Components
- Pink header card with report title
- Report type radio button group
- Gray filter section with 4 rows
- Checkbox options in bordered box
- Responsive data table with sticky header
- Party-wise grouped rows
- Maximum table height: 50vh with scroll

### Performance
- Report generates on "View" button click
- Large customer base may take time
- Use Party filter for specific customer
- Amount range helps narrow results
