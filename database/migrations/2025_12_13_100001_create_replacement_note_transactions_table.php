<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replacement_note_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('rn_no', 20)->unique(); // RN0001, RN0002...
            $table->string('series', 10)->default('RN');
            $table->date('transaction_date');
            $table->string('day_name', 20)->nullable();
            
            // Supplier info
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name', 255)->nullable();
            
            // Pending Br/Expiry and Balance
            $table->decimal('pending_br_expiry', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);
            
            // Summary fields
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('scm_percent', 8, 3)->default(0);
            $table->decimal('scm_amount', 15, 2)->default(0);
            
            // Additional info
            $table->string('pack', 50)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('cl_qty', 15, 2)->default(0);
            $table->string('comp', 100)->nullable();
            $table->string('lctn', 100)->nullable();
            $table->string('srlno', 50)->nullable();
            $table->string('case_no', 50)->nullable();
            $table->string('box', 50)->nullable();
            
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('active');
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            
            $table->index('supplier_id');
            $table->index('transaction_date');
            $table->index('rn_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replacement_note_transactions');
    }
};
