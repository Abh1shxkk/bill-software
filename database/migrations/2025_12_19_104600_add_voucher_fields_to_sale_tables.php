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
        // Add voucher_type to sale_transactions (already added)
        if (!Schema::hasColumn('sale_transactions', 'voucher_type')) {
            Schema::table('sale_transactions', function (Blueprint $table) {
                $table->enum('voucher_type', ['sale', 'voucher'])->default('sale')->after('series');
            });
        }

        // Add hsn fields to sale_transaction_items for voucher entries
        Schema::table('sale_transaction_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_transaction_items', 'hsn_description')) {
                $table->text('hsn_description')->nullable()->after('hsn_code');
            }
            if (!Schema::hasColumn('sale_transaction_items', 'gst_percent')) {
                $table->decimal('gst_percent', 8, 2)->default(0)->after('hsn_description');
            }
            if (!Schema::hasColumn('sale_transaction_items', 'gross_amount')) {
                $table->decimal('gross_amount', 15, 2)->default(0)->after('sgst_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('sale_transactions', 'voucher_type')) {
                $table->dropColumn('voucher_type');
            }
        });

        Schema::table('sale_transaction_items', function (Blueprint $table) {
            $columns = ['hsn_description', 'gst_percent', 'gross_amount'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('sale_transaction_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
