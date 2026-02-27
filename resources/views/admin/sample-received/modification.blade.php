@extends('layouts.admin')

@section('title', 'Sample Received - Modification')

@push('styles')
<style>
    .sr-form { font-size: 11px; }
    .sr-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .sr-form input, .sr-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #90EE90; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .summary-section { background: #d4edda; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; border: 2px solid #007bff !important; }
    .row-complete { background-color: #d4edda !important; }
    .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .batch-modal-backdrop.show { display: block !important; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .batch-modal.show { display: block !important; }
    .modal-header-custom { padding: 1rem; background: #28a745; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; }
    .item-row:hover { background-color: #e3f2fd !important; cursor: pointer; }
    .invoice-row:hover { background-color: #d4edda !important; cursor: pointer; }
    /* Focus indicator for keyboard navigation */
    .sr-form input:focus, .sr-form select:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.35) !important;
        outline: none !important;
        background-color: #fffde7 !important;
    }
    .sr-form input.readonly-field:focus {
        background-color: #e9ecef !important;
        box-shadow: none !important;
        border-color: #ced4da !important;
    }
    .table-compact input:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.35) !important;
        outline: none !important;
        background-color: #fffde7 !important;
    }
</style>
@endpush

@section('content')
<section class="sr-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Sample Received - Modification</h4>
                <div class="text-muted small">Load and modify existing sample received transaction</div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="loadInvoiceBtn" class="btn btn-warning btn-sm" onclick="showLoadInvoiceModal()" onkeydown="handleLoadInvoiceBtnKeydown(event)">
                    <i class="bi bi-folder2-open me-1"></i> Load Invoice
                </button>
                <a href="{{ route('admin.sample-received.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
                <a href="{{ route('admin.sample-received.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Transaction
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="srForm" method="POST" autocomplete="off" onsubmit="return false;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="transaction_id" name="transaction_id" value="">
                    
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="updateDayName()" required>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;"></label>
                                    <input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" value="{{ date('l') }}" readonly style="width: 100px;">
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 50px;">Trn.No :</label>
                                    <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 70px;">Party Type :</label>
                                    <input type="hidden" id="party_type" name="party_type" value="{{ array_key_first($partyTypes) }}">
                                    <div style="position:relative;flex:1;">
                                        <input type="text" id="partyTypeInput" class="form-control" autocomplete="off" readonly
                                            placeholder="Select type..."
                                            value="{{ collect($partyTypes)->first() }}"
                                            onfocus="_ptShowDrop()" onclick="_ptShowDrop()">
                                        <div id="partyTypeDrop" style="display:none;position:absolute;top:100%;left:0;right:0;
                                            max-height:180px;overflow-y:auto;background:#fff;border:1px solid #ccc;
                                            box-shadow:0 4px 8px rgba(0,0,0,.15);z-index:9999;font-size:12px;">
                                            @foreach($partyTypes as $key => $label)
                                            <div class="pt-opt" data-value="{{ $key }}"
                                                style="padding:5px 10px;cursor:pointer;border-bottom:1px solid #f0f0f0;"
                                                onmousedown="event.preventDefault();_ptSelect(this)">{{ $label }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Name :</label>
                                    <input type="hidden" id="party_id" name="party_id" value="">
                                    <input type="hidden" id="party_name" name="party_name" value="">
                                    <div style="position:relative;flex:1;">
                                        <input type="text" id="partyNameInput" class="form-control" autocomplete="off"
                                            placeholder="Search party..."
                                            oninput="_pnFilter()" onfocus="_pnShowDrop()">
                                        <div id="partyNameDrop" style="display:none;position:absolute;top:100%;left:0;right:0;
                                            max-height:200px;overflow-y:auto;background:#fff;border:1px solid #ccc;
                                            box-shadow:0 4px 8px rgba(0,0,0,.15);z-index:9999;font-size:12px;"></div>
                                    </div>
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
                                    <input type="date" id="gr_date" name="gr_date" class="form-control" value="{{ date('Y-m-d') }}">
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
                                <i class="bi bi-save"></i> Update (End)
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

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'sampleReceivedModItemModal',
    'module' => 'sample-received-mod',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'sampleReceivedModBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'sampleReceivedModBatchModal',
    'module' => 'sample-received-mod',
    'showOnlyAvailable' => false,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])

@endsection

@push('scripts')
<script>
let currentRowIndex = 0;
let itemsData = [];
let selectedRowIndex = null;
let loadedTransactionId = null;
let originalItems = [];

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    setTimeout(() => {
        document.getElementById('transaction_date')?.focus();
    }, 150);
    
    // Auto-load transaction if ID is passed in URL
    const urlParams = new URLSearchParams(window.location.search);
    const loadId = urlParams.get('load');
    if (loadId) {
        // Don't load party list initially if we're loading a transaction
        setTimeout(() => loadTransactionData(loadId), 300);
    } else {
        // Only load party list if not loading a transaction
        loadPartyList();
    }
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
    fetch('{{ route("admin.sample-received.getItems") }}')
        .then(response => response.json())
        .then(data => {
            itemsData = data || [];
        })
        .catch(error => console.error('Error loading items:', error));
}

// ============ LOAD INVOICE MODAL ============
let selectedInvoiceIndex = -1;
let invoiceRows = [];

// Handle keyboard on Load Invoice button
function handleLoadInvoiceBtnKeydown(event) {
    console.log('🎹 Load Invoice button keydown:', event.key);
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        console.log('✅ Opening Load Invoice modal...');
        showLoadInvoiceModal();
    }
}

function showLoadInvoiceModal() {
    console.log('🔗 showLoadInvoiceModal called');
    
    // Remove any existing modal first
    const existingModal = document.getElementById('loadInvoiceModal');
    const existingBackdrop = document.getElementById('loadInvoiceBackdrop');
    if (existingModal) existingModal.remove();
    if (existingBackdrop) existingBackdrop.remove();
    
    let html = `
        <div class="batch-modal-backdrop show" id="loadInvoiceBackdrop" style="display: block !important;"></div>
        <div class="batch-modal show" id="loadInvoiceModal" style="max-width: 900px; display: block !important;">
            <div class="modal-header-custom" style="background: #fd7e14;">
                <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Load Invoice</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeLoadInvoiceModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="invoiceSearchInput" placeholder="Search by TRN No. or Party Name...">
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
    console.log('✅ Modal HTML inserted');
    
    // Use setTimeout to ensure DOM is ready
    setTimeout(() => {
        const modal = document.getElementById('loadInvoiceModal');
        const backdrop = document.getElementById('loadInvoiceBackdrop');
        console.log('Modal element check details:', {
            modalEl: modal ? 'found' : 'NOT FOUND',
            backdropEl: backdrop ? 'found' : 'NOT FOUND',
            modalDisplay: modal ? window.getComputedStyle(modal).display : 'N/A',
            backdropDisplay: backdrop ? window.getComputedStyle(backdrop).display : 'N/A'
        });
        
        _invHil = -1;
        const searchInput = document.getElementById('invoiceSearchInput');
        if (searchInput) {
            // Only oninput for text search — NO keydown handler here
            // All arrow/enter/escape handled by window capture handler below
            searchInput.addEventListener('input', searchInvoices, false);
            searchInput.focus();
        }
    }, 50);
    
    loadPastInvoices();
}

// (legacy handleInvoiceSearchKeydown removed — window capture handler below handles all nav)

// (legacy highlightInvoiceRow removed — _invHilAt() handles all row highlighting)

function loadPastInvoices(search = '') {
    fetch(`{{ route("admin.sample-received.getPastInvoices") }}?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('invoicesListBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No invoices found</td></tr>';
                invoiceRows = [];
                selectedInvoiceIndex = -1;
                return;
            }
            
            tbody.innerHTML = data.map(inv => `
                <tr class="invoice-row" data-invoice-id="${inv.id}" onclick="selectInvoice(${inv.id})" style="cursor: pointer;">
                    <td><strong>${inv.trn_no}</strong></td>
                    <td>${inv.transaction_date ? new Date(inv.transaction_date).toLocaleDateString('en-GB') : '-'}</td>
                    <td><span class="badge bg-success">${inv.party_type || '-'}</span></td>
                    <td>${inv.party_name || '-'}</td>
                    <td class="text-end">₹${parseFloat(inv.net_amount || 0).toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success py-0 px-2" onclick="event.stopPropagation(); selectInvoice(${inv.id})">
                            <i class="bi bi-check"></i> Load
                        </button>
                    </td>
                </tr>
            `).join('');
            
            // Highlight first row via unified handler
            invoiceRows = Array.from(document.querySelectorAll('.invoice-row'));
            setTimeout(() => _invHilAt(0), 50);
        })
        .catch(error => {
            console.error('Error loading invoices:', error);
            document.getElementById('invoicesListBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading invoices</td></tr>';
            invoiceRows = [];
            selectedInvoiceIndex = -1;
        });
}

function searchInvoices() {
    const search = document.getElementById('invoiceSearchInput')?.value || '';
    loadPastInvoices(search);
}

function closeLoadInvoiceModal() {
    document.getElementById('loadInvoiceModal')?.remove();
    document.getElementById('loadInvoiceBackdrop')?.remove();
}

function selectInvoice(id) {
    closeLoadInvoiceModal();
    loadTransactionData(id);
    // Focus on date field after invoice is loaded
    setTimeout(() => {
        const dateField = document.getElementById('transaction_date');
        if (dateField) {
            dateField.focus();
            dateField.select();
            console.log('✅ Date field focused after loading invoice');
        }
    }, 500);
}

function loadTransactionData(id) {
    fetch(`{{ url('admin/sample-received') }}/${id}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data && data.id) {
            populateForm(data);
        } else {
            alert('Transaction not found');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading transaction');
    });
}

function populateForm(transaction) {
    loadedTransactionId = transaction.id;
    document.getElementById('transaction_id').value = transaction.id;
    originalItems = transaction.items ? JSON.parse(JSON.stringify(transaction.items)) : [];
    
    document.getElementById('transaction_date').value = transaction.transaction_date ? transaction.transaction_date.split('T')[0] : '';
    updateDayName();
    document.getElementById('trn_no').value = transaction.trn_no || '';
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
    document.getElementById('party_name').value = transaction.party_name || '';
    
    if (transaction.party_type) {
        document.getElementById('party_type').value = transaction.party_type;
        // Set display label
        const ptOpt = document.querySelector('#partyTypeDrop .pt-opt[data-value="' + transaction.party_type + '"]');
        document.getElementById('partyTypeInput').value = ptOpt ? ptOpt.textContent.trim() : transaction.party_type;
    }
    if (transaction.party_id && transaction.party_name) {
        document.getElementById('party_id').value = transaction.party_id;
        document.getElementById('party_name').value = transaction.party_name;
        document.getElementById('partyNameInput').value = transaction.party_name;
        // Preload party list for this type
        if (transaction.party_type) {
            fetch(`{{ url('admin/sample-received/get-party-list') }}?party_type=${transaction.party_type}`)
                .then(r=>r.json()).then(d => { _pnAllItems = d||[]; }).catch(()=>{});
        }
    }
    
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    currentRowIndex = 0;
    
    if (transaction.items && transaction.items.length > 0) {
        transaction.items.forEach((item, index) => {
            addItemRowFromData(item);
            if (index === 0) {
                document.getElementById('packing').value = item.packing || '';
                document.getElementById('unit').value = item.unit || '';
                document.getElementById('cl_qty').value = '0';
            }
        });
        if (transaction.items.length > 0) {
            selectRow(0);
        }
    }
    
    calculateTotalAmount();
    // After load: focus first row batch field
    setTimeout(function() {
        const firstRow = document.querySelector('#itemsTableBody tr');
        if (firstRow) {
            const idx = parseInt(firstRow.id.replace('row-', ''));
            selectRow(idx);
            const batch = firstRow.querySelector('input[name*="[batch]"]');
            if (batch) { batch.focus(); batch.select(); }
        } else {
            document.getElementById('transaction_date')?.focus();
        }
    }, 200);
}

function addItemRowFromData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const batchId = item.batch_id || item.batchId || '';
    const itemId = item.item_id || item.itemId || '';
    const itemCode = item.item_code || item.itemCode || itemId || '';
    const itemName = item.item_name || item.itemName || item.name || '';
    const batchNo = item.batch_no || item.batchNo || item.batch || '';
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = itemId;
    row.dataset.itemData = JSON.stringify({
        packing: item.packing || '',
        unit: item.unit || '',
        qty: item.qty || 0,
        name: itemName,
        id: itemId
    });
    if (batchId) {
        row.dataset.batchId = batchId;
        row.dataset.batchData = JSON.stringify({
            qty: 0,
            batch_no: batchNo
        });
    }
    row.onclick = function() { selectRow(rowIndex); };
    row.className = 'row-complete';
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemCode}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemName}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchNo}" onkeydown="handleBatchKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${item.expiry || ''}" onkeydown="handleExpiryKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${item.qty || 0}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" value="${parseFloat(item.rate || 0).toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${itemId}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchId}">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
        <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${item.hsn_code || ''}">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="${item.mrp || 0}">
        <input type="hidden" name="items[${rowIndex}][original_batch_id]" value="${batchId}">
        <input type="hidden" name="items[${rowIndex}][original_qty]" value="${item.qty || 0}">
    `;
    
    tbody.appendChild(row);
}

// ============ PARTY DROPDOWN FUNCTIONS ============
function loadPartyList(preserveSelection = false, autoFocus = false) {
    return new Promise((resolve) => {
        const partyType = document.getElementById('party_type').value;
        const partySelect = document.getElementById('party_id');
        
        // Preserve current selection if needed
        const currentValue = partySelect.value;
        const currentText = partySelect.options[partySelect.selectedIndex]?.text || '';
        const currentDataName = partySelect.options[partySelect.selectedIndex]?.dataset?.name || '';
        
        if (!preserveSelection) {
            partySelect.innerHTML = '<option value="">-- Loading... --</option>';
        }
        
        fetch(`{{ url('admin/sample-received/get-party-list') }}?party_type=${partyType}`)
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
                
                // Restore selection if it was preserved
                if (preserveSelection && currentValue) {
                    partySelect.value = currentValue;
                    // If not found in list, add it back
                    if (partySelect.value != currentValue && currentText) {
                        const option = document.createElement('option');
                        option.value = currentValue;
                        option.textContent = currentText;
                        option.dataset.name = currentDataName || currentText;
                        option.selected = true;
                        partySelect.appendChild(option);
                    }
                }
                
                // Auto-focus on party name dropdown only if explicitly requested (user changed party type)
                if (autoFocus) {
                    setTimeout(() => {
                        partySelect.focus();
                        console.log('✅ Party Name dropdown auto-focused (user changed Party Type)');
                    }, 100);
                }
                
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

// ============ REUSABLE MODAL BRIDGE FUNCTION ============
// This function is called by the reusable modal components
function onItemBatchSelectedFromModal(itemData, batchData) {
    console.log('🎯 Sample Received Mod: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Sample Received Mod: Invalid item data received');
        return;
    }
    
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = itemData.id;
    row.dataset.itemData = JSON.stringify(itemData);
    if (batchData && batchData.id) {
        row.dataset.batchId = batchData.id;
        row.dataset.batchData = JSON.stringify(batchData);
    }
    row.onclick = function() { selectRow(rowIndex); };
    row.className = 'row-complete';
    
    // Complete row HTML with all fields
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemData.id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemData.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchData?.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${batchData?.expiry_formatted || ''}" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${batchData?.qty || 0}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" value="${parseFloat(batchData?.s_rate || itemData.s_rate || 0).toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="0.00" step="0.01" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${itemData.id}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchData?.id || ''}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${itemData.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${itemData.unit || ''}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${itemData.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${itemData.hsn_code || ''}">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="${itemData.mrp || 0}">
        </td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    
    // Update footer with item/batch details
    document.getElementById('packing').value = itemData.packing || '';
    document.getElementById('unit').value = itemData.unit || '';
    document.getElementById('cl_qty').value = batchData?.qty || 0;
    
    // Calculate row amount
    calculateRowAmount(rowIndex);
    
    // Focus on qty field after creating the row
    setTimeout(() => {
        const qtyField = row.querySelector('input[name*="[qty]"]');
        if (qtyField) { qtyField.focus(); qtyField.select(); }
    }, 100);
    
    console.log('✅ Sample Received Mod: Row created successfully', {rowIndex, itemId: itemData.id, batchId: batchData?.id});
}

// ============ SHOW ITEM SELECTION MODAL (BRIDGE TO REUSABLE COMPONENT) ============
function showItemSelectionModal() {
    console.log('🔗 Sample Received Mod: showItemSelectionModal called - opening reusable modal');
    
    // Check if modal functions exist
    if (typeof window.openItemModal_sampleReceivedModItemModal === 'function') {
        window.openItemModal_sampleReceivedModItemModal();
    } else {
        console.error('❌ Sample Received Mod: openItemModal_sampleReceivedModItemModal function not found. Modal component may not be loaded.');
        alert('Error: Modal component not loaded. Please refresh the page.');
    }
}

// ============ LEGACY ITEM SELECTION MODAL (RENAMED TO AVOID CONFLICT) ============
function _legacy_showItemSelectionModal() {
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
                            <tr>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Packing</th>
                                <th>S.Rate</th>
                                <th>MRP</th>
                            </tr>
                        </thead>
                        <tbody id="itemsListBody">`;
    
    itemsData.forEach(item => {
        html += `
            <tr class="item-row" onclick="_legacy_selectItemFromModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                <td><strong>${item.id || ''}</strong></td>
                <td>${item.name || ''}</td>
                <td>${item.packing || ''}</td>
                <td class="text-end">${parseFloat(item.s_rate || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
            </tr>`;
    });
    
    html += `</tbody></table></div></div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput')?.focus();
}

function filterItems() {
    const search = document.getElementById('itemSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#itemsListBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
}

function closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemModalBackdrop')?.remove();
}

function _legacy_selectItemFromModal(item) {
    closeItemModal();
    
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify(item);
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" value="${parseFloat(item.s_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || '1'}">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
        <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${item.hsn_code || ''}">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="${item.mrp || 0}">
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    _legacy_showBatchSelectionForItem(item, rowIndex);
}

function _legacy_showBatchSelectionForItem(item, rowIndex) {
    fetch(`{{ url('admin/api/item-batches') }}/${item.id}`)
        .then(response => response.json())
        .then(data => {
            const batches = data.batches || data || [];
            _legacy_showBatchSelectionModal(Array.isArray(batches) ? batches : [], rowIndex, item);
        })
        .catch(error => {
            console.error('Error fetching batches:', error);
            _legacy_showBatchSelectionModal([], rowIndex, item);
        });
}

function _legacy_showBatchSelectionModal(batches, rowIndex, itemData) {
    let html = `
        <div class="batch-modal-backdrop show" id="batchBackdrop"></div>
        <div class="batch-modal show" id="batchModal">
            <div class="modal-header-custom" style="background: #17a2b8;">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Select Batch for Sample Receive</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeBatchModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="d-flex justify-content-between align-items-center mb-3 p-2" style="background: #f8f9fa; border-radius: 5px;">
                    <div>
                        <strong>ITEM:</strong> <span style="color: #6f42c1; font-weight: bold;">${itemData.name || ''}</span>
                    </div>
                    <button type="button" class="btn btn-warning btn-sm" onclick="_legacy_skipBatchSelection(${rowIndex})">
                        <i class="bi bi-skip-forward me-1"></i> Skip (No Batch)
                    </button>
                </div>`;
    
    if (batches.length > 0) {
        html += `
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 10px;">
                        <thead style="background: #90EE90;">
                            <tr>
                                <th>BATCH</th>
                                <th>S.RATE</th>
                                <th>MRP</th>
                                <th>QTY</th>
                                <th>EXP.</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>`;
    
        batches.forEach(batch => {
            const expiry = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month: '2-digit', year: '2-digit'}) : '';
            html += `
                <tr style="cursor: pointer;">
                    <td><strong>${batch.batch_no || ''}</strong></td>
                    <td class="text-end">${parseFloat(batch.s_rate || 0).toFixed(2)}</td>
                    <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                    <td class="text-end">${batch.qty || 0}</td>
                    <td>${expiry}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success py-0 px-2" onclick='_legacy_selectBatchFromModal(${rowIndex}, ${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                            <i class="bi bi-check"></i> Select
                        </button>
                    </td>
                </tr>`;
        });
    
        html += `</tbody></table></div>`;
    } else {
        html += `
                <div class="text-center py-4" style="background: #fff3cd; border-radius: 5px;">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2"><strong>No batches found for this item.</strong></p>
                    <p class="text-muted small">Click "Skip" to continue without batch selection.</p>
                </div>`;
    }
    
    html += `</div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
}

function _legacy_skipBatchSelection(rowIndex) {
    closeBatchModal();
    const row = document.getElementById(`row-${rowIndex}`);
    row?.querySelector('input[name*="[qty]"]')?.focus();
}

function _legacy_selectBatchFromModal(rowIndex, batch) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[batch]"]').value = batch.batch_no || '';
    if (batch.expiry_date) {
        const d = new Date(batch.expiry_date);
        row.querySelector('input[name*="[expiry]"]').value = `${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
    }
    row.querySelector('input[name*="[rate]"]').value = parseFloat(batch.s_rate || 0).toFixed(2);
    row.querySelector('input[name*="[batch_id]"]').value = batch.id || '';
    row.dataset.batchId = batch.id;
    row.dataset.batchData = JSON.stringify(batch);
    
    document.getElementById('cl_qty').value = batch.qty || 0;
    
    closeBatchModal();
    row.querySelector('input[name*="[qty]"]')?.focus();
}

function closeBatchModal() {
    document.getElementById('batchModal')?.remove();
    document.getElementById('batchBackdrop')?.remove();
}

// ============ KEYBOARD NAVIGATION ============
function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            // Shift+Enter: Go back - but batch is first editable, so do nothing
            return;
        }
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[expiry]"]')?.focus();
    }
}

function handleExpiryKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            const row = document.getElementById(`row-${rowIndex}`);
            row?.querySelector('input[name*="[batch]"]')?.focus();
            return;
        }
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            const row = document.getElementById(`row-${rowIndex}`);
            row?.querySelector('input[name*="[expiry]"]')?.focus();
            return;
        }
        calculateRowAmount(rowIndex);
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[rate]"]')?.focus();
    }
}

function handleRateKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            const row = document.getElementById(`row-${rowIndex}`);
            row?.querySelector('input[name*="[qty]"]')?.focus();
            return;
        }
        calculateRowAmount(rowIndex);
        completeRow(rowIndex);
        let sib = document.getElementById('row-' + rowIndex)?.nextElementSibling;
        while (sib && sib.id?.startsWith('row-')) {
            const b = sib.querySelector('input[name*="[batch]"]');
            if (b && b.value.trim()) {
                const nIdx = parseInt(sib.id.replace('row-', ''));
                selectRow(nIdx); b.focus(); b.select(); return;
            }
            sib = sib.nextElementSibling;
        }
        showItemSelectionModal();
    }
}

function completeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.remove('row-selected');
        row.classList.add('row-complete');
        calculateTotalAmount();
        selectedRowIndex = null;
    }
}


function addNewRow() {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onchange="searchItemByCode(${rowIndex}, this.value)" onkeydown="handleCodeKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
        <input type="hidden" name="items[${rowIndex}][packing]" value="">
        <input type="hidden" name="items[${rowIndex}][unit]" value="">
        <input type="hidden" name="items[${rowIndex}][company_name]" value="">
        <input type="hidden" name="items[${rowIndex}][hsn_code]" value="">
        <input type="hidden" name="items[${rowIndex}][mrp]" value="0">
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    row.querySelector('input[name*="[code]"]').focus();
}

function handleCodeKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = document.getElementById(`row-${rowIndex}`);
        const codeInput = row?.querySelector('input[name*="[code]"]');
        if (codeInput && codeInput.value.trim()) {
            searchItemByCode(rowIndex, codeInput.value);
        }
    }
}

function searchItemByCode(rowIndex, code) {
    if (!code) return;
    const item = itemsData.find(i => i.id == code);
    if (item) {
        fillRowWithItem(rowIndex, item);
    }
}

function fillRowWithItem(rowIndex, item) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    row.querySelector('input[name*="[code]"]').value = item.id || '';
    row.querySelector('input[name*="[name]"]').value = item.name || '';
    row.querySelector('input[name*="[rate]"]').value = parseFloat(item.s_rate || 0).toFixed(2);
    row.querySelector('input[name*="[item_id]"]').value = item.id;
    row.querySelector('input[name*="[packing]"]').value = item.packing || '';
    row.querySelector('input[name*="[company_name]"]').value = item.company_name || '';
    row.querySelector('input[name*="[hsn_code]"]').value = item.hsn_code || '';
    row.querySelector('input[name*="[mrp]"]').value = item.mrp || 0;
    row.dataset.itemData = JSON.stringify(item);
    row.dataset.itemId = item.id;
    
    updateFooterFromRow(row);
    _legacy_showBatchSelectionForItem(item, rowIndex);
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => {
        r.classList.remove('row-selected');
    });
    
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
        updateFooterFromRow(row);
    }
}

function updateFooterFromRow(row) {
    let packing = '';
    let unit = '';
    let clQty = '0';
    
    if (row.dataset.itemData) {
        const itemData = JSON.parse(row.dataset.itemData);
        packing = itemData.packing || '';
        unit = itemData.unit || '';
    }
    
    if (!packing) {
        packing = row.querySelector('input[name*="[packing]"]')?.value || '';
    }
    if (!unit) {
        unit = row.querySelector('input[name*="[unit]"]')?.value || '';
    }
    
    if (row.dataset.batchData) {
        const batchData = JSON.parse(row.dataset.batchData);
        clQty = batchData.qty || '0';
    }
    
    document.getElementById('packing').value = packing;
    document.getElementById('unit').value = unit || '1';
    document.getElementById('cl_qty').value = clQty;
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
    const amount = qty * rate;
    
    row.querySelector('input[name*="[amount]"]').value = amount.toFixed(2);
    
    calculateTotalAmount();
}

function calculateTotalAmount() {
    let total = 0;
    
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
        total += amount;
    });
    
    document.getElementById('net_amount').value = total.toFixed(2);
}

function removeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.remove();
        calculateTotalAmount();
    }
}

function deleteSelectedItem() {
    if (selectedRowIndex !== null) {
        removeRow(selectedRowIndex);
        selectedRowIndex = null;
    } else {
        alert('Please select an item to delete');
    }
}

let isSubmitting = false;

function updateTransaction() {
    if (!loadedTransactionId) {
        alert('Please load an invoice first using the "Load Invoice" button');
        return;
    }
    
    // Prevent double submission
    if (isSubmitting) {
        return;
    }
    isSubmitting = true;
    
    // Disable button and show loading
    const updateBtn = document.querySelector('button[onclick="updateTransaction()"]');
    const originalBtnHtml = updateBtn.innerHTML;
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';
    
    const form = document.getElementById('srForm');
    const formData = new FormData(form);
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert('Please add at least one item');
        isSubmitting = false;
        updateBtn.disabled = false;
        updateBtn.innerHTML = originalBtnHtml;
        return;
    }
    
    let totalQty = 0;
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        totalQty += qty;
    });
    formData.append('total_qty', totalQty);
    formData.append('total_amount', document.getElementById('net_amount').value);
    
    // Ensure _method is set to PUT for Laravel method spoofing
    formData.set('_method', 'PUT');
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`{{ url('admin/sample-received') }}/${loadedTransactionId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Transaction updated successfully!');
            window.location.href = '{{ route("admin.sample-received.index") }}';
        } else {
            alert(data.message || 'Error updating transaction');
            isSubmitting = false;
            updateBtn.disabled = false;
            updateBtn.innerHTML = originalBtnHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating transaction');
        isSubmitting = false;
        updateBtn.disabled = false;
        updateBtn.innerHTML = originalBtnHtml;
    });
}

function cancelModification() {
    if (confirm('Are you sure you want to cancel? Unsaved changes will be lost.')) {
        window.location.href = '{{ route("admin.sample-received.index") }}';
    }
}
// ====== GLOBAL KEYBOARD NAVIGATION ======
// ═══════════════════════════════════════════════════════
// CUSTOM DROPDOWN FUNCTIONS: Party Type + Party Name
// ═══════════════════════════════════════════════════════
let _ptHil = -1, _pnHil = -1, _pnAllItems = [];

// ── Party Type ──
function _ptShowDrop() {
    const drop = document.getElementById('partyTypeDrop');
    drop.style.display = 'block';
    const cur = document.getElementById('party_type').value;
    const opts = Array.from(drop.querySelectorAll('.pt-opt'));
    opts.forEach((o,i) => {
        const active = o.dataset.value === cur;
        o.style.background = active ? '#0d6efd' : '';
        o.style.color = active ? '#fff' : '';
        if (active) _ptHil = i;
    });
}
function _ptHideDrop() { document.getElementById('partyTypeDrop').style.display = 'none'; }
function _ptHilAt(idx) {
    const opts = Array.from(document.querySelectorAll('#partyTypeDrop .pt-opt'));
    opts.forEach(o => { o.style.background=''; o.style.color=''; });
    if (idx < 0) idx = 0; if (idx >= opts.length) idx = opts.length-1;
    _ptHil = idx;
    if (opts[idx]) { opts[idx].style.background='#0d6efd'; opts[idx].style.color='#fff'; opts[idx].scrollIntoView({block:'nearest'}); }
}
function _ptSelect(el) {
    document.getElementById('party_type').value = el.dataset.value;
    document.getElementById('partyTypeInput').value = el.textContent.trim();
    _ptHideDrop();
    _pnAllItems = [];
    document.getElementById('party_id').value = '';
    document.getElementById('party_name').value = '';
    document.getElementById('partyNameInput').value = '';
    // Fetch party list then focus name
    fetch(`{{ url('admin/sample-received/get-party-list') }}?party_type=${el.dataset.value}`)
        .then(r => r.json()).then(data => {
            _pnAllItems = data || [];
            document.getElementById('partyNameInput')?.focus();
            _pnBuild(_pnAllItems);
        }).catch(() => { _pnAllItems = []; document.getElementById('partyNameInput')?.focus(); });
}

// ── Party Name ──
function _pnBuild(items) {
    const drop = document.getElementById('partyNameDrop');
    drop.innerHTML = '';
    if (!items.length) { drop.innerHTML='<div style="padding:6px 10px;color:#999;">No results</div>'; drop.style.display='block'; return; }
    items.forEach(p => {
        const d = document.createElement('div');
        d.style.cssText = 'padding:5px 10px;cursor:pointer;border-bottom:1px solid #f0f0f0;';
        d.textContent = p.name||p.text||'';
        d.dataset.id = p.id; d.dataset.name = p.name||p.text||'';
        d.addEventListener('mousedown', ev => { ev.preventDefault(); _pnSelect(d); });
        drop.appendChild(d);
    });
    drop.style.display = 'block'; _pnHil = -1;
}
function _pnFilter() {
    const q = document.getElementById('partyNameInput').value.toLowerCase();
    _pnBuild(q ? _pnAllItems.filter(p => (p.name||p.text||'').toLowerCase().includes(q)) : _pnAllItems);
}
function _pnShowDrop() {
    if (!_pnAllItems.length) {
        const pt = document.getElementById('party_type').value;
        if (pt) fetch(`{{ url('admin/sample-received/get-party-list') }}?party_type=${pt}`)
            .then(r=>r.json()).then(d => { _pnAllItems=d||[]; _pnBuild(_pnAllItems); }).catch(()=>{});
        else { _pnBuild([]); return; }
    } else { _pnBuild(_pnAllItems); }
}
function _pnHideDrop() { document.getElementById('partyNameDrop').style.display='none'; }
function _pnHilAt(idx) {
    const items = Array.from(document.querySelectorAll('#partyNameDrop div[data-id]'));
    items.forEach(i => { i.style.background=''; i.style.color=''; });
    if (idx < 0) idx=0; if (idx >= items.length) idx=items.length-1;
    _pnHil = idx;
    if (items[idx]) { items[idx].style.background='#0d6efd'; items[idx].style.color='#fff'; items[idx].scrollIntoView({block:'nearest'}); }
}
function _pnSelect(el) {
    document.getElementById('party_id').value = el.dataset.id;
    document.getElementById('party_name').value = el.dataset.name;
    document.getElementById('partyNameInput').value = el.dataset.name;
    _pnHideDrop();
    document.getElementById('remarks')?.focus();
}
// Compatibility shim so populateForm still works
function loadPartyList(preserve, focusNext) {
    const pt = document.getElementById('party_type').value;
    return fetch(`{{ url('admin/sample-received/get-party-list') }}?party_type=${pt}`)
        .then(r=>r.json()).then(d => { _pnAllItems=d||[]; });
}
function updatePartyName() {}

// Close drops on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#partyTypeDrop') && e.target.id !== 'partyTypeInput') _ptHideDrop();
    if (!e.target.closest('#partyNameDrop') && e.target.id !== 'partyNameInput') _pnHideDrop();
});

// ── Party Type keyboard ──
window.addEventListener('keydown', function(e) {
    if (document.activeElement?.id !== 'partyTypeInput') return;
    if (!['ArrowDown','ArrowUp','Enter','Escape'].includes(e.key)) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    const drop = document.getElementById('partyTypeDrop');
    if (e.key === 'Escape') { _ptHideDrop(); return; }
    if (e.key === 'ArrowDown') { if (drop.style.display!=='block') _ptShowDrop(); _ptHilAt(_ptHil+1); return; }
    if (e.key === 'ArrowUp')   { _ptHilAt(_ptHil-1); return; }
    if (e.key === 'Enter') {
        const opts = Array.from(document.querySelectorAll('#partyTypeDrop .pt-opt'));
        const el = _ptHil>=0 ? opts[_ptHil] : opts[0];
        if (el) _ptSelect(el); else _ptHideDrop();
    }
}, true);

// ── Party Name keyboard ──
window.addEventListener('keydown', function(e) {
    if (document.activeElement?.id !== 'partyNameInput') return;
    if (!['ArrowDown','ArrowUp','Enter','Escape'].includes(e.key)) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    if (e.key === 'Escape') { _pnHideDrop(); return; }
    if (e.key === 'ArrowDown') { const d=document.getElementById('partyNameDrop'); if(d.style.display!=='block') _pnShowDrop(); _pnHilAt(_pnHil+1); return; }
    if (e.key === 'ArrowUp')   { _pnHilAt(_pnHil-1); return; }
    if (e.key === 'Enter') {
        const items = Array.from(document.querySelectorAll('#partyNameDrop div[data-id]'));
        const el = _pnHil>=0 ? items[_pnHil] : items[0];
        if (el) _pnSelect(el); else { _pnHideDrop(); document.getElementById('remarks')?.focus(); }
    }
}, true);

// ── Load Invoice modal keyboard ──
let _invHil = -1;
function _invHilAt(idx) {
    const rows = Array.from(document.querySelectorAll('#invoicesListBody tr.invoice-row'));
    if (rows.length === 0) { _invHil = -1; return; }
    if (idx < 0) idx = 0;
    if (idx >= rows.length) idx = rows.length - 1;
    rows.forEach((r, i) => {
        r.style.background  = i === idx ? '#0d6efd' : '';
        r.style.color       = i === idx ? '#fff'    : '';
        r.style.fontWeight  = i === idx ? 'bold'    : '';
    });
    _invHil = idx;
    rows[idx].scrollIntoView({ block: 'nearest' });
}
window.addEventListener('keydown', function(e) {
    if (!document.getElementById('loadInvoiceModal')) return;
    if (!['ArrowDown','ArrowUp','Enter','Escape'].includes(e.key)) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    const rows = Array.from(document.querySelectorAll('#invoicesListBody tr.invoice-row'));
    if (e.key === 'Escape') { closeLoadInvoiceModal(); return; }
    if (rows.length === 0) return;
    if (e.key === 'ArrowDown') { _invHilAt(_invHil + 1); return; }
    if (e.key === 'ArrowUp')   { _invHilAt(_invHil - 1); return; }
    if (e.key === 'Enter') {
        const idx = _invHil >= 0 ? _invHil : 0;
        const id  = rows[idx]?.dataset.invoiceId;
        if (id) selectInvoice(parseInt(id));
    }
}, true);

// ── Master header + table keyboard handler ──
window.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const activeEl = document.activeElement;
        if (!activeEl) return;
        if (document.getElementById('loadInvoiceModal')) return;
        if (document.querySelector('#sampleReceivedModItemModal.show') || document.querySelector('#sampleReceivedModBatchModal.show')) return;
        if (activeEl.closest('#itemsTableBody')) return;
        if (['partyTypeInput','partyNameInput'].includes(activeEl.id)) return;

        if (e.ctrlKey) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('srlno')?.focus(); document.getElementById('srlno')?.select(); return;
        }

        if (e.shiftKey) {
            const back = { 'loadInvoiceBtn':'transaction_date', 'partyTypeInput':'loadInvoiceBtn',
                'partyNameInput':'partyTypeInput', 'remarks':'partyNameInput',
                'on_field':'remarks','rate':'on_field','tag':'rate','gr_no':'tag',
                'gr_date':'gr_no','cases':'gr_date','road_permit_no':'cases',
                'truck_no':'road_permit_no','transport':'truck_no' };
            if (back[activeEl.id]) {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById(back[activeEl.id])?.focus(); return;
            }
            return;
        }

        // Forward
        if (activeEl.id === 'transaction_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('loadInvoiceBtn')?.focus(); return;
        }
        if (activeEl.id === 'loadInvoiceBtn') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            showLoadInvoiceModal();
            setTimeout(() => { _invHil=-1; _invHilAt(0); }, 400); return;
        }
        const fwd = { 'remarks':'on_field','on_field':'rate','rate':'tag','tag':'gr_no',
            'gr_no':'gr_date','gr_date':'cases','cases':'road_permit_no',
            'road_permit_no':'truck_no','truck_no':'transport' };
        if (fwd[activeEl.id]) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById(fwd[activeEl.id])?.focus(); return;
        }
        if (activeEl.id === 'transport') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            let sib = document.querySelector('#itemsTableBody tr');
            if (sib) {
                const idx = parseInt(sib.id.replace('row-', '')); selectRow(idx);
                const b = sib.querySelector('input[name*="[batch]"]');
                if (b) { b.focus(); b.select(); return; }
            }
            showItemSelectionModal(); return;
        }
    }
    if ((e.key==='s'||e.key==='S') && (e.ctrlKey||e.metaKey) && !e.shiftKey) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        updateTransaction();
    }
}, true);
</script>
@endpush