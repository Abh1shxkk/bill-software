@extends('layouts.admin')

@section('title', 'Quotation - Transaction')

@push('styles')
<style>
    .qt-form { font-size: 11px; }
    .qt-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .qt-form input, .qt-form select, .qt-form textarea { font-size: 11px; padding: 2px 6px; height: 26px; }
    .qt-form textarea { height: auto; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #90EE90; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .summary-section { background: #ffcccc; padding: 5px 10px; }
    .footer-section { background: #ffe4b5; padding: 8px; }
    .row-selected { background-color: #d4edff !important; border: 2px solid #007bff !important; }
    .batch-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; }
    .batch-modal-backdrop.show { display: block; }
    .batch-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
    .batch-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #28a745; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: right; }
    .item-row:hover { background-color: #e3f2fd !important; cursor: pointer; }
    
    /* Custom Dropdown Styles */
    .custom-dropdown-container { position: relative; width: 100%; }
    .custom-dropdown-list { display: none; position: absolute; z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ced4da; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top: 2px; }
    .custom-dropdown-item { padding: 6px 10px; cursor: pointer; font-size: 11px; }
    .custom-dropdown-item:hover, .custom-dropdown-item.active { background-color: #007bff; color: white; }
</style>
@endpush

@section('content')
<section class="qt-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Quotation - Transaction</h4>
                <div class="text-muted small">Create new quotation (No batch/stock impact)</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.quotation.modification') }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> Modification
                </a>
                <a href="{{ route('admin.quotation.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="qtForm" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" id="quotation_id" name="quotation_id" value="">
                    
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 40px;">Date :</label>
                                    <input type="date" id="quotation_date" name="date" class="form-control" value="{{ date('Y-m-d') }}" onchange="updateDayName()" required>
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;"></label>
                                    <input type="text" id="day_name" class="form-control readonly-field text-center" value="{{ date('l') }}" readonly style="width: 100px;">
                                </div>
                                <div class="field-group mt-1">
                                    <label style="width: 40px;">T.No :</label>
                                    <input type="text" id="quotation_no" class="form-control readonly-field" value="{{ $nextQuotationNo ?? '' }}" readonly style="width: 100px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group mb-1">
                                    <label style="width: 50px;">Name :</label>
                                    <div class="custom-dropdown-container w-100">
                                        <input type="text" id="customerSearchInput" class="form-control" placeholder="Search Customer..." autocomplete="off">
                                        <input type="hidden" id="customer_id" name="customer_id">
                                        <div id="customerDropdown" class="custom-dropdown-list">
                                            @foreach($customers as $customer)
                                            <div class="custom-dropdown-item" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->name }}</div>
                                            @endforeach
                                        </div>
                                    </div>
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

                    <!-- Items Table -->
                    <div class="bg-white border rounded p-2 mb-2">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" id="itemsTableContainer">
                            <table class="table table-bordered table-compact">
                                <thead style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th style="width: 70px;">Code</th>
                                        <th style="width: 200px;">Item Name</th>
                                        <th style="width: 80px;">Batch</th>
                                        <th style="width: 70px;">Expiry</th>
                                        <th style="width: 60px;">Qty</th>
                                        <th style="width: 80px;">Rate</th>
                                        <th style="width: 80px;">MRP</th>
                                        <th style="width: 100px;">Amount</th>
                                        <th style="width: 40px;">X</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2 d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-success" id="addRowBtn" onclick="addNewRow()">
                                <i class="bi bi-plus-circle"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="addItemsBtn" onclick="showAddItemsModal()">
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
                            <div class="col-md-2">
                                <div class="field-group mb-1">
                                    <label style="width: 45px;">Comp :</label>
                                    <input type="text" id="company" class="form-control readonly-field" readonly>
                                </div>
                                <div class="field-group">
                                    <label style="width: 40px;">Lctn :</label>
                                    <input type="text" id="location" class="form-control readonly-field" readonly>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
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
                            <button type="button" class="btn btn-success" onclick="saveQuotation()">
                                <i class="bi bi-save"></i> Save (End)
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteSelectedItem()">
                                <i class="bi bi-trash"></i> Delete Item
                            </button>
                            <button type="button" class="btn btn-primary" onclick="addNewRow()">
                                <i class="bi bi-plus-circle"></i> Insert Item
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="cancelQuotation()">
                                <i class="bi bi-x-circle"></i> Cancel Quotation
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
    'id' => 'chooseItemsModal',
    'module' => 'quotation',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'batchSelectionModal',
])

@include('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'quotation',
    'showOnlyAvailable' => false,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])

@endsection

@push('scripts')
<script>
let currentRowIndex = 0;
let itemsData = []; // Kept for storing selected items for batch processing
let selectedRowIndex = null;
let currentPage = 1;
let isLoadingItems = false;
let hasMoreItems = true;
let currentSearchTerm = '';

// Callback function when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('Item selected from reusable modal:', item);
    console.log('Batch selected from reusable modal:', batch);
    
    const tbody = document.getElementById('itemsTableBody');
    let rowIndex = currentRowIndex;
    let row;
    
    // Re-use currently selected empty row if requested from an existing row
    if (selectedRowIndex !== null && document.getElementById(`row-${selectedRowIndex}`)) {
        rowIndex = selectedRowIndex;
        row = document.getElementById(`row-${rowIndex}`);
    } else {
        // Create a new row if coming straight from "Add Items" outside of the grid
        rowIndex = currentRowIndex++;
        row = document.createElement('tr');
        row.id = `row-${rowIndex}`;
        tbody.appendChild(row);
    }
    
    // Format expiry date
    let expiryDisplay = '';
    if (batch.expiry_date) {
        try {
            const expiryDate = new Date(batch.expiry_date);
            expiryDisplay = `${String(expiryDate.getMonth() + 1).padStart(2, '0')}/${String(expiryDate.getFullYear()).slice(-2)}`;
        } catch (e) {
            expiryDisplay = batch.expiry_date;
        }
    }
    
    // Use sale rate for quotations
    const saleRate = parseFloat(batch.s_rate || batch.avg_s_rate || item.s_rate || 0);
    const mrp = parseFloat(batch.mrp || batch.avg_mrp || item.mrp || 0);
    
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.batchId = batch.id;
    row.dataset.itemData = JSON.stringify(item);
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.bar_code || item.id || ''}" readonly onkeydown="handleCodeKeydown(event, ${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][item_name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batch.batch_no || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${expiryDisplay}" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][qty]" value="1" step="0.001" onkeydown="handleQtyKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][rate]" value="${saleRate.toFixed(2)}" step="0.01" onkeydown="handleRateKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][mrp]" value="${mrp.toFixed(2)}" step="0.01" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end readonly-field" name="items[${rowIndex}][amount]" value="${saleRate.toFixed(2)}" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batch.id}">
            <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="${item.company_name || ''}">
            <input type="hidden" name="items[${rowIndex}][unit]" value="${item.unit || ''}">
        </td>
    `;
    
    // Update footer display
    if (typeof updateFooter === 'function') {
        updateFooter(item);
    }
    
    // Calculate row amount and totals
    if (typeof calculateRowAmount === 'function') calculateRowAmount(rowIndex);
    if (typeof calculateTotalAmount === 'function') calculateTotalAmount();
    
    // Focus qty field
    const qtyInput = row.querySelector('input[name*="[qty]"]');
    if (qtyInput) {
        qtyInput.focus();
        qtyInput.select();
    }
    
    // Reset selectedRowIndex
    selectedRowIndex = null;
};

document.addEventListener('DOMContentLoaded', function() {
    // Don't add empty row on load - rows will be created when items are selected
    // Initialize date/day if needed
    updateDayName();
    
    // Keyboard navigation handlers
    const qtDate = document.getElementById('quotation_date');
    const custInput = document.getElementById('customerSearchInput');
    const remarks = document.getElementById('remarks');
    const terms = document.getElementById('terms');
    const disc = document.getElementById('discount_percent');

    if (qtDate) {
        qtDate.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                // setTimeout ensures this Enter event is 100% done
                // before customerSearchInput gets focus + dropdown opens
                setTimeout(() => {
                    if (custInput) {
                        custInput.value = '';
                        custInput.focus();
                    }
                }, 10);
            }
        }, true);
    }

    if (remarks) {
        remarks.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (e.shiftKey && custInput) { custInput.focus(); }
                else if (!e.shiftKey && terms) { terms.focus(); }
            }
        });
    }

    if (terms) {
        terms.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (e.shiftKey && remarks) { remarks.focus(); }
                else if (!e.shiftKey) { showAddItemsModal(); }
            }
        });
    }

    if (disc) {
        disc.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (e.shiftKey && terms) { terms.focus(); }
                else if (!e.shiftKey) { 
                    // Open Add Items modal directly instead of just focusing the button
                    const btn = document.getElementById('addItemsBtn');
                    if(btn) btn.focus();
                    showAddItemsModal();
                }
            }
        });
    }

    const addItemsBtn = document.getElementById('addItemsBtn');
    if (addItemsBtn) {
        addItemsBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                showAddItemsModal();
            }
        });
    }
});

function updateDayName() {
    const dateInput = document.getElementById('quotation_date');
    const dayInput = document.getElementById('day_name');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayInput.value = days[date.getDay()];
    }
}

// ============== CUSTOM CUSTOMER DROPDOWN ==============
const customerSearchInput = document.getElementById('customerSearchInput');
const customerIdInput     = document.getElementById('customer_id');
const customerNameInput   = document.getElementById('customer_name');
const customerDropdown    = document.getElementById('customerDropdown');

// Track highlighted item as a DIRECT DOM reference — no index sync issues
let _customerHighlighted = null;

function getVisibleCustomerItems() {
    if (!customerDropdown) return [];
    return Array.from(customerDropdown.querySelectorAll('.custom-dropdown-item'))
        .filter(el => el.style.display !== 'none');
}

function setHighlightedCustomer(el) {
    // Clear previous
    if (_customerHighlighted) _customerHighlighted.classList.remove('active');
    _customerHighlighted = el || null;
    if (_customerHighlighted) {
        _customerHighlighted.classList.add('active');
        _customerHighlighted.scrollIntoView({ block: 'nearest' });
    }
}

function moveHighlight(direction) {
    const items = getVisibleCustomerItems();
    if (!items.length) return;
    const idx = _customerHighlighted ? items.indexOf(_customerHighlighted) : -1;
    let next = idx + direction;
    if (next < 0) next = 0;
    if (next >= items.length) next = items.length - 1;
    setHighlightedCustomer(items[next]);
}

function filterCustomers(searchTerm) {
    if (!customerDropdown) return;
    const norm = (searchTerm || '').toLowerCase();
    customerDropdown.querySelectorAll('.custom-dropdown-item').forEach(item => {
        const name = (item.getAttribute('data-name') || '').toLowerCase();
        item.style.display = name.includes(norm) ? 'block' : 'none';
    });
    setHighlightedCustomer(null); // reset highlight after filter
}

function selectCustomerItem(item) {
    if (!item) return false;
    const id   = item.getAttribute('data-id');
    const name = item.getAttribute('data-name');
    customerSearchInput.value = name;
    customerIdInput.value     = id;
    if (customerNameInput) customerNameInput.value = name;
    customerDropdown.style.display = 'none';
    setHighlightedCustomer(null);
    setTimeout(() => {
        const remarks = document.getElementById('remarks');
        if (remarks) remarks.focus();
    }, 50);
    return true;
}

// ── Window-level CAPTURE handler for customer dropdown keys ─────
// Fires FIRST (capture phase). Only acts when dropdown is visible
// AND customerSearchInput has focus — avoids interfering with other fields.
window.addEventListener('keydown', function(e) {
    // Only handle when customer dropdown is open
    if (!customerDropdown || customerDropdown.style.display === 'none') return;
    // Only handle when focus is inside the customer search input
    if (document.activeElement !== customerSearchInput) return;
    if (!['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) return;

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    if (e.key === 'ArrowDown') { moveHighlight(1); return; }
    if (e.key === 'ArrowUp')   { moveHighlight(-1); return; }
    if (e.key === 'Escape') {
        customerDropdown.style.display = 'none';
        setHighlightedCustomer(null);
        return;
    }
    if (e.key === 'Enter') {
        if (e.shiftKey) {
            customerDropdown.style.display = 'none';
            setHighlightedCustomer(null);
            const qtDate = document.getElementById('quotation_date');
            if (qtDate) qtDate.focus();
            return;
        }
        if (_customerHighlighted) {
            // Highlighted item exists → select immediately (single Enter)
            selectCustomerItem(_customerHighlighted);
        } else {
            // Nothing highlighted — highlight first item
            const items = getVisibleCustomerItems();
            if (items.length > 0) {
                setHighlightedCustomer(items[0]);
            } else {
                customerDropdown.style.display = 'none';
                const remarks = document.getElementById('remarks');
                if (remarks) remarks.focus();
            }
        }
    }
}, true /* capture phase = fires before all other handlers */);

if (customerSearchInput) {
    customerSearchInput.addEventListener('focus', function() {
        filterCustomers(this.value);
        customerDropdown.style.display = 'block';
        customerSearchInput.select();
        // Auto-highlight the first visible item immediately
        // so ONE Enter press is enough to select
        const items = getVisibleCustomerItems();
        if (items.length > 0) {
            setHighlightedCustomer(items[0]);
        }
    });

    customerSearchInput.addEventListener('input', function() {
        filterCustomers(this.value);
        customerDropdown.style.display = 'block';
        customerIdInput.value = '';
        if (customerNameInput) customerNameInput.value = '';
    });

    customerDropdown.addEventListener('mousedown', function(e) {
        const item = e.target.closest('.custom-dropdown-item');
        if (item) { e.preventDefault(); selectCustomerItem(item); }
    });

    document.addEventListener('click', function(e) {
        if (!customerSearchInput.contains(e.target) && !customerDropdown.contains(e.target)) {
            customerDropdown.style.display = 'none';
        }
    });
}
// ====================================================

// Debounce helper
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Search items via AJAX
function searchItemsAjax(search = '', page = 1) {
    if (isLoadingItems) return;
    isLoadingItems = true;
    
    const tbody = document.getElementById('itemsListBody');
    if (page === 1) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
    }
    
    fetch(`{{ route("admin.quotation.searchItems") }}?q=${encodeURIComponent(search)}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            const items = data.results || [];
            hasMoreItems = data.pagination?.more || false;
            
            if (page === 1) {
                tbody.innerHTML = '';
                itemsData = []; // Clear stored items for new search
            }
            
            if (items.length === 0 && page === 1) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No items found</td></tr>';
                return;
            }
            
            // Store items for batch processing
            items.forEach(item => {
                if (!itemsData.find(i => i.id === item.id)) {
                    itemsData.push(item);
                }
            });
            
            // Render items
            items.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'item-row';
                tr.style.cursor = 'pointer';
                tr.onclick = () => selectItem(item.id);
                tr.innerHTML = `
                    <td>${item.bar_code || ''}</td>
                    <td>${item.name || ''}</td>
                    <td>${item.packing || ''}</td>
                    <td>${item.company_name || ''}</td>
                    <td class="text-end">${parseFloat(item.s_rate || 0).toFixed(2)}</td>
                    <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
            });
            
            isLoadingItems = false;
        })
        .catch(error => {
            console.error('Error searching items:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading items</td></tr>';
            isLoadingItems = false;
        });
}

// Debounced search handler
const debouncedSearch = debounce(function(search) {
    currentSearchTerm = search;
    currentPage = 1;
    hasMoreItems = true;
    searchItemsAjax(search, 1);
}, 300);

function filterItems() {
    const search = document.getElementById('itemSearchInput').value;
    debouncedSearch(search);
}

// Handle scroll for infinite loading
function handleItemsScroll(event) {
    const container = event.target;
    if (container.scrollTop + container.clientHeight >= container.scrollHeight - 50) {
        if (hasMoreItems && !isLoadingItems) {
            currentPage++;
            searchItemsAjax(currentSearchTerm, currentPage);
        }
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
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" onkeydown="handleCodeKeydown(event, ${rowIndex})" onfocus="showItemModal(${rowIndex})"></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][item_name]" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][qty]" value="0" step="0.001" onkeydown="handleQtyKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][rate]" value="0" step="0.01" onkeydown="handleRateKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][mrp]" value="0" step="0.01" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
            <input type="hidden" name="items[${rowIndex}][packing]" value="">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="">
            <input type="hidden" name="items[${rowIndex}][unit]" value="">
        </td>
    `;
    tbody.appendChild(row);
    selectRow(rowIndex);
    
    setTimeout(() => {
        const codeInput = row.querySelector('input[name*="[code]"]');
        if (codeInput) codeInput.focus();
    }, 50);
}

function selectRow(rowIndex) {
    document.querySelectorAll('#itemsTableBody tr').forEach(r => r.classList.remove('row-selected'));
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.classList.add('row-selected');
        selectedRowIndex = rowIndex;
        updateFooterFromRow(row);
    }
}

function updateFooterFromRow(row) {
    const packing = row.querySelector('input[name*="[packing]"]')?.value || '';
    const unit = row.querySelector('input[name*="[unit]"]')?.value || '';
    const company = row.querySelector('input[name*="[company_name]"]')?.value || '';
    
    document.getElementById('packing').value = packing;
    document.getElementById('unit').value = unit;
    document.getElementById('company').value = company;
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
    }
}

function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
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
    
    const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
    const netAmount = total * (1 - discountPercent / 100);
    
    document.getElementById('net_amount').value = netAmount.toFixed(2);
}

function handleCodeKeydown(event, rowIndex) {
    if (event.key === 'Enter' || event.key === 'F2') {
        event.preventDefault();
        showItemModal(rowIndex);
    }
}

function handleQtyKeydown(event, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if (event.shiftKey) {
            // Optionally could navigate backwards if required, but staying normal for now
        } else {
            const row = document.getElementById(`row-${rowIndex}`);
            if (row) {
                const rateInput = row.querySelector('input[name*="[rate]"]');
                if (rateInput) {
                    rateInput.focus();
                    rateInput.select();
                }
            }
        }
    }
}

function handleRateKeydown(event, rowIndex) {
    // Shift+Enter → go back to Qty
    if (event.key === 'Enter' && event.shiftKey) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        const row = document.getElementById(`row-${rowIndex}`);
        if (row) {
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            if (qtyInput) { qtyInput.focus(); qtyInput.select(); }
        }
    }
    // Plain Enter → open Add Items modal
    if (event.key === 'Enter' && !event.shiftKey && !event.ctrlKey && !event.metaKey) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        showAddItemsModal();
    }
}

function showItemModal(rowIndex) {
    selectedRowIndex = rowIndex;
    currentSearchTerm = '';
    currentPage = 1;
    hasMoreItems = true;
    
    // Try to use the new reusable item selection modal
    if (typeof window.openItemModal_chooseItemsModal === 'function') {
        window.openItemModal_chooseItemsModal();
        return;
    }
    
    // Fallback to old inline modal
    let html = `
        <div class="batch-modal-backdrop show" id="itemModalBackdrop"></div>
        <div class="batch-modal show" id="itemModal">
            <div class="modal-header-custom">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="itemSearchInput" placeholder="Type to search by name or code..." onkeyup="filterItems()">
                </div>
                <div class="table-responsive" style="max-height: 300px;" onscroll="handleItemsScroll(event)">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-success" style="position: sticky; top: 0;">
                            <tr>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Packing</th>
                                <th>Company</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">MRP</th>
                            </tr>
                        </thead>
                        <tbody id="itemsListBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput')?.focus();
    // Load initial items
    searchItemsAjax('', 1);
}

function selectItem(itemId) {
    const item = itemsData.find(i => i.id === itemId);
    if (!item || selectedRowIndex === null) return;
    
    // Store selected item temporarily
    window.selectedItem = item;
    
    closeItemModal();
    
    // Show batch modal for this item
    showBatchModal(item);
}

function closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemModalBackdrop')?.remove();
}

// ============ BATCH SELECTION MODAL ============
function showBatchModal(item) {
    // Fetch batches for this item
    fetch(`{{ url('admin/quotation/get-batches') }}/${item.id}`)
        .then(response => response.json())
        .then(batches => {
            let batchRows = '';
            if (batches && batches.length > 0) {
                batchRows = batches.map(batch => `
                    <tr class="item-row" onclick="selectBatch(${batch.id}, '${batch.batch_no || ''}', '${batch.expiry_date || ''}', ${batch.mrp || 0}, ${batch.s_rate || item.s_rate || 0}, ${batch.total_qty || 0})" style="cursor: pointer;">
                        <td>${batch.batch_no || '-'}</td>
                        <td>${batch.expiry_date || '-'}</td>
                        <td class="text-end">${parseFloat(batch.total_qty || 0).toFixed(2)}</td>
                        <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                        <td class="text-end">${parseFloat(batch.s_rate || item.s_rate || 0).toFixed(2)}</td>
                    </tr>
                `).join('');
            } else {
                batchRows = '<tr><td colspan="5" class="text-center text-muted">No batches available</td></tr>';
            }
            
            let html = `
                <div class="batch-modal-backdrop show" id="batchModalBackdrop"></div>
                <div class="batch-modal show" id="batchModal">
                    <div class="modal-header-custom" style="background: #17a2b8;">
                        <h5 class="mb-0"><i class="bi bi-box me-2"></i>Select Batch - ${item.name}</h5>
                        <button type="button" class="btn-close btn-close-white" onclick="closeBatchModal()"></button>
                    </div>
                    <div class="modal-body-custom">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-bordered table-sm" style="font-size: 11px;">
                                <thead class="table-info" style="position: sticky; top: 0;">
                                    <tr>
                                        <th>Batch No</th>
                                        <th>Expiry</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">MRP</th>
                                        <th class="text-end">Rate</th>
                                    </tr>
                                </thead>
                                <tbody id="batchListBody">${batchRows}</tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-warning btn-sm" onclick="skipBatchSelection()">
                            <i class="bi bi-skip-forward"></i> Skip (No Batch)
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Cancel</button>
                    </div>
                </div>`;
            
            document.body.insertAdjacentHTML('beforeend', html);
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            // If error, just add item without batch
            skipBatchSelection();
        });
}

function selectBatch(batchId, batchNo, expiry, mrp, rate, qty) {
    const item = window.selectedItem;
    if (!item || selectedRowIndex === null) return;
    
    const row = document.getElementById(`row-${selectedRowIndex}`);
    if (row) {
        row.querySelector('input[name*="[code]"]').value = item.bar_code || '';
        row.querySelector('input[name*="[item_name]"]').value = item.name || '';
        row.querySelector('input[name*="[batch]"]').value = batchNo || '';
        row.querySelector('input[name*="[expiry]"]').value = expiry || '';
        row.querySelector('input[name*="[rate]"]').value = parseFloat(rate || item.s_rate || 0).toFixed(2);
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(mrp || item.mrp || 0).toFixed(2);
        row.querySelector('input[name*="[item_id]"]').value = item.id;
        row.querySelector('input[name*="[batch_id]"]').value = batchId || '';
        row.querySelector('input[name*="[packing]"]').value = item.packing || '';
        row.querySelector('input[name*="[company_name]"]').value = item.company_name || '';
        row.querySelector('input[name*="[unit]"]').value = item.unit || '';
        
        // Update footer with closing qty
        document.getElementById('cl_qty').value = parseFloat(qty || 0).toFixed(2);
        updateFooterFromRow(row);
        calculateRowAmount(selectedRowIndex);
    }
    
    closeBatchModal();
    
    // Focus qty field
    row.querySelector('input[name*="[qty]"]')?.focus();
}

function skipBatchSelection() {
    const item = window.selectedItem;
    if (!item || selectedRowIndex === null) {
        closeBatchModal();
        return;
    }
    
    const row = document.getElementById(`row-${selectedRowIndex}`);
    if (row) {
        row.querySelector('input[name*="[code]"]').value = item.bar_code || '';
        row.querySelector('input[name*="[item_name]"]').value = item.name || '';
        row.querySelector('input[name*="[batch]"]').value = '';
        row.querySelector('input[name*="[expiry]"]').value = '';
        row.querySelector('input[name*="[rate]"]').value = parseFloat(item.s_rate || 0).toFixed(2);
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(item.mrp || 0).toFixed(2);
        row.querySelector('input[name*="[item_id]"]').value = item.id;
        row.querySelector('input[name*="[batch_id]"]').value = '';
        row.querySelector('input[name*="[packing]"]').value = item.packing || '';
        row.querySelector('input[name*="[company_name]"]').value = item.company_name || '';
        row.querySelector('input[name*="[unit]"]').value = item.unit || '';
        
        updateFooterFromRow(row);
        calculateRowAmount(selectedRowIndex);
    }
    
    closeBatchModal();
    
    // Focus qty field
    row.querySelector('input[name*="[qty]"]')?.focus();
}

function closeBatchModal() {
    document.getElementById('batchModal')?.remove();
    document.getElementById('batchModalBackdrop')?.remove();
    window.selectedItem = null;
}

// Add Items Modal - allows selecting multiple items at once
function showAddItemsModal() {
    // Try to use the new reusable item selection modal
    if (typeof window.openItemModal_chooseItemsModal === 'function') {
        selectedRowIndex = null; // Ensure new row gets created
        window.openItemModal_chooseItemsModal();
        return;
    }
    
    // Fallback to old inline modal
    // Reset state
    addItemsPage = 1;
    hasMoreAddItems = true;
    addItemsSearchTerm = '';
    addItemsData = [];
    
    let html = `
        <div class="batch-modal-backdrop show" id="addItemsModalBackdrop"></div>
        <div class="batch-modal show" id="addItemsModal" style="max-width: 900px;">
            <div class="modal-header-custom">
                <h5 class="mb-0"><i class="bi bi-plus-square me-2"></i>Add Items</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeAddItemsModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="addItemsSearchInput" placeholder="Type to search by name or code..." onkeyup="filterAddItems()">
                </div>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-success" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="selectAllItems" onchange="toggleSelectAllItems()"></th>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Packing</th>
                                <th>Company</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">MRP</th>
                                <th style="width: 80px;" class="text-end">Qty</th>
                            </tr>
                        </thead>
                        <tbody id="addItemsListBody"></tbody>
                    </table>
                </div>
                <div class="text-center mt-2" id="loadMoreContainer" style="display: none;">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadMoreAddItems()">
                        <i class="bi bi-arrow-down-circle"></i> Load More Items
                    </button>
                    <span id="addItemsCount" class="ms-2 text-muted small"></span>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeAddItemsModal()">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" onclick="addSelectedItems()">
                    <i class="bi bi-plus-circle"></i> Add Selected Items
                </button>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', html);
    setTimeout(() => {
        const srch = document.getElementById('addItemsSearchInput');
        if (srch) { srch.focus(); srch.select(); }
    }, 60);
    // Load initial items via AJAX
    searchAddItemsAjax('', 1);
}

// Variables for Add Items modal
let addItemsPage = 1;
let isLoadingAddItems = false;
let hasMoreAddItems = true;
let addItemsSearchTerm = '';
let addItemsData = []; // Store loaded items for this modal

// Search items for Add Items modal
function searchAddItemsAjax(search = '', page = 1) {
    if (isLoadingAddItems) return;
    isLoadingAddItems = true;
    
    const tbody = document.getElementById('addItemsListBody');
    const loadMoreContainer = document.getElementById('loadMoreContainer');
    const itemsCountSpan = document.getElementById('addItemsCount');
    
    if (page === 1) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
        addItemsData = [];
        if (loadMoreContainer) loadMoreContainer.style.display = 'none';
    }
    
    fetch(`{{ route("admin.quotation.searchItems") }}?q=${encodeURIComponent(search)}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            const items = data.results || [];
            hasMoreAddItems = data.pagination?.more || false;
            const total = data.total || 0;
            
            if (page === 1) {
                tbody.innerHTML = '';
            }
            
            if (items.length === 0 && page === 1) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No items found</td></tr>';
                if (loadMoreContainer) loadMoreContainer.style.display = 'none';
                isLoadingAddItems = false;
                return;
            }
            
            // Store items for selection
            items.forEach(item => {
                if (!addItemsData.find(i => i.id === item.id)) {
                    addItemsData.push(item);
                }
            });
            
            // Render items
            items.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'item-row';
                tr.innerHTML = `
                    <td class="text-center"><input type="checkbox" class="item-checkbox" data-item-id="${item.id}"></td>
                    <td>${item.bar_code || ''}</td>
                    <td>${item.name || ''}</td>
                    <td>${item.packing || ''}</td>
                    <td>${item.company_name || ''}</td>
                    <td class="text-end">${parseFloat(item.s_rate || 0).toFixed(2)}</td>
                    <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
                    <td><input type="number" class="form-control form-control-sm text-end item-qty" data-item-id="${item.id}" value="1" min="1" step="0.001" style="width: 70px;"></td>
                `;
                tbody.appendChild(tr);
            });
            
            // Show/hide Load More button
            if (loadMoreContainer) {
                loadMoreContainer.style.display = hasMoreAddItems ? 'block' : 'none';
            }
            
            // Update count
            if (itemsCountSpan) {
                itemsCountSpan.textContent = `Showing ${addItemsData.length} of ${total} items`;
            }
            
            isLoadingAddItems = false;
        })
        .catch(error => {
            console.error('Error searching items:', error);
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading items</td></tr>';
            if (loadMoreContainer) loadMoreContainer.style.display = 'none';
            isLoadingAddItems = false;
        });
}

// Load More button handler
function loadMoreAddItems() {
    if (hasMoreAddItems && !isLoadingAddItems) {
        addItemsPage++;
        searchAddItemsAjax(addItemsSearchTerm, addItemsPage);
    }
}

// Debounced search for Add Items modal
const debouncedAddItemsSearch = debounce(function(search) {
    addItemsSearchTerm = search;
    addItemsPage = 1;
    hasMoreAddItems = true;
    searchAddItemsAjax(search, 1);
}, 300);

function filterAddItems() {
    const search = document.getElementById('addItemsSearchInput').value;
    debouncedAddItemsSearch(search);
}

// Keep scroll handler for convenience (optional auto-load)
function handleAddItemsScroll(event) {
    // Disabled - using Load More button instead
}

function toggleSelectAllItems() {
    const selectAll = document.getElementById('selectAllItems').checked;
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = selectAll);
}

function addSelectedItems() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one item');
        return;
    }
    
    // Store selected items for batch selection
    window.pendingItems = [];
    selectedCheckboxes.forEach(checkbox => {
        const itemId = parseInt(checkbox.dataset.itemId);
        // Use addItemsData which contains the AJAX-loaded items
        const item = addItemsData.find(i => i.id === itemId);
        if (item) {
            const qtyInput = document.querySelector(`.item-qty[data-item-id="${itemId}"]`);
            const qty = parseFloat(qtyInput?.value) || 1;
            window.pendingItems.push({ item, qty });
        }
    });
    
    closeAddItemsModal();
    
    // Process items one by one with batch selection
    processNextPendingItem();
}

function processNextPendingItem() {
    if (!window.pendingItems || window.pendingItems.length === 0) {
        calculateTotalAmount();
        return;
    }
    
    const { item, qty } = window.pendingItems.shift();
    window.pendingItemQty = qty;
    window.selectedItem = item;
    
    // Add a new row first
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][item_name]" value="" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][qty]" value="${qty}" step="0.001" onkeydown="handleQtyKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][rate]" value="0" step="0.01" onkeydown="handleRateKeydown(event, ${rowIndex})" onchange="calculateRowAmount(${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][mrp]" value="0" step="0.01" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-x"></i></button>
            <input type="hidden" name="items[${rowIndex}][item_id]" value="">
            <input type="hidden" name="items[${rowIndex}][batch_id]" value="">
            <input type="hidden" name="items[${rowIndex}][packing]" value="">
            <input type="hidden" name="items[${rowIndex}][company_name]" value="">
            <input type="hidden" name="items[${rowIndex}][unit]" value="">
        </td>
    `;
    
    tbody.appendChild(row);
    selectedRowIndex = rowIndex;
    selectRow(rowIndex);
    
    // Show batch modal for this item
    showBatchModalForMultiple(item);
}

function showBatchModalForMultiple(item) {
    fetch(`{{ url('admin/quotation/get-batches') }}/${item.id}`)
        .then(response => response.json())
        .then(batches => {
            let batchRows = '';
            if (batches && batches.length > 0) {
                batchRows = batches.map(batch => `
                    <tr class="item-row" onclick="selectBatchForMultiple(${batch.id}, '${batch.batch_no || ''}', '${batch.expiry_date || ''}', ${batch.mrp || 0}, ${batch.s_rate || item.s_rate || 0}, ${batch.total_qty || 0})" style="cursor: pointer;">
                        <td>${batch.batch_no || '-'}</td>
                        <td>${batch.expiry_date || '-'}</td>
                        <td class="text-end">${parseFloat(batch.total_qty || 0).toFixed(2)}</td>
                        <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
                        <td class="text-end">${parseFloat(batch.s_rate || item.s_rate || 0).toFixed(2)}</td>
                    </tr>
                `).join('');
            } else {
                batchRows = '<tr><td colspan="5" class="text-center text-muted">No batches available</td></tr>';
            }
            
            let html = `
                <div class="batch-modal-backdrop show" id="batchModalBackdrop"></div>
                <div class="batch-modal show" id="batchModal">
                    <div class="modal-header-custom" style="background: #17a2b8;">
                        <h5 class="mb-0"><i class="bi bi-box me-2"></i>Select Batch - ${item.name}</h5>
                        <button type="button" class="btn-close btn-close-white" onclick="skipBatchForMultiple()"></button>
                    </div>
                    <div class="modal-body-custom">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-bordered table-sm" style="font-size: 11px;">
                                <thead class="table-info" style="position: sticky; top: 0;">
                                    <tr>
                                        <th>Batch No</th>
                                        <th>Expiry</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">MRP</th>
                                        <th class="text-end">Rate</th>
                                    </tr>
                                </thead>
                                <tbody>${batchRows}</tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-warning btn-sm" onclick="skipBatchForMultiple()">
                            <i class="bi bi-skip-forward"></i> Skip (No Batch)
                        </button>
                    </div>
                </div>`;
            
            document.body.insertAdjacentHTML('beforeend', html);
        })
        .catch(error => {
            console.error('Error loading batches:', error);
            skipBatchForMultiple();
        });
}

function selectBatchForMultiple(batchId, batchNo, expiry, mrp, rate, qty) {
    const item = window.selectedItem;
    const pendingQty = window.pendingItemQty || 1;
    
    if (!item || selectedRowIndex === null) {
        closeBatchModal();
        processNextPendingItem();
        return;
    }
    
    const row = document.getElementById(`row-${selectedRowIndex}`);
    if (row) {
        row.querySelector('input[name*="[code]"]').value = item.bar_code || '';
        row.querySelector('input[name*="[item_name]"]').value = item.name || '';
        row.querySelector('input[name*="[batch]"]').value = batchNo || '';
        row.querySelector('input[name*="[expiry]"]').value = expiry || '';
        row.querySelector('input[name*="[qty]"]').value = pendingQty;
        row.querySelector('input[name*="[rate]"]').value = parseFloat(rate || item.s_rate || 0).toFixed(2);
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(mrp || item.mrp || 0).toFixed(2);
        row.querySelector('input[name*="[item_id]"]').value = item.id;
        row.querySelector('input[name*="[batch_id]"]').value = batchId || '';
        row.querySelector('input[name*="[packing]"]').value = item.packing || '';
        row.querySelector('input[name*="[company_name]"]').value = item.company_name || '';
        row.querySelector('input[name*="[unit]"]').value = item.unit || '';
        
        calculateRowAmount(selectedRowIndex);
    }
    
    closeBatchModal();
    processNextPendingItem();
}

function skipBatchForMultiple() {
    const item = window.selectedItem;
    const pendingQty = window.pendingItemQty || 1;
    
    if (!item || selectedRowIndex === null) {
        closeBatchModal();
        processNextPendingItem();
        return;
    }
    
    const row = document.getElementById(`row-${selectedRowIndex}`);
    if (row) {
        row.querySelector('input[name*="[code]"]').value = item.bar_code || '';
        row.querySelector('input[name*="[item_name]"]').value = item.name || '';
        row.querySelector('input[name*="[batch]"]').value = '';
        row.querySelector('input[name*="[expiry]"]').value = '';
        row.querySelector('input[name*="[qty]"]').value = pendingQty;
        row.querySelector('input[name*="[rate]"]').value = parseFloat(item.s_rate || 0).toFixed(2);
        row.querySelector('input[name*="[mrp]"]').value = parseFloat(item.mrp || 0).toFixed(2);
        row.querySelector('input[name*="[item_id]"]').value = item.id;
        row.querySelector('input[name*="[batch_id]"]').value = '';
        row.querySelector('input[name*="[packing]"]').value = item.packing || '';
        row.querySelector('input[name*="[company_name]"]').value = item.company_name || '';
        row.querySelector('input[name*="[unit]"]').value = item.unit || '';
        
        calculateRowAmount(selectedRowIndex);
    }
    
    closeBatchModal();
    processNextPendingItem();
}

function closeAddItemsModal() {
    document.getElementById('addItemsModal')?.remove();
    document.getElementById('addItemsModalBackdrop')?.remove();
}

function saveQuotation() {
    const items = [];
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const itemName = row.querySelector('input[name*="[item_name]"]')?.value;
        if (itemName) {
            items.push({
                item_id: row.querySelector('input[name*="[item_id]"]')?.value || null,
                batch_id: row.querySelector('input[name*="[batch_id]"]')?.value || null,
                code: row.querySelector('input[name*="[code]"]')?.value || '',
                item_name: itemName,
                batch: row.querySelector('input[name*="[batch]"]')?.value || '',
                expiry: row.querySelector('input[name*="[expiry]"]')?.value || '',
                qty: row.querySelector('input[name*="[qty]"]')?.value || 0,
                rate: row.querySelector('input[name*="[rate]"]')?.value || 0,
                mrp: row.querySelector('input[name*="[mrp]"]')?.value || 0,
                packing: row.querySelector('input[name*="[packing]"]')?.value || '',
                company_name: row.querySelector('input[name*="[company_name]"]')?.value || '',
                unit: row.querySelector('input[name*="[unit]"]')?.value || '',
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    const data = {
        date: document.getElementById('quotation_date').value,
        customer_id: document.getElementById('customer_id').value,
        customer_name: document.getElementById('customer_name').value,
        discount_percent: document.getElementById('discount_percent').value,
        remarks: document.getElementById('remarks').value,
        terms: document.getElementById('terms').value,
        items: items,
        _token: '{{ csrf_token() }}'
    };
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    fetch('{{ route("admin.quotation.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Quotation saved: ' + result.quotation_no);
            window.location.href = '{{ route("admin.quotation.index") }}';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving quotation');
    });
}

function cancelQuotation() {
    if (confirm('Are you sure you want to cancel this quotation?')) {
        window.location.href = '{{ route("admin.quotation.index") }}';
    }
}

// ── RATE FIELD: Enter → open Add Items modal ──────────────────────
// Window capture phase fires ABSOLUTELY FIRST — before browser default,
// before form submit, before any button click. This prevents the X button
// from being triggered when Enter is pressed in a rate input.
window.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter' || e.shiftKey || e.ctrlKey || e.metaKey) return;
    const active = document.activeElement;
    if (!active) return;
    // Check if focus is on a rate input in the items table
    const isRateInput = active.matches('input[name*="[rate]"]') &&
                        active.closest('#itemsTableBody');
    if (!isRateInput) return;

    // KILL the event completely — nothing else sees it
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Open modal in next tick so event is fully gone
    setTimeout(() => showAddItemsModal(), 0);
}, true /* capture = fires before ALL other handlers */);

// Also block keyup on rate fields so button doesn't react to keyup
window.addEventListener('keyup', function(e) {
    if (e.key !== 'Enter') return;
    const active = document.activeElement;
    if (!active) return;
    if (active.matches('input[name*="[rate]"]') && active.closest('#itemsTableBody')) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
    }
}, true);

// Global Shortcuts — window CAPTURE phase, fires before everything
window.addEventListener('keydown', function(e) {
    // Ctrl+S → Save (works from any field/state)
    if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        saveQuotation();
        return;
    }
    // Ctrl+Enter → jump to Srlno
    if (e.ctrlKey && e.key === 'Enter') {
        const hasOpenModal = document.querySelector('.batch-modal.show, [id$="Modal"].show');
        if (hasOpenModal) return;
        const srlno = document.getElementById('srlno');
        if (srlno) {
            e.preventDefault();
            e.stopPropagation();
            srlno.focus();
            srlno.select();
        }
    }
}, true /* capture phase */);
</script>
@endpush