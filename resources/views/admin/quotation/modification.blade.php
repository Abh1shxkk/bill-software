@extends('layouts.admin')

@section('title', 'Quotation - Modification')

@push('styles')
<style>
    .qt-form { font-size: 11px; }
    .qt-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .qt-form input, .qt-form select, .qt-form textarea { font-size: 11px; padding: 2px 6px; height: 26px; }
    .qt-form textarea { height: auto; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; }
    .table-compact th { background: #90EE90; font-weight: 600; text-align: center; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; }
    .summary-section { background: #ffcccc; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; }
    .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; }
    .batch-modal-backdrop.show { display: block; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; }
    .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #fd7e14; color: white; display: flex; justify-content: space-between; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; text-align: right; }
    .invoice-row:hover { background-color: #fff3cd !important; cursor: pointer; }
</style>
@endpush

@section('content')
<section class="qt-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Quotation - Modification</h4>
                <div class="text-muted small">Load and modify existing quotation</div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning btn-sm" onclick="showLoadModal()">
                    <i class="bi bi-folder2-open me-1"></i> Load Quotation
                </button>
                <a href="{{ route('admin.quotation.transaction') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Quotation
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="qtForm" method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="quotation_id" name="quotation_id" value="">
                    
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="quotation_date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;">T.No :</label>
                                    <input type="text" id="quotation_no" class="form-control readonly-field" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Name :</label>
                                    <select id="customer_id" name="customer_id" class="form-select" onchange="updateCustomerName()">
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="customer_name" name="customer_name" value="">
                                </div>
                                <div class="field-group mb-1">
                                    <label style="width: 60px;">Remarks :</label>
                                    <input type="text" id="remarks" name="remarks" class="form-control">
                                </div>
                                <div class="field-group">
                                    <label style="width: 50px;">Terms :</label>
                                    <textarea id="terms" name="terms" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">Dis % :</label>
                                    <input type="number" id="discount_percent" name="discount_percent" class="form-control text-end" step="0.01" value="0" style="width: 80px;" onchange="calculateTotalAmount()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border rounded p-2 mb-2">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-bordered table-compact">
                                <thead style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th style="width: 70px;">Code</th>
                                        <th style="width: 250px;">Item Name</th>
                                        <th style="width: 60px;">Qty</th>
                                        <th style="width: 80px;">Rate</th>
                                        <th style="width: 80px;">MRP</th>
                                        <th style="width: 100px;">Amount</th>
                                        <th style="width: 40px;">X</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="summary-section mb-2 d-flex justify-content-end">
                        <div class="field-group">
                            <label>Net :</label>
                            <input type="text" id="net_amount" class="form-control readonly-field text-end" style="width: 150px;" value="0.00" readonly>
                        </div>
                    </div>

                    <div class="footer-section">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group mb-1"><label style="width: 40px;">Pack :</label><input type="text" id="packing" class="form-control readonly-field" readonly></div>
                                <div class="field-group"><label style="width: 40px;">Unit :</label><input type="text" id="unit" class="form-control readonly-field" readonly></div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1"><label style="width: 45px;">Comp :</label><input type="text" id="company" class="form-control readonly-field" readonly></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="updateQuotation()"><i class="bi bi-save"></i> Update (End)</button>
                            <button type="button" class="btn btn-danger" onclick="deleteSelectedItem()"><i class="bi bi-trash"></i> Delete Item</button>
                            <button type="button" class="btn btn-primary" onclick="addNewRow()"><i class="bi bi-plus-circle"></i> Insert Item</button>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="cancelQuotation()"><i class="bi bi-x-circle"></i> Cancel Quotation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
let currentRowIndex = 0;
let itemsData = [];
let selectedRowIndex = null;
let loadedQuotationId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    showLoadModal();
});

function loadItems() {
    fetch('{{ route("admin.quotation.getItems") }}')
        .then(response => response.json())
        .then(data => { itemsData = data || []; })
        .catch(error => console.error('Error:', error));
}

function showLoadModal() {
    let html = `
        <div class="batch-modal-backdrop show" id="loadBackdrop"></div>
        <div class="batch-modal show" id="loadModal" style="max-width: 900px;">
            <div class="modal-header-custom">
                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Load Quotation</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeLoadModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by T.No or Customer..." onkeyup="searchQuotations()">
                </div>
                <div class="table-responsive" style="max-height: 350px;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-warning" style="position: sticky; top: 0;">
                            <tr><th>T.No</th><th>Date</th><th>Customer</th><th class="text-end">Amount</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody id="quotationsListBody"><tr><td colspan="6" class="text-center">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeLoadModal()">Close</button>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('searchInput')?.focus();
    loadQuotations();
}

function loadQuotations(search = '') {
    fetch(`{{ route("admin.quotation.getQuotations") }}?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('quotationsListBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No quotations found</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(q => `
                <tr class="invoice-row" onclick="selectQuotation(${q.id})">
                    <td><strong>${q.quotation_no}</strong></td>
                    <td>${new Date(q.quotation_date).toLocaleDateString('en-GB')}</td>
                    <td>${q.customer_name || '-'}</td>
                    <td class="text-end">₹${parseFloat(q.net_amount || 0).toFixed(2)}</td>
                    <td><span class="badge bg-${q.status === 'active' ? 'success' : 'danger'}">${q.status}</span></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-success py-0 px-2" onclick="event.stopPropagation(); selectQuotation(${q.id})"><i class="bi bi-check"></i> Load</button></td>
                </tr>
            `).join('');
        })
        .catch(error => {
            document.getElementById('quotationsListBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading</td></tr>';
        });
}

function searchQuotations() {
    loadQuotations(document.getElementById('searchInput').value);
}

function closeLoadModal() {
    document.getElementById('loadModal')?.remove();
    document.getElementById('loadBackdrop')?.remove();
}

function selectQuotation(id) {
    closeLoadModal();
    fetch(`{{ url('admin/quotation') }}/${id}/edit`, { headers: { 'Accept': 'application/json' } })
        .then(response => response.json())
        .then(data => { if (data && data.id) populateForm(data); else alert('Quotation not found'); })
        .catch(error => { console.error('Error:', error); alert('Error loading quotation'); });
}

function updateCustomerName() {
    const customerSelect = document.getElementById('customer_id');
    const customerNameInput = document.getElementById('customer_name');
    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.name) {
        customerNameInput.value = selectedOption.dataset.name;
    } else {
        customerNameInput.value = '';
    }
}

function populateForm(quotation) {
    loadedQuotationId = quotation.id;
    document.getElementById('quotation_id').value = quotation.id;
    document.getElementById('quotation_date').value = quotation.quotation_date ? quotation.quotation_date.split('T')[0] : '';
    document.getElementById('quotation_no').value = quotation.quotation_no || '';
    
    // Set customer dropdown
    const customerSelect = document.getElementById('customer_id');
    if (quotation.customer_id) {
        customerSelect.value = quotation.customer_id;
        // If customer not found in dropdown, add it
        if (customerSelect.value != quotation.customer_id && quotation.customer_name) {
            const option = document.createElement('option');
            option.value = quotation.customer_id;
            option.textContent = quotation.customer_name;
            option.dataset.name = quotation.customer_name;
            option.selected = true;
            customerSelect.appendChild(option);
        }
    }
    document.getElementById('customer_name').value = quotation.customer_name || '';
    
    document.getElementById('discount_percent').value = quotation.discount_percent || 0;
    document.getElementById('remarks').value = quotation.remarks || '';
    document.getElementById('terms').value = quotation.terms || '';
    document.getElementById('net_amount').value = parseFloat(quotation.net_amount || 0).toFixed(2);
    
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    currentRowIndex = 0;
    
    if (quotation.items && quotation.items.length > 0) {
        quotation.items.forEach(item => addItemRowFromData(item));
    }
    calculateTotalAmount();
}

function addItemRowFromData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.item_code || ''}"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][item_name]" value="${item.item_name || ''}" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][qty]" value="${item.qty || 0}" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][rate]" value="${parseFloat(item.rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][mrp]" value="${parseFloat(item.mrp || 0).toFixed(2)}" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end readonly-field" name="items[${rowIndex}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.item_id || ''}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
        </td>
    `;
    tbody.appendChild(row);
}

function addNewRow() {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onfocus="showItemModal(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][item_name]" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][qty]" value="0" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][rate]" value="0" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][mrp]" value="0" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="">
            <input type="hidden" name="items[${rowIndex}][packing]" value="">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="">
            <input type="hidden" name="items[${rowIndex}][unit]" value="">
        </td>
    `;
    tbody.appendChild(row);
    selectRow(rowIndex);
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) { row.classList.add('row-selected'); selectedRowIndex = rowIndex; }
}

function removeRow(rowIndex) {
    document.getElementById(`row-${rowIndex}`)?.remove();
    calculateTotalAmount();
}

function deleteSelectedItem() {
    if (selectedRowIndex !== null) { removeRow(selectedRowIndex); selectedRowIndex = null; }
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
    row.querySelector('input[name*="[amount]"]').value = (qty * rate).toFixed(2);
    calculateTotalAmount();
}

function calculateTotalAmount() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        total += parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
    });
    const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
    document.getElementById('net_amount').value = (total * (1 - discountPercent / 100)).toFixed(2);
}

function showItemModal(rowIndex) {
    selectedRowIndex = rowIndex;
    let html = `
        <div class="batch-modal-backdrop show" id="itemBackdrop"></div>
        <div class="batch-modal show" id="itemModal">
            <div class="modal-header-custom" style="background: #28a745;">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <input type="text" class="form-control mb-3" id="itemSearchInput" placeholder="Search..." onkeyup="filterItems()">
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-success"><tr><th>Code</th><th>Item Name</th><th>Packing</th><th class="text-end">Rate</th><th class="text-end">MRP</th></tr></thead>
                        <tbody id="itemsListBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom"><button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button></div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput')?.focus();
    renderItemsList();
}

function renderItemsList(filter = '') {
    const tbody = document.getElementById('itemsListBody');
    const filtered = itemsData.filter(item => !filter || item.name?.toLowerCase().includes(filter.toLowerCase()) || item.bar_code?.toLowerCase().includes(filter.toLowerCase()));
    tbody.innerHTML = filtered.map(item => `
        <tr class="item-row" onclick="selectItem(${item.id})" style="cursor: pointer;">
            <td>${item.bar_code || ''}</td><td>${item.name || ''}</td><td>${item.packing || ''}</td>
            <td class="text-end">${parseFloat(item.s_rate || 0).toFixed(2)}</td><td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function filterItems() { renderItemsList(document.getElementById('itemSearchInput').value); }

function selectItem(itemId) {
    const item = itemsData.find(i => i.id === itemId);
    if (!item || selectedRowIndex === null) return;
    const row = document.getElementById(`row-${selectedRowIndex}`);
    if (row) {
        row.querySelector('input[name*="[code]"]').value = item.bar_code || '';
        row.querySelector('input[name*="[item_name]"]').value = item.name || '';
        row.querySelector('input[name*="[rate]"]').value = parseFloat(item.s_rate || 0).toFixed(2);
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(item.mrp || 0).toFixed(2);
        row.querySelector('input[name*="[item_id]"]').value = item.id;
        row.querySelector('input[name*="[packing]"]').value = item.packing || '';
        row.querySelector('input[name*="[company_name]"]').value = item.company_name || '';
        row.querySelector('input[name*="[unit]"]').value = item.unit || '';
        calculateRowAmount(selectedRowIndex);
    }
    closeItemModal();
    row.querySelector('input[name*="[qty]"]')?.focus();
}

function closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemBackdrop')?.remove();
}

function updateQuotation() {
    if (!loadedQuotationId) { alert('Please load a quotation first'); return; }
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const itemName = row.querySelector('input[name*="[item_name]"]')?.value;
        if (itemName) {
            items.push({
                item_id: row.querySelector('input[name*="[item_id]"]')?.value || null,
                code: row.querySelector('input[name*="[code]"]')?.value || '',
                item_name: itemName,
                qty: row.querySelector('input[name*="[qty]"]')?.value || 0,
                rate: row.querySelector('input[name*="[rate]"]')?.value || 0,
                mrp: row.querySelector('input[name*="[mrp]"]')?.value || 0,
                packing: row.querySelector('input[name*="[packing]"]')?.value || '',
                company_name: row.querySelector('input[name*="[company_name]"]')?.value || '',
                unit: row.querySelector('input[name*="[unit]"]')?.value || '',
            });
        }
    });
    if (items.length === 0) { alert('Please add at least one item'); return; }
    
    fetch(`{{ url('admin/quotation') }}/${loadedQuotationId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            date: document.getElementById('quotation_date').value,
            customer_id: document.getElementById('customer_id').value,
            customer_name: document.getElementById('customer_name').value,
            discount_percent: document.getElementById('discount_percent').value,
            remarks: document.getElementById('remarks').value,
            terms: document.getElementById('terms').value,
            items: items
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) { alert('Quotation updated: ' + result.quotation_no); window.location.href = '{{ route("admin.quotation.index") }}'; }
        else { alert('Error: ' + result.message); }
    })
    .catch(error => { console.error('Error:', error); alert('Error updating quotation'); });
}

function cancelQuotation() {
    if (!loadedQuotationId) { alert('Please load a quotation first'); return; }
    if (!confirm('Are you sure you want to cancel this quotation?')) return;
    
    fetch(`{{ url('admin/quotation') }}/${loadedQuotationId}/cancel`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) { alert('Quotation cancelled'); window.location.href = '{{ route("admin.quotation.index") }}'; }
        else { alert('Error: ' + result.message); }
    })
    .catch(error => { console.error('Error:', error); alert('Error cancelling quotation'); });
}
</script>
@endpush
