<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BreakageExpiryTransaction;
use App\Models\SaleTransaction;

class BreakageExpiryAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'breakage_expiry_transaction_id',
        'sale_transaction_id',
        'adjusted_amount',
        'adjustment_date',
        'created_by',
    ];

    protected $casts = [
        'adjusted_amount' => 'decimal:2',
        'adjustment_date' => 'date',
    ];

    /**
     * Get the breakage/expiry transaction that owns this adjustment
     */
    public function breakageExpiryTransaction()
    {
        return $this->belongsTo(BreakageExpiryTransaction::class);
    }

    /**
     * Get the sale transaction being adjusted
     */
    public function saleTransaction()
    {
        return $this->belongsTo(SaleTransaction::class);
    }
}
