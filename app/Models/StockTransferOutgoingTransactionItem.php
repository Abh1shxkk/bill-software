<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class StockTransferOutgoingTransactionItem extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'stock_transfer_outgoing_transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry',
        'qty',
        'f_qty',
        'mrp',
        'p_rate',
        's_rate',
        'scm_percent',
        'dis_percent',
        'amount',
        'hsn_code',
        'cgst_percent',
        'sgst_percent',
        'cgst_amount',
        'sgst_amount',
        'tax_percent',
        'tax_amount',
        'packing',
        'company_name',
        'row_order',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'f_qty' => 'decimal:2',
        'mrp' => 'decimal:2',
        'p_rate' => 'decimal:2',
        's_rate' => 'decimal:2',
        'scm_percent' => 'decimal:2',
        'dis_percent' => 'decimal:2',
        'amount' => 'decimal:2',
        'cgst_percent' => 'decimal:2',
        'sgst_percent' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(StockTransferOutgoingTransaction::class, 'stock_transfer_outgoing_transaction_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
