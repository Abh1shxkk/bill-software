<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('claim_to_supplier_transactions', function (Blueprint $table) {
            $table->decimal('balance_amount', 15, 2)->default(0)->after('net_amount');
        });
        
        // Set initial balance_amount = net_amount for existing records
        DB::statement('UPDATE claim_to_supplier_transactions SET balance_amount = net_amount WHERE balance_amount = 0 OR balance_amount IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_to_supplier_transactions', function (Blueprint $table) {
            $table->dropColumn('balance_amount');
        });
    }
};
