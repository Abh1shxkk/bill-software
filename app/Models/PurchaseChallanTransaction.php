<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class PurchaseChallanTransaction extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'challan_no',
        'series',
        'challan_date',
        'due_date',
        'supplier_id',
        'supplier_invoice_no',
        'supplier_invoice_date',
        'cash_flag',
        'transfer_flag',
        'remarks',
        'nt_amount',
        'sc_amount',
        'ft_amount',
        'dis_amount',
        'scm_amount',
        'tax_amount',
        'net_amount',
        'scm_percent',
        'tcs_amount',
        'excise_amount',
        'is_invoiced',
        'purchase_transaction_id',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'challan_date' => 'date',
        'due_date' => 'date',
        'supplier_invoice_date' => 'date',
        'nt_amount' => 'decimal:2',
        'sc_amount' => 'decimal:2',
        'ft_amount' => 'decimal:2',
        'dis_amount' => 'decimal:2',
        'scm_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'scm_percent' => 'decimal:3',
        'tcs_amount' => 'decimal:2',
        'excise_amount' => 'decimal:2',
        'is_invoiced' => 'boolean',
    ];

    /**
     * Get the supplier that owns the purchase challan transaction.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the items for the purchase challan transaction.
     */
    public function items()
    {
        return $this->hasMany(PurchaseChallanTransactionItem::class, 'purchase_challan_transaction_id')->orderBy('row_order');
    }

    /**
     * Get the linked purchase transaction (if invoiced).
     */
    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'purchase_transaction_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the user who last updated the transaction.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}
