<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralManager extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'telephone',
        'mobile',
        'email',
        'status',
        'dc_mgr'
    ];

    protected $casts = [
        //
    ];
}
