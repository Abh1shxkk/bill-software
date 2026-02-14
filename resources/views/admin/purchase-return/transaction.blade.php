@extends('layouts.admin')

@section('title', 'Purchase Return Transaction')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    input:focus {
        box-shadow: none !important;
    }

    .prt .header-section-prt {
        background: white;
        border: 1px solid #dee2e6;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 4px;
    }

    .prt .header-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 6px;
    }

    .prt .field-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .prt .inner-card-prt {
        background: #e8f4f8;
        border: 1px solid #b8d4e0;
        padding: 8px;
        border-radius: 3px;
    }

    .prt .readonly-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
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

    /* Searchable Dropdown Styles (Custom Supplier Dropdown) */
    .searchable-dropdown {
        position: relative;
    }

    .searchable-dropdown-input {
        width: 100%;
        cursor: text;
    }

    .searchable-dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 4px 4px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .searchable-dropdown-list .dropdown-item {
        padding: 6px 10px;
        cursor: pointer;
        font-size: 11px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.15s;
    }

    .searchable-dropdown-list .dropdown-item:last-child {
        border-bottom: none;
    }

    .searchable-dropdown-list .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .searchable-dropdown-list .dropdown-item.highlighted {
        background-color: #007bff !important;
        color: white !important;
    }

    .searchable-dropdown-list .dropdown-item.selected {
        background-color: #e7f3ff;
        font-weight: 600;
    }

    .searchable-dropdown-list .dropdown-item.hidden {
        display: none;
    }

    /* Keyboard focus indicator (blue border like sale module) */
    .prt .form-control:focus,
    .prt .searchable-dropdown-input:focus,
    .prt input:focus {
        outline: 2px solid #0d6efd !important;
        outline-offset: 1px;
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
    }

    .prt .form-control:focus:not(:focus-visible),
    .prt .searchable-dropdown-input:focus:not(:focus-visible),
    .prt input:focus:not(:focus-visible) {
        outline: none !important;
        box-shadow: none !important;
    }
</style>
@endpush

@section('content')
    <section class="prt py-5">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-cart-plus me-2"></i> Purchase Return
                        Transaction
                    </h4>
                    <div class="text-muted small">Create new purchase return transaction</div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded">
                <div class="card-body">
                    <form id="prtrans" method="POST" autocomplete="off">

                        <!-- Header Section -->
                        <div class="header-section-prt">
                            <!-- Row 1 -->
                            <div class="d-flex gap-3 mb-2">
                                <!-- Left Side - Date, Day, T.No, and Insert Orders stacked vertically -->
                                <div style="width: 200px;">
                                    <div class="field-group mb-2">
                                        <label style="width: 50px;">Date:</label>
                                        <input type="date" id="return_date" name="return_date" class="form-control" style="width: 140px;" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="field-group mb-2">
                                        <label style="width: 50px;">Day:</label>
                                        <input type="text" id="day_name" class="form-control readonly-field" style="width: 140px;" value="{{ date('l') }}" readonly>
                                    </div>
                                    <div class="field-group mb-2">
                                        <label style="width: 50px;">T. No.:</label>
                                        <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" style="width: 140px;" readonly>
                                    </div>
                                    <div>
                                        <button type="button" id="insertOrderBtn" class="btn btn-sm btn-info" style="width: 100%;">
                                            <i class="bi bi-list-check"></i> Insert Orders
                                        </button>
                                    </div>
                                </div>

                                <!-- Right Side - Inner Card prt -->
                                <div class="inner-card-prt flex-grow-1 overflow-hidden">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <div class="field-group">
                                                <label style="width: 100px;">Supplier :</label>
                                                <div class="searchable-dropdown" id="supplierDropdownWrapper" style="position: relative; width: 100%;">
                                                    <input type="text"
                                                           class="form-control searchable-dropdown-input"
                                                           id="supplierSearchInput"
                                                           placeholder="Type to search supplier..."
                                                           autocomplete="off"
                                                           style="width: 100%;">
                                                    <input type="hidden" name="supplier_id" id="supplierSelect" value="">
                                                    <div class="searchable-dropdown-list" id="supplierDropdownList" style="display: none;">
                                                        <div class="dropdown-item" data-value="" data-name="" data-code="">Select Supplier</div>
                                                        @foreach($suppliers as $supplier)
                                                            <div class="dropdown-item"
                                                                 data-value="{{ $supplier->supplier_id }}"
                                                                 data-name="{{ $supplier->name }}"
                                                                 data-code="{{ $supplier->code ?? '' }}">
                                                                {{ $supplier->code ?? '' }} {{ $supplier->code ? '-' : '' }} {{ $supplier->name }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="field-group">
                                                <label style="width: 80px;">Inv. No.:</label>
                                                <input type="text" class="form-control" id="invoice_no" name="invoice_no">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="field-group">
                                                <label>Date</label>
                                                <input type="date" class="form-control" id="invoice_date" name="invoice_date">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-1">
                                        <div class="col-md-5">
                                            <div class="field-group">
                                                <label style="width: 100px;">GST Vno :</label>
                                                <input type="text" class="form-control" id="gst_vno">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="field-group">
                                                <label style="width: 100px;">Remarks :</label>
                                                <input type="text" class="form-control" id="remarks">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-1">
                                        <div class="col-md-2">
                                            <div class="field-group">
                                                <label>Tax:</label>
                                                <input type="text" class="form-control" id="tax_flag" value="Y" maxlength="1"
                                                    style="width: 50px;">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="field-group">
                                                <label style="width: 80px;">Rate Diff :</label>
                                                <input type="text" class="form-control" id="rate_diff" value="N" maxlength="1"
                                                    onkeydown="return handleRateDiffKeydown(event);"
                                                    style="width: 50px;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="field-group">
                                                <label style="width: 250px;"><a class="text-decoration-none" href="#">Show Purchase Inv.(F2)</a></label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="bg-white border rounded p-2 mb-2">
                            <div class="table-responsive" style="overflow-y: auto; max-height: 310px;" id="itemsTableContainer">
                                <table class="table table-bordered table-compact">
                                    <thead style="position: sticky; top: 0; background: #9999cc; color: #000; z-index: 10;">
                                        <tr>
                                            <th style="width: 80px;">Item Code</th>
                                            <th style="width: 200px;">Item Name</th>
                                            <th style="width: 80px;">Batch</th>
                                            <th style="width: 60px;">Exp.</th>
                                            <th style="width: 60px;">Qty.</th>
                                            <th style="width: 60px;">F.Qty</th>
                                            <th style="width: 80px;">Pur. Rate</th>
                                            <th style="width: 60px;">Dis.%</th>
                                            <th style="width: 80px;">F.T. Rate</th>
                                            <th style="width: 90px;">F.T. Amt.</th>
                                            <th style="width: 50px; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <!-- Items will be added dynamically -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- Add Row Button -->
                            <div class="text-center mt-2">
                                <button type="button" class="btn btn-sm btn-success" id="addRowBtn" onclick="addNewRow()">
                                    <i class="fas fa-plus-circle"></i> Add Row
                                </button>
                            </div>
                        </div>


                        <!-- Calculation Section -->
                        <div class="bg-white border rounded p-2 mb-2" style="overflow: hidden;">
                            <div class="d-flex flex-wrap align-items-center gap-3" style="font-size: 11px;">
                                <!-- HSN Code -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>HSN:</strong></label>
                                    <input type="text" class="form-control readonly-field text-center" id="calc_hsn_code" readonly style="width: 80px; height: 26px; font-size: 11px;" value="---">
                                </div>
                                
                                <!-- CGST -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>CGST:</strong></label>
                                    <input type="text" class="form-control readonly-field text-center" id="calc_cgst_percent" readonly style="width: 45px; height: 26px; font-size: 11px;" value="0">
                                    <input type="text" class="form-control readonly-field text-end" id="calc_cgst_amount" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
                                </div>
                                
                                <!-- SGST -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>SGST:</strong></label>
                                    <input type="text" class="form-control readonly-field text-center" id="calc_sgst_percent" readonly style="width: 45px; height: 26px; font-size: 11px;" value="0">
                                    <input type="text" class="form-control readonly-field text-end" id="calc_sgst_amount" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
                                </div>
                                
                                <!-- Cess -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>Cess:</strong></label>
                                    <input type="text" class="form-control readonly-field text-center" id="calc_cess_percent" readonly style="width: 45px; height: 26px; font-size: 11px;" value="0">
                                    <input type="text" class="form-control readonly-field text-end" id="calc_cess_amount" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
                                </div>
                                
                                <!-- SC % -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>SC%</strong></label>
                                    <input type="number" class="form-control readonly-field text-center" id="calc_sc_percent" readonly style="width: 60px; height: 26px; font-size: 11px;" value="0.000">
                                </div>
                                
                                <!-- TAX % -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>TAX%</strong></label>
                                    <input type="number" class="form-control readonly-field text-center" id="calc_tax_percent" readonly style="width: 60px; height: 26px; font-size: 11px;" value="0.000">
                                </div>
                                
                                <!-- TSR -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>TSR</strong></label>
                                    <input type="text" class="form-control text-center readonly-field" id="calc_tsr" readonly style="width: 60px; height: 26px; font-size: 11px;" value="0.00">
                                </div>
                                
                                <!-- Excise -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>Excise</strong></label>
                                    <input type="text" class="form-control text-center readonly-field" id="calc_excise" readonly style="width: 60px; height: 26px; font-size: 11px;" value="0.00">
                                </div>
                                
                                <!-- WS Rate -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>WS Rate</strong></label>
                                    <input type="number" class="form-control readonly-field text-center" id="calc_ws_rate" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
                                </div>
                                
                                <!-- S.Rate -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>S.Rate</strong></label>
                                    <input type="number" class="form-control readonly-field text-center" id="calc_s_rate" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
                                </div>
                                
                                <!-- MRP -->
                                <div class="d-flex align-items-center gap-1">
                                    <label class="mb-0"><strong>MRP</strong></label>
                                    <input type="text" class="form-control text-center readonly-field" id="calc_mrp" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
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
                                    <label class="mb-0" style="font-weight: bold;">DIS AMT</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" name="dis_amount" id="disAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>

                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <label class="mb-0" style="font-weight: bold;">SCM AMT</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" name="scm_amount" id="scmAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>

                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <label class="mb-0" style="font-weight: bold;">Tax</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" name="tax_amount" id="taxAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>

                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <label class="mb-0" style="font-weight: bold;">INV AMT</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" name="inv_amount" id="invAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>

                            <!-- Row 2: 3 fields -->
                            <div class="d-flex align-items-center mt-2" style="font-size: 11px; gap: 10px;">
                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <label class="mb-0" style="font-weight: bold; white-space: nowrap;">Scm.%</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" name="scm_percent" id="scmPercent" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>

                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <label class="mb-0" style="font-weight: bold;">TCS</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" name="tcs_amount" id="tcsAmount" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>

                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <label class="mb-0" style="font-weight: bold;">DIS1 AMT</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" name="dis1_amount" id="dis1Amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Additional Fields Section -->
                        <div class="col-12 mb-4 bg-white border rounded p-2 mb-2">
                            <div class="row gx-3" style="font-size: 11px;">
                                <!-- col 1 - Packing, Unit, Cl.Qty, Lctn -->
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

                                <!-- col 2 - N.T.Amt, SC Amt, Dis.Amt, Hs.Amt -->
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
                                                <label class="mb-0" style="font-weight: bold; width: 80px;">Hs.Amt.</label>
                                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="hs_amount" id="hsAmount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- col 3 - Scm.Amt, Dis1.Amt, Tax Amt, Gross Tot -->
                                <div class="col-lg-2">
                                    <div class="row flex-column">
                                        <div class="col-lg-12">
                                            <div class="d-flex align-items-center mb-2">
                                                <label class="mb-0" style="font-weight: bold; width: 80px;">Scm.Amt.</label>
                                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_scm_amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="d-flex align-items-center mb-2">
                                                <label class="mb-0" style="font-weight: bold; width: 80px;">Dis1.Amt.</label>
                                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_dis1_amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
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
                                                <label class="mb-0" style="font-weight: bold; width: 80px;">Gross Tot.</label>
                                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_gross_total" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- col 4 - Sub.Tot, Net Amt, Vol -->
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
                                                <label class="mb-0" style="font-weight: bold; width: 80px;">Net Amt.</label>
                                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_net_amount" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
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

                                <!-- col 5 - Comp, SCM -->
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

                                <!-- col 6 - Srino, Scm.%, Dis1.%, % -->
                                <div class="col-lg-2">
                                    <div class="row flex-column">
                                        <div class="col-lg-12">
                                            <div class="d-flex align-items-center mb-2">
                                                <label class="mb-0" style="font-weight: bold; width: 50px;">Srino</label>
                                                <input type="text" class="form-control form-control-sm readonly-field text-center" id="addl_srino" readonly style="width: 40px; height: 26px;" value="1">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="d-flex align-items-center mb-2">
                                                <label class="mb-0" style="font-weight: bold; width: 50px;">Scm.%</label>
                                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_scm_percent" readonly step="0.01" style="width: 60px; height: 26px;" value="0.00">
                                                <label class="mb-0 ms-2" style="font-weight: bold; width: 50px;">Dis1.%</label>
                                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_dis1_percent" readonly step="0.01" style="width: 60px; height: 26px;" value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="d-flex align-items-center mb-2">
                                                <label class="mb-0" style="font-weight: bold; width: 50px;">%</label>
                                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="addl_percent" readonly step="0.01" style="width: 60px; height: 26px;" value="10.00">
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
                            <button type="button" class="btn btn-primary" id="submitBtn" onclick="askCreditAdjustment()">
                                <i class="bi bi-check-circle me-1"></i> Save Purchase Return
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </section>

@endsection

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'purchaseReturnItemModal',
    'module' => 'purchase-return',
    'showStock' => true,
    'rateType' => 'pur_rate',
    'showCompany' => true,
    'showHsn' => true,
    'batchModalId' => 'purchaseReturnBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'purchaseReturnBatchModal',
    'module' => 'purchase-return',
    'showOnlyAvailable' => true,
    'rateType' => 'pur_rate',
    'showCostDetails' => true,
])

<script>
console.log('🟢 Purchase Return: Modal components included');
console.log('🟢 Purchase Return: Checking if modal functions exist...');
console.log('🟢 Purchase Return: openItemModal_purchaseReturnItemModal =', typeof window.openItemModal_purchaseReturnItemModal);
console.log('🟢 Purchase Return: openBatchModal_purchaseReturnBatchModal =', typeof window.openBatchModal_purchaseReturnBatchModal);
</script>

@push('styles')
<style>
    /* Insert Orders Modal Styles */
    .insert-orders-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99999998;
        opacity: 0;
        transition: opacity 0.3s ease;
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
        width: 90%;
        max-width: 900px;
        max-height: 90vh;
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        z-index: 99999999;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .insert-orders-modal.show {
        display: block;
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .insert-orders-modal-content {
        display: flex;
        flex-direction: column;
        height: 100%;
        max-height: 90vh;
    }

    .insert-orders-modal-header {
        padding: 15px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .insert-orders-modal-title {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .btn-close-modal {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 24px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-close-modal:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .insert-orders-modal-body {
        padding: 15px;
        overflow-y: auto;
        flex: 1;
    }

    .insert-orders-modal-footer {
        padding: 10px 15px;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-shrink: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    let selectedItem = null;
    let selectedSupplier = null;
    let allItems = [];
    let returnItems = [];
    let currentRowIndex = 0;
    let selectedRowIndex = null;
    let pendingItemSelectionRowIndex = null;
    let disPercentEnterLoopLock = false;
    let codeEnterModalOpenLock = false;
    let confirmModalActiveIndex = 0;
    let confirmModalKeydownHandler = null;
    let creditAdjustModalKeydownHandler = null;
    let creditAdjustActiveInputIndex = 0;
    let creditAdjustActiveButtonIndex = 0;
    let successModalActiveIndex = 1;
    let successModalKeydownHandler = null;

    function getSelectedSupplierId() {
        return selectedSupplier?.id || document.getElementById('supplierSelect')?.value || '';
    }

    function getSelectedSupplierName() {
        if (selectedSupplier?.name) return selectedSupplier.name;
        const selectedId = document.getElementById('supplierSelect')?.value;
        if (selectedId) {
            const item = document.querySelector(`#supplierDropdownList .dropdown-item[data-value="${selectedId}"]`);
            return item?.dataset?.name || '';
        }
        return '';
    }

    function setSelectedSupplier(id, name, code = '') {
        if (id) {
            selectedSupplier = { id, name, code };
        } else {
            selectedSupplier = null;
        }
    }

    function initSupplierDropdown() {
        const input = document.getElementById('supplierSearchInput');
        const hiddenInput = document.getElementById('supplierSelect');
        const dropdownList = document.getElementById('supplierDropdownList');

        if (!input || !hiddenInput || !dropdownList) {
            console.warn('Supplier dropdown elements not found');
            return;
        }

        let highlightedIndex = -1;
        let isDropdownOpen = false;

        function getVisibleItems() {
            return Array.from(dropdownList.querySelectorAll('.dropdown-item:not(.hidden)'));
        }

        function showDropdown() {
            dropdownList.style.display = 'block';
            isDropdownOpen = true;
            highlightedIndex = -1;
        }

        function hideDropdown() {
            dropdownList.style.display = 'none';
            isDropdownOpen = false;
            highlightedIndex = -1;
            dropdownList.querySelectorAll('.dropdown-item').forEach(item => item.classList.remove('highlighted'));
        }

        function filterItems(searchText) {
            const items = dropdownList.querySelectorAll('.dropdown-item');
            const search = (searchText || '').toLowerCase().trim();

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                const code = (item.dataset.code || '').toLowerCase();
                const name = (item.dataset.name || '').toLowerCase();

                if (search === '' || text.includes(search) || code.includes(search) || name.includes(search)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });

            highlightedIndex = -1;
            items.forEach(item => item.classList.remove('highlighted'));
        }

        function highlightItem(index) {
            const visibleItems = getVisibleItems();
            visibleItems.forEach(item => item.classList.remove('highlighted'));

            if (index >= 0 && index < visibleItems.length) {
                highlightedIndex = index;
                visibleItems[index].classList.add('highlighted');
                visibleItems[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        }

        function selectItem(item, moveNext = true) {
            const value = item.dataset.value || '';
            const name = item.dataset.name || '';
            const code = item.dataset.code || '';

            hiddenInput.value = value;
            if (value) {
                input.value = code ? `${code} - ${name}` : name;
            } else {
                input.value = '';
            }

            dropdownList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');

            setSelectedSupplier(value, name, code);
            hideDropdown();

            if (moveNext && typeof window.focusNextHeaderField === 'function') {
                window.focusNextHeaderField('supplierSearchInput');
            }
        }

        input.addEventListener('focus', function() {
            showDropdown();
            filterItems(this.value);
        });

        input.addEventListener('input', function() {
            showDropdown();
            filterItems(this.value);
        });

        input.addEventListener('keydown', function(e) {
            if (!isDropdownOpen) {
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    showDropdown();
                    filterItems(this.value);
                }
                return;
            }

            const visibleItems = getVisibleItems();

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    if (highlightedIndex < visibleItems.length - 1) {
                        highlightItem(highlightedIndex + 1);
                    } else {
                        highlightItem(0);
                    }
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    if (highlightedIndex > 0) {
                        highlightItem(highlightedIndex - 1);
                    } else {
                        highlightItem(visibleItems.length - 1);
                    }
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (highlightedIndex >= 0 && highlightedIndex < visibleItems.length) {
                        selectItem(visibleItems[highlightedIndex]);
                    } else if (visibleItems.length > 0) {
                        selectItem(visibleItems[0]);
                    }
                    break;
                case 'Escape':
                    e.preventDefault();
                    hideDropdown();
                    break;
                case 'Tab':
                    if (highlightedIndex >= 0 && highlightedIndex < visibleItems.length) {
                        selectItem(visibleItems[highlightedIndex], false);
                    }
                    hideDropdown();
                    break;
            }
        });

        dropdownList.addEventListener('click', function(e) {
            const item = e.target.closest('.dropdown-item');
            if (item) {
                selectItem(item, false);
            }
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#supplierDropdownWrapper')) {
                hideDropdown();
            }
        });
    }

    let awaitingDateSelection = false;
    let datePickerJustOpened = false;
    let supplierFocusForceTimer = null;
    let startupSupplierFocusTimer = null;
    let dateConfirmTargetId = 'supplierSearchInput';
    let rateDiffToDateFlowActive = false;
    let rateDiffFocusedAt = 0;
    let insertOrdersAutoClickLock = false;

    function clearSupplierFocusForceTimer() {
        if (supplierFocusForceTimer) {
            clearInterval(supplierFocusForceTimer);
            supplierFocusForceTimer = null;
        }
    }

    function setDateConfirmTarget(targetId, source = 'unknown') {
        dateConfirmTargetId = targetId || 'supplierSearchInput';
        console.log('[KB-PR][Date] confirm target set', {
            source,
            dateConfirmTargetId
        });
    }

    function clearStartupSupplierFocusTimer() {
        if (startupSupplierFocusTimer) {
            clearInterval(startupSupplierFocusTimer);
            startupSupplierFocusTimer = null;
        }
    }

    function focusSupplierOnLoad() {
        const supplierInput = document.getElementById('supplierSearchInput');
        if (!supplierInput) {
            console.warn('[KB-PR][Startup] supplierSearchInput not found');
            return;
        }

        clearStartupSupplierFocusTimer();

        let attempts = 0;
        startupSupplierFocusTimer = setInterval(() => {
            attempts += 1;

            // Do not override focus if user has already moved to another field intentionally.
            const active = document.activeElement;
            if (!active || active === document.body || active.id === 'return_date' || active.id === 'supplierSearchInput') {
                supplierInput.focus();
                console.log('[KB-PR][Startup] supplier focus applied', {
                    attempts,
                    activeId: document.activeElement?.id || null
                });
            }

            if (attempts >= 8) {
                clearStartupSupplierFocusTimer();
            }
        }, 100);
    }

    function focusHeaderFieldById(targetId, source = 'unknown') {
        const targetInput = document.getElementById(targetId);
        if (!targetInput) {
            console.warn('[KB-PR][Date->Next] target not found', { source, targetId });
            return;
        }

        clearSupplierFocusForceTimer();

        // Force focus after current event cycle to override any global key handlers.
        setTimeout(() => {
            targetInput.focus();
            console.log('[KB-PR][Date->Next] focus applied', {
                source,
                targetId,
                activeId: document.activeElement?.id || null
            });
        }, 0);

        // Second pass fallback if some other handler re-focused another field.
        setTimeout(() => {
            if (document.activeElement !== targetInput) {
                targetInput.focus();
                console.log('[KB-PR][Date->Next] fallback focus applied', {
                    source,
                    targetId,
                    activeId: document.activeElement?.id || null
                });
            }
        }, 120);

        // Late fallback for any async/global handlers that run after key events.
        setTimeout(() => {
            if (document.activeElement !== targetInput) {
                targetInput.focus();
                console.log('[KB-PR][Date->Next] late fallback focus applied', {
                    source,
                    targetId,
                    activeId: document.activeElement?.id || null
                });
            }
        }, 250);

        // Keep forcing focus briefly to beat any late global "next-field" handlers.
        let attempts = 0;
        supplierFocusForceTimer = setInterval(() => {
            attempts += 1;
            if (document.activeElement !== targetInput) {
                targetInput.focus();
                console.log('[KB-PR][Date->Next] loop focus applied', {
                    source,
                    targetId,
                    attempts,
                    activeId: document.activeElement?.id || null
                });
            }
            if (attempts >= 10) {
                clearSupplierFocusForceTimer();
            }
        }, 80);
    }

    function openReturnDatePicker() {
        const dateInput = document.getElementById('return_date');
        if (!dateInput) return;

        awaitingDateSelection = true;
        datePickerJustOpened = true;
        console.log('[KB-PR][Date] picker open requested', {
            value: dateInput.value
        });
        setTimeout(() => {
            datePickerJustOpened = false;
        }, 300);

        if (typeof dateInput.showPicker === 'function') {
            dateInput.showPicker();
        } else {
            // Fallback for browsers without showPicker
            dateInput.focus();
            dateInput.click();
        }
    }

    function moveRateDiffToHeaderDate(source = 'unknown') {
        const dateInput = document.getElementById('return_date');
        if (!dateInput) return;
        rateDiffToDateFlowActive = true;
        setDateConfirmTarget('insertOrderBtn', source);
        dateInput.focus();
        setTimeout(() => {
            openReturnDatePicker();
        }, 0);
        console.log('[KB-PR][RateDiff->Date] moved to date', {
            source,
            activeId: document.activeElement?.id || null
        });
    }

    function shouldIgnoreImmediateRateDiffKey(event, source = 'unknown') {
        const elapsed = Date.now() - rateDiffFocusedAt;
        const isImmediate = elapsed >= 0 && elapsed < 140;
        if (event.key === 'Enter' && (event.repeat || isImmediate)) {
            console.log('[KB-PR][RateDiff] key ignored (immediate/repeat)', {
                source,
                key: event.key,
                repeat: event.repeat,
                elapsed
            });
            return true;
        }
        return false;
    }

    function triggerInsertOrdersAfterDateConfirm(source = 'unknown') {
        if (insertOrdersAutoClickLock) {
            console.log('[KB-PR][InsertOrders] auto trigger skipped (locked)', { source });
            return;
        }

        const insertBtn = document.getElementById('insertOrderBtn');
        if (!insertBtn) {
            console.warn('[KB-PR][InsertOrders] button not found for auto trigger', { source });
            return;
        }

        insertOrdersAutoClickLock = true;
        setTimeout(() => {
            insertBtn.focus();
            console.log('[KB-PR][InsertOrders] auto trigger click', {
                source,
                activeId: document.activeElement?.id || null
            });
            insertBtn.click();

            setTimeout(() => {
                insertOrdersAutoClickLock = false;
            }, 300);
        }, 60);
    }

    // Hard binding for rate_diff input via inline onkeydown attribute.
    window.handleRateDiffKeydown = function(event) {
        if (!event) return true;
        if (event.key !== 'Enter' && event.key !== 'Tab') return true;
        if (shouldIgnoreImmediateRateDiffKey(event, 'rate_diff.inline')) return false;

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }

        console.log('[KB-PR][RateDiff] inline key captured', {
            key: event.key,
            activeId: document.activeElement?.id || null
        });

        moveRateDiffToHeaderDate(`rate_diff.inline.${event.key.toLowerCase()}`);
        return false;
    };

    function initHeaderKeyboardNavigation() {
        const order = [
            'return_date',
            'supplierSearchInput',
            'invoice_no',
            'invoice_date',
            'gst_vno',
            'remarks',
            'tax_flag',
            'rate_diff',
            'insertOrderBtn'
        ];

        function isFocusable(el) {
            return el && !el.disabled && el.offsetParent !== null;
        }

        function focusNext(currentId) {
            const currentIndex = order.indexOf(currentId);
            if (currentIndex === -1) return;

            for (let i = currentIndex + 1; i < order.length; i++) {
                const nextEl = document.getElementById(order[i]);
                if (isFocusable(nextEl)) {
                    nextEl.focus();
                    return;
                }
            }
        }

        window.focusNextHeaderField = focusNext;

        function confirmReturnDateSelection(source = 'unknown') {
            if (!awaitingDateSelection) return;
            awaitingDateSelection = false;
            console.log('[KB-PR][Date] selection confirmed', {
                source,
                rateDiffToDateFlowActive,
                nextTarget: dateConfirmTargetId,
                value: document.getElementById('return_date')?.value || null,
                activeId: document.activeElement?.id || null
            });
            const nextTarget = dateConfirmTargetId || 'supplierSearchInput';
            const shouldAutoTriggerInsertOrders = nextTarget === 'insertOrderBtn' && rateDiffToDateFlowActive;
            if (shouldAutoTriggerInsertOrders) {
                triggerInsertOrdersAfterDateConfirm(source);
            } else {
                focusHeaderFieldById(nextTarget, source);
            }
            if (nextTarget === 'insertOrderBtn' || rateDiffToDateFlowActive) {
                // Reset default behavior after rate_diff flow is completed.
                rateDiffToDateFlowActive = false;
                setDateConfirmTarget('supplierSearchInput', 'date.confirm.reset-default');
            }
        }

        order.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;

            el.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;
                if (id === 'supplierSearchInput') return;
                if (id === 'rate_diff') {
                    if (shouldIgnoreImmediateRateDiffKey(e, 'rate_diff.bubble')) return;
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof e.stopImmediatePropagation === 'function') {
                        e.stopImmediatePropagation();
                    }
                    moveRateDiffToHeaderDate('rate_diff.keydown.enter');
                    return;
                }
                if (id === 'return_date') {
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof e.stopImmediatePropagation === 'function') {
                        e.stopImmediatePropagation();
                    }
                    // If date picker already opened and user presses Enter again, treat as select/confirm
                    if (awaitingDateSelection) {
                        confirmReturnDateSelection('return_date.keydown.enter.confirm');
                    } else {
                        openReturnDatePicker();
                    }
                    return;
                }
                e.preventDefault();

                if (id === 'insertOrderBtn') {
                    el.click();
                    return;
                }

                focusNext(id);
            });

            if (id === 'rate_diff') {
                el.addEventListener('focus', function() {
                    rateDiffFocusedAt = Date.now();
                    console.log('[KB-PR][RateDiff] focused', { rateDiffFocusedAt });
                });

                el.addEventListener('keydown', function(e) {
                    if (e.key !== 'Tab') return;
                    if (shouldIgnoreImmediateRateDiffKey(e, 'rate_diff.tab')) return;
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof e.stopImmediatePropagation === 'function') {
                        e.stopImmediatePropagation();
                    }
                    moveRateDiffToHeaderDate('rate_diff.keydown.tab');
                });
            }
        });

        const returnDateInput = document.getElementById('return_date');
        if (returnDateInput) {
            console.log('[KB-PR][Date] init header handlers', { id: returnDateInput.id });

            // Capture-phase handler for date Enter. This gives us an early hook on target.
            returnDateInput.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;
                console.log('[KB-PR][Date] capture keydown Enter', {
                    awaitingDateSelection,
                    activeId: document.activeElement?.id || null
                });
            }, true);

            returnDateInput.addEventListener('input', function() {
                confirmReturnDateSelection('return_date.input');
            });

            returnDateInput.addEventListener('change', function() {
                confirmReturnDateSelection('return_date.change');
            });

            returnDateInput.addEventListener('keyup', function(e) {
                if (e.key !== 'Enter') return;
                if (!awaitingDateSelection) return;
                confirmReturnDateSelection('return_date.keyup.enter');
            });

            returnDateInput.addEventListener('blur', function() {
                if (datePickerJustOpened) return;
                confirmReturnDateSelection('return_date.blur');
            });

            returnDateInput.addEventListener('focus', function() {
                if (dateConfirmTargetId === 'insertOrderBtn' && !awaitingDateSelection) {
                    console.log('[KB-PR][Date] focus with insert-order target, opening picker');
                    openReturnDatePicker();
                }
            });
        }

        // Fallback: if Enter is pressed while awaiting date selection, move to supplier
        document.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            if (!awaitingDateSelection) return;
            console.log('[KB-PR][Date] document keydown fallback', {
                targetId: e.target?.id || null,
                activeId: document.activeElement?.id || null
            });
            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') {
                e.stopImmediatePropagation();
            }
            confirmReturnDateSelection('document.keydown.enter.fallback');
        }, true);

        // Root capture handler to enforce rate_diff -> date -> insert-order flow
        document.addEventListener('keydown', function(e) {
            const activeId = document.activeElement?.id || null;

            if (e.key === 'Enter' && activeId === 'return_date' && rateDiffToDateFlowActive) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof e.stopImmediatePropagation === 'function') {
                    e.stopImmediatePropagation();
                }
                console.log('[KB-PR][Root] return_date Enter captured for rate_diff flow', {
                    awaitingDateSelection,
                    activeId
                });

                if (awaitingDateSelection) {
                    confirmReturnDateSelection('root.capture.return_date.enter.confirm');
                } else {
                    openReturnDatePicker();
                }
            }
        }, true);

        // Earliest capture layer on window to beat document-level shortcut handlers.
        if (!window.__kbPrWindowRateDiffCaptureBound) {
            window.addEventListener('keydown', function(e) {
                const targetId = e.target?.id || null;
                if ((e.key !== 'Enter' && e.key !== 'Tab') || targetId !== 'rate_diff') return;
                if (shouldIgnoreImmediateRateDiffKey(e, 'window.capture.rate_diff')) return;

                e.preventDefault();
                e.stopPropagation();
                if (typeof e.stopImmediatePropagation === 'function') {
                    e.stopImmediatePropagation();
                }

                console.log('[KB-PR][Root] window capture rate_diff key', {
                    key: e.key,
                    targetId
                });
                moveRateDiffToHeaderDate(`window.capture.rate_diff.${e.key.toLowerCase()}`);
            }, true);

            window.__kbPrWindowRateDiffCaptureBound = true;
        }
    }

    $(document).ready(function() {
        const existingTableRows = document.querySelectorAll('#itemsTableBody tr').length;
        if (existingTableRows > 0) {
            currentRowIndex = existingTableRows;
        }

        // Initialize transaction number
        fetchNextTransactionNumber();

        // Update day when date changes
        $('#return_date').on('change', function() {
            const date = new Date($(this).val());
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $('#day_name').val(days[date.getDay()]);
        });

        // Initialize supplier dropdown + header keyboard navigation
        initSupplierDropdown();
        initHeaderKeyboardNavigation();
        console.log('[KB-PR] header keyboard initialized');
        focusSupplierOnLoad();

        // Insert Orders button - check if supplier is selected
        $('#insertOrderBtn').on('click', function(e) {
            e.preventDefault();
            if (!getSelectedSupplierId()) {
                alert('Please select a supplier first!');
                return false;
            }
            openInsertOrdersModal();
        });
    });

    // Legacy (unused): old Add Row flow that directly opened item modal
    function _legacy_addNewRowViaModalDeprecated() {
        console.log('🔵 Purchase Return: addNewRow() called');
        
        if (!getSelectedSupplierId()) {
            alert('Please select a supplier first!');
            return;
        }
        
        console.log('🔵 Purchase Return: Supplier selected:', {
            id: getSelectedSupplierId(),
            name: getSelectedSupplierName()
        });
        console.log('🔵 Purchase Return: Checking for modal function...');
        console.log('🔵 Purchase Return: typeof openItemModal_purchaseReturnItemModal =', typeof openItemModal_purchaseReturnItemModal);
        
        // Use reusable item selection modal
        if (typeof openItemModal_purchaseReturnItemModal === 'function') {
            console.log('✅ Purchase Return: Modal function found! Opening modal...');
            openItemModal_purchaseReturnItemModal();
        } else {
            console.error('❌ Purchase Return: Modal function NOT found!');
            console.error('❌ Available window functions:', Object.keys(window).filter(k => k.includes('Modal')));
            alert('Item selection modal not initialized. Please reload the page.');
        }
    }

    // Legacy callback (unused). Real callback is redefined later for keyboard loop flow.
    window._legacy_onItemBatchSelectedFromModal = function(item, batch) {
        console.log('Item selected from modal:', item);
        console.log('Batch selected from modal:', batch);
        
        // Create a new row for the item
        const tbody = document.getElementById('itemsTableBody');
        const rowIndex = tbody.querySelectorAll('tr').length;
        
        // Format expiry date
        let expiryDisplay = '';
        if (batch.expiry_date) {
            const expiryDate = new Date(batch.expiry_date);
            expiryDisplay = `${String(expiryDate.getMonth() + 1).padStart(2, '0')}/${String(expiryDate.getFullYear()).slice(-2)}`;
        }
        
        const purchaseRate = parseFloat(batch.p_rate || batch.pur_rate || batch.purchase_rate || 0);
        
        const row = document.createElement('tr');
        row.id = `row-${rowIndex}`;
        row.dataset.itemData = JSON.stringify({
            item_id: item.id,
            item_name: item.name,
            batch_id: batch.id,
            batch_no: batch.batch_no,
            hsn_code: item.hsn_code || '',
            cgst_percent: item.cgst_percent || 0,
            sgst_percent: item.sgst_percent || 0,
            cess_percent: item.cess_percent || 0,
            s_rate: batch.s_rate || 0,
            ws_rate: batch.ws_rate || 0,
            mrp: batch.mrp || 0
        });
        
        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.bar_code || item.id || ''}" readonly>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${batch.batch_no || ''}" readonly>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${expiryDisplay}" readonly>
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][qty]" value="0" step="1" 
                       onchange="calculateRowAmount(${rowIndex})" onclick="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][free_qty]" value="0" step="1"
                       onchange="calculateRowAmount(${rowIndex})" onclick="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][purchase_rate]" value="${purchaseRate.toFixed(2)}" step="0.01" 
                       onchange="calculateRowAmount(${rowIndex})" onclick="selectRowForCalculation(${rowIndex})" readonly>
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][dis_percent]" value="0" step="0.01" 
                       onchange="calculateRowAmount(${rowIndex})" onclick="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][ft_rate]" value="${purchaseRate.toFixed(2)}" step="0.01" 
                       onclick="selectRowForCalculation(${rowIndex})" readonly>
            </td>
            <td>
                <input type="number" class="form-control readonly-field" name="items[${rowIndex}][ft_amount]" value="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" style="padding: 2px 6px;">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batch.id}">
            <input type="hidden" name="items[${rowIndex}][cgst_percent]" value="${item.cgst_percent || 0}">
            <input type="hidden" name="items[${rowIndex}][sgst_percent]" value="${item.sgst_percent || 0}">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="${batch.mrp || 0}">
        `;
        
        tbody.appendChild(row);
        
        // Select the new row
        selectRowForCalculation(rowIndex);
        
        // Focus on qty input
        setTimeout(() => {
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            if (qtyInput) {
                qtyInput.focus();
                qtyInput.select();
            }
        }, 100);
        
        alert('Item added! Enter return quantity.');
        calculateRowAmount(rowIndex);
        if (typeof calculateTotals === 'function') calculateTotals();
    };

    // Keyboard loop flow override:
    // 1) Add Row => only creates a blank row.
    // 2) Code Enter => opens item modal for that row.
    // 3) Modal selection fills that same row.
    // 4) Dis.% Enter => completes row and auto-clicks Add Row.
    function getNextTableRowIndex() {
        return currentRowIndex++;
    }

    function buildDefaultRowItemData() {
        return {
            item_id: '',
            item_code: '',
            item_name: '',
            batch_id: '',
            batch_no: '',
            hsn_code: '',
            company_name: '',
            packing: '',
            unit: '',
            cgst_percent: 0,
            sgst_percent: 0,
            cess_percent: 0,
            s_rate: 0,
            ws_rate: 0,
            spl_rate: 0,
            mrp: 0,
            total_cl_qty: 0
        };
    }

    function formatBatchExpiryForRow(batch) {
        if (!batch) return '';
        if (batch.expiry_display) return batch.expiry_display;
        if (batch.expiry) return batch.expiry;
        if (batch.expiry_date) {
            const expiryDate = new Date(batch.expiry_date);
            if (!Number.isNaN(expiryDate.getTime())) {
                return `${String(expiryDate.getMonth() + 1).padStart(2, '0')}/${String(expiryDate.getFullYear()).slice(-2)}`;
            }
        }
        return '';
    }

    function ensureRowElement(rowIndex) {
        const tbody = document.getElementById('itemsTableBody');
        if (!tbody) return null;

        let row = document.getElementById(`row-${rowIndex}`);
        if (!row) {
            row = document.createElement('tr');
            row.id = `row-${rowIndex}`;
            tbody.appendChild(row);
        }
        return row;
    }

    function renderEmptyRow(rowIndex, focusCode = true) {
        const row = ensureRowElement(rowIndex);
        if (!row) return null;

        row.dataset.itemData = JSON.stringify(buildDefaultRowItemData());
        row.dataset.completed = 'false';
        row.dataset.empty = 'true';
        row.style.backgroundColor = '';
        row.style.outline = '';
        row.style.outlineOffset = '';

        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][code]" value=""
                       placeholder="Press Enter"
                       onfocus="selectRowForCalculation(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); openItemSelectionForRow(${rowIndex}); return false; }">
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][name]" value="" readonly>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][batch]" value="" readonly>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="" readonly>
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][qty]" value="0" step="1"
                       onchange="calculateRowAmount(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'free_qty'); return false; }"
                       onfocus="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][free_qty]" value="0" step="1"
                       onchange="calculateRowAmount(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'purchase_rate'); return false; }"
                       onfocus="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][purchase_rate]" value="0.00" step="0.01"
                       onchange="calculateRowAmount(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'dis_percent'); return false; }"
                       onfocus="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][dis_percent]" value="0" step="0.01"
                       onchange="handleDiscountChange(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); handleDiscountAndCompleteRow(${rowIndex}); return false; }"
                       onfocus="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control readonly-field" name="items[${rowIndex}][ft_rate]" value="0.00" step="0.01" readonly>
            </td>
            <td>
                <input type="number" class="form-control readonly-field" name="items[${rowIndex}][ft_amount]" value="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" style="padding: 2px 6px;">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
            <input type="hidden" name="items[${rowIndex}][hsn_code]" value="">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="">
            <input type="hidden" name="items[${rowIndex}][packing]" value="">
            <input type="hidden" name="items[${rowIndex}][unit]" value="">
            <input type="hidden" name="items[${rowIndex}][cgst_percent]" value="0">
            <input type="hidden" name="items[${rowIndex}][sgst_percent]" value="0">
            <input type="hidden" name="items[${rowIndex}][cess_percent]" value="0">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="0">
        `;

        row.onclick = function() {
            selectRowForCalculation(rowIndex);
        };

        calculateRowAmount(rowIndex);
        recalculateTotals();

        if (focusCode) {
            setTimeout(() => {
                selectRowForCalculation(rowIndex);
                const codeInput = row.querySelector('input[name*="[code]"]');
                if (codeInput) {
                    codeInput.focus();
                    codeInput.select();
                }
            }, 80);
        }

        return row;
    }

    function openItemSelectionForRow(rowIndex) {
        if (!getSelectedSupplierId()) {
            alert('Please select a supplier first!');
            return;
        }

        pendingItemSelectionRowIndex = rowIndex;
        console.log('[KB-PR][Code] open item modal for row', { rowIndex });

        if (typeof openItemModal_purchaseReturnItemModal === 'function') {
            openItemModal_purchaseReturnItemModal();
        } else {
            alert('Item selection modal not initialized. Please reload the page.');
        }
    }

    function renderSelectedItemInRow(rowIndex, item, batch) {
        const row = ensureRowElement(rowIndex);
        if (!row) return;

        const expiryDisplay = formatBatchExpiryForRow(batch);
        const purchaseRate = parseFloat(batch.p_rate || batch.pur_rate || batch.purchase_rate || 0);
        const codeValue = item.bar_code || item.code || item.id || '';

        row.dataset.itemData = JSON.stringify({
            item_id: item.id,
            item_code: codeValue,
            item_name: item.name || '',
            batch_id: batch.id || batch.batch_id || '',
            batch_no: batch.batch_no || '',
            hsn_code: item.hsn_code || batch.hsn_code || '',
            company_name: item.company_name || batch.company_name || '',
            packing: item.packing || batch.packing || '',
            unit: item.unit || batch.unit || '',
            cgst_percent: item.cgst_percent || batch.cgst_percent || 0,
            sgst_percent: item.sgst_percent || batch.sgst_percent || 0,
            cess_percent: item.cess_percent || batch.cess_percent || 0,
            s_rate: batch.s_rate || batch.sale_rate || 0,
            ws_rate: batch.ws_rate || 0,
            spl_rate: batch.spl_rate || 0,
            mrp: batch.mrp || 0,
            total_cl_qty: batch.total_cl_qty || 0
        });
        row.dataset.completed = 'false';
        row.dataset.empty = 'false';
        row.style.backgroundColor = '';
        row.style.outline = '';
        row.style.outlineOffset = '';

        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][code]" value="${codeValue}" readonly
                       onfocus="selectRowForCalculation(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); openItemSelectionForRow(${rowIndex}); return false; }">
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${batch.batch_no || ''}" readonly>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${expiryDisplay}" readonly>
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][qty]" value="0" step="1"
                       onchange="calculateRowAmount(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'free_qty'); return false; }"
                       onfocus="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][free_qty]" value="0" step="1"
                       onchange="calculateRowAmount(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'purchase_rate'); return false; }"
                       onfocus="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][purchase_rate]" value="${purchaseRate.toFixed(2)}" step="0.01"
                       onchange="calculateRowAmount(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'dis_percent'); return false; }"
                       onfocus="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][dis_percent]" value="0" step="0.01"
                       onchange="handleDiscountChange(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); handleDiscountAndCompleteRow(${rowIndex}); return false; }"
                       onfocus="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control readonly-field" name="items[${rowIndex}][ft_rate]" value="${purchaseRate.toFixed(2)}" step="0.01" readonly>
            </td>
            <td>
                <input type="number" class="form-control readonly-field" name="items[${rowIndex}][ft_amount]" value="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" style="padding: 2px 6px;">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id || ''}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batch.id || batch.batch_id || ''}">
            <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${item.hsn_code || batch.hsn_code || ''}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || batch.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || batch.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || batch.unit || ''}">
            <input type="hidden" name="items[${rowIndex}][cgst_percent]" value="${item.cgst_percent || batch.cgst_percent || 0}">
            <input type="hidden" name="items[${rowIndex}][sgst_percent]" value="${item.sgst_percent || batch.sgst_percent || 0}">
            <input type="hidden" name="items[${rowIndex}][cess_percent]" value="${item.cess_percent || batch.cess_percent || 0}">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="${batch.mrp || 0}">
        `;

        row.onclick = function() {
            selectRowForCalculation(rowIndex);
        };

        setTimeout(() => {
            selectRowForCalculation(rowIndex);
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            if (qtyInput) {
                qtyInput.focus();
                qtyInput.select();
            }
        }, 80);

        calculateRowAmount(rowIndex);
        recalculateTotals();
    }

    // Override existing Add Row flow
    function addNewRow(focusCode = true) {
        if (!getSelectedSupplierId()) {
            alert('Please select a supplier first!');
            return null;
        }

        const rowIndex = getNextTableRowIndex();
        renderEmptyRow(rowIndex, focusCode !== false);
        return rowIndex;
    }

    // Override reusable modal callback to fill selected row when opened from Code field.
    window.onItemBatchSelectedFromModal = function(item, batch) {
        console.log('[KB-PR][ModalSelect]', { item, batch, pendingItemSelectionRowIndex });

        let rowIndex = pendingItemSelectionRowIndex;
        if (rowIndex === null || rowIndex === undefined || !document.getElementById(`row-${rowIndex}`)) {
            rowIndex = getNextTableRowIndex();
            renderEmptyRow(rowIndex, false);
        }

        pendingItemSelectionRowIndex = null;
        renderSelectedItemInRow(rowIndex, item, batch);
    };
    function openInsertOrdersModal() {
        pendingItemSelectionRowIndex = null;
        console.log('🔵 Purchase Return: openInsertOrdersModal() called');
        
        if (!getSelectedSupplierId()) {
            alert('Please select a supplier first!');
            return;
        }
        
        console.log('🔵 Purchase Return: Opening reusable item modal for Insert Orders');
        
        // Use reusable item selection modal instead of legacy modal
        if (typeof openItemModal_purchaseReturnItemModal === 'function') {
            console.log('✅ Purchase Return: Reusable modal function found! Opening...');
            openItemModal_purchaseReturnItemModal();
        } else {
            console.error('❌ Purchase Return: Reusable modal not available, falling back to legacy');
            // Fallback to legacy modal if reusable not available
            fetch('{{ route("admin.items.all") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                allItems = data.items || data;
                _legacy_showItemSelectionModal(allItems);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load items');
            });
        }
    }

    // LEGACY: Show item selection modal for Insert Orders feature
    function _legacy_showItemSelectionModal(items) {
        const modalHTML = `
            <div class="insert-orders-modal-backdrop" id="insertOrdersModalBackdrop" onclick="closeInsertOrdersModal()"></div>
            <div class="insert-orders-modal" id="insertOrdersModal">
                <div class="insert-orders-modal-content">
                    <div class="insert-orders-modal-header">
                        <h5 class="insert-orders-modal-title"><i class="bi bi-box-seam me-2"></i>Select Item</h5>
                        <button type="button" class="btn-close-modal" onclick="closeInsertOrdersModal()">&times;</button>
                    </div>
                    <div class="insert-orders-modal-body">
                        <!-- Search Box -->
                        <div style="margin-bottom: 10px;">
                            <input type="text" id="itemSearchInput" class="form-control form-control-sm" 
                                   placeholder="Search by item name or code..." 
                                   onkeyup="_legacy_filterItems()"
                                   style="font-size: 11px;">
                        </div>
                        
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-sm" style="font-size: 10px; margin-bottom: 0;" id="itemsSelectionTable">
                                <thead style="position: sticky; top: 0; background: #0d6efd; color: white; z-index: 10;">
                                    <tr>
                                        <th style="width: 35px; text-align: center;">S.N</th>
                                        <th style="width: 80px;">Code</th>
                                        <th style="width: 200px;">Item Name</th>
                                        <th style="width: 120px;">Company</th>
                                        <th style="width: 100px; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${items.map((item, index) => {
                                        const itemData = {
                                            id: item.id,
                                            code: item.code,
                                            name: item.name,
                                            company_name: item.company_name,
                                            hsn_code: item.hsn_code || item.hsn || '',
                                            packing: item.packing || '',
                                            unit: item.unit || 'PCS',
                                            s_rate: item.s_rate || 0,
                                            cgst_percent: item.cgst || item.cgst_percent || 0,
                                            sgst_percent: item.sgst || item.sgst_percent || 0,
                                            cess_percent: item.gst_cess || item.cess_percent || 0
                                        };
                                        return `
                                        <tr class="item-row" data-item-name="${String(item.name || '').toLowerCase()}" data-item-code="${String(item.code || '').toLowerCase()}">
                                            <td style="text-align: center;">${index + 1}</td>
                                            <td>${item.code || ''}</td>
                                            <td>${item.name || ''}</td>
                                            <td>${item.company_name || ''}</td>
                                            <td style="text-align: center;">
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        onclick='_legacy_selectItemForBatch(${JSON.stringify(itemData).replace(/'/g, "\\'")})' 
                                                        style="font-size: 9px; padding: 2px 8px;">
                                                    Select
                                                </button>
                                            </td>
                                        </tr>
                                    `}).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="insert-orders-modal-footer">
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
        
        // Show modal with animation
        setTimeout(() => {
            document.getElementById('insertOrdersModalBackdrop').classList.add('show');
            document.getElementById('insertOrdersModal').classList.add('show');
        }, 10);
    }

    // LEGACY: Filter items in the selection table (Insert Orders)
    function _legacy_filterItems() {
        const searchValue = document.getElementById('itemSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.item-row');
        
        rows.forEach(row => {
            const itemName = row.getAttribute('data-item-name');
            const itemCode = row.getAttribute('data-item-code');
            
            if (itemName.includes(searchValue) || itemCode.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // LEGACY: Select item and show batch modal (Insert Orders)
    function _legacy_selectItemForBatch(item) {
        if (typeof item === 'string') {
            item = JSON.parse(item);
        }
        
        selectedItem = {
            id: item.id,
            code: item.code,
            name: item.name,
            hsn_code: item.hsn_code || item.hsn || '',
            packing: item.packing || '',
            unit: item.unit || 'PCS',
            company_name: item.company_name || '',
            s_rate: item.s_rate || 0,
            cgst_percent: item.cgst || item.cgst_percent || 0,
            sgst_percent: item.sgst || item.sgst_percent || 0,
            cess_percent: item.gst_cess || item.cess_percent || 0
        };
        
        // Close item modal
        closeInsertOrdersModal();
        
        // Check if this is first time or add row
        const tbody = document.getElementById('itemsTableBody');
        const existingRows = tbody.querySelectorAll('tr').length;
        
        if (existingRows === 0) {
            // First time - show normal batch modal
            _legacy_loadBatchesForSupplierAndItem(getSelectedSupplierId(), item.id, false);
        } else {
            // Add row - show ALL batches of this item (any supplier)
            _legacy_loadAllBatchesForItem(item.id);
        }
    }

    // LEGACY: Load batches from past purchases (Insert Orders)
    function _legacy_loadBatchesForSupplierAndItem(supplierId, itemId, isAddRow = false) {
        fetch(`{{ route("admin.purchase-return.batches") }}?supplier_id=${supplierId}&item_id=${itemId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            _legacy_showBatchSelectionModal(data.batches || [], isAddRow);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load batches');
        });
    }

    // LEGACY: Load all batches for item (any supplier) - for add row (Insert Orders)
    function _legacy_loadAllBatchesForItem(itemId) {
        fetch(`{{ url('/admin/api/item-batches') }}/${itemId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Format batches to match our structure
            const formattedBatches = (data.batches || []).map(batch => ({
                batch_id: batch.id,
                batch_no: batch.batch_no,
                expiry: batch.expiry_display || batch.expiry_date,
                qty: batch.total_qty || batch.qty || 0,
                available_qty: batch.qty || 0,
                mrp: batch.mrp || 0,
                purchase_rate: batch.pur_rate || 0,
                s_rate: batch.s_rate || 0,
                bill_no: '',
                bill_date: batch.purchase_date_display || '',
                hsn_code: selectedItem.hsn_code || batch.hsn_code || '',
                packing: batch.packing || selectedItem.packing || '',
                unit: batch.unit || selectedItem.unit || 'PCS',
                company_name: batch.company_name || selectedItem.company_name || '',
                cgst_percent: selectedItem.cgst_percent || batch.cgst_percent || 0,
                sgst_percent: selectedItem.sgst_percent || batch.sgst_percent || 0,
                cess_percent: selectedItem.cess_percent || batch.cess_percent || 0,
                ws_rate: batch.ws_rate || 0,
                spl_rate: batch.spl_rate || 0,
                cost_gst: batch.cost_gst || 0,
                total_cl_qty: batch.total_cl_qty || 0,
                purchase_transaction_id: batch.purchase_transaction_id || null,
                invoice_no: batch.invoice_no || null,
                invoice_date: batch.invoice_date || null
            }));
            
            _legacy_showBatchSelectionModal(formattedBatches, true); // true = add row format
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            alert('Failed to load batches for this item');
        });
    }

    // LEGACY: Show batch selection modal (Insert Orders)
    function _legacy_showBatchSelectionModal(batches, isAddRow = false) {
        const modalHTML = `
            <div class="insert-orders-modal-backdrop" id="insertOrdersModalBackdrop" onclick="closeInsertOrdersModal()"></div>
            <div class="insert-orders-modal" id="insertOrdersModal">
                <div class="insert-orders-modal-content">
                    <div class="insert-orders-modal-header">
                        <h5 class="insert-orders-modal-title"><i class="bi bi-upc-scan me-2"></i>Select Batch - ${selectedItem.name}</h5>
                        <button type="button" class="btn-close-modal" onclick="closeInsertOrdersModal()">&times;</button>
                    </div>
                    <div class="insert-orders-modal-body">
                        <div class="alert alert-info" style="font-size: 11px; padding: 8px;">
                            <strong>Supplier:</strong> ${getSelectedSupplierName()}
                        </div>
                        
                        <div style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-bordered table-sm" style="font-size: 10px; margin-bottom: 0;">
                                <thead style="position: sticky; top: 0; background: #28a745; color: white; z-index: 10;">
                                    <tr>
                                        ${isAddRow ? `
                                            <th style="width: 100px;">BATCH</th>
                                            <th style="width: 80px;">DATE</th>
                                            <th style="width: 60px; text-align: right;">RATE</th>
                                            <th style="width: 60px; text-align: right;">P.RATE</th>
                                            <th style="width: 60px; text-align: right;">MRP</th>
                                            <th style="width: 50px; text-align: right;">QTY.</th>
                                            <th style="width: 60px;">EXP.</th>
                                            <th style="width: 70px;">CODE</th>
                                            <th style="width: 70px; text-align: right;">Cost+GST</th>
                                            <th style="width: 50px;">SCM</th>
                                            <th style="width: 80px; text-align: center;">Action</th>
                                        ` : `
                                            <th style="width: 80px;">Date</th>
                                            <th style="width: 100px;">Bill No</th>
                                            <th style="width: 100px;">Batch</th>
                                            <th style="width: 70px;">Expiry</th>
                                            <th style="width: 60px; text-align: right;">Pur.Qty</th>
                                            <th style="width: 60px; text-align: right;">Avl.Qty</th>
                                            <th style="width: 70px; text-align: right;">MRP</th>
                                            <th style="width: 70px; text-align: right;">P.Rate</th>
                                            <th style="width: 80px; text-align: center;">Action</th>
                                        `}
                                    </tr>
                                </thead>
                                <tbody>
                                    ${batches.length === 0 ? 
                                        `<tr><td colspan="${isAddRow ? '11' : '9'}" class="text-center">No batches found for this supplier and item</td></tr>` :
                                        batches.map(batch => `
                                            <tr>
                                                ${isAddRow ? `
                                                    <td>${batch.batch_no || ''}</td>
                                                    <td>${formatDate(batch.bill_date)}</td>
                                                    <td style="text-align: right;">${parseFloat(batch.s_rate || 0).toFixed(2)}</td>
                                                    <td style="text-align: right;">${parseFloat(batch.purchase_rate || 0).toFixed(2)}</td>
                                                    <td style="text-align: right;">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                                                    <td style="text-align: right;">${batch.available_qty || 0}</td>
                                                    <td>${batch.expiry || ''}</td>
                                                    <td>${selectedItem.hsn_code || batch.hsn_code || ''}</td>
                                                    <td style="text-align: right;">${parseFloat(batch.cost_gst || 0).toFixed(2)}</td>
                                                    <td>0</td>
                                                ` : `
                                                    <td>${formatDate(batch.bill_date)}</td>
                                                    <td>${batch.bill_no || ''}</td>
                                                    <td>${batch.batch_no || ''}</td>
                                                    <td>${batch.expiry || ''}</td>
                                                    <td style="text-align: right;">${batch.qty || 0}</td>
                                                    <td style="text-align: right;">${batch.available_qty || 0}</td>
                                                    <td style="text-align: right;">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                                                    <td style="text-align: right;">${parseFloat(batch.purchase_rate || 0).toFixed(2)}</td>
                                                `}
                                                <td style="text-align: center;">
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick='selectBatch(${JSON.stringify(batch).replace(/'/g, "\\'")})'
                                                            style="font-size: 9px; padding: 2px 8px;">
                                                        Select
                                                    </button>
                                                </td>
                                            </tr>
                                        `).join('')
                                    }
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="insert-orders-modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeInsertOrdersModal()">Close</button>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal
        const existingModal = document.getElementById('insertOrdersModal');
        if (existingModal) existingModal.remove();
        const existingBackdrop = document.getElementById('insertOrdersModalBackdrop');
        if (existingBackdrop) existingBackdrop.remove();
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Show modal with animation
        setTimeout(() => {
            document.getElementById('insertOrdersModalBackdrop').classList.add('show');
            document.getElementById('insertOrdersModal').classList.add('show');
        }, 10);
    }

    // Close modal
    function closeInsertOrdersModal() {
        const modal = document.getElementById('insertOrdersModal');
        const backdrop = document.getElementById('insertOrdersModalBackdrop');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        
        setTimeout(() => {
            if (modal) modal.remove();
            if (backdrop) backdrop.remove();
        }, 300);
    }

    // Select batch and add to table
    function selectBatch(batch) {
        if (typeof batch === 'string') {
            batch = JSON.parse(batch);
        }
        
        // Check if this is add row and batch might be from different supplier
        const tbody = document.getElementById('itemsTableBody');
        const existingRows = tbody.querySelectorAll('tr').length;
        
        if (existingRows > 0) {
            // This is add row - check if batch is actually from different supplier
            // We need to check if this batch's purchase transaction belongs to current supplier
            const currentSupplierId = getSelectedSupplierId();
            const batchPurchaseTransactionId = batch.purchase_transaction_id;
            
            // Check if batch is from different supplier by verifying purchase transaction
            if (batchPurchaseTransactionId) {
                // Make AJAX call to verify supplier
                fetch(`{{ url('/admin/api/verify-batch-supplier') }}?purchase_transaction_id=${batchPurchaseTransactionId}&supplier_id=${currentSupplierId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.is_same_supplier) {
                        // Same supplier - add directly
                        closeInsertOrdersModal();
                        _legacy_addItemToReturnTable(batch);
                    } else {
                        // Different supplier - show warning
                        const supplierName = data.batch_supplier_name || 'Unknown Supplier';
                        const confirmMessage = `⚠️ Warning: This batch "${batch.batch_no || 'Unknown'}" is from "${supplierName}", not from the selected supplier "${getSelectedSupplierName()}".
            
Batch Details:
• Supplier: ${supplierName}
• Date: ${batch.bill_date || ''}
• Bill No: ${batch.bill_no || 'N/A'}
• Rate: ₹${parseFloat(batch.purchase_rate || 0).toFixed(2)}

Do you still want to add this batch to the return?`;
                        
                        if (confirm(confirmMessage)) {
                            closeInsertOrdersModal();
                            _legacy_addItemToReturnTable(batch);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error verifying supplier:', error);
                    // Fallback - add directly if verification fails
                    closeInsertOrdersModal();
                    _legacy_addItemToReturnTable(batch);
                });
                return; // Exit here to wait for AJAX response
            }
        }
        
        closeInsertOrdersModal();
        _legacy_addItemToReturnTable(batch);
    }

    // Global variables for table management
    // currentRowIndex and selectedRowIndex are declared at script top.

    // LEGACY: Add item to return table - Enhanced version with proper structure (Insert Orders)
    function _legacy_addItemToReturnTable(batch) {
        // Auto-populate invoice details from batch's purchase transaction
        // Use bill_no and bill_date as fallback if invoice fields are null
        const invoiceNo = batch.invoice_no || batch.bill_no || null;
        const invoiceDate = batch.invoice_date || batch.bill_date || null;
        
        console.log('Batch invoice details:', {
            invoice_no: invoiceNo,
            invoice_date: invoiceDate,
            bill_no: batch.bill_no,
            bill_date: batch.bill_date
        });
        
        const invoiceNoField = document.getElementById('invoice_no');
        const invoiceDateField = document.getElementById('invoice_date');
        
        if (invoiceNo && invoiceNoField) {
            invoiceNoField.value = invoiceNo;
            invoiceNoField.readOnly = true;
            invoiceNoField.style.backgroundColor = '#f8f9fa';
            invoiceNoField.style.cursor = 'not-allowed';
            console.log('Set invoice_no to:', invoiceNo);
        }
        if (invoiceDate && invoiceDateField) {
            invoiceDateField.value = invoiceDate;
            invoiceDateField.readOnly = true;
            invoiceDateField.style.backgroundColor = '#f8f9fa';
            invoiceDateField.style.cursor = 'not-allowed';
            console.log('Set invoice_date to:', invoiceDate);
        }
        
        // s_rate and ws_rate come from batch (selected batch's rates)
        const item = {
            item_id: selectedItem.id,
            item_code: selectedItem.code,
            item_name: selectedItem.name,
            batch_id: batch.batch_id || batch.id,
            batch_no: batch.batch_no,
            expiry_date: batch.expiry,
            packing: batch.packing || selectedItem.packing || '',
            unit: batch.unit || selectedItem.unit || 'PCS',
            company_name: batch.company_name || selectedItem.company_name || '',
            hsn_code: batch.hsn_code || selectedItem.hsn_code || '',
            purchase_rate: parseFloat(batch.purchase_rate || 0),
            mrp: parseFloat(batch.mrp || 0),
            // Batch specific rates - from selected batch
            ws_rate: parseFloat(batch.ws_rate || 0),
            spl_rate: parseFloat(batch.spl_rate || 0),
            s_rate: parseFloat(batch.sale_rate || batch.s_rate || 0),
            discount_percent: 0,
            cgst_percent: parseFloat(batch.cgst_percent || 0),
            sgst_percent: parseFloat(batch.sgst_percent || 0),
            cess_percent: parseFloat(batch.cess_percent || 0),
            total_cl_qty: parseFloat(batch.total_cl_qty || 0),
            return_qty: 0,
            return_fqty: 0
        };

        const newIndex = returnItems.length;
        _legacy_addItemRow(item, newIndex);
        returnItems.push(item);
    }

    // LEGACY: Add a single item row to the table (Insert Orders)
    function _legacy_addItemRow(item, index) {
        const tbody = document.getElementById('itemsTableBody');
        const rowIndex = currentRowIndex++;
        
        const row = document.createElement('tr');
        row.id = `row-${rowIndex}`;
        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.item_code || ''}" readonly
                       onfocus="selectRowForCalculation(${rowIndex})"
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); openItemSelectionForRow(${rowIndex}); return false; }">
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.item_name || ''}" readonly>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${item.batch_no || ''}" readonly>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${item.expiry_date || ''}" readonly>
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
                       onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'purchase_rate'); return false; }"
                       onfocus="selectRowForCalculation(${rowIndex})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${rowIndex}][purchase_rate]" value="${item.purchase_rate || 0}" step="0.01" 
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
                <input type="number" class="form-control readonly-field" name="items[${rowIndex}][ft_rate]" value="0.00" step="0.01" readonly>
            </td>
            <td>
                <input type="number" class="form-control readonly-field" name="items[${rowIndex}][ft_amount]" value="0.00" readonly>
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
        
        // Always focus on newly added row and select it for calculation
        setTimeout(() => {
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            if (qtyInput) {
                // First select row for calculation to update sections
                selectRowForCalculation(rowIndex);
                
                // Then focus and select the input
                qtyInput.focus();
                qtyInput.select();
                
                // Trigger calculation update again to ensure everything is synced
                setTimeout(() => {
                    updateCalculationSection(rowIndex);
                    updateAdditionalDetails(rowIndex);
                }, 100);
            }
        }, 300);
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

    // Handle Discount Change
    function handleDiscountChange(rowIndex) {
        calculateRowAmount(rowIndex);
        if (selectedRowIndex === rowIndex) {
            updateCalculationSection(rowIndex);
        }
    }

    function triggerAddRowFromDisPercent(source = 'unknown') {
        if (disPercentEnterLoopLock) return;
        disPercentEnterLoopLock = true;

        console.log('[KB-PR][Dis%] trigger add row', { source });

        // Call add row directly (do not rely only on button click handlers).
        const createdRowIndex = addNewRow(true);
        if (createdRowIndex === null || createdRowIndex === undefined) {
            const addRowBtn = document.getElementById('addRowBtn');
            if (addRowBtn) addRowBtn.click();
        }

        setTimeout(() => {
            disPercentEnterLoopLock = false;
        }, 120);
    }

    // Handle Discount and Complete Row
    function handleDiscountAndCompleteRow(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (!row) return;

        const itemId = row.querySelector('input[name*="[item_id]"]')?.value || '';
        if (!itemId) {
            openItemSelectionForRow(rowIndex);
            return;
        }
        
        // Mark row as completed (permanent green)
        markRowAsCompleted(rowIndex);
        
        // Calculate the row amount
        calculateRowAmount(rowIndex);
        
        // Update summary totals
        recalculateTotals();
        
        // Clear calculation section
        clearCalculationSection();
        
        // Clear additional details section
        clearAdditionalDetails();
        
        // Remove focus
        if (document.activeElement && typeof document.activeElement.blur === 'function') {
            document.activeElement.blur();
        }
        
        // Clear selection
        selectedRowIndex = null;

        // Loop flow: Dis.% Enter should trigger Add Row and focus new row Code field.
        setTimeout(() => {
            triggerAddRowFromDisPercent('handleDiscountAndCompleteRow');
        }, 30);
    }

    // Mark row as completed (green background)
    function markRowAsCompleted(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (!row) return;
        
        row.style.backgroundColor = '#d4edda';
        row.dataset.completed = 'true';
        
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            cell.style.backgroundColor = '#d4edda';
        });
    }

    // Select Row for Calculation
    function selectRowForCalculation(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (!row) return;
        
        const isCompleted = row.dataset.completed === 'true';
        selectedRowIndex = rowIndex;
        
        // Clear all row highlights except completed ones
        clearRowHighlights();
        
        // Highlight selected row
        if (!isCompleted) {
            row.style.backgroundColor = '#e7f3ff';
            row.style.outline = '';
            row.style.outlineOffset = '';
        } else {
            // Completed row - keep green background but add border outline to show selection
            row.style.backgroundColor = '#d4edda';
            row.style.outline = '3px solid #0d6efd';
            row.style.outlineOffset = '-1px';
        }
        
        // Update calculation section
        updateCalculationSection(rowIndex);
        
        // Update additional details section
        updateAdditionalDetails(rowIndex);
    }

    // Clear row highlights except completed ones
    function clearRowHighlights() {
        const allRows = document.querySelectorAll('#itemsTableBody tr');
        allRows.forEach(r => {
            // Remove outline from all rows
            r.style.outline = '';
            r.style.outlineOffset = '';
            
            if (r.dataset.completed !== 'true') {
                r.style.backgroundColor = '';
            } else {
                r.style.backgroundColor = '#d4edda';
            }
        });
    }

    // Update Calculation Section
    function updateCalculationSection(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (!row) return;
        
        const itemData = JSON.parse(row.dataset.itemData || '{}');
        
        // Update HSN and tax information
        document.getElementById('calc_hsn_code').value = (itemData.hsn_code && itemData.hsn_code.trim() !== '') ? itemData.hsn_code : '---';
        document.getElementById('calc_cgst_percent').value = parseFloat(itemData.cgst_percent || 0).toFixed(2);
        document.getElementById('calc_sgst_percent').value = parseFloat(itemData.sgst_percent || 0).toFixed(2);
        document.getElementById('calc_cess_percent').value = parseFloat(itemData.cess_percent || 0).toFixed(2);
        
        // Update rates
        document.getElementById('calc_ws_rate').value = parseFloat(itemData.ws_rate || 0).toFixed(2);
        document.getElementById('calc_s_rate').value = parseFloat(itemData.s_rate || itemData.spl_rate || 0).toFixed(2);
        document.getElementById('calc_mrp').value = parseFloat(itemData.mrp || 0).toFixed(2);
        
        // Calculate and show tax amounts
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value || 0);
        const rate = parseFloat(row.querySelector('input[name*="[purchase_rate]"]')?.value || 0);
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

    // Update Additional Details Section
    function updateAdditionalDetails(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (!row) return;
        
        const itemData = JSON.parse(row.dataset.itemData || '{}');
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value || 0);
        const rate = parseFloat(row.querySelector('input[name*="[purchase_rate]"]')?.value || 0);
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
        const grossTotal = amountAfterDiscount; // Gross total before tax
        
        // Update additional fields - Packing, Unit, Company from item data
        document.getElementById('packing').value = itemData.packing || '';
        document.getElementById('unit').value = itemData.unit || 'PCS';
        document.getElementById('location').value = itemData.location || '';
        // Cl.Qty = Total closing qty across all batches for this item
        document.getElementById('clQty').value = parseFloat(itemData.total_cl_qty || 0).toFixed(0);
        
        // Update detailed calculation fields
        document.getElementById('addl_nt_amount').value = amount.toFixed(2);
        document.getElementById('addl_sc_amount').value = '0.00';
        document.getElementById('addl_dis_amount').value = discountAmount.toFixed(2);
        document.getElementById('addl_scm_amount').value = '0.00';
        document.getElementById('addl_dis1_amount').value = '0.00';
        document.getElementById('addl_tax_amount').value = totalTaxAmount.toFixed(2);
        document.getElementById('addl_gross_total').value = grossTotal.toFixed(2);
        document.getElementById('addl_sub_total').value = amountAfterDiscount.toFixed(2);
        document.getElementById('addl_net_amount').value = netAmount.toFixed(2);
        document.getElementById('addl_volume').value = '0';
        // Company name from item data
        document.getElementById('addl_company').value = itemData.company_name || '';
        document.getElementById('addl_scm_flag').value = '0';
        document.getElementById('addl_scm_value').value = '0';
        document.getElementById('addl_srino').value = '1';
        document.getElementById('addl_scm_percent').value = '0.00';
        document.getElementById('addl_dis1_percent').value = '0.00';
        document.getElementById('addl_percent').value = '10.00';
    }

    // Calculate Row Amount
    function calculateRowAmount(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (!row) return;
        
        const itemData = JSON.parse(row.dataset.itemData || '{}');
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value || 0);
        const rate = parseFloat(row.querySelector('input[name*="[purchase_rate]"]')?.value || 0);
        const discountPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value || 0);
        
        // Calculate F.T. Rate (Purchase Rate after discount)
        const ftRate = rate - (rate * discountPercent / 100);
        
        // Calculate F.T. Amount (Qty * F.T. Rate)
        const ftAmount = qty * ftRate;
        
        // Update F.T. Rate field
        const ftRateInput = row.querySelector('input[name*="[ft_rate]"]');
        if (ftRateInput) {
            ftRateInput.value = ftRate.toFixed(2);
        }
        
        // Update F.T. Amount field
        const ftAmountInput = row.querySelector('input[name*="[ft_amount]"]');
        if (ftAmountInput) {
            ftAmountInput.value = ftAmount.toFixed(2);
        }
        
        if (selectedRowIndex === rowIndex) {
            updateCalculationSection(rowIndex);
            updateAdditionalDetails(rowIndex);
        }
        
        recalculateTotals();
    }

    // Recalculate all totals
    function recalculateTotals() {
        const tbody = document.getElementById('itemsTableBody');
        const rows = tbody.querySelectorAll('tr');
        
        let totalAmount = 0;
        let totalDiscount = 0;
        let totalCgst = 0;
        let totalSgst = 0;
        let totalCess = 0;
        
        rows.forEach((row) => {
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            const rateInput = row.querySelector('input[name*="[purchase_rate]"]');
            const discountInput = row.querySelector('input[name*="[dis_percent]"]');
            
            let itemData = {};
            try {
                itemData = JSON.parse(row.dataset.itemData || '{}');
            } catch (e) {
                itemData = {};
            }
            const cgstPercent = parseFloat(itemData.cgst_percent || 0);
            const sgstPercent = parseFloat(itemData.sgst_percent || 0);
            const cessPercent = parseFloat(itemData.cess_percent || 0);
            
            if (qtyInput && rateInput) {
                const qty = parseFloat(qtyInput.value || 0);
                const rate = parseFloat(rateInput.value || 0);
                const discountPercent = parseFloat(discountInput?.value || 0);
                
                const amount = qty * rate;
                totalAmount += amount;
                
                const discountAmount = (amount * discountPercent) / 100;
                totalDiscount += discountAmount;
                
                const amountAfterDiscount = amount - discountAmount;
                totalCgst += (amountAfterDiscount * cgstPercent) / 100;
                totalSgst += (amountAfterDiscount * sgstPercent) / 100;
                totalCess += (amountAfterDiscount * cessPercent) / 100;
            }
        });
        
        // Calculate summary
        const ntAmount = totalAmount;
        const taxAmount = totalCgst + totalSgst + totalCess;
        const invAmount = ntAmount - totalDiscount + taxAmount;
        
        // Update summary fields
        document.getElementById('ntAmount').value = ntAmount.toFixed(2);
        document.getElementById('scAmount').value = '0.00';
        document.getElementById('disAmount').value = totalDiscount.toFixed(2);
        document.getElementById('scmAmount').value = '0.00';
        document.getElementById('taxAmount').value = taxAmount.toFixed(2);
        document.getElementById('invAmount').value = invAmount.toFixed(2);
        document.getElementById('scmPercent').value = '0.00';
        document.getElementById('tcsAmount').value = '0.00';
        document.getElementById('dis1Amount').value = '0.00';
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
        document.getElementById('calc_ws_rate').value = '0.00';
        document.getElementById('calc_s_rate').value = '0.00';
        document.getElementById('calc_mrp').value = '0.00';
    }

    // Clear additional details section
    function clearAdditionalDetails() {
        document.getElementById('packing').value = '';
        document.getElementById('unit').value = '';
        document.getElementById('clQty').value = '0';
        document.getElementById('location').value = '';
        document.getElementById('hsAmount').value = '0.00';
        document.getElementById('addl_nt_amount').value = '0.00';
        document.getElementById('addl_sc_amount').value = '0.00';
        document.getElementById('addl_dis_amount').value = '0.00';
        document.getElementById('addl_scm_amount').value = '0.00';
        document.getElementById('addl_dis1_amount').value = '0.00';
        document.getElementById('addl_tax_amount').value = '0.00';
        document.getElementById('addl_gross_total').value = '0.00';
        document.getElementById('addl_sub_total').value = '0.00';
        document.getElementById('addl_net_amount').value = '0.00';
        document.getElementById('addl_volume').value = '0';
        document.getElementById('addl_company').value = '';
        document.getElementById('addl_scm_flag').value = '0';
        document.getElementById('addl_scm_value').value = '0';
        document.getElementById('addl_srino').value = '1';
        document.getElementById('addl_scm_percent').value = '0.00';
        document.getElementById('addl_dis1_percent').value = '0.00';
        document.getElementById('addl_percent').value = '10.00';
    }

    // Remove row
    function removeRow(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (row) {
            row.remove();
            
            if (selectedRowIndex === rowIndex) {
                selectedRowIndex = null;
                clearCalculationSection();
                clearAdditionalDetails();
            }
            
            recalculateTotals();
        }
    }

    // LEGACY: Add new row button - Opens Insert Orders modal (DUPLICATE - SHOULD NOT BE USED)
    // This function was overriding the correct addNewRow() function
    // Renamed to prevent conflict
    function _legacy_addNewRowViaInsertOrders() {
        if (!getSelectedSupplierId()) {
            alert('Please select a supplier first!');
            return;
        }
        openInsertOrdersModal();
    }

    // Save Transaction
    function saveTransaction() {
        const tbody = document.getElementById('itemsTableBody');
        const rows = tbody.querySelectorAll('tr');
        
        if (rows.length === 0) {
            alert('Please add items to return before saving.');
            return;
        }
        
        const supplierId = getSelectedSupplierId();
        if (!supplierId) {
            alert('Please select a supplier.');
            return;
        }
        
        alert('Save functionality will be implemented with backend integration.');
    }

    // Fetch next transaction number
    function fetchNextTransactionNumber() {
        $.ajax({
            url: '{{ route("admin.purchase-return.next-trn-no") }}',
            method: 'GET',
            success: function(response) {
                $('#trn_no').val(response.next_trn_no);
            },
            error: function() {
                $('#trn_no').val('1');
            }
        });
    }

    // Helper function to format date
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = String(date.getFullYear()).slice(-2);
        return `${day}.${month}.${year}`;
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeInsertOrdersModal();
            closeCreditAdjustmentModal();
        }
    });

    // Capture fallback for Dis.% Enter:
    // protects against global handlers that consume Enter before inline handlers run.
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        const target = e.target;
        if (!target || !(target instanceof Element)) return;
        if (!target.matches('#itemsTableBody input[name*="[dis_percent]"]')) return;

        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') {
            e.stopImmediatePropagation();
        }

        const row = target.closest('tr');
        const rowId = row ? row.id : '';
        const rowIndex = rowId && rowId.startsWith('row-') ? parseInt(rowId.replace('row-', ''), 10) : NaN;

        console.log('[KB-PR][Dis%][Capture] Enter', { rowId, rowIndex });

        if (!Number.isNaN(rowIndex)) {
            handleDiscountAndCompleteRow(rowIndex);
        } else {
            triggerAddRowFromDisPercent('capture.no-row');
        }
    }, true);

    // Earliest capture layer for Dis.% Enter to avoid interference from other global handlers.
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        const target = e.target;
        if (!target || !(target instanceof Element)) return;
        if (!target.matches('#itemsTableBody input[name*="[dis_percent]"]')) return;

        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') {
            e.stopImmediatePropagation();
        }

        const row = target.closest('tr');
        const rowId = row ? row.id : '';
        const rowIndex = rowId && rowId.startsWith('row-') ? parseInt(rowId.replace('row-', ''), 10) : NaN;

        console.log('[KB-PR][Dis%][WindowCapture] Enter', { rowId, rowIndex });

        if (!Number.isNaN(rowIndex)) {
            handleDiscountAndCompleteRow(rowIndex);
        } else {
            triggerAddRowFromDisPercent('window.capture.no-row');
        }
    }, true);

    // Capture fallback for Code Enter:
    // ensures item modal opens even if global Enter handlers exist.
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        const target = e.target;
        if (!target || !(target instanceof Element)) return;
        if (!target.matches('#itemsTableBody input[name*="[code]"]')) return;
        if (codeEnterModalOpenLock) return;

        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') {
            e.stopImmediatePropagation();
        }

        const row = target.closest('tr');
        const rowId = row ? row.id : '';
        const rowIndex = rowId && rowId.startsWith('row-') ? parseInt(rowId.replace('row-', ''), 10) : NaN;

        console.log('[KB-PR][Code][Capture] Enter', { rowId, rowIndex });

        if (!Number.isNaN(rowIndex)) {
            codeEnterModalOpenLock = true;
            openItemSelectionForRow(rowIndex);
            setTimeout(() => {
                codeEnterModalOpenLock = false;
            }, 120);
        }
    }, true);

    // NOTE: Dis.% keyup fallback intentionally removed.
    // It was causing carry-over Enter from previous field to trigger Add Row immediately on focus.

    // Keyup fallback for Code Enter.
    document.addEventListener('keyup', function(e) {
        if (e.key !== 'Enter') return;
        const target = e.target;
        if (!target || !(target instanceof Element)) return;
        if (!target.matches('#itemsTableBody input[name*="[code]"]')) return;
        if (codeEnterModalOpenLock) return;

        const row = target.closest('tr');
        const rowId = row ? row.id : '';
        const rowIndex = rowId && rowId.startsWith('row-') ? parseInt(rowId.replace('row-', ''), 10) : NaN;

        console.log('[KB-PR][Code][KeyupFallback] Enter', { rowId, rowIndex });

        if (!Number.isNaN(rowIndex)) {
            codeEnterModalOpenLock = true;
            openItemSelectionForRow(rowIndex);
            setTimeout(() => {
                codeEnterModalOpenLock = false;
            }, 120);
        }
    }, true);

    // Ctrl+S => trigger Save Purchase Return
    document.addEventListener('keydown', function(e) {
        const isSaveShortcut = (e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S');
        if (!isSaveShortcut) return;

        const creditAdjustModalOpen = !!document.getElementById('creditAdjustModal') &&
            document.getElementById('creditAdjustModal').classList.contains('show');
        const confirmModalOpen = !!document.getElementById('confirmModal') &&
            document.getElementById('confirmModal').classList.contains('show');
        if (creditAdjustModalOpen || confirmModalOpen) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') {
            e.stopImmediatePropagation();
        }

        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.click();
        } else if (typeof askCreditAdjustment === 'function') {
            askCreditAdjustment();
        }
    }, true);

    // Credit Adjustment Modal Variables
    let creditAdjustmentData = [];
    let returnNetAmount = 0;

    // Open Credit Adjustment Modal
    function openCreditAdjustmentModal() {
        const supplierId = getSelectedSupplierId();
        if (!supplierId) {
            alert('Please select a supplier first!');
            return;
        }

        const tbody = document.getElementById('itemsTableBody');
        if (!tbody || tbody.querySelectorAll('tr').length === 0) {
            alert('Please add items before credit adjustment!');
            return;
        }

        // Get net amount
        returnNetAmount = parseFloat(document.getElementById('invAmount')?.value || 0);
        if (returnNetAmount <= 0) {
            alert('Return amount must be greater than 0!');
            return;
        }

        // Fetch supplier invoices
        fetch(`{{ url('admin/purchase-return/supplier-invoices') }}/${supplierId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCreditAdjustmentModal(data.invoices);
            } else {
                alert(data.message || 'Failed to load invoices');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load invoices');
        });
    }

    function getCreditAdjustInputs() {
        return Array.from(document.querySelectorAll('#creditAdjustModal .adjust-amount-input'));
    }

    function getCreditAdjustButtons() {
        const saveBtn = document.getElementById('creditAdjustSaveBtn');
        const cancelBtn = document.getElementById('creditAdjustCancelBtn');
        return [saveBtn, cancelBtn].filter(Boolean);
    }

    function setActiveCreditAdjustInput(index) {
        const inputs = getCreditAdjustInputs();
        if (!inputs.length) return;

        const len = inputs.length;
        creditAdjustActiveInputIndex = ((index % len) + len) % len;

        inputs.forEach((input, i) => {
            input.classList.toggle('kb-active-choice', i === creditAdjustActiveInputIndex);
        });

        const active = inputs[creditAdjustActiveInputIndex];
        active.focus({ preventScroll: true });
        active.select();
        active.scrollIntoView({ block: 'nearest' });
    }

    function setActiveCreditAdjustButton(index) {
        const buttons = getCreditAdjustButtons();
        if (!buttons.length) return;

        const len = buttons.length;
        creditAdjustActiveButtonIndex = ((index % len) + len) % len;

        buttons.forEach((btn, i) => {
            btn.classList.toggle('kb-active-choice', i === creditAdjustActiveButtonIndex);
        });

        buttons[creditAdjustActiveButtonIndex].focus({ preventScroll: true });
    }

    function unbindCreditAdjustModalKeyboard() {
        if (!creditAdjustModalKeydownHandler) return;
        document.removeEventListener('keydown', creditAdjustModalKeydownHandler, true);
        creditAdjustModalKeydownHandler = null;
    }

    function bindCreditAdjustModalKeyboard() {
        unbindCreditAdjustModalKeyboard();

        const inputs = getCreditAdjustInputs();
        if (inputs.length) {
            inputs.forEach((input, idx) => {
                input.addEventListener('focus', function() {
                    creditAdjustActiveInputIndex = idx;
                    inputs.forEach((i, j) => i.classList.toggle('kb-active-choice', j === idx));
                });
            });
            setActiveCreditAdjustInput(0);
        } else {
            setActiveCreditAdjustButton(0);
        }

        creditAdjustModalKeydownHandler = function(e) {
            const modal = document.getElementById('creditAdjustModal');
            if (!modal || !modal.classList.contains('show')) {
                unbindCreditAdjustModalKeyboard();
                return;
            }

            const isCtrlS = (e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S');
            const key = e.key;
            if (!isCtrlS && !['Escape', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Enter'].includes(key)) return;

            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') {
                e.stopImmediatePropagation();
            }

            if (isCtrlS) {
                saveCreditAdjustment();
                return;
            }

            if (key === 'Escape') {
                closeCreditAdjustmentModal();
                return;
            }

            const activeEl = document.activeElement;
            const inputsNow = getCreditAdjustInputs();
            const buttonsNow = getCreditAdjustButtons();
            const activeInputIndex = inputsNow.indexOf(activeEl);
            const activeButtonIndex = buttonsNow.indexOf(activeEl);

            if (activeInputIndex !== -1) {
                if (key === 'ArrowDown' || key === 'Enter') {
                    setActiveCreditAdjustInput(activeInputIndex + 1);
                    return;
                }
                if (key === 'ArrowUp') {
                    setActiveCreditAdjustInput(activeInputIndex - 1);
                    return;
                }
                if (key === 'ArrowRight') {
                    setActiveCreditAdjustButton(0);
                    return;
                }
            }

            if (activeButtonIndex !== -1) {
                if (key === 'ArrowLeft' || key === 'ArrowUp') {
                    setActiveCreditAdjustButton(activeButtonIndex - 1);
                    return;
                }
                if (key === 'ArrowRight' || key === 'ArrowDown') {
                    setActiveCreditAdjustButton(activeButtonIndex + 1);
                    return;
                }
                if (key === 'Enter') {
                    const btn = buttonsNow[activeButtonIndex];
                    if (btn) btn.click();
                    return;
                }
            }

            if (inputsNow.length) {
                setActiveCreditAdjustInput(0);
            } else if (buttonsNow.length) {
                setActiveCreditAdjustButton(0);
            }
        };

        document.addEventListener('keydown', creditAdjustModalKeydownHandler, true);
    }

    // Show Credit Adjustment Modal
    function showCreditAdjustmentModal(invoices) {
        creditAdjustmentData = invoices;
        
        const modalHTML = `
            <div class="credit-adjust-modal-backdrop" id="creditAdjustModalBackdrop" onclick="closeCreditAdjustmentModal()"></div>
            <div class="credit-adjust-modal" id="creditAdjustModal">
                <div class="credit-adjust-modal-content">
                    <div class="credit-adjust-modal-header">
                        <h5 class="credit-adjust-modal-title">Purchase Return Adjustment</h5>
                        <button type="button" class="btn-close-modal" onclick="closeCreditAdjustmentModal()">&times;</button>
                    </div>
                    <div class="credit-adjust-modal-body">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-sm" style="font-size: 12px; margin-bottom: 0;">
                                <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                                    <tr>
                                        <th style="width: 60px; text-align: center;">SRNO.</th>
                                        <th style="width: 120px;">TRANS NO.</th>
                                        <th style="width: 100px; text-align: center;">DATE</th>
                                        <th style="width: 110px; text-align: right;">BILL AMT.</th>
                                        <th style="width: 110px; text-align: center;">ADJUSTED</th>
                                        <th style="width: 110px; text-align: right;">BALANCE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${invoices.length === 0 ? 
                                        '<tr><td colspan="6" class="text-center">No outstanding invoices found</td></tr>' :
                                        invoices.map((inv, idx) => `
                                            <tr>
                                                <td style="text-align: center;">${idx + 1}</td>
                                                <td>PB / ${inv.bill_no || ''}</td>
                                                <td style="text-align: center;">${inv.bill_date || ''}</td>
                                                <td style="text-align: right;">${parseFloat(inv.balance_amount).toFixed(2)}</td>
                                                <td style="text-align: center;">
                                                    <input type="number" class="form-control form-control-sm adjust-amount-input" 
                                                           data-invoice-id="${inv.id}" 
                                                           data-balance="${inv.balance_amount}"
                                                           value="0.00" min="0" max="${inv.balance_amount}" step="0.01"
                                                           onchange="updateAdjustmentTotal()"
                                                           oninput="updateBalanceColumn(this)"
                                                           style="width: 90px; font-size: 11px; text-align: right;">
                                                </td>
                                                <td style="text-align: right;" class="balance-col" data-original="${inv.balance_amount}">${parseFloat(inv.balance_amount).toFixed(2)}</td>
                                            </tr>
                                        `).join('')
                                    }
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="credit-adjust-modal-footer" style="justify-content: space-between;">
                        <div style="font-size: 13px; color: #d00;">
                            <strong>BALANCE (Rs): </strong><span id="totalBalance">${returnNetAmount.toFixed(2)}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size: 11px; color: #666; margin-right: 10px;">EXIT : &lt;ESC&gt;</span>
                            <button type="button" class="btn btn-success btn-sm" id="creditAdjustSaveBtn" onclick="saveCreditAdjustment()">
                                <i class="bi bi-check-circle"></i> Save
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="creditAdjustCancelBtn" onclick="closeCreditAdjustmentModal()">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal
        const existingModal = document.getElementById('creditAdjustModal');
        if (existingModal) existingModal.remove();
        const existingBackdrop = document.getElementById('creditAdjustModalBackdrop');
        if (existingBackdrop) existingBackdrop.remove();
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        setTimeout(() => {
            document.getElementById('creditAdjustModalBackdrop').classList.add('show');
            document.getElementById('creditAdjustModal').classList.add('show');
            bindCreditAdjustModalKeyboard();
        }, 10);
    }

    // Update balance column when adjusted amount changes
    function updateBalanceColumn(input) {
        const row = input.closest('tr');
        const balanceCol = row.querySelector('.balance-col');
        const originalBalance = parseFloat(balanceCol.dataset.original || 0);
        const adjustedValue = parseFloat(input.value || 0);
        
        // Validate against original balance
        if (adjustedValue > originalBalance) {
            input.value = originalBalance.toFixed(2);
        }
        
        const newBalance = originalBalance - parseFloat(input.value || 0);
        balanceCol.textContent = newBalance.toFixed(2);
    }

    // Update adjustment total
    function updateAdjustmentTotal() {
        const inputs = document.querySelectorAll('.adjust-amount-input');
        let totalAdjusted = 0;
        
        inputs.forEach(input => {
            const value = parseFloat(input.value || 0);
            const balance = parseFloat(input.dataset.balance || 0);
            
            // Validate against balance
            if (value > balance) {
                input.value = balance.toFixed(2);
                input.style.borderColor = 'red';
            } else {
                input.style.borderColor = '';
            }
            
            totalAdjusted += parseFloat(input.value || 0);
        });
        
        // Validate against return amount
        if (totalAdjusted > returnNetAmount) {
            alert('Total adjustment cannot exceed return amount!');
        }
        
        // Update remaining balance
        const remainingBalance = returnNetAmount - totalAdjusted;
        document.getElementById('totalBalance').textContent = remainingBalance.toFixed(2);
    }

    // Close Credit Adjustment Modal
    function closeCreditAdjustmentModal() {
        unbindCreditAdjustModalKeyboard();
        const modal = document.getElementById('creditAdjustModal');
        const backdrop = document.getElementById('creditAdjustModalBackdrop');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        
        setTimeout(() => {
            if (modal) modal.remove();
            if (backdrop) backdrop.remove();
        }, 300);
    }

    // Save Credit Adjustment and Submit Transaction
    function saveCreditAdjustment() {
        const inputs = document.querySelectorAll('.adjust-amount-input');
        const adjustments = [];
        let totalAdjusted = 0;
        
        inputs.forEach(input => {
            const amount = parseFloat(input.value || 0);
            if (amount > 0) {
                adjustments.push({
                    purchase_transaction_id: input.dataset.invoiceId,
                    amount: amount
                });
                totalAdjusted += amount;
            }
        });
        
        if (totalAdjusted > returnNetAmount) {
            alert('Total adjustment cannot exceed return amount!');
            return;
        }
        
        closeCreditAdjustmentModal();
        saveTransactionWithAdjustments(adjustments);
    }

    // Save Transaction with Adjustments
    function saveTransactionWithAdjustments(adjustments) {
        const tbody = document.getElementById('itemsTableBody');
        const rows = tbody.querySelectorAll('tr');
        
        if (rows.length === 0) {
            alert('Please add items before saving.');
            return;
        }
        
        const supplierId = getSelectedSupplierId();
        if (!supplierId) {
            alert('Please select a supplier.');
            return;
        }
        
        // Collect items data with calculated tax amounts
        const items = [];
        rows.forEach((row, index) => {
            const itemData = JSON.parse(row.dataset.itemData || '{}');
            const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value || 0);
            
            if (qty > 0) {
                const rate = parseFloat(row.querySelector('input[name*="[purchase_rate]"]')?.value || 0);
                const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value || 0);
                const freeQty = parseFloat(row.querySelector('input[name*="[free_qty]"]')?.value || 0);
                
                // Calculate amounts
                const amount = qty * rate;
                const discountAmount = (amount * disPercent) / 100;
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
                
                items.push({
                    item_id: itemData.item_id,
                    batch_id: itemData.batch_id,
                    code: itemData.item_code,
                    name: itemData.item_name,
                    batch: itemData.batch_no,
                    expiry: itemData.expiry_date,
                    qty: qty,
                    free_qty: freeQty,
                    purchase_rate: rate,
                    dis_percent: disPercent,
                    ft_rate: parseFloat(row.querySelector('input[name*="[ft_rate]"]')?.value || 0),
                    ft_amount: parseFloat(row.querySelector('input[name*="[ft_amount]"]')?.value || 0),
                    mrp: itemData.mrp || 0,
                    ws_rate: itemData.ws_rate || 0,
                    s_rate: itemData.s_rate || 0,
                    spl_rate: itemData.spl_rate || 0,
                    cgst_percent: cgstPercent,
                    sgst_percent: sgstPercent,
                    cess_percent: cessPercent,
                    cgst_amount: cgstAmount,
                    sgst_amount: sgstAmount,
                    cess_amount: cessAmount,
                    tax_amount: totalTaxAmount,
                    net_amount: netAmount,
                    hsn_code: itemData.hsn_code || '',
                    packing: itemData.packing || '',
                    unit: itemData.unit || '',
                    company_name: itemData.company_name || ''
                });
            }
        });
        
        const formData = {
            return_date: document.getElementById('return_date').value,
            supplier_id: supplierId,
            supplier_name: getSelectedSupplierName(),
            nt_amount: parseFloat(document.getElementById('ntAmount')?.value || 0),
            dis_amount: parseFloat(document.getElementById('disAmount')?.value || 0),
            tax_amount: parseFloat(document.getElementById('taxAmount')?.value || 0),
            net_amount: parseFloat(document.getElementById('invAmount')?.value || 0),
            items: items,
            adjustments: adjustments
        };
        
        // 🔥 Mark as saving to prevent exit confirmation dialog
        if (typeof window.markAsSaving === 'function') {
            window.markAsSaving();
        }
        
        // Submit to server
        fetch('{{ route("admin.purchase-return.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal(data.pr_no, data.net_amount || document.getElementById('invAmount')?.value || 0);
            } else {
                alert(data.message || 'Error saving purchase return');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving purchase return');
        });
    }

    function getConfirmModalButtons() {
        const noBtn = document.getElementById('confirmNoBtn');
        const yesBtn = document.getElementById('confirmYesBtn');
        return [noBtn, yesBtn].filter(Boolean);
    }

    function setConfirmModalActiveButton(index) {
        const buttons = getConfirmModalButtons();
        if (!buttons.length) return;

        const len = buttons.length;
        confirmModalActiveIndex = ((index % len) + len) % len;

        buttons.forEach((btn, i) => {
            const active = i === confirmModalActiveIndex;
            btn.classList.toggle('kb-active-choice', active);
            btn.setAttribute('data-kb-active', active ? '1' : '0');
        });

        buttons[confirmModalActiveIndex].focus({ preventScroll: true });
    }

    function unbindConfirmModalKeyboard() {
        if (!confirmModalKeydownHandler) return;
        document.removeEventListener('keydown', confirmModalKeydownHandler, true);
        confirmModalKeydownHandler = null;
    }

    function bindConfirmModalKeyboard() {
        unbindConfirmModalKeyboard();

        confirmModalKeydownHandler = function(e) {
            const modal = document.getElementById('confirmModal');
            if (!modal || !modal.classList.contains('show')) {
                unbindConfirmModalKeyboard();
                return;
            }

            const key = e.key;
            if (!['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Enter', 'Escape'].includes(key)) return;

            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') {
                e.stopImmediatePropagation();
            }

            if (key === 'ArrowLeft' || key === 'ArrowUp') {
                setConfirmModalActiveButton(confirmModalActiveIndex - 1);
                return;
            }

            if (key === 'ArrowRight' || key === 'ArrowDown') {
                setConfirmModalActiveButton(confirmModalActiveIndex + 1);
                return;
            }

            if (key === 'Enter') {
                const buttons = getConfirmModalButtons();
                const btn = buttons[confirmModalActiveIndex];
                if (btn) btn.click();
                return;
            }

            if (key === 'Escape') {
                closeConfirmModal();
            }
        };

        document.addEventListener('keydown', confirmModalKeydownHandler, true);
        setConfirmModalActiveButton(0);
    }

    // Ask user if they want to adjust credit note
    function askCreditAdjustment() {
        const tbody = document.getElementById('itemsTableBody');
        if (!tbody || tbody.querySelectorAll('tr').length === 0) {
            alert('Please add items before saving.');
            return;
        }
        
        const supplierId = getSelectedSupplierId();
        if (!supplierId) {
            alert('Please select a supplier.');
            return;
        }

        // Show confirmation modal
        const confirmHTML = `
            <div class="confirm-modal-backdrop" id="confirmModalBackdrop"></div>
            <div class="confirm-modal" id="confirmModal">
                <div class="confirm-modal-content">
                    <div class="confirm-modal-header">
                        <h5>Credit Note Adjustment</h5>
                    </div>
                    <div class="confirm-modal-body">
                        <p style="font-size: 14px; margin: 0;">Do you want to adjust this return amount against supplier's outstanding invoices?</p>
                    </div>
                    <div class="confirm-modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" id="confirmNoBtn" onclick="closeConfirmAndSave()">No, Just Save</button>
                        <button type="button" class="btn btn-success btn-sm" id="confirmYesBtn" onclick="closeConfirmAndOpenAdjustment()">Yes, Adjust</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', confirmHTML);
        setTimeout(() => {
            document.getElementById('confirmModalBackdrop').classList.add('show');
            document.getElementById('confirmModal').classList.add('show');
            bindConfirmModalKeyboard();
        }, 10);
    }

    function closeConfirmModal() {
        unbindConfirmModalKeyboard();
        const modal = document.getElementById('confirmModal');
        const backdrop = document.getElementById('confirmModalBackdrop');
        if (modal) modal.remove();
        if (backdrop) backdrop.remove();
    }

    function closeConfirmAndSave() {
        closeConfirmModal();
        saveTransactionWithAdjustments([]);
    }

    function closeConfirmAndOpenAdjustment() {
        closeConfirmModal();
        openCreditAdjustmentModal();
    }

    function getSuccessModalButtons() {
        const newBtn = document.getElementById('successNewBtn');
        const okBtn = document.getElementById('successOkBtn');
        return [newBtn, okBtn].filter(Boolean);
    }

    function setSuccessModalActiveButton(index) {
        const buttons = getSuccessModalButtons();
        if (!buttons.length) return;

        const len = buttons.length;
        successModalActiveIndex = ((index % len) + len) % len;

        buttons.forEach((btn, i) => {
            const isActive = i === successModalActiveIndex;
            btn.classList.toggle('kb-active-choice', isActive);
            btn.setAttribute('data-kb-active', isActive ? '1' : '0');
        });

        buttons[successModalActiveIndex].focus({ preventScroll: true });
    }

    function unbindSuccessModalKeyboard() {
        if (!successModalKeydownHandler) return;
        document.removeEventListener('keydown', successModalKeydownHandler, true);
        successModalKeydownHandler = null;
    }

    function bindSuccessModalKeyboard() {
        unbindSuccessModalKeyboard();

        successModalKeydownHandler = function(e) {
            const modal = document.getElementById('successModal');
            if (!modal || !modal.classList.contains('show')) {
                unbindSuccessModalKeyboard();
                return;
            }

            const key = e.key;
            if (!['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Enter', 'Escape'].includes(key)) return;

            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') {
                e.stopImmediatePropagation();
            }

            if (key === 'ArrowLeft' || key === 'ArrowUp') {
                setSuccessModalActiveButton(successModalActiveIndex - 1);
                return;
            }

            if (key === 'ArrowRight' || key === 'ArrowDown') {
                setSuccessModalActiveButton(successModalActiveIndex + 1);
                return;
            }

            if (key === 'Enter') {
                const buttons = getSuccessModalButtons();
                const btn = buttons[successModalActiveIndex];
                if (btn) btn.click();
                return;
            }

            if (key === 'Escape') {
                closeSuccessModal();
            }
        };

        document.addEventListener('keydown', successModalKeydownHandler, true);
        setSuccessModalActiveButton(1); // default on OK
    }

    // Original Save Transaction (without adjustments)
    function saveTransaction() {
        saveTransactionWithAdjustments([]);
    }

    // Show Success Modal
    function showSuccessModal(prNo, netAmount) {
        const modalHTML = `
            <div class="success-modal-backdrop" id="successModalBackdrop"></div>
            <div class="success-modal" id="successModal">
                <div class="success-modal-content">
                    <div class="success-modal-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="success-modal-body">
                        <h4>Purchase Return Saved Successfully!</h4>
                        <p>Your purchase return has been recorded.</p>
                        <div class="pr-number">PR No: ${prNo}</div>
                        <p style="margin-top: 10px;"><strong>Net Amount:</strong> ₹${parseFloat(netAmount).toFixed(2)}</p>
                    </div>
                    <div class="success-modal-footer">
                        <button type="button" class="btn btn-outline-secondary" id="successNewBtn" onclick="closeSuccessAndNew()">
                            <i class="bi bi-plus-circle me-1"></i> New Return
                        </button>
                        <button type="button" class="btn btn-success" id="successOkBtn" onclick="closeSuccessModal()">
                            <i class="bi bi-check me-1"></i> OK
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Remove all existing modals before showing success modal
        const existingSuccessModal = document.getElementById('successModal');
        if (existingSuccessModal) existingSuccessModal.remove();
        const existingSuccessBackdrop = document.getElementById('successModalBackdrop');
        if (existingSuccessBackdrop) existingSuccessBackdrop.remove();
        
        // Remove credit adjustment modal
        const existingCreditModal = document.getElementById('creditAdjustmentModal');
        if (existingCreditModal) existingCreditModal.remove();
        const existingCreditBackdrop = document.getElementById('creditAdjustmentModalBackdrop');
        if (existingCreditBackdrop) existingCreditBackdrop.remove();
        
        // Remove confirmation modal
        const existingConfirmModal = document.getElementById('confirmModal');
        if (existingConfirmModal) existingConfirmModal.remove();
        const existingConfirmBackdrop = document.getElementById('confirmModalBackdrop');
        if (existingConfirmBackdrop) existingConfirmBackdrop.remove();
        
        // Remove insert orders modal
        const existingOrdersModal = document.getElementById('insertOrdersModal');
        if (existingOrdersModal) existingOrdersModal.remove();
        const existingOrdersBackdrop = document.getElementById('insertOrdersModalBackdrop');
        if (existingOrdersBackdrop) existingOrdersBackdrop.remove();
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        setTimeout(() => {
            document.getElementById('successModalBackdrop').classList.add('show');
            document.getElementById('successModal').classList.add('show');
            bindSuccessModalKeyboard();
        }, 10);
    }

    // Close Success Modal
    function closeSuccessModal() {
        unbindSuccessModalKeyboard();
        const modal = document.getElementById('successModal');
        const backdrop = document.getElementById('successModalBackdrop');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        
        setTimeout(() => {
            if (modal) modal.remove();
            if (backdrop) backdrop.remove();
            window.location.reload();
        }, 300);
    }

    // Close Success Modal and Start New Return
    function closeSuccessAndNew() {
        unbindSuccessModalKeyboard();
        const modal = document.getElementById('successModal');
        const backdrop = document.getElementById('successModalBackdrop');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        
        setTimeout(() => {
            if (modal) modal.remove();
            if (backdrop) backdrop.remove();
            window.location.reload();
        }, 300);
    }
</script>

<style>
/* Credit Adjustment Modal Styles */
.credit-adjust-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99999998;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.credit-adjust-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.credit-adjust-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 90%;
    max-width: 700px;
    max-height: 90vh;
    background: #f0f0f0;
    border: 2px solid #333;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    z-index: 99999999;
    opacity: 0;
    transition: all 0.3s ease;
}

.credit-adjust-modal.show {
    display: block;
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.credit-adjust-modal-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    max-height: 90vh;
}

.credit-adjust-modal-header {
    padding: 8px 15px;
    background: #f0f0f0;
    border-bottom: 1px solid #ccc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.credit-adjust-modal-title {
    margin: 0;
    font-size: 14px;
    font-weight: normal;
    color: #333;
}

.btn-close-modal {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #666;
}

.credit-adjust-modal-body {
    padding: 10px;
    overflow-y: auto;
    flex: 1;
    background: #fff;
}

.credit-adjust-modal-body table {
    border-collapse: collapse;
    width: 100%;
}

.credit-adjust-modal-body table th {
    background: #e9ecef;
    border: 1px solid #999;
    padding: 8px;
    font-weight: bold;
    color: #333;
}

.credit-adjust-modal-body table td {
    border: 1px solid #999;
    padding: 6px 8px;
}

.credit-adjust-modal-body table tbody tr:hover {
    background: #f5f5f5;
}

.credit-adjust-modal-footer {
    padding: 10px 15px;
    background: #f0f0f0;
    border-top: 1px solid #ccc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.adjust-amount-input {
    border: 1px solid #999 !important;
    background: #fff;
}

.adjust-amount-input:focus {
    border-color: #0066cc !important;
    outline: none;
}

#creditAdjustModal .adjust-amount-input.kb-active-choice,
#creditAdjustModal .kb-active-choice {
    outline: 2px solid #0d6efd;
    outline-offset: 1px;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Confirmation Modal Styles */
.confirm-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99999996;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.confirm-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.confirm-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 400px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    z-index: 99999997;
    opacity: 0;
    transition: all 0.3s ease;
}

.confirm-modal.show {
    display: block;
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.confirm-modal-content {
    padding: 0;
}

.confirm-modal-header {
    padding: 12px 15px;
    background: #007bff;
    color: white;
    border-radius: 8px 8px 0 0;
}

.confirm-modal-header h5 {
    margin: 0;
    font-size: 15px;
}

.confirm-modal-body {
    padding: 20px 15px;
}

.confirm-modal-footer {
    padding: 10px 15px;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.confirm-modal-footer .kb-active-choice {
    outline: 2px solid #0d6efd;
    outline-offset: 1px;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Success Modal Styles */
.success-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99999998;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.success-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.success-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 420px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 99999999;
    opacity: 0;
    transition: all 0.3s ease;
    overflow: hidden;
}

.success-modal.show {
    display: block;
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.success-modal-content {
    text-align: center;
    padding: 0;
}

.success-modal-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    padding: 30px;
}

.success-modal-icon i {
    font-size: 60px;
    color: white;
    animation: successPulse 0.5s ease-out;
}

@keyframes successPulse {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); opacity: 1; }
}

.success-modal-body {
    padding: 25px 20px;
}

.success-modal-body h4 {
    color: #28a745;
    margin-bottom: 10px;
    font-weight: 600;
}

.success-modal-body p {
    color: #666;
    margin-bottom: 5px;
    font-size: 14px;
}

.success-modal-body .pr-number {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    background: #f8f9fa;
    padding: 10px 20px;
    border-radius: 8px;
    display: inline-block;
    margin: 10px 0;
}

.success-modal-footer {
    padding: 15px 20px 25px;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.success-modal-footer .btn {
    min-width: 120px;
}

.success-modal-footer .kb-active-choice {
    outline: 2px solid #0d6efd;
    outline-offset: 1px;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush
