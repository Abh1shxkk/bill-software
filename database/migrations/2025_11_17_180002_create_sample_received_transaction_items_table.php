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
        Schema::create('sample_received_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_received_transaction_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            
            // Item details
            $table->string('item_code', 50)->nullable();
            $table->string('item_name', 150)->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->string('expiry', 20)->nullable();
            $table->date('expiry_date')->nullable();
            
            // Quantities
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('free_qty', 10, 2)->default(0);
            
            // Rates
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            
            // Additional item info
            $table->string('packing', 50)->nullable();
            $table->string('unit', 10)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('hsn_code', 20)->nullable();
            
            // Row order for display
            $table->integer('row_order')->default(0);
            
            $table->timestamps();

            // Foreign keys with shorter names
            $table->foreign('sample_received_transaction_id', 'sr_trans_items_trans_id_fk')
                  ->references('id')
                  ->on('sample_received_transactions')
                  ->onDelete('cascade');
                  
            $table->foreign('item_id', 'sr_trans_items_item_id_fk')
                  ->references('id')
                  ->on('items')
                  ->onDelete('restrict');
                  
            $table->foreign('batch_id', 'sr_trans_items_batch_id_fk')
                  ->references('id')
                  ->on('batches')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_received_transaction_items');
    }
};
