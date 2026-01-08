# Local Central Sale Register Report Documentation

## Overview
Local Central Sale Register ek GST-focused report hai jo sales ko Local (Intra-state) aur Central (Inter-state) categories mein segregate karta hai. Ye report GST filing, CGST/SGST/IGST reconciliation aur tax compliance ke liye essential hai.

---

## Report Details

### Purpose
- Local vs Central sales segregation
- GST tax breakup (CGST, SGST, IGST)
- GST return filing support (GSTR-1)
- Intra-state vs Inter-state sales analysis
- Tax compliance and audit

### Route
`admin.reports.sales.local-central-sale-register`

---

## Filter Options

### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Report period start date |
| To Date | Report period end date |

### Transaction Type
| Code | Type | Description |
|------|------|-------------|
| 1 | Sale | Only sales |
| 2 | Return | Only returns |
| 3 | DN | Debit Notes |
| 4 | CN | Credit Notes |
| 5 | All | All transactions (default) |

### Location Filter (L/C/B)
| Code | Option | Description |
|------|--------|-------------|
| L | Local | Intra-state sales only (CGST+SGST) |
| C | Central | Inter-state sales only (IGST) |
| B | Both | All sales (default) |

### Other Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Series | Dropdown | Invoice series filter |
| Cancelled | Y/N | Include cancelled bills (default: N) |
| Party | Dropdown | Specific customer filter |
| T/R | Tax/Retail/All | Tax invoice or Retail invoice |

---

## Summary Cards

Report ke top par 3 summary cards display hote hain:

| Card | Color | Description |
|------|-------|-------------|
| LOCAL | Green (Success) | Local sales count & amount |
| CENTRAL | Cyan (Info) | Central sales count & amount |
| TOTAL | Blue (Primary) | Combined total |

---

## Output Columns

| Column | Description |
|--------|-------------|
| # | Serial number |
| Date | Transaction date |
| Bill No | Invoice number (clickable link) |
| Code | Customer code |
| Party Name | Customer name |
| L/C | Local/Central badge |
| GSTN | Customer GST number |
| NT Amount | Net taxable amount |
| CGST | Central GST (Local sales only) |
| SGST | State GST (Local sales only) |
| IGST | Integrated GST (Central sales only) |
| Net Amount | Final invoice amount |

---

## Report Structure

### Local/Central Grouping
```
┌─────────────────────────────────────────────────────────────┐
│ LOCAL SALES (XX Bills)                                      │  ← Green header
├─────────────────────────────────────────────────────────────┤
│ Bill 1... NT Amt   CGST    SGST    -       Net Amt         │
│ Bill 2... NT Amt   CGST    SGST    -       Net Amt         │
├─────────────────────────────────────────────────────────────┤
│ Local Total:       XXX     XXX     XXX     -       XXX     │  ← Gray subtotal
├─────────────────────────────────────────────────────────────┤
│ CENTRAL SALES (XX Bills)                                    │  ← Cyan header
├─────────────────────────────────────────────────────────────┤
│ Bill 1... NT Amt   -       -       IGST    Net Amt         │
│ Bill 2... NT Amt   -       -       IGST    Net Amt         │
├─────────────────────────────────────────────────────────────┤
│ Central Total:     XXX     -       -       XXX     XXX     │  ← Gray subtotal
├─────────────────────────────────────────────────────────────┤
│ Grand Total:       XXX     XXX     XXX     XXX     XXX     │  ← Dark footer
└─────────────────────────────────────────────────────────────┘
```

### GST Tax Logic
| Sale Type | CGST | SGST | IGST |
|-----------|------|------|------|
| Local (L) | ✓ | ✓ | - |
| Central (C) | - | - | ✓ |

### Row Types
| Row Type | Color | Description |
|----------|-------|-------------|
| Local Header | Green (table-success) | Local sales section header |
| Central Header | Cyan (table-info) | Central sales section header |
| Data Row | Striped | Individual transaction |
| Subtotal | Gray (table-secondary) | Section-wise totals |
| Grand Total | Dark (table-dark) | Overall totals |

### L/C Badge
| Badge | Color | Meaning |
|-------|-------|---------|
| L | Green | Local/Intra-state sale |
| C | Cyan | Central/Inter-state sale |

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

### 1. GSTR-1 Filing
- Generate report for filing period
- Get Local vs Central breakup
- Verify CGST/SGST/IGST amounts
- Cross-check with GST portal

### 2. Monthly Tax Reconciliation
- Compare Local and Central totals
- Verify tax calculations
- Identify discrepancies

### 3. State-wise Sales Analysis
- L/C = "L" for local sales only
- L/C = "C" for inter-state sales
- Analyze geographic sales distribution

### 4. Tax Invoice Verification
- T/R = "T" for tax invoices only
- Verify GST compliance
- Check GSTN presence

### 5. Audit Preparation
- Generate complete report (Type = 5)
- Include all transaction types
- Export to Excel for auditor

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Dependencies
- SaleTransaction Model
- Customer Model
- State Model (for L/C determination)

### GST Determination Logic
- **Local Sale**: Customer state = Business state → CGST + SGST
- **Central Sale**: Customer state ≠ Business state → IGST

### UI Components
- Pink header card with report title
- Gray filter section with 2-row layout
- 3 color-coded summary cards
- Responsive data table with sticky header
- Local/Central grouped sections
- Maximum table height: 50vh with scroll

### Performance
- Report generates on "View" button click
- Large date ranges may include many transactions
- Use filters to narrow results
