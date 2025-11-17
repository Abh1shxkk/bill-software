<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingOrder extends Model
{
    protected $fillable = [
        'supplier_id',
        'order_no',
        'item_id',
        'order_date',
        'balance_qty',
        'order_qty',
        'free_qty',
        'other_order',
    ];

    protected $casts = [
        'order_date' => 'date',
        'balance_qty' => 'integer',
        'order_qty' => 'integer',
        'free_qty' => 'integer',
        'other_order' => 'decimal:2',
    ];

    /**
     * Relationship with Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    /**
     * Relationship with Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get pending quantity
     */
    public function getPendingQty()
    {
        return $this->quantity - $this->received_qty;
    }

    /**
     * Get pending amount
     */
    public function getPendingAmount()
    {
        return $this->total_amount - $this->received_amount;
    }

    /**
     * Get status badge
     */
    public function getStatusBadge()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'partial' => '<span class="badge bg-info">Partial</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        ];
        return $badges[$this->po_status] ?? $this->po_status;
    }

    /**
     * Scope to get active orders
     */
    public function scopeActive($query)
    {
        return $query->whereIn('po_status', ['pending', 'partial']);
    }

    /**
     * Scope to filter by supplier
     */
    public function scopeForSupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Scope to filter by item
     */
    public function scopeForItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }
}
