<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionalManager extends Model
{
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
