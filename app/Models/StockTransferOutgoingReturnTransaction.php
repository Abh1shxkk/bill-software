<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferOutgoingReturnTransaction extends Model
{
    use HasFactory;

    protected $table = 'stock_transfer_outgoing_return_transactions';

    protected $fillable = [
        'sr_no',
        'series',
        'transaction_date',
        'original_transfer_id',
        'original_sr_no',
        'transfer_from',
        'transfer_from_name',
        'trf_return_no',
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
        return $this->hasMany(StockTransferOutgoingReturnTransactionItem::class, 'stock_transfer_outgoing_return_transaction_id');
    }

    public function originalTransfer()
    {
        return $this->belongsTo(StockTransferOutgoingTransaction::class, 'original_transfer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}
