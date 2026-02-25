<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class SaleReturnReplacementTransaction extends Model
{
    use BelongsToOrganization;

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

    // BelongsToOrganization trait global scope uses 'transaction_date' by default
    // but this model's date column is 'trn_date' — override here
    protected static $defaultOrderColumn = 'trn_date';

    protected static function booted()
    {
        parent::booted();
        // Remove any global scope that orders by 'transaction_date'
        // and re-add with correct column 'trn_date'
        static::addGlobalScope('order_by_trn_date', function ($builder) {
            // Clear any default ordering injected by trait, apply correct one
            $builder->reorder('trn_date', 'desc');
        });
    }

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
        $orgId = auth()->user()->organization_id ?? 1;
        $maxTrnNo = self::withoutGlobalScopes()->where('organization_id', $orgId)->max('trn_no');
        return $maxTrnNo ? $maxTrnNo + 1 : 1;
    }
}










