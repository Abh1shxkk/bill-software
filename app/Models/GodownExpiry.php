<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GodownExpiry extends Model
{
    protected $table = 'godown_expiry';

    protected $fillable = [
        'item_id',
        'batch_id',
        'expiry_date',
        'quantity',
        'godown_location',
        'status',
        'remarks',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function isExpired()
    {
        return $this->expiry_date < now()->toDateString();
    }

    public function daysUntilExpiry()
    {
        return now()->diffInDays($this->expiry_date);
    }
}
