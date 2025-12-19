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
        // Add voucher_type to sale_return_transactions
        if (Schema::hasTable('sale_return_transactions') && !Schema::hasColumn('sale_return_transactions', 'voucher_type')) {
            Schema::table('sale_return_transactions', function (Blueprint $table) {
                $table->string('voucher_type', 20)->nullable()->default(null)->after('series');
            });
        }

        // Add voucher_type to purchase_return_transactions
        if (Schema::hasTable('purchase_return_transactions') && !Schema::hasColumn('purchase_return_transactions', 'voucher_type')) {
            Schema::table('purchase_return_transactions', function (Blueprint $table) {
                $table->string('voucher_type', 20)->nullable()->default(null)->after('series');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sale_return_transactions', 'voucher_type')) {
            Schema::table('sale_return_transactions', function (Blueprint $table) {
                $table->dropColumn('voucher_type');
            });
        }

        if (Schema::hasColumn('purchase_return_transactions', 'voucher_type')) {
            Schema::table('purchase_return_transactions', function (Blueprint $table) {
                $table->dropColumn('voucher_type');
            });
        }
    }
};
