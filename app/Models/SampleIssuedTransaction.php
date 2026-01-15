<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class SampleIssuedTransaction extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'sample_issued_transactions';

    protected $fillable = [
        'trn_no',
        'series',
        'transaction_date',
        'day_name',
        'party_type',
        'party_id',
        'party_name',
        'gr_no',
        'gr_date',
        'cases',
        'road_permit_no',
        'truck_no',
        'transport',
        'remarks',
        'on_field',
        'rate',
        'tag',
        'total_qty',
        'total_amount',
        'net_amount',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'gr_date' => 'date',
        'rate' => 'decimal:2',
        'total_qty' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    /**
     * Get the items for this transaction
     */
    public function items()
    {
        return $this->hasMany(SampleIssuedTransactionItem::class, 'sample_issued_transaction_id');
    }

    /**
     * Generate unique transaction number (per organization)
     */
    public static function generateTrnNumber(): string
    {
        $orgId = auth()->user()->organization_id ?? 1;
        $prefix = 'SI';
        $year = date('y');
        $month = date('m');
        
        $lastTransaction = self::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->where('trn_no', 'LIKE', "{$prefix}{$year}{$month}%")
            ->orderBy('trn_no', 'desc')
            ->first();
        
        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->trn_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get party type options
     */
    public static function getPartyTypes(): array
    {
        return [
            'CUSTOMER' => 'Customer',
            'SALES_MAN' => 'Sales Man',
            'AREA_MGR' => 'Area Manager',
            'REG_MGR' => 'Regional Manager',
            'MKT_MGR' => 'Marketing Manager',
            'GEN_MGR' => 'General Manager',
        ];
    }

    /**
     * Scope to filter active (non-deleted) transactions
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }
}
