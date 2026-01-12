<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class PurchaseVoucher extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'voucher_date',
        'voucher_no',
        'bill_no',
        'bill_date',
        'local_inter',
        'rcm',
        'description',
        'supplier_id',
        'supplier_code',
        'supplier_name',
        'gst_no',
        'pan_no',
        'city',
        'pin',
        'amount',
        'total_gst',
        'net_amount',
        'round_off',
        'total_debit',
        'tds_percent',
        'tds_amount',
        'payment_type',
        'credit_account_id',
        'credit_account_type',
        'credit_account_name',
        'cheque_no',
        'total_credit',
        'total_cgst_amount',
        'total_sgst_amount',
        'total_igst_amount',
        'status',
        'remarks',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'bill_date' => 'date',
        'amount' => 'decimal:2',
        'total_gst' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'total_debit' => 'decimal:2',
        'tds_percent' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'total_cgst_amount' => 'decimal:2',
        'total_sgst_amount' => 'decimal:2',
        'total_igst_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseVoucherItem::class)->orderBy('sort_order');
    }

    public function accounts()
    {
        return $this->hasMany(PurchaseVoucherAccount::class)->orderBy('sort_order');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function getNextVoucherNo()
    {
        $maxNo = self::max('voucher_no');
        return ($maxNo ?? 0) + 1;
    }
}
