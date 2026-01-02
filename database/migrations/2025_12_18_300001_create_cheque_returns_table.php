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
        Schema::create('cheque_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_receipt_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_receipt_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            
            // Customer Details
            $table->string('customer_code', 20)->nullable();
            $table->string('customer_name', 255)->nullable();
            
            // Cheque Details (copied from receipt item)
            $table->string('cheque_no', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_area', 255)->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            
            // Transaction Details
            $table->integer('trn_no')->nullable();
            $table->date('receipt_date')->nullable();
            $table->date('deposit_date')->nullable();
            
            // Return Status
            $table->enum('status', ['pending', 'returned', 'cancelled'])->default('pending');
            $table->date('return_date')->nullable();
            $table->date('status_date')->nullable();
            $table->text('remarks')->nullable();
            
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['cheque_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheque_returns');
    }
};
