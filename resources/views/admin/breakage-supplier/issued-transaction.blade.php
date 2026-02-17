@extends('layouts.admin')

@section('title', 'Breakage/Expiry to Supplier - Issued Transaction')
@section('disable_select2', '1')

@push('styles')
<style>
    .bsi-form { font-size: 11px; }
    .bsi-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .bsi-form input, .bsi-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: #fff; border: 1px solid #ccc; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }
    .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .readonly-field { background-color: #e9ecef !important; }
    
    /* Custom searchable dropdown (supplier) */
    .custom-dropdown-wrapper { position: relative; width: 100%; }
    .custom-dropdown-menu { 
        display: none; 
        position: absolute; 
        top: 100%; 
        left: 0; 
        width: 100%; 
        max-height: 300px; 
        overflow-y: auto; 
        background: white; 
        border: 1px solid #ccc; 
        border-radius: 4px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        z-index: 1000;
    }
    .custom-dropdown-menu .dropdown-item { 
        padding: 6px 10px; 
        cursor: pointer; 
        font-size: 11px;
    }
    .custom-dropdown-menu .dropdown-item:hover { 
        background-color: #f1f5ff; 
    }
    .custom-dropdown-menu .dropdown-item.active { 
        background-color: #cfe2ff; 
    }
    .custom-dropdown-menu .dropdown-header { 
        padding: 8px 12px; 
        background: #f8f9fa; 
        border-bottom: 1px solid #dee2e6; 
        font-weight: 600; 
        font-size: 11px;
    }
    .custom-dropdown-menu::-webkit-scrollbar { width: 6px; }
    .custom-dropdown-menu::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-dropdown-menu::-webkit-scrollbar-thumb { background: #888; border-radius: 3px; }
    .custom-dropdown-menu::-webkit-scrollbar-thumb:hover { background: #555; }
    
    /* Items Table (Sale transaction style) */
    .table-compact { font-size: 11px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 3px; vertical-align: middle; }
    .table-compact td { background: #fffacd; }
    .table-compact input, .table-compact select { font-size: 11px; padding: 2px 4px; height: 24px; border: 1px solid #aaa; width: 100%; box-sizing: border-box; min-width: 0; max-width: 100%; border-radius: 0 !important; }
    #itemsTable thead th { background: linear-gradient(180deg, #8B4513 0%, #654321 100%); color: #fff; font-weight: 600; text-align: center; border: 1px solid #5a3a1a; white-space: nowrap; padding: 4px 3px; font-size: 11px; }
    #itemsTable td { border: 1px solid #ccc; }
    .table-compact input[type="text"], .table-compact input[readonly] { text-overflow: ellipsis; white-space: nowrap; }
    #itemsTableBody .row-selected td { background: #cce5ff !important; }
    #itemsTableContainer { overflow-x: auto !important; overflow-y: auto; }
    #itemsTable { width: 100% !important; table-layout: fixed; min-width: 900px; }
    
    /* Items Table - Brown Header */
    .items-table { font-size: 11px; margin-bottom: 0; border-collapse: collapse; width: 100%; }
    .items-table th { background: linear-gradient(180deg, #8B4513 0%, #654321 100%); color: #fff; font-weight: 600; text-align: center; padding: 4px 3px; border: 1px solid #5a3a1a; white-space: nowrap; font-size: 11px; }
    .items-table td { padding: 3px; border: 1px solid #ccc; background: #fffacd; }
    .items-table input, .items-table select { font-size: 11px; padding: 2px 4px; height: 24px; border: 1px solid #aaa; width: 100%; }
    .items-table .row-selected td { background: #cce5ff !important; }
    
    /* Summary Row - Pink */
    .summary-section { background: #ffcccc; padding: 8px; border: 1px solid #cc9999; margin-bottom: 6px; border-radius: 3px; }
    .summary-section label { font-weight: bold; font-size: 11px; }
    .summary-section input { height: 24px; font-size: 11px; }
    
    /* Footer Section - Gray */
    .footer-section { background: #d4d4d4; padding: 8px; border: 1px solid #999; border-radius: 3px; }
    .footer-section label { font-size: 10px; margin-bottom: 1px; }
    .footer-section input { height: 22px; font-size: 10px; }
    
    /* First Footer Section - Purple */
    .first-footer-section { background: #e6d9f5; padding: 8px; border: 1px solid #b399d9; border-radius: 3px; }
    .first-footer-section label { font-size: 10px; margin-bottom: 1px; }
    .first-footer-section input { height: 22px; font-size: 10px; }
    .gst-box { background: #ffe6e6; border: 1px solid #cc9999; padding: 4px 6px; display: inline-flex; align-items: center; gap: 4px; border-radius: 3px; }
    .gst-box label { color: #dc3545; font-weight: bold; font-size: 10px; margin: 0; }
    .gst-box input { width: 45px; height: 20px; font-size: 10px; }
    
    /* Modal Styles */
    .modal-backdrop-custom { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; }
    .modal-backdrop-custom.show { display: block; }
    .custom-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; background: #fff; border-radius: 6px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); z-index: 1055; }
    .custom-modal.show { display: block; }
    .modal-header-custom { padding: 10px 15px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-radius: 6px 6px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .modal-header-custom.batch { background: #ffc107; color: #000; }
    .modal-body-custom { padding: 12px; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 8px 12px; border-top: 1px solid #ddd; text-align: right; }
    .item-row:hover, .batch-row:hover { background: #e3f2fd !important; cursor: pointer; }
    .item-row.selected, .batch-row.selected { background: #007bff !important; color: #fff !important; }
    
    /* Action Buttons */
    .action-buttons { display: flex; gap: 8px; justify-content: center; margin-top: 10px; }
    .action-buttons .btn { min-width: 100px; }
    
    /* Br/Ex Custom Dropdown */
    .br-ex-input { font-size: 11px !important; padding: 2px 4px !important; }
    .br-ex-dropdown { font-family: inherit; }
    .br-ex-option:hover { background-color: #e3f2fd !important; color: #000 !important; }
</style>
@endpush

@section('content')
<div class="bsi-form">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0"><i class="bi bi-box-arrow-up me-1"></i> Breakage/Expiry to Supplier - Issued Transaction</h6>
        <a href="{{ route('admin.breakage-supplier.issued-index') }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-list"></i> View All</a>
    </div>

    {{-- GST Vno Enter handler - defined early so inline onkeydown works --}}
    <script>
    function handleGstVnoEnter() {
        console.log('🔥 handleGstVnoEnter() called - GST Vno Enter key pressed');
        
        // Try showItemModal first (defined in main script block)
        if (typeof showItemModal === 'function') {
            console.log('✓ Calling showItemModal()');
            showItemModal();
            return;
        }
        
        // Direct fallback: open the reusable modal component
        if (typeof openItemModal_chooseItemsModal === 'function') {
            console.log('✓ Calling openItemModal_chooseItemsModal() directly');
            openItemModal_chooseItemsModal();
            return;
        }
        
        // Last resort fallback: open old-style modal
        var backdrop = document.getElementById('itemModalBackdrop');
        var modal = document.getElementById('itemModal');
        if (backdrop && modal) {
            console.log('✓ Opening fallback item modal');
            backdrop.classList.add('show');
            modal.classList.add('show');
            var searchInput = document.getElementById('itemSearchInput');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            return;
        }
        
        console.error('✗ No modal function found!');
    }
    </script>

    <form id="bsiForm" autocomplete="off" onsubmit="return false;">
        @csrf
        <!-- Header Section -->
        <div class="header-section">
            <div class="row g-2">
                <div class="col-md-2">
                    <div class="field-group"><label style="width:40px;">Date:</label><input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="updateDayName()"></div>
                    <div class="field-group"><label style="width:40px;"></label><input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" value="{{ date('l') }}" readonly style="width:85px;"></div>
                    <div class="field-group"><label style="width:40px;">Trn.No:</label><input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="{{ $trnNo }}" readonly style="width:60px;"></div>
                </div>
                <div class="col-md-10">
                    <div class="inner-card">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label style="width:55px;">Supplier:</label>
                                    <div class="custom-dropdown-wrapper" style="width: 100%;">
                                        <input type="text" class="form-control" id="supplierSearchInput" placeholder="Type to search supplier..." autocomplete="off">
                                        <input type="hidden" name="supplier_id" id="supplier_id">
                                        <div id="supplierDropdown" class="custom-dropdown-menu">
                                            <div class="dropdown-header">Select Supplier</div>
                                            <div id="supplierList">
                                                @foreach($suppliers as $s)
                                                    <div class="dropdown-item" data-id="{{ $s->supplier_id }}" data-name="{{ $s->name }}">
                                                        {{ $s->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>R(epl)/C(redit):</label>
                                    <div class="custom-dropdown-wrapper" style="width: 60px; position: relative;">
                                        <input type="text" class="form-control text-center" id="noteTypeSearchInput" placeholder="" autocomplete="off" readonly style="cursor: pointer; width: 60px;">
                                        <input type="hidden" name="note_type" id="note_type" value="C">
                                        <div id="noteTypeDropdown" class="custom-dropdown-menu" style="width: 60px;">
                                            <div class="dropdown-item" data-value="C">C</div>
                                            <div class="dropdown-item" data-value="R">R</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"><div class="field-group"><label>Tax[Y/N]:</label><input type="text" id="tax_flag" name="tax_flag" class="form-control text-center" value="N" maxlength="1" style="width:30px;"><label class="ms-2">Inc.</label><input type="text" id="inc_flag" name="inc_flag" class="form-control text-center" value="N" maxlength="1" style="width:30px;"></div></div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-md-3"><div class="field-group"><label>GST Vno.:</label><input type="text" id="gst_vno" name="gst_vno" class="form-control" data-custom-enter="true" onkeydown="if(event.key==='Enter'){event.preventDefault();event.stopPropagation();handleGstVnoEnter();return false;}"></div></div>
                            <div class="col-md-2"><div class="field-group"><label>Dis:</label><input type="number" id="dis_count" class="form-control readonly-field text-end" value="0" readonly style="width:45px;"></div></div>
                            <div class="col-md-2"><div class="field-group"><label>Rpl:</label><input type="number" id="rpl_count" class="form-control readonly-field text-end" value="0" readonly style="width:45px;"></div></div>
                            <div class="col-md-2"><div class="field-group"><label>Brk.:</label><input type="number" id="brk_count" class="form-control readonly-field text-end" value="0" readonly style="width:45px;"></div></div>
                            <div class="col-md-2"><div class="field-group"><label>Exp:</label><input type="number" id="exp_count" class="form-control readonly-field text-end" value="0" readonly style="width:45px;"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="supplier_name" name="supplier_name">

        <!-- Items Table Section -->
        <div class="bg-white border rounded p-2 mb-2">
            <div class="table-responsive" style="max-height: 280px; overflow-y: auto; overflow-x: auto;" id="itemsTableContainer">
                <table class="items-table" id="itemsTable" style="table-layout: fixed; width: 100%; min-width: 900px;">
                    <thead style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 50px;">Code</th>
                            <th style="width: 150px;">Item Name</th>
                            <th style="width: 70px;">Batch</th>
                            <th style="width: 60px;">Exp</th>
                            <th style="width: 45px;">Qty</th>
                            <th style="width: 45px;">F.Q</th>
                            <th style="width: 60px;">Rate</th>
                            <th style="width: 50px;">Dis%</th>
                            <th style="width: 50px;">Scm%</th>
                            <th style="width: 55px;">Br/Ex</th>
                            <th style="width: 70px;">Amount</th>
                            <th style="width: 30px;">X</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody"></tbody>
                </table>
            </div>
            <div class="text-center mt-2 border-top pt-2">
                <button type="button" class="btn btn-link text-decoration-none fw-bold text-primary p-0" onclick="showItemModal()">
                    <i class="bi bi-plus-circle me-1"></i> Add Item (F2)
                </button>
            </div>
        </div>

        
                <!-- Section 2 - Gray (SC%, EXCISE, TAX%, CGST, SGST, etc.) -->
                <div class="footer-section mb-2">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center gap-1 me-2"><label>SC %</label><input type="number" id="footer_sc_percent" class="form-control readonly-field text-end" readonly style="width:50px;"></div>
                        <div class="d-flex align-items-center gap-1 me-2"><label>EXCISE</label><input type="number" id="footer_excise" class="form-control readonly-field text-end" readonly style="width:60px;"></div>
                        <div class="gst-box me-2"><label class="text-danger">CGST(%):</label><input type="number" id="footer_cgst" class="form-control text-end" readonly style="width:50px;"></div>
                        <div class="d-flex align-items-center gap-1 me-2"><label>HSN</label><input type="text" id="footer_hsn" class="form-control readonly-field" readonly style="width:80px;"></div>
                        <div class="d-flex align-items-center gap-1 me-3"><label>Pack</label><input type="text" id="footer_pack2" class="form-control readonly-field" readonly style="width:70px;"></div>
                        <div class="d-flex align-items-center gap-1 me-1"><label>Disallow</label><input type="text" id="footer_disallow" class="form-control readonly-field text-center" value="N" readonly style="width:30px;"></div>
                        <div class="d-flex align-items-center gap-1 me-2"><label>MRP</label><input type="number" id="footer_mrp" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                    </div>
                    <div class="d-flex align-items-center mt-1">
                        <div class="d-flex align-items-center gap-1 me-2"><label>TAX %</label><input type="number" id="footer_tax_percent" class="form-control readonly-field text-end" readonly style="width:50px;"></div>
                        <div class="gst-box me-2"><label class="text-danger">CGST Amt:</label><input type="number" id="footer_cgst_amt" class="form-control text-end" readonly style="width:60px;"></div>
                        <div class="gst-box me-2"><label class="text-danger">SGST(%):</label><input type="number" id="footer_sgst" class="form-control text-end" readonly style="width:50px;"></div>
                        <div class="gst-box me-2"><label class="text-danger">SGST Amt:</label><input type="number" id="footer_sgst_amt" class="form-control text-end" readonly style="width:60px;"></div>
                        <div class="d-flex align-items-center gap-1 me-2"><label>P.RATE</label><input type="number" id="footer_prate" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>S.RATE</label><input type="number" id="footer_srate" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                    </div>
                </div>
        <!-- Section 1 - Pink Summary (N.T AMT, SC, DIS. AMT, etc.) -->
        <div class="summary-section mb-2">
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">N.T AMT</label><input type="number" id="total_nt_amt" name="total_nt_amt" class="form-control readonly-field text-end fw-bold" readonly style="width:90px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">SC</label><input type="number" id="total_sc" name="total_sc" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">DIS. AMT</label><input type="number" id="total_dis_amt" name="total_dis_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">Scm. AMT</label><input type="number" id="total_scm_amt" name="total_scm_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">Half.Scm.</label><input type="number" id="total_half_scm" name="total_half_scm" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">Tax</label><input type="number" id="total_tax" name="total_tax" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="fw-bold">INV. AMT</label><input type="number" id="total_inv_amt" name="total_inv_amt" class="form-control text-end fw-bold" readonly style="width:100px;"></div>
            </div>
        </div>

        <!-- Section 3 - Purple Header (Pack, Comp, N.T Amt, etc.) -->
        <div class="first-footer-section mb-2">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center gap-1 me-2"><label>Pack</label><input type="text" id="footer_pack" class="form-control readonly-field" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Comp :</label><input type="text" id="footer_comp" class="form-control readonly-field" readonly style="width:100px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>N.T Amt.</label><input type="number" id="footer_nt_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>DIS. Amt.</label><input type="number" id="footer_dis_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Net Amt.</label><input type="number" id="footer_net_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>P.Scm.</label><input type="number" id="footer_pscm" class="form-control readonly-field text-end" readonly style="width:60px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>S.Scm.</label><input type="number" id="footer_sscm" class="form-control readonly-field text-end" readonly style="width:60px;"></div>
                <div class="d-flex align-items-center gap-1"><label>+</label></div>
            </div>
            <div class="d-flex align-items-center mt-1">
                <div class="d-flex align-items-center gap-1 me-2"><label>Unit</label><input type="text" id="footer_unit" class="form-control readonly-field" readonly style="width:50px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Bal.</label><input type="number" id="footer_bal" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Srlno.</label><input type="text" id="footer_srlno" class="form-control readonly-field" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Half Scm.</label><input type="number" id="footer_half_scm" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Scm.Amt.</label><input type="number" id="footer_scm_amt" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1 me-2"><label>Tax Amt.</label><input type="number" id="footer_tax_amt" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label>+</label></div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="button" class="btn btn-success btn-sm" onclick="saveTransaction()"><i class="bi bi-check-lg me-1"></i> Save (End)</button>
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedItem()"><i class="bi bi-trash me-1"></i> Delete Item</button>
            <button type="button" class="btn btn-info btn-sm text-white" onclick="viewOnScreen()"><i class="bi bi-eye me-1"></i> View On Screen</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelTransaction()"><i class="bi bi-x-lg me-1"></i> Cancel</button>
        </div>
    </form>
</div>

<!-- Item Selection Modal -->
<div class="modal-backdrop-custom" id="itemModalBackdrop" onclick="closeItemModal()"></div>
<div class="custom-modal" id="itemModal">
    <div class="modal-header-custom">
        <h6 class="mb-0"><i class="bi bi-search me-1"></i> Select Item</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeItemModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="itemSearchInput" class="form-control form-control-sm mb-2" placeholder="Search by code or name..." onkeyup="filterItems()">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-light sticky-top"><tr><th>Code</th><th>Item Name</th><th>Pack</th><th>Company</th></tr></thead>
                <tbody id="itemsListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
    </div>
</div>

<!-- Batch Selection Modal -->
<div class="modal-backdrop-custom" id="batchModalBackdrop" onclick="closeBatchModal()"></div>
<div class="custom-modal" id="batchModal">
    <div class="modal-header-custom batch">
        <h6 class="mb-0"><i class="bi bi-box me-1"></i> Select Batch</h6>
        <button type="button" class="btn btn-sm btn-dark" onclick="closeBatchModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <div class="mb-2 p-2 bg-light rounded"><strong id="selectedItemName">-</strong></div>
        <div class="table-responsive" style="max-height: 280px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-warning sticky-top"><tr><th>Batch</th><th>Expiry</th><th>Qty</th><th>MRP</th><th>P.Rate</th><th>S.Rate</th></tr></thead>
                <tbody id="batchesListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
    </div>
</div>

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'breakage-supplier',
    'showStock' => true,
    'rateType' => 'p_rate',
    'showCompany' => true,
    'showHsn' => true,
    'batchModalId' => 'batchSelectionModal',
])

@include('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'breakage-supplier',
    'showOnlyAvailable' => true,
    'rateType' => 'p_rate',
    'showCostDetails' => true,
])

@endsection


@push('scripts')
<script>
console.log('SCRIPT STARTED');

let rowIndex = 0, allItems = [], selectedRowIndex = null, selectedItem = null;

document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log('=== DOMContentLoaded Event Fired ===');
        console.log('Initializing modules...');
        
        // Prevent form submission on Enter key
        const bsiForm = document.getElementById('bsiForm');
        if (bsiForm) {
            bsiForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submission prevented');
                return false;
            });
            
            bsiForm.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    // GST Vno field has its own capture-phase handler that triggers Add Item
                    if (e.target.id === 'gst_vno') {
                        return; // Handled by field-specific capture-phase handler
                    }
                    
                    // Prevent default form submission for all other fields
                    e.preventDefault();
                    console.log('Enter pressed in form on:', e.target.id || e.target.name);
                }
            });
            console.log('✓ Form submit prevention added');
        }
        
        loadItems();
        console.log('✓ loadItems called');
    } catch(e) {
        console.error('Error in loadItems:', e);
        alert('Error in loadItems: ' + e.message);
    }
    
    try {
        initSupplierDropdown();
        console.log('✓ initSupplierDropdown called');
    } catch(e) {
        console.error('Error in initSupplierDropdown:', e);
        alert('Error in initSupplierDropdown: ' + e.message);
    }
    
    try {
        initNoteTypeDropdown();
        console.log('✓ initNoteTypeDropdown called');
    } catch(e) {
        console.error('Error in initNoteTypeDropdown:', e);
        alert('Error in initNoteTypeDropdown: ' + e.message);
    }
    
    try {
        initHeaderFieldNavigation();
        console.log('✓ initHeaderFieldNavigation called');
    } catch(e) {
        console.error('Error in initHeaderFieldNavigation:', e);
        alert('Error in initHeaderFieldNavigation: ' + e.message);
    }
    
    try {
        initGlobalKeyboardShortcuts();
        console.log('✓ initGlobalKeyboardShortcuts called');
    } catch(e) {
        console.error('Error in initGlobalKeyboardShortcuts:', e);
        alert('Error in initGlobalKeyboardShortcuts: ' + e.message);
    }
    
    // Verify GST Vno handler is properly attached
    setTimeout(() => {
        const gstVnoField = document.getElementById('gst_vno');
        if (gstVnoField) {
            console.log('✅ GST Vno field verified — Enter key will trigger Add Item (F2)');
        } else {
            console.error('❌ GST Vno field NOT found in DOM');
        }
    }, 200);
    
    // Set default focus on date field
    setTimeout(() => {
        try {
            const dateField = document.getElementById('transaction_date');
            if (dateField) {
                dateField.focus();
                console.log('✓ Default focus set on date field');
            }
        } catch(e) {
            console.error('Error setting focus:', e);
        }
    }, 100);
    
    console.log('=== All Initialization Complete ===');
});

function updateDayName() {
    const d = new Date(document.getElementById('transaction_date').value);
    document.getElementById('day_name').value = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d.getDay()];
}

function updateSupplierName() {
    const sel = document.getElementById('supplier_id');
    document.getElementById('supplier_name').value = sel.options[sel.selectedIndex]?.dataset.name || '';
}

function loadItems() {
    fetch('{{ route("admin.breakage-supplier.get-items") }}')
        .then(r => r.json())
        .then(data => { allItems = data || []; })
        .catch(e => console.error('Error loading items:', e));
}

// Item Modal - Redirect to reusable modal component
function showItemModal() {
    console.log('showItemModal called');
    // Use reusable item selection modal
    if (typeof openItemModal_chooseItemsModal === 'function') {
        console.log('Using reusable modal component');
        openItemModal_chooseItemsModal();
    } else {
        console.log('Using fallback modal');
        // Fallback to old modal behavior
        document.getElementById('itemModalBackdrop').classList.add('show');
        document.getElementById('itemModal').classList.add('show');
        document.getElementById('itemSearchInput').value = '';
        renderItemsList(allItems);
        setTimeout(() => document.getElementById('itemSearchInput').focus(), 100);
    }
}

// Callback function when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('Item selected from modal:', item);
    console.log('Batch selected from modal:', batch);
    
    // Transform item to match expected format for addItemRow
    const transformedItem = {
        id: item.id,
        item_code: item.bar_code || item.code || '',
        item_name: item.name || '',
        packing: item.packing || '',
        company_name: item.company_name || '',
        hsn_code: item.hsn_code || '',
        unit: item.unit || '',
        cgst: item.cgst_percent || 0,
        sgst: item.sgst_percent || 0
    };
    
    // Transform batch to match expected format
    const transformedBatch = {
        id: batch.id,
        batch_no: batch.batch_no || '',
        expiry_date: batch.expiry_date || '',
        mrp: batch.mrp || 0,
        purchase_rate: batch.p_rate || batch.pur_rate || batch.purchase_rate || 0,
        sale_rate: batch.s_rate || batch.sale_rate || 0,
        quantity: batch.qty || batch.quantity || 0
    };
    
    // Use existing addItemRow function
    addItemRow(transformedItem, transformedBatch);
};

function closeItemModal() {
    document.getElementById('itemModalBackdrop').classList.remove('show');
    document.getElementById('itemModal').classList.remove('show');
}

function filterItems() {
    const search = document.getElementById('itemSearchInput').value.toLowerCase();
    const filtered = allItems.filter(item => 
        (item.item_code && item.item_code.toLowerCase().includes(search)) ||
        (item.item_name && item.item_name.toLowerCase().includes(search))
    );
    renderItemsList(filtered);
}

function renderItemsList(items) {
    const tbody = document.getElementById('itemsListBody');
    if (!items.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No items found</td></tr>';
        return;
    }
    tbody.innerHTML = items.slice(0, 100).map(item => `
        <tr class="item-row" onclick="selectItem(${item.id})">
            <td>${item.item_code || ''}</td>
            <td>${item.item_name || ''}</td>
            <td>${item.packing || ''}</td>
            <td>${item.company_name || ''}</td>
        </tr>
    `).join('');
}

function selectItem(itemId) {
    selectedItem = allItems.find(i => i.id === itemId);
    if (!selectedItem) return;
    closeItemModal();
    document.getElementById('selectedItemName').textContent = `${selectedItem.item_code} - ${selectedItem.item_name}`;
    loadBatches(itemId);
}

function loadBatches(itemId) {
    fetch(`{{ url('admin/breakage-supplier/get-batches') }}/${itemId}`)
        .then(r => r.json())
        .then(batches => {
            document.getElementById('batchModalBackdrop').classList.add('show');
            document.getElementById('batchModal').classList.add('show');
            renderBatchesList(batches);
        })
        .catch(e => {
            console.error('Error loading batches:', e);
            addItemRow(selectedItem, null);
        });
}

function renderBatchesList(batches) {
    const tbody = document.getElementById('batchesListBody');
    if (!batches.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No batches found</td></tr>';
        return;
    }
    tbody.innerHTML = batches.map(batch => `
        <tr class="batch-row" onclick="selectBatch(${JSON.stringify(batch).replace(/"/g, '&quot;')})">
            <td>${batch.batch_no || ''}</td>
            <td>${batch.expiry_date || ''}</td>
            <td class="text-end">${batch.quantity || 0}</td>
            <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.purchase_rate || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.sale_rate || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function closeBatchModal() {
    document.getElementById('batchModalBackdrop').classList.remove('show');
    document.getElementById('batchModal').classList.remove('show');
}

function selectBatch(batch) {
    closeBatchModal();
    addItemRow(selectedItem, batch);
}

function addItemRow(item, batch) {
    const tbody = document.getElementById('itemsTableBody');
    const idx = rowIndex++;
    const rate = batch ? parseFloat(batch.purchase_rate || 0) : 0;
    
    console.log('Adding item row - Item:', item);
    console.log('CGST:', item.cgst, 'SGST:', item.sgst);
    
    const tr = document.createElement('tr');
    tr.id = `row_${idx}`;
    tr.onclick = function() { selectRow(idx); };
    tr.innerHTML = `
        <td><input type="text" name="items[${idx}][item_code]" value="${item.item_code || ''}" readonly class="readonly-field" 
                   onkeydown="handleGridEnterKey(event, ${idx}, 'item_code')"></td>
        <td><input type="text" name="items[${idx}][item_name]" value="${item.item_name || ''}" readonly class="readonly-field"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'item_name')"></td>
        <td><input type="text" name="items[${idx}][batch_no]" value="${batch?.batch_no || ''}" readonly class="readonly-field"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'batch_no')"></td>
        <td><input type="text" name="items[${idx}][expiry]" value="${batch?.expiry_date || ''}" readonly class="readonly-field"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'expiry')"></td>
        <td><input type="number" name="items[${idx}][qty]" value="" min="0" class="text-end" 
                   onchange="calculateRowAmount(${idx})" 
                   onkeydown="handleGridEnterKey(event, ${idx}, 'qty')"></td>
        <td><input type="number" name="items[${idx}][free_qty]" value="0" min="0" class="text-end"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'free_qty')"></td>
        <td><input type="number" name="items[${idx}][rate]" value="${rate.toFixed(2)}" step="0.01" class="text-end" 
                   onchange="calculateRowAmount(${idx})"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'rate')"></td>
        <td><input type="number" name="items[${idx}][dis_percent]" value="0" step="0.01" class="text-end" 
                   onchange="calculateRowAmount(${idx})"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'dis_percent')"></td>
        <td><input type="number" name="items[${idx}][scm_percent]" value="0" step="0.01" class="text-end" 
                   data-custom-enter="true"
                   onchange="calculateRowAmount(${idx})"
                   onkeydown="handleGridEnterKey(event, ${idx}, 'scm_percent')"></td>
        <td style="position:relative;">
            <input type="text" class="form-control text-center br-ex-input" id="br_ex_display_${idx}" 
                   value="Brk" readonly style="cursor:pointer;width:55px;"
                   onfocus="openBrExDropdown(${idx})"
                   onclick="openBrExDropdown(${idx})"
                   onkeydown="handleBrExKeyDown(event, ${idx})">
            <input type="hidden" name="items[${idx}][br_ex]" id="br_ex_${idx}" value="B">
            <div class="br-ex-dropdown" id="br_ex_dropdown_${idx}" style="display:none;position:absolute;top:100%;left:0;z-index:100;background:#fff;border:1px solid #ccc;border-radius:4px;box-shadow:0 2px 8px rgba(0,0,0,0.15);width:55px;">
                <div class="br-ex-option" data-value="B" onclick="selectBrEx(${idx},'B','Brk')" style="padding:4px 8px;cursor:pointer;font-size:11px;">Brk</div>
                <div class="br-ex-option" data-value="E" onclick="selectBrEx(${idx},'E','Exp')" style="padding:4px 8px;cursor:pointer;font-size:11px;">Exp</div>
            </div>
        </td>
        <td><input type="number" name="items[${idx}][amount]" value="0" step="0.01" class="text-end readonly-field" readonly></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm py-0 px-1" onclick="removeRow(${idx})">&times;</button>
            <input type="hidden" name="items[${idx}][item_id]" value="${item.id}">
            <input type="hidden" name="items[${idx}][batch_id]" value="${batch?.id || ''}">
            <input type="hidden" name="items[${idx}][mrp]" value="${batch?.mrp || 0}">
            <input type="hidden" name="items[${idx}][purchase_rate]" value="${batch?.purchase_rate || 0}">
            <input type="hidden" name="items[${idx}][sale_rate]" value="${batch?.sale_rate || 0}">
            <input type="hidden" name="items[${idx}][cgst]" value="${item.cgst || 0}">
            <input type="hidden" name="items[${idx}][sgst]" value="${item.sgst || 0}">
            <input type="hidden" name="items[${idx}][company_name]" value="${item.company_name || ''}">
            <input type="hidden" name="items[${idx}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${idx}][unit]" value="${item.unit || ''}">
            <input type="hidden" name="items[${idx}][hsn_code]" value="${item.hsn_code || ''}">
        </td>
    `;
    tbody.appendChild(tr);
    selectRow(idx);
    
    // Focus on qty field after adding row
    setTimeout(() => {
        const qtyInput = tr.querySelector('input[name*="[qty]"]');
        if (qtyInput) {
            qtyInput.focus();
            qtyInput.select();
        }
    }, 100);
    
    calculateTotals();
}

function selectRow(idx) {
    document.querySelectorAll('#itemsTableBody tr').forEach(tr => tr.classList.remove('row-selected'));
    const row = document.getElementById(`row_${idx}`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = idx;
        updateFooterFromRow(row);
    }
}

function updateFooterFromRow(row) {
    const getValue = (name) => row.querySelector(`input[name*="[${name}]"]`)?.value || '';
    const getHiddenValue = (name) => row.querySelector(`input[type="hidden"][name*="[${name}]"]`)?.value || '';
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const rate = parseFloat(getValue('rate')) || 0;
    const amount = parseFloat(getValue('amount')) || 0;
    const cgstPercent = parseFloat(getHiddenValue('cgst')) || 0;
    const sgstPercent = parseFloat(getHiddenValue('sgst')) || 0;
    const disPercent = parseFloat(getValue('dis_percent')) || 0;
    const scmPercent = parseFloat(getValue('scm_percent')) || 0;
    
    console.log('Footer update - CGST:', cgstPercent, 'SGST:', sgstPercent);
    
    // Calculate N.T Amount (qty * rate)
    const ntAmount = qty * rate;
    
    // Calculate discount amount
    const disAmount = (ntAmount * disPercent) / 100;
    
    // Calculate scheme amount
    const scmAmount = (ntAmount * scmPercent) / 100;
    
    // Calculate net amount after discount
    const netAmount = ntAmount - disAmount;
    
    // Calculate CGST and SGST amounts based on net amount
    const cgstAmount = (netAmount * cgstPercent) / 100;
    const sgstAmount = (netAmount * sgstPercent) / 100;
    const totalTaxPercent = cgstPercent + sgstPercent;
    const totalTaxAmount = cgstAmount + sgstAmount;
    
    // Section 2 - Gray (Tax details)
    document.getElementById('footer_mrp').value = getHiddenValue('mrp');
    document.getElementById('footer_prate').value = getHiddenValue('purchase_rate');
    document.getElementById('footer_srate').value = getHiddenValue('sale_rate');
    document.getElementById('footer_cgst').value = cgstPercent.toFixed(2);
    document.getElementById('footer_sgst').value = sgstPercent.toFixed(2);
    document.getElementById('footer_cgst_amt').value = cgstAmount.toFixed(2);
    document.getElementById('footer_sgst_amt').value = sgstAmount.toFixed(2);
    document.getElementById('footer_tax_percent').value = totalTaxPercent.toFixed(2);
    document.getElementById('footer_hsn').value = getHiddenValue('hsn_code');
    document.getElementById('footer_pack2').value = getHiddenValue('packing');
    document.getElementById('footer_sc_percent').value = scmPercent.toFixed(2);
    document.getElementById('footer_excise').value = '0.00';
    document.getElementById('footer_disallow').value = 'N';
    
    // Section 3 - Purple (Item details)
    document.getElementById('footer_comp').value = getHiddenValue('company_name');
    document.getElementById('footer_pack').value = getHiddenValue('packing');
    document.getElementById('footer_unit').value = getHiddenValue('unit');
    document.getElementById('footer_nt_amt').value = ntAmount.toFixed(2);
    document.getElementById('footer_dis_amt').value = disAmount.toFixed(2);
    document.getElementById('footer_net_amt').value = netAmount.toFixed(2);
    document.getElementById('footer_scm_amt').value = scmAmount.toFixed(2);
    document.getElementById('footer_tax_amt').value = totalTaxAmount.toFixed(2);
    document.getElementById('footer_pscm').value = '0.00';
    document.getElementById('footer_sscm').value = '0.00';
    document.getElementById('footer_half_scm').value = '0.00';
    document.getElementById('footer_bal').value = '0.00';
    document.getElementById('footer_srlno').value = '';
}

function removeRow(idx) {
    const row = document.getElementById(`row_${idx}`);
    if (row) row.remove();
    calculateTotals();
}

function deleteSelectedItem() {
    if (selectedRowIndex !== null) removeRow(selectedRowIndex);
}

function calculateRowAmount(idx) {
    const row = document.getElementById(`row_${idx}`);
    if (!row) return;
    const qty = parseFloat(row.querySelector(`input[name="items[${idx}][qty]"]`).value) || 0;
    const rate = parseFloat(row.querySelector(`input[name="items[${idx}][rate]"]`).value) || 0;
    const disPct = parseFloat(row.querySelector(`input[name="items[${idx}][dis_percent]"]`).value) || 0;
    
    let amount = qty * rate;
    if (disPct > 0) amount -= (amount * disPct / 100);
    
    row.querySelector(`input[name="items[${idx}][amount]"]`).value = amount.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let totalNtAmt = 0, totalDisAmt = 0, totalScmAmt = 0, totalTax = 0, totalSc = 0;
    let brkCount = 0, expCount = 0, disCount = 0, rplCount = 0;
    
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
        const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
        const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value) || 0;
        const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]')?.value) || 0;
        const brEx = row.querySelector('input[name*="[br_ex]"]')?.value || 'B';
        
        const cgstPercent = parseFloat(row.querySelector('input[type="hidden"][name*="[cgst]"]')?.value) || 0;
        const sgstPercent = parseFloat(row.querySelector('input[type="hidden"][name*="[sgst]"]')?.value) || 0;
        
        // Calculate N.T Amount
        const ntAmt = qty * rate;
        totalNtAmt += ntAmt;
        
        // Calculate discount amount
        const disAmt = (ntAmt * disPercent) / 100;
        totalDisAmt += disAmt;
        
        // Calculate scheme amount
        const scmAmt = (ntAmt * scmPercent) / 100;
        totalScmAmt += scmAmt;
        
        // Calculate net amount after discount
        const netAmt = ntAmt - disAmt;
        
        // Calculate tax on net amount
        const taxAmt = (netAmt * (cgstPercent + sgstPercent)) / 100;
        totalTax += taxAmt;
        
        // Count by type
        if (brEx === 'B') brkCount++;
        else if (brEx === 'E') expCount++;
        else if (brEx === 'D') disCount++;
        else if (brEx === 'R') rplCount++;
    });
    
    // Calculate invoice amount (net amount + tax)
    const totalInvAmt = totalNtAmt - totalDisAmt + totalTax;
    
    // Update Section 1 - Pink (Summary)
    document.getElementById('total_nt_amt').value = totalNtAmt.toFixed(2);
    document.getElementById('total_sc').value = totalSc.toFixed(2);
    document.getElementById('total_dis_amt').value = totalDisAmt.toFixed(2);
    document.getElementById('total_scm_amt').value = totalScmAmt.toFixed(2);
    document.getElementById('total_half_scm').value = '0.00';
    document.getElementById('total_tax').value = totalTax.toFixed(2);
    document.getElementById('total_inv_amt').value = totalInvAmt.toFixed(2);
    
    // Update counts
    document.getElementById('brk_count').value = brkCount;
    document.getElementById('exp_count').value = expCount;
    document.getElementById('dis_count').value = disCount;
    document.getElementById('rpl_count').value = rplCount;
}

function saveTransaction() {
    const supplierId = document.getElementById('supplier_id').value;
    if (!supplierId) { alert('Please select a supplier'); return; }
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (!rows.length) { alert('Please add at least one item'); return; }
    
    const formData = new FormData(document.getElementById('bsiForm'));
    
    fetch('{{ route("admin.breakage-supplier.store-issued") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (typeof resetFormDirty === 'function') resetFormDirty();
            alert('Transaction saved successfully!');
            window.location.href = '{{ route("admin.breakage-supplier.issued-index") }}';
        } else {
            alert(data.message || 'Error saving transaction');
        }
    })
    .catch(e => {
        console.error('Error:', e);
        alert('Error saving transaction');
    });
}

function cancelTransaction() {
    if (confirm('Are you sure you want to cancel?')) {
        window.location.href = '{{ route("admin.breakage-supplier.issued-index") }}';
    }
}

function viewOnScreen() {
    alert('View on screen feature - Coming soon');
}

// ============================================
// KEYBOARD NAVIGATION FUNCTIONS
// ============================================

// Initialize Supplier Dropdown with Keyboard Navigation
function initSupplierDropdown() {
    const supplierSearchInput = document.getElementById('supplierSearchInput');
    const supplierDropdown = document.getElementById('supplierDropdown');
    const supplierIdInput = document.getElementById('supplier_id');
    const supplierList = document.getElementById('supplierList');
    
    if (!supplierSearchInput || !supplierDropdown || !supplierIdInput || !supplierList) return;
    
    let supplierActiveIndex = -1;
    
    // Get visible supplier items
    function getVisibleSupplierItems() {
        return Array.from(supplierList.querySelectorAll('.dropdown-item')).filter(item => 
            item.style.display !== 'none'
        );
    }
    
    // Set active supplier item
    function setActiveSupplierItem(index) {
        const items = getVisibleSupplierItems();
        items.forEach(item => item.classList.remove('active'));
        if (index >= 0 && index < items.length) {
            items[index].classList.add('active');
            items[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            supplierActiveIndex = index;
        }
    }
    
    // Select supplier item
    function selectSupplierItem(item, closeDropdown = false) {
        const supplierId = item.getAttribute('data-id');
        const supplierName = item.getAttribute('data-name');
        
        console.log('selectSupplierItem called', { supplierId, supplierName, closeDropdown });
        
        supplierSearchInput.value = supplierName;
        supplierIdInput.value = supplierId;
        document.getElementById('supplier_name').value = supplierName;
        
        if (closeDropdown) {
            console.log('Closing dropdown and moving focus...');
            supplierDropdown.style.display = 'none';
            
            // Blur the supplier input first to ensure clean focus transfer
            supplierSearchInput.blur();
            
            // Move focus to next field (noteTypeSearchInput) after dropdown closes
            setTimeout(() => {
                const noteTypeField = document.getElementById('noteTypeSearchInput');
                const noteTypeDropdownEl = document.getElementById('noteTypeDropdown');
                console.log('Attempting to focus noteTypeSearchInput:', noteTypeField);
                if (noteTypeField && noteTypeDropdownEl) {
                    noteTypeField.focus();
                    // Manually open the dropdown
                    noteTypeDropdownEl.style.display = 'block';
                    console.log('✓ Focus moved to noteTypeSearchInput and dropdown opened');
                    console.log('Active element after focus:', document.activeElement.id);
                    
                    // Trigger the focus event to set active index
                    const focusEvent = new Event('focus');
                    noteTypeField.dispatchEvent(focusEvent);
                } else {
                    console.error('✗ noteTypeSearchInput field not found');
                }
            }, 100);
        }
        return true;
    }
    
    // Filter suppliers
    function filterSuppliers(searchText) {
        const search = searchText.toLowerCase();
        const items = supplierList.querySelectorAll('.dropdown-item');
        let visibleCount = 0;
        
        items.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            if (name.includes(search)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        supplierActiveIndex = visibleCount > 0 ? 0 : -1;
        if (visibleCount > 0) {
            setActiveSupplierItem(0);
        }
    }
    
    // Event listeners
    supplierSearchInput.addEventListener('focus', function() {
        supplierDropdown.style.display = 'block';
        filterSuppliers(this.value || '');
    });
    
    supplierSearchInput.addEventListener('input', function() {
        filterSuppliers(this.value);
        supplierDropdown.style.display = 'block';
    });
    
    // Enter key on supplier input should also move to next field if dropdown is closed
    supplierSearchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && supplierDropdown.style.display === 'none' && supplierIdInput.value) {
            e.preventDefault();
            const noteTypeField = document.getElementById('note_type');
            if (noteTypeField) {
                noteTypeField.focus();
            }
        }
    });
    
    // Click outside to close
    document.addEventListener('click', function(e) {
        if (!supplierSearchInput.contains(e.target) && !supplierDropdown.contains(e.target)) {
            supplierDropdown.style.display = 'none';
        }
    });
    
    // Click on item
    supplierList.addEventListener('click', function(e) {
        const item = e.target.closest('.dropdown-item');
        if (item) {
            e.preventDefault();
            e.stopPropagation();
            selectSupplierItem(item, true);
        }
    });
    
    // Keyboard navigation
    supplierSearchInput.addEventListener('keydown', function(e) {
        console.log('Supplier input keydown:', e.key);
        
        // Prevent Tab key
        if (e.key === 'Tab') {
            e.preventDefault();
            console.log('Tab key prevented on supplier');
            return false;
        }
        
        if (e.key === 'Enter') {
            console.log('Enter key pressed on supplier input');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const visibleItems = getVisibleSupplierItems();
            console.log('Visible items:', visibleItems.length, 'Active index:', supplierActiveIndex);
            
            let selected = false;
            if (supplierActiveIndex >= 0 && visibleItems[supplierActiveIndex]) {
                console.log('Selecting active item at index:', supplierActiveIndex);
                selected = selectSupplierItem(visibleItems[supplierActiveIndex], true);
            } else if (visibleItems.length >= 1) {
                console.log('Selecting first visible item');
                selected = selectSupplierItem(visibleItems[0], true);
            }
            
            // If selection was successful, ensure dropdown is closed
            if (selected) {
                console.log('Selection successful, closing dropdown');
                supplierDropdown.style.display = 'none';
            } else {
                console.log('Selection failed');
            }
            
            return false;
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            const items = getVisibleSupplierItems();
            if (!items.length) return;
            if (supplierDropdown.style.display !== 'block') {
                supplierDropdown.style.display = 'block';
            }
            const nextIndex = supplierActiveIndex < 0 ? 0 : Math.min(supplierActiveIndex + 1, items.length - 1);
            setActiveSupplierItem(nextIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            const items = getVisibleSupplierItems();
            if (!items.length) return;
            if (supplierDropdown.style.display !== 'block') {
                supplierDropdown.style.display = 'block';
            }
            const prevIndex = supplierActiveIndex <= 0 ? 0 : supplierActiveIndex - 1;
            setActiveSupplierItem(prevIndex);
        } else if (e.key === 'Escape') {
            e.preventDefault();
            supplierDropdown.style.display = 'none';
        }
    }, true);
    
    // Global capture to ensure dropdown selection works
    window.addEventListener('keydown', function(e) {
        const activeEl = document.activeElement;
        const isSupplierFocus = activeEl === supplierSearchInput || supplierDropdown.contains(activeEl);
        const isDropdownOpen = supplierDropdown.style.display === 'block';
        
        if (!isSupplierFocus || !isDropdownOpen) return;

        if (e.key === 'Enter') {
            console.log('Global Enter handler triggered for supplier dropdown');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const visibleItems = getVisibleSupplierItems();
            console.log('Global handler - Visible items:', visibleItems.length, 'Active index:', supplierActiveIndex);
            
            let selected = false;
            if (supplierActiveIndex >= 0 && visibleItems[supplierActiveIndex]) {
                console.log('Global handler - Selecting active item');
                selected = selectSupplierItem(visibleItems[supplierActiveIndex], true);
            } else if (visibleItems.length >= 1) {
                console.log('Global handler - Selecting first item');
                selected = selectSupplierItem(visibleItems[0], true);
            }
            
            // If selection was successful, ensure dropdown is closed
            if (selected) {
                console.log('Global handler - Selection successful');
                supplierDropdown.style.display = 'none';
            }
            
            return false;
        }
    }, true);
}

// Initialize Note Type (R/C) Dropdown with Auto-Open and Keyboard Navigation
function initNoteTypeDropdown() {
    const noteTypeSearchInput = document.getElementById('noteTypeSearchInput');
    const noteTypeDropdown = document.getElementById('noteTypeDropdown');
    const noteTypeHiddenInput = document.getElementById('note_type');
    
    if (!noteTypeSearchInput || !noteTypeDropdown || !noteTypeHiddenInput) return;
    
    let noteTypeActiveIndex = -1;
    
    // Set initial display value
    noteTypeSearchInput.value = noteTypeHiddenInput.value || 'C';
    
    // Get visible note type items
    function getVisibleNoteTypeItems() {
        return Array.from(noteTypeDropdown.querySelectorAll('.dropdown-item'));
    }
    
    // Set active note type item
    function setActiveNoteTypeItem(index) {
        const items = getVisibleNoteTypeItems();
        items.forEach(item => item.classList.remove('active'));
        if (index >= 0 && index < items.length) {
            items[index].classList.add('active');
            items[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            noteTypeActiveIndex = index;
        }
    }
    
    // Select note type item
    function selectNoteTypeItem(item, closeDropdown = false) {
        const value = item.getAttribute('data-value');
        
        console.log('selectNoteTypeItem called', { value, closeDropdown });
        
        noteTypeSearchInput.value = value;
        noteTypeHiddenInput.value = value;
        
        if (closeDropdown) {
            console.log('Closing note type dropdown and moving focus...');
            noteTypeDropdown.style.display = 'none';
            
            // Blur the input first
            noteTypeSearchInput.blur();
            
            // Move focus to next field (tax_flag)
            requestAnimationFrame(() => {
                const taxFlagField = document.getElementById('tax_flag');
                console.log('Attempting to focus tax_flag:', taxFlagField);
                if (taxFlagField) {
                    taxFlagField.focus();
                    taxFlagField.select();
                    console.log('✓ Focus moved to tax_flag');
                } else {
                    console.error('✗ tax_flag field not found');
                }
            });
        }
        return true;
    }
    
    // Open dropdown on focus
    noteTypeSearchInput.addEventListener('focus', function() {
        console.log('Note type field focused - opening dropdown');
        noteTypeDropdown.style.display = 'block';
        
        // Set active index to current value
        const items = getVisibleNoteTypeItems();
        const currentValue = noteTypeHiddenInput.value;
        const currentIndex = items.findIndex(item => item.getAttribute('data-value') === currentValue);
        setActiveNoteTypeItem(currentIndex >= 0 ? currentIndex : 0);
    });
    
    // Open dropdown on click
    noteTypeSearchInput.addEventListener('click', function(e) {
        e.stopPropagation();
        if (noteTypeDropdown.style.display === 'block') {
            noteTypeDropdown.style.display = 'none';
        } else {
            noteTypeDropdown.style.display = 'block';
            const items = getVisibleNoteTypeItems();
            const currentValue = noteTypeHiddenInput.value;
            const currentIndex = items.findIndex(item => item.getAttribute('data-value') === currentValue);
            setActiveNoteTypeItem(currentIndex >= 0 ? currentIndex : 0);
        }
    });
    
    // Click outside to close
    document.addEventListener('click', function(e) {
        if (!noteTypeSearchInput.contains(e.target) && !noteTypeDropdown.contains(e.target)) {
            noteTypeDropdown.style.display = 'none';
        }
    });
    
    // Click on item
    noteTypeDropdown.addEventListener('click', function(e) {
        const item = e.target.closest('.dropdown-item');
        if (item) {
            e.preventDefault();
            e.stopPropagation();
            selectNoteTypeItem(item, true);
        }
    });
    
    // Keyboard navigation
    noteTypeSearchInput.addEventListener('keydown', function(e) {
        console.log('Note type input keydown:', e.key);
        
        // Prevent Tab key
        if (e.key === 'Tab') {
            e.preventDefault();
            console.log('Tab key prevented on note type');
            return false;
        }
        
        if (e.key === 'Enter') {
            console.log('Enter key pressed on note type input');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const items = getVisibleNoteTypeItems();
            console.log('Items:', items.length, 'Active index:', noteTypeActiveIndex);
            
            let selected = false;
            if (noteTypeActiveIndex >= 0 && items[noteTypeActiveIndex]) {
                console.log('Selecting active item at index:', noteTypeActiveIndex);
                selected = selectNoteTypeItem(items[noteTypeActiveIndex], true);
            } else if (items.length >= 1) {
                console.log('Selecting first item');
                selected = selectNoteTypeItem(items[0], true);
            }
            
            if (selected) {
                console.log('Selection successful, closing dropdown');
                noteTypeDropdown.style.display = 'none';
            }
            
            return false;
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            const items = getVisibleNoteTypeItems();
            if (!items.length) return;
            if (noteTypeDropdown.style.display !== 'block') {
                noteTypeDropdown.style.display = 'block';
            }
            const nextIndex = noteTypeActiveIndex < 0 ? 0 : Math.min(noteTypeActiveIndex + 1, items.length - 1);
            setActiveNoteTypeItem(nextIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            const items = getVisibleNoteTypeItems();
            if (!items.length) return;
            if (noteTypeDropdown.style.display !== 'block') {
                noteTypeDropdown.style.display = 'block';
            }
            const prevIndex = noteTypeActiveIndex <= 0 ? 0 : noteTypeActiveIndex - 1;
            setActiveNoteTypeItem(prevIndex);
        } else if (e.key === 'Escape') {
            e.preventDefault();
            noteTypeDropdown.style.display = 'none';
        }
    }, true);
    
    // Global capture to ensure dropdown selection works
    window.addEventListener('keydown', function(e) {
        const activeEl = document.activeElement;
        const isNoteTypeFocus = activeEl === noteTypeSearchInput || noteTypeDropdown.contains(activeEl);
        const isDropdownOpen = noteTypeDropdown.style.display === 'block';
        
        if (!isNoteTypeFocus || !isDropdownOpen) return;

        if (e.key === 'Enter') {
            console.log('Global Enter handler triggered for note type dropdown');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const items = getVisibleNoteTypeItems();
            console.log('Global handler - Items:', items.length, 'Active index:', noteTypeActiveIndex);
            
            let selected = false;
            if (noteTypeActiveIndex >= 0 && items[noteTypeActiveIndex]) {
                console.log('Global handler - Selecting active item');
                selected = selectNoteTypeItem(items[noteTypeActiveIndex], true);
            } else if (items.length >= 1) {
                console.log('Global handler - Selecting first item');
                selected = selectNoteTypeItem(items[0], true);
            }
            
            if (selected) {
                console.log('Global handler - Selection successful');
                noteTypeDropdown.style.display = 'none';
            }
            
            return false;
        }
    }, true);
}

// Initialize Header Field Navigation
function initHeaderFieldNavigation() {
    console.log('initHeaderFieldNavigation called');
    
    // Prevent Tab key navigation on all header fields (but allow Enter key)
    const headerFields = [
        'transaction_date',
        'supplierSearchInput',
        'noteTypeSearchInput',
        'tax_flag',
        'inc_flag',
        'gst_vno'
    ];
    
    headerFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('keydown', function(e) {
                // Only prevent Tab, NOT Enter
                if (e.key === 'Tab') {
                    e.preventDefault();
                    console.log('Tab key prevented on', fieldId);
                }
                // Let Enter key pass through to field-specific handlers
            });
        }
    });
    
    // Tax Flag (Y/N) field
    const taxFlagField = document.getElementById('tax_flag');
    if (taxFlagField) {
        taxFlagField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                console.log('Enter pressed on tax_flag, moving to inc_flag');
                
                const incFlagField = document.getElementById('inc_flag');
                if (incFlagField) {
                    incFlagField.focus();
                    incFlagField.select();
                    console.log('✓ Focus moved to inc_flag');
                }
            }
        });
    }
    
    // Inc field
    const incFlagField = document.getElementById('inc_flag');
    if (incFlagField) {
        incFlagField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                console.log('Enter pressed on inc_flag, moving to gst_vno');
                
                const gstVnoField = document.getElementById('gst_vno');
                if (gstVnoField) {
                    gstVnoField.focus();
                    gstVnoField.select();
                    console.log('✓ Focus moved to gst_vno');
                }
            }
        });
    }
    
    // GST Vno field - Enter key triggers Add Item (F2) modal
    // NOTE: The primary handler is the INLINE onkeydown on the HTML element itself.
    // This addEventListener is a backup in case the inline handler is ever removed.
    const gstVnoField = document.getElementById('gst_vno');
    
    if (gstVnoField) {
        gstVnoField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                console.log('🔥 GST VNO ENTER (JS backup handler)');
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                handleGstVnoEnter();
                return false;
            }
        }, true); // Capture phase
        console.log('✅ GST Vno Enter handler attached (inline + JS backup)');
    }
    
    // Date field
    const dateField = document.getElementById('transaction_date');
    if (dateField) {
        dateField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                console.log('Enter pressed on date, moving to supplier');
                
                const supplierField = document.getElementById('supplierSearchInput');
                if (supplierField) {
                    supplierField.focus();
                    supplierField.select();
                    console.log('✓ Focus moved to supplier');
                }
            }
        });
    }
}

// Move to Next Field in Row
function moveToNextField(rowIndex, nextFieldName) {
    const row = document.getElementById(`row_${rowIndex}`);
    if (!row) return;
    
    // Special case for br_ex - focus the display input and open dropdown
    if (nextFieldName === 'br_ex') {
        const brExDisplay = document.getElementById(`br_ex_display_${rowIndex}`);
        if (brExDisplay) {
            setTimeout(() => {
                brExDisplay.focus();
                openBrExDropdown(rowIndex); // Explicitly open dropdown
            }, 50);
        }
        return;
    }
    
    const nextField = row.querySelector(`[name*="[${nextFieldName}]"]`);
    if (nextField) {
        setTimeout(() => {
            nextField.focus();
            if (nextField.tagName === 'INPUT' && nextField.type === 'number') {
                nextField.select();
            }
        }, 50);
    }
}

// Move to Previous Field in Row (Shift+Enter)
function moveToPrevField(rowIndex, prevFieldName) {
    const row = document.getElementById(`row_${rowIndex}`);
    if (!row) return;
    
    // Special case for br_ex - focus the display input and open dropdown
    if (prevFieldName === 'br_ex') {
        const brExDisplay = document.getElementById(`br_ex_display_${rowIndex}`);
        if (brExDisplay) {
            setTimeout(() => {
                brExDisplay.focus();
                openBrExDropdown(rowIndex); // Explicitly open dropdown
            }, 50);
        }
        return;
    }
    
    const prevField = row.querySelector(`[name*="[${prevFieldName}]"]`);
    if (prevField) {
        setTimeout(() => {
            prevField.focus();
            if (prevField.tagName === 'INPUT' && prevField.type === 'number') {
                prevField.select();
            }
        }, 50);
    }
}

// ============================================
// Br/Ex Custom Dropdown Functions
// ============================================

function openBrExDropdown(idx) {
    closeAllBrExDropdowns();
    const dropdown = document.getElementById(`br_ex_dropdown_${idx}`);
    if (dropdown) {
        dropdown.style.display = 'block';
        const currentVal = document.getElementById(`br_ex_${idx}`).value;
        dropdown.querySelectorAll('.br-ex-option').forEach(opt => {
            if (opt.dataset.value === currentVal) {
                opt.style.backgroundColor = '#0d6efd';
                opt.style.color = '#fff';
            } else {
                opt.style.backgroundColor = '';
                opt.style.color = '';
            }
        });
    }
}

function closeBrExDropdown(idx) {
    const dropdown = document.getElementById(`br_ex_dropdown_${idx}`);
    if (dropdown) dropdown.style.display = 'none';
}

function closeAllBrExDropdowns() {
    document.querySelectorAll('.br-ex-dropdown').forEach(d => d.style.display = 'none');
}

function selectBrEx(idx, value, label) {
    document.getElementById(`br_ex_${idx}`).value = value;
    document.getElementById(`br_ex_display_${idx}`).value = label;
    closeBrExDropdown(idx);
    handleRowComplete(idx);
}

function handleBrExKeyDown(event, idx) {
    const dropdown = document.getElementById(`br_ex_dropdown_${idx}`);
    const isOpen = dropdown && dropdown.style.display !== 'none';
    
    // Block spacebar - dropdown opens only via focus, not spacebar
    if (event.key === ' ' || event.key === 'Spacebar') {
        event.preventDefault();
        event.stopPropagation();
        return;
    }
    
    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();
        event.stopPropagation();
        const currentVal = document.getElementById(`br_ex_${idx}`).value;
        if (currentVal === 'B') {
            document.getElementById(`br_ex_${idx}`).value = 'E';
            document.getElementById(`br_ex_display_${idx}`).value = 'Exp';
        } else {
            document.getElementById(`br_ex_${idx}`).value = 'B';
            document.getElementById(`br_ex_display_${idx}`).value = 'Brk';
        }
        if (isOpen) openBrExDropdown(idx);
        return;
    }
    
    if (event.key === 'Enter') {
        event.preventDefault();
        event.stopPropagation();
        closeBrExDropdown(idx);
        if (event.shiftKey) {
            // Shift+Enter: go back to scm_percent
            moveToPrevField(idx, 'scm_percent');
        } else {
            handleRowComplete(idx);
        }
        return;
    }
    
    if (event.key === 'Escape') {
        event.preventDefault();
        closeBrExDropdown(idx);
        return;
    }
    
    if (event.key.toLowerCase() === 'b') {
        event.preventDefault();
        document.getElementById(`br_ex_${idx}`).value = 'B';
        document.getElementById(`br_ex_display_${idx}`).value = 'Brk';
        if (isOpen) openBrExDropdown(idx);
        return;
    }
    if (event.key.toLowerCase() === 'e') {
        event.preventDefault();
        document.getElementById(`br_ex_${idx}`).value = 'E';
        document.getElementById(`br_ex_display_${idx}`).value = 'Exp';
        if (isOpen) openBrExDropdown(idx);
        return;
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.br-ex-input') && !e.target.closest('.br-ex-dropdown')) {
        closeAllBrExDropdowns();
    }
});

// Handle Grid Enter Key Navigation
function handleGridEnterKey(event, rowIndex, currentField) {
    if (event.key !== 'Enter') return;
    
    event.preventDefault();
    event.stopPropagation();
    
    // Define field navigation order
    const fieldOrder = [
        'item_code',
        'item_name', 
        'batch_no',
        'expiry',
        'qty',
        'free_qty',
        'rate',
        'dis_percent',
        'scm_percent',
        'br_ex'
    ];
    
    const currentIndex = fieldOrder.indexOf(currentField);
    
    // Shift+Enter: move to PREVIOUS field
    if (event.shiftKey) {
        if (currentIndex > 0) {
            const prevFieldName = fieldOrder[currentIndex - 1];
            moveToPrevField(rowIndex, prevFieldName);
        }
        return;
    }
    
    // Special handling for item_code - open item modal
    if (currentField === 'item_code') {
        showItemModal();
        return;
    }
    
    // Special handling for scm_percent - complete row and open item modal for next item
    if (currentField === 'scm_percent') {
        calculateRowAmount(rowIndex);
        calculateTotals();
        setTimeout(() => {
            showItemModal();
        }, 100);
        return;
    }
    
    // If it's the last field (br_ex), complete the row and add new one
    if (currentField === 'br_ex') {
        handleRowComplete(rowIndex);
        return;
    }
    
    // Move to next field in the order
    if (currentIndex >= 0 && currentIndex < fieldOrder.length - 1) {
        const nextFieldName = fieldOrder[currentIndex + 1];
        moveToNextField(rowIndex, nextFieldName);
    }
}

// Handle Row Complete - Called when Enter is pressed on last field (Br/Ex)
function handleRowComplete(rowIndex) {
    console.log('Row completed:', rowIndex);
    const row = document.getElementById(`row_${rowIndex}`);
    if (!row) return;
    
    // Calculate the row amount
    calculateRowAmount(rowIndex);
    
    // Recalculate totals
    calculateTotals();
    
    // Add new row and focus on first field
    setTimeout(() => {
        showItemModal();
    }, 100);
}

// Initialize Global Keyboard Shortcuts
function initGlobalKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // F2 - Add Item
        if (e.key === 'F2') {
            e.preventDefault();
            showItemModal();
        }
        
        // Escape - Close Modals
        if (e.key === 'Escape') {
            closeItemModal();
            closeBatchModal();
        }
        
        // Ctrl+S - Save Transaction
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            e.stopPropagation();
            saveTransaction();
        }
    });
}

// Update supplier name (legacy function for compatibility)
function updateSupplierName() {
    const supplierIdInput = document.getElementById('supplier_id');
    const supplierSearchInput = document.getElementById('supplierSearchInput');
    if (supplierIdInput && supplierIdInput.value) {
        const item = document.querySelector(`#supplierList .dropdown-item[data-id="${supplierIdInput.value}"]`);
        if (item && supplierSearchInput) {
            supplierSearchInput.value = item.getAttribute('data-name');
            document.getElementById('supplier_name').value = item.getAttribute('data-name');
        }
    }
}
</script>
@endpush
