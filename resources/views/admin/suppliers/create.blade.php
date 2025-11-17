@extends('layouts.admin')
@section('title', 'Add Supplier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Add New Supplier</h2>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Suppliers
                </a>
            </div>

            <form action="{{ route('admin.suppliers.store') }}" method="POST" novalidate>
                @csrf

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
                                       name="name" value="{{ old('name') }}" placeholder="Enter your name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Code</label>
                                <input type="text" class="form-control" name="code" 
                                       value="{{ old('code') }}" placeholder="Enter code">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">T(ax) / R(etail)</label>
                                <select class="form-select" name="tax_retail_flag">
                                    <option value="T" {{ old('tax_retail_flag', 'T') == 'T' ? 'selected' : '' }}>T</option>
                                    <option value="R" {{ old('tax_retail_flag') == 'R' ? 'selected' : '' }}>R</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">TAN No.</label>
                                <input type="text" class="form-control" name="tan_no" 
                                       value="{{ old('tan_no') }}" placeholder="TAN number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">MSME Lic.</label>
                                <input type="text" class="form-control" name="msme_lic" 
                                       value="{{ old('msme_lic') }}" placeholder="MSME license">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          name="address" rows="3" placeholder="Enter complete address">{{ old('address') }}</textarea>
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
                                                   name="opening_balance" value="{{ old('opening_balance', '0.00') }}" 
                                                   placeholder="0.00">
                                            <select class="form-select" name="opening_balance_type" style="max-width: 80px;">
                                                <option value="C" {{ old('opening_balance_type', 'C') == 'C' ? 'selected' : '' }}>C</option>
                                                <option value="D" {{ old('opening_balance_type') == 'D' ? 'selected' : '' }}>D</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <input type="text" class="form-control" name="status" 
                                               value="{{ old('status') }}" placeholder="Enter status" maxlength="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fax</label>
                                        <input type="text" class="form-control" name="fax" 
                                               value="{{ old('fax') }}" placeholder="Fax number">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Flag</label>
                                        <input type="text" class="form-control" name="flag" 
                                               value="{{ old('flag') }}" placeholder="Flag">
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
                                       name="telephone" value="{{ old('telephone') }}" placeholder="Phone number">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">E-Mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" placeholder="Email address">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">B' Day</label>
                                <input type="date" class="form-control" name="b_day" value="{{ old('b_day') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">A' Day</label>
                                <input type="date" class="form-control" name="a_day" value="{{ old('a_day') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="mobile" 
                                       value="{{ old('mobile') }}" placeholder="Mobile number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contact Person I</label>
                                <input type="text" class="form-control" name="contact_person_1" 
                                       value="{{ old('contact_person_1') }}" placeholder="Contact person 1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contact Person II</label>
                                <input type="text" class="form-control" name="contact_person_2" 
                                       value="{{ old('contact_person_2') }}" placeholder="Contact person 2">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="mobile_additional" 
                                       value="{{ old('mobile_additional') }}" placeholder="Mobile number 2">
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
                                       value="{{ old('dl_no') }}" placeholder="DL number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">D.L No.-1</label>
                                <input type="text" class="form-control" name="dl_no_1" 
                                       value="{{ old('dl_no_1') }}" placeholder="DL number 1">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Food Lic.</label>
                                <input type="text" class="form-control" name="food_lic" 
                                       value="{{ old('food_lic') }}" placeholder="Food license">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">CST No.</label>
                                <input type="text" class="form-control" name="cst_no" 
                                       value="{{ old('cst_no') }}" placeholder="CST number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">TIN No.</label>
                                <input type="text" class="form-control" name="tin_no" 
                                       value="{{ old('tin_no') }}" placeholder="TIN number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">PAN</label>
                                <input type="text" class="form-control" name="pan" 
                                       value="{{ old('pan') }}" placeholder="PAN number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">State Code</label>
                                <select class="form-select" name="state_code">
                                    <option value="">Select State</option>
                                    <option value="09 Uttar Pradesh" {{ old('state_code') == '09 Uttar Pradesh' ? 'selected' : '' }}>09 Uttar Pradesh</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">L(ocal)/C(entral)</label>
                                <select class="form-select" name="local_central_flag">
                                    <option value="L" {{ old('local_central_flag', 'L') == 'L' ? 'selected' : '' }}>L</option>
                                    <option value="C" {{ old('local_central_flag') == 'C' ? 'selected' : '' }}>C</option>
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
                                    <option value="N" {{ old('invoice_on_trade_rate', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('invoice_on_trade_rate') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Net Rate [ Y/N/M ]</label>
                                <select class="form-select" name="net_rate_yn">
                                    <option value="N" {{ old('net_rate_yn', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('net_rate_yn') == 'Y' ? 'selected' : '' }}>Y</option>
                                    <option value="M" {{ old('net_rate_yn') == 'M' ? 'selected' : '' }}>M</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Visit Days</label>
                                <input type="text" class="form-control" name="visit_days" 
                                       value="{{ old('visit_days') }}" placeholder="Visit days">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Invoice Roff</label>
                                <select class="form-select" name="invoice_roff">
                                    <option value="N" {{ old('invoice_roff', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('invoice_roff') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Scm. In Decimal</label>
                                <select class="form-select" name="scheme_in_decimal">
                                    <option value="N" {{ old('scheme_in_decimal', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('scheme_in_decimal') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Dis. On Excise ?</label>
                                <select class="form-select" name="discount_on_excise">
                                    <option value="N" {{ old('discount_on_excise', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('discount_on_excise') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sch.Type [ H/F ]</label>
                                <select class="form-select" name="scheme_type">
                                    <option value="">Select</option>
                                    <option value="H" {{ old('scheme_type') == 'H' ? 'selected' : '' }}>H</option>
                                    <option value="F" {{ old('scheme_type') == 'F' ? 'selected' : '' }}>F</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Cr. Limit</label>
                                <input type="number" step="1" class="form-control" 
                                       name="credit_limit" value="{{ old('credit_limit', '0') }}" placeholder="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Composite Scheme</label>
                                <select class="form-select" name="composite_scheme">
                                    <option value="N" {{ old('composite_scheme', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('composite_scheme') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Stock Transfer</label>
                                <select class="form-select" name="stock_transfer">
                                    <option value="N" {{ old('stock_transfer', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('stock_transfer') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Cash Purchase</label>
                                <select class="form-select" name="cash_purchase">
                                    <option value="N" {{ old('cash_purchase', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('cash_purchase') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sale Pur. Status</label>
                                <select class="form-select" name="sale_purchase_status">
                                    <option value="B" {{ old('sale_purchase_status', 'B') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="P" {{ old('sale_purchase_status') == 'P' ? 'selected' : '' }}>P</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Dis. After Scheme</label>
                                <select class="form-select" name="discount_after_scheme">
                                    <option value="N" {{ old('discount_after_scheme', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('discount_after_scheme') == 'Y' ? 'selected' : '' }}>Y</option>
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
                                    <option value="N" {{ old('vat_on_bill_expiry', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('vat_on_bill_expiry') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tax on FQty</label>
                                <select class="form-select" name="tax_on_fqty">
                                    <option value="N" {{ old('tax_on_fqty', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('tax_on_fqty') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Add Charges with GST</label>
                                <select class="form-select" name="add_charges_with_gst">
                                    <option value="N" {{ old('add_charges_with_gst', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('add_charges_with_gst') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Expiry on MRP/Sale Rate/Purchase Rate</label>
                                <select class="form-select" name="expiry_on_mrp_sale_rate_purchase_rate">
                                    <option value="M" {{ old('expiry_on_mrp_sale_rate_purchase_rate', 'M') == 'M' ? 'selected' : '' }}>M</option>
                                    <option value="S" {{ old('expiry_on_mrp_sale_rate_purchase_rate') == 'S' ? 'selected' : '' }}>S</option>
                                    <option value="P" {{ old('expiry_on_mrp_sale_rate_purchase_rate') == 'P' ? 'selected' : '' }}>P</option>
                                    <option value="F" {{ old('expiry_on_mrp_sale_rate_purchase_rate') == 'F' ? 'selected' : '' }}>F</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Pur. Import : Box Conversion</label>
                                <select class="form-select" name="purchase_import_box_conversion">
                                    <option value="N" {{ old('purchase_import_box_conversion', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('purchase_import_box_conversion') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">TCS Applicable [ Y / N / # ]</label>
                                <select class="form-select" name="tcs_applicable">
                                    <option value="N" {{ old('tcs_applicable', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('tcs_applicable') == 'Y' ? 'selected' : '' }}>Y</option>
                                    <option value="#" {{ old('tcs_applicable') == '#' ? 'selected' : '' }}>#</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">TDS [ Y / N ]</label>
                                <select class="form-select" name="tds_yn">
                                    <option value="N" {{ old('tds_yn', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('tds_yn') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">TDS on Return</label>
                                <select class="form-select" name="tds_on_return">
                                    <option value="N" {{ old('tds_on_return', 'N') == 'N' ? 'selected' : '' }}>N</option>
                                    <option value="Y" {{ old('tds_on_return') == 'Y' ? 'selected' : '' }}>Y</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="tds_tcs_on_bill_amount" 
                                           id="tds_tcs_on_bill_amount" value="1" 
                                           {{ old('tds_tcs_on_bill_amount') ? 'checked' : '' }}>
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
                                    <option value="T" {{ old('direct_indirect', 'T') == 'T' ? 'selected' : '' }}>T</option>
                                    <option value="I" {{ old('direct_indirect') == 'I' ? 'selected' : '' }}>I</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" 
                                       value="{{ old('full_name') }}" placeholder="Full name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Aadhar</label>
                                <input type="text" class="form-control" name="aadhar" 
                                       value="{{ old('aadhar') }}" placeholder="Aadhar number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">R(egistered) / U(nregistered) / C(omposite)</label>
                                <select class="form-select" name="registered_unregistered_composite">
                                    <option value="U" {{ old('registered_unregistered_composite', 'U') == 'U' ? 'selected' : '' }}>U</option>
                                    <option value="R" {{ old('registered_unregistered_composite') == 'R' ? 'selected' : '' }}>R</option>
                                    <option value="C" {{ old('registered_unregistered_composite') == 'C' ? 'selected' : '' }}>C</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Bank</label>
                                <input type="text" class="form-control" name="bank" 
                                       value="{{ old('bank') }}" placeholder="Bank name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Branch</label>
                                <input type="text" class="form-control" name="branch" 
                                       value="{{ old('branch') }}" placeholder="Branch name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">A/c No.</label>
                                <input type="text" class="form-control" name="account_no" 
                                       value="{{ old('account_no') }}" placeholder="Account number">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">IFSC</label>
                                <input type="text" class="form-control" name="ifsc_code" 
                                       value="{{ old('ifsc_code') }}" placeholder="IFSC code">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">GST No.</label>
                                <input type="text" class="form-control" name="gst_no" 
                                       value="{{ old('gst_no') }}" placeholder="GST number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Regd. Date</label>
                                <input type="date" class="form-control" name="registration_date" 
                                       value="{{ old('registration_date') }}">
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
                                          placeholder="Add notebook entries or notes about this supplier">{{ old('notebook') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" rows="4" 
                                          placeholder="Add any additional remarks or comments">{{ old('remarks') }}</textarea>
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
                        <i class="bi bi-save me-2"></i>Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection