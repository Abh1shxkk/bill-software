<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('item_code', 50)->nullable();
            $table->string('item_name', 255)->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->string('expiry_date', 20)->nullable();
            $table->string('packing', 50)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->decimal('qty', 10, 3)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('mrp', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('unit', 20)->nullable();
            $table->integer('row_order')->default(0);
            $table->timestamps();

            // Foreign keys removed for compatibility
            $table->index('quotation_id');
            $table->index('item_id');
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
