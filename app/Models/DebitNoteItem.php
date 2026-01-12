<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class DebitNoteItem extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'debit_note_id',
        'hsn_code',
        'amount',
        'gst_percent',
        'cgst_percent',
        'cgst_amount',
        'sgst_percent',
        'sgst_amount',
        'igst_percent',
        'igst_amount',
        'row_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'cgst_percent' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_percent' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_percent' => 'decimal:2',
        'igst_amount' => 'decimal:2',
    ];

    /**
     * Get the debit note
     */
    public function debitNote()
    {
        return $this->belongsTo(DebitNote::class);
    }
}
