# Sale Challan Reports Module Documentation

## Overview
Sale Challan Reports module delivery challans ki tracking aur management ke liye use hota hai. Ye module 2 main reports provide karta hai - Sale Challan Book (complete challan listing) aur Pending Challans (unbilled challans).

---

## Module Structure

```
sale-challan-reports/
├── sale-challan-book.blade.php        # Complete Challan Listing
├── sale-challan-book-print.blade.php  # Print version
├── pending-challans.blade.php         # Pending/Unbilled Challans
└── pending-challans-print.blade.php   # Print version
```

---

## 1. Sale Challan Book (Challan List)

### Purpose
Sabhi sale challans ka comprehensive listing with tagging functionality for batch processing.

### Route
`admin.reports.sales.sale-challan-book`

### Filter Options

#### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Start date for report period |
| To Date | End date for report period |

#### Party & Location Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Party | Dropdown | Customer selection |
| Salesman | Dropdown | Salesman filter |
| Route | Dropdown | Route-wise filter |
| Area | Dropdown | Area-wise filter |

#### Transaction Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Flag | All/Cash/Credit | Payment type filter |
| (D)/(S) | D/S | Detailed/Summarised format |
| Day | Monday-Sunday | Day of week filter |
| Order By | Date/Name/Challan No | Sorting option |
| Hold Only | Checkbox | Show only hold challans |

### Tagging Feature
Report mein unique tagging functionality hai jo multiple challans ko select karne ki facility deta hai:

#### How Tagging Works
1. **Tag Button (+)**: Green button - click to tag challan
2. **Untag Button (-)**: Red button - click to remove tag
3. **Tagged Row**: Yellow highlighted row indicates tagged challan
4. **Tagged Summary**: Real-time count and amount of tagged challans

#### Tagging Use Cases
- Batch billing of multiple challans
- Bulk export of selected challans
- Group printing of tagged challans

### Output Columns
| Column | Description |
|--------|-------------|
| TAG | Tag/Untag button (+/-) |
| DATE | Challan date |
| TRN.No | Challan/Transaction number |
| CODE | Customer code |
| PARTY NAME | Customer name |
| AMOUNT | Net challan amount |

### Summary Cards
| Card | Color | Description |
|------|-------|-------------|
| Total Challans | Blue | Total number of challans |
| Total Amount | Green | Sum of all challan amounts |
| Tagged | Yellow | Count of tagged challans |
| Tagged Amount | Cyan | Sum of tagged challan amounts |

### Action Buttons
| Button | Shortcut | Description |
|--------|----------|-------------|
| Excel | Alt+E | Export to Excel |
| View | Alt+V | Display report |
| Print | Alt+P / F7 | Print report |
| Close | Alt+C / Esc | Return to reports menu |

### JavaScript Functions
```javascript
toggleTag(id)        // Tag/Untag a challan
updateTaggedSummary() // Recalculate tagged totals
exportToExcel()      // Export functionality
printReport()        // Print functionality
```

---

## 2. Pending Challans (List of Pending Challans)

### Purpose
Un challans ki list jo abhi tak invoice mein convert nahi hue hain. Ye report billing team ke liye important hai.

### Route
`admin.reports.sales.pending-challans`

### Filter Options

#### Date Filters
| Filter | Description |
|--------|-------------|
| From Date | Start date for challan period |
| To Date | End date for challan period |

#### Party & Location Filters
| Filter | Options | Description |
|--------|---------|-------------|
| Party | Dropdown | Customer selection |
| Salesman | Dropdown | Salesman filter |
| Route | Dropdown | Route-wise filter |
| Area | Dropdown | Area-wise filter |
| Flag | All/Cash/Credit | Payment type filter |

### Output Columns
| Column | Description |
|--------|-------------|
| # | Serial number |
| Chaln.Date | Challan date |
| Chln.No | Challan number (clickable link) |
| Party Name | Customer name |
| Amount | Challan amount |
| Inv.Date | Invoice date (if converted) |
| Inv.No | Invoice number (if converted) |
| Status | Pending badge |

### Summary Cards
| Card | Color | Description |
|------|-------|-------------|
| Pending Challans | Yellow | Count of pending challans |
| Total Amount | Red | Total pending amount |

### Row Selection Feature
- **Single Click**: Select row (highlighted in blue)
- **Double Click**: Open challan for modification
- **Selected Row Actions**: Enable Modify and Bill Details buttons

### Additional Action Buttons
| Button | Shortcut | Description |
|--------|----------|-------------|
| Modify | Enter | Open selected challan for modification |
| Bill Details | F11 | Show challan details in modal |

### Bill Details Modal
Modal popup jo selected challan ki complete details show karta hai:
- Challan header information
- Item-wise details
- Customer information
- Amount breakup

### Keyboard Shortcuts
| Key | Action |
|-----|--------|
| Alt+V | View report |
| Alt+P | Print report |
| Alt+C | Close |
| Alt+E | Export to Excel |
| F7 | Print report |
| F11 | Show bill details |
| Enter | Modify selected challan |
| Escape | Close/Back |

### JavaScript Functions
```javascript
modifyChallan()      // Navigate to challan modification
showBillDetails()    // Open bill details modal
exportToExcel()      // Export functionality
printReport()        // Print functionality
```

---

## Common Features

### UI Components
- Pink header card with report title
- Gray filter section with compact inputs
- Summary cards with color coding
- Responsive data table with sticky header
- Action buttons at bottom

### Styling
```css
/* Common styles for both reports */
.input-group-text { font-size: 0.75rem; }
.form-control, .form-select { font-size: 0.8rem; }
.table th, .table td { font-size: 0.8rem; }
.sticky-top { position: sticky; top: 0; }
```

### Export Options
- **Excel Export**: Full data export with all columns
- **Print**: Optimized print layout

---

## Business Logic

### Challan Status Flow
```
Challan Created → Pending → Converted to Invoice → Completed
                    ↓
              Hold (Optional)
```

### Pending Challan Criteria
A challan is considered "pending" when:
1. No linked sale transaction exists
2. OR linked sale transaction is cancelled
3. AND challan is not cancelled

### Tagging Logic (Sale Challan Book)
```javascript
// Tagged IDs stored as comma-separated string
taggedIds = "1,5,12,23"

// Toggle adds/removes from array
if (exists) remove(id)
else add(id)
```

---

## Technical Notes

### Controller
`App\Http\Controllers\Admin\SalesReportController`

### Models Used
- SaleChallan
- Customer
- Salesman
- Area
- Route
- SaleTransaction (for pending check)

### Routes
```php
Route::get('sale-challan-book', [SalesReportController::class, 'saleChallanBook'])
    ->name('admin.reports.sales.sale-challan-book');

Route::get('pending-challans', [SalesReportController::class, 'pendingChallans'])
    ->name('admin.reports.sales.pending-challans');
```

### Query Optimization
- Use eager loading for customer relationship
- Index on challan_date for date range queries
- Index on customer_id for party filter

---

## Use Cases

### Sale Challan Book
1. **Daily Challan Review**: Check all challans created in a day
2. **Salesman Performance**: Filter by salesman to see delivery count
3. **Route Analysis**: Analyze deliveries by route
4. **Batch Processing**: Tag multiple challans for bulk operations

### Pending Challans
1. **Billing Queue**: Identify challans waiting for invoicing
2. **Follow-up**: Track old pending challans
3. **Quick Billing**: Double-click to modify and convert to invoice
4. **Audit**: Ensure no challan is left unbilled
