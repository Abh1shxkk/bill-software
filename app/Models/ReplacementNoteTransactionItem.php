<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class ReplacementNoteTransactionItem extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'replacement_note_transaction_id',
        'item_id',
        'batch_id',
        'item_code',
        'item_name',
        'batch_no',
        'expiry',
        'expiry_date',
        'qty',
        'mrp',
        'amount',
        'packing',
        'unit',
        'company_name',
        'hsn_code',
        'row_order',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty' => 'decimal:2',
        'mrp' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(ReplacementNoteTransaction::class, 'replacement_note_transaction_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
