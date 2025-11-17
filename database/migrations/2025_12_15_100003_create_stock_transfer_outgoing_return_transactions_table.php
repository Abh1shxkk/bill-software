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
        Schema::create('stock_transfer_outgoing_return_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('sr_no')->unique();
            $table->string('series', 10)->default('STOR');
            $table->date('transaction_date');
            
            // Reference to original outgoing transfer
            $table->unsignedBigInteger('original_transfer_id')->nullable();
            $table->foreign('original_transfer_id', 'stor_original_transfer_fk')->references('id')->on('stock_transfer_outgoing_transactions')->onDelete('set null');
            $table->string('original_sr_no')->nullable();
            
            // Transfer Details
            $table->string('transfer_from')->nullable(); // Source/Branch
            $table->string('transfer_from_name')->nullable();
            $table->string('trf_return_no')->nullable();
            $table->string('challan_no')->nullable();
            $table->date('challan_date')->nullable();
            $table->integer('cases')->default(0);
            $table->string('transport')->nullable();
            
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
            
            $table->index('transaction_date', 'stor_trans_date_idx');
            $table->index('status', 'stor_status_idx');
            $table->index('transfer_from', 'stor_transfer_from_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_outgoing_return_transactions');
    }
};
