<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_transaction_id',
        'item_id',
        'batch_id', // 🔥 Batch ID for quantity tracking
        'item_code',
        'item_name',
        'batch_no',
        'expiry_date',
        'qty',
        'free_qty',
        'sale_rate',
        'mrp',
        'discount_percent',
        'discount_amount',
        'amount',
        'net_amount',
        'cgst_percent',
        'sgst_percent',
        'cess_percent',
        'cgst_amount',
        'sgst_amount',
        'cess_amount',
        'tax_amount',
        'unit',
        'packing',
        'company_name',
        'hsn_code',
        'row_order',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'free_qty' => 'decimal:3',
        'sale_rate' => 'decimal:2',
        'mrp' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'cgst_percent' => 'decimal:2',
        'sgst_percent' => 'decimal:2',
        'cess_percent' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'cess_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'row_order' => 'integer',
    ];

    /**
     * Get the sale transaction that owns the item.
     */
    public function saleTransaction()
    {
        return $this->belongsTo(SaleTransaction::class, 'sale_transaction_id');
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
