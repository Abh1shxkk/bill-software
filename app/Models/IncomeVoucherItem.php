<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class IncomeVoucherItem extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'income_voucher_id', 'hsn_code', 'amount', 'gst_percent',
        'cgst_percent', 'cgst_amount', 'sgst_percent', 'sgst_amount',
        'igst_percent', 'igst_amount', 'total_amount', 'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'cgst_percent' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_percent' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
    ];

    public function incomeVoucher() { return $this->belongsTo(IncomeVoucher::class); }
}
