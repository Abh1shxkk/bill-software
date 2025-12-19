<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('multi_vouchers', function (Blueprint $table) {
            $table->id();
            $table->date('voucher_date');
            $table->integer('voucher_no');
            $table->text('narration')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->index(['voucher_date', 'voucher_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multi_vouchers');
    }
};
