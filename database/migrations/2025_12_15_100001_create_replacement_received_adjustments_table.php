<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replacement_received_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('replacement_received_id');
            $table->unsignedBigInteger('purchase_return_id');
            $table->decimal('adjusted_amount', 15, 2)->default(0);
            $table->date('adjustment_date')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('replacement_received_id')
                ->references('id')
                ->on('replacement_received_transactions')
                ->onDelete('cascade');

            $table->foreign('purchase_return_id')
                ->references('id')
                ->on('purchase_return_transactions')
                ->onDelete('cascade');

            $table->index(['replacement_received_id']);
            $table->index(['purchase_return_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replacement_received_adjustments');
    }
};
