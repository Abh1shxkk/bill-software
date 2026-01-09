@extends('layouts.admin')

@section('title', 'Sale Transaction')

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
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-receipt me-2"></i> Sale Transaction</h4>
        <div class="text-muted small">Create new sale transaction</div>
    </div>
    <div>
        <a href="{{ route('admin.sale.invoices') }}" class="btn btn-primary">
            <i class="bi bi-receipt-cutoff me-1"></i> Sale Invoices
        </a>
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
                        <select class="form-control no-select2" name="series" id="seriesSelect" style="width: 100px; padding: 2px 4px; font-weight: 600;" onchange="updateInvoiceType()">
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
                        <select class="form-control" name="customer_id" id="customerSelect" style="width: 250px;" autocomplete="off" onchange="updateCustomerName(); fetchCustomerDue();">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->code ?? '' }} - {{ $customer->name }}</option>
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
                            <input type="text" class="form-control readonly-field" name="invoice_no" id="invoiceNo" value="{{ $nextInvoiceNo }}" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                        </div>
                        <div class="field-group mb-2">
                            <label style="width: 70px;">Sales Man:</label>
                            <select class="form-control" name="salesman_id" id="salesmanSelect" autocomplete="off" onchange="updateSalesmanName()">
                                <option value="">Select</option>
                                @foreach($salesmen as $salesman)
                                    <option value="{{ $salesman->id }}" data-name="{{ $salesman->name }}">{{ $salesman->code ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-sm" id="chooseItemsBtn" onclick="openChooseItemsModal()" style="width: 100%;">
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
                                    <select class="form-control" name="cash" id="cash" style="width: 60px;">
                                        <option value="N" selected>N</option>
                                        <option value="Y">Y</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>Transfer:</label>
                                    <select class="form-control" name="transfer" id="transfer" style="width: 60px;">
                                        <option value="N" selected>N</option>
                                        <option value="Y">Y</option>
                                    </select>
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
                        
                        <div class="row g-2 mt-1">
                            <div class="col-md-12">
                                <div class="d-flex gap-2">
                                    <div class="field-group flex-grow-1">
                                        <label>DUE:</label>
                                        <input type="text" class="form-control readonly-field" name="due" readonly value="0.00">
                                    </div>
                                    <div class="field-group flex-grow-1">
                                        <label>PDC:</label>
                                        <input type="text" class="form-control readonly-field" name="pdc" readonly value="0.00">
                                    </div>
                                    <div class="field-group flex-grow-1">
                                        <label>TOTAL:</label>
                                        <input type="text" class="form-control readonly-field" name="total" id="totalAmount" readonly value="0.00" style="font-weight: bold;">
                                    </div>
                                </div>
                            </div>
                        </div>
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
            
            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="saveSale()">
                    <i class="bi bi-save"></i> Save
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

<!-- Pending Challan Modal Backdrop -->
<div id="pendingChallanBackdrop" class="pending-orders-backdrop"></div>

<!-- Pending Challan Modal -->
<div id="pendingChallanModal" class="pending-orders-modal" style="max-width: 650px;">
    <div class="pending-orders-content">
        <div class="pending-orders-header" style="background: #ffc107; padding: 8px 15px;">
            <h5 class="pending-orders-title" style="font-size: 14px;"><i class="bi bi-exclamation-triangle me-2"></i> Pending Sale Challans</h5>
            <button type="button" class="btn-close-modal" onclick="closePendingChallanModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body" style="padding: 10px;">
            <div class="alert alert-warning mb-2 py-2" style="font-size: 11px;">
                <i class="bi bi-info-circle me-1"></i>
                <strong>This customer has pending sale challans.</strong> You can convert a challan to sale invoice (stock already deducted) or skip to add new items.
            </div>
            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #fff3cd; z-index: 10;">
                        <tr>
                            <th style="width: 40px; padding: 5px;">Select</th>
                            <th style="width: 100px; padding: 5px;">Challan No</th>
                            <th style="width: 90px; padding: 5px;">Date</th>
                            <th style="width: 70px; padding: 5px; text-align: center;">Generate</th>
                            <th style="width: 100px; text-align: right; padding: 5px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="pendingChallanBody">
                        <!-- Pending challans will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pending-orders-footer" style="padding: 8px 15px;">
            <button type="button" class="btn btn-success btn-sm" id="loadChallanBtn" onclick="loadSelectedChallan()" disabled style="font-size: 11px;">
                <i class="bi bi-arrow-down-circle"></i> Load Selected Challan
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="skipChallanAndOpenItems()" style="font-size: 11px;">
                <i class="bi bi-plus-circle"></i> Skip & Add New Items
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closePendingChallanModal()" style="font-size: 11px;">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
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

<script>
// Global variables
let itemsData = [];
let itemIndex = -1;
let currentSelectedRowIndex = null;
let currentDiscountRowIndex = null; // Track which row's discount is being edited
let companyDiscounts = {}; // Store company-wise discounts for this session
let pendingItemSelection = null; // Store item data when waiting for batch selection
let rowGstData = {}; // Store GST calculations for each row
let selectedChallanId = null; // Store selected challan ID for loading
let pendingBarcodeRowIndex = null; // 🔥 Store row index when barcode is entered for batch selection

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
});

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

// Fetch Customer Due Amount
function fetchCustomerDue() {
    const customerSelect = document.getElementById('customerSelect');
    const customerId = customerSelect.value;
    
    // Reset if no customer selected
    if (!customerId) {
        document.querySelector('input[name="due"]').value = '0.00';
        document.getElementById('totalAmount').value = '0.00';
        return;
    }
    
    // Fetch customer's outstanding due
    fetch(`{{ url('admin/sale/customer') }}/${customerId}/due`, {
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
            document.querySelector('input[name="due"]').value = dueAmount.toFixed(2);
            document.getElementById('totalAmount').value = dueAmount.toFixed(2);
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
    const dateInput = document.getElementById('saleDate');
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
    const chooseItemsBtn = document.getElementById('chooseItemsBtn');
    
    if (chooseItemsBtn) {
        if (customerId) {
            // Customer selected - enable button (visual only)
            chooseItemsBtn.classList.remove('btn-secondary', 'btn-warning');
            chooseItemsBtn.classList.add('btn-info');
            chooseItemsBtn.style.opacity = '1';
            chooseItemsBtn.title = 'Click to choose items';
        } else {
            // Customer not selected - show as warning (but keep clickable for validation message)
            chooseItemsBtn.classList.remove('btn-info');
            chooseItemsBtn.classList.add('btn-warning');
            chooseItemsBtn.style.opacity = '0.7';
            chooseItemsBtn.title = 'Please select Customer first';
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
    
    // Note: Salesman is optional - no validation required
    
    // Check for pending challans first
    checkPendingChallans(customerId);
}

// Check if customer has pending challans
function checkPendingChallans(customerId) {
    fetch(`{{ url('admin/sale-challan/pending') }}?customer_id=${customerId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.challans && data.challans.length > 0) {
            // Customer has pending challans - show modal
            displayPendingChallans(data.challans);
            openPendingChallanModal();
        } else {
            // No pending challans - directly open items modal
            openItemsModalDirectly();
        }
    })
    .catch(error => {
        console.error('Error checking pending challans:', error);
        // On error, just open items modal
        openItemsModalDirectly();
    });
}

// Display pending challans in modal
function displayPendingChallans(challans) {
    const tbody = document.getElementById('pendingChallanBody');
    tbody.innerHTML = '';
    
    challans.forEach(challan => {
        const date = new Date(challan.challan_date);
        const formattedDate = date.toLocaleDateString('en-GB');
        
        // Get item names - now directly from transformed data
        let itemNames = challan.item_names || 'N/A';
        let itemsCount = challan.items_count || 0;
        
        // Generate status - N means not invoiced yet
        const generateStatus = challan.is_invoiced ? 'Y' : 'N';
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center" style="padding: 5px;">
                <input type="radio" name="selectedChallan" value="${challan.id}" 
                       onchange="selectChallanForLoad(${challan.id})" style="cursor: pointer;">
            </td>
            <td style="padding: 5px;"><strong>${challan.challan_no}</strong></td>
            <td style="padding: 5px;">${formattedDate}</td>
            <td class="text-center" style="padding: 5px;"><span class="badge ${generateStatus === 'Y' ? 'bg-success' : 'bg-danger'}">${generateStatus}</span></td>
            <td class="text-end" style="padding: 5px;"><strong>${parseFloat(challan.net_amount || 0).toFixed(2)}</strong></td>
        `;
        row.style.cursor = 'pointer';
        row.onclick = function(e) {
            if (e.target.type !== 'radio') {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                selectChallanForLoad(challan.id);
            }
        };
        tbody.appendChild(row);
    });
}

// Select challan for loading
function selectChallanForLoad(challanId) {
    selectedChallanId = challanId;
    document.getElementById('loadChallanBtn').disabled = false;
}

// Open pending challan modal
function openPendingChallanModal() {
    const modal = document.getElementById('pendingChallanModal');
    const backdrop = document.getElementById('pendingChallanBackdrop');
    
    setTimeout(() => {
        modal.classList.add('show');
        backdrop.classList.add('show');
    }, 10);
}

// Close pending challan modal
function closePendingChallanModal() {
    const modal = document.getElementById('pendingChallanModal');
    const backdrop = document.getElementById('pendingChallanBackdrop');
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    selectedChallanId = null;
    document.getElementById('loadChallanBtn').disabled = true;
}

// Skip challan and open items modal
function skipChallanAndOpenItems() {
    closePendingChallanModal();
    openItemsModalDirectly();
}

// Open items modal directly (without checking challans)
function openItemsModalDirectly() {
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

// Load selected challan items into sale
function loadSelectedChallan() {
    if (!selectedChallanId) {
        showAlert('Please select a challan first', 'warning', 'Selection Required');
        return;
    }
    
    // Fetch challan details
    fetch(`{{ url('admin/sale-challan/modification') }}/${selectedChallanId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Challan response:', data);
        // Controller returns 'transaction' not 'challan'
        if (data.success && data.transaction) {
            closePendingChallanModal();
            loadChallanItemsToSale(data.transaction);
        } else {
            showAlert('Error loading challan: ' + (data.message || 'Unknown error'), 'error', 'Error');
        }
    })
    .catch(error => {
        console.error('Error loading challan:', error);
        showAlert('Error loading challan details', 'error', 'Error');
    });
}

// Load challan items into sale table
function loadChallanItemsToSale(challan) {
    // Store challan ID for reference when saving
    window.loadedChallanId = challan.id;
    
    // Clear existing items
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    itemIndex = -1;
    
    // Load each item from challan - same structure as normal addItemToTable
    if (challan.items && challan.items.length > 0) {
        challan.items.forEach((item, index) => {
            itemIndex++;
            
            // Format expiry date
            let expiryDisplay = '';
            if (item.expiry_date) {
                try {
                    const date = new Date(item.expiry_date);
                    expiryDisplay = date.toLocaleDateString('en-GB', { month: '2-digit', year: '2-digit' }).replace('/', '/');
                } catch (e) {
                    expiryDisplay = item.expiry_date;
                }
            }
            
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-row-index', itemIndex);
            newRow.setAttribute('data-item-id', item.item_id || '');
            newRow.setAttribute('data-hsn-code', item.hsn_code || '');
            newRow.setAttribute('data-cgst', item.cgst_percent || 0);
            newRow.setAttribute('data-sgst', item.sgst_percent || 0);
            newRow.setAttribute('data-cess', item.cess_percent || 0);
            newRow.setAttribute('data-batch-id', item.batch_id || '');
            newRow.setAttribute('data-from-challan', 'true'); // Mark as from challan - no stock deduction needed
            newRow.setAttribute('data-packing', item.packing || '');
            newRow.setAttribute('data-unit', item.unit || '');
            newRow.setAttribute('data-company', item.company_name || '');
            newRow.setAttribute('data-complete', 'false'); // Same as normal - incomplete initially
            newRow.style.cursor = 'pointer';
            
            // Row click handler - same as normal sale
            newRow.addEventListener('click', function(e) {
                const clickedRow = e.currentTarget;
                const rowIdx = parseInt(clickedRow.getAttribute('data-row-index'));
                selectRow(rowIdx);
            });
            
            // Controller returns item_code, item_name directly (not nested in item object)
            newRow.innerHTML = `
                <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][code]" value="${item.item_code || ''}" style="font-size: 10px;" autocomplete="off"></td>
                <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][item_name]" value="${item.item_name || ''}" style="font-size: 10px;" autocomplete="off"></td>
                <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][batch]" value="${item.batch_no || ''}" style="font-size: 10px;" autocomplete="off"></td>
                <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][expiry]" value="${expiryDisplay}" style="font-size: 10px;" autocomplete="off"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-qty" name="items[${itemIndex}][qty]" id="qty_${itemIndex}" value="${item.qty || ''}" placeholder="0" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][free_qty]" id="free_qty_${itemIndex}" value="${item.free_qty || 0}" style="font-size: 10px;"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-rate" name="items[${itemIndex}][rate]" id="rate_${itemIndex}" value="${parseFloat(item.sale_rate || 0).toFixed(2)}" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-discount" name="items[${itemIndex}][discount]" id="discount_${itemIndex}" value="${item.discount_percent || ''}" placeholder="0" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][mrp]" id="mrp_${itemIndex}" value="${parseFloat(item.mrp || 0).toFixed(2)}" step="0.01" style="font-size: 10px;" readonly></td>
                <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][amount]" id="amount_${itemIndex}" value="${parseFloat(item.net_amount || 0).toFixed(2)}" style="font-size: 10px;" readonly></td>
                <td class="p-0 text-center">
                    <input type="hidden" name="items[${itemIndex}][batch_id]" value="${item.batch_id || ''}">
                    <input type="hidden" name="items[${itemIndex}][from_challan]" value="true">
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${itemIndex})" title="Delete Row" style="font-size: 9px; padding: 2px 5px;">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
            
            // Add event listeners - SAME as normal sale module
            addRowEventListeners(newRow, itemIndex);
            
            // Update row color - SAME as normal sale module
            updateRowColor(itemIndex);
            
            // Calculate row amount
            calculateRowAmount(itemIndex);
        });
    }
    
    // Select first row and focus on Qty - SAME as normal sale module
    if (challan.items && challan.items.length > 0) {
        selectRow(0);
        updateDetailedSummary(0);
        
        setTimeout(() => {
            const qtyField = document.getElementById('qty_0');
            if (qtyField) {
                qtyField.focus();
            }
        }, 100);
    }
    
    // Calculate totals - SAME as normal sale module
    calculateTotal();
    
    // Show success message
    showAlert(`Challan ${challan.challan_no} loaded successfully! ${challan.items?.length || 0} items added.`, 'success', 'Challan Loaded');
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
    pendingBarcodeRowIndex = null; // 🔥 Clear barcode row index when modal is closed
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
// 🔥 MODIFIED: Now handles both cases:
// 1. Barcode entry - populates existing row (pendingBarcodeRowIndex is set)
// 2. Choose Items modal - adds new row (pendingBarcodeRowIndex is null)
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
    
    // 🔥 Check if this is from barcode entry (existing row) or Choose Items modal (new row)
    if (pendingBarcodeRowIndex !== null) {
        // 🔥 CASE 1: From barcode entry - populate the existing row
        console.log('📱 Populating existing row from barcode entry, row index:', pendingBarcodeRowIndex);
        populateRowWithItemAndBatch(pendingBarcodeRowIndex, pendingItemSelection, selectedBatch);
        
        // Clear the pending barcode row index
        pendingBarcodeRowIndex = null;
    } else {
        // 🔥 CASE 2: From Choose Items modal - add item to table (creates new row or uses empty row)
        console.log('➕ Adding new item from Choose Items modal');
        addItemToTable(pendingItemSelection, selectedBatch);
    }
    
    // Close batch modal
    closeBatchSelectionModal();
    
    // Clear selected batch
    window.selectedBatch = null;
}

// 🔥 NEW FUNCTION: Populate an existing row with item and batch data (for barcode entry)
function populateRowWithItemAndBatch(rowIndex, item, batch) {
    const row = document.querySelector(`tr[data-row-index="${rowIndex}"]`);
    if (!row) {
        console.error('Row not found for index:', rowIndex);
        return;
    }
    
    console.log('🔄 Populating row', rowIndex, 'with item:', item.name, 'batch:', batch.batch_no);
    
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
    
    // Get input fields
    const codeInput = row.querySelector('input[name*="[code]"]');
    const nameInput = row.querySelector('input[name*="[item_name]"]');
    const batchInput = row.querySelector('input[name*="[batch]"]');
    const expiryInput = row.querySelector('input[name*="[expiry]"]');
    const rateInput = row.querySelector('input[name*="[rate]"]');
    const mrpInput = row.querySelector('input[name*="[mrp]"]');
    const qtyInput = row.querySelector('input[name*="[qty]"]');
    
    // Populate fields
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
    row.setAttribute('data-case-qty', item.case_qty || 0);
    row.setAttribute('data-box-qty', item.box_qty || 0);
    
    // Store batch purchase details
    row.setAttribute('data-batch-purchase-rate', batch.avg_pur_rate || batch.pur_rate || 0);
    row.setAttribute('data-batch-cost-gst', batch.avg_cost_gst || batch.cost_gst || 0);
    row.setAttribute('data-batch-supplier', batch.supplier_name || '');
    row.setAttribute('data-batch-purchase-date', batch.purchase_date_display || batch.purchase_date || '');
    
    // 🔥 IMPORTANT: Store batch ID for quantity reduction (must be number)
    const batchId = batch.id ? parseInt(batch.id) : '';
    row.setAttribute('data-batch-id', batchId);
    console.log('🔑 Batch ID stored in row:', batchId);
    
    // Calculate row amount
    calculateRowAmount(rowIndex);
    
    // Update row color
    updateRowColor(rowIndex);
    
    // Select the row
    selectRow(rowIndex);
    
    // Update calculation section
    updateCalculationSection(rowIndex);
    
    // Update detailed summary
    updateDetailedSummary(rowIndex);
    
    // Calculate totals
    calculateTotal();
    calculateSummary();
    
    // Focus on qty field for immediate entry
    setTimeout(() => {
        if (qtyInput) {
            qtyInput.focus();
            qtyInput.select();
        }
    }, 100);
    
    console.log('✅ Row populated successfully');
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
    row.setAttribute('data-company-id', item.company_id || '');
    row.setAttribute('data-company-name', item.company_name || item.company || '');
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
    
    // Store original discount for change detection
    const itemDiscount = getDiscountForItem(item.id, item.company_id);
    row.setAttribute('data-original-discount', itemDiscount || '0');
    
    // Apply discount if available
    const discountInput = document.getElementById(`discount_${rowIndex}`);
    if (discountInput && itemDiscount > 0) {
        discountInput.value = itemDiscount;
    }
    
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
    
    // Get discount for this item (from item or company)
    const itemDiscount = getDiscountForItem(item.id, item.company_id);
    
    if (existingEmptyRow) {
        // Reuse existing empty row (populateExistingRow handles discount)
        console.log('✅ Found empty row, reusing it');
        targetRowIndex = populateExistingRow(existingEmptyRow, item, batch);
        targetRow = existingEmptyRow;
    } else {
        // No empty row found, create a new one
        console.log('➕ No empty row found, creating new row');
        itemIndex++;
        const tbody = document.getElementById('itemsTableBody');
        
        targetRow = document.createElement('tr');
        targetRow.setAttribute('data-row-index', itemIndex);
        targetRow.setAttribute('data-item-id', item.id);
        targetRow.setAttribute('data-company-id', item.company_id || '');
        targetRow.setAttribute('data-company-name', item.company_name || item.company || '');
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
        
        // Apply discount value
        const discountValue = itemDiscount > 0 ? itemDiscount : '';
        
        targetRow.innerHTML = `
            <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][code]" value="${item.bar_code || ''}" style="font-size: 10px;" autocomplete="off"></td>
            <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][item_name]" value="${item.name || ''}" style="font-size: 10px;" autocomplete="off"></td>
            <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][batch]" value="${batch.batch_no || ''}" style="font-size: 10px;" autocomplete="off"></td>
            <td class="p-0"><input type="text" class="form-control form-control-sm border-0" name="items[${itemIndex}][expiry]" value="${expiryDisplay}" style="font-size: 10px;" autocomplete="off"></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-qty" name="items[${itemIndex}][qty]" id="qty_${itemIndex}" value="" placeholder="0" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0" name="items[${itemIndex}][free_qty]" id="free_qty_${itemIndex}" value="0" style="font-size: 10px;"></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-rate" name="items[${itemIndex}][rate]" id="rate_${itemIndex}" value="${rate.toFixed(2)}" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
            <td class="p-0"><input type="number" class="form-control form-control-sm border-0 item-discount" name="items[${itemIndex}][discount]" id="discount_${itemIndex}" value="${discountValue}" placeholder="0" step="0.01" style="font-size: 10px;" data-row="${itemIndex}" onchange="calculateRowAmount(${itemIndex})" oninput="calculateRowAmount(${itemIndex})"></td>
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
        
        // Store original discount for change detection
        targetRow.setAttribute('data-original-discount', discountValue || '0');
        
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
    
    // Focus on Qty field after a small delay to ensure DOM is ready
    setTimeout(() => {
        const qtyField = document.getElementById(`qty_${targetRowIndex}`);
        if (qtyField) {
            qtyField.focus();
            // Don't select - let user type directly
        }
    }, 100);
    
    // Calculate totals
    calculateTotal();
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
    
    // Discount field - Enter shows discount options modal if value CHANGED from original
    if (discountInput) {
        // Store original discount value when field gets focus
        discountInput.addEventListener('focus', function() {
            const rowIndex = parseInt(this.getAttribute('data-row'));
            const row = document.querySelector(`#itemsTableBody tr[data-row-index="${rowIndex}"]`);
            const originalDiscount = parseFloat(row?.getAttribute('data-original-discount') || this.value || 0);
            // Store original value if not already stored
            if (!row?.hasAttribute('data-original-discount')) {
                row?.setAttribute('data-original-discount', this.value || '0');
            }
        });
        
        discountInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const currentValue = parseFloat(this.value) || 0;
                const row = document.querySelector(`#itemsTableBody tr[data-row-index="${rowIndex}"]`);
                const originalValue = parseFloat(row?.getAttribute('data-original-discount') || 0);
                
                // Show modal if discount value has CHANGED from original (including changing TO 0 to remove)
                if (currentValue !== originalValue) {
                    showDiscountOptionsModal(rowIndex, currentValue);
                } else {
                    // No change, just move to next row
                    calculateRowAmount(rowIndex);
                    calculateTotal();
                    moveToNextRow(rowIndex);
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
// 🔥 MODIFIED: Now opens batch selection modal first, then populates row after batch selection
function fetchItemDetailsForRow(itemCode, rowIndex) {
    const url = `{{ url('/admin/items/get-by-code') }}/${itemCode}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {
                console.log('📱 Barcode scanned! Item found:', data.item.name);
                console.log('🔢 Item ID:', data.item.id);
                
                // 🔥 Store the row index for later population after batch selection
                pendingBarcodeRowIndex = rowIndex;
                
                // 🔥 Store item data for batch selection (similar to pendingItemSelection)
                // Create item object matching the format expected by openBatchSelectionModal
                const itemForBatch = {
                    id: data.item.id,
                    name: data.item.name,
                    packing: data.item.packing,
                    bar_code: itemCode,
                    hsn_code: data.item.hsn_code,
                    s_rate: data.item.s_rate,
                    mrp: data.item.mrp,
                    cgst_percent: data.item.cgst_percent,
                    sgst_percent: data.item.sgst_percent,
                    cess_percent: data.item.cess_percent,
                    case_qty: data.item.case_qty,
                    box_qty: data.item.box_qty,
                    unit: data.item.unit || '1',
                    company_name: data.item.company_name || data.item.company || ''
                };
                
                // 🔥 Store item for batch selection
                pendingItemSelection = itemForBatch;
                
                // 🔥 Open batch selection modal
                openBatchSelectionModal(itemForBatch);
                
            } else {
                console.log('❌ Item not found for barcode:', itemCode);
                showToast(`Item not found for barcode: ${itemCode}`, 'error', 'Item Not Found');
            }
        })
        .catch(error => {
            console.error('Error fetching item:', error);
            showToast('Error fetching item details', 'error', 'Error');
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
    
    document.getElementById('totalAmount').value = total.toFixed(2);
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

// ============================================
// DISCOUNT OPTIONS MODAL FUNCTIONS
// ============================================

// Show discount options modal
function showDiscountOptionsModal(rowIndex, discountValue) {
    currentDiscountRowIndex = rowIndex;
    
    // Get item and company info from the row
    const row = document.querySelector(`#itemsTableBody tr[data-row-index="${rowIndex}"]`);
    const itemName = row?.querySelector('input[name*="[item_name]"]')?.value || 'Unknown Item';
    const companyName = row?.getAttribute('data-company-name') || row?.querySelector('input[name*="[company_name]"]')?.value || 'Unknown Company';
    
    // Update modal content
    document.getElementById('discountItemName').textContent = itemName;
    document.getElementById('discountCompanyName').textContent = companyName;
    
    // Show appropriate message based on discount value
    if (discountValue === 0) {
        document.getElementById('discountValue').textContent = 'Remove Discount';
        document.getElementById('discountValue').style.color = '#dc3545'; // Red color for removal
    } else {
        document.getElementById('discountValue').textContent = discountValue + '%';
        document.getElementById('discountValue').style.color = '#28a745'; // Green color for setting
    }
    
    // Show modal
    document.getElementById('discountOptionsBackdrop').style.display = 'block';
    document.getElementById('discountOptionsModal').style.display = 'block';
    setTimeout(() => {
        document.getElementById('discountOptionsBackdrop').classList.add('show');
        document.getElementById('discountOptionsModal').classList.add('show');
    }, 10);
}

// Close discount options modal
function closeDiscountOptionsModal() {
    document.getElementById('discountOptionsBackdrop').classList.remove('show');
    document.getElementById('discountOptionsModal').classList.remove('show');
    setTimeout(() => {
        document.getElementById('discountOptionsBackdrop').style.display = 'none';
        document.getElementById('discountOptionsModal').style.display = 'none';
    }, 300);
    
    // Continue with row calculation and move to next
    if (currentDiscountRowIndex !== null) {
        calculateRowAmount(currentDiscountRowIndex);
        calculateTotal();
        moveToNextRow(currentDiscountRowIndex);
        currentDiscountRowIndex = null;
    }
}

// Apply discount option
function applyDiscountOption(option) {
    const rowIndex = currentDiscountRowIndex;
    const row = document.querySelector(`#itemsTableBody tr[data-row-index="${rowIndex}"]`);
    const discountValue = parseFloat(document.getElementById(`discount_${rowIndex}`)?.value) || 0;
    const itemId = row?.getAttribute('data-item-id');
    const companyId = row?.getAttribute('data-company-id');
    const companyName = row?.getAttribute('data-company-name') || '';
    
    // Determine if this is a removal (discount = 0)
    const isRemoval = discountValue === 0;
    const actionText = isRemoval ? 'removed' : `set to ${discountValue}%`;
    
    // Disable all buttons to prevent multiple clicks
    disableDiscountModalButtons();
    
    switch(option) {
        case 'temporary':
            // Just close modal and continue - discount already applied to this row
            // Update original discount so it won't trigger modal again for same value
            row?.setAttribute('data-original-discount', discountValue.toString());
            showToast(`Discount ${actionText} temporarily`, 'success');
            closeDiscountOptionsModal();
            enableDiscountModalButtons();
            break;
            
        case 'company':
            // Save discount to company INSTANTLY to database
            if (companyId) {
                // Show loading state
                showToast('Saving discount to company...', 'info');
                
                // Save to database FIRST (instant save)
                saveDiscountToCompany(companyId, discountValue, function(success) {
                    if (success) {
                        // Store in session for this transaction
                        companyDiscounts[companyId] = discountValue;
                        
                        // Apply to all existing rows with same company and update their original discount
                        applyCompanyDiscountToAllRows(companyId, discountValue);
                        
                        // Update current row's original discount
                        row?.setAttribute('data-original-discount', discountValue.toString());
                        
                        if (isRemoval) {
                            showToast(`✅ Discount removed for company: ${companyName}`, 'success');
                        } else {
                            showToast(`✅ Discount ${discountValue}% saved for company: ${companyName}`, 'success');
                        }
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
            // Save discount to item INSTANTLY to database
            if (itemId) {
                // Show loading state
                showToast('Saving discount to item...', 'info');
                
                // Save to database FIRST (instant save)
                saveDiscountToItem(itemId, discountValue, function(success) {
                    if (success) {
                        // Update original discount
                        row?.setAttribute('data-original-discount', discountValue.toString());
                        
                        if (isRemoval) {
                            showToast('✅ Discount removed permanently for this item', 'success');
                        } else {
                            showToast(`✅ Discount ${discountValue}% saved permanently for this item`, 'success');
                        }
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

// Disable discount modal buttons during save
function disableDiscountModalButtons() {
    const buttons = ['discountBtnTemporary', 'discountBtnCompany', 'discountBtnItem'];
    buttons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.6';
            btn.style.cursor = 'not-allowed';
        }
    });
}

// Enable discount modal buttons after save
function enableDiscountModalButtons() {
    const buttons = ['discountBtnTemporary', 'discountBtnCompany', 'discountBtnItem'];
    buttons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        }
    });
}

// Apply company discount to all rows with same company
function applyCompanyDiscountToAllRows(companyId, discountValue) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    rows.forEach(row => {
        const rowCompanyId = row.getAttribute('data-company-id');
        if (rowCompanyId == companyId) {
            const rowIndex = row.getAttribute('data-row-index');
            const discountInput = document.getElementById(`discount_${rowIndex}`);
            if (discountInput && !discountInput.value) {
                // Only apply if discount is not already set
                discountInput.value = discountValue;
                // Update original discount so it won't trigger modal again
                row.setAttribute('data-original-discount', discountValue.toString());
                calculateRowAmount(parseInt(rowIndex));
            }
        }
    });
    calculateTotal();
    calculateSummary();
}

// Save discount to company via API - INSTANT SAVE with callback
function saveDiscountToCompany(companyId, discountValue, callback) {
    console.log('🔵 Saving company discount:', { companyId, discountValue });
    
    fetch('{{ route("admin.sale.saveCompanyDiscount") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            company_id: companyId,
            discount_percent: discountValue
        })
    })
    .then(response => {
        console.log('📥 Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📦 Response data:', data);
        if (data.success) {
            console.log('✅ Company discount saved successfully:', data.message);
            if (callback) callback(true);
        } else {
            console.error('❌ Failed to save company discount:', data.message);
            alert('Error: ' + data.message);
            if (callback) callback(false);
        }
    })
    .catch(error => {
        console.error('❌ Error saving company discount:', error);
        alert('Network error: ' + error.message);
        if (callback) callback(false);
    });
}

// Save discount to item via API - INSTANT SAVE with callback
function saveDiscountToItem(itemId, discountValue, callback) {
    console.log('🔵 Saving item discount:', { itemId, discountValue });
    
    fetch('{{ route("admin.sale.saveItemDiscount") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            item_id: itemId,
            discount_percent: discountValue
        })
    })
    .then(response => {
        console.log('📥 Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📦 Response data:', data);
        if (data.success) {
            console.log('✅ Item discount saved successfully:', data.message);
            if (callback) callback(true);
        } else {
            console.error('❌ Failed to save item discount:', data.message);
            alert('Error: ' + data.message);
            if (callback) callback(false);
        }
    })
    .catch(error => {
        console.error('❌ Error saving item discount:', error);
        alert('Network error: ' + error.message);
        if (callback) callback(false);
    });
}

// Get discount for item (checks item first, then company)
function getDiscountForItem(itemId, companyId) {
    // First check session company discounts
    if (companyId && companyDiscounts[companyId]) {
        return companyDiscounts[companyId];
    }
    
    // Check from itemsData (loaded from server with discounts)
    const item = itemsData.find(i => i.id == itemId);
    if (item) {
        // Item discount takes priority
        if (item.fixed_dis_percent && parseFloat(item.fixed_dis_percent) > 0) {
            return parseFloat(item.fixed_dis_percent);
        }
        // Then company discount
        if (item.company_discount && parseFloat(item.company_discount) > 0) {
            return parseFloat(item.company_discount);
        }
    }
    
    return 0;
}

// Show toast notification
function showToast(message, type = 'info') {
    // Try SweetAlert2 first
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: type,
            title: message
        });
        return;
    }
    
    // Try toastr
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
        return;
    }
    
    // Fallback to alert
    alert(message);
}

// Close modal on backdrop click
document.getElementById('discountOptionsBackdrop')?.addEventListener('click', closeDiscountOptionsModal);

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
        alert('Please select a row to delete');
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
            
            // Check if item is from challan (hidden input or row attribute)
            const fromChallanInput = row.querySelector('input[name*="[from_challan]"]');
            const fromChallan = fromChallanInput?.value === 'true' || row.getAttribute('data-from-challan') === 'true';
            
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
                row_order: index,
                from_challan: fromChallan // 🔥 Skip stock deduction if from challan
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
        challan_id: window.loadedChallanId || null, // 🔥 Challan ID if converting from challan
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