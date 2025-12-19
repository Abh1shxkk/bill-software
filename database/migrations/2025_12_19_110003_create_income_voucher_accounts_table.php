<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_voucher_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('income_voucher_id');
            $table->string('account_type', 10)->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('account_code', 50)->nullable();
            $table->string('account_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('income_voucher_id')->references('id')->on('income_vouchers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_voucher_accounts');
    }
};
