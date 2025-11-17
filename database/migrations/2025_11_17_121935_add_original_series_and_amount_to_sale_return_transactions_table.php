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
        Schema::table('sale_return_transactions', function (Blueprint $table) {
            // Add original_amount column after original_series
            $table->decimal('original_amount', 10, 2)->default(0)->after('original_series');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_return_transactions', function (Blueprint $table) {
            $table->dropColumn('original_amount');
        });
    }
};
