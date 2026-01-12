<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class IncomeVoucherAccount extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'income_voucher_id', 'account_type', 'account_id', 'account_code', 'account_name', 'sort_order',
    ];

    public function incomeVoucher() { return $this->belongsTo(IncomeVoucher::class); }
}
