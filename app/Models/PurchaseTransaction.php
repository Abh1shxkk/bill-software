<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'trn_no',
        'bill_date',
        'bill_no',
        'supplier_id',
        'receive_date',
        'due_date',
        'cash_flag',
        'transfer_flag',
        'remarks',
        'nt_amount',
        'sc_amount',
        'scm_amount',
        'dis_amount',
        'less_amount',
        'tax_amount',
        'net_amount',
        'scm_percent',
        'tcs_amount',
        'dis1_amount',
        'tof_amount',
        'inv_amount',
        'status',
        'order_no',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'bill_date' => 'date',
        'receive_date' => 'date',
        'due_date' => 'date',
        'nt_amount' => 'decimal:2',
        'sc_amount' => 'decimal:2',
        'scm_amount' => 'decimal:2',
        'dis_amount' => 'decimal:2',
        'less_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'scm_percent' => 'decimal:3',
        'tcs_amount' => 'decimal:2',
        'dis1_amount' => 'decimal:2',
        'tof_amount' => 'decimal:2',
        'inv_amount' => 'decimal:2',
    ];

    /**
     * Get the supplier that owns the purchase transaction.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the items for the purchase transaction.
     */
    public function items()
    {
        return $this->hasMany(PurchaseTransactionItem::class)->orderBy('row_order');
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
