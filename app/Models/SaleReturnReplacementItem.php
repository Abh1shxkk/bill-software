<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class SaleReturnReplacementItem extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'transaction_id',
        'item_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry_date',
        'qty',
        'free_qty',
        'sale_rate',
        'discount_percent',
        'ft_rate',
        'amount',
        'packing',
        'unit',
        'mrp'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'sale_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'mrp' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(SaleReturnReplacementTransaction::class, 'transaction_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
