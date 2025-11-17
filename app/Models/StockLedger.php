<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $fillable = [
        'trans_no',
        'item_id',
        'batch_id',
        'customer_id',
        'supplier_id',
        'transaction_type',
        'quantity',
        'free_quantity',
        'opening_qty',
        'closing_qty',
        'running_balance',
        'reference_type',
        'reference_id',
        'transaction_date',
        'godown',
        'remarks',
        'salesman_id',
        'bill_number',
        'bill_date',
        'rate',
        'created_by'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'bill_date' => 'date',
        'quantity' => 'decimal:2',
        'free_quantity' => 'decimal:2',
        'opening_qty' => 'decimal:2',
        'closing_qty' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    /**
     * Relationship with Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Relationship with Batch
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relationship with Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Relationship with SalesMan
     */
    public function salesman()
    {
        return $this->belongsTo(SalesMan::class, 'salesman_id');
    }

    /**
     * Relationship with User (who created the entry)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get party name (Customer or Supplier)
     */
    public function getPartyNameAttribute()
    {
        if ($this->customer_id) {
            return $this->customer->name ?? 'N/A';
        }
        if ($this->supplier_id) {
            return $this->supplier->name ?? 'N/A';
        }
        return 'N/A';
    }

    /**
     * Scope to get IN transactions
     */
    public function scopeIncoming($query)
    {
        return $query->where('transaction_type', 'IN');
    }

    /**
     * Scope to get OUT transactions
     */
    public function scopeOutgoing($query)
    {
        return $query->where('transaction_type', 'OUT');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('transaction_date', [$fromDate, $toDate]);
    }

    /**
     * Scope to filter by item
     */
    public function scopeForItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    /**
     * Scope to filter by batch
     */
    public function scopeForBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Scope to filter by reference
     */
    public function scopeByReference($query, $referenceType, $referenceId)
    {
        return $query->where('reference_type', $referenceType)
                     ->where('reference_id', $referenceId);
    }
}
