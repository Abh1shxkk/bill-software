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
        Schema::create('sale_return_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id'); // The sale return transaction
            $table->unsignedBigInteger('sale_transaction_id'); // The original sale invoice being adjusted
            $table->decimal('adjusted_amount', 15, 2)->default(0); // Amount adjusted against this invoice
            $table->timestamps();

            // Foreign keys
            $table->foreign('sale_return_id')->references('id')->on('sale_return_transactions')->onDelete('cascade');
            $table->foreign('sale_transaction_id')->references('id')->on('sale_transactions')->onDelete('cascade');

            // Indexes
            $table->index('sale_return_id');
            $table->index('sale_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_adjustments');
    }
};
