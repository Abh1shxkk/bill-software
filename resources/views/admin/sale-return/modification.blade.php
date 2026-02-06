@extends('layouts.admin')

@section('title', 'Sale Return Modification')

@section('content')
<style>
    /* Compact form adjustments */
    .compact-form {
        font-size: 11px;
        padding: 8px;
        background: #f5f5f5;
    }
    
    .compact-form label {
        font-weight: 600;
        font-size: 11px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .compact-form input,
    .compact-form select {
        font-size: 11px;
        padding: 2px 6px;
        height: 26px;
    }
    
    .header-section {
        background: white;
        border: 1px solid #dee2e6;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 4px;
    }
    
    .header-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 6px;
    }
    
    .field-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .field-group label {
        font-weight: 600;
        font-size: 11px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .field-group input,
    .field-group select {
        font-size: 11px;
        padding: 2px 6px;
        height: 26px;
    }
    
    .inner-card-sr {
        background: #e8f4f8;
        border: 1px solid #b8d4e0;
        padding: 8px;
        border-radius: 3px;
    }
    
    .table-compact {
        font-size: 10px;
        margin-bottom: 0;
    }
    
    .table-compact th,
    .table-compact td {
        padding: 4px;
        vertical-align: middle;
        height: 45px;
    }
    
    .table-compact th {
        background: #e9ecef;
        font-weight: 600;
        text-align: center;
        border: 1px solid #dee2e6;
        height: 40px;
    }
    
    .table-compact input {
        font-size: 10px;
        padding: 2px 4px;
        height: 22px;
        border: 1px solid #ced4da;
        width: 100%;
    }
    
    .readonly-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }

    /* Custom searchable dropdown (customer) */
    .custom-dropdown-menu .dropdown-item:hover {
        background-color: #f1f5ff;
    }
    
    .custom-dropdown-menu .dropdown-item:active {
        background-color: #e3ebff;
    }

    .custom-dropdown-menu .dropdown-item.active {
        background-color: #e3ebff;
    }

    /* Keyboard-selected rows in modals */
.kb-row-active,
.kb-row-active td {
    background-color: #cfe2ff !important;
}

.credit-note-options button.kb-active,
.credit-note-options button:focus,
.credit-note-options button:focus-visible {
    outline: 2px solid #0d6efd !important;
    outline-offset: 2px !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
}

.adjustment-input.kb-active,
.adjustment-input:focus,
.adjustment-input:focus-visible {
    outline: 2px solid #0d6efd !important;
    outline-offset: 2px !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil-square me-2"></i> Sale Return Modification</h4>
        <div class="text-muted small">Modify existing sale return transaction</div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form id="saleReturnTransactionForm" method="POST" autocomplete="off" onsubmit="return false;">
            @csrf
            
            <!-- Header Section -->
            <div class="header-section">
                <!-- Row 1: SR No, Date -->
                <div class="header-row">
                    <div class="field-group">
                        <label>SR.:</label>
                        <select class="form-control no-select2" name="series" id="seriesSelect" style="width: 60px;" onchange="updateSeriesLabel()">
                            <option value="SR" selected>SR</option>
                        </select>
                        <span id="seriesLabel" style="font-weight: bold; color: #0d6efd; margin-left: 10px;">SALES RETURN - CREDIT</span>
                    </div>
                    
                    <div class="field-group">
                        <label>Date:</label>
                        <input type="date" class="form-control" name="return_date" id="returnDate" value="{{ date('Y-m-d') }}" style="width: 140px;" onchange="updateDayName()">
                        <input type="text" class="form-control readonly-field" id="dayName" value="{{ date('l') }}" readonly style="width: 90px;">
                    </div>
                </div>
                
                <!-- Row 2: Inner Card and Right Side -->
                <div class="d-flex gap-3">
                    <!-- Left Side - Inner Card -->
                    <div class="inner-card-sr flex-grow-1">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="field-group" style="position: relative;">
                                    <label style="width: 100px;">Name:</label>
                                    <div class="custom-dropdown-wrapper" style="width: 100%; position: relative;">
                                        <input type="text" 
                                               class="form-control no-select2" 
                                               id="customerSearchInput" 
                                               placeholder="Type to search customer..."
                                               autocomplete="off"
                                               style="width: 100%;">
                                        <select class="form-control no-select2" name="customer_id" id="customerSelect" autocomplete="off" style="display: none;">
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->code ?? '' }} - {{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        
                                        <div id="customerDropdown" class="custom-dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; max-height: 300px; overflow-y: auto; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000;">
                                            <div class="dropdown-header" style="padding: 8px 12px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; font-weight: 600; font-size: 13px;">
                                                Select Customer
                                            </div>
                                            <div id="customerList" class="dropdown-list">
                                                @foreach($customers as $customer)
                                                    <div class="dropdown-item" 
                                                         data-id="{{ $customer->id }}" 
                                                         data-name="{{ $customer->name }}"
                                                         data-code="{{ $customer->code ?? '' }}"
                                                         style="padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f0f0f0;">
                                                        {{ $customer->code ?? '' }} - {{ $customer->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>Rate Diff.:</label>
                                    <input type="text" class="form-control" name="rate_diff_flag" id="rateDiff" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>Cash:</label>
                                    <input type="text" class="form-control" name="cash_flag" id="cash" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>Tax:</label>
                                    <input type="text" class="form-control" name="tax_flag" id="tax" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mt-1">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label style="width: 100px;">Sales Man:</label>
                                    <select class="form-control no-select2" name="salesman_id" id="salesmanSelect" autocomplete="off" onchange="updateSalesmanName()">
                                        <option value="">Select</option>
                                        @foreach($salesmen as $salesman)
                                            <option value="{{ $salesman->id }}" data-name="{{ $salesman->name }}">{{ $salesman->code ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 80px;">Inv.No.:</label>
                                    <input type="text" class="form-control" name="original_invoice_no" id="originalInvoiceNo" onkeypress="handleInvoiceSearch(event)">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 80px;">Date:</label>
                                    <input type="date" class="form-control readonly-field" name="original_invoice_date" id="originalInvoiceDate" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mt-1">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label style="width: 100px;">Remarks:</label>
                                    <input type="text" class="form-control" name="remarks" id="remarks">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 80px;">Series:</label>
                                    <input type="text" class="form-control readonly-field" id="originalSeries" name="original_series" value="" readonly style="width: 50px;">
                                </div>
                            </div>
                            
                            <div class="col-md-3" id="originalAmountContainer" style="display: none;">
                                <div class="field-group">
                                    <label style="width: 80px;">Amount:</label>
                                    <input type="text" class="form-control readonly-field" id="originalAmount" name="original_amount" value="" readonly style="width: 120px; text-align: right; font-weight: bold;">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side -->
                    <div style="width: 200px;">
                        <div class="field-group mb-2">
                            <label style="width: 150px;">S.R. No.:</label>
                            <input type="text" class="form-control" name="sr_no" id="srNo" value="" placeholder="Enter SR No">
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 150px;">Fixed Dis.:</label>
                            <input type="number" class="form-control" name="fixed_discount" id="fixedDiscount" value="0" step="0.01">
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-info" id="insertOrdersBtn" style="width: 100%;" onclick="openInsertOrdersModal()">
                                <i class="bi bi-list-check"></i> Insert Orders
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto; max-height: 310px;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                            <tr>
                                <th style="width: 60px;">Code</th>
                                <th style="width: 250px;">Item Name</th>
                                <th style="width: 80px;">Batch</th>
                                <th style="width: 70px;">Exp.</th>
                                <th style="width: 60px;">Qty.</th>
                                <th style="width: 60px;">F.Qty</th>
                                <th style="width: 80px;">Sale Rate</th>
                                <th style="width: 60px;">Dis.%</th>
                                <th style="width: 80px;">MRP</th>
                                <th style="width: 90px;">Amount</th>
                                <th style="width: 60px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Items will be added dynamically -->
                        </tbody>
                    </table>
                </div>
                <!-- Add Row Button -->
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                        <i class="fas fa-plus-circle"></i> Add Row
                    </button>
                </div>
            </div>

            
            <!-- Calculation Section -->
            <div class="bg-white border rounded p-3 mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="d-flex align-items-start gap-3 border rounded p-2" style="font-size: 11px; background: #fafafa;">
                    <!-- Left Section: HSN Code, CGST -->
                    <div class="d-flex flex-column gap-2">
                        <!-- HSN Code -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>HSN Code:</strong></label>
                            <input type="text" class="form-control readonly-field text-center" id="calc_hsn_code" readonly style="width: 100px; height: 28px;" value="---">
                        </div>
                        
                        <!-- CGST(%) -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>CGST(%):</strong></label>
                            <input type="text" class="form-control readonly-field text-center" id="calc_cgst_percent" readonly style="width: 50px; height: 28px;" value="0">
                            <input type="text" class="form-control readonly-field text-end" id="calc_cgst_amount" readonly style="width: 70px; height: 28px;" value="0.00">
                        </div>
                    </div>
                    
                    <!-- Middle Section: SGST, Cess -->
                    <div class="d-flex flex-column gap-2">
                        <!-- SGST(%) -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>SGST(%):</strong></label>
                            <input type="text" class="form-control readonly-field text-center" id="calc_sgst_percent" readonly style="width: 50px; height: 28px;" value="0">
                            <input type="text" class="form-control readonly-field text-end" id="calc_sgst_amount" readonly style="width: 70px; height: 28px;" value="0.00">
                        </div>
                        
                        <!-- Cess (%) -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>Cess(%):</strong></label>
                            <input type="text" class="form-control readonly-field text-center" id="calc_cess_percent" readonly style="width: 50px; height: 28px;" value="0">
                            <input type="text" class="form-control readonly-field text-end" id="calc_cess_amount" readonly style="width: 70px; height: 28px;" value="0.00">
                        </div>
                    </div>
                    
                    <!-- Right Side: SC%, TAX%, Excise, TSR -->
                    <div class="d-flex gap-3">
                        <!-- Column 1: SC % -->
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 65px;"><strong>SC %</strong></label>
                                <input type="number" class="form-control readonly-field" id="calc_sc_percent" readonly style="width: 80px; height: 28px;" value="0.000">
                            </div>
                        </div>
                        
                        <!-- Column 2: TAX % -->
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>TAX %</strong></label>
                                <input type="number" class="form-control readonly-field" id="calc_tax_percent" readonly style="width: 70px; height: 28px;" value="0.000">
                            </div>
                        </div>
                        
                        <!-- Column 3: Excise -->
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>Excise</strong></label>
                                <input type="text" class="form-control text-center readonly-field" id="calc_excise" readonly style="width: 60px; height: 28px;" value="0.00">
                            </div>
                        </div>
                        
                        <!-- Column 4: TSR -->
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>TSR</strong></label>
                                <input type="text" class="form-control text-center readonly-field" id="calc_tsr" readonly style="width: 60px; height: 28px;" value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Section -->
            <div class="bg-white border rounded p-2 mb-2">
                <!-- Row 1: 6 fields -->
                <div class="d-flex align-items-center" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold; white-space: nowrap;">N.T AMT</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="nt_amount" id="ntAmount" readonly step="0.01" style="width: 80px; height: 26px; background: #fff3cd;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">SC</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="sc_amount" id="scAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">F.T. Amt.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="ft_amount" id="ftAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Dis.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="dis_amount" id="disAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="scm_amount" id="scmAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Tax</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="tax_amount" id="taxAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>

                <!-- Row 2: 3 fields -->
                <div class="d-flex align-items-center mt-2" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold; white-space: nowrap;">Net</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="net_amount" id="netAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.%</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="scm_percent" id="scmPercent" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">TCS</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="tcs_amount" id="tcsAmount" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                    </div>
                </div>
            </div>

            <!-- Additional Fields Section -->
            <div class="col-12 mb-4 bg-white border rounded p-2 mb-2">
                <div class="row gx-3" style="font-size: 11px;">
                    <!-- col 1 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 70px;">Packing</label>
                                    <input type="text" class="form-control form-control-sm readonly-field text-start" name="packing" id="packing" readonly style="width: 80px; height: 26px;" value="">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 70px;">Unit</label>
                                    <input type="text" class="form-control form-control-sm readonly-field text-start" name="unit" id="unit" readonly style="width: 80px; height: 26px;" value="">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 70px;">Cl. Qty</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" name="cl_qty" id="clQty" readonly step="0.01" style="width: 80px; height: 26px;" value="0">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 70px;">Lctn :</label>
                                    <input type="text" class="form-control form-control-sm readonly-field text-start" name="location" id="location" readonly style="width: 80px; height: 26px;" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col 2 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">N.T.Amt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_nt_amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">SC Amt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_sc_amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">Dis.Amt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_dis_amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">HS.Amt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" name="hs_amount" id="hsAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col 3 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">Scm. %</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_scm_percent" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">Scm.Amt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_scm_amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">Tax Amt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_tax_amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">Net Amt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_net_amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col 4 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">Sub.Tot.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_sub_total" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 80px;">Vol.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_volume" readonly step="0.01" style="width: 80px; height: 26px;" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col 5 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 60px;">Comp :</label>
                                    <input type="text" class="form-control form-control-sm readonly-field text-start" id="addl_company" readonly style="width: 100px; height: 26px;" value="">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 60px;">SCM.</label>
                                    <input type="text" class="form-control form-control-sm readonly-field text-center" id="addl_scm_flag" readonly style="width: 40px; height: 26px;" value="0">
                                    <span style="margin: 0 5px;">+</span>
                                    <input type="text" class="form-control form-control-sm readonly-field text-center" id="addl_scm_value" readonly style="width: 40px; height: 26px;" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col 6 -->
                    <div class="col-lg-1">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0" style="font-weight: bold; width: 50px;">Srino</label>
                                    <input type="text" class="form-control form-control-sm readonly-field text-center" id="addl_srino" readonly style="width: 40px; height: 26px;" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="submitBtn" onclick="saveTransaction()">
                    <i class="bi bi-check-circle me-1"></i> Save Sale Return
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Credit Note Modal -->
<div id="creditNoteModal" class="credit-note-modal">
    <div class="credit-note-modal-content">
        <div class="credit-note-modal-header">
            <h5>Save Transaction</h5>
            <button type="button" class="credit-note-close-btn" onclick="closeCreditNoteModal()">&times;</button>
        </div>
        <div class="credit-note-modal-body">
            <p>How would you like to save this transaction?</p>
            <div class="credit-note-options">
                <button type="button" class="btn btn-secondary" onclick="saveWithoutCreditNote()">
                    Save Without Credit Note
                </button>
                <button type="button" class="btn btn-primary" onclick="saveWithCreditNote()">
                    Save With Credit Note
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Update day name when date changes
function updateDayName() {
    const dateInput = document.getElementById('returnDate');
    const dayNameInput = document.getElementById('dayName');
    
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayNameInput.value = days[date.getDay()];
    }
}

// Update customer name (custom dropdown)
function updateCustomerName() {
    const customerSelect = document.getElementById('customerSelect');
    const customerSearchInput = document.getElementById('customerSearchInput');
    const customerList = document.getElementById('customerList');
    if (!customerSelect || !customerList) return;

    const item = customerList.querySelector(`.dropdown-item[data-id="${customerSelect.value}"]`);
    if (item) {
        const name = item.getAttribute('data-name') || '';
        const code = item.getAttribute('data-code') || '';
        customerSelect.setAttribute('data-customer-name', name);
        if (customerSearchInput) {
            customerSearchInput.value = code ? `${code} - ${name}` : name;
        }
        return;
    }

    // Fallback: use selected option text if dropdown list doesn't include item
    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
    if (selectedOption && customerSearchInput) {
        customerSearchInput.value = selectedOption.textContent || '';
    }
}

// Customer dropdown functionality (searchable)
function initCustomerDropdown() {
    const customerSearchInput = document.getElementById('customerSearchInput');
    const customerDropdown = document.getElementById('customerDropdown');
    const customerSelect = document.getElementById('customerSelect');
    const customerList = document.getElementById('customerList');
    if (!customerSearchInput || !customerDropdown || !customerSelect || !customerList) return;
    if (customerSearchInput.dataset.kbInit === 'true') return;
    customerSearchInput.dataset.kbInit = 'true';

    let customerActiveIndex = -1;

    function syncCustomerListFromSelect() {
        const existing = customerList.querySelectorAll('.dropdown-item[data-id]').length;
        if (existing > 0) return;
        const options = Array.from(customerSelect.options || []);
        options.forEach(opt => {
            if (!opt.value) return;
            const item = document.createElement('div');
            item.className = 'dropdown-item';
            item.setAttribute('data-id', opt.value);
            item.setAttribute('data-name', opt.getAttribute('data-name') || opt.textContent || '');
            item.setAttribute('data-code', '');
            item.style.padding = '8px 12px';
            item.style.cursor = 'pointer';
            item.style.fontSize = '13px';
            item.style.borderBottom = '1px solid #f0f0f0';
            item.textContent = opt.textContent || '';
            customerList.appendChild(item);
        });
    }

    syncCustomerListFromSelect();

    function loadCustomersFromApi() {
        if (window.kbCustomersLoaded) return;
        window.kbCustomersLoaded = true;
        fetch('{{ route("admin.customers.all") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success || !Array.isArray(data.customers)) return;

            // Clear existing select options (keep placeholder)
            const placeholder = customerSelect.querySelector('option[value=""]');
            customerSelect.innerHTML = '';
            if (placeholder) customerSelect.appendChild(placeholder);

            // Clear list
            customerList.innerHTML = '';

            data.customers.forEach(cust => {
                const option = document.createElement('option');
                option.value = cust.customer_id;
                option.textContent = `${cust.code || ''} - ${cust.name}`;
                option.setAttribute('data-name', cust.name || '');
                customerSelect.appendChild(option);

                const item = document.createElement('div');
                item.className = 'dropdown-item';
                item.setAttribute('data-id', cust.customer_id);
                item.setAttribute('data-name', cust.name || '');
                item.setAttribute('data-code', cust.code || '');
                item.style.padding = '8px 12px';
                item.style.cursor = 'pointer';
                item.style.fontSize = '13px';
                item.style.borderBottom = '1px solid #f0f0f0';
                item.textContent = `${cust.code || ''} - ${cust.name}`;
                customerList.appendChild(item);
            });

            if (!customerList.children.length) {
                const empty = document.createElement('div');
                empty.className = 'dropdown-item';
                empty.textContent = 'No customers found';
                empty.style.padding = '8px 12px';
                empty.style.fontSize = '13px';
                empty.style.color = '#888';
                customerList.appendChild(empty);
            }
        })
        .catch(err => {
            console.error('Failed to load customers:', err);
        });
    }

    // If no customers were rendered by Blade, fetch from API
    if (customerList.querySelectorAll('.dropdown-item[data-id]').length === 0) {
        loadCustomersFromApi();
    }

    function isSelectableCustomerItem(item) {
        return !!(item && item.getAttribute('data-id'));
    }

    function getVisibleCustomerItems() {
        return Array.from(customerList.querySelectorAll('.dropdown-item:not([style*="display: none"])'))
            .filter(isSelectableCustomerItem);
    }

    function setActiveCustomerItem(index) {
        const items = getVisibleCustomerItems();
        items.forEach(item => item.classList.remove('active'));
        if (items[index]) {
            items[index].classList.add('active');
            items[index].scrollIntoView({ block: 'nearest' });
            customerActiveIndex = index;
        }
    }

    function filterCustomers(query) {
        const items = customerList.querySelectorAll('.dropdown-item');
        const q = (query || '').toLowerCase();
        let visibleIndex = 0;
        customerActiveIndex = -1;

        items.forEach(item => {
            const text = (item.textContent || '').toLowerCase();
            if (!q || text.includes(q)) {
                item.style.display = 'block';
                if (customerActiveIndex === -1) {
                    customerActiveIndex = visibleIndex;
                }
                visibleIndex++;
            } else {
                item.style.display = 'none';
            }
        });
    }

    function selectCustomerItem(item, moveNext = false) {
        if (!item) return false;
        const id = item.getAttribute('data-id') || '';
        customerSelect.value = id;
        customerSelect.dispatchEvent(new Event('change', { bubbles: true }));
        updateCustomerName();
        customerDropdown.style.display = 'none';
        if (moveNext) {
            const rateDiff = document.getElementById('rateDiff');
            if (rateDiff) rateDiff.focus();
        }
        return true;
    }

    customerSearchInput.addEventListener('focus', function() {
        customerDropdown.style.display = 'block';
        // Show all customers when dropdown opens - don't filter by current value
        // This prevents empty dropdown when input has previous selection text
        filterCustomers('');
    });

    customerSearchInput.addEventListener('input', function() {
        filterCustomers(this.value);
        customerDropdown.style.display = 'block';
    });

    customerList.addEventListener('click', function(e) {
        const item = e.target.closest('.dropdown-item');
        if (!item) return;
        selectCustomerItem(item, false);
    });

    document.addEventListener('click', function(e) {
        if (!customerSearchInput.contains(e.target) && !customerDropdown.contains(e.target)) {
            customerDropdown.style.display = 'none';
        }
    });

    customerSearchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const visibleItems = getVisibleCustomerItems();
            let selected = false;
            if (customerActiveIndex >= 0 && visibleItems[customerActiveIndex]) {
                selected = selectCustomerItem(visibleItems[customerActiveIndex], true);
            } else if (visibleItems.length >= 1) {
                selected = selectCustomerItem(visibleItems[0], true);
            }
            if (!selected) {
                customerDropdown.style.display = 'block';
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const items = getVisibleCustomerItems();
            if (!items.length) return;
            if (customerDropdown.style.display !== 'block') {
                customerDropdown.style.display = 'block';
            }
            const nextIndex = customerActiveIndex < 0 ? 0 : Math.min(customerActiveIndex + 1, items.length - 1);
            setActiveCustomerItem(nextIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const items = getVisibleCustomerItems();
            if (!items.length) return;
            if (customerDropdown.style.display !== 'block') {
                customerDropdown.style.display = 'block';
            }
            const prevIndex = customerActiveIndex <= 0 ? 0 : customerActiveIndex - 1;
            setActiveCustomerItem(prevIndex);
        } else if (e.key === 'Escape') {
            customerDropdown.style.display = 'none';
        }
    }, true);

    // Global capture to ensure dropdown selection works even if focus shifts
    window.addEventListener('keydown', function(e) {
        const activeEl = document.activeElement;
        const isCustomerFocus = activeEl === customerSearchInput || customerDropdown.contains(activeEl);
        const isDropdownOpen = customerDropdown.style.display === 'block';
        if (!isCustomerFocus || !isDropdownOpen) return;

        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const visibleItems = getVisibleCustomerItems();
            let selected = false;
            if (customerActiveIndex >= 0 && visibleItems[customerActiveIndex]) {
                selected = selectCustomerItem(visibleItems[customerActiveIndex], true);
            } else if (visibleItems.length >= 1) {
                selected = selectCustomerItem(visibleItems[0], true);
            }
            if (!selected) {
                customerDropdown.style.display = 'block';
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const items = getVisibleCustomerItems();
            if (!items.length) return;
            const nextIndex = customerActiveIndex < 0 ? 0 : Math.min(customerActiveIndex + 1, items.length - 1);
            setActiveCustomerItem(nextIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const items = getVisibleCustomerItems();
            if (!items.length) return;
            const prevIndex = customerActiveIndex <= 0 ? 0 : customerActiveIndex - 1;
            setActiveCustomerItem(prevIndex);
        } else if (e.key === 'Escape') {
            customerDropdown.style.display = 'none';
        }
    }, true);
}

// Update salesman name
function updateSalesmanName() {
    const select = document.getElementById('salesmanSelect');
    const selectedOption = select.options[select.selectedIndex];
    // Store salesman name for later use
    if (selectedOption) {
        select.setAttribute('data-salesman-name', selectedOption.getAttribute('data-name') || '');
    }
}

// Track pending row for item selection
if (typeof window.pendingReturnRowIndex === 'undefined') {
    window.pendingReturnRowIndex = null;
}

// Open item modal for a specific row (code field Enter)
function openItemModalForRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    const codeInput = row.querySelector('input[name*="[code]"]');
    if (!codeInput) return;
    const isEditableRow = row.dataset.editable === 'true' || !codeInput.hasAttribute('readonly');
    if (!isEditableRow) return;

    window.pendingReturnRowIndex = rowIndex;
    if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    } else {
        console.error('Item selection modal not initialized');
        showAlert('error', 'Could not open item selection modal');
    }
}

// Capture Enter on Code field to always open item modal
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter' && e.keyCode !== 13) return;
    const target = e.target;
    if (!target || target.tagName !== 'INPUT') return;
    const name = target.getAttribute('name') || '';
    if (!name.includes('[code]')) return;
    const row = target.closest('tr');
    const rowId = row?.id || '';
    const rowIndex = rowId.startsWith('row-') ? parseInt(rowId.replace('row-', '')) : NaN;
    if (Number.isNaN(rowIndex)) return;
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    openItemModalForRow(rowIndex);
}, true);

// Add new row to items table (empty editable row)
function addNewRow() {
    // Prevent duplicate empty rows
    const lastRow = document.querySelector('#itemsTableBody tr:last-child');
    if (lastRow) {
        const codeInput = lastRow.querySelector('input[name*="[code]"]');
        const nameInput = lastRow.querySelector('input[name*="[name]"]');
        if (codeInput && !codeInput.value && nameInput && !nameInput.value) {
            codeInput.focus();
            if (codeInput.select) codeInput.select();
            return;
        }
    }

    const emptyItem = {
        item_id: '',
        item_code: '',
        item_name: '',
        batch_id: '',
        batch_no: '',
        expiry_date: '',
        packing: '',
        unit: '',
        company_name: '',
        hsn_code: '',
        sale_rate: 0,
        mrp: 0,
        discount_percent: 0,
        cgst_percent: 0,
        sgst_percent: 0,
        cess_percent: 0,
        return_qty: 0,
        return_fqty: 0,
        _editable: true
    };

    const newRowIndex = currentRowIndex;
    addItemRow(emptyItem, newRowIndex);

    setTimeout(() => {
        const row = document.getElementById(`row-${newRowIndex}`) || document.querySelector('#itemsTableBody tr:last-child');
        if (!row) return;
        const codeInput = row.querySelector('input[name*="[code]"]');
        if (codeInput) {
            codeInput.focus();
            if (codeInput.select) codeInput.select();
        }
    }, 100);
}

// Callback function when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('Item selected from modal:', item);
    console.log('Batch selected from modal:', batch);

    // If we are populating an existing row (code field flow)
    if (window.pendingReturnRowIndex !== null && window.pendingReturnRowIndex !== undefined) {
        const rowIndex = window.pendingReturnRowIndex;
        window.pendingReturnRowIndex = null;
        populateExistingRowWithItemBatch(rowIndex, item, batch);
        return;
    }

    // Create item object for the table
    const newItem = {
        item_id: item.id,
        item_code: item.bar_code || item.id,
        item_name: item.name,
        batch_id: batch.id,
        batch_no: batch.batch_no,
        expiry_date: batch.expiry_display || batch.expiry_date || '',
        packing: item.packing || '',
        unit: item.unit || 'PCS',
        company_name: item.company_name || '',
        hsn_code: item.hsn_code || '',
        sale_rate: parseFloat(batch.s_rate || batch.avg_s_rate || 0),
        mrp: parseFloat(batch.mrp || batch.avg_mrp || 0),
        discount_percent: 0,
        cgst_percent: parseFloat(item.cgst_percent || 6),
        sgst_percent: parseFloat(item.sgst_percent || 6),
        cess_percent: parseFloat(item.cess_percent || 0),
        return_qty: 0,
        return_fqty: 0
    };
    
    // Add to table using existing function
    populateItemsTable([newItem]);
    showAlert('success', 'Item added successfully! Enter return quantity.');
};

// Populate an existing row with item + batch (code field flow)
function populateExistingRowWithItemBatch(rowIndex, item, batch) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;

    const newItem = {
        item_id: item.id,
        item_code: item.bar_code || item.id,
        item_name: item.name,
        batch_id: batch.id,
        batch_no: batch.batch_no,
        expiry_date: batch.expiry_display || batch.expiry_date || '',
        packing: item.packing || '',
        unit: item.unit || 'PCS',
        company_name: item.company_name || '',
        hsn_code: item.hsn_code || '',
        sale_rate: parseFloat(batch.s_rate || batch.avg_s_rate || 0),
        mrp: parseFloat(batch.mrp || batch.avg_mrp || 0),
        discount_percent: 0,
        cgst_percent: parseFloat(item.cgst_percent || 6),
        sgst_percent: parseFloat(item.sgst_percent || 6),
        cess_percent: parseFloat(item.cess_percent || 0),
        return_qty: 0,
        return_fqty: 0
    };

    row.querySelector('input[name*="[code]"]').value = newItem.item_code || '';
    row.querySelector('input[name*="[name]"]').value = newItem.item_name || '';
    row.querySelector('input[name*="[batch]"]').value = newItem.batch_no || '';
    row.querySelector('input[name*="[expiry]"]').value = newItem.expiry_date || '';
    row.querySelector('input[name*="[sale_rate]"]').value = newItem.sale_rate || 0;
    row.querySelector('input[name*="[mrp]"]').value = newItem.mrp || 0;

    row.querySelector('input[name*="[item_id]"]').value = newItem.item_id || '';
    row.querySelector('input[name*="[batch_id]"]').value = newItem.batch_id || '';
    row.querySelector('input[name*="[hsn_code]"]').value = newItem.hsn_code || '';
    row.querySelector('input[name*="[company_name]"]').value = newItem.company_name || '';
    row.querySelector('input[name*="[packing]"]').value = newItem.packing || '';
    row.querySelector('input[name*="[unit]"]').value = newItem.unit || '';
    row.querySelector('input[name*="[cgst_percent]"]').value = newItem.cgst_percent || 0;
    row.querySelector('input[name*="[sgst_percent]"]').value = newItem.sgst_percent || 0;
    row.querySelector('input[name*="[cess_percent]"]').value = newItem.cess_percent || 0;

    row.dataset.itemData = JSON.stringify(newItem);
    row.dataset.completed = 'false';
    row.dataset.editable = 'false';

    calculateRowAmount(rowIndex);
    selectRowForCalculation(rowIndex);

    const qtyInput = row.querySelector('input[name*="[qty]"]');
    if (qtyInput) {
        qtyInput.focus();
        qtyInput.select();
    }
}

// Populate items table with items (ADD new items, don't clear existing)
function populateItemsTable(items) {
    console.log('populateItemsTable called with items:', items);
    if (!items || items.length === 0) {
        console.log('No items to populate');
        return;
    }

    // Filter out empty placeholder items (prevents extra blank row)
    items = items.filter(it => it && (it.item_id || it.item_code || it.item_name));
    
    console.log(`Adding ${items.length} items to table. Current row index: ${currentRowIndex}`);
    
    // Add new items to existing table (don't clear existing rows)
    // Note: currentRowIndex will continue from existing rows
    items.forEach((item, index) => {
        console.log(`Adding item ${index}:`, item);
        addItemRow(item, index);
    });
    
    // Calculate summary after populating
    recalculateTotals();
    
    // Focus on the newly added row's quantity field
    setTimeout(() => {
        const allRows = document.querySelectorAll('#itemsTableBody tr');
        const lastRow = allRows[allRows.length - 1]; // Get the last (newly added) row
        
        if (lastRow) {
            const qtyInput = lastRow.querySelector('input[name*="[qty]"]');
            if (qtyInput && !qtyInput.readOnly && !qtyInput.disabled) {
                qtyInput.focus();
                qtyInput.select();
                console.log('Focus set on newly added row qty input:', qtyInput);
                
                // Get the row index from the row ID
                const rowId = lastRow.id;
                const rowIndex = parseInt(rowId.replace('row-', ''));
                
                // Select the newly added row for calculation
                selectRowForCalculation(rowIndex);
            } else {
                console.log('Qty input not focusable:', qtyInput);
            }
        } else {
            console.log('Newly added row not found');
        }
    }, 500);
}

// Global variables for table management
let currentRowIndex = 0;
let selectedRowIndex = null;
// Global variable to store existing adjustments for modification
window.existingAdjustments = {};
// Global variable to store current sale return ID (for modification mode)
window.currentSaleReturnId = null;

// Helper function to set sale return ID when loading for modification
function setSaleReturnId(id) {
    window.currentSaleReturnId = id;
    console.log('📝 Sale Return ID set:', id);
}

// Helper function to store existing adjustments from loaded sale return data
function storeExistingAdjustments(saleReturnData) {
    if (saleReturnData && saleReturnData.adjustments && saleReturnData.adjustments.length > 0) {
        window.existingAdjustments = {};
        saleReturnData.adjustments.forEach(adj => {
            window.existingAdjustments[adj.invoice_id] = adj.adjusted_amount;
        });
        console.log('✅ Stored existing adjustments:', window.existingAdjustments);
    } else {
        window.existingAdjustments = {};
        console.log('ℹ️ No existing adjustments found');
    }
    
    // Also set the sale return ID if available
    if (saleReturnData && saleReturnData.id) {
        setSaleReturnId(saleReturnData.id);
    }
}

// Add a single item row to the table
function addItemRow(item, index) {
    console.log(`addItemRow called for item ${index}, will create row-${currentRowIndex}`);
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    const isEditable = !!item._editable;
    const readonlyAttr = isEditable ? '' : 'readonly';
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    console.log(`Creating row with ID: ${row.id}`);
    row.innerHTML = `
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.item_code || ''}" ${readonlyAttr}>
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.item_name || ''}" ${readonlyAttr}>
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${item.batch_no || ''}" ${readonlyAttr}>
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${item.expiry_date || ''}" ${readonlyAttr}>
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][qty]" value="${item.return_qty || 0}" step="1" 
                   onchange="calculateRowAmount(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'free_qty'); return false; }" 
                   onfocus="selectRowForCalculation(${rowIndex})">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][free_qty]" value="${item.return_fqty || 0}" step="1"
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'sale_rate'); return false; }"
                   onfocus="selectRowForCalculation(${rowIndex})">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][sale_rate]" value="${item.sale_rate || 0}" step="0.01" 
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'dis_percent'); return false; }"
                   onfocus="selectRowForCalculation(${rowIndex})">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][dis_percent]" value="${item.discount_percent || 0}" step="0.01" 
                   onchange="handleDiscountChange(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); handleDiscountAndCompleteRow(${rowIndex}); return false; }" 
                   onfocus="selectRowForCalculation(${rowIndex})">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][mrp]" value="${item.mrp || 0}" step="0.01" readonly>
        </td>
        <td>
            <input type="number" class="form-control readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.item_id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${item.batch_id}">
        <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${item.hsn_code || ''}">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
        <input type="hidden" name="items[${rowIndex}][cgst_percent]" value="${item.cgst_percent || 0}">
        <input type="hidden" name="items[${rowIndex}][sgst_percent]" value="${item.sgst_percent || 0}">
        <input type="hidden" name="items[${rowIndex}][cess_percent]" value="${item.cess_percent || 0}">
    `;
    
    tbody.appendChild(row);
    
    // Store item data for calculations
    row.dataset.itemData = JSON.stringify(item);
    row.dataset.completed = 'false';
    
    // Add click event to row for selection
    row.addEventListener('click', function() {
        selectRowForCalculation(rowIndex);
    });
    
    // Calculate initial amount
    calculateRowAmount(rowIndex);
    
    // Focus and select first row for calculation
    if (index === 0) {
        setTimeout(() => {
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            if (qtyInput && !qtyInput.readOnly && !qtyInput.disabled) {
                // Scroll into view first
                qtyInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                setTimeout(() => {
                    qtyInput.focus();
                    qtyInput.select();
                    console.log('Focus set on qty input for row:', rowIndex, qtyInput);
                    
                    // Highlight the row and update calculation sections
                    selectRowForCalculation(rowIndex);
                }, 100);
            } else {
                console.log('Qty input not focusable for row:', rowIndex, qtyInput);
                // Still select the row for calculation even if can't focus
                selectRowForCalculation(rowIndex);
            }
        }, 300);
    }
}

// Update series label
function updateSeriesLabel() {
    const seriesSelect = document.getElementById('seriesSelect');
    const seriesLabel = document.getElementById('seriesLabel');
    
    if (seriesSelect.value === 'SR') {
        seriesLabel.textContent = 'SALES RETURN - CREDIT';
    } else {
        seriesLabel.textContent = 'SALES RETURN';
    }
}

// Handle invoice search on Enter key
function handleInvoiceSearch(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        searchInvoice();
    }
}

// Search for invoice
function searchInvoice() {
    const invoiceNo = document.getElementById('originalInvoiceNo').value.trim();
    const customerId = document.getElementById('customerSelect').value;
    
    if (!customerId) {
        showAlert('error', 'Please select a customer first.');
        return;
    }
    
    if (!invoiceNo) {
        showAlert('error', 'Please enter an invoice number.');
        return;
    }
    
    // Show loading
    showAlert('info', 'Searching for invoice...');
    
    // Make AJAX request
    fetch('{{ route("admin.sale-return.search-invoice") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            invoice_no: invoiceNo,
            customer_id: customerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Always show modal with transactions (even if only one)
            showInvoiceModal(data.transactions);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while searching for the invoice.');
    });
}

// Show invoice selection modal
function showInvoiceModal(transactions) {
    // Store all transactions globally for pagination
    window.allTransactions = transactions;
    window.currentPage = 1;
    window.recordsPerPage = 10;
    
    // Create modal HTML
    const modalHTML = `
        <div class="invoice-modal-backdrop" id="invoiceModalBackdrop" onclick="closeInvoiceModal()"></div>
        <div class="invoice-modal" id="invoiceModal">
            <div class="invoice-modal-content">
                <div class="invoice-modal-header">
                    <h5 class="invoice-modal-title">Select Invoice (${transactions.length} records)</h5>
                    <button type="button" class="btn-close-modal" onclick="closeInvoiceModal()">&times;</button>
                </div>
                <div class="invoice-modal-body" id="invoiceModalBody">
                    <table class="table table-bordered table-hover" style="font-size: 12px; margin-bottom: 0;">
                        <thead style="background: #e9ecef; position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th style="width: 30%;">Date</th>
                                <th style="width: 40%;">Bill No.</th>
                                <th style="width: 30%; text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTableBody">
                            <!-- Records will be loaded here -->
                        </tbody>
                    </table>
                    <div id="loadingIndicator" style="display: none; text-align: center; padding: 20px;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2">Loading more records...</span>
                    </div>
                </div>
                <div class="invoice-modal-footer">
                    <small class="text-muted me-auto" id="recordsInfo">Showing 0 of ${transactions.length} records</small>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoiceModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('invoiceModal');
    if (existingModal) {
        existingModal.remove();
    }
    const existingBackdrop = document.getElementById('invoiceModalBackdrop');
    if (existingBackdrop) {
        existingBackdrop.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal with animation
    setTimeout(() => {
        document.getElementById('invoiceModalBackdrop').classList.add('show');
        document.getElementById('invoiceModal').classList.add('show');
        
        // Load initial records
        loadInvoiceRecords();
        
        // Setup infinite scroll
        setupInvoiceInfiniteScroll();
    }, 10);
}

// Load invoice records with pagination
function loadInvoiceRecords() {
    const tbody = document.getElementById('invoiceTableBody');
    const recordsInfo = document.getElementById('recordsInfo');
    
    if (!window.allTransactions || !tbody) return;
    
    const startIndex = (window.currentPage - 1) * window.recordsPerPage;
    const endIndex = startIndex + window.recordsPerPage;
    const pageRecords = window.allTransactions.slice(startIndex, endIndex);
    
    // Append new records
    pageRecords.forEach(t => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        row.onclick = () => selectInvoice(t.id);
        row.innerHTML = `
            <td>${t.date}</td>
            <td>${t.invoice_no}</td>
            <td style="text-align: right;">${t.amount}</td>
        `;
        tbody.appendChild(row);
    });
    
    // Update records info
    const totalLoaded = tbody.children.length;
    recordsInfo.textContent = `Showing ${totalLoaded} of ${window.allTransactions.length} records`;
    
    window.currentPage++;
}

// Setup infinite scroll for invoice modal
function setupInvoiceInfiniteScroll() {
    const modalBody = document.getElementById('invoiceModalBody');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    if (!modalBody) return;
    
    modalBody.addEventListener('scroll', function() {
        if (modalBody.scrollTop + modalBody.clientHeight >= modalBody.scrollHeight - 5) {
            // Near bottom, load more records
            const totalLoaded = document.getElementById('invoiceTableBody').children.length;
            
            if (totalLoaded < window.allTransactions.length && !loadingIndicator.style.display === 'block') {
                loadingIndicator.style.display = 'block';
                
                setTimeout(() => {
                    loadInvoiceRecords();
                    loadingIndicator.style.display = 'none';
                }, 300);
            }
        }
    });
}

// Close invoice modal
function closeInvoiceModal() {
    const modal = document.getElementById('invoiceModal');
    const backdrop = document.getElementById('invoiceModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        backdrop.classList.remove('show');
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.remove();
        }
        if (backdrop) backdrop.remove();
    }, 300);
}

// Select invoice from modal
function selectInvoice(transactionId) {
    closeInvoiceModal();
    loadTransactionDetails(transactionId);
}

// Load transaction details
function loadTransactionDetails(transactionId) {
    showAlert('info', 'Loading transaction details...');
    
    const url = "{{ route('admin.sale-return.transaction-details', ':id') }}".replace(':id', transactionId);
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const transaction = data.transaction;
                
                // Populate header fields
                document.getElementById('originalInvoiceNo').value = transaction.invoice_no;
                document.getElementById('originalInvoiceDate').value = transaction.sale_date;
                
                // Populate the original series field (shows the original sale transaction series like SB, S2, etc.)
                document.getElementById('originalSeries').value = transaction.series;
                
                // Show and populate the amount field
                const amountContainer = document.getElementById('originalAmountContainer');
                const amountField = document.getElementById('originalAmount');
                if (amountContainer && amountField) {
                    amountContainer.style.display = 'block';
                    amountField.value = transaction.net_amount || '0.00';
                }
                
                // Keep the SR series select at top unchanged (sale return series stays SR)
                
                // Store transaction data in memory
                window.currentSaleTransaction = transaction;
                
                console.log('Transaction loaded:', transaction);
                showAlert('success', 'Transaction loaded successfully!');
                
                // You can now populate items table here if needed
                // populateItemsTable(transaction.items);
            } else {
                showAlert('error', data.message || 'Failed to load transaction details.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred: ' + error.message);
        });
}

// Show alert function
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertTypes = {
        'success': { bg: '#28a745', icon: '✓' },
        'error': { bg: '#dc3545', icon: '✕' },
        'info': { bg: '#17a2b8', icon: 'ℹ' },
        'warning': { bg: '#ffc107', icon: '⚠' }
    };
    
    const alertConfig = alertTypes[type] || alertTypes['info'];
    
    const alertHTML = `
        <div class="custom-alert" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${alertConfig.bg};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10001;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        ">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px; font-weight: bold;">${alertConfig.icon}</span>
                <span>${message}</span>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        const alert = document.querySelector('.custom-alert');
        if (alert) {
            alert.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => alert.remove(), 300);
        }
    }, 3000);
}

// Open Insert Orders Modal - Modified for sale return modification
function openInsertOrdersModal() {
    // First check if SR No is entered
    const srNo = document.getElementById('srNo').value.trim();
    
    if (srNo) {
        // Try to load this specific sale return invoice
        searchSaleReturnBySRNo(srNo);
        return;
    }
    
    // No SR No entered, show modal of past sale return invoices
    showPastSaleReturnsModal();
}

// Open modal with items from loaded invoice (original functionality)
function openInvoiceItemsModal() {
    const items = window.currentSaleTransaction.items;
    
    if (items.length === 0) {
        showAlert('warning', 'No items found in the selected invoice.');
        return;
    }
    
    // Get first batch number for header
    const firstBatchNo = items.length > 0 ? (items[0].batch_no || '') : '';
    
    // Create modal HTML with items table
    const modalHTML = `
        <div class="insert-orders-modal-backdrop" id="insertOrdersModalBackdrop" onclick="closeInsertOrdersModal()"></div>
        <div class="insert-orders-modal" id="insertOrdersModal">
            <div class="insert-orders-modal-content">
                <div class="insert-orders-modal-header">
                    <h5 class="insert-orders-modal-title">Batch: ${firstBatchNo}</h5>
                    <button type="button" class="btn-close-modal" onclick="closeInsertOrdersModal()">&times;</button>
                </div>
                <div class="insert-orders-modal-body">
                    <div style="background: #28a745; color: white; padding: 8px; text-align: center; margin-bottom: 10px; border-radius: 4px;">
                        <strong>Return All Items (F11)</strong>
                    </div>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered" style="font-size: 10px; margin-bottom: 0;">
                            <thead style="position: sticky; top: 0; background: #28a745; color: white; z-index: 10;">
                                <tr>
                                    <th style="width: 40px; text-align: center;">S.N</th>
                                    <th style="width: 200px;">Name</th>
                                    <th style="width: 80px; text-align: center;">Pack</th>
                                    <th style="width: 70px; text-align: right;">Rate</th>
                                    <th style="width: 60px; text-align: right;">Dis%</th>
                                    <th style="width: 70px; text-align: right;">Tot.Qty</th>
                                    <th style="width: 70px; text-align: right;">Tot.FQty</th>
                                    <th style="width: 70px; text-align: right;">Bal.Qty</th>
                                    <th style="width: 70px; text-align: right;">Bal.FQty</th>
                                    <th style="width: 70px; text-align: center;">R.Qty</th>
                                    <th style="width: 70px; text-align: center;">R.FQty</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${items.map((item, index) => {
                                    // Calculate balance quantities (available to return)
                                    const balQty = parseFloat(item.balance_qty || item.quantity || 0);
                                    const balFQty = parseFloat(item.balance_free_qty || item.free_quantity || 0);
                                    
                                    return `
                                    <tr ${index === 0 ? 'style="background: #cfe2ff;"' : ''}>
                                        <td style="text-align: center;">${index + 1}</td>
                                        <td>${item.item_name || ''}</td>
                                        <td style="text-align: center;">${item.packing || ''}</td>
                                        <td style="text-align: right;">${parseFloat(item.sale_rate || 0).toFixed(2)}</td>
                                        <td style="text-align: right;">${parseFloat(item.discount_percent || 0).toFixed(1)}</td>
                                        <td style="text-align: right;">${parseFloat(item.quantity || 0).toFixed(0)}</td>
                                        <td style="text-align: right;">${parseFloat(item.free_quantity || 0).toFixed(0)}</td>
                                        <td style="text-align: right; ${balQty < item.quantity ? 'font-weight: bold; color: #dc3545;' : ''}">${balQty.toFixed(0)}</td>
                                        <td style="text-align: right; ${balFQty < item.free_quantity ? 'font-weight: bold; color: #dc3545;' : ''}">${balFQty.toFixed(0)}</td>
                                        <td style="text-align: center;">
                                            <input type="number" class="form-control form-control-sm" 
                                                   id="rqty_${index}" 
                                                   value="0" 
                                                   min="0" 
                                                   max="${balQty}"
                                                   style="width: 60px; height: 24px; padding: 2px 4px; font-size: 10px; text-align: right;">
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="number" class="form-control form-control-sm" 
                                                   id="rfqty_${index}" 
                                                   value="0" 
                                                   min="0" 
                                                   max="${balFQty}"
                                                   style="width: 60px; height: 24px; padding: 2px 4px; font-size: 10px; text-align: right;">
                                        </td>
                                    </tr>
                                `}).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="insert-orders-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeInsertOrdersModal()">Close</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="generateReturn()">
                        <i class="bi bi-check-circle"></i> Generate Return
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('insertOrdersModal');
    if (existingModal) {
        existingModal.remove();
    }
    const existingBackdrop = document.getElementById('insertOrdersModalBackdrop');
    if (existingBackdrop) {
        existingBackdrop.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal with animation
    setTimeout(() => {
        document.getElementById('insertOrdersModalBackdrop').classList.add('show');
        document.getElementById('insertOrdersModal').classList.add('show');
    }, 10);
}

// Close Insert Orders Modal
function closeInsertOrdersModal() {
    const modal = document.getElementById('insertOrdersModal');
    const backdrop = document.getElementById('insertOrdersModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'slideDown 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        backdrop.classList.remove('show');
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.remove();
        }
        if (backdrop) backdrop.remove();
    }, 300);
}

// Open modal with all items from items module (OPTIMIZED with pagination)
// Global state for item pagination
let itemsCurrentPage = 1;
let itemsPerPage = 50;
let itemsHasMore = true;
let itemsLoading = false;
let allLoadedItems = [];

function openAllItemsModal() {
    // LEGACY REDIRECT: Use new reusable modal component
    console.log('openAllItemsModal called - redirecting to new modal component');
    if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    } else {
        showAlert('error', 'Item selection modal not initialized. Please reload the page.');
    }
}

// Fetch paginated items from server
function fetchPaginatedItems(page, isInitial = false) {
    if (itemsLoading || (!itemsHasMore && !isInitial)) return;
    
    itemsLoading = true;
    
    // Show loading indicator
    if (!isInitial) {
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'block';
    }
    
    // Use existing items/all route with pagination params
    const url = `{{ route("admin.items.all") }}?page=${page}&per_page=${itemsPerPage}`;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        itemsLoading = false;
        
        // Hide loading indicator
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'none';
        
        if (data.success && data.items) {
            const items = data.items;
            
            // Check if we have pagination info
            if (data.pagination) {
                itemsHasMore = data.pagination.has_more || (page < data.pagination.last_page);
            } else {
                // If no pagination info, assume all items are returned (legacy)
                itemsHasMore = false;
            }
            
            // Store loaded items
            allLoadedItems = allLoadedItems.concat(items);
            
            if (isInitial) {
                // Create and show the modal with initial items
                showPaginatedItemSelectionModal(items);
            } else {
                // Append items to existing table
                appendItemsToTable(items);
            }
            
            // Update records info
            updateItemsRecordsInfo();
            
            itemsCurrentPage++;
        } else {
            if (isInitial) {
                showAlert('error', 'Failed to load items.');
            }
        }
    })
    .catch(error => {
        itemsLoading = false;
        console.error('Error:', error);
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'none';
        
        if (isInitial) {
            showAlert('error', 'An error occurred while loading items.');
        }
    });
}

// Show item selection modal with pagination support
function showPaginatedItemSelectionModal(items) {
    const totalInfo = itemsHasMore ? `Showing first ${items.length} items (scroll for more)` : `Showing all ${items.length} items`;
    
    const modalHTML = `
        <div class="insert-orders-modal-backdrop" id="insertOrdersModalBackdrop" onclick="closeInsertOrdersModal()"></div>
        <div class="insert-orders-modal item-selection-modal" id="insertOrdersModal">
            <div class="insert-orders-modal-content">
                <div class="insert-orders-modal-header">
                    <h5 class="insert-orders-modal-title">Select Items for Sale Return</h5>
                    <button type="button" class="btn-close-modal" onclick="closeInsertOrdersModal()">&times;</button>
                </div>
                <div class="insert-orders-modal-body">
                    <!-- Search Box -->
                    <div style="margin-bottom: 10px;">
                        <input type="text" id="itemSearchInput" class="form-control form-control-sm" 
                               placeholder="Search by item name or code..." 
                               onkeyup="filterItems()"
                               style="font-size: 11px;">
                    </div>
                    
                    <div style="max-height: 400px; overflow-y: auto;" id="itemsScrollContainer">
                        <table class="table table-bordered" style="font-size: 10px; margin-bottom: 0; width: 100%;" id="itemsSelectionTable">
                            <thead style="position: sticky; top: 0; background: #0d6efd; color: white; z-index: 10;">
                                <tr>
                                    <th style="width: 35px; text-align: center;">S.N</th>
                                    <th style="width: 60px;">Code</th>
                                    <th style="width: 180px;">Item Name</th>
                                    <th style="width: 70px; text-align: right;">MRP</th>
                                    <th style="width: 70px; text-align: right;">Rate</th>
                                    <th style="width: 100px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsSelectionTableBody">
                            </tbody>
                        </table>
                        <!-- Loading indicator -->
                        <div id="itemsLoadingIndicator" style="display: none; text-align: center; padding: 15px; color: #6c757d;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2">Loading more items...</span>
                        </div>
                    </div>
                </div>
                <div class="insert-orders-modal-footer">
                    <small class="text-muted me-auto" id="itemsRecordsInfo">${totalInfo}</small>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeInsertOrdersModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('insertOrdersModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('insertOrdersModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add initial items to table
    appendItemsToTable(items);
    
    // Show modal with animation
    setTimeout(() => {
        document.getElementById('insertOrdersModalBackdrop').classList.add('show');
        document.getElementById('insertOrdersModal').classList.add('show');
        
        // Setup infinite scroll after modal is visible
        setupItemsInfiniteScroll();
    }, 10);
}

// Append items to the selection table
function appendItemsToTable(items) {
    const tbody = document.getElementById('itemsSelectionTableBody');
    if (!tbody) return;
    
    const startIndex = tbody.children.length;
    
    items.forEach((item, index) => {
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.setAttribute('data-item-name', (item.name || '').toLowerCase());
        row.innerHTML = `
            <td style="text-align: center;">${startIndex + index + 1}</td>
            <td>${item.code || ''}</td>
            <td>${item.name || ''}</td>
            <td style="text-align: right;">${parseFloat(item.mrp || 0).toFixed(2)}</td>
            <td style="text-align: right;">${parseFloat(item.s_rate || 0).toFixed(2)}</td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-sm btn-primary" 
                        onclick='selectItemBatch(${JSON.stringify(item).replace(/'/g, "\\'")})'
                        style="font-size: 9px; padding: 2px 6px; white-space: nowrap;">
                    <i class="bi bi-box-seam"></i> Batch
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Update items records info
function updateItemsRecordsInfo() {
    const infoEl = document.getElementById('itemsRecordsInfo');
    if (!infoEl) return;
    
    const loadedCount = allLoadedItems.length;
    if (itemsHasMore) {
        infoEl.textContent = `Showing ${loadedCount} items (scroll for more)`;
    } else {
        infoEl.textContent = `Showing all ${loadedCount} items`;
    }
}

// Setup infinite scroll for items
function setupItemsInfiniteScroll() {
    const scrollContainer = document.getElementById('itemsScrollContainer');
    if (!scrollContainer) return;
    
    scrollContainer.addEventListener('scroll', function() {
        // Check if scrolled near bottom
        if (scrollContainer.scrollTop + scrollContainer.clientHeight >= scrollContainer.scrollHeight - 50) {
            // Load more items if available
            if (itemsHasMore && !itemsLoading) {
                fetchPaginatedItems(itemsCurrentPage, false);
            }
        }
    });
}

// Show item selection modal (legacy - kept for backward compatibility)
function showItemSelectionModal(items) {
    // Use the new paginated modal
    allLoadedItems = items;
    itemsHasMore = false; // All items loaded
    showPaginatedItemSelectionModal(items);
}

// Filter items in the selection table
function filterItems() {
    const searchValue = document.getElementById('itemSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.item-row');
    
    rows.forEach(row => {
        const itemName = row.getAttribute('data-item-name');
        const code = row.children[1].textContent.toLowerCase();
        
        if (itemName.includes(searchValue) || code.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Select batch for an item
function selectItemBatch(item) {
    // Parse item if it's a string
    if (typeof item === 'string') {
        item = JSON.parse(item);
    }
    
    // Store complete item details including HSN and tax data
    window.selectedItem = {
        item_id: item.id || item.code,
        item_code: item.code,
        item_name: item.name,
        mrp: parseFloat(item.mrp || 0),
        sale_rate: parseFloat(item.s_rate || item.srate || 0),
        hsn_code: item.hsn_code || '',
        cgst_percent: parseFloat(item.cgst || 6),
        sgst_percent: parseFloat(item.sgst || 6),
        cess_percent: parseFloat(item.gst_cess || 0),
        packing: item.packing || '',
        company_name: item.company_name || '',
        unit: item.unit || 'PCS'
    };
    
    console.log('Selected Item Data:', window.selectedItem);
    
    // Fetch batches for this item
    showAlert('info', 'Loading batches...');
    
    fetch(`{{ url('/admin/api/item-batches') }}/${window.selectedItem.item_id}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.batches && data.batches.length > 0) {
            showBatchSelectionModal(data.batches);
        } else {
            // No batches found, directly show create batch modal
            showAlert('info', 'No batches found. Please create a new batch.');
            setTimeout(() => {
                openCreateBatchModal();
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while loading batches.');
    });
}

// Show batch selection modal
function showBatchSelectionModal(batches) {
    const modalHTML = `
        <div class="batch-modal-backdrop" id="batchModalBackdrop" onclick="closeBatchModal()"></div>
        <div class="batch-modal" id="batchModal">
            <div class="batch-modal-content">
                <div class="batch-modal-header">
                    <h5 class="batch-modal-title">Select Batch for ${window.selectedItem.item_name}</h5>
                    <button type="button" class="btn-close-modal" onclick="closeBatchModal()">&times;</button>
                </div>
                <div class="batch-modal-body">
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered" style="font-size: 10px; margin-bottom: 0;">
                            <thead style="position: sticky; top: 0; background: #28a745; color: white; z-index: 10;">
                                <tr>
                                    <th style="width: 40px;">S.N</th>
                                    <th style="width: 100px;">Batch No</th>
                                    <th style="width: 100px;">Expiry</th>
                                    <th style="width: 80px; text-align: right;">Qty</th>
                                    <th style="width: 80px; text-align: right;">MRP</th>
                                    <th style="width: 80px; text-align: right;">Rate</th>
                                    <th style="width: 100px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${batches.map((batch, index) => `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${batch.batch_no || ''}</td>
                                        <td>${batch.expiry_date || ''}</td>
                                        <td style="text-align: right;">${parseFloat(batch.qty || 0).toFixed(0)}</td>
                                        <td style="text-align: right;">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                                        <td style="text-align: right;">${parseFloat(batch.s_rate || 0).toFixed(2)}</td>
                                        <td style="text-align: center;">
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick='addItemToReturn(${JSON.stringify(batch).replace(/'/g, "\\'")})'
                                                    style="font-size: 9px; padding: 2px 8px;">
                                                <i class="bi bi-plus-circle"></i> Add
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="batch-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openCreateBatchModal()">
                        <i class="bi bi-plus-circle"></i> Create New Batch
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing batch modal if any
    const existingModal = document.getElementById('batchModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('batchModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal with animation
    setTimeout(() => {
        document.getElementById('batchModalBackdrop').classList.add('show');
        document.getElementById('batchModal').classList.add('show');
    }, 10);
}

// Close batch modal
function closeBatchModal() {
    const modal = document.getElementById('batchModal');
    const backdrop = document.getElementById('batchModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        backdrop.classList.remove('show');
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.remove();
        }
        if (backdrop) backdrop.remove();
    }, 300);
}

// Open Create Batch Modal
function openCreateBatchModal() {
    const selectedItem = window.selectedItem;
    
    const modalHTML = `
        <div class="create-batch-modal-backdrop" id="createBatchModalBackdrop" onclick="closeCreateBatchModal()"></div>
        <div class="create-batch-modal" id="createBatchModal">
            <div class="create-batch-modal-content">
                <div class="create-batch-modal-header">
                    <h5 class="create-batch-modal-title">Opening New Batch</h5>
                    <button type="button" class="btn-close-modal" onclick="closeCreateBatchModal()">&times;</button>
                </div>
                <div class="create-batch-modal-body">
                    <form id="createBatchForm">
                        <div style="margin-bottom: 10px;">
                            <label style="font-weight: bold; font-size: 11px; color: #0066cc;">Item Name:</label>
                            <span style="font-weight: bold; font-size: 14px; color: #0066cc; margin-left: 10px;">${selectedItem.item_name}</span>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label style="font-weight: bold; font-size: 11px;">Batch Number:</label>
                                <input type="text" class="form-control form-control-sm" id="newBatchNo" required style="font-size: 11px;">
                            </div>
                            <div class="col-md-6">
                                <label style="font-weight: bold; font-size: 11px;">Pack:</label>
                                <input type="text" class="form-control form-control-sm" id="newBatchPack" value="1*10" style="font-size: 11px;">
                            </div>
                        </div>
                        
                        <div class="row g-2 mt-2">
                            <div class="col-md-4">
                                <label style="font-weight: bold; font-size: 11px;">S.Rate:</label>
                                <input type="number" class="form-control form-control-sm" id="newBatchSRate" value="${selectedItem.sale_rate || 0}" required step="0.01" style="font-size: 11px; background: #ffffcc;">
                            </div>
                            <div class="col-md-4">
                                <label style="font-weight: bold; font-size: 11px;">Expiry:</label>
                                <input type="text" class="form-control form-control-sm" id="newBatchExpiry" placeholder="MM/YY" style="font-size: 11px;" maxlength="5">
                            </div>
                            <div class="col-md-4">
                                <label style="font-weight: bold; font-size: 11px;">MRP:</label>
                                <input type="number" class="form-control form-control-sm" id="newBatchMRP" value="${selectedItem.mrp || 0}" required step="0.01" style="font-size: 11px;">
                            </div>
                        </div>
                        
                        <div class="row g-2 mt-2">
                            <div class="col-md-6">
                                <label style="font-weight: bold; font-size: 11px;">Location:</label>
                                <input type="text" class="form-control form-control-sm" id="newBatchLocation" value="MAIN" style="font-size: 11px;">
                            </div>
                            <div class="col-md-6">
                                <label style="font-weight: bold; font-size: 11px;">Inclusive:</label>
                                <select class="form-control form-control-sm" id="newBatchInclusive" style="font-size: 11px;">
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <small class="text-muted" style="font-size: 10px;">*Expiry format: MM/YY (example: 11/25)</small>
                        </div>
                    </form>
                </div>
                <div class="create-batch-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeCreateBatchModal()">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="createNewBatch()">
                        <i class="bi bi-check-circle"></i> OK
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing create batch modal if any
    const existingModal = document.getElementById('createBatchModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('createBatchModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    // Close batch selection modal if open
    const batchModal = document.getElementById('batchModal');
    if (batchModal) closeBatchModal();
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal with animation
    setTimeout(() => {
        document.getElementById('createBatchModalBackdrop').classList.add('show');
        document.getElementById('createBatchModal').classList.add('show');
        // Focus on batch number input
        document.getElementById('newBatchNo').focus();
    }, 10);
}

// Close Create Batch Modal
function closeCreateBatchModal() {
    const modal = document.getElementById('createBatchModal');
    const backdrop = document.getElementById('createBatchModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        backdrop.classList.remove('show');
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.remove();
        }
        if (backdrop) backdrop.remove();
    }, 300);
}

// Create New Batch
function createNewBatch() {
    const selectedItem = window.selectedItem;
    const batchNo = document.getElementById('newBatchNo').value.trim();
    const sRate = parseFloat(document.getElementById('newBatchSRate').value);
    const mrp = parseFloat(document.getElementById('newBatchMRP').value);
    const expiry = document.getElementById('newBatchExpiry').value.trim();
    const pack = document.getElementById('newBatchPack').value.trim();
    const location = document.getElementById('newBatchLocation').value.trim();
    const inclusive = document.getElementById('newBatchInclusive').value;
    
    // Validation
    if (!batchNo) {
        showAlert('error', 'Please enter a batch number.');
        return;
    }
    
    if (!sRate || sRate <= 0) {
        showAlert('error', 'Please enter a valid sale rate.');
        return;
    }
    
    if (!mrp || mrp <= 0) {
        showAlert('error', 'Please enter a valid MRP.');
        return;
    }
    
    // Validate expiry format if provided
    if (expiry && !/^\d{2}\/\d{2}$/.test(expiry)) {
        showAlert('error', 'Please enter expiry in MM/YY format.');
        return;
    }
    
    showAlert('info', 'Creating batch...');
    
    // Prepare batch data
    const batchData = {
        item_id: selectedItem.item_id,
        batch_no: batchNo,
        s_rate: sRate,
        mrp: mrp,
        pur_rate: sRate, // Use sale rate as purchase rate for now
        expiry_date: expiry,
        total_qty: 0 // Will be set during return
    };
    
    // Send AJAX request to create batch
    fetch('{{ route("admin.batches.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify(batchData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Batch created successfully!');
            
            // Close create batch modal
            closeCreateBatchModal();
            
            // Add the newly created batch to the return transaction
            const newBatch = data.batch;
            addItemToReturn(newBatch);
        } else {
            showAlert('error', data.message || 'Failed to create batch.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while creating the batch.');
    });
}

// Add item with selected batch to return transaction
function addItemToReturn(batch) {
    console.log('addItemToReturn called with batch:', batch);
    const selectedItem = window.selectedItem;
    console.log('Selected item:', selectedItem);
    
    // Create item object similar to transaction items
    const newItem = {
        item_id: selectedItem.item_id,
        item_code: selectedItem.item_code || selectedItem.item_id,
        item_name: selectedItem.item_name,
        batch_id: batch.id,
        batch_no: batch.batch_no,
        expiry_date: batch.expiry_date,
        packing: selectedItem.packing || batch.packing || '',
        unit: selectedItem.unit || batch.unit || 'PCS',
        company_name: selectedItem.company_name || batch.company_name || '',
        hsn_code: selectedItem.hsn_code || batch.hsn_code || '',
        sale_rate: parseFloat(batch.s_rate || selectedItem.sale_rate || 0),
        mrp: parseFloat(batch.mrp || selectedItem.mrp || 0),
        discount_percent: 0,
        cgst_percent: parseFloat(selectedItem.cgst_percent || batch.cgst_percent || 6),
        sgst_percent: parseFloat(selectedItem.sgst_percent || batch.sgst_percent || 6),
        cess_percent: parseFloat(selectedItem.cess_percent || batch.cess_percent || 0),
        return_qty: 0, // Default quantity
        return_fqty: 0
    };
    
    // Initialize items array if not exists
    if (!window.returnItems) {
        window.returnItems = [];
    }
    
    // Check if item already exists to prevent duplicates
    const existingItem = window.returnItems.find(item => 
        item.item_id === newItem.item_id && item.batch_id === newItem.batch_id
    );
    
    if (existingItem) {
        console.log('Item already exists in return list');
        showAlert('warning', 'This item with same batch is already added to return list.');
        return;
    }
    
    // Add to items array
    window.returnItems.push(newItem);
    console.log('Item added to return array. Total items:', window.returnItems.length);
    
    // Close both modals
    closeBatchModal();
    closeInsertOrdersModal();
    
    // Add only the new item to table (not all items)
    populateItemsTable([newItem]);
    
    showAlert('success', 'Item added successfully! You can modify quantities and rates.');
}

// Search sale return by SR No
function searchSaleReturnBySRNo(srNo) {
    showAlert('info', 'Searching for sale return...');
    
    fetch('{{ route("admin.sale-return.search-by-sr") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({ sr_no: srNo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.saleReturn) {
            loadSaleReturnForModification(data.saleReturn);
        } else {
            showAlert('error', data.message || 'Sale return not found.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while searching.');
    });
}

// Show past sale returns modal
function showPastSaleReturnsModal() {
    showAlert('info', 'Loading sale returns...');
    
    fetch('{{ route("admin.sale-return.past-returns") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.saleReturns) {
            displayPastSaleReturnsModal(data.saleReturns);
        } else {
            showAlert('error', 'Failed to load sale returns.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while loading sale returns.');
    });
}

// Display past sale returns modal
function displayPastSaleReturnsModal(saleReturns) {
    console.log('[KB] displayPastSaleReturnsModal', { count: saleReturns?.length });
    const modalHTML = `
        <div class="invoice-modal-backdrop" id="saleReturnModalBackdrop" onclick="closeSaleReturnModal()"></div>
        <div class="invoice-modal" id="saleReturnModal">
            <div class="invoice-modal-content">
                <div class="invoice-modal-header" style="background: #dc3545; border-bottom-color: #c82333;">
                    <h5 class="invoice-modal-title">Select Sale Return to Modify (${saleReturns.length} records)</h5>
                    <button type="button" class="btn-close-modal" onclick="closeSaleReturnModal()">&times;</button>
                </div>
                <div class="invoice-modal-body" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover" style="font-size: 12px; margin-bottom: 0;">
                        <thead style="background: #f8d7da; position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th style="width: 25%;">SR No.</th>
                                <th style="width: 25%;">Date</th>
                                <th style="width: 30%;">Customer</th>
                                <th style="width: 20%; text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${saleReturns.map(sr => `
                                <tr style="cursor: pointer;" onclick="selectSaleReturnForModification(${sr.id})">
                                    <td>${sr.sr_no || ''}</td>
                                    <td>${sr.return_date || ''}</td>
                                    <td>${sr.customer_name || ''}</td>
                                    <td style="text-align: right;">${parseFloat(sr.net_amount || 0).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="invoice-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeSaleReturnModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('saleReturnModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('saleReturnModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal with animation
    setTimeout(() => {
        document.getElementById('saleReturnModalBackdrop').classList.add('show');
        document.getElementById('saleReturnModal').classList.add('show');
        console.log('[KB] saleReturnModal shown');
        initSaleReturnModalKeyboard();
    }, 10);
}

// Close sale return modal
function closeSaleReturnModal() {
    const modal = document.getElementById('saleReturnModal');
    const backdrop = document.getElementById('saleReturnModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        backdrop.classList.remove('show');
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.remove();
        }
        if (backdrop) backdrop.remove();
        if (saleReturnModalKeyHandler) {
            window.removeEventListener('keydown', saleReturnModalKeyHandler, true);
            saleReturnModalKeyHandler = null;
        }
    }, 300);
}

// Keyboard navigation for Sale Return selection modal
let saleReturnModalKeyHandler = null;
function initSaleReturnModalKeyboard() {
    const modal = document.getElementById('saleReturnModal');
    if (!modal) return;
    const body = modal.querySelector('tbody');
    if (!body) return;

    let activeIndex = 0;
    modal.setAttribute('tabindex', '-1');
    modal.focus();
    console.log('[KB] initSaleReturnModalKeyboard', { rows: body.querySelectorAll('tr').length });

    function getRows() {
        return Array.from(body.querySelectorAll('tr'));
    }

    function setActiveRow(index) {
        const rows = getRows();
        rows.forEach(r => r.classList.remove('kb-row-active'));
        if (!rows.length) return;
        if (index < 0) index = 0;
        if (index >= rows.length) index = rows.length - 1;
        activeIndex = index;
        const row = rows[activeIndex];
        row.classList.add('kb-row-active');
        row.scrollIntoView({ block: 'nearest' });
    }

    setActiveRow(0);

    if (saleReturnModalKeyHandler) {
        window.removeEventListener('keydown', saleReturnModalKeyHandler, true);
    }

    saleReturnModalKeyHandler = function(e) {
        const modalEl = document.getElementById('saleReturnModal');
        if (!modalEl || !modalEl.classList.contains('show')) return;

        if (e.key === 'ArrowDown') {
            console.log('[KB] saleReturnModal ArrowDown');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            setActiveRow(activeIndex + 1);
        } else if (e.key === 'ArrowUp') {
            console.log('[KB] saleReturnModal ArrowUp');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            setActiveRow(activeIndex - 1);
        } else if (e.key === 'Enter') {
            console.log('[KB] saleReturnModal Enter');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const rows = getRows();
            if (!rows.length) return;
            window.kbFocusTableAfterLoad = true;
            rows[activeIndex].click();
        } else if (e.key === 'Escape') {
            console.log('[KB] saleReturnModal Escape');
            closeSaleReturnModal();
        }
    };

    window.addEventListener('keydown', saleReturnModalKeyHandler, true);
}

// Global capture to keep modal keyboard active even if focus is outside
window.addEventListener('keydown', function(e) {
    const modalEl = document.getElementById('saleReturnModal');
    if (!modalEl || !modalEl.classList.contains('show')) return;
    if (!saleReturnModalKeyHandler) return;
    if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) {
        console.log('[KB] saleReturnModal global capture', { key: e.key, active: document.activeElement?.id || document.activeElement?.tagName });
        saleReturnModalKeyHandler(e);
    }
}, true);

// Select sale return for modification
function selectSaleReturnForModification(saleReturnId) {
    closeSaleReturnModal();
    
    showAlert('info', 'Loading sale return details...');
    
    fetch(`{{ url('/admin/sale-return/details') }}/${saleReturnId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.saleReturn) {
            loadSaleReturnForModification(data.saleReturn);
        } else {
            showAlert('error', 'Failed to load sale return details.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while loading sale return.');
    });
}

// Load sale return data for modification
function loadSaleReturnForModification(saleReturn) {
    console.log('Loading sale return for modification:', saleReturn);
    console.log('Customer ID:', saleReturn.customer_id);
    console.log('Customer Name:', saleReturn.customer_name);
    
    // Store current sale return ID for update
    window.currentSaleReturnId = saleReturn.id;
    
    // Populate header fields
    document.getElementById('returnDate').value = saleReturn.return_date || '';
    updateDayName();
    
    // Set customer - with retry if customer not found
    const customerSelect = document.getElementById('customerSelect');
    console.log('Available customer options:', Array.from(customerSelect.options).map(o => ({value: o.value, text: o.text})));
    
    if (saleReturn.customer_id) {
        // Force select the customer option
        customerSelect.value = saleReturn.customer_id;
        
        // Trigger change event to update UI
        customerSelect.dispatchEvent(new Event('change', { bubbles: true }));
        
        console.log('Set customer dropdown to:', saleReturn.customer_id);
        console.log('Actual dropdown value after setting:', customerSelect.value);
        console.log('Selected option text:', customerSelect.options[customerSelect.selectedIndex]?.text);
        
        // Check if customer exists in dropdown
        if (customerSelect.value != saleReturn.customer_id) {
            console.warn('❌ Customer ID not found in dropdown:', saleReturn.customer_id);
            // Try to add the customer option if we have the name
            if (saleReturn.customer_name) {
                const option = document.createElement('option');
                option.value = saleReturn.customer_id;
                option.textContent = saleReturn.customer_name;
                option.selected = true;
                customerSelect.appendChild(option);
                const customerList = document.getElementById('customerList');
                if (customerList) {
                    const item = document.createElement('div');
                    item.className = 'dropdown-item';
                    item.setAttribute('data-id', saleReturn.customer_id);
                    item.setAttribute('data-name', saleReturn.customer_name);
                    item.setAttribute('data-code', '');
                    item.style.padding = '8px 12px';
                    item.style.cursor = 'pointer';
                    item.style.fontSize = '13px';
                    item.style.borderBottom = '1px solid #f0f0f0';
                    item.textContent = saleReturn.customer_name;
                    customerList.appendChild(item);
                }
                // Trigger change event after adding option
                customerSelect.dispatchEvent(new Event('change', { bubbles: true }));
                console.log('✅ Added missing customer option:', saleReturn.customer_name);
            } else {
                console.error('❌ Customer name also missing, cannot add option');
            }
        } else {
            console.log('✅ Customer found and selected successfully');
        }
    }
    
    // Set salesman
    const salesmanSelect = document.getElementById('salesmanSelect');
    if (saleReturn.salesman_id) {
        salesmanSelect.value = saleReturn.salesman_id;
        
        // Trigger change event to update UI
        salesmanSelect.dispatchEvent(new Event('change', { bubbles: true }));
        
        console.log('Set salesman dropdown to:', saleReturn.salesman_id);
        console.log('Selected salesman text:', salesmanSelect.options[salesmanSelect.selectedIndex]?.text);
        
        // Check if salesman exists in dropdown
        if (salesmanSelect.value != saleReturn.salesman_id) {
            console.warn('❌ Salesman ID not found in dropdown:', saleReturn.salesman_id);
            // Try to add the salesman option if we have the name
            if (saleReturn.salesman_name) {
                const option = document.createElement('option');
                option.value = saleReturn.salesman_id;
                option.textContent = saleReturn.salesman_name;
                option.selected = true;
                salesmanSelect.appendChild(option);
                // Trigger change event after adding option
                salesmanSelect.dispatchEvent(new Event('change', { bubbles: true }));
                console.log('✅ Added missing salesman option:', saleReturn.salesman_name);
            }
        } else {
            console.log('✅ Salesman found and selected successfully');
        }
    }
    
    document.getElementById('rateDiff').value = saleReturn.rate_diff_flag || 'N';
    document.getElementById('cash').value = saleReturn.cash_flag || 'N';
    document.getElementById('tax').value = saleReturn.tax_flag || 'N';
    
    document.getElementById('originalInvoiceNo').value = saleReturn.original_invoice_no || '';
    document.getElementById('originalInvoiceDate').value = saleReturn.original_invoice_date || '';
    document.getElementById('originalSeries').value = saleReturn.original_series || '';
    
    if (saleReturn.original_amount) {
        document.getElementById('originalAmountContainer').style.display = 'block';
        document.getElementById('originalAmount').value = parseFloat(saleReturn.original_amount).toFixed(2);
    }
    
    document.getElementById('remarks').value = saleReturn.remarks || '';
    document.getElementById('srNo').value = saleReturn.sr_no || '';
    document.getElementById('fixedDiscount').value = saleReturn.fixed_discount || '0';
    
    // Clear existing items table
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    currentRowIndex = 0;
    
    // Populate items
    if (saleReturn.items && saleReturn.items.length > 0) {
        populateItemsTable(saleReturn.items);
    }
    
    // Update button text to indicate modification
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-check-circle me-1"></i> Update Sale Return';
    
    showAlert('success', 'Sale return loaded successfully! You can now modify it.');

    // If SR No enter triggered load, move focus to table
    if (window.kbFocusTableAfterLoad) {
        window.kbFocusTableAfterLoad = false;
        setTimeout(() => {
            const firstRow = document.querySelector('#itemsTableBody tr');
            if (!firstRow) return;
            const qtyInput = firstRow.querySelector('input[name*="[qty]"]');
            const firstInput = firstRow.querySelector('input');
            const target = qtyInput || firstInput;
            if (target) {
                target.focus();
                if (target.select) target.select();
            }
        }, 150);
    }
}

// Generate Return from Modal
function generateReturn() {
    const items = window.currentSaleTransaction.items;
    const returnItems = [];
    
    // Collect items with return quantities
    items.forEach((item, index) => {
        const rQty = parseFloat(document.getElementById(`rqty_${index}`).value || 0);
        const rFQty = parseFloat(document.getElementById(`rfqty_${index}`).value || 0);
        
        if (rQty > 0 || rFQty > 0) {
            returnItems.push({
                ...item,
                return_qty: rQty,
                return_fqty: rFQty
            });
        }
    });
    
    if (returnItems.length === 0) {
        showAlert('warning', 'Please enter return quantities for at least one item.');
        return;
    }
    
    // Populate the items table with return items
    populateItemsTable(returnItems);
    
    closeInsertOrdersModal();
    showAlert('success', `${returnItems.length} item(s) added to return transaction.`);
}


// Move to Next Field - Enhanced cursor navigation
function moveToNextField(rowIndex, nextFieldName) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const nextField = row.querySelector(`[name*="[${nextFieldName}]"]`);
    if (nextField) {
        setTimeout(() => {
            nextField.focus();
            if (nextField.tagName === 'INPUT' && nextField.type === 'number') {
                nextField.select();
            }
        }, 100);
    }
}

// Handle Discount Change - Highlights row and triggers calculations
function handleDiscountChange(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    // Calculate the row amount
    calculateRowAmount(rowIndex);
    
    // Update calculations for selected row
    if (selectedRowIndex === rowIndex) {
        updateCalculationSection(rowIndex);
    }
}

// Handle Discount and Complete Row - Called when Enter is pressed on Dis% field
function handleDiscountAndCompleteRow(rowIndex) {
    console.log('handleDiscountAndCompleteRow called for row:', rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) {
        console.log('Row not found:', rowIndex);
        return;
    }
    
    console.log('Marking row as completed');
    // Mark row as completed (permanent green)
    markRowAsCompleted(rowIndex);
    
    // Calculate the row amount
    calculateRowAmount(rowIndex);
    
    // Update summary totals (add this row's data to summary)
    recalculateTotals();
    
    // Clear calculation section (reset for next row)
    clearCalculationSection();
    
    // Clear additional details section (reset for next row)
    clearAdditionalDetails();
    
    // Remove focus from current field
    document.activeElement.blur();
    
    // Clear selection (no row selected now)
    selectedRowIndex = null;
    
    console.log('Row completed and sections cleared for next row');

    // After completing row, create new row and focus code
    addNewRow();
}

// Capture Enter on Dis% field to ensure add row triggers
window.addEventListener('keydown', function(e) {
    const target = e.target;
    if (!target || (e.key !== 'Enter' && e.keyCode !== 13)) return;
    const name = target.getAttribute('name') || '';
    if (!name.includes('[dis_percent]')) return;
    const row = target.closest('tr');
    const rowId = row?.id || '';
    const rowIndex = rowId.startsWith('row-') ? parseInt(rowId.replace('row-', '')) : NaN;
    if (Number.isNaN(rowIndex)) return;
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    handleDiscountAndCompleteRow(rowIndex);
}, true);

// Mark row as completed (green background)
function markRowAsCompleted(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    // Set green background
    row.style.backgroundColor = '#d4edda';
    row.dataset.completed = 'true';
    
    // Apply to all cells
    const cells = row.querySelectorAll('td');
    cells.forEach(cell => {
        cell.style.backgroundColor = '#d4edda';
    });
}

// Select Row for Calculation - Updates calculation section with selected row's data
function selectRowForCalculation(rowIndex) {
    console.log('selectRowForCalculation called for row:', rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) {
        console.log('Row not found:', rowIndex);
        return;
    }
    
    // Check if row is completed (green)
    const isCompleted = row.dataset.completed === 'true';
    console.log('Row completed status:', isCompleted);
    
    // Allow selection of any row (completed or not) for viewing data
    // Store selected row
    selectedRowIndex = rowIndex;
    
    // Clear all row highlights except completed ones
    clearRowHighlights();
    
    // Highlight selected row (light blue) if not completed, keep green if completed
    if (!isCompleted) {
        row.style.backgroundColor = '#e7f3ff';
        console.log('Row highlighted blue (editing mode)');
    } else {
        row.style.backgroundColor = '#d4edda';
        console.log('Row kept green (completed)');
    }
    
    // Update calculation section with this row's data
    updateCalculationSection(rowIndex);
    
    // Update additional details section
    updateAdditionalDetails(rowIndex);
    
    console.log('Calculation and additional details updated for row:', rowIndex);
}

// Clear row highlights except completed ones
function clearRowHighlights() {
    const allRows = document.querySelectorAll('#itemsTableBody tr');
    allRows.forEach(r => {
        if (r.dataset.completed !== 'true') {
            r.style.backgroundColor = '';
        } else {
            // Keep completed rows green
            r.style.backgroundColor = '#d4edda';
        }
    });
}

// Update Calculation Section with selected row's data
function updateCalculationSection(rowIndex) {
    console.log('updateCalculationSection called for row:', rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) {
        console.log('Row not found in updateCalculationSection:', rowIndex);
        return;
    }
    
    const itemData = JSON.parse(row.dataset.itemData || '{}');
    console.log('Item data for calculation:', itemData);
    
    // Update HSN and tax information
    document.getElementById('calc_hsn_code').value = itemData.hsn_code || '---';
    document.getElementById('calc_cgst_percent').value = parseFloat(itemData.cgst_percent || 0).toFixed(2);
    document.getElementById('calc_sgst_percent').value = parseFloat(itemData.sgst_percent || 0).toFixed(2);
    document.getElementById('calc_cess_percent').value = parseFloat(itemData.cess_percent || 0).toFixed(2);
    
    console.log('HSN Code set to:', itemData.hsn_code || '---');
    
    // Calculate and show tax amounts for this row
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value || 0);
    const rate = parseFloat(row.querySelector('input[name*="[sale_rate]"]')?.value || 0);
    const discountPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value || 0);
    
    const amount = qty * rate;
    const discountAmount = (amount * discountPercent) / 100;
    const amountAfterDiscount = amount - discountAmount;
    
    const cgstAmount = (amountAfterDiscount * parseFloat(itemData.cgst_percent || 0)) / 100;
    const sgstAmount = (amountAfterDiscount * parseFloat(itemData.sgst_percent || 0)) / 100;
    const cessAmount = (amountAfterDiscount * parseFloat(itemData.cess_percent || 0)) / 100;
    
    document.getElementById('calc_cgst_amount').value = cgstAmount.toFixed(2);
    document.getElementById('calc_sgst_amount').value = sgstAmount.toFixed(2);
    document.getElementById('calc_cess_amount').value = cessAmount.toFixed(2);
    
    // Update other calculation fields
    const totalTaxPercent = parseFloat(itemData.cgst_percent || 0) + parseFloat(itemData.sgst_percent || 0) + parseFloat(itemData.cess_percent || 0);
    document.getElementById('calc_sc_percent').value = '0.000';
    document.getElementById('calc_tax_percent').value = totalTaxPercent.toFixed(3);
    document.getElementById('calc_excise').value = '0.00';
    document.getElementById('calc_tsr').value = '0.00';
}

// Update Additional Details Section with selected row's data
function updateAdditionalDetails(rowIndex) {
    console.log('updateAdditionalDetails called for row:', rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) {
        console.log('Row not found in updateAdditionalDetails:', rowIndex);
        return;
    }
    
    const itemData = JSON.parse(row.dataset.itemData || '{}');
    console.log('Item data for additional details:', itemData);
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value || 0);
    const rate = parseFloat(row.querySelector('input[name*="[sale_rate]"]')?.value || 0);
    const discountPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value || 0);
    
    // Calculate individual row values
    const amount = qty * rate;
    const discountAmount = (amount * discountPercent) / 100;
    const amountAfterDiscount = amount - discountAmount;
    
    // Calculate tax amounts
    const cgstPercent = parseFloat(itemData.cgst_percent || 0);
    const sgstPercent = parseFloat(itemData.sgst_percent || 0);
    const cessPercent = parseFloat(itemData.cess_percent || 0);
    
    const cgstAmount = (amountAfterDiscount * cgstPercent) / 100;
    const sgstAmount = (amountAfterDiscount * sgstPercent) / 100;
    const cessAmount = (amountAfterDiscount * cessPercent) / 100;
    const totalTaxAmount = cgstAmount + sgstAmount + cessAmount;
    const netAmount = amountAfterDiscount + totalTaxAmount;
    
    // Update additional fields with this row's data
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '';
    document.getElementById('location').value = itemData.location || '';
    
    // Fetch total quantity from all batches for this item
    const itemId = itemData.item_id || row.getAttribute('data-item-id');
    if (itemId) {
        fetchTotalBatchQuantity(itemId);
    } else {
        // If no item ID, just show current row quantity
        document.getElementById('clQty').value = qty.toFixed(0);
    }
    
    // Update detailed calculation fields
    document.getElementById('addl_nt_amount').value = amount.toFixed(2);
    document.getElementById('addl_sc_amount').value = '0.00';
    document.getElementById('addl_dis_amount').value = discountAmount.toFixed(2);
    document.getElementById('addl_scm_percent').value = '0.00';
    document.getElementById('addl_scm_amount').value = '0.00';
    document.getElementById('addl_tax_amount').value = totalTaxAmount.toFixed(2);
    document.getElementById('addl_net_amount').value = netAmount.toFixed(2);
    document.getElementById('addl_sub_total').value = amountAfterDiscount.toFixed(2);
    document.getElementById('addl_volume').value = '0';
    document.getElementById('addl_company').value = itemData.company_name || '';
    document.getElementById('addl_scm_flag').value = '0';
    document.getElementById('addl_scm_value').value = '0';
    document.getElementById('addl_srino').value = '1';
}

// Fetch total quantity from all batches for an item
function fetchTotalBatchQuantity(itemId) {
    const url = `{{ url('/admin/api/item-batches') }}/${itemId}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Handle different response formats
            let batches = [];
            if (Array.isArray(data)) {
                batches = data;
            } else if (data.success && Array.isArray(data.batches)) {
                batches = data.batches;
            } else if (data.batches && Array.isArray(data.batches)) {
                batches = data.batches;
            }
            
            // Sum up total quantity from all batches
            let totalQty = 0;
            batches.forEach(batch => {
                const batchQty = parseFloat(batch.total_qty || batch.qty || 0);
                totalQty += batchQty;
            });
            
            // Update CL QTY field with total from all batches
            document.getElementById('clQty').value = totalQty > 0 ? totalQty.toFixed(0) : '0';
        })
        .catch(error => {
            console.error('Error fetching batch quantities:', error);
            // On error, show 0
            document.getElementById('clQty').value = '0';
        });
}

// Calculate Row Amount - Enhanced version
function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value || 0);
    const rate = parseFloat(row.querySelector('input[name*="[sale_rate]"]')?.value || 0);
    const discountPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value || 0);
    
    // Calculate amount
    const amount = qty * rate;
    
    // Update amount field
    const amountInput = row.querySelector('input[name*="[amount]"]');
    if (amountInput) {
        amountInput.value = amount.toFixed(2);
    }
    
    // Update calculations if this row is selected
    if (selectedRowIndex === rowIndex) {
        updateCalculationSection(rowIndex);
        updateAdditionalDetails(rowIndex);
    }
    
    // Recalculate summary totals
    recalculateTotals();
}

// Remove Row - Enhanced version
function removeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.remove();
        
        // Clear selection if this row was selected
        if (selectedRowIndex === rowIndex) {
            selectedRowIndex = null;
            clearCalculationSection();
        }
        
        recalculateTotals();
    }
}

// Clear calculation section
function clearCalculationSection() {
    document.getElementById('calc_hsn_code').value = '---';
    document.getElementById('calc_cgst_percent').value = '0';
    document.getElementById('calc_sgst_percent').value = '0';
    document.getElementById('calc_cess_percent').value = '0';
    document.getElementById('calc_cgst_amount').value = '0.00';
    document.getElementById('calc_sgst_amount').value = '0.00';
    document.getElementById('calc_cess_amount').value = '0.00';
    document.getElementById('calc_sc_percent').value = '0.000';
    document.getElementById('calc_tax_percent').value = '0.000';
    document.getElementById('calc_excise').value = '0.00';
    document.getElementById('calc_tsr').value = '0.00';
}

// Clear additional details section
function clearAdditionalDetails() {
    document.getElementById('packing').value = '';
    document.getElementById('unit').value = '';
    document.getElementById('clQty').value = '0';
    document.getElementById('location').value = '';
    document.getElementById('hsAmount').value = '0.00';
    
    // Clear detailed calculation fields
    document.getElementById('addl_nt_amount').value = '0.00';
    document.getElementById('addl_sc_amount').value = '0.00';
    document.getElementById('addl_dis_amount').value = '0.00';
    document.getElementById('addl_scm_percent').value = '0.00';
    document.getElementById('addl_scm_amount').value = '0.00';
    document.getElementById('addl_tax_amount').value = '0.00';
    document.getElementById('addl_net_amount').value = '0.00';
    document.getElementById('addl_sub_total').value = '0.00';
    document.getElementById('addl_volume').value = '0';
    document.getElementById('addl_company').value = '';
    document.getElementById('addl_scm_flag').value = '0';
    document.getElementById('addl_scm_value').value = '0';
    document.getElementById('addl_srino').value = '1';
}

// Recalculate row when values change - Updated version
function recalculateRow(input, index) {
    calculateRowAmount(index);
}

// Recalculate all totals
function recalculateTotals() {
    console.log('recalculateTotals called');
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    console.log('Total rows found:', rows.length);
    
    let totalAmount = 0;
    let totalDiscount = 0;
    let totalCgst = 0;
    let totalSgst = 0;
    let totalCess = 0;
    let totalPacking = 0;
    let totalUnit = 0;
    let totalClQty = 0;
    
    // Calculate totals from all rows
    rows.forEach((row, idx) => {
        // Use more flexible selectors to find inputs
        const qtyInput = row.querySelector('input[name*="[qty]"]');
        const rateInput = row.querySelector('input[name*="[sale_rate]"]');
        const discountInput = row.querySelector('input[name*="[dis_percent]"]');
        const amountInput = row.querySelector('input[name*="[amount]"]');
        
        // Get tax percentages from hidden fields or dataset
        let itemData = {};
        try {
            itemData = JSON.parse(row.dataset.itemData || '{}');
        } catch (e) {
            console.error('Error parsing item data for row:', idx, e);
            itemData = {};
        }
        const cgstPercent = parseFloat(itemData.cgst_percent || 0);
        const sgstPercent = parseFloat(itemData.sgst_percent || 0);
        const cessPercent = parseFloat(itemData.cess_percent || 0);
        
        console.log(`Row ${idx}: qty=${qtyInput?.value}, rate=${rateInput?.value}, discount=${discountInput?.value}`);
        
        if (qtyInput && rateInput) {
            const qty = parseFloat(qtyInput.value || 0);
            const rate = parseFloat(rateInput.value || 0);
            const discountPercent = parseFloat(discountInput?.value || 0);
            
            // Amount = Qty * Rate
            const amount = qty * rate;
            totalAmount += amount;
            
            // Discount = Amount * Discount% / 100
            const discountAmount = (amount * discountPercent) / 100;
            totalDiscount += discountAmount;
            
            // Calculate GST on amount after discount
            const amountAfterDiscount = amount - discountAmount;
            totalCgst += (amountAfterDiscount * cgstPercent) / 100;
            totalSgst += (amountAfterDiscount * sgstPercent) / 100;
            totalCess += (amountAfterDiscount * cessPercent) / 100;
            
            // Accumulate packing, unit, qty from item data
            totalPacking += parseFloat(itemData.packing || 0);
            totalUnit += parseFloat(itemData.unit || 0);
            totalClQty += qty;
            
            console.log(`Row ${idx} calculated: amount=${amount}, discount=${discountAmount}, cgst=${(amountAfterDiscount * cgstPercent) / 100}`);
        }
    });
    
    // Update GST amounts in calculation section
    document.getElementById('calc_cgst_amount').value = totalCgst.toFixed(2);
    document.getElementById('calc_sgst_amount').value = totalSgst.toFixed(2);
    document.getElementById('calc_cess_amount').value = totalCess.toFixed(2);
    
    // Calculate summary
    const ntAmount = totalAmount; // NT Amount = sum of amounts
    const ftAmount = ntAmount; // FT Amount = NT Amount
    const disAmount = totalDiscount; // Discount from items
    const taxAmount = totalCgst + totalSgst + totalCess; // Total tax
    const netAmount = ntAmount - disAmount + taxAmount; // Net = NT - Discount + Tax
    
    // Update summary fields
    document.getElementById('ntAmount').value = ntAmount.toFixed(2);
    document.getElementById('scAmount').value = '0.00'; // SC Amount
    document.getElementById('ftAmount').value = ftAmount.toFixed(2);
    document.getElementById('disAmount').value = disAmount.toFixed(2);
    document.getElementById('scmAmount').value = '0.00'; // Scheme Amount
    document.getElementById('taxAmount').value = taxAmount.toFixed(2);
    document.getElementById('netAmount').value = netAmount.toFixed(2);
    document.getElementById('scmPercent').value = '0.00'; // Scheme Percent
    document.getElementById('tcsAmount').value = '0.00'; // TCS Amount
    
    // Update additional fields
    document.getElementById('clQty').value = totalClQty.toFixed(0);
    
    // Update additional detail fields
    document.getElementById('addl_nt_amount').value = ntAmount.toFixed(2);
    document.getElementById('addl_sc_amount').value = '0.00';
    document.getElementById('addl_dis_amount').value = disAmount.toFixed(2);
    document.getElementById('addl_scm_percent').value = '0.00';
    document.getElementById('addl_scm_amount').value = '0.00';
    document.getElementById('addl_tax_amount').value = taxAmount.toFixed(2);
    document.getElementById('addl_net_amount').value = netAmount.toFixed(2);
    document.getElementById('addl_sub_total').value = (ntAmount - disAmount).toFixed(2);
    document.getElementById('addl_volume').value = '0';
    document.getElementById('addl_scm_flag').value = '0';
    document.getElementById('addl_scm_value').value = '0';
    document.getElementById('addl_srino').value = '1';
    
    console.log('Summary updated:', {
        ntAmount: ntAmount.toFixed(2),
        disAmount: disAmount.toFixed(2),
        taxAmount: taxAmount.toFixed(2),
        netAmount: netAmount.toFixed(2)
    });
}

// Save Transaction - Entry point for Save button
function saveTransaction() {
    // Validate that we have items
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    if (rows.length === 0) {
        showAlert('error', 'Please add items to return before saving.');
        return;
    }
    
    // Get customer ID
    const customerId = document.getElementById('customerSelect').value;
    if (!customerId) {
        showAlert('error', 'Please select a customer.');
        return;
    }
    
    // Store net amount globally for adjustment modal
    window.netAmount = parseFloat(document.getElementById('netAmount').value || 0);
    
    // Show credit note modal
    showCreditNoteModal();
}

// Credit Note Modal keyboard helper
function initCreditNoteKeyboard() {
    const modal = document.getElementById('creditNoteModal');
    if (!modal) return;
    const buttons = Array.from(modal.querySelectorAll('.credit-note-options button'));
    if (!buttons.length) return;

    let activeIndex = 0;
    const setActive = (index) => {
        activeIndex = index;
        buttons.forEach((btn, i) => {
            btn.classList.toggle('kb-active', i === activeIndex);
        });
        const activeBtn = buttons[activeIndex];
        if (activeBtn) {
            activeBtn.focus();
        }
    };

    // Ensure buttons update highlight when focused via mouse/tab
    buttons.forEach((btn, idx) => {
        btn.addEventListener('focus', () => setActive(idx));
        btn.addEventListener('mouseenter', () => setActive(idx));
    });

    setTimeout(() => setActive(0), 50);

    if (window.creditNoteKeyHandler) {
        document.removeEventListener('keydown', window.creditNoteKeyHandler, true);
    }

    window.creditNoteKeyHandler = function(e) {
        if (!modal.classList.contains('show')) return;

        if (e.key === 'ArrowRight') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            setActive((activeIndex + 1) % buttons.length);
            return;
        }

        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            setActive((activeIndex - 1 + buttons.length) % buttons.length);
            return;
        }

        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const activeBtn = buttons[activeIndex];
            if (activeBtn) activeBtn.click();
            return;
        }

        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeCreditNoteModal();
        }
    };

    document.addEventListener('keydown', window.creditNoteKeyHandler, true);
}

// Show Credit Note Modal
function showCreditNoteModal() {
    const modal = document.getElementById('creditNoteModal');
    if (!modal) return;
    modal.classList.add('show');
    modal.style.display = 'block';
    initCreditNoteKeyboard();
}

// Close Credit Note Modal
function closeCreditNoteModal() {
    const modal = document.getElementById('creditNoteModal');
    modal.classList.remove('show');
    if (window.creditNoteKeyHandler) {
        document.removeEventListener('keydown', window.creditNoteKeyHandler, true);
    }
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Save Without Credit Note
function saveWithoutCreditNote() {
    closeCreditNoteModal();
    submitTransaction(false);
}

// Save With Credit Note
function saveWithCreditNote() {
    closeCreditNoteModal();
    
    const customerId = document.getElementById('customerSelect').value;
    if (!customerId) {
        showAlert('error', 'Please select a customer first');
        return;
    }
    
    fetchCustomerSales(customerId);
}

// Fetch Customer Sales for Adjustment
function fetchCustomerSales(customerId) {
    showAlert('info', 'Loading customer invoices...');
    
    fetch('{{ route("admin.sale-return.customer-invoices") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            customer_id: customerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAdjustmentModal(data.invoices, window.netAmount);
        } else {
            showAlert('error', data.message || 'Failed to load invoices.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while loading invoices.');
    });
}

// Submit Transaction (Original Logic)
function submitTransaction(withCreditNote = false, adjustments = []) {
    const form = document.getElementById('saleReturnTransactionForm');
    const formData = new FormData(form);
    
    // Add credit note data
    formData.append('with_credit_note', withCreditNote);
    formData.append('adjustments', JSON.stringify(adjustments));
    
    // Check if this is an update or new entry
    const isUpdate = window.currentSaleReturnId ? true : false;
    const url = isUpdate 
        ? `{{ url('/admin/sale-return/update') }}/${window.currentSaleReturnId}`
        : '{{ route("admin.sale-return.store") }}';
    
    // Show loading
    showAlert('info', isUpdate ? 'Updating sale return...' : 'Saving sale return transaction...');
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    // Submit via AJAX
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || (isUpdate ? 'Sale return updated successfully!' : 'Sale return saved successfully!'));
            // Reload page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('error', data.message || 'Failed to save sale return.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while saving the sale return.');
    });
}

// Open Adjustment Modal
function openAdjustmentModal() {
    // Validate that we have items
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    if (rows.length === 0) {
        showAlert('error', 'Please add items to return before saving.');
        return;
    }
    
    // Get customer ID
    const customerId = document.getElementById('customerSelect').value;
    if (!customerId) {
        showAlert('error', 'Please select a customer.');
        return;
    }
    
    // Get net amount to adjust
    const netAmount = parseFloat(document.getElementById('netAmount').value || 0);
    
    // Fetch customer's past invoices
    showAlert('info', 'Loading customer invoices...');
    
    fetch('{{ route("admin.sale-return.customer-invoices") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            customer_id: customerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAdjustmentModal(data.invoices, netAmount);
        } else {
            showAlert('error', data.message || 'Failed to load invoices.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while loading invoices.');
    });
}

// Show Adjustment Modal
function showAdjustmentModal(invoices, returnAmount) {
    // Check if we're editing an existing sale return
    const saleReturnId = window.currentSaleReturnId || null;
    
    // Function to render modal content with optional pre-filled adjustments
    const renderModalContent = (existingAdjustments = {}) => {
        const modalHTML = `
            <div class="adjustment-modal-backdrop" id="adjustmentModalBackdrop"></div>
            <div class="adjustment-modal" id="adjustmentModal">
                <div class="adjustment-modal-content">
                    <div class="adjustment-modal-header">
                        <h5 class="adjustment-modal-title">Sale Return Credit Note Adjustment</h5>
                    </div>
                    <div class="adjustment-modal-body">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered" style="font-size: 11px; margin-bottom: 0;">
                                <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                                    <tr>
                                        <th style="width: 60px; text-align: center;">SR.NO.</th>
                                        <th style="width: 150px; text-align: center;">TRANS NO.</th>
                                        <th style="width: 120px; text-align: center;">DATE</th>
                                        <th style="width: 120px; text-align: right;">BILL AMT.</th>
                                        <th style="width: 120px; text-align: center;">ADJUSTED</th>
                                        <th style="width: 120px; text-align: right;">BALANCE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${invoices.map((invoice, index) => {
                                        const billAmount = parseFloat(invoice.bill_amount || 0);
                                        const currentBalance = parseFloat(invoice.balance || invoice.bill_amount || 0);
                                        const preFilledAmount = existingAdjustments[invoice.id] || '';
                                        // For modification: Adj Balance = current balance + pre-filled (what was previously adjusted from this invoice)
                                        const adjBalance = currentBalance + parseFloat(preFilledAmount || 0);
                                        return `
                                        <tr>
                                            <td style="text-align: center;">${index + 1}</td>
                                            <td style="text-align: center;">${invoice.trans_no}</td>
                                            <td style="text-align: center;">${invoice.date}</td>
                                            <td style="text-align: right; font-weight: bold; color: #0d6efd;">₹ ${adjBalance.toFixed(2)}</td>
                                            <td style="text-align: center;">
                                                <input type="number" class="form-control form-control-sm adjustment-input" 
                                                       id="adj_${invoice.id}" 
                                                       data-invoice-id="${invoice.id}"
                                                       data-adj-balance="${adjBalance}"
                                                       data-balance="${currentBalance}"
                                                       value="${preFilledAmount}" 
                                                       min="0" 
                                                       max="${adjBalance}"
                                                       step="0.01"
                                                       onchange="updateAdjustmentBalance()"
                                                       style="width: 100px; height: 24px; padding: 2px 4px; font-size: 11px; text-align: right;">
                                            </td>
                                            <td style="text-align: right;" id="balance_${invoice.id}"><span style="color: #28a745;">₹ ${currentBalance.toFixed(2)}</span></td>
                                        </tr>
                                    `}).join('')}
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-weight: bold; color: #dc3545;">EXIT : &lt;ESC &gt;</span>
                                <span style="font-weight: bold; font-size: 16px; color: #0d6efd;">
                                    NET AMOUNT TO ADJUST (Rs) : <span id="adjustmentBalance">₹ ${returnAmount.toFixed(2)}</span>
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <label style="font-weight: bold; color: #495057;">Auto Adjust Amount:</label>
                                <input type="number" id="autoAdjustAmount" class="form-control form-control-sm" 
                                       style="width: 120px;" step="0.01" placeholder="Enter amount"
                                       value="${returnAmount.toFixed(2)}"
                                       onchange="autoDistributeAmount()">
                                <button type="button" class="btn btn-info btn-sm" onclick="autoDistributeAmount()">
                                    <i class="bi bi-magic me-1"></i>Auto Distribute
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="adjustment-modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustmentModal()">Cancel</button>
                        <button type="button" class="btn btn-success btn-sm" onclick="saveAdjustment()">
                            <i class="bi bi-check-circle"></i> Save & Submit
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('adjustmentModal');
        if (existingModal) existingModal.remove();
        const existingBackdrop = document.getElementById('adjustmentModalBackdrop');
        if (existingBackdrop) existingBackdrop.remove();
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Store return amount
        window.returnAmount = returnAmount;
        
        // Show modal with animation
        setTimeout(() => {
            document.getElementById('adjustmentModalBackdrop').classList.add('show');
            document.getElementById('adjustmentModal').classList.add('show');
            const hasExisting = Object.keys(existingAdjustments || {}).length > 0;
            prefillAdjustmentFirstRow(hasExisting);
            initAdjustmentModalKeyboard();
        }, 10);
        
        // Update balance display after pre-filling
        if (Object.keys(existingAdjustments).length > 0) {
            updateAdjustmentBalance();
        }
        
        // Add ESC / Ctrl+S key listener
        document.addEventListener('keydown', handleAdjustmentEsc);
    };
    
    // If editing existing sale return, fetch existing adjustments
    if (saleReturnId) {
        fetch(`{{ url('/admin/sale-return') }}/${saleReturnId}/adjustments`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.adjustments) {
                // Create a map of sale_transaction_id => adjusted_amount
                const adjustmentsMap = {};
                data.adjustments.forEach(adj => {
                    adjustmentsMap[adj.sale_transaction_id] = adj.adjusted_amount;
                });
                renderModalContent(adjustmentsMap);
            } else {
                renderModalContent();
            }
        })
        .catch(error => {
            console.error('Error fetching existing adjustments:', error);
            renderModalContent(); // Show modal anyway without pre-filled data
        });
    } else {
        // New transaction, no pre-filled data
        renderModalContent();
    }
}

// Handle ESC key for adjustment modal
function handleAdjustmentEsc(e) {
    if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        closeAdjustmentModal();
        return;
    }
    const isCtrlS = (e.key === 's' || e.key === 'S') && (e.ctrlKey || e.metaKey);
    if (isCtrlS) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        saveAdjustment();
    }
}

function prefillAdjustmentFirstRow(hasExisting = false) {
    const inputs = Array.from(document.querySelectorAll('.adjustment-input'));
    if (!inputs.length) return;
    const firstInput = inputs[0];

    if (hasExisting) {
        firstInput.focus();
        firstInput.select();
        return;
    }

    const balance = parseFloat(firstInput.getAttribute('data-adj-balance') || firstInput.getAttribute('data-balance') || 0);
    const desired = parseFloat(window.returnAmount || 0);
    const prefill = Math.min(desired, balance);
    firstInput.value = prefill.toFixed(2);
    firstInput.placeholder = desired.toFixed(2);
    updateAdjustmentBalance();
}

function getRemainingAdjustment() {
    const inputs = document.querySelectorAll('.adjustment-input');
    let totalAdjusted = 0;
    inputs.forEach(input => {
        totalAdjusted += parseFloat(input.value || 0);
    });
    return Math.max(0, (parseFloat(window.returnAmount || 0) - totalAdjusted));
}

function initAdjustmentModalKeyboard() {
    const inputs = Array.from(document.querySelectorAll('.adjustment-input'));
    if (!inputs.length) return;

    const setActive = (input) => {
        inputs.forEach(i => i.classList.remove('kb-active'));
        if (input) {
            input.classList.add('kb-active');
            input.focus();
            input.select();
        }
    };

    inputs.forEach((input, idx) => {
        input.addEventListener('focus', () => setActive(input));
        input.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const next = inputs[idx + 1];
                if (next) {
                    const currentValue = parseFloat(input.value || 0);
                    const remaining = getRemainingAdjustment();
                    const nextBalance = parseFloat(next.getAttribute('data-adj-balance') || next.getAttribute('data-balance') || 0);

                    if (remaining <= 0 && currentValue > 0) {
                        const moveValue = Math.min(currentValue, nextBalance);
                        next.value = moveValue.toFixed(2);
                        input.value = '0.00';
                    } else {
                        const nextValue = Math.min(remaining, nextBalance);
                        next.value = nextValue.toFixed(2);
                    }

                    updateAdjustmentBalance();
                    setActive(next);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prev = inputs[idx - 1];
                if (prev) {
                    const currentValue = parseFloat(input.value || 0);
                    const prevBalance = parseFloat(prev.getAttribute('data-adj-balance') || prev.getAttribute('data-balance') || 0);

                    if (currentValue > 0) {
                        const moveValue = Math.min(currentValue, prevBalance);
                        prev.value = moveValue.toFixed(2);
                        input.value = '0.00';
                        updateAdjustmentBalance();
                    }

                    setActive(prev);
                }
            }
        });
    });

    setActive(inputs[0]);
}

// Update adjustment balance
function updateAdjustmentBalance() {
    const inputs = document.querySelectorAll('.adjustment-input');
    let totalAdjusted = 0;
    
    inputs.forEach(input => {
        let adjusted = parseFloat(input.value || 0);
        const invoiceId = input.getAttribute('data-invoice-id');
        const adjBalance = parseFloat(input.getAttribute('data-adj-balance'));
        
        // Prevent adjusting more than adjusted balance
        if (adjusted > adjBalance) {
            input.value = adjBalance.toFixed(2);
            adjusted = adjBalance;
        }
        
        totalAdjusted += adjusted;
        
        // Calculate new balance: Balance = Adjusted Balance - Adjusted Amount
        const newBalance = adjBalance - adjusted;
        const balanceCell = document.getElementById(`balance_${invoiceId}`);
        if (balanceCell) {
            // Color code the balance based on value
            if (newBalance < 0) {
                balanceCell.innerHTML = `<span style="color: #dc3545; font-weight: bold;">₹ ${newBalance.toFixed(2)}</span>`;
            } else if (newBalance === 0) {
                balanceCell.innerHTML = `<span style="color: #28a745; font-weight: bold;">₹ ${newBalance.toFixed(2)}</span>`;
            } else {
                balanceCell.innerHTML = `<span style="color: #28a745;">₹ ${newBalance.toFixed(2)}</span>`;
            }
        }
    });
    
    // Update remaining balance
    const remainingBalance = window.returnAmount - totalAdjusted;
    const adjustmentBalanceEl = document.getElementById('adjustmentBalance');
    adjustmentBalanceEl.textContent = `₹ ${remainingBalance.toFixed(2)}`;
    
    // Change color if over-adjusted
    const balanceSpan = adjustmentBalanceEl.parentElement;
    if (remainingBalance < 0) {
        balanceSpan.style.color = '#dc3545';
    } else if (remainingBalance === 0) {
        balanceSpan.style.color = '#28a745';
    } else {
        balanceSpan.style.color = '#0d6efd';
    }
}

// Auto Distribute Amount Across Transactions
function autoDistributeAmount() {
    const totalAmount = parseFloat(document.getElementById('autoAdjustAmount').value || 0);
    
    if (totalAmount <= 0) {
        showAlert('warning', 'Please enter a valid amount to distribute');
        return;
    }
    
    // Clear all existing adjustments first
    document.querySelectorAll('.adjustment-input').forEach(input => {
        input.value = '';
    });
    
    // Get all transactions sorted by adjusted balance (highest first for better distribution)
    const inputs = Array.from(document.querySelectorAll('.adjustment-input'));
    const transactions = inputs.map(input => ({
        input: input,
        invoiceId: input.getAttribute('data-invoice-id'),
        balance: parseFloat(input.getAttribute('data-adj-balance'))
    })).filter(t => t.balance > 0).sort((a, b) => b.balance - a.balance);
    
    let remainingAmount = totalAmount;
    
    // Distribute amount across transactions
    transactions.forEach(transaction => {
        if (remainingAmount <= 0) return;
        
        const adjustAmount = Math.min(remainingAmount, transaction.balance);
        transaction.input.value = adjustAmount.toFixed(2);
        remainingAmount -= adjustAmount;
    });
    
    // If still amount remaining, show warning
    if (remainingAmount > 0) {
        showAlert('warning', `₹${remainingAmount.toFixed(2)} could not be distributed. Insufficient outstanding balance.`);
    } else {
        showAlert('success', `₹${totalAmount.toFixed(2)} distributed successfully across ${transactions.length} transaction(s)`);
    }
    
    // Update the balance display
    updateAdjustmentBalance();
}

// Close Adjustment Modal
function closeAdjustmentModal() {
    const modal = document.getElementById('adjustmentModal');
    const backdrop = document.getElementById('adjustmentModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'bounceOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
        backdrop.classList.remove('show');
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.remove();
        }
        if (backdrop) backdrop.remove();
    }, 300);
    
    document.removeEventListener('keydown', handleAdjustmentEsc);
}

// Save Adjustment and Submit Form
function saveAdjustment() {
    // Collect adjustment data
    const inputs = document.querySelectorAll('.adjustment-input');
    const adjustments = [];
    
    inputs.forEach(input => {
        const adjusted = parseFloat(input.value || 0);
        if (adjusted > 0) {
            adjustments.push({
                invoice_id: input.getAttribute('data-invoice-id'),
                adjusted_amount: adjusted
            });
        }
    });
    
    // Check if fully adjusted - parse the remaining balance text (remove ₹ symbol)
    const remainingBalanceText = document.getElementById('adjustmentBalance').textContent.replace('₹', '').trim();
    const remainingBalance = parseFloat(remainingBalanceText);
    if (remainingBalance != 0) {
        if (!confirm(`Balance remaining is Rs ${remainingBalance.toFixed(2)}. Do you want to continue?`)) {
            return;
        }
    }
    
    // Close modal
    closeAdjustmentModal();
    
    // Submit with credit note adjustments
    submitTransaction(true, adjustments);
}


// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateDayName();
    updateSeriesLabel();

    // Remove Select2 if it was initialized on header dropdowns
    if (window.$ && $.fn.select2) {
        ['#seriesSelect', '#customerSelect', '#salesmanSelect'].forEach(selector => {
            const $el = $(selector);
            if ($el.length && $el.data('select2')) {
                $el.select2('destroy');
                $el.removeClass('select2-hidden-accessible');
                $el.next('.select2-container').remove();
            }
        });
    }

    if (typeof initCustomerDropdown === 'function') {
        initCustomerDropdown();
    }

    const customerSelect = document.getElementById('customerSelect');
    if (customerSelect) {
        customerSelect.addEventListener('change', updateCustomerName);
        updateCustomerName();
    }
});

// Ctrl+S -> Update Sale Return
window.addEventListener('keydown', function(e) {
    const isCtrlS = (e.key === 's' || e.key === 'S') && (e.ctrlKey || e.metaKey);
    if (!isCtrlS || e.repeat) return;
    const modalOpen = !!document.querySelector(
        '.credit-note-modal.show, .adjustment-modal.show, #chooseItemsModal.show, #batchSelectionModal.show, .insert-orders-modal.show, .batch-modal.show, .create-batch-modal.show, .invoice-modal.show, #alertModal.show, .alert-modal.show'
    );
    if (modalOpen) return;
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    const updateBtn = document.getElementById('submitBtn');
    if (updateBtn) {
        updateBtn.click();
    } else if (typeof saveTransaction === 'function') {
        saveTransaction();
    }
}, true);

// Header keyboard handling: SR No -> load, Fixed Dis -> insert orders
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter' && e.keyCode !== 13) return;
    const active = document.activeElement;
    if (!active) return;

    const isBlockingModalOpen = !!document.querySelector(
        '.invoice-modal.show, .insert-orders-modal.show, .batch-modal.show, .create-batch-modal.show, .adjustment-modal.show, .credit-note-modal.show, #alertModal.show, .alert-modal.show'
    );
    if (isBlockingModalOpen) return;

    if (active.id === 'srNo') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const srNoValue = (active.value || '').trim();
        if (srNoValue) {
            window.kbFocusTableAfterLoad = true;
            searchSaleReturnBySRNo(srNoValue);
        } else {
            const fixed = document.getElementById('fixedDiscount');
            if (fixed) fixed.focus();
        }
    } else if (active.id === 'fixedDiscount') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        const insertBtn = document.getElementById('insertOrdersBtn');
        if (insertBtn) {
            insertBtn.click();
        } else if (typeof openInsertOrdersModal === 'function') {
            openInsertOrdersModal();
        }
    }
}, true);
</script>

<style>
/* Animation Keyframes */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

@keyframes zoomIn {
    from {
        transform: translate(-50%, -50%) scale(0.7);
        opacity: 0;
    }
    to {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
}

@keyframes zoomOut {
    from {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    to {
        transform: translate(-50%, -50%) scale(0.7);
        opacity: 0;
    }
}

@keyframes slideUp {
    from {
        transform: translate(-50%, -30%) scale(0.8);
        opacity: 0;
    }
    to {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
}

@keyframes slideDown {
    from {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    to {
        transform: translate(-50%, -30%) scale(0.8);
        opacity: 0;
    }
}

@keyframes bounceIn {
    0% {
        transform: translate(-50%, -50%) scale(0.3);
        opacity: 0;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.05);
    }
    70% {
        transform: translate(-50%, -50%) scale(0.9);
    }
    100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
}

@keyframes bounceOut {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.05);
    }
    100% {
        transform: translate(-50%, -50%) scale(0.3);
        opacity: 0;
    }
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Credit Note Modal Styles */
.credit-note-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10008;
    opacity: 0;
    animation: fadeIn 0.3s ease forwards;
}

.credit-note-modal.show {
    display: block;
    opacity: 1;
}

.credit-note-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 0;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 450px;
    animation: zoomIn 0.3s ease forwards;
}

.credit-note-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.credit-note-modal-header h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
}

.credit-note-modal-body {
    padding: 30px 25px;
    text-align: center;
}

.credit-note-modal-body p {
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 25px;
}

.credit-note-options {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.credit-note-options button {
    padding: 12px 35px;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.credit-note-options button:first-child {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.credit-note-options button:first-child:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(40, 167, 69, 0.4);
}

.credit-note-options button:last-child {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.credit-note-options button:last-child:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(108, 117, 125, 0.4);
}

.credit-note-close-btn {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.credit-note-close-btn:hover {
    opacity: 1;
}

/* Invoice Modal Styles */
.invoice-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9998;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: fadeIn 0.4s ease forwards;
}

.invoice-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.invoice-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 70%;
    max-width: 500px;
    z-index: 9999;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    animation: zoomIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.invoice-modal.show {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

.invoice-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.invoice-modal-header {
    padding: 1rem 1.5rem;
    background: #0d6efd;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #0b5ed7;
}

.invoice-modal-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.btn-close-modal {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: background 0.2s;
}

.btn-close-modal:hover {
    background: rgba(255, 255, 255, 0.2);
}

.invoice-modal-body {
    padding: 0;
    background: #fff;
    max-height: 300px;
    overflow-y: auto;
}

.invoice-modal-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Insert Orders Modal Styles */
.insert-orders-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: fadeIn 0.4s ease forwards;
}

.insert-orders-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.insert-orders-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 75%;
    max-width: 750px;
    z-index: 10001;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    animation: slideUp 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.insert-orders-modal.show {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

/* Item Selection Modal - Smaller and constrained to content area */
.item-selection-modal {
    width: 55% !important;
    max-width: 550px !important;
    max-height: 80vh;
    overflow-y: auto;
    /* Center in viewport but ensure it doesn't go beyond content area */
    left: 50% !important;
    top: 50% !important;
    transform: translate(-50%, -50%) scale(0.7);
    /* Ensure modal doesn't go beyond viewport edges */
    margin-left: 0;
    margin-top: 0;
}

.item-selection-modal.show {
    transform: translate(-50%, -50%) scale(1);
}

/* Ensure modal body content is scrollable if needed */
.item-selection-modal .insert-orders-modal-body {
    max-height: calc(80vh - 150px);
    overflow-y: auto;
    padding: 0.75rem !important;
}

/* Make modal header more compact */
.item-selection-modal .insert-orders-modal-header {
    padding: 0.75rem 1rem !important;
}

.item-selection-modal .insert-orders-modal-title {
    font-size: 1rem !important;
}

/* Make modal footer more compact */
.item-selection-modal .insert-orders-modal-footer {
    padding: 0.75rem 1rem !important;
}

/* Ensure table fits properly */
.item-selection-modal table {
    font-size: 9px !important;
}

.item-selection-modal td, .item-selection-modal th {
    padding: 4px 6px !important;
}

/* Responsive adjustments */
@media (max-width: 1400px) {
    .item-selection-modal {
        width: 50% !important;
        max-width: 500px !important;
    }
}

@media (max-width: 1200px) {
    .item-selection-modal {
        width: 60% !important;
        max-width: 550px !important;
    }
}

@media (max-width: 992px) {
    .item-selection-modal {
        width: 70% !important;
        max-width: 600px !important;
    }
}

.insert-orders-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.insert-orders-modal-header {
    padding: 1rem 1.5rem;
    background: #28a745;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #218838;
}

.insert-orders-modal-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.insert-orders-modal-body {
    padding: 1rem;
    background: #fff;
}

.insert-orders-modal-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Adjustment Modal Styles */
.adjustment-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10002;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: fadeIn 0.4s ease forwards;
}

.adjustment-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.adjustment-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 75%;
    max-width: 700px;
    z-index: 10003;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    animation: bounceIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.adjustment-modal.show {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

.adjustment-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.adjustment-modal-header {
    padding: 1rem 1.5rem;
    background: #0d6efd;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #0b5ed7;
}

.adjustment-modal-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.adjustment-modal-body {
    padding: 1rem;
    background: #fff;
}

.adjustment-modal-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Batch Modal Styles */
.batch-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10004;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: fadeIn 0.4s ease forwards;
}

.batch-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.batch-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 70%;
    max-width: 650px;
    z-index: 10005;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    animation: zoomIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.batch-modal.show {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

.batch-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.batch-modal-header {
    padding: 1rem 1.5rem;
    background: #28a745;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #218838;
}

.batch-modal-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.batch-modal-body {
    padding: 1rem;
    background: #fff;
}

.batch-modal-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Create Batch Modal Styles */
.create-batch-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10006;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: fadeIn 0.4s ease forwards;
}

.create-batch-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.create-batch-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 50%;
    max-width: 500px;
    z-index: 10007;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    animation: zoomIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.create-batch-modal.show {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

.create-batch-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.create-batch-modal-header {
    padding: 0.75rem 1rem;
    background: #f0f0f0;
    color: #333;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
}

.create-batch-modal-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.create-batch-modal-body {
    padding: 1rem;
    background: #fff;
}

.create-batch-modal-footer {
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Focus ring (blue border) */
.form-control:focus,
select:focus,
input:focus {
    outline: 2px solid #0d6efd !important;
    outline-offset: 1px;
    box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
}

.form-control:focus:not(:focus-visible),
select:focus:not(:focus-visible),
input:focus:not(:focus-visible) {
    outline: none !important;
    box-shadow: none !important;
}

#itemsTableBody tr:focus-within {
    background-color: #e7f3ff !important;
}

#itemsTableBody tr:focus-within td {
    background-color: #e7f3ff !important;
}
</style>

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'sale-return',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => true,
    'batchModalId' => 'batchSelectionModal',
])

@include('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'sale-return',
    'showOnlyAvailable' => false,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])

@endsection
