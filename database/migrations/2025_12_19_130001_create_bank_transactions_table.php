<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->integer('transaction_no');
            $table->string('transaction_type', 1)->default('D'); // D = Deposit, W = Withdraw
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('cheque_no', 50)->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('narration')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            
            $table->index(['transaction_date', 'transaction_no']);
            $table->index('transaction_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
