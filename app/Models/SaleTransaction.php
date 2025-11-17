<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'series',
        'sale_date',
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
        'paid_amount',
        'balance_amount',
        'payment_status',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
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
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the sale transaction.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the salesman that owns the sale transaction.
     */
    public function salesman()
    {
        return $this->belongsTo(SalesMan::class, 'salesman_id');
    }

    /**
     * Get the items for the sale transaction.
     */
    public function items()
    {
        return $this->hasMany(SaleTransactionItem::class, 'sale_transaction_id');
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
