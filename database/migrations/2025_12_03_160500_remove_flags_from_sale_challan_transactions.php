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
        Schema::table('sale_challan_transactions', function (Blueprint $table) {
            $table->dropColumn(['cash_flag', 'transfer_flag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_challan_transactions', function (Blueprint $table) {
            $table->char('cash_flag', 1)->default('N')->after('salesman_id');
            $table->char('transfer_flag', 1)->default('N')->after('cash_flag');
        });
    }
};
