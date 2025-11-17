<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_no',
        'series',
        'return_date',
        'supplier_id',
        'supplier_name',
        'invoice_no',
        'invoice_date',
        'gst_vno',
        'tax_flag',
        'rate_diff_flag',
        'nt_amount',
        'sc_amount',
        'dis_amount',
        'scm_amount',
        'scm_percent',
        'tax_amount',
        'net_amount',
        'tcs_amount',
        'dis1_amount',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'invoice_date' => 'date',
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

    /**
     * Get the supplier that owns the purchase return transaction.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id')
            ->withDefault(['name' => '']);
    }

    /**
     * Get the items for the purchase return transaction.
     */
    public function items()
    {
        return $this->hasMany(PurchaseReturnTransactionItem::class, 'purchase_return_transaction_id');
    }

    /**
     * Get the adjustments for the purchase return transaction.
     */
    public function adjustments()
    {
        return $this->hasMany(PurchaseReturnAdjustment::class, 'purchase_return_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the user who last updated the transaction.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    /**
     * Generate next PR number
     */
    public static function generatePRNumber()
    {
        $lastReturn = self::orderBy('id', 'desc')->first();
        
        if ($lastReturn) {
            $lastNumber = (int) substr($lastReturn->pr_no, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'PR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
