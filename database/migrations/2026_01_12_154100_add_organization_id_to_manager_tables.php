<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'area_managers',
            'regional_managers', 
            'marketing_managers',
            'general_managers',
            'dc_managers',
            'collection_managers',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'organization_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('organization_id')->nullable()->after('id');
                    $t->index('organization_id');
                });
                
                // Set existing records to org 1
                DB::table($table)->whereNull('organization_id')->update(['organization_id' => 1]);
                
                echo "Added organization_id to {$table}\n";
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'area_managers',
            'regional_managers',
            'marketing_managers', 
            'general_managers',
            'dc_managers',
            'collection_managers',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'organization_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('organization_id');
                });
            }
        }
    }
};
