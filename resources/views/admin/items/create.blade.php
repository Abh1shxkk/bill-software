@extends('layouts.admin')
@section('title', 'Add Item')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Add New Item</h2>
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Items
                    </a>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Validation Errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.items.store') }}" method="POST">
                    @csrf



                    <!-- Header Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Header Section</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row 1: Name and Company -->
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" placeholder="Item name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Company <span class="text-danger">*</span></label>
                                    <select class="form-select @error('company_id') is-invalid @enderror"
                                        name="company_id" id="company_id">
                                        <option value="">-- Select Company --</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}"
                                                data-short-name="{{ $company->short_name }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }} ({{ $company->short_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Company Short Name</label>
                                    <input type="text" class="form-control" name="company_short_name"
                                        id="company_short_name" value="{{ old('company_short_name') }}" readonly style="background-color: #f0f0f0;">
                                </div>
                            </div>

                            <!-- Row 2: Packing, Unit (Fixed), Unit Type, Mfg. By, Location, Status, Schedule -->
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Packing</label>
                                    <input type="text" class="form-control" name="packing" value="{{ old('packing') }}"
                                        placeholder="Packing">
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">Unit</label>
                                    <input type="number" class="form-control" name="unit" value="{{ old('unit', 1) }}"
                                        readonly style="background-color: #f0f0f0;">
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">Unit</label>
                                    <select class="form-select" name="unit_type">
                                        <option value="Unit" {{ old('unit_type', 'Unit') == 'Unit' ? 'selected' : '' }}>Unit</option>
                                        <option value="Kg." {{ old('unit_type') == 'Kg.' ? 'selected' : '' }}>Kg.</option>
                                        <option value="L." {{ old('unit_type') == 'L.' ? 'selected' : '' }}>L.</option>
                                        <option value="Gm." {{ old('unit_type') == 'Gm.' ? 'selected' : '' }}>Gm.</option>
                                        <option value="Ml." {{ old('unit_type') == 'Ml.' ? 'selected' : '' }}>Ml.</option>
                                        <option value="Doz." {{ old('unit_type') == 'Doz.' ? 'selected' : '' }}>Doz.</option>
                                        <option value="Mtr." {{ old('unit_type') == 'Mtr.' ? 'selected' : '' }}>Mtr.</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Mfg. By</label>
                                    <input type="text" class="form-control" name="mfg_by" value="{{ old('mfg_by') }}"
                                        placeholder="Manufacturer">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" class="form-control" name="location" value="{{ old('location') }}"
                                        placeholder="Location">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control" name="status" value="{{ old('status') }}"
                                        placeholder="Status">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Schedule</label>
                                    <input type="text" class="form-control" name="schedule"
                                        value="{{ old('schedule', '00') }}" placeholder="00">
                                </div>
                            </div>

                            <!-- Row 3: Box Qty, Case Qty, Min. Level, Max. Level, Bar Code, Division -->
                            <div class="row">
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">Box Qty</label>
                                    <input type="number" class="form-control" name="box_qty" value="{{ old('box_qty', 0) }}"
                                        placeholder="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Case Qty</label>
                                    <input type="number" class="form-control" name="case_qty"
                                        value="{{ old('case_qty', 0) }}" placeholder="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Min. Level</label>
                                    <input type="number" step="0.01" class="form-control" name="min_level"
                                        value="{{ old('min_level', 0) }}" placeholder="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Max. Level</label>
                                    <input type="number" step="0.01" class="form-control" name="max_level"
                                        value="{{ old('max_level', 0) }}" placeholder="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Bar Code</label>
                                    <input type="text" class="form-control" name="bar_code" value="{{ old('bar_code') }}"
                                        placeholder="Bar code">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Division</label>
                                    <input type="text" class="form-control" name="division"
                                        value="{{ old('division', '00') }}" placeholder="00">
                                </div>
                            </div>

                            <!-- Row 4: Flag, Narcotic Checkbox -->
                            <div class="row">
                                <div class="col-md-10 mb-3">
                                    <label class="form-label">Flag</label>
                                    <input type="text" class="form-control" name="flag" value="{{ old('flag') }}"
                                        placeholder="Flag">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label d-block">Narcotic</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="narcotic_flag"
                                            id="narcotic_flag" value="Y" {{ old('narcotic_flag') == 'Y' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="narcotic_flag">Yes</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Details Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Sale Details</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row 1: S. Rate -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">S. Rate</label>
                                    <input type="number" step="0.01" class="form-control" name="s_rate"
                                        value="{{ old('s_rate', '0.00') }}" placeholder="0.00">
                                </div>
                            </div>

                            <!-- Row 2: MRP with Net label -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">MRP</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="mrp"
                                            value="{{ old('mrp', '0.00') }}" placeholder="0.00">
                                        <span class="input-group-text" style="background-color: #e3f2fd; color: #1976d2; font-weight: bold;">Net</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: W.S.Rate with Y Toggle -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">W.S.Rate</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="ws_rate"
                                            value="{{ old('ws_rate', '0.00') }}" placeholder="0.00" style="max-width: 120px;">
                                        <select class="form-select" name="ws_net_toggle" style="max-width: 60px;">
                                            <option value="Y" {{ old('ws_net_toggle', 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                            <option value="N" {{ old('ws_net_toggle') == 'N' ? 'selected' : '' }}>N</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 4: Spl.Rate with Y Toggle -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Spl.Rate</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="spl_rate"
                                            value="{{ old('spl_rate', '0.00') }}" placeholder="0.00" style="max-width: 120px;">
                                        <select class="form-select" name="spl_net_toggle" style="max-width: 60px;">
                                            <option value="Y" {{ old('spl_net_toggle', 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                                            <option value="N" {{ old('spl_net_toggle') == 'N' ? 'selected' : '' }}>N</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 5: Scheme (0 + 0) -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Scheme</label>
                                    <div class="input-group">
                                        <input type="number" step="1" class="form-control" name="scheme_plus"
                                            value="{{ old('scheme_plus', 0) }}" placeholder="0" style="max-width: 80px;">
                                        <span class="input-group-text">+</span>
                                        <input type="number" step="1" class="form-control" name="scheme_minus"
                                            value="{{ old('scheme_minus', 0) }}" placeholder="0" style="max-width: 80px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Row 6: Min.GP -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Min.GP</label>
                                    <input type="number" step="0.01" class="form-control" name="min_gp"
                                        value="{{ old('min_gp', '0.00') }}" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Details Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-cart me-2"></i>Purchase Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Pur. Rate -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Pur. Rate</label>
                                    <input type="number" step="0.01" class="form-control" name="pur_rate"
                                        value="{{ old('pur_rate', '0.00') }}" placeholder="0.00">
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Cost -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cost</label>
                                    <input type="number" step="0.01" class="form-control" name="cost"
                                        value="{{ old('cost', '0.00') }}" placeholder="0.00">
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Scheme (+ and -) -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Scheme</label>
                                    <div class="input-group">
                                        <input type="number" step="1" class="form-control" name="pur_scheme_plus"
                                            value="{{ old('pur_scheme_plus', 0) }}" placeholder="0" style="max-width: 80px;">
                                        <span class="input-group-text">+</span>
                                        <input type="number" step="1" class="form-control" name="pur_scheme_minus"
                                            value="{{ old('pur_scheme_minus', 0) }}" placeholder="0" style="max-width: 80px;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- NR -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">NR</label>
                                    <input type="number" step="0.01" class="form-control" name="nr"
                                        value="{{ old('nr', '0.00') }}" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GST Details Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-percent me-2"></i>GST Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">HSN Code</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="hsn_code_input" name="hsn_code" 
                                               value="{{ old('hsn_code') }}" placeholder="Click to select HSN" readonly 
                                               style="cursor: pointer; background-color: #fff;">
                                        <button type="button" class="btn btn-outline-secondary" id="hsn_code_btn">
                                            <i class="bi bi-search"></i>
                                        </button>
                                        
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">CGST (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="cgst_percent" name="cgst_percent"
                                        value="{{ old('cgst_percent', 0) }}" placeholder="0.00" readonly style="background-color: #f0f0f0;">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">SGST (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="sgst_percent" name="sgst_percent"
                                        value="{{ old('sgst_percent', 0) }}" placeholder="0.00" readonly style="background-color: #f0f0f0;">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">IGST (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="igst_percent" name="igst_percent"
                                        value="{{ old('igst_percent', 0) }}" placeholder="0.00" readonly style="background-color: #f0f0f0;">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cess (%)</label>
                                    <input type="number" step="0.01" class="form-control" name="cess_percent"
                                        value="{{ old('cess_percent', 0) }}" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Details Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Other Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- LEFT PARTITION -->
                                <div class="col-md-6">
                                    <!-- VAT -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">VAT(%)</label>
                                            <input type="number" step="0.01" class="form-control" name="vat_percent"
                                                value="{{ old('vat_percent', 0) }}" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Fixed Dis. (Y/N/M)</label>
                                            <select class="form-select" name="fixed_dis">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('fixed_dis') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('fixed_dis') == 'N' ? 'selected' : '' }}>N</option>
                                                <option value="M" {{ old('fixed_dis') == 'M' ? 'selected' : '' }}>M</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Expiry, Inclusive -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Expiry</label>
                                            <select class="form-select" name="expiry_flag">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('expiry_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('expiry_flag') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Inclusive :</label>
                                            <select class="form-select" name="inclusive_flag">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('inclusive_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('inclusive_flag') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Generic, Bar Code -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Generic</label>
                                            <select class="form-select" name="generic_flag">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('generic_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('generic_flag') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Bar Code :</label>
                                            <select class="form-select" name="bar_code_flag">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('bar_code_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('bar_code_flag') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- H.Scm, Max Inv. Qty (Numeric) -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">H.Scm</label>
                                            <select class="form-select" name="h_scm_flag">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('h_scm_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('h_scm_flag') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Max Inv.Qty :</label>
                                            <input type="number" step="0.01" class="form-control" name="max_inv_qty_value"
                                                value="{{ old('max_inv_qty_value', 0) }}" placeholder="21332">
                                        </div>
                                    </div>

                                    <!-- Q.Scm, Weight -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Q.Scm</label>
                                            <select class="form-select" name="q_scm_flag">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('q_scm_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('q_scm_flag') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Weight :</label>
                                            <input type="number" step="0.01" class="form-control" name="weight_new"
                                                value="{{ old('weight_new', 0) }}" placeholder="0.000">
                                        </div>
                                    </div>

                                    <!-- Locks, Bar Code (empty for spacing) -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Locks</label>
                                            <input type="text" class="form-control" name="locks_flag" value="S" readonly style="background-color: #f0f0f0;">
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT PARTITION -->
                                <div class="col-md-6">
                                    <!-- Fixed Dis %, Fixed Dis Type -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">%</label>
                                            <input type="number" step="0.01" class="form-control" name="fixed_dis_percent"
                                                value="{{ old('fixed_dis_percent', 0) }}" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Fixed Dis Type</label>
                                            <select class="form-select" name="fixed_dis_type">
                                                <option value="">-- Select --</option>
                                                <option value="W" {{ old('fixed_dis_type') == 'W' ? 'selected' : '' }}>W</option>
                                                <option value="R" {{ old('fixed_dis_type') == 'R' ? 'selected' : '' }}>R</option>
                                                <option value="I" {{ old('fixed_dis_type') == 'I' ? 'selected' : '' }}>I</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Def. Qty, Volume -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Def. Qty :</label>
                                            <select class="form-select" name="def_qty_flag">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('def_qty_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('def_qty_flag') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Volume :</label>
                                            <input type="number" step="0.01" class="form-control" name="volume_new"
                                                value="{{ old('volume_new', 0) }}" placeholder="0">
                                        </div>
                                    </div>

                                    <!-- Comp. Name (BC) -->
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <label class="form-label">Comp.Name (BC) :</label>
                                            <select class="form-select" name="comp_name_bc_new">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('comp_name_bc_new') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('comp_name_bc_new') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- DPC Item, Lock Sale -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">DPC Item</label>
                                            <select class="form-select" name="dpc_item_flag">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('dpc_item_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('dpc_item_flag') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Lock Sale :</label>
                                            <select class="form-select" name="lock_sale_flag">
                                                <option value="">-- Select --</option>
                                                <option value="Y" {{ old('lock_sale_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                                <option value="N" {{ old('lock_sale_flag') == 'N' ? 'selected' : '' }}>N</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 1(Max) 2(Min), MRP for Sale -->
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">1.(Max) 2.(Min)</label>
                                            <select class="form-select" name="max_min_flag">
                                                <option value="">-- Select --</option>
                                                <option value="1" {{ old('max_min_flag') == '1' ? 'selected' : '' }}>1</option>
                                                <option value="2" {{ old('max_min_flag') == '2' ? 'selected' : '' }}>2</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">MRP for Sale</label>
                                            <input type="number" step="0.01" class="form-control" name="mrp_for_sale_new"
                                                value="{{ old('mrp_for_sale_new', 0) }}" placeholder="0.00">
                                        </div>
                                    </div>

                                    <!-- Max Inv. Qty Type (W/R/I) -->
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <label class="form-label">Max Inv. Qty Type</label>
                                            <select class="form-select" name="max_inv_qty_new">
                                                <option value="">-- Select --</option>
                                                <option value="W" {{ old('max_inv_qty_new') == 'W' ? 'selected' : '' }}>W</option>
                                                <option value="R" {{ old('max_inv_qty_new') == 'R' ? 'selected' : '' }}>R</option>
                                                <option value="I" {{ old('max_inv_qty_new') == 'I' ? 'selected' : '' }}>I</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-box me-2"></i>Bottom Section</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row 1: Commodity (left), Current Scheme (right) -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Commodity :</label>
                                    <input type="text" class="form-control" name="commodity" value="{{ old('commodity') }}" placeholder="">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Current Scheme :</label>
                                    <select class="form-select" name="current_scheme_flag" id="current_scheme_flag">
                                        <option value="">-- Select --</option>
                                        <option value="Y" {{ old('current_scheme_flag') == 'Y' ? 'selected' : '' }}>Y</option>
                                        <option value="N" {{ old('current_scheme_flag') == 'N' ? 'selected' : '' }}>N</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Row 1.5: Scheme (0 + 0) -->
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Scheme</label>
                                    <div class="input-group">
                                        <input type="number" step="1" class="form-control" name="scheme_plus_value"
                                            value="{{ old('scheme_plus_value', 0) }}" placeholder="0" style="max-width: 80px;">
                                        <span class="input-group-text">+</span>
                                        <input type="number" step="1" class="form-control" name="scheme_minus_value"
                                            value="{{ old('scheme_minus_value', 0) }}" placeholder="0" style="max-width: 80px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Row 1.6: From/To dates -->
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">From</label>
                                    <input type="date" class="form-control" name="from_date" value="{{ old('from_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">To</label>
                                    <input type="date" class="form-control" name="to_date" value="{{ old('to_date') }}">
                                </div>
                            </div>

                            <!-- Row 2: Category (left with 2 fields), Empty (right) -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Category :</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="category" value="{{ old('category') }}" placeholder="">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="category_2" value="{{ old('category_2') }}" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                            </div>

                            <!-- Row 3: UPC (left), Empty (right) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">UPC</label>
                                    <input type="text" class="form-control" name="upc" value="{{ old('upc') }}" placeholder="">
                                </div>
                                <div class="col-md-6"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Item
                            </button>
                            <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Simple Test Modal -->
    <div class="modal fade" id="testModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>This is a test modal to check if modals work at all.</p>
                    <button type="button" class="btn btn-success" onclick="alert('Button works!')">
                        Click Me to Test
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- HSN Code Selection Modal - Right Sliding -->
    <div id="hsnCodeModal" class="hsn-modal">
        <div class="hsn-modal-content">
            <div class="hsn-modal-header">
                <h5 class="hsn-modal-title">
                    <i class="bi bi-upc-scan me-2"></i>Select HSN Code
                </h5>
                <button type="button" class="btn-close-modal" onclick="closeHsnModal()" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="hsn-modal-body" id="hsnModalBody">
                <!-- Search Box -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0"><i class="bi bi-search me-2"></i>Search HSN Codes</h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="hsn_modal_search" 
                                   placeholder="Search HSN code or name..." autocomplete="off">
                        </div>
                    </div>
                </div>
                
                <!-- Loading Spinner -->
                <div id="hsn_modal_loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2">Loading HSN codes...</div>
                </div>
                
                <!-- HSN Codes List -->
                <div id="hsn_modal_table_container" style="display: none;">
                    <div id="hsn_codes_list">
                        <!-- HSN codes will be populated here -->
                    </div>
                    <div id="hsn_modal_no_results" class="text-center py-4 text-muted" style="display: none;">
                        <i class="bi bi-search fs-1"></i>
                        <p class="mt-2">No HSN codes found</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="hsnModalBackdrop" class="hsn-modal-backdrop"></div>

    <!-- HSN Modal Scroll to Top Button -->
    <button id="hsnScrollToTop" type="button" title="Scroll to top" onclick="scrollHsnModalToTop()" style="display: none;">
        <i class="bi bi-arrow-up"></i>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Script loaded');
            
            // HSN Code Modal Functionality
            let hsnCodesData = [];
            const hsnModal = document.getElementById('hsnCodeModal');
            const hsnModalSearch = document.getElementById('hsn_modal_search');
            const hsnModalLoading = document.getElementById('hsn_modal_loading');
            const hsnModalTableContainer = document.getElementById('hsn_modal_table_container');
            const hsnModalNoResults = document.getElementById('hsn_modal_no_results');
            const hsnCodesList = document.getElementById('hsn_codes_list');
            const hsnCodeInput = document.getElementById('hsn_code_input');
            
            // Make HSN input clickable to open modal
            hsnCodeInput.addEventListener('click', function(e) {
                console.log('HSN input clicked');
                e.preventDefault();
                openHsnModal();
            });
            
            // Also make the search button work
            const hsnSearchBtn = document.getElementById('hsn_code_btn');
            if (hsnSearchBtn) {
                hsnSearchBtn.addEventListener('click', function(e) {
                    console.log('HSN search button clicked');
                    e.preventDefault();
                    e.stopPropagation();
                    openHsnModal();
                });
            }

            // Open HSN Modal Function
            function openHsnModal() {
                const backdrop = document.getElementById('hsnModalBackdrop');
                const mainScrollBtn = document.getElementById('scrollToTop');
                const modalScrollBtn = document.getElementById('hsnScrollToTop');

                backdrop.style.display = 'block';
                hsnModal.style.display = 'block';

                // Completely hide main scroll button with all possible methods
                if (mainScrollBtn) {
                    mainScrollBtn.style.display = 'none !important';
                    mainScrollBtn.style.opacity = '0';
                    mainScrollBtn.style.visibility = 'hidden';
                    mainScrollBtn.style.pointerEvents = 'none';
                    mainScrollBtn.classList.add('d-none');
                    mainScrollBtn.classList.remove('show');
                    console.log('Main scroll button hidden');
                }
                
                // Initialize modal scroll button
                if (modalScrollBtn) {
                    modalScrollBtn.style.display = 'flex';
                    modalScrollBtn.style.opacity = '0';
                    modalScrollBtn.style.visibility = 'hidden';
                    modalScrollBtn.style.pointerEvents = 'auto';
                    modalScrollBtn.classList.remove('show');
                    modalScrollBtn.classList.remove('d-none');
                    console.log('Modal scroll button initialized');
                }
                
                // Setup modal scroll functionality after a delay to ensure modal is rendered
                setTimeout(() => {
                    setupModalScrollToTop();
                }, 100);

                setTimeout(() => {
                    backdrop.classList.add('show');
                    hsnModal.classList.add('show');
                }, 10);

                // Load HSN codes if not loaded
                if (hsnCodesData.length === 0) {
                    loadHsnCodes();
                }
            }

            // Close HSN Modal Function
            window.closeHsnModal = function() {
                const backdrop = document.getElementById('hsnModalBackdrop');
                const mainScrollBtn = document.getElementById('scrollToTop');
                const modalScrollBtn = document.getElementById('hsnScrollToTop');

                // Completely hide modal scroll button
                if (modalScrollBtn) {
                    modalScrollBtn.style.display = 'none';
                    modalScrollBtn.style.opacity = '0';
                    modalScrollBtn.style.visibility = 'hidden';
                    modalScrollBtn.style.pointerEvents = 'none';
                    modalScrollBtn.classList.add('d-none');
                    modalScrollBtn.classList.remove('show');
                    console.log('Modal scroll button hidden');
                }
                
                // Restore main scroll button completely
                if (mainScrollBtn) {
                    mainScrollBtn.style.display = '';
                    mainScrollBtn.style.opacity = '';
                    mainScrollBtn.style.visibility = '';
                    mainScrollBtn.style.pointerEvents = '';
                    mainScrollBtn.classList.remove('d-none');
                    // Check if main page is scrolled to show the button
                    if (window.pageYOffset > 200) {
                        mainScrollBtn.classList.add('show');
                        mainScrollBtn.style.opacity = '1';
                        mainScrollBtn.style.visibility = 'visible';
                    }
                    console.log('Main scroll button restored');
                }

                // Remove show classes for slide-out animation
                hsnModal.classList.remove('show');
                backdrop.classList.remove('show');

                // Hide modal after animation
                setTimeout(() => {
                    hsnModal.style.display = 'none';
                    backdrop.style.display = 'none';
                }, 300);
            }

            // Setup Modal Scroll to Top functionality
            function setupModalScrollToTop() {
                const modalBody = document.getElementById('hsnModalBody');
                const modalScrollBtn = document.getElementById('hsnScrollToTop');

                if (!modalBody || !modalScrollBtn) {
                    console.log('Modal body or scroll button not found');
                    return;
                }

                console.log('Setting up modal scroll functionality');

                // Remove any existing scroll listeners to prevent duplicates
                modalBody.removeEventListener('scroll', handleModalScroll);
                
                // Show/hide modal scroll button based on scroll position
                function handleModalScroll() {
                    console.log('Modal scroll detected, scrollTop:', modalBody.scrollTop);
                    
                    // Ensure main scroll button stays hidden
                    const mainScrollBtn = document.getElementById('scrollToTop');
                    if (mainScrollBtn) {
                        mainScrollBtn.style.display = 'none !important';
                        mainScrollBtn.classList.add('d-none');
                    }
                    
                    if (modalBody.scrollTop > 50) {
                        modalScrollBtn.style.display = 'flex';
                        modalScrollBtn.style.opacity = '1';
                        modalScrollBtn.style.visibility = 'visible';
                        modalScrollBtn.style.pointerEvents = 'auto';
                        modalScrollBtn.classList.add('show');
                        modalScrollBtn.classList.remove('d-none');
                        console.log('Showing modal scroll button');
                    } else {
                        modalScrollBtn.classList.remove('show');
                        modalScrollBtn.style.opacity = '0';
                        modalScrollBtn.style.visibility = 'hidden';
                        setTimeout(() => {
                            if (!modalScrollBtn.classList.contains('show')) {
                                modalScrollBtn.style.display = 'flex';
                                modalScrollBtn.style.opacity = '0';
                            }
                        }, 300);
                        console.log('Hiding modal scroll button');
                    }
                }

                modalBody.addEventListener('scroll', handleModalScroll);
                
                // Initial check in case modal is already scrolled
                setTimeout(() => {
                    handleModalScroll();
                }, 500);
            }

            // Scroll HSN Modal to Top function
            window.scrollHsnModalToTop = function() {
                console.log('Scroll to top button clicked');
                const modalBody = document.getElementById('hsnModalBody');
                const modalScrollBtn = document.getElementById('hsnScrollToTop');
                
                if (modalBody) {
                    console.log('Scrolling modal to top, current scrollTop:', modalBody.scrollTop);
                    modalBody.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    
                    // Hide button immediately after clicking
                    if (modalScrollBtn) {
                        modalScrollBtn.classList.remove('show');
                        modalScrollBtn.style.opacity = '0';
                        modalScrollBtn.style.visibility = 'hidden';
                        setTimeout(() => {
                            modalScrollBtn.style.display = 'none';
                        }, 300);
                    }
                } else {
                    console.log('Modal body not found');
                }
            }
            
            // Simple test modal function
            window.testSimpleModal = function() {
                console.log('Opening simple test modal...');
                const testModal = document.getElementById('testModal');
                const modal = new bootstrap.Modal(testModal);
                modal.show();
            };
            
            // Debug function to test HSN modal
            window.testHsnModal = function() {
                console.log('Testing HSN modal...');
                const modal = new bootstrap.Modal(hsnModal);
                modal.show();
                
                // Force load data if not loaded
                if (hsnCodesData.length === 0) {
                    console.log('Loading HSN codes for test...');
                    loadHsnCodes();
                }
            };
            
            // Debugging function to detect what's blocking clicks
            window.detectBlockingElement = function(event) {
                console.log('=== CLICK DETECTION DEBUG ===');
                console.log('Event:', event);
                console.log('Mouse position:', event.clientX, event.clientY);
                
                // Get element at mouse position
                const elementAtPoint = document.elementFromPoint(event.clientX, event.clientY);
                console.log('Element at click point:', elementAtPoint);
                
                // Check all elements with high z-index
                const allElements = document.querySelectorAll('*');
                const highZIndexElements = [];
                
                allElements.forEach(el => {
                    const zIndex = window.getComputedStyle(el).zIndex;
                    if (zIndex && parseInt(zIndex) > 1000) {
                        highZIndexElements.push({
                            element: el,
                            zIndex: zIndex,
                            id: el.id,
                            class: el.className
                        });
                    }
                });
                
                console.log('High z-index elements:', highZIndexElements);
                
                // Check modal's computed styles
                const modalStyles = window.getComputedStyle(hsnModal);
                console.log('Modal z-index:', modalStyles.zIndex);
                console.log('Modal position:', modalStyles.position);
                console.log('Modal pointer-events:', modalStyles.pointerEvents);
                
                alert('Check console for detailed debug info!');
            };
            
            // Load HSN codes from server
            function loadHsnCodes() {
                hsnModalLoading.style.display = 'block';
                hsnModalTableContainer.style.display = 'none';
                
                fetch('{{ route('admin.hsn-codes.index') }}?all=1', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hsnCodesData = data;
                    renderHsnCodes(hsnCodesData);
                    hsnModalLoading.style.display = 'none';
                    hsnModalTableContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error loading HSN codes:', error);
                    hsnModalLoading.innerHTML = '<p class="text-danger">Error loading HSN codes. Please try again.</p>';
                });
            }
            
            // Render HSN codes in organized card layout (matching sales man modal pattern)
            function renderHsnCodes(codes) {
                hsnCodesList.innerHTML = '';
                
                if (codes.length === 0) {
                    hsnModalTableContainer.style.display = 'none';
                    hsnModalNoResults.style.display = 'block';
                    return;
                }
                
                hsnModalTableContainer.style.display = 'block';
                hsnModalNoResults.style.display = 'none';
                
                codes.forEach((code, index) => {
                    const card = document.createElement('div');
                    card.className = 'card border-0 shadow-sm mb-2 hsn-code-card';
                    card.style.cursor = 'pointer';
                    card.setAttribute('data-hsn-code', code.hsn_code);
                    card.setAttribute('data-index', index);
                    
                    card.innerHTML = `
                        <div class="card-body py-2 px-3">
                            <div class="row align-items-center g-2">
                                <div class="col-md-3">
                                    <div class="fw-semibold text-success">${code.name || 'Unnamed HSN Code'}</div>
                                    <small class="text-muted">HSN: ${code.hsn_code || '-'}</small>
                                </div>
                                <div class="col-md-2 text-center">
                                    <small class="text-muted d-block">CGST</small>
                                    <span class="badge bg-light text-dark">${parseFloat(code.cgst_percent || 0).toFixed(1)}%</span>
                                </div>
                                <div class="col-md-2 text-center">
                                    <small class="text-muted d-block">SGST</small>
                                    <span class="badge bg-light text-dark">${parseFloat(code.sgst_percent || 0).toFixed(1)}%</span>
                                </div>
                                <div class="col-md-2 text-center">
                                    <small class="text-muted d-block">IGST</small>
                                    <span class="badge bg-light text-dark">${parseFloat(code.igst_percent || 0).toFixed(1)}%</span>
                                </div>
                                <div class="col-md-2 text-center">
                                    <small class="text-muted d-block">Total</small>
                                    <span class="badge bg-primary">${parseFloat(code.total_gst_percent || 0).toFixed(1)}%</span>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-sm btn-success select-hsn-btn" 
                                            data-hsn-code="${code.hsn_code}" data-index="${index}" title="Select this HSN">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Add hover effect
                    card.addEventListener('mouseenter', function() {
                        this.classList.add('border-success');
                        this.style.transform = 'translateY(-1px)';
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        this.classList.remove('border-success');
                        this.style.transform = '';
                    });
                    
                    // Add click event to select button
                    const selectBtn = card.querySelector('.select-hsn-btn');
                    selectBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectHsnCode(code);
                    });
                    
                    // Also make card clickable
                    card.addEventListener('click', function(e) {
                        if (!e.target.closest('.select-hsn-btn')) {
                            e.preventDefault();
                            e.stopPropagation();
                            selectHsnCode(code);
                        }
                    });
                    
                    hsnCodesList.appendChild(card);
                });
            }
            
            // Select HSN code and populate fields
            function selectHsnCode(code) {
                console.log('Selecting HSN code:', code);
                
                // Populate form fields
                document.getElementById('hsn_code_input').value = code.hsn_code || '';
                document.getElementById('cgst_percent').value = parseFloat(code.cgst_percent || 0).toFixed(2);
                document.getElementById('sgst_percent').value = parseFloat(code.sgst_percent || 0).toFixed(2);
                document.getElementById('igst_percent').value = parseFloat(code.igst_percent || 0).toFixed(2);
                
                // Close modal using our custom function
                closeHsnModal();
                
                // Show success feedback
                const hsnInput = document.getElementById('hsn_code_input');
                hsnInput.classList.add('border-success');
                setTimeout(() => {
                    hsnInput.classList.remove('border-success');
                }, 1500);
                
                console.log('HSN code selected successfully');
            }
            
            // Search functionality
            hsnModalSearch.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                if (searchTerm === '') {
                    renderHsnCodes(hsnCodesData);
                    return;
                }
                
                const filtered = hsnCodesData.filter(code => {
                    return (code.name && code.name.toLowerCase().includes(searchTerm)) ||
                           (code.hsn_code && code.hsn_code.toLowerCase().includes(searchTerm));
                });
                
                renderHsnCodes(filtered);
            });
            
            // Wait for Select2 to initialize
            setTimeout(function() {
                const companySelect = $('#company_id'); // Use jQuery for Select2
                const shortNameInput = document.getElementById('company_short_name');

                console.log('Company Select:', companySelect);
                console.log('Short Name Input:', shortNameInput);

                if (companySelect.length > 0) {
                    // Use Select2's change event
                    companySelect.on('change', function () {
                        console.log('Company changed');
                        const selectedOption = $(this).find('option:selected');
                        const shortName = selectedOption.data('short-name');
                        console.log('Selected Option:', selectedOption);
                        console.log('Short Name from data attribute:', shortName);
                        
                        if (shortNameInput) {
                            shortNameInput.value = shortName || '';
                            console.log('Short name set to:', shortNameInput.value);
                        } else {
                            console.error('Short name input not found!');
                        }
                    });

                    // Trigger on page load if pre-selected
                    if (companySelect.val()) {
                        console.log('Triggering change on page load');
                        companySelect.trigger('change');
                    }
                } else {
                    console.error('Company select not found!');
                }
            }, 500); // Wait 500ms for Select2 to initialize

            // Close modal when clicking backdrop
            document.addEventListener('click', function (e) {
                if (e.target && e.target.id === 'hsnModalBackdrop') {
                    closeHsnModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('hsnCodeModal');
                    if (modal && modal.classList.contains('show')) {
                        closeHsnModal();
                    }
                }
            });
        });
    </script>

    <style>
        /* Ensure header and sidebar stay above modal backdrop */
        .navbar, .sidebar {
            z-index: 999999 !important;
            position: relative !important;
        }

        /* Prevent modal backdrop from affecting header */
        .navbar {
            background: white !important;
        }

        /* Hide main scroll button when modal is open */
        .hsn-modal.show ~ #scrollToTop,
        .hsn-modal.show + #scrollToTop {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        /* Force hide main scroll button with class */
        #scrollToTop.d-none {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        /* HSN Modal Styles - Matching Sales Man Modal */
        .hsn-modal {
            display: none;
            position: fixed;
            top: 70px;
            right: 0;
            width: 800px;
            height: calc(100vh - 100px);
            max-height: calc(100vh - 140px);
            z-index: 999999 !important;
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .hsn-modal.show {
            transform: translateX(0);
        }

        .hsn-modal-content {
            background: white;
            height: 100%;
            box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
        }

        .hsn-modal-header {
            padding: 1rem 1.25rem;
            border-bottom: 2px solid #dee2e6;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .hsn-modal-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #ffffff;
        }

        /* Close Button in Header */
        .btn-close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .btn-close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .hsn-modal-body {
            padding: 0.75rem;
            overflow-y: auto;
            flex: 1;
            background: #f8f9fa;
        }

        .hsn-modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 999998 !important;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .hsn-modal-backdrop.show {
            opacity: 0.7;
        }

        /* HSN Code Cards */
        .hsn-code-card {
            transition: all 0.2s ease;
            border: 1px solid #e9ecef !important;
        }

        .hsn-code-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .hsn-code-card.border-success {
            border-color: #198754 !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hsn-modal {
                width: 100%;
            }
            
            .hsn-modal-backdrop {
                left: 0; /* Full width on mobile */
                width: 100vw;
            }
        }

        @media (max-width: 576px) {
            .hsn-modal-body {
                padding: 0.75rem;
            }

            .hsn-modal-header {
                padding: 0.75rem 1rem;
            }
        }

        /* Card styling in modal - Compact for better UX */
        .hsn-modal .card {
            margin-bottom: 0.5rem;
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .hsn-modal .card:last-child {
            margin-bottom: 0;
        }

        .hsn-modal .card-header {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            font-weight: 600;
        }

        .hsn-modal .card-header h6 {
            font-size: 0.85rem;
            margin: 0;
        }

        .hsn-modal .card-header .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .hsn-modal .card-body {
            padding: 0.75rem;
            background: white;
        }

        .hsn-modal .card-body small {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 600;
        }

        .hsn-modal .fw-semibold {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
            color: #2c3e50;
        }

        .hsn-modal .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.4rem;
        }

        /* Smooth scrollbar for modal */
        .hsn-modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .hsn-modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .hsn-modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .hsn-modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* HSN Modal Scroll to Top Button */
        #hsnScrollToTop {
            position: fixed !important;
            bottom: 30px !important;
            right: 30px !important;
            z-index: 99999999 !important;
            border-radius: 50% !important;
            width: 50px !important;
            height: 50px !important;
            background: #198754 !important;
            color: #fff !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            padding: 0 !important;
            pointer-events: auto !important;
        }

        #hsnScrollToTop:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2) !important;
            background: #157347 !important;
        }

        #hsnScrollToTop:active {
            transform: translateY(-1px) !important;
        }

        #hsnScrollToTop i {
            font-size: 22px !important;
        }

        #hsnScrollToTop.show {
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Ensure modal scroll button appears above everything */
        .hsn-modal.show ~ #hsnScrollToTop {
            z-index: 99999999 !important;
        }
    </style>
@endsection