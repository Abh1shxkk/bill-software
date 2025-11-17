<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'name',
        'alter_code',
        'status'
    ];

    protected $casts = [
        //
    ];
}
