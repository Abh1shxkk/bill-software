<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Area extends Model
{
    use BelongsToOrganization;

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
