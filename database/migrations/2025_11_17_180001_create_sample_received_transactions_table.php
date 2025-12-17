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
        Schema::create('sample_received_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trn_no', 50)->unique();
            $table->string('series', 10)->default('SR');
            $table->date('transaction_date');
            $table->string('day_name', 20)->nullable();
            
            // Party Type: CUSTOMER, DOCTOR, SALES MAN, AREA MGR., REG.MGR., MKT.MGR.
            $table->string('party_type', 20)->nullable();
            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_name', 100)->nullable();
            
            // Transport details
            $table->string('gr_no', 50)->nullable();
            $table->date('gr_date')->nullable();
            $table->integer('cases')->default(0);
            $table->string('road_permit_no', 50)->nullable();
            $table->string('truck_no', 50)->nullable();
            $table->string('transport', 100)->nullable();
            
            // Additional fields
            $table->text('remarks')->nullable();
            $table->string('on_field', 10)->nullable();
            $table->decimal('rate', 10, 2)->default(0);
            $table->string('tag', 20)->nullable();
            
            // Totals
            $table->decimal('total_qty', 10, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            
            // Status
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->tinyInteger('is_deleted')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_received_transactions');
    }
};
