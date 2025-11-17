<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferOutgoingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sr_no',
        'series',
        'transaction_date',
        'transfer_to',
        'transfer_to_name',
        'challan_no',
        'challan_date',
        'cases',
        'transport',
        'gst_vno',
        'with_gst',
        'mrp_value',
        'gross_amount',
        'discount_amount',
        'scheme_amount',
        'tax_amount',
        'net_amount',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'challan_date' => 'date',
        'mrp_value' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'scheme_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(StockTransferOutgoingTransactionItem::class);
    }

    public function returns()
    {
        return $this->hasMany(StockTransferOutgoingReturnTransaction::class, 'original_transfer_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}
