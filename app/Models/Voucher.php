<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Voucher extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'voucher_no',
        'voucher_date',
        'day_name',
        'voucher_type',
        'multi_narration',
        'narration',
        'total_debit',
        'total_credit',
        'status',
        'remarks',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'multi_narration' => 'boolean',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    /**
     * Get voucher items
     */
    public function items()
    {
        return $this->hasMany(VoucherItem::class)->orderBy('sort_order');
    }

    /**
     * Get next voucher number for a specific type
     */
    public static function getNextVoucherNo($type = null)
    {
        $query = self::query();
        if ($type) {
            $query->where('voucher_type', $type);
        }
        $maxNo = $query->max('voucher_no');
        return ($maxNo ?? 0) + 1;
    }

    /**
     * Get voucher type label
     */
    public function getVoucherTypeLabelAttribute()
    {
        $labels = [
            'receipt' => 'Receipt Voucher',
            'payment' => 'Payment Voucher',
            'contra' => 'Contra Voucher',
            'journal' => 'Journal Voucher',
        ];
        return $labels[$this->voucher_type] ?? 'Voucher';
    }
}
