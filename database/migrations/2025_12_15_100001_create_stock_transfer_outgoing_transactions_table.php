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
        Schema::create('stock_transfer_outgoing_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('sr_no')->unique();
            $table->string('series', 10)->default('STO');
            $table->date('transaction_date');
            
            // Transfer Details
            $table->string('transfer_to')->nullable(); // Destination/Branch
            $table->string('transfer_to_name')->nullable();
            $table->string('challan_no')->nullable();
            $table->date('challan_date')->nullable();
            
            // Flags
            $table->char('gst_vno', 1)->default('N');
            $table->char('with_gst', 1)->default('N');
            
            // Financial Fields
            $table->decimal('mrp_value', 12, 2)->default(0);
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('scheme_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('active');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('transaction_date');
            $table->index('status');
            $table->index('transfer_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_outgoing_transactions');
    }
};
