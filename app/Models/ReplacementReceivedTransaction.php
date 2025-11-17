<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplacementReceivedTransaction extends Model
{
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
        $lastTransaction = self::whereNotNull('rr_no')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastTransaction || !$lastTransaction->rr_no) {
            return 'RR00001';
        }

        $lastNumber = (int) substr($lastTransaction->rr_no, 2);
        $newNumber = $lastNumber + 1;
        
        return 'RR' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
