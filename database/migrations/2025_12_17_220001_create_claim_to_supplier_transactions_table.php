<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_to_supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('claim_no', 20)->unique();
            $table->string('series', 10)->default('CTS');
            $table->date('claim_date');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name', 255)->nullable();
            $table->string('invoice_no', 50)->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('gst_vno', 50)->nullable();
            $table->char('tax_flag', 1)->default('Y');
            $table->string('narration', 500)->nullable();
            
            // Amount fields
            $table->decimal('nt_amount', 15, 2)->default(0);
            $table->decimal('sc_amount', 15, 2)->default(0);
            $table->decimal('dis_amount', 15, 2)->default(0);
            $table->decimal('scm_amount', 15, 2)->default(0);
            $table->decimal('scm_percent', 8, 3)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('tcs_amount', 15, 2)->default(0);
            $table->decimal('dis1_amount', 15, 2)->default(0);
            
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('claim_date');
            $table->index('supplier_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_to_supplier_transactions');
    }
};
