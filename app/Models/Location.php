<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Location extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'state',
        'pincode',
        'phone',
        'email',
        'is_deleted',
        'status'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }
}
