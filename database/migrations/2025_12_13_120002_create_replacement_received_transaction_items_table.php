<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replacement_received_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('replacement_received_transaction_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('item_code', 50)->nullable();
            $table->string('item_name')->nullable();
            $table->string('batch_no', 100)->nullable();
            $table->string('expiry', 20)->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('free_qty', 10, 2)->default(0);
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('ft_rate', 10, 2)->default(0);
            $table->decimal('ft_amount', 10, 2)->default(0);
            $table->string('packing')->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('company_name')->nullable();
            $table->string('hsn_code', 50)->nullable();
            $table->integer('row_order')->default(0);
            $table->timestamps();

            $table->foreign('replacement_received_transaction_id', 'rr_transaction_fk')
                ->references('id')
                ->on('replacement_received_transactions')
                ->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
            
            $table->index('replacement_received_transaction_id', 'rr_trans_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replacement_received_transaction_items');
    }
};
