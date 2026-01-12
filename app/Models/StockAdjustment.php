<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToOrganization;

class StockAdjustment extends Model
{
    use SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'trn_no',
        'adjustment_date',
        'day_name',
        'remarks',
        'total_amount',
        'total_items',
        'shortage_items',
        'excess_items',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }
}
