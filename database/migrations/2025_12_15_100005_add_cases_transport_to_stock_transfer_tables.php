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
        // Add to stock_transfer_outgoing_transactions
        Schema::table('stock_transfer_outgoing_transactions', function (Blueprint $table) {
            $table->integer('cases')->default(0)->after('challan_date');
            $table->string('transport')->nullable()->after('cases');
        });

        // Add to stock_transfer_outgoing_return_transactions
        Schema::table('stock_transfer_outgoing_return_transactions', function (Blueprint $table) {
            $table->integer('cases')->default(0)->after('challan_date');
            $table->string('transport')->nullable()->after('cases');
            $table->string('return_from')->nullable()->after('transfer_from_name');
            $table->string('return_from_name')->nullable()->after('return_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transfer_outgoing_transactions', function (Blueprint $table) {
            $table->dropColumn(['cases', 'transport']);
        });

        Schema::table('stock_transfer_outgoing_return_transactions', function (Blueprint $table) {
            $table->dropColumn(['cases', 'transport', 'return_from', 'return_from_name']);
        });
    }
};
