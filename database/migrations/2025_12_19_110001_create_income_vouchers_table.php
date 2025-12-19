<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_vouchers', function (Blueprint $table) {
            $table->id();
            $table->date('voucher_date');
            $table->integer('voucher_no');
            $table->string('local_inter', 1)->default('L');
            $table->text('description')->nullable();
            
            // Customer info
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('gst_no', 50)->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pin', 10)->nullable();
            $table->text('address')->nullable();
            
            // Credit section (amounts)
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('total_gst', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('round_off', 10, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            
            // Debit section (TDS)
            $table->decimal('tds_percent', 5, 2)->default(0);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('debit_account_id')->nullable();
            $table->string('debit_account_type', 10)->nullable();
            $table->string('debit_account_name')->nullable();
            $table->decimal('total_debit', 15, 2)->default(0);
            
            // GST totals
            $table->decimal('total_cgst_amount', 15, 2)->default(0);
            $table->decimal('total_sgst_amount', 15, 2)->default(0);
            $table->decimal('total_igst_amount', 15, 2)->default(0);
            
            $table->string('status', 20)->default('active');
            $table->timestamps();
            
            $table->index(['voucher_date', 'voucher_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_vouchers');
    }
};
