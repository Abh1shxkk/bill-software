<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class SaleReturnTransaction extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'sr_no',
        'series',
        'voucher_type',
        'invoice_no',
        'return_date',
        'customer_id',
        'customer_name',
        'salesman_id',
        'salesman_name',
        'original_invoice_no',
        'original_invoice_date',
        'original_series',
        'original_amount',
        'rate_diff_flag',
        'cash_flag',
        'tax_flag',
        'fixed_discount',
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
        'packing',
        'unit',
        'cl_qty',
        'location',
        'hs_amount',
        'balance_amount',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'original_invoice_date' => 'date',
        'original_amount' => 'decimal:2',
        'fixed_discount' => 'decimal:2',
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
        'packing' => 'decimal:2',
        'unit' => 'decimal:2',
        'cl_qty' => 'decimal:2',
        'hs_amount' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the sale return transaction.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the salesman that owns the sale return transaction.
     */
    public function salesman()
    {
        return $this->belongsTo(SalesMan::class, 'salesman_id');
    }

    /**
     * Get the items for the sale return transaction.
     */
    public function items()
    {
        return $this->hasMany(SaleReturnTransactionItem::class, 'sale_return_transaction_id');
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
