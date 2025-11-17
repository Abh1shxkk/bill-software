<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change bc column from char(1) to varchar(50) to store batch codes
        DB::statement("ALTER TABLE batches MODIFY COLUMN bc VARCHAR(50) NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE batches MODIFY COLUMN bc CHAR(1) NOT NULL DEFAULT 'N'");
    }
};
