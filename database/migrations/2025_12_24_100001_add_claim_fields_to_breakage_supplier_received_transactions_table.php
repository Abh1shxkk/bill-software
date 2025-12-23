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
        Schema::table('breakage_supplier_received_transactions', function (Blueprint $table) {
            $table->string('series')->nullable()->after('trn_no');
            $table->string('party_trn_no')->nullable()->after('supplier_name');
            $table->date('party_date')->nullable()->after('party_trn_no');
            $table->unsignedBigInteger('claim_transaction_id')->nullable()->after('party_date');
            $table->char('claim_flag', 1)->default('N')->after('claim_transaction_id');
            $table->boolean('received_as_debit_note')->default(false)->after('claim_flag');
            $table->decimal('claim_amount', 12, 2)->default(0)->after('received_as_debit_note');
            $table->decimal('gross_amt', 12, 2)->default(0)->after('total_inv_amt');
            $table->decimal('total_gst', 12, 2)->default(0)->after('gross_amt');
            $table->decimal('net_amt', 12, 2)->default(0)->after('total_gst');
            $table->decimal('round_off', 10, 2)->default(0)->after('net_amt');
            $table->decimal('final_amount', 12, 2)->default(0)->after('round_off');
            $table->text('remarks')->nullable()->after('final_amount');
            $table->boolean('is_deleted')->default(false)->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakage_supplier_received_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'series', 'party_trn_no', 'party_date', 'claim_transaction_id',
                'claim_flag', 'received_as_debit_note', 'claim_amount',
                'gross_amt', 'total_gst', 'net_amt', 'round_off', 'final_amount',
                'remarks', 'is_deleted'
            ]);
        });
    }
};
