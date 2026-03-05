<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_return_replacement_items', function (Blueprint $table) {
            $table->decimal('qty', 10, 2)->default(0)->change();
            $table->decimal('free_qty', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('sale_return_replacement_items', function (Blueprint $table) {
            $table->integer('qty')->default(0)->change();
            $table->integer('free_qty')->default(0)->change();
        });
    }
};
