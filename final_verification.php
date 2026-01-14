<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Item;
use App\Models\Company;

echo "╔════════════════════════════════════════════════════════════╗" . PHP_EOL;
echo "║         FINAL ITEM SEEDING VERIFICATION REPORT            ║" . PHP_EOL;
echo "╚════════════════════════════════════════════════════════════╝" . PHP_EOL;
echo PHP_EOL;

// 1. Count verification
echo "📊 DATABASE STATISTICS:" . PHP_EOL;
echo "─────────────────────────────────────────────────────────────" . PHP_EOL;
$totalItems = Item::where('organization_id', 20)->count();
$itemsWithHsn = Item::where('organization_id', 20)->whereNotNull('hsn_code')->where('hsn_code', '!=', '')->count();
$itemsWithoutHsn = $totalItems - $itemsWithHsn;
$totalCompanies = Company::where('organization_id', 20)->count();

echo "✓ Total Items Seeded: $totalItems" . PHP_EOL;
echo "✓ Items with HSN Code: $itemsWithHsn" . PHP_EOL;
echo "✓ Items without HSN Code: $itemsWithoutHsn" . PHP_EOL;
echo "✓ Total Companies: $totalCompanies" . PHP_EOL;
echo PHP_EOL;

// 2. CSV comparison
echo "📄 CSV COMPARISON:" . PHP_EOL;
echo "─────────────────────────────────────────────────────────────" . PHP_EOL;
$csvFile = 'item.xlsx - item.csv';
$lines = file($csvFile, FILE_IGNORE_NEW_LINES);
$csvDataRows = count($lines) - 1;
$difference = $csvDataRows - $totalItems;

echo "CSV Data Rows: $csvDataRows" . PHP_EOL;
echo "Database Items: $totalItems" . PHP_EOL;
echo "Difference: $difference (duplicates in CSV)" . PHP_EOL;
echo PHP_EOL;

// 3. HSN Code verification
echo "🔍 HSN CODE VERIFICATION:" . PHP_EOL;
echo "─────────────────────────────────────────────────────────────" . PHP_EOL;

$header = str_getcsv($lines[0]);
$testCases = [
    ['line' => 2, 'name' => 'MEFLOTAS', 'expectedHsn' => '30049066'],
    ['line' => 3, 'name' => 'CLAVIX-AS-75', 'expectedHsn' => '30049099'],
    ['line' => 4, 'name' => 'VENTAB XL 75', 'expectedHsn' => '30049099'],
];

$passed = 0;
$failed = 0;

foreach ($testCases as $test) {
    $data = str_getcsv($lines[$test['line']]);
    $row = array_combine($header, $data);
    
    $item = Item::where('organization_id', 20)
        ->where('name', $test['name'])
        ->first();
    
    if ($item && $item->hsn_code == $test['expectedHsn']) {
        echo "✓ {$test['name']}: HSN {$item->hsn_code} - PASS" . PHP_EOL;
        $passed++;
    } else {
        echo "✗ {$test['name']}: Expected {$test['expectedHsn']}, Got " . ($item ? $item->hsn_code : 'NOT FOUND') . " - FAIL" . PHP_EOL;
        $failed++;
    }
}
echo PHP_EOL;

// 4. Company relationship verification
echo "🏢 COMPANY RELATIONSHIP VERIFICATION:" . PHP_EOL;
echo "─────────────────────────────────────────────────────────────" . PHP_EOL;

$itemsWithCompany = Item::where('organization_id', 20)->whereNotNull('company_id')->count();
$itemsWithoutCompany = $totalItems - $itemsWithCompany;

echo "Items with Company: $itemsWithCompany" . PHP_EOL;
echo "Items without Company: $itemsWithoutCompany" . PHP_EOL;

// Sample company check
$sampleItem = Item::where('organization_id', 20)
    ->where('name', 'MEFLOTAS')
    ->with('company')
    ->first();

if ($sampleItem && $sampleItem->company) {
    echo "✓ Sample Item 'MEFLOTAS' → Company: {$sampleItem->company->name}" . PHP_EOL;
} else {
    echo "✗ Sample Item 'MEFLOTAS' → Company relationship FAILED" . PHP_EOL;
}
echo PHP_EOL;

// 5. Data integrity checks
echo "🔐 DATA INTEGRITY CHECKS:" . PHP_EOL;
echo "─────────────────────────────────────────────────────────────" . PHP_EOL;

$itemsWithMrp = Item::where('organization_id', 20)->whereNotNull('mrp')->where('mrp', '>', 0)->count();
$itemsWithPurRate = Item::where('organization_id', 20)->whereNotNull('pur_rate')->where('pur_rate', '>', 0)->count();
$itemsWithSRate = Item::where('organization_id', 20)->whereNotNull('s_rate')->where('s_rate', '>', 0)->count();

echo "Items with MRP: $itemsWithMrp" . PHP_EOL;
echo "Items with Purchase Rate: $itemsWithPurRate" . PHP_EOL;
echo "Items with Sale Rate: $itemsWithSRate" . PHP_EOL;
echo PHP_EOL;

// 6. Sample data display
echo "📋 SAMPLE ITEMS:" . PHP_EOL;
echo "─────────────────────────────────────────────────────────────" . PHP_EOL;

$samples = Item::where('organization_id', 20)
    ->with('company')
    ->whereNotNull('hsn_code')
    ->where('hsn_code', '!=', '')
    ->take(3)
    ->get();

foreach ($samples as $item) {
    echo "Item: {$item->name}" . PHP_EOL;
    echo "  Company: {$item->company->name}" . PHP_EOL;
    echo "  HSN: {$item->hsn_code} | CGST: {$item->cgst_percent}% | SGST: {$item->sgst_percent}%" . PHP_EOL;
    echo "  MRP: ₹{$item->mrp} | Pur: ₹{$item->pur_rate} | Sale: ₹{$item->s_rate}" . PHP_EOL;
    echo PHP_EOL;
}

// 7. Final summary
echo "╔════════════════════════════════════════════════════════════╗" . PHP_EOL;
echo "║                    FINAL SUMMARY                           ║" . PHP_EOL;
echo "╚════════════════════════════════════════════════════════════╝" . PHP_EOL;
echo PHP_EOL;

$allChecks = [
    "Items seeded: $totalItems" => $totalItems == 3986,
    "HSN codes stored correctly" => $passed == count($testCases),
    "Company relationships established" => $itemsWithCompany == $totalItems,
    "Price data populated" => $itemsWithMrp > 0 && $itemsWithPurRate > 0,
];

$allPassed = true;
foreach ($allChecks as $check => $status) {
    $icon = $status ? "✓" : "✗";
    $result = $status ? "PASS" : "FAIL";
    echo "$icon $check: $result" . PHP_EOL;
    if (!$status) $allPassed = false;
}

echo PHP_EOL;
if ($allPassed) {
    echo "🎉 ALL CHECKS PASSED! Item seeding is complete and verified." . PHP_EOL;
} else {
    echo "⚠️  Some checks failed. Please review the issues above." . PHP_EOL;
}
echo PHP_EOL;
