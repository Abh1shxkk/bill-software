<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class ReplacementNoteTransaction extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'rn_no',
        'series',
        'transaction_date',
        'day_name',
        'supplier_id',
        'supplier_name',
        'pending_br_expiry',
        'balance_amount',
        'net_amount',
        'scm_percent',
        'scm_amount',
        'pack',
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
        'pending_br_expiry' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'scm_percent' => 'decimal:3',
        'scm_amount' => 'decimal:2',
        'cl_qty' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id')
            ->withDefault(['name' => '']);
    }

    public function items()
    {
        return $this->hasMany(ReplacementNoteTransactionItem::class, 'replacement_note_transaction_id');
    }

    public static function generateRNNumber()
    {
        $orgId = auth()->user()->organization_id ?? 1;
        
        // Get the maximum RN number by extracting the numeric part
        $maxNumber = self::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->where('rn_no', 'LIKE', 'RN%')
            ->selectRaw("MAX(CAST(SUBSTRING(rn_no, 3) AS UNSIGNED)) as max_num")
            ->value('max_num');
        
        $nextNumber = ($maxNumber ?? 0) + 1;
        
        // Generate number and check if it already exists (race condition protection)
        $rnNo = 'RN' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        // Double-check to avoid duplicates
        while (self::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->where('rn_no', $rnNo)
            ->exists()) {
            $nextNumber++;
            $rnNo = 'RN' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }
        
        return $rnNo;
    }
}
