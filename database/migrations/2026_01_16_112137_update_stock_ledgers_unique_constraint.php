<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * The trans_no unique constraint is incorrect because:
     * 1. A single transaction can have multiple stock ledger entries (one per item)
     * 2. Different organizations can have the same trans_no
     * 
     * We need to remove the unique constraint on trans_no alone and replace with
     * a composite unique on trans_no + item_id + batch_id + organization_id
     */
    public function up(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {
            // Drop the existing unique constraint on trans_no only
            $table->dropUnique('stock_ledgers_trans_no_unique');
            
            // Add a regular index on trans_no for performance (not unique)
            $table->index('trans_no', 'stock_ledgers_trans_no_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {
            // Drop the regular index
            $table->dropIndex('stock_ledgers_trans_no_index');
            
            // Restore the unique constraint (may fail if duplicates exist)
            $table->unique('trans_no', 'stock_ledgers_trans_no_unique');
        });
    }
};
