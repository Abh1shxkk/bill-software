<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_transaction_id',
        'item_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry_date',
        'qty',
        'free_qty',
        'pur_rate',
        'mrp',
        's_rate',
        'ws_rate',
        'spl_rate',
        'dis_percent',
        'amount',
        'cgst_percent',
        'sgst_percent',
        'cess_percent',
        'cgst_amount',
        'sgst_amount',
        'cess_amount',
        'tax_amount',
        'net_amount',
        'cost',
        'cost_gst',
        'unit',
        'packing',
        'company_name',
        'location',
        'row_order'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:2',
        'free_qty' => 'decimal:2',
        'pur_rate' => 'decimal:2',
        'mrp' => 'decimal:2',
        's_rate' => 'decimal:2',
        'ws_rate' => 'decimal:2',
        'spl_rate' => 'decimal:2',
        'dis_percent' => 'decimal:3',
        'amount' => 'decimal:2',
        'cgst_percent' => 'decimal:3',
        'sgst_percent' => 'decimal:3',
        'cess_percent' => 'decimal:3',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'cess_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'cost' => 'decimal:2',
        'cost_gst' => 'decimal:2',
    ];

    /**
     * Get the purchase transaction that owns the item.
     */
    public function transaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'purchase_transaction_id');
    }

    /**
     * Get the item details.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
