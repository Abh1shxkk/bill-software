<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class DebitNoteAdjustment extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'debit_note_id',
        'adjustment_type',
        'purchase_transaction_id',
        'purchase_invoice_no',
        'purchase_invoice_date',
        'purchase_invoice_amount',
        'purchase_balance_amount',
        'credit_note_id',
        'credit_note_no',
        'credit_note_date',
        'credit_note_amount',
        'credit_note_balance',
        'adjusted_amount',
        'remarks',
    ];

    protected $casts = [
        'purchase_invoice_date' => 'date',
        'credit_note_date' => 'date',
        'purchase_invoice_amount' => 'decimal:2',
        'purchase_balance_amount' => 'decimal:2',
        'credit_note_amount' => 'decimal:2',
        'credit_note_balance' => 'decimal:2',
        'adjusted_amount' => 'decimal:2',
    ];

    /**
     * Get the debit note that owns the adjustment
     */
    public function debitNote()
    {
        return $this->belongsTo(DebitNote::class);
    }

    /**
     * Get the purchase transaction
     */
    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class);
    }

    /**
     * Get the credit note
     */
    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }
}
