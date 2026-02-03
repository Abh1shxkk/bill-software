@extends('layouts.admin')

@section('title', 'Sale Modification')

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
    
    /* Receipt Mode Button */
    .btn-purple {
        background-color: #6f42c1 !important;
        border-color: #6f42c1 !important;
        color: white !important;
    }
    .btn-purple:hover {
        background-color: #5a32a3 !important;
        border-color: #5a32a3 !important;
        color: white !important;
    }
    .btn-outline-purple {
        border-color: #6f42c1 !important;
        color: #6f42c1 !important;
    }
    .btn-outline-purple:hover {
        background-color: #6f42c1 !important;
        color: white !important;
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

    .row-selected {
        background-color: #d4edff !important;
        border: 2px solid #007bff !important;
    }

    .row-selected td {
        background-color: #d4edff !important;
    }

    /* Pending Orders Modal Styles (matching purchase transaction) */
    .pending-orders-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: calc(240px + 45%); /* Sidebar width + 45% of remaining width */
        transform: translate(-50%, -50%) scale(0.7);
        width: 70%; /* Smaller modal width */
        max-width: 950px;
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
        left: 240px; /* Start after sidebar */
        right: 0;
        bottom: 0;
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
        left: calc(240px + 45%); /* Sidebar width + 45% of remaining width */
        transform: translate(-50%, -50%) scale(0.7);
        width: 70%; /* Smaller modal width */
        max-width: 900px;
        z-index: 9999;
        opacity: 0;
        transition: all 0.3s ease-in-out;
    }

    .choose-items-modal.show {
        display: block;
        transform: translate(-50%, -50()) scale(1);
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
        left: 240px; /* Start after sidebar */
        right: 0;
        bottom: 0;
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
    
    /* Compact styling for invoices table */
    #invoicesTableBody td {
        padding: 6px 4px;
        font-size: 11px;
    }
    
    #invoicesTableBody .badge {
        font-size: 9px;
        padding: 3px 6px;
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
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil-square me-2"></i> Sale Modification</h4>
        <div class="text-muted small">Modify existing sale transactions</div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" onclick="openDateRangeModal()">
            <i class="bi bi-calendar-range"></i> Filter by Date
        </button>
        <button type="button" class="btn btn-success btn-sm" onclick="openAllInvoicesModal()">
            <i class="bi bi-list-ul"></i> All Invoices
        </button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form id="saleTransactionForm" method="POST" autocomplete="off" onsubmit="return false;">
            @csrf
            
            <!-- Header Section -->
            <div class="header-section">
                <!-- Row 1: Series, Date, Customer -->
                <div class="header-row">
                    <div class="field-group">
                        <label>Series:</label>
                        <select class="form-control" name="series" id="seriesSelect" style="width: 60px;" onchange="updateInvoiceType()">
                            <option value="SB" selected>SB</option>
                            <option value="S2">S2</option>
                        </select>
                        <input type="text" class="form-control readonly-field" id="invoiceTypeDisplay" value="TAX INVOICE" readonly style="width: 130px; text-align: center; font-weight: bold;">
                    </div>
                    
                    <div class="field-group">
                        <label>Sale Date</label>
                        <input type="date" class="form-control" name="date" id="saleDate" value="{{ date('Y-m-d') }}" style="width: 140px;" onchange="updateDayName()">
                        <input type="text" class="form-control readonly-field" id="dayName" value="{{ date('l') }}" readonly style="width: 90px;">
                    </div>
                    
                    <div class="field-group">
                        <label>Customer:</label>
                        <select class="form-control readonly-field" name="customer_id" id="customerSelect" style="width: 250px;" autocomplete="off" disabled>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-code="{{ $customer->code ?? '' }}">{{ $customer->code ?? '' }} - {{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Row 2: Invoice No, Sales Man, Inner Card -->
                <div class="d-flex gap-3">
                    <!-- Left Side - Invoice & Salesman -->
                    <div style="width: 250px;">
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Inv.No.:</label>
                            <input type="text" class="form-control" name="invoice_no" id="invoiceNo" value="" placeholder="Type invoice no." style="background-color: #fff8dc;">
                            <input type="hidden" id="transactionId" value="">
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Sales Man:</label>
                            <select class="form-control readonly-field" name="salesman_id" id="salesmanSelect" style="width: 170px;" autocomplete="off" disabled>
                                <option value="">-- Select Salesman --</option>
                                @foreach($salesmen as $salesman)
                                    <option value="{{ $salesman->id }}" data-name="{{ $salesman->name }}" data-code="{{ $salesman->code ?? '' }}">{{ $salesman->code ?? '' }} - {{ $salesman->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-warning btn-sm" id="chooseItemsBtn" onclick="handleChooseItemsClick()" style="width: 100%;">
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
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>Cash:</label>
                                    <input type="text" class="form-control" name="cash" id="cash" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>Transfer:</label>
                                    <input type="text" class="form-control" name="transfer" id="transfer" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mt-1">
                            <div class="col-md-12">
                                <div class="field-group">
                                    <label>Remarks:</label>
                                    <input type="text" class="form-control" name="remarks" id="remarks">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Uploaded Receipts Section - Only visible for receipt-mode customers -->
            <div id="uploadedReceiptsSection" class="bg-white border rounded p-3 mb-2" style="display: none; border-color: #6f42c1 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0" style="color: #6f42c1; font-size: 13px;">
                        <i class="bi bi-file-earmark-image me-2"></i>Uploaded Receipts
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-purple" onclick="openReceiptUploadModal()" style="font-size: 11px; border-color: #6f42c1; color: #6f42c1;">
                        <i class="bi bi-cloud-upload"></i> Upload More
                    </button>
                </div>
                <div id="uploadedReceiptsContainer" class="d-flex flex-wrap gap-2">
                    <!-- Uploaded receipts will be displayed here -->
                    <div class="text-muted small" id="noReceiptsMessage" style="font-size: 11px;">
                        <i class="bi bi-info-circle me-1"></i>No receipts uploaded yet. Click "Choose Items" or "Upload More" to add receipts.
                    </div>
                </div>
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
            
            <!-- Detailed Info Section (matching image - orange background) -->
            <div class="bg-white border rounded p-2 mb-2" style="background: #ffe6cc;">
                <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                    <!-- Row 1: Packing | N.T.Amt. | Scm. % | Sub.Tot. | Comp | Srino -->
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Packing</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field" id="detailPacking" readonly value="" style="height: 22px; width: 60px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>N.T.Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailNtAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Scm. %</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScmPercent" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Sub.Tot.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailSubTot" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Comp</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field" id="detailCompany" readonly value="" style="height: 22px; width: 100px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Srino</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="detailSrIno" readonly value="" style="height: 22px; width: 60px;"></td>
                    </tr>
                    
                    <!-- Row 2: Unit | SC Amt. | Scm.Amt. | Tax Amt. | SCM. -->
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Unit</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="detailUnit" readonly value="1" style="height: 22px; width: 60px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>SC Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Scm.Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScmAmt" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Tax Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailTaxAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;" colspan="2"><strong>SCM.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-center" id="detailScm1" readonly value="0" style="height: 22px; width: 40px;"></td>
                        <td style="padding: 3px; text-align: center; font-weight: bold;">+</td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-center" id="detailScm2" readonly value="0" style="height: 22px; width: 40px;"></td>
                    </tr>
                    
                    <!-- Row 3: Cl. Qty | Dis. Amt. | Net Amt. | COST + GST | Vol. | Batch Code -->
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Cl. Qty</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-end" id="detailClQty" readonly value="" style="height: 22px; width: 60px; background: #add8e6;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Dis. Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailDisAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Net Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailNetAmt" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>COST + GST</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailCostGst" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Vol.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailVol" readonly value="0" style="height: 22px; width: 40px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Batch Code</strong></td>
                        <td style="padding: 3px;" colspan="2"><input type="text" class="form-control form-control-sm readonly-field text-center" id="detailBatchCode" readonly value="" style="height: 22px; width: 100px;"></td>
                    </tr>
                    
                    <!-- Row 4: Lctn | HS Amt. -->
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Lctn</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field" id="detailLctn" readonly value="" style="height: 22px; width: 60px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>HS Amt.</strong></td>
                        <td style="padding: 3px;" colspan="9"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailHsAmt" readonly value="0.00" style="height: 22px; width: 100px;"></td>
                    </tr>
                </table>
            </div>
            
            <!-- TEMP Transaction Receipt Section (only shown for TEMP transactions) -->
            <div id="tempReceiptSection" class="card mb-3" style="display: none;">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-image"></i> Scanned Receipts</h6>
                    <span class="badge bg-dark" id="tempReceiptCount">0 receipts</span>
                </div>
                <div class="card-body" id="tempReceiptImages" style="max-height: 200px; overflow-y: auto;">
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">No receipts attached</p>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2 justify-content-between">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openReceiptScanner()">
                        <i class="bi bi-camera"></i> Scan More Receipts
                    </button>
                    <small class="text-muted align-self-center">Click on receipt to open OCR preview</small>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" id="saveBtn" onclick="saveSale()">
                    <i class="bi bi-save"></i> <span id="saveBtnText">Save</span>
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.reload()">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reusable Item Selection Modal Component -->
@include('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'sale-modification',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => true,
    'batchModalId' => 'batchSelectionModal',
])

<!-- Reusable Batch Selection Modal Component (shows only available stock) -->
@include('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'sale-modification',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showPurchaseRate' => true,
    'showCostDetails' => true,
    'showSupplier' => true,
])

<!-- Date Range Modal Backdrop -->
<div id="dateRangeBackdrop" class="pending-orders-backdrop"></div>

<!-- Date Range Modal -->
<div id="dateRangeModal" class="pending-orders-modal" style="max-width: 450px;">
    <div class="pending-orders-content">
        <div class="pending-orders-header" style="background: #0d6efd;">
            <h5 class="pending-orders-title"><i class="bi bi-calendar-range me-2"></i>Filter by Date Range</h5>
            <button type="button" class="btn-close-modal" onclick="closeDateRangeModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body" style="padding: 20px;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600;">From Date</label>
                    <input type="date" class="form-control" id="filterFromDate">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600;">To Date</label>
                    <input type="date" class="form-control" id="filterToDate">
                </div>
            </div>
        </div>
        <div class="pending-orders-footer">
            <button type="button" class="btn btn-primary btn-sm" onclick="filterInvoicesByDate()">
                <i class="bi bi-search"></i> Apply Filter
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeDateRangeModal()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
        </div>
    </div>
</div>

<!-- Invoices Modal Backdrop -->
<div id="invoicesBackdrop" class="pending-orders-backdrop"></div>

<!-- Invoices Modal -->
<div id="invoicesModal" class="pending-orders-modal" style="max-width: 1000px;">
    <div class="pending-orders-content">
        <div class="pending-orders-header" style="background: #198754;">
            <h5 class="pending-orders-title" id="invoicesModalTitle"><i class="bi bi-receipt me-2"></i>Select Invoice to Modify</h5>
            <button type="button" class="btn-close-modal" onclick="closeInvoicesModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body">
            <div class="p-3 border-bottom bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <input type="text" class="form-control" id="invoiceSearchInput" placeholder="Search by Invoice No, Customer..." style="max-width: 300px; font-size: 12px;" oninput="filterInvoicesInModal()">
                    <span class="text-muted" style="font-size: 12px;" id="invoicesTotal">Total: 0 invoice(s)</span>
                </div>
                <div class="text-muted mt-2" style="font-size: 11px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Use <kbd>↑</kbd> <kbd>↓</kbd> to navigate, <kbd>Enter</kbd> to select, <kbd>Esc</kbd> to close
                </div>
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                        <tr>
                            <th style="width: 100px; text-align: center;">Invoice No</th>
                            <th style="width: 100px; text-align: center;">Date</th>
                            <th style="width: 200px;">Customer</th>
                            <th style="width: 100px; text-align: right;">Amount</th>
                            <th style="width: 80px; text-align: center;">Status</th>
                            <th style="width: 80px; text-align: center;">Payment</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesTableBody">
                        <tr><td colspan="6" class="text-center text-muted">Click "All Invoices" or "Filter by Date" to load invoices</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pending-orders-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoicesModal()">
                <i class="bi bi-x-circle"></i> Close
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
    
    // Auto-load transaction if invoice_no is provided in URL
    const urlParams = new URLSearchParams(window.location.search);
    const invoiceNoParam = urlParams.get('invoice_no');
    const modeParam = urlParams.get('mode');
    
    // 🔥 FIX: If mode=finalize, the transaction was just finalized and the TEMP invoice 
    // no longer exists. Clear URL params and don't try to load it.
    if (modeParam === 'finalize') {
        // Clear URL parameters to prevent the error on page load
        const cleanUrl = window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
        console.log('✅ Transaction finalized - cleared URL parameters');
        // Don't try to load the old invoice number
    } else if (invoiceNoParam) {
        const invoiceNoInput = document.getElementById('invoiceNo');
        if (invoiceNoInput) {
            invoiceNoInput.value = invoiceNoParam;
            // Auto-fetch the invoice data
            setTimeout(() => {
                loadTransactionByInvoiceNo(invoiceNoParam);
            }, 500);
        }
    }
    
    // Invoice number update and button state check
    const invoiceNoInput = document.getElementById('invoiceNo');
    if (invoiceNoInput) {
        invoiceNoInput.addEventListener('input', function() {
            checkChooseItemsButtonState();
        });
        
        // Load transaction on Enter key
        invoiceNoInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const invoiceNo = this.value.trim();
                if (invoiceNo && invoiceNo !== 'Loading...') {
                    loadTransactionByInvoiceNo(invoiceNo);
                } else if (!invoiceNo) {
                    // If empty, open invoices modal
                    openAllInvoicesModal();
                }
            }
        });
    }
    
    // Customer name update
    const customerSelect = document.getElementById('customerSelect');
    if (customerSelect) {
        customerSelect.addEventListener('change', function() {
            updateCustomerName();
        });
    }
    
    // Salesman name update
    const salesmanSelect = document.getElementById('salesmanSelect');
    if (salesmanSelect) {
        salesmanSelect.addEventListener('change', function() {
            updateSalesmanName();
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
});

// Ensure a select has an option with the given value; create if missing, then select it
function setSelectOption(selectElement, value, displayText) {
    if (!selectElement) return;
    // Temporarily enable to allow value changes to reflect cleanly
    const wasDisabled = selectElement.disabled;
    selectElement.disabled = false;
    
    let option = Array.from(selectElement.options).find(opt => String(opt.value) === String(value));
    if (!option && value) {
        option = new Option(displayText || String(value), String(value), true, true);
        selectElement.add(option);
    }
    if (option) {
        // If we have a label to show, update option text as well
        if (displayText && option.text !== displayText) {
            option.text = displayText;
        }
        selectElement.value = String(value);
    }
    
    // Restore disabled state
    selectElement.disabled = wasDisabled;
}

// Load items from server
function loadItems() {
    fetch('{{ route("admin.sale.getItems") }}')
        .then(response => response.json())
        .then(data => {
            itemsData = data;
            console.log('Items loaded:', itemsData.length);
        })
        .catch(error => console.error('Error loading items:', error));
}

// Update invoice type based on series
function updateInvoiceType() {
    const series = document.getElementById('seriesSelect').value;
    const display = document.getElementById('invoiceTypeDisplay');
    if (series === 'S2') {
        display.value = 'GST INVOICE';
    } else if (series === 'SB') {
        display.value = 'TAX INVOICE';
    }
}

// Update customer name (no separate field needed - name shown in dropdown)
function updateCustomerName() {
    // Customer name already displayed in dropdown, no separate field needed
}

// Update salesman name (no separate field needed - name shown in dropdown)
function updateSalesmanName() {
    // Salesman name already displayed in dropdown, no separate field needed
}

// Update day name
function updateDayName() {
    const dateInput = document.getElementById('saleDate');
    const dayNameInput = document.getElementById('dayName');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayNameInput.value = days[date.getDay()];
    }
}

// Check if Choose Items button should be enabled (modified for modification page)
function checkChooseItemsButtonState() {
    const invoiceNoInput = document.getElementById('invoiceNo');
    const invoiceNo = invoiceNoInput?.value?.trim();
    const transactionId = document.getElementById('transactionId')?.value;
    const chooseItemsBtn = document.getElementById('chooseItemsBtn');
    
    if (chooseItemsBtn) {
        if (transactionId) {
            // Transaction loaded - button opens items modal to add more items
            chooseItemsBtn.classList.remove('btn-warning', 'btn-info');
            chooseItemsBtn.classList.add('btn-success');
            chooseItemsBtn.style.opacity = '1';
            chooseItemsBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Add More Items';
            chooseItemsBtn.title = 'Add more items to this invoice';
        } else {
            // No transaction loaded - button opens invoices modal
            chooseItemsBtn.classList.remove('btn-info', 'btn-success');
            chooseItemsBtn.classList.add('btn-warning');
            chooseItemsBtn.style.opacity = '1';
            chooseItemsBtn.innerHTML = '<i class="bi bi-list-check"></i> Choose Invoice';
            chooseItemsBtn.title = 'Click to select an invoice to modify';
        }
    }
}

// Handle Choose Items button click - opens invoices modal if no transaction, or items modal if transaction loaded
function handleChooseItemsClick() {
    const transactionId = document.getElementById('transactionId')?.value;
    
    if (transactionId) {
        // Transaction already loaded - open items modal to add more items
        openChooseItemsModal();
    } else {
        // No transaction loaded - open invoices modal to select invoice
        openAllInvoicesModal();
    }
}

// Open Choose Items Modal (Modified for modification page - only called when transaction is loaded)
async function openChooseItemsModal() {
    const transactionId = document.getElementById('transactionId')?.value;
    
    if (!transactionId) {
        showAlert('Please select an invoice first!\n\nClick "Choose Invoice" or "All Invoices" to select an invoice to modify.', 'warning', 'Invoice Required');
        return;
    }
    
    // Now open the modal to add more items
    const modal = document.getElementById('chooseItemsModal');
    const backdrop = document.getElementById('chooseItemsBackdrop');
    
    // Load and display items
    displayItemsInModal();
    
    // Show modal
    setTimeout(() => {
        modal.classList.add('show');
        backdrop.classList.add('show');
    }, 10);
}

// Close Choose Items Modal
function closeChooseItemsModal() {
    const modal = document.getElementById('chooseItemsModal');
    const backdrop = document.getElementById('chooseItemsBackdrop');
    modal.classList.remove('show');
    backdrop.classList.remove('show');
}

// Display items in modal
function displayItemsInModal() {
    const tbody = document.getElementById('chooseItemsBody');
    tbody.innerHTML = '';
    
    if (itemsData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No items found</td></tr>';
        return;
    }
    
    itemsData.forEach(item => {
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
}

// Filter items in modal
function filterItemsInModal() {
    const searchText = document.getElementById('itemSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#chooseItemsBody tr');
    
    rows.forEach(row => {
        const name = (row.cells[0]?.textContent || '').toLowerCase();
        const hsn = (row.cells[1]?.textContent || '').toLowerCase();
        const company = (row.cells[3]?.textContent || '').toLowerCase();
        
        if (name.includes(searchText) || hsn.includes(searchText) || company.includes(searchText)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
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
// Shows ALL batches from the Batch table (qty field = current remaining stock)
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
    
    // Add item to table with batch
    addItemToTable(pendingItemSelection, selectedBatch);
    
    // Close batch modal
    closeBatchSelectionModal();
    
    // Clear selected batch
    window.selectedBatch = null;
}

// Add item to table
function addItemToTable(item, batch) {
    itemIndex++;
    const tbody = document.getElementById('itemsTableBody');
    
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-row-index', itemIndex);
    newRow.setAttribute('data-item-id', item.id);
    newRow.style.cursor = 'pointer';
    newRow.addEventListener('click', function(e) {
        const clickedRow = e.currentTarget;
        const rowIdx = parseInt(clickedRow.getAttribute('data-row-index'));
        selectRow(rowIdx);
    });
    
    // Don't populate qty - user will enter it manually
    const qty = 0;
    // Use batch's sale rate if available, otherwise use item's sale rate
    const rate = parseFloat(batch.avg_s_rate || batch.s_rate || item.s_rate || 0);
    const amount = 0.00;  // Will be calculated when user enters qty
    
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
    
    newRow.innerHTML = `
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][code]" value="${item.bar_code || ''}" style="font-size: 10px;" autocomplete="off"></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][item_name]" value="${item.name || ''}" style="font-size: 10px; background: transparent;" autocomplete="off" readonly></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][batch]" value="${batch.batch_no || ''}" style="font-size: 10px;" autocomplete="off"></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][expiry]" value="${expiryDisplay}" style="font-size: 10px;" autocomplete="off"></td>
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
    newRow.setAttribute('data-hsn-code', item.hsn_code || '');
    newRow.setAttribute('data-cgst', item.cgst_percent || 0);
    newRow.setAttribute('data-sgst', item.sgst_percent || 0);
    newRow.setAttribute('data-cess', item.cess_percent || 0);
    newRow.setAttribute('data-packing', item.packing || '');
    newRow.setAttribute('data-unit', item.unit || '');
    newRow.setAttribute('data-company', item.company_name || item.company || '');
    newRow.setAttribute('data-batch-code', batch.batch_no || '');
    newRow.setAttribute('data-case-qty', item.case_qty || 0);
    newRow.setAttribute('data-box-qty', item.box_qty || 0);
    
    // Store batch purchase details
    newRow.setAttribute('data-batch-purchase-rate', batch.avg_pur_rate || batch.pur_rate || 0);
    newRow.setAttribute('data-batch-cost-gst', batch.avg_cost_gst || batch.cost_gst || 0);
    newRow.setAttribute('data-batch-supplier', batch.supplier_name || '');
    newRow.setAttribute('data-batch-purchase-date', batch.purchase_date_display || batch.purchase_date || '');
    
    // 🔥 IMPORTANT: Store batch ID for quantity reduction (must be number)
    const batchId = batch.id ? parseInt(batch.id) : '';
    if (batchId) {
        newRow.setAttribute('data-batch-id', batchId.toString());
        console.log('✅ Batch ID stored in row:', batchId);
    } else {
        console.warn('⚠️ No batch ID found in batch object:', batch);
    }
    
    // Mark row as incomplete initially
    newRow.setAttribute('data-complete', 'false');
    newRow.classList.add('table-danger'); // Red background for incomplete
    
    tbody.appendChild(newRow);
    
    // Add event listeners for editing
    addRowEventListeners(newRow, itemIndex);
    
    // Update row color
    updateRowColor(itemIndex);
    
    // Select the row (this will populate detailed summary immediately)
    selectRow(itemIndex);
    
    // Update detailed summary immediately since item is populated
    updateDetailedSummary(itemIndex);
    
    // Scroll row into view
    newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Focus on Qty field after a small delay to ensure DOM is ready
    setTimeout(() => {
        const qtyField = document.getElementById(`qty_${itemIndex}`);
        if (qtyField) {
            qtyField.focus();
            // Don't select - let user type directly
        }
    }, 100);
    
    // Calculate totals
    calculateTotal();
}

// Track which row barcode was entered for
if (typeof window.pendingBarcodeRowIndex === 'undefined') {
    window.pendingBarcodeRowIndex = null;
}

// Move to next row's code field (creates new row if needed)
function moveToNextRowCodeField(currentRowIndex) {
    const tbody = document.getElementById('itemsTableBody');
    const allRows = tbody.querySelectorAll('tr[data-row-index]');
    let nextRow = null;
    
    // Find next row after current
    for (let i = 0; i < allRows.length; i++) {
        const rowIdx = parseInt(allRows[i].getAttribute('data-row-index'));
        if (rowIdx > currentRowIndex) {
            nextRow = allRows[i];
            break;
        }
    }
    
    // If no next row, create a new empty row
    if (!nextRow) {
        nextRow = addEmptyRow();
    }
    
    // Focus on code field of next row
    if (nextRow) {
        const codeInput = nextRow.querySelector('input[name*="[code]"]');
        if (codeInput) {
            codeInput.focus();
            codeInput.select();
        }
    }
}

// Add empty row to table
function addEmptyRow() {
    itemIndex++;
    const tbody = document.getElementById('itemsTableBody');
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-row-index', itemIndex);
    newRow.style.cursor = 'pointer';
    
    newRow.innerHTML = `
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][code]" value="" style="font-size: 10px;" autocomplete="off"></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][item_name]" value="" style="font-size: 10px;" autocomplete="off"></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][batch]" value="" style="font-size: 10px;" autocomplete="off"></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][expiry]" value="" style="font-size: 10px;" autocomplete="off"></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-qty" name="items[${itemIndex}][qty]" id="qty_${itemIndex}" value="" placeholder="0" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][free_qty]" id="free_qty_${itemIndex}" value="0" style="font-size: 10px;"></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-rate" name="items[${itemIndex}][rate]" id="rate_${itemIndex}" value="0.00" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-discount" name="items[${itemIndex}][discount]" id="discount_${itemIndex}" value="" placeholder="0" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][mrp]" id="mrp_${itemIndex}" value="0.00" step="0.01" style="font-size: 10px;" readonly></td>
        <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][amount]" id="amount_${itemIndex}" value="0.00" style="font-size: 10px;" readonly></td>
        <td class="p-0 text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${itemIndex})" title="Delete Row" style="font-size: 9px; padding: 2px 5px;">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    addRowEventListeners(newRow, itemIndex);
    
    return newRow;
}

// Fetch item by barcode and open batch modal
function fetchItemByBarcodeAndOpenBatchModal(barcode, rowIndex) {
    console.log('🔍 Fetching item by barcode:', barcode, 'for row:', rowIndex);
    
    // Store the row index for later population
    window.pendingBarcodeRowIndex = rowIndex;
    
    // Fetch item from API
    fetch(`{{ url('/admin/api/items/search') }}?search=${encodeURIComponent(barcode)}&exact=1`)
        .then(response => response.json())
        .then(data => {
            if (data.items && data.items.length > 0) {
                const item = data.items[0];
                console.log('✅ Found item:', item.name);
                
                // Store item and open batch modal
                window.pendingItemSelection = item;
                
                // Open batch modal for this item
                if (typeof openBatchModal_batchSelectionModal === 'function') {
                    openBatchModal_batchSelectionModal(item);
                } else if (typeof openBatchSelectionModal === 'function') {
                    openBatchSelectionModal(item);
                }
            } else {
                console.warn('⚠️ No item found for barcode:', barcode);
                alert('Item not found for barcode: ' + barcode);
                window.pendingBarcodeRowIndex = null;
            }
        })
        .catch(error => {
            console.error('Error fetching item:', error);
            alert('Error fetching item. Please try again.');
            window.pendingBarcodeRowIndex = null;
        });
}

// Populate a specific row with item and batch data (for barcode entry)
function populateRowWithItemAndBatch(rowIndex, item, batch) {
    const row = document.querySelector(`#itemsTableBody tr[data-row-index="${rowIndex}"]`);
    if (!row) {
        console.error('Row not found for index:', rowIndex);
        // Fallback to addItem
        addItem(item, batch);
        return;
    }
    
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
    if (nameInput) {
        nameInput.value = item.name || '';
        nameInput.setAttribute('readonly', 'readonly');
        nameInput.style.background = 'transparent';
    }
    if (batchInput) batchInput.value = batch.batch_no || '';
    if (expiryInput) expiryInput.value = expiryDisplay;
    if (rateInput) rateInput.value = rate.toFixed(2);
    if (mrpInput) mrpInput.value = parseFloat(batch.avg_mrp || batch.mrp || item.mrp || 0).toFixed(2);
    
    // Store item data in row attributes
    row.setAttribute('data-item-id', item.id || '');
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
    
    // Store batch ID for quantity reduction
    const batchId = batch.id ? parseInt(batch.id) : '';
    if (batchId) {
        row.setAttribute('data-batch-id', batchId.toString());
        console.log('✅ Batch ID stored in row:', batchId);
    }
    
    // Update detailed summary
    updateDetailedSummary(rowIndex);
    
    // Focus on qty field
    setTimeout(() => {
        const qtyInput = row.querySelector('input[name*="[qty]"]');
        if (qtyInput) {
            qtyInput.focus();
            qtyInput.select();
        }
    }, 100);
    
    console.log('✅ Row populated successfully');
}

// Add event listeners to row for editing functionality
function addRowEventListeners(row, rowIndex) {
    // Get all input fields in order
    const qtyInput = row.querySelector('input[name*="[qty]"]');
    const freeQtyInput = row.querySelector('input[name*="[free_qty]"]');
    const rateInput = row.querySelector('input[name*="[rate]"]');
    const discountInput = row.querySelector('input[name*="[discount]"]');
    
    // Qty field - Enter moves to Free Qty
    if (qtyInput) {
        qtyInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                calculateRowAmount(rowIndex);
                if (freeQtyInput) freeQtyInput.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                navigateToRow(rowIndex - 1);
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                navigateToRow(rowIndex + 1);
            }
        });
    }
    
    // Free Qty field - Enter moves to Rate
    if (freeQtyInput) {
        freeQtyInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (rateInput) rateInput.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                navigateToRow(rowIndex - 1);
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                navigateToRow(rowIndex + 1);
            }
        });
    }
    
    // Rate field - Enter moves to Discount
    if (rateInput) {
        rateInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                calculateRowAmount(rowIndex);
                if (discountInput) discountInput.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                navigateToRow(rowIndex - 1);
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                navigateToRow(rowIndex + 1);
            }
        });
    }
    
    // Discount field - Enter finalizes row and moves to next
    if (discountInput) {
        discountInput.addEventListener('focus', function() {
            this.setAttribute('data-original-discount', this.value || '0');
        });
        
        discountInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const currentValue = parseFloat(this.value) || 0;
                const originalValue = parseFloat(this.getAttribute('data-original-discount') || 0);
                
                if (currentValue !== originalValue) {
                    showDiscountOptionsModal(rowIndex, currentValue);
                } else {
                    calculateRowAmount(rowIndex);
                    calculateTotal();
                    moveToNextRowCodeField(rowIndex);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                navigateToRow(rowIndex - 1);
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                navigateToRow(rowIndex + 1);
            }
        });
    }
    
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
                if (!itemCode) {
                    // Empty code field - open item modal
                    openChooseItemsModal();
                } else {
                    // Has barcode - fetch item and open batch modal
                    fetchItemByBarcodeAndOpenBatchModal(itemCode, rowIndex);
                }
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


// Select row
function selectRow(rowIndex) {
    // Find the actual row by data-row-index attribute
    const targetRow = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!targetRow) {
        return; // Row doesn't exist
    }
    
    // Remove previous selection from all rows
    const allRows = document.querySelectorAll('#itemsTableBody tr');
    allRows.forEach(r => r.classList.remove('row-selected'));
    
    // Add selection to target row
    targetRow.classList.add('row-selected');
    currentSelectedRowIndex = rowIndex;
    
    // Scroll row into view if needed
    targetRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Update calculation section (HSN, GST, Case, Box)
    updateCalculationSection(rowIndex);
    
    // Update detailed summary section
    updateDetailedSummary(rowIndex);
}

// Update calculation section with current row data
function updateCalculationSection(rowIndex) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) {
        clearCalculationSection();
        return;
    }
    
    // Get item data
    const hsnCode = row.getAttribute('data-hsn-code') || '---';
    const cgst = parseFloat(row.getAttribute('data-cgst') || 0);
    const sgst = parseFloat(row.getAttribute('data-sgst') || 0);
    const cess = parseFloat(row.getAttribute('data-cess') || 0);
    const caseQty = parseFloat(row.getAttribute('data-case-qty') || 0);
    const boxQty = parseFloat(row.getAttribute('data-box-qty') || 0);
    
    // Get current values from inputs
    const qty = parseFloat(document.getElementById(`qty_${rowIndex}`)?.value) || 0;
    const rate = parseFloat(document.getElementById(`rate_${rowIndex}`)?.value) || 0;
    const discount = parseFloat(document.getElementById(`discount_${rowIndex}`)?.value) || 0;
    
    // Calculate total amount (before discount)
    const totalAmount = qty * rate;
    
    // Calculate discount amount
    const discountAmount = totalAmount * (discount / 100);
    
    // Calculate discounted amount (amount after discount)
    const discountedAmount = totalAmount - discountAmount;
    
    // Calculate Case and Box
    const cases = caseQty > 0 ? Math.floor(qty / caseQty) : 0;
    const boxes = boxQty > 0 ? Math.floor((qty % caseQty) / boxQty) : 0;
    
    // Update Case and Box
    document.getElementById('calc_case').value = cases;
    document.getElementById('calc_box').value = boxes;
    
    // Update HSN Code
    document.getElementById('calc_hsn_code').value = hsnCode;
    
    // Update GST percentages
    document.getElementById('calc_cgst').value = cgst.toFixed(2);
    document.getElementById('calc_sgst').value = sgst.toFixed(2);
    document.getElementById('calc_cess').value = cess.toFixed(2);
    
    // Calculate total tax percentage
    const totalTaxPercent = cgst + sgst + cess;
    document.getElementById('calc_tax_percent').value = totalTaxPercent.toFixed(3);
    
    // Calculate GST amounts on DISCOUNTED AMOUNT
    if (discountedAmount > 0) {
        const cgstAmount = (discountedAmount * cgst / 100).toFixed(2);
        const sgstAmount = (discountedAmount * sgst / 100).toFixed(2);
        const cessAmount = (discountedAmount * cess / 100).toFixed(2);
        
        document.getElementById('calc_cgst_amount').textContent = cgstAmount;
        document.getElementById('calc_sgst_amount').textContent = sgstAmount;
        document.getElementById('calc_cess_amount').textContent = cessAmount;
    } else {
        document.getElementById('calc_cgst_amount').textContent = '0.00';
        document.getElementById('calc_sgst_amount').textContent = '0.00';
        document.getElementById('calc_cess_amount').textContent = '0.00';
    }
}

// Clear calculation section
function clearCalculationSection() {
    document.getElementById('calc_case').value = '0';
    document.getElementById('calc_box').value = '0';
    document.getElementById('calc_hsn_code').value = '---';
    document.getElementById('calc_cgst').value = '0';
    document.getElementById('calc_sgst').value = '0';
    document.getElementById('calc_cess').value = '0';
    document.getElementById('calc_tax_percent').value = '0.000';
    document.getElementById('calc_cgst_amount').textContent = '0.00';
    document.getElementById('calc_sgst_amount').textContent = '0.00';
    document.getElementById('calc_cess_amount').textContent = '0.00';
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
    const caseQty = row.getAttribute('data-case-qty') || 0;
    const boxQty = row.getAttribute('data-box-qty') || 0;
    
    // Get batch purchase details
    const batchPurchaseRate = parseFloat(row.getAttribute('data-batch-purchase-rate') || 0);
    
    // Calculate Cost + GST using formula: P.RATE × (1 + TotalGST%)
    const totalGstPercent = parseFloat(cgst) + parseFloat(sgst) + parseFloat(cess);
    const costPlusGst = batchPurchaseRate * (1 + (totalGstPercent / 100));
    
    // Get values from inputs
    const qty = parseFloat(document.getElementById(`qty_${rowIndex}`)?.value) || 0;
    const rate = parseFloat(document.getElementById(`rate_${rowIndex}`)?.value) || 0;
    const discount = parseFloat(document.getElementById(`discount_${rowIndex}`)?.value) || 0;
    
    // ALWAYS show basic fields
    document.getElementById('detailPacking').value = packing;
    document.getElementById('detailUnit').value = unit;
    document.getElementById('detailCompany').value = company;
    document.getElementById('detailCostGst').value = costPlusGst.toFixed(2);
    
    // Get batch code from row
    const batchInput = row.querySelector('input[name*="[batch]"]');
    const batchCodeValue = batchInput ? batchInput.value : '';
    document.getElementById('detailBatchCode').value = batchCodeValue;
    
    // Fetch total quantity from all batches for this item
    const itemId = row.getAttribute('data-item-id');
    if (itemId) {
        fetchTotalBatchQuantity(itemId);
    } else {
        // If no item ID, just show current row quantity
        document.getElementById('detailClQty').value = qty || '';
    }
    
    // Calculate amounts properly
    const ntAmt = qty * rate;  // N.T.Amt = Total amount before discount
    const discountAmt = ntAmt * (discount / 100);  // Discount amount
    const subTot = ntAmt - discountAmt;  // Sub.Tot = Amount after discount
    
    // Calculate tax on DISCOUNTED amount (Sub.Tot)
    const taxAmt = subTot * ((parseFloat(cgst) + parseFloat(sgst) + parseFloat(cess)) / 100);
    
    // Net Amount = Sub.Tot + Tax
    const netAmt = subTot + taxAmt;
    
    // Update detailed summary fields
    document.getElementById('detailNtAmt').value = ntAmt.toFixed(2);  // Total before discount
    document.getElementById('detailDisAmt').value = discountAmt.toFixed(2);  // Discount amount
    document.getElementById('detailSubTot').value = subTot.toFixed(2);  // Sub Total = Total - Discount
    document.getElementById('detailTaxAmt').value = taxAmt.toFixed(2);  // Tax on discounted amount
    document.getElementById('detailNetAmt').value = netAmt.toFixed(2);  // Final net amount
    document.getElementById('detailScAmt').value = '0.00';  // SC Amount (not used)
    document.getElementById('detailScmPercent').value = '0.00';  // Scm % (not used)
    document.getElementById('detailScmAmt').value = '0.00';  // Scm Amount (not used)
    document.getElementById('detailHsAmt').value = '0.00';  // HS Amount (not used)
    document.getElementById('detailLctn').value = '';  // Location (not used)
    document.getElementById('detailVol').value = '0';  // Volume (not used)
    document.getElementById('detailSrIno').value = '';  // Serial no (not used)
    document.getElementById('detailScm1').value = '0';  // Scm 1 (not used)
    document.getElementById('detailScm2').value = '0';  // Scm 2 (not used)
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
    document.getElementById('detailPacking').value = '';
    document.getElementById('detailUnit').value = '1';
    document.getElementById('detailCompany').value = '';
    document.getElementById('detailClQty').value = '';
    document.getElementById('detailLctn').value = '';
    document.getElementById('detailBatchCode').value = '';
    document.getElementById('detailNtAmt').value = '0.00';
    document.getElementById('detailScAmt').value = '0.00';
    document.getElementById('detailDisAmt').value = '0.00';
    document.getElementById('detailHsAmt').value = '0.00';
    document.getElementById('detailScmPercent').value = '0.00';
    document.getElementById('detailScmAmt').value = '0.00';
    document.getElementById('detailSubTot').value = '0.00';
    document.getElementById('detailTaxAmt').value = '0.00';
    document.getElementById('detailNetAmt').value = '0.00';
    document.getElementById('detailCostGst').value = '0.00';
    document.getElementById('detailVol').value = '0';
    document.getElementById('detailSrIno').value = '';
    document.getElementById('detailScm1').value = '0';
    document.getElementById('detailScm2').value = '0';
}

// Calculate row amount
function calculateRowAmount(rowIndex) {
    const qty = parseFloat(document.getElementById(`qty_${rowIndex}`)?.value) || 0;
    const rate = parseFloat(document.getElementById(`rate_${rowIndex}`)?.value) || 0;
    
    // Amount = Qty × Rate ONLY (discount NOT applied here)
    const amount = qty * rate;
    
    document.getElementById(`amount_${rowIndex}`).value = amount.toFixed(2);
    
    // Update row color
    updateRowColor(rowIndex);
    
    // Calculate totals
    calculateTotal();
    
    // Always update summary (not just for complete rows)
    calculateSummary();
    
    // If this is the currently selected row, update calculation & detailed summary
    if (currentSelectedRowIndex === rowIndex) {
        updateCalculationSection(rowIndex);
        updateDetailedSummary(rowIndex);
    }
}

// Check if row is complete (always true when called from moveToNextRow)
function isRowComplete(rowIndex) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) return false;
    
    // If row has any data, consider it complete
    // User can always edit it later
    const code = row.querySelector('input[name*="[code]"]')?.value?.trim();
    const itemName = row.querySelector('input[name*="[item_name]"]')?.value?.trim();
    
    // Row is complete if it has item code or name
    return (code || itemName) ? true : false;
}

// Update row color based on completion
function updateRowColor(rowIndex) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) return;
    
    // Remove old color classes
    row.classList.remove('table-danger', 'table-success');
    
    // Check if row is complete
    if (isRowComplete(rowIndex)) {
        // Mark as complete - GREEN
        row.setAttribute('data-complete', 'true');
        row.classList.add('table-success');
    } else {
        // Mark as incomplete - RED (if has any data)
        const code = row.querySelector('input[name*="[code]"]')?.value?.trim();
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
        
        if (code || qty > 0 || rate > 0) {
            row.setAttribute('data-complete', 'false');
            row.classList.add('table-danger');
        }
    }
}

// Calculate total
function calculateTotal() {
    let total = 0;
    const rows = document.querySelectorAll('#itemsTableBody tr');
    
    rows.forEach(row => {
        const amountInput = row.querySelector('input[name*="[amount]"]');
        if (amountInput) {
            const amount = parseFloat(amountInput.value) || 0;
            total += amount;
        }
    });
    
    const totalEl = document.getElementById('totalAmount');
    if (totalEl) {
        totalEl.value = total.toFixed(2);
    }
}

// Calculate summary (when all rows are complete)
function calculateSummary() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    let totalNtAmt = 0;      // N.T.Amt - Total amount before discount
    let totalDisAmt = 0;     // Dis - Total discount amount
    let totalFTAmt = 0;      // F.T.Amt - Total after discount (before tax)
    let totalTax = 0;        // Tax - Total CGST + SGST + CESS
    let totalNet = 0;        // Net - Final amount
    
    rows.forEach(row => {
        // Count ALL rows that have item data (RED or GREEN doesn't matter)
        const rowIndex = row.getAttribute('data-row-index');
        const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
        const itemName = row.querySelector('input[name*="[item_name]"]')?.value?.trim();
        
        // Only process rows that have item
        if (itemCode || itemName) {
            const qty = parseFloat(document.getElementById(`qty_${rowIndex}`)?.value) || 0;
            const rate = parseFloat(document.getElementById(`rate_${rowIndex}`)?.value) || 0;
            const discount = parseFloat(document.getElementById(`discount_${rowIndex}`)?.value) || 0;
            
            // Calculate amounts for this row
            const rowAmount = qty * rate;  // Amount before discount
            const rowDiscount = rowAmount * (discount / 100);  // Discount amount
            const rowAfterDiscount = rowAmount - rowDiscount;  // Amount after discount
            
            // Get GST percentages
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
        }
    });
    
    // Calculate Net Amount: Amount after discount + Tax
    totalNet = totalFTAmt + totalTax;
    
    // Update summary fields
    document.getElementById('nt_amt').value = totalNtAmt.toFixed(2);  // Total before discount
    document.getElementById('sc_amt').value = '0.00';  // SC (not used in sale)
    document.getElementById('ft_amt').value = totalNtAmt.toFixed(2);  // F.T.Amt = N.T.Amt
    document.getElementById('dis_amt').value = totalDisAmt.toFixed(2);  // Total discount
    document.getElementById('scm_amt').value = '0.00';  // Scm (not used)
    document.getElementById('tax_amt').value = totalTax.toFixed(2);  // Total tax
    document.getElementById('net_amt').value = totalNet.toFixed(2);  // Final net amount
    document.getElementById('scm_percent').value = '0.00';  // Scm % (not used)
}

// Add new empty row
function addNewRow() {
    itemIndex++;
    const tbody = document.getElementById('itemsTableBody');
    
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-row-index', itemIndex);
    newRow.style.cursor = 'pointer';
    newRow.addEventListener('click', function(e) {
        const clickedRow = e.currentTarget;
        const rowIdx = parseInt(clickedRow.getAttribute('data-row-index'));
        selectRow(rowIdx);
    });
    
    newRow.innerHTML = `
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][code]" style="font-size: 10px;" autocomplete="off"></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][item_name]" style="font-size: 10px;" autocomplete="off"></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][batch]" style="font-size: 10px;" autocomplete="off"></td>
        <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][expiry]" style="font-size: 10px;" autocomplete="off"></td>
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
        showAlert('Please select a row to delete', 'warning', 'No Row Selected');
    }
}

// Insert item
function insertItem() {
    openChooseItemsModal();
}

// Save sale transaction
function saveSale() {
    // Collect header data
    const headerData = {
        series: document.getElementById('seriesSelect')?.value || 'SB',
        date: document.getElementById('saleDate')?.value || '',
        invoice_no: document.getElementById('invoiceNo')?.value || '',
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
    
    rows.forEach((row, index) => {
        const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
        const itemName = row.querySelector('input[name*="[item_name]"]')?.value?.trim();
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
        
        const hasItemInfo = itemCode || itemName;
        const hasQuantityOrRate = qty > 0 || rate > 0;
        
        if (hasItemInfo && hasQuantityOrRate) {
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
            console.log('📦 Adding item to sale:', {
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
        showAlert('Please add at least one item.\n\nUse "Choose Items" button to add items.', 'warning', 'Items Required');
        return;
    }
    
    // Prepare final payload
    const payload = {
        ...headerData,
        items: items,
        _token: document.querySelector('input[name="_token"]').value
    };
    
    console.log('=== SAVING SALE TRANSACTION ===');
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
    fetch('{{ route("admin.sale.store") }}', {
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
            showSuccessModalWithReload('Sale Transaction saved successfully!\n\nInvoice No: ' + data.invoice_no, 'Success');
        } else {
            showAlert('Error: ' + (data.message || 'Unknown error'), 'error', 'Save Failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error: ' + error.message + '\n\nCheck browser console for details.', 'error', 'Save Failed');
    });
}

// Fetch next invoice number from server
async function fetchNextInvoiceNo() {
    try {
        const response = await fetch('{{ route("admin.sale.next-invoice-no") }}');
        const data = await response.json();
        if (data.success && data.next_invoice_no) {
            document.getElementById('invoiceNo').value = data.next_invoice_no;
            console.log('Next invoice number updated:', data.next_invoice_no);
        } else {
            // Fallback: increment locally
            const currentInvoiceNo = document.getElementById('invoiceNo').value;
            const match = currentInvoiceNo.match(/INV-(\d+)/);
            if (match) {
                const nextNum = parseInt(match[1]) + 1;
                const nextInvoiceNo = 'INV-' + String(nextNum).padStart(6, '0');
                document.getElementById('invoiceNo').value = nextInvoiceNo;
            }
        }
    } catch (error) {
        console.error('Error fetching next invoice number:', error);
        // Fallback: increment locally
        const currentInvoiceNo = document.getElementById('invoiceNo').value;
        const match = currentInvoiceNo.match(/INV-(\d+)/);
        if (match) {
            const nextNum = parseInt(match[1]) + 1;
            const nextInvoiceNo = 'INV-' + String(nextNum).padStart(6, '0');
            document.getElementById('invoiceNo').value = nextInvoiceNo;
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
document.getElementById('dateRangeBackdrop')?.addEventListener('click', closeDateRangeModal);
document.getElementById('invoicesBackdrop')?.addEventListener('click', closeInvoicesModal);

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeChooseItemsModal();
        closeBatchSelectionModal();
        closeDateRangeModal();
        closeInvoicesModal();
    }
});

// ============================================
// MODIFICATION FUNCTIONALITY - DATE RANGE & INVOICE SELECTION
// ============================================

// Open Date Range Modal
function openDateRangeModal() {
    const modal = document.getElementById('dateRangeModal');
    const backdrop = document.getElementById('dateRangeBackdrop');
    
    // Set default dates (last 30 days)
    const today = new Date();
    const last30Days = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('filterFromDate').value = last30Days.toISOString().split('T')[0];
    document.getElementById('filterToDate').value = today.toISOString().split('T')[0];
    
    modal.classList.add('show');
    backdrop.classList.add('show');
}

// Close Date Range Modal
function closeDateRangeModal() {
    const modal = document.getElementById('dateRangeModal');
    const backdrop = document.getElementById('dateRangeBackdrop');
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
}

// Filter Invoices by Date Range
async function filterInvoicesByDate() {
    const fromDate = document.getElementById('filterFromDate').value;
    const toDate = document.getElementById('filterToDate').value;
    
    if (!fromDate || !toDate) {
        showAlert('Please select both From and To dates', 'warning', 'Date Required');
        return;
    }
    
    if (new Date(fromDate) > new Date(toDate)) {
        showAlert('From date cannot be after To date', 'warning', 'Invalid Date Range');
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
    const modal = document.getElementById('invoicesModal');
    const backdrop = document.getElementById('invoicesBackdrop');
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
}

// Load Invoices (with optional date filter)
async function loadInvoices(fromDate = null, toDate = null) {
    const modal = document.getElementById('invoicesModal');
    const backdrop = document.getElementById('invoicesBackdrop');
    const tbody = document.getElementById('invoicesTableBody');
    const title = document.getElementById('invoicesModalTitle');
    
    // Show modal with loading state
    modal.classList.add('show');
    backdrop.classList.add('show');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm me-2"></div>Loading invoices...</td></tr>';
    
    // Update title based on filter
    if (fromDate && toDate) {
        title.textContent = `Sale Invoices (${formatDate(fromDate)} to ${formatDate(toDate)})`;
    } else {
        title.textContent = 'All Sale Invoices';
    }
    
    try {
        // Build URL with query parameters
        let url = '{{ route("admin.sale.modification.invoices") }}';
        if (fromDate && toDate) {
            url += `?from_date=${fromDate}&to_date=${toDate}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success && data.invoices.length > 0) {
            tbody.innerHTML = '';
            data.invoices.forEach((invoice, index) => {
                const row = `
                    <tr style="cursor: pointer;" onclick="selectInvoice(${invoice.id})" onmouseover="this.style.backgroundColor='#f0f8ff'" onmouseout="this.style.backgroundColor=''">
                        <td class="text-center">${invoice.invoice_no}</td>
                        <td class="text-center">${invoice.sale_date}</td>
                        <td>${invoice.customer_name}</td>
                        <td class="text-end">₹${parseFloat(invoice.net_amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                        <td class="text-center">${invoice.status_badge}</td>
                        <td class="text-center">
                            <span class="badge bg-${invoice.payment_status === 'paid' ? 'success' : invoice.payment_status === 'partial' ? 'warning' : 'danger'} text-uppercase">
                                ${invoice.payment_status}
                            </span>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
            
            // Update total count
            document.getElementById('invoicesTotal').textContent = `Total: ${data.invoices.length} invoice(s)`;
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No invoices found</td></tr>';
            document.getElementById('invoicesTotal').textContent = 'Total: 0 invoice(s)';
        }
    } catch (error) {
        console.error('Error loading invoices:', error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading invoices</td></tr>';
        showAlert('Error loading invoices: ' + error.message, 'error', 'Load Failed');
    }
}

// Select Invoice from the modal and load it
function selectInvoice(invoiceId) {
    console.log('📄 Selecting invoice with ID:', invoiceId);
    
    // Close the modal
    closeInvoicesModal();
    
    // Fetch the transaction by ID and load it
    fetch(`{{ url('/admin/sale/modification/get') }}/${invoiceId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load invoice');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.transaction) {
                // Set the invoice number in the input field
                const invoiceNoInput = document.getElementById('invoiceNo');
                if (invoiceNoInput) {
                    invoiceNoInput.value = data.transaction.invoice_no;
                }
                // Populate the form with transaction data
                populateFormWithTransaction(data.transaction);
                showAlert('Invoice loaded successfully!\n\nYou can now modify the transaction or add more items.', 'success', 'Invoice Loaded');
            } else {
                showAlert('Failed to load invoice: ' + (data.message || 'Unknown error'), 'error', 'Load Failed');
            }
        })
        .catch(error => {
            console.error('Error loading invoice:', error);
            showAlert('Error loading invoice: ' + error.message, 'error', 'Load Failed');
        });
}

// Format date for display (dd/mm/yyyy)
function formatDate(dateStr) {
    if (!dateStr) return '-';
    try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB');
    } catch (e) {
        return dateStr;
    }
}

// Load Transaction by Invoice Number
async function loadTransactionByInvoiceNo(invoiceNo) {
    try {
        // Get invoice number from input if not provided
        const invoiceNoInput = document.getElementById('invoiceNo');
        let invoiceNumber = invoiceNo || invoiceNoInput.value.trim();
        
        // Validate invoice number
        if (!invoiceNumber || invoiceNumber === 'Loading...') {
            showAlert('Please enter a valid invoice number', 'warning', 'Invoice Number Required');
            return false;
        }
        
        // Show loading indicator
        const originalValue = invoiceNoInput.value;
        invoiceNoInput.value = 'Loading...';
        invoiceNoInput.disabled = true;
        
        // Search for invoice by invoice number
        const url = `{{ url('/admin/sale/modification/search') }}?invoice_no=${encodeURIComponent(invoiceNumber)}`;
        const response = await fetch(url);
        
        // Check if response is OK
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            throw new Error(`Server error: ${response.status} ${response.statusText}`);
        }
        
        // Parse JSON response
        const data = await response.json();
        
        // Re-enable input
        invoiceNoInput.disabled = false;
        
        if (data.success && data.transaction) {
            populateFormWithTransaction(data.transaction);
            showAlert('Invoice loaded successfully!\n\nYou can now modify the transaction or add more items.', 'success', 'Invoice Loaded');
            return true;
        } else {
            showAlert('Invoice not found: ' + invoiceNumber + '\n\nPlease check the invoice number and try again.', 'error', 'Invoice Not Found');
            invoiceNoInput.value = originalValue;
            return false;
        }
    } catch (error) {
        console.error('Error loading invoice:', error);
        
        // Re-enable input
        const invoiceNoInput = document.getElementById('invoiceNo');
        invoiceNoInput.disabled = false;
        
        // Restore original value
        const invoiceNoInput2 = document.getElementById('invoiceNo');
        const originalValue = invoiceNoInput2.value === 'Loading...' ? '' : invoiceNoInput2.value;
        invoiceNoInput2.value = originalValue;
        
        showAlert('Error loading invoice: ' + error.message + '\n\nPlease check the invoice number and try again.', 'error', 'Load Failed');
        return false;
    }
}

// Select Invoice and Load Data
async function selectInvoice(transactionId) {
    closeInvoicesModal();
    
    // Show loading indicator
    const invoiceNoInput = document.getElementById('invoiceNo');
    invoiceNoInput.value = 'Loading...';
    
    try {
        const url = `{{ url('/admin/sale/modification') }}/${transactionId}`;
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success && data.transaction) {
            populateFormWithTransaction(data.transaction);
            showAlert('Invoice loaded successfully!\n\nYou can now modify the transaction.', 'success', 'Invoice Loaded');
        } else {
            showAlert('Error: ' + (data.message || 'Failed to load invoice'), 'error', 'Load Failed');
            invoiceNoInput.value = '';
        }
    } catch (error) {
        console.error('Error loading invoice:', error);
        showAlert('Error loading invoice: ' + error.message, 'error', 'Load Failed');
        invoiceNoInput.value = '';
    }
}

// Fetch customer name by ID
async function fetchCustomerName(customerId) {
    if (!customerId) return '';
    
    try {
        // Find customer in the dropdown options first
        const customerSelect = document.getElementById('customerSelect');
        if (customerSelect) {
            for (let i = 0; i < customerSelect.options.length; i++) {
                if (customerSelect.options[i].value == customerId) {
                    return customerSelect.options[i].getAttribute('data-name') || '';
                }
            }
        }
        
        // If not found in dropdown, we'll use the customer_id as fallback
        return 'Customer ID: ' + customerId;
    } catch (error) {
        console.error('Error fetching customer name:', error);
        return 'Customer ID: ' + customerId;
    }
}

// Fetch salesman name by ID
async function fetchSalesmanName(salesmanId) {
    if (!salesmanId) return '';
    
    try {
        // Find salesman in the dropdown options first
        const salesmanSelect = document.getElementById('salesmanSelect');
        if (salesmanSelect) {
            for (let i = 0; i < salesmanSelect.options.length; i++) {
                if (salesmanSelect.options[i].value == salesmanId) {
                    return salesmanSelect.options[i].getAttribute('data-name') || '';
                }
            }
        }
        
        // If not found in dropdown, we'll use the salesman_id as fallback
        return 'Salesman ID: ' + salesmanId;
    } catch (error) {
        console.error('Error fetching salesman name:', error);
        return 'Salesman ID: ' + salesmanId;
    }
}

// Populate Form with Transaction Data
async function populateFormWithTransaction(transaction) {
    console.log('Populating form with transaction:', transaction);
    
    // Store transaction ID for update
    document.getElementById('transactionId').value = transaction.id;
    
    // Populate header fields
    document.getElementById('invoiceNo').value = transaction.invoice_no;
    document.getElementById('seriesSelect').value = transaction.series || 'SB';
    document.getElementById('saleDate').value = transaction.sale_date;
    document.getElementById('dueDate').value = transaction.due_date || transaction.sale_date;
    
    // Populate customer and salesman data
    const customerSelect = document.getElementById('customerSelect');
    const salesmanSelect = document.getElementById('salesmanSelect');
    
    // Handle customer data
    const customerId = transaction.customer_id || '';
    let customerName = transaction.customer_name || (transaction.customer && transaction.customer.name) || '';
    let customerCode = transaction.customer_code || (transaction.customer && transaction.customer.code) || '';
    
    // Debug: Customer data
    console.log('Customer ID:', customerId, 'Name:', customerName);
    
    // If customer name is missing, fetch it
    if (customerId && !customerName) {
        customerName = await fetchCustomerName(customerId);
    }
    
    if (customerId && customerSelect) {
        // Temporarily enable to set value
        customerSelect.disabled = false;
        
        // Find and select the customer option
        let customerFound = false;
        for (let i = 0; i < customerSelect.options.length; i++) {
            if (customerSelect.options[i].value == customerId) {
                customerSelect.selectedIndex = i;
                customerFound = true;
                console.log('✅ Customer selected:', customerSelect.options[i].text);
                // Get the actual name and code from dropdown if missing
                if (!customerName) {
                    customerName = customerSelect.options[i].getAttribute('data-name') || '';
                }
                if (!customerCode) {
                    customerCode = customerSelect.options[i].getAttribute('data-code') || '';
                }
                break;
            }
        }
        
        // If customer not found in dropdown, add it
        if (!customerFound && customerName) {
            console.log('Adding new customer option:', customerName);
            const customerOptionText = (customerCode ? customerCode + ' - ' : '') + customerName;
            const newOption = new Option(customerOptionText, customerId, true, true);
            newOption.setAttribute('data-name', customerName);
            newOption.setAttribute('data-code', customerCode);
            customerSelect.add(newOption);
        }
        
        // Force the selection to show
        if (customerFound || (!customerFound && customerName)) {
            customerSelect.value = customerId;
            // Force display update
            customerSelect.dispatchEvent(new Event('change'));
        }
        
        // Add a small delay before disabling to ensure selection sticks
        setTimeout(() => {
            customerSelect.disabled = true;
            console.log('Customer dropdown disabled. Current value:', customerSelect.value, customerSelect.options[customerSelect.selectedIndex]?.text);
        }, 100);
    }
    
    // Handle salesman data
    const salesmanId = transaction.salesman_id || '';
    let salesmanName = transaction.salesman_name || (transaction.salesman && transaction.salesman.name) || '';
    let salesmanCode = transaction.salesman_code || (transaction.salesman && transaction.salesman.code) || '';
    
    // Debug: Salesman data
    console.log('Salesman ID:', salesmanId, 'Name:', salesmanName);
    
    // If salesman name is missing, fetch it
    if (salesmanId && !salesmanName) {
        salesmanName = await fetchSalesmanName(salesmanId);
    }
    
    if (salesmanId && salesmanSelect) {
        // Temporarily enable to set value
        salesmanSelect.disabled = false;
        
        // Find and select the salesman option
        let salesmanFound = false;
        for (let i = 0; i < salesmanSelect.options.length; i++) {
            if (salesmanSelect.options[i].value == salesmanId) {
                salesmanSelect.selectedIndex = i;
                salesmanFound = true;
                console.log('✅ Salesman selected:', salesmanSelect.options[i].text);
                // Get the actual name and code from dropdown if missing
                if (!salesmanName) {
                    salesmanName = salesmanSelect.options[i].getAttribute('data-name') || '';
                }
                if (!salesmanCode) {
                    salesmanCode = salesmanSelect.options[i].getAttribute('data-code') || '';
                }
                break;
            }
        }
        
        // If salesman not found in dropdown, add it
        if (!salesmanFound && salesmanName) {
            console.log('Adding new salesman option:', salesmanName);
            const salesmanOptionText = (salesmanCode ? salesmanCode + ' - ' : '') + salesmanName;
            const newOption = new Option(salesmanOptionText, salesmanId, true, true);
            newOption.setAttribute('data-name', salesmanName);
            newOption.setAttribute('data-code', salesmanCode);
            salesmanSelect.add(newOption);
        }
        
        // Force the selection to show
        if (salesmanFound || (!salesmanFound && salesmanName)) {
            salesmanSelect.value = salesmanId;
            // Force display update
            salesmanSelect.dispatchEvent(new Event('change'));
        }
        
        // Add a small delay before disabling to ensure selection sticks
        setTimeout(() => {
            salesmanSelect.disabled = true;
            console.log('Salesman dropdown disabled. Current value:', salesmanSelect.value, salesmanSelect.options[salesmanSelect.selectedIndex]?.text);
        }, 100);
    }
    
    document.getElementById('cash').value = transaction.cash_flag || 'N';
    document.getElementById('transfer').value = transaction.transfer_flag || 'N';
    document.getElementById('remarks').value = transaction.remarks || '';
    
    // Populate summary amounts from transaction
    document.getElementById('nt_amt').value = parseFloat(transaction.nt_amount || 0).toFixed(2);
    document.getElementById('sc_amt').value = parseFloat(transaction.sc_amount || 0).toFixed(2);
    document.getElementById('ft_amt').value = parseFloat(transaction.ft_amount || 0).toFixed(2);
    document.getElementById('dis_amt').value = parseFloat(transaction.dis_amount || 0).toFixed(2);
    document.getElementById('scm_amt').value = parseFloat(transaction.scm_amount || 0).toFixed(2);
    document.getElementById('tax_amt').value = parseFloat(transaction.tax_amount || 0).toFixed(2);
    document.getElementById('net_amt').value = parseFloat(transaction.net_amount || 0).toFixed(2);
    document.getElementById('scm_percent').value = parseFloat(transaction.scm_percent || 0).toFixed(2);
    
    // totalAmount element may not exist in modification page
    const totalAmountEl = document.getElementById('totalAmount');
    if (totalAmountEl) {
        totalAmountEl.value = parseFloat(transaction.net_amount || 0).toFixed(2);
    }
    
    // Update UI states
    updateDayName();
    updateInvoiceType();
    checkChooseItemsButtonState();
    
    // Clear existing items
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    itemIndex = -1; // Reset item index
    
    // Debug: Log items data
    console.log('Transaction items to populate:', transaction.items);
    console.log('Items count:', transaction.items ? transaction.items.length : 0);
    
    // Populate items
    if (transaction.items && transaction.items.length > 0) {
        transaction.items.forEach((item, index) => {
            itemIndex++;
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-row-index', itemIndex);
            newRow.setAttribute('data-item-id', item.item_id || '');
            newRow.style.cursor = 'pointer';
            
            const rowIdx = itemIndex;
            newRow.addEventListener('click', function(e) {
                const clickedRow = e.currentTarget;
                const rowIndex = parseInt(clickedRow.getAttribute('data-row-index'));
                selectRow(rowIndex);
            });
            
            // Create row HTML
            newRow.innerHTML = `
                <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][code]" value="${item.item_code || ''}" style="font-size: 10px;" autocomplete="off"></td>
                <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][item_name]" value="${item.item_name || ''}" style="font-size: 10px;" autocomplete="off"></td>
                <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][batch]" value="${item.batch_no || ''}" style="font-size: 10px;" autocomplete="off"></td>
                <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][expiry]" value="${item.expiry_date || ''}" style="font-size: 10px;" autocomplete="off"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-qty" name="items[${itemIndex}][qty]" id="qty_${itemIndex}" value="${item.qty || 0}" placeholder="0" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][free_qty]" id="free_qty_${itemIndex}" value="${item.free_qty || 0}" style="font-size: 10px;"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-rate" name="items[${itemIndex}][rate]" id="rate_${itemIndex}" value="${item.sale_rate || 0}" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-discount" name="items[${itemIndex}][discount]" id="discount_${itemIndex}" value="${item.discount_percent > 0 ? item.discount_percent : ''}" placeholder="0" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][mrp]" id="mrp_${itemIndex}" value="${item.mrp || 0}" step="0.01" style="font-size: 10px;" readonly></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][amount]" id="amount_${itemIndex}" value="${item.amount || 0}" style="font-size: 10px;" readonly></td>
                <td class="p-0 text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${itemIndex})" title="Delete Row" style="font-size: 9px; padding: 2px 5px;">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            
            // Store item attributes for calculations
            newRow.setAttribute('data-hsn-code', item.hsn_code || '');
            newRow.setAttribute('data-cgst', item.cgst_percent || 0);
            newRow.setAttribute('data-sgst', item.sgst_percent || 0);
            newRow.setAttribute('data-cess', item.cess_percent || 0);
            newRow.setAttribute('data-packing', item.packing || '');
            newRow.setAttribute('data-unit', item.unit || '1');
            newRow.setAttribute('data-company', item.company_name || '');
            newRow.setAttribute('data-batch-code', item.batch_no || '');
            newRow.setAttribute('data-case-qty', item.case_qty || 0);
            newRow.setAttribute('data-box-qty', item.box_qty || 0);
            
            // Store batch_id if available
            if (item.batch_id) {
                newRow.setAttribute('data-batch-id', item.batch_id);
            }
            
            // Mark row as complete
            newRow.setAttribute('data-complete', 'true');
            newRow.classList.add('table-success');
            
            tbody.appendChild(newRow);
            
            // Add event listeners
            addRowEventListeners(newRow, itemIndex);
            
            console.log(`✅ Row ${index} added: ${item.item_name} - Qty: ${item.qty}`);
        });
        
        console.log(`✅ Total ${transaction.items.length} items populated in table`);
    } else {
        console.warn('⚠️ No items found in transaction to populate');
    }
    
    // Don't auto-calculate - use values from database
    // The summary values are already populated above from transaction data
    
    // Select first row if available to show detailed summary
    if (transaction.items && transaction.items.length > 0) {
        setTimeout(() => {
            selectRow(0);
            // Populate detailed summary for first item
            updateDetailedSummary(0);
            updateCalculationSection(0);
        }, 100);
    }
    
    console.log('Form populated successfully with data from database');
    
    // Final check - ensure dropdowns maintain their selections
    setTimeout(() => {
        const finalCustomerSelect = document.getElementById('customerSelect');
        const finalSalesmanSelect = document.getElementById('salesmanSelect');
        
        if (transaction.customer_id && finalCustomerSelect) {
            finalCustomerSelect.value = transaction.customer_id;
            console.log('Final check - Customer dropdown value:', finalCustomerSelect.value, finalCustomerSelect.options[finalCustomerSelect.selectedIndex]?.text);
        }
        
        if (transaction.salesman_id && finalSalesmanSelect) {
            finalSalesmanSelect.value = transaction.salesman_id;
            console.log('Final check - Salesman dropdown value:', finalSalesmanSelect.value, finalSalesmanSelect.options[finalSalesmanSelect.selectedIndex]?.text);
        }
    }, 200);
    
    // ============================================
    // TEMP TRANSACTION HANDLING
    // ============================================
    const isTemp = transaction.is_temp || transaction.series === 'TEMP' || (transaction.invoice_no && transaction.invoice_no.startsWith('TEMP-'));
    
    if (isTemp) {
        console.log('🔶 TEMP Transaction detected - activating finalize mode');
        
        // Show receipt section
        const receiptSection = document.getElementById('tempReceiptSection');
        if (receiptSection) {
            receiptSection.style.display = 'block';
        }
        
        // Change save button to Finalize
        const saveBtnText = document.getElementById('saveBtnText');
        const saveBtn = document.getElementById('saveBtn');
        if (saveBtnText) {
            saveBtnText.textContent = 'Finalize Invoice';
        }
        if (saveBtn) {
            saveBtn.classList.remove('btn-primary');
            saveBtn.classList.add('btn-warning');
            saveBtn.innerHTML = '<i class="bi bi-check-circle"></i> <span id="saveBtnText">Finalize Invoice</span>';
        }
        
        // Set series to SB for finalization (will get new invoice number)
        const seriesSelect = document.getElementById('seriesSelect');
        if (seriesSelect) {
            seriesSelect.value = 'SB';
            updateInvoiceType();
        }
        
        // Load receipt images if available
        if (transaction.receipt_path && transaction.receipt_path.trim() !== '') {
            displayTempReceiptImages(transaction.receipt_path);
        } else {
            // No receipt path - show helpful message
            const container = document.getElementById('tempReceiptImages');
            const countBadge = document.getElementById('tempReceiptCount');
            if (container) {
                container.innerHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-camera" style="font-size: 2rem; color: #ffc107;"></i>
                        <p class="mb-1 mt-2" style="font-weight: 500;">No receipts were stored</p>
                        <small>This transaction was created before receipt storage was enabled.<br>Use "Scan More Receipts" to add receipts now.</small>
                    </div>
                `;
            }
            if (countBadge) {
                countBadge.textContent = '0 receipts';
            }
        }
        
        // Store that this is a temp finalization
        window.isTempFinalization = true;
    } else {
        // Normal transaction - hide receipt section
        const receiptSection = document.getElementById('tempReceiptSection');
        if (receiptSection) {
            receiptSection.style.display = 'none';
        }
        window.isTempFinalization = false;
    }
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

// Modify the saveSale function to handle updates
const originalSaveSale = saveSale;
saveSale = function() {
    const transactionId = document.getElementById('transactionId').value;
    
    if (transactionId) {
        // Update existing transaction
        updateSale(transactionId);
    } else {
        // This is modification page, shouldn't create new
        showAlert('Please select an invoice to modify.\n\nUse "Filter by Date" or "All Invoices" button to select an invoice.', 'warning', 'Invoice Required');
    }
};

// Update Sale Transaction
async function updateSale(transactionId) {
    // Collect header data
    const headerData = {
        series: document.getElementById('seriesSelect')?.value || 'SB',
        date: document.getElementById('saleDate')?.value || '',
        invoice_no: document.getElementById('invoiceNo')?.value || '',
        due_date: document.getElementById('dueDate')?.value || null,
        customer_id: document.getElementById('customerSelect')?.value || '',
        salesman_id: document.getElementById('salesmanSelect')?.value || null,
        cash: document.getElementById('cash')?.value || 'N',
        transfer: document.getElementById('transfer')?.value || 'N',
        remarks: document.getElementById('remarks')?.value || '',
        
        // Summary amounts
        nt_amount: parseFloat(document.getElementById('nt_amt')?.value) || 0,
        sc_amount: parseFloat(document.getElementById('sc_amt')?.value) || 0,
        ft_amount: parseFloat(document.getElementById('ft_amt')?.value) || 0,
        dis_amount: parseFloat(document.getElementById('dis_amt')?.value) || 0,
        scm_amount: parseFloat(document.getElementById('scm_amt')?.value) || 0,
        tax_amount: parseFloat(document.getElementById('tax_amt')?.value) || 0,
        net_amount: parseFloat(document.getElementById('net_amt')?.value) || 0,
        scm_percent: parseFloat(document.getElementById('scm_percent')?.value) || 0,
        tcs_amount: 0,
        excise_amount: 0,
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
    
    // Collect items data
    const items = [];
    const rows = document.querySelectorAll('#itemsTableBody tr');
    
    rows.forEach((row, index) => {
        const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
        const itemName = row.querySelector('input[name*="[item_name]"]')?.value?.trim();
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const rate = parseFloat(row.querySelector('input[name*="[rate]"]')?.value) || 0;
        
        const hasItemInfo = itemCode || itemName;
        const hasQuantityOrRate = qty > 0 || rate > 0;
        
        if (hasItemInfo && hasQuantityOrRate) {
            let batchId = row.getAttribute('data-batch-id');
            if (batchId) {
                batchId = parseInt(batchId);
                if (isNaN(batchId)) batchId = null;
            } else {
                batchId = null;
            }
            
            items.push({
                item_code: itemCode || '',
                item_name: itemName || '',
                batch: row.querySelector('input[name*="[batch]"]')?.value?.trim() || '',
                batch_id: batchId,
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
        showAlert('Please add at least one item.', 'warning', 'Items Required');
        return;
    }
    
    // Prepare payload
    const payload = {
        ...headerData,
        items: items,
        _token: document.querySelector('input[name="_token"]').value
    };
    
    console.log('=== UPDATING SALE TRANSACTION ===');
    console.log('Transaction ID:', transactionId);
    console.log('Payload:', payload);
    
    try {
        const url = `{{ url('/admin/sale/modification') }}/${transactionId}`;
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccessModalWithReload('Sale Transaction updated successfully!\n\nInvoice No: ' + data.invoice_no + '\n\nPage will refresh now.', 'Update Successful');
        } else {
            showAlert('Error: ' + (data.message || 'Unknown error'), 'error', 'Update Failed');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error: ' + error.message, 'error', 'Update Failed');
    }
}
</script>

<!-- Date Range Filter Modal -->
<div id="dateRangeModal" class="pending-orders-modal">
    <div class="pending-orders-content" style="max-width: 500px;">
        <div class="pending-orders-header">
            <h5 class="pending-orders-title">Filter Sale Invoices by Date</h5>
            <button type="button" class="btn-close-modal" onclick="closeDateRangeModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body p-4">
            <div class="mb-3">
                <label class="form-label"><strong>From Date:</strong></label>
                <input type="date" class="form-control" id="filterFromDate">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>To Date:</strong></label>
                <input type="date" class="form-control" id="filterToDate">
            </div>
        </div>
        <div class="pending-orders-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDateRangeModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="filterInvoicesByDate()">
                <i class="bi bi-search"></i> Search Invoices
            </button>
        </div>
    </div>
</div>
<div id="dateRangeBackdrop" class="pending-orders-backdrop"></div>

<!-- Sale Invoices List Modal -->
<div id="invoicesModal" class="pending-orders-modal" style="max-width: 800px;">
    <div class="pending-orders-content">
        <div class="pending-orders-header">
            <h5 class="pending-orders-title" id="invoicesModalTitle">List of Sale Invoices as on: {{ date('d-M-y') }}</h5>
            <button type="button" class="btn-close-modal" onclick="closeInvoicesModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body">
            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 11px; min-width: 100%;">
                    <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                        <tr>
                            <th class="text-center" style="width: 16%; font-size: 11px; padding: 8px 4px;">Invoice No.</th>
                            <th class="text-center" style="width: 12%; font-size: 11px; padding: 8px 4px;">Date</th>
                            <th style="width: 24%; font-size: 11px; padding: 8px 4px;">Customer Name</th>
                            <th class="text-end" style="width: 14%; font-size: 11px; padding: 8px 4px;">Amount</th>
                            <th class="text-center" style="width: 17%; font-size: 11px; padding: 8px 4px;">Status</th>
                            <th class="text-center" style="width: 17%; font-size: 11px; padding: 8px 4px;">Payment</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesTableBody">
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="spinner-border spinner-border-sm me-2"></div>Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pending-orders-footer">
            <div class="flex-grow-1">
                <strong id="invoicesTotal">Total: 0 invoice(s)</strong>
            </div>
            <button type="button" class="btn btn-secondary" onclick="closeInvoicesModal()">Close</button>
        </div>
    </div>
</div>
<div id="invoicesBackdrop" class="pending-orders-backdrop"></div>

<script>
// Initialize backdrop click listeners for new modals
document.getElementById('dateRangeBackdrop')?.addEventListener('click', closeDateRangeModal);
document.getElementById('invoicesBackdrop')?.addEventListener('click', closeInvoicesModal);

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

// Close alert modal with Enter or Escape key
document.addEventListener('keydown', function(e) {
    const alertModal = document.getElementById('alertModal');
    if (alertModal && alertModal.classList.contains('show')) {
        if (e.key === 'Enter' || e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            
            // Check if it's a reload modal (has reloadPageAfterSuccess button)
            const reloadBtn = alertModal.querySelector('button[onclick*="reloadPageAfterSuccess"]');
            if (reloadBtn && e.key === 'Enter') {
                reloadPageAfterSuccess();
                return;
            }
            
            // Check if it's a confirmation modal (has Yes/No buttons)
            const yesBtn = alertModal.querySelector('button[onclick*="handleConfirmYes"]');
            const noBtn = alertModal.querySelector('button[onclick*="handleConfirmCancel"]');
            
            if (yesBtn && noBtn) {
                // For confirm dialogs, Enter = Yes, Escape = No
                if (e.key === 'Enter') {
                    handleConfirmYes();
                } else {
                    handleConfirmCancel();
                }
            } else {
                // For simple alerts, both Enter and Escape close it
                closeAlert();
            }
        }
    }
}, true); // Use capture phase to run before other handlers

// ============================================
// DISCOUNT OPTIONS MODAL FUNCTIONS
// ============================================

let currentDiscountRowIndex = null;
let companyDiscounts = {};

function showDiscountOptionsModal(rowIndex, discountValue) {
    currentDiscountRowIndex = rowIndex;
    const row = document.querySelector(`#itemsTableBody tr[data-row-index="${rowIndex}"]`);
    const itemName = row?.querySelector('input[name*="[item_name]"]')?.value || 'Unknown Item';
    const companyName = row?.getAttribute('data-company-name') || 'Unknown Company';
    
    document.getElementById('discountItemName').textContent = itemName;
    document.getElementById('discountCompanyName').textContent = companyName;
    
    if (discountValue === 0) {
        document.getElementById('discountValue').textContent = 'Remove Discount';
        document.getElementById('discountValue').style.color = '#dc3545';
    } else {
        document.getElementById('discountValue').textContent = discountValue + '%';
        document.getElementById('discountValue').style.color = '#28a745';
    }
    
    document.getElementById('discountOptionsBackdrop').style.display = 'block';
    document.getElementById('discountOptionsModal').style.display = 'block';
    setTimeout(() => {
        document.getElementById('discountOptionsBackdrop').classList.add('show');
        document.getElementById('discountOptionsModal').classList.add('show');
    }, 10);
}

function closeDiscountOptionsModal() {
    document.getElementById('discountOptionsBackdrop').classList.remove('show');
    document.getElementById('discountOptionsModal').classList.remove('show');
    setTimeout(() => {
        document.getElementById('discountOptionsBackdrop').style.display = 'none';
        document.getElementById('discountOptionsModal').style.display = 'none';
    }, 300);
    
    if (currentDiscountRowIndex !== null) {
        calculateRowAmount(currentDiscountRowIndex);
        calculateTotal();
        moveToNextRow(currentDiscountRowIndex);
        currentDiscountRowIndex = null;
    }
}

function applyDiscountOption(option) {
    const rowIndex = currentDiscountRowIndex;
    const row = document.querySelector(`#itemsTableBody tr[data-row-index="${rowIndex}"]`);
    const discountValue = parseFloat(document.getElementById(`discount_${rowIndex}`)?.value) || 0;
    const itemId = row?.getAttribute('data-item-id');
    const companyId = row?.getAttribute('data-company-id');
    const companyName = row?.getAttribute('data-company-name') || '';
    
    const isRemoval = discountValue === 0;
    const actionText = isRemoval ? 'removed' : `set to ${discountValue}%`;
    
    disableDiscountModalButtons();
    
    switch(option) {
        case 'temporary':
            row?.setAttribute('data-original-discount', discountValue.toString());
            showToast(`Discount ${actionText} temporarily`, 'success');
            closeDiscountOptionsModal();
            enableDiscountModalButtons();
            break;
            
        case 'company':
            if (companyId) {
                showToast('Saving discount to company...', 'info');
                saveDiscountToCompany(companyId, discountValue, function(success) {
                    if (success) {
                        companyDiscounts[companyId] = discountValue;
                        applyCompanyDiscountToAllRows(companyId, discountValue);
                        row?.setAttribute('data-original-discount', discountValue.toString());
                        showToast(isRemoval ? `✅ Discount removed for company: ${companyName}` : `✅ Discount ${discountValue}% saved for company: ${companyName}`, 'success');
                    } else {
                        showToast('❌ Failed to save discount to company', 'error');
                    }
                    closeDiscountOptionsModal();
                    enableDiscountModalButtons();
                });
            } else {
                showToast('Company not found for this item', 'warning');
                closeDiscountOptionsModal();
                enableDiscountModalButtons();
            }
            break;
            
        case 'item':
            if (itemId) {
                showToast('Saving discount to item...', 'info');
                saveDiscountToItem(itemId, discountValue, function(success) {
                    if (success) {
                        row?.setAttribute('data-original-discount', discountValue.toString());
                        showToast(isRemoval ? '✅ Discount removed permanently for this item' : `✅ Discount ${discountValue}% saved permanently for this item`, 'success');
                    } else {
                        showToast('❌ Failed to save discount to item', 'error');
                    }
                    closeDiscountOptionsModal();
                    enableDiscountModalButtons();
                });
            } else {
                showToast('Item ID not found', 'warning');
                closeDiscountOptionsModal();
                enableDiscountModalButtons();
            }
            break;
    }
}

function disableDiscountModalButtons() {
    ['discountBtnTemporary', 'discountBtnCompany', 'discountBtnItem'].forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; btn.style.cursor = 'not-allowed'; }
    });
}

function enableDiscountModalButtons() {
    ['discountBtnTemporary', 'discountBtnCompany', 'discountBtnItem'].forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer'; }
    });
}

function applyCompanyDiscountToAllRows(companyId, discountValue) {
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        if (row.getAttribute('data-company-id') == companyId) {
            const rowIndex = row.getAttribute('data-row-index');
            const discountInput = document.getElementById(`discount_${rowIndex}`);
            if (discountInput) {
                discountInput.value = discountValue;
                row.setAttribute('data-original-discount', discountValue.toString());
                calculateRowAmount(parseInt(rowIndex));
            }
        }
    });
    calculateTotal();
}

function saveDiscountToCompany(companyId, discountValue, callback) {
    fetch('{{ route("admin.sale.saveCompanyDiscount") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ company_id: companyId, discount_percent: discountValue })
    })
    .then(response => response.json())
    .then(data => { if (callback) callback(data.success); })
    .catch(error => { console.error('Error:', error); if (callback) callback(false); });
}

function saveDiscountToItem(itemId, discountValue, callback) {
    fetch('{{ route("admin.sale.saveItemDiscount") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ item_id: itemId, discount_percent: discountValue })
    })
    .then(response => response.json())
    .then(data => { if (callback) callback(data.success); })
    .catch(error => { console.error('Error:', error); if (callback) callback(false); });
}

document.getElementById('discountOptionsBackdrop')?.addEventListener('click', closeDiscountOptionsModal);

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

<!-- Discount Options Modal -->
<div id="discountOptionsBackdrop" style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; background: rgba(0, 0, 0, 0.7) !important; z-index: 99998 !important; opacity: 1 !important;"></div>
<div id="discountOptionsModal" style="display: none; position: fixed !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; max-width: 400px !important; width: 90% !important; z-index: 99999 !important; background: #ffffff !important; border-radius: 8px !important; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5) !important; opacity: 1 !important;">
    <div style="padding: 1rem 1.5rem !important; background: #6c5ce7 !important; color: white !important; border-radius: 8px 8px 0 0 !important; display: flex !important; justify-content: space-between !important; align-items: center !important;">
        <h5 style="margin: 0 !important; font-size: 1.1rem !important; font-weight: 600 !important; color: white !important;"><i class="bi bi-percent me-2"></i>Discount Options</h5>
        <button type="button" style="background: transparent !important; border: none !important; color: white !important; font-size: 1.5rem !important; cursor: pointer !important;" onclick="closeDiscountOptionsModal()">×</button>
    </div>
    <div style="padding: 1.5rem !important; background: #ffffff !important;">
        <div class="text-center mb-3">
            <div class="mb-2">
                <strong>Item:</strong> <span id="discountItemName" style="color: #0d6efd;">-</span>
            </div>
            <div class="mb-2">
                <strong>Company:</strong> <span id="discountCompanyName" style="color: #0dcaf0;">-</span>
            </div>
            <div class="mb-3">
                <strong>Action:</strong> <span id="discountValue" style="font-size: 1.25rem; font-weight: bold;">0%</span>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button type="button" id="discountBtnTemporary" class="btn btn-outline-secondary" onclick="applyDiscountOption('temporary')">
                <i class="bi bi-clock me-2"></i> Temporary Change
                <small class="d-block text-muted">Only for this transaction</small>
            </button>
            <button type="button" id="discountBtnCompany" class="btn btn-outline-info" onclick="applyDiscountOption('company')">
                <i class="bi bi-building me-2"></i> Save to Company
                <small class="d-block text-muted">Apply to all items of this company</small>
            </button>
            <button type="button" id="discountBtnItem" class="btn btn-outline-success" onclick="applyDiscountOption('item')">
                <i class="bi bi-box-seam me-2"></i> Save to Item
                <small class="d-block text-muted">Apply permanently to this item only</small>
            </button>
        </div>
    </div>
    <div style="padding: 1rem 1.5rem !important; background: #f8f9fa !important; border-top: 1px solid #dee2e6 !important; border-radius: 0 0 8px 8px !important; text-align: right !important;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeDiscountOptionsModal()">Cancel</button>
    </div>
</div>

<!-- ============================================ -->
<!-- RECEIPT UPLOAD MODAL FUNCTIONS -->
<!-- ============================================ -->
<script>
let selectedReceiptFile = null;

function openReceiptUploadModal() {
    const modal = document.getElementById('receiptUploadModal');
    const backdrop = document.getElementById('receiptUploadBackdrop');
    clearReceiptFile();
    document.getElementById('receiptItemDescription').value = '';
    setTimeout(() => {
        modal.classList.add('show');
        backdrop.classList.add('show');
    }, 10);
}

function closeReceiptUploadModal() {
    const modal = document.getElementById('receiptUploadModal');
    const backdrop = document.getElementById('receiptUploadBackdrop');
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    clearReceiptFile();
    stopCamera();
    showUploadTab();
}

let cameraStream = null;

function showUploadTab() {
    document.getElementById('uploadTabBtn').classList.add('btn-purple');
    document.getElementById('uploadTabBtn').classList.remove('btn-outline-secondary');
    document.getElementById('scanTabBtn').classList.remove('btn-purple');
    document.getElementById('scanTabBtn').classList.add('btn-outline-secondary');
    document.getElementById('scannerTabBtn').classList.remove('btn-purple');
    document.getElementById('scannerTabBtn').classList.add('btn-outline-secondary');
    document.getElementById('receiptDropZone').style.display = 'block';
    document.getElementById('cameraScanArea').style.display = 'none';
    document.getElementById('physicalScannerArea').style.display = 'none';
    stopCamera();
}

function showScanTab() {
    document.getElementById('scanTabBtn').classList.add('btn-purple');
    document.getElementById('scanTabBtn').classList.remove('btn-outline-secondary');
    document.getElementById('uploadTabBtn').classList.remove('btn-purple');
    document.getElementById('uploadTabBtn').classList.add('btn-outline-secondary');
    document.getElementById('scannerTabBtn').classList.remove('btn-purple');
    document.getElementById('scannerTabBtn').classList.add('btn-outline-secondary');
    document.getElementById('receiptDropZone').style.display = 'none';
    document.getElementById('cameraScanArea').style.display = 'block';
    document.getElementById('physicalScannerArea').style.display = 'none';
    startCamera();
}

async function startCamera() {
    try {
        const video = document.getElementById('cameraVideo');
        const constraints = { video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } } };
        cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = cameraStream;
    } catch (error) {
        console.error('Camera access error:', error);
        showAlert('Could not access camera.', 'warning', 'Camera Error');
        showUploadTab();
    }
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
    const video = document.getElementById('cameraVideo');
    if (video) video.srcObject = null;
}

function capturePhoto() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    if (!video.srcObject) {
        showAlert('Camera not ready.', 'warning', 'Capture Error');
        return;
    }
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    canvas.toBlob(function(blob) {
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        const capturedFile = new File([blob], `receipt_scan_${timestamp}.jpg`, { type: 'image/jpeg' });
        
        // Add to preview section
        addReceiptToPreviewSection(capturedFile, 'Camera Capture');
        
        // Close modal
        closeReceiptUploadModal();
        
        // Open OCR preview immediately
        if (typeof openReceiptOCRPreview === 'function') {
            openReceiptOCRPreview(capturedFile, {
                ocrApiUrl: '{{ route("admin.api.ocr.extract") }}',
                itemSearchUrl: '{{ route("admin.api.ocr.search-items") }}',
                batchApiUrl: '{{ url("admin/api/item-batches") }}',
                csrfToken: '{{ csrf_token() }}'
            });
        } else {
            showAlert('Receipt captured! Click on it to extract items.', 'success', 'Photo Captured');
        }
    }, 'image/jpeg', 0.9);
}

const SCANNER_SERVICE_URL = 'http://localhost:51234';
let scannerServiceConnected = false;
let availableScanners = [];

function showScannerTab() {
    document.getElementById('scannerTabBtn').classList.add('btn-purple');
    document.getElementById('scannerTabBtn').classList.remove('btn-outline-secondary');
    document.getElementById('uploadTabBtn').classList.remove('btn-purple');
    document.getElementById('uploadTabBtn').classList.add('btn-outline-secondary');
    document.getElementById('scanTabBtn').classList.remove('btn-purple');
    document.getElementById('scanTabBtn').classList.add('btn-outline-secondary');
    document.getElementById('receiptDropZone').style.display = 'none';
    document.getElementById('cameraScanArea').style.display = 'none';
    document.getElementById('physicalScannerArea').style.display = 'block';
    stopCamera();
    checkScannerService();
}

async function checkScannerService() {
    const statusDiv = document.getElementById('scannerServiceStatus');
    const scannerSelect = document.getElementById('scannerSelect');
    const triggerScanBtn = document.getElementById('triggerScanBtn');
    try {
        const response = await fetch(`${SCANNER_SERVICE_URL}/api/status`, { method: 'GET', headers: { 'Accept': 'application/json' }, signal: AbortSignal.timeout(3000) });
        if (response.ok) {
            scannerServiceConnected = true;
            statusDiv.className = 'alert alert-success py-2 mb-3';
            statusDiv.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Scanner service connected';
            detectScanners();
        } else throw new Error('Service not responding');
    } catch (error) {
        scannerServiceConnected = false;
        statusDiv.className = 'alert alert-warning py-2 mb-3';
        statusDiv.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> Scanner service not running.`;
        scannerSelect.innerHTML = '<option value="">-- Service Not Available --</option>';
        scannerSelect.disabled = true;
        triggerScanBtn.disabled = true;
    }
}

async function detectScanners() {
    const scannerSelect = document.getElementById('scannerSelect');
    const triggerScanBtn = document.getElementById('triggerScanBtn');
    scannerSelect.innerHTML = '<option value="">-- Detecting Scanners --</option>';
    scannerSelect.disabled = true;
    try {
        const response = await fetch(`${SCANNER_SERVICE_URL}/api/scanners`, { method: 'GET', headers: { 'Accept': 'application/json' } });
        if (response.ok) {
            const data = await response.json();
            availableScanners = data.scanners || [];
            if (availableScanners.length > 0) {
                scannerSelect.innerHTML = '';
                availableScanners.forEach((scanner, index) => {
                    const option = document.createElement('option');
                    option.value = scanner.id || index;
                    option.textContent = scanner.name || `Scanner ${index + 1}`;
                    scannerSelect.appendChild(option);
                });
                scannerSelect.disabled = false;
                triggerScanBtn.disabled = false;
            } else {
                scannerSelect.innerHTML = '<option value="">-- No Scanners Found --</option>';
                triggerScanBtn.disabled = true;
            }
        }
    } catch (error) {
        console.error('Error detecting scanners:', error);
        scannerSelect.innerHTML = '<option value="">-- Detection Failed --</option>';
        triggerScanBtn.disabled = true;
    }
}

async function triggerScan() {
    const scannerSelect = document.getElementById('scannerSelect');
    const progressDiv = document.getElementById('scanProgress');
    const triggerScanBtn = document.getElementById('triggerScanBtn');
    const selectedScanner = scannerSelect.value;
    const dpi = document.querySelector('input[name="scanQuality"]:checked')?.value || '200';
    progressDiv.style.display = 'block';
    triggerScanBtn.disabled = true;
    try {
        const response = await fetch(`${SCANNER_SERVICE_URL}/api/scan`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ scanner_id: selectedScanner || 'default', dpi: parseInt(dpi), color_mode: 'color', format: 'jpeg' })
        });
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.image) {
                const byteCharacters = atob(data.image);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) byteNumbers[i] = byteCharacters.charCodeAt(i);
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], { type: 'image/jpeg' });
                const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
                const scannedFile = new File([blob], `scanned_receipt_${timestamp}.jpg`, { type: 'image/jpeg' });
                
                // Add to preview section
                addReceiptToPreviewSection(scannedFile, 'Scanned Receipt');
                
                // Close modal
                closeReceiptUploadModal();
                
                // Open OCR preview immediately
                if (typeof openReceiptOCRPreview === 'function') {
                    openReceiptOCRPreview(scannedFile, {
                        ocrApiUrl: '{{ route("admin.api.ocr.extract") }}',
                        itemSearchUrl: '{{ route("admin.api.ocr.search-items") }}',
                        batchApiUrl: '{{ url("admin/api/item-batches") }}',
                        csrfToken: '{{ csrf_token() }}'
                    });
                } else {
                    showAlert('Receipt scanned! Click on it to extract items.', 'success', 'Scan Complete');
                }
            } else throw new Error(data.message || 'Scan failed');
        } else throw new Error('Scan request failed');
    } catch (error) {
        console.error('Scan error:', error);
        showAlert('Scan failed: ' + error.message, 'error', 'Scan Error');
    } finally {
        progressDiv.style.display = 'none';
        triggerScanBtn.disabled = false;
    }
}

function openScanFolder() { document.getElementById('scanFolderInput').click(); }
function handleScanFolderSelect(event) {
    const files = event.target.files;
    if (files.length > 0) {
        const file = files[0];
        
        // Add to preview section
        addReceiptToPreviewSection(file, 'Imported Receipt');
        
        // Close modal if open
        const modal = document.getElementById('receiptUploadModal');
        if (modal && modal.classList.contains('show')) {
            closeReceiptUploadModal();
        }
        
        // Open OCR preview immediately
        if (typeof openReceiptOCRPreview === 'function') {
            openReceiptOCRPreview(file, {
                ocrApiUrl: '{{ route("admin.api.ocr.extract") }}',
                itemSearchUrl: '{{ route("admin.api.ocr.search-items") }}',
                batchApiUrl: '{{ url("admin/api/item-batches") }}',
                csrfToken: '{{ csrf_token() }}'
            });
        } else {
            showAlert('Receipt imported! Click on it to extract items.', 'success', 'Import Complete');
        }
    }
}

function handleDragOver(event) {
    event.preventDefault();
    event.stopPropagation();
    const dropZone = document.getElementById('receiptDropZone');
    dropZone.style.borderColor = '#28a745';
    dropZone.style.background = '#f0fff0';
}

function handleDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();
    const dropZone = document.getElementById('receiptDropZone');
    dropZone.style.borderColor = '#6f42c1';
    dropZone.style.background = '#f8f5fc';
}

function handleFileDrop(event) {
    event.preventDefault();
    event.stopPropagation();
    handleDragLeave(event);
    const files = event.dataTransfer.files;
    if (files.length > 0) processReceiptFile(files[0]);
}

function handleFileSelect(event) {
    const files = event.target.files;
    if (files.length > 0) processReceiptFile(files[0]);
}

function processReceiptFile(file) {
    if (file.size > 5 * 1024 * 1024) {
        showAlert('File size exceeds 5MB limit.', 'warning', 'File Too Large');
        return;
    }
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    if (!validTypes.includes(file.type)) {
        showAlert('Invalid file type.', 'warning', 'Invalid File');
        return;
    }
    selectedReceiptFile = file;
    const previewArea = document.getElementById('receiptPreviewArea');
    const previewImg = document.getElementById('receiptPreviewImg');
    const pdfName = document.getElementById('receiptPdfName');
    const fileName = document.getElementById('receiptFileName');
    const dropZone = document.getElementById('receiptDropZone');
    previewArea.style.display = 'block';
    dropZone.style.display = 'none';
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
        };
        reader.readAsDataURL(file);
        pdfName.style.display = 'none';
    } else {
        previewImg.style.display = 'none';
        pdfName.style.display = 'block';
        fileName.textContent = file.name;
    }
}

function clearReceiptFile() {
    selectedReceiptFile = null;
    const previewArea = document.getElementById('receiptPreviewArea');
    const previewImg = document.getElementById('receiptPreviewImg');
    const dropZone = document.getElementById('receiptDropZone');
    const fileInput = document.getElementById('receiptFileInput');
    previewArea.style.display = 'none';
    previewImg.src = '';
    previewImg.style.display = 'none';
    dropZone.style.display = 'block';
    fileInput.value = '';
}

function submitReceiptAndContinue() {
    const description = document.getElementById('receiptItemDescription').value.trim();
    if (!selectedReceiptFile) {
        showAlert('Please select a receipt file.', 'warning', 'No File Selected');
        return;
    }
    
    // Add receipt to preview section
    addReceiptToPreviewSection(selectedReceiptFile, description);
    
    // Close the upload modal
    closeReceiptUploadModal();
    
    // Open OCR preview immediately for item extraction
    if (typeof openReceiptOCRPreview === 'function') {
        openReceiptOCRPreview(selectedReceiptFile, {
            ocrApiUrl: '{{ route("admin.api.ocr.extract") }}',
            itemSearchUrl: '{{ route("admin.api.ocr.search-items") }}',
            batchApiUrl: '{{ url("admin/api/item-batches") }}',
            csrfToken: '{{ csrf_token() }}'
        });
    } else {
        showAlert('Receipt uploaded! Click on it to extract items.', 'success', 'Receipt Added');
    }
}

let uploadedReceipts = [];

function addReceiptToPreviewSection(file, description) {
    const section = document.getElementById('uploadedReceiptsSection');
    const container = document.getElementById('uploadedReceiptsContainer');
    const noReceiptsMsg = document.getElementById('noReceiptsMessage');
    if (section) section.style.display = 'block';
    if (noReceiptsMsg) noReceiptsMsg.style.display = 'none';
    const receiptIndex = uploadedReceipts.length;
    uploadedReceipts.push({ file: file, description: description, index: receiptIndex });
    const receiptCard = document.createElement('div');
    receiptCard.className = 'receipt-card';
    receiptCard.id = `receipt-card-${receiptIndex}`;
    receiptCard.style.cssText = 'position: relative; border: 1px solid #dee2e6; border-radius: 8px; padding: 8px; background: white; width: 150px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);';
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            receiptCard.innerHTML = `
                <button type="button" class="btn btn-sm btn-danger" onclick="removeReceipt(${receiptIndex})" 
                        style="position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; border-radius: 50%; padding: 0; font-size: 10px; z-index: 10;">
                    <i class="bi bi-x"></i>
                </button>
                <img src="${e.target.result}" alt="Receipt ${receiptIndex + 1}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="viewReceiptFull(${receiptIndex})">
                <div style="font-size: 10px; margin-top: 5px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${description || file.name}">
                    ${description || file.name}
                </div>
            `;
        };
        reader.readAsDataURL(file);
    } else {
        receiptCard.innerHTML = `
            <button type="button" class="btn btn-sm btn-danger" onclick="removeReceipt(${receiptIndex})" 
                    style="position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; border-radius: 50%; padding: 0; font-size: 10px; z-index: 10;">
                <i class="bi bi-x"></i>
            </button>
            <div style="width: 100%; height: 100px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 4px;">
                <i class="bi bi-file-pdf" style="font-size: 48px; color: #dc3545;"></i>
            </div>
            <div style="font-size: 10px; margin-top: 5px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${description || file.name}">
                ${description || file.name}
            </div>
        `;
    }
    container.appendChild(receiptCard);
}

function removeReceipt(index) {
    const card = document.getElementById(`receipt-card-${index}`);
    if (card) card.remove();
    if (uploadedReceipts[index]) uploadedReceipts[index] = null;
    const activeReceipts = uploadedReceipts.filter(r => r !== null);
    if (activeReceipts.length === 0) {
        const noReceiptsMsg = document.getElementById('noReceiptsMessage');
        if (noReceiptsMsg) noReceiptsMsg.style.display = 'block';
    }
}

function viewReceiptFull(index) {
    const receipt = uploadedReceipts[index];
    if (!receipt || !receipt.file) return;
    if (receipt.file.type.startsWith('image/')) {
        if (typeof openReceiptOCRPreview === 'function') {
            openReceiptOCRPreview(receipt.file, {
                ocrApiUrl: '{{ route("admin.api.ocr.extract") }}',
                itemSearchUrl: '{{ route("admin.api.ocr.search-items") }}',
                batchApiUrl: '{{ url("admin/api/item-batches") }}',
                csrfToken: '{{ csrf_token() }}'
            });
        } else {
            const reader = new FileReader();
            reader.onload = function(e) {
                const overlay = document.createElement('div');
                overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 99999; display: flex; align-items: center; justify-content: center; cursor: pointer;';
                overlay.onclick = () => overlay.remove();
                overlay.innerHTML = `<img src="${e.target.result}" style="max-width: 90%; max-height: 90%; object-fit: contain;"><button type="button" style="position: absolute; top: 20px; right: 20px; background: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 20px; cursor: pointer;">×</button>`;
                document.body.appendChild(overlay);
            };
            reader.readAsDataURL(receipt.file);
        }
    }
}
</script>

<!-- ============================================ -->
<!-- KEYBOARD NAVIGATION SYSTEM FOR MODIFICATION -->
<!-- ============================================ -->
<script>
(function() {
    'use strict';
    
    // ============================================
    // CONFIGURATION
    // ============================================
    const CONFIG = {
        // Selectors for focusable elements in tab order (skip disabled elements)
        focusableSelector: [
            'input:not([type="hidden"]):not([readonly]):not([disabled]):not(.readonly-field)',
            'select:not([disabled])',
            'textarea:not([readonly]):not([disabled])',
            'button:not([disabled]):not(.btn-close-modal)'
        ].join(', '),
        
        // Special selectors for items table
        tableInputSelector: '#itemsTableBody input:not([readonly]):not([disabled])',
        tableRowSelector: '#itemsTableBody tr',
        
        // Modals
        modalSelectors: '.pending-orders-modal.show, .choose-items-modal.show, .alert-modal.show'
    };
    
    // ============================================
    // UTILITY FUNCTIONS
    // ============================================
    
    /**
     * Get all visible focusable elements in order
     */
    function getFocusableElements(container = document) {
        const elements = Array.from(container.querySelectorAll(CONFIG.focusableSelector));
        return elements.filter(el => {
            // Check visibility
            const style = window.getComputedStyle(el);
            if (style.display === 'none' || style.visibility === 'hidden' || el.offsetParent === null) {
                return false;
            }
            // Skip elements in hidden modals
            const modal = el.closest('.pending-orders-modal, .choose-items-modal, .alert-modal');
            if (modal && !modal.classList.contains('show')) {
                return false;
            }
            return true;
        });
    }
    
    /**
     * Check if any modal is currently open
     */
    function isModalOpen() {
        return document.querySelector(CONFIG.modalSelectors) !== null;
    }
    
    /**
     * Check if element is in items table
     */
    function isInItemsTable(element) {
        return element.closest('#itemsTableBody') !== null;
    }
    
    /**
     * Get table cell info for navigation
     */
    function getTableCellInfo(element) {
        const td = element.closest('td');
        if (!td) return null;
        
        const tr = td.closest('tr');
        const tbody = tr.closest('tbody');
        const cells = Array.from(tr.querySelectorAll('td'));
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        return {
            element,
            td,
            tr,
            tbody,
            colIndex: cells.indexOf(td),
            rowIndex: rows.indexOf(tr),
            totalCols: cells.length,
            totalRows: rows.length
        };
    }
    
    /**
     * Focus next element in the form (skipping buttons and disabled elements)
     */
    function focusNextElement(currentElement, direction = 1, skipButtons = true) {
        const focusable = getFocusableElements();
        let currentIndex = focusable.indexOf(currentElement);
        
        if (currentIndex === -1) return false;
        
        let nextIndex = currentIndex + direction;
        
        // Skip buttons if requested
        while (skipButtons && nextIndex >= 0 && nextIndex < focusable.length) {
            const nextEl = focusable[nextIndex];
            if (nextEl.tagName !== 'BUTTON') {
                break;
            }
            nextIndex += direction;
        }
        
        if (nextIndex >= 0 && nextIndex < focusable.length) {
            const nextEl = focusable[nextIndex];
            nextEl.focus();
            
            // Select text for input fields
            if (nextEl.tagName === 'INPUT' && nextEl.type !== 'checkbox' && nextEl.type !== 'radio') {
                nextEl.select();
            }
            return true;
        }
        return false;
    }
    
    /**
     * Focus input in a specific table cell
     */
    function focusTableCell(rowIndex, colIndex) {
        const tbody = document.getElementById('itemsTableBody');
        if (!tbody) return false;
        
        const rows = tbody.querySelectorAll('tr');
        if (rowIndex >= rows.length) return false;
        
        const cells = rows[rowIndex].querySelectorAll('td');
        if (colIndex >= cells.length) return false;
        
        const input = cells[colIndex].querySelector('input:not([readonly]):not([disabled])');
        if (input) {
            input.focus();
            input.select();
            return true;
        }
        return false;
    }
    
    // ============================================
    // KEY HANDLERS
    // ============================================
    
    /**
     * Handle Enter key
     */
    function handleEnterKey(e) {
        if (isModalOpen()) return;
        
        const activeEl = document.activeElement;
        const tagName = activeEl.tagName.toLowerCase();
        
        // Don't interfere with buttons
        if (tagName === 'button') return;
        
        // Don't interfere with textarea (allow new lines)
        if (tagName === 'textarea') return;
        
        // If in items table, handle specially
        if (isInItemsTable(activeEl)) {
            e.preventDefault();
            const cellInfo = getTableCellInfo(activeEl);
            if (cellInfo) {
                // Try to move to next column
                const nextCol = cellInfo.colIndex + 1;
                if (nextCol < cellInfo.totalCols - 1) { // -1 to skip action column
                    if (focusTableCell(cellInfo.rowIndex, nextCol)) return;
                }
                // At end of row, move to first input of next row
                if (cellInfo.rowIndex < cellInfo.totalRows - 1) {
                    if (focusTableCell(cellInfo.rowIndex + 1, 0)) return;
                }
                // At last row, add new row if addNewRow function exists
                if (typeof addNewRow === 'function') {
                    addNewRow();
                    setTimeout(() => focusTableCell(cellInfo.rowIndex + 1, 0), 50);
                }
            }
            return;
        }
        
        // For SELECT elements - Enter confirms selection and moves to next field
        if (tagName === 'select') {
            e.preventDefault();
            if (e.shiftKey) {
                focusNextElement(activeEl, -1);
            } else {
                focusNextElement(activeEl, 1);
            }
            return;
        }
        
        // For regular input fields - move to next field
        e.preventDefault();
        if (e.shiftKey) {
            focusNextElement(activeEl, -1);
        } else {
            focusNextElement(activeEl, 1);
        }
    }
    
    /**
     * Handle Arrow keys for table navigation
     */
    function handleArrowKeys(e) {
        if (isModalOpen()) return;
        
        const activeEl = document.activeElement;
        
        // Only handle arrow keys in table
        if (!isInItemsTable(activeEl)) return;
        
        const cellInfo = getTableCellInfo(activeEl);
        if (!cellInfo) return;
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (cellInfo.rowIndex < cellInfo.totalRows - 1) {
                    focusTableCell(cellInfo.rowIndex + 1, cellInfo.colIndex);
                }
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                if (cellInfo.rowIndex > 0) {
                    focusTableCell(cellInfo.rowIndex - 1, cellInfo.colIndex);
                }
                break;
                
            case 'ArrowRight':
                // Only navigate if at end of input
                if (activeEl.selectionStart === activeEl.value.length) {
                    e.preventDefault();
                    if (cellInfo.colIndex < cellInfo.totalCols - 2) {
                        focusTableCell(cellInfo.rowIndex, cellInfo.colIndex + 1);
                    }
                }
                break;
                
            case 'ArrowLeft':
                // Only navigate if at start of input
                if (activeEl.selectionStart === 0) {
                    e.preventDefault();
                    if (cellInfo.colIndex > 0) {
                        focusTableCell(cellInfo.rowIndex, cellInfo.colIndex - 1);
                    }
                }
                break;
        }
    }
    
    /**
     * Handle End key - Save transaction
     */
    function handleEndKey(e) {
        if (isModalOpen()) return;
        
        const activeEl = document.activeElement;
        
        // If in an input that supports cursor positioning, check if we're at the end
        if (activeEl.tagName === 'INPUT' && activeEl.type === 'text') {
            if (activeEl.selectionStart !== activeEl.value.length) {
                return; // Let End key work normally in text input
            }
        }
        
        e.preventDefault();
        
        // Trigger save function
        if (typeof saveSale === 'function') {
            saveSale();
        }
    }
    
    /**
     * Handle Ctrl+S - Save transaction
     */
    function handleCtrlS(e) {
        e.preventDefault();
        if (typeof saveSale === 'function') {
            saveSale();
        }
    }
    
    /**
     * Handle Ctrl+I - Open Choose Items modal
     */
    function handleCtrlI(e) {
        e.preventDefault();
        if (typeof handleChooseItemsClick === 'function') {
            handleChooseItemsClick();
        }
    }
    
    /**
     * Handle Escape - Close modals
     */
    function handleEscapeKey(e) {
        // Close alert modal
        if (typeof closeAlert === 'function') {
            const alertModal = document.getElementById('alertModal');
            if (alertModal?.classList.contains('show')) {
                closeAlert();
                e.preventDefault();
                return;
            }
        }
        
        // Close batch selection modal
        if (typeof closeBatchSelectionModal === 'function') {
            const batchModal = document.getElementById('batchSelectionModal');
            if (batchModal?.classList.contains('show')) {
                closeBatchSelectionModal();
                e.preventDefault();
                return;
            }
        }
        
        // Close choose items modal
        if (typeof closeChooseItemsModal === 'function') {
            const itemsModal = document.getElementById('chooseItemsModal');
            if (itemsModal?.classList.contains('show')) {
                closeChooseItemsModal();
                e.preventDefault();
                return;
            }
        }
        
        // Close all invoices modal
        if (typeof closeAllInvoicesModal === 'function') {
            const invoicesModal = document.getElementById('allInvoicesModal');
            if (invoicesModal?.classList.contains('show')) {
                closeAllInvoicesModal();
                e.preventDefault();
                return;
            }
        }
        
        // Close date range modal
        if (typeof closeDateRangeModal === 'function') {
            const dateModal = document.getElementById('dateRangeModal');
            if (dateModal?.classList.contains('show')) {
                closeDateRangeModal();
                e.preventDefault();
                return;
            }
        }
    }
    
    // ============================================
    // MODAL KEYBOARD NAVIGATION
    // ============================================
    
    // Track selected row index in modals
    let chooseItemsSelectedIndex = -1;
    let batchSelectedIndex = -1;
    
    function isChooseItemsModalOpen() {
        const modal = document.getElementById('chooseItemsModal');
        return modal && modal.classList.contains('show');
    }
    
    function isBatchModalOpen() {
        const modal = document.getElementById('batchSelectionModal');
        return modal && modal.classList.contains('show');
    }
    
    function navigateChooseItemsModal(direction) {
        const rows = document.querySelectorAll('#chooseItemsBody tr:not([style*="display: none"])');
        if (rows.length === 0) return;
        
        rows.forEach(r => r.classList.remove('item-row-selected'));
        
        if (direction === 'down') {
            chooseItemsSelectedIndex = (chooseItemsSelectedIndex + 1) % rows.length;
        } else {
            chooseItemsSelectedIndex = chooseItemsSelectedIndex <= 0 ? rows.length - 1 : chooseItemsSelectedIndex - 1;
        }
        
        const selectedRow = rows[chooseItemsSelectedIndex];
        if (selectedRow) {
            selectedRow.classList.add('item-row-selected');
            selectedRow.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }
    
    function selectCurrentChooseItem() {
        const rows = document.querySelectorAll('#chooseItemsBody tr:not([style*="display: none"])');
        if (chooseItemsSelectedIndex >= 0 && chooseItemsSelectedIndex < rows.length) {
            rows[chooseItemsSelectedIndex].click();
        }
    }
    
    function navigateBatchModal(direction) {
        const rows = document.querySelectorAll('#batchSelectionBody tr:not([style*="display: none"])');
        if (rows.length === 0) return;
        
        rows.forEach(r => r.classList.remove('item-row-selected'));
        
        if (direction === 'down') {
            batchSelectedIndex = (batchSelectedIndex + 1) % rows.length;
        } else {
            batchSelectedIndex = batchSelectedIndex <= 0 ? rows.length - 1 : batchSelectedIndex - 1;
        }
        
        const selectedRow = rows[batchSelectedIndex];
        if (selectedRow) {
            selectedRow.classList.add('item-row-selected');
            selectedRow.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            selectedRow.click();
        }
    }
    
    function selectCurrentBatch() {
        if (window.selectedBatch && typeof selectBatchFromModal === 'function') {
            selectBatchFromModal(window.selectedBatch);
        }
    }
    
    function handleChooseItemsModalKeyboard(e) {
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                navigateChooseItemsModal('down');
                break;
            case 'ArrowUp':
                e.preventDefault();
                navigateChooseItemsModal('up');
                break;
            case 'Enter':
                if (document.activeElement.id === 'itemSearchInput') {
                    e.preventDefault();
                    const visibleRows = document.querySelectorAll('#chooseItemsBody tr:not([style*="display: none"])');
                    if (visibleRows.length > 0) {
                        chooseItemsSelectedIndex = 0;
                        visibleRows[0].click();
                    }
                    return;
                }
                e.preventDefault();
                selectCurrentChooseItem();
                break;
            case 'f':
            case 'F':
                if (!e.ctrlKey && !e.altKey) {
                    e.preventDefault();
                    const searchInput = document.getElementById('itemSearchInput');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
                break;
        }
    }
    
    function handleBatchModalKeyboard(e) {
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                navigateBatchModal('down');
                break;
            case 'ArrowUp':
                e.preventDefault();
                navigateBatchModal('up');
                break;
            case 'Enter':
                if (document.activeElement.id === 'batchSearchInput') {
                    e.preventDefault();
                    const visibleRows = document.querySelectorAll('#batchSelectionBody tr:not([style*="display: none"])');
                    if (visibleRows.length > 0) {
                        visibleRows[0].click();
                        batchSelectedIndex = 0;
                        setTimeout(() => {
                            if (window.selectedBatch) {
                                selectBatchFromModal(window.selectedBatch);
                            }
                        }, 50);
                    }
                    return;
                }
                e.preventDefault();
                selectCurrentBatch();
                break;
            case 'f':
            case 'F':
                if (!e.ctrlKey && !e.altKey) {
                    e.preventDefault();
                    const searchInput = document.getElementById('batchSearchInput');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
                break;
        }
    }
    
    // ============================================
    // INVOICES MODAL KEYBOARD NAVIGATION
    // ============================================
    
    let invoicesSelectedIndex = -1;
    
    function isInvoicesModalOpen() {
        const modal = document.getElementById('invoicesModal');
        return modal && modal.classList.contains('show');
    }
    
    function navigateInvoicesModal(direction) {
        const rows = document.querySelectorAll('#invoicesTableBody tr:not([style*="display: none"])');
        if (rows.length === 0) return;
        
        // Remove previous selection
        rows.forEach(r => r.classList.remove('item-row-selected'));
        
        if (direction === 'down') {
            invoicesSelectedIndex = Math.min(invoicesSelectedIndex + 1, rows.length - 1);
        } else if (direction === 'up') {
            invoicesSelectedIndex = Math.max(invoicesSelectedIndex - 1, 0);
        }
        
        if (invoicesSelectedIndex >= 0 && invoicesSelectedIndex < rows.length) {
            const selectedRow = rows[invoicesSelectedIndex];
            selectedRow.classList.add('item-row-selected');
            selectedRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    
    function selectCurrentInvoice() {
        const rows = document.querySelectorAll('#invoicesTableBody tr:not([style*="display: none"])');
        if (invoicesSelectedIndex >= 0 && invoicesSelectedIndex < rows.length) {
            rows[invoicesSelectedIndex].click();
        }
    }
    
    function handleInvoicesModalKeyboard(e) {
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                navigateInvoicesModal('down');
                break;
            case 'ArrowUp':
                e.preventDefault();
                navigateInvoicesModal('up');
                break;
            case 'Enter':
                if (document.activeElement.id === 'invoiceSearchInput') {
                    e.preventDefault();
                    const visibleRows = document.querySelectorAll('#invoicesTableBody tr:not([style*="display: none"])');
                    if (visibleRows.length > 0) {
                        invoicesSelectedIndex = 0;
                        visibleRows[0].click();
                    }
                    return;
                }
                e.preventDefault();
                selectCurrentInvoice();
                break;
            case 'f':
            case 'F':
                if (!e.ctrlKey && !e.altKey) {
                    e.preventDefault();
                    const searchInput = document.getElementById('invoiceSearchInput');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
                break;
        }
    }
    
    // Filter invoices in the modal based on search input
    function filterInvoicesInModal() {
        const searchText = document.getElementById('invoiceSearchInput')?.value?.toLowerCase() || '';
        const rows = document.querySelectorAll('#invoicesTableBody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const invoiceNo = (row.cells[0]?.textContent || '').toLowerCase();
            const customer = (row.cells[2]?.textContent || '').toLowerCase();
            
            if (invoiceNo.includes(searchText) || customer.includes(searchText)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Reset selection index after filtering
        invoicesSelectedIndex = -1;
        
        // Update visible count in footer
        const totalEl = document.getElementById('invoicesTotal');
        if (totalEl) {
            totalEl.textContent = `Total: ${visibleCount} invoice(s)`;
        }
    }
    
    // ============================================
    // MAIN KEYBOARD EVENT LISTENER
    // ============================================
    
    document.addEventListener('keydown', function(e) {
        // Check alert modal first (highest priority)
        const alertModal = document.getElementById('alertModal');
        if (alertModal && alertModal.classList.contains('show')) {
            // Alert modal has its own handler, let it handle
            return;
        }
        
        // Check if modals are open and handle their keyboard navigation
        if (isChooseItemsModalOpen()) {
            handleChooseItemsModalKeyboard(e);
            if (e.key === 'Escape') {
                e.preventDefault();
                closeChooseItemsModal();
                chooseItemsSelectedIndex = -1;
            }
            return;
        }
        
        if (isBatchModalOpen()) {
            handleBatchModalKeyboard(e);
            if (e.key === 'Escape') {
                e.preventDefault();
                closeBatchSelectionModal();
                batchSelectedIndex = -1;
            }
            return;
        }
        
        if (isInvoicesModalOpen()) {
            handleInvoicesModalKeyboard(e);
            if (e.key === 'Escape') {
                e.preventDefault();
                closeInvoicesModal();
                invoicesSelectedIndex = -1;
            }
            return;
        }
        
        // Handle specific key combinations for main form
        switch (e.key) {
            case 'Enter':
                handleEnterKey(e);
                break;
                
            case 'ArrowDown':
            case 'ArrowUp':
            case 'ArrowLeft':
            case 'ArrowRight':
                handleArrowKeys(e);
                break;
                
            case 'End':
                handleEndKey(e);
                break;
                
            case 'Escape':
                handleEscapeKey(e);
                break;
                
            case 's':
            case 'S':
                if (e.ctrlKey) {
                    handleCtrlS(e);
                }
                break;
                
            case 'i':
            case 'I':
                if (e.ctrlKey) {
                    handleCtrlI(e);
                }
                break;
        }
    }, true);
    
    // ============================================
    // AUTO-FOCUS INVOICE NO FIELD ON PAGE LOAD
    // ============================================
    
    document.addEventListener('DOMContentLoaded', function() {
        // Focus Invoice No field on page load (main entry point for modification)
        setTimeout(function() {
            const invoiceNoInput = document.getElementById('invoiceNo');
            if (invoiceNoInput) {
                invoiceNoInput.focus();
                invoiceNoInput.select();
            }
        }, 200);
    });
    
    // ============================================
    // VISUAL FOCUS INDICATOR
    // ============================================
    
    const focusStyle = document.createElement('style');
    focusStyle.textContent = `
        /* Enhanced focus styles for keyboard navigation */
        .form-control:focus,
        select:focus,
        input:focus {
            outline: 2px solid #0d6efd !important;
            outline-offset: 1px;
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25) !important;
        }
        
        /* Items table row focus indicator */
        #itemsTableBody tr:focus-within {
            background-color: #e7f3ff !important;
        }
        
        #itemsTableBody tr:focus-within td {
            background-color: #e7f3ff !important;
        }
        
        /* Modal row selection highlight */
        #chooseItemsBody tr.item-row-selected,
        #batchSelectionBody tr.item-row-selected {
            background-color: #007bff !important;
            color: white !important;
        }
        
        #chooseItemsBody tr.item-row-selected td,
        #batchSelectionBody tr.item-row-selected td {
            background-color: #007bff !important;
            color: white !important;
        }
    `;
    document.head.appendChild(focusStyle);
    
    console.log('🎹 Modification Keyboard Navigation Loaded');
    console.log('   Invoice No → Entry point | Enter → Next field');
    console.log('   Arrow Keys → Navigate table | End/Ctrl+S → Save');
    console.log('   Ctrl+I → Choose Items | Escape → Close modal');
    
})();
</script>

<!-- ============================================ -->
<!-- TEMP TRANSACTION RECEIPT HANDLING -->
<!-- ============================================ -->
<script>
// Display receipt images for TEMP transactions
function displayTempReceiptImages(receiptPath) {
    const container = document.getElementById('tempReceiptImages');
    const countBadge = document.getElementById('tempReceiptCount');
    
    if (!container) return;
    
    // Better null/empty check
    if (!receiptPath || receiptPath.trim() === '' || receiptPath === 'null' || receiptPath === 'undefined') {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="bi bi-camera" style="font-size: 2rem; color: #ffc107;"></i>
                <p class="mb-1 mt-2" style="font-weight: 500;">No receipts stored</p>
                <small>Use "Scan More Receipts" to add receipts.</small>
            </div>
        `;
        if (countBadge) countBadge.textContent = '0 receipts';
        return;
    }
    
    console.log('📷 Receipt path from database:', receiptPath);
    
    // Parse single or multiple paths (comma separated)
    const paths = receiptPath.split(',').map(p => p.trim()).filter(p => p && p !== 'null' && p !== 'undefined');
    
    console.log('📷 Parsed paths:', paths);
    
    if (paths.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="bi bi-camera" style="font-size: 2rem; color: #ffc107;"></i>
                <p class="mb-1 mt-2" style="font-weight: 500;">No receipts stored</p>
                <small>Use "Scan More Receipts" to add receipts.</small>
            </div>
        `;
        if (countBadge) countBadge.textContent = '0 receipts';
        return;
    }
    
    // Build image gallery
    // Get base path for the application (handles subdirectory installations)
    const basePath = '{{ url('/') }}';
    
    let html = '<div class="d-flex flex-wrap gap-2">';
    paths.forEach((path, index) => {
        // Handle storage path - prepend base URL for subdirectory support
        let imgSrc = path;
        if (!path.startsWith('http')) {
            // Remove leading slash if present, then prepend base path
            imgSrc = basePath + '/' + path.replace(/^\//, '');
        }
        
        console.log(`📷 Receipt ${index + 1} - Path: ${path}, URL: ${imgSrc}`);
        
        html += `
            <div class="receipt-thumb" style="position: relative; width: 150px; height: 120px;">
                <img src="${imgSrc}" 
                     alt="Receipt ${index + 1}" 
                     style="width: 150px; height: 100px; object-fit: cover; border-radius: 8px 8px 0 0; cursor: pointer; border: 2px solid #ddd; border-bottom: none;"
                     onclick="openReceiptForOCR('${imgSrc}')"
                     onerror="this.onerror=null; this.parentElement.innerHTML='<div style=\\'width: 150px; height: 100px; background: #f5f5f5; border-radius: 8px 8px 0 0; border: 2px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: not-allowed;\\'><i class=\\'bi bi-image\\' style=\\'font-size: 24px; color: #999;\\'></i><small style=\\'color: #999; margin-top: 5px;\\'>File not found</small></div><div style=\\'background: rgba(0,0,0,0.6); color: white; font-size: 10px; text-align: center; border-radius: 0 0 6px 6px; padding: 3px;\\'>Receipt ${index + 1}</div>';">
                <div style="background: rgba(0,0,0,0.6); color: white; font-size: 10px; text-align: center; border-radius: 0 0 6px 6px; padding: 3px;">
                    Receipt ${index + 1}
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
    if (countBadge) countBadge.textContent = paths.length + ' receipt' + (paths.length > 1 ? 's' : '');
}

// Open receipt in OCR preview modal
function openReceiptForOCR(imageSrc) {
    console.log('📷 Opening receipt for OCR:', imageSrc);
    
    // Check if OCR preview function exists
    if (typeof openReceiptOCRPreview === 'function') {
        // Create a mock file object for the image
        fetch(imageSrc)
            .then(response => response.blob())
            .then(blob => {
                const file = new File([blob], 'receipt.jpg', { type: blob.type });
                openReceiptOCRPreview(file, {
                    ocrApiUrl: '{{ route("admin.api.ocr.extract") }}',
                    itemSearchUrl: '{{ route("admin.api.ocr.search-items") }}',
                    batchApiUrl: '{{ url("admin/api/item-batches") }}',
                    csrfToken: '{{ csrf_token() }}',
                    onItemsSelected: function(selectedItems) {
                        console.log('📷 Items selected via callback:', selectedItems);
                    }
                });
            })
            .catch(error => {
                console.error('Error loading image for OCR:', error);
                showAlert('Error loading image for OCR preview', 'error', 'Load Failed');
            });
    } else {
        // Fallback - just show the image in a modal
        const overlay = document.createElement('div');
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 99999; display: flex; align-items: center; justify-content: center; cursor: pointer;';
        overlay.onclick = () => overlay.remove();
        overlay.innerHTML = `
            <img src="${imageSrc}" style="max-width: 90%; max-height: 90%; object-fit: contain;">
            <button type="button" style="position: absolute; top: 20px; right: 20px; background: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 20px; cursor: pointer;">×</button>
        `;
        document.body.appendChild(overlay);
    }
}

// Open receipt scanner (camera/file upload)
function openReceiptScanner() {
    console.log('📷 Opening receipt scanner');
    
    // Open the receipt upload modal instead of direct file input
    openReceiptUploadModal();
}

// Listen for OCR items selected event (for modification page)
window.addEventListener('ocrItemsSelected', function(event) {
    const selectedItems = event.detail?.items || [];
    
    if (selectedItems.length === 0) return;
    
    console.log('📷 OCR Items received in modification:', selectedItems);
    
    // Add each item to the table
    selectedItems.forEach((selection, index) => {
        setTimeout(() => {
            const item = selection.item;
            const batch = selection.batch;
            
            if (!item) return;
            
            // Prepare item data for addItemRow (or similar function)
            const itemData = {
                id: item.id,
                name: item.name || '',
                bar_code: item.bar_code || '',
                packing: item.packing || '',
                company_id: item.company_id || '',
                company_name: item.company_short_name || '',
                mrp: parseFloat(item.mrp || 0),
                s_rate: parseFloat(item.s_rate || 0),
                hsn_code: item.hsn_code || '',
                cgst_percent: parseFloat(item.cgst_percent || 0),
                sgst_percent: parseFloat(item.sgst_percent || 0),
                cess_percent: parseFloat(item.cess_percent || 0),
                unit: item.unit || 'PCS'
            };
            
            const batchData = batch ? {
                id: batch.id,
                batch_no: batch.batch_no || '',
                expiry_date: batch.expiry_date || '',
                expiry_display: batch.expiry_display || '',
                avg_s_rate: parseFloat(batch.avg_s_rate || batch.s_rate || item.s_rate || 0),
                avg_mrp: parseFloat(batch.avg_mrp || batch.mrp || item.mrp || 0)
            } : {
                id: '',
                batch_no: '',
                expiry_date: '',
                expiry_display: '',
                avg_s_rate: parseFloat(item.s_rate || 0),
                avg_mrp: parseFloat(item.mrp || 0)
            };
            
            // Use the existing addItemToTable or similar function
            if (typeof addItemToTable === 'function') {
                addItemToTable(itemData, batchData);
            } else {
                // Fallback: Add a new row manually
                addNewRow();
                // Fill in the last row
                const tbody = document.getElementById('itemsTableBody');
                const lastRow = tbody.lastElementChild;
                if (lastRow) {
                    const rowIdx = lastRow.getAttribute('data-row-index');
                    const codeInput = lastRow.querySelector(`input[name*="[code]"]`);
                    const nameInput = lastRow.querySelector(`input[name*="[item_name]"]`);
                    const batchInput = lastRow.querySelector(`input[name*="[batch]"]`);
                    const expiryInput = lastRow.querySelector(`input[name*="[expiry]"]`);
                    const rateInput = lastRow.querySelector(`input[name*="[rate]"]`);
                    const mrpInput = lastRow.querySelector(`input[name*="[mrp]"]`);
                    
                    if (codeInput) codeInput.value = itemData.bar_code;
                    if (nameInput) nameInput.value = itemData.name;
                    if (batchInput) batchInput.value = batchData.batch_no;
                    if (expiryInput) expiryInput.value = batchData.expiry_display;
                    if (rateInput) rateInput.value = batchData.avg_s_rate;
                    if (mrpInput) mrpInput.value = batchData.avg_mrp;
                    
                    // Store attributes
                    lastRow.setAttribute('data-item-id', itemData.id);
                    lastRow.setAttribute('data-batch-id', batchData.id);
                    lastRow.setAttribute('data-hsn-code', itemData.hsn_code);
                    lastRow.setAttribute('data-cgst', itemData.cgst_percent);
                    lastRow.setAttribute('data-sgst', itemData.sgst_percent);
                    lastRow.setAttribute('data-cess', itemData.cess_percent);
                    lastRow.setAttribute('data-packing', itemData.packing);
                    lastRow.setAttribute('data-unit', itemData.unit);
                    lastRow.setAttribute('data-company', itemData.company_name);
                }
            }
        }, index * 150);
    });
    
    // Show success message
    setTimeout(() => {
        showAlert(`${selectedItems.length} item(s) added from OCR scan`, 'success', 'Items Added');
        updateSummary();
    }, selectedItems.length * 150 + 200);
});
</script>

<!-- Receipt Upload Modal Backdrop -->
<div id="receiptUploadBackdrop" class="pending-orders-backdrop"></div>

<!-- Receipt Upload Modal -->
<div id="receiptUploadModal" class="pending-orders-modal" style="max-width: 650px;">
    <div class="pending-orders-content">
        <div class="pending-orders-header" style="background: #6f42c1; padding: 10px 15px;">
            <h5 class="pending-orders-title" style="font-size: 14px; color: white;"><i class="bi bi-file-earmark-image me-2"></i> Upload Item Receipt</h5>
            <button type="button" class="btn-close-modal" onclick="closeReceiptUploadModal()" title="Close" style="color: white;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body" style="padding: 15px;">
            <div class="alert alert-info mb-3 py-2" style="font-size: 11px;">
                <i class="bi bi-info-circle me-1"></i>
                <strong>This customer is set for Item Description Receipt mode.</strong> Upload the receipt image and enter item details below.
            </div>
            
            <!-- Upload Options Tabs -->
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-sm btn-purple flex-fill" id="uploadTabBtn" onclick="showUploadTab()" style="font-size: 11px;">
                    <i class="bi bi-cloud-upload"></i> Upload File
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" id="scanTabBtn" onclick="showScanTab()" style="font-size: 11px;">
                    <i class="bi bi-camera"></i> Camera
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" id="scannerTabBtn" onclick="showScannerTab()" style="font-size: 11px;">
                    <i class="bi bi-printer"></i> Scanner
                </button>
            </div>
            
            <!-- File Upload Area -->
            <div id="receiptDropZone" style="border: 2px dashed #6f42c1; border-radius: 8px; padding: 30px; text-align: center; background: #f8f5fc; cursor: pointer; margin-bottom: 15px; transition: all 0.3s;" 
                 ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleFileDrop(event)" onclick="document.getElementById('receiptFileInput').click()">
                <i class="bi bi-cloud-upload" style="font-size: 48px; color: #6f42c1;"></i>
                <div style="font-size: 14px; font-weight: 600; margin-top: 10px; color: #6f42c1;">Drag & Drop Receipt Image</div>
                <div style="font-size: 11px; color: #666; margin-top: 5px;">or click to browse (JPG, PNG, PDF - Max 5MB)</div>
                <input type="file" id="receiptFileInput" accept="image/*,.pdf" style="display: none;" onchange="handleFileSelect(event)">
            </div>
            
            <!-- Camera Scan Area -->
            <div id="cameraScanArea" style="display: none; margin-bottom: 15px;">
                <div id="cameraContainer" style="border: 2px solid #6f42c1; border-radius: 8px; overflow: hidden; background: #000; position: relative;">
                    <video id="cameraVideo" style="width: 100%; max-height: 300px; display: block;" playsinline autoplay></video>
                    <canvas id="cameraCanvas" style="display: none;"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-2">
                    <button type="button" class="btn btn-success btn-sm" onclick="capturePhoto()" style="font-size: 11px;">
                        <i class="bi bi-camera-fill"></i> Capture
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="stopCamera()" style="font-size: 11px;">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                </div>
            </div>
            
            <!-- Physical Scanner Area -->
            <div id="physicalScannerArea" style="display: none; margin-bottom: 15px;">
                <!-- Scanner Service Status -->
                <div id="scannerServiceStatus" class="alert alert-info py-2 mb-3" style="font-size: 11px;">
                    <i class="bi bi-hourglass-split me-1"></i> Checking scanner service...
                </div>
                
                <!-- Scanner Selection -->
                <div class="mb-3">
                    <label style="font-weight: 600; font-size: 12px; display: block; margin-bottom: 5px;">
                        <i class="bi bi-printer me-1"></i> Select Scanner:
                    </label>
                    <select id="scannerSelect" class="form-control form-select" style="font-size: 12px;" disabled>
                        <option value="">-- Detecting Scanners --</option>
                    </select>
                </div>
                
                <!-- Scan Quality Options -->
                <div class="mb-3">
                    <label style="font-weight: 600; font-size: 12px; display: block; margin-bottom: 5px;">Scan Quality:</label>
                    <div class="d-flex gap-2">
                        <label class="form-check" style="font-size: 11px;">
                            <input type="radio" name="scanQuality" value="150" class="form-check-input"> Fast (150 DPI)
                        </label>
                        <label class="form-check" style="font-size: 11px;">
                            <input type="radio" name="scanQuality" value="300" class="form-check-input" checked> Standard (300 DPI)
                        </label>
                        <label class="form-check" style="font-size: 11px;">
                            <input type="radio" name="scanQuality" value="600" class="form-check-input"> High (600 DPI)
                        </label>
                    </div>
                </div>
                
                <!-- Scan Button -->
                <div class="text-center">
                    <button type="button" id="triggerScanBtn" class="btn btn-purple" onclick="triggerScan()" style="font-size: 12px;" disabled>
                        <i class="bi bi-printer-fill"></i> Scan Receipt
                    </button>
                </div>
                
                <!-- Scan Progress -->
                <div id="scanProgress" class="text-center mt-3" style="display: none;">
                    <div class="spinner-border text-purple spinner-border-sm"></div>
                    <span style="font-size: 12px; margin-left: 8px;">Scanning... Please wait</span>
                </div>
                
                <!-- Fallback: Manual File Selection -->
                <div class="mt-3 pt-3 border-top">
                    <div style="font-size: 11px; color: #666; text-align: center;">
                        <i class="bi bi-folder2-open me-1"></i>
                        Or <a href="javascript:void(0)" onclick="openScanFolder()" style="color: #6f42c1;">import from scan folder</a>
                        <input type="file" id="scanFolderInput" accept="image/*,.pdf" style="display: none;" onchange="handleScanFolderSelect(event)">
                    </div>
                </div>
            </div>
            
            <!-- Preview Area -->
            <div id="receiptPreviewArea" style="display: none; margin-bottom: 15px;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-weight: 600; font-size: 12px;">Selected File:</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearReceiptFile()" style="font-size: 10px; padding: 2px 8px;">
                        <i class="bi bi-x"></i> Remove
                    </button>
                </div>
                <div id="receiptPreviewContainer" style="border: 1px solid #dee2e6; border-radius: 4px; padding: 10px; background: white;">
                    <img id="receiptPreviewImg" src="" alt="Receipt Preview" style="max-width: 100%; max-height: 200px; display: none;">
                    <div id="receiptPdfName" style="display: none; font-size: 12px;"><i class="bi bi-file-pdf text-danger me-2"></i><span id="receiptFileName"></span></div>
                </div>
            </div>
            
            <!-- Item Description Input -->
            <div class="mb-3">
                <label style="font-weight: 600; font-size: 12px; display: block; margin-bottom: 5px;">Item Description / Remarks:</label>
                <textarea id="receiptItemDescription" class="form-control" rows="3" placeholder="Enter item details or description from the receipt..." style="font-size: 12px;"></textarea>
            </div>
        </div>
        <div class="pending-orders-footer" style="padding: 10px 15px;">
            <button type="button" class="btn btn-success btn-sm" id="submitReceiptBtn" onclick="submitReceiptAndContinue()" style="font-size: 11px;">
                <i class="bi bi-check-circle"></i> Submit & Continue
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeReceiptUploadModal()" style="font-size: 11px;">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
        </div>
    </div>
</div>

<!-- Include OCR Preview Module with Batch Selection -->
@include('admin.sale.partials.receipt-ocr-preview')

<!-- Bridge Script for New Modal Components -->
<script>
/**
 * Bridge to connect new reusable modal components with existing sale modification functions
 * This ensures backward compatibility while using the new components
 */

// Override openChooseItemsModal to use new component
window.openChooseItemsModal = async function() {
    const transactionId = document.getElementById('transactionId')?.value;
    
    if (!transactionId) {
        showAlert('Please select an invoice first!\n\nClick "Choose Invoice" or "All Invoices" to select an invoice to modify.', 'warning', 'Invoice Required');
        return;
    }
    
    console.log('🔗 Bridge: Opening Item Modal via new component');
    if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    } else {
        console.error('Item Modal component not loaded');
    }
};

// Override closeChooseItemsModal to use new component  
window.closeChooseItemsModal = function() {
    console.log('🔗 Bridge: Closing Item Modal via new component');
    if (typeof closeItemModal_chooseItemsModal === 'function') {
        closeItemModal_chooseItemsModal();
    }
};

// Override openBatchSelectionModal to use new component
window.openBatchSelectionModal = function(item) {
    console.log('🔗 Bridge: Opening Batch Modal via new component for:', item?.name);
    pendingItemSelection = item;
    if (typeof openBatchModal_batchSelectionModal === 'function') {
        openBatchModal_batchSelectionModal(item);
    } else {
        console.error('Batch Modal component not loaded');
    }
};

// Override closeBatchSelectionModal to use new component
window.closeBatchSelectionModal = function() {
    console.log('🔗 Bridge: Closing Batch Modal via new component');
    if (typeof closeBatchModal_batchSelectionModal === 'function') {
        closeBatchModal_batchSelectionModal();
    }
    pendingItemSelection = null;
    window.pendingBarcodeRowIndex = null;
};

// Callback when item and batch are selected from new modal component
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('✅ Bridge: Item+Batch selected from new modal:', item?.name, batch?.batch_no);
    
    // Store selected batch for compatibility
    window.selectedBatch = batch;
    pendingItemSelection = item;
    
    // Check if this is from barcode entry (existing row) or Choose Items modal (new row)
    if (window.pendingBarcodeRowIndex !== null) {
        // From barcode entry - populate existing row
        console.log('📱 Bridge: Populating existing row from barcode, index:', window.pendingBarcodeRowIndex);
        if (typeof populateRowWithItemAndBatch === 'function') {
            populateRowWithItemAndBatch(window.pendingBarcodeRowIndex, item, batch);
        }
        window.pendingBarcodeRowIndex = null;
    } else {
        // From Choose Items modal - add new row
        console.log('➕ Bridge: Adding new item via addItem');
        if (typeof addItem === 'function') {
            addItem(item, batch);
        }
    }
    
    // Clear selections
    window.selectedBatch = null;
    pendingItemSelection = null;
};

// Also support the simpler callback name
window.onBatchSelectedFromModal = function(item, batch) {
    window.onItemBatchSelectedFromModal(item, batch);
};

// Listen for item selection to open batch modal (for compatibility)
window.onItemSelectedFromModal = function(item) {
    console.log('🔗 Bridge: Item selected, opening batch modal for:', item?.name);
    pendingItemSelection = item;
    if (typeof openBatchModal_batchSelectionModal === 'function') {
        openBatchModal_batchSelectionModal(item);
    }
};

console.log('🔗 Modal Component Bridge Loaded - Sale Modification');
</script>

@endsection