<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'email',
        'contact_person_1',
        'contact_person_2',
        'website',
        'alter_code',
        'telephone',
        'short_name',
        'location',
        'mobile_1',
        'mobile_2',
        'pur_sc',
        'sale_sc',
        'expiry',
        'dis_on_sale_percent',
        'min_gp',
        'pur_tax',
        'sale_tax',
        'generic',
        'invoice_print_order',
        'direct_indirect',
        'surcharge_after_dis_yn',
        'add_surcharge_yn',
        'vat_percent',
        'inclusive_yn',
        'lock_aiocd',
        'lock_ims',
        'disallow_expiry_after_months',
        'fixed_maximum',
        'discount',
        'flag',
        'status',
        'notes',
        'is_deleted',
        'deleted_at'
    ];
}


