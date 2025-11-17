<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'ledger_name',
        'form_type',
        'sale_tax',
        'desc',
        'type',
        'status',
        'alter_code',
        'opening_balance',
        'form_required',
        'charges',
        'under',
        'address',
        'birth_day',
        'anniversary',
        'telephone',
        'fax',
        'email',
        'contact_1',
        'mobile_1',
        'contact_2',
        'mobile_2',
    ];

    protected $casts = [
        'sale_tax' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'charges' => 'decimal:2',
        'birth_day' => 'date',
        'anniversary' => 'date',
    ];
}
