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
    
    /* Custom dropdown styles */
    .custom-dropdown-container { position: relative; width: 250px; }
    .custom-dropdown-list {
        display: none; position: absolute; top: 100%; left: 0; right: 0;
        max-height: 200px; overflow-y: auto; background: white;
        border: 1px solid #ced4da; border-top: none; z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .custom-dropdown-item {
        padding: 4px 8px; cursor: pointer; font-size: 11px;
        border-bottom: 1px solid #f0f0f0;
    }
    .custom-dropdown-item:hover, .custom-dropdown-item.highlighted {
        background-color: #cce5ff;
    }
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
                                <div class="custom-dropdown-container">
                                    <input type="text" id="supplierSearchInput" class="form-control" placeholder="Search supplier..." autocomplete="off">
                                    <div class="custom-dropdown-list" id="supplierDropdownList">
                                        @foreach($suppliers as $supplier)
                                        <div class="custom-dropdown-item" data-value="{{ $supplier->supplier_id }}" data-name="{{ $supplier->name }}">{{ $supplier->name }}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" id="supplier_id" name="supplier_id" value="">
                            </div>
                            <div class="field-group">
                                <label>Rpl.No:</label>
                                <input type="text" id="rn_no" class="form-control readonly-field" style="width: 100px;" readonly>
                            </div>
                            <button type="button" class="btn btn-success btn-sm ms-auto" id="loadInvoiceBtn" onclick="openInsertInvoiceModal()">
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

<!-- Reusable Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'reusableItemsModal',
    'module' => 'replacement-note-modification',
    'showStock' => true,
    'rateType' => 'p_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'reusableBatchModal',
])

@include('components.modals.batch-selection', [
    'id' => 'reusableBatchModal',
    'module' => 'replacement-note-modification',
    'showOnlyAvailable' => true,
    'rateType' => 'p_rate',
    'showCostDetails' => true,
])

@endsection

@push('scripts')
<script>
let currentRowIndex = 0, itemsData = [], currentTransactionId = null, selectedRowIndex = null;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    initSupplierDropdown();
    initKeyboardNavigation();
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id) loadTransaction(id);
    
    // Focus date on load
    document.getElementById('transaction_date')?.focus();
});

// ====================================================
// CUSTOM SEARCHABLE SUPPLIER DROPDOWN
// ====================================================
function initSupplierDropdown() {
    const searchInput = document.getElementById('supplierSearchInput');
    const dropdownList = document.getElementById('supplierDropdownList');
    const supplierIdInput = document.getElementById('supplier_id');
    if (!searchInput || !dropdownList) return;

    let highlightedIndex = -1;
    const allItems = dropdownList.querySelectorAll('.custom-dropdown-item');

    function showDropdown() {
        dropdownList.style.display = 'block';
        filterDropdown();
    }

    function hideDropdown() {
        dropdownList.style.display = 'none';
        highlightedIndex = -1;
        clearHighlights();
    }

    function filterDropdown() {
        const query = searchInput.value.toLowerCase().trim();
        allItems.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            item.style.display = (!query || name.includes(query)) ? 'block' : 'none';
        });
        highlightedIndex = -1;
        clearHighlights();
    }

    function getVisibleItems() {
        return Array.from(allItems).filter(item => item.style.display !== 'none');
    }

    function clearHighlights() {
        allItems.forEach(item => item.classList.remove('highlighted'));
    }

    function highlightItem(index) {
        const visible = getVisibleItems();
        clearHighlights();
        if (index >= 0 && index < visible.length) {
            visible[index].classList.add('highlighted');
            visible[index].scrollIntoView({ block: 'nearest' });
            highlightedIndex = index;
        }
    }

    function selectSupplierItem(item) {
        supplierIdInput.value = item.dataset.value;
        searchInput.value = item.dataset.name;
        hideDropdown();
        // Move focus and automatically trigger Load Invoice button click
        const loadBtn = document.getElementById('loadInvoiceBtn');
        if (loadBtn) {
            loadBtn.focus();
            loadBtn.click();
        }
    }

    searchInput.addEventListener('focus', showDropdown);
    searchInput.addEventListener('input', () => { showDropdown(); filterDropdown(); });
    searchInput.addEventListener('blur', function() { setTimeout(hideDropdown, 200); });

    searchInput.addEventListener('keydown', function(e) {
        const visible = getVisibleItems();

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (dropdownList.style.display !== 'block') showDropdown();
            if (highlightedIndex < visible.length - 1) highlightItem(highlightedIndex + 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (highlightedIndex > 0) highlightItem(highlightedIndex - 1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (e.shiftKey) {
                document.getElementById('transaction_date')?.focus();
            } else if (highlightedIndex >= 0 && visible[highlightedIndex]) {
                selectSupplierItem(visible[highlightedIndex]);
            } else if (visible.length > 0) {
                selectSupplierItem(visible[0]);
            } else {
                hideDropdown();
                const loadBtn = document.getElementById('loadInvoiceBtn');
                if (loadBtn) {
                    loadBtn.focus();
                    loadBtn.click();
                }
            }
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    });

    dropdownList.addEventListener('mousedown', function(e) {
        const item = e.target.closest('.custom-dropdown-item');
        if (item) { e.preventDefault(); selectSupplierItem(item); }
    });
}

// ====================================================
// KEYBOARD NAVIGATION: Date → Name → Load Invoice
// ====================================================
function initKeyboardNavigation() {
    const dateField = document.getElementById('transaction_date');
    const loadBtn = document.getElementById('loadInvoiceBtn');
    let loadInvoiceEnterLock = false;

    function triggerLoadInvoiceFromKeyboard(source) {
        const btn = document.getElementById('loadInvoiceBtn');
        if (!btn) {
            console.warn('[KB-RN-MOD] Load Invoice button not found', { source });
            return;
        }
        console.log('[KB-RN-MOD] Trigger Load Invoice', {
            source,
            activeId: document.activeElement?.id || null
        });
        btn.click();
    }

    // Date → Enter → Supplier Name
    if (dateField) {
        dateField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                loadBtn?.focus();
                console.log('[KB-RN-MOD] Date Enter -> focus Load Invoice');
            }
        });
    }

    // Capture fallback: keep Date Enter flow stable even if global handlers exist
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        const activeEl = document.activeElement;
        if (!activeEl || activeEl.id !== 'transaction_date') return;
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        loadBtn?.focus();
        console.log('[KB-RN-MOD] Date Enter (capture) -> focus Load Invoice');
    }, true);

    // Earliest capture guard for Enter on Load Invoice (prevents global conflicts)
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        const activeEl = document.activeElement;
        if (!activeEl || activeEl.id !== 'loadInvoiceBtn') return;

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (loadInvoiceEnterLock) return;
        loadInvoiceEnterLock = true;
        triggerLoadInvoiceFromKeyboard('window-capture');
        setTimeout(() => { loadInvoiceEnterLock = false; }, 120);
    }, true);

    // Load Invoice button → Enter → open modal (or back if Shift+Enter)
    if (loadBtn) {
        loadBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                if (e.shiftKey) {
                    document.getElementById('supplierSearchInput')?.focus();
                } else {
                    if (loadInvoiceEnterLock) return;
                    loadInvoiceEnterLock = true;
                    triggerLoadInvoiceFromKeyboard('button-keydown');
                    setTimeout(() => { loadInvoiceEnterLock = false; }, 120);
                }
            }
        });

        loadBtn.addEventListener('click', function() {
            console.log('[KB-RN-MOD] Load Invoice click fired');
        });
    }

    // Ctrl+S to save, Ctrl+Enter to jump to Case field
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            e.stopPropagation();
            saveTransaction();
            return;
        }

        // Ctrl+Enter → jump to Case field
        if (e.ctrlKey && e.key === 'Enter') {
            // Don't trigger if a modal is open
            const hasOpenModal = document.getElementById('invoiceModal') || document.getElementById('existingBatchModal') || document.getElementById('createBatchModal') || document.getElementById('batchModal') || document.querySelector('.modal.show');
            if (hasOpenModal) return;

            e.preventDefault();
            e.stopPropagation();
            const caseField = document.getElementById('case');
            if (caseField) {
                caseField.focus();
                caseField.select();
            }
        }
    }, true);

    // Hard guard: MRP Enter should never jump to action/delete
    window.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        const activeEl = document.activeElement;
        if (!activeEl) return;
        if (!activeEl.matches('#itemsTableBody input[name*="[mrp]"]')) return;

        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') {
            e.stopImmediatePropagation();
        }

        const row = activeEl.closest('tr');
        if (!row) return;
        console.log('[KB-RN-MOD] Capture MRP Enter -> custom navigation');
        handleMrpEnterNavigation(row);
    }, true);
}

// Callback function when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('Item selected from reusable modal:', item);
    console.log('Batch selected from reusable modal:', batch);
    
    // Create a new row with item and batch data
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
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
    
    const mrp = parseFloat(batch.mrp || batch.avg_mrp || item.mrp || 0);
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.dataset.rowIndex = rowIndex;
    row.dataset.itemId = item.id;
    row.dataset.batchId = batch.id;
    row.dataset.itemData = JSON.stringify(item);
    row.onclick = function() { selectRow(rowIndex); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.bar_code || item.id || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" value="${batch.batch_no || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" value="${expiryDisplay}" readonly onkeydown="handleGridEnterKey(event, 'expiry', ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][qty]" value="1" step="1" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleGridEnterKey(event, 'qty', ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][mrp]" value="${mrp.toFixed(2)}" step="0.01" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleGridEnterKey(event, 'mrp', ${rowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm text-end" name="items[${rowIndex}][amount]" value="${mrp.toFixed(2)}" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button>
        </td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batch.id}">
    `;
    
    tbody.appendChild(row);
    
    // Update footer display
    if (typeof updateFooter === 'function') {
        updateFooter(item);
    }
    
    // Calculate totals
    if (typeof calculateRowAmount === 'function') calculateRowAmount(rowIndex);
    if (typeof calculateTotal === 'function') calculateTotal();
    
    // Focus qty field
    const qtyInput = row.querySelector('input[name*="[qty]"]');
    if (qtyInput) {
        qtyInput.focus();
        qtyInput.select();
    }
};

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
    if (items?.length > 0) setTimeout(() => {
        selectRow(0);
        document.getElementById('row-0')?.querySelector('input[name*="[batch]"]')?.focus();
    }, 100);
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
        <td><input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${item.expiry || ''}" placeholder="MM/YY" onkeydown="handleGridEnterKey(event, 'expiry', ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][qty]" value="${qty > 0 ? qty : ''}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleGridEnterKey(event, 'qty', ${rowIndex})"></td>
        <td><input type="number" class="form-control text-end" name="items[${rowIndex}][mrp]" value="${mrp > 0 ? mrp.toFixed(2) : ''}" onchange="calculateRowAmount(${rowIndex})" onkeydown="handleGridEnterKey(event, 'mrp', ${rowIndex})"></td>
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
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
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

let invoiceModalKeydownHandler = null;

function openInsertInvoiceModal() {
    fetch('{{ route("admin.replacement-note.past-notes") }}')
        .then(r => r.json())
        .then(d => {
            const txs = d.transactions || [];
            let html = `<div id="invoiceBackdrop" class="show" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1050;" onclick="closeModal()"></div>
                <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:80%;max-width:700px;background:white;border-radius:8px;z-index:1055;" id="invoiceModal" class="custom-modal show">
                    <div style="padding:1rem;background:#6f42c1;color:white;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;">
                        <h6 class="mb-0">Select Invoice</h6>
                        <button onclick="closeModal(); document.getElementById('loadInvoiceBtn')?.focus();" style="background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
                    </div>
                    <div style="padding:1rem;">
                        <input type="text" id="invoiceSearchInput" class="form-control mb-2" placeholder="Search Invoice... (Arrow keys to navigate)" autocomplete="off">
                        <div style="max-height:350px;overflow-y:auto;" id="invoiceTableContainer">
                            <table class="table table-sm table-hover" id="invoiceTable">
                                <thead class="table-primary"><tr><th>RN No</th><th>Date</th><th>Supplier</th><th>Amount</th><th></th></tr></thead>
                                <tbody>`;
            txs.forEach((t, i) => {
                html += `<tr class="invoice-row" style="cursor:pointer;" tabindex="0" data-id="${t.id}" data-index="${i}" onclick="loadInvoiceFromModal(${t.id})">
                    <td>${t.rn_no}</td><td>${t.transaction_date||''}</td><td>${t.supplier_name||''}</td><td class="text-end">${t.amount||'0.00'}</td>
                    <td><button class="btn btn-sm btn-primary py-0" tabindex="-1"><i class="bi bi-eye"></i></button></td></tr>`;
            });
            html += `</tbody></table></div></div></div>`;
            document.getElementById('modalContainer').innerHTML = html;
            
            setTimeout(() => {
                initInvoiceModalNavigation();
            }, 100);
        });
}

function initInvoiceModalNavigation() {
    const searchInput = document.getElementById('invoiceSearchInput');
    const tableContainer = document.getElementById('invoiceTableContainer');
    if (!searchInput) return;

    searchInput.focus();
    let highlightedIndex = -1;

    // Remove old listener if exists
    if (invoiceModalKeydownHandler) {
        document.removeEventListener('keydown', invoiceModalKeydownHandler, true);
    }

    invoiceModalKeydownHandler = function(e) {
        const modal = document.getElementById('invoiceModal');
        if (!modal) {
            document.removeEventListener('keydown', invoiceModalKeydownHandler, true);
            invoiceModalKeydownHandler = null;
            return;
        }

        const table = document.getElementById('invoiceTable');
        if (!table) return;

        const visibleRows = Array.from(table.querySelectorAll('.invoice-row')).filter(r => r.style.display !== 'none');

        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            closeModal();
            document.getElementById('loadInvoiceBtn')?.focus();
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            if (highlightedIndex < visibleRows.length - 1) {
                highlightedIndex++;
                updateHighlight(visibleRows, highlightedIndex);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            if (highlightedIndex > 0) {
                highlightedIndex--;
                updateHighlight(visibleRows, highlightedIndex);
            } else if (highlightedIndex === 0) {
                highlightedIndex = -1;
                updateHighlight(visibleRows, highlightedIndex);
                searchInput.focus();
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            if (highlightedIndex >= 0 && visibleRows[highlightedIndex]) {
                visibleRows[highlightedIndex].click();
            } else if (document.activeElement === searchInput && visibleRows.length > 0) {
                visibleRows[0].click();
            }
        }
    };

    document.addEventListener('keydown', invoiceModalKeydownHandler, true);

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const table = document.getElementById('invoiceTable');
        if (table) {
            const rows = Array.from(table.querySelectorAll('.invoice-row'));
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
            highlightedIndex = -1;
            updateHighlight(Array.from(table.querySelectorAll('.invoice-row')).filter(r => r.style.display !== 'none'), -1);
        }
    });

    function updateHighlight(visibleRows, index) {
        document.querySelectorAll('.invoice-row').forEach(r => {
            r.classList.remove('row-selected', 'bg-primary', 'text-white');
        });
        if (index >= 0 && index < visibleRows.length) {
            visibleRows[index].classList.add('row-selected');
            // Ensure element is scrolled into view efficiently
            visibleRows[index].scrollIntoView({ block: 'nearest' });
            visibleRows[index].focus();
        }
    }
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

function handleGridEnterKey(event, fieldName, rowIndex) {
    if (event.key === 'Enter') {
        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }
        const row = document.getElementById(`row-${rowIndex}`);
        if (!row) return;
        
        if (fieldName === 'expiry') {
            row.querySelector('input[name*="[qty]"]')?.focus();
        } else if (fieldName === 'qty') {
            row.querySelector('input[name*="[mrp]"]')?.focus();
            row.querySelector('input[name*="[mrp]"]')?.select();
        } else if (fieldName === 'mrp') {
            handleMrpEnterNavigation(row);
        }
    }
}

function handleMrpEnterNavigation(currentRow) {
    if (!currentRow) return;

    const triggerInsertItem = () => {
        const insertBtn = Array.from(document.querySelectorAll('button'))
            .find(btn => (btn.getAttribute('onclick') || '').includes('openInsertItemsModal'));
        if (insertBtn) {
            insertBtn.focus();
            insertBtn.click();
        } else {
            openInsertItemsModal();
        }
    };

    const rows = Array.from(document.querySelectorAll('#itemsTableBody tr'));
    const currentPos = rows.findIndex(r => r === currentRow);
    const nextRow = currentPos >= 0 ? rows[currentPos + 1] : null;

    if (!nextRow) {
        console.log('[KB-RN-MOD] MRP Enter -> no next row, trigger Insert Item');
        triggerInsertItem();
        return;
    }

    const nextRowHasItem = !!(
        (nextRow.dataset.itemId && String(nextRow.dataset.itemId).trim() !== '') ||
        (nextRow.querySelector('input[name*="[name]"]')?.value || '').trim() !== '' ||
        (nextRow.querySelector('input[name*="[code]"]')?.value || '').trim() !== ''
    );

    if (!nextRowHasItem) {
        console.log('[KB-RN-MOD] MRP Enter -> next row empty, trigger Insert Item');
        triggerInsertItem();
        return;
    }

    const nextBatch = nextRow.querySelector('input[name*="[batch]"]');
    if (nextBatch) {
        console.log('[KB-RN-MOD] MRP Enter -> focus next row batch');
        setTimeout(() => {
            nextBatch.focus();
            nextBatch.select();
        }, 0);
        return;
    }

    console.log('[KB-RN-MOD] MRP Enter -> next row has no batch input, trigger Insert Item');
    triggerInsertItem();
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
    let html = `<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1060;" id="existingBatchBackdrop" class="show"></div>
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:80%;max-width:700px;background:white;border-radius:8px;z-index:1065;" id="existingBatchModal" class="custom-modal show">
            <div style="padding:0.75rem;background:#17a2b8;color:white;border-radius:8px 8px 0 0;">
                <h6 class="mb-0"><i class="bi bi-box me-1"></i> Select Existing Batch - ${itemData.name || ''}</h6>
                <button onclick="closeExistingBatchModal()" style="position:absolute;top:10px;right:15px;background:none;border:none;color:white;font-size:20px;cursor:pointer;">&times;</button>
            </div>
            <div style="padding:1rem;max-height:400px;overflow-y:auto;">
                <table class="table table-sm table-hover" id="existingBatchTable"><thead class="table-info"><tr><th>Batch</th><th>Expiry</th><th>MRP</th><th>Qty</th><th>Action</th></tr></thead><tbody>`;
    batches.forEach((batch, idx) => {
        const exp = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {month:'2-digit',year:'numeric'}) : '';
        html += `<tr class="existing-batch-row" style="cursor:pointer;" tabindex="0" data-index="${idx}" onclick='selectExistingBatch(${rowIndex}, ${JSON.stringify(batch).replace(/'/g,"&apos;")})'><td><strong>${batch.batch_no||''}</strong></td><td>${exp}</td><td>${parseFloat(batch.mrp||0).toFixed(2)}</td><td>${batch.qty||0}</td>
            <td><button class="btn btn-sm btn-success py-0" tabindex="-1"><i class="bi bi-check"></i></button></td></tr>`;
    });
    html += `</tbody></table></div>
        <div style="padding:0.75rem;background:#f8f9fa;border-top:1px solid #dee2e6;">
            <button class="btn btn-secondary btn-sm" onclick="closeExistingBatchModal()">Cancel</button>
        </div></div>`;
    document.getElementById('modalContainer').innerHTML = html;
    
    setTimeout(() => {
        initExistingBatchModalNavigation();
    }, 100);
}

let existingBatchModalKeydownHandler = null;

function initExistingBatchModalNavigation() {
    const tableContainer = document.getElementById('existingBatchModal');
    if (!tableContainer) return;

    let highlightedIndex = 0;
    const table = document.getElementById('existingBatchTable');
    if (!table) return;
    
    const visibleRows = Array.from(table.querySelectorAll('.existing-batch-row'));
    if (visibleRows.length > 0) {
        updateBatchHighlight(visibleRows, highlightedIndex);
    }

    if (existingBatchModalKeydownHandler) {
        document.removeEventListener('keydown', existingBatchModalKeydownHandler, true);
    }

    existingBatchModalKeydownHandler = function(e) {
        const modal = document.getElementById('existingBatchModal');
        if (!modal) {
            document.removeEventListener('keydown', existingBatchModalKeydownHandler, true);
            existingBatchModalKeydownHandler = null;
            return;
        }

        const table = document.getElementById('existingBatchTable');
        if (!table) return;

        const visibleRows = Array.from(table.querySelectorAll('.existing-batch-row'));

        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            closeExistingBatchModal();
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            if (highlightedIndex < visibleRows.length - 1) {
                highlightedIndex++;
                updateBatchHighlight(visibleRows, highlightedIndex);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            if (highlightedIndex > 0) {
                highlightedIndex--;
                updateBatchHighlight(visibleRows, highlightedIndex);
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            if (highlightedIndex >= 0 && visibleRows[highlightedIndex]) {
                visibleRows[highlightedIndex].click();
            }
        }
    };

    document.addEventListener('keydown', existingBatchModalKeydownHandler, true);

    function updateBatchHighlight(visibleRows, index) {
        document.querySelectorAll('.existing-batch-row').forEach(r => {
            r.classList.remove('row-selected');
        });
        if (index >= 0 && index < visibleRows.length) {
            visibleRows[index].classList.add('row-selected');
            visibleRows[index].scrollIntoView({ block: 'nearest' });
            visibleRows[index].focus();
        }
    }
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
    setTimeout(() => row?.querySelector('input[name*="[expiry]"]')?.focus(), 100);
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
    // Use reusable item selection modal
    if (typeof openItemModal_reusableItemsModal === 'function') {
        openItemModal_reusableItemsModal();
    } else {
        console.error('Reusable item modal not found');
        alert('Item selection modal not available. Please reload the page.');
    }
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


