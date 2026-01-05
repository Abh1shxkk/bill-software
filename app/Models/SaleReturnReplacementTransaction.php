<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnReplacementTransaction extends Model
{
    protected $fillable = [
        'series',
        'trn_no',
        'trn_date',
        'is_cash',
        'customer_id',
        'customer_name',
        'fixed_discount',
        'sc_percent',
        'tax_percent',
        'excise',
        'tsr',
        'nt_amt',
        'sc_amt',
        'ft_amt',
        'dis_amt',
        'scm_amt',
        'tax_amt',
        'net_amt',
        'remarks',
        'status'
    ];

    protected $casts = [
        'trn_date' => 'date',
        'fixed_discount' => 'decimal:2',
        'net_amt' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnReplacementItem::class, 'transaction_id');
    }

    public static function getNextTrnNo()
    {
        $maxTrnNo = self::max('trn_no');
        return $maxTrnNo ? $maxTrnNo + 1 : 1;
    }
}
