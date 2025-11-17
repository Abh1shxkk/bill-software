<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesMan extends Model
{
    protected $table = 'sales_men';
    
    protected $fillable = [
        'code',
        'name',
        'email',
        'mobile',
        'telephone',
        'address',
        'city',
        'pin',
        'sales_type',
        'delivery_type',
        'area_mgr_code',
        'area_mgr_name',
        'monthly_target',
        'status',
        'is_deleted',
        'created_date',
        'modified_date'
    ];

    protected $casts = [
        'monthly_target' => 'decimal:2',
        'is_deleted' => 'integer',
        'created_date' => 'datetime',
        'modified_date' => 'datetime'
    ];
}
