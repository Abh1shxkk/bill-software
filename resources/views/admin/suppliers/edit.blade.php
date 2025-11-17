@extends('layouts.admin')
@section('title', 'Edit Supplier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Edit Supplier - {{ $supplier->name }}</h2>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Suppliers
                </a>
            </div>

            <!-- Error Display -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Errors Found!</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                
                <script>
                    // Show alert with all errors
                    let errors = @json($errors->all());
                    if(errors.length > 0) {
                        alert('Update Failed!\n\nErrors:\n' + errors.join('\n'));
                    }
                </script>
            @endif

            <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $supplier->name) }}" placeholder="TYHDRT">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Code</label>
                                <input type="text" class="form-control" name="code" 
                                       value="{{ old('code', $supplier->code) }}" placeholder="Enter code">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">T(ax) / R(etail)</label>
                                <select class="form-select" name="tax_retail_flag">
                                    <option value="T" {{ old('tax_retail_flag', $supplier->tax_retail_flag) == 'T' ? 'selected' : '' }}>T</option>
                                    <option value="R" {{ old('tax_retail_flag', $supplier->tax_retail_flag) == 'R' ? 'selected' : '' }}>R</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">TAN No.</label>
                                <input type="text" class="form-control" name="tan_no" 
                                       value="{{ old('tan_no', $supplier->tan_no) }}" placeholder="TAN number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">MSME Lic.</label>
                                <input type="text" class="form-control" name="msme_lic" 
                                       value="{{ old('msme_lic', $supplier->msme_lic) }}" placeholder="MSME license">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          name="address" rows="3" placeholder="Enter complete address">{{ old('address', $supplier->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Opening Bal.</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" 
                                                   name="opening_balance" value="{{ old('opening_balance', $supplier->opening_balance ?? '0.00') }}" 
                                                   placeholder="0.00">
                                            <select class="form-select" name="opening_balance_type" style="max-width: 80px;">
                                                <option value="C" {{ old('opening_balance_type', $supplier->opening_balance_type ?? 'C') == 'C' ? 'selected' : '' }}>C</option>
                                                <option value="D" {{ old('opening_balance_type', $supplier->opening_balance_type ?? 'C') == 'D' ? 'selected' : '' }}>D</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <input type="text" class="form-control" name="status" 
                                               value="{{ old('status', $supplier->status) }}" placeholder="Enter 5-digit status" maxlength="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fax</label>
                                        <input type="text" class="form-control" name="fax" 
                                               value="{{ old('fax', $supplier->fax) }}" placeholder="Fax number">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Flag</label>
                                        <input type="text" class="form-control" name="flag" 
                                               value="{{ old('flag', $supplier->flag) }}" placeholder="Flag">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Telephone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                                       name="telephone" value="{{ old('telephone', $supplier->telephone) }}" placeholder="Phone number">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">E-Mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email', $supplier->email) }}" placeholder="Email address">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">B' Day</label>
                                <input type="date" class="form-control" name="b_day" value="{{ old('b_day', $supplier->b_day) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">A' Day</label>
                                <input type="date" class="form-control" name="a_day" value="{{ old('a_day', $supplier->a_day) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="mobile" 
                                       value="{{ old('mobile', $supplier->mobile) }}" placeholder="Mobile number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contact Person I</label>
                                <input type="text" class="form-control" name="contact_person_1" 
                                       value="{{ old('contact_person_1', $supplier->contact_person_1) }}" placeholder="Contact person 1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contact Person II</label>
                                <input type="text" class="form-control" name="contact_person_2" 
                                       value="{{ old('contact_person_2', $supplier->contact_person_2) }}" placeholder="Contact person 2">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="mobile_additional" 
                                       value="{{ old('mobile_additional', $supplier->mobile_additional) }}" placeholder="Mobile number 2">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- License & Registration Information -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>License & Registration Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">D.L No.</label>
                                <input type="text" class="form-control" name="dl_no" 
                                       value="{{ old('dl_no', $supplier->dl_no) }}" placeholder="DL number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">D.L No.-1</label>
                                <input type="text" class="form-control" name="dl_no_1" 
                                       value="{{ old('dl_no_1', $supplier->dl_no_1) }}" placeholder="DL number 1">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Food Lic.</label>
                                <input type="text" class="form-control" name="food_lic" 
                                       value="{{ old('food_lic', $supplier->food_lic) }}" placeholder="Food license">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">CST No.</label>
                                <input type="text" class="form-control" name="cst_no" 
                                       value="{{ old('cst_no', $supplier->cst_no) }}" placeholder="CST number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">TIN No.</label>
                                <input type="text" class="form-control" name="tin_no" 
                                       value="{{ old('tin_no', $supplier->tin_no) }}" placeholder="TIN number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">PAN</label>
                                <input type="text" class="form-control" name="pan" 
                                       value="{{ old('pan', $supplier->pan) }}" placeholder="PAN number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">State Code</label>
                                <select class="form-select" name="state_code">
                                    <option value="">Select State</option>
                                    <option value="09 Uttar Pradesh" {{ old('state_code', $supplier->state_code) == '09 Uttar Pradesh' ? 'selected' : '' }}>09 Uttar Pradesh</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">L(ocal)/C(entral)</label>
                                <select class="form-select" name="local_central_flag">
                                    <option value="L" {{ old('local_central_flag', $supplier->local_central_flag) == 'L' ? 'selected' : '' }}>L</option>
                                    <option value="C" {{ old('local_central_flag', $supplier->local_central_flag) == 'C' ? 'selected' : '' }}>C</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase & Trade Settings -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-cart me-2"></i>Purchase & Trade Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Inv. on F.T.Rate</label>
                                <select class="form-select" name="invoice_on_trade_rate">
                                    @php
                                        $invoiceOnTradeRateValue = old('invoice_on_trade_rate', $supplier->invoice_on_trade_rate ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $invoiceOnTradeRateValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $invoiceOnTradeRateValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Net Rate [ Y/N/M ]</label>
                                <select class="form-select" name="net_rate_yn">
                                    @php
                                        $netRateYnValue = old('net_rate_yn', $supplier->net_rate_yn ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $netRateYnValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $netRateYnValue == 'Y' ? 'selected' : '' }}>Y</option>
                                    <option value="M" {{ $netRateYnValue == 'M' ? 'selected' : '' }}>M</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Visit Days</label>
                                <input type="text" class="form-control" name="visit_days" 
                                       value="{{ old('visit_days', $supplier->visit_days) }}" placeholder="Visit days">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Invoice Roff</label>
                                <select class="form-select" name="invoice_roff">
                                    @php
                                        $invoiceRoffValue = old('invoice_roff', $supplier->invoice_roff > 0 ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $invoiceRoffValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $invoiceRoffValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Scm. In Decimal</label>
                                <select class="form-select" name="scheme_in_decimal">
                                    @php
                                        $schemeInDecimalValue = old('scheme_in_decimal', $supplier->scheme_in_decimal ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $schemeInDecimalValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $schemeInDecimalValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Dis. On Excise ?</label>
                                <select class="form-select" name="discount_on_excise">
                                    @php
                                        $discountOnExciseValue = old('discount_on_excise', $supplier->discount_on_excise ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $discountOnExciseValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $discountOnExciseValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sch.Type [ H/F ]</label>
                                <select class="form-select" name="scheme_type">
                                    <option value="">Select</option>
                                    <option value="H" {{ old('scheme_type', $supplier->scheme_type) == 'H' ? 'selected' : '' }}>H</option>
                                    <option value="F" {{ old('scheme_type', $supplier->scheme_type) == 'F' ? 'selected' : '' }}>F</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Cr. Limit</label>
                                <input type="number" step="0.01" class="form-control" 
                                       name="credit_limit" value="{{ old('credit_limit', $supplier->credit_limit ?? '0') }}" placeholder="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Composite Scheme</label>
                                <select class="form-select" name="composite_scheme">
                                    @php
                                        $compositeSchemeValue = old('composite_scheme', $supplier->composite_scheme ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $compositeSchemeValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $compositeSchemeValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Stock Transfer</label>
                                <select class="form-select" name="stock_transfer">
                                    @php
                                        $stockTransferValue = old('stock_transfer', $supplier->stock_transfer ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $stockTransferValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $stockTransferValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Cash Purchase</label>
                                <select class="form-select" name="cash_purchase">
                                    @php
                                        $cashPurchaseValue = old('cash_purchase', $supplier->cash_purchase ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $cashPurchaseValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $cashPurchaseValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sale Pur. Status</label>
                                <select class="form-select" name="sale_purchase_status">
                                    <option value="B" {{ old('sale_purchase_status', $supplier->sale_purchase_status) == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="P" {{ old('sale_purchase_status', $supplier->sale_purchase_status) == 'P' ? 'selected' : '' }}>P</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Dis. After Scheme</label>
                                <select class="form-select" name="discount_after_scheme">
                                    @php
                                        $discountAfterSchemeValue = old('discount_after_scheme', $supplier->discount_after_scheme ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $discountAfterSchemeValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $discountAfterSchemeValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax & GST Settings -->
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-percent me-2"></i>Tax & GST Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">VAT on Br./Expiry</label>
                                <select class="form-select" name="vat_on_bill_expiry">
                                    @php
                                        $vatOnBillExpiryValue = old('vat_on_bill_expiry', $supplier->vat_on_bill_expiry ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $vatOnBillExpiryValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $vatOnBillExpiryValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tax on FQty</label>
                                <select class="form-select" name="tax_on_fqty">
                                    @php
                                        $taxOnFqtyValue = old('tax_on_fqty', $supplier->tax_on_fqty ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $taxOnFqtyValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $taxOnFqtyValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Add Charges with GST</label>
                                <select class="form-select" name="add_charges_with_gst">
                                    @php
                                        $addChargesWithGstValue = old('add_charges_with_gst', $supplier->add_charges_with_gst ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $addChargesWithGstValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $addChargesWithGstValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Expiry on MRP/Sale Rate/Purchase Rate</label>
                                <select class="form-select" name="expiry_on_mrp_sale_rate_purchase_rate">
                                    @php
                                        $expiryValue = old('expiry_on_mrp_sale_rate_purchase_rate', $supplier->expiry_on_mrp_sale_rate_purchase_rate ?? 'M');
                                    @endphp
                                    <option value="M" {{ $expiryValue == 'M' ? 'selected' : '' }}>M</option>
                                    <option value="S" {{ $expiryValue == 'S' ? 'selected' : '' }}>S</option>
                                    <option value="P" {{ $expiryValue == 'P' ? 'selected' : '' }}>P</option>
                                    <option value="F" {{ $expiryValue == 'F' ? 'selected' : '' }}>F</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Pur. Import : Box Conversion</label>
                                <select class="form-select" name="purchase_import_box_conversion">
                                    @php
                                        $purchaseImportBoxConversionValue = old('purchase_import_box_conversion', $supplier->purchase_import_box_conversion ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $purchaseImportBoxConversionValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $purchaseImportBoxConversionValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">TCS Applicable [ Y / N / # ]</label>
                                <select class="form-select" name="tcs_applicable">
                                    @php
                                        $tcsApplicableValue = old('tcs_applicable', $supplier->tcs_applicable ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $tcsApplicableValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $tcsApplicableValue == 'Y' ? 'selected' : '' }}>Y</option>
                                    <option value="#" {{ $tcsApplicableValue == '#' ? 'selected' : '' }}>#</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">TDS [ Y / N ]</label>
                                <select class="form-select" name="tds_yn">
                                    @php
                                        $tdsYnValue = old('tds_yn', $supplier->tds_yn ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $tdsYnValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $tdsYnValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">TDS on Return</label>
                                <select class="form-select" name="tds_on_return">
                                    @php
                                        $tdsOnReturnValue = old('tds_on_return', $supplier->tds_on_return ? 'Y' : 'N');
                                    @endphp
                                    <option value="N" {{ $tdsOnReturnValue == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ $tdsOnReturnValue == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="tds_tcs_on_bill_amount" 
                                           id="tds_tcs_on_bill_amount" value="1" 
                                           {{ old('tds_tcs_on_bill_amount', $supplier->tds_tcs_on_bill_amount) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tds_tcs_on_bill_amount">
                                        TDS / TCS on Bill Amt.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal & Banking Information -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Personal & Banking Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Direct / Indirect</label>
                                <select class="form-select" name="direct_indirect">
                                    <option value="T" {{ old('direct_indirect', $supplier->direct_indirect) == 'T' ? 'selected' : '' }}>T</option>
                                    <option value="I" {{ old('direct_indirect', $supplier->direct_indirect) == 'I' ? 'selected' : '' }}>I</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" 
                                       value="{{ old('full_name', $supplier->full_name) }}" placeholder="Full name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Aadhar</label>
                                <input type="text" class="form-control" name="aadhar" 
                                       value="{{ old('aadhar', $supplier->aadhar) }}" placeholder="Aadhar number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">R(egistered) / U(nregistered) / C(omposite)</label>
                                <select class="form-select" name="registered_unregistered_composite">
                                    <option value="U" {{ old('registered_unregistered_composite', $supplier->registered_unregistered_composite) == 'U' ? 'selected' : '' }}>U</option>
                                    <option value="R" {{ old('registered_unregistered_composite', $supplier->registered_unregistered_composite) == 'R' ? 'selected' : '' }}>R</option>
                                    <option value="C" {{ old('registered_unregistered_composite', $supplier->registered_unregistered_composite) == 'C' ? 'selected' : '' }}>C</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Bank</label>
                                <input type="text" class="form-control" name="bank" 
                                       value="{{ old('bank', $supplier->bank) }}" placeholder="Bank name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Branch</label>
                                <input type="text" class="form-control" name="branch" 
                                       value="{{ old('branch', $supplier->branch) }}" placeholder="Branch name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">A/c No.</label>
                                <input type="text" class="form-control" name="account_no" 
                                       value="{{ old('account_no', $supplier->account_no) }}" placeholder="Account number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">IFSC</label>
                                <input type="text" class="form-control" name="ifsc_code" 
                                       value="{{ old('ifsc_code', $supplier->ifsc_code) }}" placeholder="IFSC code">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">GST No.</label>
                                <input type="text" class="form-control" name="gst_no" 
                                       value="{{ old('gst_no', $supplier->gst_no) }}" placeholder="GST number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Regd. Date</label>
                                <input type="date" class="form-control" name="registration_date" 
                                       value="{{ old('registration_date', $supplier->registration_date) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Additional Notes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Notebook</label>
                                <textarea class="form-control" name="notebook" rows="4" 
                                          placeholder="Add notebook entries or notes about this supplier">{{ old('notebook', $supplier->notebook) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" rows="4" 
                                          placeholder="Add any additional remarks or comments">{{ old('remarks', $supplier->remarks) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection