<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replacement_note_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('replacement_note_transaction_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            
            $table->string('item_code', 50)->nullable();
            $table->string('item_name', 255)->nullable();
            $table->string('batch_no', 100)->nullable();
            $table->string('expiry', 20)->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('mrp', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            
            // Additional item info
            $table->string('packing', 50)->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('hsn_code', 20)->nullable();
            
            $table->integer('row_order')->default(0);
            $table->timestamps();
            
            $table->foreign('replacement_note_transaction_id', 'rn_trx_items_rn_trx_id_fk')
                  ->references('id')
                  ->on('replacement_note_transactions')
                  ->onDelete('cascade');
                  
            $table->index('item_id');
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replacement_note_transaction_items');
    }
};
