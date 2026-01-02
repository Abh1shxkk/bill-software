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
        Schema::create('purchase_vouchers', function (Blueprint $table) {
            $table->id();
            $table->date('voucher_date');
            $table->integer('voucher_no');
            $table->string('bill_no', 100)->nullable();
            $table->date('bill_date')->nullable();
            $table->enum('local_inter', ['L', 'I'])->default('L'); // L=Local, I=Inter
            $table->enum('rcm', ['Y', 'N'])->default('N'); // Reverse Charge Mechanism
            $table->text('description')->nullable();
            
            // Supplier info
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_code', 50)->nullable();
            $table->string('supplier_name', 255)->nullable();
            $table->string('gst_no', 20)->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pin', 10)->nullable();
            
            // Debit side totals
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('total_gst', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('total_debit', 15, 2)->default(0);
            
            // Credit side
            $table->decimal('tds_percent', 5, 2)->default(0);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->enum('payment_type', ['1', '2'])->default('1'); // 1=Cash & Bank, 2=General
            $table->unsignedBigInteger('credit_account_id')->nullable();
            $table->string('credit_account_type', 20)->nullable(); // CB, GL
            $table->string('credit_account_name', 255)->nullable();
            $table->string('cheque_no', 50)->nullable();
            $table->decimal('total_credit', 15, 2)->default(0);
            
            // GST Summary totals
            $table->decimal('total_cgst_amount', 15, 2)->default(0);
            $table->decimal('total_sgst_amount', 15, 2)->default(0);
            $table->decimal('total_igst_amount', 15, 2)->default(0);
            
            $table->enum('status', ['active', 'cancelled', 'reversed'])->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['voucher_no', 'voucher_date']);
            $table->index('supplier_id');
            $table->index('bill_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_vouchers');
    }
};
