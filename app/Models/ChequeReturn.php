<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequeReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_receipt_item_id',
        'customer_receipt_id',
        'customer_id',
        'customer_code',
        'customer_name',
        'cheque_no',
        'cheque_date',
        'bank_name',
        'bank_area',
        'amount',
        'trn_no',
        'receipt_date',
        'deposit_date',
        'status',
        'return_date',
        'status_date',
        'remarks',
    ];

    protected $casts = [
        'cheque_date' => 'date',
        'receipt_date' => 'date',
        'deposit_date' => 'date',
        'return_date' => 'date',
        'status_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customerReceiptItem()
    {
        return $this->belongsTo(CustomerReceiptItem::class);
    }

    public function customerReceipt()
    {
        return $this->belongsTo(CustomerReceipt::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
