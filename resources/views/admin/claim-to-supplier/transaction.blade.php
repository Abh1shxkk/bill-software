@extends('layouts.admin')

@section('title', 'Claim to Supplier Transaction')

@push('styles')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    input:focus { box-shadow: none !important; }
    .cts .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .cts .header-row { display: flex; align-items: center; gap: 15px; margin-bottom: 6px; }
    .cts .field-group { display: flex; align-items: center; gap: 6px; }
    .cts .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .cts .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #e9ecef; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    
    /* Modal Styles */
    .modal-backdrop-custom {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); z-index: 99998; opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-backdrop-custom.show { display: block; opacity: 1; }
    .item-modal {
        display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7);
        width: 90%; max-width: 800px; max-height: 90vh; background: white; border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); z-index: 99999; opacity: 0; transition: all 0.3s ease;
    }
    .item-modal.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .item-modal-header {
        padding: 15px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;
        border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;
    }
    .item-modal-header h5 { margin: 0; font-size: 16px; }
    .btn-close-custom { background: none; border: none; color: white; font-size: 24px; cursor: pointer; line-height: 1; }
    .item-modal-body { padding: 15px 20px; max-height: 60vh; overflow-y: auto; }
    .item-modal-footer { padding: 10px 20px; border-top: 1px solid #dee2e6; text-align: right; }
    
    /* Additional Details Modal */
    .additional-modal {
        display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7);
        width: 450px; max-height: 90vh; background: #f0f0f0; border: 2px solid #999;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); z-index: 99999; opacity: 0; transition: all 0.3s ease;
    }
    .additional-modal.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .additional-modal-body { padding: 15px 20px; }
    .additional-modal .field-row { display: flex; align-items: center; margin-bottom: 10px; }
    .additional-modal .field-row label { width: 180px; font-weight: 500; }
    .additional-modal .field-row input, .additional-modal .field-row select { border: 1px solid #999; padding: 3px 6px; font-size: 12px; }
    .additional-modal .field-row input[type="text"].small-input { width: 40px; text-align: center; }
    .additional-modal .field-row input[type="date"] { width: 120px; }
    .additional-modal .ok-btn { background: #e0e0e0; border: 2px outset #ccc; padding: 3px 20px; cursor: pointer; font-weight: 500; }
    .additional-modal .ok-btn:hover { background: #d0d0d0; }
    
    /* Rate Modal Styles */
    .rate-modal {
        display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7);
        width: 400px; background: #f8c0c0; border: 2px solid #999;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); z-index: 99999; opacity: 0; transition: all 0.3s ease;
    }
    .rate-modal.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .rate-modal-body { padding: 15px 20px; }
    .rate-modal .field-row { display: flex; align-items: center; margin-bottom: 10px; gap: 15px; }
    .rate-modal .field-row label { font-weight: 500; white-space: nowrap; }
    .rate-modal .field-row input { border: 1px solid #999; padding: 5px 8px; font-size: 12px; width: 120px; }
    .rate-modal .field-row input.yellow-bg { background: #ffff99; }
    
    /* Row Selection Highlight */
    .table-compact tbody tr { cursor: pointer; transition: all 0.2s ease; }
    .table-compact tbody tr:hover { background: #e3f2fd; }
    .table-compact tbody tr.selected-row { background: #bbdefb !important; border: 2px solid #1976d2 !important; }
    .table-compact tbody tr.selected-row td { border-color: #1976d2; }
</style>
@endpush

@section('content')
<section class="cts py-5">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-file-earmark-text me-2"></i> Claim to Supplier Transaction</h4>
                <div class="text-muted small">Create new claim to supplier transaction</div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="ctsForm" method="POST" autocomplete="off">
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="d-flex gap-3 mb-2">
                            <div style="width: 200px;">
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">Date:</label>
                                    <input type="date" id="claim_date" name="claim_date" class="form-control" style="width: 140px;" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">Day:</label>
                                    <input type="text" id="day_name" class="form-control readonly-field" style="width: 140px;" value="{{ date('l') }}" readonly>
                                </div>
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">T. No.:</label>
                                    <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" style="width: 140px;" readonly>
                                </div>
                            </div>

                            <div class="inner-card flex-grow-1 overflow-hidden">
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
                                            <label style="width: 80px;">Claim Date:</label>
                                            <input type="date" class="form-control" id="invoice_date" name="invoice_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 mt-1">
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label style="width: 100px;">Tax [Y/N]:</label>
                                            <input type="text" class="form-control" id="tax_flag" name="tax_flag" value="Y" maxlength="1" style="width: 50px;">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="field-group">
                                            <label style="width: 80px;">Narration:</label>
                                            <input type="text" class="form-control" id="narration" name="narration">
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <button type="button" class="btn btn-info btn-sm" onclick="showAdditionalDetailsModal()">
                                            <i class="bi bi-gear me-1"></i> Additional Details
                                        </button>
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
                                        <th style="width: 80px;">Rate</th>
                                        <th style="width: 60px;">Dis.%</th>
                                        <th style="width: 90px;">Amount</th>
                                        <th style="width: 50px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody"></tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="showAddItemModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add Item
                            </button>
                        </div>
                    </div>

                    <!-- Calculation Section -->
                    <div class="bg-white border rounded p-2 mb-2" style="overflow: hidden;">
                        <div class="d-flex flex-wrap align-items-center gap-3" style="font-size: 11px;">
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>HSN</strong></label>
                                <input type="text" class="form-control readonly-field text-center" id="calc_hsn_code" readonly style="width: 80px; height: 26px; font-size: 11px;" value="">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>SC%</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_sc_percent" readonly style="width: 60px; height: 26px; font-size: 11px;" value="0.000">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>TAX%</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_tax_percent" readonly style="width: 60px; height: 26px; font-size: 11px;" value="0.000">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>CGST(%)</strong></label>
                                <input type="text" class="form-control readonly-field text-center" id="calc_cgst_percent" readonly style="width: 45px; height: 26px; font-size: 11px;" value="0">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>CGST Amt</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_cgst_amount" readonly style="width: 70px; height: 26px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>SGST(%)</strong></label>
                                <input type="text" class="form-control readonly-field text-center" id="calc_sgst_percent" readonly style="width: 45px; height: 26px; font-size: 11px;" value="0">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>SGST Amt</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_sgst_amount" readonly style="width: 70px; height: 26px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>W.S.RATE</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_ws_rate" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>S.RATE</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_s_rate" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Info Section (Pink Background) -->
                    <div class="border rounded p-2 mb-2" style="background: #f8c0f8; overflow: hidden;">
                        <div class="d-flex align-items-center gap-3" style="font-size: 11px;">
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="font-weight: bold;">Inv.No :</label>
                                <input type="text" class="form-control form-control-sm" id="ref_inv_no" name="ref_inv_no" style="width: 80px; height: 26px; font-size: 11px;" value="/0">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="font-weight: bold;">Inv.Date :</label>
                                <input type="date" class="form-control form-control-sm" id="ref_inv_date" name="ref_inv_date" style="width: 120px; height: 26px; font-size: 11px;" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="d-flex align-items-center gap-1 flex-grow-1">
                                <label class="mb-0" style="font-weight: bold;">Customer :</label>
                                <input type="text" class="form-control form-control-sm" id="ref_customer_code" name="ref_customer_code" style="width: 80px; height: 26px; font-size: 11px;" placeholder="Code">
                                <input type="text" class="form-control form-control-sm flex-grow-1" id="ref_customer_name" name="ref_customer_name" style="height: 26px; font-size: 11px;" readonly placeholder="Customer Name">
                            </div>
                        </div>
                    </div>

                    <!-- Totals Row Section (Pink Background) -->
                    <div class="border rounded p-2 mb-2" style="background: #f8c0c0; overflow: hidden;">
                        <div class="d-flex align-items-center justify-content-between" style="font-size: 11px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">N.T AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_nt_amt" readonly style="width: 90px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SC</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_sc" readonly style="width: 80px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">DIS. AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_dis_amt" readonly style="width: 90px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SCM. AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_scm_amt" readonly style="width: 90px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Tax</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_tax" readonly style="width: 80px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">INV. AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_inv_amt" readonly style="width: 90px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="border rounded p-2 mb-2" style="background: #d4d4d4;">
                        <!-- Row 1: Packing, N.T Amt, Scm. Amt, Comp, Srlno -->
                        <div class="d-flex align-items-center" style="font-size: 11px; gap: 15px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold; width: 50px;">Packing</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="packing" readonly style="width: 60px; height: 24px; font-size: 11px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">N.T Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="nt_amount" id="ntAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Scm. Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="scm_amount" id="scmAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Comp :</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="companyName" readonly style="width: 100px; height: 24px; font-size: 11px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Srlno.</label>
                                <input type="text" class="form-control form-control-sm readonly-field text-center" id="srlNo" readonly style="width: 40px; height: 24px; font-size: 11px;">
                            </div>
                        </div>
                        <!-- Row 2: Unit, SC Amt, Net Amt, Lctn, SCM -->
                        <div class="d-flex align-items-center mt-1" style="font-size: 11px; gap: 15px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold; width: 50px;">Unit</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="unit" readonly style="width: 60px; height: 24px; font-size: 11px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SC Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="sc_amount" id="scAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Net Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="inv_amount" id="invAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Lctn :</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="locationField" readonly style="width: 60px; height: 24px; font-size: 11px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SCM.</label>
                                <input type="text" class="form-control form-control-sm readonly-field text-center" id="scmField" readonly style="width: 40px; height: 24px; font-size: 11px;" value="0">
                                <span>+</span>
                                <input type="text" class="form-control form-control-sm readonly-field text-center" id="scmField2" readonly style="width: 40px; height: 24px; font-size: 11px;" value="0">
                            </div>
                        </div>
                        <!-- Row 3: Cl. Qty, DIS. Amt, Tax Amt -->
                        <div class="d-flex align-items-center mt-1" style="font-size: 11px; gap: 15px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold; width: 50px;">Cl. Qty</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="clQty" readonly style="width: 60px; height: 24px; font-size: 11px;" value="0">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">DIS. Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="dis_amount" id="disAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Tax Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="tax_amount" id="taxAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <button type="button" class="btn btn-success" id="saveBtn">Save (End)</button>
                            <button type="button" class="btn btn-danger" id="deleteItemBtn">Delete Item</button>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'claimToSupplierItemModal',
    'module' => 'claim-to-supplier',
    'showStock' => true,
    'rateType' => 'pur_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'claimToSupplierBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'claimToSupplierBatchModal',
    'module' => 'claim-to-supplier',
    'showOnlyAvailable' => true,
    'rateType' => 'pur_rate',
    'showCostDetails' => true,
])

@endsection

@push('scripts')
<script>
let rowIndex = 0;
let allItems = [];
let selectedItem = null;
let currentRowForRate = null; // Track which row needs rate modal

// Additional Details Modal Data
let additionalDetails = {
    blank_statement: 'Y',
    rate_type: 'R',
    from_date: '{{ date("Y-m-d") }}',
    to_date: '{{ date("Y-m-d") }}',
    company_code: '',
    company_name: '',
    division: '00'
};

// ============================================================================
// BRIDGE FUNCTIONS FOR REUSABLE MODAL COMPONENTS
// ============================================================================

/**
 * Bridge function called by reusable modal components after item and batch selection
 */
function onItemBatchSelectedFromModal(itemData, batchData) {
    console.log('🎯 Claim to Supplier: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Claim to Supplier: Invalid item data received');
        return;
    }
    
    // Create new row
    const tbody = document.getElementById('itemsTableBody');
    const newRowIndex = rowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${newRowIndex}`;
    row.dataset.rowIndex = newRowIndex;
    row.dataset.itemId = itemData.id;
    row.dataset.batchId = batchData?.id || '';
    row.onclick = function() { selectRow(newRowIndex); };
    
    // Get rates and calculate
    const rate = batchData?.pur_rate || batchData?.cost || batchData?.avg_pur_rate || itemData.pur_rate || itemData.p_rate || 0;
    const qty = 1;
    const amount = (qty * rate).toFixed(2);
    
    row.innerHTML = `
        <td><input type="text" class="form-control" value="${itemData.id || ''}" readonly></td>
        <td><input type="text" class="form-control" value="${itemData.name || ''}" readonly></td>
        <td><input type="text" class="form-control" value="${batchData?.batch_no || ''}" readonly></td>
        <td><input type="text" class="form-control" value="${batchData?.expiry_display || batchData?.expiry || ''}" readonly></td>
        <td><input type="number" class="form-control" value="${qty}" onchange="calculateRowAmount(${newRowIndex})"></td>
        <td><input type="number" class="form-control" value="0" onchange="calculateRowAmount(${newRowIndex})"></td>
        <td><input type="number" class="form-control" value="${rate}" step="0.01" onchange="calculateRowAmount(${newRowIndex})"></td>
        <td><input type="number" class="form-control" value="0" step="0.01" onchange="calculateRowAmount(${newRowIndex})"></td>
        <td><input type="number" class="form-control readonly-field" value="${amount}" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${newRowIndex})"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${newRowIndex}][item_id]" value="${itemData.id || ''}">
        <input type="hidden" name="items[${newRowIndex}][batch_id]" value="${batchData?.id || ''}">
        <input type="hidden" name="items[${newRowIndex}][hsn_code]" value="${itemData.hsn_code || ''}">
        <input type="hidden" name="items[${newRowIndex}][packing]" value="${itemData.packing || ''}">
        <input type="hidden" name="items[${newRowIndex}][unit]" value="${itemData.unit || '1'}">
        <input type="hidden" name="items[${newRowIndex}][company_name]" value="${itemData.company_name || ''}">
        <input type="hidden" name="items[${newRowIndex}][mrp]" value="${itemData.mrp || 0}">
        <input type="hidden" name="items[${newRowIndex}][s_rate]" value="${itemData.s_rate || 0}">
        <input type="hidden" name="items[${newRowIndex}][pur_rate]" value="${rate}">
    `;
    
    tbody.appendChild(row);
    selectRow(newRowIndex);
    
    // Update calculations
    if (typeof calculateRowAmount === 'function') {
        calculateRowAmount(newRowIndex);
    }
    if (typeof calculateTotals === 'function') {
        calculateTotals();
    }
    
    console.log('✅ Claim to Supplier: Row created successfully', newRowIndex);
    
    // Focus qty field
    setTimeout(() => {
        row.querySelector('input[type="number"]')?.focus();
    }, 100);
}

/**
 * Bridge function to open item selection modal
 */
function showAddItemModal() {
    console.log('🎯 Claim to Supplier: showAddItemModal called');
    
    // Check if modal component function exists
    if (typeof window.openItemModal_claimToSupplierItemModal === 'function') {
        console.log('✅ Claim to Supplier: Opening reusable item modal');
        window.openItemModal_claimToSupplierItemModal();
    } else {
        console.error('❌ Claim to Supplier: openItemModal_claimToSupplierItemModal function not found. Modal component may not be loaded.');
        alert('Error: Item selection modal not available. Please refresh the page.');
    }
}

// ============================================================================
// EXISTING FUNCTIONS
// ============================================================================

$(document).ready(function() {
    loadNextTransactionNumber();
    
    $('#claim_date').on('change', function() {
        const date = new Date($(this).val());
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $('#day_name').val(days[date.getDay()]);
    });
    
    // Customer code lookup
    $('#ref_customer_code').on('change', function() {
        const code = $(this).val();
        if (code) {
            $.get("{{ url('admin/customers') }}/" + code, function(response) {
                if (response && response.name) {
                    $('#ref_customer_name').val(response.name);
                } else {
                    $('#ref_customer_name').val('');
                }
            }).fail(function() { 
                $('#ref_customer_name').val(''); 
            });
        } else {
            $('#ref_customer_name').val('');
        }
    });
    
    $('#saveBtn').on('click', saveTransaction);
});

function loadNextTransactionNumber() {
    $.get("{{ route('admin.claim-to-supplier.next-trn-no') }}", function(response) {
        $('#trn_no').val(response.next_trn_no);
    });
}

// ==================== ADDITIONAL DETAILS MODAL ====================
function showAdditionalDetailsModal() {
    const isBlankY = additionalDetails.blank_statement === 'Y';
    const disabledAttr = isBlankY ? 'disabled' : '';
    const disabledStyle = isBlankY ? 'background: #ccc; cursor: not-allowed;' : '';
    
    const modalHTML = `
        <div class="modal-backdrop-custom" id="additionalModalBackdrop" onclick="closeAdditionalDetailsModal()"></div>
        <div class="additional-modal" id="additionalModal">
            <div class="additional-modal-body">
                <div class="field-row">
                    <label>Blank statement [ Y/N ] :</label>
                    <input type="text" id="add_blank_statement" class="small-input" value="${additionalDetails.blank_statement}" maxlength="1" style="background: #ffff00;" onkeyup="toggleAdditionalFields()">
                </div>
                <div class="field-row">
                    <label>From P / S / R :</label>
                    <input type="text" id="add_rate_type" class="small-input additional-field" value="${additionalDetails.rate_type}" maxlength="1" ${disabledAttr} style="${disabledStyle}">
                </div>
                <div class="field-row">
                    <label>From :</label>
                    <input type="date" id="add_from_date" class="additional-field" value="${additionalDetails.from_date}" ${disabledAttr} style="${disabledStyle}">
                    <label style="width: auto; margin-left: 15px; margin-right: 5px;">To :</label>
                    <input type="date" id="add_to_date" class="additional-field" value="${additionalDetails.to_date}" ${disabledAttr} style="${disabledStyle}">
                </div>
                <div class="field-row">
                    <label>Company :</label>
                    <input type="text" id="add_company_code" class="additional-field" value="${additionalDetails.company_code}" style="width: 80px; ${disabledStyle}" ${disabledAttr} placeholder="Code">
                    <input type="text" id="add_company_name" value="${additionalDetails.company_name}" readonly style="flex: 1; margin-left: 5px; background: #e9ecef;">
                </div>
                <div class="field-row">
                    <label>Division :</label>
                    <input type="text" id="add_division" class="additional-field" value="${additionalDetails.division}" style="width: 60px; ${disabledStyle}" ${disabledAttr}>
                </div>
                <div class="field-row" style="justify-content: flex-end; margin-top: 15px;">
                    <button type="button" class="ok-btn" onclick="saveAdditionalDetails()">Ok</button>
                </div>
            </div>
        </div>
    `;
    
    $('#additionalModal, #additionalModalBackdrop').remove();
    $('body').append(modalHTML);
    
    // Company code lookup
    $('#add_company_code').on('change', function() {
        const code = $(this).val();
        if (code) {
            $.get("{{ url('admin/companies/by-code') }}/" + code, function(response) {
                if (response.success) {
                    $('#add_company_name').val(response.company.name);
                } else {
                    $('#add_company_name').val('');
                }
            }).fail(function() { $('#add_company_name').val(''); });
        }
    });
    
    setTimeout(() => { $('#additionalModalBackdrop, #additionalModal').addClass('show'); }, 10);
}

function toggleAdditionalFields() {
    const blankValue = $('#add_blank_statement').val().toUpperCase();
    const isBlankY = blankValue === 'Y';
    
    if (isBlankY) {
        $('.additional-field').prop('disabled', true).css({ 'background': '#ccc', 'cursor': 'not-allowed' });
        $('#add_rate_type').val('R');
        $('#add_from_date').val('{{ date("Y-m-d") }}');
        $('#add_to_date').val('{{ date("Y-m-d") }}');
        $('#add_company_code').val('');
        $('#add_company_name').val('');
        $('#add_division').val('00');
    } else {
        $('.additional-field').prop('disabled', false).css({ 'background': '#fff', 'cursor': 'text' });
    }
}

function closeAdditionalDetailsModal() {
    $('#additionalModalBackdrop, #additionalModal').removeClass('show');
    setTimeout(() => { $('#additionalModal, #additionalModalBackdrop').remove(); }, 300);
}

function saveAdditionalDetails() {
    additionalDetails.blank_statement = $('#add_blank_statement').val().toUpperCase();
    additionalDetails.rate_type = $('#add_rate_type').val().toUpperCase();
    additionalDetails.from_date = $('#add_from_date').val();
    additionalDetails.to_date = $('#add_to_date').val();
    additionalDetails.company_code = $('#add_company_code').val();
    additionalDetails.company_name = $('#add_company_name').val();
    additionalDetails.division = $('#add_division').val();
    closeAdditionalDetailsModal();
}


// ==================== LEGACY ITEM SELECTION MODAL ====================
function _legacy_showAddItemModal() {
    const supplierId = $('#supplier_id').val();
    if (!supplierId) {
        alert('Please select a supplier first');
        return;
    }
    
    let params = { supplier_id: supplierId };
    if (additionalDetails.company_code) params.company_code = additionalDetails.company_code;
    if (additionalDetails.division && additionalDetails.division !== '00') params.division = additionalDetails.division;
    
    $.get("{{ route('admin.items.get-all') }}", params, function(data) {
        allItems = data.items || data;
        _legacy_showItemSelectionModal(allItems);
    }).fail(function() {
        alert('Failed to load items');
    });
}

function _legacy_showItemSelectionModal(items) {
    let itemsHtml = items.map((item, index) => {
        // Prepare item data with all required fields
        const itemData = {
            id: item.id,
            code: item.code || item.id,
            name: item.name || '',
            packing: item.packing || '',
            unit: item.unit || '',
            company_name: item.company_short_name || item.company_name || '',
            s_rate: item.s_rate || 0,
            ws_rate: item.ws_rate || 0,
            mrp: item.mrp || 0,
            pur_rate: item.pur_rate || 0,
            hsn_code: item.hsn_code || '',
            cgst_percent: item.cgst_percent || 0,
            sgst_percent: item.sgst_percent || 0,
            sc_percent: item.sc_percent || 0,
            scm_percent: item.scm_percent || 0,
            location: item.location || ''
        };
        return `
        <tr class="item-row" data-item-name="${(item.name || '').toLowerCase()}" data-item-code="${String(item.id).toLowerCase()}">
            <td style="text-align: center;">${index + 1}</td>
            <td>${item.id || ''}</td>
            <td>${item.name || ''}</td>
            <td>${item.company_short_name || ''}</td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-sm btn-primary" onclick='_legacy_selectItem(${JSON.stringify(itemData).replace(/'/g, "\\'")})'
                    style="font-size: 9px; padding: 2px 8px;">Select</button>
            </td>
        </tr>
    `}).join('');
    
    const modalHTML = `
        <div class="modal-backdrop-custom" id="itemModalBackdrop" onclick="_legacy_closeItemModal()"></div>
        <div class="item-modal" id="itemModal">
            <div class="item-modal-header">
                <h5><i class="bi bi-box-seam me-2"></i>Select Item</h5>
                <button type="button" class="btn-close-custom" onclick="_legacy_closeItemModal()">&times;</button>
            </div>
            <div class="item-modal-body">
                <div style="margin-bottom: 10px;">
                    <input type="text" id="itemSearchInput" class="form-control form-control-sm" 
                           placeholder="Search by item name or code..." onkeyup="_legacy_filterItems()" style="font-size: 11px;">
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 10px; margin-bottom: 0;">
                        <thead style="position: sticky; top: 0; background: #0d6efd; color: white; z-index: 10;">
                            <tr>
                                <th style="width: 35px; text-align: center;">S.N</th>
                                <th style="width: 80px;">Code</th>
                                <th style="width: 200px;">Item Name</th>
                                <th style="width: 120px;">Company</th>
                                <th style="width: 80px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>
                </div>
            </div>
            <div class="item-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeItemModal()">Close</button>
            </div>
        </div>
    `;
    
    $('#itemModal, #itemModalBackdrop').remove();
    $('body').append(modalHTML);
    setTimeout(() => { $('#itemModalBackdrop, #itemModal').addClass('show'); $('#itemSearchInput').focus(); }, 10);
}

function _legacy_filterItems() {
    const searchValue = $('#itemSearchInput').val().toLowerCase();
    $('.item-row').each(function() {
        const name = $(this).data('item-name');
        const code = $(this).data('item-code');
        $(this).toggle(name.includes(searchValue) || code.includes(searchValue));
    });
}

function _legacy_closeItemModal() {
    $('#itemModalBackdrop, #itemModal').removeClass('show');
    setTimeout(() => { $('#itemModal, #itemModalBackdrop').remove(); }, 300);
}

// ==================== LEGACY SELECT ITEM & ADD ROW (NO BATCH MODAL) ====================
function _legacy_selectItem(item) {
    if (typeof item === 'string') item = JSON.parse(item);
    selectedItem = item;
    _legacy_closeItemModal();
    
    // Directly add row without batch modal - user will enter batch manually
    _legacy_addItemRowManual(item);
}

function _legacy_addItemRowManual(item) {
    const row = `
        <tr data-row="${rowIndex}">
            <td><input type="text" class="form-control item-code" data-row="${rowIndex}" value="${item.code || ''}" readonly tabindex="-1"></td>
            <td><input type="text" class="form-control item-name" data-row="${rowIndex}" value="${item.name || ''}" readonly tabindex="-1"></td>
            <td><input type="text" class="form-control batch-no" data-row="${rowIndex}" value="" placeholder="Enter batch"></td>
            <td><input type="text" class="form-control expiry" data-row="${rowIndex}" value="" placeholder="MM/YY"></td>
            <td><input type="number" class="form-control qty" data-row="${rowIndex}" value="0" min="0"></td>
            <td><input type="number" class="form-control free-qty" data-row="${rowIndex}" value="0" min="0"></td>
            <td><input type="number" class="form-control rate" data-row="${rowIndex}" value="0" step="0.01"></td>
            <td><input type="number" class="form-control dis-percent" data-row="${rowIndex}" value="0" step="0.01"></td>
            <td><input type="number" class="form-control amount" data-row="${rowIndex}" value="0" step="0.01" readonly tabindex="-1"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
    $('#itemsTableBody').append(row);
    
    const $row = $(`tr[data-row="${rowIndex}"]`);
    $row.data('item_id', item.id);
    $row.data('rate_charged', 0);
    $row.data('actual_rate', 0);
    // Store item data for later use
    const itemDataObj = {
        s_rate: parseFloat(item.s_rate) || 0,
        ws_rate: parseFloat(item.ws_rate) || 0,
        hsn_code: item.hsn_code || '',
        cgst_percent: parseFloat(item.cgst_percent) || 0,
        sgst_percent: parseFloat(item.sgst_percent) || 0,
        sc_percent: parseFloat(item.sc_percent) || 0,
        scm_percent: parseFloat(item.scm_percent) || 0,
        packing: item.packing || '',
        unit: item.unit || '',
        company_name: item.company_name || '',
        location: item.location || ''
    };
    console.log('Item received:', item);
    console.log('Item data stored:', itemDataObj);
    $row.data('item_data', itemDataObj);
    
    // Select this row and update all sections
    $('#itemsTableBody tr').removeClass('selected-row');
    $row.addClass('selected-row');
    
    // Update Calculation Section and Summary Section with this row's data
    updateSelectedRowDetails($row);
    
    // Update totals
    calculateTotals();
    
    // Focus on batch field
    setTimeout(() => {
        $row.find('.batch-no').focus();
    }, 100);
    
    rowIndex++;
}

// Update calculation fields when row is focused
function updateCalcFieldsFromRow($row) {
    const itemData = $row.data('item_data') || {};
    
    $('#calc_hsn_code').val(itemData.hsn_code || '');
    $('#calc_s_rate').val(parseFloat(itemData.s_rate || 0).toFixed(2));
    $('#calc_ws_rate').val(parseFloat(itemData.ws_rate || 0).toFixed(2));
    $('#calc_cgst_percent').val(itemData.cgst_percent || 0);
    $('#calc_sgst_percent').val(itemData.sgst_percent || 0);
    $('#packing').val(itemData.packing || '');
    $('#unit').val(itemData.unit || '');
    $('#companyName').val(itemData.company_name || '');
}

// Update calc fields when any input in row is focused - also select the row
$(document).on('focus', '#itemsTableBody tr input', function() {
    const $row = $(this).closest('tr');
    
    // Select this row
    $('#itemsTableBody tr').removeClass('selected-row');
    $row.addClass('selected-row');
    
    // Update all sections with this row's data
    updateSelectedRowDetails($row);
});

// ==================== FIELD NAVIGATION WITH ENTER KEY ====================
$(document).on('keydown', '.batch-no, .expiry, .qty, .free-qty, .rate, .dis-percent', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const $row = $(this).closest('tr');
        const rowIdx = $row.data('row');
        
        if ($(this).hasClass('batch-no')) {
            // Batch → Expiry
            $row.find('.expiry').focus();
        } else if ($(this).hasClass('expiry')) {
            // Expiry → Qty
            $row.find('.qty').focus();
        } else if ($(this).hasClass('qty')) {
            // Qty → F.Qty
            $row.find('.free-qty').focus();
        } else if ($(this).hasClass('free-qty')) {
            // F.Qty → Show Rate Modal
            currentRowForRate = rowIdx;
            showRateModal(rowIdx);
        } else if ($(this).hasClass('rate')) {
            // Rate → Dis%
            $row.find('.dis-percent').focus();
        } else if ($(this).hasClass('dis-percent')) {
            // Dis% → Calculate and done
            calculateRowAmount($row);
            calculateTotals();
        }
    }
});

// ==================== RATE CHARGED / ACTUAL RATE MODAL ====================
function showRateModal(rowIdx) {
    const $row = $(`tr[data-row="${rowIdx}"]`);
    const rateCharged = $row.data('rate_charged') || 0;
    const actualRate = $row.data('actual_rate') || 0;
    
    const modalHTML = `
        <div class="modal-backdrop-custom" id="rateModalBackdrop"></div>
        <div class="rate-modal" id="rateModal">
            <div class="rate-modal-body">
                <div class="field-row">
                    <label>Rate Charged :</label>
                    <input type="number" id="rate_charged" class="yellow-bg" value="${rateCharged}" step="0.01">
                    <label style="margin-left: 20px;">Actual Rate :</label>
                    <input type="number" id="actual_rate" value="${actualRate}" step="0.01">
                </div>
                <div class="field-row" style="justify-content: flex-end; margin-top: 15px;">
                    <button type="button" class="ok-btn" onclick="saveRateModal()">Ok</button>
                </div>
            </div>
        </div>
    `;
    
    $('#rateModal, #rateModalBackdrop').remove();
    $('body').append(modalHTML);
    setTimeout(() => { 
        $('#rateModalBackdrop, #rateModal').addClass('show'); 
        $('#rate_charged').focus().select();
    }, 10);
    
    // Enter key navigation in rate modal
    $('#rate_charged').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#actual_rate').focus().select();
        }
    });
    
    $('#actual_rate').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveRateModal();
        }
    });
}

function saveRateModal() {
    const rateCharged = parseFloat($('#rate_charged').val()) || 0;
    const actualRate = parseFloat($('#actual_rate').val()) || 0;
    
    if (currentRowForRate !== null) {
        const $row = $(`tr[data-row="${currentRowForRate}"]`);
        $row.data('rate_charged', rateCharged);
        $row.data('actual_rate', actualRate);
        
        // Set rate field value (use rate_charged as the rate)
        $row.find('.rate').val(rateCharged.toFixed(2));
    }
    
    closeRateModal();
    
    // Focus on rate field after modal closes
    if (currentRowForRate !== null) {
        setTimeout(() => {
            $(`tr[data-row="${currentRowForRate}"]`).find('.rate').focus();
        }, 100);
    }
    currentRowForRate = null;
}

function closeRateModal() {
    $('#rateModalBackdrop, #rateModal').removeClass('show');
    setTimeout(() => { $('#rateModal, #rateModalBackdrop').remove(); }, 300);
}


// ==================== ROW CALCULATIONS ====================
function removeRow(row) {
    $(`tr[data-row="${row}"]`).remove();
    calculateTotals();
}

$(document).on('change', '.qty, .free-qty, .rate, .dis-percent', function() {
    const $row = $(this).closest('tr');
    calculateRowAmount($row);
    calculateTotals();
});

// Row selection highlight and update selected row details
$(document).on('click', '#itemsTableBody tr', function(e) {
    // Don't select if clicking on input or button
    if ($(e.target).is('input, button, i')) return;
    
    $('#itemsTableBody tr').removeClass('selected-row');
    $(this).addClass('selected-row');
    
    // Update Calculation Section and Summary Section with selected row data
    updateSelectedRowDetails($(this));
});

// Update Calculation Section and Summary Section with selected row's item details
function updateSelectedRowDetails($row) {
    const itemId = $row.data('item_id');
    
    // If no item_id, just update with empty/zero values
    if (!itemId) {
        updateSectionsWithItemData($row, {});
        return;
    }
    
    // Fetch item data from Items table via API
    $.ajax({
        url: "{{ url('admin/items/search') }}",
        method: 'GET',
        data: { code: itemId },
        success: function(response) {
            if (response.success && response.items && response.items.length > 0) {
                const item = response.items[0];
                const itemData = {
                    hsn_code: item.hsn_code || '',
                    cgst_percent: parseFloat(item.cgst_percent) || 0,
                    sgst_percent: parseFloat(item.sgst_percent) || 0,
                    packing: item.packing || '',
                    unit: item.unit || '',
                    company_name: item.company || item.mfg_by || '',
                    location: item.location || '',
                    ws_rate: parseFloat(item.ws_rate) || 0,
                    s_rate: parseFloat(item.s_rate) || 0,
                    sc_percent: 0,
                    scm_percent: 0
                };
                // Store for future use
                $row.data('item_data', itemData);
                updateSectionsWithItemData($row, itemData);
            } else {
                updateSectionsWithItemData($row, $row.data('item_data') || {});
            }
        },
        error: function() {
            updateSectionsWithItemData($row, $row.data('item_data') || {});
        }
    });
}

// Helper function to update sections with item data
function updateSectionsWithItemData($row, itemData) {
    const qty = parseFloat($row.find('.qty').val()) || 0;
    const rate = parseFloat($row.find('.rate').val()) || 0;
    const disPercent = parseFloat($row.find('.dis-percent').val()) || 0;
    const amount = parseFloat($row.find('.amount').val()) || 0; // Amount = Qty × Rate
    
    const cgstPercent = parseFloat(itemData.cgst_percent) || 0;
    const sgstPercent = parseFloat(itemData.sgst_percent) || 0;
    const scPercent = parseFloat(itemData.sc_percent) || 0;
    const scmPercent = parseFloat(itemData.scm_percent) || 0;
    const taxPercent = cgstPercent + sgstPercent;
    
    // Calculate for this single row
    // N.T Amt = Amount (Qty × Rate)
    const ntAmt = amount;
    
    // Discount Amount = N.T Amt × Dis%
    const disAmount = ntAmt * disPercent / 100;
    
    // SC Amount = N.T Amt × SC%
    const scAmount = ntAmt * scPercent / 100;
    
    // SCM Amount = N.T Amt × SCM%
    const scmAmount = ntAmt * scmPercent / 100;
    
    // Net Amount = N.T Amt - DIS Amt
    const netAmount = ntAmt - disAmount;
    
    // Tax calculated on net amount
    const cgstAmount = netAmount * cgstPercent / 100;
    const sgstAmount = netAmount * sgstPercent / 100;
    const taxAmount = cgstAmount + sgstAmount;
    
    // Update Calculation Section (Item-based details)
    $('#calc_hsn_code').val(itemData.hsn_code || '');
    $('#calc_sc_percent').val(scPercent.toFixed(3));
    $('#calc_tax_percent').val(taxPercent.toFixed(3));
    $('#calc_cgst_percent').val(cgstPercent);
    $('#calc_cgst_amount').val(cgstAmount.toFixed(2));
    $('#calc_sgst_percent').val(sgstPercent);
    $('#calc_sgst_amount').val(sgstAmount.toFixed(2));
    $('#calc_ws_rate').val(parseFloat(itemData.ws_rate || 0).toFixed(2));
    $('#calc_s_rate').val(parseFloat(itemData.s_rate || 0).toFixed(2));
    
    // Update Summary Section (Item-based details)
    $('#packing').val(itemData.packing || '');
    $('#unit').val(itemData.unit || '');
    $('#companyName').val(itemData.company_name || '');
    $('#srlNo').val($row.data('row') !== undefined ? ($row.data('row') + 1) : '');
    $('#locationField').val(itemData.location || '');
    
    // Item-level amounts in Summary
    $('#ntAmount').val(ntAmt.toFixed(2));
    $('#scAmount').val(scAmount.toFixed(2));
    $('#scmAmount').val(scmAmount.toFixed(2));
    $('#disAmount').val(disAmount.toFixed(2));
    $('#taxAmount').val(taxAmount.toFixed(2));
    $('#invAmount').val(netAmount.toFixed(2));
    $('#clQty').val(qty);
}

function calculateRowAmount($row) {
    const qty = parseFloat($row.find('.qty').val()) || 0;
    const rate = parseFloat($row.find('.rate').val()) || 0;
    
    // Amount = Qty × Rate ONLY (no discount applied here)
    const amount = qty * rate;
    $row.find('.amount').val(amount.toFixed(2));
    
    // If this row is selected, update calculation and summary sections
    if ($row.hasClass('selected-row')) {
        updateSelectedRowDetails($row);
    }
}

// Calculate totals for all items (Totals Row Section - Pink)
function calculateTotals() {
    let totalNtAmount = 0;
    let totalCgstAmount = 0;
    let totalSgstAmount = 0;
    let totalDisAmount = 0;
    let totalScAmount = 0;
    let totalScmAmount = 0;
    let totalQty = 0;
    
    $('#itemsTableBody tr').each(function() {
        const $row = $(this);
        const qty = parseFloat($row.find('.qty').val()) || 0;
        const rate = parseFloat($row.find('.rate').val()) || 0;
        const disPercent = parseFloat($row.find('.dis-percent').val()) || 0;
        const amount = parseFloat($row.find('.amount').val()) || 0; // Amount = Qty × Rate
        const itemData = $row.data('item_data') || {};
        const cgstPercent = parseFloat(itemData.cgst_percent) || 0;
        const sgstPercent = parseFloat(itemData.sgst_percent) || 0;
        const scPercent = parseFloat(itemData.sc_percent) || 0;
        const scmPercent = parseFloat(itemData.scm_percent) || 0;
        
        // N.T Amount = Sum of all Amount (Qty × Rate)
        totalNtAmount += amount;
        
        // Discount Amount = Amount × Dis%
        const disAmount = amount * disPercent / 100;
        totalDisAmount += disAmount;
        
        // SC Amount = Amount × SC%
        const scAmount = amount * scPercent / 100;
        totalScAmount += scAmount;
        
        // SCM Amount = Amount × SCM%
        const scmAmount = amount * scmPercent / 100;
        totalScmAmount += scmAmount;
        
        // Tax calculated on amount after discount
        const amountAfterDiscount = amount - disAmount;
        totalCgstAmount += amountAfterDiscount * cgstPercent / 100;
        totalSgstAmount += amountAfterDiscount * sgstPercent / 100;
        totalQty += qty;
    });
    
    const totalTaxAmount = totalCgstAmount + totalSgstAmount;
    const totalInvAmount = (totalNtAmount - totalDisAmount) + totalTaxAmount;
    
    // Update Totals Row Section (Pink) - All items sum
    $('#total_nt_amt').val(totalNtAmount.toFixed(2));
    $('#total_sc').val(totalScAmount.toFixed(2));
    $('#total_dis_amt').val(totalDisAmount.toFixed(2));
    $('#total_scm_amt').val(totalScmAmount.toFixed(2));
    $('#total_tax').val(totalTaxAmount.toFixed(2));
    $('#total_inv_amt').val(totalInvAmount.toFixed(2));
    
    // If a row is selected, update its details, otherwise show totals in summary
    const $selectedRow = $('#itemsTableBody tr.selected-row');
    if ($selectedRow.length > 0) {
        updateSelectedRowDetails($selectedRow);
    }
}

// ==================== SAVE TRANSACTION ====================
function saveTransaction() {
    const supplierId = $('#supplier_id').val();
    if (!supplierId) {
        alert('Please select a supplier');
        return;
    }
    
    const items = [];
    $('#itemsTableBody tr').each(function() {
        const $row = $(this);
        const qty = parseFloat($row.find('.qty').val()) || 0;
        if (qty > 0) {
            const itemData = $row.data('item_data') || {};
            items.push({
                item_id: $row.data('item_id'),
                item_code: $row.find('.item-code').val(),
                item_name: $row.find('.item-name').val(),
                batch_no: $row.find('.batch-no').val(),
                expiry: $row.find('.expiry').val(),
                qty: qty,
                free_qty: parseFloat($row.find('.free-qty').val()) || 0,
                pur_rate: parseFloat($row.find('.rate').val()) || 0,
                dis_percent: parseFloat($row.find('.dis-percent').val()) || 0,
                ft_amount: parseFloat($row.find('.amount').val()) || 0,
                rate_charged: $row.data('rate_charged') || 0,
                actual_rate: $row.data('actual_rate') || 0,
                cgst_percent: itemData.cgst_percent || 0,
                sgst_percent: itemData.sgst_percent || 0,
                hsn_code: itemData.hsn_code || '',
                packing: itemData.packing || '',
                unit: itemData.unit || '',
                company_name: itemData.company_name || '',
                ws_rate: itemData.ws_rate || 0,
                s_rate: itemData.s_rate || 0,
                sc_percent: itemData.sc_percent || 0,
                scm_percent: itemData.scm_percent || 0,
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    const data = {
        _token: '{{ csrf_token() }}',
        claim_date: $('#claim_date').val(),
        supplier_id: supplierId,
        supplier_name: $('#supplier_id option:selected').text(),
        invoice_no: $('#invoice_no').val(),
        invoice_date: $('#invoice_date').val(),
        tax_flag: $('#tax_flag').val(),
        narration: $('#narration').val(),
        nt_amount: parseFloat($('#ntAmount').val()) || 0,
        tax_amount: parseFloat($('#taxAmount').val()) || 0,
        net_amount: parseFloat($('#invAmount').val()) || 0,
        // Additional Details
        blank_statement: additionalDetails.blank_statement,
        rate_type: additionalDetails.rate_type,
        filter_from_date: additionalDetails.from_date,
        filter_to_date: additionalDetails.to_date,
        company_code: additionalDetails.company_code,
        division: additionalDetails.division,
        // Reference Invoice Info
        ref_inv_no: $('#ref_inv_no').val(),
        ref_inv_date: $('#ref_inv_date').val(),
        ref_customer_code: $('#ref_customer_code').val(),
        ref_customer_name: $('#ref_customer_name').val(),
        items: items
    };
    
    $.ajax({
        url: "{{ route('admin.claim-to-supplier.store') }}",
        method: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                alert('Claim saved successfully! Claim No: ' + response.claim_no);
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error saving claim: ' + (xhr.responseJSON?.message || 'Unknown error'));
        }
    });
}
</script>
@endpush
