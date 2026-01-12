<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the auto_backup_logs table to track automated daily backups
     * for each organization. Implements rolling 7-day backup retention.
     */
    public function up(): void
    {
        Schema::create('auto_backup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->string('day_of_week', 10); // monday, tuesday, etc.
            $table->string('backup_filename')->nullable();
            $table->string('backup_path')->nullable();
            $table->bigInteger('backup_size')->default(0); // in bytes
            $table->enum('status', ['success', 'failed', 'in_progress'])->default('in_progress');
            $table->text('error_message')->nullable();
            $table->date('backup_date'); // The date this backup represents
            $table->timestamps();
            
            // Foreign key for user
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            
            // Index for quick lookups
            $table->index(['organization_id', 'day_of_week']);
            $table->index(['organization_id', 'backup_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_backup_logs');
    }
};
