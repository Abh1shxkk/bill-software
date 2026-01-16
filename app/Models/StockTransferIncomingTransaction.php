<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class StockTransferIncomingTransaction extends Model
{
    use BelongsToOrganization;

    protected $table = 'stock_transfer_incoming_transactions';

    protected $fillable = [
        'trf_no',
        'series',
        'transaction_date',
        'day_name',
        'supplier_id',
        'supplier_name',
        'st_date',
        'gr_no',
        'gr_date',
        'cases',
        'transport',
        'remarks',
        'total_amount',
        'packing',
        'unit',
        'cl_qty',
        'comp',
        'lctn',
        'srlno',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'st_date' => 'date',
        'gr_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(StockTransferIncomingTransactionItem::class, 'stock_transfer_incoming_transaction_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    public static function generateTrfNumber()
    {
        $orgId = auth()->user()->organization_id ?? 1;
        
        // Get max number using SQL to avoid counting issues
        $maxNumber = self::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->whereNotNull('trf_no')
            ->where('trf_no', 'LIKE', 'STI%')
            ->selectRaw("MAX(CAST(SUBSTRING(trf_no, 4) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $nextNumber = ($maxNumber ?? 0) + 1;
        $trfNo = 'STI' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        
        // Double-check to avoid duplicates
        while (self::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->where('trf_no', $trfNo)
            ->exists()) {
            $nextNumber++;
            $trfNo = 'STI' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }
        
        return $trfNo;
    }
}
