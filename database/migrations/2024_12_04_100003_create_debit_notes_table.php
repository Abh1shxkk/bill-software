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
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('debit_note_no', 50)->unique();
            $table->date('debit_note_date');
            $table->string('day_name', 20)->nullable();
            
            // Debit Party (Supplier/Customer)
            $table->enum('debit_party_type', ['S', 'C'])->default('S'); // S=Supplier, C=Customer
            $table->unsignedBigInteger('debit_party_id')->nullable();
            $table->string('debit_party_name', 255)->nullable();
            
            // Credit Account (Purchase/Sale/General)
            $table->enum('credit_account_type', ['P', 'S', 'G'])->default('P'); // P=Purchase, S=Sale, G=General
            $table->string('credit_account_no', 100)->nullable();
            
            // Reference Details
            $table->string('inv_ref_no', 100)->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('gst_vno', 100)->nullable();
            
            // Party Transaction Details
            $table->string('party_trn_no', 100)->nullable();
            $table->date('party_trn_date')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            
            // Salesman
            $table->unsignedBigInteger('salesman_id')->nullable();
            
            // Reason
            $table->string('reason', 255)->nullable();
            
            // Summary Amounts
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->decimal('total_gst', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('tcs_amount', 15, 2)->default(0);
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('dn_amount', 15, 2)->default(0);
            
            // Narration
            $table->text('narration')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            
            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('debit_note_date');
            $table->index('debit_party_type');
            $table->index('debit_party_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_notes');
    }
};
