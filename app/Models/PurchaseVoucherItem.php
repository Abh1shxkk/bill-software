<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class PurchaseVoucherItem extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'purchase_voucher_id',
        'hsn_code',
        'amount',
        'gst_percent',
        'cgst_percent',
        'cgst_amount',
        'sgst_percent',
        'sgst_amount',
        'igst_percent',
        'igst_amount',
        'total_amount',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'cgst_percent' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_percent' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_percent' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function purchaseVoucher()
    {
        return $this->belongsTo(PurchaseVoucher::class);
    }
}
