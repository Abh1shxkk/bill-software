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
        Schema::table('customer_receipt_items', function (Blueprint $table) {
            // Bank Details for Cheque
            $table->string('cheque_bank_name', 255)->nullable()->after('cheque_date');
            $table->string('cheque_bank_area', 255)->nullable()->after('cheque_bank_name');
            $table->date('cheque_closed_on')->nullable()->after('cheque_bank_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_receipt_items', function (Blueprint $table) {
            $table->dropColumn(['cheque_bank_name', 'cheque_bank_area', 'cheque_closed_on']);
        });
    }
};
