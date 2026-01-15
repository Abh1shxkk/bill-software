<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('breakage_supplier_issued_transactions', 'cases')) {
            return; // Column already exists, skip
        }
        
        Schema::table('breakage_supplier_issued_transactions', function (Blueprint $table) {
            $table->decimal('cases', 15, 2)->default(0)->nullable()->after('total_qty');
        });
    }

    public function down(): void
    {
        Schema::table('breakage_supplier_issued_transactions', function (Blueprint $table) {
            $table->dropColumn('cases');
        });
    }
};
