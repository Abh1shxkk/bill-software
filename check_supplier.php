<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// List all tables with 'purchase' in name
echo "=== Tables with 'purchase' ===\n";
$tables = DB::select('SHOW TABLES');
foreach($tables as $t) {
    $name = array_values((array)$t)[0];
    if(stripos($name, 'purchase') !== false) {
        echo $name . "\n";
    }
}

// Check purchases table
echo "\n=== purchases table (first 5) ===\n";
try {
    $purchases = DB::table('purchases')->select('id', 'supplier', 'supplier_id', 'bill_no', 'net_amount', 'total_amount')->limit(5)->get();
    foreach($purchases as $p) {
        echo "ID: {$p->id}, supplier: " . ($p->supplier ?? 'NA') . ", supplier_id: " . ($p->supplier_id ?? 'NA') . ", bill: " . ($p->bill_no ?? 'NA') . ", net: " . ($p->net_amount ?? $p->total_amount ?? 'NA') . "\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check purchase_transactions table
echo "\n=== purchase_transactions table (first 5) ===\n";
try {
    $pts = DB::table('purchase_transactions')->select('id', 'supplier_id', 'bill_no', 'net_amount', 'balance_amount', 'inv_amount')->limit(5)->get();
    foreach($pts as $p) {
        echo "ID: {$p->id}, supplier_id: {$p->supplier_id}, bill: {$p->bill_no}, net: {$p->net_amount}, balance: " . ($p->balance_amount ?? 'NA') . ", inv: " . ($p->inv_amount ?? 'NA') . "\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check supplier 19
echo "\n=== Supplier 19 (Delta Agencies) ===\n";
$s19 = App\Models\Supplier::where('supplier_id', 19)->first();
echo "Name: " . ($s19->name ?? 'Not found') . "\n";

// Check purchases for supplier 19
echo "\n=== Purchases for supplier_id=19 ===\n";
$p19_pt = DB::table('purchase_transactions')->where('supplier_id', 19)->count();
echo "purchase_transactions count: {$p19_pt}\n";

$p19_p = DB::table('purchases')->where(function($q) { $q->where('supplier_id', 19)->orWhere('supplier', 19); })->count();
echo "purchases count: {$p19_p}\n";
