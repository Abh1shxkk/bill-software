<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claim_to_supplier_transactions', function (Blueprint $table) {
            // Additional Details fields
            $table->char('blank_statement', 1)->default('Y')->after('narration');
            $table->char('rate_type', 1)->default('R')->after('blank_statement'); // P=Purchase, S=Sale, R=Rate Diff
            $table->date('filter_from_date')->nullable()->after('rate_type');
            $table->date('filter_to_date')->nullable()->after('filter_from_date');
            $table->string('company_code', 50)->nullable()->after('filter_to_date');
            $table->string('division', 20)->default('00')->after('company_code');
        });
    }

    public function down(): void
    {
        Schema::table('claim_to_supplier_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'blank_statement',
                'rate_type',
                'filter_from_date',
                'filter_to_date',
                'company_code',
                'division'
            ]);
        });
    }
};
