<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'item_id',
        'code',
        'item_name',
        'batch',
        'exp',
        'qty',
        'f_qty',
        'purchase_rate',
        'dis_percent',
        'mrp',
        'amount',
    ];

    /**
     * Get the purchase that owns the item.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the item details.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
