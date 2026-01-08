# Customer Visit Status Report Documentation

## Overview
Customer Visit Status report ek CRM-focused report hai jo customers ki visit/purchase activity track karta hai. Ye report identify karta hai ki konse customers ne given period mein purchase kiya aur konse nahi kiya - jo sales team ke liye follow-up planning mein helpful hai.

---

## Report Details

### Purpose
- Customer visit/purchase frequency tracking
- Non-visiting customers identification
- Salesman-wise customer coverage analysis
- Area/Route-wise visit pattern analysis

### Route
`admin.reports.sales.customer-visit-status`

---

## Filter Options

### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Report period start date |
| To Date | Report period end date |

### Location/Assignment Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Sales Man | Dropdown | Filter by specific salesman |
| Area | Dropdown | Filter by customer area |
| Route | Dropdown | Filter by delivery route |

### Visit Filter (V/N/A)
| Code | Option | Description |
|------|--------|-------------|
| A | All | Show all customers |
| V | Visited | Only customers who made purchase |
| N | Not Visited | Only customers with no purchase |

### Group By (S/A/R)
| Code | Option | Description |
|------|--------|-------------|
| S | Salesman | Group results by salesman |
| A | Area | Group results by area |
| R | Route | Group results by route |
| All | All | No grouping, flat list |

---

## Output Columns

| Column | Description |
|--------|-------------|
| Code | Customer code |
| Party Name | Customer name (truncated to 35 chars) |
| Sales Man | Assigned salesman name |
| No. of Bills | Number of invoices in period |
| Amount | Total purchase amount |

### Row Highlighting
- **Yellow Row (table-warning)**: Customers with 0 visits/bills - needs attention

---

## Summary Footer

| Metric | Description |
|--------|-------------|
| Total Records | Number of customers in report |
| Total Visits | Sum of all bills |
| Total Amount | Sum of all purchase amounts |

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

### 1. Non-Visiting Customer Follow-up
- Set Visit Filter = "N" (Not Visited)
- Select date range (e.g., last 30 days)
- Get list of customers who haven't purchased
- Assign to sales team for follow-up

### 2. Salesman Performance Review
- Group By = "S" (Salesman)
- See customer coverage per salesman
- Identify salesmen with low visit counts

### 3. Area-wise Coverage Analysis
- Group By = "A" (Area)
- Identify areas with low customer engagement
- Plan marketing/sales campaigns

### 4. Route Optimization
- Group By = "R" (Route)
- Analyze route-wise customer activity
- Optimize delivery routes based on activity

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

### UI Components
- Pink header card with report title
- Gray filter section
- Responsive data table with sticky header
- Dark footer with totals
- Maximum table height: 55vh with scroll

### Performance
- Report generates on "View" button click
- Large customer base may take time
- Use filters to narrow down results
