@extends('layouts.admin')
@section('title', 'Add Customer')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Add New Customer</h2>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Customers
                    </a>
                </div>

                <form action="{{ route('admin.customers.store') }}" method="POST" id="customerForm" novalidate>
                    @csrf

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="customerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" 
                                type="button" role="tab">General Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="other-tab" data-bs-toggle="tab" data-bs-target="#other" 
                                type="button" role="tab" data-lazy-load="other">Other Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="locks-tab" data-bs-toggle="tab" data-bs-target="#locks" 
                                type="button" role="tab" data-lazy-load="locks">Locks</button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="customerTabsContent">
                        
                        <!-- GENERAL INFORMATION TAB - Loads immediately -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <!-- LEFT COLUMN -->
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Enter customer name" required>
                                                </div>
                                                <div class="col-8">
                                                    <label class="form-label fw-semibold">Code</label>
                                                    <input type="text" class="form-control" name="code" value="{{ old('code') }}" placeholder="Customer code">
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label fw-semibold">T(ax)/R(et)</label>
                                                    <select class="form-select" name="tax_registration">
                                                        <option value="R">Retail</option>
                                                        <option value="T">Tax</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Address 1</label>
                                                    <input type="text" class="form-control mb-2" name="address" value="{{ old('address') }}" placeholder="Address line 1">
                                                    <input type="text" class="form-control mb-2" name="address_line2" value="{{ old('address_line2') }}" placeholder="Address line 2">
                                                    <div class="row g-2">
                                                        <div class="col-8"><input type="text" class="form-control" name="address_line3" value="{{ old('address_line3') }}" placeholder="Address line 3"></div>
                                                        <div class="col-4"><input type="text" class="form-control" name="pin_code" value="{{ old('pin_code') }}" placeholder="Pin Code"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Address 2 (Optional)</label>
                                                    <input type="text" class="form-control mb-2" name="address2" value="{{ old('address2') }}" placeholder="Address line 1">
                                                    <input type="text" class="form-control mb-2" name="address2_line2" value="{{ old('address2_line2') }}" placeholder="Address line 2">
                                                    <input type="text" class="form-control" name="address2_line3" value="{{ old('address2_line3') }}" placeholder="Address line 3">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">City</label>
                                                    <input type="text" class="form-control" name="city" value="{{ old('city') }}" placeholder="Enter city">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Tel. (Office)</label>
                                                    <input type="text" class="form-control" name="telephone_office" value="{{ old('telephone_office') }}" placeholder="Office telephone">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Tel. (Residence)</label>
                                                    <input type="text" class="form-control" name="telephone_residence" value="{{ old('telephone_residence') }}" placeholder="Residence telephone">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Mobile</label>
                                                    <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}" placeholder="Mobile number">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">E-Mail</label>
                                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN -->
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Contact Person I</label>
                                                    <input type="text" class="form-control mb-2" name="contact_person1" value="{{ old('contact_person1') }}" placeholder="Contact person name">
                                                    <input type="text" class="form-control" name="mobile_contact1" value="{{ old('mobile_contact1') }}" placeholder="Mobile number">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Contact Person II</label>
                                                    <input type="text" class="form-control mb-2" name="contact_person2" value="{{ old('contact_person2') }}" placeholder="Contact person name">
                                                    <input type="text" class="form-control" name="mobile_contact2" value="{{ old('mobile_contact2') }}" placeholder="Mobile number">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Fax No.</label>
                                                    <input type="text" class="form-control" name="fax_number" value="{{ old('fax_number') }}" placeholder="Fax number">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Opening Balance</label>
                                                    <div class="row g-2">
                                                        <div class="col-8"><input type="number" step="0.01" class="form-control" name="opening_balance" value="{{ old('opening_balance', '0.00') }}" placeholder="0.00"></div>
                                                        <div class="col-4">
                                                            <select class="form-select" name="balance_type">
                                                                <option value="D">Debit</option>
                                                                <option value="C">Credit</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Local/Central</label>
                                                    <select class="form-select" name="local_central">
                                                        <option value="L">Local</option>
                                                        <option value="C">Central</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Anniversary Day</label>
                                                    <input type="date" class="form-control" name="anniversary_day" value="{{ old('anniversary_day') }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Birth Day</label>
                                                    <input type="date" class="form-control" name="birth_day" value="{{ old('birth_day') }}">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <input type="text" class="form-control" name="status" value="{{ old('status') }}" placeholder="Status">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Invoice Export</label>
                                                    <select class="form-select" name="invoice_export">
                                                        <option value="N">No</option>
                                                        <option value="Y">Yes</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Flag</label>
                                                    <input type="text" class="form-control" name="flag" value="{{ old('flag') }}" placeholder="Flag">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Due List Sequence</label>
                                                    <input type="number" class="form-control" name="due_list_sequence" value="{{ old('due_list_sequence', '0') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- LICENSE & TAX INFORMATION -->
                                    <h6 class="fw-bold mb-3 text-primary">License & Tax Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">DL No.</label>
                                                    <input type="text" class="form-control" name="dl_number" value="{{ old('dl_number') }}" placeholder="Drug license number">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">DL Expiry Date</label>
                                                    <input type="date" class="form-control" name="dl_expiry" value="{{ old('dl_expiry') }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">DL No. 1</label>
                                                    <input type="text" class="form-control" name="dl_number1" value="{{ old('dl_number1') }}" placeholder="Additional DL number">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Food License</label>
                                                    <input type="text" class="form-control" name="food_license" value="{{ old('food_license') }}" placeholder="Food license number">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">CST No.</label>
                                                    <input type="text" class="form-control" name="cst_number" value="{{ old('cst_number') }}" placeholder="CST number">
                                                </div>
                                                <div class="col-7">
                                                    <label class="form-label fw-semibold">TIN No.</label>
                                                    <input type="text" class="form-control" name="tin_number" value="{{ old('tin_number') }}" placeholder="TIN number">
                                                </div>
                                                <div class="col-5">
                                                    <label class="form-label fw-semibold">PAN</label>
                                                    <input type="text" class="form-control" name="pan_number" value="{{ old('pan_number') }}" placeholder="PAN number">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">DAY <span class="badge bg-danger ms-2">CST-New</span></label>
                                                    <input type="number" class="form-control" name="day_value" value="{{ old('day_value', '0') }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">GST No.</label>
                                                    <input type="text" class="form-control" name="gst_number" value="{{ old('gst_number') }}" placeholder="GST number">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Name for GSTR</label>
                                                    <input type="text" class="form-control" name="gst_name" value="{{ old('gst_name') }}" placeholder="Name as per GST records">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">State Code</label>
                                                    <select class="form-select" name="state_code_gst">
                                                        <option value="09">09-Uttar Pradesh</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">GST Status</label>
                                                    <select class="form-select" name="registration_status">
                                                        <option value="U">Unregistered</option>
                                                        <option value="R">Registered</option>
                                                        <option value="C">Composite</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Sales Man</label>
                                                    <div class="row g-2">
                                                        <div class="col-3"><input type="text" class="form-control" name="sales_man_code" value="{{ old('sales_man_code', '00') }}" placeholder="Code"></div>
                                                        <div class="col-9"><input type="text" class="form-control" name="sales_man_name" value="{{ old('sales_man_name') }}" placeholder="Salesman name"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Area</label>
                                                    <div class="row g-2">
                                                        <div class="col-3"><input type="text" class="form-control" name="area_code" value="{{ old('area_code', '00') }}" placeholder="Code"></div>
                                                        <div class="col-9"><input type="text" class="form-control" name="area_name" value="{{ old('area_name') }}" placeholder="Area name"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Route</label>
                                                    <div class="row g-2">
                                                        <div class="col-3"><input type="text" class="form-control" name="route_code" value="{{ old('route_code', '00') }}" placeholder="Code"></div>
                                                        <div class="col-9"><input type="text" class="form-control" name="route_name" value="{{ old('route_name') }}" placeholder="Route name"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">State</label>
                                                    <div class="row g-2">
                                                        <div class="col-3"><input type="text" class="form-control" name="state_code" value="{{ old('state_code', '00') }}" placeholder="Code"></div>
                                                        <div class="col-9"><input type="text" class="form-control" name="state_name" value="{{ old('state_name') }}" placeholder="State name"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Business Type</label>
                                                    <select class="form-select" name="business_type">
                                                        <option value="R">Retail</option>
                                                        <option value="W">Wholesale</option>
                                                        <option value="I">Institution</option>
                                                        <option value="D">Dept. Store</option>
                                                        <option value="O">Others</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Description</label>
                                                    <input type="text" class="form-control" name="description" value="{{ old('description') }}" placeholder="Additional description">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Order No. Required</label>
                                                    <select class="form-select" name="order_required">
                                                        <option value="N">No</option>
                                                        <option value="Y">Yes</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Aadhar</label>
                                                    <input type="text" class="form-control" name="aadhar_number" value="{{ old('aadhar_number') }}" placeholder="Aadhar number">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Registration Date</label>
                                                    <input type="date" class="form-control" name="registration_date" value="{{ old('registration_date', '2000-01-01') }}">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">End Date</label>
                                                    <input type="date" class="form-control" name="end_date" value="{{ old('end_date', '2000-01-01') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- OTHER DETAILS TAB - Lazy loaded -->
                        <div class="tab-pane fade" id="other" role="tabpanel">
                            <div class="lazy-tab-content" data-tab="other">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading...</p>
                                </div>
                            </div>
                        </div>

                        <!-- LOCKS TAB - Lazy loaded -->
                        <div class="tab-pane fade" id="locks" role="tabpanel">
                            <div class="lazy-tab-content" data-tab="locks">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading...</p>
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

    <!-- Lazy Tab Content Templates (Hidden, loaded via JS) -->
    <template id="tab-other-template">
        @include('admin.customers.partials.tab-other')
    </template>
    <template id="tab-locks-template">
        @include('admin.customers.partials.tab-locks')
    </template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadedTabs = { general: true };
    
    // Load tabs in background after page loads
    setTimeout(() => {
        loadTabContent('other');
        loadTabContent('locks');
    }, 100);
    
    // Also load on tab click (fallback)
    document.querySelectorAll('[data-lazy-load]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function() {
            const tabName = this.dataset.lazyLoad;
            loadTabContent(tabName);
        });
    });
    
    function loadTabContent(tabName) {
        if (loadedTabs[tabName]) return;
        
        const template = document.getElementById(`tab-${tabName}-template`);
        const container = document.querySelector(`.lazy-tab-content[data-tab="${tabName}"]`);
        
        if (template && container) {
            container.innerHTML = template.innerHTML;
            loadedTabs[tabName] = true;
        }
    }
});
</script>
@endpush
