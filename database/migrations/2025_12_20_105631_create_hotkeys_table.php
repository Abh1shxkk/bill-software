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
        Schema::create('hotkeys', function (Blueprint $table) {
            $table->id();
            $table->string('key_combination', 50)->unique(); // e.g., 'ctrl+f1', 'alt+s'
            $table->string('module_name', 100); // e.g., 'Sale Transaction', 'Items'
            $table->string('route_name', 150); // e.g., 'admin.sale.transaction'
            $table->string('category', 50); // e.g., 'masters', 'transactions', 'reports', 'utilities'
            $table->enum('scope', ['global', 'index'])->default('global'); // global = navigation, index = blade-specific
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(true); // system hotkeys can't be deleted, only modified
            $table->timestamps();
            
            $table->index('category');
            $table->index('scope');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotkeys');
    }
};