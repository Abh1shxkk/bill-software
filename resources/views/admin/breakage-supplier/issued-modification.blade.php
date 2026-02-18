@extends('layouts.admin')

@section('title', 'Breakage/Expiry to Supplier - Issued Modification')
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
    
    /* Table Styles - Brown Header */
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
    .modal-header-custom.invoice { background: #17a2b8; color: #fff; }
    .modal-body-custom { padding: 12px; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 8px 12px; border-top: 1px solid #ddd; text-align: right; }
    .item-row:hover, .batch-row:hover, .invoice-row:hover { background: #e3f2fd !important; cursor: pointer; }
    .item-row.selected, .batch-row.selected, .invoice-row.selected { background: #007bff !important; color: #fff !important; }
    .invoice-row.table-active { background: #cce5ff !important; font-weight: bold; }
    
    /* Action Buttons */
    .action-buttons { display: flex; gap: 8px; justify-content: center; margin-top: 10px; }
    .action-buttons .btn { min-width: 100px; }
</style>
@endpush

@section('content')
<div class="bsi-form">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0"><i class="bi bi-pencil-square me-1"></i> Breakage/Expiry to Supplier - Issued Modification</h6>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-info btn-sm text-white py-0" id="btn_load_invoice" onclick="showLoadInvoiceModal()"><i class="bi bi-folder-open me-1"></i> Load Invoice</button>
            <a href="{{ route('admin.breakage-supplier.issued-index') }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-list"></i> View All</a>
        </div>
    </div>

    <form id="bsiForm" autocomplete="off">
        @csrf
        <input type="hidden" id="transaction_id" name="transaction_id">
        
        <!-- Header Section -->
        <div class="header-section">
            <div class="row g-2">
                <div class="col-md-2">
                    <div class="field-group"><label style="width:40px;">Date:</label><input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="updateDayName()"></div>
                    <div class="field-group"><label style="width:40px;"></label><input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" value="{{ date('l') }}" readonly style="width:85px;"></div>
                    <div class="field-group"><label style="width:40px;">Trn.No:</label><input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" readonly style="width:60px;"></div>
                </div>
                <div class="col-md-10">
                    <div class="inner-card">
                        <div class="row g-2">
                            <div class="col-md-5"><div class="field-group"><label style="width:55px;">Supplier:</label><select id="supplier_id" name="supplier_id" class="form-control no-select2" onchange="updateSupplierName()"><option value="">Select Supplier</option>@foreach($suppliers ?? [] as $s)<option value="{{ $s->supplier_id }}" data-name="{{ $s->name }}">{{ $s->name }}</option>@endforeach</select></div></div>
                            <div class="col-md-3"><div class="field-group"><label>R(epl)/C(redit):</label><select id="note_type" name="note_type" class="form-control no-select2" style="width:50px;"><option value="C">C</option><option value="R">R</option></select></div></div>
                            <div class="col-md-4"><div class="field-group"><label>Tax[Y/N]:</label><input type="text" id="tax_flag" name="tax_flag" class="form-control text-center" value="N" maxlength="1" style="width:30px;"><label class="ms-2">Inc.</label><input type="text" id="inc_flag" name="inc_flag" class="form-control text-center" value="N" maxlength="1" style="width:30px;"></div></div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-md-3"><div class="field-group"><label>GST Vno.:</label><input type="text" id="gst_vno" name="gst_vno" class="form-control"></div></div>
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
            <div class="table-responsive" style="max-height: 280px; overflow-y: auto; overflow-x: auto;">
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
            <button type="button" class="btn btn-success btn-sm" onclick="updateTransaction()" id="updateBtn" disabled><i class="bi bi-check-lg me-1"></i> Update</button>
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedItem()"><i class="bi bi-trash me-1"></i> Remove Item</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelModification()"><i class="bi bi-x-lg me-1"></i> Cancel</button>
        </div>
    </form>
</div>

<!-- Load Invoice Modal -->
<div class="modal-backdrop-custom" id="invoiceModalBackdrop" onclick="closeInvoiceModal()"></div>
<div class="custom-modal" id="invoiceModal">
    <div class="modal-header-custom invoice">
        <h6 class="mb-0"><i class="bi bi-folder-open me-1"></i> Load Invoice</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeInvoiceModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="invoiceSearchInput" class="form-control form-control-sm mb-2" placeholder="Search by Trn No or Supplier..." onkeyup="searchInvoices()">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-light sticky-top"><tr><th>Trn No</th><th>Date</th><th>Supplier</th><th class="text-end">Amount</th></tr></thead>
                <tbody id="invoicesListBody"><tr><td colspan="4" class="text-center py-3">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoiceModal()">Close</button>
    </div>
</div>

<!-- Item Selection Modal -->
<div class="modal-backdrop-custom" id="itemModalBackdrop" onclick="_legacy_closeItemModal()"></div>
<div class="custom-modal" id="itemModal">
    <div class="modal-header-custom">
        <h6 class="mb-0"><i class="bi bi-search me-1"></i> Select Item</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="_legacy_closeItemModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="itemSearchInput" class="form-control form-control-sm mb-2" placeholder="Search by code or name..." onkeyup="_legacy_filterItems()">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-light sticky-top"><tr><th>Code</th><th>Item Name</th><th>Pack</th><th>Company</th></tr></thead>
                <tbody id="itemsListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeItemModal()">Close</button>
    </div>
</div>

<!-- Batch Selection Modal -->
<div class="modal-backdrop-custom" id="batchModalBackdrop" onclick="_legacy_closeBatchModal()"></div>
<div class="custom-modal" id="batchModal">
    <div class="modal-header-custom batch">
        <h6 class="mb-0"><i class="bi bi-box me-1"></i> Select Batch</h6>
        <button type="button" class="btn btn-sm btn-dark" onclick="_legacy_closeBatchModal()">&times;</button>
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
        <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeBatchModal()">Close</button>
    </div>
</div>
@endsection

<!-- Reusable Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'breakageSupplierIssuedModItemModal',
    'module' => 'breakage-supplier',
    'showStock' => true,
    'rateType' => 'p_rate',
    'showCompany' => true,
    'showHsn' => true,
    'batchModalId' => 'breakageSupplierIssuedModBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'breakageSupplierIssuedModBatchModal',
    'module' => 'breakage-supplier',
    'showOnlyAvailable' => true,
    'rateType' => 'p_rate',
    'showCostDetails' => true,
])

@push('scripts')
<script>
let rowIndex = 0, allItems = [], selectedRowIndex = null, selectedItem = null, currentTransactionId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    
    
    // Auto-focus the date field on page load (AFTER all initializations)
    setTimeout(function() {
        const dateField = document.getElementById('transaction_date');
        if (dateField) dateField.focus();
    }, 1200);
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') { e.preventDefault(); showItemModal(); }
        if (e.key === 'Escape') { _legacy_closeItemModal(); _legacy_closeBatchModal(); closeInvoiceModal(); }
    });
    
    // ==========================================
    // LOAD INVOICE MODAL KEYBOARD NAVIGATION
    // Arrow Up/Down, Enter selection, Esc close, highlight
    // ==========================================
    let invoiceSelectedIndex = -1;
    
    // Expose setter for loadInvoices callback
    window.setInvoiceHighlight = function(idx) {
        invoiceSelectedIndex = idx;
    };
    window.highlightInvoiceRow = function(rows) {
        // Remove all highlights
        rows.forEach(function(row) {
            row.style.backgroundColor = '';
            row.style.fontWeight = '';
            row.classList.remove('table-active');
        });
        // Add highlight to selected row
        if (invoiceSelectedIndex >= 0 && invoiceSelectedIndex < rows.length) {
            rows[invoiceSelectedIndex].style.backgroundColor = '#cce5ff';
            rows[invoiceSelectedIndex].style.fontWeight = 'bold';
            rows[invoiceSelectedIndex].classList.add('table-active');
            rows[invoiceSelectedIndex].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    };
    
    // Capture-phase keydown for invoice modal navigation
    document.addEventListener('keydown', function(e) {
        const invoiceModal = document.getElementById('invoiceModal');
        if (!invoiceModal || !invoiceModal.classList.contains('show')) return;
        
        const tbody = document.getElementById('invoicesListBody');
        const rows = Array.from(tbody.querySelectorAll('tr.invoice-row'));
        
        // Arrow Down → next row
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Blur search input so arrows don't move text cursor
            const searchInput = document.getElementById('invoiceSearchInput');
            if (document.activeElement === searchInput) {
                searchInput.blur();
            }
            
            if (!rows.length) return;
            
            if (invoiceSelectedIndex < rows.length - 1) {
                invoiceSelectedIndex++;
            } else if (invoiceSelectedIndex === -1) {
                invoiceSelectedIndex = 0; // Auto-select first row
            }
            window.highlightInvoiceRow(rows);
            return;
        }
        
        // Arrow Up → previous row or back to search
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (!rows.length) return;
            
            if (invoiceSelectedIndex > 0) {
                invoiceSelectedIndex--;
                window.highlightInvoiceRow(rows);
            } else if (invoiceSelectedIndex === 0) {
                // On first row → move focus back to search input
                invoiceSelectedIndex = -1;
                window.highlightInvoiceRow(rows); // Remove all highlights
                const searchInput = document.getElementById('invoiceSearchInput');
                if (searchInput) searchInput.focus();
            } else if (invoiceSelectedIndex === -1 && rows.length > 0) {
                invoiceSelectedIndex = 0; // Auto-select first row
                window.highlightInvoiceRow(rows);
            }
            return;
        }
        
        // Enter → select highlighted row and load invoice
        if (e.key === 'Enter') {
            // If search input is focused and no row selected, let Enter re-filter
            const searchInput = document.getElementById('invoiceSearchInput');
            if (document.activeElement === searchInput && invoiceSelectedIndex === -1) {
                return; // Let search work normally
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (!rows.length) return;
            
            // If no row selected yet, select first one
            if (invoiceSelectedIndex === -1 && rows.length > 0) {
                invoiceSelectedIndex = 0;
                window.highlightInvoiceRow(rows);
                return;
            }
            
            // Load the selected invoice
            if (invoiceSelectedIndex >= 0 && invoiceSelectedIndex < rows.length) {
                rows[invoiceSelectedIndex].click();
            }
            return;
        }
        
        // Escape → close modal
        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            closeInvoiceModal();
            return;
        }
        
        // Any letter/number key → refocus search input for typing
        if (e.key.length === 1 && !e.ctrlKey && !e.altKey) {
            const searchInput = document.getElementById('invoiceSearchInput');
            if (searchInput && document.activeElement !== searchInput) {
                searchInput.focus();
                // Reset row selection when user starts typing
                invoiceSelectedIndex = -1;
                window.highlightInvoiceRow(rows);
            }
        }
    }, true); // Capture phase to beat other handlers
    
    // Reset invoice selection when modal opens/closes via MutationObserver
    const invoiceModalEl = document.getElementById('invoiceModal');
    if (invoiceModalEl) {
        const invoiceObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (invoiceModalEl.classList.contains('show')) {
                        // Modal opened - reset selection and focus search
                        invoiceSelectedIndex = -1;
                        setTimeout(function() {
                            const searchInput = document.getElementById('invoiceSearchInput');
                            if (searchInput) searchInput.focus();
                        }, 150);
                    } else {
                        // Modal closed - reset selection
                        invoiceSelectedIndex = -1;
                    }
                }
            });
        });
        invoiceObserver.observe(invoiceModalEl, { attributes: true, attributeFilter: ['class'] });
    }
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

// Invoice Modal
function showLoadInvoiceModal() {
    document.getElementById('invoiceModalBackdrop').classList.add('show');
    document.getElementById('invoiceModal').classList.add('show');
    document.getElementById('invoiceSearchInput').value = '';
    if (typeof window.setInvoiceHighlight === 'function') {
        window.setInvoiceHighlight(-1); // Reset highlight
    }
    setTimeout(() => document.getElementById('invoiceSearchInput').focus(), 100);
    loadInvoices();
}

function closeInvoiceModal() {
    document.getElementById('invoiceModalBackdrop').classList.remove('show');
    document.getElementById('invoiceModal').classList.remove('show');
}

function loadInvoices(search = '') {
    fetch(`{{ route('admin.breakage-supplier.get-issued-past-invoices') }}?search=${encodeURIComponent(search)}`)
        .then(r => r.json())
        .then(invoices => {
            const tbody = document.getElementById('invoicesListBody');
            if (!invoices.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">No invoices found</td></tr>';
                return;
            }
            tbody.innerHTML = invoices.map(inv => `
                <tr class="invoice-row" onclick="loadInvoice(${inv.id})">
                    <td><strong>${inv.trn_no}</strong></td>
                    <td>${inv.transaction_date ? new Date(inv.transaction_date).toLocaleDateString() : '-'}</td>
                    <td>${inv.supplier_name || '-'}</td>
                    <td class="text-end">${parseFloat(inv.total_inv_amt || 0).toFixed(2)}</td>
                </tr>
            `).join('');

            // Reset highlight and refocus search after invoices load
            if (typeof window.setInvoiceHighlight === 'function') {
                window.setInvoiceHighlight(-1); // Start with no selection
                setTimeout(() => {
                    const allRows = document.querySelectorAll('#invoicesListBody .invoice-row');
                    window.highlightInvoiceRow(Array.from(allRows));
                    document.getElementById('invoiceSearchInput').focus();
                }, 50);
            } else {
                setTimeout(() => document.getElementById('invoiceSearchInput').focus(), 50);
            }
        })
        .catch(() => {
            document.getElementById('invoicesListBody').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Error loading invoices</td></tr>';
        });
}

function searchInvoices() {
    loadInvoices(document.getElementById('invoiceSearchInput').value);
}

function loadInvoice(id) {
    console.log('Loading invoice ID:', id);
    fetch(`{{ url('admin/breakage-supplier/issued') }}/${id}`)
        .then(r => {
            console.log('Response status:', r.status);
            return r.json();
        })
        .then(data => {
            console.log('Received data:', data);
            populateForm(data);
            closeInvoiceModal();
        })
        .catch(e => {
            console.error('Error loading invoice:', e);
            alert('Error loading invoice: ' + e.message);
        });
}

function populateForm(data) {
    console.log('Populating form with data:', data);
    currentTransactionId = data.id;
    document.getElementById('transaction_id').value = data.id;
    document.getElementById('trn_no').value = data.trn_no || '';
    document.getElementById('transaction_date').value = data.transaction_date ? data.transaction_date.split('T')[0] : '';
    updateDayName();
    document.getElementById('supplier_id').value = data.supplier_id || '';
    updateSupplierName();
    document.getElementById('note_type').value = data.note_type || 'C';
    document.getElementById('tax_flag').value = data.tax_flag || 'N';
    document.getElementById('inc_flag').value = data.inc_flag || 'N';
    document.getElementById('gst_vno').value = data.gst_vno || '';
    
    // Update counts
    document.getElementById('dis_count').value = data.dis_count || 0;
    document.getElementById('rpl_count').value = data.rpl_count || 0;
    document.getElementById('brk_count').value = data.brk_count || 0;
    document.getElementById('exp_count').value = data.exp_count || 0;
    
    // Clear and populate items
    document.getElementById('itemsTableBody').innerHTML = '';
    rowIndex = 0;
    
    if (data.items && data.items.length) {
        console.log('Loading items:', data.items.length);
        data.items.forEach(item => {
            addItemRowFromData(item);
        });
    } else {
        console.warn('No items found in transaction data');
    }
    
    calculateTotals();
    document.getElementById('updateBtn').disabled = false;
    console.log('Form populated successfully');

    // Auto-focus Qty field of first row after invoice load
    setTimeout(() => {
        const firstRow = document.querySelector('#itemsTableBody tr');
        if (firstRow) {
            const qtyInput = firstRow.querySelector('input[name*="[qty]"]');
            if (qtyInput) {
                qtyInput.focus();
                qtyInput.select();
                console.log('Auto-focused Qty field of first row');
            }
        }
    }, 200);
}

function addItemRowFromData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const idx = rowIndex++;
    
    console.log('Loading item data:', item);
    console.log('CGST:', item.cgst_percent, 'SGST:', item.sgst_percent, 'HSN:', item.hsn_code);
    
    // Get CGST/SGST from item or item.item (nested relationship)
    const cgstValue = item.cgst_percent || (item.item ? item.item.cgst_percent : 0) || 0;
    const sgstValue = item.sgst_percent || (item.item ? item.item.sgst_percent : 0) || 0;
    const hsnValue = item.hsn_code || (item.item ? item.item.hsn_code : '') || '';
    const packingValue = item.packing || (item.item ? item.item.packing : '') || '';
    const unitValue = item.unit || (item.item ? item.item.unit : '') || '';
    const companyValue = item.company_name || (item.item ? item.item.company_short_name : '') || '';
    
    console.log('Final values - CGST:', cgstValue, 'SGST:', sgstValue, 'HSN:', hsnValue);
    
    // Map br_ex_type from database to br_ex for form
    let brExValue = 'B'; // default
    if (item.br_ex_type) {
        if (item.br_ex_type === 'EXPIRY' || item.br_ex_type === 'E') brExValue = 'E';
        else if (item.br_ex_type === 'BREAKAGE' || item.br_ex_type === 'B') brExValue = 'B';
    } else if (item.br_ex) {
        brExValue = item.br_ex;
    }
    
    const tr = document.createElement('tr');
    tr.id = `row_${idx}`;
    tr.onclick = function() { selectRow(idx); };
    tr.innerHTML = `
        <td><input type="text" name="items[${idx}][item_code]" value="${item.item_code || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][item_name]" value="${item.item_name || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][batch_no]" value="${item.batch_no || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][expiry]" value="${item.expiry || item.expiry_date || ''}" readonly class="readonly-field"></td>
        <td><input type="number" name="items[${idx}][qty]" value="${item.qty || ''}" min="0" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][free_qty]" value="${item.free_qty || 0}" min="0" class="text-end"></td>
        <td><input type="number" name="items[${idx}][rate]" value="${parseFloat(item.rate || 0).toFixed(2)}" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][dis_percent]" value="${item.dis_percent || 0}" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][scm_percent]" value="${item.scm_percent || 0}" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><select name="items[${idx}][br_ex]" class="form-control br-ex-select no-select2" id="br_ex_${idx}"><option value="B" ${brExValue === 'B' ? 'selected' : ''}>Brk</option><option value="E" ${brExValue === 'E' ? 'selected' : ''}>Exp</option></select></td>
        <td><input type="number" name="items[${idx}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" step="0.01" class="text-end readonly-field" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm py-0 px-1" onclick="removeRow(${idx})">&times;</button></td>
        <input type="hidden" name="items[${idx}][id]" value="${item.id || ''}">
        <input type="hidden" name="items[${idx}][item_id]" value="${item.item_id || ''}">
        <input type="hidden" name="items[${idx}][batch_id]" value="${item.batch_id || ''}">
        <input type="hidden" name="items[${idx}][mrp]" value="${item.mrp || 0}">
        <input type="hidden" name="items[${idx}][purchase_rate]" value="${item.p_rate || item.purchase_rate || 0}">
        <input type="hidden" name="items[${idx}][sale_rate]" value="${item.s_rate || item.sale_rate || 0}">
        <input type="hidden" name="items[${idx}][cgst]" value="${cgstValue}">
        <input type="hidden" name="items[${idx}][sgst]" value="${sgstValue}">
        <input type="hidden" name="items[${idx}][company_name]" value="${companyValue}">
        <input type="hidden" name="items[${idx}][packing]" value="${packingValue}">
        <input type="hidden" name="items[${idx}][unit]" value="${unitValue}">
        <input type="hidden" name="items[${idx}][hsn_code]" value="${hsnValue}">
    `;
    tbody.appendChild(tr);
    
    
    // Select this row to update footer
    selectRow(idx);
}

// Item Modal - Bridge function to use reusable modal
function showItemModal() {
    console.log('showItemModal called - attempting to use reusable modal');
    if (typeof openItemModal_breakageSupplierIssuedModItemModal === 'function') {
        console.log('Using reusable item modal');
        openItemModal_breakageSupplierIssuedModItemModal();
    } else {
        console.log('Fallback to legacy modal');
        _legacy_showItemModal();
    }
}

// Callback function when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('Item selected from modal:', item);
    console.log('Batch selected from modal:', batch);
    
    // Transform item to match expected format
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

// Legacy Item Modal Functions
function _legacy_showItemModal() {
    document.getElementById('itemModalBackdrop').classList.add('show');
    document.getElementById('itemModal').classList.add('show');
    document.getElementById('itemSearchInput').value = '';
    _legacy_renderItemsList(allItems);
    setTimeout(() => document.getElementById('itemSearchInput').focus(), 100);
}

function _legacy_closeItemModal() {
    document.getElementById('itemModalBackdrop').classList.remove('show');
    document.getElementById('itemModal').classList.remove('show');
}

function _legacy_filterItems() {
    const search = document.getElementById('itemSearchInput').value.toLowerCase();
    const filtered = allItems.filter(item => 
        (item.item_code && item.item_code.toLowerCase().includes(search)) ||
        (item.item_name && item.item_name.toLowerCase().includes(search))
    );
    _legacy_renderItemsList(filtered);
}

function _legacy_renderItemsList(items) {
    const tbody = document.getElementById('itemsListBody');
    if (!items.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No items found</td></tr>';
        return;
    }
    tbody.innerHTML = items.slice(0, 100).map(item => `
        <tr class="item-row" onclick="_legacy_selectItem(${item.id})">
            <td>${item.item_code || ''}</td>
            <td>${item.item_name || ''}</td>
            <td>${item.packing || ''}</td>
            <td>${item.company_name || ''}</td>
        </tr>
    `).join('');
}

function _legacy_selectItem(itemId) {
    selectedItem = allItems.find(i => i.id === itemId);
    if (!selectedItem) return;
    _legacy_closeItemModal();
    document.getElementById('selectedItemName').textContent = `${selectedItem.item_code} - ${selectedItem.item_name}`;
    _legacy_loadBatches(itemId);
}

function _legacy_loadBatches(itemId) {
    fetch(`{{ url('admin/breakage-supplier/get-batches') }}/${itemId}`)
        .then(r => r.json())
        .then(batches => {
            document.getElementById('batchModalBackdrop').classList.add('show');
            document.getElementById('batchModal').classList.add('show');
            _legacy_renderBatchesList(batches);
        })
        .catch(e => {
            console.error('Error loading batches:', e);
            addItemRow(selectedItem, null);
        });
}

function _legacy_renderBatchesList(batches) {
    const tbody = document.getElementById('batchesListBody');
    if (!batches.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No batches found</td></tr>';
        return;
    }
    tbody.innerHTML = batches.map(batch => `
        <tr class="batch-row" onclick="_legacy_selectBatch(${JSON.stringify(batch).replace(/"/g, '&quot;')})">
            <td>${batch.batch_no || ''}</td>
            <td>${batch.expiry_date || ''}</td>
            <td class="text-end">${batch.quantity || 0}</td>
            <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.purchase_rate || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.sale_rate || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function _legacy_closeBatchModal() {
    document.getElementById('batchModalBackdrop').classList.remove('show');
    document.getElementById('batchModal').classList.remove('show');
}

function _legacy_selectBatch(batch) {
    _legacy_closeBatchModal();
    addItemRow(selectedItem, batch);
}

function addItemRow(item, batch) {
    const tbody = document.getElementById('itemsTableBody');
    const idx = rowIndex++;
    const rate = batch ? parseFloat(batch.purchase_rate || 0) : 0;
    
    const tr = document.createElement('tr');
    tr.id = `row_${idx}`;
    tr.onclick = function() { selectRow(idx); };
    tr.innerHTML = `
        <td><input type="text" name="items[${idx}][item_code]" value="${item.item_code || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][item_name]" value="${item.item_name || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][batch_no]" value="${batch?.batch_no || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][expiry]" value="${batch?.expiry_date || ''}" readonly class="readonly-field"></td>
        <td><input type="number" name="items[${idx}][qty]" value="" min="0" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][free_qty]" value="0" min="0" class="text-end"></td>
        <td><input type="number" name="items[${idx}][rate]" value="${rate.toFixed(2)}" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][dis_percent]" value="0" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][scm_percent]" value="0" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><select name="items[${idx}][br_ex]" class="form-control br-ex-select no-select2" id="br_ex_${idx}"><option value="B">Brk</option><option value="E">Exp</option></select></td>
        <td><input type="number" name="items[${idx}][amount]" value="0" step="0.01" class="text-end readonly-field" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm py-0 px-1" onclick="removeRow(${idx})">&times;</button></td>
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
    `;
    tbody.appendChild(tr);
    

    selectRow(idx);
    calculateTotals();
    
    // Focus Qty field
    setTimeout(() => {
        const qtyInput = tr.querySelector(`input[name="items[${idx}][qty]"]`);
        if (qtyInput) {
            qtyInput.focus();
            qtyInput.select();
        }
    }, 100);
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

function calculateTotals() {
    let totalNtAmt = 0, totalDisAmt = 0, totalScmAmt = 0, totalTax = 0, totalSc = 0;
    let brkCount = 0, expCount = 0, disCount = 0, rplCount = 0;
    
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
        const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
        const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value) || 0;
        const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]')?.value) || 0;
        const brEx = row.querySelector('select[name*="[br_ex]"]')?.value || 'B';
        
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
    
    // Update footer if this is the selected row
    if (selectedRowIndex === idx) {
        updateFooterFromRow(row);
    }
}

// ============================================
// KEYBOARD NAVIGATION SYSTEM
// ============================================
(function() {
    'use strict';

    // -----------------------------------------------
    // Explicit header-field navigation order.
    // Enter key walks through these, then opens the Item Modal.
    // -----------------------------------------------
    const HEADER_FIELD_ORDER = [
        'transaction_date',   // input[type="date"]
        'supplier_id',        // select (native)
        'note_type',          // select (native)
        'tax_flag',           // input[text]
        'inc_flag',           // input[text]
        'gst_vno'             // input[text]  → after this, Enter opens item modal
    ];

    // -----------------------------------------------
    // Focus a field by ID (native selects)
    // -----------------------------------------------
    function focusField(id) {
        const el = document.getElementById(id);
        if (!el) return false;

        el.focus();
        if (el.tagName === 'SELECT') {
            try { el.showPicker(); } catch(e) { /* browser doesn't support showPicker */ }
        } else if (el.tagName === 'INPUT' && el.type !== 'checkbox' && el.type !== 'radio') {
            el.select();
        }
        return true;
    }

    // -----------------------------------------------
    // Table helpers
    // -----------------------------------------------
    function isInItemsTable(el) {
        return el && el.closest('#itemsTableBody') !== null;
    }

    function getTableCellInfo(el) {
        const td = el.closest('td');
        const tr = el.closest('tr');
        if (!td || !tr) return null;
        const tbody = tr.closest('tbody');
        if (!tbody) return null;
        const cells = Array.from(tr.querySelectorAll('td'));
        const rows  = Array.from(tbody.querySelectorAll('tr'));
        return {
            col: cells.indexOf(td),
            row: rows.indexOf(tr),
            totalCols: cells.length,
            totalRows: rows.length
        };
    }

    function focusCell(rowIdx, colIdx) {
        const tbody = document.getElementById('itemsTableBody');
        if (!tbody) return false;
        const rows = tbody.querySelectorAll('tr');
        if (rowIdx < 0 || rowIdx >= rows.length) return false;
        const cells = rows[rowIdx].querySelectorAll('td');
        if (colIdx < 0 || colIdx >= cells.length) return false;
        const input = cells[colIdx].querySelector(
            'input:not([readonly]):not([disabled]):not(.readonly-field), select:not([disabled])'
        );
        if (!input) return false;

        input.focus();
        if (input.tagName === 'SELECT') {
            try { input.showPicker(); } catch(e) { /* browser doesn't support showPicker */ }
        } else if (typeof input.select === 'function' && input.tagName === 'INPUT') {
            input.select();
        }
        document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
        rows[rowIdx].classList.add('row-selected');
        return true;
    }

    function focusNextEditableCell(rowIdx, startCol, totalCols) {
        for (let c = startCol; c < totalCols; c++) {
            if (focusCell(rowIdx, c)) return true;
        }
        return false;
    }

    // -----------------------------------------------
    // Helper: resolve active element ID
    // -----------------------------------------------
    function resolveActiveElement(active) {
        if (active.id && HEADER_FIELD_ORDER.includes(active.id)) {
            return active.id;
        }
        return active.id;
    }

    // -----------------------------------------------
    // ENTER key handler
    // -----------------------------------------------
    function handleEnter(e) {
        let active = document.activeElement;
        
        // Debugging
        console.log('Enter pressed on:', active.tagName, active.id, active.className);
        
        if (active.tagName === 'BUTTON' || active.tagName === 'TEXTAREA') return;


        const direction = e.shiftKey ? -1 : 1;
        
        // DIRECT & EXPLICIT HANDLERS FOR CRITICAL HEADER FIELDS
        // We handle these first to ensure no logic skips them
        if (active.id === 'tax_flag' && direction === 1) {
            console.log('Direct handler: Tax -> Inc');
            e.preventDefault();
            e.stopImmediatePropagation();
            const inc = document.getElementById('inc_flag');
            if (inc) {
                inc.focus();
                setTimeout(() => inc.select(), 10);
            }
            return;
        }

        if (active.id === 'inc_flag' && direction === 1) {
            console.log('Direct handler: Inc -> GST');
            e.preventDefault();
            e.stopImmediatePropagation();
            const gst = document.getElementById('gst_vno');
            if (gst) {
                gst.focus();
                setTimeout(() => gst.select(), 10);
            }
            return;
        }

        // REVERSE NAVIGATION (Shift+Enter)
        if (active.id === 'gst_vno' && direction === -1) {
            console.log('Direct handler: GST -> Inc (Reverse)');
            e.preventDefault();
            e.stopImmediatePropagation();
            const inc = document.getElementById('inc_flag');
            if (inc) {
                inc.focus();
                setTimeout(() => inc.select(), 10);
            }
            return;
        }

        if (active.id === 'inc_flag' && direction === -1) {
            console.log('Direct handler: Inc -> Tax (Reverse)');
            e.preventDefault();
            e.stopImmediatePropagation();
            const tax = document.getElementById('tax_flag');
            if (tax) {
                tax.focus();
                setTimeout(() => tax.select(), 10);
            }
            return;
        }
        
        if (active.id === 'gst_vno' && direction === 1) {
            console.log('Direct handler: GST -> Load Invoice Button');
            e.preventDefault();
            e.stopImmediatePropagation();
            const invBtn = document.getElementById('btn_load_invoice');
            if (invBtn) {
                invBtn.focus();
                // Trigger click after a very short delay to ensure focus is registered
                setTimeout(() => invBtn.click(), 50);
            }
            return;
        }

        // Standard logic for other fields
        const currentId = resolveActiveElement(active);
        const headerIdx = HEADER_FIELD_ORDER.indexOf(currentId);

        if (headerIdx !== -1) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const nextIdx = headerIdx + direction;

            if (nextIdx < 0) return;

            if (nextIdx >= HEADER_FIELD_ORDER.length) {
                showItemModal();
                return;
            }

            focusField(HEADER_FIELD_ORDER[nextIdx]);
            return;
        }

        // ----- Items table navigation -----
        if (isInItemsTable(active)) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const info = getTableCellInfo(active);
            if (!info) return;

            if (direction === 1) {
                if (focusNextEditableCell(info.row, info.col + 1, info.totalCols)) return;
                if (info.row < info.totalRows - 1) {
                    if (focusNextEditableCell(info.row + 1, 0, info.totalCols)) return;
                }
                showItemModal();
            } else {
                for (let c = info.col - 1; c >= 0; c--) {
                    if (focusCell(info.row, c)) return;
                }
                if (info.row > 0) {
                     for (let c = info.totalCols - 1; c >= 0; c--) {
                        if (focusCell(info.row - 1, c)) return;
                    }
                }
                focusField(HEADER_FIELD_ORDER[HEADER_FIELD_ORDER.length - 1]);
            }
            return;
        }
    }


    // -----------------------------------------------
    // ARROW key handler (table only)
    // -----------------------------------------------
    function handleArrows(e) {
        const active = document.activeElement;
        if (!isInItemsTable(active)) return;
        
        const info = getTableCellInfo(active);
        if (!info) return;

        let handled = false;
        switch (e.key) {
            case 'ArrowDown':
                if (info.row < info.totalRows - 1) handled = focusCell(info.row + 1, info.col);
                break;
            case 'ArrowUp':
                if (info.row > 0) handled = focusCell(info.row - 1, info.col);
                break;
            case 'ArrowRight':
                if (active.tagName === 'INPUT' && active.selectionStart === active.value.length) {
                    for (let c = info.col + 1; c < info.totalCols; c++) {
                        if (focusCell(info.row, c)) { handled = true; break; }
                    }
                }
                break;
            case 'ArrowLeft':
                if (active.tagName === 'INPUT' && active.selectionStart === 0) {
                    for (let c = info.col - 1; c >= 0; c--) {
                        if (focusCell(info.row, c)) { handled = true; break; }
                    }
                }
                break;
        }
        
        if (handled) {
            e.preventDefault();
            console.log('Table navigation handled:', e.key, 'Row:', info.row, 'Col:', info.col);
        }
    }

    // -----------------------------------------------
    // Modal keyboard navigation (Item & Batch only)
    // Invoice modal is handled by the DOMContentLoaded capture-phase handler
    // -----------------------------------------------
    let itemHighlight = -1;
    let batchHighlight = -1;

    function handleItemModalKeys(e) {
        const rows = document.querySelectorAll('#itemsListBody .item-row');
        if (!rows.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            itemHighlight = (itemHighlight + 1) % rows.length;
            rows.forEach(r => r.classList.remove('selected'));
            rows[itemHighlight].classList.add('selected');
            rows[itemHighlight].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            itemHighlight = itemHighlight <= 0 ? rows.length - 1 : itemHighlight - 1;
            rows.forEach(r => r.classList.remove('selected'));
            rows[itemHighlight].classList.add('selected');
            rows[itemHighlight].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (document.activeElement.id === 'itemSearchInput') {
                const highlighted = document.querySelector('#itemsListBody .item-row.selected');
                if (highlighted) highlighted.click();
                else if (rows[0]) rows[0].click();
            } else if (itemHighlight >= 0 && itemHighlight < rows.length) {
                rows[itemHighlight].click();
            }
        }
    }

    function handleBatchModalKeys(e) {
        const rows = document.querySelectorAll('#batchesListBody .batch-row');
        if (!rows.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            batchHighlight = (batchHighlight + 1) % rows.length;
            rows.forEach(r => r.classList.remove('selected'));
            rows[batchHighlight].classList.add('selected');
            rows[batchHighlight].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            batchHighlight = batchHighlight <= 0 ? rows.length - 1 : batchHighlight - 1;
            rows.forEach(r => r.classList.remove('selected'));
            rows[batchHighlight].classList.add('selected');
            rows[batchHighlight].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (batchHighlight >= 0 && batchHighlight < rows.length) {
                rows[batchHighlight].click();
            } else if (rows[0]) {
                rows[0].click();
            }
        }
    }
    
    // -----------------------------------------------
    // GLOBAL TRACKERS
    // -----------------------------------------------
    let isShiftHeld = false;
    document.addEventListener('keydown', e => { if (e.key === 'Shift') isShiftHeld = true; });
    document.addEventListener('keyup', e => { if (e.key === 'Shift') isShiftHeld = false; });

    // -----------------------------------------------
    // MAIN KEYDOWN LISTENER (capture phase)
    // -----------------------------------------------
    document.addEventListener('keydown', function(e) {
        // --- TOP PRIORITY: Ctrl+S → Save/Update Transaction ---
        if (e.key === 's' && e.ctrlKey) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (typeof updateTransaction === 'function') {
                updateTransaction();
            }
            return;
        }

        // --- PRIORITY 1: Modal handling (highest priority) ---
        const itemModal    = document.getElementById('itemModal');
        const batchModal   = document.getElementById('batchModal');
        const invoiceModal = document.getElementById('invoiceModal');

        // Invoice modal keyboard navigation is handled by the
        // DOMContentLoaded capture-phase handler — skip here entirely
        if (invoiceModal && invoiceModal.classList.contains('show')) {
            return;
        }

        if (itemModal && itemModal.classList.contains('show')) {
            handleItemModalKeys(e);
            if (e.key === 'Escape') { e.preventDefault(); _legacy_closeItemModal(); itemHighlight = -1; }
            return;
        }
        if (batchModal && batchModal.classList.contains('show')) {
            handleBatchModalKeys(e);
            if (e.key === 'Escape') { e.preventDefault(); _legacy_closeBatchModal(); batchHighlight = -1; }
            return;
        }

        // --- PRIORITY 2: Main form handling ---
        switch (e.key) {
            case 'Enter':
                handleEnter(e);
                break;
            case 'ArrowDown':
            case 'ArrowUp':
            case 'ArrowLeft':
            case 'ArrowRight':
                handleArrows(e);
                break;
            case 'F2':
                e.preventDefault();
                showItemModal();
                break;
        }
    }, true);

    // Visual focus styles
    const style = document.createElement('style');
    style.textContent = `
        .bsi-form input:focus, .bsi-form select:focus {
            outline: 2px solid #0d6efd !important;
            outline-offset: 1px;
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
        }
        #itemsTableBody tr:focus-within td { background: #e7f3ff !important; }
        #itemsListBody .item-row.selected,
        #batchesListBody .batch-row.selected {
            background: #007bff !important;
            color: #fff !important;
        }
    `;
    document.head.appendChild(style);

})();


function updateDayName() {
    const dateInput = document.getElementById('transaction_date');
    const dayInput = document.getElementById('day_name');
    if (!dateInput || !dayInput) return;
    const dateVal = dateInput.value;
    if (dateVal) {
        const date = new Date(dateVal);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        if (!isNaN(date.getDay())) {
            dayInput.value = days[date.getDay()];
        } else {
            dayInput.value = '';
        }
    } else {
        dayInput.value = '';
    }
}

function updateSupplierName() {
    const supplierSelect = document.getElementById('supplier_id');
    const supplierNameInput = document.getElementById('supplier_name');
    if (!supplierSelect || !supplierNameInput) return;
    const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
    if (selectedOption) {
        supplierNameInput.value = selectedOption.text;
    } else {
        supplierNameInput.value = '';
    }
}

function updateTransaction() {
    const transactionId = document.getElementById('transaction_id').value;
    if (!transactionId) { alert('No transaction loaded'); return; }
    
    const supplierId = document.getElementById('supplier_id').value;
    if (!supplierId) { alert('Please select a supplier'); return; }
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (!rows.length) { alert('Please add at least one item'); return; }
    
    const formData = new FormData(document.getElementById('bsiForm'));
    formData.append('_method', 'PUT');
    
    // Log form data for debugging
    console.log('Update Request Payload:');
    for (var pair of formData.entries()) {
        console.log(pair[0] + ', ' + pair[1]);
    }
    
    fetch(`{{ url('admin/breakage-supplier/issued') }}/${transactionId}`, {
        method: 'POST',
        body: formData,
        headers: { 
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => {
        if (!r.ok) {
            return r.text().then(text => {
                console.error('Server error response:', r.status, text);
                throw new Error('Server error ' + r.status + ': ' + text.substring(0, 200));
            });
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            if (typeof resetFormDirty === 'function') resetFormDirty();
            // Show debug info in alert to verify what server received
            const serverDate = data.debug_payload ? data.debug_payload.transaction_date : 'N/A';
            alert('Transaction updated successfully!\nServer received date: ' + serverDate);
            window.location.href = '{{ route("admin.breakage-supplier.issued-index") }}';
        } else {
            alert(data.message || 'Error updating transaction');
        }
    })
    .catch(e => {
        console.error('Update error:', e);
        alert('Error updating transaction: ' + e.message);
    });
}

function cancelModification() {
    if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
        window.location.href = '{{ route("admin.breakage-supplier.issued-index") }}';
    }
}
</script>
