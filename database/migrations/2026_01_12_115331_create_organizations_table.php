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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('India');
            $table->string('pin_code', 20)->nullable();
            $table->string('gst_no', 50)->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->string('dl_no', 100)->nullable();
            $table->string('dl_no_1', 100)->nullable();
            $table->string('food_license', 100)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('timezone', 50)->default('Asia/Kolkata');
            $table->string('currency', 10)->default('INR');
            $table->string('date_format', 20)->default('d-m-Y');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
