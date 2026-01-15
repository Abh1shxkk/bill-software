<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change the unique constraint on rr_no to include organization_id
     * so different organizations can have their own sequence (RR00001, RR00002, etc.)
     */
    public function up(): void
    {
        Schema::table('replacement_received_transactions', function (Blueprint $table) {
            // Drop the existing unique constraint on rr_no only
            $table->dropUnique('replacement_received_transactions_rr_no_unique');
            
            // Add new composite unique constraint on rr_no + organization_id
            $table->unique(['rr_no', 'organization_id'], 'replacement_received_rr_no_org_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replacement_received_transactions', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('replacement_received_rr_no_org_unique');
            
            // Restore the original unique constraint on rr_no only
            $table->unique('rr_no', 'replacement_received_transactions_rr_no_unique');
        });
    }
};
