<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sale_return_replacement_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('series')->default('RG');
            $table->string('trn_no'); // S.R.No
            $table->date('trn_date');
            $table->enum('is_cash', ['Y', 'N'])->default('N');
            $table->unsignedBigInteger('customer_id')->nullable(); // Assuming "Name" refers to customer
            $table->string('customer_name')->nullable();
            $table->decimal('fixed_discount', 10, 2)->default(0);
            
            // Footer Inputs
            $table->decimal('sc_percent', 5, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('excise', 10, 2)->default(0);
            $table->decimal('tsr', 10, 2)->default(0);
            
            // Footer Totals
            $table->decimal('nt_amt', 15, 2)->default(0); // Net Taxable Amount
            $table->decimal('sc_amt', 15, 2)->default(0); // Surcharge Amount
            $table->decimal('ft_amt', 15, 2)->default(0); // ?
            $table->decimal('dis_amt', 15, 2)->default(0); // Discount Amount
            $table->decimal('scm_amt', 15, 2)->default(0); // Scheme Amount
            $table->decimal('tax_amt', 15, 2)->default(0); // Tax Amount
            $table->decimal('net_amt', 15, 2)->default(0); // Net Amount
            
            $table->text('remarks')->nullable();
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        Schema::create('sale_return_replacement_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('item_id');
            $table->string('item_code');
            $table->string('item_name');
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->integer('qty')->default(0);
            $table->integer('free_qty')->default(0);
            
            $table->decimal('sale_rate', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('ft_rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            
            // Additional Item Details (from bottom panel) if needed to persist
            $table->string('packing')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('mrp', 15, 2)->default(0);
            
            $table->timestamps();
            
            $table->foreign('transaction_id')->references('id')->on('sale_return_replacement_transactions')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_return_replacement_items');
        Schema::dropIfExists('sale_return_replacement_transactions');
    }
};
