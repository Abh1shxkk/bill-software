<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerChallan extends Model
{
    protected $table = 'customer_challans';

    protected $fillable = [
        'customer_id',
        'challan_number',
        'challan_date',
        'total_items',
        'pending_items',
        'delivery_status',
        'remarks',
    ];

    protected $casts = [
        'challan_date' => 'date',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if challan is pending
     */
    public function isPending()
    {
        return $this->delivery_status === 'Pending' && $this->pending_items > 0;
    }

    /**
     * Get delivery percentage
     */
    public function deliveryPercentage()
    {
        if ($this->total_items === 0) {
            return 0;
        }
        return round((($this->total_items - $this->pending_items) / $this->total_items) * 100, 2);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('delivery_status', $status);
    }

    /**
     * Scope to filter pending
     */
    public function scopePending($query)
    {
        return $query->where('delivery_status', '!=', 'Delivered')
                     ->where('delivery_status', '!=', 'Cancelled');
    }

    /**
     * Scope to filter by customer
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
