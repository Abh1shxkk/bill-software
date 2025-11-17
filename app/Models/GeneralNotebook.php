<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralNotebook extends Model
{
    use HasFactory;

    protected $table = 'general_notebooks';

    protected $fillable = [
        // Fields will be added as you specify
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
