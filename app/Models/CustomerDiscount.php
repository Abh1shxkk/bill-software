<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomerDiscount extends Model
{
    protected $table = 'customer_discounts';

    protected $fillable = [
        'customer_id',
        'discount_type',
        'discount_percent',
        'effective_from',
        'effective_to',
        'remarks',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'discount_percent' => 'decimal:2',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if discount is active
     */
    public function isActive()
    {
        $today = Carbon::now()->toDateString();
        return $this->effective_from <= $today && 
               ($this->effective_to === null || $this->effective_to >= $today);
    }

    /**
     * Scope to filter active discounts
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
     * Scope to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('discount_type', $type);
    }

    /**
     * Scope to filter by customer
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
