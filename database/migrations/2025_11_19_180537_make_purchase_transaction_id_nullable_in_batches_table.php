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
        Schema::table('batches', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_transaction_id')->nullable()->change();
            $table->unsignedBigInteger('purchase_transaction_item_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_transaction_id')->nullable(false)->change();
            $table->unsignedBigInteger('purchase_transaction_item_id')->nullable(false)->change();
        });
    }
};
