<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add voucher_type to purchase_transactions for Purchase Voucher
        Schema::table('purchase_transactions', function (Blueprint $table) {
            $table->string('voucher_type', 20)->default('purchase')->after('status');
        });

        // Add voucher_type to sale_return_transactions for Sale Return Voucher
        Schema::table('sale_return_transactions', function (Blueprint $table) {
            $table->string('voucher_type', 20)->default('return')->after('status');
        });

        // Add voucher_type to purchase_return_transactions for Purchase Return Voucher
        Schema::table('purchase_return_transactions', function (Blueprint $table) {
            $table->string('voucher_type', 20)->default('return')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_transactions', function (Blueprint $table) {
            $table->dropColumn('voucher_type');
        });

        Schema::table('sale_return_transactions', function (Blueprint $table) {
            $table->dropColumn('voucher_type');
        });

        Schema::table('purchase_return_transactions', function (Blueprint $table) {
            $table->dropColumn('voucher_type');
        });
    }
};
