<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'companies', 'customers', 'sale'
            $table->string('display_name'); // e.g., 'Companies', 'Customers', 'Sale Transaction'
            $table->string('group')->nullable(); // e.g., 'Masters', 'Transactions', 'Reports'
            $table->string('icon')->nullable(); // Bootstrap icon class
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
