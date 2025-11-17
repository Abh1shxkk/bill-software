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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('trn_no', 50)->unique();
            $table->date('adjustment_date');
            $table->string('day_name', 20)->nullable();
            $table->text('remarks')->nullable();
            
            // Summary fields
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->integer('shortage_items')->default(0);
            $table->integer('excess_items')->default(0);
            
            // Status
            $table->enum('status', ['active', 'cancelled'])->default('active');
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_adjustment_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            
            // Item details
            $table->string('item_code', 100)->nullable();
            $table->string('item_name', 255);
            $table->string('batch_no', 100)->nullable();
            $table->date('expiry_date')->nullable();
            
            // Adjustment type: S = Shortage (minus), E = Excess (plus)
            $table->enum('adjustment_type', ['S', 'E'])->default('S');
            
            // Quantities
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            
            // Additional details
            $table->string('packing', 100)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->decimal('mrp', 15, 2)->default(0);
            
            // Row order for display
            $table->integer('row_order')->default(0);
            
            $table->timestamps();
            
            $table->foreign('stock_adjustment_id')->references('id')->on('stock_adjustments')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
    }
};
