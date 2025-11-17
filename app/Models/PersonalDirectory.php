<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalDirectory extends Model
{
    use HasFactory;

    protected $table = 'personal_directories';

    protected $fillable = [
        'name',
        'alt_code',
        'address_office',
        'address_residence',
        'tel_office',
        'tel_residence',
        'mobile',
        'fax',
        'email',
        'status',
        'contact_person',
        'birthday',
        'anniversary',
        'spouse',
        'spouse_dob',
        'child_1',
        'child_1_dob',
        'child_2',
        'child_2_dob',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
