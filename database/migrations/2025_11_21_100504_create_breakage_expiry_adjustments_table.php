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
        Schema::create('breakage_expiry_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('breakage_expiry_transaction_id'); // The breakage/expiry transaction
            $table->unsignedBigInteger('sale_transaction_id'); // The original sale invoice being adjusted
            $table->decimal('adjusted_amount', 15, 2)->default(0); // Amount adjusted against this invoice
            $table->date('adjustment_date'); // Date of adjustment
            $table->string('created_by')->nullable(); // User who created the adjustment
            $table->timestamps();

            // Foreign keys with custom names
            $table->foreign('breakage_expiry_transaction_id', 'be_adj_be_trans_id_foreign')->references('id')->on('breakage_expiry_transactions')->onDelete('cascade');
            $table->foreign('sale_transaction_id', 'be_adj_sale_trans_id_foreign')->references('id')->on('sale_transactions')->onDelete('cascade');

            // Indexes
            $table->index('breakage_expiry_transaction_id');
            $table->index('sale_transaction_id');
            $table->index('adjustment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakage_expiry_adjustments');
    }
};
