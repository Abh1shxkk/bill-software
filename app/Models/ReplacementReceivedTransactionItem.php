<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class ReplacementReceivedTransactionItem extends Model
{
    use BelongsToOrganization;

    protected $table = 'replacement_received_transaction_items';

    protected $fillable = [
        'replacement_received_transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry',
        'expiry_date',
        'qty',
        'free_qty',
        'mrp',
        'discount_percent',
        'ft_rate',
        'ft_amount',
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
        'mrp' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'ft_rate' => 'decimal:2',
        'ft_amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(ReplacementReceivedTransaction::class, 'replacement_received_transaction_id');
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
