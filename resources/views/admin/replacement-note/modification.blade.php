@extends('layouts.admin')

@section('title', 'Replacement Note Modification')

@push('styles')
<style>
    .compact-form { font-size: 11px; padding: 8px; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    .header-section { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; }
    .table-compact th { background: #90EE90; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 35px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; width: 100%; }
    
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .row-selected { background-color: #d4edff !important; }
    .row-selected td { background-color: #d4edff !important; }
    
    .calc-section { background: #f5f5f5; border: 1px solid #dee2e6; padding: 8px; }
    .summary-section { background: #ffcccc; border: 1px solid #dee2e6; padding: 8px; }
    .footer-section { background: #ffe4b5; border: 1px solid #dee2e6; padding: 8px; }
</style>
@endpush

@section('content')
<section class="compact-form py-2">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Replacement Note Modification</h5>
            <div>
                <a href="{{ route('admin.replacement-note.transaction') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> New
                </a>
                <a href="{{ route('admin.replacement-note.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-2">
                <form id="rnForm">
                    @csrf
                    <input type="hidden" id="transaction_id">
                    
                    <!-- Header -->
                    <div class="header-section mb-2">
                        <div class="d-flex gap-3 align-items-center">
                            <div class="field-group">
                                <label>Date:</label>
                                <input type="date" id="transaction_date" class="form-control" style="width: 130px;" onchange="updateDayName()">
                                <input type="text" id="day_name" class="form-control readonly-field" style="width: 90px;" readonly>
                            </div>
                            <div class="field-group">
                                <label>Supplier:</label>
                                <select id="supplier_id" class="form-control" style="width: 250px;">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field-group">
                                <label>Rpl.No:</label>
                                <input type="text" id="rn_no" class="form-control readonly-field" style="width: 100px;" readonly>
                            </div>
                            <button type="button" class="btn btn-success btn-sm ms-auto" onclick="openInsertInvoiceModal()">
                                <i class="bi bi-file-earmark-plus"></i> Load Invoice
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="border rounded mb-2" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-compact mb-0">
                            <thead style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="width: 70px;">Code</th>
                                    <th style="width: 250px;">Item Name</th>
                                    <th style="width: 90px;">Batch</th>
                                    <th style="width: 70px;">Expiry</th>
                                    <th style="width: 60px;">Qty</th>
                                    <th style="width: 80px;">M.R.P</th>
                                    <th style="width: 90px;">Amount</th>
                                    <th style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody"></tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mb-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="addNewRow()">
                            <i class="bi bi-plus"></i> Add Row
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="openInsertItemsModal()">
                            <i class="bi bi-list-check"></i> Insert Item
                        </button>
                    </div>

                    <!-- Calculation Section - Removed -->
                    

                    <!-- Summary -->
                    <div class="summary-section mb-2">
                        <div class="d-flex justify-content-end">
                            <label class="fw-bold me-2">Net</label>
                            <input type="text" id="total_amount" class="form-control readonly-field text-end fw-bold" style="width: 120px;" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer-section mb-2">
                        <div class="row g-2">
                            <div class="col-md-2"><div class="field-group"><label>Pack</label><input type="text" id="packing" class="form-control readonly-field" readonly></div></div>
                            <div class="col-md-1"><div class="field-group"><label>Unit</label><input type="text" id="unit" class="form-control readonly-field" readonly></div></div>
                            <div class="col-md-2"><div class="field-group"><label>Cl. Qty</label><input type="text" id="cl_qty" class="form-control readonly-field" readonly></div></div>
                            <div class="col-md-2"><div class="field-group"><label>Comp :</label><input type="text" id="comp" class="form-control readonly-field" readonly></div></div>
                            <div class="col-md-2"><div class="field-group"><label>Lctn :</label><input type="text" id="lctn" name="lctn" class="form-control"></div></div>
                            <div class="col-md-1"><div class="field-group"><label>Srlno</label><input type="text" id="srlno" class="form-control readonly-field" readonly></div></div>
                            <div class="col-md-1"><div class="field-group"><label>Case :</label><input type="text" id="case" class="form-control"></div></div>
                            <div class="col-md-1"><div class="field-group"><label>Box :</label><input type="text" id="box" class="form-control"></div></div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" onclick="saveTransaction()">Save</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteTransaction()" id="deleteBtn" disabled>Delete</button>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="clearForm()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Modal placeholder -->
<div id="modalContainer"></div>

@endsection

@push('scripts')
<script>
let currentRowIndex = 0, itemsData = [], currentTransactionId = null, selectedRowIndex = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id) loadTransaction(id);
});

function loadItems() {
    fetch('{{ route("admin.items.get-all") }}')
        .then(r => r.json())
        .then(d => itemsData = d.items || [])
        .catch(e => console.error(e));
}

function loadTransaction(id) {
    console.log('Loading transaction ID:', id);
    fetch(`{{ url('admin/replacement-note/details') }}/${id}`)
        .then(r => {
            if (!r.ok) throw new Error('Network response not ok: ' + r.status);
            return r.json();
        })
        .then(d => {
            console.log('Transaction data received:', d);
            if (d.success) {
                populateForm(d.transaction, d.items);
                currentTransactionId = id;
                document.getElementById('deleteBtn').disabled = false;
            } else {
                console.error('Failed to load transaction:', d.message);
                alert('Failed to load transaction: ' + (d.message || 'Unknown error'));
            }
        })
        .catch(e => {
            console.error('Load transaction error:', e);
            alert('Error loading transaction: ' + e.message);
        });
}

function populateForm(t, items) {
    document.getElementById('transaction_id').value = t.id;
    document.getElementById('rn_no').value = t.rn_no || t.replacement_note_no || '';
    
    // Parse date properly - handle Laravel Carbon format
    let dateValue = '';
    if (t.transaction_date) {
        try {
            let dateStr = '';
            if (typeof t.transaction_date === 'string') {
                dateStr = t.transaction_date;
            } else if (t.transaction_date.date) {
                dateStr = t.transaction_date.date;
            }
            // Extract YYYY-MM-DD from ISO format or datetime
            const dateMatch = dateStr.match(/(\d{4}-\d{2}-\d{2})/);
            if (dateMatch) {
                dateValue = dateMatch[1];
            }
        } catch (e) {
            console.error('Date parse error:', e);
        }
    }
    document.getElementById('transaction_date').value = dateValue;
    
    // Set day name
    if (t.day_name) {
        document.getElementById('day_name').value = t.day_name;
    } else if (dateValue) {
        updateDayName();
    }
    
    // Set supplier - with slight delay to ensure DOM is ready
    setTimeout(() => {
        if (t.supplier_id) {
            const supplierSelect = document.getElementById('supplier_id');
            if (supplierSelect) {
                supplierSelect.value = t.supplier_id;
                // Trigger change event in case there's a plugin
                const event = new Event('change', { bubbles: true });
                supplierSelect.dispatchEvent(event);
                // Also try jQuery trigger if available
                if (typeof $ !== 'undefined') {
                    $(supplierSelect).trigger('change');
                }
                console.log('Supplier set to:', t.supplier_id);
            }
        }
    }, 100);
    
    // Set location and other fields
    document.getElementById('lctn').value = t.lctn || t.location || '';
    document.getElementById('srlno').value = t.srlno || '';
    document.getElementById('case').value = t.case_no || '';
    document.getElementById('box').value = t.box || '';
    
    document.getElementById('itemsTableBody').innerHTML = '';
    currentRowIndex = 0;
    (items || []).forEach(item => addNewRowWithData(item));
    if (items?.length > 0) setTimeout(() => selectRow(0), 100);
    calculateTotals();
}

function addNewRow() {
    addNewRowWithData({});
    setTimeout(() => {
        selectRow(currentRowIndex - 1);
        document.getElementById(`row-${currentRowIndex - 1}`)?.querySelector('input[name*="[code]"]')?.focus();
    }, 50);
}

function addNewRowWithData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.itemId = item.item_id || '';
    row.dataset.batchId = item.batch_id || '';
    row.dataset.sRate = parseFloat(item.s_rate || 0).toFixed(2);
    row.onclick = () => selectRow(rowIndex);
    
    if (item.item_id) {
        const itemObj = itemsData.find(i => i.id == item.item_id);
        row.dataset.itemData = JSON.stringify(itemObj || {
            id: item.item_id, name: item.item_name || item.name, item_code: item.item_code || item.code,
            packing: item.packing, unit: item.unit, company_short_name: item.company_name || item.company_short_name,
            mrp: item.mrp, ws_rate: item.ws_rate || item.wholesale_rate, 
            spl_rate: item.spl_rate || item.special_rate, s_rate: item.s_rate || item.sale_rate
        });
    }
    
    const qty = parseFloat(item.qty || 0);
    const mrp = parseFloat(item.mrp || 0);
    const amount = parseFloat(item.amount || 0);
    row.innerHTML = `
        <td><input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.item_code || ''}" onchange="searchItemByCode(${rowIndex}, this.value)"></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.item_name || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${item.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${item.expiry || ''}" placeholder="MM/YY"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" value="${qty > 0 ? qty : ''}" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][mrp]" value="${mrp > 0 ? mrp.toFixed(2) : ''}" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control text-end readonly-field" name="items[${rowIndex}][amount]" value="${amount > 0 ? amount.toFixed(2) : ''}" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger py-0" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
    `;
    tbody.appendChild(row);
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
        const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
        document.getElementById('packing').value = itemData.packing || '';
        document.getElementById('unit').value = itemData.unit || '1';
        document.getElementById('comp').value = itemData.company_short_name || '';
    }
}

function searchItemByCode(rowIndex, code) {
    if (!code) return;
    const item = itemsData.find(i => i.id == code || i.item_code == code);
    if (item) {
        const row = document.getElementById(`row-${rowIndex}`);
        row.querySelector('input[name*="[name]"]').value = item.name || '';
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(item.mrp || 0).toFixed(2);
        row.dataset.itemId = item.id;
        row.dataset.itemData = JSON.stringify(item);
        selectRow(rowIndex);
        calculateRowAmount(rowIndex);
    }
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const mrp = parseFloat(row.querySelector('input[name*="[mrp]"]').value) || 0;
    const amount = qty * mrp;
    row.querySelector('input[name*="[amount]"]').value = amount.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        total += parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
    });
    document.getElementById('total_amount').value = total.toFixed(2);
}

function removeRow(rowIndex) {
    document.getElementById(`row-${rowIndex}`)?.remove();
    calculateTotals();
}

function updateDayName() {
    const dateInput = document.getElementById('transaction_date');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('day_name').value = days[date.getDay()];
    }
}

function clearForm() {
    document.getElementById('rnForm').reset();
    document.getElementById('itemsTableBody').innerHTML = '';
    document.getElementById('total_amount').value = '0.00';
    currentTransactionId = null;
    currentRowIndex = 0;
    document.getElementById('deleteBtn').disabled = true;
}

function saveTransaction() {
    if (!currentTransactionId) { alert('Please select a transaction'); return; }
    
    const transactionDate = document.getElementById('transaction_date').value;
    if (!transactionDate) { alert('Please select transaction date'); return; }
    
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        if (qty > 0) {
            const itemData = {
                item_id: row.dataset.itemId,
                batch_id: row.dataset.batchId || null,
                item_code: row.querySelector('input[name*="[code]"]')?.value,
                item_name: row.querySelector('input[name*="[name]"]')?.value,
                batch_no: row.querySelector('input[name*="[batch]"]')?.value,
                expiry: row.querySelector('input[name*="[expiry]"]')?.value,
                qty: qty,
                mrp: parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0,
                amount: parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0
            };
            
            // Include new batch data if this is a new batch
            if (row.dataset.isNewBatch === 'true' && row.dataset.newBatchData) {
                try {
                    const batchData = JSON.parse(row.dataset.newBatchData);
                    itemData.is_new_batch = true;
                    itemData.new_batch_s_rate = parseFloat(batchData.s_rate) || 0;
                    itemData.new_batch_location = batchData.location || '';
                } catch (e) {
                    console.error('Error parsing batch data:', e);
                }
            }
            
            items.push(itemData);
        }
    });
    if (items.length === 0) { alert('Please add items'); return; }
    
    fetch(`{{ url('admin/replacement-note/update') }}/${currentTransactionId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            transaction_date: transactionDate,
            day_name: document.getElementById('day_name').value || '',
            supplier_id: document.getElementById('supplier_id').value || null,
            net_amount: parseFloat(document.getElementById('total_amount').value) || 0,
            pack: document.getElementById('packing').value || '',
            unit: document.getElementById('unit').value || '',
            cl_qty: document.getElementById('cl_qty').value || 0,
            comp: document.getElementById('comp').value || '',
            lctn: document.getElementById('lctn').value || '',
            srlno: document.getElementById('srlno').value || '',
            case_no: document.getElementById('case').value || '',
            box: document.getElementById('box').value || '',
            items: items
        })
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Updated successfully!');
            window.location.reload();
        } else alert('Error: ' + (result.message || 'Update failed'));
    }).catch(e => {
        console.error('Save error:', e);
        alert('Error updating transaction');
    });
}

function deleteTransaction() {
    if (!currentTransactionId || !confirm('Delete this replacement note?')) return;
    fetch(`{{ url('admin/replacement-note') }}/${currentTransactionId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Deleted!');
            window.location.href = '{{ route("admin.replacement-note.index") }}';
        } else alert('Error: ' + (result.message || 'Delete failed'));
    }).catch(e => alert('Error deleting'));
}

function openInsertInvoiceModal() {
    fetch('{{ route("admin.replacement-note.past-notes") }}')
        .then(r => r.json())
        .then(d => {
            const txs = d.transactions || [];
            let html = `<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;" onclick="closeModal()"></div>
                <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:80%;max-width:700px;background:white;border-radius:8px;z-index:1055;" id="invoiceModal">
                    <div style="padding:1rem;background:#6f42c1;color:white;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;">
                        <h6 class="mb-0">Select Invoice</h6>
                        <button onclick="closeModal()" style="background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
                    </div>
                    <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                        <table class="table table-sm table-hover"><thead class="table-primary"><tr><th>RN No</th><th>Date</th><th>Supplier</th><th>Amount</th><th></th></tr></thead><tbody>`;
            txs.forEach(t => {
                html += `<tr><td>${t.rn_no}</td><td>${t.transaction_date||''}</td><td>${t.supplier_name||''}</td><td class="text-end">${t.amount||'0.00'}</td>
                    <td><button class="btn btn-sm btn-primary py-0" onclick="loadInvoiceFromModal(${t.id})"><i class="bi bi-eye"></i></button></td></tr>`;
            });
            html += `</tbody></table></div></div>`;
            document.getElementById('modalContainer').innerHTML = html;
        });
}

let pendingItemForBatch = null;
let pendingBatchRowIndex = null;
let batchCheckInProgress = false;

function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        const batchInput = row?.querySelector('input[name*="[batch]"]');
        if (batchInput && batchInput.value.trim()) {
            checkBatch(rowIndex);
        } else {
            row?.querySelector('input[name*="[expiry]"]')?.focus();
        }
    }
}

function checkBatch(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const itemId = row.dataset.itemId;
    const batchNo = row.querySelector('input[name*="[batch]"]').value.trim();
    
    if (!itemId || !batchNo) return;
    if (batchCheckInProgress) return;
    
    batchCheckInProgress = true;
    pendingBatchRowIndex = rowIndex;
    
    const itemData = row.dataset.itemData ? JSON.parse(row.dataset.itemData) : {};
    
    fetch(`{{ route('admin.batches.check-batch') }}?item_id=${itemId}&batch_no=${encodeURIComponent(batchNo)}`)
        .then(r => r.json())
        .then(d => {
            if (d.exists && d.batches && d.batches.length > 0) {
                showExistingBatchModal(d.batches, rowIndex, itemData);
            } else {
                showCreateBatchModal(rowIndex, batchNo, itemData);
            }
        })
        .catch(e => {
            console.error(e);
            showCreateBatchModal(rowIndex, batchNo, itemData);
        })
        .finally(() => {
            setTimeout(() => { batchCheckInProgress = false; }, 500);
        });
}

function showExistingBatchModal(batches, rowIndex, itemData) {
    let html = `<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1060;" id="existingBatchBackdrop"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:80%;max-width:700px;background:white;border-radius:8px;z-index:1065;" id="existingBatchModal">
            <div style="padding:0.75rem;background:#17a2b8;color:white;border-radius:8px 8px 0 0;">
                <h6 class="mb-0"><i class="bi bi-box me-1"></i> Select Existing Batch - ${itemData.name || ''}</h6>
                <button onclick="closeExistingBatchModal()" style="position:absolute;top:10px;right:15px;background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
            </div>
            <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                <table class="table table-sm table-hover"><thead class="table-info"><tr><th>Batch</th><th>Expiry</th><th>MRP</th><th>Qty</th><th>Action</th></tr></thead><tbody>`;
    batches.forEach(batch => {
        const exp = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month:'2-digit',year:'numeric'}) : '';
        html += `<tr ondblclick='selectExistingBatch(${rowIndex}, ${JSON.stringify(batch).replace(/'/g,"&apos;")})'><td><strong>${batch.batch_no||''}</strong></td><td>${exp}</td><td>${parseFloat(batch.mrp||0).toFixed(2)}</td><td>${batch.qty||0}</td>
            <td><button class="btn btn-sm btn-success py-0" onclick='selectExistingBatch(${rowIndex}, ${JSON.stringify(batch).replace(/'/g,"&apos;")})'><i class="bi bi-check"></i></button></td></tr>`;
    });
    html += `</tbody></table></div>
        <div style="padding:0.75rem;background:#f8f9fa;border-top:1px solid #dee2e6;">
            <button class="btn btn-secondary btn-sm" onclick="closeExistingBatchModal()">Cancel</button>
        </div></div>`;
    document.getElementById('modalContainer').innerHTML = html;
}

function selectExistingBatch(rowIndex, batch) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.querySelector('input[name*="[batch]"]').value = batch.batch_no || '';
        row.querySelector('input[name*="[expiry]"]').value = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month:'2-digit',year:'numeric'}).replace('/','') : '';
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(batch.mrp || 0).toFixed(2);
        row.dataset.batchId = batch.id;
        row.dataset.sRate = parseFloat(batch.s_rate || 0).toFixed(2);
        calculateRowAmount(rowIndex);
    }
    closeExistingBatchModal();
    setTimeout(() => row?.querySelector('input[name*="[qty]"]')?.focus(), 100);
}

function closeExistingBatchModal() {
    document.getElementById('existingBatchBackdrop')?.remove();
    document.getElementById('existingBatchModal')?.remove();
}

function showCreateBatchModal(rowIndex, batchNo, itemData) {
    const html = `<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1060;" id="createBatchBackdrop"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:450px;background:white;border-radius:8px;z-index:1065;" id="createBatchModal">
            <div style="padding:0.75rem;background:#6c757d;color:white;border-radius:8px 8px 0 0;">
                <h6 class="mb-0">Opening New Batch</h6>
                <button onclick="closeCreateBatchModal()" style="position:absolute;top:10px;right:15px;background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
            </div>
            <div style="padding:1rem;background:#f0f0f0;">
                <div class="mb-3"><strong>Item:</strong> <span style="color:#0d6efd;font-weight:bold;">${itemData.name || ''}</span></div>
                <div class="mb-3"><strong>Pack:</strong> <span style="color:#0d6efd;font-weight:bold;">${itemData.packing || ''}</span></div>
                <div class="row mb-2">
                    <div class="col-4"><label class="form-label mb-1"><strong>S.Rate:</strong></label><input type="number" class="form-control form-control-sm" id="newBatchSRate" value="${parseFloat(itemData.s_rate || itemData.mrp || 0).toFixed(2)}" step="0.01"></div>
                    <div class="col-4"><label class="form-label mb-1"><strong>Expiry:</strong></label><input type="text" class="form-control form-control-sm" id="newBatchExpiry" placeholder="MM/YYYY"></div>
                    <div class="col-4"><label class="form-label mb-1"><strong>MRP:</strong></label><input type="number" class="form-control form-control-sm" id="newBatchMRP" value="${parseFloat(itemData.mrp || 0).toFixed(2)}" step="0.01"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><label class="form-label mb-1"><strong>Location:</strong></label><input type="text" class="form-control form-control-sm" id="newBatchLocation" value=""></div>
                    <div class="col-6"><label class="form-label mb-1"><strong>Inclusive:</strong></label><input type="text" class="form-control form-control-sm" id="newBatchInclusive" value="Y" maxlength="1" style="width:50px;"></div>
                </div>
                <input type="hidden" id="newBatchNo" value="${batchNo}">
                <input type="hidden" id="newBatchRowIndex" value="${rowIndex}">
                <input type="hidden" id="newBatchItemId" value="${itemData.id || ''}">
            </div>
            <div style="padding:0.75rem;background:#f8f9fa;border-top:1px solid #dee2e6;">
                <button class="btn btn-primary btn-sm" onclick="createNewBatch()"><i class="bi bi-check-circle me-1"></i> OK</button>
                <button class="btn btn-secondary btn-sm" onclick="closeCreateBatchModal()">Cancel</button>
            </div>
        </div>`;
    document.getElementById('modalContainer').innerHTML = html;
    document.getElementById('newBatchExpiry')?.focus();
}

function createNewBatch() {
    const batchNo = document.getElementById('newBatchNo').value;
    const rowIndex = document.getElementById('newBatchRowIndex').value;
    const itemId = document.getElementById('newBatchItemId').value;
    const sRate = parseFloat(document.getElementById('newBatchSRate').value) || 0;
    const expiry = document.getElementById('newBatchExpiry').value;
    const mrp = parseFloat(document.getElementById('newBatchMRP').value) || 0;
    const location = document.getElementById('newBatchLocation').value;
    
    if (!batchNo || !itemId) { alert('Batch number and item are required'); return; }
    
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.querySelector('input[name*="[batch]"]').value = batchNo;
        row.querySelector('input[name*="[expiry]"]').value = expiry;
        row.querySelector('input[name*="[mrp]"]').value = mrp.toFixed(2);
        row.dataset.batchId = '';
        row.dataset.isNewBatch = 'true';
        row.dataset.sRate = sRate.toFixed(2);
        row.dataset.newBatchData = JSON.stringify({ batch_no: batchNo, expiry: expiry, mrp: mrp, s_rate: sRate, location: location });
    }
    
    closeCreateBatchModal();
    setTimeout(() => row?.querySelector('input[name*="[qty]"]')?.focus(), 100);
}

function closeCreateBatchModal() {
    document.getElementById('createBatchModal')?.remove();
    document.getElementById('createBatchBackdrop')?.remove();
}

function openInsertItemsModal() {
    let html = `<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;" id="itemBackdrop"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:80%;max-width:700px;background:white;border-radius:8px;z-index:1055;" id="itemModal">
            <div style="padding:0.75rem;background:#198754;color:white;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;">
                <h6 class="mb-0"><i class="bi bi-plus-square me-1"></i> Insert Items</h6>
                <button onclick="closeItemModal()" style="background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
            </div>
            <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                <input type="text" class="form-control mb-2" id="itemSearchInput" placeholder="Search item..." onkeyup="filterItems()">
                <table class="table table-sm table-hover"><thead class="table-success"><tr><th>Code</th><th>Name</th><th>Packing</th><th>MRP</th><th>Action</th></tr></thead><tbody id="itemListBody">`;
    itemsData.slice(0,50).forEach(item => {
        html += `<tr ondblclick='selectInsertItem(${JSON.stringify(item).replace(/'/g,"&apos;")})'><td>${item.id}</td><td>${item.name||''}</td><td>${item.packing||''}</td><td class="text-end">${parseFloat(item.mrp||0).toFixed(2)}</td>
            <td><button class="btn btn-sm btn-success py-0" onclick='selectInsertItem(${JSON.stringify(item).replace(/'/g,"&apos;")})'><i class="bi bi-check"></i></button></td></tr>`;
    });
    html += `</tbody></table></div></div>`;
    document.getElementById('modalContainer').innerHTML = html;
}

function filterItems() {
    const search = document.getElementById('itemSearchInput').value.toLowerCase();
    const filtered = itemsData.filter(i => (i.name||'').toLowerCase().includes(search) || (i.id||'').toString().includes(search));
    let html = '';
    filtered.slice(0,50).forEach(item => {
        html += `<tr ondblclick='selectInsertItem(${JSON.stringify(item).replace(/'/g,"&apos;")})'><td>${item.id}</td><td>${item.name||''}</td><td>${item.packing||''}</td><td class="text-end">${parseFloat(item.mrp||0).toFixed(2)}</td>
            <td><button class="btn btn-sm btn-success py-0" onclick='selectInsertItem(${JSON.stringify(item).replace(/'/g,"&apos;")})'><i class="bi bi-check"></i></button></td></tr>`;
    });
    document.getElementById('itemListBody').innerHTML = html;
}

function selectInsertItem(item) {
    pendingItemForBatch = item;
    closeItemModal();
    
    fetch(`{{ url('admin/api/item-batches') }}/${item.id}`)
        .then(r => r.json())
        .then(d => {
            const availableBatches = (d.batches || []).filter(b => (b.qty || 0) > 0);
            if (availableBatches.length > 0) {
                showBatchModal(availableBatches, item);
            } else {
                addItemToTable(item, null);
            }
        })
        .catch(e => {
            console.error(e);
            addItemToTable(item, null);
        });
}

function showBatchModal(batches, item) {
    const totalStock = batches.reduce((sum, b) => sum + (b.qty || 0), 0);
    let html = `<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;" id="batchBackdrop"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:80%;max-width:700px;background:white;border-radius:8px;z-index:1055;" id="batchModal">
            <div style="padding:0.75rem;background:#17a2b8;color:white;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;">
                <h6 class="mb-0"><i class="bi bi-box me-1"></i> Select Batch - ${item.name}</h6>
                <button onclick="closeBatchModalAndReopen()" style="background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
            </div>
            <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                <div class="mb-2"><strong>Total Stock: <span class="text-success">${totalStock}</span></strong></div>
                <table class="table table-sm table-hover"><thead class="table-info"><tr><th>Batch</th><th>Expiry</th><th>MRP</th><th>Avl.Qty</th><th>Action</th></tr></thead><tbody>`;
    batches.forEach(batch => {
        const exp = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month:'2-digit',year:'numeric'}) : '';
        html += `<tr style="background:#d4edda;" ondblclick='selectBatch(${JSON.stringify(batch).replace(/'/g,"&apos;")})'><td><strong>${batch.batch_no||''}</strong></td><td>${exp}</td><td>${parseFloat(batch.mrp||0).toFixed(2)}</td><td><strong>${batch.qty||0}</strong></td>
            <td><button class="btn btn-sm btn-success py-0" onclick='selectBatch(${JSON.stringify(batch).replace(/'/g,"&apos;")})'><i class="bi bi-check"></i></button></td></tr>`;
    });
    html += `</tbody></table></div>
        <div style="padding:0.75rem;background:#f8f9fa;border-top:1px solid #dee2e6;">
            <button class="btn btn-outline-primary btn-sm" onclick="addItemWithoutBatch()"><i class="bi bi-plus"></i> Add Without Batch</button>
            <button class="btn btn-secondary btn-sm" onclick="closeBatchModalAndReopen()">Back</button>
        </div></div>`;
    document.getElementById('modalContainer').innerHTML = html;
}

function selectBatch(batch) {
    closeModal();
    addItemToTable(pendingItemForBatch, batch);
}

function addItemWithoutBatch() {
    closeModal();
    addItemToTable(pendingItemForBatch, null);
}

function addItemToTable(item, batch) {
    addNewRow();
    const rowIndex = currentRowIndex - 1;
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.querySelector('input[name*="[code]"]').value = item.id;
        row.querySelector('input[name*="[name]"]').value = item.name;
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(item.mrp||0).toFixed(2);
        row.dataset.itemId = item.id;
        row.dataset.itemData = JSON.stringify(item);
        
        if (batch) {
            row.querySelector('input[name*="[batch]"]').value = batch.batch_no || '';
            row.querySelector('input[name*="[expiry]"]').value = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month:'2-digit',year:'numeric'}).replace('/','') : '';
            row.dataset.batchId = batch.id;
        }
        
        selectRow(rowIndex);
        row.querySelector('input[name*="[qty]"]')?.focus();
    }
}

function closeItemModal() {
    document.getElementById('itemBackdrop')?.remove();
    document.getElementById('itemModal')?.remove();
}

function closeBatchModalAndReopen() {
    closeModal();
    setTimeout(openInsertItemsModal, 100);
}

function loadInvoiceFromModal(id) {
    closeModal();
    loadTransaction(id);
}

function closeModal() {
    document.getElementById('modalContainer').innerHTML = '';
}
</script>
@endpush
