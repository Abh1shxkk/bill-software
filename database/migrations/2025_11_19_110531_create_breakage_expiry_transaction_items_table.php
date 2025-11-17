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
        if (Schema::hasTable('breakage_expiry_transaction_items')) {
            return;
        }
        
        Schema::create('breakage_expiry_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('breakage_expiry_transaction_id');
            $table->foreign('breakage_expiry_transaction_id', 'be_trans_items_be_trans_id_fk')
                  ->references('id')->on('breakage_expiry_transactions')->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            
            // Item Details
            $table->string('item_code')->nullable();
            $table->string('item_name');
            $table->string('batch_no')->nullable();
            $table->string('expiry')->nullable(); // Expiry (MM/YY format)
            $table->char('br_ex', 1)->nullable(); // B for Breakage, E for Expiry
            
            // Quantities
            $table->decimal('qty', 10, 3)->default(0); // Quantity
            $table->decimal('f_qty', 10, 3)->default(0); // Free Quantity
            
            // Rates and Amounts
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('scm_percent', 10, 2)->default(0); // Scheme Percentage
            $table->decimal('dis_percent', 10, 2)->default(0); // Discount Percentage
            $table->decimal('amount', 10, 2)->default(0);
            
            // Tax Details
            $table->string('hsn_code')->nullable();
            $table->decimal('cgst_percent', 10, 2)->default(0);
            $table->decimal('sgst_percent', 10, 2)->default(0);
            $table->decimal('cgst_amount', 10, 2)->default(0);
            $table->decimal('sgst_amount', 10, 2)->default(0);
            $table->decimal('tax_percent', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            
            // Batch Rates (from batch data)
            $table->decimal('s_rate', 10, 2)->default(0); // Sale Rate
            $table->decimal('p_rate', 10, 2)->default(0); // Purchase Rate
            
            // Additional Details
            $table->string('packing')->nullable();
            $table->string('company_name')->nullable();
            
            $table->integer('row_order')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('breakage_expiry_transaction_id');
            $table->index('item_id');
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakage_expiry_transaction_items');
    }
};
