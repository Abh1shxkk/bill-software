<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class BreakageSupplierIssuedTransaction extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'trn_no', 'series', 'transaction_date', 'day_name', 'supplier_id', 'supplier_name',
        'note_type', 'tax_flag', 'inc_flag', 'gst_vno', 'dis_count', 'rpl_count', 'brk_count', 'exp_count',
        'narration', 'total_nt_amt', 'total_sc', 'total_dis_amt', 'total_scm_amt', 'total_half_scm',
        'total_tax', 'total_inv_amt', 'total_qty', 'status', 'is_deleted', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'is_deleted' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(BreakageSupplierIssuedTransactionItem::class, 'transaction_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0)->where('status', '!=', 'cancelled');
    }

    public static function generateTrnNumber()
    {
        $orgId = auth()->user()->organization_id ?? 1;
        
        $lastTransaction = self::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastTransaction ? ((int)$lastTransaction->trn_no + 1) : 1;
        return (string)$nextNumber;
    }

    public static function getBrExTypes()
    {
        return [
            'BREAKAGE' => 'Breakage',
            'EXPIRY' => 'Expiry'
        ];
    }

    public static function getNoteTypes()
    {
        return [
            'R' => 'Replacement',
            'C' => 'Credit'
        ];
    }
}
