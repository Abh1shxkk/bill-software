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
        Schema::create('voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
            $table->string('account_type', 20)->nullable(); // GL, CB, SL, PL, CL, SU
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('account_code', 50)->nullable();
            $table->string('account_name', 255)->nullable();
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->text('item_narration')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('voucher_id');
            $table->index('account_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_items');
    }
};
