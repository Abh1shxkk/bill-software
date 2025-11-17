@extends('layouts.admin')

@section('title', 'Sample Issued - Modification')

@push('styles')
<style>
    .si-form { font-size: 11px; }
    .si-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .si-form input, .si-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #ffb6c1; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .summary-section { background: #ffcccc; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; border: 2px solid #007bff !important; }
    .row-complete { background-color: #d4edda !important; }
    .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .batch-modal-backdrop.show { display: block; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #0d6efd; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; }
    .item-row:hover { background-color: #e3f2fd !important; cursor: pointer; }
    .invoice-row:hover { background-color: #fff3cd !important; cursor: pointer; }
    .no-invoice-loaded { opacity: 0.5; pointer-events: none; }
</style>
@endpush

@section('content')
<section class="si-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Sample Issued - Modification</h4>
                <div class="text-muted small">Load and modify existing sample issued transaction</div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning btn-sm" onclick="showLoadInvoiceModal()">
                    <i class="bi bi-folder2-open me-1"></i> Load Invoice
                </button>
                <a href="{{ route('admin.sample-issued.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
                <a href="{{ route('admin.sample-issued.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Transaction
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body" id="formContainer">
                <div class="text-center py-5" id="noInvoiceMessage">
                    <i class="bi bi-folder2-open text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">No Invoice Loaded</h5>
                    <p class="text-muted">Click "Load Invoice" button to select and load a transaction for modification.</p>
                    <button type="button" class="btn btn-warning" onclick="showLoadInvoiceModal()">
                        <i class="bi bi-folder2-open me-1"></i> Load Invoice
                    </button>
                </div>

                <form id="siForm" method="POST" autocomplete="off" style="display: none;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="transaction_id" name="transaction_id" value="">
                    
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" onchange="updateDayName()" required>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;"></label>
                                    <input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" readonly style="width: 100px;">
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 50px;">Trn.No :</label>
                                    <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 70px;">Party Type :</label>
                                    <select id="party_type" name="party_type" class="form-select" onchange="loadPartyList()">
                                        @foreach($partyTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Name :</label>
                                    <select id="party_id" name="party_id" class="form-select" onchange="updatePartyName()">
                                        <option value="">-- Select --</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="party_name" name="party_name">
                                </div>
                                <div class="field-group">
                                    <label style="width: 60px;">Remarks :</label>
                                    <input type="text" id="remarks" name="remarks" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 30px;">On :</label>
                                    <input type="text" id="on_field" name="on_field" class="form-control" style="width: 50px;">
                                    <label style="width: 35px;">Rate :</label>
                                    <input type="number" id="rate" name="rate" class="form-control text-end" step="0.01" value="0" style="width: 70px;">
                                </div>
                                <div class="field-group">
                                    <label style="width: 30px;">Tag :</label>
                                    <input type="text" id="tag" name="tag" class="form-control" style="width: 80px;">
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">GR No. :</label>
                                    <input type="text" id="gr_no" name="gr_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">GR Date :</label>
                                    <input type="date" id="gr_date" name="gr_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="field-group">
                                    <label style="width: 40px;">Cases :</label>
                                    <input type="number" id="cases" name="cases" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 80px;">Road Permit :</label>
                                    <input type="text" id="road_permit_no" name="road_permit_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">Truck No. :</label>
                                    <input type="text" id="truck_no" name="truck_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 70px;">Transport :</label>
                                    <input type="text" id="transport" name="transport" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="bg-white border rounded p-2 mb-2">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" id="itemsTableContainer">
                            <table class="table table-bordered table-compact">
                                <thead style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th style="width: 70px;">Code</th>
                                        <th style="width: 200px;">Item Name</th>
                                        <th style="width: 90px;">Batch</th>
                                        <th style="width: 70px;">Expiry</th>
                                        <th style="width: 60px;">Qty</th>
                                        <th style="width: 80px;">Rate</th>
                                        <th style="width: 100px;">Amount</th>
                                        <th style="width: 40px;">X</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2 d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                                <i class="bi bi-plus-circle"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="showItemSelectionModal()">
                                <i class="bi bi-search"></i> Add Items
                            </button>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="summary-section mb-2 d-flex justify-content-end">
                        <div class="field-group">
                            <label>Net :</label>
                            <input type="text" id="net_amount" name="net_amount" class="form-control readonly-field text-end" style="width: 150px;" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Footer Section -->
                    <div class="footer-section">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 40px;">Pack :</label>
                                    <input type="text" id="packing" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 40px;">Unit :</label>
                                    <input type="text" id="unit" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;">Cl.Qty :</label>
                                    <input type="text" id="cl_qty" class="form-control readonly-field text-end" value="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-8"></div>
                            <div class="col-md-2 text-end">
                                <div class="field-group justify-content-end">
                                    <label style="width: 40px;">Srlno :</label>
                                    <input type="text" id="srlno" class="form-control text-end" style="width: 80px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="updateTransaction()">
                                <i class="bi bi-save"></i> Save (End)
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteSelectedItem()">
                                <i class="bi bi-trash"></i> Delete Item
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="cancelModification()">
                                <i class="bi bi-x-circle"></i> Cancel Modification
                            </button>
                        </div>
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
let loadedTransactionId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
});

function updateDayName() {
    const dateInput = document.getElementById('transaction_date');
    const dayInput = document.getElementById('day_name');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayInput.value = days[date.getDay()];
    }
}

function loadItems() {
    fetch('{{ route("admin.sample-issued.getItems") }}')
        .then(response => response.json())
        .then(data => {
            itemsData = data || [];
        })
        .catch(error => console.error('Error loading items:', error));
}

// ============ LOAD INVOICE MODAL ============
function showLoadInvoiceModal() {
    let html = `
        <div class="batch-modal-backdrop show" id="loadInvoiceBackdrop"></div>
        <div class="batch-modal show" id="loadInvoiceModal" style="max-width: 900px;">
            <div class="modal-header-custom" style="background: #fd7e14;">
                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Load Invoice</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeLoadInvoiceModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="invoiceSearchInput" placeholder="Search by TRN No. or Party Name..." onkeyup="searchInvoices()">
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-warning" style="position: sticky; top: 0;">
                            <tr>
                                <th>TRN No.</th>
                                <th>Date</th>
                                <th>Party Type</th>
                                <th>Party Name</th>
                                <th class="text-end">Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="invoicesListBody">
                            <tr><td colspan="6" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeLoadInvoiceModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('invoiceSearchInput')?.focus();
    loadPastInvoices();
}

function loadPastInvoices(search = '') {
    fetch(`{{ route("admin.sample-issued.getPastInvoices") }}?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('invoicesListBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No invoices found</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.map(inv => `
                <tr class="invoice-row" onclick="selectInvoice(${inv.id})">
                    <td><strong>${inv.trn_no}</strong></td>
                    <td>${inv.transaction_date ? new Date(inv.transaction_date).toLocaleDateString('en-GB') : '-'}</td>
                    <td><span class="badge bg-info">${inv.party_type || '-'}</span></td>
                    <td>${inv.party_name || '-'}</td>
                    <td class="text-end">₹${parseFloat(inv.net_amount || 0).toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success py-0 px-2" onclick="event.stopPropagation(); selectInvoice(${inv.id})">
                            <i class="bi bi-check"></i> Load
                        </button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading invoices:', error);
            document.getElementById('invoicesListBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading invoices</td></tr>';
        });
}

function searchInvoices() {
    const search = document.getElementById('invoiceSearchInput').value;
    loadPastInvoices(search);
}

function closeLoadInvoiceModal() {
    document.getElementById('loadInvoiceModal')?.remove();
    document.getElementById('loadInvoiceBackdrop')?.remove();
}

function selectInvoice(id) {
    closeLoadInvoiceModal();
    loadTransactionData(id);
}

function loadTransactionData(id) {
    fetch(`{{ url('admin/sample-issued/load-by-trn-no') }}?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success && !data.transaction) {
                // Try another way
                fetch(`{{ url('admin/sample-issued') }}/${id}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(d => populateForm(d))
                .catch(e => alert('Error loading transaction'));
                return;
            }
            populateForm(data.transaction || data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading transaction');
        });
}

function populateForm(transaction) {
    // Hide no invoice message, show form
    document.getElementById('noInvoiceMessage').style.display = 'none';
    document.getElementById('siForm').style.display = 'block';
    
    loadedTransactionId = transaction.id;
    document.getElementById('transaction_id').value = transaction.id;
    
    // Populate header fields
    document.getElementById('transaction_date').value = transaction.transaction_date ? transaction.transaction_date.split('T')[0] : '';
    document.getElementById('day_name').value = transaction.day_name || '';
    document.getElementById('trn_no').value = transaction.trn_no || '';
    document.getElementById('party_type').value = transaction.party_type || '';
    document.getElementById('party_name').value = transaction.party_name || '';
    document.getElementById('remarks').value = transaction.remarks || '';
    document.getElementById('on_field').value = transaction.on_field || '';
    document.getElementById('rate').value = transaction.rate || 0;
    document.getElementById('tag').value = transaction.tag || '';
    document.getElementById('gr_no').value = transaction.gr_no || '';
    document.getElementById('gr_date').value = transaction.gr_date ? transaction.gr_date.split('T')[0] : '';
    document.getElementById('cases').value = transaction.cases || 0;
    document.getElementById('road_permit_no').value = transaction.road_permit_no || '';
    document.getElementById('truck_no').value = transaction.truck_no || '';
    document.getElementById('transport').value = transaction.transport || '';
    document.getElementById('net_amount').value = parseFloat(transaction.net_amount || 0).toFixed(2);
    
    // Load party list and set party_id
    loadPartyList().then(() => {
        const partySelect = document.getElementById('party_id');
        if (transaction.party_id) {
            partySelect.value = transaction.party_id;
        }
    });
    
    // Populate items
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    currentRowIndex = 0;
    
    if (transaction.items && transaction.items.length > 0) {
        transaction.items.forEach((item, index) => {
            addItemRow(item);
        });
    }
    
    calculateTotalAmount();
}

function addItemRow(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.item_id;
    row.onclick = function() { selectRow(rowIndex); };
    row.className = 'row-complete';
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.item_code || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.item_name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${item.batch_no || ''}"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${item.expiry || ''}"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${item.qty || 0}" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" value="${parseFloat(item.rate || 0).toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.item_id || ''}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${item.batch_id || ''}">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
        <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${item.hsn_code || ''}">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="${item.mrp || 0}">
    `;
    
    tbody.appendChild(row);
}

// ============ PARTY DROPDOWN FUNCTIONS ============
function loadPartyList() {
    return new Promise((resolve) => {
        const partyType = document.getElementById('party_type').value;
        const partySelect = document.getElementById('party_id');
        
        partySelect.innerHTML = '<option value="">-- Loading... --</option>';
        
        fetch(`{{ url('admin/sample-issued/get-party-list') }}?party_type=${partyType}`)
            .then(response => response.json())
            .then(data => {
                partySelect.innerHTML = '<option value="">-- Select --</option>';
                data.forEach(party => {
                    const option = document.createElement('option');
                    option.value = party.id;
                    option.textContent = party.name;
                    option.dataset.name = party.name;
                    partySelect.appendChild(option);
                });
                resolve();
            })
            .catch(error => {
                console.error('Error loading party list:', error);
                partySelect.innerHTML = '<option value="">-- Error loading --</option>';
                resolve();
            });
    });
}

function updatePartyName() {
    const partySelect = document.getElementById('party_id');
    const partyNameInput = document.getElementById('party_name');
    const selectedOption = partySelect.options[partySelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.name) {
        partyNameInput.value = selectedOption.dataset.name;
    } else {
        partyNameInput.value = '';
    }
}

// ============ ITEM SELECTION MODAL ============
function showItemSelectionModal() {
    let html = `
        <div class="batch-modal-backdrop show" id="itemModalBackdrop"></div>
        <div class="batch-modal show" id="itemModal" style="max-width: 900px;">
            <div class="modal-header-custom" style="background: #28a745;">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by code or name..." onkeyup="filterItems()">
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-success" style="position: sticky; top: 0;">
                            <tr><th>Code</th><th>Item Name</th><th>Packing</th><th>S.Rate</th><th>MRP</th></tr>
                        </thead>
                        <tbody id="itemsListBody">` +
    itemsData.map(item => `
        <tr class="item-row" onclick="selectItemFromModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">
            <td><strong>${item.id || ''}</strong></td>
            <td>${item.name || ''}</td>
            <td>${item.packing || ''}</td>
            <td class="text-end">${parseFloat(item.s_rate || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
        </tr>`).join('') +
    `</tbody></table></div></div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput')?.focus();
}

function filterItems() {
    const search = document.getElementById('itemSearchInput').value.toLowerCase();
    document.querySelectorAll('#itemsListBody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
}

function closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemModalBackdrop')?.remove();
}

function selectItemFromModal(item) {
    closeItemModal();
    
    const newItem = {
        item_id: item.id,
        item_code: item.id,
        item_name: item.name,
        batch_no: '',
        expiry: '',
        qty: 0,
        rate: item.s_rate || 0,
        amount: 0,
        packing: item.packing || '',
        company_name: item.company_name || '',
        hsn_code: item.hsn_code || '',
        mrp: item.mrp || 0
    };
    
    addItemRow(newItem);
    selectRow(currentRowIndex - 1);
}

function addNewRow() {
    const newItem = {
        item_id: '',
        item_code: '',
        item_name: '',
        batch_no: '',
        expiry: '',
        qty: 0,
        rate: 0,
        amount: 0
    };
    addItemRow(newItem);
    selectRow(currentRowIndex - 1);
    document.querySelector(`#row-${currentRowIndex - 1} input[name*="[code]"]`)?.focus();
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
    }
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
    row.querySelector('input[name*="[amount]"]').value = (qty * rate).toFixed(2);
    
    calculateTotalAmount();
}

function calculateTotalAmount() {
    let total = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        total += parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
    });
    document.getElementById('net_amount').value = total.toFixed(2);
}

function removeRow(rowIndex) {
    document.getElementById(`row-${rowIndex}`)?.remove();
    calculateTotalAmount();
}

function deleteSelectedItem() {
    if (selectedRowIndex !== null) {
        removeRow(selectedRowIndex);
        selectedRowIndex = null;
    } else {
        alert('Please select an item to delete');
    }
}

function updateTransaction() {
    if (!loadedTransactionId) {
        alert('No transaction loaded');
        return;
    }
    
    const form = document.getElementById('siForm');
    const formData = new FormData(form);
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    let totalQty = 0;
    rows.forEach(row => {
        totalQty += parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    });
    formData.append('total_qty', totalQty);
    formData.append('total_amount', document.getElementById('net_amount').value);
    
    fetch(`{{ url('admin/sample-issued') }}/${loadedTransactionId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("admin.sample-issued.index") }}';
        } else {
            alert(data.message || 'Error updating transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating transaction');
    });
}

function cancelModification() {
    if (confirm('Are you sure you want to cancel? Unsaved changes will be lost.')) {
        window.location.href = '{{ route("admin.sample-issued.index") }}';
    }
}
</script>
@endpush
