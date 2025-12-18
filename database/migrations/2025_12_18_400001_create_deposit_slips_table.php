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
        Schema::create('deposit_slips', function (Blueprint $table) {
            $table->id();
            $table->integer('slip_no');
            $table->date('deposit_date');
            $table->date('clearing_date')->nullable();
            $table->date('payin_slip_date')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('cheque_no');
            $table->date('cheque_date')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'posted'])->default('pending');
            $table->date('posted_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['cheque_no', 'customer_id']);
            $table->index('deposit_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_slips');
    }
};
