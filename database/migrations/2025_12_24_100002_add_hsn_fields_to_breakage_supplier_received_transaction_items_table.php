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
        Schema::table('breakage_supplier_received_transaction_items', function (Blueprint $table) {
            // Make item_id nullable for HSN-only entries
            $table->unsignedBigInteger('item_id')->nullable()->change();
            
            // Add GST fields for HSN entries
            $table->decimal('gst_percent', 5, 2)->default(0)->after('sgst');
            $table->decimal('igst_percent', 5, 2)->default(0)->after('gst_percent');
            $table->decimal('gst_amount', 12, 2)->default(0)->after('igst_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakage_supplier_received_transaction_items', function (Blueprint $table) {
            $table->dropColumn(['gst_percent', 'igst_percent', 'gst_amount']);
        });
    }
};
