<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class BreakageExpiryTransaction extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'sr_no',
        'series',
        'transaction_date',
        'end_date',
        'customer_id',
        'customer_name',
        'salesman_id',
        'salesman_name',
        'gst_vno',
        'note_type',
        'with_gst',
        'inc',
        'rev_charge',
        'adjusted',
        'dis_rpl',
        'brk',
        'exp',
        'mrp_value',
        'gross_amount',
        'discount_amount',
        'scheme_amount',
        'tax_amount',
        'net_amount',
        'packing',
        'unit',
        'cl_qty',
        'scm_amt',
        'dis_amt',
        'subtotal',
        'tax_amt',
        'net_amt',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'end_date' => 'date',
        'mrp_value' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'scheme_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'packing' => 'decimal:2',
        'unit' => 'decimal:2',
        'cl_qty' => 'decimal:2',
        'scm_amt' => 'decimal:2',
        'dis_amt' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amt' => 'decimal:2',
        'net_amt' => 'decimal:2',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship with Salesman
     */
    public function salesman()
    {
        return $this->belongsTo(SalesMan::class, 'salesman_id');
    }

    /**
     * Relationship with Items
     */
    public function items()
    {
        return $this->hasMany(BreakageExpiryTransactionItem::class);
    }

    /**
     * Relationship with Creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Relationship with Updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}
