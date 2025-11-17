<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportMaster extends Model
{
    use HasFactory;

    protected $table = 'transport_masters';

    protected $fillable = [
        'name',
        'address',
        'alter_code',
        'telephone',
        'email',
        'mobile',
        'gst_no',
        'status',
        'vehicle_no',
        'trans_mode',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
