<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_date',
        'day_name',
        'trn_no',
        'ledger',
        'salesman_id',
        'salesman_code',
        'salesman_name',
        'area_id',
        'area_code',
        'area_name',
        'route_id',
        'route_code',
        'route_name',
        'bank_code',
        'bank_name',
        'coll_boy_id',
        'coll_boy_code',
        'coll_boy_name',
        'day_value',
        'tag',
        'total_cash',
        'total_cheque',
        'amt_outstanding',
        'amt_adjusted',
        'tds_amount',
        'currency_detail',
        'remarks',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'total_cash' => 'decimal:2',
        'total_cheque' => 'decimal:2',
        'amt_outstanding' => 'decimal:2',
        'amt_adjusted' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'currency_detail' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(CustomerReceiptItem::class);
    }

    public function adjustments()
    {
        return $this->hasMany(CustomerReceiptAdjustment::class);
    }

    public function salesman()
    {
        return $this->belongsTo(SalesMan::class, 'salesman_code', 'code');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_code', 'code');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_code', 'code');
    }

    public function bank()
    {
        return $this->belongsTo(CashBankBook::class, 'bank_code', 'alter_code');
    }
}
