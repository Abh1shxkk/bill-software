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
        Schema::create('supplier_payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_payment_id')->constrained()->onDelete('cascade');
            
            // Party Details
            $table->string('party_code', 20)->nullable();
            $table->string('party_name', 255)->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            
            // Cheque Details
            $table->string('cheque_no', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('cheque_bank_name', 100)->nullable();
            $table->string('cheque_bank_area', 100)->nullable();
            $table->date('cheque_closed_on')->nullable();
            
            // Amount
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('unadjusted', 15, 2)->default(0);
            
            // Payment Type: cash or cheque
            $table->enum('payment_type', ['cash', 'cheque'])->default('cash');
            
            $table->timestamps();
            
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payment_items');
    }
};
