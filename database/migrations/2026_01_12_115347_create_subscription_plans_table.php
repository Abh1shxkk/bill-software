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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->integer('max_users')->default(5);
            $table->integer('max_items')->default(1000);
            $table->integer('max_transactions_per_month')->default(10000);
            $table->integer('validity_days')->default(30);
            $table->json('features')->nullable(); // {"reports":true,"backup":true,"multi_godown":false}
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
        
        // Seed default plans
        DB::table('subscription_plans')->insert([
            [
                'name' => 'Trial',
                'code' => 'trial',
                'description' => '14-day free trial with limited features',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_users' => 2,
                'max_items' => 100,
                'max_transactions_per_month' => 500,
                'validity_days' => 14,
                'features' => json_encode(['reports' => true, 'backup' => false, 'multi_godown' => false]),
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Basic',
                'code' => 'basic',
                'description' => 'For small medical stores',
                'price_monthly' => 999,
                'price_yearly' => 9999,
                'max_users' => 3,
                'max_items' => 500,
                'max_transactions_per_month' => 2000,
                'validity_days' => 30,
                'features' => json_encode(['reports' => true, 'backup' => true, 'multi_godown' => false]),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard',
                'code' => 'standard',
                'description' => 'For medium medical stores',
                'price_monthly' => 1999,
                'price_yearly' => 19999,
                'max_users' => 5,
                'max_items' => 2000,
                'max_transactions_per_month' => 10000,
                'validity_days' => 30,
                'features' => json_encode(['reports' => true, 'backup' => true, 'multi_godown' => true]),
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'code' => 'premium',
                'description' => 'For large medical distributors',
                'price_monthly' => 4999,
                'price_yearly' => 49999,
                'max_users' => 15,
                'max_items' => 10000,
                'max_transactions_per_month' => 50000,
                'validity_days' => 30,
                'features' => json_encode(['reports' => true, 'backup' => true, 'multi_godown' => true, 'api_access' => true]),
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'code' => 'enterprise',
                'description' => 'Unlimited access for enterprise customers',
                'price_monthly' => 9999,
                'price_yearly' => 99999,
                'max_users' => 999,
                'max_items' => 999999,
                'max_transactions_per_month' => 999999,
                'validity_days' => 365,
                'features' => json_encode(['reports' => true, 'backup' => true, 'multi_godown' => true, 'api_access' => true, 'white_label' => true]),
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
