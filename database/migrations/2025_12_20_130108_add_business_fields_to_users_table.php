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
        Schema::table('users', function (Blueprint $table) {
            $table->text('address')->nullable()->after('profile_picture');
            $table->string('telephone', 50)->nullable()->after('address');
            $table->string('tin_no', 50)->nullable()->after('telephone');
            $table->string('gst_no', 50)->nullable()->after('tin_no');
            $table->string('dl_no', 50)->nullable()->after('gst_no');
            $table->string('dl_no_1', 50)->nullable()->after('dl_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'telephone', 'tin_no', 'gst_no', 'dl_no', 'dl_no_1']);
        });
    }
};
