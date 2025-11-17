<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'series', 'date', 'invoice_no', 'due_date', 'customer_id', 'salesman_id',
        'cash_type', 'due', 'pdc', 'total', 'cgst_percent', 'sgst_percent',
        'cess_percent', 'case', 'box', 'nt_amt', 'sc', 'ft_amt', 'dis', 'scm',
        'scm_percent', 'tax_percent', 'excise', 'tcs', 'sc_percent', 'tax', 'net',
        'packing', 'packing_nt_amt', 'packing_scm_percent', 'sub_total', 'unit',
        'sc_amt', 'scm_amt', 'tax_amt', 'cl_qty', 'dis_amt', 'net_amt', 'location',
        'hs_amt', 'comp', 'srino', 'cost_gst', 'scm_final', 'volume', 'batch_code',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the customer that owns the sale
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the salesman that owns the sale
     */
    public function salesman()
    {
        return $this->belongsTo(SalesMan::class, 'salesman_id');
    }

    /**
     * Get the items for the sale
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
