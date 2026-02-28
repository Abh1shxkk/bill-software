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
        Schema::table('purchase_return_transaction_items', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable()->after('spl_rate');
            $table->decimal('gst_percent', 5, 2)->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_return_transaction_items', function (Blueprint $table) {
            $table->dropColumn(['amount', 'gst_percent']);
        });
    }
};
