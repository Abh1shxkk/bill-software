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
        Schema::create('stock_transfer_outgoing_return_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_outgoing_return_transaction_id');
            $table->foreign('stock_transfer_outgoing_return_transaction_id', 'stor_items_transaction_fk')->references('id')->on('stock_transfer_outgoing_return_transactions')->onDelete('cascade');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->foreign('item_id', 'stor_items_item_fk')->references('id')->on('items')->onDelete('set null');
            $table->foreign('batch_id', 'stor_items_batch_fk')->references('id')->on('batches')->onDelete('set null');
            
            $table->string('item_code')->nullable();
            $table->string('item_name');
            $table->string('batch_no')->nullable();
            $table->string('expiry')->nullable();
            
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('f_qty', 10, 2)->default(0);
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('p_rate', 10, 2)->default(0);
            $table->decimal('s_rate', 10, 2)->default(0);
            $table->decimal('scm_percent', 5, 2)->default(0);
            $table->decimal('dis_percent', 5, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            
            $table->string('hsn_code')->nullable();
            $table->decimal('cgst_percent', 5, 2)->default(0);
            $table->decimal('sgst_percent', 5, 2)->default(0);
            $table->decimal('cgst_amount', 10, 2)->default(0);
            $table->decimal('sgst_amount', 10, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            
            $table->string('packing')->nullable();
            $table->string('company_name')->nullable();
            $table->integer('row_order')->default(0);
            
            $table->timestamps();
            
            $table->index('item_id', 'stor_items_item_idx');
            $table->index('batch_id', 'stor_items_batch_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_outgoing_return_transaction_items');
    }
};
