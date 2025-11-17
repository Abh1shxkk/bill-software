<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsnCode extends Model
{
    protected $fillable = [
        'name',
        'hsn_code',
        'cgst_percent',
        'sgst_percent',
        'igst_percent',
        'total_gst_percent',
        'is_inactive',
        'is_service',
    ];

    protected $casts = [
        'cgst_percent' => 'decimal:2',
        'sgst_percent' => 'decimal:2',
        'igst_percent' => 'decimal:2',
        'total_gst_percent' => 'decimal:2',
        'is_inactive' => 'boolean',
        'is_service' => 'boolean',
    ];
}
