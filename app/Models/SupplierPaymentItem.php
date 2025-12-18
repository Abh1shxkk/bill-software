<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPaymentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_payment_id',
        'party_code',
        'party_name',
        'supplier_id',
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

    public function payment()
    {
        return $this->belongsTo(SupplierPayment::class, 'supplier_payment_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function adjustments()
    {
        return $this->hasMany(SupplierPaymentAdjustment::class);
    }
}
