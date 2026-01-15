<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class StockAdjustmentItem extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'stock_adjustment_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry_date',
        'adjustment_type',
        'qty',
        'cost',
        'amount',
        'packing',
        'company_name',
        'mrp',
        'row_order'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:2',
        'cost' => 'decimal:2',
        'amount' => 'decimal:2',
        'mrp' => 'decimal:2',
    ];

    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
