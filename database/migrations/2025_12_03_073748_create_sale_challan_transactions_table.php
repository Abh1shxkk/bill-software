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
        // Main Sale Challan Transactions Table
        Schema::create('sale_challan_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('challan_no', 50)->unique();
            $table->string('series', 10)->default('SC');
            $table->date('challan_date');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('salesman_id')->nullable();
            $table->char('cash_flag', 1)->default('N');
            $table->char('transfer_flag', 1)->default('N');
            $table->text('remarks')->nullable();
            $table->decimal('nt_amount', 15, 2)->default(0);
            $table->decimal('sc_amount', 15, 2)->default(0);
            $table->decimal('ft_amount', 15, 2)->default(0);
            $table->decimal('dis_amount', 15, 2)->default(0);
            $table->decimal('scm_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('scm_percent', 8, 3)->default(0);
            $table->decimal('tcs_amount', 15, 2)->default(0);
            $table->decimal('excise_amount', 15, 2)->default(0);
            $table->boolean('is_invoiced')->default(false);
            $table->unsignedBigInteger('sale_transaction_id')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->foreign('salesman_id')->references('id')->on('sales_men')->onDelete('set null');
            $table->foreign('sale_transaction_id')->references('id')->on('sale_transactions')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        // Sale Challan Transaction Items Table
        Schema::create('sale_challan_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_challan_transaction_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('batch_no', 100)->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('qty', 15, 3)->default(0);
            $table->decimal('free_qty', 15, 3)->default(0);
            $table->decimal('sale_rate', 15, 2)->default(0);
            $table->decimal('mrp', 15, 2)->default(0);
            $table->decimal('discount_percent', 8, 3)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('cgst_percent', 8, 3)->default(0);
            $table->decimal('sgst_percent', 8, 3)->default(0);
            $table->decimal('cess_percent', 8, 3)->default(0);
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('cess_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->integer('row_order')->default(0);
            $table->timestamps();

            $table->foreign('sale_challan_transaction_id')->references('id')->on('sale_challan_transactions')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_challan_transaction_items');
        Schema::dropIfExists('sale_challan_transactions');
    }
};
