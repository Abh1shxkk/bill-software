<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change the unique constraint on sr_no to include organization_id
     * so different organizations can have their own sequence (STOR-001, STOR-002, etc.)
     */
    public function up(): void
    {
        Schema::table('stock_transfer_outgoing_return_transactions', function (Blueprint $table) {
            // Drop the existing unique constraint on sr_no only
            $table->dropUnique('stock_transfer_outgoing_return_transactions_sr_no_unique');
            
            // Add new composite unique constraint on sr_no + organization_id
            $table->unique(['sr_no', 'organization_id'], 'stock_transfer_outgoing_return_sr_no_org_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transfer_outgoing_return_transactions', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('stock_transfer_outgoing_return_sr_no_org_unique');
            
            // Restore the original unique constraint on sr_no only
            $table->unique('sr_no', 'stock_transfer_outgoing_return_transactions_sr_no_unique');
        });
    }
};
