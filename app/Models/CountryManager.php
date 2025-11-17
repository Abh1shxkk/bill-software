<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryManager extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'telephone',
        'mobile',
        'email',
        'status'
    ];

    protected $casts = [
        //
    ];
}
