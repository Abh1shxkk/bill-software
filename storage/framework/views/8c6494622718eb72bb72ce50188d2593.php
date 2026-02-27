<?php $__env->startSection('title', 'Claim to Supplier Transaction'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    input:focus { box-shadow: none !important; }
    .cts .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .cts .header-row { display: flex; align-items: center; gap: 15px; margin-bottom: 6px; }
    .cts .field-group { display: flex; align-items: center; gap: 6px; }
    .cts .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .cts .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #e9ecef; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    
    /* Modal Styles */
    .modal-backdrop-custom {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); z-index: 99998; opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-backdrop-custom.show { display: block; opacity: 1; }
    .item-modal {
        display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7);
        width: 90%; max-width: 800px; max-height: 90vh; background: white; border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); z-index: 99999; opacity: 0; transition: all 0.3s ease;
    }
    .item-modal.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .item-modal-header {
        padding: 15px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;
        border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;
    }
    .item-modal-header h5 { margin: 0; font-size: 16px; }
    .btn-close-custom { background: none; border: none; color: white; font-size: 24px; cursor: pointer; line-height: 1; }
    .item-modal-body { padding: 15px 20px; max-height: 60vh; overflow-y: auto; }
    .item-modal-footer { padding: 10px 20px; border-top: 1px solid #dee2e6; text-align: right; }
    
    /* Additional Details Modal */
    .additional-modal {
        display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7);
        width: 450px; max-height: 90vh; background: #f0f0f0; border: 2px solid #999;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); z-index: 99999; opacity: 0; transition: all 0.3s ease;
    }
    .additional-modal.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .additional-modal-body { padding: 15px 20px; }
    .additional-modal .field-row { display: flex; align-items: center; margin-bottom: 10px; }
    .additional-modal .field-row label { width: 180px; font-weight: 500; }
    .additional-modal .field-row input, .additional-modal .field-row select { border: 1px solid #999; padding: 3px 6px; font-size: 12px; }
    .additional-modal .field-row input[type="text"].small-input { width: 40px; text-align: center; }
    .additional-modal .field-row input[type="date"] { width: 120px; }
    .additional-modal .ok-btn { background: #e0e0e0; border: 2px outset #ccc; padding: 3px 20px; cursor: pointer; font-weight: 500; }
    .additional-modal .ok-btn:hover { background: #d0d0d0; }
    
    /* Rate Modal Styles */
    .rate-modal {
        display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7);
        width: 420px; background: #fff; border-radius: 6px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.3); z-index: 99999; opacity: 0; transition: all 0.25s ease;
    }
    .rate-modal.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .rate-modal-header {
        background: #dc3545; color: white; padding: 10px 16px;
        border-radius: 6px 6px 0 0; font-weight: 600; font-size: 13px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .rate-modal-body { padding: 18px 20px 14px; background: #fff8f8; border-radius: 0 0 6px 6px; }
    .rate-modal .field-row { display: flex; align-items: center; margin-bottom: 12px; gap: 12px; flex-wrap: wrap; }
    .rate-modal .field-row label { font-weight: 600; font-size: 12px; white-space: nowrap; min-width: 100px; }
    .rate-modal .field-row input { border: 1px solid #aaa; padding: 5px 8px; font-size: 12px; width: 110px; border-radius: 3px; }
    .rate-modal .field-row input.yellow-bg { background: #ffff99; }
    .rate-modal .rate-ok-btn {
        background: #0d6efd; color: white; border: none; padding: 5px 24px;
        font-size: 12px; font-weight: 600; border-radius: 3px; cursor: pointer;
    }
    .rate-modal .rate-ok-btn:hover { background: #0b5ed7; }
    
    /* Row Selection Highlight */
    .table-compact tbody tr { cursor: pointer; transition: all 0.2s ease; }
    .table-compact tbody tr:hover { background: #e3f2fd; }
    .table-compact tbody tr.selected-row { background: #bbdefb !important; border: 2px solid #1976d2 !important; }
    .table-compact tbody tr.selected-row td { border-color: #1976d2; }
    
    /* Load Invoices Modal */
    .invoices-modal {
        display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7);
        width: 90%; max-width: 900px; max-height: 90vh; background: white; border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); z-index: 99999; opacity: 0; transition: all 0.3s ease;
    }
    .invoices-modal.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .invoices-modal-header {
        padding: 15px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;
        border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;
    }
    .invoices-modal-header h5 { margin: 0; font-size: 16px; }
    .invoices-modal-body { padding: 15px 20px; max-height: 60vh; overflow-y: auto; }
    .invoices-modal-footer { padding: 10px 20px; border-top: 1px solid #dee2e6; text-align: right; }
    .invoices-table { width: 100%; font-size: 12px; }
    .invoices-table th { background: #f8f9fa; padding: 8px; text-align: left; border-bottom: 2px solid #dee2e6; }
    .invoices-table td { padding: 8px; border-bottom: 1px solid #dee2e6; }
    .invoices-table tr:hover { background: #e3f2fd; cursor: pointer; }
    .invoices-table tr.selected { background: #bbdefb !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="cts py-5">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil-square me-2"></i> Claim to Supplier Modification</h4>
                <div class="text-muted small">Edit existing claim to supplier transaction</div>
            </div>
            <div>
                <button type="button" class="btn btn-primary btn-sm" onclick="showLoadInvoicesModal()">
                    <i class="bi bi-folder2-open me-1"></i> Load Invoices
                </button>
                <a href="<?php echo e(route('admin.claim-to-supplier.transaction')); ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Transaction
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <form id="ctsForm" method="POST" autocomplete="off">
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="d-flex gap-3 mb-2">
                            <div style="width: 200px;">
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">Date:</label>
                                    <input type="date" id="claim_date" name="claim_date" class="form-control" style="width: 140px;" value="<?php echo e(date('Y-m-d')); ?>" required>
                                </div>
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">Day:</label>
                                    <input type="text" id="day_name" class="form-control readonly-field" style="width: 140px;" value="<?php echo e(date('l')); ?>" readonly>
                                </div>
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">T. No.:</label>
                                    <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" style="width: 140px;" readonly>
                                </div>
                            </div>

                            <div class="inner-card flex-grow-1 overflow-hidden">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label style="width: 100px;">Supplier :</label>
                                            <input type="hidden" id="supplier_id" name="supplier_id">
                                            <div style="position:relative; flex:1;" id="supplierWrapper">
                                                <input type="text" id="supplier_search" class="form-control no-select2"
                                                       placeholder="Search supplier..."
                                                       autocomplete="off"
                                                       oninput="_filterSupplierList()"
                                                       onclick="_openSupplierDrop()"
                                                       style="font-size:12px;">
                                                <div id="supplierDropList"
                                                     style="display:none; position:absolute; z-index:99999; top:100%; left:0;
                                                            width:250px; max-height:220px; overflow-y:auto;
                                                            background:white; border:1px solid #ccc;
                                                            box-shadow:0 4px 8px rgba(0,0,0,.15);">
                                                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="supplier-drop-item"
                                                         data-value="<?php echo e($supplier->supplier_id); ?>"
                                                         data-name="<?php echo e($supplier->name); ?>"
                                                         style="padding:5px 10px; cursor:pointer; font-size:12px;"
                                                         onmousedown="_selectSupplierItem(this)"><?php echo e($supplier->name); ?></div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="field-group">
                                            <label style="width: 80px;">Claim Date:</label>
                                            <input type="date" class="form-control" id="invoice_date" name="invoice_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 mt-1">
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label style="width: 100px;">Tax [Y/N]:</label>
                                            <input type="text" class="form-control" id="tax_flag" name="tax_flag" value="Y" maxlength="1" style="width: 50px;">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="field-group">
                                            <label style="width: 80px;">Narration:</label>
                                            <input type="text" class="form-control" id="narration" name="narration">
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <button type="button" class="btn btn-info btn-sm" onclick="showAdditionalDetailsModal()">
                                            <i class="bi bi-gear me-1"></i> Additional Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="bg-white border rounded p-2 mb-2">
                        <div class="table-responsive" style="overflow-y: auto; max-height: 310px;" id="itemsTableContainer">
                            <table class="table table-bordered table-compact">
                                <thead style="position: sticky; top: 0; background: #9999cc; color: #000; z-index: 10;">
                                    <tr>
                                        <th style="width: 80px;">Item Code</th>
                                        <th style="width: 200px;">Item Name</th>
                                        <th style="width: 80px;">Batch</th>
                                        <th style="width: 60px;">Exp.</th>
                                        <th style="width: 60px;">Qty.</th>
                                        <th style="width: 60px;">F.Qty</th>
                                        <th style="width: 80px;">Rate</th>
                                        <th style="width: 60px;">Dis.%</th>
                                        <th style="width: 90px;">Amount</th>
                                        <th style="width: 50px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody"></tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="showAddItemModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add Item
                            </button>
                        </div>
                    </div>

                    <!-- Calculation Section -->
                    <div class="bg-white border rounded p-2 mb-2" style="overflow: hidden;">
                        <div class="d-flex flex-wrap align-items-center gap-3" style="font-size: 11px;">
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>HSN</strong></label>
                                <input type="text" class="form-control readonly-field text-center" id="calc_hsn_code" readonly style="width: 80px; height: 26px; font-size: 11px;" value="">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>SC%</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_sc_percent" readonly style="width: 60px; height: 26px; font-size: 11px;" value="0.000">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>TAX%</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_tax_percent" readonly style="width: 60px; height: 26px; font-size: 11px;" value="0.000">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>CGST(%)</strong></label>
                                <input type="text" class="form-control readonly-field text-center" id="calc_cgst_percent" readonly style="width: 45px; height: 26px; font-size: 11px;" value="0">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>CGST Amt</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_cgst_amount" readonly style="width: 70px; height: 26px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>SGST(%)</strong></label>
                                <input type="text" class="form-control readonly-field text-center" id="calc_sgst_percent" readonly style="width: 45px; height: 26px; font-size: 11px;" value="0">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>SGST Amt</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_sgst_amount" readonly style="width: 70px; height: 26px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>W.S.RATE</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_ws_rate" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0"><strong>S.RATE</strong></label>
                                <input type="number" class="form-control readonly-field text-center" id="calc_s_rate" readonly style="width: 65px; height: 26px; font-size: 11px;" value="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Info Section (Pink Background) -->
                    <div class="border rounded p-2 mb-2" style="background: #f8c0f8; overflow: hidden;">
                        <div class="d-flex align-items-center gap-3" style="font-size: 11px;">
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="font-weight: bold;">Inv.No :</label>
                                <input type="text" class="form-control form-control-sm" id="ref_inv_no" name="ref_inv_no" style="width: 80px; height: 26px; font-size: 11px;" value="/0">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="mb-0" style="font-weight: bold;">Inv.Date :</label>
                                <input type="date" class="form-control form-control-sm" id="ref_inv_date" name="ref_inv_date" style="width: 120px; height: 26px; font-size: 11px;" value="<?php echo e(date('Y-m-d')); ?>">
                            </div>
                            <div class="d-flex align-items-center gap-1 flex-grow-1">
                                <label class="mb-0" style="font-weight: bold;">Customer :</label>
                                <input type="text" class="form-control form-control-sm" id="ref_customer_code" name="ref_customer_code" style="width: 80px; height: 26px; font-size: 11px;" placeholder="Code">
                                <input type="text" class="form-control form-control-sm flex-grow-1" id="ref_customer_name" name="ref_customer_name" style="height: 26px; font-size: 11px;" readonly placeholder="Customer Name">
                            </div>
                        </div>
                    </div>

                    <!-- Totals Row Section (Pink Background) -->
                    <div class="border rounded p-2 mb-2" style="background: #f8c0c0; overflow: hidden;">
                        <div class="d-flex align-items-center justify-content-between" style="font-size: 11px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">N.T AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_nt_amt" readonly style="width: 90px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SC</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_sc" readonly style="width: 80px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">DIS. AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_dis_amt" readonly style="width: 90px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SCM. AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_scm_amt" readonly style="width: 90px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Tax</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_tax" readonly style="width: 80px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">INV. AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="total_inv_amt" readonly style="width: 90px; height: 26px; font-size: 11px; background: #fff;" value="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="border rounded p-2 mb-2" style="background: #d4d4d4;">
                        <!-- Row 1: Packing, N.T Amt, Scm. Amt, Comp, Srlno -->
                        <div class="d-flex align-items-center" style="font-size: 11px; gap: 15px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold; width: 50px;">Packing</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="packing" readonly style="width: 60px; height: 24px; font-size: 11px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">N.T Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="nt_amount" id="ntAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Scm. Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="scm_amount" id="scmAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Comp :</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="companyName" readonly style="width: 100px; height: 24px; font-size: 11px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Srlno.</label>
                                <input type="text" class="form-control form-control-sm readonly-field text-center" id="srlNo" readonly style="width: 40px; height: 24px; font-size: 11px;">
                            </div>
                        </div>
                        <!-- Row 2: Unit, SC Amt, Net Amt, Lctn, SCM -->
                        <div class="d-flex align-items-center mt-1" style="font-size: 11px; gap: 15px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold; width: 50px;">Unit</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="unit" readonly style="width: 60px; height: 24px; font-size: 11px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SC Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="sc_amount" id="scAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Net Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="inv_amount" id="invAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Lctn :</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="locationField" readonly style="width: 60px; height: 24px; font-size: 11px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SCM.</label>
                                <input type="text" class="form-control form-control-sm readonly-field text-center" id="scmField" readonly style="width: 40px; height: 24px; font-size: 11px;" value="0">
                                <span>+</span>
                                <input type="text" class="form-control form-control-sm readonly-field text-center" id="scmField2" readonly style="width: 40px; height: 24px; font-size: 11px;" value="0">
                            </div>
                        </div>
                        <!-- Row 3: Cl. Qty, DIS. Amt, Tax Amt -->
                        <div class="d-flex align-items-center mt-1" style="font-size: 11px; gap: 15px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold; width: 50px;">Cl. Qty</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="clQty" readonly style="width: 60px; height: 24px; font-size: 11px;" value="0">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">DIS. Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="dis_amount" id="disAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Tax Amt.</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="tax_amount" id="taxAmount" readonly style="width: 80px; height: 24px; font-size: 11px;" value="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <button type="button" class="btn btn-success" id="saveBtn">Save (End)</button>
                            <button type="button" class="btn btn-danger" id="deleteItemBtn">Delete Item</button>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<!-- Item and Batch Selection Modal Components -->
<?php echo $__env->make('components.modals.item-selection', [
    'id' => 'claimToSupplierModItemModal',
    'module' => 'claim-to-supplier-mod',
    'showStock' => true,
    'rateType' => 'pur_rate',
    'showCompany' => true,
    'showHsn' => false,
    'batchModalId' => 'claimToSupplierModBatchModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id' => 'claimToSupplierModBatchModal',
    'module' => 'claim-to-supplier-mod',
    'showOnlyAvailable' => true,
    'rateType' => 'pur_rate',
    'showCostDetails' => true,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let rowIndex = 0;
let allItems = [];
let selectedItem = null;
let currentRowForRate = null; // Track which row needs rate modal

// Additional Details Modal Data
let additionalDetails = {
    blank_statement: 'Y',
    rate_type: 'R',
    from_date: '<?php echo e(date("Y-m-d")); ?>',
    to_date: '<?php echo e(date("Y-m-d")); ?>',
    company_code: '',
    company_name: '',
    division: '00'
};

// ============================================================================
// BRIDGE FUNCTIONS FOR REUSABLE MODAL COMPONENTS
// ============================================================================

/**
 * Bridge function called by reusable modal components after item and batch selection
 */
function onItemBatchSelectedFromModal(itemData, batchData) {
    if (!itemData || !itemData.id) return;
    
    const tbody = document.getElementById('itemsTableBody');
    const newRowIndex = rowIndex++;
    
    const row = document.createElement('tr');
    row.id = `row-${newRowIndex}`;
    row.setAttribute('data-row', newRowIndex);
    row.dataset.itemId = itemData.id;
    row.dataset.batchId = batchData?.id || '';
    
    const rate = batchData?.pur_rate || batchData?.cost || batchData?.avg_pur_rate || itemData.pur_rate || itemData.p_rate || 0;
    const qty  = 1;
    const amount = (qty * rate).toFixed(2);
    
    row.innerHTML = `
        <td><input type="text" class="form-control item-code" value="${itemData.id || ''}" readonly tabindex="-1"></td>
        <td><input type="text" class="form-control item-name" value="${itemData.name || ''}" readonly tabindex="-1"></td>
        <td><input type="text" class="form-control batch-no" data-row="${newRowIndex}" value="${batchData?.batch_no || ''}" readonly tabindex="-1"></td>
        <td><input type="text" class="form-control expiry" data-row="${newRowIndex}" value="${batchData?.expiry_display || batchData?.expiry || ''}" readonly tabindex="-1"></td>
        <td><input type="number" class="form-control qty" data-row="${newRowIndex}" value="${qty}" step="0.01" min="0"></td>
        <td><input type="number" class="form-control free-qty" data-row="${newRowIndex}" value="0" min="0"></td>
        <td><input type="number" class="form-control rate" data-row="${newRowIndex}" value="${rate}" step="0.01"></td>
        <td><input type="number" class="form-control dis-percent" data-row="${newRowIndex}" value="0" step="0.01"></td>
        <td><input type="number" class="form-control amount readonly-field" value="${amount}" step="0.01" readonly tabindex="-1"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${newRowIndex})" tabindex="-1"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="items[${newRowIndex}][item_id]" value="${itemData.id || ''}">
        <input type="hidden" name="items[${newRowIndex}][batch_id]" value="${batchData?.id || ''}">
        <input type="hidden" name="items[${newRowIndex}][hsn_code]" value="${itemData.hsn_code || ''}">
        <input type="hidden" name="items[${newRowIndex}][packing]" value="${itemData.packing || ''}">
        <input type="hidden" name="items[${newRowIndex}][unit]" value="${itemData.unit || '1'}">
        <input type="hidden" name="items[${newRowIndex}][company_name]" value="${itemData.company_name || ''}">
        <input type="hidden" name="items[${newRowIndex}][mrp]" value="${itemData.mrp || 0}">
        <input type="hidden" name="items[${newRowIndex}][s_rate]" value="${itemData.s_rate || 0}">
        <input type="hidden" name="items[${newRowIndex}][pur_rate]" value="${rate}">
    `;
    
    tbody.appendChild(row);
    
    const $row = $(`tr[data-row="${newRowIndex}"]`);
    $row.data('item_id', itemData.id);
    $row.data('rate_charged', 0);
    $row.data('actual_rate', 0);
    $row.data('item_data', {
        s_rate: parseFloat(itemData.s_rate) || 0, ws_rate: parseFloat(itemData.ws_rate) || 0,
        hsn_code: itemData.hsn_code || '', cgst_percent: parseFloat(itemData.cgst_percent) || 0,
        sgst_percent: parseFloat(itemData.sgst_percent) || 0, sc_percent: parseFloat(itemData.sc_percent) || 0,
        scm_percent: parseFloat(itemData.scm_percent) || 0, packing: itemData.packing || '',
        unit: itemData.unit || '', company_name: itemData.company_name || '', location: itemData.location || ''
    });
    
    $('#itemsTableBody tr').removeClass('selected-row');
    $row.addClass('selected-row');
    updateSelectedRowDetails($row);
    if (typeof calculateTotals === 'function') calculateTotals();
    
    // Batch+Expiry already filled → focus qty
    setTimeout(() => {
        const qtyField = row.querySelector('.qty');
        if (qtyField) { qtyField.focus(); qtyField.select(); }
    }, 100);
}

/**
 * Bridge function to open item selection modal
 */
function showAddItemModal() {
    console.log('🎯 Claim to Supplier Mod: showAddItemModal called');
    
    // Check if modal component function exists
    if (typeof window.openItemModal_claimToSupplierModItemModal === 'function') {
        console.log('✅ Claim to Supplier Mod: Opening reusable item modal');
        window.openItemModal_claimToSupplierModItemModal();
    } else {
        console.error('❌ Claim to Supplier Mod: openItemModal_claimToSupplierModItemModal function not found. Modal component may not be loaded.');
        alert('Error: Item selection modal not available. Please refresh the page.');
    }
}

// ============================================================================
// EXISTING FUNCTIONS
// ============================================================================

$(document).ready(function() {
    loadNextTransactionNumber();
    
    $('#claim_date').on('change', function() {
        const date = new Date($(this).val());
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $('#day_name').val(days[date.getDay()]);
    });
    
    // Customer code lookup
    $('#ref_customer_code').on('change', function() {
        const code = $(this).val();
        if (code) {
            $.get("<?php echo e(url('admin/customers')); ?>/" + code, function(response) {
                if (response && response.name) {
                    $('#ref_customer_name').val(response.name);
                } else {
                    $('#ref_customer_name').val('');
                }
            }).fail(function() { 
                $('#ref_customer_name').val(''); 
            });
        } else {
            $('#ref_customer_name').val('');
        }
    });
    
    $('#saveBtn').on('click', saveTransaction);
});

function loadNextTransactionNumber() {
    $.get("<?php echo e(route('admin.claim-to-supplier.next-trn-no')); ?>", function(response) {
        $('#trn_no').val(response.next_trn_no);
    });
}

// ==================== ADDITIONAL DETAILS MODAL ====================
function showAdditionalDetailsModal() {
    const isBlankY = additionalDetails.blank_statement === 'Y';
    const disabledAttr = isBlankY ? 'disabled' : '';
    const disabledStyle = isBlankY ? 'background: #ccc; cursor: not-allowed;' : '';
    
    const modalHTML = `
        <div class="modal-backdrop-custom" id="additionalModalBackdrop" onclick="closeAdditionalDetailsModal()"></div>
        <div class="additional-modal" id="additionalModal">
            <div class="additional-modal-body">
                <div class="field-row">
                    <label>Blank statement [ Y/N ] :</label>
                    <input type="text" id="add_blank_statement" class="small-input" value="${additionalDetails.blank_statement}" maxlength="1" style="background: #ffff00;" onkeyup="toggleAdditionalFields()">
                </div>
                <div class="field-row">
                    <label>From P / S / R :</label>
                    <input type="text" id="add_rate_type" class="small-input additional-field" value="${additionalDetails.rate_type}" maxlength="1" ${disabledAttr} style="${disabledStyle}">
                </div>
                <div class="field-row">
                    <label>From :</label>
                    <input type="date" id="add_from_date" class="additional-field" value="${additionalDetails.from_date}" ${disabledAttr} style="${disabledStyle}">
                    <label style="width: auto; margin-left: 15px; margin-right: 5px;">To :</label>
                    <input type="date" id="add_to_date" class="additional-field" value="${additionalDetails.to_date}" ${disabledAttr} style="${disabledStyle}">
                </div>
                <div class="field-row">
                    <label>Company :</label>
                    <input type="text" id="add_company_code" class="additional-field" value="${additionalDetails.company_code}" style="width: 80px; ${disabledStyle}" ${disabledAttr} placeholder="Code">
                    <input type="text" id="add_company_name" value="${additionalDetails.company_name}" readonly style="flex: 1; margin-left: 5px; background: #e9ecef;">
                </div>
                <div class="field-row">
                    <label>Division :</label>
                    <input type="text" id="add_division" class="additional-field" value="${additionalDetails.division}" style="width: 60px; ${disabledStyle}" ${disabledAttr}>
                </div>
                <div class="field-row" style="justify-content: flex-end; margin-top: 15px;">
                    <button type="button" class="ok-btn" onclick="saveAdditionalDetails()">Ok</button>
                </div>
            </div>
        </div>
    `;
    
    $('#additionalModal, #additionalModalBackdrop').remove();
    $('body').append(modalHTML);
    
    // Company code lookup
    $('#add_company_code').on('change', function() {
        const code = $(this).val();
        if (code) {
            $.get("<?php echo e(url('admin/companies/by-code')); ?>/" + code, function(response) {
                if (response.success) {
                    $('#add_company_name').val(response.company.name);
                } else {
                    $('#add_company_name').val('');
                }
            }).fail(function() { $('#add_company_name').val(''); });
        }
    });
    
    setTimeout(() => {
        $('#additionalModalBackdrop, #additionalModal').addClass('show');
        window.removeEventListener('keydown', _handleAdditionalModalKey, true);
        window.addEventListener('keydown', _handleAdditionalModalKey, true);
        setTimeout(() => { document.getElementById('add_blank_statement')?.focus(); document.getElementById('add_blank_statement')?.select(); }, 80);
    }, 10);
}

function toggleAdditionalFields() {
    const blankValue = $('#add_blank_statement').val().toUpperCase();
    const isBlankY = blankValue === 'Y';
    
    if (isBlankY) {
        $('.additional-field').prop('disabled', true).css({ 'background': '#ccc', 'cursor': 'not-allowed' });
        $('#add_rate_type').val('R');
        $('#add_from_date').val('<?php echo e(date("Y-m-d")); ?>');
        $('#add_to_date').val('<?php echo e(date("Y-m-d")); ?>');
        $('#add_company_code').val('');
        $('#add_company_name').val('');
        $('#add_division').val('00');
    } else {
        $('.additional-field').prop('disabled', false).css({ 'background': '#fff', 'cursor': 'text' });
    }
}

function closeAdditionalDetailsModal() {
    window.removeEventListener('keydown', _handleAdditionalModalKey, true);
    $('#additionalModalBackdrop, #additionalModal').removeClass('show');
    setTimeout(() => { $('#additionalModal, #additionalModalBackdrop').remove(); }, 300);
}

function saveAdditionalDetails() {
    additionalDetails.blank_statement = $('#add_blank_statement').val().toUpperCase();
    additionalDetails.rate_type = $('#add_rate_type').val().toUpperCase();
    additionalDetails.from_date = $('#add_from_date').val();
    additionalDetails.to_date = $('#add_to_date').val();
    additionalDetails.company_code = $('#add_company_code').val();
    additionalDetails.company_name = $('#add_company_name').val();
    additionalDetails.division = $('#add_division').val();
    closeAdditionalDetailsModal();
    setTimeout(() => showAddItemModal(), 350);
}


// ==================== LEGACY ITEM SELECTION MODAL ====================
function _legacy_showAddItemModal() {
    const supplierId = $('#supplier_id').val();
    if (!supplierId) {
        alert('Please select a supplier first');
        return;
    }
    
    let params = { supplier_id: supplierId };
    if (additionalDetails.company_code) params.company_code = additionalDetails.company_code;
    if (additionalDetails.division && additionalDetails.division !== '00') params.division = additionalDetails.division;
    
    $.get("<?php echo e(route('admin.items.get-all')); ?>", params, function(data) {
        allItems = data.items || data;
        _legacy_showItemSelectionModal(allItems);
    }).fail(function() {
        alert('Failed to load items');
    });
}

function _legacy_showItemSelectionModal(items) {
    let itemsHtml = items.map((item, index) => {
        // Prepare item data with all required fields
        const itemData = {
            id: item.id,
            code: item.code || item.id,
            name: item.name || '',
            packing: item.packing || '',
            unit: item.unit || '',
            company_name: item.company_short_name || item.company_name || '',
            s_rate: item.s_rate || 0,
            ws_rate: item.ws_rate || 0,
            mrp: item.mrp || 0,
            pur_rate: item.pur_rate || 0,
            hsn_code: item.hsn_code || '',
            cgst_percent: item.cgst_percent || 0,
            sgst_percent: item.sgst_percent || 0,
            sc_percent: item.sc_percent || 0,
            scm_percent: item.scm_percent || 0,
            location: item.location || ''
        };
        return `
        <tr class="item-row" data-item-name="${(item.name || '').toLowerCase()}" data-item-code="${String(item.id).toLowerCase()}">
            <td style="text-align: center;">${index + 1}</td>
            <td>${item.id || ''}</td>
            <td>${item.name || ''}</td>
            <td>${item.company_short_name || ''}</td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-sm btn-primary" onclick='_legacy_selectItem(${JSON.stringify(itemData).replace(/'/g, "\\'")})'
                    style="font-size: 9px; padding: 2px 8px;">Select</button>
            </td>
        </tr>
    `}).join('');
    
    const modalHTML = `
        <div class="modal-backdrop-custom" id="itemModalBackdrop" onclick="_legacy_closeItemModal()"></div>
        <div class="item-modal" id="itemModal">
            <div class="item-modal-header">
                <h5><i class="bi bi-box-seam me-2"></i>Select Item</h5>
                <button type="button" class="btn-close-custom" onclick="_legacy_closeItemModal()">&times;</button>
            </div>
            <div class="item-modal-body">
                <div style="margin-bottom: 10px;">
                    <input type="text" id="itemSearchInput" class="form-control form-control-sm" 
                           placeholder="Search by item name or code..." onkeyup="_legacy_filterItems()" style="font-size: 11px;">
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 10px; margin-bottom: 0;">
                        <thead style="position: sticky; top: 0; background: #0d6efd; color: white; z-index: 10;">
                            <tr>
                                <th style="width: 35px; text-align: center;">S.N</th>
                                <th style="width: 80px;">Code</th>
                                <th style="width: 200px;">Item Name</th>
                                <th style="width: 120px;">Company</th>
                                <th style="width: 80px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>
                </div>
            </div>
            <div class="item-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="_legacy_closeItemModal()">Close</button>
            </div>
        </div>
    `;
    
    $('#itemModal, #itemModalBackdrop').remove();
    $('body').append(modalHTML);
    setTimeout(() => { $('#itemModalBackdrop, #itemModal').addClass('show'); $('#itemSearchInput').focus(); }, 10);
}

function _legacy_filterItems() {
    const searchValue = $('#itemSearchInput').val().toLowerCase();
    $('.item-row').each(function() {
        const name = $(this).data('item-name');
        const code = $(this).data('item-code');
        $(this).toggle(name.includes(searchValue) || code.includes(searchValue));
    });
}

function _legacy_closeItemModal() {
    $('#itemModalBackdrop, #itemModal').removeClass('show');
    setTimeout(() => { $('#itemModal, #itemModalBackdrop').remove(); }, 300);
}

// ==================== LEGACY SELECT ITEM & ADD ROW (NO BATCH MODAL) ====================
function _legacy_selectItem(item) {
    if (typeof item === 'string') item = JSON.parse(item);
    selectedItem = item;
    _legacy_closeItemModal();
    
    // Directly add row without batch modal - user will enter batch manually
    _legacy_addItemRowManual(item);
}

function _legacy_addItemRowManual(item) {
    const row = `
        <tr data-row="${rowIndex}">
            <td><input type="text" class="form-control item-code" data-row="${rowIndex}" value="${item.code || ''}" readonly tabindex="-1"></td>
            <td><input type="text" class="form-control item-name" data-row="${rowIndex}" value="${item.name || ''}" readonly tabindex="-1"></td>
            <td><input type="text" class="form-control batch-no" data-row="${rowIndex}" value="" placeholder="Enter batch"></td>
            <td><input type="text" class="form-control expiry" data-row="${rowIndex}" value="" placeholder="MM/YY"></td>
            <td><input type="number" class="form-control qty" data-row="${rowIndex}" value="0" min="0"></td>
            <td><input type="number" class="form-control free-qty" data-row="${rowIndex}" value="0" min="0"></td>
            <td><input type="number" class="form-control rate" data-row="${rowIndex}" value="0" step="0.01"></td>
            <td><input type="number" class="form-control dis-percent" data-row="${rowIndex}" value="0" step="0.01"></td>
            <td><input type="number" class="form-control amount" data-row="${rowIndex}" value="0" step="0.01" readonly tabindex="-1"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
    $('#itemsTableBody').append(row);
    
    const $row = $(`tr[data-row="${rowIndex}"]`);
    $row.data('item_id', item.id);
    $row.data('rate_charged', 0);
    $row.data('actual_rate', 0);
    // Store item data for later use
    $row.data('item_data', {
        s_rate: item.s_rate || 0,
        ws_rate: item.ws_rate || 0,
        hsn_code: item.hsn_code || '',
        cgst_percent: item.cgst_percent || 0,
        sgst_percent: item.sgst_percent || 0,
        sc_percent: item.sc_percent || 0,
        scm_percent: item.scm_percent || 0,
        packing: item.packing || '',
        unit: item.unit || '',
        company_name: item.company_name || '',
        location: item.location || ''
    });
    
    // Select this row and update all sections
    $('#itemsTableBody tr').removeClass('selected-row');
    $row.addClass('selected-row');
    
    // Update Calculation Section and Summary Section with this row's data
    updateSelectedRowDetails($row);
    
    // Update totals
    calculateTotals();
    
    // Focus on batch field
    setTimeout(() => {
        $row.find('.batch-no').focus();
    }, 100);
    
    rowIndex++;
}

// Update calculation fields when row is focused
function updateCalcFieldsFromRow($row) {
    const itemData = $row.data('item_data') || {};
    
    $('#calc_hsn_code').val(itemData.hsn_code || '');
    $('#calc_s_rate').val(parseFloat(itemData.s_rate || 0).toFixed(2));
    $('#calc_ws_rate').val(parseFloat(itemData.ws_rate || 0).toFixed(2));
    $('#calc_cgst_percent').val(itemData.cgst_percent || 0);
    $('#calc_sgst_percent').val(itemData.sgst_percent || 0);
    $('#packing').val(itemData.packing || '');
    $('#unit').val(itemData.unit || '');
    $('#companyName').val(itemData.company_name || '');
}

// Update calc fields when any input in row is focused - also select the row
$(document).on('focus', '#itemsTableBody tr input', function() {
    const $row = $(this).closest('tr');
    
    // Select this row
    $('#itemsTableBody tr').removeClass('selected-row');
    $row.addClass('selected-row');
    
    // Update all sections with this row's data
    updateSelectedRowDetails($row);
});

// Table row Enter-key navigation handled in master window capture handler below

// ==================== RATE CHARGED / ACTUAL RATE MODAL ====================
function _handleRateModalKey(e) {
    const modal = document.getElementById('rateModal');
    if (!modal || !modal.classList.contains('show')) return;
    if (!['Enter', 'Escape'].includes(e.key)) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
    if (e.key === 'Escape') { closeRateModal(); return; }
    if (e.key === 'Enter') {
        const el = document.activeElement;
        if (el?.id === 'rate_charged') {
            const ar = document.getElementById('actual_rate');
            if (ar) { ar.focus(); ar.select(); }
        } else { saveRateModal(); }
    }
}

function showRateModal(rowIdx) {
    const $row = $(`tr[data-row="${rowIdx}"]`);
    const rateCharged = $row.data('rate_charged') || 0;
    const actualRate  = $row.data('actual_rate')  || 0;
    
    const modalHTML = `
        <div class="modal-backdrop-custom show" id="rateModalBackdrop" onclick="closeRateModal()"></div>
        <div class="rate-modal show" id="rateModal">
            <div class="rate-modal-header">
                <span>&#9998; Rate Details</span>
                <button type="button" onclick="closeRateModal()" style="background:none;border:none;color:white;font-size:18px;cursor:pointer;line-height:1;">&times;</button>
            </div>
            <div class="rate-modal-body">
                <div class="field-row">
                    <label>Rate Charged :</label>
                    <input type="number" id="rate_charged" class="yellow-bg" value="${rateCharged}" step="0.01">
                    <label>Actual Rate :</label>
                    <input type="number" id="actual_rate" value="${actualRate}" step="0.01">
                </div>
                <div class="field-row" style="justify-content: flex-end; margin-top: 5px;">
                    <button type="button" class="rate-ok-btn" onclick="saveRateModal()">Ok</button>
                </div>
            </div>
        </div>
    `;
    
    $('#rateModal, #rateModalBackdrop').remove();
    $('body').append(modalHTML);
    setTimeout(() => {
        document.getElementById('rate_charged')?.focus();
        document.getElementById('rate_charged')?.select();
        window.removeEventListener('keydown', _handleRateModalKey, true);
        window.addEventListener('keydown', _handleRateModalKey, true);
    }, 30);
}

function saveRateModal() {
    // ✅ FIX: use parseFloat without || 0 — if field is blank, fall back to existing stored value
    const rateChargedInput = parseFloat($('#rate_charged').val());
    const actualRateInput  = parseFloat($('#actual_rate').val());
    if (currentRowForRate !== null) {
        const $row = $(`tr[data-row="${currentRowForRate}"]`);
        const rateCharged = isNaN(rateChargedInput) ? ($row.data('rate_charged') || 0) : rateChargedInput;
        const actualRate  = isNaN(actualRateInput)  ? ($row.data('actual_rate')  || 0) : actualRateInput;
        $row.data('rate_charged', rateCharged);
        $row.data('actual_rate',  actualRate);
        $row.find('.rate').val(rateCharged.toFixed(2));
        calculateRowAmount($row);
        calculateTotals();
    }
    const rowToClose = currentRowForRate; // save before nulling
    currentRowForRate = null;
    closeRateModal(rowToClose);
}

function closeRateModal(rowToFocus) {
    window.removeEventListener('keydown', _handleRateModalKey, true);
    // If called from saveRateModal, rowToFocus is passed in directly.
    // If called from Escape/X, currentRowForRate is still set — use it.
    if (rowToFocus === undefined) rowToFocus = currentRowForRate;
    currentRowForRate = null;
    $('#rateModal, #rateModalBackdrop').remove();
    if (rowToFocus !== null && rowToFocus !== undefined) {
        setTimeout(() => {
            const $r = $(`tr[data-row="${rowToFocus}"]`);
            if ($r.length) { $r.find('.dis-percent').focus().select(); }
        }, 50);
    }
}


// ==================== ROW CALCULATIONS ====================
function removeRow(row) {
    $(`tr[data-row="${row}"]`).remove();
    calculateTotals();
}

$(document).on('change', '.qty, .free-qty, .rate, .dis-percent', function() {
    const $row = $(this).closest('tr');
    calculateRowAmount($row);
    calculateTotals();
});

// Row selection highlight and update selected row details
$(document).on('click', '#itemsTableBody tr', function(e) {
    // Don't select if clicking on input or button
    if ($(e.target).is('input, button, i')) return;
    
    $('#itemsTableBody tr').removeClass('selected-row');
    $(this).addClass('selected-row');
    
    // Update Calculation Section and Summary Section with selected row data
    updateSelectedRowDetails($(this));
});

// Update Calculation Section and Summary Section with selected row's item details
function updateSelectedRowDetails($row) {
    const itemId = $row.data('item_id');
    
    // If no item_id, just update with empty/zero values
    if (!itemId) {
        updateSectionsWithItemData($row, {});
        return;
    }
    
    // Fetch item data from Items table via API
    $.ajax({
        url: "<?php echo e(url('admin/items/search')); ?>",
        method: 'GET',
        data: { code: itemId },
        success: function(response) {
            if (response.success && response.items && response.items.length > 0) {
                const item = response.items[0];
                const itemData = {
                    hsn_code: item.hsn_code || '',
                    cgst_percent: parseFloat(item.cgst_percent) || 0,
                    sgst_percent: parseFloat(item.sgst_percent) || 0,
                    packing: item.packing || '',
                    unit: item.unit || '',
                    company_name: item.company || item.mfg_by || '',
                    location: item.location || '',
                    ws_rate: parseFloat(item.ws_rate) || 0,
                    s_rate: parseFloat(item.s_rate) || 0,
                    sc_percent: 0,
                    scm_percent: 0
                };
                // Store for future use
                $row.data('item_data', itemData);
                updateSectionsWithItemData($row, itemData);
            } else {
                updateSectionsWithItemData($row, $row.data('item_data') || {});
            }
        },
        error: function() {
            updateSectionsWithItemData($row, $row.data('item_data') || {});
        }
    });
}

// Helper function to update sections with item data
function updateSectionsWithItemData($row, itemData) {
    const qty = parseFloat($row.find('.qty').val()) || 0;
    const rate = parseFloat($row.find('.rate').val()) || 0;
    const disPercent = parseFloat($row.find('.dis-percent').val()) || 0;
    const amount = parseFloat($row.find('.amount').val()) || 0; // Amount = Qty × Rate
    
    const cgstPercent = parseFloat(itemData.cgst_percent) || 0;
    const sgstPercent = parseFloat(itemData.sgst_percent) || 0;
    const scPercent = parseFloat(itemData.sc_percent) || 0;
    const scmPercent = parseFloat(itemData.scm_percent) || 0;
    const taxPercent = cgstPercent + sgstPercent;
    
    // Calculate for this single row
    // N.T Amt = Amount (Qty × Rate)
    const ntAmt = amount;
    
    // Discount Amount = N.T Amt × Dis%
    const disAmount = ntAmt * disPercent / 100;
    
    // SC Amount = N.T Amt × SC%
    const scAmount = ntAmt * scPercent / 100;
    
    // SCM Amount = N.T Amt × SCM%
    const scmAmount = ntAmt * scmPercent / 100;
    
    // Net Amount = N.T Amt - DIS Amt
    const netAmount = ntAmt - disAmount;
    
    // Tax calculated on net amount
    const cgstAmount = netAmount * cgstPercent / 100;
    const sgstAmount = netAmount * sgstPercent / 100;
    const taxAmount = cgstAmount + sgstAmount;
    
    // Update Calculation Section (Item-based details)
    $('#calc_hsn_code').val(itemData.hsn_code || '');
    $('#calc_sc_percent').val(scPercent.toFixed(3));
    $('#calc_tax_percent').val(taxPercent.toFixed(3));
    $('#calc_cgst_percent').val(cgstPercent);
    $('#calc_cgst_amount').val(cgstAmount.toFixed(2));
    $('#calc_sgst_percent').val(sgstPercent);
    $('#calc_sgst_amount').val(sgstAmount.toFixed(2));
    $('#calc_ws_rate').val(parseFloat(itemData.ws_rate || 0).toFixed(2));
    $('#calc_s_rate').val(parseFloat(itemData.s_rate || 0).toFixed(2));
    
    // Update Summary Section (Item-based details)
    $('#packing').val(itemData.packing || '');
    $('#unit').val(itemData.unit || '');
    $('#companyName').val(itemData.company_name || '');
    $('#srlNo').val($row.data('row') !== undefined ? ($row.data('row') + 1) : '');
    $('#locationField').val(itemData.location || '');
    
    // Item-level amounts in Summary
    $('#ntAmount').val(ntAmt.toFixed(2));
    $('#scAmount').val(scAmount.toFixed(2));
    $('#scmAmount').val(scmAmount.toFixed(2));
    $('#disAmount').val(disAmount.toFixed(2));
    $('#taxAmount').val(taxAmount.toFixed(2));
    $('#invAmount').val(netAmount.toFixed(2));
    $('#clQty').val(qty);
}

function calculateRowAmount($row) {
    const qty = parseFloat($row.find('.qty').val()) || 0;
    const rate = parseFloat($row.find('.rate').val()) || 0;
    
    // Amount = Qty × Rate ONLY (no discount applied here)
    const amount = qty * rate;
    $row.find('.amount').val(amount.toFixed(2));
    
    // If this row is selected, update calculation and summary sections
    if ($row.hasClass('selected-row')) {
        updateSelectedRowDetails($row);
    }
}

// Calculate totals for all items (Totals Row Section - Pink)
function calculateTotals() {
    let totalNtAmount = 0;
    let totalCgstAmount = 0;
    let totalSgstAmount = 0;
    let totalDisAmount = 0;
    let totalScAmount = 0;
    let totalScmAmount = 0;
    let totalQty = 0;
    
    $('#itemsTableBody tr').each(function() {
        const $row = $(this);
        const qty = parseFloat($row.find('.qty').val()) || 0;
        const rate = parseFloat($row.find('.rate').val()) || 0;
        const disPercent = parseFloat($row.find('.dis-percent').val()) || 0;
        const amount = parseFloat($row.find('.amount').val()) || 0; // Amount = Qty × Rate
        const itemData = $row.data('item_data') || {};
        const cgstPercent = parseFloat(itemData.cgst_percent) || 0;
        const sgstPercent = parseFloat(itemData.sgst_percent) || 0;
        const scPercent = parseFloat(itemData.sc_percent) || 0;
        const scmPercent = parseFloat(itemData.scm_percent) || 0;
        
        // N.T Amount = Sum of all Amount (Qty × Rate)
        totalNtAmount += amount;
        
        // Discount Amount = Amount × Dis%
        const disAmount = amount * disPercent / 100;
        totalDisAmount += disAmount;
        
        // SC Amount = Amount × SC%
        const scAmount = amount * scPercent / 100;
        totalScAmount += scAmount;
        
        // SCM Amount = Amount × SCM%
        const scmAmount = amount * scmPercent / 100;
        totalScmAmount += scmAmount;
        
        // Tax calculated on amount after discount
        const amountAfterDiscount = amount - disAmount;
        totalCgstAmount += amountAfterDiscount * cgstPercent / 100;
        totalSgstAmount += amountAfterDiscount * sgstPercent / 100;
        totalQty += qty;
    });
    
    const totalTaxAmount = totalCgstAmount + totalSgstAmount;
    const totalInvAmount = (totalNtAmount - totalDisAmount) + totalTaxAmount;
    
    // Update Totals Row Section (Pink) - All items sum
    $('#total_nt_amt').val(totalNtAmount.toFixed(2));
    $('#total_sc').val(totalScAmount.toFixed(2));
    $('#total_dis_amt').val(totalDisAmount.toFixed(2));
    $('#total_scm_amt').val(totalScmAmount.toFixed(2));
    $('#total_tax').val(totalTaxAmount.toFixed(2));
    $('#total_inv_amt').val(totalInvAmount.toFixed(2));
    
    // If a row is selected, update its details, otherwise show totals in summary
    const $selectedRow = $('#itemsTableBody tr.selected-row');
    if ($selectedRow.length > 0) {
        updateSelectedRowDetails($selectedRow);
    }
}

// ==================== SAVE TRANSACTION ====================
function saveTransaction() {
    const supplierId = $('#supplier_id').val();
    if (!supplierId) {
        alert('Please select a supplier');
        return;
    }
    
    const items = [];
    $('#itemsTableBody tr').each(function() {
        const $row = $(this);
        const qty = parseFloat($row.find('.qty').val()) || 0;
        if (qty > 0) {
            items.push({
                item_id: $row.data('item_id'),
                item_code: $row.find('.item-code').val(),
                item_name: $row.find('.item-name').val(),
                batch_no: $row.find('.batch-no').val(),
                expiry: $row.find('.expiry').val(),
                qty: qty,
                free_qty: parseFloat($row.find('.free-qty').val()) || 0,
                pur_rate: parseFloat($row.find('.rate').val()) || 0,
                dis_percent: parseFloat($row.find('.dis-percent').val()) || 0,
                ft_amount: parseFloat($row.find('.amount').val()) || 0,
                rate_charged: $row.data('rate_charged') || 0,
                actual_rate: $row.data('actual_rate') || 0,
                cgst_percent: parseFloat($('#calc_cgst_percent').val()) || 0,
                sgst_percent: parseFloat($('#calc_sgst_percent').val()) || 0,
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item');
        return;
    }
    
    const data = {
        _token: '<?php echo e(csrf_token()); ?>',
        claim_date: $('#claim_date').val(),
        supplier_id: supplierId,
        supplier_name: $('#supplier_id option:selected').text(),
        invoice_no: $('#invoice_no').val(),
        invoice_date: $('#invoice_date').val(),
        tax_flag: $('#tax_flag').val(),
        narration: $('#narration').val(),
        nt_amount: parseFloat($('#ntAmount').val()) || 0,
        tax_amount: parseFloat($('#taxAmount').val()) || 0,
        net_amount: parseFloat($('#invAmount').val()) || 0,
        // Additional Details
        blank_statement: additionalDetails.blank_statement,
        rate_type: additionalDetails.rate_type,
        filter_from_date: additionalDetails.from_date,
        filter_to_date: additionalDetails.to_date,
        company_code: additionalDetails.company_code,
        division: additionalDetails.division,
        // Reference Invoice Info
        ref_inv_no: $('#ref_inv_no').val(),
        ref_inv_date: $('#ref_inv_date').val(),
        ref_customer_code: $('#ref_customer_code').val(),
        ref_customer_name: $('#ref_customer_name').val(),
        items: items
    };
    
    $.ajax({
        url: "<?php echo e(route('admin.claim-to-supplier.store')); ?>",
        method: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                alert('Claim saved successfully! Claim No: ' + response.claim_no);
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error saving claim: ' + (xhr.responseJSON?.message || 'Unknown error'));
        }
    });
}

// ==================== LOAD INVOICES MODAL ====================
let currentClaimId = null;

function showLoadInvoicesModal() {
    const modalHTML = `
        <div class="modal-backdrop-custom" id="invoicesModalBackdrop" onclick="closeLoadInvoicesModal()"></div>
        <div class="invoices-modal" id="invoicesModal">
            <div class="invoices-modal-header">
                <h5><i class="bi bi-folder2-open me-2"></i> Load Past Invoices</h5>
                <button type="button" class="btn-close-custom" onclick="closeLoadInvoicesModal()">&times;</button>
            </div>
            <div class="invoices-modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Date</label>
                        <input type="date" class="form-control form-control-sm" id="invoiceFilterDate" onchange="loadPastInvoices()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filter by Supplier</label>
                        <select class="form-control form-control-sm" id="invoiceFilterSupplier" onchange="loadPastInvoices()">
                            <option value="">All Suppliers</option>
                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($supplier->supplier_id); ?>"><?php echo e($supplier->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="loadPastInvoices()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
                <div id="invoicesTableContainer">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">Loading invoices...</div>
                    </div>
                </div>
            </div>
            <div class="invoices-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeLoadInvoicesModal()">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="loadSelectedInvoiceBtn" onclick="loadSelectedInvoice()" disabled>
                    <i class="bi bi-check-circle me-1"></i> Load Selected
                </button>
            </div>
        </div>
    `;
    
    $('#invoicesModal, #invoicesModalBackdrop').remove();
    $('body').append(modalHTML);
    
    setTimeout(() => {
        $('#invoicesModalBackdrop, #invoicesModal').addClass('show');
        loadPastInvoices();
        window.removeEventListener('keydown', _handleInvoicesModalKey, true);
        window.addEventListener('keydown', _handleInvoicesModalKey, true);
    }, 10);
}

function closeLoadInvoicesModal() {
    window.removeEventListener('keydown', _handleInvoicesModalKey, true);
    $('#invoicesModalBackdrop, #invoicesModal').removeClass('show');
    setTimeout(() => { $('#invoicesModal, #invoicesModalBackdrop').remove(); }, 300);
}

function loadPastInvoices() {
    const date = $('#invoiceFilterDate').val();
    const supplierId = $('#invoiceFilterSupplier').val();
    
    $('#invoicesTableContainer').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2">Loading invoices...</div>
        </div>
    `);
    
    $.get("<?php echo e(route('admin.claim-to-supplier.past-claims')); ?>", { date: date, supplier_id: supplierId }, function(response) {
        if (response.success && response.transactions.length > 0) {
            let tableHTML = `
                <table class="invoices-table">
                    <thead>
                        <tr>
                            <th>Claim No.</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            response.transactions.forEach(function(trn) {
                tableHTML += `
                    <tr data-id="${trn.id}" onclick="selectInvoice(this, ${trn.id})">
                        <td><strong>${trn.claim_no}</strong></td>
                        <td>${trn.claim_date}</td>
                        <td>${trn.supplier_name}</td>
                        <td class="text-end">₹${trn.amount}</td>
                        <td><span class="badge bg-${trn.status === 'active' ? 'success' : 'danger'}">${trn.status}</span></td>
                    </tr>
                `;
            });
            
            tableHTML += '</tbody></table>';
            $('#invoicesTableContainer').html(tableHTML);
        } else {
            $('#invoicesTableContainer').html('<div class="text-center text-muted py-4">No invoices found</div>');
        }
    }).fail(function() {
        $('#invoicesTableContainer').html('<div class="text-center text-danger py-4">Error loading invoices</div>');
    });
}

function selectInvoice(row, id) {
    $('.invoices-table tr').removeClass('selected');
    $(row).addClass('selected');
    currentClaimId = id;
    $('#loadSelectedInvoiceBtn').prop('disabled', false);
}

function loadSelectedInvoice() {
    if (!currentClaimId) return;
    
    $('#loadSelectedInvoiceBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Loading...');
    
    $.get("<?php echo e(url('admin/claim-to-supplier/details')); ?>/" + currentClaimId, function(response) {
        if (response.success) {
            closeLoadInvoicesModal();
            populateFormWithData(response.header, response.items);
        } else {
            alert('Error loading invoice: ' + response.message);
            $('#loadSelectedInvoiceBtn').prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Load Selected');
        }
    }).fail(function() {
        alert('Error loading invoice details');
        $('#loadSelectedInvoiceBtn').prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Load Selected');
    });
}

function populateFormWithData(header, items) {
    // Store the claim ID for update
    currentClaimId = header.id;
    
    // Populate header fields
    $('#trn_no').val(header.claim_no);
    $('#claim_date').val(header.claim_date);
    if (header.claim_date) {
        const date = new Date(header.claim_date);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $('#day_name').val(days[date.getDay()]);
    }
    // Set supplier in custom dropdown
    $('#supplier_id').val(header.supplier_id);
    const $supplierOpt = $(`.supplier-drop-item[data-value="${header.supplier_id}"]`);
    $('#supplier_search').val($supplierOpt.length ? $supplierOpt.data('name') : (header.supplier_name || ''));
    $('#invoice_date').val(header.invoice_date);
    $('#tax_flag').val(header.tax_flag || 'Y');
    $('#narration').val(header.narration || '');
    
    // Clear existing items
    $('#itemsTableBody').empty();
    rowIndex = 0;
    
    // Add items
    items.forEach(function(item) {
        addItemRowFromData(item);
    });
    
    // Update totals
    calculateTotals();
    
    // Select first row, update calculation/summary sections, and focus batch-no (with delay for DOM)
    setTimeout(function() {
        if (items.length > 0) {
            const $firstRow = $(`tr[data-row="0"]`);
            if ($firstRow.length) {
                $('#itemsTableBody tr').removeClass('selected-row');
                $firstRow.addClass('selected-row');
                
                // Debug: log item_data
                console.log('First row item_data:', $firstRow.data('item_data'));
                
                updateSelectedRowDetails($firstRow);

                // ✅ FIX: shift cursor to batch-no of first row after invoice loads
                const batchField = $firstRow.find('.batch-no')[0];
                if (batchField) { batchField.focus(); batchField.select(); }
            }
        }
    }, 150);
    
    // Change save button to update
    $('#saveBtn').text('Update').off('click').on('click', updateTransaction);
}

function addItemRowFromData(item) {
    const row = `
        <tr data-row="${rowIndex}">
            <td><input type="text" class="form-control item-code" data-row="${rowIndex}" value="${item.item_code || ''}" readonly tabindex="-1"></td>
            <td><input type="text" class="form-control item-name" data-row="${rowIndex}" value="${item.item_name || ''}" readonly tabindex="-1"></td>
            <td><input type="text" class="form-control batch-no" data-row="${rowIndex}" value="${item.batch_no || ''}"></td>
            <td><input type="text" class="form-control expiry" data-row="${rowIndex}" value="${item.expiry || ''}"></td>
            <td><input type="number" class="form-control qty" data-row="${rowIndex}" value="${item.qty || 0}" min="0"></td>
            <td><input type="number" class="form-control free-qty" data-row="${rowIndex}" value="${item.free_qty || 0}" min="0"></td>
            <td><input type="number" class="form-control rate" data-row="${rowIndex}" value="${item.pur_rate || 0}" step="0.01"></td>
            <td><input type="number" class="form-control dis-percent" data-row="${rowIndex}" value="${item.dis_percent || 0}" step="0.01"></td>
            <td><input type="number" class="form-control amount" data-row="${rowIndex}" value="${(item.qty * item.pur_rate).toFixed(2)}" step="0.01" readonly tabindex="-1"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})" tabindex="-1"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
    $('#itemsTableBody').append(row);

    const $row = $(`tr[data-row="${rowIndex}"]`);
    $row.data('item_id', item.item_id);
    $row.data('batch_id', item.batch_id);
    // ✅ FIX: restore rate_charged (ft_rate) and actual_rate (pur_rate) from API response
    $row.data('rate_charged', parseFloat(item.ft_rate)  || parseFloat(item.pur_rate) || 0);
    $row.data('actual_rate',  parseFloat(item.pur_rate) || 0);

    // Store item data - use values from API response
    const itemDataObj = {
        s_rate: parseFloat(item.s_rate) || 0,
        ws_rate: parseFloat(item.ws_rate) || 0,
        hsn_code: item.hsn_code || '',
        cgst_percent: parseFloat(item.cgst_percent) || 0,
        sgst_percent: parseFloat(item.sgst_percent) || 0,
        sc_percent: parseFloat(item.sc_percent) || 0,
        scm_percent: parseFloat(item.scm_percent) || 0,
        packing: item.packing || '',
        unit: item.unit || '',
        company_name: item.company_name || '',
        location: item.location || ''
    };
    $row.data('item_data', itemDataObj);
    
    // Debug log
    console.log('Item data stored for row ' + rowIndex + ':', itemDataObj);
    
    rowIndex++;
}

let isSubmitting = false;

function updateTransaction() {
    if (!currentClaimId) {
        alert('No claim loaded for update');
        return;
    }
    
    // Prevent double submission
    if (isSubmitting) { return; }
    isSubmitting = true;
    
    // Disable button and show loading
    const $updateBtn = $('#saveBtn');
    const originalBtnText = $updateBtn.text();
    $updateBtn.prop('disabled', true).text('Updating...');
    
    const supplierId = $('#supplier_id').val();
    if (!supplierId) {
        alert('Please select a supplier');
        isSubmitting = false;
        $updateBtn.prop('disabled', false).text(originalBtnText);
        return;
    }
    
    const items = [];
    $('#itemsTableBody tr').each(function() {
        const $row = $(this);
        const qty = parseFloat($row.find('.qty').val()) || 0;
        if (qty > 0) {
            const itemData = $row.data('item_data') || {};
            items.push({
                item_id: $row.data('item_id'),
                batch_id: $row.data('batch_id'),
                item_code: $row.find('.item-code').val(),
                item_name: $row.find('.item-name').val(),
                batch_no: $row.find('.batch-no').val(),
                expiry: $row.find('.expiry').val(),
                qty: qty,
                free_qty: parseFloat($row.find('.free-qty').val()) || 0,
                pur_rate: parseFloat($row.find('.rate').val()) || 0,
                dis_percent: parseFloat($row.find('.dis-percent').val()) || 0,
                ft_amount: parseFloat($row.find('.amount').val()) || 0,
                cgst_percent: itemData.cgst_percent || 0,
                sgst_percent: itemData.sgst_percent || 0,
                hsn_code: itemData.hsn_code || '',
                packing: itemData.packing || '',
                unit: itemData.unit || '',
                company_name: itemData.company_name || '',
                ws_rate: itemData.ws_rate || 0,
                s_rate: itemData.s_rate || 0,
                sc_percent: itemData.sc_percent || 0,
                scm_percent: itemData.scm_percent || 0,
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one item');
        isSubmitting = false;
        $updateBtn.prop('disabled', false).text(originalBtnText);
        return;
    }
    
    const data = {
        _token: '<?php echo e(csrf_token()); ?>',
        _method: 'PUT',
        claim_date: $('#claim_date').val(),
        supplier_id: supplierId,
        supplier_name: $('#supplier_id option:selected').text(),
        invoice_date: $('#invoice_date').val(),
        tax_flag: $('#tax_flag').val(),
        narration: $('#narration').val(),
        nt_amount: parseFloat($('#total_nt_amt').val()) || 0,
        tax_amount: parseFloat($('#total_tax').val()) || 0,
        net_amount: parseFloat($('#total_inv_amt').val()) || 0,
        items: items
    };
    
    $.ajax({
        url: "<?php echo e(url('admin/claim-to-supplier')); ?>/" + currentClaimId,
        method: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                alert('Claim updated successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
                isSubmitting = false;
                $updateBtn.prop('disabled', false).text(originalBtnText);
            }
        },
        error: function(xhr) {
            alert('Error updating claim: ' + (xhr.responseJSON?.message || 'Unknown error'));
            isSubmitting = false;
            $updateBtn.prop('disabled', false).text(originalBtnText);
        }
    });
}

// Check URL for claim_no parameter on page load
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const claimNo = urlParams.get('claim_no');
    if (claimNo) {
        loadClaimByNumber(claimNo);
    }
});

function loadClaimByNumber(claimNo) {
    $.get("<?php echo e(url('admin/claim-to-supplier/get-by-claim-no')); ?>/" + claimNo, function(response) {
        if (response.success) {
            populateFormWithData(response.header, response.items);
        } else {
            alert('Claim not found: ' + claimNo);
        }
    }).fail(function() {
        alert('Error loading claim: ' + claimNo);
    });
}

// ============================================================================
// SUPPLIER CUSTOM DROPDOWN
// ============================================================================
let _supplierHil = -1;

function _openSupplierDrop() {
    const dl = document.getElementById('supplierDropList');
    if (!dl) return;
    dl.style.display = 'block';
    const cur = document.getElementById('supplier_id').value;
    const items = [...document.querySelectorAll('.supplier-drop-item')];
    let activeIdx = -1;
    items.forEach((el, i) => {
        const match = el.dataset.value === cur && el.style.display !== 'none';
        el.style.background = match ? '#0d6efd' : '';
        el.style.color      = match ? '#fff'    : '';
        if (match) activeIdx = i;
    });
    if (activeIdx >= 0) _supplierHil = activeIdx;
    else if (items.filter(el=>el.style.display!=='none').length) _highlightSupplier(0);
}
function _closeSupplierDrop() {
    const dl = document.getElementById('supplierDropList');
    if (dl) dl.style.display = 'none';
    _supplierHil = -1;
}
function _filterSupplierList() {
    const q = (document.getElementById('supplier_search')?.value||'').toLowerCase();
    const items = document.querySelectorAll('.supplier-drop-item');
    let firstVisible = -1;
    items.forEach((el, i) => {
        const match = el.dataset.name.toLowerCase().includes(q);
        el.style.display = match ? '' : 'none';
        if (match && firstVisible < 0) firstVisible = i;
    });
    _supplierHil = -1;
    const dl = document.getElementById('supplierDropList');
    if (dl && dl.style.display==='none') dl.style.display = 'block';
    if (firstVisible >= 0) _highlightSupplier(firstVisible);
}
function _highlightSupplier(idx) {
    const items = [...document.querySelectorAll('.supplier-drop-item')].filter(el=>el.style.display!=='none');
    items.forEach((el,i) => { el.style.background = i===idx?'#0d6efd':''; el.style.color = i===idx?'#fff':''; });
    _supplierHil = idx;
    if (items[idx]) {
        const container = document.getElementById('supplierDropList');
        if (container) {
            const t = items[idx].offsetTop, b = t + items[idx].offsetHeight;
            if (b > container.scrollTop + container.clientHeight) container.scrollTop = b - container.clientHeight;
            else if (t < container.scrollTop) container.scrollTop = t;
        }
    }
}
function _selectSupplierItem(el) {
    document.getElementById('supplier_id').value     = el.dataset.value;
    document.getElementById('supplier_search').value = el.dataset.name;
    _closeSupplierDrop();
    setTimeout(() => { document.getElementById('invoice_date')?.focus(); }, 50);
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('#supplierWrapper')) _closeSupplierDrop();
});

// ============================================================================
// LOAD INVOICES MODAL — KEYBOARD HANDLER
// ============================================================================
function _handleInvoicesModalKey(e) {
    const modal = document.getElementById('invoicesModal');
    if (!modal || !modal.classList.contains('show')) return;
    if (!['ArrowDown','ArrowUp','Enter','Escape'].includes(e.key)) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();

    if (e.key === 'Escape') { closeLoadInvoicesModal(); return; }

    const rows = [...document.querySelectorAll('#invoicesTableContainer .invoices-table tbody tr')];
    if (!rows.length) return;

    const selectedIdx = rows.findIndex(r => r.classList.contains('selected'));

    if (e.key === 'ArrowDown') {
        const next = selectedIdx < 0 ? 0 : Math.min(selectedIdx + 1, rows.length - 1);
        rows.forEach(r => r.classList.remove('selected'));
        rows[next].classList.add('selected');
        const id = rows[next].dataset.id;
        if (id) { currentClaimId = parseInt(id); $('#loadSelectedInvoiceBtn').prop('disabled', false); }
        rows[next].scrollIntoView({ block: 'nearest' });
        return;
    }
    if (e.key === 'ArrowUp') {
        const prev = selectedIdx <= 0 ? 0 : selectedIdx - 1;
        rows.forEach(r => r.classList.remove('selected'));
        rows[prev].classList.add('selected');
        const id = rows[prev].dataset.id;
        if (id) { currentClaimId = parseInt(id); $('#loadSelectedInvoiceBtn').prop('disabled', false); }
        rows[prev].scrollIntoView({ block: 'nearest' });
        return;
    }
    if (e.key === 'Enter') {
        if (currentClaimId) loadSelectedInvoice();
        return;
    }
}

// ============================================================================
// ADDITIONAL DETAILS MODAL — KEYBOARD HANDLER
// ============================================================================
function _handleAdditionalModalKey(e) {
    const modal = document.getElementById('additionalModal');
    if (!modal || !modal.classList.contains('show')) return;
    if (!['Enter','Escape','Tab'].includes(e.key)) return;
    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();

    if (e.key === 'Escape') { closeAdditionalDetailsModal(); return; }

    if (e.key === 'Enter' || e.key === 'Tab') {
        const el = document.activeElement;
        if (el?.id === 'add_blank_statement') {
            toggleAdditionalFields();
            const blank = document.getElementById('add_blank_statement')?.value?.toUpperCase() === 'Y';
            if (blank) { saveAdditionalDetails(); }
            else { const rt = document.getElementById('add_rate_type'); if (rt) { rt.focus(); rt.select(); } }
            return;
        }
        if (el?.id === 'add_rate_type')    { document.getElementById('add_from_date')?.focus(); return; }
        if (el?.id === 'add_from_date')    { document.getElementById('add_to_date')?.focus(); return; }
        if (el?.id === 'add_to_date')      { const cc = document.getElementById('add_company_code'); if(cc){cc.focus();cc.select();} return; }
        if (el?.id === 'add_company_code') {
            const code = el.value;
            if (code) $.get("<?php echo e(url('admin/companies/by-code')); ?>/" + code, r => { $('#add_company_name').val(r.success ? r.company.name : ''); });
            const dv = document.getElementById('add_division'); if(dv){dv.focus();dv.select();} return;
        }
        if (el?.id === 'add_division') { saveAdditionalDetails(); return; }
        saveAdditionalDetails();
    }
}

// ============================================================================
// HELPERS
// ============================================================================
function _anyItemModalOpen() {
    return !!document.querySelector('#claimToSupplierModItemModal.show, #claimToSupplierModBatchModal.show');
}
function _additionalModalOpen() { const m=document.getElementById('additionalModal'); return !!(m&&m.classList.contains('show')); }
function _rateModalOpen()       { const m=document.getElementById('rateModal');       return !!(m&&m.classList.contains('show')); }
function _invoicesModalOpen()   { const m=document.getElementById('invoicesModal');   return !!(m&&m.classList.contains('show')); }

// ============================================================================
// PAGE LOAD — focus claim_date
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => { document.getElementById('claim_date')?.focus(); }, 300);
});

// ============================================================================
// MASTER KEYBOARD HANDLER — window capture phase
// Flow:
//   claim_date → Enter → Load Invoices modal
//   Invoices modal: Arrow select, Enter load → form fills → supplier_search → invoice_date → tax_flag → narration → Additional Details
//   Additional modal → division Enter → save → showAddItemModal
//   Table: qty → free-qty → Rate modal → rate → dis% → Enter → showAddItemModal (loop)
//   Ctrl+S → updateTransaction
// ============================================================================
window.addEventListener('keydown', function(e) {

    // ── Supplier dropdown open → intercept nav ─────────────────────────────
    const suppDrop = document.getElementById('supplierDropList');
    const suppOpen = suppDrop && suppDrop.style.display !== 'none';
    if (suppOpen && document.activeElement?.id === 'supplier_search') {
        if (['ArrowDown','ArrowUp','Enter','Escape'].includes(e.key)) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const vis = [...document.querySelectorAll('.supplier-drop-item')].filter(el=>el.style.display!=='none');
            if (!vis.length) return;
            if (e.key==='Escape')    { _closeSupplierDrop(); return; }
            if (e.key==='ArrowDown') { _highlightSupplier((_supplierHil+1)%vis.length); return; }
            if (e.key==='ArrowUp')   { _highlightSupplier((_supplierHil-1+vis.length)%vis.length); return; }
            if (e.key==='Enter')     { _selectSupplierItem(vis[_supplierHil>=0?_supplierHil:0]); return; }
        }
    }

    // ── Invoices modal — handled by its own handler ────────────────────────
    if (_invoicesModalOpen()) return;

    // ── Additional modal — handled by its own handler ──────────────────────
    if (_additionalModalOpen()) return;

    // ── Rate modal — handled by its own handler ────────────────────────────
    if (_rateModalOpen()) return;

    // ── Item / Batch modals → skip ─────────────────────────────────────────
    if (_anyItemModalOpen()) return;

    // ── Ctrl+S → updateTransaction ─────────────────────────────────────────
    if (e.key==='s' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (typeof updateTransaction === 'function') updateTransaction();
        else if (typeof saveTransaction === 'function') saveTransaction();
        return;
    }

    if (e.key !== 'Enter') return;
    const el = document.activeElement;
    if (!el) return;

    // ── SHIFT+ENTER: BACKWARD NAVIGATION (all fields) ──────────────────────
    if (e.shiftKey) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();

        // Header backward: narration → tax_flag → invoice_date → supplier → claim_date
        if (el.id === 'narration')       { const tf = document.getElementById('tax_flag'); if (tf) { tf.focus(); tf.select(); } return; }
        if (el.id === 'tax_flag')        { const id = document.getElementById('invoice_date'); if (id) id.focus(); return; }
        if (el.id === 'invoice_date')    { const ss = document.getElementById('supplier_search'); if (ss) { ss.focus(); ss.select(); } return; }
        if (el.id === 'supplier_search') { const cd = document.getElementById('claim_date'); if (cd) cd.focus(); return; }

        // Table backward
        const $el  = $(el);
        const $row = $el.closest('#itemsTableBody tr');
        if (!$row.length) return;

        if ($el.hasClass('dis-percent')) { $row.find('.rate').focus().select(); return; }
        if ($el.hasClass('rate'))        { $row.find('.free-qty').focus().select(); return; }
        if ($el.hasClass('free-qty'))    { $row.find('.qty').focus().select(); return; }
        if ($el.hasClass('qty'))         { $row.find('.expiry').focus().select(); return; }
        if ($el.hasClass('expiry'))      { $row.find('.batch-no').focus().select(); return; }
        if ($el.hasClass('batch-no')) {
            const $prevRow = $row.prev('tr');
            if ($prevRow.length) { $prevRow.find('.dis-percent').focus().select(); }
            else { const nr = document.getElementById('narration'); if (nr) nr.focus(); }
            return;
        }
        return;
    }

    // ── HEADER FIELDS (forward) ─────────────────────────────────────────────

    // claim_date → Enter → open Load Invoices modal
    if (el.id === 'claim_date') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        showLoadInvoicesModal();
        return;
    }

    // supplier_search (no dropdown open) → open dropdown
    if (el.id === 'supplier_search' && !suppOpen) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _openSupplierDrop();
        return;
    }

    // invoice_date → tax_flag
    if (el.id === 'invoice_date') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        const tf = document.getElementById('tax_flag');
        if (tf) { tf.focus(); tf.select(); }
        return;
    }

    // tax_flag → narration
    if (el.id === 'tax_flag') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        document.getElementById('narration')?.focus();
        return;
    }

    // narration → open Additional Details modal
    if (el.id === 'narration') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        showAdditionalDetailsModal();
        return;
    }

    // ── TABLE ROW FIELDS (forward) ──────────────────────────────────────────
    const $el  = $(el);
    const $row = $el.closest('#itemsTableBody tr');
    if (!$row.length) return;

    e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();

    if ($el.hasClass('batch-no'))   { $row.find('.expiry').focus().select(); return; }
    if ($el.hasClass('expiry'))     { $row.find('.qty').focus().select(); return; }
    if ($el.hasClass('qty'))        { $row.find('.free-qty').focus().select(); return; }
    if ($el.hasClass('free-qty'))   {
        const rowIdx = $row.data('row');
        currentRowForRate = rowIdx;
        showRateModal(rowIdx);
        return;
    }
    if ($el.hasClass('rate'))       { $row.find('.dis-percent').focus().select(); return; }
    if ($el.hasClass('dis-percent')) {
        calculateRowAmount($row);
        calculateTotals();
        // Go to next row's batch-no if it exists, else open item modal
        const $nextRow = $row.next('tr');
        if ($nextRow.length) {
            const nextBatch = $nextRow.find('.batch-no')[0];
            if (nextBatch) {
                $('#itemsTableBody tr').removeClass('selected-row');
                $nextRow.addClass('selected-row');
                updateSelectedRowDetails($nextRow);
                setTimeout(() => { nextBatch.focus(); nextBatch.select(); }, 30);
                return;
            }
        }
        // No next row — open item modal to add a new item
        setTimeout(() => showAddItemModal(), 50);
        return;
    }

}, true); // capture phase

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/claim-to-supplier/modification.blade.php ENDPATH**/ ?>