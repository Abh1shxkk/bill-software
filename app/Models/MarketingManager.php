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
        'telephone',
        'mobile',
        'email',
        'status',
        'gen_mgr'
    ];

    protected $casts = [
        //
    ];
}
