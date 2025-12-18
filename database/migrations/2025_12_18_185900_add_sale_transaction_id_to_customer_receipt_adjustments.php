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
        Schema::table('customer_receipt_adjustments', function (Blueprint $table) {
            $table->unsignedBigInteger('sale_transaction_id')->nullable()->after('customer_receipt_item_id');
            $table->index('sale_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_receipt_adjustments', function (Blueprint $table) {
            $table->dropIndex(['sale_transaction_id']);
            $table->dropColumn('sale_transaction_id');
        });
    }
};
