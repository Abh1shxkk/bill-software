<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class PurchaseVoucherAccount extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'purchase_voucher_id',
        'account_type',
        'account_id',
        'account_code',
        'account_name',
        'sort_order',
    ];

    public function purchaseVoucher()
    {
        return $this->belongsTo(PurchaseVoucher::class);
    }
}
