<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class StockTransferIncomingReturnTransaction extends Model
{
    use BelongsToOrganization;

    protected $table = 'stock_transfer_incoming_return_transactions';

    protected $fillable = [
        'trn_no',
        'series',
        'transaction_date',
        'day_name',
        'name',
        'gr_no',
        'gr_date',
        'cases',
        'transport',
        'remarks',
        'net_amount',
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
        'gr_date' => 'date',
        'net_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(StockTransferIncomingReturnTransactionItem::class, 'stock_transfer_incoming_return_transaction_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    public static function generateTrnNumber()
    {
        $orgId = auth()->user()->organization_id ?? 1;
        
        $lastTransaction = self::whereNotNull('trn_no')
            ->withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastTransaction || !$lastTransaction->trn_no) {
            return 'STIR00001';
        }

        $lastNumber = (int) substr($lastTransaction->trn_no, 4);
        $newNumber = $lastNumber + 1;
        
        return 'STIR' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
