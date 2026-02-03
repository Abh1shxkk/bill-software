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
                                        <input type="text" id="trn_no" name="trn_no" class="form-control" style="width: 140px;" placeholder=""
                                               onkeydown="if(event.key === 'Enter') { event.preventDefault(); loadTransactionByPrNo(); }">
                                    </div>
                                    <div>
                                        <button type="button" id="insertOrderBtn" class="btn btn-sm btn-info" style="width: 100%;">
                                            <i class="bi bi-list-check"></i> Past PR
                                        </button>
                                    </div>
                                </div>

                                <!-- Right Side - Inner Card prt -->
                                <div class="inner-card-prt flex-grow-1 overflow-hidden">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <div class="field-group">
                                                <label style="width: 100px;">Supplier :</label>
                                                <select id="supplier_id" name="supplier_id" class="form-control" required>
                                                    <option value="">Select Supplier</option>
                                                    @foreach($suppliers as $supplier)
                                                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
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
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="field-group">
                                                <label style="width: 100px;">Remarks :</label>
                                                <input type="text" class="form-control" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-1">
                                        <div class="col-md-2">
                                            <div class="field-group">
                                                <label>Tax:</label>
                                                <input type="text" class="form-control" value="Y" maxlength="1"
                                                    style="width: 50px;">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="field-group">
                                                <label style="width: 80px;">Rate Diff :</label>
                                                <input type="text" class="form-control" value="N" maxlength="1"
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
                                <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
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

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'purchase-return',
    'showStock' => true,
    'rateType' => 'pur_rate',
    'showCompany' => true,
    'showHsn' => true,
    'batchModalId' => 'batchSelectionModal',
])

@include('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'purchase-return',
    'showOnlyAvailable' => true,
    'rateType' => 'pur_rate',
    'showCostDetails' => true,
])

@endsection

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
    let currentTransactionId = null; // Track transaction ID for update vs create

    $(document).ready(function() {
        // Initialize transaction number
        fetchNextTransactionNumber();

        // Update day when date changes
        $('#return_date').on('change', function() {
            const date = new Date($(this).val());
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $('#day_name').val(days[date.getDay()]);
        });

        // Supplier selection
        $('#supplier_id').on('change', function() {
            const supplierId = $(this).val();
            if (supplierId) {
                selectedSupplier = {
                    id: supplierId,
                    name: $(this).find('option:selected').text()
                };
            } else {
                selectedSupplier = null;
            }
        });

        // Insert Orders button - show past purchase returns
        $('#insertOrderBtn').on('click', function(e) {
            e.preventDefault();
            openPastReturnsModal();
        });
    });

    // Add New Row - Opens reusable item selection modal
    function addNewRow() {
        if (!selectedSupplier) {
            alert('Please select a supplier first!');
            return;
        }
        
        // Use reusable item selection modal
        if (typeof openItemModal_chooseItemsModal === 'function') {
            openItemModal_chooseItemsModal();
        } else {
            console.error('Item selection modal not initialized');
            alert('Item selection modal not initialized. Please reload the page.');
        }
    }

    // Callback function when item and batch are selected from reusable modal
    window.onItemBatchSelectedFromModal = function(item, batch) {
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
        if (typeof selectRowForCalculation === 'function') selectRowForCalculation(rowIndex);
        
        // Focus on qty input
        setTimeout(() => {
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            if (qtyInput) {
                qtyInput.focus();
                qtyInput.select();
            }
        }, 100);
        
        alert('Item added! Enter return quantity.');
        if (typeof calculateRowAmount === 'function') calculateRowAmount(rowIndex);
        if (typeof calculateTotals === 'function') calculateTotals();
    };

    // Open Insert Orders Modal
    function openInsertOrdersModal() {
        if (!selectedSupplier) {
            alert('Please select a supplier first!');
            return;
        }
        
        // Fetch all items
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
            showItemSelectionModal(allItems);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load items');
        });
    }

    // Show item selection modal
    function showItemSelectionModal(items) {
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
                                   onkeyup="filterItems()"
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
                                                        onclick='selectItemForBatch(${JSON.stringify(itemData).replace(/'/g, "\\'")})' 
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

    // Filter items in the selection table
    function filterItems() {
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

    // Select item and show batch modal
    function selectItemForBatch(item) {
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
            loadBatchesForSupplierAndItem(selectedSupplier.id, item.id, false);
        } else {
            // Add row - show ALL batches of this item (any supplier)
            loadAllBatchesForItem(item.id);
        }
    }

    // Load batches from past purchases
    function loadBatchesForSupplierAndItem(supplierId, itemId, isAddRow = false) {
        fetch(`{{ route("admin.purchase-return.batches") }}?supplier_id=${supplierId}&item_id=${itemId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showBatchSelectionModal(data.batches || [], isAddRow);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load batches');
        });
    }

    // Load all batches for item (any supplier) - for add row
    function loadAllBatchesForItem(itemId) {
        // Fetch batches using the same getBatches API but without supplier filter
        // This will get all batches for the item with total_cl_qty calculated
        fetch(`{{ route("admin.purchase-return.batches") }}?item_id=${itemId}&supplier_id=all`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showBatchSelectionModal(data.batches || [], true); // true = add row format
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            alert('Failed to load batches for this item');
        });
    }

    // Show batch selection modal
    function showBatchSelectionModal(batches, isAddRow = false) {
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
                            <strong>Supplier:</strong> ${selectedSupplier.name}
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
            const currentSupplierId = selectedSupplier.id;
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
                        addItemToReturnTable(batch);
                    } else {
                        // Different supplier - show warning
                        const supplierName = data.batch_supplier_name || 'Unknown Supplier';
                        const confirmMessage = `⚠️ Warning: This batch "${batch.batch_no || 'Unknown'}" is from "${supplierName}", not from the selected supplier "${selectedSupplier.name}".
            
Batch Details:
• Supplier: ${supplierName}
• Date: ${batch.bill_date || ''}
• Bill No: ${batch.bill_no || 'N/A'}
• Rate: ₹${parseFloat(batch.purchase_rate || 0).toFixed(2)}

Do you still want to add this batch to the return?`;
                        
                        if (confirm(confirmMessage)) {
                            closeInsertOrdersModal();
                            addItemToReturnTable(batch);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error verifying supplier:', error);
                    // Fallback - add directly if verification fails
                    closeInsertOrdersModal();
                    addItemToReturnTable(batch);
                });
                return; // Exit here to wait for AJAX response
            }
        }
        
        closeInsertOrdersModal();
        addItemToReturnTable(batch);
    }

    // Global variables for table management
    let currentRowIndex = 0;
    let selectedRowIndex = null;

    // Add item to return table - Enhanced version with proper structure
    function addItemToReturnTable(batch) {
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
        console.log('Batch data received:', batch);
        console.log('Batch total_cl_qty:', batch.total_cl_qty);
        
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
        
        console.log('Item data created with total_cl_qty:', item.total_cl_qty);

        const newIndex = returnItems.length;
        addItemRow(item, newIndex);
        returnItems.push(item);
    }

    // Add a single item row to the table
    function addItemRow(item, index) {
        const tbody = document.getElementById('itemsTableBody');
        const rowIndex = currentRowIndex++;
        
        const row = document.createElement('tr');
        row.id = `row-${rowIndex}`;
        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.item_code || ''}" readonly>
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

    // Handle Discount and Complete Row
    function handleDiscountAndCompleteRow(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (!row) return;
        
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
        document.activeElement.blur();
        
        // Clear selection
        selectedRowIndex = null;
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

    // Add new row button
    function addNewRow() {
        if (!selectedSupplier) {
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
        
        const supplierId = document.getElementById('supplier_id').value;
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
            closePastReturnsModal();
        }
    });

    // ==================== PAST PURCHASE RETURNS MODAL ====================
    
    // Open Past Returns Modal
    function openPastReturnsModal() {
        const supplierId = document.getElementById('supplier_id').value;
        
        // Fetch all past purchase returns (no date filter required)
        let url = `{{ route('admin.purchase-return.past-returns') }}`;
        let params = [];
        if (supplierId) {
            params.push(`supplier_id=${supplierId}`);
        }
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPastReturnsModal(data.transactions);
            } else {
                alert(data.message || 'Failed to load past returns');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load past returns');
        });
    }

    // Show Past Returns Modal
    function showPastReturnsModal(transactions) {
        const modalHTML = `
            <div class="past-returns-modal-backdrop" id="pastReturnsModalBackdrop" onclick="closePastReturnsModal()"></div>
            <div class="past-returns-modal" id="pastReturnsModal">
                <div class="past-returns-modal-content">
                    <div class="past-returns-modal-header">
                        <h5 class="past-returns-modal-title"><i class="bi bi-clock-history me-2"></i>Past Purchase Returns</h5>
                        <button type="button" class="btn-close-modal" onclick="closePastReturnsModal()">&times;</button>
                    </div>
                    <div class="past-returns-modal-body">
                        <div style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-bordered table-sm" style="font-size: 11px; margin-bottom: 0;">
                                <thead style="position: sticky; top: 0; background: #6c757d; color: white; z-index: 10;">
                                    <tr>
                                        <th style="width: 80px;">PR No.</th>
                                        <th style="width: 90px;">Date</th>
                                        <th style="width: 180px;">Supplier</th>
                                        <th style="width: 60px;">Time</th>
                                        <th style="width: 80px;">UID</th>
                                        <th style="width: 100px; text-align: right;">Amount</th>
                                        <th style="width: 80px; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${transactions.length === 0 ? 
                                        '<tr><td colspan="7" class="text-center text-muted">No purchase returns found</td></tr>' :
                                        transactions.map(trn => `
                                            <tr>
                                                <td><strong>${trn.pr_no || ''}</strong></td>
                                                <td>${trn.return_date || ''}</td>
                                                <td>${trn.supplier_name || ''}</td>
                                                <td>${trn.time || ''}</td>
                                                <td>${trn.uid || 'MASTER'}</td>
                                                <td style="text-align: right;">₹${trn.amount || '0.00'}</td>
                                                <td style="text-align: center;">
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="loadPastReturn(${trn.id})"
                                                            style="font-size: 9px; padding: 2px 8px;">
                                                        <i class="bi bi-download"></i> Load
                                                    </button>
                                                </td>
                                            </tr>
                                        `).join('')
                                    }
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="past-returns-modal-footer">
                        <span style="font-size: 11px; color: #666;">Total: ${transactions.length} record(s) (Last 100)</span>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="closePastReturnsModal()">Close</button>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal
        const existingModal = document.getElementById('pastReturnsModal');
        if (existingModal) existingModal.remove();
        const existingBackdrop = document.getElementById('pastReturnsModalBackdrop');
        if (existingBackdrop) existingBackdrop.remove();
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        setTimeout(() => {
            document.getElementById('pastReturnsModalBackdrop').classList.add('show');
            document.getElementById('pastReturnsModal').classList.add('show');
        }, 10);
    }

    // Close Past Returns Modal
    function closePastReturnsModal() {
        const modal = document.getElementById('pastReturnsModal');
        const backdrop = document.getElementById('pastReturnsModalBackdrop');
        
        if (modal) modal.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        
        setTimeout(() => {
            if (modal) modal.remove();
            if (backdrop) backdrop.remove();
        }, 300);
    }

    // Load Past Return by ID
    function loadPastReturn(id) {
        closePastReturnsModal();
        
        fetch(`{{ url('admin/purchase-return/details') }}/${id}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTransactionData(data);
            } else {
                alert(data.message || 'Failed to load transaction details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load transaction details');
        });
    }

    // Load Transaction by PR Number (from T.No. field)
    function loadTransactionByPrNo() {
        let prNo = document.getElementById('trn_no').value.trim();
        
        if (!prNo) {
            alert('Please enter a PR number (e.g., PR0001)');
            return;
        }
        
        // Auto-format: if user enters just number, add PR prefix
        if (/^\d+$/.test(prNo)) {
            prNo = 'PR' + prNo.padStart(4, '0');
            document.getElementById('trn_no').value = prNo;
        }
        
        // Make uppercase
        prNo = prNo.toUpperCase();
        document.getElementById('trn_no').value = prNo;
        
        fetch(`{{ url('admin/purchase-return/get-by-pr-no') }}/${prNo}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTransactionData(data);
            } else {
                alert(data.message || 'Transaction not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load transaction');
        });
    }

    // Populate Transaction Data into Form
    function populateTransactionData(data) {
        const header = data.header;
        const items = data.items || [];
        
        console.log('=== Populating Transaction Data ===');
        console.log('Header data:', header);
        console.log('Items count:', items.length);
        
        // Store transaction ID for update operation
        currentTransactionId = header.id;
        console.log('Transaction ID for modification:', currentTransactionId);
        
        // Clear existing items
        document.getElementById('itemsTableBody').innerHTML = '';
        returnItems = [];
        currentRowIndex = 0;
        selectedRowIndex = null;
        
        // Populate header fields
        if (header.return_date) {
            document.getElementById('return_date').value = header.return_date;
            const date = new Date(header.return_date);
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            document.getElementById('day_name').value = days[date.getDay()];
        }
        
        document.getElementById('trn_no').value = header.pr_no || '';
        console.log('Set PR No:', header.pr_no);
        
        // Set supplier
        if (header.supplier_id) {
            const supplierField = $('#supplier_id');
            const supplierIdStr = String(header.supplier_id);
            
            console.log('Attempting to set Supplier ID:', supplierIdStr, 'Name:', header.supplier_name);
            
            // Debug: Check all available options
            const allOptions = [];
            supplierField.find('option').each(function() {
                allOptions.push({
                    value: $(this).val(),
                    text: $(this).text(),
                    matches: $(this).val() === supplierIdStr
                });
            });
            console.log('Available supplier options:', allOptions);
            
            // Use setTimeout to ensure dropdown is fully rendered
            setTimeout(() => {
                // Try setting value
                supplierField.val(supplierIdStr);
                
                // Trigger change event to update UI
                supplierField.trigger('change');
                
                // Verify if value was set
                const currentValue = supplierField.val();
                const selectedText = supplierField.find('option:selected').text();
                
                console.log('After setting - Current value:', currentValue);
                console.log('Selected option text:', selectedText);
                
                if (currentValue === supplierIdStr && selectedText !== 'Select Supplier') {
                    console.log('✓ SUCCESS: Supplier dropdown set to:', selectedText);
                } else {
                    console.error('✗ FAILED: Supplier dropdown not set properly');
                    console.error('Expected ID:', supplierIdStr);
                    console.error('Current value:', currentValue);
                    console.error('Selected text:', selectedText);
                }
                
                // Make readonly for modification - use another setTimeout to ensure change event completes
                setTimeout(() => {
                    supplierField.prop('disabled', true);
                    supplierField.css({
                        'background-color': '#e9ecef',
                        'cursor': 'not-allowed'
                    });
                }, 50);
            }, 150);
            
            selectedSupplier = {
                id: header.supplier_id,
                name: header.supplier_name || ''
            };
        }
        
        // Set invoice details
        const invoiceNoField = document.getElementById('invoice_no');
        const invoiceDateField = document.getElementById('invoice_date');
        
        console.log('Invoice No from backend:', header.invoice_no);
        console.log('Invoice Date from backend:', header.invoice_date);
        
        // Only set and lock if values exist
        if (header.invoice_no) {
            invoiceNoField.value = header.invoice_no;
            invoiceNoField.readOnly = true;
            invoiceNoField.style.backgroundColor = '#e9ecef';
            invoiceNoField.style.cursor = 'not-allowed';
            console.log('Invoice No field set to:', invoiceNoField.value, '(readonly)');
        } else {
            // Clear field and keep editable
            invoiceNoField.value = '';
            invoiceNoField.readOnly = false;
            invoiceNoField.style.backgroundColor = '';
            invoiceNoField.style.cursor = '';
            console.log('Invoice No field is empty - keeping editable');
        }
        
        if (header.invoice_date) {
            invoiceDateField.value = header.invoice_date;
            invoiceDateField.readOnly = true;
            invoiceDateField.style.backgroundColor = '#e9ecef';
            invoiceDateField.style.cursor = 'not-allowed';
            console.log('Invoice Date field set to:', invoiceDateField.value, '(readonly)');
        } else {
            // Clear field and keep editable
            invoiceDateField.value = '';
            invoiceDateField.readOnly = false;
            invoiceDateField.style.backgroundColor = '';
            invoiceDateField.style.cursor = '';
            console.log('Invoice Date field is empty - keeping editable');
        }
        
        // Populate items
        items.forEach((item, index) => {
            const itemData = {
                item_id: item.item_id,
                item_code: item.item_code,
                item_name: item.item_name,
                batch_id: item.batch_id,
                batch_no: item.batch_no,
                expiry_date: item.expiry,
                packing: item.packing || '',
                unit: item.unit || 'PCS',
                company_name: item.company_name || '',
                hsn_code: item.hsn_code || '',
                purchase_rate: parseFloat(item.pur_rate || 0),
                mrp: parseFloat(item.mrp || 0),
                ws_rate: parseFloat(item.ws_rate || 0),
                spl_rate: parseFloat(item.spl_rate || 0),
                s_rate: parseFloat(item.s_rate || 0),
                discount_percent: parseFloat(item.dis_percent || 0),
                cgst_percent: parseFloat(item.cgst_percent || 0),
                sgst_percent: parseFloat(item.sgst_percent || 0),
                cess_percent: parseFloat(item.cess_percent || 0),
                total_cl_qty: parseFloat(item.total_cl_qty || 0), // Total closing qty from backend
                return_qty: parseFloat(item.qty || 0),
                return_fqty: parseFloat(item.free_qty || 0)
            };
            
            addItemRow(itemData, index);
            returnItems.push(itemData);
        });
        
        // Update summary totals
        document.getElementById('ntAmount').value = parseFloat(header.nt_amount || 0).toFixed(2);
        document.getElementById('scAmount').value = parseFloat(header.sc_amount || 0).toFixed(2);
        document.getElementById('disAmount').value = parseFloat(header.dis_amount || 0).toFixed(2);
        document.getElementById('scmAmount').value = parseFloat(header.scm_amount || 0).toFixed(2);
        document.getElementById('taxAmount').value = parseFloat(header.tax_amount || 0).toFixed(2);
        document.getElementById('invAmount').value = parseFloat(header.net_amount || 0).toFixed(2);
        document.getElementById('scmPercent').value = parseFloat(header.scm_percent || 0).toFixed(2);
        document.getElementById('tcsAmount').value = parseFloat(header.tcs_amount || 0).toFixed(2);
        document.getElementById('dis1Amount').value = parseFloat(header.dis1_amount || 0).toFixed(2);
        
        // Mark all rows as completed (green)
        const tbody = document.getElementById('itemsTableBody');
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, idx) => {
            markRowAsCompleted(idx);
        });
        
        // Store existing adjustments for pre-filling modal
        const adjustments = data.adjustments || [];
        window.existingAdjustments = {};
        adjustments.forEach(adj => {
            window.existingAdjustments[adj.purchase_transaction_id] = parseFloat(adj.adjusted_amount || 0);
        });
        console.log('Existing adjustments loaded:', window.existingAdjustments);
        
        // Clear calculation sections
        clearCalculationSection();
        clearAdditionalDetails();
        
        alert(`Purchase Return ${header.pr_no} loaded successfully!`);
    }

    // Credit Adjustment Modal Variables
    let creditAdjustmentData = [];
    let returnNetAmount = 0;

    // Open Credit Adjustment Modal
    function openCreditAdjustmentModal() {
        const supplierId = document.getElementById('supplier_id').value;
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

    // Show Credit Adjustment Modal
    function showCreditAdjustmentModal(invoices) {
        creditAdjustmentData = invoices;
        
        // Get existing adjustments if available
        const existingAdj = window.existingAdjustments || {};
        console.log('Pre-filling adjustments:', existingAdj);
        
        // Calculate initial total adjusted amount
        let initialTotalAdjusted = 0;
        
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
                                        invoices.map((inv, idx) => {
                                            // Get existing adjusted amount for this invoice
                                            const existingAmount = existingAdj[inv.id] || 0;
                                            initialTotalAdjusted += existingAmount;
                                            
                                            // Backend's balance_amount is AFTER all adjustments (including this return)
                                            // To show available amount to adjust, add back this return's adjustment
                                            const backendBalance = parseFloat(inv.balance_amount || 0);
                                            const billAmountToShow = backendBalance + existingAmount; // Available to adjust
                                            const currentBalance = backendBalance; // Actual balance after all adjustments
                                            
                                            console.log('Invoice ' + inv.bill_no + ': Backend Balance=' + backendBalance + ', This Return Adj=' + existingAmount + ', Bill Amt=' + billAmountToShow + ', Final Balance=' + currentBalance);
                                            
                                            return '<tr>' +
                                                '<td style="text-align: center;">' + (idx + 1) + '</td>' +
                                                '<td>PB / ' + (inv.bill_no || '') + '</td>' +
                                                '<td style="text-align: center;">' + (inv.bill_date || '') + '</td>' +
                                                '<td style="text-align: right;">' + billAmountToShow.toFixed(2) + '</td>' +
                                                '<td style="text-align: center;">' +
                                                    '<input type="number" class="form-control form-control-sm adjust-amount-input" ' +
                                                           'data-invoice-id="' + inv.id + '" ' +
                                                           'data-balance="' + currentBalance + '" ' +
                                                           'data-bill-amount="' + billAmountToShow + '" ' +
                                                           'value="' + existingAmount.toFixed(2) + '" min="0" max="' + billAmountToShow + '" step="0.01" ' +
                                                           'onchange="updateAdjustmentTotal()" ' +
                                                           'oninput="updateBalanceColumn(this)" ' +
                                                           'style="width: 90px; font-size: 11px; text-align: right; background-color: ' + (existingAmount > 0 ? '#d4edda' : '') + ';">' +
                                                '</td>' +
                                                '<td style="text-align: right;" class="balance-col" data-bill-amount="' + billAmountToShow + '" data-backend-balance="' + backendBalance + '">' + currentBalance.toFixed(2) + '</td>' +
                                            '</tr>';
                                        }).join('')
                                    }
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="credit-adjust-modal-footer" style="justify-content: space-between;">
                        <div style="font-size: 13px; color: #d00;">
                            <strong>BALANCE (Rs): </strong><span id="totalBalance">${(returnNetAmount - initialTotalAdjusted).toFixed(2)}</span>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: #666; margin-right: 10px;">EXIT : &lt;ESC&gt;</span>
                            <button type="button" class="btn btn-success btn-sm" onclick="saveCreditAdjustment()">
                                <i class="bi bi-check-circle"></i> Save
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
        }, 10);
    }

    // Update balance column when adjusted amount changes
    function updateBalanceColumn(input) {
        const row = input.closest('tr');
        const balanceCol = row.querySelector('.balance-col');
        const billAmount = parseFloat(balanceCol.dataset.billAmount || 0);
        const adjustedValue = parseFloat(input.value || 0);
        
        console.log('Updating balance - Bill Amount:', billAmount, 'Adjusted:', adjustedValue);
        
        // Validate against bill amount
        if (adjustedValue > billAmount) {
            input.value = billAmount.toFixed(2);
        }
        
        const newBalance = billAmount - parseFloat(input.value || 0);
        balanceCol.textContent = newBalance.toFixed(2);
        
        console.log('New balance:', newBalance);
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
        
        const supplierId = document.getElementById('supplier_id').value;
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
            supplier_name: document.getElementById('supplier_id').options[document.getElementById('supplier_id').selectedIndex].text,
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

    // Ask user if they want to adjust credit note
    function askCreditAdjustment() {
        const tbody = document.getElementById('itemsTableBody');
        if (!tbody || tbody.querySelectorAll('tr').length === 0) {
            alert('Please add items before saving.');
            return;
        }
        
        const supplierId = document.getElementById('supplier_id').value;
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
                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeConfirmAndSave()">No, Just Save</button>
                        <button type="button" class="btn btn-success btn-sm" onclick="closeConfirmAndOpenAdjustment()">Yes, Adjust</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', confirmHTML);
        setTimeout(() => {
            document.getElementById('confirmModalBackdrop').classList.add('show');
            document.getElementById('confirmModal').classList.add('show');
        }, 10);
    }

    function closeConfirmModal() {
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
                        <button type="button" class="btn btn-outline-secondary" onclick="closeSuccessAndNew()">
                            <i class="bi bi-plus-circle me-1"></i> New Return
                        </button>
                        <button type="button" class="btn btn-success" onclick="closeSuccessModal()">
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
        }, 10);
    }

    // Close Success Modal
    function closeSuccessModal() {
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
/* Past Returns Modal Styles */
.past-returns-modal-backdrop {
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

.past-returns-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.past-returns-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    z-index: 99999999;
    opacity: 0;
    transition: all 0.3s ease;
}

.past-returns-modal.show {
    display: block;
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.past-returns-modal-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    max-height: 90vh;
}

.past-returns-modal-header {
    padding: 12px 15px;
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.past-returns-modal-title {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
}

.past-returns-modal-body {
    padding: 15px;
    overflow-y: auto;
    flex: 1;
}

.past-returns-modal-footer {
    padding: 10px 15px;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

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
</style>
@endpush