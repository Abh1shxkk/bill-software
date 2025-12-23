<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakageSupplierReceivedTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trn_no',
        'transaction_date',
        'supplier_id',
        'supplier_name',
        'narration',
        'note_type',
        'tax_flag',
        'inc_flag',
        'gst_vno',
        'dis_count',
        'rpl_count',
        'brk_count',
        'exp_count',
        'total_nt_amt',
        'total_sc',
        'total_dis_amt',
        'total_scm_amt',
        'total_half_scm',
        'total_tax',
        'total_inv_amt',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'dis_count' => 'integer',
        'rpl_count' => 'integer',
        'brk_count' => 'integer',
        'exp_count' => 'integer',
        'total_nt_amt' => 'decimal:2',
        'total_sc' => 'decimal:2',
        'total_dis_amt' => 'decimal:2',
        'total_scm_amt' => 'decimal:2',
        'total_half_scm' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_inv_amt' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(BreakageSupplierReceivedTransactionItem::class, 'transaction_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
