@extends('layouts.admin')
@section('title', 'Supplier Details')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-truck me-2"></i> Supplier Details</h4>
    <div class="text-muted small">Detailed supplier record</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.suppliers.pending-orders', $supplier) }}" class="btn btn-info btn-sm">
      <i class="bi bi-cart-check me-1"></i> Pending Orders
    </a>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Back to Suppliers
    </a>
  </div>
</div>

<div class="card shadow-sm border-0 rounded">
  <div class="card-body">
    <!-- Basic Information Card -->
    <div class="mb-4">
      <h5 class="mb-3 d-flex align-items-center">
        <i class="bi bi-info-circle me-2"></i> Basic Information
      </h5>
      <div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Name</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->name ?? '-' }}</p>
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">Code</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->code ?? '-' }}</p>
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">T(ax) / R(etail)</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->tax_retail_flag ?? '-' }}</p>
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">TAN No.</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->tan_no ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">MSME Lic.</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->msme_lic ?? '-' }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted mb-1">Address</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->address ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Opening Bal.</label>
              <p class="fw-medium text-dark mb-0">₹{{ number_format($supplier->opening_balance ?? 0, 2) }} ({{ $supplier->opening_balance_type ?? 'C' }})</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Status</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->status ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Telephone</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->telephone ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Fax</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->fax ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Flag</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->flag ?? '-' }}</p>
            </div>
          </div>
      </div>
    </div>

    <!-- Contact Information Card -->
    <div class="mb-4">
      <h5 class="mb-3 d-flex align-items-center">
        <i class="bi bi-telephone me-2"></i> Contact Information
      </h5>
      <div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">E-Mail</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->email ?? '-' }}</p>
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">B' Day</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->b_day ? \Carbon\Carbon::parse($supplier->b_day)->format('d-m-Y') : '-' }}</p>
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">A' Day</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->a_day ? \Carbon\Carbon::parse($supplier->a_day)->format('d-m-Y') : '-' }}</p>
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">Mobile</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->mobile ?? '-' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label small text-muted mb-1">Contact Person I</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->contact_person_1 ?? '-' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label small text-muted mb-1">Contact Person II</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->contact_person_2 ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Mobile Additional</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->mobile_additional ?? '-' }}</p>
            </div>
          </div>
      </div>
    </div>

    <!-- License & Registration Information Card -->
    <div class="mb-4">
      <h5 class="mb-3 d-flex align-items-center">
        <i class="bi bi-file-text me-2"></i> License & Registration Information
      </h5>
      <div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">D.L No.</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->dl_no ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">D.L No.-1</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->dl_no_1 ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Food Lic.</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->food_lic ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">CST No.</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->cst_no ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">TIN No.</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->tin_no ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">PAN</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->pan ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">State Code</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->state_code ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">L(ocal)/C(entral)</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->local_central_flag ?? '-' }}</p>
            </div>
          </div>
      </div>
    </div>

    <!-- Purchase & Trade Settings Card -->
    <div class="mb-4">
      <h5 class="mb-3 d-flex align-items-center">
        <i class="bi bi-cart me-2"></i> Purchase & Trade Settings
      </h5>
      <div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Inv. on F.T. Rate</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->invoice_on_trade_rate ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Net Rate [ Y/N/M ]</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->net_rate_yn ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Visit Days</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->visit_days ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Invoice Roff</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->invoice_roff > 0 ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Scm. In Decimal</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->scheme_in_decimal ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Dis. On Excise ?</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->discount_on_excise ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Sch. Type [H/F]</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->scheme_type ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Cr. Limit</label>
              <p class="fw-medium text-dark mb-0">₹{{ number_format($supplier->credit_limit ?? 0, 2) }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Composite Scheme</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->composite_scheme ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Stock Transfer</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->stock_transfer ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Cash Purchase</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->cash_purchase ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Sale Pur. Status</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->sale_purchase_status ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Dis. After Scheme</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->discount_after_scheme ? 'Y' : 'N' }}</p>
            </div>
          </div>
      </div>
    </div>

    <!-- Tax & GST Settings Card -->
    <div class="mb-4">
      <h5 class="mb-3 d-flex align-items-center">
        <i class="bi bi-percent me-2"></i> Tax & GST Settings
      </h5>
      <div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">VAT on Br./Expiry</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->vat_on_bill_expiry ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Tax on FQty</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->tax_on_fqty ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Add Charges with GST</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->add_charges_with_gst ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Expiry on MRP/Sale Rate/Purchase Rate</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->expiry_on_mrp_sale_rate_purchase_rate ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Pur. Import : Box Conversion</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->purchase_import_box_conversion ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">TCS Applicable [ Y / N / # ]</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->tcs_applicable ?? '-' }}</p>
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">TDS [ Y / N ]</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->tds_yn ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">TDS on Return</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->tds_on_return ? 'Y' : 'N' }}</p>
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">TDS/TCS on Bill Amt.</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->tds_tcs_on_bill_amount ? 'Y' : 'N' }}</p>
            </div>
          </div>
      </div>
    </div>

    <!-- Personal & Banking Information Card -->
    <div class="mb-4">
      <h5 class="mb-3 d-flex align-items-center">
        <i class="bi bi-person-badge me-2"></i> Personal & Banking Information
      </h5>
      <div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Direct / Indirect</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->direct_indirect ?? 'T' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Full Name</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->full_name ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Aadhar</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->aadhar ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">R(egistered)/U(nregistered)/C(omposite)</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->registered_unregistered_composite ?? 'U' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Bank</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->bank ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">Branch</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->branch ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">A/c No.</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->account_no ?? '-' }}</p>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted mb-1">IFSC</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->ifsc_code ?? '-' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label small text-muted mb-1">GST No.</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->gst_no ?? '-' }}</p>
            </div>
            <div class="col-md-4">
              <label class="form-label small text-muted mb-1">Regd. Date</label>
              <p class="fw-medium text-dark mb-0">{{ $supplier->registration_date ? \Carbon\Carbon::parse($supplier->registration_date)->format('d-m-Y') : '-' }}</p>
            </div>
          </div>
      </div>
    </div>

    <!-- Additional Notes Card -->
    <div class="mb-4">
      <h5 class="mb-3 d-flex align-items-center">
        <i class="bi bi-file-text me-2"></i> Additional Notes
      </h5>
      <div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small text-muted mb-1">Notebook</label>
              <p class="fw-medium text-dark mb-0" style="white-space: pre-wrap;">{{ $supplier->notebook ?? '-' }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted mb-1">Remarks</label>
              <p class="fw-medium text-dark mb-0" style="white-space: pre-wrap;">{{ $supplier->remarks ?? '-' }}</p>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
@endsection