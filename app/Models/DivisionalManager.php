<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivisionalManager extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'telephone',
        'mobile',
        'email',
        'status',
        'c_mgr'
    ];

    protected $casts = [
        //
    ];
}
