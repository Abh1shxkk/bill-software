# Sale Book Module Documentation

## Overview
Sale Book module Sales Reports section ka core component hai jo comprehensive sales transaction reporting provide karta hai. Is module mein 7 sub-reports hain jo different aspects of sales data ko cover karte hain.

---

## Module Structure

```
sale-book/
├── sales-book.blade.php              # Main Sale Book Report
├── sales-book-gstr.blade.php         # GSTR Format Sale Book
├── sales-book-extra-charges.blade.php # Extra Charges Report
├── sales-book-tcs.blade.php          # TCS (Tax Collected at Source) Report
├── tcs-eligibility.blade.php         # TCS Eligibility Check Report
├── tds-input.blade.php               # TDS Input Report
├── area-wise-sale.blade.php          # Area Wise Sale Report
└── *-print.blade.php                 # Print versions of each report
```

---

## 1. Sales Book (Main Report)

### Purpose
Primary sales register jo sabhi sales transactions ka detailed/summarized view provide karta hai.

### Route
`admin.reports.sales.sales-book`

### Report Types
| Type | Code | Description |
|------|------|-------------|
| Sale | 1 | Normal sales invoices |
| Sale Return | 2 | Sales return transactions |
| Debit Note | 3 | Debit notes issued |
| Credit Note | 4 | Credit notes issued |
| Consolidated | 5 | All transactions combined |
| All CN_DN | 6 | All Credit & Debit Notes |

### Filter Options

#### Date Filters
- **From Date**: Start date for report period
- **To Date**: End date for report period

#### Report Format
| Format | Code | Description |
|--------|------|-------------|
| Detailed | D | Bill-wise detailed listing |
| Summarised | S | Day-wise summary |
| Monthly | M | Month-wise summary |
| Group | G | Grouped by criteria |

#### Transaction Filters
| Filter | Options | Description |
|--------|---------|-------------|
| VAT ROff [DN/CN] | Y/N | VAT round-off for Debit/Credit Notes |
| T(ax)/R(etail) | All/T/R | Tax invoice or Retail invoice filter |
| Cancelled | Y/N | Include cancelled bills |
| Br.Exp | Y/N/A | Breakage/Expiry filter |
| Day Total | Y/N | Show day-wise totals |
| Series | Dropdown | Filter by invoice series |
| Credit Card | Y/N | Credit card payment filter |

#### Party Filters
- **User**: Filter by user who created transaction
- **F/L User**: First/Last user filter
- **Party Code**: Direct party code input
- **Party Name**: Customer dropdown selection
- **Sales Man**: Salesman filter
- **S.Man Master**: Salesman from master (Y/N)

#### Location Filters
| Filter | Options | Description |
|--------|---------|-------------|
| L/C/B/E | Local/Central/Both/Export | Sale type by location |
| Sale Type | W/R/I/D/O | Wholesale/Retail/Institution/Dept Store/Others |
| GSTN | All/With/Without | GST number filter |
| Area | Dropdown | Area-wise filter |
| Route | Dropdown | Route-wise filter |
| State | Dropdown | State-wise filter |

#### Display Options (Checkboxes)
- GST Details
- GR Details
- Cash/Credit Card
- Show Sales Man
- Order by Customer
- Deduct Add Less Bill Amt
- Show AREA
- With Address

### Output Columns
| Column | Description |
|--------|-------------|
| # | Serial number |
| Date | Transaction date |
| Bill No | Invoice number with series |
| Party Name | Customer name with code |
| Area | Customer area (optional) |
| Salesman | Salesman name (optional) |
| Gross Amt | Gross amount before discount |
| Discount | Discount amount |
| Sch Amt | Scheme amount |
| Tax | Tax amount |
| Net Amount | Final bill amount |
| Address | Customer address (optional) |

### Summary Cards
- Total Bills
- Gross Amount
- Discount
- Tax Amount
- Scheme Amount
- Net Amount

### Action Buttons
- **Excel**: Export to Excel
- **State Wise**: State-wise grouping
- **View**: Display report
- **Print**: Print report
- **Close**: Return to reports menu

### Keyboard Shortcuts
| Key | Action |
|-----|--------|
| Alt+V | View report |
| Alt+P | Print report |
| Alt+C | Close |
| Alt+E | Export to Excel |

---

## 2. Sales Book GSTR

### Purpose
GST Return filing ke liye formatted sales report with CGST, SGST, IGST breakup.

### Route
`admin.reports.sales.sales-book-gstr`

### Additional Report Types
| Type | Code | Description |
|------|------|-------------|
| Expiry Sale | 7 | Expiry-related sales |
| Voucher Sale | 8 | Voucher-based sales |

### Unique Filters
- **Supp.Exp**: Supplier expiry (Y/N)
- **Cust.Exp**: Customer expiry (Y/N)
- **WOST**: WOST filter field

### Output Columns
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Bill No | Invoice number |
| Party Name | Customer name |
| GSTIN | Customer GST number |
| State | State code |
| Taxable | Taxable amount |
| CGST | Central GST amount |
| SGST | State GST amount |
| IGST | Integrated GST amount |
| Net Amt | Net amount |

### Summary Cards
- Bills Count
- Taxable Amount
- CGST Amount
- SGST Amount
- IGST Amount
- Total Tax
- Net Amount

### Additional Actions
- **Format-2**: Alternative format view
- **State Wise**: State-wise grouping

---

## 3. Sales Book Extra Charges

### Purpose
Extra charges (SC, FT, TCS) ke saath detailed sales report.

### Route
`admin.reports.sales.sales-book-extra-charges`

### Filter Options
- Date Range (From/To)
- D/S Format (Detailed/Summarised)
- L/C/B (Local/Central/Both)
- GSTN Filter
- Customer Selection
- Salesman, Area, Route, State filters

### Display Options
- Tag Customer
- Order by Customer

### Output Columns
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Bill No | Invoice number |
| Code | Customer code |
| Party Name | Customer name |
| NT Amt | Net taxable amount |
| Disc | Discount amount |
| Scheme | Scheme amount |
| SC | Service charge |
| FT | Freight charges |
| Tax | Tax amount |
| TCS | TCS amount |
| Net Amt | Final amount |

### Summary Cards
- Bills Count
- NT Amount
- Discount
- Scheme
- SC/FT Combined
- Tax
- Net Amount

---

## 4. Sales Book With TCS

### Purpose
TCS (Tax Collected at Source) ke saath sales transactions ka report.

### Route
`admin.reports.sales.sales-book-tcs`

### Report Format Options
- **Detailed**: Bill-wise listing
- **Summarised**: Summary view

### TCS Filter Options
| Option | Code | Description |
|--------|------|-------------|
| With TCS | T | Only TCS applicable bills |
| Without TCS | W | Bills without TCS |
| All | A | All bills |

### Sale Type Options
| Option | Code | Description |
|--------|------|-------------|
| Sale | S | Only sales |
| Return | R | Only returns |
| Both | B | Sales and returns |

### Unique Filters
- **From Source**: Transaction (T) / Master (M)

### Output Columns
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Trn No | Transaction number |
| Code | Customer code |
| Party Name | Customer name |
| PAN No | Customer PAN number |
| Taxable | Taxable amount |
| Tax Amt | Tax amount |
| TCS% | TCS percentage |
| TCS Amt | TCS amount |
| Net Amt | Net amount |

### Summary Cards
- Bills Count
- Taxable Amount
- Tax Amount
- TCS Amount
- Net Amount

---

## 5. TCS Eligibility Report

### Purpose
Parties ko identify karna jinpe TCS applicable hai (₹50 Lakhs+ sales threshold).

### Route
`admin.reports.sales.tcs-eligibility`

### Key Filters
| Filter | Description |
|--------|-------------|
| Date Range | Report period |
| Party Type | Customer (C) / Supplier (S) |
| Amount >= | Threshold amount (default: ₹50,00,000) |
| L/C/B | Local/Central/Both |
| State | State filter |

### Business Rule
TCS @ 0.1% is applicable on sales exceeding ₹50 Lakhs to a single party in a financial year.

### Output Columns
| Column | Description |
|--------|-------------|
| Party Code | Customer/Supplier code |
| Party Name | Name |
| GST No | GST number |
| PAN No | PAN number |
| Amount | Total sales amount |
| TCS% | TCS rate |
| TCS Amt | Calculated TCS amount |
| TCS Appl. | TCS applicable (Yes/No badge) |

### Summary Cards
- Number of Parties
- Total Amount
- TCS Amount

---

## 6. TDS Input Report

### Purpose
TDS (Tax Deducted at Source) input details ka report.

### Route
`admin.reports.sales.tds-input`

### Filter Options
- Date Range
- Format (Detailed/Summarised)
- L/C/B
- Customer
- Salesman, Area, Route, State

### Output Columns
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Bill No | Invoice number |
| Code | Customer code |
| Party Name | Customer name |
| PAN | PAN number |
| Amount | Bill amount |
| Taxable | Taxable amount |
| TDS% | TDS percentage |
| TDS Amt | TDS amount |

### Summary Cards
- Bills Count
- Amount
- Taxable Amount
- TDS Amount

---

## 7. Area Wise Sale

### Purpose
Area-wise grouped sales report.

### Route
`admin.reports.sales.area-wise-sale`

### Filter Options
- Date Range (From/To)
- Area Selection

### Output Structure
Report is grouped by Area with:
- Area header row
- Individual transactions under each area
- Area-wise subtotals
- Grand total at bottom

### Output Columns
| Column | Description |
|--------|-------------|
| Area | Area name (group header) |
| Invoice No | Invoice number |
| Date | Transaction date |
| Customer | Customer name |
| Amount | Net amount |

---

## Common Features Across All Reports

### Export Options
- **Excel Export**: All reports support Excel export
- **Print**: Dedicated print layouts available

### Keyboard Shortcuts (Common)
| Key | Action |
|-----|--------|
| Alt+V | View report |
| Alt+P | Print report |
| Alt+C | Close/Back |
| Alt+E | Export to Excel |
| Escape | Close (Area Wise) |
| F7 | View (Area Wise) |

### UI Components
- Pink header card with report title
- Gray filter section
- Summary cards with color coding
- Responsive data table with sticky header
- Action buttons at bottom

### Data Table Features
- Sortable columns
- Sticky header on scroll
- Striped rows
- Hover effect
- Responsive design
- Maximum height with scroll

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- Customer Model
- Sale Model
- Salesman Model
- Area Model
- Route Model
- State Model
- User Model

### Session/Cache
Reports may use session for filter persistence across page loads.

### Performance Considerations
- Large date ranges may affect performance
- Use specific filters to narrow down results
- Excel export handles large datasets better than screen display
