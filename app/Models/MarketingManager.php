<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class MarketingManager extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'name',
        'code',
        'address',
        'mobile',
        'email',
        'designation',
        'target_amount',
        'reporting_to',
        'status',
        'is_deleted',
        'created_date',
        'modified_date'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'is_deleted' => 'integer',
        'created_date' => 'datetime',
        'modified_date' => 'datetime'
    ];
}

