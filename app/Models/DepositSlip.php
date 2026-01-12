<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class DepositSlip extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'slip_no',
        'deposit_date',
        'clearing_date',
        'payin_slip_date',
        'bank_id',
        'bank_name',
        'customer_id',
        'customer_code',
        'customer_name',
        'cheque_no',
        'cheque_date',
        'amount',
        'status',
        'posted_date',
        'remarks',
    ];

    protected $casts = [
        'deposit_date' => 'date',
        'clearing_date' => 'date',
        'payin_slip_date' => 'date',
        'cheque_date' => 'date',
        'posted_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function bank()
    {
        return $this->belongsTo(CashBankBook::class, 'bank_id');
    }
}
