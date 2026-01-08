# Other Reports Module Documentation

## Overview
Other Reports module mein 26 specialized reports hain jo different aspects of sales data ko cover karte hain - discounts, schemes, stock, tax analysis, customer analysis, aur regulatory compliance.

---

## Module Structure

```
other-reports/
├── cash-coll-trnf-sale.blade.php       # Cash Collection Transfer
├── customer-consistency.blade.php       # Customer Consistency Report
├── customer-stock-details.blade.php     # Customer Stock Details
├── frige-item.blade.php                 # Frige Item Report
├── gst-sale-book.blade.php              # GST Sale Book
├── invoice-documents.blade.php          # Invoice Documents (E-Way, IRN)
├── item-wise-discount.blade.php         # Item Wise Discount
├── item-wise-scheme.blade.php           # Item Wise Scheme
├── minus-qty-sale.blade.php             # Minus Qty in Sale Invoice
├── party-volume-discount.blade.php      # Party Volume Discount
├── pending-orders.blade.php             # Customer/Supplier Pending Orders
├── rate-difference.blade.php            # Rate Change Report
├── sale-bill-wise-discount.blade.php    # Discount On Sale - Bill Wise
├── sale-book-sc.blade.php               # Sale Book SC
├── sale-book-summarised.blade.php       # Sale Book Summarised
├── sale-remarks.blade.php               # Sale Remarks Report
├── sale-return-adjustment.blade.php     # Sale Return Adjustment
├── sale-stock-detail.blade.php          # Sale/Stock Detail
├── sales-book-with-return.blade.php     # Sales Book With Return
├── sales-details.blade.php              # Sales Details
├── sales-matrix.blade.php               # Sales Matrix
├── schedule-h1-drugs.blade.php          # Register of Schedule H1 Drugs
├── st38-outword.blade.php               # List Of ST-38 OutWord
├── tax-percentage-wise-sale.blade.php   # Tax Percentage Wise Sale
├── transaction-book-address.blade.php   # Transaction Book Address
└── volume-discount.blade.php            # Volume Discount
```

---

## Report Categories

### 1. Cash & Collection Reports

#### Cash Collection Transfer
**Route:** `admin.reports.sales.other.cash-coll-trnf-sale`

**Purpose:** Cash collection aur transfer tracking for sales transactions.

**Filters:**
- Date Range (From/To)

**Output Columns:**
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Bill No | Invoice number |
| Code | Customer code |
| Party Name | Customer name |
| Salesman | Salesman name |
| Net Amount | Bill amount |
| Paid Amount | Amount received |
| Balance | Outstanding amount |

**Summary Cards:** Total Bills, Net Amount, Paid Amount, Balance

**Additional Feature:** Daily Summary table showing day-wise collection breakdown.

---

### 2. Customer Analysis Reports

#### Customer Consistency Report
**Route:** `admin.reports.sales.other.customer-consistency`

**Purpose:** Two periods ke beech customer buying pattern comparison.

**Filters:**
| Filter | Description |
|--------|-------------|
| Period 1 From/To | First comparison period |
| Period 2 From/To | Second comparison period |
| Report Type | 1-Consistent / 2-Others / 3-All |
| Items | Item filter |

**Output Columns:**
| Column | Description |
|--------|-------------|
| Code | Customer code |
| Customer Name | Customer name |
| Area | Customer area |
| P1 Bills | Period 1 bill count |
| P1 Value | Period 1 total value |
| P2 Bills | Period 2 bill count |
| P2 Value | Period 2 total value |
| Status | Consistent/Others badge |

**Business Logic:** Customer is "Consistent" if they have bills in both periods.

---

#### Customer Stock Details
**Route:** `admin.reports.sales.other.customer-stock-details`

**Purpose:** Customer-wise stock sold details.

**Filters:**
- As On Date
- Customer Selection

**Output Columns:** Customer Code, Customer Name, Item, Qty Sold, Value, Last Sale Date

---

### 3. Discount & Scheme Reports

#### Item Wise Discount
**Route:** `admin.reports.sales.other.item-wise-discount`

**Purpose:** Item-wise ya Company-wise discount analysis.

**Filters:**
| Filter | Options | Description |
|--------|---------|-------------|
| Date Range | From/To | Report period |
| I/C | Item/Company | Report grouping |
| Sel.Co | Y/N | Selective company |
| Item | Y/N | Item wise detail |
| Series | Dropdown | Invoice series |
| Company | Dropdown | Company filter |
| Tag.Cat | Y/N | Tagged categories |
| Rem.Tag | Y/N | Remove tags |
| Category | Dropdown | Item category |
| Salesman | Dropdown | Salesman filter |
| Area | Dropdown | Area filter |
| Route | Dropdown | Route filter |
| Customer | Dropdown | Customer filter |
| Day | Dropdown | Day of week |

**Output Columns (Item Wise):**
| Column | Description |
|--------|-------------|
| Item Code | Item code |
| Item Name | Item name |
| Company | Company name |
| Qty | Quantity sold |
| Gross Amt | Gross amount |
| Disc % | Discount percentage |
| Disc Amt | Discount amount |
| Net Amt | Net amount |

---

#### Item Wise Scheme
**Route:** `admin.reports.sales.other.item-wise-scheme`

**Purpose:** Item-wise scheme/free quantity analysis.

**Filters:** Date Range, Company

**Output Columns:**
| Column | Description |
|--------|-------------|
| Item Code | Item code |
| Item Name | Item name |
| Company | Company name |
| Sale Qty | Sold quantity |
| Free Qty | Free quantity given |
| Scheme % | Scheme percentage |
| Scheme Amt | Scheme amount |

---

#### Sale Bill Wise Discount
**Route:** `admin.reports.sales.other.sale-bill-wise-discount`

**Purpose:** Bill-wise discount analysis.

**Filters:**
| Filter | Options | Description |
|--------|---------|-------------|
| Date Range | From/To | Report period |
| Discount Option | 1/2/3 | With Disc/Without Disc/All |
| Salesman | Dropdown | Salesman filter |
| Area | Dropdown | Area filter |
| Route | Dropdown | Route filter |
| State | Dropdown | State filter |
| Party | Dropdown | Customer filter |
| Series | Text | Invoice series |

**Output Columns:**
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Bill No | Invoice number |
| Code | Customer code |
| Party Name | Customer name |
| Area | Area name |
| Salesman | Salesman name |
| Gross Amt | Gross amount |
| Discount | Discount amount |
| Dis% | Discount percentage |
| Scheme | Scheme amount |
| Tax | Tax amount |
| Net Amt | Net amount |

---

#### Party Volume Discount
**Route:** `admin.reports.sales.other.party-volume-discount`

**Purpose:** Party-wise volume discount summary.

**Filters:** Date Range, Party

**Output Columns:** Party Code, Party Name, Total Sale, Volume Disc, Net Amount

---

#### Volume Discount
**Route:** `admin.reports.sales.other.volume-discount`

**Purpose:** Volume-based discount analysis by party and company.

**Filters:** Date Range, Party Name, Company, Volume Only checkbox

**Output Columns:** Party, Company, Sale Qty, Sale Value, Discount, Net Value

---

### 4. Tax & GST Reports

#### GST Sale Book
**Route:** `admin.reports.sales.other.gst-sale-book`

**Purpose:** GST-compliant sale book with tax breakup.

**Filters:**
| Filter | Options | Description |
|--------|---------|-------------|
| Date Range | From/To | Report period |
| Type | 1/2/3 | Cash/Credit/Both |
| GST Detail | Y/N | Show GST details |

**Output Columns:**
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Invoice | Invoice number |
| Party | Customer name |
| GSTN | GST number |
| Taxable | Taxable amount |
| CGST | Central GST |
| SGST | State GST |
| IGST | Integrated GST |
| Total | Total amount |

---

#### Tax Percentage Wise Sale
**Route:** `admin.reports.sales.other.tax-percentage-wise-sale`

**Purpose:** GST percentage-wise sale summary.

**Filters:** Date Range

**Output Columns:**
| Column | Description |
|--------|-------------|
| GST % | Tax percentage |
| Taxable Value | Taxable amount |
| CGST | Central GST |
| SGST | State GST |
| IGST | Integrated GST |
| Total Tax | Total tax amount |
| Invoice Value | Total invoice value |

---

### 5. Rate & Price Reports

#### Rate Change Report
**Route:** `admin.reports.sales.other.rate-difference`

**Purpose:** Purchase vs Sale rate difference analysis.

**Filters:**
| Filter | Options | Description |
|--------|---------|-------------|
| Date Range | From/To | Report period |
| Item | Dropdown | Item filter |
| Company | Dropdown | Company filter |
| Party | Dropdown | Customer filter |
| Rate Type | P/S/R/C | Purchase/Sale/Rate Diff/Cost |
| Group By | I/B/P | Item/Bill/Party Wise |
| With VAT | Checkbox | Include VAT |
| With SC | Checkbox | Include SC |

**Output Columns (Item Wise):**
| Column | Description |
|--------|-------------|
| Item Name | Item name |
| Company | Company name |
| Qty | Quantity |
| Pur. Rate | Purchase rate |
| Sale Rate | Sale rate |
| Rate Diff | Rate difference |
| Diff Amt | Difference amount |
| Total Amt | Total amount |

**Color Coding:** Green for positive difference, Red for negative.

---

### 6. Stock & Inventory Reports

#### Sale/Stock Detail
**Route:** `admin.reports.sales.other.sale-stock-detail`

**Purpose:** Stock vs Sale comparison by item.

**Filters:** Date Range, Company

**Output Columns:**
| Column | Description |
|--------|-------------|
| Item Code | Item code |
| Item Name | Item name |
| Company | Company name |
| Stock | Available stock |
| Sold | Quantity sold |
| Balance | Remaining stock |
| Sale Value | Total sale value |

---

#### Frige Item Report
**Route:** `admin.reports.sales.other.frige-item`

**Purpose:** Fridge/Cold storage items tracking.

**Filters:** Date, Bill No Range, Category, Status, Salesman, Area, Route

**Output Columns:** Invoice, Date, Customer, Item, Qty, Amount

---

### 7. Order & Pending Reports

#### Pending Orders
**Route:** `admin.reports.sales.other.pending-orders`

**Purpose:** Customer/Supplier pending orders tracking.

**Filters:**
| Filter | Options | Description |
|--------|---------|-------------|
| C/S | Customer/Supplier | Order type |
| Salesman | Dropdown | Salesman filter |
| Area | Dropdown | Area filter |
| Route | Dropdown | Route filter |
| State | Dropdown | State filter |

**Output Columns:**
| Column | Description |
|--------|-------------|
| Order Date | Order date |
| Order No | Order number |
| Party | Party name |
| Item | Item name |
| Ordered | Ordered quantity |
| Delivered | Delivered quantity |
| Pending | Pending quantity |
| Status | Complete/Pending badge |

---

### 8. Return & Adjustment Reports

#### Sale Return Adjustment
**Route:** `admin.reports.sales.other.sale-return-adjustment`

**Purpose:** Sale return adjustment tracking.

**Filters:** Date Range

**Output Columns:** SL.NO., Date, Trn.No, PartyName, Amount, Adj.Bill, Bal.Amt

---

#### Sales Book With Return
**Route:** `admin.reports.sales.other.sales-book-with-return`

**Purpose:** Combined sales and returns in single report.

**Filters:** Date Range, Customer

**Output Columns:**
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Type | Sale/Return badge |
| Doc No | Document number |
| Code | Customer code |
| Party Name | Customer name |
| Area | Area name |
| Gross Amt | Gross amount |
| Discount | Discount amount |
| Tax | Tax amount |
| Net Amount | Net amount |

**Summary:** Separate totals for Sales, Returns, and Net.

---

### 9. Document & Compliance Reports

#### Invoice Documents
**Route:** `admin.reports.sales.other.invoice-documents`

**Purpose:** E-Way Bill aur IRN tracking.

**Filters:** Advice Date Range, Fin.Year, Series, Bill No Range

**Output Columns:**
| Column | Description |
|--------|-------------|
| Sr.No | Serial number |
| Date | Invoice date |
| Invoice No | Invoice number |
| Code | Party code |
| Party Name | Party name |
| GST No | GST number |
| Amount | Invoice amount |
| E-Way Bill | E-Way bill number |
| IRN No | IRN number |
| Status | Generated/Pending badge |

**Additional Actions:** Print Bank Advice, Print Form

---

#### Schedule H1 Drugs
**Route:** `admin.reports.sales.other.schedule-h1-drugs`

**Purpose:** Regulatory compliance report for Schedule H1 drugs.

**Filters:** Date Range

**Output Columns:**
| Column | Description |
|--------|-------------|
| Date | Sale date |
| Bill No | Invoice number |
| Drug Name | Drug name |
| Batch | Batch number |
| Qty | Quantity sold |
| Patient Name | Patient name |
| Dr. Name | Doctor name |
| Address | Patient address |

---

#### ST-38 OutWord
**Route:** `admin.reports.sales.other.st38-outword`

**Purpose:** ST-38 form tracking for inter-state transfers.

**Filters:** Date Range

**Output Columns:** Date, ST-38 No, Party, State, Taxable, Tax, Total

---

### 10. Detailed & Summary Reports

#### Sales Details
**Route:** `admin.reports.sales.other.sales-details`

**Purpose:** Item-level sales details.

**Filters:** Date Range, Customer, Company, Include Cancelled checkbox

**Output Columns:**
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Bill No | Invoice number |
| Party Name | Customer name |
| Item Name | Item name |
| Company | Company name |
| Qty | Quantity |
| Free | Free quantity |
| Rate | Sale rate |
| Amount | Total amount |

---

#### Sale Book Summarised
**Route:** `admin.reports.sales.other.sale-book-summarised`

**Purpose:** Customer-wise summarised sale book.

**Filters:** Date Range, Selective Y/N, Customer

**Output Columns:** Customer Code, Customer Name, Bills, Gross Amt, Discount, Tax, Net Amount

---

#### Sale Book SC
**Route:** `admin.reports.sales.other.sale-book-sc`

**Purpose:** Sale book with Service Charge details.

**Filters:** Date Range, Series

**Output Columns:** Date, Bill No, Party Name, Gross, Disc, SC, Tax, Net

---

#### Sale Remarks Report
**Route:** `admin.reports.sales.other.sale-remarks`

**Purpose:** Sales with remarks tracking.

**Filters:**
| Filter | Options | Description |
|--------|---------|-------------|
| Date Range | From/To | Report period |
| P/N/A | Pending/Non-Pending/All | Status filter |
| Series | Dropdown | Invoice series |
| Stock | 1/2/3 | With/Without/All Stock |

**Output Columns:**
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Bill No | Invoice number |
| Code | Party code |
| Party Name | Party name |
| Salesman | Salesman name |
| Remarks | Sale remarks |
| Amount | Bill amount |
| Balance | Outstanding balance |
| Status | Pending/Paid badge |

---

### 11. Matrix & Analysis Reports

#### Sales Matrix
**Route:** `admin.reports.sales.other.sales-matrix`

**Purpose:** Cross-tabulation report - Party vs Items or Items vs Party.

**Filters:**
| Filter | Options | Description |
|--------|---------|-------------|
| Date Range | From/To | Report period |
| Company | Dropdown (Required) | Company filter |
| Division | Text | Division code |
| Status | Text | Status filter |
| Show For | Party/Area/Salesman/Route | Row grouping |
| Salesman | Dropdown | Salesman filter |
| Area | Dropdown | Area filter |
| Route | Dropdown | Route filter |
| Value On | NetSale/Sale/WS/Spl/Cost | Rate type |
| Print Sales Return | Checkbox | Include returns |
| Add Free Qty | Y/N | Include free qty |
| Matrix Type | 1/2 | X-Y axis swap |

**Matrix Type Options:**
- 1: X→Party, Y→Item
- 2: X→Item, Y→Party

**Output:** Dynamic matrix table with row/column totals and grand total.

---

#### Minus Qty in Sale Invoice
**Route:** `admin.reports.sales.other.minus-qty-sale`

**Purpose:** Negative quantity items in invoices (audit report).

**Filters:** Date Range, Include Cancelled checkbox

**Output Columns:**
| Column | Description |
|--------|-------------|
| Date | Transaction date |
| Bill No | Invoice number |
| Party Name | Customer name |
| Item Code | Item code |
| Item Name | Item name |
| Qty | Quantity (negative) |
| Rate | Sale rate |
| Amount | Amount (negative) |

**Color Coding:** Red for negative values.

---

## Common Features

### Export Options
All reports support:
- **Excel Export**: Full data export
- **Print**: Optimized print layout
- **View**: Screen display

### Keyboard Shortcuts
| Key | Action |
|-----|--------|
| Alt+V / V | View report |
| Alt+E / E | Export to Excel |
| Alt+P | Print |
| Alt+C | Close |
| Escape | Close/Back |
| F7 | Print (some reports) |

### UI Components
- Pink gradient header with report title
- Gray filter section
- Summary cards (where applicable)
- Responsive data table with sticky header
- Action buttons at bottom

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Route Prefix
`admin.reports.sales.other.*`

### Common Dependencies
- Customer Model
- Sale Model
- SaleItem Model
- Company Model
- Salesman Model
- Area Model
- Route Model
- State Model
- Item Model

### Performance Tips
- Use specific date ranges
- Apply filters to narrow results
- Excel export handles large datasets better
- Matrix report requires Company selection
