@extends('layouts.admin')

@section('title', 'Breakage/Expiry to Supplier - Issued Modification')

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
    .items-table { font-size: 10px; margin-bottom: 0; border-collapse: collapse; width: 100%; }
    .items-table th { background: linear-gradient(180deg, #8B4513 0%, #654321 100%); color: #fff; font-weight: 600; text-align: center; padding: 6px 4px; border: 1px solid #5a3a1a; white-space: nowrap; }
    .items-table td { padding: 2px; border: 1px solid #ccc; background: #fffacd; }
    .items-table input, .items-table select { font-size: 10px; padding: 1px 3px; height: 22px; border: 1px solid #aaa; width: 100%; }
    .items-table .row-selected td { background: #cce5ff !important; }
    
    /* Summary Row - Pink */
    .summary-section { background: #ffcccc; padding: 8px; border: 1px solid #cc9999; margin-bottom: 6px; border-radius: 3px; }
    .summary-section label { font-weight: bold; font-size: 11px; }
    .summary-section input { height: 24px; font-size: 11px; }
    
    /* Footer Section - Gray */
    .footer-section { background: #d4d4d4; padding: 8px; border: 1px solid #999; border-radius: 3px; }
    .footer-section label { font-size: 10px; margin-bottom: 1px; }
    .footer-section input { height: 22px; font-size: 10px; }
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
    .item-row.selected, .batch-row.selected { background: #007bff !important; color: #fff !important; }
    
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
            <button type="button" class="btn btn-info btn-sm text-white py-0" onclick="showLoadInvoiceModal()"><i class="bi bi-folder-open me-1"></i> Load Invoice</button>
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
                            <div class="col-md-5"><div class="field-group"><label style="width:55px;">Supplier:</label><select id="supplier_id" name="supplier_id" class="form-control" onchange="updateSupplierName()"><option value="">Select Supplier</option>@foreach($suppliers ?? [] as $s)<option value="{{ $s->supplier_id }}" data-name="{{ $s->name }}">{{ $s->name }}</option>@endforeach</select></div></div>
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
            <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                <table class="items-table" id="itemsTable">
                    <thead style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width:70px;">Item Code</th>
                            <th style="width:200px;">Item Name</th>
                            <th style="width:80px;">Batch</th>
                            <th style="width:70px;">Exp</th>
                            <th style="width:50px;">Qty</th>
                            <th style="width:50px;">F.Qty</th>
                            <th style="width:70px;">Rate</th>
                            <th style="width:50px;">Dis.%</th>
                            <th style="width:50px;">Scm.%</th>
                            <th style="width:70px;">Br/Ex</th>
                            <th style="width:80px;">Amount</th>
                            <th style="width:30px;">X</th>
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

        <!-- Footer Section - Gray -->
        <div class="footer-section mb-2">
            <div class="row g-1 align-items-center">
                <div class="col-md-6">
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <div class="d-flex align-items-center gap-1"><label>SC%:</label><input type="number" id="footer_sc_percent" class="form-control readonly-field text-end" readonly style="width:50px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>EXCISE:</label><input type="number" id="footer_excise" class="form-control readonly-field text-end" readonly style="width:55px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>TAX%:</label><input type="number" id="footer_tax_percent" class="form-control readonly-field text-end" readonly style="width:50px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>Pack:</label><input type="text" id="footer_pack" class="form-control readonly-field" readonly style="width:60px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>Unit:</label><input type="text" id="footer_unit" class="form-control readonly-field" readonly style="width:40px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>Comp:</label><input type="text" id="footer_comp" class="form-control readonly-field" readonly style="width:80px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>Bal:</label><input type="number" id="footer_bal" class="form-control readonly-field text-end" readonly style="width:55px;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2 flex-wrap align-items-center justify-content-end">
                        <div class="gst-box"><label>CGST%:</label><input type="number" id="footer_cgst" class="form-control text-end" readonly></div>
                        <div class="gst-box"><label>SGST%:</label><input type="number" id="footer_sgst" class="form-control text-end" readonly></div>
                        <div class="d-flex align-items-center gap-1"><label>MRP:</label><input type="number" id="footer_mrp" class="form-control readonly-field text-end" readonly style="width:65px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>P.RATE:</label><input type="number" id="footer_prate" class="form-control readonly-field text-end" readonly style="width:65px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>S.RATE:</label><input type="number" id="footer_srate" class="form-control readonly-field text-end" readonly style="width:65px;"></div>
                    </div>
                </div>
            </div>
            <div class="row g-1 mt-1 align-items-center">
                <div class="col-md-12">
                    <div class="d-flex gap-2 flex-wrap align-items-center justify-content-end">
                        <div class="d-flex align-items-center gap-1"><label>N.T Amt:</label><input type="number" id="footer_nt_amt" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>DIS. Amt:</label><input type="number" id="footer_dis_amt" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>Net Amt:</label><input type="number" id="footer_net_amt" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>Half Scm:</label><input type="number" id="footer_half_scm" class="form-control readonly-field text-end" readonly style="width:60px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>Scm.Amt:</label><input type="number" id="footer_scm_amt" class="form-control readonly-field text-end" readonly style="width:60px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>Tax Amt:</label><input type="number" id="footer_tax_amt" class="form-control readonly-field text-end" readonly style="width:60px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>P.Scm:</label><input type="number" id="footer_pscm" class="form-control readonly-field text-end" readonly style="width:50px;"></div>
                        <div class="d-flex align-items-center gap-1"><label>S.Scm:</label><input type="number" id="footer_sscm" class="form-control readonly-field text-end" readonly style="width:50px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Section - Pink -->
        <div class="summary-section">
            <div class="d-flex gap-3 flex-wrap align-items-center justify-content-end">
                <div class="d-flex align-items-center gap-1"><label>N.T AMT:</label><input type="number" id="total_nt_amt" name="total_nt_amt" class="form-control readonly-field text-end fw-bold" readonly style="width:90px;"></div>
                <div class="d-flex align-items-center gap-1"><label>SC:</label><input type="number" id="total_sc" name="total_sc" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label>DIS. AMT:</label><input type="number" id="total_dis_amt" name="total_dis_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1"><label>Scm. AMT:</label><input type="number" id="total_scm_amt" name="total_scm_amt" class="form-control readonly-field text-end" readonly style="width:80px;"></div>
                <div class="d-flex align-items-center gap-1"><label>Half Scm.:</label><input type="number" id="total_half_scm" name="total_half_scm" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label>Tax:</label><input type="number" id="total_tax" name="total_tax" class="form-control readonly-field text-end" readonly style="width:70px;"></div>
                <div class="d-flex align-items-center gap-1"><label class="text-danger fw-bold">INV. AMT:</label><input type="number" id="total_inv_amt" name="total_inv_amt" class="form-control text-end fw-bold text-danger" readonly style="width:100px; border-color:#dc3545;"></div>
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
let rowIndex = 0, allItems = [], selectedRowIndex = null, selectedItem = null, currentTransactionId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') { e.preventDefault(); showItemModal(); }
        if (e.key === 'Escape') { closeItemModal(); closeBatchModal(); closeInvoiceModal(); }
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

// Invoice Modal
function showLoadInvoiceModal() {
    document.getElementById('invoiceModalBackdrop').classList.add('show');
    document.getElementById('invoiceModal').classList.add('show');
    document.getElementById('invoiceSearchInput').value = '';
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
        })
        .catch(() => {
            document.getElementById('invoicesListBody').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Error loading invoices</td></tr>';
        });
}

function searchInvoices() {
    loadInvoices(document.getElementById('invoiceSearchInput').value);
}

function loadInvoice(id) {
    fetch(`{{ url('admin/breakage-supplier/issued') }}/${id}`)
        .then(r => r.json())
        .then(data => {
            populateForm(data);
            closeInvoiceModal();
        })
        .catch(e => {
            console.error(e);
            alert('Error loading invoice');
        });
}

function populateForm(data) {
    currentTransactionId = data.id;
    document.getElementById('transaction_id').value = data.id;
    document.getElementById('trn_no').value = data.trn_no;
    document.getElementById('transaction_date').value = data.transaction_date ? data.transaction_date.split('T')[0] : '';
    updateDayName();
    document.getElementById('supplier_id').value = data.supplier_id || '';
    updateSupplierName();
    document.getElementById('note_type').value = data.note_type || 'C';
    document.getElementById('tax_flag').value = data.tax_flag || 'N';
    document.getElementById('inc_flag').value = data.inc_flag || 'N';
    document.getElementById('gst_vno').value = data.gst_vno || '';
    
    // Clear and populate items
    document.getElementById('itemsTableBody').innerHTML = '';
    rowIndex = 0;
    
    if (data.items && data.items.length) {
        data.items.forEach(item => {
            addItemRowFromData(item);
        });
    }
    
    calculateTotals();
    document.getElementById('updateBtn').disabled = false;
}

function addItemRowFromData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const idx = rowIndex++;
    
    const tr = document.createElement('tr');
    tr.id = `row_${idx}`;
    tr.onclick = function() { selectRow(idx); };
    tr.innerHTML = `
        <td><input type="text" name="items[${idx}][item_code]" value="${item.item_code || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][item_name]" value="${item.item_name || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][batch_no]" value="${item.batch_no || ''}" readonly class="readonly-field"></td>
        <td><input type="text" name="items[${idx}][expiry]" value="${item.expiry_date || ''}" readonly class="readonly-field"></td>
        <td><input type="number" name="items[${idx}][qty]" value="${item.qty || 1}" min="0" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][free_qty]" value="${item.free_qty || 0}" min="0" class="text-end"></td>
        <td><input type="number" name="items[${idx}][rate]" value="${parseFloat(item.rate || 0).toFixed(2)}" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][dis_percent]" value="${item.dis_percent || 0}" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><input type="number" name="items[${idx}][scm_percent]" value="${item.scm_percent || 0}" step="0.01" class="text-end" onchange="calculateRowAmount(${idx})"></td>
        <td><select name="items[${idx}][br_ex]" class="form-control"><option value="B" ${item.br_ex === 'B' ? 'selected' : ''}>Brk</option><option value="E" ${item.br_ex === 'E' ? 'selected' : ''}>Exp</option><option value="D" ${item.br_ex === 'D' ? 'selected' : ''}>Dis</option><option value="R" ${item.br_ex === 'R' ? 'selected' : ''}>Rpl</option></select></td>
        <td><input type="number" name="items[${idx}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" step="0.01" class="text-end readonly-field" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm py-0 px-1" onclick="removeRow(${idx})">&times;</button></td>
        <input type="hidden" name="items[${idx}][id]" value="${item.id || ''}">
        <input type="hidden" name="items[${idx}][item_id]" value="${item.item_id || ''}">
        <input type="hidden" name="items[${idx}][batch_id]" value="${item.batch_id || ''}">
        <input type="hidden" name="items[${idx}][mrp]" value="${item.mrp || 0}">
        <input type="hidden" name="items[${idx}][purchase_rate]" value="${item.purchase_rate || 0}">
        <input type="hidden" name="items[${idx}][sale_rate]" value="${item.sale_rate || 0}">
        <input type="hidden" name="items[${idx}][cgst]" value="${item.cgst || 0}">
        <input type="hidden" name="items[${idx}][sgst]" value="${item.sgst || 0}">
        <input type="hidden" name="items[${idx}][company_name]" value="${item.company_name || ''}">
        <input type="hidden" name="items[${idx}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${idx}][unit]" value="${item.unit || ''}">
    `;
    tbody.appendChild(tr);
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
    document.getEl