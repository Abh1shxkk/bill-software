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
        Schema::create('customer_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_receipt_id')->constrained()->onDelete('cascade');
            
            // Party Details
            $table->string('party_code', 20)->nullable();
            $table->string('party_name', 255)->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            
            // Cheque Details
            $table->string('cheque_no', 50)->nullable();
            $table->date('cheque_date')->nullable();
            
            // Amount
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('unadjusted', 15, 2)->default(0);
            
            // Payment Type: cash or cheque
            $table->enum('payment_type', ['cash', 'cheque'])->default('cash');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_receipt_items');
    }
};
