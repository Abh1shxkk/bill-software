<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class BankTransaction extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'transaction_date', 'transaction_no', 'transaction_type', 'bank_id', 'bank_name',
        'cheque_no', 'amount', 'narration', 'status',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function bank()
    {
        return $this->belongsTo(CashBankBook::class, 'bank_id');
    }

    public static function getNextTransactionNo()
    {
        $last = self::orderByDesc('transaction_no')->first();
        return $last ? $last->transaction_no + 1 : 1;
    }

    public function getTypeNameAttribute()
    {
        return $this->transaction_type === 'D' ? 'Deposit' : 'Withdrawal';
    }
}
