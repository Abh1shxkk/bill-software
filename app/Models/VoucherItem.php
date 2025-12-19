<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'account_type',
        'account_id',
        'account_code',
        'account_name',
        'debit_amount',
        'credit_amount',
        'item_narration',
        'sort_order',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    /**
     * Get parent voucher
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Get related account based on type
     */
    public function getAccountAttribute()
    {
        switch ($this->account_type) {
            case 'GL':
                return GeneralLedger::find($this->account_id);
            case 'CB':
                return CashBankBook::find($this->account_id);
            case 'SL':
                return SaleLedger::find($this->account_id);
            case 'PL':
                return PurchaseLedger::find($this->account_id);
            case 'CL':
                return Customer::find($this->account_id);
            case 'SU':
                return Supplier::find($this->account_id);
            default:
                return null;
        }
    }
}
