<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $primaryKey = 'supplier_id';
    public $timestamps = false;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'supplier_id';
    }

    protected $fillable = [
        'name','code','address','telephone','email','tax_retail_flag','tan_no','msme_lic','opening_balance','opening_balance_type','credit_limit','b_day','a_day','contact_person_1','contact_person_2','mobile','mobile_additional','fax','status','flag','dl_no','dl_no_1','food_lic','cst_no','tin_no','pan','gst_no','state_code','local_central_flag','discount_on_excise','scheme_type','discount_after_scheme','direct_indirect','invoice_on_trade_rate','net_rate_yn','visit_days','invoice_roff','scheme_in_decimal','vat_on_bill_expiry','tax_on_fqty','expiry_on_mrp_sale_rate_purchase_rate','sale_purchase_status','composite_scheme','stock_transfer','cash_purchase','add_charges_with_gst','purchase_import_box_conversion','full_name','aadhar','registered_unregistered_composite','registration_date','tcs_applicable','tds_yn','tds_on_return','tds_tcs_on_bill_amount','bank','branch','account_no','ifsc_code','notebook','remarks','created_by','updated_by','is_deleted','deleted_at'
    ];

    /**
     * Get pending orders for this supplier
     */
    public function pendingOrders()
    {
        return $this->hasMany(PendingOrder::class, 'supplier_id', 'supplier_id');
    }
}


