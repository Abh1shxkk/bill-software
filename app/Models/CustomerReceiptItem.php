<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_receipt_id',
        'party_code',
        'party_name',
        'customer_id',
        'cheque_no',
        'cheque_date',
        'cheque_bank_name',
        'cheque_bank_area',
        'cheque_closed_on',
        'amount',
        'unadjusted',
        'payment_type',
    ];

    protected $casts = [
        'cheque_date' => 'date',
        'cheque_closed_on' => 'date',
        'amount' => 'decimal:2',
        'unadjusted' => 'decimal:2',
    ];

    public function receipt()
    {
        return $this->belongsTo(CustomerReceipt::class, 'customer_receipt_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function adjustments()
    {
        return $this->hasMany(CustomerReceiptAdjustment::class);
    }
}
