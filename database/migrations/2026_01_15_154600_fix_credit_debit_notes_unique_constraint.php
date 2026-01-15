<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Changes the unique constraint on credit_notes and debit_notes tables
     * to be organization-specific (composite unique on note_no + organization_id)
     */
    public function up(): void
    {
        // Fix credit_notes table
        Schema::table('credit_notes', function (Blueprint $table) {
            // Drop the existing unique constraint on credit_note_no
            $table->dropUnique(['credit_note_no']);
            
            // Add composite unique constraint (credit_note_no + organization_id)
            $table->unique(['credit_note_no', 'organization_id'], 'credit_notes_no_org_unique');
        });
        
        // Fix debit_notes table
        Schema::table('debit_notes', function (Blueprint $table) {
            // Drop the existing unique constraint on debit_note_no
            $table->dropUnique(['debit_note_no']);
            
            // Add composite unique constraint (debit_note_no + organization_id)
            $table->unique(['debit_note_no', 'organization_id'], 'debit_notes_no_org_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert credit_notes table
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropUnique('credit_notes_no_org_unique');
            $table->unique('credit_note_no');
        });
        
        // Revert debit_notes table
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->dropUnique('debit_notes_no_org_unique');
            $table->unique('debit_note_no');
        });
    }
};
