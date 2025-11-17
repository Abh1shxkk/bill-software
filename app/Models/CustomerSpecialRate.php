<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomerSpecialRate extends Model
{
    protected $table = 'customer_special_rates';

    protected $fillable = [
        'customer_id',
        'item_id',
        'special_rate',
        'effective_from',
        'effective_to',
        'rate_type',
        'remarks',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'special_rate' => 'decimal:2',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship with Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Check if rate is active
     */
    public function isActive()
    {
        $today = Carbon::now()->toDateString();
        return $this->effective_from <= $today && 
               ($this->effective_to === null || $this->effective_to >= $today);
    }

    /**
     * Scope to filter active rates
     */
    public function scopeActive($query)
    {
        $today = Carbon::now()->toDateString();
        return $query->where('effective_from', '<=', $today)
                     ->where(function ($q) use ($today) {
                         $q->whereNull('effective_to')
                           ->orWhere('effective_to', '>=', $today);
                     });
    }

    /**
     * Scope to filter by customer
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope to filter by item
     */
    public function scopeForItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }
}
