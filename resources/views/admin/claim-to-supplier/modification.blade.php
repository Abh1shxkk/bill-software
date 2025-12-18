@extends('layouts.admin')

@section('title', 'Claim to Supplier Modification')

@push('styles')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    input:focus { box-shadow: none !important; }
    .cts .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .cts .field-group { display: flex; align-items: center; gap: 6px; }
    .cts .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .cts .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #e9ecef; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    .past-transactions-table { font-size: 11px; }
    .past-transactions-table td, .past-transactions-table th { padding: 4px 8px; }
    .past-transactions-table tr { cursor: pointer; }
    .past-transactions-table tr:hover { background-color: #e3f2fd; }
    .past-transactions-table tr.selected { background-color: #bbdefb; }
    
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
    .btn-close-custom {
        background: none; border: none; color: white; font-size: 24px; cursor: pointer; line-height: 1;
    }
    .item-modal-body { padding: 15px 20px; max-height: 60vh; overflow-y: auto; }
    .item-modal-footer { padding: 10px 20px; border-top: 1px solid #dee2e6; text-align: right; }
</style>
@endpush

@section('content')
<section class="cts py-5">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil-square me-2"></i> Claim to Supplier Modification</h4>
                <div class="text-muted small">Modify existing claim to supplier transaction</div>
            </div>
        </div>

        <div class="row">
            <!-- Left Panel - Past Transactions -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white py-2">
                        <strong>Past Transactions</strong>
                    </div>
                    <div class="card-body p-2">
                        <div class="mb-2">
                            <input type="date" id="filterDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-2">
                            <input type="text" id="searchClaimNo" class="form-control form-control-sm" placeholder="Search Claim No...">
                        </div>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered past-transactions-table mb-0">
                                <thead class="table-light" style="position: sticky; top: 0;">
                                    <tr>
                                        <th>Claim No</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="pastTransactionsBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="col-md-9">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body">
                        <form id="ctsForm" method="POST" autocomplete="off">
                            <input type="hidden" id="claim_id" name="claim_id">
                            
                            <!-- Header Section -->
                            <div class="header-section">
                                <div class="d-flex gap-3 mb-2">
                                    <div style="width: 200px;">
                                        <div class="field-group mb-2">
                                            <label style="width: 50px;">Date:</label>
                                            <input type="date" id="claim_date" name="claim_date" class="form-control" style="width: 140px;" required>
                                        </div>
                                        <div class="field-group mb-2">
                                            <label style="width: 50px;">Day:</label>
                                            <input type="text" id="day_name" class="form-control readonly-field" style="width: 140px;" readonly>
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
                                            <div class="col-md-8">
                                                <div class="field-group">
                                                    <label style="width: 80px;">Narration:</label>
                                                    <input type="text" class="form-control" id="narration" name="narration">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="bg-white border rounded p-2 mb-2">
                                <div class="table-responsive" style="overflow-y: auto; max-height: 250px;">
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
                                    <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                                        <i class="fas fa-plus-circle"></i> Add Row
                                    </button>
                                </div>
                            </div>

                            <!-- Summary Section -->
                            <div class="bg-white border rounded p-2 mb-2">
                                <div class="d-flex align-items-center" style="font-size: 11px; gap: 10px;">
                                    <div class="d-flex align-items-center" style="gap: 5px;">
                                        <label class="mb-0" style="font-weight: bold;">N.T AMT</label>
                                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="nt_amount" id="ntAmount" readonly style="width: 80px; height: 26px; background: #fff3cd;" value="0.00">
                                    </div>
                                    <div class="d-flex align-items-center" style="gap: 5px;">
                                        <label class="mb-0" style="font-weight: bold;">Tax</label>
                                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="tax_amount" id="taxAmount" readonly style="width: 80px; height: 26px;" value="0.00">
                                    </div>
                                    <div class="d-flex align-items-center" style="gap: 5px;">
                                        <label class="mb-0" style="font-weight: bold;">INV. AMT</label>
                                        <input type="number" class="form-control form-control-sm readonly-field text-end" name="inv_amount" id="invAmount" readonly style="width: 80px; height: 26px;" value="0.00">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    <button type="button" class="btn btn-primary" id="updateBtn" disabled>Update</button>
                                    <button type="button" class="btn btn-danger" id="deleteBtn" disabled>Delete</button>
                                </div>
                                <button type="button" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
let rowIndex = 0;
let currentClaimId = null;

$(document).ready(function() {
    loadPastTransactions();
    
    $('#filterDate').on('change', loadPastTransactions);
    $('#searchClaimNo').on('input', function() {
        const claimNo = $(this).val().trim();
        if (claimNo.length >= 3) {
            searchByClaimNo(claimNo);
        } else if (claimNo.length === 0) {
            loadPastTransactions();
        }
    });
    
    $('#claim_date').on('change', function() {
        const date = new Date($(this).val());
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $('#day_name').val(days[date.getDay()]);
    });
    
    $('#updateBtn').on('click', updateTransaction);
    $('#deleteBtn').on('click', deleteTransaction);
});

function loadPastTransactions() {
    const date = $('#filterDate').val();
    $.get("{{ route('admin.claim-to-supplier.past-claims') }}", { date: date }, function(response) {
        if (response.success) {
            renderPastTransactions(response.transactions);
        }
    });
}

function renderPastTransactions(transactions) {
    let html = '';
    transactions.forEach(trn => {
        html += `<tr onclick="loadTransaction(${trn.id})" data-id="${trn.id}">
            <td>${trn.claim_no}</td>
            <td>${trn.claim_date}</td>
            <td class="text-end">₹${trn.amount}</td>
        </tr>`;
    });
    $('#pastTransactionsBody').html(html || '<tr><td colspan="3" class="text-center text-muted">No transactions found</td></tr>');
}

function searchByClaimNo(claimNo) {
    $.get("{{ url('admin/claim-to-supplier/get-by-claim-no') }}/" + claimNo, function(response) {
        if (response.success) {
            populateForm(response.header, response.items);
        }
    });
}

function loadTransaction(id) {
    $('#pastTransactionsBody tr').removeClass('selected');
    $(`#pastTransactionsBody tr[data-id="${id}"]`).addClass('selected');
    
    $.get("{{ url('admin/claim-to-supplier/details') }}/" + id, function(response) {
        if (response.success) {
            populateForm(response.header, response.items);
        }
    });
}

function populateForm(header, items) {
    currentClaimId = header.id;
    $('#claim_id').val(header.id);
    $('#trn_no').val(header.claim_no);
    $('#claim_date').val(header.claim_date);
    $('#supplier_id').val(header.supplier_id);
    $('#invoice_date').val(header.invoice_date);
    $('#tax_flag').val(header.tax_flag);
    $('#narration').val(header.narration);
    $('#ntAmount').val(parseFloat(header.nt_amount).toFixed(2));
    $('#taxAmount').val(parseFloat(header.tax_amount).toFixed(2));
    $('#invAmount').val(parseFloat(header.net_amount).toFixed(2));
    
    if (header.claim_date) {
        const date = new Date(header.claim_date);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $('#day_name').val(days[date.getDay()]);
    }
    
    $('#itemsTableBody').empty();
    rowIndex = 0;
    
    items.forEach(item => {
        addNewRow();
        const $row = $(`tr[data-row="${rowIndex - 1}"]`);
        $row.data('item_id', item.item_id);
        $row.data('batch_id', item.batch_id);
        $row.find('.item-code').val(item.item_code);
        $row.find('.item-name').val(item.item_name);
        $row.find('.batch-no').val(item.batch_no);
        $row.find('.expiry').val(item.expiry);
        $row.find('.qty').val(item.qty);
        $row.find('.free-qty').val(item.free_qty);
        $row.find('.rate').val(item.pur_rate);
        $row.find('.dis-percent').val(item.dis_percent);
        $row.find('.amount').val(parseFloat(item.ft_amount || item.net_amount).toFixed(2));
    });
    
    $('#updateBtn, #deleteBtn').prop('disabled', false);
}

let allItems = [];
let selectedItem = null;

function addNewRow() {
    const supplierId = $('#supplier_id').val();
    if (!supplierId) {
        alert('Please select a supplier first');
        return;
    }
    
    $.get("{{ route('admin.items.get-all') }}", function(data) {
        allItems = data.items || data;
        showItemSelectionModal(allItems);
    }).fail(function() {
        alert('Failed to load items');
    });
}

function showItemSelectionModal(items) {
    let itemsHtml = items.map((item, index) => `
        <tr class="item-row" data-item-name="${(item.name || '').toLowerCase()}" data-item-code="${(item.code || '').toLowerCase()}">
            <td style="text-align: center;">${index + 1}</td>
            <td>${item.code || ''}</td>
            <td>${item.name || ''}</td>
            <td>${item.company_name || ''}</td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-sm btn-primary" onclick='selectItem(${JSON.stringify(item).replace(/'/g, "\\'")})'
                    style="font-size: 9px; padding: 2px 8px;">Select</button>
            </td>
        </tr>
    `).join('');
    
    const modalHTML = `
        <div class="modal-backdrop-custom" id="itemModalBackdrop" onclick="closeItemModal()"></div>
        <div class="item-modal" id="itemModal">
            <div class="item-modal-header">
                <h5><i class="bi bi-box-seam me-2"></i>Select Item</h5>
                <button type="button" class="btn-close-custom" onclick="closeItemModal()">&times;</button>
            </div>
            <div class="item-modal-body">
                <div style="margin-bottom: 10px;">
                    <input type="text" id="itemSearchInput" class="form-control form-control-sm" 
                           placeholder="Search by item name or code..." onkeyup="filterItems()" style="font-size: 11px;">
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
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
            </div>
        </div>
    `;
    
    $('#itemModal, #itemModalBackdrop').remove();
    $('body').append(modalHTML);
    setTimeout(() => { $('#itemModalBackdrop, #itemModal').addClass('show'); }, 10);
}

function filterItems() {
    const searchValue = $('#itemSearchInput').val().toLowerCase();
    $('.item-row').each(function() {
        const name = $(this).data('item-name');
        const code = $(this).data('item-code');
        $(this).toggle(name.includes(searchValue) || code.includes(searchValue));
    });
}

function closeItemModal() {
    $('#itemModalBackdrop, #itemModal').removeClass('show');
    setTimeout(() => { $('#itemModal, #itemModalBackdrop').remove(); }, 300);
}

function selectItem(item) {
    if (typeof item === 'string') item = JSON.parse(item);
    selectedItem = item;
    closeItemModal();
    loadBatchesForItem(item.id);
}

function loadBatchesForItem(itemId) {
    const supplierId = $('#supplier_id').val();
    $.get("{{ route('admin.claim-to-supplier.batches') }}", { supplier_id: supplierId, item_id: itemId }, function(response) {
        if (response.batches && response.batches.length > 0) {
            showBatchSelectionModal(response.batches);
        } else {
            alert('No batches found for this item');
        }
    });
}

function showBatchSelectionModal(batches) {
    let batchesHtml = batches.map((batch, index) => `
        <tr class="batch-row">
            <td style="text-align: center;">${index + 1}</td>
            <td>${batch.batch_no || ''}</td>
            <td>${batch.expiry ? new Date(batch.expiry).toLocaleDateString('en-GB', {month: '2-digit', year: '2-digit'}) : ''}</td>
            <td class="text-end">${batch.available_qty || 0}</td>
            <td class="text-end">${parseFloat(batch.purchase_rate || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-sm btn-success" onclick='selectBatch(${JSON.stringify(batch).replace(/'/g, "\\'")})'
                    style="font-size: 9px; padding: 2px 8px;">Select</button>
            </td>
        </tr>
    `).join('');
    
    const modalHTML = `
        <div class="modal-backdrop-custom" id="batchModalBackdrop" onclick="closeBatchModal()"></div>
        <div class="item-modal" id="batchModal">
            <div class="item-modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <h5><i class="bi bi-layers me-2"></i>Select Batch for: ${selectedItem.name}</h5>
                <button type="button" class="btn-close-custom" onclick="closeBatchModal()">&times;</button>
            </div>
            <div class="item-modal-body">
                <div style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 10px; margin-bottom: 0;">
                        <thead style="position: sticky; top: 0; background: #28a745; color: white; z-index: 10;">
                            <tr>
                                <th style="width: 35px; text-align: center;">S.N</th>
                                <th style="width: 80px;">Batch No</th>
                                <th style="width: 70px;">Expiry</th>
                                <th style="width: 60px; text-align: right;">Avl Qty</th>
                                <th style="width: 70px; text-align: right;">Rate</th>
                                <th style="width: 70px; text-align: right;">MRP</th>
                                <th style="width: 80px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>${batchesHtml}</tbody>
                    </table>
                </div>
            </div>
            <div class="item-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
            </div>
        </div>
    `;
    
    $('#batchModal, #batchModalBackdrop').remove();
    $('body').append(modalHTML);
    setTimeout(() => { $('#batchModalBackdrop, #batchModal').addClass('show'); }, 10);
}

function closeBatchModal() {
    $('#batchModalBackdrop, #batchModal').removeClass('show');
    setTimeout(() => { $('#batchModal, #batchModalBackdrop').remove(); }, 300);
}

function selectBatch(batch) {
    if (typeof batch === 'string') batch = JSON.parse(batch);
    closeBatchModal();
    addItemRow(selectedItem, batch);
}

function addItemRow(item, batch) {
    const expiry = batch.expiry ? new Date(batch.expiry).toLocaleDateString('en-GB', {month: '2-digit', year: '2-digit'}) : '';
    const row = `
        <tr data-row="${rowIndex}">
            <td><input type="text" class="form-control item-code" data-row="${rowIndex}" value="${item.code || ''}" readonly></td>
            <td><input type="text" class="form-control item-name" data-row="${rowIndex}" value="${item.name || ''}" readonly></td>
            <td><input type="text" class="form-control batch-no" data-row="${rowIndex}" value="${batch.batch_no || ''}" readonly></td>
            <td><input type="text" class="form-control expiry" data-row="${rowIndex}" value="${expiry}" readonly></td>
            <td><input type="number" class="form-control qty" data-row="${rowIndex}" value="1" min="0"></td>
            <td><input type="number" class="form-control free-qty" data-row="${rowIndex}" value="0" min="0"></td>
            <td><input type="number" class="form-control rate" data-row="${rowIndex}" value="${batch.purchase_rate || 0}" step="0.01"></td>
            <td><input type="number" class="form-control dis-percent" data-row="${rowIndex}" value="0" step="0.01"></td>
            <td><input type="number" class="form-control amount" data-row="${rowIndex}" value="${batch.purchase_rate || 0}" step="0.01" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
    $('#itemsTableBody').append(row);
    
    const $row = $(`tr[data-row="${rowIndex}"]`);
    $row.data('item_id', item.id);
    $row.data('batch_id', batch.batch_id);
    
    rowIndex++;
    calculateTotals();
}

// For populating existing items (when loading a transaction)
function addExistingItemRow(item) {
    const row = `
        <tr data-row="${rowIndex}">
            <td><input type="text" class="form-control item-code" data-row="${rowIndex}" value="${item.item_code || ''}" readonly></td>
            <td><input type="text" class="form-control item-name" data-row="${rowIndex}" value="${item.item_name || ''}" readonly></td>
            <td><input type="text" class="form-control batch-no" data-row="${rowIndex}" value="${item.batch_no || ''}" readonly></td>
            <td><input type="text" class="form-control expiry" data-row="${rowIndex}" value="${item.expiry || ''}" readonly></td>
            <td><input type="number" class="form-control qty" data-row="${rowIndex}" value="${item.qty || 0}" min="0"></td>
            <td><input type="number" class="form-control free-qty" data-row="${rowIndex}" value="${item.free_qty || 0}" min="0"></td>
            <td><input type="number" class="form-control rate" data-row="${rowIndex}" value="${item.pur_rate || 0}" step="0.01"></td>
            <td><input type="number" class="form-control dis-percent" data-row="${rowIndex}" value="${item.dis_percent || 0}" step="0.01"></td>
            <td><input type="number" class="form-control amount" data-row="${rowIndex}" value="${parseFloat(item.ft_amount || item.net_amount || 0).toFixed(2)}" step="0.01" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
    $('#itemsTableBody').append(row);
    
    const $row = $(`tr[data-row="${rowIndex}"]`);
    $row.data('item_id', item.item_id);
    $row.data('batch_id', item.batch_id);
    
    rowIndex++;
}

function removeRow(row) {
    $(`tr[data-row="${row}"]`).remove();
    calculateTotals();
}

$(document).on('change', '.qty, .free-qty, .rate, .dis-percent', function() {
    const $row = $(this).closest('tr');
    calculateRowAmount($row);
    calculateTotals();
});

function calculateRowAmount($row) {
    const qty = parseFloat($row.find('.qty').val()) || 0;
    const rate = parseFloat($row.find('.rate').val()) || 0;
    const disPercent = parseFloat($row.find('.dis-percent').val()) || 0;
    let amount = qty * rate;
    if (disPercent > 0) amount = amount - (amount * disPercent / 100);
    $row.find('.amount').val(amount.toFixed(2));
}

function calculateTotals() {
    let ntAmount = 0;
    $('#itemsTableBody tr').each(function() {
        ntAmount += parseFloat($(this).find('.amount').val()) || 0;
    });
    $('#ntAmount').val(ntAmount.toFixed(2));
    $('#invAmount').val(ntAmount.toFixed(2));
}

function updateTransaction() {
    if (!currentClaimId) return;
    
    const items = [];
    $('#itemsTableBody tr').each(function() {
        const $row = $(this);
        const qty = parseFloat($row.find('.qty').val()) || 0;
        if (qty > 0) {
            items.push({
                item_id: $row.data('item_id'),
                batch_id: $row.data('batch_id'),
                item_code: $row.find('.item-code').val(),
                item_name: $row.find('.item-name').val(),
                batch_no: $row.find('.batch-no').val(),
                expiry: $row.find('.expiry').val(),
                qty: qty,
                free_qty: parseFloat($row.find('.free-qty').val()) || 0,
                pur_rate: parseFloat($row.find('.rate').val()) || 0,
                dis_percent: parseFloat($row.find('.dis-percent').val()) || 0,
                ft_amount: parseFloat($row.find('.amount').val()) || 0,
            });
        }
    });
    
    $.ajax({
        url: "{{ url('admin/claim-to-supplier') }}/" + currentClaimId,
        method: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            claim_date: $('#claim_date').val(),
            supplier_id: $('#supplier_id').val(),
            supplier_name: $('#supplier_id option:selected').text(),
            invoice_date: $('#invoice_date').val(),
            tax_flag: $('#tax_flag').val(),
            narration: $('#narration').val(),
            nt_amount: parseFloat($('#ntAmount').val()) || 0,
            net_amount: parseFloat($('#invAmount').val()) || 0,
            items: items
        },
        success: function(response) {
            if (response.success) {
                alert('Claim updated successfully!');
                loadPastTransactions();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error updating claim: ' + (xhr.responseJSON?.message || 'Unknown error'));
        }
    });
}

function deleteTransaction() {
    if (!currentClaimId) return;
    if (!confirm('Are you sure you want to delete this claim?')) return;
    
    $.ajax({
        url: "{{ url('admin/claim-to-supplier') }}/" + currentClaimId,
        method: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                alert('Claim deleted successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}
</script>
@endpush
