<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        // Company Information
        'company_id', 'company_short_name',
        
        // Basic Item Information
        'name', 'packing', 'mfg_by', 'location', 'status', 'schedule', 'box_qty', 'case_qty',
        'bar_code', 'division', 'flag',
        
        // Header Section - Part 1
        'unit', 'unit_type', 'min_level', 'max_level', 'narcotic_flag',
        
        // Sale Details Section
        's_rate', 'mrp', 'ws_rate', 'ws_net_toggle', 'spl_rate', 'spl_net_toggle', 'scheme_plus', 'scheme_minus', 'min_gp',
        
        // Purchase Details Section
        'pur_rate', 'cost', 'pur_scheme_plus', 'pur_scheme_minus', 'nr',
        
        // GST Details Section
        'hsn_code', 'cgst_percent', 'sgst_percent', 'igst_percent', 'cess_percent',
        
        // Other Details Section
        'vat_percent', 'fixed_dis', 'fixed_dis_percent', 'fixed_dis_type', 'expiry_flag', 'inclusive_flag', 'generic_flag',
        'h_scm_flag', 'q_scm_flag', 'locks_flag', 'max_inv_qty_value', 'max_inv_qty_new', 'weight_new', 'bar_code_flag',
        'def_qty_flag', 'volume_new', 'comp_name_bc_new', 'dpc_item_flag', 'lock_sale_flag', 'max_min_flag',
        'mrp_for_sale_new',
        
        // Bottom Section
        'commodity', 'current_scheme_flag', 'from_date', 'to_date', 'scheme_plus_value', 'scheme_minus_value', 'category', 'category_2', 'upc',
        
        // System fields
        'is_deleted'
    ];

    /**
     * Relationship with Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relationship with Batches
     */
    public function batches()
    {
        return $this->hasMany(Batch::class, 'item_id');
    }

    /**
     * Relationship with Stock Ledger
     */
    public function stockLedgers()
    {
        return $this->hasMany(StockLedger::class, 'item_id');
    }

    /**
     * Relationship with Purchase Transaction Items
     */
    public function purchaseTransactionItems()
    {
        return $this->hasMany(PurchaseTransactionItem::class, 'item_id');
    }

    /**
     * Get total quantity from all batches
     */
    public function getTotalQuantity()
    {
        return $this->batches()->where('is_deleted', 0)->sum('qty');
    }

    /**
     * Get active batches
     */
    public function getActiveBatches()
    {
        return $this->batches()->where('is_deleted', 0)->where('status', 'active')->get();
    }

    protected $casts = [
        // All fields are now VARCHAR to accept various data formats
        'Expiry' => 'string',
        'ScmFrom' => 'string',
        'ScmTo' => 'string',
        'status' => 'string',
        'is_deleted' => 'string',
        'DisContinue' => 'string',
        'LockScm' => 'string',
        'RateLock' => 'string',
        'Add_sc' => 'string',
        'Add_tsr' => 'string',
        'Vdt' => 'string',
        'Defqty' => 'string',
        'WsNet' => 'string',
        'SplNet' => 'string',
        'CommonItem' => 'string',
        'FDis' => 'string',
        'PresReq' => 'string',
        'discount',
        'Inclusive' => 'string',
        'Wr' => 'string',
        'TaxonMrp' => 'string',
        'VATonSrate' => 'string',
        'Exon' => 'string',
        'LockBilling' => 'string',
        'SameBatchCost' => 'string',
        'splrate' => 'string',
        'Mrp' => 'string',
        'FDisWR' => 'string',
    ];

    // Accessor methods to convert string values to float for calculations
    public function getPrateAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getTsrAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getPtaxAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getExciseAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getSrateAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getStaxAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getWsrateAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getAddScAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getAddTsrAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getCostrateAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getOpqtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getClqtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getDefqtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getSplrateAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getMrpAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getWsNetAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getSplNetAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getFDisAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getFDisPAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getScmPerAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getWrAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getSconMrpAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getVATAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getMarginAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getPRateCaseAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getPRateBoxAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getMinQtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getMaxQtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getTRateAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getTempOpqtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getTempClqtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getTempAmtAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getTempAmt1Attribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getTempAmt2Attribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getIWeightAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getMaxInvQtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getMaxQtyWrAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getOpFreeQtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getExPerAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getMfgQtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getVolAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getVDisPAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getVDisSAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getFDisWRAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getLastYearCostAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getOpAddQtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getSaleLessQtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getSplDisQtyAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getSplDisPerAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getMinGPAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getMinRateAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getPurExciseAsRateAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getPurNetRateAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getCostWFQAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getFdisPWSAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getCGSTAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getSGSTAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getIGSTAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    public function getGSTCessAttribute($value)
    {
        return $this->cleanNumericValue($value);
    }

    // Helper method to clean numeric values
    private function cleanNumericValue($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        
        // Remove $ symbol and other non-numeric characters except decimal point and minus
        $cleaned = preg_replace('/[^0-9.-]/', '', $value);
        
        return floatval($cleaned);
    }
}


