<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'name',
        'alter_code',
        'status',
        'is_deleted'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Scope to exclude deleted records
    public function scopeActive($query)
    {
        return $query->where('is_deleted', '!=', 1);
    }
}
