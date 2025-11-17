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
        Schema::create('purchase_return_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('pr_no')->unique(); // Purchase Return Number (PR0001, PR0002...)
            $table->string('series', 10)->default('PR'); // Series code
            $table->date('return_date'); // Return Date
            
            // Supplier Details
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers', 'supplier_id')->onDelete('set null');
            $table->string('supplier_name')->nullable();
            
            // Invoice Details
            $table->string('invoice_no')->nullable(); // Supplier's invoice number
            $table->date('invoice_date')->nullable(); // Supplier's invoice date
            $table->string('gst_vno')->nullable(); // GST voucher number
            
            // Flags
            $table->char('tax_flag', 1)->default('Y'); // Tax applicable (Y/N)
            $table->char('rate_diff_flag', 1)->default('N'); // Rate difference flag
            
            // Financial Fields
            $table->decimal('nt_amount', 15, 2)->default(0); // Net Taxable Amount
            $table->decimal('sc_amount', 15, 2)->default(0); // Special Charge Amount
            $table->decimal('dis_amount', 15, 2)->default(0); // Discount Amount
            $table->decimal('scm_amount', 15, 2)->default(0); // Scheme Amount
            $table->decimal('scm_percent', 10, 3)->default(0); // Scheme Percentage
            $table->decimal('tax_amount', 15, 2)->default(0); // Total Tax Amount
            $table->decimal('net_amount', 15, 2)->default(0); // Net Return Amount
            $table->decimal('tcs_amount', 15, 2)->default(0); // TCS Amount
            $table->decimal('dis1_amount', 15, 2)->default(0); // Discount 1 Amount
            
            // Additional Fields
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('active'); // active, cancelled
            
            // Audit Fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('return_date');
            $table->index('supplier_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_transactions');
    }
};
