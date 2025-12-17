<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GodownBreakageExpiryTransactionItem extends Model
{
    use HasFactory;

    protected $table = 'godown_breakage_expiry_transaction_items';

    protected $fillable = [
        'godown_breakage_expiry_transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry',
        'expiry_date',
        'br_ex_type',
        'qty',
        'cost',
        'amount',
        'packing',
        'unit',
        'company_name',
        'location',
        'mrp',
        's_rate',
        'p_rate',
        'row_order',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:2',
        'cost' => 'decimal:2',
        'amount' => 'decimal:2',
        'mrp' => 'decimal:2',
        's_rate' => 'decimal:2',
        'p_rate' => 'decimal:2',
    ];

    /**
     * Get the parent transaction
     */
    public function godownBreakageExpiryTransaction()
    {
        return $this->belongsTo(GodownBreakageExpiryTransaction::class, 'godown_breakage_expiry_transaction_id');
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
