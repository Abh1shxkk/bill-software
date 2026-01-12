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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('subscription_plans')->onDelete('set null');
            $table->string('license_key', 100)->unique();
            $table->enum('plan_type', ['trial', 'basic', 'standard', 'premium', 'enterprise'])->default('basic');
            $table->integer('max_users')->default(5);
            $table->integer('max_items')->default(1000);
            $table->integer('max_transactions_per_month')->default(10000);
            $table->json('features')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('activated_at')->nullable();
            $table->string('activation_ip', 45)->nullable();
            $table->string('activation_domain')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->index('license_key');
            $table->index('organization_id');
            $table->index('is_active');
            $table->index('expires_at');
            $table->index(['organization_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
