<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomerPrescription extends Model
{
    protected $table = 'customer_prescriptions';

    protected $fillable = [
        'customer_id',
        'doctor_name',
        'patient_name',
        'prescription_date',
        'validity_date',
        'details',
        'status',
        'remarks',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'validity_date' => 'date',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if prescription is expired
     */
    public function isExpired()
    {
        return Carbon::now()->gt($this->validity_date);
    }

    /**
     * Check if prescription is active
     */
    public function isActive()
    {
        return $this->status === 'Active' && !$this->isExpired();
    }

    /**
     * Get days until expiry
     */
    public function daysUntilExpiry()
    {
        return Carbon::now()->diffInDays($this->validity_date, false);
    }

    /**
     * Scope to filter active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active')
                     ->where('validity_date', '>=', Carbon::now()->toDateString());
    }

    /**
     * Scope to filter expired
     */
    public function scopeExpired($query)
    {
        return $query->where('validity_date', '<', Carbon::now()->toDateString());
    }

    /**
     * Scope to filter by customer
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope to filter by doctor
     */
    public function scopeByDoctor($query, $doctorName)
    {
        return $query->where('doctor_name', 'like', "%{$doctorName}%");
    }
}
