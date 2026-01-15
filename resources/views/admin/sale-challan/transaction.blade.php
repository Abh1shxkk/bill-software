@extends('layouts.admin')

@section('title', 'Sale Challan Transaction')

@section('content')
<style>
    /* Compact form adjustments - preserving original functionality */
    .compact-form {
        font-size: 11px;
        padding: 8px;
        background: #f5f5f5;
    }
    
    .compact-form label {
        font-weight: 600;
        font-size: 11px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .compact-form input,
    .compact-form select {
        font-size: 11px;
        padding: 2px 6px;
        height: 26px;
    }
    
    .header-section {
        background: white;
        border: 1px solid #dee2e6;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 4px;
    }
    
    .header-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 6px;
    }
    
    .field-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .field-group label {
        font-weight: 600;
        font-size: 11px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .field-group input,
    .field-group select {
        font-size: 11px;
        padding: 2px 6px;
        height: 26px;
    }
    
    .inner-card {
        background: #e8f4f8;
        border: 1px solid #b8d4e0;
        padding: 8px;
        border-radius: 3px;
    }
    
    .table-compact {
        font-size: 10px;
        margin-bottom: 0;
    }
    
    .table-compact th,
    .table-compact td {
        padding: 4px;
        vertical-align: middle;
        height: 45px;
    }
    
    .table-compact th {
        background: #e9ecef;
        font-weight: 600;
        text-align: center;
        border: 1px solid #dee2e6;
        height: 40px;
    }
    
    .table-compact input {
        font-size: 10px;
        padding: 2px 4px;
        height: 22px;
        border: 1px solid #ced4da;
        width: 100%;
    }
    
    /* Table container - Shows exactly 6 rows + header */
    #itemsTableContainer {
        max-height: 310px !important;
    }
    
    .total-display {
        font-size: 16px;
        color: #0d6efd;
        text-align: right;
    }
    
    .readonly-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }

    /* Row validation colors */
    .row-incomplete {
        background-color: #ffebee !important;
        color: #c62828 !important;
    }

    .row-incomplete td {
        background-color: #ffebee !important;
        color: #c62828 !important;
    }

    .row-incomplete input {
        background-color: #ffebee !important;
        color: #c62828 !important;
    }

    .row-complete {
        background-color: #e8f5e9 !important;
        color: #2e7d32 !important;
    }

    .row-complete td {
        background-color: #e8f5e9 !important;
        color: #2e7d32 !important;
    }

    .row-complete input {
        background-color: #e8f5e9 !important;
        color: #2e7d32 !important;
    }

    /* Selected row - GREEN border to show which row's data is displayed */
    .row-selected {
        border: 3px solid #28a745 !important;
        box-shadow: 0 0 8px rgba(40, 167, 69, 0.5);
    }

    .row-selected td {
        border-top: none !important;
        border-bottom: none !important;
    }
    
    .row-selected td:first-child {
        border-left: 3px solid #28a745 !important;
    }
    
    .row-selected td:last-child {
        border-right: 3px solid #28a745 !important;
    }

    /* Row states - RED for incomplete, GREEN for complete */
    .table-danger td,
    .table-danger input {
        background-color: #f8d7da !important;
    }
    
    .table-success td,
    .table-success input {
        background-color: #d4edda !important;
    }
    
    /* Finalized (complete) rows - no selection allowed */
    tr[data-finalized="true"] {
        cursor: default !important;
    }

    /* Pending Orders Modal Styles (matching purchase transaction) */
    .pending-orders-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        width: 90%;
        max-width: 900px;
        z-index: 9999;
        opacity: 0;
        transition: all 0.3s ease-in-out;
    }

    .pending-orders-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .pending-orders-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .pending-orders-header {
        padding: 1rem 1.5rem;
        background: #ff6b35;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e55a25;
    }

    .pending-orders-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .btn-close-modal {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .btn-close-modal:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .pending-orders-body {
        padding: 0;
        background: #fff;
    }

    .pending-orders-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .pending-orders-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9998;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .pending-orders-backdrop.show {
        display: block;
        opacity: 1;
    }
    
    /* Legacy modal classes for backward compatibility */
    .choose-items-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        width: 90%;
        max-width: 900px;
        z-index: 9999;
        opacity: 0;
        transition: all 0.3s ease-in-out;
    }

    .choose-items-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .choose-items-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .choose-items-header {
        padding: 1rem 1.5rem;
        background: #ff6b35;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e55a25;
    }

    .choose-items-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .choose-items-body {
        padding: 0;
        background: #fff;
    }

    .choose-items-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9998;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .item-row-selected {
        background-color: #007bff !important;
        color: white !important;
    }

    .item-row-selected td {
        background-color: #007bff !important;
        color: white !important;
    }
    
    /* Action buttons styling */
    #itemsTableBody td:last-child {
        white-space: nowrap;
        padding: 5px !important;
    }
    
    #itemsTableBody td:last-child button {
        display: inline-block;
        vertical-align: middle;
    }

    /* Alert Modal Styles */
    .alert-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7) rotateX(-90deg);
        width: 90%;
        max-width: 500px;
        z-index: 10000;
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform-style: preserve-3d;
        perspective: 1000px;
    }
    
    .alert-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1) rotateX(0deg);
        opacity: 1;
    }
    
    .alert-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }
    
    .alert-modal-header {
        padding: 1rem 1.5rem;
        background: #dc3545;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #c82333;
    }
    
    .alert-modal-header.success {
        background: #28a745;
        border-bottom-color: #1e7e34;
    }
    
    .alert-modal-header.warning {
        background: #ffc107;
        border-bottom-color: #e0a800;
        color: #212529;
    }
    
    .alert-modal-header.info {
        background: #17a2b8;
        border-bottom-color: #138496;
    }
    
    .alert-modal-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .alert-modal-body {
        padding: 1.5rem;
        background: #fff;
        font-size: 14px;
        line-height: 1.5;
        white-space: pre-line;
    }
    
    .alert-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    
    .alert-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .alert-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    /* Toast Notification Styles */
    .toast-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 11000;
        max-width: 400px;
    }
    
    .toast-notification {
        background: #dc3545;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-left: 5px solid #a71e2a;
        position: relative;
        overflow: hidden;
    }
    
    .toast-notification.warning {
        background: #ffc107;
        color: #212529;
        border-left-color: #e0a800;
    }
    
    .toast-notification.show {
        transform: translateX(0);
        opacity: 1;
    }
    
    .toast-notification.hide {
        transform: translateX(100%);
        opacity: 0;
    }
    
    .toast-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
    }
    
    .toast-body {
        font-size: 13px;
        line-height: 1.4;
        white-space: pre-line;
    }
    
    .toast-close {
        background: transparent;
        border: none;
        color: inherit;
        font-size: 18px;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        transition: background 0.2s;
    }
    
    .toast-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .toast-notification.warning .toast-close:hover {
        background: rgba(0, 0, 0, 0.1);
    }
    
    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: rgba(255, 255, 255, 0.3);
        width: 100%;
        transform-origin: left;
        animation: toast-progress 5s linear forwards;
    }
    
    @keyframes toast-progress {
        from { transform: scaleX(1); }
        to { transform: scaleX(0); }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-file-earmark-text me-2"></i> Sale Challan Transaction</h4>
        <div class="text-muted small">Create new sale challan (Stock will be deducted, Invoice created later)</div>
    </div>
    <div>
        <a href="{{ route('admin.sale-challan.invoices') }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-text me-1"></i> Challan List
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form id="saleChallanForm" method="POST" autocomplete="off" onsubmit="return false;">
            @csrf
            
            <!-- Header Section -->
            <div class="header-section">
                <!-- Main Header Row -->
                <div class="d-flex gap-3">
                    <!-- Left Side - Date & Challan No (Image 1 style) -->
                    <div style="min-width: 220px; border: 1px solid #ccc; padding: 8px; background: #f5f5f5;">
                        <div class="field-group mb-2">
                            <label style="width: 80px;">Date</label>
                            <span style="margin-right: 5px;">:</span>
                            <input type="date" class="form-control" name="date" id="challanDate" value="{{ date('Y-m-d') }}" style="width: 120px;" onchange="updateDayName()">
                            <input type="text" class="form-control readonly-field" id="dayName" value="{{ date('l') }}" readonly style="width: 85px; margin-left: 5px;">
                        </div>
                        <div class="field-group">
                            <label style="width: 80px;">Challan No.</label>
                            <span style="margin-right: 5px;">:</span>
                            <input type="text" class="form-control" name="challan_no" id="challanNo" value="{{ $nextChallanNo }}" style="width: 80px; background-color: #f8f9fa;">
                        </div>
                    </div>
                    
                    <!-- Middle Section - Customer, Salesman, Choose Items -->
                    <div style="min-width: 300px;">
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Customer:</label>
                            <select class="form-control" name="customer_id" id="customerSelect" style="width: 250px;" autocomplete="off" onchange="updateCustomerName(); fetchCustomerDue();">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->code ?? '' }} - {{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Sales Man:</label>
                            <select class="form-control" name="salesman_id" id="salesmanSelect" autocomplete="off" onchange="updateSalesmanName()" style="width: 150px;">
                                <option value="">Select</option>
                                @foreach($salesmen as $salesman)
                                    <option value="{{ $salesman->id }}" data-name="{{ $salesman->name }}">{{ $salesman->code ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="chooseItemsBtn" onclick="openChooseItemsModal()">
                                <i class="bi bi-list-check"></i> Choose Items
                            </button>
                        </div>
                    </div>
                    
                    <!-- Right Side - Inner Card -->
                    <div class="inner-card flex-grow-1">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label style="width: 80px;">Due Date</label>
                                    <input type="date" class="form-control" name="due_date" id="dueDate" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label>Remarks:</label>
                                    <input type="text" class="form-control" name="remarks" id="remarks">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mt-1">
                            <div class="col-md-12">
                                <div class="d-flex gap-2">
                                    <div class="field-group flex-grow-1">
                                        <label>Customer DUE:</label>
                                        <input type="text" class="form-control readonly-field" id="customerDue" readonly value="0.00" style="background: #fff3cd; color: #856404; font-weight: bold;" title="Customer's existing pending due amount">
                                    </div>
                                    <div class="field-group flex-grow-1">
                                        <label>Pending Challans:</label>
                                        <input type="text" class="form-control readonly-field" id="pendingChallans" readonly value="0" style="background: #d1ecf1; color: #0c5460;" title="Number of pending challans for this customer">
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">* Due amount will be calculated when challan is converted to Sale Invoice</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Hidden fields for cash and transfer (default values) -->
                <input type="hidden" name="cash" id="cash" value="N">
                <input type="hidden" name="transfer" id="transfer" value="N">
            </div>

            
            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                            <tr>
                                <th style="width: 60px;">Code</th>
                                <th style="width: 250px;">Item Name</th>
                                <th style="width: 80px;">Batch</th>
                                <th style="width: 70px;">Exp.</th>
                                <th style="width: 60px;">Qty.</th>
                                <th style="width: 60px;">F.Qty</th>
                                <th style="width: 80px;">Sale Rate</th>
                                <th style="width: 60px;">Dis.%</th>
                                <th style="width: 80px;">MRP</th>
                                <th style="width: 90px;">Amount</th>
                                <th style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Items will be added dynamically -->
                        </tbody>
                    </table>
                </div>
                <!-- Add Row Button -->
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                        <i class="fas fa-plus-circle"></i> Add Row
                    </button>
                </div>
            </div>

            
            <!-- Calculation Section (matching purchase module structure) -->
            <div class="bg-white border rounded p-3 mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="d-flex align-items-start gap-3 border rounded p-2" style="font-size: 11px; background: #fafafa;">
                    <!-- Left Section: Case, Box, HSN Code Block -->
                    <div class="d-flex flex-column gap-2" style="min-width: 200px;">
                        <!-- Case -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>Case</strong></label>
                            <input type="text" class="form-control readonly-field text-end" id="calc_case" readonly style="width: 80px; height: 28px;" value="0">
                        </div>
                        
                        <!-- Box -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>Box</strong></label>
                            <input type="text" class="form-control readonly-field text-end" id="calc_box" readonly style="width: 80px; height: 28px;" value="0">
                        </div>
                        
                        <!-- HSN Code -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>HSN Code:</strong></label>
                            <input type="text" class="form-control readonly-field text-center" id="calc_hsn_code" readonly style="width: 100px; height: 28px; background: #ffcccc; font-weight: bold;" value="---">
                        </div>
                    </div>
                    
                    <!-- Middle Section: GST Details -->
                    <div class="d-flex flex-column gap-2">
                        <!-- CGST(%) -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px; background: #ffcccc; padding: 4px 8px; border-radius: 3px;"><strong>CGST(%):</strong></label>
                            <input type="text" class="form-control readonly-field text-center" id="calc_cgst" readonly style="width: 80px; height: 28px;" value="0">
                        </div>
                        
                        <!-- SGST(%) -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px; background: #ffcccc; padding: 4px 8px; border-radius: 3px;"><strong>SGST(%):</strong></label>
                            <input type="text" class="form-control readonly-field text-center" id="calc_sgst" readonly style="width: 80px; height: 28px;" value="0">
                        </div>
                        
                        <!-- Cess (%) -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px; background: #ffcccc; padding: 4px 8px; border-radius: 3px;"><strong>Cess (%):</strong></label>
                            <input type="text" class="form-control readonly-field text-center" id="calc_cess" readonly style="width: 80px; height: 28px;" value="0">
                        </div>
                    </div>
                    
                    <!-- Right Side: GST Amounts and Other Fields -->
                    <div class="d-flex gap-3">
                        <!-- Column 1: GST Amounts -->
                        <div class="d-flex flex-column gap-2">
                            <!-- CGST Amt -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 75px;"><strong>CGST Amt:</strong></label>
                                <div class="border rounded px-2 py-1" style="background: #fff; min-width: 80px; text-align: right; height: 28px; display: flex; align-items: center; justify-content: flex-end;">
                                    <strong id="calc_cgst_amount">0.00</strong>
                                </div>
                            </div>
                            
                            <!-- SGST Amt -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 75px;"><strong>SGST Amt:</strong></label>
                                <div class="border rounded px-2 py-1" style="background: #fff; min-width: 80px; text-align: right; height: 28px; display: flex; align-items: center; justify-content: flex-end;">
                                    <strong id="calc_sgst_amount">0.00</strong>
                                </div>
                            </div>
                            
                            <!-- CESS Amt -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 75px;"><strong>CESS Amt:</strong></label>
                                <div class="border rounded px-2 py-1" style="background: #fff; min-width: 80px; text-align: right; height: 28px; display: flex; align-items: center; justify-content: flex-end;">
                                    <strong id="calc_cess_amount">0.00</strong>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Column 2: TAX %, Excise, TCS, SC% -->
                        <div class="d-flex flex-column gap-2">
                            <!-- TAX % -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 60px;"><strong>TAX %</strong></label>
                                <input type="number" class="form-control readonly-field" id="calc_tax_percent" readonly step="0.01" style="width: 80px; height: 28px;" value="0.000">
                            </div>
                            
                            <!-- Excise -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 60px;"><strong>Excise</strong></label>
                                <input type="number" class="form-control readonly-field" id="calc_excise" readonly step="0.01" style="width: 80px; height: 28px;" value="0.00">
                            </div>
                            
                            <!-- TCS -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 60px;"><strong>TCS</strong></label>
                                <input type="number" class="form-control readonly-field" id="calc_tcs" readonly step="0.01" style="width: 80px; height: 28px;" value="0.00">
                            </div>
                        </div>
                        
                        <!-- Column 3: SC%, N -->
                        <div class="d-flex flex-column gap-2">
                            <!-- SC% -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>SC%</strong></label>
                                <input type="number" class="form-control readonly-field" id="calc_sc_percent" readonly step="0.01" style="width: 70px; height: 28px;" value="0.000">
                            </div>
                            
                            <!-- Empty space for alignment -->
                            <div style="height: 28px;"></div>
                            
                            <!-- N -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="font-weight: bold;">N</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Section (matching image - pink background) -->
            <div class="bg-white border rounded p-2 mb-2" style="background: #ffcccc;">
                <!-- Row 1: 7 fields -->
                <div class="d-flex align-items-center" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold; white-space: nowrap;">N.T.Amt.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="nt_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">SC</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="sc_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">F.T.Amt.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="ft_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Dis.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="dis_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="scm_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Tax</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="tax_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Net</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="net_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>
                
                <!-- Row 2: Only Scm.% -->
                <div class="d-flex align-items-center mt-2" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.%</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="scm_percent" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>
            </div>
            
            <!-- Detailed Info Section (matching image 2 - gray background) -->
            <div class="border rounded p-2 mb-2" style="background: #d0d0d0;">
                <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                    <!-- Row 1: Packing | N.T.Amt. | Scm. Amt. | Comp: | SrIno | SCM. -->
                    <tr>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Packing</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="text" class="form-control form-control-sm readonly-field" id="detailPacking" readonly value="" style="height: 22px; width: 60px;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>N.T.Amt.</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailNtAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Scm. Amt.</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScmAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Comp :</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="text" class="form-control form-control-sm readonly-field" id="detailCompany" readonly value="" style="height: 22px; width: 100px;"></td>
                        <td style="padding: 3px; border: 1px solid #999;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>SrIno</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;" colspan="3">
                            <div class="d-flex align-items-center gap-1">
                                <strong>SCM.</strong>
                                <input type="number" class="form-control form-control-sm readonly-field text-center" id="detailScm1" readonly value="0" style="height: 22px; width: 40px;">
                                <strong>+</strong>
                                <input type="number" class="form-control form-control-sm readonly-field text-center" id="detailScm2" readonly value="0" style="height: 22px; width: 40px;">
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Row 2: Unit | SC Amt. | Sub Tot. | Lctn: | -->
                    <tr>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Unit</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="detailUnit" readonly value="1" style="height: 22px; width: 60px;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>SC Amt.</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Sub Tot.</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailSubTot" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Lctn :</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="text" class="form-control form-control-sm readonly-field" id="detailLctn" readonly value="" style="height: 22px; width: 100px;"></td>
                        <td style="padding: 3px; border: 1px solid #999;" colspan="5"></td>
                    </tr>
                    
                    <!-- Row 3: Cl. Qty | Dis. Amt. | Tax Amt. | Net Amt. -->
                    <tr>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Cl. Qty</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="text" class="form-control form-control-sm readonly-field text-end" id="detailClQty" readonly value="" style="height: 22px; width: 60px; background: #add8e6;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Dis. Amt.</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailDisAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Tax Amt.</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailTaxAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #c0c0c0; border: 1px solid #999;"><strong>Net Amt.</strong></td>
                        <td style="padding: 3px; border: 1px solid #999;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailNetAmt" readonly value="0.00" style="height: 22px; width: 100px;"></td>
                        <td style="padding: 3px; border: 1px solid #999;" colspan="5"></td>
                    </tr>
                </table>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning btn-sm" onclick="saveChallan()">
                    <i class="bi bi-save"></i> Save Challan
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.reload()">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Choose Items Modal Backdrop -->
<div id="chooseItemsBackdrop" class="pending-orders-backdrop"></div>

<!-- Choose Items Modal -->
<div id="chooseItemsModal" class="pending-orders-modal">
    <div class="pending-orders-content">
        <div class="pending-orders-header">
            <h5 class="pending-orders-title">Choose Items</h5>
            <button type="button" class="btn-close-modal" onclick="closeChooseItemsModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body">
            <div class="p-3">
                <input type="text" class="form-control mb-3" id="itemSearchInput" placeholder="Search by Name, HSN Code, Company..." autocomplete="off" style="font-size: 12px;">
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                        <tr style="background: #ffcccc;">
                            <th style="width: 200px;">Name</th>
                            <th style="width: 120px;">HSN Code</th>
                            <th style="width: 100px;">Pack</th>
                            <th style="width: 150px;">Company</th>
                            <th style="width: 80px;">Qty</th>
                        </tr>
                    </thead>
                    <tbody id="chooseItemsBody">
                        <!-- Items will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pending-orders-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeChooseItemsModal()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
        </div>
    </div>
</div>

<!-- Batch Selection Modal Backdrop -->
<div id="batchSelectionBackdrop" class="pending-orders-backdrop"></div>

<!-- Batch Selection Modal -->
<div id="batchSelectionModal" class="pending-orders-modal" style="max-width: 900px;">
    <div class="pending-orders-content">
        <div class="pending-orders-header">
            <h5 class="pending-orders-title">Select Batch</h5>
            <button type="button" class="btn-close-modal" onclick="closeBatchSelectionModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body">
            <div class="p-3 bg-light border-bottom">
                <div class="mb-2">
                    <strong style="font-size: 14px;">Item: <span id="batchItemName" style="color: #7c3aed; font-size: 16px;">---</span></strong>
                </div>
                <input type="text" class="form-control mb-0" id="batchSearchInput" placeholder="Search by Batch No..." autocomplete="off" oninput="filterBatchesInModal()" style="font-size: 12px;">
            </div>
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #ffcccc; z-index: 10; font-weight: bold;">
                        <tr>
                            <th style="width: 120px; text-align: left; padding: 8px;">BATCH</th>
                            <th style="width: 80px; text-align: center; padding: 8px;">DATE</th>
                            <th style="width: 90px; text-align: right; padding: 8px;">RATE</th>
                            <th style="width: 90px; text-align: right; padding: 8px;">PRATE</th>
                            <th style="width: 90px; text-align: right; padding: 8px;">MRP</th>
                            <th style="width: 70px; text-align: right; padding: 8px;">QTY.</th>
                            <th style="width: 70px; text-align: right; padding: 8px;">EXP.</th>
                            <th style="width: 80px; text-align: center; padding: 8px;">CODE</th>
                        </tr>
                    </thead>
                    <tbody id="batchSelectionBody">
                        <!-- Batches will be loaded here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Batch Details Section (like in image) -->
            <div class="p-3 bg-white border-top">
                <div class="mb-2" style="font-weight: bold; font-size: 13px; color: #000;">
                    <strong>BRAND : </strong><span id="batchBrand" style="color: #7c3aed;">---</span>
                    <span class="float-end"><strong>Packing : </strong><span id="batchPacking" style="color: #7c3aed;">---</span></span>
                </div>
                <table class="table table-bordered mb-0" style="font-size: 11px;">
                    <thead style="background: #ffcccc; font-weight: bold;">
                        <tr>
                            <th style="padding: 5px;">BATCH</th>
                            <th style="padding: 5px; text-align: center;">DATE</th>
                            <th style="padding: 5px; text-align: right;">RATE</th>
                            <th style="padding: 5px; text-align: right;">PRATE</th>
                            <th style="padding: 5px; text-align: right;">MRP</th>
                            <th style="padding: 5px; text-align: right;">QTY.</th>
                            <th style="padding: 5px; text-align: center;">EXP.</th>
                            <th style="padding: 5px; text-align: center;">CODE</th>
                            <th style="padding: 5px; text-align: right;">Cost+GST</th>
                            <th style="padding: 5px; text-align: center;">SCM</th>
                        </tr>
                    </thead>
                    <tbody id="batchDetailsBody" style="background: #ffcccc;">
                        <tr>
                            <td colspan="10" class="text-center" style="padding: 8px;">Select a batch to view details</td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-2" style="font-size: 11px;">
                    <strong>Supplier : </strong><span id="batchSupplier" style="color: #0066cc; font-weight: bold;">---</span>
                </div>
            </div>
        </div>
        <div class="pending-orders-footer">
            <button type="button" class="btn btn-primary btn-sm" onclick="if(window.selectedBatch) selectBatchFromModal(window.selectedBatch); else alert('Please select a batch first');">
                <i class="bi bi-check-circle"></i> Select Batch
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchSelectionModal()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
        </div>
    </div>
</div>

<script>
// Global variables
let itemsData = [];
let itemIndex = -1;
let currentSelectedRowIndex = null;
let pendingItemSelection = null; // Store item data when waiting for batch selection
let rowGstData = {}; // Store GST calculations for each row

// Load items on page load
document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    
    // Customer name update and button state check
    const customerSelect = document.getElementById('customerSelect');
    if (customerSelect) {
        customerSelect.addEventListener('change', function() {
            updateCustomerName();
            checkChooseItemsButtonState();
        });
    }
    
    // Salesman name update and button state check
    const salesmanSelect = document.getElementById('salesmanSelect');
    if (salesmanSelect) {
        salesmanSelect.addEventListener('change', function() {
            updateSalesmanName();
            checkChooseItemsButtonState();
        });
    }
    
    // Initial button state check on page load
    checkChooseItemsButtonState();
    
    // Item search in modal
    const itemSearchInput = document.getElementById('itemSearchInput');
    if (itemSearchInput) {
        itemSearchInput.addEventListener('input', filterItemsInModal);
    }
    
    // Batch search in modal
    const batchSearchInput = document.getElementById('batchSearchInput');
    if (batchSearchInput) {
        batchSearchInput.addEventListener('input', filterBatchesInModal);
    }
    
    // Add click event to ALL existing rows for selection (GREEN border)
    const existingRows = document.querySelectorAll('#itemsTableBody tr');
    existingRows.forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on delete button
            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                return;
            }
            const rowIdx = parseInt(this.getAttribute('data-row-index'));
            if (!isNaN(rowIdx)) {
                selectRow(rowIdx);
            }
        });
    });
    console.log('✅ Click events added to', existingRows.length, 'existing rows');
});

// Load items from server
function loadItems() {
    // Items are now loaded on-demand when modal opens (with pagination)
    // This function is kept for backward compatibility
    console.log('Items will be loaded on-demand when modal opens');
}

// Pagination state for items
let itemsCurrentPage = 1;
let itemsPerPage = 50;
let itemsHasMore = true;
let itemsLoading = false;
let itemsSearchTerm = '';
let itemsSearchTimeout = null;

// Load paginated items with optional search
function loadPaginatedItems(page, isInitial = false, searchTerm = '') {
    if (itemsLoading || (!itemsHasMore && !isInitial)) return;
    
    itemsLoading = true;
    
    // Show loading indicator if not initial
    if (!isInitial) {
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'block';
    } else {
        // Show loading message in table body
        const tbody = document.getElementById('chooseItemsBody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading items...</td></tr>';
        }
    }
    
    // Build URL with search parameter
    let url = `{{ route("admin.sale-challan.getItems") }}?page=${page}&per_page=${itemsPerPage}`;
    if (searchTerm) {
        url += `&search=${encodeURIComponent(searchTerm)}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            itemsLoading = false;
            
            // Hide loading indicator
            const loadingIndicator = document.getElementById('itemsLoadingIndicator');
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            
            // Handle both array and paginated response formats
            let items = [];
            if (Array.isArray(data)) {
                items = data;
                itemsHasMore = false; // Legacy format - all items returned
            } else if (data.items) {
                items = data.items;
                if (data.pagination) {
                    itemsHasMore = data.pagination.has_more || (page < data.pagination.last_page);
                } else {
                    itemsHasMore = false;
                }
            }
            
            // Store loaded items
            if (isInitial) {
                itemsData = items;
            } else {
                itemsData = itemsData.concat(items);
            }
            
            if (isInitial) {
                console.log('Items loaded:', items.length);
                displayItemsInModalPaginated(items);
                setupItemsInfiniteScroll();
            } else {
                appendItemsToModalTable(items);
            }
            
            // Update records info
            updateItemsRecordsInfo();
            
            itemsCurrentPage++;
        })
        .catch(error => {
            itemsLoading = false;
            const loadingIndicator = document.getElementById('itemsLoadingIndicator');
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            console.error('Error loading items:', error);
        });
}

// Update challan type based on series
function updateChallanType() {
    const series = document.getElementById('seriesSelect').value;
    const display = document.getElementById('challanTypeDisplay');
    if (series === 'S2') {
        display.value = 'GST CHALLAN';
    } else if (series === 'SC') {
        display.value = 'SALE CHALLAN';
    }
}

// Update customer name (no separate field needed - name shown in dropdown)
function updateCustomerName() {
    // Customer name already displayed in dropdown, no separate field needed
}

// Fetch Customer Due Amount and Pending Challans
function fetchCustomerDue() {
    const customerSelect = document.getElementById('customerSelect');
    const customerId = customerSelect.value;
    
    // Reset if no customer selected
    if (!customerId) {
        document.getElementById('customerDue').value = '0.00';
        document.getElementById('pendingChallans').value = '0';
        return;
    }
    
    // Fetch customer's pending challan due from Sale Challan module
    fetch(`{{ url('admin/sale-challan/customer') }}/${customerId}/due`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const dueAmount = parseFloat(data.due_amount || 0);
            document.getElementById('customerDue').value = dueAmount.toFixed(2);
            
            // Show pending challans count if available
            if (data.pending_count !== undefined) {
                document.getElementById('pendingChallans').value = data.pending_count;
            }
        }
    })
    .catch(error => {
        console.error('Error fetching customer due:', error);
    });
}

// Update salesman name (no separate field needed - name shown in dropdown)
function updateSalesmanName() {
    // Salesman name already displayed in dropdown, no separate field needed
}

// Update day name
function updateDayName() {
    const dateInput = document.getElementById('challanDate');
    const dayNameInput = document.getElementById('dayName');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayNameInput.value = days[date.getDay()];
    }
}

// Check if Choose Items button should be enabled
function checkChooseItemsButtonState() {
    const customerId = document.getElementById('customerSelect')?.value;
    const salesmanId = document.getElementById('salesmanSelect')?.value;
    const chooseItemsBtn = document.getElementById('chooseItemsBtn');
    
    if (chooseItemsBtn) {
        if (customerId && salesmanId) {
            // Both selected - enable button (visual only)
            chooseItemsBtn.classList.remove('btn-secondary', 'btn-warning');
            chooseItemsBtn.classList.add('btn-info');
            chooseItemsBtn.style.opacity = '1';
            chooseItemsBtn.title = 'Click to choose items';
        } else {
            // Not both selected - show as warning (but keep clickable for validation message)
            chooseItemsBtn.classList.remove('btn-info');
            chooseItemsBtn.classList.add('btn-warning');
            chooseItemsBtn.style.opacity = '0.7';
            chooseItemsBtn.title = 'Please select Customer and Sales Man first';
        }
    }
}

// Open Choose Items Modal
function openChooseItemsModal() {
    // Validate: Customer must be selected
    const customerId = document.getElementById('customerSelect')?.value;
    if (!customerId) {
        showAlert('Please select Customer first!\n\nCustomer selection is required before choosing items.', 'warning', 'Customer Required');
        document.getElementById('customerSelect').focus();
        return;
    }
    
    // Validate: Salesman must be selected
    const salesmanId = document.getElementById('salesmanSelect')?.value;
    if (!salesmanId) {
        showAlert('Please select Sales Man first!\n\nSales Man selection is required before choosing items.', 'warning', 'Sales Man Required');
        document.getElementById('salesmanSelect').focus();
        return;
    }
    
    const modal = document.getElementById('chooseItemsModal');
    const backdrop = document.getElementById('chooseItemsBackdrop');
    
    // Reset pagination and search state
    itemsCurrentPage = 1;
    itemsHasMore = true;
    itemsLoading = false;
    itemsData = [];
    itemsSearchTerm = '';
    
    // Clear search input
    const searchInput = document.getElementById('itemSearchInput');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Show modal first
    setTimeout(() => {
        modal.classList.add('show');
        backdrop.classList.add('show');
        
        // Then load paginated items (with empty search)
        loadPaginatedItems(itemsCurrentPage, true, '');
    }, 10);
}

// Close Choose Items Modal
function closeChooseItemsModal() {
    const modal = document.getElementById('chooseItemsModal');
    const backdrop = document.getElementById('chooseItemsBackdrop');
    modal.classList.remove('show');
    backdrop.classList.remove('show');
}

// Display items in modal (legacy - for backward compatibility)
function displayItemsInModal() {
    displayItemsInModalPaginated(itemsData);
}

// Display items in modal with pagination support
function displayItemsInModalPaginated(items) {
    const tbody = document.getElementById('chooseItemsBody');
    tbody.innerHTML = '';
    
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Loading items...</td></tr>';
        return;
    }
    
    items.forEach(item => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        
        // Get company name (assuming it's in item data, adjust as needed)
        const company = item.company_name || item.company || 'N/A';
        const pack = item.packing || 'N/A';
        const qty = item.qty || item.available_qty || 0;
        
        row.innerHTML = `
            <td>${item.name || 'N/A'}</td>
            <td>${item.hsn_code || 'N/A'}</td>
            <td>${pack}</td>
            <td>${company}</td>
            <td>${qty}</td>
        `;
        
        row.addEventListener('click', function() {
            selectItemFromModal(item);
        });
        
        row.addEventListener('mouseenter', function() {
            if (!this.classList.contains('item-row-selected')) {
                this.style.backgroundColor = '#f8f9fa';
            }
        });
        
        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('item-row-selected')) {
                this.style.backgroundColor = '';
            }
        });
        
        tbody.appendChild(row);
    });
    
    // Add loading indicator after table if not present
    const tableContainer = document.querySelector('#chooseItemsModal .table-responsive');
    if (tableContainer && !document.getElementById('itemsLoadingIndicator')) {
        tableContainer.setAttribute('id', 'itemsScrollContainer');
        tableContainer.insertAdjacentHTML('beforeend', `
            <div id="itemsLoadingIndicator" style="display: none; text-align: center; padding: 15px; color: #6c757d;">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <span class="ms-2">Loading more items...</span>
            </div>
        `);
    }
    
    // Add records info to modal footer if not present
    const modalFooter = document.querySelector('#chooseItemsModal .pending-orders-footer');
    if (modalFooter && !document.getElementById('itemsRecordsInfo')) {
        modalFooter.insertAdjacentHTML('afterbegin', `<small class="text-muted me-auto" id="itemsRecordsInfo"></small>`);
    }
    
    updateItemsRecordsInfo();
}

// Append items to modal table (for infinite scroll)
function appendItemsToModalTable(items) {
    const tbody = document.getElementById('chooseItemsBody');
    if (!tbody) return;
    
    items.forEach(item => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        
        const company = item.company_name || item.company || 'N/A';
        const pack = item.packing || 'N/A';
        const qty = item.qty || item.available_qty || 0;
        
        row.innerHTML = `
            <td>${item.name || 'N/A'}</td>
            <td>${item.hsn_code || 'N/A'}</td>
            <td>${pack}</td>
            <td>${company}</td>
            <td>${qty}</td>
        `;
        
        row.addEventListener('click', function() {
            selectItemFromModal(item);
        });
        
        row.addEventListener('mouseenter', function() {
            if (!this.classList.contains('item-row-selected')) {
                this.style.backgroundColor = '#f8f9fa';
            }
        });
        
        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('item-row-selected')) {
                this.style.backgroundColor = '';
            }
        });
        
        tbody.appendChild(row);
    });
}

// Update items records info
function updateItemsRecordsInfo() {
    const infoEl = document.getElementById('itemsRecordsInfo');
    if (!infoEl) return;
    
    const loadedCount = itemsData.length;
    if (itemsHasMore) {
        infoEl.textContent = `Showing ${loadedCount} items (scroll for more)`;
    } else {
        infoEl.textContent = `Showing all ${loadedCount} items`;
    }
}

// Setup infinite scroll for items modal
function setupItemsInfiniteScroll() {
    const scrollContainer = document.getElementById('itemsScrollContainer');
    if (!scrollContainer) return;
    
    scrollContainer.addEventListener('scroll', function() {
        // Check if scrolled near bottom
        if (scrollContainer.scrollTop + scrollContainer.clientHeight >= scrollContainer.scrollHeight - 50) {
            // Load more items if available (with current search term)
            if (itemsHasMore && !itemsLoading) {
                loadPaginatedItems(itemsCurrentPage, false, itemsSearchTerm);
            }
        }
    });
}

// Filter items in modal - now uses server-side search
function filterItemsInModal() {
    const searchText = document.getElementById('itemSearchInput').value.trim();
    
    // Clear existing timeout
    if (itemsSearchTimeout) {
        clearTimeout(itemsSearchTimeout);
    }
    
    // Debounce search - wait 300ms after user stops typing
    itemsSearchTimeout = setTimeout(() => {
        itemsSearchTerm = searchText;
        
        // Reset pagination and reload with search term
        itemsCurrentPage = 1;
        itemsHasMore = true;
        itemsLoading = false;
        itemsData = [];
        
        loadPaginatedItems(itemsCurrentPage, true, searchText);
    }, 300);
}

// Select item from modal
function selectItemFromModal(item) {
    // Store item data for batch selection
    pendingItemSelection = item;
    
    // Close items modal
    closeChooseItemsModal();
    
    // Open batch selection modal
    openBatchSelectionModal(item);
}

// Open Batch Selection Modal
function openBatchSelectionModal(item) {
    const modal = document.getElementById('batchSelectionModal');
    const backdrop = document.getElementById('batchSelectionBackdrop');
    
    // Set item name in header
    document.getElementById('batchItemName').textContent = item.name || 'N/A';
    
    // Set initial Brand and Packing (will be updated when batch is selected)
    document.getElementById('batchBrand').textContent = item.name || '---';
    document.getElementById('batchPacking').textContent = item.packing || '---';
    
    // Clear batch details initially
    document.getElementById('batchSupplier').textContent = '---';
    document.getElementById('batchDetailsBody').innerHTML = '<tr><td colspan="10" class="text-center" style="padding: 8px;">Select a batch to view details</td></tr>';
    
    // Load batches for this item
    loadBatchesForItem(item.id);
    
    // Show modal
    setTimeout(() => {
        modal.classList.add('show');
        backdrop.classList.add('show');
    }, 10);
}

// Close Batch Selection Modal
function closeBatchSelectionModal() {
    const modal = document.getElementById('batchSelectionModal');
    const backdrop = document.getElementById('batchSelectionBackdrop');
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    pendingItemSelection = null;
}

// Load batches for item
function loadBatchesForItem(itemId) {
    console.log('🔄 Loading batches for item ID:', itemId);
    
    const url = `{{ url('/admin/api/item-batches') }}/${itemId}`;
    fetch(url)
        .then(response => {
            console.log('📡 Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ Batch data received:', data);
            
            // Handle different response formats
            let batches = [];
            if (Array.isArray(data)) {
                batches = data;
            } else if (data.success && Array.isArray(data.batches)) {
                batches = data.batches;
            } else if (data.batches && Array.isArray(data.batches)) {
                batches = data.batches;
            } else {
                console.warn('⚠️ Unexpected data format:', data);
            }
            
            console.log('📦 Processed batches:', batches);
            displayBatchesInModal(batches);
        })
        .catch(error => {
            console.error('❌ Error loading batches:', error);
            document.getElementById('batchSelectionBody').innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading batches</td></tr>';
        });
}

// Display batches in modal (Sale transaction format with correct columns)
function displayBatchesInModal(batches) {
    const tbody = document.getElementById('batchSelectionBody');
    tbody.innerHTML = '';
    
    if (!batches || !Array.isArray(batches) || batches.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No batches found for this item</td></tr>';
        return;
    }
    
    console.log(`✅ Displaying ${batches.length} batches in modal`);
    
    batches.forEach(batch => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        row.style.background = '#ffcccc'; // Pink background like in image
        row.setAttribute('data-batch', JSON.stringify(batch)); // Store batch data for details
        
        // Purchase date (from transaction) - prioritize display format
        let purchaseDate = 'N/A';
        if (batch.purchase_date_display && batch.purchase_date_display !== 'N/A') {
            purchaseDate = batch.purchase_date_display;
        } else if (batch.purchase_date) {
            // If we have raw date, format it
            try {
                const dateObj = new Date(batch.purchase_date);
                purchaseDate = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: '2-digit' }).replace(/\//g, '-');
            } catch (e) {
                purchaseDate = batch.purchase_date;
            }
        }
        
        console.log('Batch date:', { 
            batch_no: batch.batch_no, 
            purchase_date_display: batch.purchase_date_display, 
            purchase_date: batch.purchase_date,
            final_date: purchaseDate 
        });
        
        // Expiry date in MM/YY format
        let expiryDate = 'N/A';
        if (batch.expiry_display) {
            expiryDate = batch.expiry_display;
        } else if (batch.expiry_date) {
            try {
                const date = new Date(batch.expiry_date);
                expiryDate = date.toLocaleDateString('en-GB', { month: '2-digit', year: '2-digit' }).replace('/', '/');
            } catch (e) {
                expiryDate = batch.expiry_date;
            }
        }
        
        // Sale rate (s_rate) - if not available, use item's sale rate
        const saleRate = parseFloat(batch.avg_s_rate || batch.s_rate || 0).toFixed(2);
        
        // Purchase rate
        const purRate = parseFloat(batch.avg_pur_rate || batch.pur_rate || 0).toFixed(2);
        
        // MRP
        const mrp = parseFloat(batch.avg_mrp || batch.mrp || 0).toFixed(2);
        
        // Quantity
        const qty = batch.total_qty || batch.qty || 0;
        
        row.innerHTML = `
            <td style="text-align: left; padding: 5px;">${batch.batch_no || 'N/A'}</td>
            <td style="text-align: center; padding: 5px;">${purchaseDate}</td>
            <td style="text-align: right; padding: 5px;">${saleRate}</td>
            <td style="text-align: right; padding: 5px;">${purRate}</td>
            <td style="text-align: right; padding: 5px;">${mrp}</td>
            <td style="text-align: right; padding: 5px;">${qty}</td>
            <td style="text-align: center; padding: 5px;">${expiryDate}</td>
            <td style="text-align: center; padding: 5px;">---</td>
        `;
        
        row.addEventListener('click', function() {
            // Highlight selected row
            document.querySelectorAll('#batchSelectionBody tr').forEach(r => {
                r.classList.remove('item-row-selected');
            });
            this.classList.add('item-row-selected');
            
            // Populate batch details section
            populateBatchDetails(batch);
            
            // Store selected batch for when user confirms selection
            window.selectedBatch = batch;
        });
        
        // Double-click to select and add to table
        row.addEventListener('dblclick', function() {
            if (pendingItemSelection && window.selectedBatch) {
                selectBatchFromModal(window.selectedBatch);
            }
        });
        
        row.addEventListener('mouseenter', function() {
            if (!this.classList.contains('item-row-selected')) {
                this.style.backgroundColor = '#ffb3b3'; // Darker pink on hover
            }
        });
        
        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('item-row-selected')) {
                this.style.backgroundColor = '#ffcccc'; // Back to original pink
            }
        });
        
        tbody.appendChild(row);
    });
}

// Populate batch details section when batch is selected
function populateBatchDetails(batch) {
    // Update Brand and Packing
    document.getElementById('batchBrand').textContent = batch.item_name || '---';
    document.getElementById('batchPacking').textContent = batch.packing || '---';
    
    // Update Supplier
    document.getElementById('batchSupplier').textContent = batch.supplier_name || '---';
    
    // Populate details table
    const detailsBody = document.getElementById('batchDetailsBody');
    detailsBody.innerHTML = '';
    
    const row = document.createElement('tr');
    row.style.background = '#ffcccc';
    
    // Purchase date - prioritize display format
    let purchaseDate = 'N/A';
    if (batch.purchase_date_display && batch.purchase_date_display !== 'N/A') {
        purchaseDate = batch.purchase_date_display;
    } else if (batch.purchase_date) {
        // If we have raw date, format it
        try {
            const dateObj = new Date(batch.purchase_date);
            purchaseDate = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: '2-digit' }).replace(/\//g, '-');
        } catch (e) {
            purchaseDate = batch.purchase_date;
        }
    }
    
    // Expiry date
    const expiryDate = batch.expiry_display || 'N/A';
    
    // Sale rate
    const saleRate = parseFloat(batch.avg_s_rate || batch.s_rate || 0).toFixed(2);
    
    // Purchase rate
    const purRate = parseFloat(batch.avg_pur_rate || batch.pur_rate || 0).toFixed(2);
    
    // MRP
    const mrp = parseFloat(batch.avg_mrp || batch.mrp || 0).toFixed(2);
    
    // Quantity
    const qty = batch.total_qty || batch.qty || 0;
    
    // Cost+GST
    const costGst = parseFloat(batch.avg_cost_gst || batch.cost_gst || 0).toFixed(2);
    
    row.innerHTML = `
        <td style="padding: 5px;">${batch.batch_no || 'N/A'}</td>
        <td style="padding: 5px; text-align: center;">${purchaseDate}</td>
        <td style="padding: 5px; text-align: right;">${saleRate}</td>
        <td style="padding: 5px; text-align: right;">${purRate}</td>
        <td style="padding: 5px; text-align: right;">${mrp}</td>
        <td style="padding: 5px; text-align: right;">${qty}</td>
        <td style="padding: 5px; text-align: center;">${expiryDate}</td>
        <td style="padding: 5px; text-align: center;">---</td>
        <td style="padding: 5px; text-align: right;">${costGst}</td>
        <td style="padding: 5px; text-align: center;"></td>
    `;
    
    detailsBody.appendChild(row);
}

// Filter batches in modal
function filterBatchesInModal() {
    const searchText = document.getElementById('batchSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#batchSelectionBody tr');
    
    rows.forEach(row => {
        const batchNo = (row.cells[0]?.textContent || '').toLowerCase();
        if (batchNo.includes(searchText)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Select batch from modal (called when batch row is double-clicked or confirm button is clicked)
function selectBatchFromModal(batch) {
    if (!pendingItemSelection) return;
    
    // Use the batch from window.selectedBatch if available, otherwise use passed batch
    const selectedBatch = window.selectedBatch || batch;
    
    if (!selectedBatch) {
        showAlert('Please select a batch first', 'warning', 'Batch Required');
        return;
    }
    
    console.log('✅ Selected batch:', selectedBatch);
    console.log('📦 Batch ID:', selectedBatch.id);
    
    // Store item and batch before closing modal
    const itemToAdd = pendingItemSelection;
    const batchToAdd = selectedBatch;
    
    // Close batch modal FIRST
    closeBatchSelectionModal();
    
    // Clear selected batch
    window.selectedBatch = null;
    
    // Add item to table AFTER modal is fully closed
    setTimeout(() => {
        console.log('📦 Adding item to table after modal close');
        addItemToTable(itemToAdd, batchToAdd);
    }, 150);
}

// Find first empty row that can be reused
function findFirstEmptyRow() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    
    for (let row of rows) {
        const codeInput = row.querySelector('input[name*="[code]"]');
        const nameInput = row.querySelector('input[name*="[item_name]"]');
        const qtyInput = row.querySelector('input[name*="[qty]"]');
        const rateInput = row.querySelector('input[name*="[rate]"]');
        
        // Check if row is truly empty (no code, no name, no qty, no rate or rate is 0)
        const code = codeInput?.value?.trim() || '';
        const name = nameInput?.value?.trim() || '';
        const qty = parseFloat(qtyInput?.value) || 0;
        const rate = parseFloat(rateInput?.value) || 0;
        
        if (!code && !name && qty === 0 && rate === 0) {
            return row;
        }
    }
    
    return null; // No empty row found
}

// Populate existing row with item data
function populateExistingRow(row, item, batch) {
    const rowIndex = parseInt(row.getAttribute('data-row-index'));
    
    // Use batch's sale rate if available, otherwise use item's sale rate
    const rate = parseFloat(batch.avg_s_rate || batch.s_rate || item.s_rate || 0);
    
    // Format expiry date for display
    let expiryDisplay = '';
    if (batch.expiry_display) {
        expiryDisplay = batch.expiry_display;
    } else if (batch.expiry_date) {
        try {
            const date = new Date(batch.expiry_date);
            expiryDisplay = date.toLocaleDateString('en-GB', { month: '2-digit', year: '2-digit' }).replace('/', '/');
        } catch (e) {
            expiryDisplay = batch.expiry_date;
        }
    }
    
    // Populate the input fields
    const codeInput = row.querySelector('input[name*="[code]"]');
    const nameInput = row.querySelector('input[name*="[item_name]"]');
    const batchInput = row.querySelector('input[name*="[batch]"]');
    const expiryInput = row.querySelector('input[name*="[expiry]"]');
    const rateInput = row.querySelector('input[name*="[rate]"]');
    const mrpInput = row.querySelector('input[name*="[mrp]"]');
    
    if (codeInput) codeInput.value = item.bar_code || '';
    if (nameInput) nameInput.value = item.name || '';
    if (batchInput) batchInput.value = batch.batch_no || '';
    if (expiryInput) expiryInput.value = expiryDisplay;
    if (rateInput) rateInput.value = rate.toFixed(2);
    if (mrpInput) mrpInput.value = parseFloat(batch.avg_mrp || batch.mrp || item.mrp || 0).toFixed(2);
    
    // Store item data in row attributes
    row.setAttribute('data-item-id', item.id);
    row.setAttribute('data-hsn-code', item.hsn_code || '');
    row.setAttribute('data-cgst', item.cgst_percent || 0);
    row.setAttribute('data-sgst', item.sgst_percent || 0);
    row.setAttribute('data-cess', item.cess_percent || 0);
    row.setAttribute('data-packing', item.packing || '');
    row.setAttribute('data-unit', item.unit || '');
    row.setAttribute('data-company', item.company_name || item.company || '');
    row.setAttribute('data-batch-code', batch.batch_no || '');
    row.setAttribute('data-case-qty', item.case_qty || 0);
    row.setAttribute('data-box-qty', item.box_qty || 0);
    
    // Store batch purchase details
    row.setAttribute('data-batch-purchase-rate', batch.avg_pur_rate || batch.pur_rate || 0);
    row.setAttribute('data-batch-cost-gst', batch.avg_cost_gst || batch.cost_gst || 0);
    row.setAttribute('data-batch-supplier', batch.supplier_name || '');
    row.setAttribute('data-batch-purchase-date', batch.purchase_date_display || batch.purchase_date || '');
    
    // 🔥 IMPORTANT: Store batch ID for quantity reduction (must be number)
    const batchId = batch.id ? parseInt(batch.id) : '';
    if (batchId) {
        row.setAttribute('data-batch-id', batchId.toString());
        console.log('✅ Batch ID stored in existing row:', batchId);
    } else {
        console.warn('⚠️ No batch ID found in batch object:', batch);
    }
    
    return rowIndex;
}

// Add item to table (FIXED VERSION - reuses empty rows)
function addItemToTable(item, batch) {
    console.log('🔄 Adding item to table:', item.name);
    
    // First, try to find an existing empty row
    const existingEmptyRow = findFirstEmptyRow();
    
    let targetRow;
    let targetRowIndex;
    
    if (existingEmptyRow) {
        // Reuse existing empty row
        console.log('✅ Found empty row, reusing it');
        targetRowIndex = populateExistingRow(existingEmptyRow, item, batch);
        targetRow = existingEmptyRow;
        
        // Re-add event listeners for the populated row
        addRowEventListeners(targetRow, targetRowIndex);
    } else {
        // No empty row found, create a new one
        console.log('➕ No empty row found, creating new row');
        itemIndex++;
        const tbody = document.getElementById('itemsTableBody');
        
        targetRow = document.createElement('tr');
        targetRow.setAttribute('data-row-index', itemIndex);
        targetRow.setAttribute('data-item-id', item.id);
        targetRow.style.cursor = 'pointer';
        targetRow.addEventListener('click', function(e) {
            const clickedRow = e.currentTarget;
            const rowIdx = parseInt(clickedRow.getAttribute('data-row-index'));
            selectRow(rowIdx);
        });
        
        // Use batch's sale rate if available, otherwise use item's sale rate
        const rate = parseFloat(batch.avg_s_rate || batch.s_rate || item.s_rate || 0);
        
        // Format expiry date for display
        let expiryDisplay = '';
        if (batch.expiry_display) {
            expiryDisplay = batch.expiry_display;
        } else if (batch.expiry_date) {
            try {
                const date = new Date(batch.expiry_date);
                expiryDisplay = date.toLocaleDateString('en-GB', { month: '2-digit', year: '2-digit' }).replace('/', '/');
            } catch (e) {
                expiryDisplay = batch.expiry_date;
            }
        }
        
        targetRow.innerHTML = `
            <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][code]" value="${item.bar_code || ''}" style="font-size: 10px; background: #f8f9fa;" autocomplete="off" readonly></td>
            <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][item_name]" value="${item.name || ''}" style="font-size: 10px; background: #f8f9fa;" autocomplete="off" readonly></td>
            <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][batch]" value="${batch.batch_no || ''}" style="font-size: 10px; background: #f8f9fa;" autocomplete="off" readonly></td>
            <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][expiry]" value="${expiryDisplay}" style="font-size: 10px; background: #f8f9fa;" autocomplete="off" readonly></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-qty" name="items[${itemIndex}][qty]" id="qty_${itemIndex}" value="" placeholder="0" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][free_qty]" id="free_qty_${itemIndex}" value="0" style="font-size: 10px;"></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-rate" name="items[${itemIndex}][rate]" id="rate_${itemIndex}" value="${rate.toFixed(2)}" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-discount" name="items[${itemIndex}][discount]" id="discount_${itemIndex}" value="" placeholder="0" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][mrp]" id="mrp_${itemIndex}" value="${parseFloat(batch.avg_mrp || batch.mrp || item.mrp || 0).toFixed(2)}" step="0.01" style="font-size: 10px;" readonly></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][amount]" id="amount_${itemIndex}" value="0.00" style="font-size: 10px;" readonly></td>
            <td class="p-0 text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${itemIndex})" title="Delete Row" style="font-size: 9px; padding: 2px 5px;">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        
        // Store item data in row attributes
        targetRow.setAttribute('data-hsn-code', item.hsn_code || '');
        targetRow.setAttribute('data-cgst', item.cgst_percent || 0);
        targetRow.setAttribute('data-sgst', item.sgst_percent || 0);
        targetRow.setAttribute('data-cess', item.cess_percent || 0);
        targetRow.setAttribute('data-packing', item.packing || '');
        targetRow.setAttribute('data-unit', item.unit || '');
        targetRow.setAttribute('data-company', item.company_name || item.company || '');
        targetRow.setAttribute('data-batch-code', batch.batch_no || '');
        targetRow.setAttribute('data-case-qty', item.case_qty || 0);
        targetRow.setAttribute('data-box-qty', item.box_qty || 0);
        
        // Store batch purchase details
        targetRow.setAttribute('data-batch-purchase-rate', batch.avg_pur_rate || batch.pur_rate || 0);
        targetRow.setAttribute('data-batch-cost-gst', batch.avg_cost_gst || batch.cost_gst || 0);
        targetRow.setAttribute('data-batch-supplier', batch.supplier_name || '');
        targetRow.setAttribute('data-batch-purchase-date', batch.purchase_date_display || batch.purchase_date || '');
        
        // 🔥 IMPORTANT: Store batch ID for quantity reduction (must be number)
        const batchId = batch.id ? parseInt(batch.id) : '';
        if (batchId) {
            targetRow.setAttribute('data-batch-id', batchId.toString());
            console.log('✅ Batch ID stored in new row:', batchId);
        } else {
            console.warn('⚠️ No batch ID found in batch object:', batch);
        }
        
        // Mark row as incomplete initially
        targetRow.setAttribute('data-complete', 'false');
        targetRow.classList.add('table-danger'); // Red background for incomplete
        
        tbody.appendChild(targetRow);
        
        // Add event listeners for editing
        addRowEventListeners(targetRow, itemIndex);
        
        targetRowIndex = itemIndex;
    }
    
    // Update row color
    updateRowColor(targetRowIndex);
    
    // Select the row (this will populate detailed summary immediately)
    selectRow(targetRowIndex);
    
    // Update detailed summary immediately since item is populated
    updateDetailedSummary(targetRowIndex);
    
    // Scroll row into view
    targetRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Focus on Qty field after modal is fully closed and event listeners are attached
    setTimeout(() => {
        // Get fresh reference to qty field (after cloning in addRowEventListeners)
        const qtyField = targetRow.querySelector('input[name*="[qty]"]');
        if (qtyField) {
            qtyField.focus();
            qtyField.select();
            console.log('✅ Cursor moved to Qty field for row', targetRowIndex);
        } else {
            console.error('❌ Qty field not found for row', targetRowIndex);
        }
    }, 200);
    
    // Calculate totals
    calculateTotal();
}

// Add event listeners to row for editing functionality
function addRowEventListeners(row, rowIndex) {
    console.log('🔧 Adding event listeners for row', rowIndex);
    
    // Use event delegation on the row itself for better reliability
    row.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' && e.key !== 'ArrowUp' && e.key !== 'ArrowDown') return;
        
        const target = e.target;
        const fieldName = target.name || '';
        
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            
            // Determine which field and move to next
            if (fieldName.includes('[qty]')) {
                console.log('⏎ Enter on Qty → F.Qty');
                calculateRowAmount(rowIndex);
                const freeQty = row.querySelector('input[name*="[free_qty]"]');
                if (freeQty) { freeQty.focus(); freeQty.select(); }
            }
            else if (fieldName.includes('[free_qty]')) {
                console.log('⏎ Enter on F.Qty → Rate');
                const rate = row.querySelector('input[name*="[rate]"]');
                if (rate) { rate.focus(); rate.select(); }
            }
            else if (fieldName.includes('[rate]')) {
                console.log('⏎ Enter on Rate → Dis%');
                calculateRowAmount(rowIndex);
                const discount = row.querySelector('input[name*="[discount]"]');
                if (discount) { discount.focus(); discount.select(); }
            }
            else if (fieldName.includes('[discount]')) {
                console.log('⏎ Enter on Dis% → Row Complete (GREEN)');
                calculateRowAmount(rowIndex);
                
                // Mark row as complete (GREEN)
                markRowComplete(rowIndex);
                
                // Remove focus
                target.blur();
                
                // Clear row selection
                currentSelectedRowIndex = null;
                
                // Clear sections for next item
                clearCalculationSection();
                clearDetailedSummary();
                
                // Recalculate summary
                calculateSummary();
                
                console.log('✅ Row', rowIndex, 'completed');
            }
        }
        else if (e.key === 'ArrowUp') {
            e.preventDefault();
            navigateToRow(rowIndex - 1);
        }
        else if (e.key === 'ArrowDown') {
            e.preventDefault();
            navigateToRow(rowIndex + 1);
        }
    });
    
    // Listen for code changes to fetch item details
    const codeInput = row.querySelector('input[name*="[code]"]');
    if (codeInput) {
        codeInput.addEventListener('blur', function() {
            const itemCode = this.value.trim();
            if (itemCode) {
                fetchItemDetailsForRow(itemCode, rowIndex);
            } else {
                clearDetailedSummary();
            }
        });
        
        codeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const itemCode = this.value.trim();
                if (itemCode) {
                    fetchItemDetailsForRow(itemCode, rowIndex);
                }
                // Move to next field
                const nextInput = row.querySelector('input[name*="[item_name]"]');
                if (nextInput) nextInput.focus();
            }
        });
    }
    
    // Listen for item name changes
    const nameInput = row.querySelector('input[name*="[item_name]"]');
    if (nameInput) {
        nameInput.addEventListener('blur', function() {
            updateDetailedSummary(rowIndex);
        });
    }
    
    // Listen for batch changes
    const batchInput = row.querySelector('input[name*="[batch]"]');
    if (batchInput) {
        batchInput.addEventListener('blur', function() {
            updateDetailedSummary(rowIndex);
        });
    }
    
    // Listen for expiry changes
    const expiryInput = row.querySelector('input[name*="[expiry]"]');
    if (expiryInput) {
        expiryInput.addEventListener('blur', function() {
            updateDetailedSummary(rowIndex);
        });
    }
}

// Navigate to specific row
function navigateToRow(targetRowIndex) {
    // Check if row exists by data-row-index
    const targetRow = document.querySelector(`tr[data-row-index="${targetRowIndex}"]`);
    if (!targetRow) {
        return; // Row doesn't exist
    }
    
    // Select the row
    selectRow(targetRowIndex);
    
    // Focus on qty field
    const qtyField = document.getElementById(`qty_${targetRowIndex}`);
    if (qtyField) {
        qtyField.focus();
        // Don't select - let user continue typing
    }
}

// Move to next row (create new if needed)
function moveToNextRow(currentRowIndex) {
    // Mark current row as complete (will turn green if complete)
    updateRowColor(currentRowIndex);
    
    // Recalculate summary section (now that row might be complete)
    calculateSummary();
    
    // Find next row by checking all rows and their data-row-index
    const allRows = document.querySelectorAll('#itemsTableBody tr');
    let nextRowIndex = null;
    let currentRowFound = false;
    
    allRows.forEach(row => {
        const rowIdx = parseInt(row.getAttribute('data-row-index'));
        if (currentRowFound && nextRowIndex === null) {
            nextRowIndex = rowIdx;
        }
        if (rowIdx === currentRowIndex) {
            currentRowFound = true;
        }
    });
    
    // Check if next row exists
    if (nextRowIndex !== null) {
        // Next row exists, navigate to it
        navigateToRow(nextRowIndex);
    } else {
        // No next row, create a new one
        addNewRow();
        // Navigate to the newly created row
        setTimeout(() => {
            navigateToRow(itemIndex);
        }, 100);
    }
}

// Fetch item details when code is entered/changed
function fetchItemDetailsForRow(itemCode, rowIndex) {
    const url = `{{ url('/admin/items/get-by-code') }}/${itemCode}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {
                // Update row with item data
                const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
                if (row) {
                    // Update item name if empty
                    const nameInput = row.querySelector('input[name*="[item_name]"]');
                    if (nameInput && !nameInput.value.trim()) {
                        nameInput.value = data.item.name || '';
                    }
                    
                    // Update MRP if empty
                    const mrpInput = row.querySelector('input[name*="[mrp]"]');
                    if (mrpInput) {
                        mrpInput.value = parseFloat(data.item.mrp || 0).toFixed(2);
                    }
                    
                    // Update row attributes
                    row.setAttribute('data-hsn-code', data.item.hsn_code || '');
                    row.setAttribute('data-cgst', data.item.cgst_percent || 0);
                    row.setAttribute('data-sgst', data.item.sgst_percent || 0);
                    row.setAttribute('data-cess', data.item.cess_percent || 0);
                    row.setAttribute('data-packing', data.item.packing || '');
                    row.setAttribute('data-unit', data.item.unit || '1');
                    row.setAttribute('data-company', data.item.company_name || data.item.company || '');
                    row.setAttribute('data-case-qty', data.item.case_qty || 0);
                    row.setAttribute('data-box-qty', data.item.box_qty || 0);
                    
                    // Update sale rate if empty
                    const rateInput = row.querySelector('input[name*="[rate]"]');
                    if (rateInput && (!rateInput.value || parseFloat(rateInput.value) === 0)) {
                        rateInput.value = parseFloat(data.item.s_rate || 0).toFixed(2);
                        calculateRowAmount(rowIndex);
                    }
                }
                
                // Update detailed summary
                updateDetailedSummary(rowIndex);
                
                // Update calculation section
                updateCalculationSection(rowIndex);
            } else {
                console.log('Item not found:', itemCode);
            }
        })
        .catch(error => {
            console.error('Error fetching item:', error);
        });
}


// Select row - shows row data in calculation & additional details section
function selectRow(rowIndex) {
    // Find the actual row by data-row-index attribute
    const targetRow = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!targetRow) {
        return; // Row doesn't exist
    }
    
    console.log('🔵 Selecting row', rowIndex);
    
    // Remove previous selection from ALL rows
    const allRows = document.querySelectorAll('#itemsTableBody tr');
    allRows.forEach(r => {
        r.classList.remove('row-selected');
    });
    
    // Add selection to target row (GREEN border)
    targetRow.classList.add('row-selected');
    currentSelectedRowIndex = rowIndex;
    
    // Scroll row into view if needed
    targetRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Update calculation section (HSN, GST, Case, Box) - shows THIS row's data
    updateCalculationSection(rowIndex);
    
    // Update detailed summary section - shows THIS row's data
    updateDetailedSummary(rowIndex);
}

// Update calculation section with SELECTED ROW data (row-based display)
function updateCalculationSection(rowIndex) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) {
        clearCalculationSection();
        return;
    }
    
    console.log('🔢 Updating calculation section for row', rowIndex);
    
    // Get item data from row attributes
    const hsnCode = row.getAttribute('data-hsn-code') || '---';
    const cgst = parseFloat(row.getAttribute('data-cgst') || 0);
    const sgst = parseFloat(row.getAttribute('data-sgst') || 0);
    const cess = parseFloat(row.getAttribute('data-cess') || 0);
    const caseQty = parseFloat(row.getAttribute('data-case-qty') || 0);
    const boxQty = parseFloat(row.getAttribute('data-box-qty') || 0);
    
    // Get current values from row inputs (using querySelector for fresh reference)
    const qtyInput = row.querySelector('input[name*="[qty]"]');
    const rateInput = row.querySelector('input[name*="[rate]"]');
    const discountInput = row.querySelector('input[name*="[discount]"]');
    
    const qty = parseFloat(qtyInput?.value) || 0;
    const rate = parseFloat(rateInput?.value) || 0;
    const discount = parseFloat(discountInput?.value) || 0;
    
    // Calculate total amount (before discount)
    const totalAmount = qty * rate;
    
    // Calculate discount amount
    const discountAmount = totalAmount * (discount / 100);
    
    // Calculate discounted amount (amount after discount)
    const discountedAmount = totalAmount - discountAmount;
    
    // Calculate Case and Box
    const cases = caseQty > 0 ? Math.floor(qty / caseQty) : 0;
    const boxes = boxQty > 0 ? Math.floor((qty % caseQty) / boxQty) : 0;
    
    // Helper functions for null-safe updates
    const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val; };
    const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    
    // Update Case and Box
    setVal('calc_case', cases);
    setVal('calc_box', boxes);
    
    // Update HSN Code
    setVal('calc_hsn_code', hsnCode);
    
    // Update GST percentages
    setVal('calc_cgst', cgst.toFixed(2));
    setVal('calc_sgst', sgst.toFixed(2));
    setVal('calc_cess', cess.toFixed(2));
    
    // Calculate total tax percentage
    const totalTaxPercent = cgst + sgst + cess;
    setVal('calc_tax_percent', totalTaxPercent.toFixed(3));
    
    // Calculate GST amounts on DISCOUNTED AMOUNT
    if (discountedAmount > 0) {
        const cgstAmount = (discountedAmount * cgst / 100).toFixed(2);
        const sgstAmount = (discountedAmount * sgst / 100).toFixed(2);
        const cessAmount = (discountedAmount * cess / 100).toFixed(2);
        
        setText('calc_cgst_amount', cgstAmount);
        setText('calc_sgst_amount', sgstAmount);
        setText('calc_cess_amount', cessAmount);
    } else {
        setText('calc_cgst_amount', '0.00');
        setText('calc_sgst_amount', '0.00');
        setText('calc_cess_amount', '0.00');
    }
}

// Clear calculation section
function clearCalculationSection() {
    const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val; };
    const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    
    setVal('calc_case', '0');
    setVal('calc_box', '0');
    setVal('calc_hsn_code', '---');
    setVal('calc_cgst', '0');
    setVal('calc_sgst', '0');
    setVal('calc_cess', '0');
    setVal('calc_tax_percent', '0.000');
    setText('calc_cgst_amount', '0.00');
    setText('calc_sgst_amount', '0.00');
    setText('calc_cess_amount', '0.00');
}

// Update detailed summary section (shows when item is populated)
function updateDetailedSummary(rowIndex) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) {
        clearDetailedSummary();
        return;
    }
    
    // Get item code to check if item is populated
    const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim() || '';
    const itemName = row.querySelector('input[name*="[item_name]"]')?.value?.trim() || '';
    
    // Only show details if item is populated (has code or name)
    if (!itemCode && !itemName) {
        clearDetailedSummary();
        return;
    }
    
    // Get data from row attributes
    const packing = row.getAttribute('data-packing') || '';
    const unit = row.getAttribute('data-unit') || '1';
    const company = row.getAttribute('data-company') || '';
    const cgst = row.getAttribute('data-cgst') || 0;
    const sgst = row.getAttribute('data-sgst') || 0;
    const cess = row.getAttribute('data-cess') || 0;
    
    // Get batch purchase details
    const batchPurchaseRate = parseFloat(row.getAttribute('data-batch-purchase-rate') || 0);
    
    // Calculate Cost + GST using formula: P.RATE × (1 + TotalGST%)
    const totalGstPercent = parseFloat(cgst) + parseFloat(sgst) + parseFloat(cess);
    const costPlusGst = batchPurchaseRate * (1 + (totalGstPercent / 100));
    
    // Get values from inputs (use row querySelector for fresh reference)
    const qtyInput = row.querySelector('input[name*="[qty]"]');
    const rateInput = row.querySelector('input[name*="[rate]"]');
    const discountInput = row.querySelector('input[name*="[discount]"]');
    
    const qty = parseFloat(qtyInput?.value) || 0;
    const rate = parseFloat(rateInput?.value) || 0;
    const discount = parseFloat(discountInput?.value) || 0;
    
    // Helper function to safely set value
    const setFieldValue = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.value = value;
    };
    
    // ALWAYS show basic fields
    setFieldValue('detailPacking', packing);
    setFieldValue('detailUnit', unit);
    setFieldValue('detailCompany', company);
    setFieldValue('detailCostGst', costPlusGst.toFixed(2));
    
    // Get batch code from row
    const batchInput = row.querySelector('input[name*="[batch]"]');
    const batchCodeValue = batchInput ? batchInput.value : '';
    setFieldValue('detailBatchCode', batchCodeValue);
    
    // Fetch total quantity from all batches for this item
    const itemId = row.getAttribute('data-item-id');
    if (itemId) {
        fetchTotalBatchQuantity(itemId);
    } else {
        setFieldValue('detailClQty', qty || '');
    }
    
    // Calculate amounts properly
    const ntAmt = qty * rate;
    const discountAmt = ntAmt * (discount / 100);
    const subTot = ntAmt - discountAmt;
    const taxAmt = subTot * ((parseFloat(cgst) + parseFloat(sgst) + parseFloat(cess)) / 100);
    const netAmt = subTot + taxAmt;
    
    // Update detailed summary fields with null checks
    setFieldValue('detailNtAmt', ntAmt.toFixed(2));
    setFieldValue('detailDisAmt', discountAmt.toFixed(2));
    setFieldValue('detailSubTot', subTot.toFixed(2));
    setFieldValue('detailTaxAmt', taxAmt.toFixed(2));
    setFieldValue('detailNetAmt', netAmt.toFixed(2));
    setFieldValue('detailScAmt', '0.00');
    setFieldValue('detailScmPercent', '0.00');
    setFieldValue('detailScmAmt', '0.00');
    setFieldValue('detailHsAmt', '0.00');
    setFieldValue('detailLctn', '');
    setFieldValue('detailVol', '0');
    setFieldValue('detailSrIno', '');
    setFieldValue('detailScm1', '0');
    setFieldValue('detailScm2', '0');
}

// Fetch total quantity from all batches for an item
function fetchTotalBatchQuantity(itemId) {
    const url = `{{ url('/admin/api/item-batches') }}/${itemId}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Handle different response formats
            let batches = [];
            if (Array.isArray(data)) {
                batches = data;
            } else if (data.success && Array.isArray(data.batches)) {
                batches = data.batches;
            } else if (data.batches && Array.isArray(data.batches)) {
                batches = data.batches;
            }
            
            // Sum up total quantity from all batches
            let totalQty = 0;
            batches.forEach(batch => {
                const batchQty = parseFloat(batch.total_qty || batch.qty || 0);
                totalQty += batchQty;
            });
            
            // Update CL QTY field with total from all batches
            document.getElementById('detailClQty').value = totalQty > 0 ? totalQty : '';
        })
        .catch(error => {
            console.error('Error fetching batch quantities:', error);
            // On error, show empty or current qty
            document.getElementById('detailClQty').value = '';
        });
}

// Clear detailed summary
function clearDetailedSummary() {
    const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val; };
    
    setVal('detailPacking', '');
    setVal('detailUnit', '1');
    setVal('detailCompany', '');
    setVal('detailClQty', '');
    setVal('detailLctn', '');
    setVal('detailBatchCode', '');
    setVal('detailNtAmt', '0.00');
    setVal('detailScAmt', '0.00');
    setVal('detailDisAmt', '0.00');
    setVal('detailHsAmt', '0.00');
    setVal('detailScmPercent', '0.00');
    setVal('detailScmAmt', '0.00');
    setVal('detailSubTot', '0.00');
    setVal('detailTaxAmt', '0.00');
    setVal('detailNetAmt', '0.00');
    setVal('detailCostGst', '0.00');
    setVal('detailVol', '0');
    setVal('detailSrIno', '');
    setVal('detailScm1', '0');
    setVal('detailScm2', '0');
}

// Calculate row amount
function calculateRowAmount(rowIndex) {
    // Get row and use querySelector for fresh references
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) return;
    
    const qtyInput = row.querySelector('input[name*="[qty]"]');
    const rateInput = row.querySelector('input[name*="[rate]"]');
    const amountInput = row.querySelector('input[name*="[amount]"]');
    
    const qty = parseFloat(qtyInput?.value) || 0;
    const rate = parseFloat(rateInput?.value) || 0;
    
    // Amount = Qty × Rate ONLY (discount NOT applied here)
    const amount = qty * rate;
    
    if (amountInput) amountInput.value = amount.toFixed(2);
    
    // Update row color
    updateRowColor(rowIndex);
    
    // Always update summary (calculates all totals)
    calculateSummary();
    
    // If this is the currently selected row, update calculation & detailed summary
    if (currentSelectedRowIndex === rowIndex) {
        updateCalculationSection(rowIndex);
        updateDetailedSummary(rowIndex);
    }
}

// Check if row is complete
function isRowComplete(rowIndex) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) return false;
    
    // Check if row is marked as finalized (Enter pressed on Dis%)
    return row.getAttribute('data-finalized') === 'true';
}

// Mark row as complete (called when Enter pressed on Dis%)
function markRowComplete(rowIndex) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) return;
    
    // Mark as finalized
    row.setAttribute('data-finalized', 'true');
    row.setAttribute('data-complete', 'true');
    
    // Remove old color classes and add GREEN
    row.classList.remove('table-danger', 'row-selected');
    row.classList.add('table-success');
    
    console.log('✅ Row', rowIndex, 'marked as complete (GREEN)');
}

// Update row color based on state
function updateRowColor(rowIndex) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) return;
    
    // If row is already finalized (GREEN), don't change it
    if (row.getAttribute('data-finalized') === 'true') {
        return;
    }
    
    // Remove old color classes (but keep row-selected if present)
    row.classList.remove('table-danger', 'table-success');
    
    // Check if row has item data
    const code = row.querySelector('input[name*="[code]"]')?.value?.trim();
    const itemName = row.querySelector('input[name*="[item_name]"]')?.value?.trim();
    
    if (code || itemName) {
        // Has item but not finalized - RED (incomplete)
        row.setAttribute('data-complete', 'false');
        row.classList.add('table-danger');
    }
}

// Calculate total - redirects to calculateSummary
function calculateTotal() {
    // Total is now calculated in calculateSummary() and shown in summary section
    calculateSummary();
}

// Calculate summary - LOOPS through ALL rows and calculates totals
function calculateSummary() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    let totalNtAmt = 0;      // N.T.Amt - Total amount before discount
    let totalDisAmt = 0;     // Dis - Total discount amount
    let totalFTAmt = 0;      // F.T.Amt - Total after discount (before tax)
    let totalTax = 0;        // Tax - Total CGST + SGST + CESS
    let totalNet = 0;        // Net - Final amount
    
    console.log('📊 Calculating summary for', rows.length, 'rows');
    
    rows.forEach(row => {
        // Get item data using querySelector (works with cloned elements)
        const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
        const itemName = row.querySelector('input[name*="[item_name]"]')?.value?.trim();
        
        // Only process rows that have item
        if (itemCode || itemName) {
            // Use querySelector on row for fresh references
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            const rateInput = row.querySelector('input[name*="[rate]"]');
            const discountInput = row.querySelector('input[name*="[discount]"]');
            
            const qty = parseFloat(qtyInput?.value) || 0;
            const rate = parseFloat(rateInput?.value) || 0;
            const discount = parseFloat(discountInput?.value) || 0;
            
            // Calculate amounts for this row
            const rowAmount = qty * rate;  // Amount before discount
            const rowDiscount = rowAmount * (discount / 100);  // Discount amount
            const rowAfterDiscount = rowAmount - rowDiscount;  // Amount after discount
            
            // Get GST percentages from row attributes
            const cgst = parseFloat(row.getAttribute('data-cgst')) || 0;
            const sgst = parseFloat(row.getAttribute('data-sgst')) || 0;
            const cess = parseFloat(row.getAttribute('data-cess')) || 0;
            
            // Calculate tax on DISCOUNTED amount
            const rowTax = rowAfterDiscount * ((cgst + sgst + cess) / 100);
            
            // Add to totals
            totalNtAmt += rowAmount;
            totalDisAmt += rowDiscount;
            totalFTAmt += rowAfterDiscount;
            totalTax += rowTax;
            
            console.log(`   Row ${itemCode}: Qty=${qty}, Rate=${rate}, Dis=${discount}%, Amount=${rowAmount.toFixed(2)}, Tax=${rowTax.toFixed(2)}`);
        }
    });
    
    // Calculate Net Amount: Amount after discount + Tax
    totalNet = totalFTAmt + totalTax;
    
    console.log(`📊 Summary Totals: NT=${totalNtAmt.toFixed(2)}, Dis=${totalDisAmt.toFixed(2)}, Tax=${totalTax.toFixed(2)}, Net=${totalNet.toFixed(2)}`);
    
    // Update summary fields with null checks
    const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val; };
    
    setVal('nt_amt', totalNtAmt.toFixed(2));      // Total before discount
    setVal('sc_amt', '0.00');                      // SC (not used)
    setVal('ft_amt', totalNtAmt.toFixed(2));      // F.T.Amt = N.T.Amt
    setVal('dis_amt', totalDisAmt.toFixed(2));    // Total discount
    setVal('scm_amt', '0.00');                     // Scm (not used)
    setVal('tax_amt', totalTax.toFixed(2));       // Total tax
    setVal('net_amt', totalNet.toFixed(2));       // Final net amount
    setVal('scm_percent', '0.00');                 // Scm % (not used)
}

// Add new empty row
function addNewRow() {
    itemIndex++;
    const tbody = document.getElementById('itemsTableBody');
    
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-row-index', itemIndex);
    newRow.style.cursor = 'pointer';
    newRow.addEventListener('click', function(e) {
        // Don't trigger if clicking on button
        if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
            return;
        }
        const clickedRow = e.currentTarget;
        const rowIdx = parseInt(clickedRow.getAttribute('data-row-index'));
        selectRow(rowIdx);
    });
    
    newRow.innerHTML = `
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][code]" style="font-size: 10px; background: #f8f9fa;" autocomplete="off" readonly></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][item_name]" style="font-size: 10px; background: #f8f9fa;" autocomplete="off" readonly></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][batch]" style="font-size: 10px; background: #f8f9fa;" autocomplete="off" readonly></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][expiry]" style="font-size: 10px; background: #f8f9fa;" autocomplete="off" readonly></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-qty" name="items[${itemIndex}][qty]" id="qty_${itemIndex}" value="" placeholder="0" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][free_qty]" id="free_qty_${itemIndex}" value="0" style="font-size: 10px;"></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-rate" name="items[${itemIndex}][rate]" id="rate_${itemIndex}" value="0" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-discount" name="items[${itemIndex}][discount]" id="discount_${itemIndex}" value="" placeholder="0" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][mrp]" id="mrp_${itemIndex}" value="0" step="0.01" style="font-size: 10px;" readonly></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][amount]" id="amount_${itemIndex}" value="0.00" style="font-size: 10px;" readonly></td>
        <td class="p-0 text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${itemIndex})" title="Delete Row" style="font-size: 9px; padding: 2px 5px;">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    // Mark as incomplete initially
    newRow.setAttribute('data-complete', 'false');
    newRow.classList.add('table-danger'); // Red background for incomplete
    
    tbody.appendChild(newRow);
    
    // Add event listeners for editing
    addRowEventListeners(newRow, itemIndex);
    
    // Focus on code field
    setTimeout(() => {
        const codeInput = newRow.querySelector('input[name*="[code]"]');
        if (codeInput) {
            codeInput.focus();
        }
    }, 100);
}

// Delete row
function deleteRow(rowIndex) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (row) {
        row.remove();
        calculateTotal();
        calculateSummary();
        
        // Clear detailed summary if deleted row was selected
        if (currentSelectedRowIndex === rowIndex) {
            clearDetailedSummary();
            currentSelectedRowIndex = null;
        }
    }
}

// Delete selected item
function deleteSelectedItem() {
    if (currentSelectedRowIndex !== null) {
        deleteRow(currentSelectedRowIndex);
    } else {
        alert('Please select a row to delete');
    }
}

// Insert item
function insertItem() {
    openChooseItemsModal();
}

// Save sale challan transaction
function saveChallan() {
    // Collect header data
    const headerData = {
        series: document.getElementById('seriesSelect')?.value || 'SC',
        date: document.getElementById('challanDate')?.value || '',
        challan_no: document.getElementById('challanNo')?.value || '',
        due_date: document.getElementById('dueDate')?.value || null,
        customer_id: document.getElementById('customerSelect')?.value || '',
        salesman_id: document.getElementById('salesmanSelect')?.value || null,
        cash: document.getElementById('cash')?.value || 'N',
        transfer: document.getElementById('transfer')?.value || 'N',
        remarks: document.getElementById('remarks')?.value || '',
        
        // Summary amounts from top section
        nt_amount: parseFloat(document.getElementById('nt_amt')?.value) || 0,
        sc_amount: parseFloat(document.getElementById('sc_amt')?.value) || 0,
        ft_amount: parseFloat(document.getElementById('ft_amt')?.value) || 0,
        dis_amount: parseFloat(document.getElementById('dis_amt')?.value) || 0,
        scm_amount: parseFloat(document.getElementById('scm_amt')?.value) || 0,
        tax_amount: parseFloat(document.getElementById('tax_amt')?.value) || 0,
        net_amount: parseFloat(document.getElementById('net_amt')?.value) || 0,
        scm_percent: parseFloat(document.getElementById('scm_percent')?.value) || 0,
        tcs_amount: 0,  // Not visible in form but keep for consistency
        excise_amount: 0,  // Not visible in form but keep for consistency
    };
    
    // Validate required fields
    if (!headerData.date) {
        showAlert('Please select Date', 'warning', 'Date Required');
        return;
    }
    
    if (!headerData.customer_id) {
        showAlert('Please select Customer', 'warning', 'Customer Required');
        return;
    }
    
    // Invoice number will be auto-generated by backend, so no need to validate
    
    // Collect items data (only rows with actual data)
    const items = [];
    const rows = document.querySelectorAll('#itemsTableBody tr');
    
    let hasZeroQtyItems = false;
    rows.forEach((row, index) => {
        const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
        const itemName = row.querySelector('input[name*="[item_name]"]')?.value?.trim();
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
        
        const hasItemInfo = itemCode || itemName;
        
        // Check if item has info but qty is 0
        if (hasItemInfo && qty <= 0) {
            hasZeroQtyItems = true;
        }
        
        // Only include items with qty > 0
        if (hasItemInfo && qty > 0) {
            // Get batch_id from row attribute - convert to number
            let batchId = row.getAttribute('data-batch-id');
            if (batchId) {
                batchId = parseInt(batchId); // Convert to integer
                if (isNaN(batchId)) {
                    batchId = null; // If not a valid number, set to null
                }
            } else {
                batchId = null;
            }
            
            // Log for debugging
            console.log('📦 Adding item to challan:', {
                item_name: itemName || itemCode,
                batch_no: row.querySelector('input[name*="[batch]"]')?.value?.trim() || '',
                batch_id: batchId,
                qty: qty
            });
            
            items.push({
                item_code: itemCode || '',
                item_name: itemName || '',
                batch: row.querySelector('input[name*="[batch]"]')?.value?.trim() || '',
                batch_id: batchId, // 🔥 IMPORTANT: Batch ID for quantity reduction (must be integer or null)
                expiry: row.querySelector('input[name*="[expiry]"]')?.value || null,
                qty: qty,
                free_qty: parseFloat(row.querySelector('input[name*="[free_qty]"]')?.value) || 0,
                rate: rate,
                discount: parseFloat(row.querySelector('input[name*="[discount]"]')?.value) || 0,
                mrp: parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0,
                amount: parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0,
                row_order: index
            });
        }
    });
    
    // Validate items
    if (items.length === 0) {
        if (hasZeroQtyItems) {
            showAlert('All items have zero quantity.\n\nPlease enter quantity for at least one item.', 'warning', 'Quantity Required');
        } else {
            showAlert('Please add at least one item.\n\nUse "Choose Items" button to add items.', 'warning', 'Items Required');
        }
        return;
    }
    
    // 🔥 Recalculate summary before saving to ensure amounts are correct
    calculateSummary();
    
    // Re-read header data after recalculation
    headerData.nt_amount = parseFloat(document.getElementById('nt_amt')?.value) || 0;
    headerData.sc_amount = parseFloat(document.getElementById('sc_amt')?.value) || 0;
    headerData.ft_amount = parseFloat(document.getElementById('ft_amt')?.value) || 0;
    headerData.dis_amount = parseFloat(document.getElementById('dis_amt')?.value) || 0;
    headerData.scm_amount = parseFloat(document.getElementById('scm_amt')?.value) || 0;
    headerData.tax_amount = parseFloat(document.getElementById('tax_amt')?.value) || 0;
    headerData.net_amount = parseFloat(document.getElementById('net_amt')?.value) || 0;
    headerData.scm_percent = parseFloat(document.getElementById('scm_percent')?.value) || 0;
    
    // Prepare final payload
    const payload = {
        ...headerData,
        items: items,
        _token: document.querySelector('input[name="_token"]').value
    };
    
    console.log('=== SAVING SALE CHALLAN ===');
    console.log('Header Data:', headerData);
    console.log('Items Count:', items.length);
    console.log('Items Data:', items);
    
    // 🔥 DEBUG: Check batch_id in items
    items.forEach((item, idx) => {
        console.log(`Item ${idx + 1}:`, {
            item_name: item.item_name,
            batch_no: item.batch,
            batch_id: item.batch_id,
            qty: item.qty,
            batch_id_type: typeof item.batch_id
        });
    });
    
    console.log('Full Payload:', payload);
    console.log('===================================');
    
    // Send to server
    fetch('{{ route("admin.sale-challan.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const text = await response.text();
        console.log('Raw response (first 1000 chars):', text.substring(0, 1000));
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            console.error('Non-JSON response received');
            console.error('Full response:', text);
            throw new Error('Server returned HTML instead of JSON. Check browser console for full response.');
        }
        
        try {
            const data = JSON.parse(text);
            if (!response.ok) {
                return Promise.reject(data);
            }
            return data;
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response text:', text);
            throw new Error('Invalid JSON response: ' + text.substring(0, 200));
        }
    })
    .then(data => {
        if (data.success) {
            showSuccessModalWithReload('Sale Challan saved successfully!\n\nChallan No: ' + data.challan_no + '\n\nStock has been deducted. Invoice can be created later.', 'Success');
        } else {
            showAlert('Error: ' + (data.message || 'Unknown error'), 'error', 'Save Failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error: ' + error.message + '\n\nCheck browser console for details.', 'error', 'Save Failed');
    });
}

// Fetch next challan number from server
async function fetchNextChallanNo() {
    try {
        const response = await fetch('{{ route("admin.sale-challan.next-challan-no") }}');
        const data = await response.json();
        if (data.success && data.next_challan_no) {
            document.getElementById('challanNo').value = data.next_challan_no;
            console.log('Next challan number updated:', data.next_challan_no);
        } else {
            // Fallback: increment locally
            const currentChallanNo = document.getElementById('challanNo').value;
            const match = currentChallanNo.match(/SCH-(\d+)/);
            if (match) {
                const nextNum = parseInt(match[1]) + 1;
                const nextChallanNo = 'SCH-' + String(nextNum).padStart(6, '0');
                document.getElementById('challanNo').value = nextChallanNo;
            }
        }
    } catch (error) {
        console.error('Error fetching next challan number:', error);
        // Fallback: increment locally
        const currentChallanNo = document.getElementById('challanNo').value;
        const match = currentChallanNo.match(/SCH-(\d+)/);
        if (match) {
            const nextNum = parseInt(match[1]) + 1;
            const nextChallanNo = 'SCH-' + String(nextNum).padStart(6, '0');
            document.getElementById('challanNo').value = nextChallanNo;
        }
    }
}

// Clear form after successful save
function clearFormAfterSave() {
    // Clear customer and salesman
    document.getElementById('customerSelect').value = '';
    document.getElementById('salesmanSelect').value = '';
    
    // Clear all item rows
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    
    // Add one empty row
    addNewRow();
    
    // Reset summary amounts
    document.getElementById('nt_amt').value = '0.00';
    document.getElementById('sc_amt').value = '0.00';
    document.getElementById('ft_amt').value = '0.00';
    document.getElementById('dis_amt').value = '0.00';
    document.getElementById('scm_amt').value = '0.00';
    document.getElementById('tax_amt').value = '0.00';
    document.getElementById('net_amt').value = '0.00';
    
    // Reset detail summary
    document.getElementById('detailNtAmt').value = '0.00';
    document.getElementById('detailScmAmt').value = '0.00';
    document.getElementById('detailTaxAmt').value = '0.00';
    document.getElementById('detailNetAmt').value = '0.00';
    document.getElementById('detailDisAmt').value = '0.00';
    
    console.log('Form cleared for next transaction');
}

// Close modals on backdrop click
document.getElementById('chooseItemsBackdrop')?.addEventListener('click', closeChooseItemsModal);
document.getElementById('batchSelectionBackdrop')?.addEventListener('click', closeBatchSelectionModal);

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeChooseItemsModal();
        closeBatchSelectionModal();
        closeAlert();
    }
});

// ============================================
// TOAST NOTIFICATION FUNCTIONS
// ============================================

// Toast Notification Function
function showToast(message, type = 'error', title = null) {
    const container = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();
    
    let defaultTitle, icon;
    switch(type) {
        case 'warning':
            defaultTitle = 'Warning';
            icon = '⚠️';
            break;
        case 'error':
        default:
            defaultTitle = 'Error';
            icon = '❌';
            break;
    }
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast-notification ${type}`;
    toast.innerHTML = `
        <div class="toast-header">
            <span>${icon} ${title || defaultTitle}</span>
            <button type="button" class="toast-close" onclick="closeToast('${toastId}')">×</button>
        </div>
        <div class="toast-body">${message}</div>
        <div class="toast-progress"></div>
    `;
    
    container.appendChild(toast);
    
    // Show toast with animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        closeToast(toastId);
    }, 5000);
    
    return toastId;
}

// Close specific toast
function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('hide');
        setTimeout(() => {
            toast.remove();
        }, 400);
    }
}

// ============================================
// ALERT MODAL FUNCTIONS
// ============================================

// Hide all backdrops (to prevent multiple backdrops showing)
function hideAllBackdrops() {
    document.querySelectorAll('.pending-orders-backdrop, .alert-modal-backdrop').forEach(backdrop => {
        backdrop.classList.remove('show');
        backdrop.style.display = 'none';
    });
}

// Enhanced Alert Modal Functions
function showAlert(message, type = 'error', title = null) {
    // Use toast for warning and error messages (red color for errors)
    if (type === 'warning' || type === 'error') {
        return showToast(message, type, title);
    }
    
    // Hide existing backdrops
    hideAllBackdrops();
    
    const modal = document.getElementById('alertModal');
    const backdrop = document.getElementById('alertBackdrop');
    const header = modal.querySelector('.alert-modal-header');
    const titleElement = modal.querySelector('.alert-modal-title');
    const body = modal.querySelector('.alert-modal-body');
    const footer = modal.querySelector('.alert-modal-footer');
    
    // Set default titles and icons based on type
    let defaultTitle, icon;
    switch(type) {
        case 'success':
            defaultTitle = 'Success';
            icon = '✅';
            break;
        case 'info':
            defaultTitle = 'Information';
            icon = 'ℹ️';
            break;
        default:
            defaultTitle = 'Alert';
            icon = '📢';
            break;
    }
    
    // Set header class
    header.className = `alert-modal-header ${type}`;
    
    // Set title and icon
    titleElement.innerHTML = `${icon} ${title || defaultTitle}`;
    
    // Set message
    body.textContent = message;
    
    // Reset footer to single OK button
    footer.innerHTML = '<button type="button" class="btn btn-primary" onclick="closeAlert()">OK</button>';
    
    // Show modal with enhanced effects
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
    }, 10);
}

// Success modal with page reload functionality
function showSuccessModalWithReload(message, title = 'Success') {
    // Hide existing backdrops
    hideAllBackdrops();
    
    const modal = document.getElementById('alertModal');
    const backdrop = document.getElementById('alertBackdrop');
    const header = modal.querySelector('.alert-modal-header');
    const titleElement = modal.querySelector('.alert-modal-title');
    const body = modal.querySelector('.alert-modal-body');
    const footer = modal.querySelector('.alert-modal-footer');
    
    // Set header for success
    header.className = 'alert-modal-header success';
    titleElement.innerHTML = `✅ ${title}`;
    body.textContent = message;
    
    // Set footer with OK button that reloads page
    footer.innerHTML = '<button type="button" class="btn btn-success" onclick="reloadPageAfterSuccess()">OK</button>';
    
    // Show modal with enhanced effects
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
    }, 10);
}

// Reload page after success modal
function reloadPageAfterSuccess() {
    closeAlert();
    // Small delay to allow modal close animation
    setTimeout(() => {
        window.location.reload();
    }, 300);
}

// Store callback functions globally
let confirmCallback = null;
let cancelCallback = null;

// Confirmation Modal Function
function showConfirm(message, onConfirm, onCancel = null, title = 'Confirm') {
    // Hide existing backdrops
    hideAllBackdrops();
    
    // Store callbacks globally
    confirmCallback = onConfirm;
    cancelCallback = onCancel;
    
    const modal = document.getElementById('alertModal');
    const backdrop = document.getElementById('alertBackdrop');
    const header = modal.querySelector('.alert-modal-header');
    const titleElement = modal.querySelector('.alert-modal-title');
    const body = modal.querySelector('.alert-modal-body');
    const footer = modal.querySelector('.alert-modal-footer');
    
    // Set header for confirmation
    header.className = 'alert-modal-header warning';
    titleElement.innerHTML = `❓ ${title}`;
    body.textContent = message;
    
    // Set footer with Yes/No buttons
    footer.innerHTML = `
        <button type="button" class="btn btn-secondary" onclick="handleConfirmCancel()">No</button>
        <button type="button" class="btn btn-primary" onclick="handleConfirmYes()">Yes</button>
    `;
    
    // Show modal with enhanced effects
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
    }, 10);
}

// Handle confirmation Yes button
function handleConfirmYes() {
    closeAlert();
    if (confirmCallback && typeof confirmCallback === 'function') {
        confirmCallback();
    }
    // Clear callbacks
    confirmCallback = null;
    cancelCallback = null;
}

// Handle confirmation No button
function handleConfirmCancel() {
    closeAlert();
    if (cancelCallback && typeof cancelCallback === 'function') {
        cancelCallback();
    }
    // Clear callbacks
    confirmCallback = null;
    cancelCallback = null;
}

function closeAlert() {
    const modal = document.getElementById('alertModal');
    const backdrop = document.getElementById('alertBackdrop');
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    // Hide after animation
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
    }, 400);
}

// Close modal when clicking backdrop
document.addEventListener('DOMContentLoaded', function() {
    const alertBackdrop = document.getElementById('alertBackdrop');
    if (alertBackdrop) {
        alertBackdrop.addEventListener('click', closeAlert);
    }
});
</script>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<!-- Alert Modal Backdrop -->
<div id="alertBackdrop" class="alert-modal-backdrop"></div>

<!-- Alert Modal -->
<div id="alertModal" class="alert-modal">
    <div class="alert-modal-content">
        <div class="alert-modal-header">
            <h5 class="alert-modal-title">Alert</h5>
            <button type="button" class="btn-close-modal" onclick="closeAlert()">×</button>
        </div>
        <div class="alert-modal-body">
            Alert message will appear here.
        </div>
        <div class="alert-modal-footer">
            <button type="button" class="btn btn-primary" onclick="closeAlert()">OK</button>
        </div>
    </div>
</div>

@endsection
