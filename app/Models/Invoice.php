<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $primaryKey = 'invoice_id';

    public function getRouteKeyName()
    {
        return 'invoice_id';
    }

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'company_id',
        'company_name',
        'company_address',
        'company_email',
        'company_phone',
        'company_gst',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_gst',
        'customer_state',
        'customer_state_code',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'cess_amount',
        'notes',
        'terms_conditions',
        'payment_terms',
        'currency',
        'created_by',
        'updated_by',
        'is_deleted',
        'deleted_at'
    ];

    // Relation to invoice items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    // Relation to company
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    // Relation to customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}


