<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultiVoucher extends Model
{
    protected $fillable = ['voucher_date', 'voucher_no', 'narration', 'total_amount', 'status'];

    protected $casts = [
        'voucher_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function entries() { return $this->hasMany(MultiVoucherEntry::class); }

    public static function getNextVoucherNo()
    {
        $last = self::orderByDesc('voucher_no')->first();
        return $last ? $last->voucher_no + 1 : 1;
    }
}
