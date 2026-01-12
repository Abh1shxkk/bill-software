<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToOrganization;

class ClaimToSupplierTransaction extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'claim_no',
        'series',
        'claim_date',
        'supplier_id',
        'supplier_name',
        'invoice_no',
        'invoice_date',
        'gst_vno',
        'tax_flag',
        'narration',
        // Additional Details fields
        'blank_statement',
        'rate_type',
        'filter_from_date',
        'filter_to_date',
        'company_code',
        'division',
        // Amount fields
        'nt_amount',
        'sc_amount',
        'dis_amount',
        'scm_amount',
        'scm_percent',
        'tax_amount',
        'net_amount',
        'balance_amount',
        'tcs_amount',
        'dis1_amount',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'invoice_date' => 'date',
        'filter_from_date' => 'date',
        'filter_to_date' => 'date',
        'nt_amount' => 'decimal:2',
        'sc_amount' => 'decimal:2',
        'dis_amount' => 'decimal:2',
        'scm_amount' => 'decimal:2',
        'scm_percent' => 'decimal:3',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'tcs_amount' => 'decimal:2',
        'dis1_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id')
            ->withDefault(['name' => '']);
    }

    public function items()
    {
        return $this->hasMany(ClaimToSupplierTransactionItem::class, 'claim_to_supplier_transaction_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    public static function generateClaimNumber()
    {
        $lastClaim = self::withTrashed()->orderBy('id', 'desc')->first();
        
        if ($lastClaim) {
            $lastNumber = (int) substr($lastClaim->claim_no, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'CTS' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
