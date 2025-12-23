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
        Schema::create('breakage_supplier_received_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_name')->nullable();
            $table->string('batch_no')->nullable();
            $table->string('expiry_date')->nullable();
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('free_qty', 10, 2)->default(0);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('dis_percent', 5, 2)->default(0);
            $table->decimal('scm_percent', 5, 2)->default(0);
            $table->enum('br_ex', ['B', 'E'])->default('B')->comment('B=Breakage, E=Expiry');
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('purchase_rate', 10, 2)->nullable();
            $table->decimal('sale_rate', 10, 2)->nullable();
            $table->decimal('cgst', 5, 2)->default(0);
            $table->decimal('sgst', 5, 2)->default(0);
            $table->string('company_name')->nullable();
            $table->string('packing')->nullable();
            $table->string('unit')->nullable();
            $table->string('hsn_code')->nullable();
            $table->timestamps();
            
            $table->foreign('transaction_id', 'bs_recv_trans_fk')->references('id')->on('breakage_supplier_received_transactions')->onDelete('cascade');
            $table->foreign('item_id', 'bs_recv_item_fk')->references('id')->on('items')->onDelete('restrict');
            $table->foreign('batch_id', 'bs_recv_batch_fk')->references('id')->on('batches')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakage_supplier_received_transaction_items');
    }
};
