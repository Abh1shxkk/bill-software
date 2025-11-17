<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleChallanTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_challan_transaction_id',
        'item_id',
        'batch_id',
        'batch_no',
        'expiry_date',
        'qty',
        'free_qty',
        'sale_rate',
        'mrp',
        'discount_percent',
        'discount_amount',
        'cgst_percent',
        'sgst_percent',
        'cess_percent',
        'cgst_amount',
        'sgst_amount',
        'cess_amount',
        'net_amount',
        'row_order'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:3',
        'free_qty' => 'decimal:3',
        'sale_rate' => 'decimal:2',
        'mrp' => 'decimal:2',
        'discount_percent' => 'decimal:3',
        'discount_amount' => 'decimal:2',
        'cgst_percent' => 'decimal:3',
        'sgst_percent' => 'decimal:3',
        'cess_percent' => 'decimal:3',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'cess_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    /**
     * Get the challan transaction that owns the item.
     */
    public function challanTransaction()
    {
        return $this->belongsTo(SaleChallanTransaction::class, 'sale_challan_transaction_id');
    }

    /**
     * Get the item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the batch.
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
