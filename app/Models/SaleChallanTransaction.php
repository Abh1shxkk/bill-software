<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class SaleChallanTransaction extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'challan_no',
        'series',
        'challan_date',
        'due_date',
        'customer_id',
        'salesman_id',
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
        'sale_transaction_id',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'challan_date' => 'date',
        'due_date' => 'date',
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
     * Get the customer that owns the sale challan transaction.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the salesman that owns the sale challan transaction.
     */
    public function salesman()
    {
        return $this->belongsTo(SalesMan::class, 'salesman_id');
    }

    /**
     * Get the items for the sale challan transaction.
     */
    public function items()
    {
        return $this->hasMany(SaleChallanTransactionItem::class, 'sale_challan_transaction_id')->orderBy('row_order');
    }

    /**
     * Get the linked sale transaction (if invoiced).
     */
    public function saleTransaction()
    {
        return $this->belongsTo(SaleTransaction::class, 'sale_transaction_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the transaction.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
