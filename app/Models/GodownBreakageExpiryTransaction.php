<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class GodownBreakageExpiryTransaction extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'godown_breakage_expiry_transactions';

    protected $fillable = [
        'trn_no',
        'series',
        'transaction_date',
        'day_name',
        'narration',
        'total_qty',
        'total_amount',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_qty' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_deleted' => 'boolean',
    ];

    /**
     * Get the items for this transaction
     */
    public function items()
    {
        return $this->hasMany(GodownBreakageExpiryTransactionItem::class, 'godown_breakage_expiry_transaction_id');
    }

    /**
     * Scope for active (non-deleted) transactions
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    /**
 * Generate next transaction number (per organization)
 */
public static function generateTrnNumber()
{
    $orgId = auth()->user()->organization_id ?? 1;
    $prefix = 'GBE';
    $year = date('y');
    $month = date('m');
    
    $lastTransaction = self::withoutGlobalScopes()
        ->where('organization_id', $orgId)
        ->where('trn_no', 'LIKE', "{$prefix}{$year}{$month}%")
        ->orderBy('trn_no', 'desc')
        ->first();
    
    if ($lastTransaction) {
        $lastNumber = (int)substr($lastTransaction->trn_no, -3);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    
    return $prefix . $year . $month . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

    /**
     * Get breakage/expiry types
     */
    public static function getBrExTypes()
    {
        return [
            'BREAKAGE' => 'Breakage',
            'EXPIRY' => 'Expiry',
        ];
    }
}
