<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add organization_id to organization-specific tables that were missing it
     */
    public function up(): void
    {
        $tablesToUpdate = [
            'country_managers',
            'credit_note_adjustments',
            'divisional_managers',
            'expiry_ledger',
            'godown_expiry',
            'sale_items',
            'sales',
            'states',
        ];

        foreach ($tablesToUpdate as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'organization_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('organization_id')->nullable()->after('id');
                    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
                    $table->index('organization_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablesToUpdate = [
            'country_managers',
            'credit_note_adjustments',
            'divisional_managers',
            'expiry_ledger',
            'godown_expiry',
            'sale_items',
            'sales',
            'states',
        ];

        foreach ($tablesToUpdate as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'organization_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['organization_id']);
                    $table->dropIndex(['organization_id']);
                    $table->dropColumn('organization_id');
                });
            }
        }
    }
};
