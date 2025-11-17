<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        // Ledger Information
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
        // Contact Information
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
        'birth_day' => 'date',
        'anniversary' => 'date',
        'sale_tax' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'charges' => 'decimal:2',
    ];
}
