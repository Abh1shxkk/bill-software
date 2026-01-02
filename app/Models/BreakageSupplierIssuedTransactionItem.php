<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakageSupplierIssuedTransactionItem extends Model
{
    protected $fillable = [
        'transaction_id', 'item_id', 'batch_id', 'item_code', 'item_name', 'batch_no', 'expiry', 'expiry_date',
        'qty', 'free_qty', 'rate', 'dis_percent', 'scm_percent', 'br_ex_type', 'amount',
        'nt_amt', 'dis_amt', 'scm_amt', 'half_scm', 'tax_amt', 'net_amt',
        'packing', 'unit', 'company_name', 'mrp', 'p_rate', 's_rate', 'hsn_code',
        'cgst_percent', 'sgst_percent', 'cgst_amt', 'sgst_amt', 'sc_percent', 'tax_percent', 'row_order'
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function transaction()
    {
        return $this->belongsTo(BreakageSupplierIssuedTransaction::class, 'transaction_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
