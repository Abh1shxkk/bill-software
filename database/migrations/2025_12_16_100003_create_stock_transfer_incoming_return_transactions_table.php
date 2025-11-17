<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_incoming_return_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trn_no', 50)->unique();
            $table->string('series', 10)->default('STIR');
            $table->date('transaction_date');
            $table->string('day_name', 20)->nullable();
            $table->string('name')->nullable();
            $table->string('gr_no', 50)->nullable();
            $table->date('gr_date')->nullable();
            $table->string('cases', 20)->nullable();
            $table->string('transport')->nullable();
            $table->text('remarks')->nullable();
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->string('packing')->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('cl_qty', 10, 2)->default(0);
            $table->string('comp')->nullable();
            $table->string('lctn')->nullable();
            $table->string('srlno')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('transaction_date');
            $table->index('trn_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_incoming_return_transactions');
    }
};
