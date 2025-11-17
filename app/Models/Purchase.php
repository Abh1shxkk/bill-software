<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // New Transaction Fields
        'bill_date',
        'day_name',
        'supplier',
        'bill_no',
        'trn_no',
        'receive_date',
        'cash',
        'transfer',
        'remarks',
        'due_date',
        'items',
        'total_amount',
        'net_amount',
        'tax_amount',
        'discount_amount',
        
        // Old Fields (keeping for compatibility)
        'series',
        'date',
        'invoice_no',
        'supplier_id',
        'supplier_name',
        'salesman_id',
        'salesman_name',
        'cash_type',
        'fixed_dis',
        'cgst_percent',
        'sgst_percent',
        'cess_percent',
        'tax_percent',
        'excise',
        'tcs',
        'sc_percent',
        'case_qty',
        'box_qty',
        'nt_amt',
        'sc',
        'ft_amt',
        'dis',
        'scm',
        'tax',
        'net',
        'scm_percent',
        'packing',
        'nt_amt_2',
        'scm_percent_2',
        'sub_total',
        'comp',
        'srino',
        'unit',
        'sc_amt',
        'scm_amt',
        'tax_amt',
        'scm_1',
        'scm_2',
        'cl_qty',
        'dis_amt',
        'cost_gst',
        'location',
        'hs_amt',
        'vol',
        'batch_code',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'bill_date' => 'date',
        'receive_date' => 'date',
        'items' => 'array', // JSON field
    ];

    /**
     * Get the supplier for the purchase.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the salesman for the purchase.
     */
    public function salesman()
    {
        return $this->belongsTo(SalesMan::class);
    }

    /**
     * Get the items for the purchase.
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
