<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_items', 'batch_id')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('item_id');
            }
            if (!Schema::hasColumn('quotation_items', 'batch_no')) {
                $table->string('batch_no', 100)->nullable()->after('item_name');
            }
            if (!Schema::hasColumn('quotation_items', 'expiry_date')) {
                $table->string('expiry_date', 50)->nullable()->after('batch_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn(['batch_id', 'batch_no', 'expiry_date']);
        });
    }
};
