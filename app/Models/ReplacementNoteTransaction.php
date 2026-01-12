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
        $lastTransaction = self::orderBy('id', 'desc')->first();
        
        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->rn_no, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'RN' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
