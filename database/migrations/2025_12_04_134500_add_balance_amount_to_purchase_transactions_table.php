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
        Schema::table('purchase_transactions', function (Blueprint $table) {
            $table->decimal('balance_amount', 15, 2)->default(0)->after('net_amount');
        });

        // Set balance_amount = net_amount for all existing records (assuming no payments made yet)
        DB::statement('UPDATE purchase_transactions SET balance_amount = COALESCE(inv_amount, net_amount, 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_transactions', function (Blueprint $table) {
            $table->dropColumn('balance_amount');
        });
    }
};
