<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class SupplierPayment extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'payment_date',
        'day_name',
        'trn_no',
        'ledger',
        'bank_code',
        'bank_name',
        'total_cash',
        'total_cheque',
        'amt_outstanding',
        'amt_adjusted',
        'tds_amount',
        'currency_detail',
        'remarks',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'total_cash' => 'decimal:2',
        'total_cheque' => 'decimal:2',
        'amt_outstanding' => 'decimal:2',
        'amt_adjusted' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'currency_detail' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(SupplierPaymentItem::class);
    }

    public function adjustments()
    {
        return $this->hasMany(SupplierPaymentAdjustment::class);
    }

    public function bank()
    {
        return $this->belongsTo(CashBankBook::class, 'bank_code', 'alter_code');
    }
}
