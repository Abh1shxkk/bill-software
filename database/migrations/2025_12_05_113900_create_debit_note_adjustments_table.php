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
        Schema::create('debit_note_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debit_note_id');
            
            // Adjustment can be against purchase invoice or credit note
            $table->enum('adjustment_type', ['PURCHASE', 'CREDIT_NOTE'])->default('PURCHASE');
            
            // For PURCHASE type - reference to purchase_transactions
            $table->unsignedBigInteger('purchase_transaction_id')->nullable();
            $table->string('purchase_invoice_no', 100)->nullable();
            $table->date('purchase_invoice_date')->nullable();
            $table->decimal('purchase_invoice_amount', 15, 2)->default(0);
            $table->decimal('purchase_balance_amount', 15, 2)->default(0);
            
            // For CREDIT_NOTE type - reference to credit_notes
            $table->unsignedBigInteger('credit_note_id')->nullable();
            $table->string('credit_note_no', 100)->nullable();
            $table->date('credit_note_date')->nullable();
            $table->decimal('credit_note_amount', 15, 2)->default(0);
            $table->decimal('credit_note_balance', 15, 2)->default(0);
            
            // Adjusted amount
            $table->decimal('adjusted_amount', 15, 2)->default(0);
            
            // Remarks
            $table->string('remarks', 255)->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('debit_note_id')->references('id')->on('debit_notes')->onDelete('cascade');
            $table->foreign('purchase_transaction_id')->references('id')->on('purchase_transactions')->onDelete('set null');
            $table->foreign('credit_note_id')->references('id')->on('credit_notes')->onDelete('set null');
            
            // Indexes
            $table->index('debit_note_id');
            $table->index('adjustment_type');
            $table->index('purchase_transaction_id');
            $table->index('credit_note_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_note_adjustments');
    }
};
