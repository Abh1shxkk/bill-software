<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class PendingOrderItem extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'item_id',
        'item_code',
        'item_name',
        'action_type',
        'quantity',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getActionLabel()
    {
        return $this->action_type === 'I' ? 'Insert' : 'Delete';
    }
}
