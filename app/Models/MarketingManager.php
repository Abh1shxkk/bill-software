<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingManager extends Model
{
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
