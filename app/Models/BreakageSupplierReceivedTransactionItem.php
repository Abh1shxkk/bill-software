<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class BreakageSupplierReceivedTransactionItem extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry_date',
        'qty',
        'free_qty',
        'rate',
        'dis_percent',
        'scm_percent',
        'br_ex',
        'amount',
        'mrp',
        'purchase_rate',
        'sale_rate',
        'cgst',
        'sgst',
        'gst_percent',
        'igst_percent',
        'gst_amount',
        'company_name',
        'packing',
        'unit',
        'hsn_code',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'free_qty' => 'decimal:2',
        'rate' => 'decimal:2',
        'dis_percent' => 'decimal:2',
        'scm_percent' => 'decimal:2',
        'amount' => 'decimal:2',
        'mrp' => 'decimal:2',
        'purchase_rate' => 'decimal:2',
        'sale_rate' => 'decimal:2',
        'cgst' => 'decimal:2',
        'sgst' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'igst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(BreakageSupplierReceivedTransaction::class, 'transaction_id');
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
