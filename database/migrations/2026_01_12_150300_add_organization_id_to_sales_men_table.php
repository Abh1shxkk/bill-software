<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_men', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_men', 'organization_id')) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('id');
                $table->index('organization_id');
            }
        });
        
        // Set existing sales_men to org 1
        DB::table('sales_men')->whereNull('organization_id')->update(['organization_id' => 1]);
    }

    public function down(): void
    {
        Schema::table('sales_men', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });
    }
};
