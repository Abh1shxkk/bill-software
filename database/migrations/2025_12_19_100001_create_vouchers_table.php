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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->integer('voucher_no');
            $table->date('voucher_date');
            $table->string('day_name', 20)->nullable();
            $table->enum('voucher_type', ['receipt', 'payment', 'contra', 'journal'])->default('receipt');
            $table->boolean('multi_narration')->default(false);
            $table->text('narration')->nullable();
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['voucher_no', 'voucher_type']);
            $table->index('voucher_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
