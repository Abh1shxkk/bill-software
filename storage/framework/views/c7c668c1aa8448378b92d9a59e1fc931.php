<?php $__env->startSection('title', 'Sale Return Replacement - Modification'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .compact-form { font-size: 11px; padding: 8px; background: #f5f5f5; }
    .compact-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .compact-form input, .compact-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: white; border: 1px solid #dee2e6; padding: 10px; margin-bottom: 8px; border-radius: 4px; }
    .header-row { display: flex; align-items: center; gap: 15px; margin-bottom: 6px; }
    .field-group { display: flex; align-items: center; gap: 6px; }
    .field-group label { font-weight: 600; font-size: 11px; margin-bottom: 0; white-space: nowrap; }
    .field-group input, .field-group select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    .table-compact { font-size: 10px; margin-bottom: 0; }
    .table-compact th, .table-compact td { padding: 4px; vertical-align: middle; height: 45px; }
    .table-compact th { background: #e9ecef; font-weight: 600; text-align: center; border: 1px solid #dee2e6; height: 40px; }
    .table-compact input { font-size: 10px; padding: 2px 4px; height: 22px; border: 1px solid #ced4da; width: 100%; }
    #itemsTableContainer { max-height: 310px !important; }
    .readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
    .load-section { background: #fff3cd; border: 1px solid #ffc107; padding: 6px 12px; border-radius: 4px; }

    /* Browse Modal */
    .custom-modal-backdrop { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1050; }
    .custom-modal-backdrop.show { display:block; }
    .custom-modal { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); width:700px; max-width:95%; max-height:90vh; background:white; border-radius:8px; box-shadow:0 10px 40px rgba(0,0,0,0.3); z-index:1060; overflow:hidden; }
    .custom-modal.show { display:block; }
    .custom-modal-header { display:flex; justify-content:space-between; align-items:center; padding:12px 20px; background:linear-gradient(135deg,#0d6efd,#0b5ed7); color:white; }
    .custom-modal-header h5 { margin:0; font-size:1.1rem; }
    .custom-modal-close { background:none; border:none; color:white; font-size:28px; cursor:pointer; line-height:1; padding:0; }
    .custom-modal-close:hover { opacity:0.8; }
    .custom-modal-body { padding:20px; max-height:calc(90vh - 60px); overflow-y:auto; }

    /* Keyboard-selected rows in browse modal */
    .kb-row-active, .kb-row-active td { background-color: #cfe2ff !important; font-weight: bold; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil-square me-2"></i> Sale Return Replacement - Modification</h4>
        <div class="text-muted small">Modify existing sale return replacement transaction</div>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <!-- Load Section in Header -->
        <div class="load-section d-flex align-items-center gap-2">
            <label class="fw-bold mb-0" style="font-size: 12px;">Load S.R.No:</label>
            <input type="text" id="loadTrnNo" class="form-control form-control-sm" placeholder="Enter No." style="width: 80px; height: 28px;">
            <button class="btn btn-warning btn-sm" id="btnLoad" style="height: 28px;"><i class="bi bi-search"></i> Load</button>
            <button class="btn btn-outline-info btn-sm" id="btnBrowse" style="height: 28px;"><i class="bi bi-list-ul"></i> Browse</button>
        </div>
        <a href="<?php echo e(route('admin.sale-return-replacement.transaction')); ?>" class="btn btn-success btn-sm"><i class="bi bi-plus"></i> New</a>
        <a href="<?php echo e(route('admin.sale-return-replacement.index')); ?>" class="btn btn-primary btn-sm"><i class="bi bi-list"></i> View All</a>
    </div>
</div>

<!-- Browse Modal -->
<div class="custom-modal-backdrop" id="browseModalBackdrop" onclick="closeBrowseModal()"></div>
<div class="custom-modal" id="browseModal">
    <div class="custom-modal-header">
        <h5><i class="bi bi-list-ul me-2"></i> Select Transaction</h5>
        <button type="button" class="custom-modal-close" onclick="closeBrowseModal()">&times;</button>
    </div>
    <div class="custom-modal-body">
        <div class="mb-3">
            <input type="text" class="form-control" id="browseSearchInput" placeholder="Search by S.R.No, Customer Name..." oninput="filterBrowseModal()">
        </div>
        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
            <table class="table table-hover table-sm">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th>S.R.No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th class="text-end">Net Amt</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="browseModalBody">
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form id="modificationForm" method="POST" autocomplete="off" onsubmit="return false;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" name="id" id="trnId">
            
            <!-- Header Section -->
            <div class="header-section">
                <!-- Row 1: Series, Date, Customer -->
                <div class="header-row">
                    <div class="field-group">
                        <label>Series:</label>
                        <input type="text" class="form-control readonly-field" value="RG" readonly style="width: 60px; text-align: center; font-weight: bold; color: red;">
                    </div>
                    
                    <div class="field-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="trn_date" id="trn_date" style="width: 140px;" onchange="updateDayName()">
                        <input type="text" class="form-control readonly-field" id="dayName" readonly style="width: 90px;">
                    </div>
                    
                    <div class="field-group">
                        <label>Customer:</label>
                        <div style="position: relative; width: 250px;" id="customerDropdownWrapper">
                            <input type="text" id="customerSearchInput" class="form-control" placeholder="Search customer..." autocomplete="off" style="width: 250px;" onblur="_onCustomerBlur()" oninput="_filterCustomers()">
                            <div id="customerDropList" style="display:none; position:absolute; z-index:99999; top:100%; left:0; width:100%; max-height:220px; overflow-y:auto; background:white; border:1px solid #ccc; box-shadow:0 4px 8px rgba(0,0,0,.15);">
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="customer-drop-item" data-id="<?php echo e($customer->id); ?>" data-label="<?php echo e(($customer->code ?? '') . ' - ' . $customer->name); ?>" style="padding:5px 10px; cursor:pointer; font-size:11px;" onmousedown="_selectCustomerItem(this)"><?php echo e(($customer->code ?? '')); ?> - <?php echo e($customer->name); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <input type="hidden" id="customer_id" name="customer_id" value="">
                    </div>
                </div>
                
                <!-- Row 2: S.R.No, Inner Card -->
                <div class="d-flex gap-3">
                    <!-- Left Side - S.R.No -->
                    <div style="width: 250px;">
                        <div class="field-group mb-2">
                            <label style="width: 70px;">S.R.No.:</label>
                            <input type="text" class="form-control readonly-field" name="trn_no" id="trnNo" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-success" id="addRowBtn" style="width: 100%;">
                                <i class="bi bi-plus-circle"></i> Add Item Row
                            </button>
                        </div>
                    </div>
                    
                    <!-- Right Side - Inner Card -->
                    <div class="inner-card flex-grow-1">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Cash:</label>
                                    <div style="position:relative;" id="cashDropdownWrapper">
                                        <input type="text" id="is_cash_display" class="form-control" value="N"
                                               readonly style="width:60px; cursor:pointer; caret-color:transparent; background:white;"
                                               onclick="_toggleCashDrop()"
                                               onkeydown="_cashKeydown(event)">
                                        <input type="hidden" id="is_cash" name="is_cash" value="N">
                                        <div id="cashDropList" style="display:none; position:absolute; z-index:99999; top:100%; left:0; min-width:80px; background:white; border:1px solid #ccc; box-shadow:0 4px 8px rgba(0,0,0,.15);">
                                            <div class="cash-drop-item" data-value="N" style="padding:5px 10px; cursor:pointer; font-size:11px; font-weight:600;" onmousedown="_selectCashItem(this)">N</div>
                                            <div class="cash-drop-item" data-value="Y" style="padding:5px 10px; cursor:pointer; font-size:11px; font-weight:600;" onmousedown="_selectCashItem(this)">Y</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Fixed Dis.:</label>
                                    <input type="number" class="form-control" name="fixed_discount" id="fixed_discount" step="0.01" style="width: 80px;" value="0">
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

            
            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                            <tr>
                                <th style="width: 50px;">Code</th>
                                <th style="width: 180px;">Item Name</th>
                                <th style="width: 65px;">Batch</th>
                                <th style="width: 55px;">Exp.</th>
                                <th style="width: 45px;">Qty.</th>
                                <th style="width: 45px;">F.Qty</th>
                                <th style="width: 65px;">Rate</th>
                                <th style="width: 45px;">Dis%</th>
                                <th style="width: 60px;">FTRate</th>
                                <th style="width: 70px;">Amount</th>
                                <th style="width: 50px;">Act</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                        </tbody>
                    </table>
                </div>
                <!-- Add Row Button -->
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-success" id="addRowBtn2">
                        <i class="bi bi-plus-circle"></i> Add Row
                    </button>
                </div>
            </div>

            
            <!-- Calculation Section -->
            <div class="bg-white border rounded p-3 mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="d-flex align-items-start gap-3 border rounded p-2" style="font-size: 11px; background: #fafafa;">
                    <div class="d-flex flex-column gap-2" style="min-width: 200px;">
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>SC %</strong></label>
                            <input type="number" class="form-control" id="sc_percent" name="sc_percent" step="0.01" style="width: 80px; height: 28px;" value="0">
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>Tax %</strong></label>
                            <input type="number" class="form-control" id="tax_percent" name="tax_percent" step="0.01" style="width: 80px; height: 28px;" value="0">
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>Excise</strong></label>
                            <input type="number" class="form-control readonly-field" id="excise" name="excise" readonly step="0.01" style="width: 80px; height: 28px;" value="0">
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 60px;"><strong>TSR</strong></label>
                                <input type="number" class="form-control readonly-field" id="tsr" name="tsr" readonly step="0.01" style="width: 80px; height: 28px;" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Section -->
            <div class="bg-white border rounded p-2 mb-2" style="background: #ffcccc;">
                <div class="d-flex align-items-center" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold; white-space: nowrap;">N.T.Amt.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="nt_amt" name="nt_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">SC</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="sc_amt" name="sc_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">F.T.Amt.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="ft_amt" name="ft_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Dis.</label>
                        <input type="number" class="form-control form-control-sm text-end" id="dis_amt" name="dis_amt" step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.</label>
                        <input type="number" class="form-control form-control-sm text-end" id="scm_amt" name="scm_amt" step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Tax</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="tax_amt" name="tax_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Net</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="net_amt" name="net_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>
                <div class="d-flex align-items-center mt-2" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.%</label>
                        <input type="number" class="form-control form-control-sm text-end" id="scm_percent" name="scm_percent" step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>
            </div>
            
            <!-- Detailed Info Section -->
            <div class="bg-white border rounded p-2 mb-2" style="background: #ffe6cc;">
                <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
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
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Srlno</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="detailSrIno" readonly value="" style="height: 22px; width: 60px;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Unit</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="detailUnit" readonly value="1" style="height: 22px; width: 60px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>SC Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Scm.Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScmAmt" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Tax Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailTaxAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>MRP</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field" id="detailMrp" readonly value="" style="height: 22px; width: 100px;"></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Cl. Qty</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-end" id="detailClQty" readonly value="" style="height: 22px; width: 60px; background: #add8e6;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Dis. Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailDisAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Net Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailNetAmt" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Vol.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailVol" readonly value="0" style="height: 22px; width: 80px;"></td>
                        <td colspan="4"></td>
                    </tr>
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
                <button type="button" class="btn btn-warning btn-sm" id="btnUpdate">
                    <i class="bi bi-save"></i> Update
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="btnDelete">
                    <i class="bi bi-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.reload()">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// ──────────────────────────────────────────────────────────────────────
// CUSTOMER CUSTOM DROPDOWN
// ──────────────────────────────────────────────────────────────────────
let _customerHighlightIdx = -1;

function _getAllCustomerItems() {
    return document.querySelectorAll('#customerDropList .customer-drop-item');
}

function _filterCustomers() {
    const q = document.getElementById('customerSearchInput').value.toLowerCase();
    _getAllCustomerItems().forEach(el => {
        el.style.display = el.dataset.label.toLowerCase().includes(q) ? '' : 'none';
    });
    _customerHighlightIdx = -1;
    _highlightCustomer(-1);
    document.getElementById('customerDropList').style.display = 'block';
}

function _openCustomerDrop() { _filterCustomers(); }
function _closeCustomerDrop() { document.getElementById('customerDropList').style.display = 'none'; }

function _highlightCustomer(idx) {
    const items = [..._getAllCustomerItems()].filter(el => el.style.display !== 'none');
    items.forEach((el, i) => {
        el.style.background = i === idx ? '#0d6efd' : '';
        el.style.color      = i === idx ? '#fff'    : '';
        if (i === idx) el.scrollIntoView({ block: 'nearest' });
    });
}

function _selectCustomerItem(el) {
    document.getElementById('customerSearchInput').value = el.dataset.label;
    document.getElementById('customer_id').value         = el.dataset.id;
    _closeCustomerDrop();
    _customerHighlightIdx = -1;
    setTimeout(() => { document.getElementById('is_cash_display').focus(); }, 50);
}

function _onCustomerBlur() { setTimeout(_closeCustomerDrop, 150); }

// ──────────────────────────────────────────────────────────────────────
// CASH CUSTOM DROPDOWN
// ──────────────────────────────────────────────────────────────────────
let _cashHighlightIdx = -1;

function _toggleCashDrop() {
    const dl = document.getElementById('cashDropList');
    if (dl.style.display === 'none') {
        dl.style.display = 'block';
        const items = document.querySelectorAll('.cash-drop-item');
        const cur   = document.getElementById('is_cash').value;
        items.forEach((el, i) => {
            const active = el.dataset.value === cur;
            el.style.background = active ? '#0d6efd' : '';
            el.style.color      = active ? '#fff'    : '';
            if (active) _cashHighlightIdx = i;
        });
    } else {
        dl.style.display = 'none';
    }
}

function _closeCashDrop() { document.getElementById('cashDropList').style.display = 'none'; }

function _selectCashItem(el) {
    document.getElementById('is_cash_display').value = el.dataset.value;
    document.getElementById('is_cash').value         = el.dataset.value;
    _closeCashDrop();
    _cashHighlightIdx = -1;
    setTimeout(() => { const fd = document.getElementById('fixed_discount'); fd.focus(); fd.select(); }, 50);
}

function _cashKeydown(e) {
    const dl    = document.getElementById('cashDropList');
    const items = [...document.querySelectorAll('.cash-drop-item')];
    const open  = dl.style.display !== 'none';

    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (!open) { _toggleCashDrop(); return; }
        _cashHighlightIdx = e.key === 'ArrowDown'
            ? (_cashHighlightIdx + 1) % items.length
            : (_cashHighlightIdx - 1 + items.length) % items.length;
        items.forEach((el, i) => {
            el.style.background = i === _cashHighlightIdx ? '#0d6efd' : '';
            el.style.color      = i === _cashHighlightIdx ? '#fff'    : '';
        });
        return;
    }
    if (e.key === 'Enter') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (open && _cashHighlightIdx >= 0) {
            _selectCashItem(items[_cashHighlightIdx]);
        } else if (!open) {
            const fd = document.getElementById('fixed_discount');
            fd.focus(); fd.select();
        }
        return;
    }
    if (e.key.toLowerCase() === 'n' || e.key.toLowerCase() === 'y') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        const val = e.key.toUpperCase();
        document.getElementById('is_cash_display').value = val;
        document.getElementById('is_cash').value         = val;
        _closeCashDrop();
        setTimeout(() => { const fd = document.getElementById('fixed_discount'); fd.focus(); fd.select(); }, 50);
        return;
    }
    if (e.key === 'Escape') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _closeCashDrop(); return;
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#cashDropdownWrapper')) _closeCashDrop();
});

// ──────────────────────────────────────────────────────────────────────
// DAY NAME
// ──────────────────────────────────────────────────────────────────────
window.updateDayName = function() {
    const date = new Date($('#trn_date').val());
    $('#dayName').val(date.toLocaleDateString('en-US', { weekday: 'long' }));
};

// ──────────────────────────────────────────────────────────────────────
// ADD ROW / REMOVE ROW / TOTALS
// ──────────────────────────────────────────────────────────────────────
function addNewRow(item) {
    let i = $('#itemsTableBody tr').length;
    item = item || {};
    let newRow = `<tr>
        <td><input type="text" class="form-control item-code" name="items[${i}][item_code]" value="${item.item_code || ''}">
            <input type="hidden" name="items[${i}][item_id]" value="${item.item_id || ''}">
        </td>
        <td><input type="text" class="form-control item-name" name="items[${i}][item_name]" value="${item.item_name || ''}"></td>
        <td><input type="text" class="form-control" name="items[${i}][batch_no]" value="${item.batch_no || ''}"></td>
        <td><input type="text" class="form-control" name="items[${i}][expiry_date]" value="${item.expiry_date || ''}"></td>
        <td><input type="number" step="any" class="form-control qty" name="items[${i}][qty]" value="${item.qty || ''}"></td>
        <td><input type="number" step="any" class="form-control f-qty" name="items[${i}][free_qty]" value="${item.free_qty || 0}"></td>
        <td><input type="number" step="any" class="form-control sale-rate" name="items[${i}][sale_rate]" value="${item.sale_rate || ''}"></td>
        <td><input type="number" step="any" class="form-control dis-percent" name="items[${i}][discount_percent]" value="${item.discount_percent || 0}"></td>
        <td><input type="number" step="any" class="form-control ft-rate" name="items[${i}][ft_rate]" value="${item.ft_rate || ''}"></td>
        <td><input type="number" step="any" class="form-control amount" name="items[${i}][amount]" value="${item.amount || '0.00'}" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-x"></i></button></td>
    </tr>`;
    $('#itemsTableBody').append(newRow);
}

$('#addRowBtn, #addRowBtn2').click(function() { _openItemModal(); });

$(document).on('click', '.remove-row', function() {
    if ($('#itemsTableBody tr').length > 1) {
        $(this).closest('tr').remove();
        calculateTotals();
    }
});

$(document).on('input', '.qty, .sale-rate, .dis-percent', function() {
    let row = $(this).closest('tr');
    let qty = parseFloat(row.find('.qty').val()) || 0;
    let rate = parseFloat(row.find('.sale-rate').val()) || 0;
    let dis = parseFloat(row.find('.dis-percent').val()) || 0;
    let gross = qty * rate;
    let disAmt = gross * (dis / 100);
    row.find('.amount').val((gross - disAmt).toFixed(2));
    calculateTotals();
});

$('#sc_percent, #tax_percent, #dis_amt, #scm_amt').on('input', function() { calculateTotals(); });

function calculateTotals() {
    let total = 0;
    $('#itemsTableBody tr').each(function() {
        total += parseFloat($(this).find('.amount').val()) || 0;
    });
    let scPercent  = parseFloat($('#sc_percent').val())  || 0;
    let taxPercent = parseFloat($('#tax_percent').val()) || 0;
    let disAmt     = parseFloat($('#dis_amt').val())     || 0;
    let scmAmt     = parseFloat($('#scm_amt').val())     || 0;
    let scAmt  = total * (scPercent / 100);
    let taxAmt = total * (taxPercent / 100);
    let net    = total + scAmt + taxAmt - disAmt - scmAmt;
    $('#nt_amt').val(total.toFixed(2));
    $('#ft_amt').val(total.toFixed(2));
    $('#sc_amt').val(scAmt.toFixed(2));
    $('#tax_amt').val(taxAmt.toFixed(2));
    $('#net_amt').val(net.toFixed(2));
}

// ──────────────────────────────────────────────────────────────────────
// ITEM MODAL INTEGRATION (same pattern as transaction.blade.php)
// ──────────────────────────────────────────────────────────────────────
let _rowCount = 0;

function _openItemModal() {
    if (typeof openItemModal_saleReturnItemModal === 'function') {
        openItemModal_saleReturnItemModal();
    }
}

// Modal bridge callbacks — called by the item-selection / batch-selection components
window.onItemSelectedFromModal = function(item) {
    if (typeof openBatchModal_saleReturnBatchModal === 'function') {
        openBatchModal_saleReturnBatchModal(item);
    }
};

window.onItemBatchSelectedFromModal = function(item, batch) {
    _addItemRow(item, batch);
};

window.onBatchSelectedFromModal = function(item, batch) {
    _addItemRow(item, batch);
};

function _addItemRow(item, batch) {
    const tbody = document.getElementById('itemsTableBody');
    const idx   = tbody.querySelectorAll('tr').length;

    const expiryRaw     = batch?.expiry_date || '';
    const expiryDisplay = expiryRaw
        ? new Date(expiryRaw).toLocaleDateString('en-GB', { month: '2-digit', year: 'numeric' })
        : '';
    const saleRate = parseFloat(batch?.s_rate || item?.s_rate || item?.sale_rate || 0).toFixed(2);
    const mrp      = parseFloat(batch?.mrp    || item?.mrp   || 0).toFixed(2);
    const clQty    = parseFloat(batch?.qty    || 0);

    const tr = document.createElement('tr');
    tr.setAttribute('data-row', idx);
    tr.innerHTML = `
        <td><input type="text"   class="form-control item-code readonly-field" name="items[${idx}][item_code]" value="${item.id}" readonly>
            <input type="hidden" name="items[${idx}][item_id]" value="${item.id}">
        </td>
        <td><input type="text"   class="form-control item-name readonly-field" name="items[${idx}][item_name]" value="${_escHtml(item.name)}" readonly>
            <input type="hidden" name="items[${idx}][batch_id]"   value="${batch?.id || ''}">
            <input type="hidden" name="items[${idx}][packing]"    value="${_escHtml(item.packing || '')}">
            <input type="hidden" name="items[${idx}][company]"    value="${_escHtml(item.company_short_name || item.mfg_by || '')}">
            <input type="hidden" name="items[${idx}][mrp]"        value="${mrp}">
            <input type="hidden" name="items[${idx}][unit]"       value="${_escHtml(item.unit || '1')}">
            <input type="hidden" name="items[${idx}][cl_qty]"     value="${clQty}">
        </td>
        <td><input type="text"   class="form-control readonly-field" name="items[${idx}][batch_no]"     value="${_escHtml(batch?.batch_no || '')}" readonly></td>
        <td><input type="text"   class="form-control readonly-field" value="${expiryDisplay}" readonly>
            <input type="hidden" name="items[${idx}][expiry_date]" value="${expiryRaw}">
        </td>
        <td><input type="number" step="any" class="form-control qty"         name="items[${idx}][qty]"               value=""></td>
        <td><input type="number" step="any" class="form-control f-qty"       name="items[${idx}][free_qty]"          value="0"></td>
        <td><input type="number" step="any" class="form-control sale-rate"   name="items[${idx}][sale_rate]"         value="${saleRate}"></td>
        <td><input type="number" step="any" class="form-control dis-percent" name="items[${idx}][discount_percent]"  value="0"></td>
        <td><input type="number" step="any" class="form-control ft-rate"     name="items[${idx}][ft_rate]"           value="${saleRate}"></td>
        <td><input type="number" step="any" class="form-control amount"      name="items[${idx}][amount]"            value="0.00" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-x"></i></button></td>
    `;
    tbody.appendChild(tr);

    // Update detail section
    document.getElementById('detailPacking').value  = item.packing || '';
    document.getElementById('detailCompany').value  = item.company_short_name || item.mfg_by || '';
    document.getElementById('detailMrp').value      = mrp;
    document.getElementById('detailUnit').value     = item.unit || '1';
    document.getElementById('detailClQty').value    = clQty;
    document.getElementById('detailSrIno').value    = idx + 1;

    _rowCount = idx + 1;
    calculateTotals();

    // Focus qty of the new row
    setTimeout(() => {
        const qtyInput = tr.querySelector('.qty');
        if (qtyInput) { qtyInput.focus(); qtyInput.select(); }
    }, 60);
}

function _escHtml(t) {
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
}

function _anyModalOpen() {
    return (
        !!document.querySelector('#saleReturnItemModal.show')  ||
        !!document.querySelector('#saleReturnBatchModal.show')
    );
}

// ──────────────────────────────────────────────────────────────────────
// LOAD TRANSACTION BY S.R.No
// ──────────────────────────────────────────────────────────────────────
function loadTransaction(trnNo) {
    if (!trnNo) {
        // No number entered → move cursor to Browse button
        setTimeout(function() {
            var browseBtn = document.getElementById('btnBrowse');
            if (browseBtn) browseBtn.focus();
        }, 50);
        return;
    }
    $.ajax({
        url: "<?php echo e(url('admin/sale-return-replacement/get')); ?>/" + trnNo,
        method: "GET",
        success: function(res) {
            if (res.success) {
                let d = res.transaction;
                $('#trnId').val(d.id);
                $('#trn_date').val(d.trn_date);
                updateDayName();
                $('#trnNo').val(d.trn_no);
                // Set customer
                document.getElementById('customer_id').value = d.customer_id || '';
                // Try to find the customer label
                let custItem = document.querySelector('#customerDropList .customer-drop-item[data-id="' + d.customer_id + '"');
                if (custItem) {
                    document.getElementById('customerSearchInput').value = custItem.dataset.label;
                } else {
                    document.getElementById('customerSearchInput').value = d.customer_name || '';
                }
                
                $('#fixed_discount').val(d.fixed_discount || 0);
                document.getElementById('is_cash').value = d.is_cash || 'N';
                document.getElementById('is_cash_display').value = d.is_cash || 'N';
                $('#remarks').val(d.remarks || '');
                
                // Footer values
                $('#sc_percent').val(d.sc_percent || 0);
                $('#tax_percent').val(d.tax_percent || 0);
                $('#dis_amt').val(d.dis_amt || 0);
                $('#scm_amt').val(d.scm_amt || 0);
                
                // Populate Items
                $('#itemsTableBody').empty();
                if (d.items && d.items.length > 0) {
                    d.items.forEach(function(item) { addNewRow(item); });
                } else {
                    addNewRow();
                }
                calculateTotals();
                
                // Focus first qty field after successful load
                setTimeout(function() {
                    var firstQty = document.querySelector('#itemsTableBody tr:first-child .qty');
                    if (firstQty) { firstQty.focus(); firstQty.select(); }
                }, 100);
            } else {
                // Transaction not found → move cursor to Browse button
                alert('Transaction not found');
                setTimeout(function() {
                    var browseBtn = document.getElementById('btnBrowse');
                    if (browseBtn) browseBtn.focus();
                }, 50);
            }
        },
        error: function() {
            // Transaction not found → move cursor to Browse button
            alert('Transaction not found');
            setTimeout(function() {
                var browseBtn = document.getElementById('btnBrowse');
                if (browseBtn) browseBtn.focus();
            }, 50);
        }
    });
}

$('#btnLoad').click(function() {
    loadTransaction($('#loadTrnNo').val());
});

// ──────────────────────────────────────────────────────────────────────
// UPDATE TRANSACTION
// ──────────────────────────────────────────────────────────────────────
$('#btnUpdate').click(function() {
    let id = $('#trnId').val();
    if (!id) { alert('Please load a transaction first!'); return; }
    $.ajax({
        url: "<?php echo e(url('admin/sale-return-replacement')); ?>/" + id,
        method: "POST",
        data: $('#modificationForm').serialize() + "&_method=PUT",
        success: function(response) {
            if (response.success) {
                alert('Transaction updated successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            let msg = 'Error updating transaction';
            try {
                const resp = JSON.parse(xhr.responseText);
                if (resp.message) msg = resp.message;
                else if (xhr.status === 422 && resp.errors) {
                    msg = Object.values(resp.errors).flat().join('\n');
                }
            } catch(e) {
                msg = xhr.responseText?.substring(0, 300) || msg;
            }
            alert('Error (' + xhr.status + '):\n' + msg);
        }
    });
});

// ──────────────────────────────────────────────────────────────────────
// DELETE TRANSACTION
// ──────────────────────────────────────────────────────────────────────
$('#btnDelete').click(function() {
    let id = $('#trnId').val();
    if (!id) { alert('Please load a transaction first!'); return; }
    if (confirm('Are you sure you want to delete this transaction?')) {
        $.ajax({
            url: "<?php echo e(url('admin/sale-return-replacement')); ?>/" + id,
            method: "POST",
            data: { _method: 'DELETE', _token: "<?php echo e(csrf_token()); ?>" },
            success: function(response) {
                if (response.success) { alert('Transaction deleted!'); window.location.reload(); }
            },
            error: function() { alert('Error deleting transaction'); }
        });
    }
});

// ──────────────────────────────────────────────────────────────────────
// BROWSE MODAL
// ──────────────────────────────────────────────────────────────────────
let allBrowseData = [];
let browseSelectedIndex = -1;

function openBrowseModal() {
    // Register modal keyboard handler FIRST (window capture = fires before everything)
    window.removeEventListener('keydown', _handleBrowseModalKey, true); // remove stale
    document.getElementById('browseModalBackdrop').classList.add('show');
    document.getElementById('browseModal').classList.add('show');
    window.addEventListener('keydown', _handleBrowseModalKey, true);
    
    document.getElementById('browseSearchInput').value = '';
    document.getElementById('browseModalBody').innerHTML = '<tr><td colspan="5" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
    browseSelectedIndex = -1;
    
    $.ajax({
        url: "<?php echo e(route('admin.sale-return-replacement.all-transactions')); ?>",
        method: "GET",
        dataType: "json",
        success: function(res) {
            if (res.success && res.data) {
                allBrowseData = res.data;
            } else {
                allBrowseData = [];
            }
            renderBrowseRows(allBrowseData);
            setTimeout(function() {
                var searchInput = document.getElementById('browseSearchInput');
                if (searchInput) searchInput.focus();
            }, 150);
        },
        error: function() {
            document.getElementById('browseModalBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
        }
    });
}

function closeBrowseModal() {
    // Unregister modal keyboard handler
    window.removeEventListener('keydown', _handleBrowseModalKey, true);
    document.getElementById('browseModalBackdrop').classList.remove('show');
    document.getElementById('browseModal').classList.remove('show');
    browseSelectedIndex = -1;
    
    // Return focus to Browse button
    setTimeout(function() {
        var browseBtn = document.getElementById('btnBrowse');
        if (browseBtn) browseBtn.focus();
    }, 50);
}

function renderBrowseRows(data) {
    var tbody = document.getElementById('browseModalBody');
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No transactions found</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(function(t) {
        var dateDisplay = t.trn_date ? new Date(t.trn_date).toLocaleDateString('en-IN') : '';
        return '<tr>' +
            '<td><strong>' + (t.trn_no || '') + '</strong></td>' +
            '<td>' + dateDisplay + '</td>' +
            '<td>' + (t.customer_name || '') + '</td>' +
            '<td class="text-end">₹ ' + parseFloat(t.net_amt || 0).toFixed(2) + '</td>' +
            '<td><button type="button" class="btn btn-sm btn-primary" onclick="selectFromBrowse(\'' + t.trn_no + '\')"><i class="bi bi-check"></i> Select</button></td>' +
        '</tr>';
    }).join('');
}

function filterBrowseModal() {
    var term = document.getElementById('browseSearchInput').value.toLowerCase().trim();
    if (!term) { renderBrowseRows(allBrowseData); return; }
    var filtered = allBrowseData.filter(function(t) {
        var trnNo = (t.trn_no || '').toString().toLowerCase();
        var name  = (t.customer_name || '').toLowerCase();
        return trnNo.includes(term) || name.includes(term);
    });
    renderBrowseRows(filtered);
    browseSelectedIndex = -1;
}

function selectFromBrowse(trnNo) {
    closeBrowseModal();
    document.getElementById('loadTrnNo').value = trnNo;
    loadTransaction(trnNo);
}

$('#btnBrowse').click(function() { openBrowseModal(); });

// ──────────────────────────────────────────────────────────────────────
// BROWSE MODAL - KEYBOARD NAVIGATION
// Uses window capture-phase pattern per KEYBOARD_HANDLER_CONTEXT.md
// Registered on open, unregistered on close.
// ──────────────────────────────────────────────────────────────────────
function _isBrowseModalOpen() {
    var m = document.getElementById('browseModal');
    return m && m.classList.contains('show');
}

function _highlightBrowseRow(rows, index) {
    rows.forEach(function(row) {
        row.classList.remove('kb-row-active');
    });
    if (index >= 0 && index < rows.length) {
        rows[index].classList.add('kb-row-active');
        rows[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
}

// ── Handler function (registered/unregistered on modal open/close) ──
function _handleBrowseModalKey(e) {
    var modal = document.getElementById('browseModal');
    if (!modal || !modal.classList.contains('show')) return; // guard
    
    var MANAGED = ['ArrowDown', 'ArrowUp', 'Enter', 'Escape'];
    // Allow typing to refocus search
    var isTyping = (e.key.length === 1 && !e.ctrlKey && !e.altKey);
    
    if (!MANAGED.includes(e.key) && !isTyping) return;

    var tbody = document.getElementById('browseModalBody');
    var rows  = Array.from(tbody.querySelectorAll('tr')).filter(function(row) {
        return row.querySelector('button') !== null;
    });

    if (e.key === 'ArrowDown') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        var searchInput = document.getElementById('browseSearchInput');
        if (document.activeElement === searchInput) searchInput.blur();
        if (!rows.length) return;
        if (browseSelectedIndex < rows.length - 1) browseSelectedIndex++;
        else if (browseSelectedIndex === -1) browseSelectedIndex = 0;
        _highlightBrowseRow(rows, browseSelectedIndex);
        return;
    }

    if (e.key === 'ArrowUp') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        var searchInput = document.getElementById('browseSearchInput');
        if (document.activeElement === searchInput) searchInput.blur();
        if (!rows.length) return;
        if (browseSelectedIndex > 0) browseSelectedIndex--;
        else if (browseSelectedIndex === -1 && rows.length > 0) browseSelectedIndex = 0;
        _highlightBrowseRow(rows, browseSelectedIndex);
        return;
    }

    if (e.key === 'Enter') {
        var searchInput = document.getElementById('browseSearchInput');
        if (document.activeElement === searchInput && browseSelectedIndex === -1) {
            // Let search work normally, but don't let other handlers catch it
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            return;
        }
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        if (!rows.length) return;
        if (browseSelectedIndex === -1 && rows.length > 0) {
            browseSelectedIndex = 0;
            _highlightBrowseRow(rows, browseSelectedIndex);
            return;
        }
        if (browseSelectedIndex >= 0 && browseSelectedIndex < rows.length) {
            var selectBtn = rows[browseSelectedIndex].querySelector('button');
            if (selectBtn) selectBtn.click();
        }
        return;
    }

    if (e.key === 'Escape') {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        closeBrowseModal();
        return;
    }

    // Any letter/number → refocus search
    if (isTyping) {
        var searchInput = document.getElementById('browseSearchInput');
        if (searchInput && document.activeElement !== searchInput) {
            searchInput.focus();
            browseSelectedIndex = -1;
            _highlightBrowseRow(rows, -1);
        }
    }
}

// ──────────────────────────────────────────────────────────────────────
// MASTER KEYBOARD HANDLER
// Flow: loadTrnNo → btnBrowse → btnLoad → trn_date → Customer → Cash → Fixed Dis → Remarks → btnUpdate
// ──────────────────────────────────────────────────────────────────────
window.addEventListener('keydown', function(e) {
    // Skip if browse modal or item/batch modal is open
    if (_isBrowseModalOpen()) return;
    if (_anyModalOpen()) return;

    // ── Cash dropdown arrow/enter/escape ──
    const cashList = document.getElementById('cashDropList');
    const cashOpen = cashList && cashList.style.display !== 'none';
    if (cashOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter' || e.key === 'Escape')) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _cashKeydown(e);
        return;
    }

    // ── Customer dropdown arrow/enter/escape ──
    const dropList = document.getElementById('customerDropList');
    const dropOpen = dropList && dropList.style.display !== 'none';
    if (dropOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter' || e.key === 'Escape')) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        const visible = [...document.querySelectorAll('#customerDropList .customer-drop-item')]
                            .filter(el => el.style.display !== 'none');
        if (e.key === 'ArrowDown') {
            _customerHighlightIdx = (_customerHighlightIdx + 1) % visible.length;
            _highlightCustomer(_customerHighlightIdx);
        } else if (e.key === 'ArrowUp') {
            _customerHighlightIdx = (_customerHighlightIdx - 1 + visible.length) % visible.length;
            _highlightCustomer(_customerHighlightIdx);
        } else if (e.key === 'Enter') {
            if (_customerHighlightIdx >= 0 && visible[_customerHighlightIdx]) {
                _selectCustomerItem(visible[_customerHighlightIdx]);
            } else if (visible.length === 1) {
                _selectCustomerItem(visible[0]);
            }
        } else if (e.key === 'Escape') {
            _closeCustomerDrop();
        }
        return;
    }

    // ── Ctrl+S → Update ──
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        document.getElementById('btnUpdate').click();
        return;
    }

    // ── Enter key navigation ──
    if (e.key === 'Enter') {
        const el = document.activeElement;
        if (!el) return;

        // Load S.R.No field → Enter:
        //   - If has value → attempt to load transaction (on fail, cursor goes to Browse)
        //   - If empty → move cursor to Browse button directly
        if (el.id === 'loadTrnNo') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            var val = el.value.trim();
            if (val) {
                loadTransaction(val);
            } else {
                // Empty field → go to Browse button
                var browseBtn = document.getElementById('btnBrowse');
                if (browseBtn) browseBtn.focus();
            }
            return;
        }

        // Browse button → open modal
        if (el.id === 'btnBrowse') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            openBrowseModal();
            return;
        }

        // Load button → trigger load
        if (el.id === 'btnLoad') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            loadTransaction($('#loadTrnNo').val());
            return;
        }

        // Skip other buttons
        if (el.tagName === 'BUTTON' || el.tagName === 'A') return;

        // Date → Customer
        if (el.id === 'trn_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const inp = document.getElementById('customerSearchInput');
            inp.focus(); inp.select();
            _openCustomerDrop();
            return;
        }

        // Customer search → open dropdown
        if (el.id === 'customerSearchInput') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            _openCustomerDrop();
            return;
        }

        // Cash display → enter cash dropdown
        if (el.id === 'is_cash_display') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            _toggleCashDrop();
            return;
        }

        // Fixed Discount → Remarks
        if (el.id === 'fixed_discount') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('remarks').focus();
            return;
        }

        // Remarks → Update
        if (el.id === 'remarks') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('btnUpdate').focus();
            return;
        }

        // Table qty → next field in row
        if (el.classList.contains('qty')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const row = el.closest('tr');
            const nextInput = row.querySelector('.f-qty');
            if (nextInput) { nextInput.focus(); nextInput.select(); }
            return;
        }
        if (el.classList.contains('f-qty')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const row = el.closest('tr');
            const nextInput = row.querySelector('.sale-rate');
            if (nextInput) { nextInput.focus(); nextInput.select(); }
            return;
        }
        if (el.classList.contains('sale-rate')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const row = el.closest('tr');
            const nextInput = row.querySelector('.dis-percent');
            if (nextInput) { nextInput.focus(); nextInput.select(); }
            return;
        }
        // Dis% → FT Rate (same row)
        if (el.classList.contains('dis-percent')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const row = el.closest('tr');
            const ftRate = row.querySelector('.ft-rate');
            if (ftRate) { ftRate.focus(); ftRate.select(); }
            return;
        }
        // FT Rate → next row's Qty OR open item modal if last row
        if (el.classList.contains('ft-rate')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const currentRow = el.closest('tr');
            const nextRow = currentRow.nextElementSibling;
            if (nextRow) {
                // Next row exists → move to its Qty field
                const nextQty = nextRow.querySelector('.qty');
                if (nextQty) { nextQty.focus(); nextQty.select(); }
            } else {
                // Last row → open item modal to add new item
                _openItemModal();
            }
            return;
        }
    }

    // ── Escape → close any open dropdown ──
    if (e.key === 'Escape') {
        _closeCustomerDrop();
        _closeCashDrop();
    }

}, true);

// Auto-focus loadTrnNo on page load
$(document).ready(function() {
    setTimeout(function() {
        var loadField = document.getElementById('loadTrnNo');
        if (loadField) { loadField.focus(); loadField.select(); }
    }, 100);
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('components.modals.item-selection', [
    'id'           => 'saleReturnItemModal',
    'module'       => 'sale-return-replacement',
    'showStock'    => true,
    'rateType'     => 'sale',
    'showCompany'  => true,
    'showHsn'      => false,
    'batchModalId' => 'saleReturnBatchModal',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.modals.batch-selection', [
    'id'                => 'saleReturnBatchModal',
    'module'            => 'sale-return-replacement',
    'showOnlyAvailable' => false,
    'rateType'          => 'sale',
    'showCostDetails'   => false,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/sale-return-replacement/modification.blade.php ENDPATH**/ ?>