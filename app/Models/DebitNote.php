<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebitNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'debit_note_no',
        'debit_note_date',
        'day_name',
        'debit_party_type',
        'debit_party_id',
        'debit_party_name',
        'credit_account_type',
        'credit_account_no',
        'inv_ref_no',
        'invoice_date',
        'gst_vno',
        'party_trn_no',
        'party_trn_date',
        'amount',
        'salesman_id',
        'reason',
        'gross_amount',
        'total_gst',
        'net_amount',
        'tcs_amount',
        'round_off',
        'dn_amount',
        'narration',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'debit_note_date' => 'date',
        'invoice_date' => 'date',
        'party_trn_date' => 'date',
        'amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'total_gst' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'tcs_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'dn_amount' => 'decimal:2',
    ];

    /**
     * Get the items for the debit note
     */
    public function items()
    {
        return $this->hasMany(DebitNoteItem::class);
    }

    /**
     * Get the adjustments for the debit note
     */
    public function adjustments()
    {
        return $this->hasMany(DebitNoteAdjustment::class);
    }

    /**
     * Get total adjusted amount
     */
    public function getTotalAdjustedAttribute()
    {
        return $this->adjustments()->sum('adjusted_amount');
    }

    /**
     * Get remaining balance to adjust
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->dn_amount - $this->total_adjusted;
    }

    /**
     * Get the supplier (if debit party is supplier)
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'debit_party_id', 'supplier_id');
    }

    /**
     * Get the customer (if debit party is customer)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'debit_party_id');
    }

    /**
     * Get the salesman
     */
    public function salesman()
    {
        return $this->belongsTo(SalesMan::class, 'salesman_id');
    }

    /**
     * Get the party based on type
     */
    public function getPartyAttribute()
    {
        if ($this->debit_party_type === 'S') {
            return $this->supplier;
        }
        return $this->customer;
    }

    /**
     * Get the creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
