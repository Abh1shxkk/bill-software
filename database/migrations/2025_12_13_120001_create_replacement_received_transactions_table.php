<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replacement_received_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('rr_no', 50)->unique();
            $table->string('series', 10)->default('RR');
            $table->date('transaction_date');
            $table->string('day_name', 20)->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->decimal('pending_br_expiry', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('scm_percent', 5, 2)->default(0);
            $table->decimal('scm_amount', 10, 2)->default(0);
            $table->string('packing')->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('cl_qty', 10, 2)->default(0);
            $table->string('comp')->nullable();
            $table->string('lctn')->nullable();
            $table->string('srlno')->nullable();
            $table->string('case_no')->nullable();
            $table->string('box')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('set null');
            $table->index('transaction_date');
            $table->index('rr_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replacement_received_transactions');
    }
};
