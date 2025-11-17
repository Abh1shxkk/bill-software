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
        Schema::create('purchase_challan_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_challan_transaction_id');
            $table->foreign('purchase_challan_transaction_id', 'pct_items_pct_id_fk')->references('id')->on('purchase_challan_transactions')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id', 'pct_items_item_id_fk')->references('id')->on('items')->onDelete('restrict');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->foreign('batch_id', 'pct_items_batch_id_fk')->references('id')->on('batches')->onDelete('set null');
            $table->string('batch_no', 50)->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('qty', 15, 3)->default(0);
            $table->decimal('free_qty', 15, 3)->default(0);
            $table->decimal('purchase_rate', 15, 2)->default(0);
            $table->decimal('mrp', 15, 2)->default(0);
            $table->decimal('sale_rate', 15, 2)->default(0);
            $table->decimal('discount_percent', 8, 3)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('cgst_percent', 8, 3)->default(0);
            $table->decimal('sgst_percent', 8, 3)->default(0);
            $table->decimal('cess_percent', 8, 3)->default(0);
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('cess_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->integer('row_order')->default(0);
            $table->timestamps();
            
            $table->index('item_id');
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_challan_transaction_items');
    }
};
