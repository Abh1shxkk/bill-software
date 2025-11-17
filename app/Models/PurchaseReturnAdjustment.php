<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_return_id',
        'purchase_transaction_id',
        'adjusted_amount',
        'adjustment_date',
        'created_by',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'adjusted_amount' => 'decimal:2',
    ];

    /**
     * Get the purchase return transaction.
     */
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturnTransaction::class, 'purchase_return_id');
    }

    /**
     * Get the purchase transaction (invoice) being adjusted.
     */
    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'purchase_transaction_id');
    }
}
