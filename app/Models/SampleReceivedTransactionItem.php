<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleReceivedTransactionItem extends Model
{
    use HasFactory;

    protected $table = 'sample_received_transaction_items';

    protected $fillable = [
        'sample_received_transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry',
        'expiry_date',
        'qty',
        'free_qty',
        'rate',
        'mrp',
        'amount',
        'packing',
        'unit',
        'company_name',
        'hsn_code',
        'row_order',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:2',
        'free_qty' => 'decimal:2',
        'rate' => 'decimal:2',
        'mrp' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the parent transaction
     */
    public function sampleReceivedTransaction()
    {
        return $this->belongsTo(SampleReceivedTransaction::class, 'sample_received_transaction_id');
    }

    /**
     * Get the item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the batch
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
