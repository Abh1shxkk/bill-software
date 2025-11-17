<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBankBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alter_code',
        'under',
        'opening_balance',
        'opening_balance_type',
        'credit_card',
        'bank_charges',
        'address',
        'address1',
        'telephone',
        'email',
        'fax',
        'birth_day',
        'anniversary_day',
        'contact_person_1',
        'contact_person_2',
        'mobile_1',
        'mobile_2',
        'input_gst_purchase',
        'output_gst_income',
        'account_no',
        'report_no',
        'cheque_clearance_method',
        'flag',
        'receipts',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'birth_day' => 'date',
        'anniversary_day' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'bank_charges' => 'decimal:2',
        'input_gst_purchase' => 'boolean',
        'output_gst_income' => 'boolean',
    ];
}
