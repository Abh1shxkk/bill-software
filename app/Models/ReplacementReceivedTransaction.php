<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class ReplacementReceivedTransaction extends Model
{
    use BelongsToOrganization;

    protected $table = 'replacement_received_transactions';

    protected $fillable = [
        'rr_no',
        'series',
        'transaction_date',
        'day_name',
        'supplier_id',
        'supplier_name',
        'pending_br_expiry',
        'balance_amount',
        'total_amount',
        'scm_percent',
        'scm_amount',
        'packing',
        'unit',
        'cl_qty',
        'comp',
        'lctn',
        'srlno',
        'case_no',
        'box',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'scm_percent' => 'decimal:2',
        'scm_amount' => 'decimal:2',
        'pending_br_expiry' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(ReplacementReceivedTransactionItem::class, 'replacement_received_transaction_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function adjustments()
    {
        return $this->hasMany(ReplacementReceivedAdjustment::class, 'replacement_received_id');
    }

    public static function generateRRNumber()
    {
        $orgId = auth()->user()->organization_id ?? 1;
        
        // Get the maximum RR number by extracting the numeric part
        $maxNumber = self::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->where('rr_no', 'LIKE', 'RR%')
            ->selectRaw("MAX(CAST(SUBSTRING(rr_no, 3) AS UNSIGNED)) as max_num")
            ->value('max_num');
        
        $nextNumber = ($maxNumber ?? 0) + 1;
        
        // Generate number and check if it already exists (race condition protection)
        $rrNo = 'RR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        
        // Double-check to avoid duplicates
        while (self::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->where('rr_no', $rrNo)
            ->exists()) {
            $nextNumber++;
            $rrNo = 'RR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }
        
        return $rrNo;
    }
}
