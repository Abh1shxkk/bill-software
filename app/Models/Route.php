<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Route extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'name',
        'alter_code',
        'status'
    ];

    protected $casts = [
        //
    ];
}
