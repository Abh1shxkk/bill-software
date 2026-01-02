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
        Schema::table('customer_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('salesman_id')->nullable()->after('salesman_code');
            $table->unsignedBigInteger('area_id')->nullable()->after('area_code');
            $table->unsignedBigInteger('route_id')->nullable()->after('route_code');
            $table->unsignedBigInteger('coll_boy_id')->nullable()->after('coll_boy_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_receipts', function (Blueprint $table) {
            $table->dropColumn(['salesman_id', 'area_id', 'route_id', 'coll_boy_id']);
        });
    }
};
