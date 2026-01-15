<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change the unique constraint on rn_no to include organization_id
     * so different organizations can have their own sequence (RN0001, RN0002, etc.)
     */
    public function up(): void
    {
        Schema::table('replacement_note_transactions', function (Blueprint $table) {
            // Drop the existing unique constraint on rn_no only
            $table->dropUnique('replacement_note_transactions_rn_no_unique');
            
            // Add new composite unique constraint on rn_no + organization_id
            $table->unique(['rn_no', 'organization_id'], 'replacement_note_rn_no_org_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replacement_note_transactions', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('replacement_note_rn_no_org_unique');
            
            // Restore the original unique constraint on rn_no only
            $table->unique('rn_no', 'replacement_note_transactions_rn_no_unique');
        });
    }
};
