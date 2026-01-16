@extends('layouts.admin')

@section('title', 'New Item Generation in Pending Order')

@push('styles')
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
@endpush

@section('content')
<section class="poi-form py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i> New Item Generation in Pending Order</h4>
                <div class="text-muted small">Add or remove items from pending orders</div>
            </div>
            <div>
                <a href="{{ route('admin.pending-order-item.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="poiForm" autocomplete="off">
                    @csrf
                    
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
                                    <select id="action_type" name="action_type" class="form-select no-select2" style="width: 80px;">
                                        <option value="I" selected>I</option>
                                        <option value="D">D</option>
                                    </select>
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
@endsection

@push('scripts')
<script>
let itemsData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    document.getElementById('item_code').focus();
});

function loadItems() {
    fetch('{{ route("admin.pending-order-item.getItems") }}')
        .then(response => response.json())
        .then(data => { itemsData = data || []; })
        .catch(error => console.error('Error:', error));
}

function showItemModal() {
    let html = `
        <div class="item-modal-backdrop show" id="itemBackdrop"></div>
        <div class="item-modal show" id="itemModal">
            <div class="modal-header-custom">
                <h5 class="mb-0"><i class="bi bi-search me-2"></i>Select Item</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeItemModal()"></button>
            </div>
            <div class="modal-body-custom">
                <div class="mb-3">
                    <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by code or name..." onkeyup="filterItems()">
                </div>
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-bordered table-sm" style="font-size: 11px;">
                        <thead class="table-warning" style="position: sticky; top: 0;">
                            <tr>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Packing</th>
                                <th>Company</th>
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
    renderItemsList();
}

function renderItemsList(filter = '') {
    const tbody = document.getElementById('itemsListBody');
    const filtered = itemsData.filter(item => 
        !filter || 
        item.name?.toLowerCase().includes(filter.toLowerCase()) || 
        item.bar_code?.toLowerCase().includes(filter.toLowerCase())
    );
    
    tbody.innerHTML = filtered.map(item => `
        <tr class="item-row" onclick="selectItem(${item.id})">
            <td>${item.bar_code || item.id}</td>
            <td>${item.name || ''}</td>
            <td>${item.packing || ''}</td>
            <td>${item.company_name || ''}</td>
        </tr>
    `).join('');
}

function filterItems() {
    renderItemsList(document.getElementById('itemSearchInput').value);
}

function selectItem(itemId) {
    const item = itemsData.find(i => i.id === itemId);
    if (!item) return;
    
    document.getElementById('item_id').value = item.id;
    document.getElementById('item_code').value = item.bar_code || item.id;
    document.getElementById('item_name').value = item.name || '';
    
    closeItemModal();
    document.getElementById('quantity').focus();
}

function closeItemModal() {
    document.getElementById('itemModal')?.remove();
    document.getElementById('itemBackdrop')?.remove();
}

let isSubmitting = false;

function saveItem() {
    const itemId = document.getElementById('item_id').value;
    const actionType = document.getElementById('action_type').value;
    const quantity = document.getElementById('quantity').value;
    
    if (!itemId) {
        alert('Please select an item');
        return;
    }
    
    if (!quantity || parseFloat(quantity) <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    // Prevent double submission
    if (isSubmitting) { return; }
    isSubmitting = true;
    
    // Disable button and show loading
    const saveBtn = document.querySelector('button[onclick="saveItem()"]');
    const originalBtnHtml = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
    
    fetch('{{ route("admin.pending-order-item.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            item_id: itemId,
            action_type: actionType,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Item saved successfully!');
            resetForm();
            isSubmitting = false;
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnHtml;
        } else {
            alert('Error: ' + result.message);
            isSubmitting = false;
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving item');
        isSubmitting = false;
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalBtnHtml;
    });
}

function resetForm() {
    document.getElementById('item_id').value = '';
    document.getElementById('item_code').value = '';
    document.getElementById('item_name').value = '';
    document.getElementById('action_type').value = 'I';
    document.getElementById('quantity').value = '';
    document.getElementById('item_code').focus();
}
</script>
@endpush
