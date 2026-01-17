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
        Schema::table('sale_transactions', function (Blueprint $table) {
            // Add challan_id for linking sales to challans
            if (!Schema::hasColumn('sale_transactions', 'challan_id')) {
                $table->unsignedBigInteger('challan_id')->nullable()->after('remarks');
            }
            
            // Add receipt_path for TEMP transactions
            if (!Schema::hasColumn('sale_transactions', 'receipt_path')) {
                $table->string('receipt_path', 500)->nullable()->after('challan_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('sale_transactions', 'challan_id')) {
                $table->dropColumn('challan_id');
            }
            if (Schema::hasColumn('sale_transactions', 'receipt_path')) {
                $table->dropColumn('receipt_path');
            }
        });
    }
};
