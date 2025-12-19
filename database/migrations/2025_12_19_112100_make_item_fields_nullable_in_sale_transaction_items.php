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
        // Make item_code nullable for voucher entries
        Schema::table('sale_transaction_items', function (Blueprint $table) {
            $table->string('item_code', 50)->nullable()->change();
            $table->string('item_name', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes (optional)
    }
};
