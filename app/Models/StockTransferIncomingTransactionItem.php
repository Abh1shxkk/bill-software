<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class StockTransferIncomingTransactionItem extends Model
{
    use BelongsToOrganization;

    protected $table = 'stock_transfer_incoming_transaction_items';

    protected $fillable = [
        'stock_transfer_incoming_transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry',
        'expiry_date',
        'qty',
        'free_qty',
        'p_rate',
        'gst_percent',
        'ft_rate',
        'ft_amount',
        'mrp',
        's_rate',
        'packing',
        'unit',
        'company_name',
        'hsn_code',
        'row_order',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:2',
        'free_qty' => 'decimal:2',
        'p_rate' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'ft_rate' => 'decimal:2',
        'ft_amount' => 'decimal:2',
        'mrp' => 'decimal:2',
        's_rate' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(StockTransferIncomingTransaction::class, 'stock_transfer_incoming_transaction_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
