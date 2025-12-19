<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultiVoucherEntry extends Model
{
    protected $fillable = [
        'multi_voucher_id', 'entry_date', 'debit_account_type', 'debit_account_id', 'debit_account_name',
        'credit_account_type', 'credit_account_id', 'credit_account_name', 'amount', 'dr_slcd', 'sort_order',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function multiVoucher() { return $this->belongsTo(MultiVoucher::class); }
}
