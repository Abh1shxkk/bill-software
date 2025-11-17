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
        Schema::create('breakage_expiry_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('sr_no')->unique(); // Serial Number
            $table->string('series', 10)->default('BE'); // Series (BE for Breakage/Expiry)
            $table->date('transaction_date'); // Transaction Date
            $table->date('end_date')->nullable(); // End Date
            
            // Customer and Salesman
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('customer_name')->nullable();
            $table->foreignId('salesman_id')->nullable()->constrained('sales_men')->onDelete('set null');
            $table->string('salesman_name')->nullable();
            
            // Flags
            $table->char('gst_vno', 1)->default('N'); // GST Voucher Number
            $table->char('note_type', 1)->default('N'); // R(epl.) / C(redit) Note
            $table->char('with_gst', 1)->default('N'); // With GST [Y/N]
            $table->char('inc', 1)->default('N'); // Inc.
            $table->char('rev_charge', 1)->default('Y'); // Reverse Charge
            $table->char('adjusted', 1)->default('X'); // To be Adjusted? [Y/N], <X> for Imm. Posting
            
            // Additional Fields
            $table->string('dis_rpl')->nullable(); // Dis. Rpl
            $table->string('brk')->nullable(); // Brk.
            $table->string('exp')->nullable(); // Exp.
            
            // Financial Fields - Summary Section
            $table->decimal('mrp_value', 10, 2)->default(0); // MRP Value
            $table->decimal('gross_amount', 10, 2)->default(0); // Gross Amount
            $table->decimal('discount_amount', 10, 2)->default(0); // Discount Amount
            $table->decimal('scheme_amount', 10, 2)->default(0); // Scheme Amount
            $table->decimal('tax_amount', 10, 2)->default(0); // Tax Amount
            $table->decimal('net_amount', 10, 2)->default(0); // Net Amount
            
            // Additional Details Section
            $table->decimal('packing', 10, 2)->default(0);
            $table->decimal('unit', 10, 2)->default(0);
            $table->decimal('cl_qty', 10, 2)->default(0); // Closing Quantity
            $table->decimal('scm_amt', 10, 2)->default(0); // Scheme Amount
            $table->decimal('dis_amt', 10, 2)->default(0); // Discount Amount
            $table->decimal('subtotal', 10, 2)->default(0); // Sub Total
            $table->decimal('tax_amt', 10, 2)->default(0); // Tax Amount
            $table->decimal('net_amt', 10, 2)->default(0); // Net Amount
            
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('active'); // active, cancelled
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('transaction_date');
            $table->index('customer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakage_expiry_transactions');
    }
};
