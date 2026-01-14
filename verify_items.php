<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Item;

echo "=== ITEM SEEDING VERIFICATION ===" . PHP_EOL;
echo PHP_EOL;

// Total counts
$totalItems = Item::count();
$demo2026Items = Item::where('organization_id', 20)->count();
$otherOrgItems = Item::where('organization_id', '!=', 20)->count();

echo "Total items in database: $totalItems" . PHP_EOL;
echo "DEMO2026 items: $demo2026Items" . PHP_EOL;
echo "Other organization items: $otherOrgItems" . PHP_EOL;
echo PHP_EOL;

// HSN code verification
$itemsWithHsn = Item::where('organization_id', 20)
    ->whereNotNull('hsn_code')
    ->where('hsn_code', '!=', '')
    ->count();

$itemsWithoutHsn = Item::where('organization_id', 20)
    ->where(function($q) {
        $q->whereNull('hsn_code')->orWhere('hsn_code', '');
    })
    ->count();

echo "Items with HSN code: $itemsWithHsn" . PHP_EOL;
echo "Items without HSN code: $itemsWithoutHsn" . PHP_EOL;
echo PHP_EOL;

// Sample items with HSN codes
echo "=== SAMPLE ITEMS WITH HSN CODES ===" . PHP_EOL;
$sampleItems = Item::where('organization_id', 20)
    ->whereNotNull('hsn_code')
    ->where('hsn_code', '!=', '')
    ->with('company')
    ->take(10)
    ->get();

foreach ($sampleItems as $item) {
    echo "Item: {$item->name}" . PHP_EOL;
    echo "  Company: {$item->company->name}" . PHP_EOL;
    echo "  HSN Code: {$item->hsn_code}" . PHP_EOL;
    echo "  CGST: {$item->cgst_percent}% | SGST: {$item->sgst_percent}% | IGST: {$item->igst_percent}%" . PHP_EOL;
    echo "  MRP: {$item->mrp} | Purchase Rate: {$item->pur_rate} | Sale Rate: {$item->s_rate}" . PHP_EOL;
    echo PHP_EOL;
}

// Verify against CSV
echo "=== CSV VERIFICATION ===" . PHP_EOL;
$csvFile = 'item.xlsx - item.csv';
$lines = file($csvFile, FILE_IGNORE_NEW_LINES);
$header = str_getcsv($lines[0]);

echo "Total lines in CSV: " . count($lines) . PHP_EOL;
echo "Data rows in CSV: " . (count($lines) - 1) . PHP_EOL;
echo PHP_EOL;

// Check first item from CSV
$data = str_getcsv($lines[1]);
$row = array_combine($header, $data);

echo "First item in CSV:" . PHP_EOL;
echo "  Name: {$row['name']}" . PHP_EOL;
echo "  Company: {$row['Compname']}" . PHP_EOL;
echo "  HSN Code: " . ($row['HSNCode'] ?? 'N/A') . PHP_EOL;
echo "  CGST: " . ($row['CGST'] ?? '0') . "% | SGST: " . ($row['SGST'] ?? '0') . "%" . PHP_EOL;
echo PHP_EOL;

// Find in database
$dbItem = Item::where('organization_id', 20)
    ->where('name', trim($row['name']))
    ->with('company')
    ->first();

if ($dbItem) {
    echo "Found in database:" . PHP_EOL;
    echo "  Name: {$dbItem->name}" . PHP_EOL;
    echo "  Company: {$dbItem->company->name}" . PHP_EOL;
    echo "  HSN Code: " . ($dbItem->hsn_code ?? 'N/A') . PHP_EOL;
    echo "  CGST: {$dbItem->cgst_percent}% | SGST: {$dbItem->sgst_percent}%" . PHP_EOL;
    echo "  ✓ MATCH!" . PHP_EOL;
} else {
    echo "  ✗ NOT FOUND IN DATABASE!" . PHP_EOL;
}
echo PHP_EOL;

// Check item with HSN code from CSV
$data2 = str_getcsv($lines[2]);
$row2 = array_combine($header, $data2);

echo "Second item in CSV (with HSN):" . PHP_EOL;
echo "  Name: {$row2['name']}" . PHP_EOL;
echo "  Company: {$row2['Compname']}" . PHP_EOL;
echo "  HSN Code: " . ($row2['HSNCode'] ?? 'N/A') . PHP_EOL;
echo "  CGST: " . ($row2['CGST'] ?? '0') . "% | SGST: " . ($row2['SGST'] ?? '0') . "%" . PHP_EOL;
echo PHP_EOL;

$dbItem2 = Item::where('organization_id', 20)
    ->where('name', trim($row2['name']))
    ->with('company')
    ->first();

if ($dbItem2) {
    echo "Found in database:" . PHP_EOL;
    echo "  Name: {$dbItem2->name}" . PHP_EOL;
    echo "  Company: {$dbItem2->company->name}" . PHP_EOL;
    echo "  HSN Code: " . ($dbItem2->hsn_code ?? 'N/A') . PHP_EOL;
    echo "  CGST: {$dbItem2->cgst_percent}% | SGST: {$dbItem2->sgst_percent}%" . PHP_EOL;
    
    if ($dbItem2->hsn_code == $row2['HSNCode']) {
        echo "  ✓ HSN CODE MATCHES!" . PHP_EOL;
    } else {
        echo "  ✗ HSN CODE MISMATCH!" . PHP_EOL;
        echo "    Expected: {$row2['HSNCode']}" . PHP_EOL;
        echo "    Got: {$dbItem2->hsn_code}" . PHP_EOL;
    }
} else {
    echo "  ✗ NOT FOUND IN DATABASE!" . PHP_EOL;
}

echo PHP_EOL;
echo "=== VERIFICATION COMPLETE ===" . PHP_EOL;
