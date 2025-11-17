<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $timestamps = false;

   protected $fillable = [
    'name',
    'code',
    'tax_registration',
    'pin_code',
    'address',
    'address_line2',
    'address_line3',
    'address2',
    'address2_line2',
    'address2_line3',
    'city',
    'country_code',
    'country_name',
    'state_code',
    'state_name',        
    'telephone_office',
    'telephone_residence',
    'mobile',
    'email',
    'contact_person1',
    'mobile_contact1',
    'contact_person2',
    'mobile_contact2',
    'fax_number',
    'opening_balance',
    'balance_type',
    'local_central',
    'credit_days',
    'birth_day',
    'anniversary_day',
    'status',
    'flag',
    'invoice_export',
    'due_list_sequence',
    'tan_number',
    'msme_license',
    'dl_number',
    'dl_expiry',
    'dl_number1',
    'food_license',
    'cst_number',
    'tin_number',
    'pan_number',
    'sales_man_code',
    'sales_man_name',
    'area_code',
    'area_name',
    'route_code',
    'route_name',
    'business_type',
    'description',
    'order_required',
    'aadhar_number',
    'registration_date',
    'end_date',
    'day_value',
    'gst_number',
    'cst_registration',
    'gst_name',
    'state_code_gst',
    'registration_status',
    'bank',
    'branch',
    'closed_on',
    'credit_limit',
    'sale_rate_type',
    'add_percent',
    'tax_on_br_expiry',
    'expiry_on',
    'dis_after_scheme',
    'expiry_rn_on',
    'dis_on_excise',
    'sale_pur_status',
    'scm_type',
    'net_rate',
    'no_of_items_in_bill',
    'invoice_print_order',
    'sr_replacement',
    'cash_sale',
    'invoice_format',
    'fixed_discount',
    'gst_5_percent',
    'gst_12_percent',
    'gst_18_percent',
    'gst_28_percent',
    'gst_0_percent',
    'ref',
    'tds',
    'add_charges_with_gst',
    'tcs_applicable',
    'be_incl',
    'brk_expiry_msg_in_sale',
    'series_lock',
    'branch_trf',
    'trnf_account',
    'transport_code',
    'transport_name',
    'distance',
    'expiry_repl_credit',
    'max_os_amount',
    'max_limit_on',
    'max_inv_amount',
    'max_no_os_inv',
    'follow_conditions_strictly',
    'credit_limit_days_lock',
    'open_lock_once',
    'expiry_lock_type',
    'expiry_lock_value',
    'no_of_expiries_per_month',
    'created_date',
    'modified_date',
    'created_by',
    'modified_by',
    'is_deleted',
    'deleted_at'
];

    /**
     * Relationship with Customer Ledger
     */
    public function ledgers()
    {
        return $this->hasMany(CustomerLedger::class);
    }

    /**
     * Relationship with Customer Dues
     */
    public function dues()
    {
        return $this->hasMany(CustomerDue::class);
    }

    /**
     * Relationship with Customer Special Rates
     */
    public function specialRates()
    {
        return $this->hasMany(CustomerSpecialRate::class);
    }

    /**
     * Relationship with Customer Discounts
     */
    public function discounts()
    {
        return $this->hasMany(CustomerDiscount::class);
    }

    /**
     * Relationship with Customer Challans
     */
    public function challans()
    {
        return $this->hasMany(CustomerChallan::class);
    }

    /**
     * Relationship with Customer Prescriptions
     */
    public function prescriptions()
    {
        return $this->hasMany(CustomerPrescription::class);
    }

}


