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
        Schema::create('sale_return_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('sr_no')->unique(); // Sale Return Number
            $table->string('series', 10)->default('SR'); // Series (SR, etc.)
            $table->date('return_date'); // Return Date
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('customer_name')->nullable();
            $table->foreignId('salesman_id')->nullable()->constrained('sales_men')->onDelete('set null');
            $table->string('salesman_name')->nullable();
            
            // Original Invoice Details
            $table->string('original_invoice_no')->nullable();
            $table->date('original_invoice_date')->nullable();
            
            // Flags
            $table->char('rate_diff_flag', 1)->default('N'); // Rate Difference
            $table->char('cash_flag', 1)->default('N'); // Cash
            $table->char('tax_flag', 1)->default('N'); // Tax
            
            // Financial Fields
            $table->decimal('fixed_discount', 10, 2)->default(0);
            $table->decimal('nt_amount', 10, 2)->default(0); // Net Taxable Amount
            $table->decimal('sc_amount', 10, 2)->default(0); // Special Charge Amount
            $table->decimal('ft_amount', 10, 2)->default(0); // Freight Amount
            $table->decimal('dis_amount', 10, 2)->default(0); // Discount Amount
            $table->decimal('scm_amount', 10, 2)->default(0); // Scheme Amount
            $table->decimal('tax_amount', 10, 2)->default(0); // Tax Amount
            $table->decimal('net_amount', 10, 2)->default(0); // Net Amount
            $table->decimal('scm_percent', 10, 3)->default(0); // Scheme Percentage
            $table->decimal('tcs_amount', 10, 2)->default(0); // TCS Amount
            $table->decimal('excise_amount', 10, 2)->default(0); // Excise Amount
            
            // Additional Fields
            $table->decimal('packing', 10, 2)->default(0);
            $table->decimal('unit', 10, 2)->default(0);
            $table->decimal('cl_qty', 10, 2)->default(0); // Closing Quantity
            $table->string('location')->nullable();
            $table->decimal('hs_amount', 10, 2)->default(0); // HS Amount
            
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('active'); // active, cancelled
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('return_date');
            $table->index('customer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_transactions');
    }
};
