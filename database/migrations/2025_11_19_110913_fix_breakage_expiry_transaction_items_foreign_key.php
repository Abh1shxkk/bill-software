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
        Schema::table('breakage_expiry_transaction_items', function (Blueprint $table) {
            // Add foreign key with shorter name
            $table->foreign('breakage_expiry_transaction_id', 'be_trans_items_be_trans_id_fk')
                  ->references('id')->on('breakage_expiry_transactions')->onDelete('cascade');
            
            $table->foreign('item_id', 'be_trans_items_item_id_fk')
                  ->references('id')->on('items')->onDelete('set null');
            
            $table->foreign('batch_id', 'be_trans_items_batch_id_fk')
                  ->references('id')->on('batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakage_expiry_transaction_items', function (Blueprint $table) {
            $table->dropForeign('be_trans_items_be_trans_id_fk');
            $table->dropForeign('be_trans_items_item_id_fk');
            $table->dropForeign('be_trans_items_batch_id_fk');
        });
    }
};
