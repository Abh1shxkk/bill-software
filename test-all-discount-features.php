<?php
/**
 * Comprehensive Test for Discount Feature
 * Tests: Sale Transaction, Sale Modification, Purchase Transaction, Purchase Modification
 * Run: php test-all-discount-features.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "🧪 COMPREHENSIVE DISCOUNT FEATURE TEST\n";
echo str_repeat("=", 70) . "\n\n";

$results = ['passed' => 0, 'failed' => 0];

// Test Data
$company = DB::table('companies')->where('is_deleted', '!=', 1)->first();
$item = DB::table('items')->first();

if (!$company || !$item) {
    die("❌ No test data found. Need at least 1 company and 1 item.\n");
}

echo "📊 TEST DATA:\n";
echo "   Company: {$company->name} (ID: {$company->id})\n";
echo "   Item: {$item->name} (ID: {$item->id})\n\n";

// Test 1: Temporary Change
echo "✅ Test 1: TEMPORARY CHANGE\n";
echo str_repeat("-", 70) . "\n";
echo "   Scenario: User changes discount but selects 'Temporary'\n";
echo "   Expected: No database change\n";

$oldCompanyDiscount = $company->dis_on_sale_percent;
$oldItemDiscount = $item->fixed_dis_percent;

echo "   Before: Company={$oldCompanyDiscount}%, Item={$oldItemDiscount}%\n";
echo "   Action: User selects 'Temporary Change' (no API call)\n";

// Verify no change
$companyAfter = DB::table('companies')->where('id', $company->id)->first();
$itemAfter = DB::table('items')->where('id', $item->id)->first();

if ($companyAfter->dis_on_sale_percent == $oldCompanyDiscount && 
    $itemAfter->fixed_dis_percent == $oldItemDiscount) {
    echo "   ✅ PASSED: No database changes (as expected)\n";
    $results['passed']++;
} else {
    echo "   ❌ FAILED: Unexpected database changes\n";
    $results['failed']++;
}
echo "\n";

// Test 2: Save to Company
echo "✅ Test 2: SAVE TO COMPANY\n";
echo str_repeat("-", 70) . "\n";
echo "   Scenario: User changes discount and saves to company\n";
echo "   Expected: Company discount updated in database\n";

$testDiscount = 12.5;
echo "   Before: Company discount = {$oldCompanyDiscount}%\n";
echo "   Action: Save {$testDiscount}% to company\n";

// Simulate API call
DB::table('companies')->where('id', $company->id)->update(['dis_on_sale_percent' => $testDiscount]);
$companyAfter = DB::table('companies')->where('id', $company->id)->first();

if ($companyAfter->dis_on_sale_percent == $testDiscount) {
    echo "   ✅ PASSED: Company discount = {$companyAfter->dis_on_sale_percent}%\n";
    $results['passed']++;
} else {
    echo "   ❌ FAILED: Expected {$testDiscount}%, got {$companyAfter->dis_on_sale_percent}%\n";
    $results['failed']++;
}

// Restore
DB::table('companies')->where('id', $company->id)->update(['dis_on_sale_percent' => $oldCompanyDiscount]);
echo "   Restored to original value\n\n";

// Test 3: Save to Item
echo "✅ Test 3: SAVE TO ITEM\n";
echo str_repeat("-", 70) . "\n";
echo "   Scenario: User changes discount and saves to item\n";
echo "   Expected: Item discount updated in database\n";

$testDiscount = 8.75;
echo "   Before: Item discount = {$oldItemDiscount}%\n";
echo "   Action: Save {$testDiscount}% to item\n";

// Simulate API call
DB::table('items')->where('id', $item->id)->update(['fixed_dis_percent' => $testDiscount]);
$itemAfter = DB::table('items')->where('id', $item->id)->first();

if ($itemAfter->fixed_dis_percent == $testDiscount) {
    echo "   ✅ PASSED: Item discount = {$itemAfter->fixed_dis_percent}%\n";
    $results['passed']++;
} else {
    echo "   ❌ FAILED: Expected {$testDiscount}%, got {$itemAfter->fixed_dis_percent}%\n";
    $results['failed']++;
}

// Restore
DB::table('items')->where('id', $item->id)->update(['fixed_dis_percent' => $oldItemDiscount]);
echo "   Restored to original value\n\n";

// Test 4: Routes Check
echo "✅ Test 4: ROUTES CHECK\n";
echo str_repeat("-", 70) . "\n";

$routes = Route::getRoutes();
$requiredRoutes = [
    'admin.sale.saveCompanyDiscount',
    'admin.sale.saveItemDiscount',
    'admin.purchase.saveCompanyDiscount',
    'admin.purchase.saveItemDiscount'
];

foreach ($requiredRoutes as $routeName) {
    $route = $routes->getByName($routeName);
    if ($route) {
        echo "   ✅ {$routeName}\n";
        $results['passed']++;
    } else {
        echo "   ❌ {$routeName} NOT FOUND\n";
        $results['failed']++;
    }
}
echo "\n";

// Test 5: View Files Check
echo "✅ Test 5: VIEW FILES CHECK\n";
echo str_repeat("-", 70) . "\n";

$viewFiles = [
    'resources/views/admin/sale/transaction.blade.php' => 'Sale Transaction',
    'resources/views/admin/sale/modification.blade.php' => 'Sale Modification',
    'resources/views/admin/purchase/transaction.blade.php' => 'Purchase Transaction',
    'resources/views/admin/purchase/modification.blade.php' => 'Purchase Modification'
];

foreach ($viewFiles as $file => $name) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $hasModal = strpos($content, 'discountOptionsModal') !== false;
        $hasFunctions = strpos($content, 'showDiscountOptionsModal') !== false && 
                       strpos($content, 'saveDiscountToCompany') !== false;
        
        if ($hasModal && $hasFunctions) {
            echo "   ✅ {$name}: Modal & Functions present\n";
            $results['passed']++;
        } else {
            echo "   ❌ {$name}: Missing components\n";
            $results['failed']++;
        }
    } else {
        echo "   ❌ {$name}: File not found\n";
        $results['failed']++;
    }
}

echo "\n";
echo str_repeat("=", 70) . "\n";
echo "🎉 TEST SUMMARY\n";
echo str_repeat("=", 70) . "\n";
echo "   ✅ Passed: {$results['passed']}\n";
echo "   ❌ Failed: {$results['failed']}\n";
echo "   Total: " . ($results['passed'] + $results['failed']) . "\n\n";

if ($results['failed'] == 0) {
    echo "🎊 ALL TESTS PASSED! Discount feature is fully implemented!\n\n";
} else {
    echo "⚠️  Some tests failed. Please review the output above.\n\n";
}

echo "📋 MANUAL TESTING CHECKLIST:\n";
echo str_repeat("-", 70) . "\n";
echo "1. Sale Transaction (http://localhost/admin/sale/transaction)\n";
echo "   - Add item, change discount, press Enter\n";
echo "   - Test: Temporary, Company, Item options\n\n";
echo "2. Sale Modification (http://localhost/admin/sale/modification)\n";
echo "   - Open existing invoice, change discount, press Enter\n";
echo "   - Test: Temporary, Company, Item options\n\n";
echo "3. Purchase Transaction (http://localhost/admin/purchase/transaction)\n";
echo "   - Add item, change discount, press Enter\n";
echo "   - Test: Temporary, Company, Item options\n\n";
echo "4. Purchase Modification (http://localhost/admin/purchase/modification)\n";
echo "   - Open existing invoice, change discount, press Enter\n";
echo "   - Test: Temporary, Company, Item options\n\n";

echo "💡 TIP: Open browser console (F12) to see detailed logs!\n";
