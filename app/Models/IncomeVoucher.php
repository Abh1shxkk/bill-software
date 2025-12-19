<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeVoucher extends Model
{
    protected $fillable = [
        'voucher_date', 'voucher_no', 'local_inter', 'description',
        'customer_id', 'customer_name', 'gst_no', 'pan_no', 'city', 'pin', 'address',
        'amount', 'total_gst', 'net_amount', 'round_off', 'total_credit',
        'tds_percent', 'tds_amount', 'debit_account_id', 'debit_account_type', 'debit_account_name', 'total_debit',
        'total_cgst_amount', 'total_sgst_amount', 'total_igst_amount', 'status',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'amount' => 'decimal:2',
        'total_gst' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'tds_percent' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'total_debit' => 'decimal:2',
    ];

    public function items() { return $this->hasMany(IncomeVoucherItem::class); }
    public function accounts() { return $this->hasMany(IncomeVoucherAccount::class); }
    public function customer() { return $this->belongsTo(Customer::class); }

    public static function getNextVoucherNo()
    {
        $lastVoucher = self::orderByDesc('voucher_no')->first();
        return $lastVoucher ? $lastVoucher->voucher_no + 1 : 1;
    }
}
