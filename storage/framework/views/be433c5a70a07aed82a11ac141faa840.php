<?php $__env->startSection('title', 'Sample Issued - Modification'); ?>

<?php $__env->startPush('styles'); ?>
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

    /* Custom Dropdown Styles */
    .custom-dropdown-item { padding: 5px 10px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 11px; }
    .custom-dropdown-item:hover, .custom-dropdown-item.active { background-color: #f0f8ff; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="si-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Sample Issued - Modification</h4>
                <div class="text-muted small">Load and modify existing sample issued transaction</div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning btn-sm" id="sim_loadInvoiceBtn" onclick="showLoadInvoiceModal()">
                    <i class="bi bi-folder2-open me-1"></i> Load Invoice
                </button>
                <a href="<?php echo e(route('admin.sample-issued.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
                <a href="<?php echo e(route('admin.sample-issued.create')); ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Transaction
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="siForm" method="POST" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" id="transaction_id" name="transaction_id" value="">
                    
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="sim_transaction_date" name="transaction_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" onchange="updateDayName()" required data-custom-enter>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;"></label>
                                    <input type="text" id="day_name" name="day_name" class="form-control readonly-field text-center" value="<?php echo e(date('l')); ?>" readonly style="width: 100px;">
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 50px;">Trn.No :</label>
                                    <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 70px;">Party Type :</label>
                                    <div class="custom-dropdown" id="sim_partyTypeDropdownWrapper" style="flex: 1; position: relative;">
                                        <input type="text" class="form-control" id="sim_partyTypeDisplay" placeholder="Select Type..." autocomplete="off" style="background: #fff3e0; border: 2px solid #ff9800;" onfocus="openPartyTypeDropdown()" onkeyup="filterPartyTypes(event)" data-custom-enter value="<?php echo e(collect($partyTypes)->first()); ?>">
                                        <input type="hidden" id="sim_party_type" name="party_type" value="<?php echo e(collect($partyTypes)->keys()->first()); ?>">
                                        <div class="custom-dropdown-list" id="sim_partyTypeList" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ccc; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            <?php $__currentLoopData = $partyTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="custom-dropdown-item" data-value="<?php echo e($key); ?>" data-name="<?php echo e($label); ?>" onclick="selectPartyType('<?php echo e($key); ?>', '<?php echo e(addslashes($label)); ?>')"><?php echo e($label); ?></div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Name :</label>
                                    <div class="custom-dropdown" id="sim_partyDropdownWrapper" style="flex: 1; position: relative;">
                                        <input type="text" class="form-control" id="sim_partyDisplay" placeholder="Select Party..." autocomplete="off" style="background: #e8ffe8; border: 2px solid #28a745;" onfocus="openPartyDropdown()" onkeyup="filterParties(event)" data-custom-enter>
                                        <input type="hidden" id="party_id" name="party_id">
                                        <input type="hidden" id="party_name" name="party_name">
                                        <div class="custom-dropdown-list" id="sim_partyList" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ccc; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="custom-dropdown-item" data-value="<?php echo e($customer->id); ?>" data-name="<?php echo e($customer->name); ?>" onclick="selectParty('<?php echo e($customer->id); ?>', '<?php echo e(addslashes($customer->name)); ?>')"><?php echo e($customer->name); ?></div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="field-group">
                                    <label style="width: 60px;">Remarks :</label>
                                    <input type="text" id="sim_remarks" name="remarks" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 30px;">On :</label>
                                    <input type="text" id="sim_on_field" name="on_field" class="form-control" style="width: 50px;" data-custom-enter>
                                    <label style="width: 35px;">Rate :</label>
                                    <input type="number" id="sim_rate" name="rate" class="form-control text-end" step="0.01" value="0" style="width: 70px;" data-custom-enter>
                                </div>
                                <div class="field-group">
                                    <label style="width: 30px;">Tag :</label>
                                    <input type="text" id="sim_tag" name="tag" class="form-control" style="width: 80px;" data-custom-enter>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 50px;">GR No. :</label>
                                    <input type="text" id="sim_gr_no" name="gr_no" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">GR Date :</label>
                                    <input type="date" id="sim_gr_date" name="gr_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="field-group">
                                    <label style="width: 40px;">Cases :</label>
                                    <input type="number" id="sim_cases" name="cases" class="form-control" value="0" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 80px;">Road Permit :</label>
                                    <input type="text" id="sim_road_permit" name="road_permit_no" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 60px;">Truck No. :</label>
                                    <input type="text" id="sim_truck_no" name="truck_no" class="form-control" data-custom-enter>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 70px;">Transport :</label>
                                    <input type="text" id="sim_transport" name="transport" class="form-control" data-custom-enter>
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
                            <button type="button" class="btn btn-sm btn-primary" id="sim_addItemsBtn" onclick="showItemSelectionModal()">
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
<?php echo $__env->make('components.modals.item-selection', [
    'id' => 'sampleIssuedModItemModal',
    'module' => 'sample-issued-mod',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'sampleIssuedModBatchModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id' => 'sampleIssuedModBatchModal',
    'module' => 'sample-issued-mod',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => false,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentRowIndex = 0;
let itemsData = [];
let selectedRowIndex = null;
let loadedTransactionId = null;
let originalItems = []; // Store original items for comparison during update

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    
    // Auto-load transaction if ID is passed in URL
    const urlParams = new URLSearchParams(window.location.search);
    const loadId = urlParams.get('load');
    if (loadId) {
        // Don't load party list initially if we're loading a transaction
        setTimeout(() => loadTransactionData(loadId), 300);
    } else {
        // Only load party list if not loading a transaction
        loadPartyList().then(() => {
            // Focus Load Invoice button AFTER party list loaded (nothing left to steal focus)
            if (!document.getElementById('loadInvoiceModal')) {
                document.getElementById('sim_loadInvoiceBtn')?.focus();
            }
        });
    }
});

function updateDayName() {
    const dateInput = document.getElementById('sim_transaction_date');
    const dayInput = document.getElementById('day_name');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayInput.value = days[date.getDay()];
    }
}

function loadItems() {
    fetch('<?php echo e(route("admin.sample-issued.getItems")); ?>')
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
                    <input type="text" class="form-control" id="invoiceSearchInput" placeholder="Search by TRN No. or Party Name..." oninput="searchInvoices()">
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
    invoiceActiveIndex = -1;
    document.getElementById('invoiceSearchInput')?.focus();
    loadPastInvoices();
}

function loadPastInvoices(search = '') {
    fetch(`<?php echo e(route("admin.sample-issued.getPastInvoices")); ?>?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('invoicesListBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No invoices found</td></tr>';
                return;
            }
            
            document.getElementById('invoicesListBody').innerHTML = data.map((inv, index) => `
                <tr class="invoice-row" style="cursor: pointer;" onclick="selectInvoice(${inv.id})">
                    <td>${inv.trn_no || ''}</td>
                    <td>${inv.transaction_date ? new Date(inv.transaction_date).toLocaleDateString('en-GB') : ''}</td>
                    <td><span class="badge" style="background:${inv.party_type=='customer'?'#17a2b8':inv.party_type=='sales_man'?'#ffc107':'#6c757d'}">${(inv.party_type||'').toUpperCase()}</span></td>
                    <td>${inv.party_name || ''}</td>
                    <td class="text-end">₹${parseFloat(inv.net_amount || 0).toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success py-0 px-2" onclick="event.stopPropagation(); selectInvoice(${inv.id})">
                            <i class="bi bi-check"></i> Load
                        </button>
                    </td>
                </tr>
            `).join('');
            // Auto-highlight first row
            invoiceActiveIndex = 0;
            highlightInvoiceRow();
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
    fetch(`<?php echo e(url('admin/sample-issued')); ?>/${id}`, {
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

    document.getElementById('sim_transaction_date').value = transaction.transaction_date ? transaction.transaction_date.split('T')[0] : '';
    updateDayName();
    document.getElementById('trn_no').value = transaction.trn_no || '';
    document.getElementById('sim_remarks').value = transaction.remarks || '';
    document.getElementById('sim_on_field').value = transaction.on_field || '';
    document.getElementById('sim_rate').value = transaction.rate || 0;
    document.getElementById('sim_tag').value = transaction.tag || '';
    document.getElementById('sim_gr_no').value = transaction.gr_no || '';
    document.getElementById('sim_gr_date').value = transaction.gr_date ? transaction.gr_date.split('T')[0] : '';
    document.getElementById('sim_cases').value = transaction.cases || 0;
    document.getElementById('sim_road_permit').value = transaction.road_permit_no || '';
    document.getElementById('sim_truck_no').value = transaction.truck_no || '';
    document.getElementById('sim_transport').value = transaction.transport || '';
    document.getElementById('net_amount').value = parseFloat(transaction.net_amount || 0).toFixed(2);
    document.getElementById('party_name').value = transaction.party_name || '';

    // Set party type using custom dropdown
    if (transaction.party_type) {
        document.getElementById('sim_party_type').value = transaction.party_type;
        const ptItems = document.querySelectorAll('#sim_partyTypeList .custom-dropdown-item');
        ptItems.forEach(item => {
            if (item.dataset.value === transaction.party_type) {
                document.getElementById('sim_partyTypeDisplay').value = item.dataset.name;
                window.selectedPartyTypeName = item.dataset.name;
            }
        });
    }

    // Set party name using custom dropdown
    if (transaction.party_name) {
        document.getElementById('sim_partyDisplay').value = transaction.party_name;
        window.selectedPartyName = transaction.party_name;
        if (transaction.party_id) {
            document.getElementById('party_id').value = transaction.party_id;
        }
    }

    // Load party list to populate dropdown items
    loadPartyList(true).then(() => {
        if (transaction.party_id && transaction.party_name) {
            document.getElementById('party_id').value = transaction.party_id;
            document.getElementById('sim_partyDisplay').value = transaction.party_name;
        }
    });

    // Clear and populate items
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
        if (transaction.items.length > 0) selectRow(0);
    }

    calculateTotalAmount();
    document.getElementById('sim_transaction_date')?.focus();
}

function addItemRowFromData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    // Get batch_id - check multiple possible field names
    const batchId = item.batch_id || item.batchId || '';
    const itemId = item.item_id || item.itemId || '';
    const itemCode = item.item_code || item.itemCode || itemId || '';
    const itemName = item.item_name || item.itemName || item.name || '';
    const batchNo = item.batch_no || item.batchNo || item.batch || '';
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = itemId;
    // Store item data for footer updates
    row.dataset.itemData = JSON.stringify({
        packing: item.packing || '',
        unit: item.unit || '',
        qty: item.qty || 0,
        name: itemName,
        id: itemId
    });
    // Store batch data for footer updates
    if (batchId) {
        row.dataset.batchId = batchId;
        row.dataset.batchData = JSON.stringify({
            qty: 0, // Closing qty will be fetched if needed
            batch_no: batchNo
        });
    }
    row.onclick = function() { selectRow(rowIndex); };
    row.className = 'row-complete';
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemCode}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemName}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchNo}" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${item.expiry || ''}" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${item.qty || 0}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" value="${parseFloat(item.rate || 0).toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${itemId}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchId}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${item.hsn_code || ''}">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="${item.mrp || 0}">
        </td>
    `;
    
    tbody.appendChild(row);
}

// ============ PARTY DROPDOWN FUNCTIONS ============
function loadPartyList(preserveSelection = false) {
    return new Promise((resolve) => {
        const partyType = document.getElementById('sim_party_type').value;
        const listContainer = document.getElementById('sim_partyList');
        
        if (!preserveSelection) {
            listContainer.innerHTML = '<div class="custom-dropdown-item" style="color: #999;">Loading...</div>';
            document.getElementById('party_id').value = '';
            document.getElementById('party_name').value = '';
            document.getElementById('sim_partyDisplay').value = '';
        }
        
        fetch(`<?php echo e(url('admin/sample-issued/get-party-list')); ?>?party_type=${partyType}`)
            .then(response => response.json())
            .then(data => {
                listContainer.innerHTML = '';
                data.forEach(party => {
                    const div = document.createElement('div');
                    div.className = 'custom-dropdown-item';
                    div.dataset.value = party.id;
                    div.dataset.name = party.name;
                    div.textContent = party.name;
                    div.onclick = function() { selectParty(party.id, party.name); };
                    listContainer.appendChild(div);
                });
                resolve();
            })
            .catch(error => {
                console.error('Error loading party list:', error);
                listContainer.innerHTML = '<div class="custom-dropdown-item" style="color: red;">Error loading</div>';
                resolve();
            });
    });
}

function updatePartyName() {
    // No-op - handled by selectParty now
}

// ====== CUSTOM PARTY DROPDOWN ======
let partyActiveIndex = -1;

function openPartyDropdown() {
    // If party is already selected, don't open dropdown - preserve existing name
    const existingId = document.getElementById('party_id').value;
    if (existingId) {
        return;
    }
    const display = document.getElementById('sim_partyDisplay');
    display.select();
    document.querySelectorAll('#sim_partyList .custom-dropdown-item').forEach(item => { item.style.display = ''; });
    document.getElementById('sim_partyList').style.display = 'block';
    partyActiveIndex = 0;
    highlightPartyItem();
}

function closePartyDropdown() {
    setTimeout(() => {
        const list = document.getElementById('sim_partyList');
        if (list) list.style.display = 'none';
        partyActiveIndex = -1;
    }, 200);
}

function filterParties(e) {
    if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;
    // User is typing a new name - clear existing selection and open dropdown
    const existingId = document.getElementById('party_id').value;
    if (existingId) {
        document.getElementById('party_id').value = '';
        document.getElementById('party_name').value = '';
        window.selectedPartyName = '';
    }
    const listContainer = document.getElementById('sim_partyList');
    if (listContainer && listContainer.style.display === 'none') {
        document.querySelectorAll('#sim_partyList .custom-dropdown-item').forEach(item => { item.style.display = ''; });
        listContainer.style.display = 'block';
    }
    const filter = e.target.value.toLowerCase();
    document.querySelectorAll('#sim_partyList .custom-dropdown-item').forEach(item => {
        item.style.display = item.innerText.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
    });
    partyActiveIndex = 0;
    highlightPartyItem();
}

function selectParty(id, name) {
    document.getElementById('party_id').value = id;
    document.getElementById('sim_partyDisplay').value = name;
    document.getElementById('party_name').value = name;
    document.getElementById('sim_partyList').style.display = 'none';
    window.selectedPartyName = name;
    partyActiveIndex = -1;
    document.getElementById('sim_remarks')?.focus();
}

function highlightPartyItem() {
    const items = Array.from(document.querySelectorAll('#sim_partyList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
    items.forEach(i => { i.classList.remove('active'); i.style.backgroundColor = ''; });
    if (partyActiveIndex >= items.length) partyActiveIndex = 0;
    if (partyActiveIndex < -1) partyActiveIndex = items.length - 1;
    if (partyActiveIndex >= 0 && items[partyActiveIndex]) {
        items[partyActiveIndex].classList.add('active');
        items[partyActiveIndex].style.backgroundColor = '#f0f8ff';
        items[partyActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

// ====== CUSTOM PARTY TYPE DROPDOWN ======
let partyTypeActiveIndex = -1;

function openPartyTypeDropdown() {
    // If party type is already selected, don't open dropdown - preserve existing value
    const existingVal = document.getElementById('sim_party_type').value;
    if (existingVal && document.getElementById('sim_partyTypeDisplay').value) {
        return;
    }
    document.getElementById('sim_partyTypeDisplay').select();
    document.querySelectorAll('#sim_partyTypeList .custom-dropdown-item').forEach(item => { item.style.display = ''; });
    document.getElementById('sim_partyTypeList').style.display = 'block';
    partyTypeActiveIndex = 0;
    highlightPartyTypeItem();
}

function closePartyTypeDropdown() {
    setTimeout(() => {
        const list = document.getElementById('sim_partyTypeList');
        if (list) list.style.display = 'none';
        partyTypeActiveIndex = -1;
    }, 200);
}

function filterPartyTypes(e) {
    if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;
    // User is typing - open dropdown if it's not visible yet
    const listContainer = document.getElementById('sim_partyTypeList');
    if (listContainer && listContainer.style.display === 'none') {
        document.querySelectorAll('#sim_partyTypeList .custom-dropdown-item').forEach(item => { item.style.display = ''; });
        listContainer.style.display = 'block';
    }
    const filter = e.target.value.toLowerCase();
    document.querySelectorAll('#sim_partyTypeList .custom-dropdown-item').forEach(item => {
        item.style.display = item.innerText.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
    });
    partyTypeActiveIndex = 0;
    highlightPartyTypeItem();
}

function selectPartyType(id, name) {
    document.getElementById('sim_party_type').value = id;
    document.getElementById('sim_partyTypeDisplay').value = name;
    document.getElementById('sim_partyTypeList').style.display = 'none';
    window.selectedPartyTypeName = name;
    partyTypeActiveIndex = -1;
    loadPartyList();
    document.getElementById('sim_partyDisplay')?.focus();
    setTimeout(() => { openPartyDropdown(); }, 100);
}

function highlightPartyTypeItem() {
    const items = Array.from(document.querySelectorAll('#sim_partyTypeList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
    items.forEach(i => { i.classList.remove('active'); i.style.backgroundColor = ''; });
    if (partyTypeActiveIndex >= items.length) partyTypeActiveIndex = 0;
    if (partyTypeActiveIndex < -1) partyTypeActiveIndex = items.length - 1;
    if (partyTypeActiveIndex >= 0 && items[partyTypeActiveIndex]) {
        items[partyTypeActiveIndex].classList.add('active');
        items[partyTypeActiveIndex].style.backgroundColor = '#f0f8ff';
        items[partyTypeActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

// Close dropdowns on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#sim_partyDropdownWrapper')) {
        const list = document.getElementById('sim_partyList');
        if (list) list.style.display = 'none';
    }
    if (!e.target.closest('#sim_partyTypeDropdownWrapper')) {
        const list = document.getElementById('sim_partyTypeList');
        if (list) list.style.display = 'none';
    }
});

// ============ REUSABLE MODAL BRIDGE FUNCTION ============
// This function is called by the reusable modal components
function onItemBatchSelectedFromModal(itemData, batchData) {
    console.log('🎯 Sample Issued Mod: onItemBatchSelectedFromModal called', {itemData, batchData});
    
    if (!itemData || !itemData.id) {
        console.error('❌ Sample Issued Mod: Invalid item data received');
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${itemData.id || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${itemData.name || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batchData?.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${batchData?.expiry_formatted || ''}" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" value="${batchData?.qty || 0}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" value="${parseFloat(batchData?.s_rate || itemData.s_rate || 0).toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" value="0.00" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
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
    
    // Focus on Qty field so user can continue the flow (Qty → Rate → next row)
    setTimeout(() => {
        const qtyInput = row.querySelector('input[name*="[qty]"]');
        if (qtyInput) { qtyInput.focus(); qtyInput.select(); }
    }, 100);
    
    console.log('✅ Sample Issued Mod: Row created successfully', {rowIndex, itemId: itemData.id, batchId: batchData?.id});
}

// ============ SHOW ITEM SELECTION MODAL (BRIDGE TO REUSABLE COMPONENT) ============
function showItemSelectionModal() {
    console.log('🔗 Sample Issued Mod: showItemSelectionModal called - opening reusable modal');
    
    // Check if modal functions exist
    if (typeof window.openItemModal_sampleIssuedModItemModal === 'function') {
        window.openItemModal_sampleIssuedModItemModal();
    } else {
        console.error('❌ Sample Issued Mod: openItemModal_sampleIssuedModItemModal function not found. Modal component may not be loaded.');
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" value="${parseFloat(item.s_rate || 0).toFixed(2)}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || '1'}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][hsn_code]" value="${item.hsn_code || ''}">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="${item.mrp || 0}">
        </td>
    `;
    
    tbody.appendChild(row);
    selectRow(rowIndex);
    _legacy_showBatchSelectionForItem(item, rowIndex);
}

function _legacy_showBatchSelectionForItem(item, rowIndex) {
    fetch(`<?php echo e(url('admin/api/item-batches')); ?>/${item.id}`)
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
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Select Batch for Sample</h5>
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
                        <thead style="background: #ffb6c1;">
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
    
    // Update Cl.Qty in footer
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
        if (event.shiftKey) { document.getElementById('sim_transport')?.focus(); return; }
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[expiry]"]')?.focus();
    }
}

function handleExpiryKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) { const row = document.getElementById(`row-${rowIndex}`); row?.querySelector('input[name*="[batch]"]')?.focus(); return; }
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[qty]"]')?.focus();
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) { const row = document.getElementById(`row-${rowIndex}`); row?.querySelector('input[name*="[expiry]"]')?.focus(); return; }
        calculateRowAmount(rowIndex);
        const row = document.getElementById(`row-${rowIndex}`);
        row?.querySelector('input[name*="[rate]"]')?.focus();
    }
}

function handleRateKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) { const row = document.getElementById(`row-${rowIndex}`); row?.querySelector('input[name*="[qty]"]')?.focus(); return; }
        calculateRowAmount(rowIndex);
        completeRow(rowIndex);
        const currentRow = document.getElementById(`row-${rowIndex}`);
        const nextRow = currentRow ? currentRow.nextElementSibling : null;
        if (nextRow && nextRow.id && nextRow.id.startsWith('row-')) {
            const nextRowIdx = parseInt(nextRow.id.replace('row-', ''));
            selectRow(nextRowIdx);
            const nextQty = nextRow.querySelector('input[name*="[qty]"]');
            if (nextQty) { nextQty.focus(); nextQty.select(); return; }
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onchange="searchItemByCode(${rowIndex}, this.value)" onkeydown="handleCodeKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" readonly onfocus="selectRow(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" placeholder="MM/YY" onkeydown="handleExpiryKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" step="1" min="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm" name="items[${rowIndex}][rate]" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" step="0.01" readonly onfocus="selectRow(${rowIndex})"></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
            <input type="hidden" name="items[${rowIndex}][packing]" value="">
            <input type="hidden" name="items[${rowIndex}][unit]" value="">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="">
            <input type="hidden" name="items[${rowIndex}][hsn_code]" value="">
            <input type="hidden" name="items[${rowIndex}][mrp]" value="0">
        </td>
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
    
    // Try to get from dataset first
    if (row.dataset.itemData) {
        const itemData = JSON.parse(row.dataset.itemData);
        packing = itemData.packing || '';
        unit = itemData.unit || '';
    }
    
    // Fallback to hidden inputs
    if (!packing) {
        packing = row.querySelector('input[name*="[packing]"]')?.value || '';
    }
    if (!unit) {
        unit = row.querySelector('input[name*="[unit]"]')?.value || '';
    }
    
    // Get closing qty from batch data
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
    
    const form = document.getElementById('siForm');
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
        
        // Debug: Log batch_id for each row
        const batchIdInput = row.querySelector('input[name*="[batch_id]"]');
        console.log('Row batch_id:', batchIdInput ? batchIdInput.value : 'NOT FOUND');
    });
    formData.append('total_qty', totalQty);
    formData.append('total_amount', document.getElementById('net_amount').value);
    
    // Debug: Log all form data
    console.log('Form Data being sent:');
    for (let [key, value] of formData.entries()) {
        if (key.includes('batch_id') || key.includes('item_id') || key.includes('qty')) {
            console.log(key + ': ' + value);
        }
    }
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`<?php echo e(url('admin/sample-issued')); ?>/${loadedTransactionId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Transaction updated successfully!');
            window.location.href = '<?php echo e(route("admin.sample-issued.index")); ?>';
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
        window.location.href = '<?php echo e(route("admin.sample-issued.index")); ?>';
    }
}

// ====== INVOICE MODAL KEYBOARD NAV ======
let invoiceActiveIndex = -1;
function highlightInvoiceRow() {
    const rows = document.querySelectorAll('#invoicesListBody tr.invoice-row');
    rows.forEach(r => { r.style.backgroundColor = ''; r.style.outline = ''; });
    if (invoiceActiveIndex < 0) invoiceActiveIndex = 0;
    if (invoiceActiveIndex >= rows.length) invoiceActiveIndex = rows.length - 1;
    if (rows[invoiceActiveIndex]) {
        rows[invoiceActiveIndex].style.backgroundColor = '#fff3cd';
        rows[invoiceActiveIndex].style.outline = '2px solid #ffc107';
        rows[invoiceActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

// ====== GLOBAL KEYBOARD NAVIGATION ======
document.addEventListener('keydown', function(e) {
    // Invoice modal handler
    const invoiceModal = document.getElementById('loadInvoiceModal');
    if (invoiceModal) {
        if (e.key === 'ArrowDown') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            invoiceActiveIndex++;
            highlightInvoiceRow();
            return false;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            invoiceActiveIndex--;
            highlightInvoiceRow();
            return false;
        }
        if (e.key === 'Enter') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const activeEl = document.activeElement;
            const rows = document.querySelectorAll('#invoicesListBody tr.invoice-row');
            
            // If user has navigated to a row (via arrow keys), load that row directly
            if (invoiceActiveIndex >= 0 && rows[invoiceActiveIndex]) {
                const loadBtn = rows[invoiceActiveIndex].querySelector('button');
                if (loadBtn) loadBtn.click();
                return false;
            }
            
            // If no row is highlighted yet, highlight the first row
            if (activeEl && activeEl.id === 'invoiceSearchInput') {
                invoiceActiveIndex = 0;
                highlightInvoiceRow();
                document.getElementById('invoiceSearchInput')?.blur();
            }
            return false;
        }
        if (e.key === 'Escape') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            closeLoadInvoiceModal();
            return false;
        }
        return;
    }

    if (e.key === 'Enter') {
        const activeEl = document.activeElement;
        if (!activeEl) return;

        // Skip if modal is open
        const hasModalOpen = document.getElementById('itemModal') || document.getElementById('batchModal') ||
            document.querySelector('#sampleIssuedModItemModal.show') || document.querySelector('#sampleIssuedModBatchModal.show');
        if (hasModalOpen) return;

        // Ctrl+Enter → Srlno
        if (e.ctrlKey && !e.shiftKey && !e.altKey) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('srlno')?.focus();
            document.getElementById('srlno')?.select();
            return false;
        }

        // Shift+Enter backward
        if (e.shiftKey && !e.ctrlKey) {
            const backMap = {
                'sim_partyTypeDisplay': 'sim_transaction_date',
                'sim_partyDisplay': 'sim_partyTypeDisplay',
                'sim_remarks': 'sim_partyDisplay',
                'sim_on_field': 'sim_remarks',
                'sim_rate': 'sim_on_field',
                'sim_tag': 'sim_rate',
                'sim_gr_no': 'sim_tag',
                'sim_gr_date': 'sim_gr_no',
                'sim_cases': 'sim_gr_date',
                'sim_road_permit': 'sim_cases',
                'sim_truck_no': 'sim_road_permit',
                'sim_transport': 'sim_truck_no'
            };
            if (backMap[activeEl.id]) {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById(backMap[activeEl.id])?.focus();
                return false;
            }
            return;
        }

        // Party Type Dropdown Intercept
        if (activeEl.id === 'sim_partyTypeDisplay') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const listContainer = document.getElementById('sim_partyTypeList');
            // If party type already set and dropdown is NOT open, just move to Name field
            const existingPT = document.getElementById('sim_party_type').value;
            if (existingPT && (!listContainer || listContainer.style.display !== 'block')) {
                document.getElementById('sim_partyDisplay')?.focus();
                return false;
            }
            if (listContainer && listContainer.style.display === 'block') {
                const items = Array.from(document.querySelectorAll('#sim_partyTypeList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
                if (partyTypeActiveIndex >= 0 && partyTypeActiveIndex < items.length) {
                    items[partyTypeActiveIndex].click();
                } else {
                    listContainer.style.display = 'none';
                    document.getElementById('sim_partyDisplay')?.focus();
                    setTimeout(() => { openPartyDropdown(); }, 50);
                }
            } else {
                document.getElementById('sim_partyDisplay')?.focus();
                setTimeout(() => { openPartyDropdown(); }, 50);
            }
            return false;
        }

        // Party Name Dropdown Intercept
        if (activeEl.id === 'sim_partyDisplay') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const existingId = document.getElementById('party_id').value;
            const listContainer = document.getElementById('sim_partyList');
            if (existingId) {
                if (listContainer) listContainer.style.display = 'none';
                partyActiveIndex = -1;
                document.getElementById('sim_partyDisplay').value = window.selectedPartyName || '';
                document.getElementById('sim_remarks')?.focus();
                return false;
            }
            if (listContainer && listContainer.style.display === 'block') {
                const items = Array.from(document.querySelectorAll('#sim_partyList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
                if (partyActiveIndex >= 0 && partyActiveIndex < items.length) {
                    items[partyActiveIndex].click();
                } else {
                    listContainer.style.display = 'none';
                    document.getElementById('sim_remarks')?.focus();
                }
            } else {
                document.getElementById('sim_remarks')?.focus();
            }
            return false;
        }

        // Date → Party Type
        if (activeEl.id === 'sim_transaction_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('sim_partyTypeDisplay')?.focus();
            // Only open dropdown if no party type is set yet
            const existingPT = document.getElementById('sim_party_type').value;
            if (!existingPT || !document.getElementById('sim_partyTypeDisplay').value) {
                setTimeout(() => { openPartyTypeDropdown(); }, 50);
            }
            return false;
        }
        // Remarks → On
        if (activeEl.id === 'sim_remarks') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); document.getElementById('sim_on_field')?.focus(); return false; }
        if (activeEl.id === 'sim_on_field') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); document.getElementById('sim_rate')?.focus(); return false; }
        if (activeEl.id === 'sim_rate') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); document.getElementById('sim_tag')?.focus(); return false; }
        if (activeEl.id === 'sim_tag') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); document.getElementById('sim_gr_no')?.focus(); return false; }
        if (activeEl.id === 'sim_gr_no') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); document.getElementById('sim_gr_date')?.focus(); return false; }
        if (activeEl.id === 'sim_gr_date') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); document.getElementById('sim_cases')?.focus(); return false; }
        if (activeEl.id === 'sim_cases') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); document.getElementById('sim_road_permit')?.focus(); return false; }
        if (activeEl.id === 'sim_road_permit') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); document.getElementById('sim_truck_no')?.focus(); return false; }
        if (activeEl.id === 'sim_truck_no') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); document.getElementById('sim_transport')?.focus(); return false; }
        // Transport → first row Qty OR Add Items
        if (activeEl.id === 'sim_transport') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const firstRow = document.querySelector('#itemsTableBody tr');
            if (firstRow) {
                const qtyInput = firstRow.querySelector('input[name*="[qty]"]');
                if (qtyInput) { selectRow(parseInt(firstRow.dataset.rowIndex || firstRow.id.replace('row-', ''))); qtyInput.focus(); qtyInput.select(); return false; }
            }
            const addBtn = document.getElementById('sim_addItemsBtn');
            if (addBtn) { addBtn.focus(); addBtn.click(); }
            return false;
        }
        // Load Invoice button
        if (activeEl.id === 'sim_loadInvoiceBtn') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); showLoadInvoiceModal(); return false; }
        // Add Items button
        if (activeEl.id === 'sim_addItemsBtn') { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); showItemSelectionModal(); return false; }
    }

    // Dropdown arrow navigation - Party Name
    if (document.activeElement && document.activeElement.id === 'sim_partyDisplay') {
        const listContainer = document.getElementById('sim_partyList');
        if (listContainer && listContainer.style.display === 'block') {
            if (e.key === 'ArrowDown') { e.preventDefault(); partyActiveIndex++; highlightPartyItem(); return false; }
            if (e.key === 'ArrowUp') { e.preventDefault(); partyActiveIndex--; highlightPartyItem(); return false; }
            if (e.key === 'Escape') { e.preventDefault(); closePartyDropdown(); return false; }
        }
    }
    // Dropdown arrow navigation - Party Type
    if (document.activeElement && document.activeElement.id === 'sim_partyTypeDisplay') {
        const listContainer = document.getElementById('sim_partyTypeList');
        if (listContainer && listContainer.style.display === 'block') {
            if (e.key === 'ArrowDown') { e.preventDefault(); partyTypeActiveIndex++; highlightPartyTypeItem(); return false; }
            if (e.key === 'ArrowUp') { e.preventDefault(); partyTypeActiveIndex--; highlightPartyTypeItem(); return false; }
            if (e.key === 'Escape') { e.preventDefault(); closePartyTypeDropdown(); return false; }
        }
    }

    // Ctrl+S save
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault();
        updateTransaction();
        return false;
    }
}, true);

// Load Invoice auto-triggered from main DOMContentLoaded above
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/sample-issued/modification.blade.php ENDPATH**/ ?>