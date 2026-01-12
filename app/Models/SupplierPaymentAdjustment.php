<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class SupplierPaymentAdjustment extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'supplier_payment_id',
        'supplier_payment_item_id',
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

    public function payment()
    {
        return $this->belongsTo(SupplierPayment::class, 'supplier_payment_id');
    }

    public function paymentItem()
    {
        return $this->belongsTo(SupplierPaymentItem::class, 'supplier_payment_item_id');
    }
}
