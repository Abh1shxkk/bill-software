<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\BelongsToOrganization;

class Batch extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'purchase_transaction_id',
        'purchase_transaction_item_id',
        'item_id',
        'item_code',
        'item_name',
        'batch_no',
        'bc',
        'expiry_date',
        'manufacturing_date',
        'qty',
        'free_qty',
        'total_qty',
        'pur_rate',
        's_rate',
        'mrp',
        'ws_rate',
        'spl_rate',
        'sale_scheme',
        'inc',
        'n_rate',
        'dis_percent',
        'sc_amount',
        'amount',
        'cgst_percent',
        'sgst_percent',
        'cess_percent',
        'cgst_amount',
        'sgst_amount',
        'cess_amount',
        'tax_amount',
        'gst_pts',
        'net_amount',
        'cost',
        'cost_gst',
        'cost_wfq',
        'rate_diff',
        'unit',
        'packing',
        'company_name',
        'godown',
        'status',
        'hold_breakage_expiry',
        'is_deleted',
        'remarks'
    ];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'qty' => 'decimal:2',
        'free_qty' => 'decimal:2',
        'total_qty' => 'decimal:2',
        'pur_rate' => 'decimal:2',
        's_rate' => 'decimal:2',
        'mrp' => 'decimal:2',
        'ws_rate' => 'decimal:2',
        'spl_rate' => 'decimal:2',
        'n_rate' => 'decimal:2',
        'dis_percent' => 'decimal:3',
        'sc_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'cgst_percent' => 'decimal:3',
        'sgst_percent' => 'decimal:3',
        'cess_percent' => 'decimal:3',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'cess_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'gst_pts' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'cost' => 'decimal:2',
        'cost_gst' => 'decimal:2',
        'cost_wfq' => 'decimal:2',
        'rate_diff' => 'decimal:2',
    ];

    /**
     * Relationship with Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Relationship with Purchase Transaction
     */
    public function transaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'purchase_transaction_id');
    }

    /**
     * Alias for transaction relationship
     */
    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'purchase_transaction_id');
    }

    /**
     * Relationship with Purchase Transaction Item
     */
    public function transactionItem()
    {
        return $this->belongsTo(PurchaseTransactionItem::class, 'purchase_transaction_item_id');
    }

    /**
     * Relationship with Stock Ledger
     */
    public function stockLedgers()
    {
        return $this->hasMany(StockLedger::class, 'batch_id');
    }

    /**
     * Check if batch is expired
     */
    public function isExpired()
    {
        if (!$this->expiry_date) {
            return false;
        }
        return Carbon::now()->gt($this->expiry_date);
    }

    /**
     * Get days until expiry
     */
    public function daysUntilExpiry()
    {
        if (!$this->expiry_date) {
            return null;
        }
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Check if batch is expiring soon (within 30 days)
     */
    public function isExpiringsoon()
    {
        $daysLeft = $this->daysUntilExpiry();
        return $daysLeft !== null && $daysLeft > 0 && $daysLeft <= 30;
    }

    /**
     * Scope to get active batches
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0)->where('status', 'active');
    }

    /**
     * Scope to get expired batches
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::now()->toDateString());
    }

    /**
     * Scope to get expiring soon batches
     */
    public function scopeExpiringsoon($query)
    {
        $thirtyDaysFromNow = Carbon::now()->addDays(30)->toDateString();
        return $query->where('expiry_date', '<=', $thirtyDaysFromNow)
                     ->where('expiry_date', '>', Carbon::now()->toDateString());
    }

    /**
     * Scope to filter by item
     */
    public function scopeForItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    /**
     * Scope to filter by godown
     */
    public function scopeInGodown($query, $godown)
    {
        return $query->where('godown', $godown);
    }
}
