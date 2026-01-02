@extends('layouts.admin')

@section('title', 'Breakage/Expiry to Supplier - Issued Transaction')

@push('styles')
<style>
    .bsi-form { font-size: 11px; }
    .bsi-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .bsi-form input, .bsi-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: #fff; border: 1px solid #ccc; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }
    .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .readonly-field { background-color: #e9ecef !important; }
    
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
</style>
@endpush

@section('content')
<div class="bsi-form">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0"><i class="bi bi-box-arrow-up me-1"></i> Breakage/Expiry to Supplier - Issued Transaction</h6>
        <a href="{{ route('admin.breakage-supplier.issued-index') }}" class="btn btn-outline-secondary btn-sm py-0"><i class="bi bi-list"></i> View All</a>
    </div>

    <form id="bsiForm" autocomplete="off">
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
                            <div class="col-md-5"><div class="field-group"><label style="width:55px;">Supplier:</label><select id="supplier_id" name="supplier_id" class="form-control" onchange="updateSupplierName()"><option value="">Select Supplier</option>@foreach($suppliers as $s)<option value="{{ $s->supplier_id }}" data-name="{{ $s->name }}">{{ $s->name }}</option>@endforeach</select></div></div>
                            <div class="col-md-3"><div class="field-group"><label>R(epl)/C(redit):</label><select id="note_type" name="note_type" class="form-control" style="width:50px;"><option value="C">C</option><option value="R">R</option></select></div></div>
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
@endsection


@push('scripts')
<script>
let rowIndex = 0, allItems = [], selectedRowIndex = null, selectedItem = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') { e.preventDefault(); showItemModal(); }
        if (e.key === 'Escape') { closeItemModal(); closeBatchModal(); }
    });
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

// Item Modal
function showItemModal() {
    document.getElementById('itemModalBackdrop').classList.add('show');
    document.getElementById('itemModal').classList.add('show');
    document.getElementById('itemSearchInput').value = '';
    renderItemsList(allItems);
    setTimeout(() => document.getElementById('itemSearchInput').focus(), 100);
}

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
        <td><input type="text" name="items[${idx}][item_code]" value="${item.item_code || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][item_name]" value="${item.item_name || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][batch_no]" value="${batch?.batch_no || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][expiry]" value="${batch?.expiry_date || ''}" readonly class="readonly-field"></td>
        <td><input type="number" name="items[${idx}][qty]" value="" min="0" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][free_qty]" value="0" min="0" class="text-end"></td>
        <td><input type="number" name="items[${idx}][rate]" value="${rate.toFixed(2)}" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][dis_percent]" value="0" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][scm_percent]" value="0" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><select name="items[${idx}][br_ex]" class="form-control"><option value="B">Brk</option><option value="E">Exp</option></select></td>
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
</script>
@endpush
