<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_no',
        'series',
        'quotation_date',
        'customer_id',
        'customer_name',
        'discount_percent',
        'remarks',
        'terms',
        'net_amount',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'discount_percent' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
