@extends('layouts.admin')

@section('title', 'Purchase Modification')

@section('content')
<style>
    /* Scoped styles - only affect content area, not sidebar */
    .content .compact-form {
        padding: 8px;
    }
    
    .content .compact-form label {
        font-weight: 600;
        font-size: 11px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .content .compact-form input,
    .content .compact-form select {
        font-size: 11px;
        padding: 2px 6px;
        height: 26px;
    }
    
    .content .compact-form .form-control:focus {
        box-shadow: none;
        border-color: #0d6efd;
    }
    
    .content .header-section {
        background: white;
        border: 1px solid #dee2e6;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 4px;
    }
    
    .content .header-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 6px;
    }
    
    .content .field-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .content .inner-card {
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
        height: 45px; /* Fixed row height for consistent 6-row display */
    }
    
    .table-compact th {
        background: #e9ecef;
        font-weight: 600;
        text-align: center;
        border: 1px solid #dee2e6;
        height: 40px; /* Fixed header height */
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
        /* Header: 40px + 6 Rows: 6 × 45px = 310px total */
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
    
    /* Row selection highlight */
    .row-selected {
        background-color: #d4edff !important;
        border: 2px solid #007bff !important;
    }
    
    .row-selected td {
        background-color: #d4edff !important;
    }
    
    /* Row calculation status colors */
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
    
    /* When all rows are complete, make them all green */
    .all-rows-complete .row-complete {
        background-color: #c8e6c9 !important;
        color: #1b5e20 !important;
    }
    
    .all-rows-complete .row-complete td {
        background-color: #c8e6c9 !important;
        color: #1b5e20 !important;
    }
    
    .all-rows-complete .row-complete input {
        background-color: #c8e6c9 !important;
        color: #1b5e20 !important;
    }
    
    /* Pending Orders Modal Styles */
    .pending-orders-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: calc(50% + 130px); /* Account for sidebar width (260px / 2 = 130px) */
        transform: translate(-50%, -50%) scale(0.7);
        width: 90%;
        max-width: 900px;
        max-height: 90vh;
        z-index: 10000; /* Higher than sidebar z-index (1030) */
        opacity: 0;
        transition: all 0.3s ease-in-out;
        overflow: hidden;
    }
    
    .pending-orders-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    
    /* Specific styling for invoice list modal */
    #invoiceListModal {
        max-width: 1200px;
        max-height: 90vh;
        /* Adjust left position for invoice modal */
        left: calc(50% + 130px);
    }
    
    /* When sidebar is collapsed, adjust positioning */
    .collapsed .pending-orders-modal,
    .collapsed #invoiceListModal {
        left: calc(50% + 36px); /* Account for collapsed sidebar (72px / 2 = 36px) */
    }
    
    #invoiceListModal .pending-orders-content {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    
    /* Mobile responsive - center modal on mobile */
    @media (max-width: 991.98px) {
        .pending-orders-modal,
        #invoiceListModal {
            left: 50%; /* Center on mobile since sidebar is hidden */
        }
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
        z-index: 9999; /* Below modal but above everything else */
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .pending-orders-backdrop.show {
        display: block;
        opacity: 1;
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
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid rgba(0, 0, 0, 0.1);
    }
    
    .alert-modal-header.success {
        background: #28a745;
        border-bottom-color: #1e7e34;
    }
    
    .alert-modal-header.error {
        background: #dc3545;
        border-bottom-color: #c82333;
    }
    
    .alert-modal-header.warning {
        background: #ffc107;
        color: #212529;
        border-bottom-color: #e0a800;
    }
    
    .alert-modal-header.info {
        background: #17a2b8;
        border-bottom-color: #138496;
    }
    
    .alert-modal-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        letter-spacing: 1px;
    }
    
    .btn-close-modal {
        background: transparent;
        border: none;
        color: inherit;
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
    
    .alert-modal-body {
        padding: 1.5rem;
        background: #fff;
        white-space: pre-line;
        line-height: 1.5;
    }
    
    .alert-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
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
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil-square me-2"></i> Purchase Modification</h4>
        <div class="text-muted small">Modify existing purchase transaction</div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body compact-form">
    <form id="purchaseForm" method="POST" autocomplete="off" onsubmit="return false;">
        @csrf
        
        <!-- Header Section -->
        <div class="header-section">
            <!-- Row 1: Date, Supplier -->
            <div class="header-row">
                <div class="field-group">
                    <label>Bill / Ledger Date</label>
                    <input type="date" class="form-control" name="bill_date" id="billDate" value="{{ date('Y-m-d') }}" style="width: 140px;" onchange="updateDayName()">
                    <input type="text" class="form-control readonly-field" id="dayName" value="{{ date('l') }}" readonly style="width: 90px;">
                </div>
                
                <div class="field-group">
                    <label>Supplier:</label>
                    <select class="form-control" name="supplier_id" id="supplierSelect" style="width: 250px;" autocomplete="off">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Row 2: Bill No, Trn No, Inner Card -->
            <div class="d-flex gap-3">
                <!-- Left Side -->
               
                
                <!-- Right Side - Inner Card -->
                <div class="inner-card flex-grow-1">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="field-group">
                                <label style="width: 100px;">Receive Date</label>
                                <input type="date" class="form-control" name="receive_date" id="receiveDate" value="{{ date('Y-m-d') }}">
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
                        <div class="col-md-7">
                            <div class="field-group">
                                <label>Remarks:</label>
                                <input type="text" class="form-control" name="remarks" id="remarks">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="field-group">
                                <label style="width: 80px;">Due Date:</label>
                                <input type="date" class="form-control" name="due_date" id="dueDate" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
                 <div style="width: 200px;">
                    <div class="field-group mb-2">
                        <label style="width: 60px;">Bill No.:</label>
                        <input type="text" class="form-control" name="bill_no" id="billNo" placeholder="1111">
                    </div>
                    <div class="field-group mb-2">
                        <label style="width: 60px;">Trn.No.:</label>
                        <input type="text" class="form-control" name="trn_no" id="trnNo" placeholder="Enter Trn No">
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-success" onclick="fetchBillByTrnNo()" style="width: 100%;">
                            <i class="bi bi-download"></i> Fetch Bill
                        </button>
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
                            <th style="width: 60px;">F.Qty.</th>
                            <th style="width: 80px;">Pur. Rate</th>
                            <th style="width: 60px;">Dis.%</th>
                            <th style="width: 80px;">MRP</th>
                            <th style="width: 90px;">Amount</th>
                            <th style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <!-- Rows will be added dynamically when pending order is loaded or via Add Row button -->
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
        
        <!-- Calculation Section -->
        <div class="bg-white border rounded p-3 mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="d-flex align-items-start border rounded p-3" style="font-size: 11px; background: #fafafa; gap: 30px;">
                <!-- HSN Code Block (First) -->
                <div class="d-flex flex-column gap-2" style="min-width: 180px;">
                    <!-- HSN Code -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0" style="min-width: 80px;"><strong>HSN Code:</strong></label>
                        <input type="text" class="form-control readonly-field text-center" id="calc_hsn_display" readonly style="width: 110px; height: 28px;" value="---">
                    </div>
                    
                    <!-- CGST(%) -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0" style="min-width: 80px;"><strong>CGST(%):</strong></label>
                        <input type="text" class="form-control readonly-field text-center" id="calc_cgst" readonly style="width: 110px; height: 28px;" value="0">
                    </div>
                    
                    <!-- SGST(%) -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0" style="min-width: 80px;"><strong>SGST(%):</strong></label>
                        <input type="text" class="form-control readonly-field text-center" id="calc_sgst" readonly style="width: 110px; height: 28px;" value="0">
                    </div>
                    
                    <!-- Cess (%) -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0" style="min-width: 80px;"><strong>Cess (%):</strong></label>
                        <input type="text" class="form-control readonly-field text-center" id="calc_cess" readonly style="width: 110px; height: 28px;" value="0">
                    </div>
                </div>
                
                <!-- Right Side Fields (4 Columns) -->
                <div class="d-flex" style="gap: 25px; flex: 1;">
                    <!-- Column 1 -->
                    <div class="d-flex flex-column gap-2" style="min-width: 150px;">
                        <!-- Spl. Rate -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 70px;"><strong>Spl. Rate</strong></label>
                            <input type="number" class="form-control readonly-field" id="calc_spl_rate" readonly step="0.01" style="width: 90px; height: 28px;" value="0.00">
                        </div>
                        
                        <!-- W.S.Rate -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 70px;"><strong>W.S.Rate</strong></label>
                            <input type="number" class="form-control readonly-field" id="calc_ws_rate" readonly step="0.01" style="width: 90px; height: 28px;" value="0.00">
                        </div>
                        
                        <!-- TAX % -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 70px;"><strong>TAX %</strong></label>
                            <input type="number" class="form-control readonly-field" id="calc_tax_percent" readonly step="0.01" style="width: 90px; height: 28px;" value="0.000">
                        </div>
                    </div>
                    
                    <!-- Column 2 -->
                    <div class="d-flex flex-column gap-2" style="min-width: 160px;">
                        <!-- CGST Amt -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 80px;"><strong>CGST Amt:</strong></label>
                            <div class="border rounded px-2 py-1" style="background: #fff; min-width: 80px; text-align: right; height: 28px; display: flex; align-items: center; justify-content: flex-end;">
                                <strong id="calc_cgst_amount">0.00</strong>
                            </div>
                        </div>
                        
                        <!-- SGST Amt -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 80px;"><strong>SGST Amt:</strong></label>
                            <div class="border rounded px-2 py-1" style="background: #fff; min-width: 80px; text-align: right; height: 28px; display: flex; align-items: center; justify-content: flex-end;">
                                <strong id="calc_sgst_amount">0.00</strong>
                            </div>
                        </div>
                        
                        <!-- CESS Amt -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 80px;"><strong>CESS Amt:</strong></label>
                            <div class="border rounded px-2 py-1" style="background: #fff; min-width: 80px; text-align: right; height: 28px; display: flex; align-items: center; justify-content: flex-end;">
                                <strong id="calc_cess_amount">0.00</strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Column 3 -->
                    <div class="d-flex flex-column gap-2" style="min-width: 140px;">
                        <!-- Excise -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 55px;"><strong>Excise</strong></label>
                            <input type="number" class="form-control readonly-field" id="calc_excise" readonly step="0.01" style="width: 85px; height: 28px;" value="0.00">
                        </div>
                        
                        <!-- MRP -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 55px;"><strong>MRP</strong></label>
                            <input type="number" class="form-control readonly-field" id="calc_mrp" readonly step="0.01" style="width: 85px; height: 28px;" value="0.00">
                        </div>
                        
                        <!-- SC% -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 55px;"><strong>SC%</strong></label>
                            <input type="number" class="form-control readonly-field" id="calc_sc_percent" readonly step="0.01" style="width: 85px; height: 28px;" value="0.000">
                        </div>
                    </div>
                    
                    <!-- Column 4 (Inc, S.Rate, Less) -->
                    <div class="d-flex flex-column gap-2" style="min-width: 150px;">
                        <!-- Inc. -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 55px;"><strong>Inc.</strong></label>
                            <input type="text" class="form-control text-center readonly-field" id="calc_inc" readonly style="width: 70px; height: 28px;" value="Y">
                        </div>
                        
                        <!-- S.Rate -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 55px;"><strong>S.Rate</strong></label>
                            <input type="number" class="form-control text-end" id="calc_s_rate" step="0.01" style="width: 95px; height: 28px;" value="0.00">
                        </div>
                        
                        <!-- Less -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 55px;"><strong>Less</strong></label>
                            <input type="number" class="form-control text-end readonly-field" id="calc_less" readonly step="0.01" style="width: 95px; height: 28px;" value="0.00">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Section -->
        <div class="bg-white border rounded p-2 mb-2">
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px; font-size: 11px; align-items: center;">
                <!-- Row 1 -->
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold; white-space: nowrap;">N.T AMT</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="nt_amt" readonly step="0.01" style="width: 100%; height: 28px; background: #fff3cd;" value="0.00">
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold;">SC</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="sc_amt" readonly step="0.01" style="width: 100%; height: 28px;" value="0.00">
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold;">SCM.</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="scm_amt" readonly step="0.01" style="width: 100%; height: 28px;" value="0.00">
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold;">DIS.</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="dis_amt" readonly step="0.01" style="width: 100%; height: 28px;" value="0.00">
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold;">LESS</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="less_amt" readonly step="0.01" style="width: 100%; height: 28px;" value="0.00">
                </div>
                
                <div style="visibility: hidden;">
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <label class="mb-0" style="font-weight: bold;">Tax</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 100%; height: 28px;" value="0.00">
                    </div>
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold; white-space: nowrap;">NET AMT.</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="net_amt" readonly step="0.01" style="width: 100%; height: 28px; background: #fff3cd; font-weight: bold;" value="0.00">
                </div>
            </div>
            
            <!-- Row 2 -->
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px; font-size: 11px; align-items: center; margin-top: 12px;">
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold;">Tax</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="tax_amt" readonly step="0.01" style="width: 100%; height: 28px;" value="0.00">
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold;">Scm.%</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="scm_percent" readonly step="0.01" style="width: 100%; height: 28px;" value="0.00">
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold;">TCS</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="tcs_amt" readonly step="0.01" style="width: 100%; height: 28px; background: #ffcccc;" value="0.00">
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold; white-space: nowrap;">Dis1 Amt</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="dis1_amt" readonly step="0.01" style="width: 100%; height: 28px; background: #ffcccc;" value="0.00">
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold;">TOF</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="tof_amt" readonly step="0.01" style="width: 100%; height: 28px;" value="0.00">
                </div>
                
                <div style="visibility: hidden;">
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <label class="mb-0" style="font-weight: bold;">Tax</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 100%; height: 28px;" value="0.00">
                    </div>
                </div>
                
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label class="mb-0" style="font-weight: bold; white-space: nowrap;">INV.AMT.</label>
                    <input type="number" class="form-control form-control-sm readonly-field text-end" id="inv_amt" readonly step="0.01" style="width: 100%; height: 28px; background: #fff3cd; font-weight: bold;" value="0.00">
                </div>
            </div>
        </div>
        
        <!-- Detailed Info Section -->
        <div class="bg-white border rounded p-2 mb-2">
            <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 3px;"><strong>Unit</strong></td>
                    <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="unit" readonly value="1" style="height: 22px; width: 50px;"></td>
                    <td style="padding: 3px;"><strong>N.T Amt.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="nt_amt_detail" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                    <td style="padding: 3px;"><strong>Scm.Amt.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="scm_amt_detail" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                    <td style="padding: 3px;"><strong>Tax Amt.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="tax_amt_detail" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                    <td style="padding: 3px;"><strong>Cost</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="cost" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                    <td style="padding: 3px;"><strong>Srl.No.</strong></td>
                    <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="srl_no1" readonly value="1" style="height: 22px; width: 40px;"></td>
                    <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="srl_no2" readonly value="1" style="height: 22px; width: 40px;"></td>
                </tr>
                <tr>
                    <td style="padding: 3px;"><strong>Lctn</strong></td>
                    <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="lctn" readonly value="" style="height: 22px; width: 50px;"></td>
                    <td style="padding: 3px;"><strong>SC Amt.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="sc_amt_detail" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                    <td style="padding: 3px;"><strong>Dis1.Amt.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="dis1_amt_detail" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                    <td style="padding: 3px;"><strong>Net Amt.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="net_amt_detail" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                    <td style="padding: 3px;"><strong>Cost+GST</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="cost_gst" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                    <td style="padding: 3px;"><strong>P.SCM.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-center" id="p_scm1" readonly value="0" style="height: 22px; width: 40px;"></td>
                    <td style="padding: 3px; text-align: center; font-weight: bold;">+</td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-center" id="p_scm2" readonly value="0" style="height: 22px; width: 40px;"></td>
                </tr>
                <tr>
                    <td style="padding: 3px;"><strong>Cl.Qty</strong></td>
                    <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="cl_qty" readonly value="" style="height: 22px; width: 50px;"></td>
                    <td style="padding: 3px;"><strong>Dis. Amt.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="dis_amt_detail" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                    <td style="padding: 3px;"><strong>Less</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="less_detail" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                    <td style="padding: 3px;"><strong>Comp :</strong></td>
                    <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="comp" readonly value="" style="height: 22px; width: 100px;"></td>
                    <td style="padding: 3px;"><strong>Vol.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="vol" readonly value="0" style="height: 22px; width: 50px;"></td>
                    <td style="padding: 3px;"><strong>S.SCM.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-center" id="s_scm1" readonly value="0" style="height: 22px; width: 40px;"></td>
                    <td style="padding: 3px; text-align: center; font-weight: bold;">+</td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-center" id="s_scm2" readonly value="0" style="height: 22px; width: 40px;"></td>
                </tr>
                <tr>
                    <td style="padding: 3px;"><strong>Pack</strong></td>
                    <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="pack_detail" readonly value="" style="height: 22px; width: 50px;"></td>
                    <td style="padding: 3px;"><strong>Hs.Amt.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="hs_amt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                    <td style="padding: 3px;"><strong>Gross Amt.</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="gross_amt" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                    <td style="padding: 3px;"><strong>Scm%</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="scm_percent_detail" readonly value="0.00" style="height: 22px; width: 60px;"></td>
                    <td style="padding: 3px;"><strong>Dis1.%</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="dis1_percent" readonly value="0.00" style="height: 22px; width: 60px;"></td>
                    <td style="padding: 3px;"><strong>%</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="percent1" readonly value="0.00" style="height: 22px; width: 50px;"></td>
                    <td style="padding: 3px;"><strong>%</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="percent2" readonly value="0.00" style="height: 22px; width: 50px;"></td>
                    <td style="padding: 3px;"><strong>%</strong></td>
                    <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="percent3" readonly value="0.00" style="height: 22px; width: 50px;"></td>
                </tr>
            </table>
        </div>
        
        <!-- Action Buttons -->
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" onclick="savePurchase()">
                <i class="bi bi-save"></i> Save
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="resetForm()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
            <button type="button" class="btn btn-info btn-sm">
                <i class="bi bi-plus-circle"></i> Add Row
            </button>
        </div>
    </form>
    </div>
</div>

<!-- MRP Details Modal Backdrop -->
<div id="mrpDetailsBackdrop" class="pending-orders-backdrop"></div>

<!-- MRP Details Modal -->
<div id="mrpDetailsModal" class="pending-orders-modal" style="max-width: 650px;">
    <div class="pending-orders-content">
        <div class="pending-orders-header" style="background: #ff6633; color: white; padding: 10px 15px;">
            <h5 class="pending-orders-title" style="margin: 0; font-size: 16px; font-weight: bold;">MRP - Purchase Rate details</h5>
            <button type="button" class="btn-close-modal" onclick="closeMrpDetailsModal()" title="Close" style="color: white; font-size: 20px;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body" style="padding: 15px; background: white;">
            <div class="mb-3">
                <div class="mb-2" style="font-size: 13px;">
                    <strong>Item Name : <span id="mrp_item_name" style="color: #8b008b;">---</span></strong>
                </div>
                <div class="mb-3" style="font-size: 13px;">
                    <strong>Pack : <span id="mrp_pack">---</span></strong>
                </div>
                
                <hr style="margin: 10px 0;">
                
                <table style="width: 100%; font-size: 13px;">
                    <tr>
                        <td style="padding: 5px 0; width: 50%;">
                            <label style="display: inline-block; width: 80px;"><strong>Case</strong></label>
                            <span style="margin: 0 10px;">:</span>
                            <input type="number" class="form-control d-inline-block" id="mrp_case" style="width: 100px;" value="0">
                        </td>
                        <td style="padding: 5px 0;">
                            <label style="display: inline-block; width: 80px;"><strong>Box</strong></label>
                            <span style="margin: 0 10px;">:</span>
                            <input type="number" class="form-control d-inline-block" id="mrp_box" style="width: 100px;" value="0">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">
                            <label style="display: inline-block; width: 80px;"><strong>MRP</strong></label>
                            <span style="margin: 0 10px;">:</span>
                            <input type="number" class="form-control d-inline-block" id="mrp_value" step="0.01" style="width: 100px;" value="0.00">
                        </td>
                        <td style="padding: 5px 0;">
                            <label style="display: inline-block; width: 80px;"><strong>Pur. Rate</strong></label>
                            <span style="margin: 0 10px;">:</span>
                            <input type="number" class="form-control d-inline-block" id="mrp_pur_rate" step="0.01" style="width: 100px;" value="0.00">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;" colspan="2">
                            <label style="display: inline-block; width: 80px;"><strong>Sale Rate</strong></label>
                            <span style="margin: 0 10px;">:</span>
                            <input type="number" class="form-control d-inline-block" id="mrp_sale_rate" step="0.01" style="width: 100px;" value="0.00">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">
                            <label style="display: inline-block; width: 80px;"><strong>W.S. Rate</strong></label>
                            <span style="margin: 0 10px;">:</span>
                            <input type="number" class="form-control d-inline-block" id="mrp_ws_rate" step="0.01" style="width: 100px;" value="0.00">
                        </td>
                        <td style="padding: 5px 0;">
                            <label style="display: inline-block; width: 80px;"><strong>SPL.Rate</strong></label>
                            <span style="margin: 0 10px;">:</span>
                            <input type="number" class="form-control d-inline-block" id="mrp_spl_rate" step="0.01" style="width: 100px;" value="0.00">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;" colspan="2">
                            <label style="display: inline-block; width: 80px;"><strong>Excise</strong></label>
                            <span style="margin: 0 10px;">:</span>
                            <input type="number" class="form-control d-inline-block" id="mrp_excise" step="0.01" style="width: 100px;" value="0.00">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="pending-orders-footer" style="padding: 10px 15px; text-align: right; background: #f8f9fa;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeMrpDetailsModal()" style="margin-right: 10px;">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary btn-sm" id="saveMrpDetailsBtn">
                <i class="bi bi-check-circle"></i> Save
            </button>
        </div>
    </div>
</div>

<!-- Insert Item Modal Backdrop -->
<div id="insertItemBackdrop" class="pending-orders-backdrop"></div>

<!-- Insert Item Modal -->
<div id="insertItemModal" class="pending-orders-modal">
    <div class="pending-orders-content" style="max-width: 600px;">
        <div class="pending-orders-header">
            <h5 class="pending-orders-title">-- SELECT ITEM TO INSERT --</h5>
            <button type="button" class="btn-close-modal" onclick="closeInsertItemModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body">
            <!-- Search Box -->
            <div class="mb-3">
                <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by Code or Name..." autocomplete="off">
            </div>
            
            <!-- Items List -->
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                        <tr>
                            <th style="width: 80px;">CODE</th>
                            <th>ITEM NAME</th>
                            <th style="width: 100px;">MRP</th>
                            <th style="width: 100px;">S.RATE</th>
                        </tr>
                    </thead>
                    <tbody id="insertItemsBody">
                        <!-- Items will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pending-orders-footer" style="padding: 10px 15px; text-align: right; background: #f8f9fa;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeInsertItemModal()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
        </div>
    </div>
</div>

<!-- Pending Orders Modal Backdrop -->
<div id="pendingOrdersBackdrop" class="pending-orders-backdrop"></div>

<!-- Pending Orders Modal -->
<div id="pendingOrdersModal" class="pending-orders-modal">
    <div class="pending-orders-content">
        <div class="pending-orders-header">
            <h5 class="pending-orders-title">-- PENDING ORDER :--</h5>
            <button type="button" class="btn-close-modal" onclick="closePendingOrdersModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 11px;">
                    <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                        <tr>
                            <th style="width: 60px;">SRNO</th>
                            <th style="width: 80px;">CODE</th>
                            <th>NAME</th>
                            <th style="width: 80px;">QTY</th>
                            <th style="width: 100px;">O. DATE</th>
                            <th style="width: 100px;">ORD.NO</th>
                        </tr>
                    </thead>
                    <tbody id="pendingOrdersBody">
                        <!-- Orders will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pending-orders-footer">
            <button type="button" class="btn btn-secondary" onclick="closePendingOrdersModal()">
                <i class="bi bi-x-circle"></i> Exit ( Esc )
            </button>
            <button type="button" class="btn btn-primary" id="generateInvoiceBtn">
                <i class="bi bi-check-circle"></i> Generate Invoice
            </button>
        </div>
    </div>
</div>

<script>
// Update day name when date changes
function updateDayName() {
    const dateInput = document.getElementById('billDate');
    const dayNameInput = document.getElementById('dayName');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        dayNameInput.value = days[date.getDay()];
    }
}

// Current selected row index
let currentSelectedRow = null;

// S.Rate Enter key navigation to next row
document.addEventListener('DOMContentLoaded', function() {
    // Add Enter key support for Bill No and Trn No fields
    const billNoField = document.getElementById('billNo');
    const trnNoField = document.getElementById('trnNo');
    
    if (billNoField) {
        billNoField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchBillByTrnNo();
            }
        });
    }
    
    if (trnNoField) {
        trnNoField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchBillByTrnNo();
            }
        });
    }
    
    const sRateField = document.getElementById('calc_s_rate');
    if (sRateField) {
        // Save s_rate when user changes it (input/change event)
        sRateField.addEventListener('input', function(e) {
            if (currentActiveRow !== null && currentActiveRow !== undefined) {
                const sRateValue = parseFloat(e.target.value) || 0;
                // Initialize rowGstData if it doesn't exist
                if (!rowGstData[currentActiveRow]) {
                    rowGstData[currentActiveRow] = {};
                }
                // Save s_rate for this row
                rowGstData[currentActiveRow].s_rate = sRateValue;
                console.log(`S.Rate saved for row ${currentActiveRow}:`, sRateValue);
            }
        });
        
        sRateField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                console.log('S.Rate Enter pressed');
                console.log('currentActiveRow:', currentActiveRow);
                console.log('isRowSelected before:', isRowSelected);
                
                // Save s_rate before calculating GST
                if (currentActiveRow !== null && currentActiveRow !== undefined) {
                    const sRateValue = parseFloat(e.target.value) || 0;
                    if (!rowGstData[currentActiveRow]) {
                        rowGstData[currentActiveRow] = {};
                    }
                    rowGstData[currentActiveRow].s_rate = sRateValue;
                }
                
                // Calculate and save GST amounts for current row before moving
                calculateAndSaveGstForRow(currentActiveRow);
                
                // Small delay to ensure calculation is saved
                setTimeout(() => {
                    // Select next row (full row selection with blue highlight)
                    const rows = document.querySelectorAll('#itemsTableBody tr');
                    const nextRowIndex = currentActiveRow + 1;
                    console.log('nextRowIndex:', nextRowIndex, 'Total rows:', rows.length);
                    
                    if (nextRowIndex < rows.length) {
                        // Prevent default behavior completely
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        
                        // Select next row with full row highlight (blue background)
                        selectRow(nextRowIndex);
                        
                        console.log('After moving to next row - currentActiveRow:', currentActiveRow);
                        console.log('isRowSelected:', isRowSelected);
                    } else {
                        console.log('No more rows available');
                    }
                }, 100);
            }
        });
    }
});

// Setup on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update row colors for existing rows on page load
    setTimeout(() => {
        const rows = document.querySelectorAll('#itemsTableBody tr');
        rows.forEach((row, index) => {
            updateRowColor(index);
        });
        checkAllRowsComplete();
    }, 100);
    
    // Auto-load transaction if preloadTrnNo is provided
    @if(isset($preloadTrnNo) && $preloadTrnNo)
        // Set the transaction number in the input field
        const trnNoInput = document.getElementById('trnNo');
        if (trnNoInput) {
            trnNoInput.value = '{{ $preloadTrnNo }}';
            // Auto-fetch the bill data
            setTimeout(() => {
                fetchBillByTrnNo();
            }, 1000);
        }
    @endif
    
    // Prevent form submission on Enter key (except for Save button)
    const form = document.getElementById('purchaseForm');
    if (form) {
        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.type !== 'submit' && e.target.type !== 'button') {
                // Don't prevent Enter in specific cases handled by other listeners
                if (!e.target.classList.contains('item-fqty') && 
                    !e.target.classList.contains('item-dis-percent') &&
                    e.target.id !== 'calc_s_rate') {
                    // Allow default behavior for navigation
                    return true;
                }
            }
        });
    }
    
    // Auto-uppercase for Cash and Transfer fields
    ['cash', 'transfer'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }
    });
    
    // Initialize focus listeners for initial rows
    const initialRows = document.querySelectorAll('#itemsTableBody tr');
    initialRows.forEach((row, rowIndex) => {
        const inputs = row.querySelectorAll('input:not([readonly])');
        inputs.forEach(input => {
            // Add focus listener to populate calculation section
            input.addEventListener('focus', function(e) {
                currentActiveRow = rowIndex;
                isRowSelected = false;
                
                // Get item code from current row
                const itemCode = row.querySelector('input[name*="[code]"]').value;
                
                if (itemCode && itemCode.trim() !== '') {
                    // Fetch and populate item details in calculation section
                    fetchItemDetailsForCalculation(itemCode.trim(), rowIndex);
                } else {
                    // Clear calculation section if no item code
                    clearCalculationSection();
                }
            });
        });
        
        // Add amount calculation listeners for initial rows
        addAmountCalculation(row, rowIndex);
    });
    
    // Row selection for calculation section
    addRowSelectionListeners();
    
    // Arrow key navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            const rows = document.querySelectorAll('#itemsTableBody tr');
            if (currentSelectedRow !== null) {
                if (e.key === 'ArrowUp' && currentSelectedRow > 0) {
                    selectRow(currentSelectedRow - 1);
                } else if (e.key === 'ArrowDown' && currentSelectedRow < rows.length - 1) {
                    selectRow(currentSelectedRow + 1);
                }
            }
        }
    });
});

// Add row selection listeners
function addRowSelectionListeners() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    rows.forEach((row, index) => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on pending orders modal trigger
            if (!e.target.closest('input[name*="[code]"]') && !e.target.closest('input[name*="[name]"]')) {
                selectRow(index);
            }
        });
    });
}

// Select row and populate calculation section
function selectRow(index) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    
    // Remove previous selection
    rows.forEach(r => r.classList.remove('row-selected'));
    
    // Add selection to current row
    rows[index].classList.add('row-selected');
    currentSelectedRow = index;
    isRowSelected = true;
    
    // Get item code from row
    const itemCode = rows[index].querySelector('input[name*="[code]"]').value;
    
    if (itemCode && itemCode.trim() !== '') {
        // Fetch item details and populate calculation section with saved GST amounts
        fetchItemDetailsForCalculation(itemCode.trim(), index);
    } else {
        clearCalculationSection();
    }
}

// Fetch item details from database
function fetchItemDetails(itemCode) {
    const url = `{{ url('/admin/items/get-by-code') }}/${itemCode}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {
                populateCalculationSection(data.item);
            } else {
                clearCalculationSection();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            clearCalculationSection();
        });
}

// Fetch item details for calculation section (when focusing on any cell in row)
function fetchItemDetailsForCalculation(itemCode, rowIndex) {
    // In MODIFICATION mode: Preserve saved pur_rate, but use latest rates for s_rate, ws_rate, spl_rate, mrp
    const savedPurRate = rowGstData[rowIndex]?.saved_pur_rate;
    const savedSRate = rowGstData[rowIndex]?.s_rate;
    const savedWsRate = rowGstData[rowIndex]?.ws_rate;
    const savedSplRate = rowGstData[rowIndex]?.spl_rate;
    const savedMrp = rowGstData[rowIndex]?.mrp;
    
    const url = `{{ url('/admin/items/get-by-code') }}/${itemCode}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {
                // MODIFICATION MODE LOGIC:
                // If we're in modification mode (currentTransactionId exists) and have saved_pur_rate:
                // - Keep old pur_rate from transaction
                // - Use latest rates from item table for s_rate, ws_rate, spl_rate, mrp
                
                if (currentTransactionId && savedPurRate !== undefined && savedPurRate !== null) {
                    // Modification mode: Override pur_rate with saved value
                    console.log(`Row ${rowIndex}: MODIFICATION MODE - Using saved pur_rate:`, savedPurRate);
                    // Don't override item.pur_rate here, it's already in the row input
                }
                
                // If we have saved rates in rowGstData (from item table fetch), use them
                // Otherwise use rates from current item fetch (which are latest anyway)
                if (savedSRate !== undefined && savedSRate !== null) {
                    data.item.s_rate = savedSRate;
                }
                if (savedWsRate !== undefined && savedWsRate !== null) {
                    data.item.ws_rate = savedWsRate;
                }
                if (savedSplRate !== undefined && savedSplRate !== null) {
                    data.item.spl_rate = savedSplRate;
                }
                if (savedMrp !== undefined && savedMrp !== null) {
                    data.item.mrp = savedMrp;
                }
                
                populateCalculationSectionForRow(data.item, rowIndex);
            } else {
                clearCalculationSection();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            clearCalculationSection();
        });
}

// Populate calculation section with item data
function populateCalculationSection(item) {
    const row = document.querySelectorAll('#itemsTableBody tr')[currentSelectedRow];
    const amount = parseFloat(row.querySelector('input[name*="[amount]"]').value) || 0;
    
    // Populate fields
    document.getElementById('calc_hsn_display').value = item.hsn_code || '---';
    document.getElementById('calc_cgst').value = item.cgst_percent || 0;
    document.getElementById('calc_sgst').value = item.sgst_percent || 0;
    document.getElementById('calc_cess').value = item.cess_percent || 0;
    
    document.getElementById('calc_sc_percent').value = item.fixed_dis_percent || 0;
    document.getElementById('calc_spl_rate').value = item.spl_rate || 0;
    document.getElementById('calc_ws_rate').value = item.ws_rate || 0;
    document.getElementById('calc_tax_percent').value = (parseFloat(item.cgst_percent || 0) + parseFloat(item.sgst_percent || 0)).toFixed(2);
    document.getElementById('calc_excise').value = 0;
    document.getElementById('calc_mrp').value = item.mrp || 0;
    document.getElementById('calc_s_rate').value = item.s_rate || 0;
    
    // Calculate GST amounts
    const cgstPercent = parseFloat(item.cgst_percent) || 0;
    const sgstPercent = parseFloat(item.sgst_percent) || 0;
    const cessPercent = parseFloat(item.cess_percent) || 0;
    
    const cgstAmount = (amount * cgstPercent / 100).toFixed(2);
    const sgstAmount = (amount * sgstPercent / 100).toFixed(2);
    const cessAmount = (amount * cessPercent / 100).toFixed(2);
    
    document.getElementById('calc_cgst_amount').textContent = cgstAmount;
    document.getElementById('calc_sgst_amount').textContent = sgstAmount;
    document.getElementById('calc_cess_amount').textContent = cessAmount;
}

// Store calculated GST amounts for each row
const rowGstData = {};

// Store complete row data (for detailed info section)
const rowDetailedData = {};

// Populate calculation section for specific row (when focusing on any cell)
function populateCalculationSectionForRow(item, rowIndex) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const row = rows[rowIndex];
    
    if (!row) return;
    
    const amount = parseFloat(row.querySelector('input[name*="[amount]"').value) || 0;
    
    // Populate HSN Code and GST details (percentages always show)
    document.getElementById('calc_hsn_display').value = item.hsn_code || '---';
    document.getElementById('calc_cgst').value = item.cgst_percent || 0;
    document.getElementById('calc_sgst').value = item.sgst_percent || 0;
    document.getElementById('calc_cess').value = item.cess_percent || 0;
    
    // Populate rate fields
    document.getElementById('calc_sc_percent').value = parseFloat(item.fixed_dis_percent || 0).toFixed(3);
    document.getElementById('calc_tax_percent').value = (parseFloat(item.cgst_percent || 0) + parseFloat(item.sgst_percent || 0)).toFixed(3);
    document.getElementById('calc_excise').value = '0.00';
    
    // Initialize rowGstData if needed
    if (!rowGstData[rowIndex]) {
        rowGstData[rowIndex] = {};
    }
    
    // Handle S Rate - Priority: rowGstData > item.s_rate
    let sRateValue = parseFloat(item.s_rate || 0);
    if (rowGstData[rowIndex].s_rate !== undefined && rowGstData[rowIndex].s_rate !== null) {
        sRateValue = parseFloat(rowGstData[rowIndex].s_rate) || 0;
    } else {
        rowGstData[rowIndex].s_rate = sRateValue;
    }
    document.getElementById('calc_s_rate').value = sRateValue.toFixed(2);
    
    // Handle WS Rate - Priority: rowGstData > item.ws_rate
    let wsRateValue = parseFloat(item.ws_rate || 0);
    if (rowGstData[rowIndex].ws_rate !== undefined && rowGstData[rowIndex].ws_rate !== null) {
        wsRateValue = parseFloat(rowGstData[rowIndex].ws_rate) || 0;
    } else {
        rowGstData[rowIndex].ws_rate = wsRateValue;
    }
    document.getElementById('calc_ws_rate').value = wsRateValue.toFixed(2);
    
    // Handle SPL Rate - Priority: rowGstData > item.spl_rate
    let splRateValue = parseFloat(item.spl_rate || 0);
    if (rowGstData[rowIndex].spl_rate !== undefined && rowGstData[rowIndex].spl_rate !== null) {
        splRateValue = parseFloat(rowGstData[rowIndex].spl_rate) || 0;
    } else {
        rowGstData[rowIndex].spl_rate = splRateValue;
    }
    document.getElementById('calc_spl_rate').value = splRateValue.toFixed(2);
    
    // Handle MRP - Priority: rowGstData > item.mrp > row value
    let mrpValue = parseFloat(item.mrp || 0);
    if (rowGstData[rowIndex].mrp !== undefined && rowGstData[rowIndex].mrp !== null) {
        mrpValue = parseFloat(rowGstData[rowIndex].mrp) || 0;
    } else {
        rowGstData[rowIndex].mrp = mrpValue;
    }
    document.getElementById('calc_mrp').value = mrpValue.toFixed(2);
    
    // Check if this row has saved GST calculations
    if (rowGstData[rowIndex] && rowGstData[rowIndex].calculated) {
        // Show saved calculated GST amounts
        document.getElementById('calc_cgst_amount').textContent = rowGstData[rowIndex].cgstAmount;
        document.getElementById('calc_sgst_amount').textContent = rowGstData[rowIndex].sgstAmount;
        document.getElementById('calc_cess_amount').textContent = rowGstData[rowIndex].cessAmount;
    } else {
        // Don't calculate yet - show 0.00 (will calculate after S.Rate is filled)
        document.getElementById('calc_cgst_amount').textContent = '0.00';
        document.getElementById('calc_sgst_amount').textContent = '0.00';
        document.getElementById('calc_cess_amount').textContent = '0.00';
    }
    
    // Populate Inc. field (inclusive flag)
    document.getElementById('calc_inc').value = item.inclusive_flag || 'Y';
    
    // Populate Less field (if available)
    document.getElementById('calc_less').value = '0.00';
    
    // Populate Detailed Info Section
    populateDetailedInfoSection(item, rowIndex);
    
    // Update Summary Section
    updateSummarySection();
}

// Calculate and save GST amounts for current row (called after S.Rate is filled)
function calculateAndSaveGstForRow(rowIndex) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const row = rows[rowIndex];
    
    if (!row) return;
    
    const amount = parseFloat(row.querySelector('input[name*="[amount]"').value) || 0;
    const itemCode = row.querySelector('input[name*="[code]"').value;
    
    if (!itemCode || amount === 0) {
        console.log(`Row ${rowIndex}: No item code or amount is 0`);
        return;
    }
    
    console.log(`Calculating GST for row ${rowIndex}, amount: ${amount}, itemCode: ${itemCode}`);
    
    // Fetch item to get GST percentages
    const url = `{{ url('/admin/items/get-by-code') }}/${itemCode}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {
                const cgstPercent = parseFloat(data.item.cgst_percent) || 0;
                const sgstPercent = parseFloat(data.item.sgst_percent) || 0;
                const cessPercent = parseFloat(data.item.cess_percent) || 0;
                
                const cgstAmount = (amount * cgstPercent / 100).toFixed(2);
                const sgstAmount = (amount * sgstPercent / 100).toFixed(2);
                const cessAmount = (amount * cessPercent / 100).toFixed(2);
                const taxAmount = (parseFloat(cgstAmount) + parseFloat(sgstAmount) + parseFloat(cessAmount)).toFixed(2);
                const netAmount = (parseFloat(amount) + parseFloat(taxAmount)).toFixed(2);
                
                // Calculate Cost and Cost+GST
                const qty = parseFloat(row.querySelector('input[name*="[qty]"').value) || 1;
                const cost = qty > 0 ? (amount / qty).toFixed(2) : '0.00';
                const costGst = qty > 0 ? (netAmount / qty).toFixed(2) : '0.00';
                
                // CRITICAL: Preserve existing rates before updating rowGstData
                const existingSRate = rowGstData[rowIndex]?.s_rate;
                const existingWsRate = rowGstData[rowIndex]?.ws_rate;
                const existingSplRate = rowGstData[rowIndex]?.spl_rate;
                const existingMrp = rowGstData[rowIndex]?.mrp;
                
                // Save calculated amounts for this row
                rowGstData[rowIndex] = {
                    calculated: true,
                    cgstAmount: cgstAmount,
                    sgstAmount: sgstAmount,
                    cessAmount: cessAmount,
                    taxAmount: taxAmount,
                    netAmount: netAmount,
                    amount: amount,
                    cgstPercent: cgstPercent,
                    sgstPercent: sgstPercent,
                    cessPercent: cessPercent,
                    cost: cost,
                    costGst: costGst,
                    // Preserve rates from before (user entered or from saved transaction)
                    s_rate: existingSRate !== undefined && existingSRate !== null ? existingSRate : 0,
                    ws_rate: existingWsRate !== undefined && existingWsRate !== null ? existingWsRate : 0,
                    spl_rate: existingSplRate !== undefined && existingSplRate !== null ? existingSplRate : 0,
                    mrp: existingMrp !== undefined && existingMrp !== null ? existingMrp : 0
                };
                
                console.log(`✅ GST calculated and saved for row ${rowIndex}. S.Rate preserved:`, rowGstData[rowIndex].s_rate, rowGstData[rowIndex]);
                
                // Update display immediately if this is the current active row
                if (currentActiveRow === rowIndex) {
                    document.getElementById('calc_cgst_amount').textContent = cgstAmount;
                    document.getElementById('calc_sgst_amount').textContent = sgstAmount;
                    document.getElementById('calc_cess_amount').textContent = cessAmount;
                    console.log(`✅ Display updated for row ${rowIndex}`);
                    
                    // Update detailed info section with calculated values
                    updateDetailedInfoWithCalculatedData(rowIndex);
                }
                
                // Update row color based on calculation status
                updateRowColor(rowIndex);
                
                // Check if all rows are complete and update accordingly
                checkAllRowsComplete();
                
                // Update summary section
                updateSummarySection();
            }
        })
        .catch(error => {
            console.error('Error calculating GST:', error);
        });
}

// Clear calculation section
function clearCalculationSection() {
    document.getElementById('calc_hsn_display').value = '---';
    document.getElementById('calc_cgst').value = '0';
    document.getElementById('calc_sgst').value = '0';
    document.getElementById('calc_cess').value = '0';
    
    document.getElementById('calc_sc_percent').value = '0.000';
    document.getElementById('calc_spl_rate').value = '0.00';
    document.getElementById('calc_ws_rate').value = '0.00';
    document.getElementById('calc_tax_percent').value = '0.000';
    document.getElementById('calc_excise').value = '0.00';
    document.getElementById('calc_mrp').value = '0.00';
    document.getElementById('calc_s_rate').value = '0.00';
    
    document.getElementById('calc_cgst_amount').textContent = '0.00';
    document.getElementById('calc_sgst_amount').textContent = '0.00';
    document.getElementById('calc_cess_amount').textContent = '0.00';
    
    // Clear detailed info section
    clearDetailedInfoSection();
}

// Populate Detailed Info Section with item data
function populateDetailedInfoSection(item, rowIndex) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const row = rows[rowIndex];
    
    if (!row) return;
    
    // Get current row data
    const amount = parseFloat(row.querySelector('input[name*="[amount]"').value) || 0;
    const qty = parseFloat(row.querySelector('input[name*="[qty]"').value) || 1;
    
    // Populate basic fields (always show)
    document.getElementById('unit').value = item.unit || '1';
    document.getElementById('pack_detail').value = item.packing || '1*10';
    document.getElementById('comp').value = item.company?.short_name || item.company?.name || 'N/A';
    document.getElementById('cl_qty').value = qty || '0';
    
    // Check if row has complete calculated data
    if (rowGstData[rowIndex] && rowGstData[rowIndex].calculated) {
        // Show all calculated values
        document.getElementById('nt_amt_detail').value = rowGstData[rowIndex].amount;
        document.getElementById('tax_amt_detail').value = rowGstData[rowIndex].taxAmount;
        document.getElementById('net_amt_detail').value = rowGstData[rowIndex].netAmount;
        document.getElementById('cost').value = rowGstData[rowIndex].cost;
        document.getElementById('cost_gst').value = rowGstData[rowIndex].costGst;
        
        // Show other calculated fields
        document.getElementById('scm_amt_detail').value = '0.00';
        document.getElementById('sc_amt_detail').value = '0.00';
        document.getElementById('dis1_amt_detail').value = '0.00';
        document.getElementById('dis_amt_detail').value = '0.00';
        document.getElementById('less_detail').value = '0.00';
        document.getElementById('hs_amt').value = '0.00';
        document.getElementById('gross_amt').value = rowGstData[rowIndex].amount;
    } else {
        // Show only NT Amount and basic data (row not complete yet)
        document.getElementById('nt_amt_detail').value = amount.toFixed(2);
        document.getElementById('tax_amt_detail').value = '0.00';
        document.getElementById('net_amt_detail').value = '0.00';
        document.getElementById('cost').value = '0.00';
        document.getElementById('cost_gst').value = '0.00';
        document.getElementById('scm_amt_detail').value = '0.00';
        document.getElementById('sc_amt_detail').value = '0.00';
        document.getElementById('dis1_amt_detail').value = '0.00';
        document.getElementById('dis_amt_detail').value = '0.00';
        document.getElementById('less_detail').value = '0.00';
        document.getElementById('hs_amt').value = '0.00';
        document.getElementById('gross_amt').value = amount.toFixed(2);
    }
}

// Update detailed info section with calculated data (after S.Rate is filled)
function updateDetailedInfoWithCalculatedData(rowIndex) {
    if (rowGstData[rowIndex] && rowGstData[rowIndex].calculated) {
        document.getElementById('nt_amt_detail').value = rowGstData[rowIndex].amount;
        document.getElementById('tax_amt_detail').value = rowGstData[rowIndex].taxAmount;
        document.getElementById('net_amt_detail').value = rowGstData[rowIndex].netAmount;
        document.getElementById('cost').value = rowGstData[rowIndex].cost;
        document.getElementById('cost_gst').value = rowGstData[rowIndex].costGst;
        document.getElementById('gross_amt').value = rowGstData[rowIndex].amount;
    }
}

// Clear Detailed Info Section
function clearDetailedInfoSection() {
    document.getElementById('unit').value = '1';
    document.getElementById('nt_amt_detail').value = '0.00';
    document.getElementById('scm_amt_detail').value = '0.00';
    document.getElementById('tax_amt_detail').value = '0.00';
    document.getElementById('cost').value = '0.00';
    document.getElementById('lctn').value = '';
    document.getElementById('sc_amt_detail').value = '0.00';
    document.getElementById('dis1_amt_detail').value = '0.00';
    document.getElementById('net_amt_detail').value = '0.00';
    document.getElementById('cost_gst').value = '0.00';
    document.getElementById('cl_qty').value = '0';
    document.getElementById('dis_amt_detail').value = '0.00';
    document.getElementById('less_detail').value = '0.00';
    document.getElementById('comp').value = '';
    document.getElementById('vol').value = '0';
    document.getElementById('pack_detail').value = '1*10';
    document.getElementById('hs_amt').value = '0.00';
    document.getElementById('gross_amt').value = '0.00';
}

// Update Summary Section (accumulate all rows)
function updateSummarySection() {
    let totalNtAmt = 0;
    let totalTaxAmt = 0;
    let totalNetAmt = 0;
    let totalInvAmt = 0;
    
    // Loop through all rows and sum up calculated values
    const rows = document.querySelectorAll('#itemsTableBody tr');
    rows.forEach((row, index) => {
        if (rowGstData[index] && rowGstData[index].calculated) {
            totalNtAmt += parseFloat(rowGstData[index].amount) || 0;
            totalTaxAmt += parseFloat(rowGstData[index].taxAmount) || 0;
            totalNetAmt += parseFloat(rowGstData[index].netAmount) || 0;
            totalInvAmt += parseFloat(rowGstData[index].netAmount) || 0;
        }
    });
    
    // Update summary fields
    document.getElementById('nt_amt').value = totalNtAmt.toFixed(2);
    document.getElementById('tax_amt').value = totalTaxAmt.toFixed(2);
    document.getElementById('net_amt').value = totalNetAmt.toFixed(2);
    document.getElementById('inv_amt').value = totalInvAmt.toFixed(2);
    
    // Update other summary fields (default to 0 for now)
    document.getElementById('sc_amt').value = '0.00';
    document.getElementById('scm_amt').value = '0.00';
    document.getElementById('dis_amt').value = '0.00';
    document.getElementById('less_amt').value = '0.00';
    document.getElementById('scm_percent').value = '0.00';
    document.getElementById('tcs_amt').value = '0.00';
    document.getElementById('dis1_amt').value = '0.00';
    document.getElementById('tof_amt').value = '0.00';
}

// Load pending orders from supplier
function loadPendingOrders(supplierId) {
    const url = `{{ url('/admin/suppliers') }}/${supplierId}/pending-orders-data`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPendingOrders(data.orders);
                // Show modal
                showPendingOrdersModal();
            } else {
                showAlert('No pending orders found for this supplier', 'info');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading pending orders', 'error');
        });
}

// Helper function to position modal in content area (not over sidebar)
function positionModalInContentArea(modalElement) {
    if (!modalElement) return;
    
    // Calculate sidebar width dynamically
    const sidebar = document.querySelector('.sidebar');
    let sidebarWidth = 260; // Default sidebar width
    if (sidebar) {
        sidebarWidth = sidebar.offsetWidth;
    }
    
    // Adjust modal position to center in content area (not viewport)
    // Content area starts after sidebar, so we need to shift modal right by half sidebar width
    const leftOffset = sidebarWidth / 2;
    modalElement.style.left = `calc(50% + ${leftOffset}px)`;
}

// Show pending orders modal (internal function)
function showPendingOrdersModal() {
    const modal = document.getElementById('pendingOrdersModal');
    const backdrop = document.getElementById('pendingOrdersBackdrop');
    
    if (!modal || !backdrop) return;
    
    // Position modal in content area
    positionModalInContentArea(modal);
    
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
    }, 10);
}

// Open pending orders modal (called from Insert Orders button)
function openPendingOrdersModal() {
    const supplierId = document.getElementById('supplierSelect').value;
    
    if (!supplierId) {
        showAlert('Please select a supplier first!', 'warning');
        return;
    }
    
    // Load pending orders for selected supplier
    loadPendingOrders(supplierId);
}

// Close pending orders modal
function closePendingOrdersModal() {
    const modal = document.getElementById('pendingOrdersModal');
    const backdrop = document.getElementById('pendingOrdersBackdrop');
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
    }, 300);
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'pendingOrdersBackdrop') {
        closePendingOrdersModal();
    }
    if (e.target && e.target.id === 'mrpDetailsBackdrop') {
        closeMrpDetailsModal();
    }
});

// Global variable to track current active row
let currentActiveRow = 0;
let currentItemData = null;
let isRowSelected = false; // Track if entire row is selected vs cell focus

// Global variable to store current transaction ID (for update mode)
let currentTransactionId = null;

// Open MRP Details Modal
function openMrpDetailsModal() {
    const modal = document.getElementById('mrpDetailsModal');
    const backdrop = document.getElementById('mrpDetailsBackdrop');
    
    if (!modal || !backdrop) return;
    
    // Position modal in content area
    positionModalInContentArea(modal);
    
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
        // Focus on MRP input
        document.getElementById('mrp_value').focus();
        document.getElementById('mrp_value').select();
    }, 10);
}

// Close MRP Details Modal
function closeMrpDetailsModal() {
    const modal = document.getElementById('mrpDetailsModal');
    const backdrop = document.getElementById('mrpDetailsBackdrop');
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
    }, 300);
}

// Open empty MRP modal with default values
function openEmptyMrpModal() {
    currentItemData = null;
    
    document.getElementById('mrp_item_name').textContent = '---';
    document.getElementById('mrp_pack').textContent = '---';
    document.getElementById('mrp_case').value = 0;
    document.getElementById('mrp_box').value = 0;
    document.getElementById('mrp_value').value = 0;
    document.getElementById('mrp_pur_rate').value = 0;
    document.getElementById('mrp_sale_rate').value = 0;
    document.getElementById('mrp_ws_rate').value = 0;
    document.getElementById('mrp_spl_rate').value = 0;
    document.getElementById('mrp_excise').value = 0;
    
    console.log('Opening empty MRP modal...');
    openMrpDetailsModal();
}

// Populate MRP modal with item data
function populateMrpModal(itemCode) {
    console.log('populateMrpModal called with itemCode:', itemCode);
    
    const url = `{{ url('/admin/items/get-by-code') }}/${itemCode}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('Item data received:', data);
            if (data.success && data.item) {
                currentItemData = data.item;
                
                document.getElementById('mrp_item_name').textContent = data.item.name || '---';
                document.getElementById('mrp_pack').textContent = data.item.packing || '---';
                document.getElementById('mrp_case').value = data.item.case_qty || 0;
                document.getElementById('mrp_box').value = data.item.box_qty || 0;
                
                // MODIFICATION MODE LOGIC: Use latest rates from item table
                // But if we have saved_pur_rate (modification mode), show it instead
                const savedPurRate = rowGstData[currentActiveRow]?.saved_pur_rate;
                
                document.getElementById('mrp_value').value = data.item.mrp || 0;
                
                // In modification mode, show old pur_rate from transaction, otherwise show current item pur_rate
                if (currentTransactionId && savedPurRate !== undefined && savedPurRate !== null) {
                    document.getElementById('mrp_pur_rate').value = savedPurRate;
                    console.log('MRP Modal: Using saved pur_rate from transaction:', savedPurRate);
                } else {
                    document.getElementById('mrp_pur_rate').value = data.item.pur_rate || 0;
                }
                
                // Always use latest rates for s_rate, ws_rate, spl_rate (from item table)
                document.getElementById('mrp_sale_rate').value = data.item.s_rate || 0;
                document.getElementById('mrp_ws_rate').value = data.item.ws_rate || 0;
                document.getElementById('mrp_spl_rate').value = data.item.spl_rate || 0;
                document.getElementById('mrp_excise').value = 0;
                
                console.log('Opening MRP modal...');
                openMrpDetailsModal();
            } else {
                console.log('Item not found, opening empty modal');
                openEmptyMrpModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            console.log('Error loading item, opening empty modal');
            openEmptyMrpModal();
        });
}

// Save MRP details and focus on Purchase Rate
document.getElementById('saveMrpDetailsBtn').addEventListener('click', function() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const currentRow = rows[currentActiveRow];
    
    // Get values from modal
    const mrp = parseFloat(document.getElementById('mrp_value').value) || 0;
    const purRate = parseFloat(document.getElementById('mrp_pur_rate').value) || 0;
    const saleRate = parseFloat(document.getElementById('mrp_sale_rate').value) || 0;
    const wsRate = parseFloat(document.getElementById('mrp_ws_rate').value) || 0;
    const splRate = parseFloat(document.getElementById('mrp_spl_rate').value) || 0;
    
    // Update current row with MRP and Purchase Rate
    currentRow.querySelector('input[name*="[mrp]"]').value = mrp.toFixed(2);
    currentRow.querySelector('input[name*="[pur_rate]"]').value = purRate.toFixed(2);
    
    // Initialize rowGstData for this row if not exists
    if (!rowGstData[currentActiveRow]) {
        rowGstData[currentActiveRow] = {};
    }
    
    // Save rates to rowGstData so they can be used in calculation section and saved to DB
    rowGstData[currentActiveRow].s_rate = saleRate;
    rowGstData[currentActiveRow].ws_rate = wsRate;
    rowGstData[currentActiveRow].spl_rate = splRate;
    rowGstData[currentActiveRow].mrp = mrp;
    
    console.log('MRP Modal - Rates saved for row', currentActiveRow, {
        s_rate: saleRate,
        ws_rate: wsRate,
        spl_rate: splRate,
        mrp: mrp
    });
    
    // Update calculation section immediately
    document.getElementById('calc_s_rate').value = saleRate.toFixed(2);
    document.getElementById('calc_ws_rate').value = wsRate.toFixed(2);
    document.getElementById('calc_spl_rate').value = splRate.toFixed(2);
    document.getElementById('calc_mrp').value = mrp.toFixed(2);
    
    // Recalculate amount
    const qty = parseFloat(currentRow.querySelector('input[name*="[qty]"]').value) || 0;
    const disPercent = parseFloat(currentRow.querySelector('input[name*="[dis_percent]"]').value) || 0;
    let amount = qty * parseFloat(purRate || 0);
    if (disPercent > 0) {
        amount = amount - (amount * disPercent / 100);
    }
    currentRow.querySelector('input[name*="[amount]"]').value = amount > 0 ? amount.toFixed(2) : '';
    
    closeMrpDetailsModal();
    
    // Focus on Purchase Rate field
    const purRateInput = currentRow.querySelector('input[name*="[pur_rate]"]');
    if (purRateInput) {
        setTimeout(() => {
            purRateInput.focus();
            purRateInput.select();
        }, 100);
    }
});

// Enable specific row for editing
function enableRow(rowIndex) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const row = rows[rowIndex];
    
    if (row) {
        const inputs = row.querySelectorAll('input:not([readonly])');
        inputs.forEach(input => {
            input.removeAttribute('disabled');
        });
    }
}

// Disable all rows except specified
function disableAllRowsExcept(rowIndex) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    rows.forEach((row, index) => {
        if (index !== rowIndex) {
            const inputs = row.querySelectorAll('input:not([readonly])');
            inputs.forEach(input => {
                input.setAttribute('disabled', 'disabled');
            });
        }
    });
}

// Select entire row (highlight without focusing any cell)
function selectRow(rowIndex) {
    console.log('selectRow called for:', rowIndex);
    const rows = document.querySelectorAll('#itemsTableBody tr');
    
    // Remove selection from all rows
    rows.forEach(r => r.classList.remove('row-selected'));
    
    // Select the target row
    if (rows[rowIndex]) {
        rows[rowIndex].classList.add('row-selected');
        currentActiveRow = rowIndex;
        isRowSelected = true;
        
        console.log('Row selected, isRowSelected set to:', isRowSelected);
        
        // Scroll into view without smooth behavior to avoid focus issues
        rows[rowIndex].scrollIntoView({ block: 'nearest', behavior: 'auto' });
        
        // Remove focus from any active element
        if (document.activeElement) {
            document.activeElement.blur();
        }
        
        // Populate Calculation and Detailed Info sections for this row
        const itemCode = rows[rowIndex].querySelector('input[name*="[code]"]').value;
        
        if (itemCode && itemCode.trim() !== '') {
            // Fetch item details and populate both sections
            fetchItemDetailsForCalculation(itemCode.trim(), rowIndex);
        } else {
            // Clear sections if no item code
            clearCalculationSection();
        }
    }
}

// Focus first input of row (removes row selection, focuses cell)
function focusFirstInput(rowIndex) {
    console.log('focusFirstInput called for row:', rowIndex);
    console.trace('Call stack:');
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const row = rows[rowIndex];
    
    // Remove row selection
    rows.forEach(r => r.classList.remove('row-selected'));
    isRowSelected = false;
    
    if (row) {
        const firstInput = row.querySelector('input:not([readonly]):not([disabled])');
        if (firstInput) {
            currentActiveRow = rowIndex;
            firstInput.focus();
            firstInput.select();
        }
    }
}

// Global keyboard listener for row selection mode
document.addEventListener('keydown', function(e) {
    // Only handle if row is selected (not in cell edit mode)
    if (isRowSelected) {
        const rows = document.querySelectorAll('#itemsTableBody tr');
        
        if (e.key === 'Enter') {
            e.preventDefault();
            // Enter key - focus first cell of selected row
            focusFirstInput(currentActiveRow);
        }
        else if (e.key === 'ArrowDown') {
            e.preventDefault();
            // Move to next row
            const nextRowIndex = currentActiveRow + 1;
            if (nextRowIndex < rows.length) {
                selectRow(nextRowIndex);
            }
        }
        else if (e.key === 'ArrowUp') {
            e.preventDefault();
            // Move to previous row
            const prevRowIndex = currentActiveRow - 1;
            if (prevRowIndex >= 0) {
                selectRow(prevRowIndex);
            }
        }
    }
    
    // Close modal on Esc key
    if (e.key === 'Escape') {
        const modal = document.getElementById('pendingOrdersModal');
        if (modal && modal.classList.contains('show')) {
            closePendingOrdersModal();
        }
        const mrpModal = document.getElementById('mrpDetailsModal');
        if (mrpModal && mrpModal.classList.contains('show')) {
            closeMrpDetailsModal();
        }
    }
});

// Display pending orders in modal
function displayPendingOrders(orders) {
    const tbody = document.getElementById('pendingOrdersBody');
    tbody.innerHTML = '';
    
    if (orders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No pending orders found</td></tr>';
        return;
    }
    
    orders.forEach((order, index) => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        row.setAttribute('data-order-no', order.order_no);
        
        // Highlight on click
        row.addEventListener('click', function() {
            tbody.querySelectorAll('tr').forEach(r => r.classList.remove('table-primary'));
            this.classList.add('table-primary');
        });
        
        row.innerHTML = `
            <td class="text-center">${index + 1}</td>
            <td class="text-center">${order.item_code || '---'}</td>
            <td>${order.item_name || '---'}</td>
            <td class="text-center">${order.order_qty || 0}</td>
            <td class="text-center">${order.order_date || '---'}</td>
            <td class="text-center">${order.order_no || '---'}</td>
        `;
        
        tbody.appendChild(row);
    });
}

// Generate Invoice button
document.getElementById('generateInvoiceBtn').addEventListener('click', function() {
    const selectedRow = document.querySelector('#pendingOrdersBody tr.table-primary');
    
    if (!selectedRow) {
        showAlert('Please select an order first!', 'warning');
        return;
    }
    
    const orderNo = selectedRow.getAttribute('data-order-no');
    
    if (confirm(`Generate invoice for Order No: ${orderNo}?`)) {
        // Load order items
        loadOrderItems(orderNo);
    }
});

// Load order items and populate table
function loadOrderItems(orderNo) {
    const supplierId = document.getElementById('supplierSelect').value;
    
    const url = `{{ url('/admin/suppliers') }}/${supplierId}/pending-orders/${orderNo}/items`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateItemsTable(data.items);
                
                // Close modal
                closePendingOrdersModal();
                
                // Focus on first cell
                setTimeout(() => {
                    const firstInput = document.querySelector('#itemsTableBody tr:first-child input');
                    if (firstInput) {
                        firstInput.focus();
                        firstInput.select();
                    }
                }, 300);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading order items', 'error');
        });
}

// Populate items table
function populateItemsTable(items) {
    const tbody = document.getElementById('itemsTableBody');
    tbody.innerHTML = '';
    
    // Ensure minimum 10 rows
    const minRows = 10;
    const totalRows = Math.max(items.length, minRows);
    
    for (let index = 0; index < totalRows; index++) {
        const item = items[index] || {}; // Empty object if no item data
        
        // Calculate amount: pur_rate * qty
        const qty = parseFloat(item.order_qty) || 0;
        const purRate = parseFloat(item.pur_rate) || 0;
        const amount = (qty * purRate).toFixed(2);
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" class="form-control" name="items[${index}][code]" value="${item.item_code || ''}" tabindex="${index * 10 + 1}" autocomplete="off"></td>
            <td><input type="text" class="form-control" name="items[${index}][name]" value="${item.item_name || ''}" tabindex="${index * 10 + 2}" autocomplete="off"></td>
            <td><input type="text" class="form-control" name="items[${index}][batch]" tabindex="${index * 10 + 3}" autocomplete="off"></td>
            <td><input type="text" class="form-control" name="items[${index}][exp]" tabindex="${index * 10 + 4}" autocomplete="off"></td>
            <td><input type="number" class="form-control item-qty" name="items[${index}][qty]" value="${item.order_qty || ''}" tabindex="${index * 10 + 5}" autocomplete="off" data-row="${index}"></td>
            <td><input type="number" class="form-control item-fqty" name="items[${index}][free_qty]" value="${item.free_qty || ''}" tabindex="${index * 10 + 6}" autocomplete="off" data-row="${index}"></td>
            <td><input type="number" class="form-control item-pur-rate" name="items[${index}][pur_rate]" value="${item.pur_rate || ''}" step="0.01" tabindex="${index * 10 + 7}" autocomplete="off" data-row="${index}"></td>
            <td><input type="number" class="form-control item-dis-percent" name="items[${index}][dis_percent]" step="0.01" tabindex="${index * 10 + 8}" autocomplete="off" data-row="${index}"></td>
            <td><input type="number" class="form-control" name="items[${index}][mrp]" value="${item.mrp || ''}" step="0.01" tabindex="${index * 10 + 9}" autocomplete="off"></td>
            <td><input type="number" class="form-control readonly-field item-amount" name="items[${index}][amount]" value="${amount || ''}" readonly tabindex="-1"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-primary" onclick="openInsertItemModal(${index})" title="Insert Item" style="padding: 4px 8px; margin-right: 5px; font-weight: bold;">+</button>
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(${index})" title="Delete Row" style="padding: 4px 8px; font-weight: bold;">×</button>
            </td>
        `;
        tbody.appendChild(row);
        
        // Add Enter key navigation for this row
        addRowNavigationWithMrpModal(row, index);
        
        // Add amount calculation listeners
        addAmountCalculation(row, index);
        
        // Add focus listeners
        const inputs = row.querySelectorAll('input:not([readonly])');
        inputs.forEach(input => {
            input.addEventListener('focus', function(e) {
                currentActiveRow = index;
                isRowSelected = false;
                
                const itemCode = row.querySelector('input[name*="[code]"]').value;
                
                if (itemCode && itemCode.trim() !== '') {
                    fetchItemDetailsForCalculation(itemCode.trim(), index);
                } else {
                    clearCalculationSection();
                }
            });
        });
        
        // Update row color initially
        updateRowColor(index);
    }
    
    // Check all rows after populating
    checkAllRowsComplete();
    
    // Set first row as selected (full row selection mode)
    currentActiveRow = 0;
    selectRow(0);
}

// Add Enter key navigation with MRP modal trigger
function addRowNavigationWithMrpModal(row, rowIndex) {
    const inputs = row.querySelectorAll('input:not([readonly])');
    
    inputs.forEach((input, colIndex) => {
        // Add focus listener to populate calculation section when entering any cell
        input.addEventListener('focus', function(e) {
            currentActiveRow = rowIndex;
            isRowSelected = false;
            
            // Store original discount value when discount field gets focus
            if (input.classList.contains('item-dis-percent')) {
                input.setAttribute('data-original-discount', input.value || '0');
            }
            
            // Get item code from current row
            const itemCode = row.querySelector('input[name*="[code]"]').value;
            
            if (itemCode && itemCode.trim() !== '') {
                // Fetch and populate item details in calculation section
                fetchItemDetailsForCalculation(itemCode.trim(), rowIndex);
            } else {
                // Clear calculation section if no item code
                clearCalculationSection();
            }
        });
        
        // Add keyboard navigation listener
        input.addEventListener('keydown', function(e) {
            // Enter key navigation
            if (e.key === 'Enter') {
                e.preventDefault();
                
                // Check if this is the F.Qty field
                if (input.classList.contains('item-fqty')) {
                    console.log('F.Qty Enter pressed, rowIndex:', rowIndex);
                    // Get item code from current row
                    const itemCode = row.querySelector('input[name*="[code]"]').value;
                    console.log('Item code:', itemCode);
                    
                    // Always open modal, even if no item code
                    currentActiveRow = rowIndex;
                    
                    if (itemCode && itemCode.trim() !== '') {
                        console.log('Calling populateMrpModal with code:', itemCode);
                        populateMrpModal(itemCode.trim());
                    } else {
                        console.log('No item code, opening empty modal');
                        // Open modal with empty/default values
                        openEmptyMrpModal();
                    }
                }
                // Check if this is the Dis% field
                else if (input.classList.contains('item-dis-percent')) {
                    const currentValue = parseFloat(input.value) || 0;
                    const originalValue = parseFloat(input.getAttribute('data-original-discount') || 0);
                    
                    if (currentValue !== originalValue) {
                        console.log('Discount changed, showing modal');
                        showDiscountOptionsModal(rowIndex, currentValue);
                    } else {
                        console.log('Dis% Enter pressed, moving to S.Rate in calculation section');
                        currentActiveRow = rowIndex;
                        calculateAndSaveGstForRow(rowIndex);
                        const sRateField = document.getElementById('calc_s_rate');
                        if (sRateField) { sRateField.focus(); sRateField.select(); }
                    }
                } else {
                    // Move to next input in same row
                    const nextIndex = colIndex + 1;
                    if (nextIndex < inputs.length) {
                        // Check if next input is disabled
                        if (!inputs[nextIndex].disabled) {
                            inputs[nextIndex].focus();
                            inputs[nextIndex].select();
                        }
                    }
                }
            }
            // Arrow Right - Move to next cell
            else if (e.key === 'ArrowRight') {
                e.preventDefault();
                const nextIndex = colIndex + 1;
                if (nextIndex < inputs.length) {
                    if (!inputs[nextIndex].disabled) {
                        inputs[nextIndex].focus();
                        inputs[nextIndex].select();
                    }
                }
            }
            // Arrow Left - Move to previous cell
            else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                const prevIndex = colIndex - 1;
                if (prevIndex >= 0) {
                    if (!inputs[prevIndex].disabled) {
                        inputs[prevIndex].focus();
                        inputs[prevIndex].select();
                    }
                }
            }
            // Arrow Up/Down disabled in cell edit mode - only works in row selection mode
        });
        
        // Add blur event for F.Qty to trigger modal (optional - only on Tab)
        if (input.classList.contains('item-fqty')) {
            input.addEventListener('blur', function(e) {
                // Only trigger on Tab key, not on Enter
                if (e.relatedTarget && e.relatedTarget.tagName === 'INPUT') {
                    setTimeout(() => {
                        const modal = document.getElementById('mrpDetailsModal');
                        const isModalOpen = modal && modal.classList.contains('show');
                        
                        if (!isModalOpen) {
                            const itemCode = row.querySelector('input[name*="[code]"]').value;
                            if (itemCode && itemCode.trim() !== '') {
                                currentActiveRow = rowIndex;
                                console.log('F.Qty blur event, opening modal for:', itemCode);
                                populateMrpModal(itemCode.trim());
                            }
                        }
                    }, 100);
                }
            });
        }
    });
}

// Add amount calculation to row
function addAmountCalculation(row, rowIndex) {
    const qtyInput = row.querySelector('.item-qty');
    const purRateInput = row.querySelector('.item-pur-rate');
    const disPercentInput = row.querySelector('.item-dis-percent');
    const amountInput = row.querySelector('.item-amount');
    
    function calculateAmount() {
        const qty = parseFloat(qtyInput.value) || 0;
        const purRate = parseFloat(purRateInput.value) || 0;
        const disPercent = parseFloat(disPercentInput.value) || 0;
        
        // Calculate: (pur_rate * qty) - discount
        let amount = qty * purRate;
        
        // Apply discount if any
        if (disPercent > 0) {
            const discount = (amount * disPercent) / 100;
            amount = amount - discount;
        }
        
        amountInput.value = amount.toFixed(2);
        
        // Update row color based on completion status
        updateRowColor(rowIndex);
        
        // Update summaries whenever amount changes
        calculateAllSummaries();
        updateDetailedSummary();
        
        // If this row was already calculated, recalculate GST with new amount
        if (rowGstData[rowIndex] && rowGstData[rowIndex].calculated) {
            const cgstPercent = rowGstData[rowIndex].cgstPercent || 0;
            const sgstPercent = rowGstData[rowIndex].sgstPercent || 0;
            const cessPercent = rowGstData[rowIndex].cessPercent || 0;
            
            const cgstAmount = (amount * cgstPercent / 100).toFixed(2);
            const sgstAmount = (amount * sgstPercent / 100).toFixed(2);
            const cessAmount = (amount * cessPercent / 100).toFixed(2);
            const taxAmount = (parseFloat(cgstAmount) + parseFloat(sgstAmount) + parseFloat(cessAmount)).toFixed(2);
            const netAmount = (parseFloat(amount) + parseFloat(taxAmount)).toFixed(2);
            
            // Recalculate Cost and Cost+GST
            const cost = qty > 0 ? (amount / qty).toFixed(2) : '0.00';
            const costGst = qty > 0 ? (netAmount / qty).toFixed(2) : '0.00';
            
            // Update saved data
            rowGstData[rowIndex].cgstAmount = cgstAmount;
            rowGstData[rowIndex].sgstAmount = sgstAmount;
            rowGstData[rowIndex].cessAmount = cessAmount;
            rowGstData[rowIndex].taxAmount = taxAmount;
            rowGstData[rowIndex].netAmount = netAmount;
            rowGstData[rowIndex].amount = amount;
            rowGstData[rowIndex].cost = cost;
            rowGstData[rowIndex].costGst = costGst;
            
            // Update display if this is the current active row
            if (currentActiveRow === rowIndex) {
                document.getElementById('calc_cgst_amount').textContent = cgstAmount;
                document.getElementById('calc_sgst_amount').textContent = sgstAmount;
                document.getElementById('calc_cess_amount').textContent = cessAmount;
                
                // Update detailed info section
                updateDetailedInfoWithCalculatedData(rowIndex);
            }
            
            // Update all summaries
            calculateAllSummaries();
            updateDetailedSummary();
            
            // Update row color
            updateRowColor(rowIndex);
            
            console.log(`GST recalculated for row ${rowIndex} with new amount ${amount}`);
        }
    }
    
    // Add listeners
    if (qtyInput) qtyInput.addEventListener('input', calculateAmount);
    if (purRateInput) purRateInput.addEventListener('input', calculateAmount);
    if (disPercentInput) disPercentInput.addEventListener('input', calculateAmount);
}

// Check if a row is complete (has all required data and GST calculated)
function isRowComplete(rowIndex) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const row = rows[rowIndex];
    
    if (!row) return false;
    
    const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const purRate = parseFloat(row.querySelector('input[name*="[pur_rate]"]')?.value) || 0;
    const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
    
    // Row is complete if:
    // 1. Has item code
    // 2. Has quantity > 0
    // 3. Has purchase rate > 0
    // 4. Has amount > 0
    // 5. GST has been calculated (rowGstData exists and is calculated)
    const hasBasicData = itemCode && qty > 0 && purRate > 0 && amount > 0;
    const hasGstCalculated = rowGstData[rowIndex] && rowGstData[rowIndex].calculated;
    
    return hasBasicData && hasGstCalculated;
}

// Update row color based on completion status
function updateRowColor(rowIndex) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const row = rows[rowIndex];
    
    if (!row) return;
    
    // Remove existing status classes
    row.classList.remove('row-incomplete', 'row-complete');
    
    if (isRowComplete(rowIndex)) {
        row.classList.add('row-complete');
    } else {
        // Check if row has any data (incomplete)
        const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const purRate = parseFloat(row.querySelector('input[name*="[pur_rate]"]')?.value) || 0;
        
        if (itemCode || qty > 0 || purRate > 0) {
            row.classList.add('row-incomplete');
        }
    }
}

// Check if all rows with data are complete, and turn all green if last row is complete
function checkAllRowsComplete() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    let lastRowWithData = -1;
    let allRowsWithDataComplete = true;
    
    // Find last row with data
    for (let i = rows.length - 1; i >= 0; i--) {
        const row = rows[i];
        const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const purRate = parseFloat(row.querySelector('input[name*="[pur_rate]"]')?.value) || 0;
        
        if (itemCode || qty > 0 || purRate > 0) {
            lastRowWithData = i;
            break;
        }
    }
    
    // If no rows with data, return
    if (lastRowWithData === -1) {
        document.getElementById('itemsTableBody').classList.remove('all-rows-complete');
        return;
    }
    
    // Check if last row with data is complete
    const lastRowComplete = isRowComplete(lastRowWithData);
    
    // Check if all rows with data are complete
    for (let i = 0; i <= lastRowWithData; i++) {
        const row = rows[i];
        const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const purRate = parseFloat(row.querySelector('input[name*="[pur_rate]"]')?.value) || 0;
        
        if (itemCode || qty > 0 || purRate > 0) {
            if (!isRowComplete(i)) {
                allRowsWithDataComplete = false;
                break;
            }
        }
    }
    
    // If last row is complete AND all rows with data are complete, make all green
    if (lastRowComplete && allRowsWithDataComplete) {
        document.getElementById('itemsTableBody').classList.add('all-rows-complete');
        // Ensure all rows with data are marked as complete
        for (let i = 0; i <= lastRowWithData; i++) {
            const row = rows[i];
            const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
            const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
            const purRate = parseFloat(row.querySelector('input[name*="[pur_rate]"]')?.value) || 0;
            
            if (itemCode || qty > 0 || purRate > 0) {
                row.classList.add('row-complete');
                row.classList.remove('row-incomplete');
            }
        }
    } else {
        document.getElementById('itemsTableBody').classList.remove('all-rows-complete');
    }
}

// Add Enter key navigation to row
function addRowNavigation(row, rowIndex) {
    const inputs = row.querySelectorAll('input:not([readonly])');
    inputs.forEach((input, colIndex) => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                
                // Move to next input
                if (colIndex < inputs.length - 1) {
                    inputs[colIndex + 1].focus();
                    inputs[colIndex + 1].select();
                } else {
                    // Last column, move to next row
                    const nextRow = row.nextElementSibling;
                    if (nextRow) {
                        const nextInput = nextRow.querySelector('input:not([readonly])');
                        if (nextInput) {
                            nextInput.focus();
                            nextInput.select();
                        }
                    }
                }
            }
        });
    });
}

// Save button click handler
function savePurchase() {
    // 1. Collect Header Data
    const headerData = {
        bill_date: document.getElementById('billDate').value,
        supplier_id: document.getElementById('supplierSelect').value,
        bill_no: document.getElementById('billNo').value,
        // trn_no will be auto-generated by backend
        receive_date: document.getElementById('receiveDate').value,
        cash_flag: document.getElementById('cash').value,
        transfer_flag: document.getElementById('transfer').value,
        remarks: document.getElementById('remarks').value,
        due_date: document.getElementById('dueDate').value,
        
        // Summary amounts
        nt_amount: document.getElementById('nt_amt').value || 0,
        sc_amount: document.getElementById('sc_amt').value || 0,
        scm_amount: document.getElementById('scm_amt').value || 0,
        dis_amount: document.getElementById('dis_amt').value || 0,
        less_amount: document.getElementById('less_amt').value || 0,
        tax_amount: document.getElementById('tax_amt').value || 0,
        net_amount: document.getElementById('net_amt').value || 0,
        scm_percent: document.getElementById('scm_percent').value || 0,
        tcs_amount: document.getElementById('tcs_amt').value || 0,
        dis1_amount: document.getElementById('dis1_amt').value || 0,
        tof_amt: document.getElementById('tof_amt').value || 0,
        inv_amount: document.getElementById('inv_amt').value || 0
    };
    
    // Validate required fields
    if (!headerData.bill_date) {
        showAlert('⚠️ Please select Bill Date', 'warning');
        return;
    }
    
    if (!headerData.supplier_id) {
        showAlert('⚠️ Please select Supplier', 'warning');
        return;
    }
    
    if (!headerData.bill_no || headerData.bill_no.trim() === '') {
        showAlert('⚠️ Bill No. is required!\n\nPlease enter Bill No. before saving.', 'warning');
        document.getElementById('billNo').focus();
        return;
    }
    
    // 2. Collect Items Data (only rows with ACTUAL data)
    const items = [];
    const rows = document.querySelectorAll('#itemsTableBody tr');
    
    rows.forEach((row, index) => {
        const itemCode = row.querySelector(`input[name="items[${index}][code]"]`)?.value?.trim();
        const itemName = row.querySelector(`input[name="items[${index}][name]"]`)?.value?.trim();
        const qty = parseFloat(row.querySelector(`input[name="items[${index}][qty]"]`)?.value) || 0;
        const purRate = parseFloat(row.querySelector(`input[name="items[${index}][pur_rate]"]`)?.value) || 0;
        
        // Only add rows that have meaningful data
        // Must have: (item_code OR item_name) AND (qty > 0 OR pur_rate > 0)
        const hasItemInfo = itemCode || itemName;
        const hasQuantityOrRate = qty > 0 || purRate > 0;
        
        if (hasItemInfo && hasQuantityOrRate) {
            // Get calculated data from rowGstData
            const calculatedData = rowGstData[index] || {};
            
            items.push({
                item_code: itemCode || '',
                item_name: itemName || '',
                batch_no: row.querySelector(`input[name="items[${index}][batch]"]`)?.value?.trim() || '',
                expiry_date: row.querySelector(`input[name="items[${index}][exp]"]`)?.value || null,
                qty: qty,
                free_qty: parseFloat(row.querySelector(`input[name="items[${index}][free_qty]"]`)?.value) || 0,
                pur_rate: purRate,
                dis_percent: parseFloat(row.querySelector(`input[name="items[${index}][dis_percent]"]`)?.value) || 0,
                mrp: parseFloat(row.querySelector(`input[name="items[${index}][mrp]"]`)?.value) || 0,
                amount: parseFloat(row.querySelector(`input[name="items[${index}][amount]"]`)?.value) || 0,
                
                // Rates from rowGstData (user-modified via MRP modal or from item master)
                s_rate: (calculatedData.s_rate !== undefined && calculatedData.s_rate !== null) ? parseFloat(calculatedData.s_rate) : 0,
                ws_rate: (calculatedData.ws_rate !== undefined && calculatedData.ws_rate !== null) ? parseFloat(calculatedData.ws_rate) : 0,
                spl_rate: (calculatedData.spl_rate !== undefined && calculatedData.spl_rate !== null) ? parseFloat(calculatedData.spl_rate) : 0,
                
                // Calculated GST data
                cgst_percent: calculatedData.cgstPercent || 0,
                sgst_percent: calculatedData.sgstPercent || 0,
                cess_percent: calculatedData.cessPercent || 0,
                cgst_amount: calculatedData.cgstAmount || 0,
                sgst_amount: calculatedData.sgstAmount || 0,
                cess_amount: calculatedData.cessAmount || 0,
                tax_amount: calculatedData.taxAmount || 0,
                net_amount: calculatedData.netAmount || 0,
                cost: calculatedData.cost || 0,
                cost_gst: calculatedData.costGst || 0,
                
                row_order: index
            });
        }
    });
    
    console.log('Total rows:', rows.length, 'Valid items:', items.length);
    
    // Validate items
    if (items.length === 0) {
        showAlert('⚠️ Please add at least one item with quantity and rate.\n\nUse "Add Row" button to add items.', 'warning');
        return;
    }
    
    // 3. Prepare final payload
    const payload = {
        header: headerData,
        items: items
    };
    
    console.log('=== SAVING PURCHASE TRANSACTION ===');
    console.log('Current Transaction ID:', currentTransactionId);
    console.log('Header Data:', headerData);
    console.log('Items Count:', items.length);
    console.log('Items Data:', items);
    console.log('Full Payload:', payload);
    console.log('===================================');
    
    // 4. Determine if this is UPDATE or CREATE
    const isUpdate = currentTransactionId !== null && currentTransactionId !== undefined;
    const saveUrl = isUpdate 
        ? `{{ url('/admin/purchase/transactions') }}/${currentTransactionId}`
        : '{{ url('/admin/purchase/transaction/store') }}';
    const method = isUpdate ? 'PUT' : 'POST';
    
    console.log(`Using ${isUpdate ? 'UPDATE' : 'CREATE'} mode:`, saveUrl, method);
    
    // Send to backend
    fetch(saveUrl, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const text = await response.text();
        console.log('Raw response:', text);
        
        try {
            const data = JSON.parse(text);
            if (!response.ok) {
                return Promise.reject(data);
            }
            return data;
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response text:', text);
            return Promise.reject({ message: 'Invalid JSON response: ' + text.substring(0, 200) });
        }
    })
    .then(data => {
        if (data.success) {
            const action = isUpdate ? 'updated' : 'saved';
            showSuccessModalWithReload(`✅ Purchase Transaction ${action} successfully!\n\nTransaction No: ${data.trn_no || headerData.trn_no || 'N/A'}\nBill No: ${data.bill_no || headerData.bill_no}`);
        } else {
            // Check if it's a duplicate bill no error (only for CREATE mode)
            if (!isUpdate && data.error === 'DUPLICATE_BILL_NO' && data.existing_transaction) {
                const existing = data.existing_transaction;
                const confirmMsg = `⚠️ RECORD ALREADY EXISTS!\n\n` +
                    `Bill No: ${existing.bill_no}\n` +
                    `Transaction No: ${existing.trn_no}\n` +
                    `Bill Date: ${existing.bill_date}\n\n` +
                    `${data.suggestion}\n\n` +
                    `Click OK to open Modification page, or Cancel to stay here.`;
                
                if (confirm(confirmMsg)) {
                    // Redirect to modification page with transaction ID
                    window.location.href = `/admin/purchase/transactions/${existing.id}/edit`;
                }
            } else {
                // Show detailed error for other errors
                let errorMsg = `❌ Error ${isUpdate ? 'updating' : 'saving'} purchase transaction:\n\n`;
                errorMsg += 'Message: ' + (data.message || data.error || 'Unknown error') + '\n';
                if (data.file) errorMsg += 'File: ' + data.file + '\n';
                if (data.line) errorMsg += 'Line: ' + data.line + '\n';
                if (data.trace) errorMsg += 'Trace: ' + data.trace + '\n';
                
                console.error('Full error:', data);
                showAlert(errorMsg, 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('❌ An error occurred while saving the purchase transaction.\n\nPlease check console for details.', 'error');
    });
}

// ============ ADD ROW, DELETE ROW, INSERT ITEM FUNCTIONS ============

let insertRowIndex = null; // Store which row to insert item into

// Add new row to table
function addNewRow() {
    const tbody = document.getElementById('itemsTableBody');
    const rowCount = tbody.querySelectorAll('tr').length;
    const newIndex = rowCount;
    
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="text" class="form-control" name="items[${newIndex}][code]" autocomplete="off"></td>
        <td><input type="text" class="form-control" name="items[${newIndex}][name]" autocomplete="off"></td>
        <td><input type="text" class="form-control" name="items[${newIndex}][batch]" autocomplete="off"></td>
        <td><input type="text" class="form-control" name="items[${newIndex}][exp]" autocomplete="off"></td>
        <td><input type="number" class="form-control item-qty" name="items[${newIndex}][qty]" autocomplete="off"></td>
        <td><input type="number" class="form-control item-fqty" name="items[${newIndex}][free_qty]" autocomplete="off"></td>
        <td><input type="number" class="form-control item-pur-rate" name="items[${newIndex}][pur_rate]" step="0.01" autocomplete="off"></td>
        <td><input type="number" class="form-control item-dis-percent" name="items[${newIndex}][dis_percent]" step="0.01" autocomplete="off"></td>
        <td><input type="number" class="form-control" name="items[${newIndex}][mrp]" step="0.01" autocomplete="off"></td>
        <td><input type="number" class="form-control readonly-field item-amount" name="items[${newIndex}][amount]" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-primary" onclick="openInsertItemModal(${newIndex})" title="Insert Item" style="padding: 4px 8px; margin-right: 5px; font-weight: bold;">+</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(${newIndex})" title="Delete Row" style="padding: 4px 8px; font-weight: bold;">×</button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // Add event listeners to new row
    addRowNavigationWithMrpModal(row, newIndex);
    addAmountCalculation(row, newIndex);
    
    // Update row color initially
    updateRowColor(newIndex);
    
    // Add focus listeners
    const inputs = row.querySelectorAll('input:not([readonly])');
    inputs.forEach(input => {
        input.addEventListener('focus', function(e) {
            currentActiveRow = newIndex;
            isRowSelected = false;
            
            const itemCode = row.querySelector('input[name*="[code]"]').value;
            
            if (itemCode && itemCode.trim() !== '') {
                fetchItemDetailsForCalculation(itemCode.trim(), newIndex);
            } else {
                clearCalculationSection();
            }
        });
    });
    
    console.log(`New row ${newIndex} added`);
}

// Delete row from table
function deleteRow(rowIndex) {
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    // Allow deletion if there's more than 1 row, or if the row has actual data
    if (rows.length <= 1) {
        showAlert('Cannot delete! At least one row is required.', 'warning');
        return;
    }
    
    // Check if the row being deleted has any data
    const rowToDelete = rows[rowIndex];
    if (rowToDelete) {
        const hasData = Array.from(rowToDelete.querySelectorAll('input')).some(input => {
            return input.value && input.value.trim() !== '' && input.value !== '0' && input.value !== '0.00';
        });
        
        // If row has data, ask for confirmation
        if (hasData) {
            if (!confirm('This row contains data. Are you sure you want to delete it?')) {
                return;
            }
        }
    }
    
    // Proceed with deletion
    if (rowToDelete) {
        rowToDelete.remove();
        
        // Delete saved GST data for this row
        if (rowGstData[rowIndex]) {
            delete rowGstData[rowIndex];
        }
        
        // Reindex all rows
        reindexRows();
        
        console.log(`Row ${rowIndex} deleted`);
        
        // Recalculate all summaries after deletion
        calculateAllSummaries();
        
        // Update detailed summary section
        updateDetailedSummary();
        
        // Clear calculation section if no rows with data remain
        clearCalculationSectionIfEmpty();
    }
}

// Calculate all summaries (main summary section)
function calculateAllSummaries() {
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    let totalNTAmt = 0;      // N.T AMT - Net Total Amount
    let totalSCAmt = 0;      // SC - Service Charge
    let totalSCMAmt = 0;     // SCM - Scheme Amount
    let totalDisAmt = 0;     // DIS - Discount Amount
    let totalLessAmt = 0;    // LESS - Less Amount
    let totalTaxAmt = 0;     // Tax - Total Tax Amount
    let totalTCSAmt = 0;     // TCS - Tax Collected at Source
    let totalDis1Amt = 0;    // Dis1 Amt - Additional Discount
    let totalTOFAmt = 0;     // TOF - Turn Over Fee
    
    // Calculate totals from all rows
    rows.forEach((row, index) => {
        const qtyInput = row.querySelector('.item-qty');
        const purRateInput = row.querySelector('.item-pur-rate');
        const disPercentInput = row.querySelector('.item-dis-percent');
        const amountInput = row.querySelector('.item-amount');
        
        if (qtyInput && purRateInput && amountInput) {
            const qty = parseFloat(qtyInput.value) || 0;
            const purRate = parseFloat(purRateInput.value) || 0;
            const disPercent = parseFloat(disPercentInput?.value) || 0;
            const amount = parseFloat(amountInput.value) || 0;
            
            // Only calculate if row has meaningful data
            if (qty > 0 && purRate > 0) {
                const grossAmount = qty * purRate;
                const discountAmount = grossAmount * (disPercent / 100);
                const netAmount = grossAmount - discountAmount;
                
                totalNTAmt += netAmount;
                totalDisAmt += discountAmount;
                
                // Get GST data for this row if available
                if (rowGstData[index]) {
                    const gstData = rowGstData[index];
                    const cgstAmt = parseFloat(gstData.cgstAmount) || 0;
                    const sgstAmt = parseFloat(gstData.sgstAmount) || 0;
                    const cessAmt = parseFloat(gstData.cessAmount) || 0;
                    
                    totalTaxAmt += (cgstAmt + sgstAmt + cessAmt);
                }
            }
        }
    });
    
    // Calculate final amounts
    const netAmt = totalNTAmt + totalTaxAmt - totalDisAmt - totalLessAmt + totalSCAmt + totalSCMAmt;
    const invAmt = netAmt + totalTCSAmt + totalTOFAmt - totalDis1Amt;
    
    // Update summary section fields
    document.getElementById('nt_amt').value = totalNTAmt.toFixed(2);
    document.getElementById('sc_amt').value = totalSCAmt.toFixed(2);
    document.getElementById('scm_amt').value = totalSCMAmt.toFixed(2);
    document.getElementById('dis_amt').value = totalDisAmt.toFixed(2);
    document.getElementById('less_amt').value = totalLessAmt.toFixed(2);
    document.getElementById('net_amt').value = netAmt.toFixed(2);
    
    // Row 2 fields
    document.getElementById('tax_amt').value = totalTaxAmt.toFixed(2);
    document.getElementById('scm_percent').value = '0.00'; // Calculate if needed
    document.getElementById('tcs_amt').value = totalTCSAmt.toFixed(2);
    document.getElementById('dis1_amt').value = totalDis1Amt.toFixed(2);
    document.getElementById('tof_amt').value = totalTOFAmt.toFixed(2);
    document.getElementById('inv_amt').value = invAmt.toFixed(2);
    
    console.log('📊 Summary calculated:', {
        ntAmt: totalNTAmt,
        taxAmt: totalTaxAmt,
        disAmt: totalDisAmt,
        netAmt: netAmt,
        invAmt: invAmt
    });
}

// Update detailed summary section
function updateDetailedSummary() {
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    let totalNTAmt = 0;
    let totalSCAmt = 0;
    let totalSCMAmt = 0;
    let totalDisAmt = 0;
    let totalTaxAmt = 0;
    let totalNetAmt = 0;
    let totalCost = 0;
    let totalCostGST = 0;
    
    // Calculate from all rows
    rows.forEach((row, index) => {
        const qtyInput = row.querySelector('.item-qty');
        const purRateInput = row.querySelector('.item-pur-rate');
        const disPercentInput = row.querySelector('.item-dis-percent');
        
        if (qtyInput && purRateInput) {
            const qty = parseFloat(qtyInput.value) || 0;
            const purRate = parseFloat(purRateInput.value) || 0;
            const disPercent = parseFloat(disPercentInput?.value) || 0;
            
            if (qty > 0 && purRate > 0) {
                const grossAmount = qty * purRate;
                const discountAmount = grossAmount * (disPercent / 100);
                const netAmount = grossAmount - discountAmount;
                
                totalNTAmt += netAmount;
                totalDisAmt += discountAmount;
                totalCost += netAmount; // Cost = Net Amount before tax
                
                // Add GST if available
                if (rowGstData[index]) {
                    const gstData = rowGstData[index];
                    const cgstAmt = parseFloat(gstData.cgstAmount) || 0;
                    const sgstAmt = parseFloat(gstData.sgstAmount) || 0;
                    const cessAmt = parseFloat(gstData.cessAmount) || 0;
                    const totalGst = cgstAmt + sgstAmt + cessAmt;
                    
                    totalTaxAmt += totalGst;
                    totalCostGST += (netAmount + totalGst);
                }
            }
        }
    });
    
    totalNetAmt = totalNTAmt + totalTaxAmt;
    
    // Update detailed summary fields
    document.getElementById('nt_amt_detail').value = totalNTAmt.toFixed(2);
    document.getElementById('sc_amt_detail').value = totalSCAmt.toFixed(2);
    document.getElementById('scm_amt_detail').value = totalSCMAmt.toFixed(2);
    document.getElementById('dis_amt_detail').value = totalDisAmt.toFixed(2);
    document.getElementById('tax_amt_detail').value = totalTaxAmt.toFixed(2);
    document.getElementById('net_amt_detail').value = totalNetAmt.toFixed(2);
    document.getElementById('cost').value = totalCost.toFixed(2);
    document.getElementById('cost_gst').value = totalCostGST.toFixed(2);
    
    // Update other fields with default values
    document.getElementById('unit').value = '1';
    document.getElementById('srl_no1').value = '1';
    document.getElementById('srl_no2').value = '1';
    document.getElementById('lctn').value = '';
    document.getElementById('cl_qty').value = '';
    document.getElementById('comp').value = '';
    
    console.log('📋 Detailed summary updated:', {
        ntAmt: totalNTAmt,
        taxAmt: totalTaxAmt,
        netAmt: totalNetAmt,
        cost: totalCost,
        costGST: totalCostGST
    });
}

// Clear calculation section if no rows with data remain
function clearCalculationSectionIfEmpty() {
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    // Check if any row has meaningful data
    let hasData = false;
    rows.forEach(row => {
        const qtyInput = row.querySelector('.item-qty');
        const purRateInput = row.querySelector('.item-pur-rate');
        
        if (qtyInput && purRateInput) {
            const qty = parseFloat(qtyInput.value) || 0;
            const purRate = parseFloat(purRateInput.value) || 0;
            
            if (qty > 0 && purRate > 0) {
                hasData = true;
            }
        }
    });
    
    // If no data, clear calculation section
    if (!hasData) {
        // Clear HSN and GST display
        document.getElementById('calc_hsn_display').value = '---';
        document.getElementById('calc_cgst').value = '0';
        document.getElementById('calc_sgst').value = '0';
        document.getElementById('calc_cess').value = '0';
        document.getElementById('calc_tax_percent').value = '0.000';
        
        // Clear GST amounts
        document.getElementById('calc_cgst_amount').textContent = '0.00';
        document.getElementById('calc_sgst_amount').textContent = '0.00';
        document.getElementById('calc_cess_amount').textContent = '0.00';
        
        // Clear other calculation fields
        document.getElementById('calc_spl_rate').value = '0.00';
        document.getElementById('calc_ws_rate').value = '0.00';
        document.getElementById('calc_excise').value = '0.00';
        document.getElementById('calc_mrp').value = '0.00';
        document.getElementById('calc_sc_percent').value = '0.000';
        document.getElementById('calc_inc').value = 'Y';
        document.getElementById('calc_s_rate').value = '0.00';
        document.getElementById('calc_less').value = '0.00';
        
        console.log('🧹 Calculation section cleared - no data remaining');
    }
}

// Reindex rows after deletion
function reindexRows() {
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach((row, newIndex) => {
        // Update all input names
        row.querySelectorAll('input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, `[${newIndex}]`);
                input.setAttribute('name', newName);
            }
        });
        
        // Update button onclick attributes
        const insertBtn = row.querySelector('button[onclick*="openInsertItemModal"]');
        const deleteBtn = row.querySelector('button[onclick*="deleteRow"]');
        
        if (insertBtn) insertBtn.setAttribute('onclick', `openInsertItemModal(${newIndex})`);
        if (deleteBtn) deleteBtn.setAttribute('onclick', `deleteRow(${newIndex})`);
    });
}

// Open Insert Item Modal
function openInsertItemModal(rowIndex) {
    console.log('Opening insert modal for row:', rowIndex);
    insertRowIndex = rowIndex;
    
    const modal = document.getElementById('insertItemModal');
    const backdrop = document.getElementById('insertItemBackdrop');
    
    if (!modal) {
        console.error('Modal element not found!');
        return;
    }
    
    if (!backdrop) {
        console.error('Backdrop element not found!');
        return;
    }
    
    // Position modal in content area
    positionModalInContentArea(modal);
    
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
    }, 10);
    
    // Load all items
    loadAllItems();
    
    // Focus on search input
    setTimeout(() => {
        const searchInput = document.getElementById('itemSearchInput');
        if (searchInput) {
            searchInput.focus();
        }
    }, 300);
}

// Close Insert Item Modal
function closeInsertItemModal() {
    const modal = document.getElementById('insertItemModal');
    const backdrop = document.getElementById('insertItemBackdrop');
    
    if (!modal || !backdrop) return;
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
    }, 300);
    
    insertRowIndex = null;
}

// Load all items for insert modal
function loadAllItems() {
    console.log('🔄 Fetching items from backend...');
    
    // Try to fetch from backend
    const url = '{{ url('/admin/items/all') }}';
    fetch(url)
        .then(response => {
            console.log('📡 Response status:', response.status);
            if (!response.ok) {
                throw new Error(`Backend route failed with status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Data received:', data);
            if (data.success && data.items && data.items.length > 0) {
                console.log(`✅ Loading ${data.items.length} items from database`);
                displayItemsInModal(data.items);
            } else {
                console.error('❌ No items found in response');
                loadDummyItems(); // Fallback to dummy data
            }
        })
        .catch(error => {
            console.error('❌ Error loading items:', error);
            console.log('⚠️ Loading dummy items as fallback...');
            loadDummyItems(); // Fallback to dummy data
        });
}

// Fallback: Load dummy items (temporary solution)
function loadDummyItems() {
    const dummyItems = [
        { code: '1', name: 'amarsingh', mrp: 200.00, s_rate: 180.00 },
        { code: '16', name: 'paracetamol', mrp: 25.00, s_rate: 22.00 },
        { code: '19', name: 'cipla1', mrp: 15.00, s_rate: 13.50 },
        { code: '20', name: 'cipla2', mrp: 25.00, s_rate: 22.50 },
        { code: '21', name: 'cipla3', mrp: 35.00, s_rate: 31.50 },
        { code: '22', name: 'para', mrp: 20.00, s_rate: 18.00 }
    ];
    displayItemsInModal(dummyItems);
}

// Display items in modal
function displayItemsInModal(items) {
    const tbody = document.getElementById('insertItemsBody');
    tbody.innerHTML = '';
    
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No items found</td></tr>';
        return;
    }
    
    items.forEach(item => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        
        row.innerHTML = `
            <td>${item.code || '---'}</td>
            <td>${item.name || '---'}</td>
            <td class="text-end">${parseFloat(item.mrp || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(item.s_rate || 0).toFixed(2)}</td>
        `;
        
        // Click to select item
        row.addEventListener('click', function() {
            selectItemForInsertion(item);
        });
        
        // Highlight on hover
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#e9ecef';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
        
        tbody.appendChild(row);
    });
}

// Select item and insert into row
function selectItemForInsertion(item) {
    if (insertRowIndex === null) return;
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const row = rows[insertRowIndex];
    
    if (!row) return;
    
    // Populate row with item data
    row.querySelector('input[name*="[code]"]').value = item.code || '';
    row.querySelector('input[name*="[name]"]').value = item.name || '';
    row.querySelector('input[name*="[mrp]"]').value = item.mrp || '';
    
    // Close modal
    closeInsertItemModal();
    
    // Focus on Batch field
    setTimeout(() => {
        const batchInput = row.querySelector('input[name*="[batch]"]');
        if (batchInput) {
            batchInput.focus();
            batchInput.select();
        }
    }, 100);
    
    console.log(`Item ${item.code} inserted into row ${insertRowIndex}`);
}

// Search items in modal
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('itemSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#insertItemsBody tr');
            
            rows.forEach(row => {
                const code = row.cells[0]?.textContent.toLowerCase() || '';
                const name = row.cells[1]?.textContent.toLowerCase() || '';
                
                if (code.includes(searchTerm) || name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

// ============================================
// MODIFICATION BLADE SPECIFIC FUNCTIONS
// ============================================

// Fetch Bill by Transaction Number or Bill Number
function fetchBillByTrnNo() {
    const trnNo = document.getElementById('trnNo').value.trim();
    const billNo = document.getElementById('billNo').value.trim();
    
    // Check if either Trn No or Bill No is entered
    if (!trnNo && !billNo) {
        // If neither entered, show invoice list modal
        openInvoiceListModal();
        return;
    }
    
    // Priority: Trn No first, then Bill No
    const identifier = trnNo || billNo;
    const searchType = trnNo ? 'Trn No' : 'Bill No';
    
    // Show loading
    console.log(`Fetching bill with ${searchType}:`, identifier);
    
    // Disable button and show loading
    const fetchBtn = document.querySelector('button[onclick*="fetchBillByTrnNo"]');
    const originalText = fetchBtn?.innerHTML;
    if (fetchBtn) {
        fetchBtn.disabled = true;
        fetchBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Loading...';
    }
    
    // Fetch bill data from backend
    const url = `{{ url('/admin/purchase/fetch-bill') }}/${encodeURIComponent(identifier)}`;
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                populateBillData(data.bill);
                // Show success message
                console.log(`✅ Bill fetched successfully using ${searchType}: ${identifier}`);
            } else {
                showAlert(data.message || 'Bill not found', 'error');
            }
        })
        .catch(error => {
            console.error('Error fetching bill:', error);
            if (error.message.includes('Failed to fetch') || error.message.includes('ERR_CONNECTION_REFUSED')) {
                showAlert('❌ Server connection error!\n\nPlease check:\n1. Laravel server is running (php artisan serve)\n2. Server is accessible at http://127.0.0.1:8000\n3. Check browser console for details', 'error');
            } else {
                showAlert('❌ Error fetching bill: ' + error.message, 'error');
            }
        })
        .finally(() => {
            // Re-enable button
            if (fetchBtn && originalText) {
                fetchBtn.disabled = false;
                fetchBtn.innerHTML = originalText;
            }
        });
}

// Open Invoice List Modal
function openInvoiceListModal() {
    const modal = document.getElementById('invoiceListModal');
    const backdrop = document.getElementById('invoiceListBackdrop');
    
    if (!modal || !backdrop) {
        console.error('Invoice list modal elements not found!');
        return;
    }
    
    // Position modal in content area
    positionModalInContentArea(modal);
    
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
    }, 10);
    
    // Load invoices
    loadInvoiceList();
}

// Close Invoice List Modal
function closeInvoiceListModal() {
    const modal = document.getElementById('invoiceListModal');
    const backdrop = document.getElementById('invoiceListBackdrop');
    
    if (!modal || !backdrop) return;
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
    }, 300);
}

// Load Invoice List
function loadInvoiceList() {
    console.log('Loading invoice list...');
    
    const url = '{{ url('/admin/purchase/invoice-list') }}';
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayInvoiceList(data.invoices);
            } else {
                console.error('Failed to load invoices');
            }
        })
        .catch(error => {
            console.error('Error loading invoices:', error);
        });
}

// Helper function to format date
function formatDate(dateValue) {
    if (!dateValue) return '---';
    
    // If it's already a formatted date string, return as is
    if (typeof dateValue === 'string' && dateValue.length <= 10 && !dateValue.includes('T')) {
        return dateValue;
    }
    
    // Parse ISO date string
    try {
        const date = new Date(dateValue);
        if (isNaN(date.getTime())) return '---';
        
        // Format as DD-MM-YYYY
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    } catch (e) {
        return '---';
    }
}

// Display Invoice List in Modal
function displayInvoiceList(invoices) {
    const tbody = document.getElementById('invoiceListBody');
    tbody.innerHTML = '';
    
    if (invoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No invoices found</td></tr>';
        return;
    }
    
    invoices.forEach(invoice => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        row.classList.add('invoice-row');
        
        // Format dates properly
        const billDate = formatDate(invoice.bill_date);
        const receiveDate = formatDate(invoice.receive_date);
        
        row.innerHTML = `
            <td>${invoice.bill_no || '---'}</td>
            <td class="text-center">${billDate}</td>
            <td class="text-center">${receiveDate}</td>
            <td>${invoice.supplier_name || '---'}</td>
            <td class="text-center">${invoice.created_by || 'MASTER'}</td>
            <td class="text-center">${invoice.modified_by || 'MASTER'}</td>
            <td class="text-end">₹${parseFloat(invoice.net_amount || 0).toFixed(2)}</td>
            <td class="text-center"><strong>${invoice.trn_no}</strong></td>
        `;
        
        // Click to select invoice
        row.addEventListener('click', function() {
            selectInvoice(invoice.trn_no);
        });
        
        // Highlight on hover
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#d4edff';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
        
        tbody.appendChild(row);
    });
}

// Select Invoice from Modal
function selectInvoice(trnNo) {
    document.getElementById('trnNo').value = trnNo;
    closeInvoiceListModal();
    fetchBillByTrnNo();
}

// Reset form to clear modification mode
function resetForm() {
    currentTransactionId = null;
    rowGstData = {};
    rowDetailedData = {};
    window.location.reload();
}

// Helper function to format date for HTML date input (YYYY-MM-DD)
function formatDateForInput(dateValue) {
    if (!dateValue) return '';
    
    // If already in YYYY-MM-DD format, return as is
    if (typeof dateValue === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
        return dateValue;
    }
    
    // If it's a date string, try to parse it
    const date = new Date(dateValue);
    if (isNaN(date.getTime())) return '';
    
    // Format as YYYY-MM-DD
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Fetch supplier name by supplier_id
async function fetchSupplierName(supplierId) {
    if (!supplierId) return null;
    
    try {
        // Try to get from existing dropdown options first
        const supplierSelect = document.getElementById('supplierSelect');
        if (supplierSelect) {
            const option = Array.from(supplierSelect.options).find(opt => opt.value === String(supplierId));
            if (option && option.textContent.trim() !== '') {
                return option.textContent;
            }
        }
        
        // If not found, try to fetch from backend API
        try {
            const url = `{{ url('/admin/purchase/supplier') }}/${supplierId}/name`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.name) {
                    console.log('✅ Supplier name fetched from API:', data.name);
                    return data.name;
                }
            }
        } catch (err) {
            console.error('Error fetching supplier name from API:', err);
        }
        
        console.warn('⚠️ Could not fetch supplier name from any endpoint');
        return null;
    } catch (error) {
        console.error('❌ Error fetching supplier name:', error);
        return null;
    }
}

// Populate Bill Data into Form
function populateBillData(bill) {
    console.log('Populating bill data:', bill);
    console.log('Bill date:', bill.bill_date, 'Type:', typeof bill.bill_date);
    console.log('Receive date:', bill.receive_date, 'Type:', typeof bill.receive_date);
    console.log('Due date:', bill.due_date, 'Type:', typeof bill.due_date);
    console.log('Supplier ID:', bill.supplier_id, 'Type:', typeof bill.supplier_id);
    console.log('Cash:', bill.cash, 'Transfer:', bill.transfer);
    console.log('Transaction ID:', bill.transaction_id);
    
    // Store transaction ID for update mode
    currentTransactionId = bill.transaction_id || null;
    console.log('Current Transaction ID set to:', currentTransactionId);
    
    // Header Section - Format dates properly
    const billDateInput = document.getElementById('billDate');
    const receiveDateInput = document.getElementById('receiveDate');
    const dueDateInput = document.getElementById('dueDate');
    const supplierSelect = document.getElementById('supplierSelect');
    const billNoInput = document.getElementById('billNo');
    const trnNoInput = document.getElementById('trnNo');
    const cashInput = document.getElementById('cash');
    const transferInput = document.getElementById('transfer');
    const remarksInput = document.getElementById('remarks');
    
    // Set Bill Date
    if (billDateInput) {
        const formattedBillDate = formatDateForInput(bill.bill_date);
        billDateInput.value = formattedBillDate;
        console.log('Set billDate to:', formattedBillDate);
        updateDayName();
    }
    
    // Set Supplier - Ensure proper selection
    if (supplierSelect) {
        const supplierId = String(bill.supplier_id || ''); // Convert to string to match option values
        console.log('Setting supplier_id to:', supplierId, 'Type:', typeof supplierId);
        
        // Check if option exists in dropdown
        const optionExists = Array.from(supplierSelect.options).some(option => option.value === supplierId);
        
        if (optionExists) {
            supplierSelect.value = supplierId;
            console.log('✅ Supplier selected:', supplierSelect.options[supplierSelect.selectedIndex]?.text);
        } else {
            // If supplier not in dropdown, use supplier_name from bill or fetch from backend
            console.log('⚠️ Supplier ID not found in dropdown');
            
            // First try to use supplier_name from bill data
            let supplierName = bill.supplier_name || '';
            
            if (supplierName) {
                // Add option with supplier name from bill
                const option = document.createElement('option');
                option.value = supplierId;
                option.textContent = supplierName;
                supplierSelect.appendChild(option);
                supplierSelect.value = supplierId;
                console.log('✅ Supplier option added using bill data:', supplierName);
            } else if (supplierId) {
                // If no supplier_name in bill, fetch from backend
                console.log('Fetching supplier name from backend...');
                fetchSupplierName(supplierId).then(name => {
                    if (name) {
                        // Check if option was already added
                        const existingOption = Array.from(supplierSelect.options).find(opt => opt.value === supplierId);
                        if (!existingOption) {
                            const option = document.createElement('option');
                            option.value = supplierId;
                            option.textContent = name;
                            supplierSelect.appendChild(option);
                        }
                        supplierSelect.value = supplierId;
                        console.log('✅ Supplier option added from backend:', name);
                    } else {
                        console.error('❌ Could not fetch supplier name for ID:', supplierId);
                        // Add option with just ID as fallback
                        const option = document.createElement('option');
                        option.value = supplierId;
                        option.textContent = `Supplier ${supplierId}`;
                        supplierSelect.appendChild(option);
                        supplierSelect.value = supplierId;
                    }
                });
            }
        }
        
        // Trigger change event to ensure any listeners are notified
        supplierSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    // Set Bill No and Trn No
    if (billNoInput) {
        billNoInput.value = bill.bill_no || '';
        console.log('Set bill_no to:', bill.bill_no);
    }
    if (trnNoInput) {
        trnNoInput.value = bill.trn_no || '';
        console.log('Set trn_no to:', bill.trn_no);
    }
    
    // Set Receive Date
    if (receiveDateInput) {
        const formattedReceiveDate = formatDateForInput(bill.receive_date);
        receiveDateInput.value = formattedReceiveDate;
        console.log('Set receiveDate to:', formattedReceiveDate);
    }
    
    // Set Cash and Transfer (uppercase)
    if (cashInput) {
        const cashValue = (bill.cash || 'N').toString().toUpperCase();
        cashInput.value = cashValue;
        console.log('Set cash to:', cashValue);
    }
    if (transferInput) {
        const transferValue = (bill.transfer || 'N').toString().toUpperCase();
        transferInput.value = transferValue;
        console.log('Set transfer to:', transferValue);
    }
    
    // Set Remarks
    if (remarksInput) {
        remarksInput.value = bill.remarks || '';
        console.log('Set remarks to:', bill.remarks);
    }
    
    // Set Due Date
    if (dueDateInput) {
        const formattedDueDate = formatDateForInput(bill.due_date);
        dueDateInput.value = formattedDueDate;
        console.log('Set dueDate to:', formattedDueDate);
    }
    
    // Clear existing items
    document.getElementById('itemsTableBody').innerHTML = '';
    
    // Populate Items
    if (bill.items && bill.items.length > 0) {
        bill.items.forEach((item, index) => {
            addNewRow();
            const rows = document.querySelectorAll('#itemsTableBody tr');
            const row = rows[rows.length - 1];
            const rowIndex = rows.length - 1;
            
            // Fix field names to match actual form field names
            row.querySelector('input[name*="[code]"]').value = item.item_code || '';
            row.querySelector('input[name*="[name]"]').value = item.item_name || '';
            row.querySelector('input[name*="[batch]"]').value = item.batch_number || '';
            row.querySelector('input[name*="[exp]"]').value = item.expiry_date || ''; // Fixed: was [expiry]
            row.querySelector('input[name*="[qty]"]').value = item.quantity || '';
            row.querySelector('input[name*="[free_qty]"]').value = item.free_quantity || '';
            row.querySelector('input[name*="[pur_rate]"]').value = item.p_rate || ''; // Fixed: was [rate]
            row.querySelector('input[name*="[dis_percent]"]').value = item.discount_percent || ''; // Fixed: was [discount]
            row.querySelector('input[name*="[mrp]"]').value = item.mrp || '';
            row.querySelector('input[name*="[amount]"]').value = item.amount || '';
            
            // Initialize rowGstData for this row
            if (!rowGstData[rowIndex]) {
                rowGstData[rowIndex] = {};
            }
            
            // Store ONLY pur_rate from saved transaction - all other rates will come from item table
            rowGstData[rowIndex].saved_pur_rate = parseFloat(item.p_rate) || 0;
            console.log(`Row ${rowIndex}: Saved pur_rate from transaction:`, rowGstData[rowIndex].saved_pur_rate);
            
            // Fetch latest rates from item table (S.Rate, WS.Rate, SPL.Rate, MRP)
            const itemCode = item.item_code;
            if (itemCode && itemCode.trim() !== '') {
                console.log(`Row ${rowIndex}: Fetching latest rates from item table for code:`, itemCode);
                
                // Fetch item details to get latest rates
                const itemUrl = `{{ url('/admin/items/get-by-code') }}/${itemCode.trim()}`;
                fetch(itemUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.item) {
                            // Update rowGstData with LATEST rates from item table
                            rowGstData[rowIndex].s_rate = parseFloat(data.item.s_rate || 0);
                            rowGstData[rowIndex].ws_rate = parseFloat(data.item.ws_rate || 0);
                            rowGstData[rowIndex].spl_rate = parseFloat(data.item.spl_rate || 0);
                            rowGstData[rowIndex].mrp = parseFloat(data.item.mrp || 0);
                            
                            // Update MRP field in row with latest value from item table
                            row.querySelector('input[name*="[mrp]"]').value = data.item.mrp || 0;
                            
                            console.log(`Row ${rowIndex}: Latest rates from item table:`, {
                                s_rate: rowGstData[rowIndex].s_rate,
                                ws_rate: rowGstData[rowIndex].ws_rate,
                                spl_rate: rowGstData[rowIndex].spl_rate,
                                mrp: rowGstData[rowIndex].mrp,
                                pur_rate: rowGstData[rowIndex].saved_pur_rate
                            });
                        }
                    })
                    .catch(error => {
                        console.error(`Row ${rowIndex}: Error fetching item rates:`, error);
                    });
            }
            
            // Trigger amount calculation for this row
            setTimeout(() => {
                // Recalculate amount if needed
                const qtyInput = row.querySelector('input[name*="[qty]"]');
                const purRateInput = row.querySelector('input[name*="[pur_rate]"]');
                const disPercentInput = row.querySelector('input[name*="[dis_percent]"]');
                if (qtyInput && purRateInput) {
                    // Trigger input event to recalculate
                    qtyInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }, 100);
        });
    }
    
    // Populate Summary Section
    document.getElementById('nt_amt').value = parseFloat(bill.nt_amt || 0).toFixed(2);
    document.getElementById('sc_amt').value = parseFloat(bill.sc_amt || 0).toFixed(2);
    document.getElementById('scm_amt').value = parseFloat(bill.scm_amt || 0).toFixed(2);
    document.getElementById('dis_amt').value = parseFloat(bill.dis_amt || 0).toFixed(2);
    document.getElementById('less_amt').value = parseFloat(bill.less_amt || 0).toFixed(2);
    document.getElementById('tax_amt').value = parseFloat(bill.tax_amt || 0).toFixed(2);
    document.getElementById('net_amt').value = parseFloat(bill.net_amt || 0).toFixed(2);
    document.getElementById('scm_percent').value = parseFloat(bill.scm_percent || 0).toFixed(2);
    document.getElementById('tcs_amt').value = parseFloat(bill.tcs_amt || 0).toFixed(2);
    document.getElementById('dis1_amt').value = parseFloat(bill.dis1_amt || 0).toFixed(2);
    document.getElementById('tof_amt').value = parseFloat(bill.tof_amt || 0).toFixed(2);
    document.getElementById('inv_amt').value = parseFloat(bill.inv_amt || 0).toFixed(2);
    
    // Populate Detailed Info Section
    document.getElementById('unit').value = bill.unit || '1';
    document.getElementById('nt_amt_detail').value = parseFloat(bill.nt_amt || 0).toFixed(2);
    document.getElementById('scm_amt_detail').value = parseFloat(bill.scm_amt || 0).toFixed(2);
    document.getElementById('tax_amt_detail').value = parseFloat(bill.tax_amt || 0).toFixed(2);
    document.getElementById('cost').value = parseFloat(bill.cost || 0).toFixed(2);
    document.getElementById('lctn').value = bill.location || '';
    document.getElementById('sc_amt_detail').value = parseFloat(bill.sc_amt || 0).toFixed(2);
    document.getElementById('dis1_amt_detail').value = parseFloat(bill.dis1_amt || 0).toFixed(2);
    document.getElementById('net_amt_detail').value = parseFloat(bill.net_amt || 0).toFixed(2);
    document.getElementById('cost_gst').value = parseFloat(bill.cost_gst || 0).toFixed(2);
    document.getElementById('cl_qty').value = bill.cl_qty || '';
    document.getElementById('dis_amt_detail').value = parseFloat(bill.dis_amt || 0).toFixed(2);
    document.getElementById('less_detail').value = parseFloat(bill.less_amt || 0).toFixed(2);
    document.getElementById('comp').value = bill.company || '';
    document.getElementById('vol').value = bill.volume || '0';
    document.getElementById('pack_detail').value = bill.packing || '';
    document.getElementById('hs_amt').value = parseFloat(bill.hs_amt || 0).toFixed(2);
    document.getElementById('gross_amt').value = parseFloat(bill.gross_amt || 0).toFixed(2);
    document.getElementById('scm_percent_detail').value = parseFloat(bill.scm_percent || 0).toFixed(2);
    document.getElementById('dis1_percent').value = parseFloat(bill.dis1_percent || 0).toFixed(2);
    
    // Recalculate GST for all rows that have items (with delay to ensure DOM is ready)
    setTimeout(() => {
        const rows = document.querySelectorAll('#itemsTableBody tr');
        rows.forEach((row, index) => {
            const itemCode = row.querySelector('input[name*="[code]"]')?.value?.trim();
            const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
            
            if (itemCode && amount > 0) {
                // Fetch item details and calculate GST
                fetchItemDetailsForCalculation(itemCode, index);
                // Calculate GST after a short delay to allow item details to load
                setTimeout(() => {
                    calculateAndSaveGstForRow(index);
                }, 200);
            }
        });
        
        // Update summary section after all calculations
        setTimeout(() => {
            updateSummarySection();
            
            // Update row colors after all calculations
            const rows = document.querySelectorAll('#itemsTableBody tr');
            rows.forEach((row, index) => {
                updateRowColor(index);
            });
            checkAllRowsComplete();
        }, 500);
    }, 300);
    
    // Show appropriate message based on mode
    if (currentTransactionId) {
        showAlert('✅ Bill fetched successfully!\n\nYou are in MODIFICATION mode.\nChanges will UPDATE the existing transaction.', 'success');
    } else {
        showAlert('✅ Bill fetched successfully!', 'success');
    }
}

// Modal System Functions
// Hide all existing backdrops when new modal opens
function hideAllBackdrops() {
    // Hide alert modal
    const alertBackdrop = document.getElementById('alertBackdrop');
    const alertModal = document.getElementById('alertModal');
    if (alertBackdrop && alertBackdrop.classList.contains('show')) {
        alertBackdrop.classList.remove('show');
        setTimeout(() => {
            alertBackdrop.style.display = 'none';
        }, 300);
    }
    if (alertModal && alertModal.classList.contains('show')) {
        alertModal.classList.remove('show');
        setTimeout(() => {
            alertModal.style.display = 'none';
        }, 300);
    }
    
    // Hide invoice list modal
    const invoiceBackdrop = document.getElementById('invoiceListBackdrop');
    const invoiceModal = document.getElementById('invoiceListModal');
    if (invoiceBackdrop && invoiceBackdrop.classList.contains('show')) {
        invoiceBackdrop.classList.remove('show');
        setTimeout(() => {
            invoiceBackdrop.style.display = 'none';
        }, 300);
    }
    if (invoiceModal && invoiceModal.classList.contains('show')) {
        invoiceModal.classList.remove('show');
        setTimeout(() => {
            invoiceModal.style.display = 'none';
        }, 300);
    }
}

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

// ============================================
// DISCOUNT OPTIONS MODAL FUNCTIONS
// ============================================

let currentDiscountRowIndex = null;
let companyDiscounts = {};

function showDiscountOptionsModal(rowIndex, discountValue) {
    currentDiscountRowIndex = rowIndex;
    const row = document.querySelector(`#itemsTableBody tr:nth-child(${rowIndex + 1})`);
    const itemName = row?.querySelector('input[name*="[name]"]')?.value || 'Unknown Item';
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
        currentActiveRow = currentDiscountRowIndex;
        if (typeof calculateAndSaveGstForRow === 'function') calculateAndSaveGstForRow(currentDiscountRowIndex);
        const sRateField = document.getElementById('calc_s_rate');
        if (sRateField) { sRateField.focus(); sRateField.select(); }
        currentDiscountRowIndex = null;
    }
}

function applyDiscountOption(option) {
    const rowIndex = currentDiscountRowIndex;
    const row = document.querySelector(`#itemsTableBody tr:nth-child(${rowIndex + 1})`);
    const discountInput = row?.querySelector('input[name*="[dis_percent]"]');
    const discountValue = parseFloat(discountInput?.value) || 0;
    const itemId = row?.getAttribute('data-item-id');
    const companyId = row?.getAttribute('data-company-id');
    const companyName = row?.getAttribute('data-company-name') || '';
    
    const isRemoval = discountValue === 0;
    disableDiscountModalButtons();
    
    switch(option) {
        case 'temporary':
            row?.setAttribute('data-original-discount', discountValue.toString());
            showToast(`Discount ${isRemoval ? 'removed' : 'set to ' + discountValue + '%'} temporarily`, 'success');
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
                showToast('Company not found', 'warning');
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
    document.querySelectorAll('#itemsTableBody tr').forEach((row, index) => {
        if (row.getAttribute('data-company-id') == companyId) {
            const discountInput = row.querySelector('input[name*="[dis_percent]"]');
            if (discountInput) {
                discountInput.value = discountValue;
                row.setAttribute('data-original-discount', discountValue.toString());
                if (typeof calculateRowAmount === 'function') calculateRowAmount(index);
            }
        }
    });
    if (typeof updateSummarySection === 'function') updateSummarySection();
}

function saveDiscountToCompany(companyId, discountValue, callback) {
    fetch('{{ route("admin.purchase.saveCompanyDiscount") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ company_id: companyId, discount_percent: discountValue })
    })
    .then(response => response.json())
    .then(data => { if (callback) callback(data.success); })
    .catch(error => { console.error('Error:', error); if (callback) callback(false); });
}

function saveDiscountToItem(itemId, discountValue, callback) {
    fetch('{{ route("admin.purchase.saveItemDiscount") }}', {
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

<!-- Invoice List Modal Backdrop -->
<div id="invoiceListBackdrop" class="pending-orders-backdrop"></div>

<!-- Invoice List Modal -->
<div id="invoiceListModal" class="pending-orders-modal" style="max-width: 900px; max-height: 70vh;">
    <div class="pending-orders-content" style="display: flex; flex-direction: column; max-height: 70vh;">
        <div class="pending-orders-header" style="background: #ff6b35; color: white; flex-shrink: 0;">
            <h5 class="pending-orders-title">List of Purchase Invoices</h5>
            <button type="button" class="btn-close-modal" onclick="closeInvoiceListModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pending-orders-body" style="padding: 0; overflow: hidden; flex: 1; display: flex; flex-direction: column;">
            <div class="table-responsive" style="flex: 1; overflow-y: auto; max-height: calc(70vh - 120px);">
                <table class="table table-bordered table-hover mb-0" style="font-size: 12px;">
                    <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                        <tr>
                            <th style="width: 120px;">Pur Inv. No</th>
                            <th style="width: 110px;">Inv. Date</th>
                            <th style="width: 110px;">Entry Date</th>
                            <th>Supplier</th>
                            <th style="width: 80px;">UID</th>
                            <th style="width: 80px;">F UID</th>
                            <th style="width: 100px;">Amount</th>
                            <th style="width: 100px;">Trn.No.</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceListBody">
                        <tr>
                            <td colspan="8" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pending-orders-footer" style="flex-shrink: 0; padding: 10px 15px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoiceListModal()">
                <i class="bi bi-x-circle"></i> Close
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

@endsection
