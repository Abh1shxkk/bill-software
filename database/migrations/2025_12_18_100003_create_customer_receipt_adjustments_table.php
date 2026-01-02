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
        Schema::create('customer_receipt_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_receipt_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_receipt_item_id')->nullable()->constrained()->onDelete('cascade');
            
            // Adjustment Type: outstanding or adjusted
            $table->enum('adjustment_type', ['outstanding', 'adjusted'])->default('outstanding');
            
            // Reference to invoice/bill
            $table->string('reference_no', 50)->nullable();
            $table->date('reference_date')->nullable();
            $table->decimal('reference_amount', 15, 2)->default(0);
            $table->decimal('adjusted_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_receipt_adjustments');
    }
};
