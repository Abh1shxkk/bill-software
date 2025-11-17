<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakageExpiryTransactionItem extends Model
{
    protected $fillable = [
        'breakage_expiry_transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry',
        'br_ex',
        'qty',
        'f_qty',
        'mrp',
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
        's_rate',
        'p_rate',
        'packing',
        'company_name',
        'row_order',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'f_qty' => 'decimal:3',
        'mrp' => 'decimal:2',
        'scm_percent' => 'decimal:2',
        'dis_percent' => 'decimal:2',
        'amount' => 'decimal:2',
        'cgst_percent' => 'decimal:2',
        'sgst_percent' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        's_rate' => 'decimal:2',
        'p_rate' => 'decimal:2',
    ];

    /**
     * Relationship with Transaction
     */
    public function transaction()
    {
        return $this->belongsTo(BreakageExpiryTransaction::class, 'breakage_expiry_transaction_id');
    }

    /**
     * Relationship with Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relationship with Batch
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
