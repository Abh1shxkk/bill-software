<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class AreaManager extends Model
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
        'reg_mgr'
    ];

    protected $casts = [
        //
    ];
}
