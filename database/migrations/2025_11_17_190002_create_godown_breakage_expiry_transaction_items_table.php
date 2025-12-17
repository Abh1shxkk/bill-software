<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('godown_breakage_expiry_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('godown_breakage_expiry_transaction_id')->constrained('godown_breakage_expiry_transactions')->onDelete('cascade')->name('gbe_items_transaction_id_fk');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade')->name('gbe_items_item_id_fk');
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null')->name('gbe_items_batch_id_fk');
            $table->string('item_code', 50)->nullable();
            $table->string('item_name', 150)->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->string('expiry', 20)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('br_ex_type', 20)->default('BREAKAGE'); // BREAKAGE or EXPIRY
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('cost', 10, 2)->default(0); // Purchase rate
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('packing', 50)->nullable();
            $table->string('unit', 10)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('s_rate', 10, 2)->default(0);
            $table->decimal('p_rate', 10, 2)->default(0);
            $table->integer('row_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('godown_breakage_expiry_transaction_items');
    }
};
