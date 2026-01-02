<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('godown_breakage_expiry_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trn_no', 50)->unique();
            $table->string('series', 10)->default('GBE');
            $table->date('transaction_date');
            $table->string('day_name', 20)->nullable();
            $table->text('narration')->nullable();
            $table->decimal('total_qty', 10, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status', 20)->default('completed');
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('godown_breakage_expiry_transactions');
    }
};
