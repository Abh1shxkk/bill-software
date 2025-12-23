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
        Schema::create('breakage_supplier_unused_dump_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trn_no')->unique();
            $table->date('transaction_date');
            $table->text('narration')->nullable();
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakage_supplier_unused_dump_transactions');
    }
};
