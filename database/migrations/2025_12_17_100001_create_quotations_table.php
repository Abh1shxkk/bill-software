<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_no', 50)->unique();
            $table->string('series', 10)->default('QT');
            $table->date('quotation_date');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->text('terms')->nullable();
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('customer_id');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
