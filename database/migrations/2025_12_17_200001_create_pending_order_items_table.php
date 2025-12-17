<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_code', 50)->nullable();
            $table->string('item_name', 255)->nullable();
            $table->enum('action_type', ['I', 'D'])->default('I')->comment('I=Insert, D=Delete');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('item_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_order_items');
    }
};
