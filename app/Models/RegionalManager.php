<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class RegionalManager extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'name',
        'code',
        'address',
        'telephone',
        'mobile',
        'email',
        'status',
        'mkt_mgr'
    ];

    protected $casts = [
        //
    ];
}
