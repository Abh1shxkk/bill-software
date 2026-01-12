<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class ReplacementReceivedAdjustment extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'replacement_received_id',
        'purchase_return_id',
        'adjusted_amount',
        'adjustment_date',
        'created_by',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'adjusted_amount' => 'decimal:2',
    ];

    /**
     * Get the replacement received transaction
     */
    public function replacementReceived()
    {
        return $this->belongsTo(ReplacementReceivedTransaction::class, 'replacement_received_id');
    }

    /**
     * Get the purchase return transaction
     */
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturnTransaction::class, 'purchase_return_id');
    }
}
