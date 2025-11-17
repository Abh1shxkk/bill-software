<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'product_description',
        'hsn_code',
        'quantity',
        'unit',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'line_total',
        'tax_rate',
        'tax_amount',
        'cgst_rate',
        'sgst_rate',
        'igst_rate',
        'cess_rate'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'product_id');
    }

    // Accessors for form compatibility
    public function getItemIdAttribute()
    {
        return $this->product_id;
    }

    public function getCodeAttribute()
    {
        return $this->item ? $this->item->code : '';
    }

    public function getDescriptionAttribute()
    {
        return $this->product_description;
    }

    public function getQtyAttribute()
    {
        return $this->quantity;
    }

    public function getFreeQtyAttribute()
    {
        return 0; // Default value since this column doesn't exist in DB
    }

    public function getRateAttribute()
    {
        return $this->unit_price;
    }

    public function getDiscountAttribute()
    {
        return $this->discount_percent;
    }

    public function getMrpAttribute()
    {
        return $this->item ? $this->item->Mrp : 0;
    }

    public function getGstAttribute()
    {
        return $this->tax_rate;
    }

    public function getBatchAttribute()
    {
        return $this->item ? $this->item->Batchcode : '';
    }

    public function getExpiryAttribute()
    {
        return $this->item ? $this->item->Expiry : null;
    }
}
