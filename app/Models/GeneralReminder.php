<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class GeneralReminder extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'general_reminders';

    protected $fillable = [
        'name',
        'code',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
