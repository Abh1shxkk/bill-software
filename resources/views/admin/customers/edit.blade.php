@extends('layouts.admin')
@section('title', 'Edit Customer')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Edit Customer</h2>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Customers
                    </a>
                </div>

                <form action="{{ route('admin.customers.update', $customer) }}" method="POST" id="customerForm" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="customerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" 
                                type="button" role="tab">General Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="other-tab" data-bs-toggle="tab" data-bs-target="#other" 
                                type="button" role="tab">Other Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="locks-tab" data-bs-toggle="tab" data-bs-target="#locks" 
                                type="button" role="tab">Locks</button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="customerTabsContent">
                        
                        <!-- GENERAL INFORMATION TAB -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <!-- LEFT COLUMN -->
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <!-- Name -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name" value="{{ old('name', $customer->name) }}" placeholder="Enter customer name" required>
                                                </div>

                                                <!-- Code & Tax Registration -->
                                                <div class="col-8">
                                                    <label class="form-label fw-semibold">Code</label>
                                                    <input type="text" class="form-control" name="code" value="{{ old('code', $customer->code) }}" placeholder="Customer code">
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label fw-semibold">T(ax)/R(et)</label>
                                                    <select class="form-select" name="tax_registration">
                                                        <option value="R" {{ old('tax_registration', $customer->tax_registration) == 'R' ? 'selected' : '' }}>Retail</option>
                                                        <option value="T" {{ old('tax_registration', $customer->tax_registration) == 'T' ? 'selected' : '' }}>Tax</option>
                                                    </select>
                                                </div>

                                                <!-- Address 1 -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Address 1</label>
                                                    <input type="text" class="form-control mb-2" name="address" value="{{ old('address', $customer->address) }}" placeholder="Address line 1">
                                                    <input type="text" class="form-control mb-2" name="address_line2" value="{{ old('address_line2', $customer->address_line2) }}" placeholder="Address line 2">
                                                    <div class="row g-2">
                                                        <div class="col-8">
                                                            <input type="text" class="form-control" name="address_line3" value="{{ old('address_line3', $customer->address_line3) }}" placeholder="Address line 3">
                                                        </div>
                                                        <div class="col-4">
                                                            <input type="text" class="form-control" name="pin_code" value="{{ old('pin_code', $customer->pin_code) }}" placeholder="Pin Code">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Address 2 -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Address 2 (Optional)</label>
                                                    <input type="text" class="form-control mb-2" name="address2" value="{{ old('address2', $customer->address2) }}" placeholder="Address line 1">
                                                    <input type="text" class="form-control mb-2" name="address2_line2" value="{{ old('address2_line2', $customer->address2_line2) }}" placeholder="Address line 2">
                                                    <input type="text" class="form-control" name="address2_line3" value="{{ old('address2_line3', $customer->address2_line3) }}" placeholder="Address line 3">
                                                </div>

                                                <!-- City -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">City</label>
                                                    <input type="text" class="form-control" name="city" value="{{ old('city', $customer->city) }}" placeholder="Enter city">
                                                </div>

                                                <!-- Contact Numbers -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Tel. (Office)</label>
                                                    <input type="text" class="form-control" name="telephone_office" value="{{ old('telephone_office', $customer->telephone_office) }}" placeholder="Office telephone">
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Tel. (Residence)</label>
                                                    <input type="text" class="form-control" name="telephone_residence" value="{{ old('telephone_residence', $customer->telephone_residence) }}" placeholder="Residence telephone">
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Mobile</label>
                                                    <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $customer->mobile) }}" placeholder="Mobile number">
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">E-Mail</label>
                                                    <input type="email" class="form-control" name="email" value="{{ old('email', $customer->email) }}" placeholder="email@example.com">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN -->
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <!-- Contact Person I -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Contact Person I</label>
                                                    <input type="text" class="form-control mb-2" name="contact_person1" value="{{ old('contact_person1', $customer->contact_person1) }}" placeholder="Contact person name">
                                                    <input type="text" class="form-control" name="mobile_contact1" value="{{ old('mobile_contact1', $customer->mobile_contact1) }}" placeholder="Mobile number">
                                                </div>

                                                <!-- Contact Person II -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Contact Person II</label>
                                                    <input type="text" class="form-control mb-2" name="contact_person2" value="{{ old('contact_person2', $customer->contact_person2) }}" placeholder="Contact person name">
                                                    <input type="text" class="form-control" name="mobile_contact2" value="{{ old('mobile_contact2', $customer->mobile_contact2) }}" placeholder="Mobile number">
                                                </div>

                                                <!-- Fax -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Fax No.</label>
                                                    <input type="text" class="form-control" name="fax_number" value="{{ old('fax_number', $customer->fax_number) }}" placeholder="Fax number">
                                                </div>

                                                <!-- Opening Balance -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Opening Balance</label>
                                                    <div class="row g-2">
                                                        <div class="col-8">
                                                            <input type="number" step="0.01" class="form-control" name="opening_balance" value="{{ old('opening_balance', $customer->opening_balance ?? '0.00') }}" placeholder="0.00">
                                                        </div>
                                                        <div class="col-4">
                                                            <select class="form-select" name="balance_type">
                                                                <option value="D" {{ old('balance_type', $customer->balance_type) == 'D' ? 'selected' : '' }}>Debit</option>
                                                                <option value="C" {{ old('balance_type', $customer->balance_type) == 'C' ? 'selected' : '' }}>Credit</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Local/Central & Anniversary -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Local/Central</label>
                                                    <select class="form-select" name="local_central">
                                                        <option value="L" {{ old('local_central', $customer->local_central) == 'L' ? 'selected' : '' }}>Local</option>
                                                        <option value="C" {{ old('local_central', $customer->local_central) == 'C' ? 'selected' : '' }}>Central</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Anniversary Day</label>
                                                    <input type="date" class="form-control" name="anniversary_day" value="{{ old('anniversary_day', $customer->anniversary_day) }}">
                                                </div>

                                                <!-- Birthday -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Birth Day</label>
                                                    <input type="date" class="form-control" name="birth_day" value="{{ old('birth_day', $customer->birth_day) }}">
                                                </div>

                                                <!-- Status & Invoice Export -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <input type="text" class="form-control" name="status" value="{{ old('status', $customer->status) }}" placeholder="Status">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Invoice Export</label>
                                                    <select class="form-select" name="invoice_export">
                                                        <option value="N" {{ old('invoice_export', $customer->invoice_export) == 'N' ? 'selected' : '' }}>No</option>
                                                        <option value="Y" {{ old('invoice_export', $customer->invoice_export) == 'Y' ? 'selected' : '' }}>Yes</option>
                                                    </select>
                                                </div>

                                                <!-- Flag & Due List Sequence -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Flag</label>
                                                    <input type="text" class="form-control" name="flag" value="{{ old('flag', $customer->flag) }}" placeholder="Flag">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Due List Sequence</label>
                                                    <input type="number" class="form-control" name="due_list_sequence" value="{{ old('due_list_sequence', $customer->due_list_sequence ?? '0') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- LICENSE & TAX INFORMATION -->
                                    <h6 class="fw-bold mb-3 text-primary">License & Tax Information</h6>
                                    <div class="row g-3">
                                        <!-- LEFT COLUMN -->
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <!-- DL Number -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">DL No.</label>
                                                    <input type="text" class="form-control" name="dl_number" value="{{ old('dl_number', $customer->dl_number) }}" placeholder="Drug license number">
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">DL Expiry Date</label>
                                                    <input type="date" class="form-control" name="dl_expiry" value="{{ old('dl_expiry', $customer->dl_expiry) }}">
                                                </div>

                                                <!-- DL Number 1 -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">DL No. 1</label>
                                                    <input type="text" class="form-control" name="dl_number1" value="{{ old('dl_number1', $customer->dl_number1) }}" placeholder="Additional DL number">
                                                </div>

                                                <!-- Food License -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Food License</label>
                                                    <input type="text" class="form-control" name="food_license" value="{{ old('food_license', $customer->food_license) }}" placeholder="Food license number">
                                                </div>

                                                <!-- CST Number -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">CST No.</label>
                                                    <input type="text" class="form-control" name="cst_number" value="{{ old('cst_number', $customer->cst_number) }}" placeholder="CST number">
                                                </div>

                                                <!-- TIN & PAN -->
                                                <div class="col-7">
                                                    <label class="form-label fw-semibold">TIN No.</label>
                                                    <input type="text" class="form-control" name="tin_number" value="{{ old('tin_number', $customer->tin_number) }}" placeholder="TIN number">
                                                </div>
                                                <div class="col-5">
                                                    <label class="form-label fw-semibold">PAN</label>
                                                    <input type="text" class="form-control" name="pan_number" value="{{ old('pan_number', $customer->pan_number) }}" placeholder="PAN number">
                                                </div>

                                                <!-- DAY -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">DAY <span class="badge bg-danger ms-2">CST-New</span></label>
                                                    <input type="number" class="form-control" name="day_value" value="{{ old('day_value', $customer->day_value ?? '0') }}">
                                                </div>

                                                <!-- GST Number -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">GST No.</label>
                                                    <input type="text" class="form-control" name="gst_number" value="{{ old('gst_number', $customer->gst_number) }}" placeholder="GST number">
                                                </div>

                                                <!-- GST Name -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Name for GSTR</label>
                                                    <input type="text" class="form-control" name="gst_name" value="{{ old('gst_name', $customer->gst_name) }}" placeholder="Name as per GST records">
                                                </div>

                                                <!-- State Code & Registration Status -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">State Code</label>
                                                    <select class="form-select" name="state_code_gst">
                                                        <option value="09">09-Uttar Pradesh</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">GST Status</label>
                                                    <select class="form-select" name="registration_status">
                                                        <option value="U" {{ old('registration_status', $customer->registration_status) == 'U' ? 'selected' : '' }}>Unregistered</option>
                                                        <option value="R" {{ old('registration_status', $customer->registration_status) == 'R' ? 'selected' : '' }}>Registered</option>
                                                        <option value="C" {{ old('registration_status', $customer->registration_status) == 'C' ? 'selected' : '' }}>Composite</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN -->
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <!-- Sales Man -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Sales Man</label>
                                                    <div class="row g-2">
                                                        <div class="col-3">
                                                            <input type="text" class="form-control" name="sales_man_code" value="{{ old('sales_man_code', $customer->sales_man_code ?? '00') }}" placeholder="Code">
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" name="sales_man_name" value="{{ old('sales_man_name', $customer->sales_man_name) }}" placeholder="Salesman name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Area -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Area</label>
                                                    <div class="row g-2">
                                                        <div class="col-3">
                                                            <input type="text" class="form-control" name="area_code" value="{{ old('area_code', $customer->area_code ?? '00') }}" placeholder="Code">
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" name="area_name" value="{{ old('area_name', $customer->area_name) }}" placeholder="Area name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Route -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Route</label>
                                                    <div class="row g-2">
                                                        <div class="col-3">
                                                            <input type="text" class="form-control" name="route_code" value="{{ old('route_code', $customer->route_code ?? '00') }}" placeholder="Code">
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" name="route_name" value="{{ old('route_name', $customer->route_name) }}" placeholder="Route name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- State -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">State</label>
                                                    <div class="row g-2">
                                                        <div class="col-3">
                                                            <input type="text" class="form-control" name="state_code" value="{{ old('state_code', $customer->state_code ?? '00') }}" placeholder="Code">
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" name="state_name" value="{{ old('state_name', $customer->state_name) }}" placeholder="State name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Business Type -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Business Type</label>
                                                    <select class="form-select" name="business_type">
                                                        <option value="R" {{ old('tax_registration', $customer->tax_registration) == 'R' ? 'selected' : '' }}>Retail</option>
                                                        <option value="W">Wholesale</option>
                                                        <option value="I">Institution</option>
                                                        <option value="D">Dept. Store</option>
                                                        <option value="O">Others</option>
                                                    </select>
                                                </div>

                                                <!-- Description -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Description</label>
                                                    <input type="text" class="form-control" name="description" value="{{ old('description', $customer->description) }}" placeholder="Additional description">
                                                </div>

                                                <!-- Order Required & Aadhar -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Order No. Required</label>
                                                    <select class="form-select" name="order_required">
                                                        <option value="N" {{ old('order_required', $customer->order_required) == 'N' ? 'selected' : '' }}>No</option>
                                                        <option value="Y" {{ old('order_required', $customer->order_required) == 'Y' ? 'selected' : '' }}>Yes</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Aadhar</label>
                                                    <input type="text" class="form-control" name="aadhar_number" value="{{ old('aadhar_number', $customer->aadhar_number) }}" placeholder="Aadhar number">
                                                </div>

                                                <!-- Registration Date -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Registration Date</label>
                                                    <input type="date" class="form-control" name="registration_date" value="{{ old('registration_date', $customer->registration_date ?? '2000-01-01') }}">
                                                </div>

                                                <!-- End Date -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">End Date</label>
                                                    <input type="date" class="form-control" name="end_date" value="{{ old('end_date', $customer->end_date ?? '2000-01-01') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- OTHER DETAILS TAB -->
                        <div class="tab-pane fade" id="other" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="row g-4">
                                        <!-- LEFT COLUMN -->
                                        <div class="col-md-6">
                                            <h6 class="fw-bold mb-3 text-primary">Banking & Pricing</h6>
                                            <div class="row g-3">
                                                <!-- Bank -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Bank</label>
                                                    <input type="text" class="form-control" name="bank" value="{{ old('bank', $customer->bank) }}" placeholder="Bank name">
                                                </div>

                                                <!-- Branch -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Branch</label>
                                                    <input type="text" class="form-control" name="branch" value="{{ old('branch', $customer->branch) }}" placeholder="Branch name">
                                                </div>

                                                <!-- Closed On & Credit Limit -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Closed On</label>
                                                    <input type="date" class="form-control" name="closed_on" value="{{ old('closed_on', $customer->closed_on) }}">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Credit Limit</label>
                                                    <input type="number" step="0.01" class="form-control" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit ?? '0') }}" placeholder="0.00">
                                                </div>

                                                <!-- Sale Rate Type -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Sale Rate Type</label>
                                                    <small class="d-block text-muted mb-2">1.Sale / 2.W.Sale / 3.Spl.Rate / 4.Pur.Rate / 5.Cost / 6.MRP / 7.T.Rate / 8.Cost W/O F.Qty.</small>
                                                    <div class="row g-2">
                                                        <div class="col-4">
                                                            <select class="form-select" name="sale_rate_type">
                                                                <option value="1" {{ old('sale_rate_type', $customer->sale_rate_type) == '1' ? 'selected' : '' }}>1 - Sale</option>
                                                                <option value="2" {{ old('sale_rate_type', $customer->sale_rate_type) == '2' ? 'selected' : '' }}>2 - W.Sale</option>
                                                                <option value="3" {{ old('sale_rate_type', $customer->sale_rate_type) == '3' ? 'selected' : '' }}>3 - Spl. Rate</option>
                                                                <option value="4" {{ old('sale_rate_type', $customer->sale_rate_type) == '4' ? 'selected' : '' }}>4 - Pur. Rate</option>
                                                                <option value="5" {{ old('sale_rate_type', $customer->sale_rate_type) == '5' ? 'selected' : '' }}>5 - Cost</option>
                                                                <option value="6" {{ old('sale_rate_type', $customer->sale_rate_type) == '6' ? 'selected' : '' }}>6 - MRP</option>
                                                                <option value="7" {{ old('sale_rate_type', $customer->sale_rate_type) == '7' ? 'selected' : '' }}>7 - T.Rate</option>
                                                                <option value="8" {{ old('sale_rate_type', $customer->sale_rate_type) == '8' ? 'selected' : '' }}>8 - Cost W/O F.Qty.</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="form-label small">Add %</label>
                                                        </div>
                                                        <div class="col-4">
                                                            <input type="number" step="0.01" class="form-control" name="add_percent" value="{{ old('add_percent', $customer->add_percent ?? '0') }}" placeholder="0.00">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tax & Expiry Options -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Tax on Br./Expiry</label>
                                                    <select class="form-select" name="tax_on_br_expiry">
                                                        <option value="N" {{ old('tax_on_br_expiry', $customer->tax_on_br_expiry) == 'N' ? 'selected' : '' }}>No</option>
                                                        <option value="Y" {{ old('tax_on_br_expiry', $customer->tax_on_br_expiry) == 'Y' ? 'selected' : '' }}>Yes</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Expiry CN On</label>
                                                    <select class="form-select" name="expiry_on">
                                                        <option value="M" {{ old('expiry_on', $customer->expiry_on) == 'M' ? 'selected' : '' }}>MRP</option>
                                                        <option value="S" {{ old('expiry_on', $customer->expiry_on) == 'S' ? 'selected' : '' }}>Sale Rate</option>
                                                        <option value="P" {{ old('expiry_on', $customer->expiry_on) == 'P' ? 'selected' : '' }}>Pur. Rate</option>
                                                        <option value="W" {{ old('expiry_on', $customer->expiry_on) == 'W' ? 'selected' : '' }}>WS Rate</option>
                                                        <option value="L" {{ old('expiry_on', $customer->expiry_on) == 'L' ? 'selected' : '' }}>Spl. Rate</option>
                                                    </select>
                                                </div>

                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Dis. After Scheme</label>
                                                    <select class="form-select" name="dis_after_scheme">
                                                        <option value="Y" {{ old('dis_after_scheme', $customer->dis_after_scheme) == 'Y' ? 'selected' : '' }}>Yes</option>
                                                        <option value="N" {{ old('dis_after_scheme', $customer->dis_after_scheme) == 'N' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Expiry RN On</label>
                                                    <select class="form-select" name="expiry_rn_on">
                                                        <option value="M">MRP</option>
                                                        <option value="S">Sale Rate</option>
                                                        <option value="P">Pur. Rate</option>
                                                        <option value="W">WS Rate</option>
                                                    </select>
                                                </div>

                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Dis. On Excise</label>
                                                    <select class="form-select" name="dis_on_excise">
                                                        <option value="Y">Yes</option>
                                                        <option value="N">No</option>
                                                        <option value="X">X</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Sale Pur. Status</label>
                                                    <select class="form-select" name="sale_pur_status">
                                                        <option value="S">Sale</option>
                                                        <option value="P">Purchase</option>
                                                        <option value="B">Both</option>
                                                    </select>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Scheme Type</label>
                                                    <select class="form-select" name="scm_type">
                                                        <option value="F">F</option>
                                                        <option value="H">H</option>
                                                    </select>
                                                </div>

                                                <!-- Net Rate -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Net Rate</label>
                                                    <select class="form-select" name="net_rate">
                                                        <option value="N">No</option>
                                                        <option value="Y">Yes</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">No. of Items In Bill</label>
                                                    <input type="number" class="form-control" name="no_of_items_in_bill" value="{{ old('no_of_items_in_bill', $customer->no_of_items_in_bill ?? '0') }}">
                                                </div>

                                                <!-- Invoice Print Order -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Invoice Print Order</label>
                                                    <small class="d-block text-muted mb-2">0.Default / 1.Company / 2.User Defined / 3.Name</small>
                                                    <input type="text" class="form-control" name="invoice_print_order" value="{{ old('invoice_print_order', $customer->invoice_print_order) }}" placeholder="Enter order type">
                                                </div>

                                                <!-- SR Replacement -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">SR Replacement</label>
                                                    <select class="form-select" name="sr_replacement">
                                                        <option value="N">No</option>
                                                        <option value="Y">Yes</option>
                                                    </select>
                                                </div>

                                                <!-- Cash Sale -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Cash Sale</label>
                                                    <select class="form-select" name="cash_sale">
                                                        <option value="N">No</option>
                                                        <option value="Y">Yes</option>
                                                    </select>
                                                </div>

                                                <!-- Invoice Format -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Invoice Format</label>
                                                    <input type="number" class="form-control" name="invoice_format" value="{{ old('invoice_format', $customer->invoice_format ?? '0') }}">
                                                </div>

                                                <!-- Fixed Discount -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Fixed Discount</label>
                                                    <input type="number" step="0.01" class="form-control" name="fixed_discount" value="{{ old('fixed_discount', $customer->fixed_discount ?? '0') }}" placeholder="0.00">
                                                </div>

                                                <!-- GST Discount Fields -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold mb-2">Brk./Expiry Dis. On Item With GST</label>
                                                    <div class="row g-2">
                                                        <div class="col">
                                                            <label class="form-label small">5%</label>
                                                            <input type="number" step="0.01" class="form-control" name="gst_5_percent" value="{{ old('gst_5_percent', $customer->gst_5_percent ?? '0') }}">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label small">12%</label>
                                                            <input type="number" step="0.01" class="form-control" name="gst_12_percent" value="{{ old('gst_12_percent', $customer->gst_12_percent ?? '0') }}">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label small">18%</label>
                                                            <input type="number" step="0.01" class="form-control" name="gst_18_percent" value="{{ old('gst_18_percent', $customer->gst_18_percent ?? '0') }}">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label small">28%</label>
                                                            <input type="number" step="0.01" class="form-control" name="gst_28_percent" value="{{ old('gst_28_percent', $customer->gst_28_percent ?? '0') }}">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label small">0%</label>
                                                            <input type="number" step="0.01" class="form-control" name="gst_0_percent" value="{{ old('gst_0_percent', $customer->gst_0_percent ?? '0') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Update Button -->
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-outline-secondary w-100">
                                                        Update Brk./Expiry Dis. to All Customer
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN -->
                                        <div class="col-md-6">
                                            <h6 class="fw-bold mb-3 text-primary">Tax & Additional Settings</h6>
                                            <div class="row g-3">
                                                <!-- Ref -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Reference</label>
                                                    <input type="text" class="form-control" name="ref" value="{{ old('ref', $customer->ref) }}" placeholder="Reference details">
                                                </div>

                                                <!-- TDS -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">TDS (Tax Deducted at Source)</label>
                                                    <select class="form-select" name="tds">
                                                        <option value="N">No</option>
                                                        <option value="Y">Yes</option>
                                                    </select>
                                                </div>

                                                <!-- Add Charges & TCS -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Add Charges with GST</label>
                                                    <select class="form-select" name="add_charges_with_gst">
                                                        <option value="N">No</option>
                                                        <option value="Y">Yes</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">TCS Applicable</label>
                                                    <select class="form-select" name="tcs_applicable">
                                                        <option value="N">No</option>
                                                        <option value="Y">Yes</option>
                                                        <option value="#">#</option>
                                                    </select>
                                                </div>

                                                <!-- BE Incl. -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">BE Incl.</label>
                                                    <select class="form-select" name="be_incl">
                                                        <option value="N">No</option>
                                                        <option value="Y">Yes</option>
                                                    </select>
                                                </div>

                                                <!-- Brk/Expiry Msg -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Brk./Expiry Msg. in Sale</label>
                                                    <select class="form-select" name="brk_expiry_msg_in_sale">
                                                        <option value="Y">Yes</option>
                                                        <option value="N">No</option>
                                                    </select>
                                                </div>

                                                <!-- Series Lock & Branch Transfer -->
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Series Lock</label>
                                                    <input type="text" class="form-control" name="series_lock" value="{{ old('series_lock', $customer->series_lock) }}" placeholder="Series">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Branch Trf.</label>
                                                    <input type="text" class="form-control" name="branch_trf" value="{{ old('branch_trf', $customer->branch_trf) }}" placeholder="Branch">
                                                </div>

                                                <!-- Transfer Account -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Transfer Account</label>
                                                    <input type="text" class="form-control" name="trnf_account" value="{{ old('trnf_account', $customer->trnf_account) }}" placeholder="Account details">
                                                </div>

                                                <!-- eWay Details Section -->
                                                <div class="col-12">
                                                    <div class="border rounded p-3 bg-light">
                                                        <h6 class="fw-bold mb-3">eWay Details</h6>
                                                        
                                                        <!-- Transport -->
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Transport</label>
                                                            <div class="row g-2">
                                                                <div class="col-3">
                                                                    <input type="text" class="form-control" name="transport_code" value="{{ old('transport_code', $customer->transport_code ?? '00') }}" placeholder="Code">
                                                                </div>
                                                                <div class="col-9">
                                                                    <input type="text" class="form-control" name="transport_name" value="{{ old('transport_name', $customer->transport_name ?? '0') }}" placeholder="Transport name">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Distance -->
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold">Distance (KM)</label>
                                                            <input type="number" class="form-control" name="distance" value="{{ old('distance', $customer->distance) }}" placeholder="Distance in kilometers">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Expiry Options -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Expiry - R(epl) / C(redit Note)</label>
                                                    <select class="form-select" name="expiry_repl_credit">
                                                        <option value="C">Credit Note</option>
                                                        <option value="R">Replacement</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LOCKS TAB -->
                        <div class="tab-pane fade" id="locks" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3 text-primary">Credit & Outstanding Limits</h6>
                                    <div class="row g-3">
                                        <!-- LEFT COLUMN -->
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <!-- Maximum O/S Amount -->
                                                <div class="col-8">
                                                    <label class="form-label fw-semibold">Maximum O/S Amount</label>
                                                    <input type="number" step="0.01" class="form-control" name="max_os_amount" value="{{ old('max_os_amount', $customer->max_os_amount ?? '0.00') }}" placeholder="0.00">
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label fw-semibold">Max Limit On</label>
                                                    <select class="form-select" name="max_limit_on">
                                                        <option value="D" {{ old('max_limit_on', $customer->max_limit_on) == 'D' ? 'selected' : '' }}>Due</option>
                                                        <option value="L" {{ old('max_limit_on', $customer->max_limit_on) == 'L' ? 'selected' : '' }}>Ledger</option>
                                                    </select>
                                                </div>

                                                <!-- Maximum Invoice Amount -->
                                                <div class="col-8">
                                                    <label class="form-label fw-semibold">Maximum Inv. Amount</label>
                                                    <input type="number" step="0.01" class="form-control" name="max_inv_amount" value="{{ old('max_inv_amount', $customer->max_inv_amount ?? '0.00') }}" placeholder="0.00">
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label fw-semibold">Max No. O/S Inv.</label>
                                                    <input type="number" class="form-control" name="max_no_os_inv" value="{{ old('max_no_os_inv', $customer->max_no_os_inv ?? '0') }}">
                                                </div>

                                                <!-- Follow Conditions Strictly -->
                                                <div class="col-8">
                                                    <label class="form-label fw-semibold">Follow Conditions Strictly</label>
                                                    <select class="form-select" name="follow_conditions_strictly">
                                                        <option value="N" {{ old('follow_conditions_strictly', $customer->follow_conditions_strictly) == 'N' ? 'selected' : '' }}>No</option>
                                                        <option value="Y" {{ old('follow_conditions_strictly', $customer->follow_conditions_strictly) == 'Y' ? 'selected' : '' }}>Yes</option>
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label fw-semibold">Credit Days Lock</label>
                                                    <input type="number" class="form-control" name="credit_limit_days_lock" value="{{ old('credit_limit_days_lock', $customer->credit_limit_days_lock ?? '0') }}">
                                                </div>

                                                <!-- Open Lock Once -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Open Lock Once</label>
                                                    <select class="form-select" name="open_lock_once">
                                                        <option value="N" {{ old('open_lock_once', $customer->open_lock_once) == 'N' ? 'selected' : '' }}>No</option>
                                                        <option value="Y" {{ old('open_lock_once', $customer->open_lock_once) == 'Y' ? 'selected' : '' }}>Yes</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN - Expiry Locks -->
                                        <div class="col-md-6">
                                            <div class="border rounded p-3" style="background-color: #faf5ff;">
                                                <h6 class="fw-bold mb-3" style="color: #a855f7;">Expiry Locks</h6>
                                                
                                                <div class="row g-3">
                                                    <!-- A(mount) / P(ercentage) -->
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">A(mount) / P(ercentage)</label>
                                                        <select class="form-select" name="expiry_lock_type">
                                                            <option value="A" {{ old('expiry_lock_type', $customer->expiry_lock_type) == 'A' ? 'selected' : '' }}>Amount</option>
                                                            <option value="P" {{ old('expiry_lock_type', $customer->expiry_lock_type) == 'P' ? 'selected' : '' }}>Percentage</option>
                                                        </select>
                                                    </div>

                                                    <!-- Value -->
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Value</label>
                                                        <input type="number" step="0.01" class="form-control" name="expiry_lock_value" value="{{ old('expiry_lock_value', $customer->expiry_lock_value ?? '0.00') }}" placeholder="0.00">
                                                    </div>

                                                    <!-- No of Expiries per month -->
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">No. of Expiries per Month</label>
                                                        <input type="number" class="form-control" name="no_of_expiries_per_month" value="{{ old('no_of_expiries_per_month', $customer->no_of_expiries_per_month ?? '0') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Additional License Fields -->
                                    <h6 class="fw-bold mb-3 text-primary">Additional License Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <!-- TAN Number -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">TAN No.</label>
                                                    <input type="text" class="form-control" name="tan_number" value="{{ old('tan_number', $customer->tan_number) }}" placeholder="TAN number">
                                                </div>

                                                <!-- MSME License -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">MSME License</label>
                                                    <input type="text" class="form-control" name="msme_license" value="{{ old('msme_license', $customer->msme_license) }}" placeholder="MSME license number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4 mb-4">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection