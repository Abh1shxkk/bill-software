<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'code', 'item_id', 'item_name', 'batch_id', 'batch',
        'expiry', 'qty', 'free_qty', 'rate', 'discount', 'mrp', 'amount'
    ];

    /**
     * Get the sale that owns the item
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
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
    public function batchRecord()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
