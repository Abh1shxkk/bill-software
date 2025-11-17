<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_incoming_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_incoming_transaction_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('item_code', 50)->nullable();
            $table->string('item_name')->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->string('expiry', 20)->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('free_qty', 10, 2)->default(0);
            $table->decimal('p_rate', 10, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('ft_rate', 10, 2)->default(0);
            $table->decimal('ft_amount', 12, 2)->default(0);
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('s_rate', 10, 2)->default(0);
            $table->string('packing')->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('company_name')->nullable();
            $table->string('hsn_code', 20)->nullable();
            $table->integer('row_order')->default(0);
            $table->timestamps();

            $table->foreign('stock_transfer_incoming_transaction_id', 'sti_transaction_id_foreign')
                  ->references('id')
                  ->on('stock_transfer_incoming_transactions')
                  ->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_incoming_transaction_items');
    }
};
