<?php
/**
 * One-time script to fix receipt storage issues
 * 1. Move receipt files from wrong location to correct location
 * 2. Update the receipt_path in database
 * 
 * Run this script once via browser: http://localhost/bill-software/fix-receipts.php
 */

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "<h2>Receipt Storage Fix Script</h2>";

// Source and destination paths
$sourcePath = storage_path('app/private/public/receipts');
$destPath = storage_path('app/public/receipts');

echo "<p><strong>Source:</strong> $sourcePath</p>";
echo "<p><strong>Destination:</strong> $destPath</p>";

// Check if source exists
if (!File::exists($sourcePath)) {
    echo "<p style='color: orange;'>⚠️ Source folder doesn't exist. Nothing to move.</p>";
} else {
    // Create destination if it doesn't exist
    if (!File::exists($destPath)) {
        File::makeDirectory($destPath, 0755, true);
        echo "<p>✅ Created destination folder</p>";
    }
    
    // Copy all files
    try {
        File::copyDirectory($sourcePath, $destPath);
        echo "<p style='color: green;'>✅ Successfully copied all receipt files!</p>";
        
        // List copied files
        $files = File::allFiles($destPath);
        echo "<p>Copied files:</p><ul>";
        foreach ($files as $file) {
            echo "<li>" . $file->getFilename() . "</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error copying files: " . $e->getMessage() . "</p>";
    }
}

// Now fix the database path if needed (the path was stored correctly, just file was in wrong place)
echo "<hr><h3>Database Check</h3>";

$tempTransactions = DB::table('sale_transactions')
    ->where('series', 'TEMP')
    ->orWhere('invoice_no', 'like', 'TEMP-%')
    ->select('id', 'invoice_no', 'receipt_path', 'receipt_description')
    ->get();

echo "<p>Found " . count($tempTransactions) . " TEMP transactions:</p>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Invoice No</th><th>Receipt Path</th><th>Description</th></tr>";

foreach ($tempTransactions as $trans) {
    echo "<tr>";
    echo "<td>{$trans->id}</td>";
    echo "<td>{$trans->invoice_no}</td>";
    echo "<td>" . ($trans->receipt_path ?: '<em>NULL</em>') . "</td>";
    echo "<td>" . ($trans->receipt_description ?: '<em>NULL</em>') . "</td>";
    echo "</tr>";
    
    // Check if file exists
    if ($trans->receipt_path) {
        $fullPath = public_path(str_replace('storage/', 'storage/', $trans->receipt_path));
        if (File::exists($fullPath)) {
            echo "<tr><td colspan='4' style='color: green;'>✅ File exists at: $fullPath</td></tr>";
        } else {
            echo "<tr><td colspan='4' style='color: red;'>❌ File NOT found at: $fullPath</td></tr>";
        }
    }
}
echo "</table>";

echo "<hr><p><strong>Script completed!</strong></p>";
echo "<p><a href='/bill-software/admin/sale/invoices'>Go to Sale Invoices</a></p>";
