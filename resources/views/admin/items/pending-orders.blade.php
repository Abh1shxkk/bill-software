@extends('layouts.admin')
@section('title', 'Pending Orders - ' . $item->name)
@section('content')
    <style>
        .pending-header {
            background: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .pending-table {
            font-size: 12px;
            border-collapse: collapse;
        }
        .pending-table th {
            background: #e9ecef;
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .pending-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        .pending-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .action-buttons button,
        .action-buttons a {
            padding: 6px 12px;
            margin: 0 5px;
            font-size: 12px;
        }
        .form-section {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .form-group input,
        .form-group select {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            font-size: 12px;
        }
        .pending-row {
            background: #fff3cd;
        }
        .received-row {
            background: #d4edda;
        }
        
        /* Warning Modal Styles */
        .warning-modal {
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

        .warning-modal.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .warning-modal-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .warning-modal-header {
            padding: 1.5rem;
            border-bottom: 2px solid #dee2e6;
            background: #fff3cd;
        }

        .warning-modal-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #856404;
        }

        .warning-modal-body {
            padding: 1.5rem;
            background: #fff;
        }

        .warning-modal-backdrop {
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

        .warning-modal-backdrop.show {
            opacity: 1;
        }
        
        @media print {
            .action-buttons, .btn {
                display: none !important;
            }
        }
    </style>

    <div class="container-fluid">
        <!-- Header -->
        <div class="pending-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0; color: #0d6efd;">
                        <strong>PENDING ORDERS</strong>
                    </h4>
                    <div style="margin-top: 10px; font-size: 13px;">
                        <strong>Item:</strong> {{ $item->name }} | 
                        <strong>Packing:</strong> {{ $item->packing ?? '1*1' }} | 
                        <strong>Qty:</strong> {{ $item->getTotalQuantity() }}
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 14px; color: #6c757d;">
                        <i class="bi bi-telephone-fill me-2"></i><strong>Supplier Contact:</strong>
                    </div>
                    <div id="supplierContact" style="font-size: 18px; font-weight: bold; color: #0d6efd; margin-top: 5px;">
                        Click on supplier row
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders Table -->
        <div style="border: 1px solid #dee2e6; border-radius: 4px; overflow: hidden; max-height: 500px; overflow-y: auto;">
            <table class="pending-table" style="width: 100%; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 12%;">Supplier Name</th>
                        <th style="width: 8%;">Date</th>
                        <th style="width: 8%;">Rate</th>
                        <th style="width: 6%;">Tax%</th>
                        <th style="width: 6%;">Dis%</th>
                        <th style="width: 6%;">Ex.</th>
                        <th style="width: 8%;">Cost</th>
                        <th style="width: 6%;">SCM %</th>
                        <th style="width: 6%;">Qty</th>
                        <th style="width: 6%;">Days</th>
                        <th style="width: 6%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: #fff3cd; font-weight: bold;">
                        <td colspan="9">PENDING ORDER</td>
                        <td style="text-align: center;">{{ $item->getTotalQuantity() }}</td>
                        <td></td>
                    </tr>
                    @if($pendingOrders->count() > 0)
                        @foreach($pendingOrders as $order)
                            <tr class="pending-row order-row-{{ $order->id }} supplier-row" 
                                data-supplier-mobile="{{ $order->supplier->mobile ?? 'N/A' }}"
                                data-supplier-telephone="{{ $order->supplier->telephone ?? 'N/A' }}"
                                style="cursor: pointer;">
                                <!-- Supplier Name -->
                                <td>
                                    {{ strtoupper($order->supplier->name ?? 'N/A') }}
                                </td>
                                <!-- Order Date -->
                                <td style="text-align: center;">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d-M-y') : '-' }}</td>
                                <!-- Rate -->
                                <td style="text-align: right;">-</td>
                                <!-- Tax% -->
                                <td style="text-align: center;">-</td>
                                <!-- Dis% (Discount%) -->
                                <td style="text-align: center;">-</td>
                                <!-- Ex. (Extra/Excise) -->
                                <td style="text-align: center;">-</td>
                                <!-- Cost -->
                                <td style="text-align: right;">-</td>
                                <!-- SCM% -->
                                <td style="text-align: center;">-</td>
                                <!-- Qty -->
                                <td style="text-align: center; font-weight: bold;" class="qty-display-{{ $order->id }}">
                                    {{ ($order->order_qty ?? 0) + ($order->free_qty ?? 0) }}
                                </td>
                                <!-- Days Pending -->
                                <td style="text-align: center;">
                                    @php
                                        $days = $order->order_date ? abs(now()->diffInDays(\Carbon\Carbon::parse($order->order_date))) : 0;
                                    @endphp
                                    {{ (int)$days }}
                                </td>
                                <!-- Actions -->
                                <td style="text-align: center;">
                                    <button class="btn btn-sm btn-primary edit-qty-btn" 
                                        data-order-id="{{ $order->id }}"
                                        data-order-qty="{{ $order->order_qty ?? 0 }}"
                                        data-free-qty="{{ $order->free_qty ?? 0 }}"
                                        title="Edit Quantity"
                                        style="padding: 4px 8px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <!-- Edit Row (Hidden by default) -->
                            <tr class="edit-row-{{ $order->id }}" style="display: none; background: #e7f3ff; border: 2px solid #0d6efd;">
                                <td colspan="11" style="padding: 15px;">
                                    <div style="display: flex; align-items: center; gap: 20px; justify-content: center;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <label style="font-weight: bold; font-size: 14px; margin: 0;">QTY:</label>
                                            <input type="number" 
                                                class="form-control form-control-sm" 
                                                id="edit-qty-{{ $order->id }}" 
                                                value="{{ $order->order_qty ?? 0 }}" 
                                                style="width: 120px; font-size: 14px; text-align: center; font-weight: bold;">
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <label style="font-weight: bold; font-size: 14px; margin: 0;">FREE:</label>
                                            <input type="number" 
                                                class="form-control form-control-sm" 
                                                id="edit-free-{{ $order->id }}" 
                                                value="{{ $order->free_qty ?? 0 }}" 
                                                style="width: 120px; font-size: 14px; text-align: center; font-weight: bold;">
                                        </div>
                                        <div style="display: flex; gap: 10px;">
                                            <button class="btn btn-success save-qty-btn" 
                                                data-order-id="{{ $order->id }}"
                                                style="padding: 8px 20px; font-weight: bold;">
                                                <i class="fas fa-check"></i> Save
                                            </button>
                                            <button class="btn btn-secondary cancel-edit-btn" 
                                                data-order-id="{{ $order->id }}"
                                                style="padding: 8px 20px; font-weight: bold;">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 20px; color: #999;">
                                No pending orders found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pendingOrders->hasPages())
            <div style="margin-top: 20px;">
                {{ $pendingOrders->links() }}
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Items
            </a>
        </div>
    </div>

    <!-- Edit Warning Modal -->
    <div id="warningModalBackdrop" class="warning-modal-backdrop"></div>
    <div id="editWarningModal" class="warning-modal">
        <div class="warning-modal-content">
            <div class="warning-modal-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem; color: #ffc107;"></i>
                    <h5 class="warning-modal-title">Order Already Given</h5>
                </div>
            </div>
            <div class="warning-modal-body">
                <p style="font-size: 16px; margin-bottom: 20px;">
                    This order has already been given to the supplier. Do you want to continue editing?
                </p>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeWarningModal()">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmEditBtn">
                        <i class="bi bi-check-circle"></i> Yes, Continue
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to display supplier contact
    function displaySupplierContact(row) {
        const mobile = row.getAttribute('data-supplier-mobile');
        const telephone = row.getAttribute('data-supplier-telephone');
        const contactDisplay = document.getElementById('supplierContact');
        
        // Remove highlight from all rows
        document.querySelectorAll('.supplier-row').forEach(r => {
            r.style.backgroundColor = '';
        });
        
        // Highlight clicked row
        row.style.backgroundColor = '#cfe2ff';
        
        // Display contact
        let contactText = '';
        if (mobile !== 'N/A' && telephone !== 'N/A') {
            contactText = `📱 ${mobile} | ☎️ ${telephone}`;
        } else if (mobile !== 'N/A') {
            contactText = `📱 ${mobile}`;
        } else if (telephone !== 'N/A') {
            contactText = `☎️ ${telephone}`;
        } else {
            contactText = 'No contact available';
        }
        
        contactDisplay.textContent = contactText;
    }
    
    // Supplier row click to show contact
    document.querySelectorAll('.supplier-row').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on Edit button
            if (e.target.closest('.edit-qty-btn') || e.target.closest('.cancel-edit-btn') || e.target.closest('.save-qty-btn')) {
                return;
            }
            
            displaySupplierContact(this);
        });
    });
    
    // Auto-display first supplier contact on page load
    const firstSupplierRow = document.querySelector('.supplier-row');
    if (firstSupplierRow) {
        displaySupplierContact(firstSupplierRow);
    }
    
    // Warning Modal Functions
    let currentEditOrderId = null;
    let currentEditButton = null;
    
    window.openWarningModal = function(orderId, button) {
        currentEditOrderId = orderId;
        currentEditButton = button;
        
        const modal = document.getElementById('editWarningModal');
        const backdrop = document.getElementById('warningModalBackdrop');
        
        backdrop.style.display = 'block';
        modal.style.display = 'block';
        
        setTimeout(() => {
            backdrop.classList.add('show');
            modal.classList.add('show');
        }, 10);
    };
    
    window.closeWarningModal = function() {
        const modal = document.getElementById('editWarningModal');
        const backdrop = document.getElementById('warningModalBackdrop');
        
        modal.classList.remove('show');
        backdrop.classList.remove('show');
        
        setTimeout(() => {
            modal.style.display = 'none';
            backdrop.style.display = 'none';
            currentEditOrderId = null;
            currentEditButton = null;
        }, 300);
    };
    
    // Confirm edit button
    document.getElementById('confirmEditBtn').addEventListener('click', function() {
        if (currentEditOrderId && currentEditButton) {
            document.querySelector('.edit-row-' + currentEditOrderId).style.display = 'table-row';
            currentEditButton.disabled = true;
        }
        closeWarningModal();
    });
    
    // Close modal on backdrop click
    document.getElementById('warningModalBackdrop').addEventListener('click', function() {
        closeWarningModal();
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('editWarningModal');
            if (modal && modal.classList.contains('show')) {
                closeWarningModal();
            }
        }
    });
    
    // Edit button click with modal confirmation
    document.querySelectorAll('.edit-qty-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const orderId = this.getAttribute('data-order-id');
            openWarningModal(orderId, this);
        });
    });
    
    // Cancel button click
    document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            document.querySelector('.edit-row-' + orderId).style.display = 'none';
            document.querySelector('.edit-qty-btn[data-order-id="' + orderId + '"]').disabled = false;
        });
    });
    
    // Save button click
    document.querySelectorAll('.save-qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderQty = document.getElementById('edit-qty-' + orderId).value;
            const freeQty = document.getElementById('edit-free-' + orderId).value;
            
            // Disable button and show loading
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            // Send AJAX request
            const url = `{{ url('/admin/items/pending-orders') }}/${orderId}/update-qty`;
            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_qty: orderQty,
                    free_qty: freeQty
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the display
                    const totalQty = parseInt(orderQty) + parseInt(freeQty);
                    document.querySelector('.qty-display-' + orderId).textContent = totalQty;
                    
                    // Hide edit row
                    document.querySelector('.edit-row-' + orderId).style.display = 'none';
                    
                    // Enable edit button
                    document.querySelector('.edit-qty-btn[data-order-id="' + orderId + '"]').disabled = false;
                    
                    // Show success message
                    alert('Quantity updated successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to update quantity'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating quantity');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-check"></i> Save';
            });
        });
    });
});
</script>
@endpush
