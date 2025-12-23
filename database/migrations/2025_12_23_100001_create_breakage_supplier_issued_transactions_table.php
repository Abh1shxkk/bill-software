<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breakage_supplier_issued_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trn_no', 50)->unique();
            $table->string('series', 10)->default('BSI');
            $table->date('transaction_date');
            $table->string('day_name', 20)->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name', 255)->nullable();
            $table->string('note_type', 1)->default('C')->comment('R=Replacement, C=Credit');
            $table->string('tax_flag', 1)->default('N');
            $table->string('inc_flag', 1)->default('N');
            $table->string('gst_vno', 50)->nullable();
            $table->decimal('dis_count', 10, 2)->default(0);
            $table->decimal('rpl_count', 10, 2)->default(0);
            $table->decimal('brk_count', 10, 2)->default(0);
            $table->decimal('exp_count', 10, 2)->default(0);
            $table->string('narration', 500)->nullable();
            $table->decimal('total_nt_amt', 15, 2)->default(0);
            $table->decimal('total_sc', 15, 2)->default(0);
            $table->decimal('total_dis_amt', 15, 2)->default(0);
            $table->decimal('total_scm_amt', 15, 2)->default(0);
            $table->decimal('total_half_scm', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_inv_amt', 15, 2)->default(0);
            $table->decimal('total_qty', 15, 2)->default(0);
            $table->string('status', 20)->default('completed');
            $table->boolean('is_deleted')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->index('transaction_date');
            $table->index('supplier_id');
            $table->index('status');
        });

        Schema::create('breakage_supplier_issued_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->string('item_code', 50)->nullable();
            $table->string('item_name', 255)->nullable();
            $table->string('batch_no', 100)->nullable();
            $table->string('expiry', 20)->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('free_qty', 15, 2)->default(0);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('dis_percent', 10, 2)->default(0);
            $table->decimal('scm_percent', 10, 2)->default(0);
            $table->string('br_ex_type', 20)->default('BREAKAGE')->comment('BREAKAGE or EXPIRY');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('nt_amt', 15, 2)->default(0);
            $table->decimal('dis_amt', 15, 2)->default(0);
            $table->decimal('scm_amt', 15, 2)->default(0);
            $table->decimal('half_scm', 15, 2)->default(0);
            $table->decimal('tax_amt', 15, 2)->default(0);
            $table->decimal('net_amt', 15, 2)->default(0);
            $table->string('packing', 50)->nullable();
            $table->string('unit', 20)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->decimal('mrp', 15, 2)->default(0);
            $table->decimal('p_rate', 15, 2)->default(0);
            $table->decimal('s_rate', 15, 2)->default(0);
            $table->string('hsn_code', 20)->nullable();
            $table->decimal('cgst_percent', 10, 2)->default(0);
            $table->decimal('sgst_percent', 10, 2)->default(0);
            $table->decimal('cgst_amt', 15, 2)->default(0);
            $table->decimal('sgst_amt', 15, 2)->default(0);
            $table->decimal('sc_percent', 10, 2)->default(0);
            $table->decimal('tax_percent', 10, 2)->default(0);
            $table->integer('row_order')->default(0);
            $table->timestamps();
            
            $table->foreign('transaction_id', 'bsi_items_transaction_id_fk')->references('id')->on('breakage_supplier_issued_transactions')->onDelete('cascade');
            $table->index('item_id');
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breakage_supplier_issued_transaction_items');
        Schema::dropIfExists('breakage_supplier_issued_transactions');
    }
};
