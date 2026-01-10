<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_schedules', function (Blueprint $table) {
            $table->id();
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->string('time', 5)->default('02:00'); // HH:MM format
            $table->tinyInteger('day_of_week')->nullable(); // 0-6 for weekly (0=Sunday)
            $table->tinyInteger('day_of_month')->nullable(); // 1-28 for monthly
            $table->boolean('compress')->default(true);
            $table->boolean('is_active')->default(false);
            $table->integer('retention_days')->default(30); // Auto-delete backups older than X days
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_schedules');
    }
};
