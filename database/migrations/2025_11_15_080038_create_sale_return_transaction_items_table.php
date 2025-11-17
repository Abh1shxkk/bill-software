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
        Schema::create('sale_return_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_return_transaction_id')->constrained('sale_return_transactions')->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            
            // Item Details
            $table->string('item_code')->nullable();
            $table->string('item_name');
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Quantities
            $table->decimal('qty', 10, 3)->default(0); // Quantity
            $table->decimal('free_qty', 10, 3)->default(0); // Free Quantity
            
            // Rates and Amounts
            $table->decimal('sale_rate', 10, 2)->default(0);
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('discount_percent', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            
            // Tax Details
            $table->decimal('cgst_percent', 10, 2)->default(0);
            $table->decimal('sgst_percent', 10, 2)->default(0);
            $table->decimal('cess_percent', 10, 2)->default(0);
            $table->decimal('cgst_amount', 10, 2)->default(0);
            $table->decimal('sgst_amount', 10, 2)->default(0);
            $table->decimal('cess_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            
            // Additional Details
            $table->string('unit')->nullable();
            $table->string('packing')->nullable();
            $table->string('company_name')->nullable();
            $table->string('hsn_code')->nullable();
            
            $table->integer('row_order')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('sale_return_transaction_id');
            $table->index('item_id');
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_transaction_items');
    }
};
