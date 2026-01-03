# Management Reports Module Guide

## Database Rules
- `SaleTransaction`: Use `sale_date`, `net_amount` (NOT `date`, `total_amount`)
- `SaleTransaction`: NO `is_deleted` column - don't add this condition
- `Route`: NO `is_deleted` column - use `Route::orderBy('name')->get()`
- `Customer`: Use `ledgers()` relationship (NOT `ledgerEntries`)

## Styling
- Header: Pink background `#ffc4d0`, italic font, Times New Roman
- Form: Gray background `#f0f0f0`, `border-radius: 0`
- Single char inputs: Add `text-uppercase` class

## View/Print Pattern
```php
// Controller
if ($request->has('view') || $request->has('print')) {
    $reportData = Model::query()...->get();
    if ($request->has('print')) {
        return view('...print', compact('reportData'));
    }
}

// Blade - View button
<button type="submit" name="view" value="1">View</button>

// Blade - Print function
function printReport() {
    window.open('{{ route(...) }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
```

## Data Sources
- Primary: `SaleTransaction` with `whereRaw('COALESCE(net_amount, 0) > COALESCE(paid_amount, 0)')`
- Fallback: `CustomerLedger` when SaleTransaction is empty
- Summary: `Customer::withSum('ledgers as balance', 'amount')`

## File Structure
```
due-reports/
├── report-name.blade.php      # Main form + results
└── report-name-print.blade.php # Print layout
```
