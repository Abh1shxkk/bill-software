<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('multi_voucher_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('multi_voucher_id');
            $table->date('entry_date');
            $table->string('debit_account_type', 20)->nullable(); // Customer, Supplier, General
            $table->unsignedBigInteger('debit_account_id')->nullable();
            $table->string('debit_account_name')->nullable();
            $table->string('credit_account_type', 20)->nullable();
            $table->unsignedBigInteger('credit_account_id')->nullable();
            $table->string('credit_account_name')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('dr_slcd', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('multi_voucher_id')->references('id')->on('multi_vouchers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multi_voucher_entries');
    }
};
