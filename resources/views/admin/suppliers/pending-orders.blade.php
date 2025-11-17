@extends('layouts.admin')
@section('title', 'Pending Orders - ' . $supplier->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center">
            <i class="bi bi-cart-check me-2"></i> Pending Orders - {{ $supplier->name }}
        </h4>
        <div class="text-muted small">Manage pending orders for this supplier</div>
    </div>
    <div>
        <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        @php
            $uniqueOrderNos = $orders->pluck('order_no')->unique()->filter();
        @endphp
        @if($uniqueOrderNos->isNotEmpty())
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openPrintModal()">
            <i class="bi bi-printer"></i> Print Order
        </button>
        @endif
    </div>
</div>

<!-- Alert Messages -->
<div id="alert-container"></div>

<div class="card shadow-sm mb-2">
    <div class="card-header bg-primary text-white py-2">
        <h6 class="mb-0 small"><i class="bi bi-plus-circle me-2"></i>Add New Order</h6>
    </div>
    <div class="card-body p-2">
        <form id="addOrderForm">
            @csrf
            <div class="row g-2 mb-2">
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Order No.</label>
                    <input type="text" name="order_no" id="orderNo" class="form-control form-control-sm" placeholder="ORD-001" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold mb-1">Select Item</label>
                    <select name="item_id" id="itemSelect" class="form-select form-select-sm" required onchange="handleItemSelectChange()">
                        <option value="">-- Select Item --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" 
                                data-code="{{ $item->bar_code ?: $item->id }}" 
                                data-name="{{ $item->name }}" 
                                data-pack="{{ $item->packing }}"
                                data-company="{{ $item->company_short_name }}"
                                data-stock="{{ $item->total_stock }}">
                                {{ $item->bar_code ?: $item->id }} - {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Company</label>
                    <input type="text" id="itemCompany" class="form-control form-control-sm" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Balance Qty</label>
                    <input type="number" name="balance_qty" id="balanceQty" class="form-control form-control-sm" value="0" step="1" required readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Order Date</label>
                    <input type="date" name="order_date" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Item Code</label>
                    <input type="text" id="itemCode" class="form-control form-control-sm" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Item Name</label>
                    <input type="text" id="itemName" class="form-control form-control-sm" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold mb-1">Pack</label>
                    <input type="text" id="itemPack" class="form-control form-control-sm" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Order Qty</label>
                    <input type="number" name="order_qty" class="form-control form-control-sm" value="0" required step="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Free Qty</label>
                    <input type="number" name="free_qty" class="form-control form-control-sm" value="0" step="1" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success btn-sm w-100" id="submitBtn">
                        <i class="bi bi-plus-circle"></i> Add
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card shadow-sm">
    <div class="table-responsive" style="max-height: 350px; overflow-y: auto; position: relative;">
        <div id="table-loading" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 999; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <table class="table table-sm align-middle mb-0" style="font-size: 0.875rem;">
            <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Order No.</th>
                    <th class="text-center">Company</th>
                    <th class="text-center">Bal Qty</th>
                    <th class="text-center">Item Code</th>
                    <th>Item Name</th>
                    <th class="text-center">Pack</th>
                    <th class="text-center">Order Qty</th>
                    <th class="text-center">Free Qty</th>
                    <th class="text-center">Other Order</th>
                    <th class="text-center">Order Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="orders-table-body">
                @forelse($orders as $index => $order)
                <tr data-order-id="{{ $order->id }}" 
                    data-item-cost="{{ $order->item->cost ?? 0 }}"
                    data-order-qty="{{ $order->order_qty ?? 0 }}"
                    data-free-qty="{{ $order->free_qty ?? 0 }}"
                    data-item-name="{{ $order->item->name ?? '---' }}"
                    class="order-row clickable-row"
                    style="cursor: pointer;">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center"><span class="badge bg-primary">{{ $order->order_no ?? '---' }}</span></td>
                    <td class="text-center">{{ $order->item->company_short_name ?? '---' }}</td>
                    <td class="text-center">{{ $order->balance_qty ?? 0 }}</td>
                    <td class="text-center">{{ $order->item->bar_code ?? '---' }}</td>
                    <td>{{ $order->item->name ?? '---' }}</td>
                    <td class="text-center">{{ $order->item->packing ?? '---' }}</td>
                    <td class="text-center">{{ $order->order_qty ?? 0 }}</td>
                    <td class="text-center">{{ $order->free_qty ?? 0 }}</td>
                    <td class="text-center">{{ $order->other_order ?? 0 }}</td>
                    <td class="text-center">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d-M-y') : '---' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.items.pending-orders', $order->item_id) }}" 
                            class="btn btn-sm btn-outline-info me-1" 
                            title="View Item Pending Orders">
                            <i class="bi bi-eye"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-order" 
                            data-order-id="{{ $order->id }}" 
                            data-item-name="{{ $order->item->name ?? 'Unknown Item' }}" 
                            title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr id="no-data-row">
                    <td colspan="12" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">No orders added yet. Add your first order above.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-md-4">
                <div class="text-muted small">Total Orders: <span id="total-orders">{{ $orders->count() }}</span></div>
            </div>
            <div class="col-md-4 text-center">
                <strong>Item Cost:</strong> 
                <span class="badge bg-info text-dark fs-6" id="item-cost">Click on row to view</span>
            </div>
            <div class="col-md-4 text-end">
                <strong>Total Value (All Items):</strong> 
                <span class="badge bg-success fs-6" id="total-value">₹{{ number_format($orders->sum(function($order) { return ($order->item->cost ?? 0) * (($order->order_qty ?? 0) + ($order->free_qty ?? 0)); }), 2) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Print Order Modal -->
<div id="printOrderModal" class="print-modal">
    <div class="print-modal-content">
        <div class="print-modal-header">
            <h5 class="print-modal-title">
                <i class="bi bi-printer me-2"></i>Print Order
            </h5>
            <button type="button" class="btn-close-modal" onclick="closePrintModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="print-modal-body">
            <form id="printOrderForm">
                <div class="mb-3">
                    <label for="printOrderNo" class="form-label fw-bold">Select Order No.</label>
                    <select class="form-select" id="printOrderNo" required>
                        <option value="">-- Select Order No --</option>
                        @foreach($uniqueOrderNos as $orderNo)
                            <option value="{{ $orderNo }}">{{ $orderNo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Print with Total Value?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="printWithTotal" id="printWithTotal" value="1" checked>
                        <label class="form-check-label" for="printWithTotal">
                            Yes, include total value
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="printWithTotal" id="printWithoutTotal" value="0">
                        <label class="form-check-label" for="printWithoutTotal">
                            No, without total value
                        </label>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-secondary" onclick="closePrintModal()">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="printModalBackdrop" class="print-modal-backdrop"></div>

<!-- Order Confirmation Modal -->
<div id="orderConfirmModal" class="confirm-modal">
    <div class="confirm-modal-content">
        <div class="confirm-modal-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 2.5rem; color: #0d6efd;"></i>
                <h5 class="confirm-modal-title">Order Already Given</h5>
            </div>
        </div>
        <div class="confirm-modal-body">
            <p style="font-size: 15px; margin-bottom: 15px; font-weight: 500;">
                This item's order has already been given to:
            </p>
            <div id="suppliersList" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; max-height: 200px; overflow-y: auto;">
                <!-- Suppliers list will be inserted here -->
            </div>
            <p style="font-size: 14px; color: #6c757d; margin-bottom: 20px;">
                Do you still want to continue?
            </p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="cancelOrderConfirm()">
                    <i class="bi bi-x-circle"></i> No
                </button>
                <button type="button" class="btn btn-primary" id="confirmOrderBtn">
                    <i class="bi bi-check-circle"></i> Yes
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="cancelOrderConfirm()">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div id="confirmModalBackdrop" class="confirm-modal-backdrop"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addOrderForm');
    const submitBtn = document.getElementById('submitBtn');
    const tableBody = document.getElementById('orders-table-body');
    const alertContainer = document.getElementById('alert-container');
    const totalOrders = document.getElementById('total-orders');
    const tableLoading = document.getElementById('table-loading');
    const itemSelect = document.getElementById('itemSelect');
    const itemCostElement = document.getElementById('item-cost');
    const totalValueElement = document.getElementById('total-value');
    
    // Calculate total value of all items
    function calculateTotalValue() {
        const allRows = document.querySelectorAll('.order-row');
        let totalValue = 0;
        
        allRows.forEach(row => {
            const itemCost = parseFloat(row.getAttribute('data-item-cost')) || 0;
            const orderQty = parseFloat(row.getAttribute('data-order-qty')) || 0;
            const freeQty = parseFloat(row.getAttribute('data-free-qty')) || 0;
            totalValue += (itemCost * (orderQty + freeQty));
        });
        
        totalValueElement.textContent = '₹' + totalValue.toFixed(2);
    }
    
    // Row click to show item cost
    document.addEventListener('click', function(e) {
        const row = e.target.closest('.clickable-row');
        if (row && !e.target.closest('button')) {
            // Remove highlight from all rows
            document.querySelectorAll('.clickable-row').forEach(r => {
                r.classList.remove('table-active');
            });
            
            // Highlight clicked row
            row.classList.add('table-active');
            
            const itemCost = parseFloat(row.getAttribute('data-item-cost')) || 0;
            const itemName = row.getAttribute('data-item-name') || 'Unknown';
            
            itemCostElement.innerHTML = `<strong>${itemName}:</strong> ₹${itemCost.toFixed(2)}`;
        }
    });
    
    // Auto-populate item fields - Global function for inline onchange
    window.handleItemSelectChange = function() {
        const itemSelect = document.getElementById('itemSelect');
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        
        if (itemSelect.value) {
            // Populate all readonly fields
            document.getElementById('itemCode').value = selectedOption.getAttribute('data-code') || '---';
            document.getElementById('itemName').value = selectedOption.getAttribute('data-name') || '';
            document.getElementById('itemPack').value = selectedOption.getAttribute('data-pack') || '---';
            document.getElementById('itemCompany').value = selectedOption.getAttribute('data-company') || '---';
            document.getElementById('balanceQty').value = selectedOption.getAttribute('data-stock') || 0;
            
            // Focus on Order Qty field for user input
            document.querySelector('input[name="order_qty"]').focus();
        } else {
            // Clear all fields when no item selected
            document.getElementById('itemCode').value = '';
            document.getElementById('itemName').value = '';
            document.getElementById('itemPack').value = '';
            document.getElementById('itemCompany').value = '';
            document.getElementById('balanceQty').value = 0;
        }
    };
    
    // Show alert message
    function showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alert);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
    
    // Add/Update Order via AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Adding...`;
        
        const url = '{{ route("admin.suppliers.pending-orders.store", $supplier) }}';
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Check if confirmation is required
            if (data.requires_confirmation) {
                // Show confirmation modal with suppliers list
                showOrderConfirmModal(data.suppliers, formData);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Add';
                return;
            }
            
            if (data.success) {
                showAlert(data.message, 'success');
                form.reset();
                    // Remove no-data row if exists
                    const noDataRow = document.getElementById('no-data-row');
                    if (noDataRow) {
                        noDataRow.remove();
                    }
                    
                    // Add new row to table
                    const newRow = document.createElement('tr');
                    newRow.setAttribute('data-order-id', data.order.id);
                    newRow.setAttribute('data-item-cost', data.order.item_cost || 0);
                    newRow.setAttribute('data-order-qty', data.order.order_qty);
                    newRow.setAttribute('data-free-qty', data.order.free_qty);
                    newRow.setAttribute('data-item-name', data.order.item_name || 'Unknown');
                    newRow.classList.add('order-row', 'clickable-row');
                    newRow.style.cursor = 'pointer';
                    newRow.innerHTML = `
                        <td class="text-center">${data.order.index}</td>
                        <td class="text-center"><span class="badge bg-primary">${data.order.order_no || '---'}</span></td>
                        <td class="text-center">${data.order.company || '---'}</td>
                        <td class="text-center">${data.order.balance_qty}</td>
                        <td class="text-center">${data.order.item_code || '---'}</td>
                        <td>${data.order.item_name || '---'}</td>
                        <td class="text-center">${data.order.pack || '---'}</td>
                        <td class="text-center">${data.order.order_qty}</td>
                        <td class="text-center">${data.order.free_qty}</td>
                        <td class="text-center">${data.order.other_order}</td>
                        <td class="text-center">${data.order.order_date}</td>
                        <td class="text-end">
                            <a href="/admin/items/${data.order.item_id}/pending-orders" 
                                class="btn btn-sm btn-outline-info me-1" 
                                title="View Item Pending Orders">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-order" 
                                data-order-id="${data.order.id}" 
                                data-item-name="${data.order.item_name || 'Unknown Item'}" 
                                title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(newRow);
                    
                    // Update total count
                    totalOrders.textContent = parseInt(totalOrders.textContent) + 1;
                    
                    // Recalculate total value
                    calculateTotalValue();
                    
                    // Update order numbers in print modal dropdown
                    updatePrintOrderDropdown(data.order.order_no);
                
                // Clear readonly fields
                document.getElementById('itemCode').value = '';
                document.getElementById('itemName').value = '';
                document.getElementById('itemPack').value = '';
                document.getElementById('itemCompany').value = '';
                document.getElementById('balanceQty').value = 0;
                
                // Reset button
                submitBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Add';
            } else {
                showAlert(data.message || 'Error saving order', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Add';
        });
    });
    
    
    // Delete Order via AJAX using Global Modal
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-order')) {
            const btn = e.target.closest('.delete-order');
            const orderId = btn.getAttribute('data-order-id');
            const itemName = btn.getAttribute('data-item-name') || 'this order';
            
            // Show global delete modal
            window.deleteModal.show({
                message: `Are you sure you want to delete the order for "${itemName}"? This action cannot be undone.`,
                onConfirm: function() {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                    
                    fetch(`{{ route("admin.suppliers.pending-orders.delete", [$supplier, "ORDER_ID"]) }}`.replace('ORDER_ID', orderId), {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message, 'success');
                            
                            // Remove row from table with fade effect
                            const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                            if (row) {
                                row.style.transition = 'opacity 0.3s';
                                row.style.opacity = '0';
                                setTimeout(() => row.remove(), 300);
                            }
                            
                            // Update total count
                            setTimeout(() => {
                                totalOrders.textContent = parseInt(totalOrders.textContent) - 1;
                                
                                // Recalculate totals
                                calculateTotalValue();
                                
                                // Show no-data row if no orders left
                                if (tableBody.children.length === 0) {
                                    tableBody.innerHTML = `
                                        <tr id="no-data-row">
                                            <td colspan="12" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                <p class="mb-0 mt-2">No orders added yet. Add your first order above.</p>
                                            </td>
                                        </tr>
                                    `;
                                    totalValueElement.textContent = '₹0.00';
                                    itemCostElement.textContent = 'Click on row to view';
                                }
                            }, 300);
                        } else {
                            showAlert(data.message || 'Error deleting order', 'danger');
                            btn.disabled = false;
                            btn.innerHTML = '<i class="bi bi-trash"></i>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('An error occurred. Please try again.', 'danger');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-trash"></i>';
                    });
                }
            });
        }
    });
    
    // Update print order dropdown with new order number
    function updatePrintOrderDropdown(orderNo) {
        const dropdown = document.getElementById('printOrderNo');
        
        // Check if order number already exists
        let exists = false;
        for (let i = 0; i < dropdown.options.length; i++) {
            if (dropdown.options[i].value === orderNo) {
                exists = true;
                break;
            }
        }
        
        // Add if doesn't exist
        if (!exists && orderNo) {
            const option = document.createElement('option');
            option.value = orderNo;
            option.textContent = orderNo;
            dropdown.appendChild(option);
        }
    }
    
    // Print Modal Functions
    window.openPrintModal = function() {
        const modal = document.getElementById('printOrderModal');
        const backdrop = document.getElementById('printModalBackdrop');
        
        backdrop.style.display = 'block';
        modal.style.display = 'block';
        
        setTimeout(() => {
            backdrop.classList.add('show');
            modal.classList.add('show');
        }, 10);
    };
    
    window.closePrintModal = function() {
        const modal = document.getElementById('printOrderModal');
        const backdrop = document.getElementById('printModalBackdrop');
        
        modal.classList.remove('show');
        backdrop.classList.remove('show');
        
        setTimeout(() => {
            modal.style.display = 'none';
            backdrop.style.display = 'none';
        }, 300);
    };
    
    // Close modal when clicking backdrop
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'printModalBackdrop') {
            closePrintModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('printOrderModal');
            if (modal && modal.classList.contains('show')) {
                closePrintModal();
            }
        }
    });
    
    // Handle print form submission
    document.getElementById('printOrderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const orderNo = document.getElementById('printOrderNo').value;
        const withTotal = document.querySelector('input[name="printWithTotal"]:checked').value;
        
        if (!orderNo) {
            alert('Please select an order number');
            return;
        }
        
        // Build URL with route helper
        const baseUrl = '{{ route("admin.suppliers.pending-orders.print", [$supplier, "ORDER_NO_PLACEHOLDER"]) }}';
        const url = baseUrl.replace('ORDER_NO_PLACEHOLDER', encodeURIComponent(orderNo)) + `?with_total=${withTotal}`;
        window.open(url, '_blank');
        
        // Close modal
        closePrintModal();
    });
    
    // Order Confirmation Modal Functions
    let pendingFormData = null;
    
    function showOrderConfirmModal(suppliers, formData) {
        pendingFormData = formData;
        const modal = document.getElementById('orderConfirmModal');
        const backdrop = document.getElementById('confirmModalBackdrop');
        const suppliersList = document.getElementById('suppliersList');
        
        // Build suppliers list HTML
        let html = '<ul style="list-style: none; padding: 0; margin: 0;">';
        suppliers.forEach((supplier, index) => {
            html += `
                <li style="padding: 10px; margin-bottom: 8px; background: white; border-left: 4px solid #0d6efd; border-radius: 4px;">
                    <strong style="color: #0d6efd;">${index + 1}. ${supplier.name}</strong>
                    ${supplier.order_no !== 'N/A' ? `<span style="color: #6c757d; font-size: 13px;"> [${supplier.order_no}]</span>` : ''}
                </li>
            `;
        });
        html += '</ul>';
        suppliersList.innerHTML = html;
        
        backdrop.style.display = 'block';
        modal.style.display = 'block';
        
        setTimeout(() => {
            backdrop.classList.add('show');
            modal.classList.add('show');
        }, 10);
    }
    
    window.cancelOrderConfirm = function() {
        const modal = document.getElementById('orderConfirmModal');
        const backdrop = document.getElementById('confirmModalBackdrop');
        
        modal.classList.remove('show');
        backdrop.classList.remove('show');
        
        setTimeout(() => {
            modal.style.display = 'none';
            backdrop.style.display = 'none';
            pendingFormData = null;
        }, 300);
    };
    
    // Confirm order button - force create order
    document.getElementById('confirmOrderBtn').addEventListener('click', function() {
        if (!pendingFormData) return;
        
        // Add a flag to bypass confirmation check
        pendingFormData.append('force_create', '1');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Adding...`;
        
        const url = '{{ route("admin.suppliers.pending-orders.store", $supplier) }}';
        
        fetch(url, {
            method: 'POST',
            body: pendingFormData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            cancelOrderConfirm();
            
            if (data.success) {
                showAlert(data.message, 'success');
                form.reset();
                location.reload(); // Reload to show new order
            } else {
                showAlert(data.message || 'Error saving order', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Add';
        });
    });
    
    // Close modal on backdrop click
    document.getElementById('confirmModalBackdrop').addEventListener('click', function() {
        cancelOrderConfirm();
    });
});
</script>

<style>
/* Print Modal Styles */
.print-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 90%;
    max-width: 500px;
    z-index: 1050;
    opacity: 0;
    transition: all 0.3s ease-in-out;
}

.print-modal.show {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

.print-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.print-modal-header {
    padding: 1rem 1.25rem;
    border-bottom: 2px solid #dee2e6;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.print-modal-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #ffffff;
}

.btn-close-modal {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 1rem;
}

.btn-close-modal:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.print-modal-body {
    padding: 1.5rem;
    background: #f8f9fa;
}

.print-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.print-modal-backdrop.show {
    opacity: 1;
}

/* Confirmation Modal Styles */
.confirm-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    width: 90%;
    max-width: 550px;
    z-index: 1060;
    opacity: 0;
    transition: all 0.3s ease-in-out;
}

.confirm-modal.show {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

.confirm-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.confirm-modal-header {
    padding: 1.5rem;
    border-bottom: 2px solid #dee2e6;
    background: #e7f3ff;
}

.confirm-modal-title {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
    color: #0d6efd;
}

.confirm-modal-body {
    padding: 1.5rem;
    background: #fff;
}

.confirm-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1055;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.confirm-modal-backdrop.show {
    opacity: 1;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .print-modal {
        width: 95%;
    }
    
    .print-modal-body {
        padding: 1rem;
    }
    
    .print-modal-header {
        padding: 0.75rem 1rem;
    }
}
</style>
@endpush
