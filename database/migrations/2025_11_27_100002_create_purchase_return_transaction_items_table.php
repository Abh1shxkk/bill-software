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
        Schema::create('purchase_return_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_return_transaction_id');
            $table->foreign('purchase_return_transaction_id', 'pr_trans_items_pr_trans_id_fk')
                ->references('id')->on('purchase_return_transactions')->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            
            // Item Details
            $table->string('item_code')->nullable();
            $table->string('item_name');
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Quantities
            $table->decimal('qty', 10, 3)->default(0); // Return Quantity
            $table->decimal('free_qty', 10, 3)->default(0); // Free Quantity
            
            // Rates and Amounts
            $table->decimal('pur_rate', 10, 2)->default(0); // Purchase Rate
            $table->decimal('dis_percent', 10, 3)->default(0); // Discount Percentage
            $table->decimal('ft_rate', 10, 2)->default(0); // Final Taxable Rate
            $table->decimal('ft_amount', 10, 2)->default(0); // Final Taxable Amount
            $table->decimal('mrp', 10, 2)->default(0); // MRP
            $table->decimal('ws_rate', 10, 2)->default(0); // Wholesale Rate
            $table->decimal('s_rate', 10, 2)->default(0); // Sale Rate
            $table->decimal('spl_rate', 10, 2)->default(0); // Special Rate
            
            // Tax Details
            $table->decimal('cgst_percent', 10, 3)->default(0);
            $table->decimal('sgst_percent', 10, 3)->default(0);
            $table->decimal('cess_percent', 10, 3)->default(0);
            $table->decimal('cgst_amount', 10, 2)->default(0);
            $table->decimal('sgst_amount', 10, 2)->default(0);
            $table->decimal('cess_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            
            // Additional Details
            $table->string('hsn_code')->nullable();
            $table->string('packing')->nullable();
            $table->string('unit')->nullable();
            $table->string('company_name')->nullable();
            
            $table->integer('row_order')->default(0);
            $table->timestamps();
            
            // Indexes with shorter names
            $table->index('purchase_return_transaction_id', 'pr_trans_items_pr_trans_idx');
            $table->index('item_id', 'pr_trans_items_item_idx');
            $table->index('batch_id', 'pr_trans_items_batch_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_transaction_items');
    }
};
