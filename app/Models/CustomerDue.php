<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomerDue extends Model
{
    protected $table = 'customer_dues';

    protected $fillable = [
        'customer_id',
        'trans_no',
        'invoice_date',
        'days_from_invoice',
        'due_date',
        'days_from_due',
        'trans_amount',
        'debit',
        'credit',
        'hold',
        'remarks',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'trans_amount' => 'decimal:2',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'hold' => 'boolean',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if due is overdue
     */
    public function isOverdue()
    {
        return Carbon::now()->gt($this->due_date) && $this->payment_status !== 'Paid';
    }

    /**
     * Get days overdue
     */
    public function daysOverdue()
    {
        if ($this->isOverdue()) {
            return Carbon::now()->diffInDays($this->due_date);
        }
        return 0;
    }

    /**
     * Scope to filter by payment status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Scope to filter overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now()->toDateString())
                     ->where('payment_status', '!=', 'Paid');
    }

    /**
     * Scope to filter by customer
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
