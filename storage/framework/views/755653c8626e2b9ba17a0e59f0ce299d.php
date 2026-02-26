<?php $__env->startSection('title', 'Stock Transfer Outgoing Modification'); ?>

<?php $__env->startSection('content'); ?>
<style>
    /* Compact form adjustments */
    .compact-form { font-size: 11px; padding: 8px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .header-row { display: flex; align-items: center; gap: 15px; margin-bottom: 6px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .field-group label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .field-group input, .field-group select { font-size: 11px; padding: 2px 6px; height: 26px; }
    
    .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .gr-section { background: #f0f0f0; border: 1px solid #ccc; padding: 8px; margin-bottom: 8px; border-radius: 4px; }
    .search-section { background: #e3f2fd; padding: 10px; border: 1px solid #90caf9; margin-bottom: 10px; border-radius: 4px; }
    
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; }
    .table-compact th { background: #e9ecef; font-weight: 600; text-align: center; border: 1px solid #dee2e6; }
    .table-compact td { border: 1px solid #dee2e6; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    
    #itemsTableContainer { max-height: 280px !important; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    
    .row-selected { background-color: #d4edff !important; border: 2px solid #007bff !important; }
    .row-selected td { background-color: #d4edff !important; }
    
    .net-section { background: #ffcccc; padding: 8px; border: 1px solid #cc0000; margin-top: 8px; border-radius: 4px; }
    .footer-section { background: #ffe4c4; padding: 8px; border: 1px solid #deb887; margin-top: 8px; border-radius: 4px; }
    .btn-section { background: #f0f0f0; padding: 8px; border: 1px solid #ccc; margin-top: 8px; text-align: center; border-radius: 4px; }

    /* Modal Styles */
    .item-modal-backdrop, .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .item-modal-backdrop.show, .batch-modal-backdrop.show { display: block; }
    .item-modal, .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .item-modal.show, .batch-modal.show { display: block; }
    .item-modal-content, .batch-modal-content { background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); overflow: hidden; }
    .item-modal-header { padding: 1rem 1.5rem; background: #0d6efd; color: white; display: flex; justify-content: space-between; align-items: center; }
    .batch-modal-header { padding: 1rem 1.5rem; background: #17a2b8; color: white; display: flex; justify-content: space-between; align-items: center; }
    .item-modal-title, .batch-modal-title { margin: 0; font-size: 1.2rem; font-weight: 600; }
    .btn-close-modal { background: none; border: none; color: white; font-size: 2rem; cursor: pointer; padding: 0; width: 30px; height: 30px; }
    .item-modal-body, .batch-modal-body { padding: 1rem; max-height: 350px; overflow-y: auto; }
    .batch-modal-body { padding: 0; }
    .item-modal-footer, .batch-modal-footer { padding: 1rem 1.5rem; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px; }

    /* Custom Dropdown Styles */
    .custom-dropdown-item { padding: 5px 10px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 11px; }
    .custom-dropdown-item:hover, .custom-dropdown-item.active { background-color: #f0f8ff; }

    /* Invoice row highlight */
    #invoicesTableBody tr.invoice-row-active { background-color: #cce5ff !important; outline: 2px solid #007bff; }
    #invoicesTableBody tr.invoice-row-active td { background-color: #cce5ff !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil-square me-2"></i> Stock Transfer Outgoing - Modification</h4>
        <div class="text-muted small">Edit existing stock transfer outgoing transactions</div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" onclick="openDateRangeModal()">
            <i class="bi bi-calendar-range me-1"></i> Filter by Date
        </button>
        <button type="button" class="btn btn-success btn-sm" onclick="openAllInvoicesModal()">
            <i class="bi bi-list-ul me-1"></i> All Invoices
        </button>
        <a href="<?php echo e(route('admin.stock-transfer-outgoing.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body p-2">
        <!-- Search Section -->
        <div class="search-section">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <div class="field-group">
                        <label>Sr No:</label>
                        <input type="text" class="form-control form-control-sm" id="search_sr_no" style="width: 120px;" placeholder="Enter Sr No" data-custom-enter>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary btn-sm" id="stom_loadBtn" onclick="loadTransaction()">
                        <i class="bi bi-search me-1"></i> Load
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-success btn-sm" id="stom_allInvoicesBtn" onclick="openAllInvoicesModal()">
                        <i class="bi bi-list-ul me-1"></i> All Invoices
                    </button>
                </div>
            </div>
        </div>

        <form id="stockTransferOutgoingForm" method="POST" autocomplete="off">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="transaction_id" id="transaction_id">

            <!-- Header Section -->
            <div class="header-section">
                <div class="row g-2">
                    <div class="col-md-2">
                        <div class="field-group">
                            <label>Date:</label>
                            <input type="date" class="form-control form-control-sm" name="transaction_date" id="stom_transaction_date" style="width: 130px;" data-custom-enter>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="field-group">
                            <label>Name:</label>
                            <div class="custom-dropdown" id="customerDropdownWrapper" style="flex: 1; position: relative;">
                                <input type="text" class="form-control form-control-sm" id="stom_customerDisplay" 
                                       placeholder="Select Customer..." autocomplete="off"
                                       style="background: #e8ffe8; border: 2px solid #28a745;"
                                       onfocus="openCustomerDropdown()" onkeyup="filterCustomers(event)" data-custom-enter>
                                <input type="hidden" name="transfer_to" id="customerSelect">
                                <div class="custom-dropdown-list" id="customerList" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ccc; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="custom-dropdown-item" 
                                             data-value="<?php echo e($customer->id); ?>" 
                                             data-name="<?php echo e($customer->name); ?>"
                                             onclick="selectCustomer('<?php echo e($customer->id); ?>', '<?php echo e(addslashes($customer->name)); ?>', '<?php echo e($customer->code ?? $customer->id); ?>')">
                                            <?php echo e($customer->code ?? $customer->id); ?> - <?php echo e($customer->name); ?>

                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <input type="hidden" name="transfer_to_name" id="transfer_to_name">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="field-group">
                            <label>Remarks:</label>
                            <input type="text" class="form-control form-control-sm" name="remarks" id="stom_remarks" data-custom-enter>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="field-group">
                            <label>Trn.No:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" name="trn_no" id="trn_no" readonly style="width: 80px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- GR Section -->
            <div class="gr-section">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <div class="field-group">
                            <label>GR No.:</label>
                            <input type="text" class="form-control form-control-sm" name="gr_no" id="stom_gr_no" style="width: 100px;" data-custom-enter>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>GR Date:</label>
                            <input type="date" class="form-control form-control-sm" name="gr_date" id="stom_gr_date" style="width: 130px;" data-custom-enter>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Cases:</label>
                            <input type="number" class="form-control form-control-sm" name="cases" id="stom_cases" style="width: 70px;" data-custom-enter>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Transport:</label>
                            <input type="text" class="form-control form-control-sm" name="transport" id="stom_transport" style="width: 200px;" data-custom-enter>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto; max-height: 280px;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="width: 60px;">Code</th>
                                <th style="width: 250px;">Item Name</th>
                                <th style="width: 80px;">Batch</th>
                                <th style="width: 70px;">Expiry</th>
                                <th style="width: 60px;">Qty</th>
                                <th style="width: 80px;">Rate</th>
                                <th style="width: 90px;">Amount</th>
                                <th style="width: 60px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                        <i class="fas fa-plus-circle"></i> Add Row
                    </button>
                    <button type="button" class="btn btn-sm btn-info" id="stom_insertItemsBtn" onclick="openInsertItemsModal()">
                        <i class="bi bi-list-check"></i> Insert Items
                    </button>
                </div>
            </div>

            <!-- Net Section -->
            <div class="net-section">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Net:</label>
                            <input type="text" class="form-control form-control-sm readonly-field text-end" id="summary_net" name="summary_net" style="width: 120px;" value="0.00" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="footer-section">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Pack:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_pack" style="width: 60px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Comp:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_comp" style="width: 100px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Unit:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_unit" style="width: 60px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Lctn:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_lctn" style="width: 60px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>Cl.Qty:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_cl_qty" style="width: 70px;" readonly>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="field-group">
                            <label>SrNo:</label>
                            <input type="text" class="form-control form-control-sm readonly-field" id="detail_srno" style="width: 60px;" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Section -->
            <div class="btn-section">
                <button type="button" class="btn btn-primary btn-sm" onclick="updateTransaction()">
                    <i class="bi bi-check-circle me-1"></i> Update
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedItem()">
                    <i class="bi bi-trash me-1"></i> Delete Item
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="cancelModification()">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Item Selection Modal -->
<div class="item-modal-backdrop" id="itemModalBackdrop"></div>
<div class="item-modal" id="itemModal">
    <div class="item-modal-content">
        <div class="item-modal-header">
            <h5 class="item-modal-title"><i class="bi bi-search me-2"></i>Select Item</h5>
            <button type="button" class="btn-close-modal" onclick="closeItemModal()">&times;</button>
        </div>
        <div class="item-modal-body">
            <input type="text" class="form-control mb-3" id="itemSearchInput" placeholder="Search by code or name..." onkeyup="searchItems()">
            <div class="table-responsive">
                <table class="table table-sm table-hover" id="itemSearchTable">
                    <thead><tr><th>Code</th><th>Name</th><th>Pack</th><th>Company</th></tr></thead>
                    <tbody id="itemSearchResults"></tbody>
                </table>
            </div>
        </div>
        <div class="item-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
        </div>
    </div>
</div>

<!-- Batch Selection Modal -->
<div class="batch-modal-backdrop" id="batchModalBackdrop"></div>
<div class="batch-modal" id="batchModal">
    <div class="batch-modal-content">
        <div class="batch-modal-header">
            <h5 class="batch-modal-title"><i class="bi bi-box-seam me-2"></i>Select Batch</h5>
            <button type="button" class="btn-close-modal" onclick="closeBatchModal()">&times;</button>
        </div>
        <div class="batch-modal-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>Batch</th><th>Expiry</th><th>MRP</th><th>Rate</th><th>Stock</th></tr></thead>
                    <tbody id="batchSearchResults"></tbody>
                </table>
            </div>
        </div>
        <div class="batch-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
        </div>
    </div>
</div>

<!-- Create Batch Modal -->
<div class="batch-modal-backdrop" id="createBatchBackdrop"></div>
<div class="batch-modal" id="createBatchModal" style="max-width: 450px;">
    <div class="batch-modal-content">
        <div class="batch-modal-header" style="background: #6c757d;">
            <h5 class="batch-modal-title"><i class="bi bi-plus-circle me-2"></i>Create New Batch</h5>
            <button type="button" class="btn-close-modal" onclick="closeCreateBatchModal()">&times;</button>
        </div>
        <div class="batch-modal-body" style="background: #e8e8e8; padding: 1rem;">
            <div class="mb-2" style="background: white; padding: 8px; border-radius: 4px;">
                <strong>Item Name:</strong> <span id="newBatchItemName" style="color: #0d6efd; font-weight: bold;"></span>
            </div>
            <div class="mb-2" style="background: white; padding: 8px; border-radius: 4px;">
                <strong>Packing:</strong> <span id="newBatchItemPacking" style="color: #0d6efd; font-weight: bold;"></span>
            </div>
            
            <div class="row mb-2">
                <div class="col-6">
                    <label class="form-label mb-1" style="font-size: 12px;"><strong>Batch No.:</strong></label>
                    <input type="text" class="form-control form-control-sm" id="newBatchNo" readonly style="background: #fff; font-weight: bold;">
                </div>
                <div class="col-6">
                    <label class="form-label mb-1" style="font-size: 12px;"><strong>Expiry (MM/YYYY):</strong></label>
                    <input type="text" class="form-control form-control-sm" id="newBatchExpiry" placeholder="MM/YYYY" style="background: #fff;">
                </div>
            </div>
            
            <div class="row mb-2">
                <div class="col-6">
                    <label class="form-label mb-1" style="font-size: 12px;"><strong>MRP:</strong></label>
                    <input type="number" class="form-control form-control-sm" id="newBatchMRP" step="0.01" value="0.00" style="background: #fff;">
                </div>
                <div class="col-6">
                    <label class="form-label mb-1" style="font-size: 12px;"><strong>Sale Rate:</strong></label>
                    <input type="number" class="form-control form-control-sm" id="newBatchSRate" step="0.01" value="0.00" style="background: #fff;">
                </div>
            </div>
            
            <div class="row mb-2">
                <div class="col-6">
                    <label class="form-label mb-1" style="font-size: 12px;"><strong>Pur. Rate:</strong></label>
                    <input type="number" class="form-control form-control-sm" id="newBatchPRate" step="0.01" value="0.00" style="background: #fff;">
                </div>
                <div class="col-6">
                    <label class="form-label mb-1" style="font-size: 12px;"><strong>W.S. Rate:</strong></label>
                    <input type="number" class="form-control form-control-sm" id="newBatchWSRate" step="0.01" value="0.00" style="background: #fff;">
                </div>
            </div>
            
            <input type="hidden" id="newBatchRowIndex">
            <input type="hidden" id="newBatchItemId">
        </div>
        <div class="batch-modal-footer">
            <button type="button" class="btn btn-primary btn-sm" onclick="saveNewBatch()">
                <i class="bi bi-check-circle me-1"></i> Save Batch
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeCreateBatchModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Date Range Modal -->
<div class="batch-modal-backdrop" id="dateRangeBackdrop" onclick="closeDateRangeModal()"></div>
<div class="batch-modal" id="dateRangeModal">
    <div class="batch-modal-content">
        <div class="batch-modal-header">
            <h5 class="batch-modal-title">Filter by Date Range</h5>
            <button type="button" class="btn-close-modal" onclick="closeDateRangeModal()">&times;</button>
        </div>
        <div class="batch-modal-body" style="padding: 20px;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" id="filterFromDate">
                </div>
                <div class="col-md-6">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" id="filterToDate">
                </div>
            </div>
        </div>
        <div class="batch-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeDateRangeModal()">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="applyDateFilter()">Apply Filter</button>
        </div>
    </div>
</div>

<!-- Invoices List Modal -->
<div class="batch-modal-backdrop" id="invoicesBackdrop" onclick="closeInvoicesModal()"></div>
<div class="item-modal" id="invoicesModal" style="max-width: 1000px;">
    <div class="item-modal-content">
        <div class="item-modal-header">
            <h5 class="item-modal-title">Select Invoice to Modify</h5>
            <button type="button" class="btn-close-modal" onclick="closeInvoicesModal()">&times;</button>
        </div>
        <div class="item-modal-body" style="max-height: 500px; overflow-y: auto; padding: 0;">
            <table class="table table-sm table-hover mb-0" style="font-size: 12px;">
                <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 1;">
                    <tr>
                        <th>Sr No</th>
                        <th>Date</th>
                        <th>Transfer To</th>
                        <th>Challan No</th>
                        <th class="text-end">Amount</th>
                        <th>Remarks</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="invoicesTableBody">
                    <tr><td colspan="7" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="item-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoicesModal()">Close</button>
        </div>
    </div>
</div>

<!-- Item and Batch Selection Modal Components -->
<?php echo $__env->make('components.modals.item-selection', [
    'id' => 'stockTransferOutgoingModItemModal',
    'module' => 'stock-transfer-outgoing',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'stockTransferOutgoingModBatchModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id' => 'stockTransferOutgoingModBatchModal',
    'module' => 'stock-transfer-outgoing',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => false,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentRowIndex = 0;
let selectedRowIndex = null;
let itemsData = {};
let loadedTransactionId = null;

function loadTransaction() {
    const srNo = document.getElementById('search_sr_no').value.trim();
    if (!srNo) {
        alert('Please enter Sr No');
        return;
    }
    
    fetch(`<?php echo e(url('admin/stock-transfer-outgoing/get-by-sr-no')); ?>/${encodeURIComponent(srNo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.transaction) {
                populateForm(data.transaction);
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
    
    // Format dates to yyyy-MM-dd for HTML date input
    document.getElementById('stom_transaction_date').value = formatDateForInput(transaction.transaction_date);
    document.getElementById('transfer_to_name').value = transaction.transfer_to_name || '';
    
    // Set customer custom dropdown
    if (transaction.transfer_to) {
        document.getElementById('customerSelect').value = transaction.transfer_to;
        // Find the customer display text from dropdown items
        const customerItem = document.querySelector(`#customerList .custom-dropdown-item[data-value="${transaction.transfer_to}"]`);
        if (customerItem) {
            document.getElementById('stom_customerDisplay').value = customerItem.innerText.trim();
        } else {
            document.getElementById('stom_customerDisplay').value = (transaction.transfer_to_code || transaction.transfer_to) + ' - ' + (transaction.transfer_to_name || '');
        }
    }
    
    // Set all header fields
    document.getElementById('stom_remarks').value = transaction.remarks || '';
    document.getElementById('trn_no').value = transaction.sr_no || '';
    document.getElementById('stom_gr_no').value = transaction.challan_no || '';
    document.getElementById('stom_gr_date').value = formatDateForInput(transaction.challan_date);
    document.getElementById('stom_cases').value = transaction.cases || 0;
    document.getElementById('stom_transport').value = transaction.transport || '';
    document.getElementById('summary_net').value = parseFloat(transaction.net_amount || 0).toFixed(2);
    
    // Clear existing rows
    document.getElementById('itemsTableBody').innerHTML = '';
    currentRowIndex = 0;
    itemsData = {};
    
    // Add items
    if (transaction.items && transaction.items.length > 0) {
        transaction.items.forEach(item => {
            addNewRowWithData(item);
        });
    }
    
    // Calculate totals after loading items
    calculateTotals();
    
    // Select first row if items exist
    if (transaction.items && transaction.items.length > 0) {
        setTimeout(() => selectRow(0), 100);
    }
}

function addNewRowWithData(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row_${rowIndex}`;
    row.onclick = function() { selectRow(rowIndex); };
    row.innerHTML = `
        <td><input type="text" class="form-control" name="items[${rowIndex}][code]" id="code_${rowIndex}" value="${item.item_code || item.item_id || ''}" onkeydown="handleCodeKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" id="name_${rowIndex}" value="${item.item_name || ''}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][batch]" id="batch_${rowIndex}" value="${item.batch_no || ''}" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" id="expiry_${rowIndex}" value="${item.expiry || ''}" readonly></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" id="qty_${rowIndex}" value="${item.qty || 0}" min="0" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][rate]" id="rate_${rowIndex}" value="${parseFloat(item.s_rate || item.rate || 0).toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][amount]" id="amount_${rowIndex}" value="${parseFloat(item.amount || 0).toFixed(2)}" step="0.01" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${rowIndex})"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(row);
    
    itemsData[rowIndex] = {
        id: item.item_id,
        name: item.item_name,
        packing: item.packing || '',
        company: item.company_name || '',
        unit: item.unit || '',
        location: item.location || '',
        cl_qty: item.cl_qty || 0
    };
}

function addNewRow() {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row_${rowIndex}`;
    row.onclick = function() { selectRow(rowIndex); };
    row.innerHTML = `
        <td><input type="text" class="form-control" name="items[${rowIndex}][code]" id="code_${rowIndex}" onkeydown="handleCodeKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][name]" id="name_${rowIndex}" readonly></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][batch]" id="batch_${rowIndex}" onkeydown="handleBatchKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" id="expiry_${rowIndex}" readonly></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" id="qty_${rowIndex}" value="0" min="0" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleQtyKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][rate]" id="rate_${rowIndex}" value="0" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleRateKeydown(event, ${rowIndex})" onfocus="selectRow(${rowIndex})" data-custom-enter></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][amount]" id="amount_${rowIndex}" value="0.00" step="0.01" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${rowIndex})"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(row);
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('table-active'));
    const row = document.getElementById(`row_${rowIndex}`);
    if (row) {
        row.classList.add('table-active');
        selectedRowIndex = rowIndex;
        updateFooterDetails(rowIndex);
    }
}

function updateFooterDetails(rowIndex) {
    const itemData = itemsData[rowIndex];
    if (itemData) {
        document.getElementById('detail_pack').value = itemData.packing || '';
        document.getElementById('detail_comp').value = itemData.company || '';
        document.getElementById('detail_unit').value = itemData.unit || '';
        document.getElementById('detail_lctn').value = itemData.location || '';
        document.getElementById('detail_cl_qty').value = itemData.cl_qty || '0';
        document.getElementById('detail_srno').value = rowIndex + 1;
    } else {
        document.getElementById('detail_pack').value = '';
        document.getElementById('detail_comp').value = '';
        document.getElementById('detail_unit').value = '';
        document.getElementById('detail_lctn').value = '';
        document.getElementById('detail_cl_qty').value = '0';
        document.getElementById('detail_srno').value = rowIndex + 1;
    }
}

function handleCodeKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            document.getElementById('stom_transport')?.focus();
            return;
        }
        const code = document.getElementById(`code_${rowIndex}`).value.trim();
        if (code) {
            fetchItemByCode(code, rowIndex);
        } else {
            openItemModal(rowIndex);
        }
    }
}

function handleBatchKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            document.getElementById('stom_transport')?.focus();
            return;
        }
        const itemCode = document.getElementById(`code_${rowIndex}`).value.trim();
        const batchNo = document.getElementById(`batch_${rowIndex}`).value.trim();
        if (itemCode && batchNo) {
            fetchBatchDetails(itemCode, batchNo, rowIndex);
        } else if (itemCode) {
            openBatchModal(rowIndex, itemCode);
        }
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            document.getElementById(`batch_${rowIndex}`)?.focus();
            return;
        }
        calculateRowAmount(rowIndex);
        document.getElementById(`rate_${rowIndex}`)?.focus();
        document.getElementById(`rate_${rowIndex}`)?.select();
    }
}

function handleRateKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            document.getElementById(`qty_${rowIndex}`)?.focus();
            return;
        }
        calculateRowAmount(rowIndex);
        // Check if next row exists
        const currentRow = document.getElementById(`row_${rowIndex}`);
        const nextRow = currentRow ? currentRow.nextElementSibling : null;
        if (nextRow && nextRow.id && nextRow.id.startsWith('row_')) {
            const nextRowIdx = parseInt(nextRow.id.replace('row_', ''));
            selectRow(nextRowIdx);
            const nextQty = document.getElementById(`qty_${nextRowIdx}`);
            if (nextQty) { nextQty.focus(); nextQty.select(); return; }
        }
        // No next row - trigger Insert Items
        openInsertItemsModal();
    }
}

function fetchItemByCode(code, rowIndex) {
    fetch(`<?php echo e(url('admin/items/search')); ?>?code=${encodeURIComponent(code)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items.length > 0) {
                const item = data.items[0];
                populateItemRow(rowIndex, item);
                document.getElementById(`batch_${rowIndex}`).focus();
            } else {
                openItemModal(rowIndex);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            openItemModal(rowIndex);
        });
}

function populateItemRow(rowIndex, item) {
    document.getElementById(`code_${rowIndex}`).value = item.id || item.code;
    document.getElementById(`name_${rowIndex}`).value = item.name;
    
    itemsData[rowIndex] = {
        id: item.id,
        name: item.name,
        packing: item.packing,
        company: item.mfg_by || item.company,
        unit: item.unit,
        location: item.location || '',
        cl_qty: item.closing_qty || 0
    };
    
    updateFooterDetails(rowIndex);
}

// ====== NEW MODAL COMPONENT BRIDGE ======
// ====== NEW MODAL COMPONENT BRIDGE ======
function openInsertItemsModal() {
    console.log('📦 Opening stock transfer outgoing modification item modal');
    if (typeof openItemModal_stockTransferOutgoingModItemModal === 'function') {
        openItemModal_stockTransferOutgoingModItemModal();
    } else {
        console.error('❌ Item modal function not found');
    }
}

window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('✅ Stock Transfer Outgoing Modification - Item+Batch selected:', item?.name, batch?.batch_no);
    console.log('Item data:', item);
    console.log('Batch data:', batch);
    addNewRow();
    const rowIndex = currentRowIndex - 1;
    
    populateItemRow(rowIndex, item);
    
    if (batch) {
        document.getElementById(`batch_${rowIndex}`).value = batch.batch_no || '';
        document.getElementById(`expiry_${rowIndex}`).value = batch.expiry || '';
        document.getElementById(`rate_${rowIndex}`).value = parseFloat(batch.s_rate || 0).toFixed(2);
        if (itemsData[rowIndex]) {
            itemsData[rowIndex].cl_qty = batch.qty || 0;
            updateFooterDetails(rowIndex);
        }
    }
    
    document.getElementById(`qty_${rowIndex}`).focus();
    calculateRowAmount(rowIndex);
};

window.onBatchSelectedFromModal = function(item, batch) {
    window.onItemBatchSelectedFromModal(item, batch);
};

window.onItemSelectedFromModal = function(item) {
    console.log('🔗 Item selected, opening batch modal for:', item?.name);
    if (typeof openBatchModal_stockTransferOutgoingModBatchModal === 'function') {
        openBatchModal_stockTransferOutgoingModBatchModal(item);
    } else {
        console.error('❌ Batch modal function not found');
    }
};
// ====== END MODAL COMPONENT BRIDGE ======

// Old openItemModal renamed to avoid conflict with button call on line 189
function openItemModal(rowIndex) {
    console.log('📦 Opening item modal for row:', rowIndex);
    // For row-based item selection, use new component
    if (typeof openItemModal_stockTransferOutgoingModItemModal === 'function') {
        selectedRowIndex = rowIndex;
        openItemModal_stockTransferOutgoingModItemModal();
        return;
    }
    // Fallback to legacy modal
    console.warn('⚠️ Falling back to legacy item modal');
    _legacy_openItemModal(rowIndex);
}

function _legacy_openItemModal(rowIndex) {
    selectedRowIndex = rowIndex;
    document.getElementById('itemModalBackdrop').classList.add('show');
    document.getElementById('itemModal').classList.add('show');
    document.getElementById('itemSearchInput').value = '';
    document.getElementById('itemSearchResults').innerHTML = '';
    document.getElementById('itemSearchInput').focus();
}

function closeItemModal() {
    document.getElementById('itemModalBackdrop').classList.remove('show');
    document.getElementById('itemModal').classList.remove('show');
}

function searchItems() {
    const query = document.getElementById('itemSearchInput').value.trim();
    if (query.length < 1) {
        document.getElementById('itemSearchResults').innerHTML = '';
        return;
    }
    
    fetch(`<?php echo e(url('admin/items/search')); ?>?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('itemSearchResults');
            tbody.innerHTML = '';
            if (data.success && data.items.length > 0) {
                data.items.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.style.cursor = 'pointer';
                    tr.onclick = function() { selectItem(item); };
                    tr.innerHTML = `<td>${item.id}</td><td>${item.name}</td><td>${item.packing || ''}</td><td>${item.mfg_by || ''}</td>`;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No items found</td></tr>';
            }
        });
}

function selectItem(item) {
    populateItemRow(selectedRowIndex, item);
    closeItemModal();
    document.getElementById(`batch_${selectedRowIndex}`).focus();
}

function openBatchModal(rowIndex, itemCode) {
    selectedRowIndex = rowIndex;
    fetch(`<?php echo e(url('admin/batches/by-item')); ?>/${itemCode}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('batchSearchResults');
            tbody.innerHTML = '';
            if (data.success && data.batches.length > 0) {
                data.batches.forEach(batch => {
                    const tr = document.createElement('tr');
                    tr.style.cursor = 'pointer';
                    tr.onclick = function() { selectBatch(batch); };
                    tr.innerHTML = `<td>${batch.batch_no}</td><td>${batch.expiry || ''}</td><td>${parseFloat(batch.mrp || 0).toFixed(2)}</td><td>${parseFloat(batch.s_rate || batch.pur_rate || 0).toFixed(2)}</td><td>${batch.qty || 0}</td>`;
                    tbody.appendChild(tr);
                });
                document.getElementById('batchModalBackdrop').classList.add('show');
                document.getElementById('batchModal').classList.add('show');
            } else {
                alert('No batches found for this item');
            }
        });
}

function closeBatchModal() {
    document.getElementById('batchModalBackdrop').classList.remove('show');
    document.getElementById('batchModal').classList.remove('show');
}

function selectBatch(batch) {
    document.getElementById(`batch_${selectedRowIndex}`).value = batch.batch_no;
    document.getElementById(`expiry_${selectedRowIndex}`).value = batch.expiry || '';
    document.getElementById(`rate_${selectedRowIndex}`).value = parseFloat(batch.s_rate || batch.pur_rate || 0).toFixed(2);
    
    if (itemsData[selectedRowIndex]) {
        itemsData[selectedRowIndex].cl_qty = batch.qty || 0;
        updateFooterDetails(selectedRowIndex);
    }
    
    closeBatchModal();
    document.getElementById(`qty_${selectedRowIndex}`).focus();
    calculateRowAmount(selectedRowIndex);
}

function fetchBatchDetails(itemCode, batchNo, rowIndex) {
    fetch(`<?php echo e(url('admin/batches/details')); ?>?item_id=${itemCode}&batch_no=${encodeURIComponent(batchNo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.batch) {
                document.getElementById(`expiry_${rowIndex}`).value = data.batch.expiry || '';
                document.getElementById(`rate_${rowIndex}`).value = parseFloat(data.batch.s_rate || data.batch.pur_rate || 0).toFixed(2);
                if (itemsData[rowIndex]) {
                    itemsData[rowIndex].cl_qty = data.batch.qty || 0;
                    updateFooterDetails(rowIndex);
                }
                document.getElementById(`qty_${rowIndex}`).focus();
                calculateRowAmount(rowIndex);
            } else {
                // Batch doesn't exist - open create batch modal
                openCreateBatchModal(rowIndex, batchNo);
            }
        })
        .catch(() => openCreateBatchModal(rowIndex, batchNo));
}

function calculateRowAmount(rowIndex) {
    const qty = parseFloat(document.getElementById(`qty_${rowIndex}`).value) || 0;
    const rate = parseFloat(document.getElementById(`rate_${rowIndex}`).value) || 0;
    const amount = qty * rate;
    document.getElementById(`amount_${rowIndex}`).value = amount.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let totalNet = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const amountInput = row.querySelector('input[name$="[amount]"]');
        if (amountInput) {
            totalNet += parseFloat(amountInput.value) || 0;
        }
    });
    document.getElementById('summary_net').value = totalNet.toFixed(2);
}

function deleteRow(rowIndex) {
    const row = document.getElementById(`row_${rowIndex}`);
    if (row) {
        row.remove();
        delete itemsData[rowIndex];
        calculateTotals();
    }
}

function deleteSelectedItem() {
    if (selectedRowIndex !== null) {
        deleteRow(selectedRowIndex);
        selectedRowIndex = null;
    } else {
        alert('Please select a row to delete');
    }
}

function cancelModification() {
    if (confirm('Are you sure you want to cancel?')) {
        window.location.href = '<?php echo e(route("admin.stock-transfer-outgoing.index")); ?>';
    }
}

let isSubmitting = false;

function updateTransaction() {
    if (!loadedTransactionId) {
        alert('Please load a transaction first');
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
    updateBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Updating...';
    
    const form = document.getElementById('stockTransferOutgoingForm');
    
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const codeInput = row.querySelector('input[name$="[code]"]');
        const nameInput = row.querySelector('input[name$="[name]"]');
        if (codeInput && nameInput && codeInput.value && nameInput.value) {
            const rowIndex = codeInput.name.match(/\[(\d+)\]/)[1];
            items.push({
                code: codeInput.value,
                name: nameInput.value,
                batch: document.getElementById(`batch_${rowIndex}`).value,
                expiry: document.getElementById(`expiry_${rowIndex}`).value,
                qty: document.getElementById(`qty_${rowIndex}`).value,
                rate: document.getElementById(`rate_${rowIndex}`).value,
                amount: document.getElementById(`amount_${rowIndex}`).value
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item');
        isSubmitting = false;
        updateBtn.disabled = false;
        updateBtn.innerHTML = originalBtnHtml;
        return;
    }
    
    const data = {
        _token: '<?php echo e(csrf_token()); ?>',
        transaction_date: form.querySelector('[name="transaction_date"]').value,
        transfer_to: form.querySelector('[name="transfer_to"]').value,
        transfer_to_name: form.querySelector('[name="transfer_to_name"]').value,
        remarks: form.querySelector('[name="remarks"]').value,
        gr_no: form.querySelector('[name="gr_no"]').value,
        gr_date: form.querySelector('[name="gr_date"]').value,
        cases: form.querySelector('[name="cases"]').value,
        transport: form.querySelector('[name="transport"]').value,
        summary_net: document.getElementById('summary_net').value,
        items: items
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch(`<?php echo e(url('admin/stock-transfer-outgoing/transaction')); ?>/${loadedTransactionId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Transaction updated successfully!');
            window.location.href = '<?php echo e(route("admin.stock-transfer-outgoing.index")); ?>';
        } else {
            alert('Error: ' + result.message);
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

// ====== CUSTOM CUSTOMER DROPDOWN ======
let customerActiveIndex = -1;

function openCustomerDropdown() {
    document.getElementById('customerList').style.display = 'block';
    customerActiveIndex = -1;
    highlightCustomerItem();
}

function closeCustomerDropdown() {
    setTimeout(() => {
        const list = document.getElementById('customerList');
        if(list) list.style.display = 'none';
        customerActiveIndex = -1;
    }, 200);
}

function filterCustomers(e) {
    if (['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;
    
    const input = e.target;
    const filter = input.value.toLowerCase();
    const items = document.querySelectorAll('#customerList .custom-dropdown-item');
    
    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        if (text.indexOf(filter) > -1) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
    customerActiveIndex = -1;
    highlightCustomerItem();
}

function selectCustomer(id, name, code) {
    document.getElementById('customerSelect').value = id;
    document.getElementById('stom_customerDisplay').value = code + ' - ' + name;
    document.getElementById('transfer_to_name').value = name;
    document.getElementById('customerList').style.display = 'none';
    customerActiveIndex = -1;
    
    // Jump to remarks
    document.querySelector('input[name="remarks"]')?.focus();
}

function highlightCustomerItem() {
    const items = Array.from(document.querySelectorAll('#customerList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
    items.forEach(i => i.classList.remove('active'));
    
    if (customerActiveIndex >= items.length) customerActiveIndex = 0;
    if (customerActiveIndex < -1) customerActiveIndex = items.length - 1;
    
    if (customerActiveIndex >= 0 && items[customerActiveIndex]) {
        items[customerActiveIndex].classList.add('active');
        items[customerActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

// Update Customer Name (for compatibility)
function updateCustomerName() {
    // No-op: selectCustomer handles this now
}

// Set customer dropdown value when loading transaction
function setCustomerDropdown(customerId) {
    if (customerId) {
        document.getElementById('customerSelect').value = customerId;
        const customerItem = document.querySelector(`#customerList .custom-dropdown-item[data-value="${customerId}"]`);
        if (customerItem) {
            document.getElementById('stom_customerDisplay').value = customerItem.innerText.trim();
            document.getElementById('transfer_to_name').value = customerItem.getAttribute('data-name') || '';
        }
    }
}

// Global Click - close customer dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#customerDropdownWrapper')) {
        const listContainer = document.getElementById('customerList');
        if (listContainer) listContainer.style.display = 'none';
    }
});

// ====== KEYBOARD NAVIGATION ======
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const activeEl = document.activeElement;
        if (!activeEl) return;
        
        // Skip if modal is open
        const hasModalOpen = document.querySelector(
            '#itemModal.show, #batchModal.show, #createBatchModal.show, #invoicesModal.show, #dateRangeModal.show, ' +
            '#stockTransferOutgoingModItemModal.show, #stockTransferOutgoingModBatchModal.show'
        );
        if (hasModalOpen) return;

        // Shift+Enter backward navigation
        if (e.shiftKey) {
            if (activeEl.id === 'stom_customerDisplay') {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById('stom_transaction_date')?.focus();
                return false;
            }
            if (activeEl.id === 'stom_remarks') {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById('stom_customerDisplay')?.focus();
                return false;
            }
            if (activeEl.id === 'stom_gr_no') {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById('stom_remarks')?.focus();
                return false;
            }
            if (activeEl.id === 'stom_gr_date') {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById('stom_gr_no')?.focus();
                return false;
            }
            if (activeEl.id === 'stom_cases') {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById('stom_gr_date')?.focus();
                return false;
            }
            if (activeEl.id === 'stom_transport') {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.getElementById('stom_cases')?.focus();
                return false;
            }
            return;
        }

        // Sr No → Load button
        if (activeEl.id === 'search_sr_no') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stom_loadBtn')?.focus();
            return false;
        }
        // Load button → trigger load (same as click), then focus All Invoices
        if (activeEl.id === 'stom_loadBtn') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            loadTransaction();
            // After load, focus All Invoices button
            setTimeout(() => {
                document.getElementById('stom_allInvoicesBtn')?.focus();
            }, 500);
            return false;
        }
        // All Invoices button → trigger All Invoices modal
        if (activeEl.id === 'stom_allInvoicesBtn') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            openAllInvoicesModal();
            return false;
        }

        // Customer Dropdown Intercept
        if (activeEl.id === 'stom_customerDisplay') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const listContainer = document.getElementById('customerList');
            if (listContainer && listContainer.style.display === 'block') {
                const items = Array.from(document.querySelectorAll('#customerList .custom-dropdown-item')).filter(i => i.style.display !== 'none');
                if (customerActiveIndex >= 0 && customerActiveIndex < items.length) {
                    items[customerActiveIndex].click();
                } else {
                    listContainer.style.display = 'none';
                    customerActiveIndex = -1;
                    document.getElementById('stom_remarks')?.focus();
                }
            } else {
                document.getElementById('stom_remarks')?.focus();
            }
            return false;
        }

        // Forward navigation: Date → Name
        if (activeEl.id === 'stom_transaction_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const customerDisplay = document.getElementById('stom_customerDisplay');
            if (customerDisplay) {
                customerDisplay.focus();
                setTimeout(() => { openCustomerDropdown(); }, 50);
            }
            return false;
        }
        // Remarks → GR No.
        if (activeEl.id === 'stom_remarks') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stom_gr_no')?.focus();
            return false;
        }
        // GR No. → GR Date
        if (activeEl.id === 'stom_gr_no') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stom_gr_date')?.focus();
            return false;
        }
        // GR Date → Cases
        if (activeEl.id === 'stom_gr_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stom_cases')?.focus();
            return false;
        }
        // Cases → Transport
        if (activeEl.id === 'stom_cases') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('stom_transport')?.focus();
            return false;
        }
        // Transport → first row Qty (if items exist) OR Insert Items
        if (activeEl.id === 'stom_transport') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const firstRow = document.querySelector('#itemsTableBody tr');
            if (firstRow) {
                const qtyInput = firstRow.querySelector('input[name*="[qty]"]');
                if (qtyInput) {
                    const rowIdx = parseInt(firstRow.id.replace('row_', ''));
                    selectRow(rowIdx);
                    qtyInput.focus();
                    qtyInput.select();
                    return false;
                }
            }
            const addBtn = document.getElementById('stom_insertItemsBtn');
            if (addBtn) { addBtn.focus(); addBtn.click(); }
            return false;
        }
        // Insert Items button
        if (activeEl.id === 'stom_insertItemsBtn') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (typeof openInsertItemsModal === 'function') openInsertItemsModal();
            return false;
        }
    }
    
    // Dropdown arrow navigation
    if (document.activeElement && document.activeElement.id === 'stom_customerDisplay') {
        const listContainer = document.getElementById('customerList');
        if (listContainer && listContainer.style.display === 'block') {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                customerActiveIndex++;
                highlightCustomerItem();
                return false;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                customerActiveIndex--;
                highlightCustomerItem();
                return false;
            } else if (e.key === 'Escape') {
                e.preventDefault();
                closeCustomerDropdown();
                return false;
            }
        }
    }

    // Ctrl+S shortcut to save/update
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault();
        updateTransaction();
        return false;
    }
}, true);

// openInsertItemsModal is defined in the MODAL COMPONENT BRIDGE section above

// Handle batch check when entering manually
function checkBatchExists(rowIndex) {
    const batchInput = document.getElementById(`batch_${rowIndex}`);
    const codeInput = document.getElementById(`code_${rowIndex}`);
    
    if (!batchInput || !codeInput) return;
    
    const batchNo = batchInput.value.trim();
    const itemCode = codeInput.value.trim();
    
    if (!batchNo || !itemCode) return;
    
    // Check if batch exists
    fetch(`<?php echo e(url('admin/batches/details')); ?>?item_id=${itemCode}&batch_no=${encodeURIComponent(batchNo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.batch) {
                // Batch exists - populate details
                document.getElementById(`expiry_${rowIndex}`).value = data.batch.expiry || '';
                document.getElementById(`rate_${rowIndex}`).value = parseFloat(data.batch.s_rate || data.batch.pur_rate || 0).toFixed(2);
                if (itemsData[rowIndex]) {
                    itemsData[rowIndex].cl_qty = data.batch.qty || 0;
                    updateFooterDetails(rowIndex);
                }
                document.getElementById(`qty_${rowIndex}`).focus();
            } else {
                // Batch doesn't exist - open create batch modal
                openCreateBatchModal(rowIndex, batchNo);
            }
        })
        .catch(() => {
            // On error, open create batch modal
            openCreateBatchModal(rowIndex, batchNo);
        });
}

// Open Create Batch Modal
function openCreateBatchModal(rowIndex, batchNo) {
    const itemData = itemsData[rowIndex] || {};
    
    document.getElementById('newBatchItemName').textContent = itemData.name || '';
    document.getElementById('newBatchItemPacking').textContent = itemData.packing || '';
    document.getElementById('newBatchNo').value = batchNo;
    document.getElementById('newBatchExpiry').value = '';
    document.getElementById('newBatchMRP').value = '0.00';
    document.getElementById('newBatchSRate').value = '0.00';
    document.getElementById('newBatchPRate').value = '0.00';
    document.getElementById('newBatchWSRate').value = '0.00';
    document.getElementById('newBatchRowIndex').value = rowIndex;
    document.getElementById('newBatchItemId').value = itemData.id || '';
    
    document.getElementById('createBatchBackdrop').classList.add('show');
    document.getElementById('createBatchModal').classList.add('show');
    document.getElementById('newBatchExpiry').focus();
}

// Close Create Batch Modal
function closeCreateBatchModal() {
    document.getElementById('createBatchBackdrop').classList.remove('show');
    document.getElementById('createBatchModal').classList.remove('show');
}

// Save New Batch
function saveNewBatch() {
    const rowIndex = document.getElementById('newBatchRowIndex').value;
    const batchNo = document.getElementById('newBatchNo').value;
    const expiry = document.getElementById('newBatchExpiry').value;
    const mrp = document.getElementById('newBatchMRP').value;
    const sRate = document.getElementById('newBatchSRate').value;
    const pRate = document.getElementById('newBatchPRate').value;
    const itemId = document.getElementById('newBatchItemId').value;
    
    if (!batchNo) {
        alert('Batch number is required');
        return;
    }
    
    // Update row with batch details
    document.getElementById(`batch_${rowIndex}`).value = batchNo;
    document.getElementById(`expiry_${rowIndex}`).value = expiry;
    document.getElementById(`rate_${rowIndex}`).value = parseFloat(sRate).toFixed(2);
    
    // Store batch data for saving later
    const row = document.getElementById(`row_${rowIndex}`);
    if (row) {
        row.dataset.newBatch = 'true';
        row.dataset.newBatchData = JSON.stringify({
            batch_no: batchNo,
            expiry: expiry,
            mrp: parseFloat(mrp),
            s_rate: parseFloat(sRate),
            pur_rate: parseFloat(pRate)
        });
    }
    
    closeCreateBatchModal();
    document.getElementById(`qty_${rowIndex}`).focus();
    calculateRowAmount(rowIndex);
}

// ==================== LOAD INVOICES FUNCTIONALITY ====================

// Open Date Range Modal
function openDateRangeModal() {
    document.getElementById('dateRangeBackdrop').classList.add('show');
    document.getElementById('dateRangeModal').classList.add('show');
}

// Close Date Range Modal
function closeDateRangeModal() {
    document.getElementById('dateRangeBackdrop').classList.remove('show');
    document.getElementById('dateRangeModal').classList.remove('show');
}

// Apply Date Filter
function applyDateFilter() {
    const fromDate = document.getElementById('filterFromDate').value;
    const toDate = document.getElementById('filterToDate').value;
    
    if (!fromDate || !toDate) {
        alert('Please select both From and To dates');
        return;
    }
    
    closeDateRangeModal();
    loadInvoices(fromDate, toDate);
}

// Open All Invoices Modal
function openAllInvoicesModal() {
    loadInvoices(); // Load all invoices without date filter
}

// Close Invoices Modal
function closeInvoicesModal() {
    document.getElementById('invoicesBackdrop').classList.remove('show');
    document.getElementById('invoicesModal').classList.remove('show');
    invoiceActiveIndex = -1;
}

// Load Invoices (with optional date filter)
async function loadInvoices(fromDate = null, toDate = null) {
    const modal = document.getElementById('invoicesModal');
    const backdrop = document.getElementById('invoicesBackdrop');
    const tbody = document.getElementById('invoicesTableBody');
    
    // Show loading
    tbody.innerHTML = '<tr><td colspan="7" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
    backdrop.classList.add('show');
    modal.classList.add('show');
    
    try {
        let url = '<?php echo e(route("admin.stock-transfer-outgoing.index")); ?>?ajax=1';
        if (fromDate && toDate) {
            url += `&date_from=${fromDate}&date_to=${toDate}`;
        }
        
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) throw new Error('Failed to load invoices');
        
        const data = await response.json();
        
        if (data.transactions && data.transactions.length > 0) {
            tbody.innerHTML = data.transactions.map(txn => `
                <tr onclick="selectInvoice('${txn.sr_no}')" style="cursor: pointer;" data-sr-no="${txn.sr_no}">
                    <td>${txn.sr_no}</td>
                    <td>${formatDate(txn.transaction_date)}</td>
                    <td>${txn.transfer_to_name || 'N/A'}</td>
                    <td>${txn.challan_no || '-'}</td>
                    <td class="text-end">${parseFloat(txn.net_amount || 0).toFixed(2)}</td>
                    <td>${txn.remarks || '-'}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-primary" onclick="event.stopPropagation(); selectInvoice('${txn.sr_no}')">
                            <i class="bi bi-check-circle"></i> Select
                        </button>
                    </td>
                </tr>
            `).join('');
            // Auto-highlight first row
            invoiceActiveIndex = 0;
            highlightInvoiceRow();
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No invoices found</td></tr>';
        }
    } catch (error) {
        console.error('Error loading invoices:', error);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading invoices</td></tr>';
    }
}

// Select Invoice and Load
function selectInvoice(srNo) {
    closeInvoicesModal();
    document.getElementById('search_sr_no').value = srNo;
    loadTransaction();
    // Focus Date field after loading
    setTimeout(() => {
        document.getElementById('stom_transaction_date')?.focus();
    }, 500);
}

// ====== INVOICE MODAL KEYBOARD NAVIGATION ======
let invoiceActiveIndex = -1;

function highlightInvoiceRow() {
    const rows = document.querySelectorAll('#invoicesTableBody tr[data-sr-no]');
    rows.forEach(r => r.classList.remove('invoice-row-active'));
    
    if (invoiceActiveIndex >= rows.length) invoiceActiveIndex = 0;
    if (invoiceActiveIndex < 0) invoiceActiveIndex = rows.length - 1;
    
    if (invoiceActiveIndex >= 0 && rows[invoiceActiveIndex]) {
        rows[invoiceActiveIndex].classList.add('invoice-row-active');
        rows[invoiceActiveIndex].scrollIntoView({ block: 'nearest' });
    }
}

// Invoice modal keyboard handler
document.addEventListener('keydown', function(e) {
    const invoicesModal = document.getElementById('invoicesModal');
    if (!invoicesModal || !invoicesModal.classList.contains('show')) return;
    
    const rows = document.querySelectorAll('#invoicesTableBody tr[data-sr-no]');
    if (!rows.length) return;
    
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        invoiceActiveIndex++;
        highlightInvoiceRow();
        return false;
    }
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        invoiceActiveIndex--;
        highlightInvoiceRow();
        return false;
    }
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (invoiceActiveIndex >= 0 && rows[invoiceActiveIndex]) {
            const srNo = rows[invoiceActiveIndex].getAttribute('data-sr-no');
            if (srNo) selectInvoice(srNo);
        }
        return false;
    }
    if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        closeInvoicesModal();
        return false;
    }
}, true);

// Format Date Helper for display (dd/mm/yyyy)
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-GB');
}

// Format Date for HTML date input (yyyy-MM-dd)
function formatDateForInput(dateStr) {
    if (!dateStr) return '';
    // Handle ISO format like "2025-12-15T00:00:00.000000Z"
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return '';
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/stock-transfer-outgoing/modification.blade.php ENDPATH**/ ?>