<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry_date',
        'packing',
        'company_name',
        'location',
        'qty',
        'rate',
        'mrp',
        'amount',
        'unit',
        'row_order',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'rate' => 'decimal:2',
        'mrp' => 'decimal:2',
        'amount' => 'decimal:2',
        'row_order' => 'integer',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
