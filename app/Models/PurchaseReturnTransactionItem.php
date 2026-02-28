<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class PurchaseReturnTransactionItem extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'purchase_return_transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry_date',
        'qty',
        'free_qty',
        'pur_rate',
        'dis_percent',
        'ft_rate',
        'ft_amount',
        'mrp',
        'ws_rate',
        's_rate',
        'spl_rate',
        'amount',
        'gst_percent',
        'cgst_percent',
        'sgst_percent',
        'cess_percent',
        'cgst_amount',
        'sgst_amount',
        'cess_amount',
        'tax_amount',
        'net_amount',
        'hsn_code',
        'packing',
        'unit',
        'company_name',
        'row_order',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:3',
        'free_qty' => 'decimal:3',
        'pur_rate' => 'decimal:2',
        'dis_percent' => 'decimal:3',
        'ft_rate' => 'decimal:2',
        'ft_amount' => 'decimal:2',
        'mrp' => 'decimal:2',
        'ws_rate' => 'decimal:2',
        's_rate' => 'decimal:2',
        'spl_rate' => 'decimal:2',
        'cgst_percent' => 'decimal:3',
        'sgst_percent' => 'decimal:3',
        'cess_percent' => 'decimal:3',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'cess_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'row_order' => 'integer',
    ];

    /**
     * Get the purchase return transaction that owns the item.
     */
    public function purchaseReturnTransaction()
    {
        return $this->belongsTo(PurchaseReturnTransaction::class, 'purchase_return_transaction_id');
    }

    /**
     * Get the item that this transaction item refers to.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the batch that this transaction item refers to.
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
