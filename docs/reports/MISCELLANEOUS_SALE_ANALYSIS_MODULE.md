# Miscellaneous Sale Analysis Module Documentation

## Overview
Miscellaneous Sale Analysis module Sales Reports section ka sabse comprehensive module hai jo multi-dimensional sales analysis provide karta hai. Is module mein 9 major categories aur 5+ standalone reports hain jo different perspectives se sales data analyze karte hain.

---

## Module Structure

```
miscellaneous-sale-analysis/
├── area-wise-sales/              # Area-based sales analysis (12 reports)
├── company-wise-sales/           # Company/Brand-based analysis (10 reports)
├── customer-wise-sale/           # Customer-based analysis (10 reports)
├── discount-wise-sales/          # Discount analysis (3 reports)
├── item-wise-sales/              # Item/Product-based analysis (11 reports)
├── route-wise-sale/              # Route-based analysis (12 reports)
├── salesman-wise-sales/          # Salesman performance analysis (13 reports)
├── scheme-issued/                # Scheme & Free goods analysis (5 reports)
├── state-wise-sale/              # State-based analysis (10 reports)
├── cancelled-invoices.blade.php  # Cancelled invoices list
├── missing-invoices.blade.php    # Missing invoice numbers
├── mrp-wise-sales.blade.php      # MRP-based sales analysis
├── salesman-level-sale.blade.php # Salesman level hierarchy sales
└── display-amount-report.blade.php # Display amount report
```

---

## Common Transaction Types (All Reports)

| Code | Transaction Type |
|------|-----------------|
| 1 | Sale |
| 2 | Sale Return |
| 3 | Debit Note |
| 4 | Credit Note |
| 5 | Consolidated Sale |

---

## Common Filter Options (Most Reports)

### Date Filters
- **From Date**: Report period start
- **To Date**: Report period end

### Location Filters
| Filter | Description |
|--------|-------------|
| Salesman | Filter by salesman |
| Area | Filter by area |
| Route | Filter by route |
| State | Filter by state |

### Sorting Options
| Option | Code | Description |
|--------|------|-------------|
| Order By Name | N | Sort alphabetically |
| Order By Value | V | Sort by amount |
| Ascending | A | Low to high |
| Descending | D | High to low |

### Additional Filters
| Filter | Options | Description |
|--------|---------|-------------|
| With Br./Expiry | Y/N | Include breakage/expiry transactions |
| Series | Code | Filter by invoice series |
| Tagged | Y/N | Show only tagged records |
| Remove Tags | Y/N | Clear existing tags |

---

## 1. Area Wise Sales (12 Reports)

### Purpose
Area-based sales analysis with multiple drill-down options.

### Sub-Reports

| Report | File | Description |
|--------|------|-------------|
| All Area | all-area.blade.php | Summary of all areas |
| Bill Wise | bill-wise.blade.php | Invoice-wise breakdown per area |
| Item Wise | item-wise.blade.php | Item-wise sales per area |
| Customer Wise | customer-wise.blade.php | Customer-wise sales per area |
| Company Wise | company-wise.blade.php | Company-wise sales per area |
| Route Wise | route-wise.blade.php | Route-wise sales per area |
| Salesman Wise | salesman-wise.blade.php | Salesman-wise sales per area |
| State Wise | state-wise.blade.php | State-wise sales per area |
| Sale Book | sale-book.blade.php | Detailed sale book per area |
| Invoice Item Wise | invoice-item-wise.blade.php | Invoice → Item hierarchy |
| Item Invoice Wise | item-invoice-wise.blade.php | Item → Invoice hierarchy |
| Month Wise | month-wise/ | Monthly trend analysis |

### All Area Report Filters
- Transaction Type (1-5)
- Date Range
- Tagged Areas [Y/N]
- Remove Tags [Y/N]
- Salesman, Area, Route, State filters
- Order By (Name/Value)
- Ascending/Descending
- With Br./Expiry [Y/N]
- Series filter

---

## 2. Company Wise Sales (10 Reports)

### Purpose
Company/Brand-wise sales analysis for manufacturer performance tracking.

### Sub-Reports

| Report | File | Description |
|--------|------|-------------|
| All Company | all-company.blade.php | Summary of all companies |
| Bill Wise | bill-wise.blade.php | Invoice-wise per company |
| Item Wise | item-wise.blade.php | Item-wise per company |
| Customer Wise | customer-wise.blade.php | Customer-wise per company |
| Customer Item Wise | customer-item-wise.blade.php | Customer → Item hierarchy |
| Customer Item Invoice Wise | customer-item-invoice-wise.blade.php | Full drill-down |
| Area Wise | area-wise.blade.php | Area-wise per company |
| Route Wise | route-wise.blade.php | Route-wise per company |
| Salesman Wise | salesman-wise.blade.php | Salesman-wise per company |
| Month Wise | month-wise/ | Monthly trend analysis |

### All Company Report Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Transaction Type | 1/2/3 | Sale/Return/Consolidated |
| Tagged Companies | Y/N | Show tagged only |
| S(elective)/A(ll) | S/A | Selective or all companies |
| With Tax | 1/2 | With Tax / Without Tax |
| Company | Dropdown | Specific company filter |
| Division | Dropdown | Division filter |
| Item Category | Dropdown | Category filter |
| Party Code | Dropdown | Customer filter |
| Sort By | Company/Amount | Sorting criteria |

---

## 3. Customer Wise Sale (10 Reports)

### Purpose
Customer-wise sales analysis for customer performance and behavior tracking.

### Sub-Reports

| Report | File | Description |
|--------|------|-------------|
| All Customer | all-customer.blade.php | Summary of all customers |
| Bill Wise | bill-wise.blade.php | Invoice-wise per customer |
| Item Wise | item-wise.blade.php | Item-wise per customer |
| Company Wise | company-wise.blade.php | Company-wise per customer |
| Invoice Item Wise | invoice-item-wise.blade.php | Invoice → Item hierarchy |
| Item Invoice Wise | item-invoice-wise.blade.php | Item → Invoice hierarchy |
| Sale With Area | sale-with-area.blade.php | Sales with area details |
| Quantity Wise Summary | quantity-wise-summary.blade.php | Quantity-based summary |
| Party Billwise Volume Discount | party-billwise-volume-discount.blade.php | Volume discount analysis |
| Month Wise | month-wise/ | Monthly trend analysis |

### All Customer Report Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Transaction Type | 1-5 | All transaction types |
| S(elective)/A(ll) | S/A | Selective or all |
| Tagged Customers | Y/N | Show tagged only |
| Remove Tags | Y/N | Clear tags |
| Customer | Dropdown | Specific customer |
| With Br./Expiry | Y/N | Include breakage/expiry |
| Flag | Text | Custom flag filter |

---

## 4. Item Wise Sales (11 Reports)

### Purpose
Product/Item-wise sales analysis for inventory and product performance.

### Sub-Reports

| Report | File | Description |
|--------|------|-------------|
| All Item Sale | all-item-sale.blade.php | Summary of all items |
| All Item Summary | all-item-summary.blade.php | Condensed item summary |
| Bill Wise | bill-wise.blade.php | Invoice-wise per item |
| Customer Wise | customer-wise.blade.php | Customer-wise per item |
| Area Wise | area-wise.blade.php | Area-wise per item |
| Area Wise Matrix | area-wise-matrix.blade.php | Item × Area matrix |
| Route Wise | route-wise.blade.php | Route-wise per item |
| Salesman Wise | salesman-wise.blade.php | Salesman-wise per item |
| State Wise | state-wise.blade.php | State-wise per item |
| Below Cost Item Sale | below-cost-item-sale.blade.php | Items sold below cost |

### All Item Sale Report Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Transaction Type | 1-4 | Sale/Return/Both/Challan |
| Series | Code | Invoice series |
| Bill No Range | From-To | Bill number range |
| Tagged Companies | Y/N | Company tagging |
| Tagged Categories | Y/N | Category tagging |
| Company | Dropdown | Company filter |
| Division | Code | Division filter |
| Item | Dropdown | Specific item |
| Category | Dropdown | Category filter |
| Commodity | Code | Commodity filter |
| Range | Y/N | Enable value range |
| Add GST | Checkbox | Include GST in value |
| Value Range | From-To | Amount range filter |
| Order By | Q/V/N | Quantity/Value/Name |
| Top Items | Number | Limit to top N items |
| Batch Wise | Checkbox | Group by batch |
| With Return Det. | Y/N | Include return details |
| DPC Item | Y/N | DPC items only |
| Item Type | Text | Item type filter |
| Item Status | Text | Item status filter |

### Action Buttons
- Tax Wise
- Excel Export
- View
- Close

---

## 5. Route Wise Sale (12 Reports)

### Purpose
Route-based sales analysis for delivery and distribution optimization.

### Sub-Reports

| Report | File | Description |
|--------|------|-------------|
| All Route | all-route.blade.php | Summary of all routes |
| Bill Wise | bill-wise.blade.php | Invoice-wise per route |
| Item Wise | item-wise.blade.php | Item-wise per route |
| Customer Wise | customer-wise.blade.php | Customer-wise per route |
| Company Wise | company-wise.blade.php | Company-wise per route |
| Area Wise | area-wise.blade.php | Area-wise per route |
| Salesman Wise | salesman-wise.blade.php | Salesman-wise per route |
| State Wise | state-wise.blade.php | State-wise per route |
| Sale Book | sale-book.blade.php | Detailed sale book per route |
| Invoice Item Wise | invoice-item-wise.blade.php | Invoice → Item hierarchy |
| Item Invoice Wise | item-invoice-wise.blade.php | Item → Invoice hierarchy |
| Month Wise | month-wise/ | Monthly trend analysis |

---

## 6. Salesman Wise Sales (13 Reports)

### Purpose
Salesman performance analysis and target tracking.

### Sub-Reports

| Report | File | Description |
|--------|------|-------------|
| All Salesman | all-salesman.blade.php | Summary of all salesmen |
| Bill Wise | bill-wise.blade.php | Invoice-wise per salesman |
| Item Wise | item-wise.blade.php | Item-wise per salesman |
| Customer Wise | customer-wise.blade.php | Customer-wise per salesman |
| Company Wise | company-wise.blade.php | Company-wise per salesman |
| Area Wise | area-wise.blade.php | Area-wise per salesman |
| Route Wise | route-wise.blade.php | Route-wise per salesman |
| State Wise | state-wise.blade.php | State-wise per salesman |
| Sale Book | sale-book.blade.php | Detailed sale book per salesman |
| Invoice Item Wise | invoice-item-wise.blade.php | Invoice → Item hierarchy |
| Item Invoice Wise | item-invoice-wise.blade.php | Item → Invoice hierarchy |
| Monthly Target | monthly-target.blade.php | Target vs Achievement |
| Month Wise | month-wise/ | Monthly trend analysis |

### All Salesman Report Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Transaction Type | 1-5 | All transaction types |
| Tagged Salesman | Y/N | Show tagged only |
| Salesman | Dropdown | Specific salesman |
| Area | Dropdown | Area filter |
| Route | Dropdown | Route filter |
| State | Dropdown | State filter |
| Order By | N/V | Name/Value |
| Asc/Desc | A/D | Sort direction |
| With Br./Expiry | Y/N | Include breakage/expiry |
| Series | Dropdown | Invoice series |

---

## 7. State Wise Sale (10 Reports)

### Purpose
State-wise sales analysis for regional performance and GST compliance.

### Sub-Reports

| Report | File | Description |
|--------|------|-------------|
| All State | all-state.blade.php | Summary of all states |
| Bill Wise | bill-wise.blade.php | Invoice-wise per state |
| Item Wise | item-wise.blade.php | Item-wise per state |
| Customer Wise | customer-wise.blade.php | Customer-wise per state |
| Company Wise | company-wise.blade.php | Company-wise per state |
| Area Wise | area-wise.blade.php | Area-wise per state |
| Route Wise | route-wise.blade.php | Route-wise per state |
| Salesman Wise | salesman-wise.blade.php | Salesman-wise per state |
| Invoice Item Wise | invoice-item-wise.blade.php | Invoice → Item hierarchy |
| Month Wise | month-wise/ | Monthly trend analysis |

---

## 8. Discount Wise Sales (3 Reports)

### Purpose
Discount analysis for margin and profitability tracking.

### Sub-Reports

| Report | File | Description |
|--------|------|-------------|
| All Discount | all-discount.blade.php | Summary of all discounts |
| Item Wise | item-wise.blade.php | Item-wise discount analysis |
| Item Wise Invoice Wise | item-wise-invoice-wise.blade.php | Detailed drill-down |

### All Discount Report Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Selective Discount | Y/N | Filter by specific discount |
| Discount % | Number | Discount percentage |
| Comparison | 1/2/3 | >= / <= / = |
| DPC Item | Y/N | DPC items only |
| Company | Dropdown | Company filter |
| Item | Dropdown | Item filter |
| Customer | Dropdown | Customer filter |

---

## 9. Scheme Issued (5 Reports)

### Purpose
Free goods and scheme tracking for promotional analysis.

### Sub-Reports

| Report | File | Description |
|--------|------|-------------|
| Free Scheme Issued | free-scheme-issued.blade.php | Free goods issued |
| Half Scheme Issued | half-scheme-issued.blade.php | Half-rate scheme items |
| Invalid Free Scheme | invalid-free-scheme-issued.blade.php | Invalid/incorrect schemes |
| Free Issues Without Qty | free-issues-without-qty.blade.php | Free without purchase qty |
| Item Wise Less | item-wise-less.blade.php | Item-wise scheme deductions |

### Free Scheme Issued Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Transaction Type | 1-5 | All transaction types |
| Company | Dropdown | Company filter |
| Item | Dropdown | Item filter |
| Salesman | Dropdown | Salesman filter |
| Customer | Dropdown | Customer filter |
| Order By | C/I | Company/Item |
| With Batch | Y/N | Include batch details |

---

## 10. Standalone Reports

### 10.1 Cancelled Invoices

**Purpose**: List of cancelled/voided invoices for audit trail.

**Filters**:
- Date Range (From/To)
- Salesman
- Customer

**Output Columns**:
| Column | Description |
|--------|-------------|
| Date | Cancellation date |
| Invoice No | Original invoice number |
| Series | Invoice series |
| Customer | Customer name |
| Amount | Invoice amount |
| Cancelled By | User who cancelled |
| Reason | Cancellation reason |

---

### 10.2 Missing Invoices

**Purpose**: Identify gaps in invoice number sequence for compliance.

**Filters**:
- Date Range (From/To)
- Salesman
- Customer

**Output Columns**:
| Column | Description |
|--------|-------------|
| Series | Invoice series |
| Missing No | Missing invoice number |
| Date Range | Expected date range |

---

### 10.3 MRP Wise Sales

**Purpose**: Sales analysis grouped by MRP for pricing analysis.

**Filters**:
- Date Range
- Company
- Item
- MRP Range

---

### 10.4 Salesman Level Sale

**Purpose**: Hierarchical salesman sales with levels/tiers.

**Filters**:
- Date Range
- Salesman Level
- Area
- Route

---

### 10.5 Display Amount Report

**Purpose**: Display/visibility amount tracking for merchandising.

---

## Common Features Across All Reports

### Export Options
- **Excel Export**: All reports support Excel export
- **Print**: Dedicated print layouts (*-print.blade.php)

### Keyboard Shortcuts
| Key | Action |
|-----|--------|
| F7 | View report |
| Escape | Close/Back |
| Alt+V | View |
| Alt+E | Excel export |

### UI Components
- Pink/Blue gradient header with report title
- Gray/Purple filter section background
- Action buttons at bottom
- Responsive form layout

### Tagging Feature
Most reports support tagging for:
- Selective filtering
- Batch operations
- Custom grouping

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
- Company Model
- Item Model
- Division Model
- Category Model

### Performance Considerations
- Large date ranges may affect performance
- Use specific filters to narrow down results
- Excel export handles large datasets better
- Month-wise reports are pre-aggregated for speed

### Report Hierarchy Pattern
Most categories follow this drill-down pattern:
1. **All [Entity]** - Summary level
2. **Bill Wise** - Invoice level
3. **Item Wise** - Product level
4. **Customer/Company/Area Wise** - Cross-dimensional
5. **Invoice Item Wise** - Full detail
6. **Month Wise** - Trend analysis
