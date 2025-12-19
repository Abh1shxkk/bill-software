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
        Schema::create('purchase_voucher_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_voucher_id')->constrained()->onDelete('cascade');
            
            // Account (Code/Name grid)
            $table->string('account_type', 20)->nullable(); // GL, PL, etc.
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('account_code', 50)->nullable();
            $table->string('account_name', 255)->nullable();
            
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('purchase_voucher_id');
            $table->index('account_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_voucher_accounts');
    }
};
