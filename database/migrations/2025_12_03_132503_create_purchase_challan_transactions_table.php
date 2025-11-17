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
        Schema::create('purchase_challan_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('challan_no', 50)->unique();
            $table->string('series', 10)->default('PC');
            $table->date('challan_date');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('restrict');
            $table->string('supplier_invoice_no', 100)->nullable();
            $table->date('supplier_invoice_date')->nullable();
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
            $table->unsignedBigInteger('purchase_transaction_id')->nullable();
            $table->foreign('purchase_transaction_id')->references('id')->on('purchase_transactions')->onDelete('set null');
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['supplier_id', 'is_invoiced']);
            $table->index('challan_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_challan_transactions');
    }
};
