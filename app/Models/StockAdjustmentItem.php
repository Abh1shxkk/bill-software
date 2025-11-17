<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
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
        'row_order',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:2',
        'cost' => 'decimal:2',
        'amount' => 'decimal:2',
        'mrp' => 'decimal:2',
    ];

    /**
     * Relationship with StockAdjustment
     */
    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class, 'stock_adjustment_id');
    }

    /**
     * Relationship with Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Relationship with Batch
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    /**
     * Get adjustment type label
     */
    public function getAdjustmentTypeLabelAttribute()
    {
        return $this->adjustment_type === 'S' ? 'Shortage' : 'Excess';
    }

    /**
     * Get formatted expiry date
     */
    public function getFormattedExpiryAttribute()
    {
        return $this->expiry_date ? $this->expiry_date->format('m/Y') : '-';
    }
}
