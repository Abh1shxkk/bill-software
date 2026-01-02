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
        Schema::create('breakage_supplier_received_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('received_transaction_id');
            $table->unsignedBigInteger('purchase_transaction_id');
            $table->decimal('adjusted_amount', 15, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('received_transaction_id', 'bsr_adj_received_fk')->references('id')->on('breakage_supplier_received_transactions')->onDelete('cascade');
            $table->foreign('purchase_transaction_id', 'bsr_adj_purchase_fk')->references('id')->on('purchase_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakage_supplier_received_adjustments');
    }
};
