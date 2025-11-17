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
        Schema::create('purchase_return_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_return_id'); // The purchase return transaction
            $table->unsignedBigInteger('purchase_transaction_id'); // The original purchase invoice being adjusted
            $table->decimal('adjusted_amount', 15, 2)->default(0); // Amount adjusted against this invoice
            $table->date('adjustment_date')->nullable(); // Date of adjustment
            $table->string('created_by')->nullable(); // User who created the adjustment
            $table->timestamps();

            // Foreign keys
            $table->foreign('purchase_return_id')->references('id')->on('purchase_return_transactions')->onDelete('cascade');
            $table->foreign('purchase_transaction_id')->references('id')->on('purchase_transactions')->onDelete('cascade');

            // Indexes
            $table->index('purchase_return_id');
            $table->index('purchase_transaction_id');
            $table->index('adjustment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_adjustments');
    }
};
