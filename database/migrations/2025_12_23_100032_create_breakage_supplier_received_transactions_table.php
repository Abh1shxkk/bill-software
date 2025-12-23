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
        Schema::create('breakage_supplier_received_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trn_no')->unique();
            $table->date('transaction_date');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->text('narration')->nullable();
            $table->enum('note_type', ['C', 'R'])->default('C')->comment('C=Credit, R=Replace');
            $table->char('tax_flag', 1)->default('N');
            $table->char('inc_flag', 1)->default('N');
            $table->string('gst_vno')->nullable();
            $table->integer('dis_count')->default(0);
            $table->integer('rpl_count')->default(0);
            $table->integer('brk_count')->default(0);
            $table->integer('exp_count')->default(0);
            $table->decimal('total_nt_amt', 12, 2)->default(0);
            $table->decimal('total_sc', 12, 2)->default(0);
            $table->decimal('total_dis_amt', 12, 2)->default(0);
            $table->decimal('total_scm_amt', 12, 2)->default(0);
            $table->decimal('total_half_scm', 12, 2)->default(0);
            $table->decimal('total_tax', 12, 2)->default(0);
            $table->decimal('total_inv_amt', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('supplier_id', 'bs_recv_supplier_fk')->references('supplier_id')->on('suppliers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakage_supplier_received_transactions');
    }
};
