<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class StockTransferIncomingReturnTransactionItem extends Model
{
    use BelongsToOrganization;

    protected $table = 'stock_transfer_incoming_return_transaction_items';

    protected $fillable = [
        'stock_transfer_incoming_return_transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry',
        'expiry_date',
        'qty',
        'rate',
        'amount',
        'packing',
        'unit',
        'company_name',
        'hsn_code',
        'row_order',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(StockTransferIncomingReturnTransaction::class, 'stock_transfer_incoming_return_transaction_id');
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
