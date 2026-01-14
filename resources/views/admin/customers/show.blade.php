@extends('layouts.admin')
@section('title', 'Customer Details')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Customer Details: {{ $customer->name }}</h2>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Customer
                        </a>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Customers
                        </a>
                    </div>
                </div>
  <!-- Customer Features Section -->
                <div class="card shadow-sm mt-4 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Customer Features & Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <h6 class="mb-3 fw-bold">Transaction & Ledger</h6>
                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ route('admin.customers.ledger', $customer) }}" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-book me-2"></i>Ledger (F10)
                                    </a>
                                    <a href="{{ route('admin.customers.bills', $customer) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-receipt me-2"></i>List of Bills
                                    </a>
                                    <a href="{{ route('admin.customers.dues', $customer) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-list-check me-2"></i>Due List (F5)
                                    </a>
                                    <a href="{{ route('admin.customers.expiry-ledger', $customer) }}" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-calendar-x me-2"></i>Expiry Ledger
                                    </a>
                                </div>

                                <h6 class="mb-3 fw-bold mt-4">Delivery & Inventory</h6>
                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ route('admin.customers.challans', $customer) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-box-seam me-2"></i>Pending Challans
                                    </a>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <h6 class="mb-3 fw-bold">Pricing & Discounts</h6>
                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ route('admin.customers.special-rates', $customer) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-tag me-2"></i>Special Rates
                                    </a>
                                    <a href="{{ route('admin.customers.discounts', $customer) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-percent me-2"></i>Discount (Brk/Exp)
                                    </a>
                                    <a href="{{ route('admin.customers.copy-discount', $customer) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-files me-2"></i>Copy Discount
                                    </a>
                                </div>

                                <h6 class="mb-3 fw-bold mt-4">Pharmacy & Medical</h6>
                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ route('admin.customers.prescriptions', $customer) }}" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-prescription me-2"></i>Prescription List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                                <p class="form-control-plaintext">{{ $customer->name ?? '-' }}</p>
                                            </div>

                                            <!-- Code & Tax Registration -->
                                            <div class="col-8">
                                                <label class="form-label fw-semibold">Code</label>
                                                <p class="form-control-plaintext">{{ $customer->code ?? '-' }}</p>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label fw-semibold">T(ax)/R(et)</label>
                                                <p class="form-control-plaintext">{{ $customer->tax_registration ?? '-' }}</p>
                                            </div>

                                            <!-- Address 1 -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Address 1</label>
                                                <p class="form-control-plaintext mb-1">{{ $customer->address ?? '-' }}</p>
                                                <p class="form-control-plaintext mb-1">{{ $customer->address_line2 ?? '-' }}</p>
                                                <div class="row g-2">
                                                    <div class="col-8">
                                                        <p class="form-control-plaintext">{{ $customer->address_line3 ?? '-' }}</p>
                                                    </div>
                                                    <div class="col-4">
                                                        <p class="form-control-plaintext">{{ $customer->pin_code ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Address 2 -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Address 2 (Optional)</label>
                                                <p class="form-control-plaintext mb-1">{{ $customer->address2 ?? '-' }}</p>
                                                <p class="form-control-plaintext mb-1">{{ $customer->address2_line2 ?? '-' }}</p>
                                                <p class="form-control-plaintext">{{ $customer->address2_line3 ?? '-' }}</p>
                                            </div>

                                            <!-- City -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">City</label>
                                                <p class="form-control-plaintext">{{ $customer->city ?? '-' }}</p>
                                            </div>

                                            <!-- Contact Numbers -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Tel. (Office)</label>
                                                <p class="form-control-plaintext">{{ $customer->telephone_office ?? '-' }}</p>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Tel. (Residence)</label>
                                                <p class="form-control-plaintext">{{ $customer->telephone_residence ?? '-' }}</p>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Mobile</label>
                                                <p class="form-control-plaintext">{{ $customer->mobile ?? '-' }}</p>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">E-Mail</label>
                                                <p class="form-control-plaintext">{{ $customer->email ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- RIGHT COLUMN -->
                                    <div class="col-md-6">
                                        <div class="row g-3">
                                            <!-- Contact Person I -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Contact Person I</label>
                                                <p class="form-control-plaintext mb-1">{{ $customer->contact_person1 ?? '-' }}</p>
                                                <p class="form-control-plaintext">{{ $customer->mobile_contact1 ?? '-' }}</p>
                                            </div>

                                            <!-- Contact Person II -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Contact Person II</label>
                                                <p class="form-control-plaintext mb-1">{{ $customer->contact_person2 ?? '-' }}</p>
                                                <p class="form-control-plaintext">{{ $customer->mobile_contact2 ?? '-' }}</p>
                                            </div>

                                            <!-- Fax -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Fax No.</label>
                                                <p class="form-control-plaintext">{{ $customer->fax_number ?? '-' }}</p>
                                            </div>

                                            <!-- Opening Balance -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Opening Balance</label>
                                                <div class="row g-2">
                                                    <div class="col-8">
                                                        <p class="form-control-plaintext">{{ $customer->opening_balance ?? '0.00' }}</p>
                                                    </div>
                                                    <div class="col-4">
                                                        <p class="form-control-plaintext">{{ $customer->balance_type == 'D' ? 'Debit' : 'Credit' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Local/Central & Anniversary -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Local/Central</label>
                                                <p class="form-control-plaintext">{{ $customer->local_central == 'L' ? 'Local' : 'Central' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Anniversary Day</label>
                                                <p class="form-control-plaintext">{{ $customer->anniversary_day ?? '-' }}</p>
                                            </div>

                                            <!-- Birthday -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Birth Day</label>
                                                <p class="form-control-plaintext">{{ $customer->birth_day ?? '-' }}</p>
                                            </div>

                                            <!-- Status & Invoice Export -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Status</label>
                                                <p class="form-control-plaintext">{{ $customer->status ?? '-' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Invoice Export</label>
                                                <p class="form-control-plaintext">{{ $customer->invoice_export == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>

                                            <!-- Flag & Due List Sequence -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Flag</label>
                                                <p class="form-control-plaintext">{{ $customer->flag ?? '-' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Due List Sequence</label>
                                                <p class="form-control-plaintext">{{ $customer->due_list_sequence ?? '0' }}</p>
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
                                                <p class="form-control-plaintext">{{ $customer->dl_number ?? '-' }}</p>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">DL Expiry Date</label>
                                                <p class="form-control-plaintext">{{ $customer->dl_expiry ?? '-' }}</p>
                                            </div>

                                            <!-- DL Number 1 -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">DL No. 1</label>
                                                <p class="form-control-plaintext">{{ $customer->dl_number1 ?? '-' }}</p>
                                            </div>

                                            <!-- Food License -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Food License</label>
                                                <p class="form-control-plaintext">{{ $customer->food_license ?? '-' }}</p>
                                            </div>

                                            <!-- CST Number -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">CST No.</label>
                                                <p class="form-control-plaintext">{{ $customer->cst_number ?? '-' }}</p>
                                            </div>

                                            <!-- TIN & PAN -->
                                            <div class="col-7">
                                                <label class="form-label fw-semibold">TIN No.</label>
                                                <p class="form-control-plaintext">{{ $customer->tin_number ?? '-' }}</p>
                                            </div>
                                            <div class="col-5">
                                                <label class="form-label fw-semibold">PAN</label>
                                                <p class="form-control-plaintext">{{ $customer->pan_number ?? '-' }}</p>
                                            </div>

                                            <!-- DAY -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">DAY <span class="badge bg-danger ms-2">CST-New</span></label>
                                                <p class="form-control-plaintext">{{ $customer->day_value ?? '0' }}</p>
                                            </div>

                                            <!-- GST Number -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">GST No.</label>
                                                <p class="form-control-plaintext">{{ $customer->gst_number ?? '-' }}</p>
                                            </div>

                                            <!-- GST Name -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Name for GSTR</label>
                                                <p class="form-control-plaintext">{{ $customer->gst_name ?? '-' }}</p>
                                            </div>

                                            <!-- State Code & Registration Status -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">State Code</label>
                                                <p class="form-control-plaintext">{{ $customer->state_code_gst ?? '09' }} - Uttar Pradesh</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">GST Status</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->registration_status == 'U') Unregistered
                                                    @elseif($customer->registration_status == 'R') Registered
                                                    @elseif($customer->registration_status == 'C') Composite
                                                    @else - @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- RIGHT COLUMN -->
                                    <div class="col-md-6">
                                        <div class="row g-3">
                                            <!-- Sales Man -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Sales Man</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->salesman)
                                                        {{ $customer->salesman->code ? $customer->salesman->code . ' - ' : '' }}{{ $customer->salesman->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>

                                            <!-- Area -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Area</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->area)
                                                        {{ $customer->area->alter_code ? $customer->area->alter_code . ' - ' : '' }}{{ $customer->area->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>

                                            <!-- Route -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Route</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->route)
                                                        {{ $customer->route->alter_code ? $customer->route->alter_code . ' - ' : '' }}{{ $customer->route->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>

                                            <!-- State -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">State</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->state)
                                                        {{ $customer->state->alter_code ? $customer->state->alter_code . ' - ' : '' }}{{ $customer->state->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>

                                            <!-- Business Type -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Business Type</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->business_type == 'R') Retail
                                                    @elseif($customer->business_type == 'W') Wholesale
                                                    @elseif($customer->business_type == 'I') Institution
                                                    @elseif($customer->business_type == 'D') Dept. Store
                                                    @elseif($customer->business_type == 'O') Others
                                                    @else - @endif
                                                </p>
                                            </div>

                                            <!-- Description -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Description</label>
                                                <p class="form-control-plaintext">{{ $customer->description ?? '-' }}</p>
                                            </div>

                                            <!-- Order Required & Aadhar -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Order No. Required</label>
                                                <p class="form-control-plaintext">{{ $customer->order_required == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Aadhar</label>
                                                <p class="form-control-plaintext">{{ $customer->aadhar_number ?? '-' }}</p>
                                            </div>

                                            <!-- Registration Date -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Registration Date</label>
                                                <p class="form-control-plaintext">{{ $customer->registration_date ?? '-' }}</p>
                                            </div>

                                            <!-- End Date -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">End Date</label>
                                                <p class="form-control-plaintext">{{ $customer->end_date ?? '-' }}</p>
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
                                                <p class="form-control-plaintext">{{ $customer->bank ?? '-' }}</p>
                                            </div>

                                            <!-- Branch -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Branch</label>
                                                <p class="form-control-plaintext">{{ $customer->branch ?? '-' }}</p>
                                            </div>

                                            <!-- Closed On & Credit Limit -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Closed On</label>
                                                <p class="form-control-plaintext">{{ $customer->closed_on ?? '-' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Credit Limit</label>
                                                <p class="form-control-plaintext">{{ $customer->credit_limit ?? '0' }}</p>
                                            </div>

                                            <!-- Sale Rate Type -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Sale Rate Type</label>
                                                <small class="d-block text-muted mb-2">1.Sale / 2.W.Sale / 3.Spl.Rate / 4.Pur.Rate / 5.Cost / 6.MRP / 7.T.Rate / 8.Cost W/O F.Qty.</small>
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <p class="form-control-plaintext">
                                                            @if($customer->sale_rate_type == 1) 1 - Sale
                                                            @elseif($customer->sale_rate_type == 2) 2 - W.Sale
                                                            @elseif($customer->sale_rate_type == 3) 3 - Spl. Rate
                                                            @elseif($customer->sale_rate_type == 4) 4 - Pur. Rate
                                                            @elseif($customer->sale_rate_type == 5) 5 - Cost
                                                            @elseif($customer->sale_rate_type == 6) 6 - MRP
                                                            @elseif($customer->sale_rate_type == 7) 7 - T.Rate
                                                            @elseif($customer->sale_rate_type == 8) 8 - Cost W/O F.Qty.
                                                            @else - @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label small">Add %</label>
                                                    </div>
                                                    <div class="col-4">
                                                        <p class="form-control-plaintext">{{ $customer->add_percent ?? '0' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tax & Expiry Options -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Tax on Br./Expiry</label>
                                                <p class="form-control-plaintext">{{ $customer->tax_on_br_expiry == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Expiry CN On</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->expiry_on == 'M') MRP
                                                    @elseif($customer->expiry_on == 'S') Sale Rate
                                                    @elseif($customer->expiry_on == 'P') Pur. Rate
                                                    @elseif($customer->expiry_on == 'W') WS Rate
                                                    @elseif($customer->expiry_on == 'L') Spl. Rate
                                                    @else - @endif
                                                </p>
                                            </div>

                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Dis. After Scheme</label>
                                                <p class="form-control-plaintext">{{ $customer->dis_after_scheme == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Expiry RN On</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->expiry_rn_on == 'M') MRP
                                                    @elseif($customer->expiry_rn_on == 'S') Sale Rate
                                                    @elseif($customer->expiry_rn_on == 'P') Pur. Rate
                                                    @elseif($customer->expiry_rn_on == 'W') WS Rate
                                                    @else - @endif
                                                </p>
                                            </div>

                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Dis. On Excise</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->dis_on_excise == 'Y') Yes
                                                    @elseif($customer->dis_on_excise == 'N') No
                                                    @elseif($customer->dis_on_excise == 'X') X
                                                    @else - @endif
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Sale Pur. Status</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->sale_pur_status == 'S') Sale
                                                    @elseif($customer->sale_pur_status == 'P') Purchase
                                                    @elseif($customer->sale_pur_status == 'B') Both
                                                    @else - @endif
                                                </p>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Scheme Type</label>
                                                <p class="form-control-plaintext">{{ $customer->scm_type ?? '-' }}</p>
                                            </div>

                                            <!-- Net Rate -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Net Rate</label>
                                                <p class="form-control-plaintext">{{ $customer->net_rate == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">No. of Items In Bill</label>
                                                <p class="form-control-plaintext">{{ $customer->no_of_items_in_bill ?? '0' }}</p>
                                            </div>

                                            <!-- Invoice Print Order -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Invoice Print Order</label>
                                                <small class="d-block text-muted mb-2">0.Default / 1.Company / 2.User Defined / 3.Name</small>
                                                <p class="form-control-plaintext">{{ $customer->invoice_print_order ?? '-' }}</p>
                                            </div>

                                            <!-- SR Replacement -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">SR Replacement</label>
                                                <p class="form-control-plaintext">{{ $customer->sr_replacement == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>

                                            <!-- Cash Sale -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Cash Sale</label>
                                                <p class="form-control-plaintext">{{ $customer->cash_sale == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>

                                            <!-- Invoice Format -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Invoice Format</label>
                                                <p class="form-control-plaintext">{{ $customer->invoice_format ?? '0' }}</p>
                                            </div>

                                            <!-- Fixed Discount -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Fixed Discount</label>
                                                <p class="form-control-plaintext">{{ $customer->fixed_discount ?? '0' }}</p>
                                            </div>

                                            <!-- GST Discount Fields -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold mb-2">Brk./Expiry Dis. On Item With GST</label>
                                                <div class="row g-2">
                                                    <div class="col">
                                                        <label class="form-label small">5%</label>
                                                        <p class="form-control-plaintext">{{ $customer->gst_5_percent ?? '0' }}</p>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label small">12%</label>
                                                        <p class="form-control-plaintext">{{ $customer->gst_12_percent ?? '0' }}</p>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label small">18%</label>
                                                        <p class="form-control-plaintext">{{ $customer->gst_18_percent ?? '0' }}</p>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label small">28%</label>
                                                        <p class="form-control-plaintext">{{ $customer->gst_28_percent ?? '0' }}</p>
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label small">0%</label>
                                                        <p class="form-control-plaintext">{{ $customer->gst_0_percent ?? '0' }}</p>
                                                    </div>
                                                </div>
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
                                                <p class="form-control-plaintext">{{ $customer->ref ?? '-' }}</p>
                                            </div>

                                            <!-- TDS -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">TDS (Tax Deducted at Source)</label>
                                                <p class="form-control-plaintext">{{ $customer->tds == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>

                                            <!-- Add Charges & TCS -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Add Charges with GST</label>
                                                <p class="form-control-plaintext">{{ $customer->add_charges_with_gst == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">TCS Applicable</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->tcs_applicable == 'Y') Yes
                                                    @elseif($customer->tcs_applicable == 'N') No
                                                    @elseif($customer->tcs_applicable == '#') #
                                                    @else - @endif
                                                </p>
                                            </div>

                                            <!-- BE Incl. -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">BE Incl.</label>
                                                <p class="form-control-plaintext">{{ $customer->be_incl == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>

                                            <!-- Brk/Expiry Msg -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Brk./Expiry Msg. in Sale</label>
                                                <p class="form-control-plaintext">{{ $customer->brk_expiry_msg_in_sale == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>

                                            <!-- Series Lock & Branch Transfer -->
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Series Lock</label>
                                                <p class="form-control-plaintext">{{ $customer->series_lock ?? '-' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Branch Trf.</label>
                                                <p class="form-control-plaintext">{{ $customer->branch_trf ?? '-' }}</p>
                                            </div>

                                            <!-- Transfer Account -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Transfer Account</label>
                                                <p class="form-control-plaintext">{{ $customer->trnf_account ?? '-' }}</p>
                                            </div>

                                            <!-- eWay Details Section -->
                                            <div class="col-12">
                                                <div class="border rounded p-3 bg-light">
                                                    <h6 class="fw-bold mb-3">eWay Details</h6>
                                                    
                                                    <!-- Transport -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Transport</label>
                                                        <p class="form-control-plaintext">
                                                            @if($customer->transport)
                                                                {{ $customer->transport->alter_code ? $customer->transport->alter_code . ' - ' : '' }}{{ $customer->transport->name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </p>
                                                    </div>

                                                    <!-- Distance -->
                                                    <div class="mb-0">
                                                        <label class="form-label fw-semibold">Distance (KM)</label>
                                                        <p class="form-control-plaintext">{{ $customer->distance ?? '0' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Expiry Options -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Expiry - R(epl) / C(redit Note)</label>
                                                <p class="form-control-plaintext">
                                                    @if($customer->expiry_repl_credit == 'C') Credit Note
                                                    @elseif($customer->expiry_repl_credit == 'R') Replacement
                                                    @else - @endif
                                                </p>
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
                                                <p class="form-control-plaintext">{{ $customer->max_os_amount ?? '0.00' }}</p>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label fw-semibold">Max Limit On</label>
                                                <p class="form-control-plaintext">{{ $customer->max_limit_on == 'D' ? 'Due' : 'Ledger' }}</p>
                                            </div>

                                            <!-- Maximum Invoice Amount -->
                                            <div class="col-8">
                                                <label class="form-label fw-semibold">Maximum Inv. Amount</label>
                                                <p class="form-control-plaintext">{{ $customer->max_inv_amount ?? '0.00' }}</p>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label fw-semibold">Max No. O/S Inv.</label>
                                                <p class="form-control-plaintext">{{ $customer->max_no_os_inv ?? '0' }}</p>
                                            </div>

                                            <!-- Follow Conditions Strictly -->
                                            <div class="col-8">
                                                <label class="form-label fw-semibold">Follow Conditions Strictly</label>
                                                <p class="form-control-plaintext">{{ $customer->follow_conditions_strictly == 'Y' ? 'Yes' : 'No' }}</p>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label fw-semibold">Credit Days Lock</label>
                                                <p class="form-control-plaintext">{{ $customer->credit_limit_days_lock ?? '0' }}</p>
                                            </div>

                                            <!-- Open Lock Once -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Open Lock Once</label>
                                                <p class="form-control-plaintext">{{ $customer->open_lock_once == 'Y' ? 'Yes' : 'No' }}</p>
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
                                                    <p class="form-control-plaintext">{{ $customer->expiry_lock_type == 'A' ? 'Amount' : 'Percentage' }}</p>
                                                </div>

                                                <!-- Value -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Value</label>
                                                    <p class="form-control-plaintext">{{ $customer->expiry_lock_value ?? '0.00' }}</p>
                                                </div>

                                                <!-- No of Expiries per month -->
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">No. of Expiries per Month</label>
                                                    <p class="form-control-plaintext">{{ $customer->no_of_expiries_per_month ?? '0' }}</p>
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
                                                <p class="form-control-plaintext">{{ $customer->tan_number ?? '-' }}</p>
                                            </div>

                                            <!-- MSME License -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">MSME License</label>
                                                <p class="form-control-plaintext">{{ $customer->msme_license ?? '-' }}</p>
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
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Customer
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection