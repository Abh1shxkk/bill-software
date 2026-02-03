@extends('layouts.admin')

@section('title', 'Breakage/Expiry Modification')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    input:focus {
        box-shadow: none !important;
    }

    .header-sectionSR {
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

    .inner-card-sr {
        background: #e8f4f8;
        border: 1px solid #b8d4e0;
        padding: 8px;
        border-radius: 3px;
    }

    .readonly-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
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

    .item-modal-backdrop, .batch-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        opacity: 0;
        animation: fadeIn 0.3s ease forwards;
    }

    .item-modal-backdrop.show, .batch-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .item-modal, .batch-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 57%;
        transform: translate(-50%, -50%) scale(0.7);
        width: calc(100% - 200px);
        max-width: 800px;
        z-index: 1055;
        opacity: 0;
        animation: zoomIn 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        margin-left: 0;
    }

    .item-modal.show, .batch-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .item-modal-content, .batch-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .item-modal-header {
        padding: 1rem 1.5rem;
        background: #0d6efd;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .batch-modal-header {
        padding: 1rem 1.5rem;
        background: #17a2b8;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .item-modal-title, .batch-modal-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .btn-close-modal {
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
    }

    .item-modal-body, .batch-modal-body {
        padding: 1rem;
        max-height: 350px;
        overflow-y: auto;
    }

    .batch-modal-body {
        padding: 0;
    }

    .item-modal-footer, .batch-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    @keyframes zoomIn {
        from {
            transform: translate(-50%, -50%) scale(0.7);
            opacity: 0;
        }
        to {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }

    @keyframes zoomOut {
        from {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        to {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.8);
        }
    }

    /* Batch Not Exist Modal Styles */
    .batch-not-exist-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1055;
        opacity: 0;
        animation: fadeIn 0.4s ease forwards;
    }

    .batch-not-exist-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .batch-not-exist-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        z-index: 1060;
        width: 90%;
        max-width: 400px;
        opacity: 0;
        animation: zoomIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    .batch-not-exist-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .batch-not-exist-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .batch-not-exist-modal-header {
        padding: 1rem 1.5rem;
        background: #ffc107;
        color: #212529;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e0a800;
    }

    .batch-not-exist-modal-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .batch-not-exist-modal-body {
        padding: 1.5rem;
        background: #fff;
        text-align: center;
    }

    .batch-not-exist-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        text-align: center;
    }

    /* Create Batch Modal Styles */
    .create-batch-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1060;
        opacity: 0;
        animation: fadeIn 0.4s ease forwards;
    }

    .create-batch-modal-backdrop.show {
        display: block;
        opacity: 1;
    }

    .create-batch-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        z-index: 1065;
        width: 90%;
        max-width: 600px;
        opacity: 0;
        animation: zoomIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    .create-batch-modal.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .create-batch-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .create-batch-modal-header {
        padding: 1rem 1.5rem;
        background: #007bff;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #0056b3;
    }

    .create-batch-modal-title {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .create-batch-modal-body {
        padding: 1.5rem;
        background: #fff;
    }

    .create-batch-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        text-align: right;
    }

    /* Alert Animations */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    /* Credit Note Modal Styles */
    .credit-note-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1060;
        opacity: 0;
        animation: fadeIn 0.3s ease forwards;
    }

    .credit-note-modal.show {
        display: block;
        opacity: 1;
    }

    .credit-note-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        width: 400px;
        opacity: 0;
        animation: zoomIn 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    .credit-note-modal.show .credit-note-modal-content {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .credit-note-modal-header {
        padding: 1rem 1.5rem;
        background: #007bff;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #0056b3;
    }

    .credit-note-modal-body {
        padding: 1.5rem;
        text-align: center;
    }

    .credit-note-options {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
    }

    .credit-note-close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
    }

    /* Adjustment Modal Styles */
    .adjustment-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1065;
        opacity: 0;
        animation: fadeIn 0.3s ease forwards;
    }

    .adjustment-modal.show {
        display: block;
        opacity: 1;
    }

    .adjustment-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        width: 90%;
        max-width: 800px;
        max-height: 80vh;
        opacity: 0;
        animation: zoomIn 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    .adjustment-modal.show .adjustment-modal-content {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .adjustment-modal-header {
        padding: 1rem 1.5rem;
        background: #28a745;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #1e7e34;
    }

    .adjustment-modal-body {
        padding: 1rem;
        max-height: 60vh;
        overflow-y: auto;
    }

    .adjustment-close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
    }

</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil-square me-2"></i> Breakage/Expiry Modification</h4>
        <div class="text-muted small">Modify existing breakage/expiry invoices</div>
    </div>
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form id="breakageExpiryTransactionForm" method="POST" autocomplete="off" onkeydown="return event.key !== 'Enter';">
            @csrf

            <!-- Header Section -->
            <div class="header-sectionSR">
                <!-- Row 1 -->
                <div class="header-row">
                    <div class="field-group">
                        <label>Series:</label>
                        <input type="text" class="form-control" name="series" style="width: 60px;" value="BE">
                    </div>

                    <div class="field-group">
                        <label>Date:</label>
                        <input type="date" class="form-control" name="transaction_date" style="width: 140px;" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="field-group">
                        <label>End Date:</label>
                        <input type="date" class="form-control" name="end_date" style="width: 140px;">
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="d-flex gap-3">

                    <!-- Right Side - Inner Card SR -->
                    <div class="inner-card-sr flex-grow-1">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label style="width: 100px;">Name</label>
                                    <select class="form-control" name="customer_id">
                                        <option value="">Select Customer</option>
                                        @foreach($customers ?? [] as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>GST Vno:</label>
                                    <input type="text" class="form-control" name="gst_vno" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>R(epl.) / C(redit) Note:</label>
                                    <input type="text" class="form-control" name="note_type" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>With GST[Y/N]:</label>
                                    <input type="text" class="form-control" name="with_gst" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mt-1">
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label style="width: 100px;">Sales Man</label>
                                    <select class="form-control" name="salesman_id">
                                        <option value="">Select Salesman</option>
                                        @foreach($salesmen ?? [] as $salesman)
                                            <option value="{{ $salesman->id }}">{{ $salesman->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>Inc.</label>
                                    <input type="text" class="form-control" name="inc" value="N" maxlength="1" style="width: 50px;">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="field-group">
                                    <label>Rev.Charge</label>
                                    <input type="text" class="form-control" name="rev_charge" value="Y" maxlength="1" style="width: 50px;">
                                </div>
                            </div>

                            

                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label>To be Adjusted?[Y/N],&lt;X&gt; for Imm. Posting</label>
                                    <input type="text" class="form-control" name="adjusted" value="X" maxlength="1" style="width: 50px;">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width: 80px;">Dis. Rpl:</label>
                                    <input type="text" class="form-control" name="dis_rpl">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 100px;">Brk. :</label>
                                    <input type="text" class="form-control" name="brk">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="field-group">
                                    <label style="width: 80px;">Exp. :</label>
                                    <input type="text" class="form-control" name="exp">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div style="width: 220px;">
                        <div class="field-group mb-2">
                            <label style="width: 80px;">Sr. No.:</label>
                            <input type="text" id="sr_no_input" class="form-control" name="sr_no" placeholder="Enter SR No" style="width: 130px;">
                        </div>
                        
                        <div class="field-group mb-2">
                            <label style="width: 80px;">Or Date:</label>
                            <input type="date" id="invoice_date_filter" class="form-control" style="width: 130px;" value="{{ date('Y-m-d') }}">
                        </div>
                        
                        <div class="text-center mt-2">
                            <button type="button" class="btn btn-sm btn-info" style="width: 100%;" onclick="loadInvoice()">
                                <i class="bi bi-list-check"></i> Insert Orders
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto; max-height: 310px;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                            <tr>
                                <th style="width: 60px;">Code</th>
                                <th style="width: 250px;">Item Name</th>
                                <th style="width: 80px;">Batch</th>
                                <th style="width: 70px;">Exp.</th>
                                <th style="width: 60px;">Br/Ex.</th>
                                <th style="width: 60px;">Qty</th>
                                <th style="width: 80px;">F.Qty.</th>
                                <th style="width: 80px;">MRP</th>
                                <th style="width: 80px;">Scm.%</th>   
                                <th style="width: 80px;">Dis.%</th>
                                <th style="width: 90px;">Amount</th>
                                <th style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Rows will be added dynamically -->
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
                <div class="d-flex align-items-start gap-3 border rounded p-2" style="font-size: 11px; background: #fafafa;">
                    <!--  -->
                    <div class="d-flex flex-column gap-2">
                        <!-- HSN Code -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>HSN Code:</strong></label>
                            <input type="text" id="calc_hsn_code" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="---" readonly>
                        </div>

                        <!-- CGST(%) -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>CGST(%):</strong></label>
                            <input type="text" id="calc_cgst_percent" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="0" readonly>
                        </div>

                        <!-- CGST Amount -->
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>CGST Amt:</strong></label>
                            <input type="text" id="calc_cgst_amount" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="0.00" readonly>
                        </div>


                    </div>

                    <!-- Right Side Fields (2 Columns) -->
                    <div class="d-flex gap-3">
                        <!-- Column 1 -->
                        <div class="d-flex flex-column gap-2">
                            <!-- SC -->
                            <!-- SGST(%) -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 75px;"><strong>SGST(%):</strong></label>
                                <input type="text" id="calc_sgst_percent" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="0" readonly>
                            </div>

                            <!-- SGST Amount -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 75px;"><strong>SGST Amt:</strong></label>
                                <input type="text" id="calc_sgst_amount" class="form-control readonly-field text-center" style="width: 100px; height: 28px;" value="0.00" readonly>
                            </div>
                        </div>

                        <!-- Column 2 -->
                        <div class="d-flex flex-column gap-2">
                            <!--  -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 65px;"><strong>TAX %</strong></label>
                                <input type="number" id="calc_tax_percent" class="form-control readonly-field" readonly style="width: 80px; height: 28px;" value="0">
                            </div> 

                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 65px;"><strong>P.Rate</strong></label>
                                <input type="number" id="calc_prate" class="form-control readonly-field" readonly style="width: 80px; height: 28px;" value="0.00">
                            </div>

                        </div>

                        <!-- Column 3 -->
                        <div class="d-flex flex-column gap-2">
                            <!--  -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>S.Rate</strong></label>
                                <input type="number" id="calc_srate" class="form-control readonly-field" readonly style="width: 70px; height: 28px;" value="0.00">
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>MRP</strong></label>
                                <input type="text" id="calc_mrp" class="form-control text-center readonly-field" readonly style="width: 60px; height: 28px;" value="0.00">
                            </div>

                        </div>

                        <!-- Column 4 -->
                        <div class="d-flex flex-column gap-2">
                            <!-- Excise -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>Pack</strong></label>
                                <input type="text" id="calc_pack" class="form-control text-center readonly-field" readonly style="width: 60px; height: 28px;" value="">
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 50px;"><strong>Disallow</strong></label>
                                <input type="text" class="form-control text-center readonly-field" readonly style="width: 60px; height: 28px;" value="N">
                            </div>

                        </div>
                        <!-- Column 4 -->
                        <div class="d-flex flex-column gap-2">
                            <!-- TSR. -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 80px;"><strong>Payable Amt</strong></label>
                                <input type="text" id="calc_payable_amount" class="form-control text-center readonly-field" readonly style="width: 80px; height: 28px;" value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Summary Section -->
            <div class="bg-white border rounded p-2 mb-2">
                <!-- Row 1: 6 fields -->
                <div class="d-flex align-items-center" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold; white-space: nowrap;">MRP Value</label>
                        <input type="number" id="summary_mrp_value" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #fff3cd;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Gross</label>
                        <input type="number" id="summary_gross" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Dis.</label>
                        <input type="number" id="summary_discount" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.</label>
                        <input type="number" id="summary_scheme" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Tax</label>
                        <input type="number" id="summary_tax" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>

                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Net</label>
                        <input type="number" id="summary_net" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>

            </div>

            <!-- Additional Fields (same as original) -->
            <div class="col-12 mb-4 bg-white border rounded p-2 mb-2">
                <div class="row gx-3" style="font-size: 11px; gap: 10px;">
                    <!-- col 1 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Packing</label>
                                    <input type="number" id="detail_packing" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Unit</label>
                                    <input type="number" id="detail_unit" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Cl. Qty.</label>
                                    <input type="number" id="detail_cl_qty" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col 2 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Gross</label>
                                    <input type="number" id="detail_gross" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Scm. Amt.</label>
                                    <input type="number" id="detail_scm_amt" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Dis.Amt.</label>
                                    <input type="number" id="detail_dis_amt" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col 3 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Sub Tot.</label>
                                    <input type="number" id="detail_subtotal" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Tax Amt.</label>
                                    <input type="number" id="detail_tax_amt" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Net Amt.</label>
                                    <input type="number" id="detail_net_amt" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col-4 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Less Pcnt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- col-5 -->
                    <div class="col-lg-2">
                        <div class="row flex-column">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Inv.Amt.</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">Srino</label>
                                    <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="width: 80px; height: 26px; background: #ffcccc;" value="0.00">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-2">
                                    <label class="mb-0 w-50" style="font-weight: bold;">SCM.</label>
                                    <div class="d-flex align-items-center">
                                        <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="height: 26px; background: #ffcccc;" value="0.00">
                                        <label class="form-label mx-1 mb-0 fs-4 fw-bold">+</label>
                                        <input type="number" class="form-control form-control-sm readonly-field text-end" readonly step="0.01" style="height: 26px; background: #ffcccc;" value="0.00">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="saveTransaction()">
                    <i class="bi bi-save"></i> Save
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="addNewRow()">
                    <i class="bi bi-plus-circle"></i> Add Row
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Modals will be dynamically created by JavaScript -->

<script>
let currentRowIndex = 0;
let itemsData = [];
let currentItemForBatch = null;

// Show alert function
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertTypes = {
        'success': { bg: '#28a745', icon: '✓' },
        'error': { bg: '#dc3545', icon: '✕' },
        'info': { bg: '#17a2b8', icon: 'ℹ' },
        'warning': { bg: '#ffc107', icon: '⚠' }
    };
    
    const alertConfig = alertTypes[type] || alertTypes['info'];
    
    const alertHTML = `
        <div class="custom-alert" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${alertConfig.bg};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10001;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        ">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px; font-weight: bold;">${alertConfig.icon}</span>
                <span>${message}</span>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        const alert = document.querySelector('.custom-alert');
        if (alert) {
            alert.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => alert.remove(), 300);
        }
    }, 3000);
}

// Add new row to items table - opens reusable item selection modal
function addNewRow() {
    // Use reusable item selection modal
    if (typeof openItemModal_chooseItemsModal === 'function') {
        openItemModal_chooseItemsModal();
    } else {
        showAlert('error', 'Item selection modal not initialized. Please reload the page.');
    }
}

// Callback function when item and batch are selected from reusable modal
window.onItemBatchSelectedFromModal = function(item, batch) {
    console.log('Item selected from modal:', item);
    console.log('Batch selected from modal:', batch);
    
    // Create a new row for the item
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = tbody.querySelectorAll('tr').length;
    
    // Format expiry date
    let expiryDisplay = '';
    if (batch.expiry_date) {
        const expiryDate = new Date(batch.expiry_date);
        expiryDisplay = `${String(expiryDate.getMonth() + 1).padStart(2, '0')}/${String(expiryDate.getFullYear()).slice(-2)}`;
    }
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.innerHTML = `
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][code]" value="${item.bar_code || item.id || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][batch]" value="${batch.batch_no || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][expiry]" value="${expiryDisplay}" readonly>
        </td>
        <td>
            <select class="form-control" name="items[${rowIndex}][br_ex]" style="width: 60px;">
                <option value="B">B</option>
                <option value="E">E</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][qty]" value="0" step="1" 
                   onchange="calculateRowTotal(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'free_qty'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][free_qty]" value="0" step="1"
                   onchange="calculateRowTotal(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'mrp'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][mrp]" value="${parseFloat(batch.mrp || 0).toFixed(2)}" step="0.01" 
                   onchange="calculateRowTotal(${rowIndex})" readonly>
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][scheme_percent]" value="0" step="0.01" 
                   onchange="calculateRowTotal(${rowIndex})">
        </td>
        <td>
            <input type="number" class="form-control" name="items[${rowIndex}][dis_percent]" value="0" step="0.01" 
                   onchange="calculateRowTotal(${rowIndex})">
        </td>
        <td>
            <input type="number" class="form-control readonly-field" name="items[${rowIndex}][amount]" value="0.00" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
        <input type="hidden" name="items[${rowIndex}][item_id]" value="${item.id}">
        <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batch.id}">
        <input type="hidden" name="items[${rowIndex}][s_rate]" value="${batch.s_rate || 0}">
        <input type="hidden" name="items[${rowIndex}][p_rate]" value="${batch.p_rate || batch.pur_rate || 0}">
        <input type="hidden" name="items[${rowIndex}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${rowIndex}][cgst_percent]" value="${item.cgst_percent || 0}">
        <input type="hidden" name="items[${rowIndex}][sgst_percent]" value="${item.sgst_percent || 0}">
    `;
    
    tbody.appendChild(row);
    
    // Store batch data
    row.dataset.batchData = JSON.stringify(batch);
    row.dataset.sRate = batch.s_rate || 0;
    row.dataset.pRate = batch.p_rate || batch.pur_rate || 0;
    row.dataset.mrp = batch.mrp || 0;
    
    // Focus on qty input
    setTimeout(() => {
        const qtyInput = row.querySelector('input[name*="[qty]"]');
        if (qtyInput) {
            qtyInput.focus();
            qtyInput.select();
        }
    }, 100);
    
    showAlert('success', 'Item added! Enter quantity.');
    if (typeof calculateRowTotal === 'function') calculateRowTotal(rowIndex);
    if (typeof recalculateTotals === 'function') recalculateTotals();
};

// Main function to load invoice - handles both SR No and Date-based loading
function loadInvoice() {
    const srNo = document.getElementById('sr_no_input').value.trim();
    const invoiceDate = document.getElementById('invoice_date_filter').value;
    
    // Priority 1: If SR No is entered, load that specific invoice
    if (srNo) {
        loadInvoiceBySrNo(srNo);
        return;
    }
    
    // Priority 2: If no SR No but date is selected, show invoice selection modal
    if (invoiceDate) {
        loadInvoicesByDate(invoiceDate);
        return;
    }
    
    // If neither SR No nor date is provided
    showAlert('error', 'Please enter SR No or select a date to load invoices.');
}

// Load Invoice by SR No
function loadInvoiceBySrNo(srNo) {
    showAlert('info', 'Loading invoice...');
    
    fetch(`{{ url('/admin/breakage-expiry') }}/get-by-sr-no/${encodeURIComponent(srNo)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.transaction) {
            // Load the invoice with its items
            selectInvoice(data.transaction.id);
        } else {
            showAlert('error', data.message || 'Invoice not found with SR No: ' + srNo);
        }
    })
    .catch(error => {
        console.error('Error loading invoice by SR No:', error);
        showAlert('error', 'Error loading invoice. Please try again.');
    });
}

// Open Invoice Selection Modal (for Date-based selection)
function openInvoiceSelectionModal() {
    const invoiceDate = document.getElementById('invoice_date_filter').value;
    
    if (!invoiceDate) {
        showAlert('error', 'Please select an invoice date first.');
        return;
    }
    
    // Load invoices for the selected date
    loadInvoicesByDate(invoiceDate);
}

// Load Invoices by Date
function loadInvoicesByDate(date) {
    showAlert('info', 'Loading invoices...');
    
    fetch(`{{ route("admin.breakage-expiry.index") }}?ajax=1&date_from=${date}&date_to=${date}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.transactions) {
            showInvoiceModal(data.transactions);
        } else {
            showAlert('error', 'No invoices found for the selected date.');
        }
    })
    .catch(error => {
        console.error('Error loading invoices:', error);
        showAlert('error', 'Error loading invoices. Please try again.');
    });
}

// Show Invoice Selection Modal
function showInvoiceModal(invoices) {
    let tableRows = '';
    
    if (invoices.length === 0) {
        tableRows = '<tr><td colspan="6" class="text-center text-muted">No invoices found for this date</td></tr>';
    } else {
        invoices.forEach(invoice => {
            const invoiceDate = new Date(invoice.transaction_date).toLocaleDateString('en-GB');
            tableRows += `
                <tr style="cursor: pointer;" onclick='selectInvoice(${invoice.id})'>
                    <td style="padding: 4px;">${invoice.sr_no || ''}</td>
                    <td style="padding: 4px;">${invoiceDate}</td>
                    <td style="padding: 4px;">${invoice.customer_name || ''}</td>
                    <td style="padding: 4px;">${invoice.salesman_name || '-'}</td>
                    <td class="text-end" style="padding: 4px;">₹${parseFloat(invoice.net_amount || 0).toFixed(2)}</td>
                    <td class="text-center" style="padding: 4px;">
                        <button type="button" class="btn btn-sm btn-primary" style="padding: 2px 6px; font-size: 10px;" onclick='event.stopPropagation(); selectInvoice(${invoice.id})'>
                            <i class="bi bi-check-circle"></i> Select
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    const modalHTML = `
        <div class="item-modal-backdrop" id="invoiceModalBackdrop" onclick="closeInvoiceModal()"></div>
        <div class="item-modal" id="invoiceModal">
            <div class="item-modal-content">
                <div class="item-modal-header">
                    <h5 class="item-modal-title"><i class="bi bi-receipt"></i> Select Invoice</h5>
                    <button type="button" class="btn-close-modal" onclick="closeInvoiceModal()">&times;</button>
                </div>
                <div class="item-modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="invoiceSearchInput" placeholder="Search by SR No, Customer, or Salesman..." onkeyup="filterInvoices()">
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 11px; margin-bottom: 0;">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 10; background: #e9ecef;">
                                <tr>
                                    <th style="width: 80px; padding: 4px;">SR No</th>
                                    <th style="width: 80px; padding: 4px;">Date</th>
                                    <th style="width: 150px; padding: 4px;">Customer</th>
                                    <th style="width: 100px; padding: 4px;">Salesman</th>
                                    <th style="width: 80px; padding: 4px;">Amount</th>
                                    <th style="width: 80px; padding: 4px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="invoicesListBody">${tableRows}</tbody>
                        </table>
                    </div>
                </div>
                <div class="item-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoiceModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('invoiceModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('invoiceModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    setTimeout(() => {
        document.getElementById('invoiceModalBackdrop').classList.add('show');
        document.getElementById('invoiceModal').classList.add('show');
    }, 10);
}

// Close Invoice Modal
function closeInvoiceModal() {
    const modal = document.getElementById('invoiceModal');
    const backdrop = document.getElementById('invoiceModalBackdrop');
    
    if (modal) modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    if (backdrop) backdrop.style.animation = 'fadeOut 0.3s ease forwards';
    
    setTimeout(() => {
        if (modal) modal.remove();
        if (backdrop) backdrop.remove();
    }, 300);
}

// Filter Invoices
function filterInvoices() {
    const searchTerm = document.getElementById('invoiceSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#invoicesListBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Select Invoice and Load Its Items
function selectInvoice(invoiceId) {
    showAlert('info', 'Loading invoice details...');
    
    fetch(`{{ url('/admin/breakage-expiry') }}/${invoiceId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.transaction) {
            closeInvoiceModal();
            populateInvoiceData(data.transaction);
            showAlert('success', 'Invoice loaded successfully');
        } else {
            showAlert('error', 'Error loading invoice details');
        }
    })
    .catch(error => {
        console.error('Error loading invoice:', error);
        showAlert('error', 'Error loading invoice details');
    });
}

// Populate Invoice Data into Form
function populateInvoiceData(transaction) {
    console.log('Populating invoice data:', transaction);
    
    // Store transaction ID for update operations
    window.currentTransactionId = transaction.id;
    console.log('✅ Transaction ID stored for editing:', transaction.id);
    
    // Store for debugging
    window.lastLoadedTransaction = transaction;
    
    // Clear existing rows
    document.getElementById('itemsTableBody').innerHTML = '';
    currentRowIndex = 0;
    
    // Populate SR No
    if (transaction.sr_no) {
        document.getElementById('sr_no_input').value = transaction.sr_no;
        console.log('✅ SR No set to:', transaction.sr_no);
    }
    
    // Populate Series
    if (transaction.series) {
        const seriesInput = document.querySelector('input[name="series"]');
        if (seriesInput) {
            seriesInput.value = transaction.series;
            console.log('✅ Series set to:', transaction.series);
        }
    }
    
    // Populate dates
    if (transaction.transaction_date) {
        const dateInput = document.querySelector('input[name="transaction_date"]');
        if (dateInput) {
            // Convert ISO date to YYYY-MM-DD format
            const date = new Date(transaction.transaction_date);
            const formattedDate = date.toISOString().split('T')[0];
            dateInput.value = formattedDate;
            console.log('✅ Transaction Date set to:', formattedDate);
        }
    }
    if (transaction.end_date) {
        const endDateInput = document.querySelector('input[name="end_date"]');
        if (endDateInput) {
            // Convert ISO date to YYYY-MM-DD format
            const date = new Date(transaction.end_date);
            const formattedDate = date.toISOString().split('T')[0];
            endDateInput.value = formattedDate;
            console.log('✅ End Date set to:', formattedDate);
        }
    } else {
        console.log('ℹ️ No End Date in transaction');
    }
    
    // Populate customer dropdown
    if (transaction.customer_id) {
        const customerSelect = document.querySelector('select[name="customer_id"]');
        if (customerSelect) {
            // Convert to string to match option values
            const customerId = String(transaction.customer_id);
            console.log('Setting customer_id to:', customerId);
            console.log('Available customer options:', Array.from(customerSelect.options).map(o => ({value: o.value, text: o.text})));
            
            // Set the value
            customerSelect.value = customerId;
            
            // Trigger change event
            customerSelect.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Verify it was set
            setTimeout(() => {
                if (customerSelect.value != customerId) {
                    console.error('❌ Customer ID not found in dropdown:', customerId);
                    console.log('Dropdown current value:', customerSelect.value);
                    
                    // Try to find by text match as fallback
                    if (transaction.customer_name) {
                        const matchingOption = Array.from(customerSelect.options).find(
                            opt => opt.text.toLowerCase() === transaction.customer_name.toLowerCase()
                        );
                        if (matchingOption) {
                            console.log('Found customer by name, setting to:', matchingOption.value);
                            customerSelect.value = matchingOption.value;
                            customerSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                } else {
                    console.log('✅ Customer set successfully:', customerSelect.options[customerSelect.selectedIndex].text);
                }
            }, 100);
        }
    }
    
    // Populate salesman dropdown
    if (transaction.salesman_id) {
        const salesmanSelect = document.querySelector('select[name="salesman_id"]');
        if (salesmanSelect) {
            // Convert to string to match option values
            const salesmanId = String(transaction.salesman_id);
            console.log('Setting salesman_id to:', salesmanId);
            console.log('Available salesman options:', Array.from(salesmanSelect.options).map(o => ({value: o.value, text: o.text})));
            
            // Set the value
            salesmanSelect.value = salesmanId;
            
            // Trigger change event
            salesmanSelect.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Verify it was set
            setTimeout(() => {
                if (salesmanSelect.value != salesmanId) {
                    console.error('❌ Salesman ID not found in dropdown:', salesmanId);
                    console.log('Dropdown current value:', salesmanSelect.value);
                    
                    // Try to find by text match as fallback
                    if (transaction.salesman_name) {
                        const matchingOption = Array.from(salesmanSelect.options).find(
                            opt => opt.text.toLowerCase() === transaction.salesman_name.toLowerCase()
                        );
                        if (matchingOption) {
                            console.log('Found salesman by name, setting to:', matchingOption.value);
                            salesmanSelect.value = matchingOption.value;
                            salesmanSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                } else {
                    console.log('✅ Salesman set successfully:', salesmanSelect.options[salesmanSelect.selectedIndex].text);
                }
            }, 100);
        }
    }
    
    // Populate other fields
    console.log('📝 Populating other fields...');
    const fieldsToPopulate = {
        'gst_vno': transaction.gst_vno || 'N',
        'note_type': transaction.note_type || 'N',
        'with_gst': transaction.with_gst || 'N',
        'inc': transaction.inc || 'N',
        'rev_charge': transaction.rev_charge || 'Y',
        'adjusted': transaction.adjusted || 'X',
        'dis_rpl': transaction.dis_rpl || '',
        'brk': transaction.brk || '',
        'exp': transaction.exp || ''
    };
    
    Object.keys(fieldsToPopulate).forEach(fieldName => {
        const input = document.querySelector(`input[name="${fieldName}"]`);
        if (input) {
            input.value = fieldsToPopulate[fieldName];
            console.log(`  ✅ ${fieldName} = ${fieldsToPopulate[fieldName]}`);
        } else {
            console.warn(`  ⚠️ Field not found: ${fieldName}`);
        }
    });
    
    // Populate items
    if (transaction.items && transaction.items.length > 0) {
        transaction.items.forEach((item, index) => {
            addInvoiceItemToTable(item, index);
        });
    }
    
    // Recalculate all totals
    updateAllCalculations();
    
    console.log('Invoice data populated successfully');
}

// Add Invoice Item to Table
function addInvoiceItemToTable(item, index) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.innerHTML = `
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.item_code || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.item_name || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" 
                   value="${item.batch_no || ''}"
                   onblur="checkBatchExists(${rowIndex}, ${item.item_id}, this.value)"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); checkBatchExists(${rowIndex}, ${item.item_id}, this.value); return false; }"
                   placeholder="Enter batch no.">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" 
                   value="${item.expiry || ''}"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'br_ex'); return false; }" 
                   placeholder="MM/YY">
        </td>
        <td>
            <select class="form-control form-control-sm" name="items[${rowIndex}][br_ex]"
                    onchange="moveToNextField(${rowIndex}, 'qty')"
                    onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'qty'); return false; }">
                <option value="">Select</option>
                <option value="B" ${item.br_ex === 'B' ? 'selected' : ''}>Breakage</option>
                <option value="E" ${item.br_ex === 'E' ? 'selected' : ''}>Expiry</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" 
                   value="${item.qty || 0}"
                   step="0.01" onchange="calculateRowAmount(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'f_qty'); return false; }" 
                   placeholder="0.00">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][f_qty]" 
                   value="${item.f_qty || 0}"
                   step="0.01" 
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'mrp'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][mrp]" 
                   value="${item.mrp || 0}"
                   step="0.01" 
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'scm_percent'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][scm_percent]" 
                   value="${item.scm_percent || 0}"
                   step="0.01" onchange="calculateRowAmount(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'dis_percent'); return false; }">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][dis_percent]" 
                   value="${item.dis_percent || 0}"
                   step="0.01" onchange="handleDiscountChange(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); handleDiscountAndAddRow(${rowIndex}); return false; }">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" 
                   value="${item.amount || 0}"
                   step="0.01" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // Create item data object from the invoice item
    const itemData = {
        id: item.item_id || item.item_code,
        name: item.item_name,
        packing: item.packing,
        mrp: item.mrp,
        hsn_code: item.hsn_code,
        gst_percent: item.tax_percent || (parseFloat(item.cgst_percent || 0) + parseFloat(item.sgst_percent || 0)),
        total_qty: 0 // Will be fetched if needed
    };
    
    // Store item data for later use
    row.dataset.itemId = item.item_id || item.item_code;
    row.dataset.itemData = JSON.stringify(itemData);
    
    // If batch data exists, store it
    if (item.batch_no) {
        const batchData = {
            batch_no: item.batch_no,
            s_rate: item.s_rate || 0,
            pur_rate: item.p_rate || 0,
            mrp: item.mrp || 0,
            expiry_date: item.expiry || null
        };
        row.dataset.batchData = JSON.stringify(batchData);
    }
    
    // Add click event to row to update calculation section
    row.addEventListener('click', function() {
        selectRowForCalculation(rowIndex);
    });
    
    // Calculate row amount
    calculateRowAmount(rowIndex);
}

// Pagination state for items
let itemsCurrentPage = 1;
let itemsPerPage = 50;
let itemsHasMore = true;
let itemsLoading = false;

// Open Item Selection Modal (kept for backward compatibility if needed)
function openItemSelectionModal() {
    // Check if customer is selected
    const customerId = document.querySelector('select[name="customer_id"]').value;
    
    if (!customerId) {
        showAlert('error', 'Please select a customer first.');
        return;
    }
    
    // Reset pagination state and load first page
    itemsCurrentPage = 1;
    itemsHasMore = true;
    itemsLoading = false;
    itemsData = [];
    
    loadPaginatedItems(itemsCurrentPage, true);
}

// Load Items from Database with Pagination
function loadPaginatedItems(page, isInitial = false) {
    if (itemsLoading || (!itemsHasMore && !isInitial)) return;
    
    itemsLoading = true;
    
    // Show loading indicator if not initial
    if (!isInitial) {
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'block';
    }
    
    const url = `{{ route("admin.items.all") }}?page=${page}&per_page=${itemsPerPage}`;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        itemsLoading = false;
        
        // Hide loading indicator
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'none';
        
        const items = data.items || [];
        
        // Check if we have pagination info
        if (data.pagination) {
            itemsHasMore = data.pagination.has_more || (page < data.pagination.last_page);
        } else {
            // Legacy: no pagination, all items returned
            itemsHasMore = false;
        }
        
        // Store loaded items
        itemsData = itemsData.concat(items);
        
        if (isInitial) {
            // Show modal with initial items
            showPaginatedItemModal(items);
        } else {
            // Append items to existing table
            appendItemsToTable(items);
        }
        
        // Update records info
        updateItemsRecordsInfo();
        
        itemsCurrentPage++;
    })
    .catch(error => {
        itemsLoading = false;
        console.error('Error loading items:', error);
        const loadingIndicator = document.getElementById('itemsLoadingIndicator');
        if (loadingIndicator) loadingIndicator.style.display = 'none';
        
        if (isInitial) {
            showAlert('error', 'Error loading items. Please try again.');
        }
    });
}

// Load Items (legacy - kept for backward compatibility)
function loadItems() {
    // Reset and load paginated
    itemsCurrentPage = 1;
    itemsHasMore = true;
    itemsLoading = false;
    itemsData = [];
    loadPaginatedItems(itemsCurrentPage, true);
}

// Display Items in Modal (legacy - still used for search filtering)
function displayItems(items) {
    const tbody = document.getElementById('itemsListBody');
    if (!tbody) return;
    
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No items found</td></tr>';
        return;
    }
    
    let html = '';
    items.forEach((item, index) => {
        html += `
            <tr style="padding: 2px;">
                <td style="padding: 4px;">${item.id || ''}</td>
                <td style="padding: 4px;">${item.name || ''}</td>
                <td style="padding: 4px;">${item.packing || ''}</td>
                <td class="text-end" style="padding: 4px;">₹${parseFloat(item.mrp || 0).toFixed(2)}</td>
                <td class="text-end" style="padding: 4px;">${parseFloat(item.total_qty || 0).toFixed(2)}</td>
                <td class="text-center" style="padding: 4px;">
                    <button type="button" class="btn btn-sm btn-primary" style="padding: 2px 6px; font-size: 10px;" onclick='selectItem(${JSON.stringify(item).replace(/'/g, "&apos;")})'>
                        <i class="bi bi-check-circle"></i> Select
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Append items to the modal table (for infinite scroll)
function appendItemsToTable(items) {
    const tbody = document.getElementById('itemsListBody');
    if (!tbody) return;
    
    items.forEach((item, index) => {
        const row = document.createElement('tr');
        row.style.padding = '2px';
        row.innerHTML = `
            <td style="padding: 4px;">${item.id || ''}</td>
            <td style="padding: 4px;">${item.name || ''}</td>
            <td style="padding: 4px;">${item.packing || ''}</td>
            <td class="text-end" style="padding: 4px;">₹${parseFloat(item.mrp || 0).toFixed(2)}</td>
            <td class="text-end" style="padding: 4px;">${parseFloat(item.total_qty || 0).toFixed(2)}</td>
            <td class="text-center" style="padding: 4px;">
                <button type="button" class="btn btn-sm btn-primary" style="padding: 2px 6px; font-size: 10px;" onclick='selectItem(${JSON.stringify(item).replace(/'/g, "&apos;")})'>
                    <i class="bi bi-check-circle"></i> Select
                </button>
            </td>
        `;
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
            // Load more items if available
            if (itemsHasMore && !itemsLoading) {
                loadPaginatedItems(itemsCurrentPage, false);
            }
        }
    });
}

// Show Item Selection Modal with Pagination
function showPaginatedItemModal(items) {
    const totalInfo = itemsHasMore ? `Showing first ${items.length} items (scroll for more)` : `Showing all ${items.length} items`;
    
    const modalHTML = `
        <div class="item-modal-backdrop" id="itemModalBackdrop" onclick="closeItemModal()"></div>
        <div class="item-modal" id="itemModal">
            <div class="item-modal-content">
                <div class="item-modal-header">
                    <h5 class="item-modal-title"><i class="bi bi-box-seam"></i> Select Item</h5>
                    <button type="button" class="btn-close-modal" onclick="closeItemModal()">&times;</button>
                </div>
                <div class="item-modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="itemSearchInput" placeholder="Search by item name or code..." onkeyup="filterItems()">
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" id="itemsScrollContainer">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 11px; margin-bottom: 0;">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 10; background: #e9ecef;">
                                <tr>
                                    <th style="width: 50px; padding: 4px;">Code</th>
                                    <th style="width: 100px; padding: 4px;">Item Name</th>
                                    <th style="width: 40px; padding: 4px;">Packing</th>
                                    <th style="width: 40px; padding: 4px;">MRP</th>
                                    <th style="width: 40px; padding: 4px;">Stock</th>
                                    <th style="width: 80px; padding: 4px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsListBody"></tbody>
                        </table>
                        <!-- Loading indicator -->
                        <div id="itemsLoadingIndicator" style="display: none; text-align: center; padding: 15px; color: #6c757d;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2">Loading more items...</span>
                        </div>
                    </div>
                </div>
                <div class="item-modal-footer">
                    <small class="text-muted me-auto" id="itemsRecordsInfo">${totalInfo}</small>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('itemModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('itemModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    setTimeout(() => {
        document.getElementById('itemModalBackdrop').classList.add('show');
        document.getElementById('itemModal').classList.add('show');
        
        // Setup infinite scroll
        setupItemsInfiniteScroll();
    }, 10);
    
    displayItems(items);
}

// Show Item Selection Modal (legacy - kept for backward compatibility)
function showItemModal() {
    // Use paginated modal with current data
    showPaginatedItemModal(itemsData);
}

// Close Item Modal
function closeItemModal() {
    const modal = document.getElementById('itemModal');
    const backdrop = document.getElementById('itemModalBackdrop');
    
    if (modal) modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    if (backdrop) backdrop.style.animation = 'fadeOut 0.3s ease forwards';
    
    setTimeout(() => {
        if (modal) modal.remove();
        if (backdrop) backdrop.remove();
    }, 300);
}

// Filter Items
function filterItems() {
    const searchTerm = document.getElementById('itemSearchInput').value.toLowerCase();
    const filteredItems = itemsData.filter(item => {
        const name = (item.name || '').toLowerCase();
        const code = (item.id || '').toString().toLowerCase();
        return name.includes(searchTerm) || code.includes(searchTerm);
    });
    displayItems(filteredItems);
}

// Select Item and Add to Table
function selectItem(item) {
    closeItemModal();
    addNewRowWithItem(item);
}

// Add New Row with Item
function addNewRowWithItem(item) {
    const tbody = document.getElementById('itemsTableBody');
    const rowIndex = currentRowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.innerHTML = `
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][code]" value="${item.id || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][name]" value="${item.name || ''}" readonly>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][batch]" 
                   onblur="checkBatchExists(${rowIndex}, ${item.id}, this.value)"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); checkBatchExists(${rowIndex}, ${item.id}, this.value); return false; }"
                   placeholder="Enter batch no.">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][expiry]" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'br_ex'); return false; }" 
                   placeholder="MM/YY">
        </td>
        <td>
            <select class="form-control form-control-sm" name="items[${rowIndex}][br_ex]"
                    onchange="moveToNextField(${rowIndex}, 'qty')"
                    onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'qty'); return false; }">
                <option value="">Select</option>
                <option value="B">Breakage</option>
                <option value="E">Expiry</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][qty]" 
                   step="0.01" onchange="calculateRowAmount(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'f_qty'); return false; }" 
                   placeholder="0.00">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][f_qty]" 
                   step="0.01" 
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'mrp'); return false; }" 
                   value="0.00">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][mrp]" 
                   step="0.01" 
                   onchange="calculateRowAmount(${rowIndex})"
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'scm_percent'); return false; }" 
                   value="">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][scm_percent]" 
                   step="0.01" onchange="calculateRowAmount(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); moveToNextField(${rowIndex}, 'dis_percent'); return false; }" 
                   value="0.00">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="items[${rowIndex}][dis_percent]" 
                   step="0.01" onchange="handleDiscountChange(${rowIndex})" 
                   onkeydown="if(event.key === 'Enter') { event.preventDefault(); handleDiscountAndAddRow(${rowIndex}); return false; }" 
                   value="0.00">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm readonly-field" name="items[${rowIndex}][amount]" 
                   step="0.01" readonly value="0.00">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // Store item data for later use
    row.dataset.itemId = item.id;
    row.dataset.itemData = JSON.stringify(item);
    
    // Add click event to row to update calculation section
    row.addEventListener('click', function() {
        selectRowForCalculation(rowIndex);
    });
    
    // Focus on batch input
    setTimeout(() => {
        const batchInput = row.querySelector('input[name*="[batch]"]');
        if (batchInput) {
            batchInput.focus();
        }
    }, 100);
}

// Check if Batch Exists
function checkBatchExists(rowIndex, itemId, batchNo) {
    if (!batchNo || batchNo.trim() === '') {
        return;
    }
    
    // Show loading indicator
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    fetch(`{{ route('admin.batches.check-batch') }}?item_id=${itemId}&batch_no=${encodeURIComponent(batchNo)}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Batch check response:', data);
        if (data.exists && data.batches && data.batches.length > 0) {
            // Show batch details modal
            currentItemForBatch = {
                rowIndex: rowIndex,
                itemId: itemId,
                batchNo: batchNo,
                itemName: row.querySelector('input[name*="[name]"]').value,
                packing: data.item_packing || ''
            };
            console.log('Showing batch modal with batches:', data.batches);
            showBatchDetailsModal(data.batches, data.item_name, data.item_packing);
        } else {
            // Batch doesn't exist, show confirmation modal
            console.log('Batch not found or no batches returned');
            currentItemForBatch = {
                rowIndex: rowIndex,
                itemId: itemId,
                batchNo: batchNo,
                itemName: row.querySelector('input[name*="[name]"]').value,
                packing: data.item_packing || ''
            };
            showBatchNotExistModal();
        }
    })
    .catch(error => {
        console.error('Error checking batch:', error);
        if (window.crudNotification) {
            crudNotification.showToast('error', 'Error', 'Error checking batch: ' + error.message);
        }
    });
}

// Show Batch Details Modal
function showBatchDetailsModal(batches, itemName, packing) {
    let tableRows = '';
    
    batches.forEach(batch => {
        const expiryDate = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}).replace(/ /g, '-') : '';
        const mfgDate = batch.manufacturing_date ? new Date(batch.manufacturing_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}).replace(/ /g, '-') : '';
        
        tableRows += `
            <tr style="background-color: ${batch.total_qty > 0 ? '#ffcccc' : '#ffffff'}; padding: 2px;">
                <td style="padding: 3px;"><strong>${batch.batch_no || ''}</strong></td>
                <td style="padding: 3px;">${mfgDate}</td>
                <td class="text-end" style="padding: 3px;">${parseFloat(batch.s_rate || 0).toFixed(2)}</td>
                <td class="text-end" style="padding: 3px;">${parseFloat(batch.pur_rate || 0).toFixed(2)}</td>
                <td class="text-end" style="padding: 3px;"><strong>${parseFloat(batch.mrp || 0).toFixed(2)}</strong></td>
                <td class="text-end" style="padding: 3px;"><strong>${parseFloat(batch.total_qty || 0).toFixed(0)}</strong></td>
                <td style="padding: 3px;"><strong>${expiryDate}</strong></td>
                <td style="padding: 3px;">${batch.item_code || ''}</td>
                <td class="text-end" style="padding: 3px;">${parseFloat(batch.cost_gst || 0).toFixed(2)}</td>
                <td class="text-end" style="padding: 3px;">${parseFloat(batch.sale_scheme || 0).toFixed(2)}</td>
                <td class="text-center" style="padding: 3px;">
                    <button type="button" class="btn btn-sm btn-success" style="padding: 2px 6px; font-size: 10px;" onclick='selectBatch(${JSON.stringify(batch).replace(/'/g, "&apos;")})'>
                        <i class="bi bi-check-circle"></i> Select
                    </button>
                </td>
            </tr>
        `;
    });
    
    const modalHTML = `
        <div class="batch-modal-backdrop" id="batchModalBackdrop" onclick="closeBatchModal()"></div>
        <div class="batch-modal" id="batchModal">
            <div class="batch-modal-content">
                <div class="batch-modal-header">
                    <h5 class="batch-modal-title">Batch Details - ${itemName || ''} | Packing: ${packing || ''}</h5>
                    <button type="button" class="btn-close-modal" onclick="closeBatchModal()">&times;</button>
                </div>
                <div class="batch-modal-body">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 10px; margin-bottom: 0;">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 10; background: #e9ecef;">
                                <tr>
                                    <th style="width: 60px; padding: 3px;">BATCH</th>
                                    <th style="width: 70px; padding: 3px;">DATE</th>
                                    <th style="width: 50px; padding: 3px;">RATE</th>
                                    <th style="width: 50px; padding: 3px;">P.RATE</th>
                                    <th style="width: 50px; padding: 3px;">MRP</th>
                                    <th style="width: 45px; padding: 3px;">QTY.</th>
                                    <th style="width: 60px; padding: 3px;">EXP.</th>
                                    <th style="width: 50px; padding: 3px;">CODE</th>
                                    <th style="width: 70px; padding: 3px;">Cost+GST</th>
                                    <th style="width: 50px; padding: 3px;">SCM</th>
                                    <th style="width: 60px; padding: 3px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>${tableRows}</tbody>
                        </table>
                    </div>
                </div>
                <div class="batch-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('batchModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('batchModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Ensure modal elements exist before adding show class
    setTimeout(() => {
        const backdrop = document.getElementById('batchModalBackdrop');
        const modal = document.getElementById('batchModal');
        if (backdrop) backdrop.classList.add('show');
        if (modal) modal.classList.add('show');
    }, 50);
}

// Close Batch Modal
function closeBatchModal() {
    const modal = document.getElementById('batchModal');
    const backdrop = document.getElementById('batchModalBackdrop');
    
    if (modal) modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    if (backdrop) backdrop.style.animation = 'fadeOut 0.3s ease forwards';
    
    setTimeout(() => {
        if (modal) modal.remove();
        if (backdrop) backdrop.remove();
    }, 300);
}

// Select Batch and Fill Row
function selectBatch(batch) {
    if (!currentItemForBatch) return;
    
    const rowIndex = currentItemForBatch.rowIndex;
    const row = document.getElementById(`row-${rowIndex}`);
    
    if (row) {
        // Fill expiry date
        const expiryInput = row.querySelector('input[name*="[expiry]"]');
        if (expiryInput && batch.expiry_date) {
            const expiryDate = new Date(batch.expiry_date);
            const formattedExpiry = `${String(expiryDate.getMonth() + 1).padStart(2, '0')}/${String(expiryDate.getFullYear()).slice(-2)}`;
            expiryInput.value = formattedExpiry;
        }
        
        // Fill MRP
        const mrpInput = row.querySelector('input[name*="[mrp]"]');
        if (mrpInput) {
            mrpInput.value = parseFloat(batch.mrp || 0).toFixed(2);
        }
        
        // Store batch data including rates
        row.dataset.batchData = JSON.stringify(batch);
        row.dataset.sRate = batch.s_rate || 0;
        row.dataset.pRate = batch.pur_rate || 0;
        row.dataset.mrp = batch.mrp || 0;
        
        // Get item data from row
        const itemData = JSON.parse(row.dataset.itemData || '{}');
        
        // Don't show HSN details yet - will show when row is completed
        // Just highlight the row as active
        row.style.backgroundColor = '#e7f3ff';
    }
    
    // Close modal
    closeBatchModal();
    
    // Recalculate all amounts
    calculateRowAmount(rowIndex);
    updateAllCalculations();
    
    // Move cursor to expiry field and select it
    setTimeout(() => {
        const expiryInput = row.querySelector('input[name*="[expiry]"]');
        if (expiryInput) {
            expiryInput.focus();
            expiryInput.select();
        }
    }, 150);
    
    currentItemForBatch = null;
}

// Move to Next Field
function moveToNextField(rowIndex, nextFieldName) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const nextField = row.querySelector(`[name*="[${nextFieldName}]"]`);
    if (nextField) {
        setTimeout(() => {
            nextField.focus();
            // Select text for input fields
            if (nextField.tagName === 'INPUT' && nextField.type !== 'number') {
                nextField.select();
            } else if (nextField.tagName === 'INPUT' && nextField.type === 'number') {
                // For number inputs, select all
                nextField.select();
            } else if (nextField.tagName === 'SELECT') {
                // For select, just focus
                nextField.focus();
            }
        }, 100);
    }
}

// Handle Discount and Add New Row - Called when Enter is pressed on Dis% field
function handleDiscountAndAddRow(rowIndex) {
    console.log('handleDiscountAndAddRow called for row:', rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) {
        console.log('Row not found:', rowIndex);
        return;
    }
    
    console.log('Marking row as completed');
    // Mark row as completed
    markRowAsCompleted(rowIndex);
    
    // Calculate the row amount
    calculateRowAmount(rowIndex);
    
    // Clear HSN section (will show next row's data when selected)
    clearCalculationSection();
    
    // Remove focus from current field
    document.activeElement.blur();
    
    // Don't automatically add new row - user will click "Insert Items" button manually
}

// Handle Discount Change - Highlights row and triggers calculations
function handleDiscountChange(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    // Calculate the row amount
    calculateRowAmount(rowIndex);
}

// Mark row as completed (green background)
function markRowAsCompleted(rowIndex) {
    console.log('markRowAsCompleted called for row:', rowIndex);
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) {
        console.log('Row not found in markRowAsCompleted:', rowIndex);
        return;
    }
    
    console.log('Setting green background');
    // Set green background
    row.style.backgroundColor = '#d4edda';
    row.dataset.completed = 'true';
    
    // Apply to all cells
    const cells = row.querySelectorAll('td');
    cells.forEach(cell => {
        cell.style.backgroundColor = '#d4edda';
    });
    
    console.log('Row marked as completed, background:', row.style.backgroundColor);
}

// Clear calculation section
function clearCalculationSection() {
    document.getElementById('calc_hsn_code').value = '---';
    document.getElementById('calc_cgst_percent').value = '0';
    document.getElementById('calc_sgst_percent').value = '0';
    document.getElementById('calc_cgst_amount').value = '0.00';
    document.getElementById('calc_sgst_amount').value = '0.00';
    document.getElementById('calc_tax_percent').value = '0';
    document.getElementById('calc_srate').value = '0.00';
    document.getElementById('calc_prate').value = '0.00';
    document.getElementById('calc_mrp').value = '0.00';
    document.getElementById('calc_pack').value = '';
    
    // Clear additional details
    document.getElementById('detail_packing').value = '0.00';
    document.getElementById('detail_unit').value = '0.00';
    document.getElementById('detail_cl_qty').value = '0.00';
    document.getElementById('detail_gross').value = '0.00';
    document.getElementById('detail_scm_amt').value = '0.00';
    document.getElementById('detail_dis_amt').value = '0.00';
    document.getElementById('detail_subtotal').value = '0.00';
    document.getElementById('detail_tax_amt').value = '0.00';
    document.getElementById('detail_net_amt').value = '0.00';
}

// Calculate Row Amount
function calculateRowAmount(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const mrp = parseFloat(row.querySelector('input[name*="[mrp]"]').value) || 0;
    const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]').value) || 0;
    const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]').value) || 0;
    
    // Calculate amount
    const grossAmount = qty * mrp;
    const schemeAmount = grossAmount * (scmPercent / 100);
    const afterScheme = grossAmount - schemeAmount;
    const discountAmount = afterScheme * (disPercent / 100);
    const amount = afterScheme - discountAmount;
    
    // Update amount field
    const amountInput = row.querySelector('input[name*="[amount]"]');
    if (amountInput) {
        amountInput.value = amount.toFixed(2);
    }
    
    // Update calculation section and additional details if this row is selected
    const itemData = JSON.parse(row.dataset.itemData || '{}');
    const batchData = JSON.parse(row.dataset.batchData || '{}');
    
    // Check if this row is currently selected (has light blue background)
    if (row.style.backgroundColor === 'rgb(231, 243, 255)' || row.style.backgroundColor === '#e7f3ff') {
        updateAdditionalDetails(row, itemData);
    }
    
    // Recalculate summary totals
    updateAllCalculations();
}

// Select Row for Calculation - Updates calculation section with selected row's data
function selectRowForCalculation(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (!row) return;
    
    // Check if row is completed (green)
    const isCompleted = row.dataset.completed === 'true';
    
    // Remove highlight from all rows except completed ones
    const allRows = document.querySelectorAll('#itemsTableBody tr');
    allRows.forEach(r => {
        if (r.dataset.completed !== 'true') {
            r.style.backgroundColor = '';
        }
    });
    
    // Highlight selected row (light blue) if not completed
    if (!isCompleted) {
        row.style.backgroundColor = '#e7f3ff';
    }
    
    // Only show HSN details if row is completed
    if (isCompleted) {
        // Get item and batch data from row
        const itemData = JSON.parse(row.dataset.itemData || '{}');
        const batchData = JSON.parse(row.dataset.batchData || '{}');
        
        // Update calculation section
        updateCalculationSection(batchData, itemData);
        
        // Update additional details section with this row's data
        updateAdditionalDetails(row, itemData);
    } else {
        // Clear calculation section for incomplete rows
        clearCalculationSection();
    }
}

// Update Calculation Section with Batch and Item Data
function updateCalculationSection(batch, item) {
    console.log('Updating calculation section with:', { batch, item });
    
    // Fill from batch
    document.getElementById('calc_srate').value = parseFloat(batch.s_rate || 0).toFixed(2);
    document.getElementById('calc_prate').value = parseFloat(batch.pur_rate || 0).toFixed(2);
    document.getElementById('calc_mrp').value = parseFloat(batch.mrp || 0).toFixed(2);
    
    // Fill from item
    document.getElementById('calc_hsn_code').value = item.hsn_code || '---';
    document.getElementById('calc_pack').value = item.packing || '';
    
    // Calculate tax percentages (assuming GST is split equally between CGST and SGST)
    const gstPercent = parseFloat(item.gst_percent || 0);
    const cgstPercent = gstPercent / 2;
    const sgstPercent = gstPercent / 2;
    
    console.log('Tax calculations:', { gstPercent, cgstPercent, sgstPercent });
    
    document.getElementById('calc_cgst_percent').value = cgstPercent.toFixed(2);
    document.getElementById('calc_sgst_percent').value = sgstPercent.toFixed(2);
    document.getElementById('calc_tax_percent').value = gstPercent.toFixed(2);
}

// Update Additional Details Section with selected row's data
function updateAdditionalDetails(row, itemData) {
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
    const mrp = parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0;
    const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]')?.value) || 0;
    const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value) || 0;
    const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
    
    // Calculate individual row values
    const grossAmount = qty * mrp;
    const schemeAmount = grossAmount * (scmPercent / 100);
    const discountAmount = (grossAmount - schemeAmount) * (disPercent / 100);
    const subtotal = grossAmount - schemeAmount - discountAmount;
    
    // Get tax from calculation section
    const cgstPercent = parseFloat(document.getElementById('calc_cgst_percent').value) || 0;
    const sgstPercent = parseFloat(document.getElementById('calc_sgst_percent').value) || 0;
    const taxPercent = cgstPercent + sgstPercent;
    
    // Calculate CGST and SGST amounts separately
    const cgstAmount = subtotal * (cgstPercent / 100);
    const sgstAmount = subtotal * (sgstPercent / 100);
    const taxAmount = cgstAmount + sgstAmount;
    const netAmount = subtotal + taxAmount;
    
    // Update CGST and SGST amounts in calculation section
    document.getElementById('calc_cgst_amount').value = cgstAmount.toFixed(2);
    document.getElementById('calc_sgst_amount').value = sgstAmount.toFixed(2);
    
    // Get closing quantity from item data (total of all batches)
    const closingQty = parseFloat(itemData.total_qty || 0);
    
    // Update additional details section
    document.getElementById('detail_packing').value = parseFloat(itemData.packing || 0).toFixed(2);
    document.getElementById('detail_unit').value = parseFloat(itemData.unit || 0).toFixed(2);
    document.getElementById('detail_cl_qty').value = closingQty.toFixed(2);
    document.getElementById('detail_gross').value = grossAmount.toFixed(2);
    document.getElementById('detail_scm_amt').value = schemeAmount.toFixed(2);
    document.getElementById('detail_dis_amt').value = discountAmount.toFixed(2);
    document.getElementById('detail_subtotal').value = subtotal.toFixed(2);
    document.getElementById('detail_tax_amt').value = taxAmount.toFixed(2);
    document.getElementById('detail_net_amt').value = netAmount.toFixed(2);
}

// Update All Calculations - Updates SUMMARY section with totals from all rows
function updateAllCalculations() {
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    let totalMrpValue = 0;
    let totalGross = 0;
    let totalDiscount = 0;
    let totalScheme = 0;
    let totalQty = 0;
    let totalTaxAmount = 0;
    
    // Calculate totals from all rows
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]')?.value) || 0;
        const mrp = parseFloat(row.querySelector('input[name*="[mrp]"]')?.value) || 0;
        const scmPercent = parseFloat(row.querySelector('input[name*="[scm_percent]"]')?.value) || 0;
        const disPercent = parseFloat(row.querySelector('input[name*="[dis_percent]"]')?.value) || 0;
        
        totalQty += qty;
        
        // Calculate for this row
        const grossAmount = qty * mrp;
        const schemeAmount = grossAmount * (scmPercent / 100);
        const discountAmount = (grossAmount - schemeAmount) * (disPercent / 100);
        const subtotal = grossAmount - schemeAmount - discountAmount;
        
        // Get tax percentage from item data
        const itemData = JSON.parse(row.dataset.itemData || '{}');
        const gstPercent = parseFloat(itemData.gst_percent || 0);
        const taxAmount = subtotal * (gstPercent / 100);
        
        totalMrpValue += grossAmount;
        totalGross += subtotal;
        totalScheme += schemeAmount;
        totalDiscount += discountAmount;
        totalTaxAmount += taxAmount;
    });
    
    const totalNet = totalGross + totalTaxAmount;
    
    // Update SUMMARY section only (cumulative totals)
    document.getElementById('summary_mrp_value').value = totalMrpValue.toFixed(2);
    document.getElementById('summary_gross').value = totalGross.toFixed(2);
    document.getElementById('summary_discount').value = totalDiscount.toFixed(2);
    document.getElementById('summary_scheme').value = totalScheme.toFixed(2);
    document.getElementById('summary_tax').value = totalTaxAmount.toFixed(2);
    document.getElementById('summary_net').value = totalNet.toFixed(2);
    
    // Update payable amount in calculation section
    document.getElementById('calc_payable_amount').value = totalNet.toFixed(2);
}

// Remove Row
function removeRow(rowIndex) {
    const row = document.getElementById(`row-${rowIndex}`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

// Add New Row - Opens item selection modal
function addNewRow() {
    // Check if customer is selected
    const customerId = document.querySelector('select[name="customer_id"]').value;
    
    if (!customerId) {
        showAlert('error', 'Please select a customer first.');
        return;
    }
    
    openItemSelectionModal();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Breakage/Expiry Transaction page loaded');
    
    // Handle form submission
    document.getElementById('breakageExpiryTransactionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveTransaction();
    });
});

// Save Transaction - Show Credit Note Modal First
function saveTransaction() {
    // Validate first
    const form = document.getElementById('breakageExpiryTransactionForm');
    const formData = new FormData(form);
    
    if (!formData.get('customer_id')) {
        showAlert('error', 'Please select a customer');
        return;
    }
    
    const rows = document.querySelectorAll('#itemsTableBody tr');
    let hasItems = false;
    rows.forEach((row) => {
        const code = row.querySelector('input[name*="[code]"]')?.value || '';
        const name = row.querySelector('input[name*="[name]"]')?.value || '';
        if (code && name) {
            hasItems = true;
        }
    });
    
    if (!hasItems) {
        showAlert('error', 'Please add at least one item');
        return;
    }
    
    // Store Payable Amount globally for adjustment modal
    window.netAmount = parseFloat(document.getElementById('calc_payable_amount').value || 0);
    
    // Show credit note modal
    showCreditNoteModal();
}

// Show Credit Note Modal
function showCreditNoteModal() {
    const modal = document.getElementById('creditNoteModal');
    modal.classList.add('show');
    
    // Remove any existing ESC listeners first
    document.removeEventListener('keydown', window.creditNoteEscHandler);
    
    // Create new ESC key handler
    window.creditNoteEscHandler = function(e) {
        if (e.key === 'Escape') {
            closeCreditNoteModal();
        }
    };
    
    document.addEventListener('keydown', window.creditNoteEscHandler);
}

// Close Credit Note Modal
function closeCreditNoteModal() {
    const modal = document.getElementById('creditNoteModal');
    modal.classList.remove('show');
    
    // Remove ESC key listener
    document.removeEventListener('keydown', window.creditNoteEscHandler);
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Save Without Credit Note
function saveWithoutCreditNote() {
    closeCreditNoteModal();
    submitTransaction(false);
}

// Save With Credit Note
function saveWithCreditNote() {
    closeCreditNoteModal();
    
    const customerId = document.querySelector('select[name="customer_id"]').value;
    if (!customerId) {
        showAlert('error', 'Please select a customer first');
        return;
    }
    
    fetchCustomerSales(customerId);
}

// Submit Transaction (Original Logic)
function submitTransaction(withCreditNote = false, adjustments = []) {
    const form = document.getElementById('breakageExpiryTransactionForm');
    const formData = new FormData(form);
    
    // Get all form data
    const data = {
        _token: formData.get('_token'),
        series: formData.get('series'),
        transaction_date: formData.get('transaction_date'),
        end_date: formData.get('end_date'),
        customer_id: formData.get('customer_id'),
        salesman_id: formData.get('salesman_id'),
        gst_vno: formData.get('gst_vno'),
        note_type: formData.get('note_type'),
        with_gst: formData.get('with_gst'),
        inc: formData.get('inc'),
        rev_charge: formData.get('rev_charge'),
        adjusted: formData.get('adjusted'),
        dis_rpl: formData.get('dis_rpl'),
        brk: formData.get('brk'),
        exp: formData.get('exp'),
        
        // Summary values
        summary_mrp_value: document.getElementById('summary_mrp_value').value,
        summary_gross: document.getElementById('summary_gross').value,
        summary_discount: document.getElementById('summary_discount').value,
        summary_scheme: document.getElementById('summary_scheme').value,
        summary_tax: document.getElementById('summary_tax').value,
        summary_net: document.getElementById('summary_net').value,
        
        // Detail values
        detail_packing: document.getElementById('detail_packing').value,
        detail_unit: document.getElementById('detail_unit').value,
        detail_cl_qty: document.getElementById('detail_cl_qty').value,
        detail_scm_amt: document.getElementById('detail_scm_amt').value,
        detail_dis_amt: document.getElementById('detail_dis_amt').value,
        detail_subtotal: document.getElementById('detail_subtotal').value,
        detail_tax_amt: document.getElementById('detail_tax_amt').value,
        detail_net_amt: document.getElementById('detail_net_amt').value,
        
        // Credit note data
        with_credit_note: withCreditNote,
        adjustments: adjustments,
        
        // Items
        items: []
    };
    
    // Get all items from table
    const rows = document.querySelectorAll('#itemsTableBody tr');
    rows.forEach((row, index) => {
        const item = {
            code: row.querySelector('input[name*="[code]"]')?.value || '',
            name: row.querySelector('input[name*="[name]"]')?.value || '',
            batch: row.querySelector('input[name*="[batch]"]')?.value || '',
            expiry: row.querySelector('input[name*="[expiry]"]')?.value || '',
            br_ex: row.querySelector('select[name*="[br_ex]"]')?.value || '',
            qty: row.querySelector('input[name*="[qty]"]')?.value || 0,
            f_qty: row.querySelector('input[name*="[f_qty]"]')?.value || 0,
            mrp: row.querySelector('input[name*="[mrp]"]')?.value || 0,
            scm_percent: row.querySelector('input[name*="[scm_percent]"]')?.value || 0,
            dis_percent: row.querySelector('input[name*="[dis_percent]"]')?.value || 0,
            amount: row.querySelector('input[name*="[amount]"]')?.value || 0
        };
        
        // Only add if item has code and name
        if (item.code && item.name) {
            data.items.push(item);
        }
    });
    
    // Show loading
    showAlert('info', 'Saving transaction...');
    
    // Determine if this is an update or create
    const transactionId = window.currentTransactionId || null;
    
    // DEBUG: Check transaction ID
    console.log('🔍 DEBUG - Transaction ID Check:', {
        'window.currentTransactionId': window.currentTransactionId,
        'transactionId': transactionId,
        'mode': transactionId ? 'UPDATE' : 'CREATE'
    });
    
    const url = transactionId 
        ? `{{ url('/admin/breakage-expiry/transaction') }}/${transactionId}`
        : '{{ route("admin.breakage-expiry.transaction.store") }}';
    const method = transactionId ? 'PUT' : 'POST';
    
    console.log('Transaction mode:', transactionId ? 'UPDATE' : 'CREATE');
    console.log('Transaction ID:', transactionId);
    console.log('URL:', url);
    console.log('Method:', method);
    
    // Log the data being sent for debugging
    console.log('Submitting transaction data:', data);
    
    // 🔥 Mark as saving to prevent exit confirmation dialog
    if (typeof window.markAsSaving === 'function') {
        window.markAsSaving();
    }
    
    // Submit
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': data._token
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json().then(data => ({
            status: response.status,
            data: data
        }));
    })
    .then(({status, data}) => {
        console.log('Response data:', data);
        
        if (data.success) {
            showAlert('success', 'Transaction saved successfully');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            // Show validation errors if present
            if (data.errors) {
                console.error('Validation errors:', data.errors);
                let errorMessage = 'Validation errors:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `- ${key}: ${data.errors[key].join(', ')}\n`;
                });
                showAlert('error', errorMessage);
            } else {
                showAlert('error', data.message || 'Error saving transaction');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error saving transaction: ' + error.message);
    });
}

// Fetch Customer Sales for Adjustment
function fetchCustomerSales(customerId) {
    showAlert('info', 'Loading customer sales...');
    
    // Use the existing customers/{customer}/sales route
    const url = `{{ url('/admin/customers') }}/${customerId}/sales`;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.sales && data.sales.length > 0) {
            showAdjustmentModal(data.sales, data.customer);
        } else {
            showAlert('warning', 'No sales transactions found for this customer');
            // Still allow saving without credit note
            submitTransaction(false);
        }
    })
    .catch(error => {
        console.error('Error fetching customer sales:', error);
        showAlert('error', 'Error loading customer sales. Saving without credit note.');
        submitTransaction(false);
    });
}

// Show Adjustment Modal
// Show Adjustment Modal
function showAdjustmentModal(sales, customer) {
    if (!sales || sales.length === 0) {
        showAlert('warning', 'No outstanding sales found for this customer');
        submitTransaction(false);
        return;
    }
    
    const netAmount = window.netAmount || 0;
    
    // Check if we're editing an existing transaction
    const transactionId = window.currentTransactionId || null;
    
    // Function to render modal content with optional pre-filled adjustments
    const renderModalContent = (existingAdjustments = {}) => {
        const modalContent = `
            <div style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #dee2e6;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h6 style="margin: 0; color: #495057;">Customer: ${customer.name} (${customer.code})</h6>
                        <small style="color: #6c757d;">Outstanding Sales: ${sales.length} invoice(s)</small>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 14px; color: #495057;">Payable Amount</div>
                        <div style="font-size: 18px; font-weight: bold; color: #28a745;">₹ ${netAmount.toFixed(2)}</div>
                    </div>
                </div>
            </div>
            
            <div style="padding: 15px;">
                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                    <table class="table table-sm table-bordered mb-0" style="font-size: 11px;">
                        <thead style="background: #e9ecef; position: sticky; top: 0;">
                            <tr>
                                <th style="width: 50px; text-align: center;">SR.NO.</th>
                                <th style="width: 120px; text-align: center;">Invoice No</th>
                                <th style="width: 80px; text-align: center;">Date</th>
                                <th style="width: 90px; text-align: right;">BILL AMT.</th>
                                <th style="width: 90px; text-align: center;">ADJUST AMT.</th>
                                <th style="width: 90px; text-align: right;">REMAINING BAL.</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${sales.map((sale, index) => {
                                const billAmount = parseFloat(sale.bill_amount || 0);
                                const currentBalance = parseFloat(sale.balance || sale.bill_amount || 0);
                                const preFilledAmount = parseFloat(existingAdjustments[sale.id] || 0);
                                
                                // Calculate original bill amount: current balance + previous adjustment
                                const originalBillAmount = currentBalance + preFilledAmount;
                                
                                return `
                                <tr>
                                    <td style="text-align: center;">${index + 1}</td>
                                    <td style="text-align: center;">${sale.trans_no}</td>
                                    <td style="text-align: center;">${sale.date}</td>
                                    <td style="text-align: right; font-weight: bold; color: #0d6efd;">${originalBillAmount.toFixed(2)}</td>
                                    <td style="text-align: center;">
                                        <input type="number" class="form-control form-control-sm adjustment-input" 
                                               id="adj_${sale.id}" 
                                               data-sale-id="${sale.id}"
                                               data-balance="${originalBillAmount}"
                                               data-bill-amount="${originalBillAmount}"
                                               max="${originalBillAmount}"
                                               step="0.01" 
                                               style="width: 90px; font-size: 10px;"
                                               value="${preFilledAmount > 0 ? preFilledAmount.toFixed(2) : ''}"
                                               placeholder="0.00">
                                    </td>
                                    <td style="text-align: right; font-weight: bold;" id="remaining_${sale.id}">
                                        <span style="color: #28a745;">${currentBalance.toFixed(2)}</span>
                                    </td>
                                </tr>
                            `}).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="mt-3" style="background: #f8f9fa; padding: 10px; border-radius: 4px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="font-weight: bold; color: #dc3545;">EXIT : &lt;ESC &gt;</span>
                        <span style="font-weight: bold; font-size: 16px; color: #0d6efd;">
                            ADJUST AMOUNT (Rs) : <span id="adjustmentBalance">${netAmount.toFixed(2)}</span>
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <label style="font-weight: bold; color: #495057;">Auto Adjust Amount:</label>
                        <input type="number" id="autoAdjustAmount" class="form-control form-control-sm" 
                               style="width: 120px;" step="0.01" placeholder="Enter amount"
                               onchange="autoDistributeAmount()">
                        <button type="button" class="btn btn-info btn-sm" onclick="autoDistributeAmount()">
                            <i class="bi bi-magic me-1"></i>Auto Distribute
                        </button>
                    </div>
                    <div class="mt-2" style="text-align: right;">
                        <button type="button" class="btn btn-success btn-sm me-2" onclick="saveAdjustment()">
                            <i class="bi bi-check-circle me-1"></i>Save & Submit
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustmentModal()">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('adjustmentContent').innerHTML = modalContent;
        
        const modal = document.getElementById('adjustmentModal');
        modal.classList.add('show');
        
        // Add event listeners using event delegation
        const adjustmentContent = document.getElementById('adjustmentContent');
        if (adjustmentContent) {
            adjustmentContent.addEventListener('input', function(e) {
                if (e.target.classList.contains('adjustment-input')) {
                    const saleId = e.target.getAttribute('data-sale-id');
                    updateRemainingBalance(saleId);
                }
            });
            
            adjustmentContent.addEventListener('change', function(e) {
                if (e.target.classList.contains('adjustment-input')) {
                    const saleId = e.target.getAttribute('data-sale-id');
                    updateRemainingBalance(saleId);
                }
            });
        }
        
        // Update balance display after pre-filling
        if (Object.keys(existingAdjustments).length > 0) {
            Object.keys(existingAdjustments).forEach(saleId => {
                updateRemainingBalance(saleId);
            });
        }
        
        // Remove any existing ESC listeners first
        document.removeEventListener('keydown', window.adjustmentEscHandler);
        
        // Create new ESC key handler
        window.adjustmentEscHandler = function(e) {
            if (e.key === 'Escape') {
                closeAdjustmentModal();
            }
        };
        
        document.addEventListener('keydown', window.adjustmentEscHandler);
    };
    
    // If editing existing transaction, fetch existing adjustments
    if (transactionId) {
        fetch(`{{ url('/admin/breakage-expiry/transaction') }}/${transactionId}/adjustments`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.adjustments) {
                // Create a map of sale_transaction_id => adjusted_amount
                const adjustmentsMap = {};
                data.adjustments.forEach(adj => {
                    adjustmentsMap[adj.sale_transaction_id] = adj.adjusted_amount;
                });
                renderModalContent(adjustmentsMap);
            } else {
                renderModalContent();
            }
        })
        .catch(error => {
            console.error('Error fetching existing adjustments:', error);
            renderModalContent(); // Show modal anyway without pre-filled data
        });
    } else {
        // New transaction, no pre-filled data
        renderModalContent();
    }
}

// Update Remaining Balance for Individual Row
function updateRemainingBalance(saleId) {
    const id = String(saleId);
    const input = document.getElementById(`adj_${id}`);
    const remainingCell = document.getElementById(`remaining_${id}`);
    
    if (!input || !remainingCell) return;
    
    const currentBalance = parseFloat(input.getAttribute('data-balance') || 0);
    const adjustedAmount = parseFloat(input.value || 0);
    
    // Validate adjustment amount
    if (adjustedAmount > currentBalance) {
        input.value = currentBalance.toFixed(2);
        showAlert('warning', 'Adjustment amount cannot exceed current balance');
        return;
    }
    
    const remainingBalance = currentBalance - adjustedAmount;
    
    // Update remaining balance with color coding
    let color = '#28a745'; // Green for positive
    if (remainingBalance === 0) {
        color = '#6c757d'; // Gray for zero
    } else if (remainingBalance < currentBalance * 0.3) {
        color = '#ffc107'; // Yellow for low balance
    }
    
    remainingCell.innerHTML = `<span style="color: ${color};">${remainingBalance.toFixed(2)}</span>`;
    
    // Also update total adjustment balance
    updateAdjustmentBalance();
}

// Auto Distribute Amount Across Transactions
function autoDistributeAmount() {
    const totalAmount = parseFloat(document.getElementById('autoAdjustAmount').value || 0);
    
    if (totalAmount <= 0) {
        showAlert('warning', 'Please enter a valid amount to distribute');
        return;
    }
    
    // Clear all existing adjustments first
    document.querySelectorAll('.adjustment-input').forEach(input => {
        input.value = '';
        const saleId = input.getAttribute('data-sale-id');
        updateRemainingBalance(saleId);
    });
    
    // Get all transactions sorted by balance
    const inputs = Array.from(document.querySelectorAll('.adjustment-input'));
    const transactions = inputs.map(input => ({
        input: input,
        saleId: input.getAttribute('data-sale-id'),
        balance: parseFloat(input.getAttribute('data-balance'))
    })).filter(t => t.balance > 0).sort((a, b) => b.balance - a.balance);
    
    let remainingAmount = totalAmount;
    
    // Distribute amount across transactions
    transactions.forEach(transaction => {
        if (remainingAmount <= 0) return;
        
        const adjustAmount = Math.min(remainingAmount, transaction.balance);
        transaction.input.value = adjustAmount.toFixed(2);
        remainingAmount -= adjustAmount;
        
        updateRemainingBalance(transaction.saleId);
    });
    
    // If still amount remaining, show warning
    if (remainingAmount > 0) {
        showAlert('warning', `₹${remainingAmount.toFixed(2)} could not be distributed. Insufficient outstanding balance.`);
    } else {
        showAlert('success', `₹${totalAmount.toFixed(2)} distributed successfully across ${transactions.length} transaction(s)`);
    }
}

// Update Adjustment Balance
function updateAdjustmentBalance() {
    const inputs = document.querySelectorAll('.adjustment-input');
    let totalAdjusted = 0;
    
    inputs.forEach(input => {
        totalAdjusted += parseFloat(input.value || 0);
    });
    
    // Update remaining adjustment balance at the bottom
    const remainingBalance = (window.netAmount || 0) - totalAdjusted;
    const adjustmentBalanceEl = document.getElementById('adjustmentBalance');
    
    if (adjustmentBalanceEl) {
        adjustmentBalanceEl.textContent = `${remainingBalance.toFixed(2)}`;
        
        // Change color based on remaining balance
        const balanceSpan = adjustmentBalanceEl.parentElement;
        if (balanceSpan) {
            if (remainingBalance < 0) {
                balanceSpan.style.color = '#dc3545'; // Red for over-adjusted
            } else if (remainingBalance === 0) {
                balanceSpan.style.color = '#28a745'; // Green for fully adjusted
            } else {
                balanceSpan.style.color = '#0d6efd'; // Blue for partial
            }
        }
    }
}

// Close Adjustment Modal
function closeAdjustmentModal() {
    const modal = document.getElementById('adjustmentModal');
    modal.classList.remove('show');
    
    // Remove ESC key listener
    document.removeEventListener('keydown', window.adjustmentEscHandler);
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Save Adjustment
function saveAdjustment() {
    const inputs = document.querySelectorAll('.adjustment-input');
    const adjustments = [];
    
    inputs.forEach(input => {
        const adjustedAmount = parseFloat(input.value || 0);
        if (adjustedAmount > 0) {
            adjustments.push({
                sale_id: input.getAttribute('data-sale-id'),
                adjusted_amount: adjustedAmount
            });
        }
    });
    
    closeAdjustmentModal();
    submitTransaction(true, adjustments);
}

// Open Create Batch Modal
function openCreateBatchModal() {
    // Close the batch not exist modal first
    closeBatchNotExistModal();
    
    if (!currentItemForBatch) return;
    
    const { itemId, batchNo, rowIndex, itemName, packing } = currentItemForBatch;
    const row = document.getElementById(`row-${rowIndex}`);
    
    if (!row) return;
    
    // Get item data from the row
    const itemData = JSON.parse(row.dataset.itemData || '{}');
    
    // Fetch item details to pre-populate the form
    fetch(`{{ url('/admin/items') }}/${itemId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                const item = data.item;
                
                // Pre-populate the form
                document.getElementById('batchItemName').value = item.name || itemName || '';
                document.getElementById('batchPack').value = item.packing || packing || '';
                document.getElementById('batchMrp').value = parseFloat(item.mrp || 0).toFixed(2);
                document.getElementById('batchSRate').value = parseFloat(item.s_rate || 0).toFixed(2);
                document.getElementById('batchPRate').value = parseFloat(item.pur_rate || 0).toFixed(2);
                document.getElementById('batchExpiry').value = '';
                
                // Store hidden values
                document.getElementById('batchItemId').value = itemId;
                document.getElementById('batchNumber').value = batchNo;
                document.getElementById('batchRowIndex').value = rowIndex;
                
                // Show the create batch modal
                setTimeout(() => {
                    const backdrop = document.getElementById('createBatchModalBackdrop');
                    const modal = document.getElementById('createBatchModal');
                    
                    if (backdrop && modal) {
                        backdrop.style.display = 'block';
                        modal.style.display = 'block';
                        
                        setTimeout(() => {
                            backdrop.classList.add('show');
                            modal.classList.add('show');
                        }, 10);
                    }
                }, 10);
            } else {
                showAlert('error', 'Error loading item details');
            }
        })
        .catch(error => {
            console.error('Error loading item details:', error);
            showAlert('error', 'Error loading item details: ' + error.message);
        });
}

// Save Batch
function saveBatch() {
    const form = document.getElementById('createBatchForm');
    
    const batchData = {
        item_id: document.getElementById('batchItemId').value,
        batch_no: document.getElementById('batchNumber').value,
        mrp: parseFloat(document.getElementById('batchMrp').value) || 0,
        s_rate: parseFloat(document.getElementById('batchSRate').value) || 0,
        pur_rate: parseFloat(document.getElementById('batchPRate').value) || 0,
        expiry_date: document.getElementById('batchExpiry').value || null,
        total_qty: 0 // Default quantity for new batch
    };
    
    // Validate required fields
    if (!batchData.mrp || !batchData.s_rate || !batchData.pur_rate) {
        showAlert('error', 'Please fill all required fields (MRP, S.Rate, P.Rate)');
        return;
    }
    
    // Validate item_id and batch_no
    if (!batchData.item_id || !batchData.batch_no) {
        showAlert('error', 'Missing item or batch information');
        return;
    }
    
    // Save batch to database
    fetch(`{{ route('admin.batches.store') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(batchData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Batch data sent:', batchData);
        if (!response.ok) {
            // Clone the response to read it as text for error logging
            return response.text().then(text => {
                console.error('=== BATCH CREATION ERROR ===');
                console.error('Status:', response.status);
                console.error('Status Text:', response.statusText);
                console.error('Response Body:', text);
                try {
                    const jsonError = JSON.parse(text);
                    console.error('Parsed Error:', jsonError);
                    throw new Error(jsonError.message || `HTTP error! status: ${response.status}`);
                } catch (e) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Batch creation response:', data);
        if (data.success) {
            // Close the modal
            closeCreateBatchModal();
            
            // Reopen the batch selection modal for the newly created batch
            const rowIndex = document.getElementById('batchRowIndex').value;
            const itemId = document.getElementById('batchItemId').value;
            const batchNo = batchData.batch_no;
            
            // Use checkBatchExists to fetch the batch list (which will now include the new batch)
            // and open the "Batch Details" modal
            checkBatchExists(rowIndex, itemId, batchNo);
            
            showAlert('success', 'Batch created successfully');
            // currentItemForBatch is NOT cleared here, so it persists for the selectBatch call
        } else {
            console.error('Batch creation failed:', data);
            showAlert('error', data.message || 'Error creating batch');
        }
    })
    .catch(error => {
        console.error('=== CATCH BLOCK ERROR ===');
        console.error('Error creating batch:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        showAlert('error', 'Error creating batch: ' + error.message);
    });
}

// Show Batch Not Exist Modal
function showBatchNotExistModal() {
    setTimeout(() => {
        const backdrop = document.getElementById('batchNotExistModalBackdrop');
        const modal = document.getElementById('batchNotExistModal');
        
        if (backdrop && modal) {
            backdrop.style.display = 'block';
            modal.style.display = 'block';
            
            setTimeout(() => {
                backdrop.classList.add('show');
                modal.classList.add('show');
            }, 10);
        }
    }, 10);
}

// Close Batch Not Exist Modal
function closeBatchNotExistModal() {
    const modal = document.getElementById('batchNotExistModal');
    const backdrop = document.getElementById('batchNotExistModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
        if (backdrop) {
            backdrop.classList.remove('show');
            backdrop.style.display = 'none';
        }
    }, 300);
}

// Close Create Batch Modal
function closeCreateBatchModal() {
    const modal = document.getElementById('createBatchModal');
    const backdrop = document.getElementById('createBatchModalBackdrop');
    
    if (modal) {
        modal.style.animation = 'zoomOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards';
    }
    if (backdrop) {
        backdrop.style.animation = 'fadeOut 0.3s ease forwards';
    }
    
    setTimeout(() => {
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
        if (backdrop) {
            backdrop.classList.remove('show');
            backdrop.style.display = 'none';
        }
    }, 300);
}

// Show alert function (like sale return)
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertTypes = {
        'success': { bg: '#28a745', icon: '✓' },
        'error': { bg: '#dc3545', icon: '✕' },
        'info': { bg: '#17a2b8', icon: 'ℹ' },
        'warning': { bg: '#ffc107', icon: '⚠' }
    };
    
    const alertConfig = alertTypes[type] || alertTypes['info'];
    
    const alertHTML = `
        <div class="custom-alert" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${alertConfig.bg};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10001;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        ">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px; font-weight: bold;">${alertConfig.icon}</span>
                <span>${message}</span>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        const alert = document.querySelector('.custom-alert');
        if (alert) {
            alert.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => alert.remove(), 300);
        }
    }, 3000);
}
</script>

<!-- Batch Does Not Exist Confirmation Modal -->
<div class="batch-not-exist-modal-backdrop" id="batchNotExistModalBackdrop" onclick="closeBatchNotExistModal()" style="display: none;"></div>
<div class="batch-not-exist-modal" id="batchNotExistModal" style="display: none;">
    <div class="batch-not-exist-modal-content">
        <div class="batch-not-exist-modal-header">
            <h5 class="batch-not-exist-modal-title">
                <i class="bi bi-question-circle me-2"></i>Batch Does not Exists Create New Batch
            </h5>
            <button type="button" class="btn-close-modal" onclick="closeBatchNotExistModal()">&times;</button>
        </div>
        <div class="batch-not-exist-modal-body">
            <p class="mb-3">The batch number you entered does not exist. Would you like to create a new batch?</p>
        </div>
        <div class="batch-not-exist-modal-footer">
            <button type="button" class="btn btn-success btn-sm" onclick="openCreateBatchModal()">Yes</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchNotExistModal()">No</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="closeBatchNotExistModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Create New Batch Modal -->
<div class="create-batch-modal-backdrop" id="createBatchModalBackdrop" onclick="closeCreateBatchModal()" style="display: none;"></div>
<div class="create-batch-modal" id="createBatchModal" style="display: none;">
    <div class="create-batch-modal-content">
        <div class="create-batch-modal-header">
            <h5 class="create-batch-modal-title">
                <i class="bi bi-plus-circle me-2"></i>Create New Batch
            </h5>
            <button type="button" class="btn-close-modal" onclick="closeCreateBatchModal()">&times;</button>
        </div>
        <div class="create-batch-modal-body">
            <form id="createBatchForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Item Name:</label>
                            <input type="text" class="form-control form-control-sm" id="batchItemName" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pack:</label>
                            <input type="text" class="form-control form-control-sm" id="batchPack" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">MRP:</label>
                            <input type="number" class="form-control form-control-sm" id="batchMrp" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">S.Rate:</label>
                            <input type="number" class="form-control form-control-sm" id="batchSRate" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">P.Rate:</label>
                            <input type="number" class="form-control form-control-sm" id="batchPRate" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Expiry:</label>
                            <input type="text" class="form-control form-control-sm" id="batchExpiry" placeholder="MM/YY">
                        </div>
                    </div>
                </div>
                <input type="hidden" id="batchItemId">
                <input type="hidden" id="batchNumber">
                <input type="hidden" id="batchRowIndex">
            </form>
        </div>
        <div class="create-batch-modal-footer">
            <button type="button" class="btn btn-primary btn-sm" onclick="saveBatch()">
                <i class="bi bi-check-circle me-1"></i>OK
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeCreateBatchModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Credit Note Modal -->
<div id="creditNoteModal" class="credit-note-modal">
    <div class="credit-note-modal-content">
        <div class="credit-note-modal-header">
            <h5>Save Transaction</h5>
            <button type="button" class="credit-note-close-btn" onclick="closeCreditNoteModal()">&times;</button>
        </div>
        <div class="credit-note-modal-body">
            <p>How would you like to save this transaction?</p>
            <div class="credit-note-options">
                <button type="button" class="btn btn-secondary" onclick="saveWithoutCreditNote()">
                    Save Without Credit Note
                </button>
                <button type="button" class="btn btn-primary" onclick="saveWithCreditNote()">
                    Save With Credit Note
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Adjustment Modal -->
<div id="adjustmentModal" class="adjustment-modal">
    <div class="adjustment-modal-content">
        <div class="adjustment-modal-header">
            <h5>Credit Note Adjustment</h5>
            <button type="button" class="adjustment-close-btn" onclick="closeAdjustmentModal()">&times;</button>
        </div>
        <div class="adjustment-modal-body">
            <div id="adjustmentContent">
                <!-- Dynamic content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Item and Batch Selection Modal Components -->
@include('components.modals.item-selection', [
    'id' => 'chooseItemsModal',
    'module' => 'breakage-expiry',
    'showStock' => true,
    'rateType' => 's_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'batchSelectionModal',
])

@include('components.modals.batch-selection', [
    'id' => 'batchSelectionModal',
    'module' => 'breakage-expiry',
    'showOnlyAvailable' => true,
    'rateType' => 's_rate',
    'showCostDetails' => false,
])

@endsection