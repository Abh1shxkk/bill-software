<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReceiptAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_receipt_id',
        'customer_receipt_item_id',
        'sale_transaction_id',
        'adjustment_type',
        'reference_no',
        'reference_date',
        'reference_amount',
        'adjusted_amount',
        'balance_amount',
    ];

    protected $casts = [
        'reference_date' => 'date',
        'reference_amount' => 'decimal:2',
        'adjusted_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    public function receipt()
    {
        return $this->belongsTo(CustomerReceipt::class, 'customer_receipt_id');
    }

    public function receiptItem()
    {
        return $this->belongsTo(CustomerReceiptItem::class, 'customer_receipt_item_id');
    }
}
