<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_to_supplier_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_to_supplier_transaction_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('item_code', 50)->nullable();
            $table->string('item_name', 255)->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('free_qty', 15, 2)->default(0);
            $table->decimal('pur_rate', 15, 2)->default(0);
            $table->decimal('dis_percent', 8, 2)->default(0);
            $table->decimal('ft_rate', 15, 2)->default(0);
            $table->decimal('ft_amount', 15, 2)->default(0);
            $table->decimal('mrp', 15, 2)->default(0);
            $table->decimal('ws_rate', 15, 2)->default(0);
            $table->decimal('s_rate', 15, 2)->default(0);
            $table->decimal('spl_rate', 15, 2)->default(0);
            
            // Tax fields
            $table->decimal('cgst_percent', 8, 2)->default(0);
            $table->decimal('sgst_percent', 8, 2)->default(0);
            $table->decimal('cess_percent', 8, 2)->default(0);
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('cess_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            
            $table->string('hsn_code', 20)->nullable();
            $table->string('packing', 50)->nullable();
            $table->string('unit', 20)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->integer('row_order')->default(0);
            
            $table->timestamps();
            
            $table->foreign('claim_to_supplier_transaction_id', 'cts_items_transaction_id_fk')
                ->references('id')
                ->on('claim_to_supplier_transactions')
                ->onDelete('cascade');
                
            $table->index('item_id');
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_to_supplier_transaction_items');
    }
};
