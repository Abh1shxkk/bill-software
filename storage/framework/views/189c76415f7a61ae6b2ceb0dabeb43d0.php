<?php $__env->startSection('title', 'New Item Generation in Pending Order'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .poi-form { font-size: 12px; }
    .poi-form label { font-weight: 600; font-size: 12px; margin-bottom: 0; }
    .poi-form input, .poi-form select { font-size: 12px; padding: 4px 8px; height: 32px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 15px; margin-bottom: 10px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
    .field-group label { min-width: 100px; }
    .readonly-field { background-color: #e9ecef !important; }
    .item-modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; }
    .item-modal-backdrop.show { display: block; }
    .item-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; z-index: 1055; background: white; border-radius: 8px; }
    .item-modal.show { display: block; }
    .modal-header-custom { padding: 1rem; background: #fd7e14; color: white; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0; }
    .modal-body-custom { padding: 1rem; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 1rem; background: #f8f9fa; text-align: right; border-radius: 0 0 8px 8px; }
    .item-row:hover { background-color: #fff3cd !important; cursor: pointer; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="poi-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i> New Item Generation in Pending Order</h4>
                <div class="text-muted small">Add or remove items from pending orders</div>
            </div>
            <div>
                <a href="<?php echo e(route('admin.pending-order-item.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="poiForm" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    
                    <div class="header-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label>Item :</label>
                                    <input type="text" id="item_code" class="form-control" style="width: 100px;" readonly onclick="showItemModal()">
                                    <input type="text" id="item_name" class="form-control flex-grow-1 readonly-field" readonly>
                                    <input type="hidden" id="item_id" name="item_id">
                                </div>
                                
                                <div class="field-group">
                                    <label>I(nsert) / D(elete) :</label>
                                    <div style="position:relative;" id="actionTypeWrapper">
                                        <input type="text" id="action_type_display" class="form-control no-select2"
                                               value="I" readonly
                                               style="width:80px; cursor:pointer; caret-color:transparent; background:white; font-weight:bold;"
                                               onclick="_toggleActionDrop()"
                                               onkeydown="_actionKeydown(event)">
                                        <input type="hidden" id="action_type" name="action_type" value="I">
                                        <div id="actionDropList" style="display:none; position:absolute; z-index:99999; top:100%; left:0; min-width:80px; background:white; border:1px solid #ccc; box-shadow:0 4px 8px rgba(0,0,0,.15);">
                                            <div class="action-drop-item" data-value="I" style="padding:5px 10px; cursor:pointer; font-size:12px; font-weight:600;" onmousedown="_selectActionItem(this)">I (Insert)</div>
                                            <div class="action-drop-item" data-value="D" style="padding:5px 10px; cursor:pointer; font-size:12px; font-weight:600;" onmousedown="_selectActionItem(this)">D (Delete)</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="field-group">
                                    <label>Quantity :</label>
                                    <input type="number" id="quantity" name="quantity" class="form-control" style="width: 120px;" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="button" class="btn btn-primary px-4" onclick="saveItem()">
                            <i class="bi bi-check-lg me-1"></i> Ok
                        </button>
                        <button type="button" class="btn btn-secondary px-4" onclick="resetForm()">
                            <i class="bi bi-x-lg me-1"></i> Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<!-- Item Selection Modal Component -->
<?php echo $__env->make('components.modals.item-selection', [
    'id' => 'pendingOrderItemModal',
    'module' => 'pending-order-item',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => '',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let itemsData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    // Use longer timeout so layout's global handlers don't steal focus
    setTimeout(() => {
        const ic = document.getElementById('item_code');
        if (ic) { ic.focus(); }
    }, 300);
});

function loadItems() {
    fetch('<?php echo e(route("admin.pending-order-item.getItems")); ?>')
        .then(response => response.json())
        .then(data => { itemsData = data || []; })
        .catch(error => console.error('Error:', error));
}

// ── Action Type custom dropdown ───────────────────────────────────────────────
let _actionHil = -1;

function _toggleActionDrop() {
    const dl = document.getElementById('actionDropList');
    if (dl.style.display === 'none') {
        dl.style.display = 'block';
        const items = document.querySelectorAll('.action-drop-item');
        const cur = document.getElementById('action_type').value;
        items.forEach((el, i) => {
            const active = el.dataset.value === cur;
            el.style.background = active ? '#0d6efd' : '';
            el.style.color      = active ? '#fff'    : '';
            if (active) _actionHil = i;
        });
    } else {
        dl.style.display = 'none';
    }
}

function _closeActionDrop() {
    document.getElementById('actionDropList').style.display = 'none';
}

function _selectActionItem(el) {
    document.getElementById('action_type_display').value = el.dataset.value;
    document.getElementById('action_type').value         = el.dataset.value;
    _closeActionDrop();
    _actionHil = -1;
    setTimeout(() => {
        const qty = document.getElementById('quantity');
        qty.focus(); qty.select();
    }, 50);
}

function _actionKeydown(e) {
    const dl    = document.getElementById('actionDropList');
    const items = [...document.querySelectorAll('.action-drop-item')];
    const open  = dl.style.display !== 'none';

    // Press I or D directly
    if (e.key.toLowerCase() === 'i' || e.key.toLowerCase() === 'd') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        const val = e.key.toUpperCase();
        document.getElementById('action_type_display').value = val;
        document.getElementById('action_type').value         = val;
        _closeActionDrop();
        setTimeout(() => { const qty = document.getElementById('quantity'); qty.focus(); qty.select(); }, 50);
        return;
    }
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (!open) { _toggleActionDrop(); return; }
        _actionHil = e.key === 'ArrowDown'
            ? (_actionHil + 1) % items.length
            : (_actionHil - 1 + items.length) % items.length;
        items.forEach((el, i) => {
            el.style.background = i === _actionHil ? '#0d6efd' : '';
            el.style.color      = i === _actionHil ? '#fff'    : '';
        });
        return;
    }
    if (e.key === 'Enter') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (open && _actionHil >= 0) {
            _selectActionItem(items[_actionHil]);
        } else {
            // Already selected, just move forward
            const qty = document.getElementById('quantity');
            qty.focus(); qty.select();
        }
        return;
    }
    if (e.key === 'Escape') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _closeActionDrop(); return;
    }
}

// Close action drop on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#actionTypeWrapper')) _closeActionDrop();
});

// ============================================================================
// BRIDGE FUNCTIONS FOR REUSABLE MODAL COMPONENTS
// ============================================================================
function onItemSelectedFromModal(itemData) {
    if (!itemData || !itemData.id) return;
    document.getElementById('item_id').value   = itemData.id || '';
    document.getElementById('item_code').value = itemData.bar_code || itemData.id || '';
    document.getElementById('item_name').value = itemData.name || '';
    // Focus back to item_code — user presses Enter from here to go to I/D dropdown
    setTimeout(() => {
        document.getElementById('item_code')?.focus();
    }, 100);
}

function showItemModal() {
    if (typeof window.openItemModal_pendingOrderItemModal === 'function') {
        window.openItemModal_pendingOrderItemModal();
    } else {
        alert('Error: Item selection modal not available. Please refresh the page.');
    }
}

// ── Helper: any modal open? ───────────────────────────────────────────────────
function _anyModalOpen() {
    return !!document.querySelector('#pendingOrderItemModal.show');
}

// ============================================================================
// MASTER KEYBOARD HANDLER — window capture phase
// Flow: item_code → [modal] → action_type → quantity → Save
// ============================================================================
window.addEventListener('keydown', function(e) {

    // ── Action type dropdown open → intercept nav keys ────────
    const actionList = document.getElementById('actionDropList');
    const actionOpen = actionList && actionList.style.display !== 'none';
    if (actionOpen && ['ArrowDown','ArrowUp','Enter','Escape','i','d','I','D'].includes(e.key)) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _actionKeydown(e);
        return;
    }

    // ── Skip if modal open ────────────────────────────────────
    if (_anyModalOpen()) return;

    if (e.key === 'Enter') {
        const el = document.activeElement;
        if (!el) return;

        // item_code → if item already selected go to I/D dropdown (open it), else open item modal
        if (el.id === 'item_code') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const itemId = document.getElementById('item_id').value;
            if (itemId) {
                // Item selected → go to action type and open the dropdown
                const at = document.getElementById('action_type_display');
                at.focus();
                setTimeout(() => _toggleActionDrop(), 30);
            } else {
                showItemModal();
            }
            return;
        }

        // action_type_display → open/confirm dropdown
        if (el.id === 'action_type_display') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            if (!actionOpen) {
                // Just move to qty since value already set
                const qty = document.getElementById('quantity');
                qty.focus(); qty.select();
            }
            return;
        }

        // quantity → save
        if (el.id === 'quantity') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            saveItem();
            return;
        }
    }

    // ── Ctrl+S → Save ─────────────────────────────────────────
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        saveItem();
        return;
    }

}, true); // capture phase

// ============================================================================
// LEGACY FUNCTIONS (fallback)
// ============================================================================
function _legacy_showItemModal() {
    let html = `
        <div class="item-modal-backdrop show" id="itemBackdrop"></div>
        <div class="item-modal show" id="itemModal">
            <div class="modal-header-custom">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item</h5>
                <button type="button" class="btn-close btn-close-white" onclick="_legacy_closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by code or name..." oninput="_legacy_filterItems()">
                </div>
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-warning" style="position: sticky; top: 0;">
                            <tr><th>Code</th><th>Item Name</th><th>Packing</th><th>Company</th></tr>
                        </thead>
                        <tbody id="itemsListBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeItemModal()">Close</button>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput')?.focus();
    _legacy_renderItemsList();
}

function _legacy_renderItemsList(filter = '') {
    const tbody = document.getElementById('itemsListBody');
    const filtered = itemsData.filter(item =>
        !filter ||
        item.name?.toLowerCase().includes(filter.toLowerCase()) ||
        item.bar_code?.toLowerCase().includes(filter.toLowerCase())
    );
    tbody.innerHTML = filtered.map(item => `
        <tr class="item-row" onclick="_legacy_selectItem(${item.id})">
            <td>${item.bar_code || item.id}</td>
            <td>${item.name || ''}</td>
            <td>${item.packing || ''}</td>
            <td>${item.company_name || ''}</td>
        </tr>
    `).join('');
}

function _legacy_filterItems() {
    _legacy_renderItemsList(document.getElementById('itemSearchInput').value);
}

function _legacy_selectItem(itemId) {
    const item = itemsData.find(i => i.id === itemId);
    if (!item) return;
    document.getElementById('item_id').value   = item.id;
    document.getElementById('item_code').value = item.bar_code || item.id;
    document.getElementById('item_name').value = item.name || '';
    _legacy_closeItemModal();
    document.getElementById('action_type_display').focus();
}

function _legacy_closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemBackdrop')?.remove();
}

let isSubmitting = false;

function saveItem() {
    const itemId     = document.getElementById('item_id').value;
    const actionType = document.getElementById('action_type').value;
    const quantity   = document.getElementById('quantity').value;

    if (!itemId) {
        alert('Please select an item');
        document.getElementById('item_code').focus();
        return;
    }
    if (!quantity || parseFloat(quantity) <= 0) {
        alert('Please enter a valid quantity');
        document.getElementById('quantity').focus();
        return;
    }
    if (isSubmitting) return;
    isSubmitting = true;

    const saveBtn = document.querySelector('button[onclick="saveItem()"]');
    const originalBtnHtml = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

    if (typeof window.markAsSaving === 'function') window.markAsSaving();

    fetch('<?php echo e(route("admin.pending-order-item.store")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ item_id: itemId, action_type: actionType, quantity: quantity })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Item saved successfully!');
            resetForm();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving item');
    })
    .finally(() => {
        isSubmitting = false;
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalBtnHtml;
    });
}

function resetForm() {
    document.getElementById('item_id').value            = '';
    document.getElementById('item_code').value          = '';
    document.getElementById('item_name').value          = '';
    document.getElementById('action_type').value        = 'I';
    document.getElementById('action_type_display').value = 'I';
    document.getElementById('quantity').value           = '';
    document.getElementById('item_code').focus();
}

// (all functions defined above)

// (legacy and save functions defined above in main script block)
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/pending-order-item/transaction.blade.php ENDPATH**/ ?>