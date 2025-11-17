<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_code',
        'alter_code',
        'under',
        'opening_balance',
        'balance_type',
        'input_gst_purchase',
        'output_gst_income',
        'flag',
        'address',
        'address_line2',
        'address_line3',
        'birth_day',
        'anniversary_day',
        'telephone',
        'email',
        'fax',
        'contact_person_1',
        'mobile_1',
        'contact_person_2',
        'mobile_2',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
    ];
}
