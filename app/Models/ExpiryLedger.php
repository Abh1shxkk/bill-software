<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpiryLedger extends Model
{
    protected $table = 'expiry_ledger';

    protected $fillable = [
        'item_id',
        'batch_id',
        'customer_id',
        'supplier_id',
        'transaction_date',
        'trans_no',
        'transaction_type',
        'party_name',
        'quantity',
        'free_quantity',
        'running_balance',
        'expiry_date',
        'remarks',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'expiry_date' => 'date',
        'running_balance' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
}
