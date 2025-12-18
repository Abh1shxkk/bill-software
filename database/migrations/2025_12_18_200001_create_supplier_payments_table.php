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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->date('payment_date');
            $table->string('day_name', 20)->nullable();
            $table->integer('trn_no')->nullable();
            $table->string('ledger', 10)->default('SL');
            
            // Bank Details
            $table->string('bank_code', 20)->nullable();
            $table->string('bank_name', 100)->nullable();
            
            // Totals
            $table->decimal('total_cash', 15, 2)->default(0);
            $table->decimal('total_cheque', 15, 2)->default(0);
            $table->decimal('amt_outstanding', 15, 2)->default(0);
            $table->decimal('amt_adjusted', 15, 2)->default(0);
            $table->decimal('tds_amount', 15, 2)->default(0);
            
            // Currency Detail
            $table->boolean('currency_detail')->default(false);
            
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
