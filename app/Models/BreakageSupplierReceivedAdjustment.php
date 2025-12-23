<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakageSupplierReceivedAdjustment extends Model
{
    protected $fillable = [
        'received_transaction_id',
        'purchase_transaction_id',
        'adjusted_amount',
    ];
    
    protected $casts = [
        'adjusted_amount' => 'decimal:2',
    ];
    
    public function receivedTransaction()
    {
        return $this->belongsTo(BreakageSupplierReceivedTransaction::class, 'received_transaction_id');
    }
    
    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'purchase_transaction_id');
    }
}
