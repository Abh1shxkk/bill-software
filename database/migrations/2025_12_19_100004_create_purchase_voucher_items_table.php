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
        Schema::create('purchase_voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_voucher_id')->constrained()->onDelete('cascade');
            
            // HSN based item
            $table->string('hsn_code', 20)->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('cgst_percent', 5, 2)->default(0);
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_percent', 5, 2)->default(0);
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('igst_percent', 5, 2)->default(0);
            $table->decimal('igst_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('purchase_voucher_id');
            $table->index('hsn_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_voucher_items');
    }
};
