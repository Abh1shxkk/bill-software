@extends('layouts.admin')

@section('title', 'Sale Return Replacement Transaction')

@section('content')
<style>
    /* Compact form adjustments - matching Sale Module */
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
    
    .readonly-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-arrow-return-left me-2"></i> Sale Return Replacement Transaction</h4>
        <div class="text-muted small">Create new sale return replacement transaction</div>
    </div>
    <div>
        <a href="{{ route('admin.sale-return-replacement.index') }}" class="btn btn-primary">
            <i class="bi bi-receipt-cutoff me-1"></i> View All Transactions
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form id="saleReturnReplacementForm" method="POST" autocomplete="off" onsubmit="return false;">
            @csrf
            
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
                        <input type="date" class="form-control" name="trn_date" id="trn_date" value="{{ date('Y-m-d') }}" style="width: 140px;" onchange="updateDayName()">
                        <input type="text" class="form-control readonly-field" id="dayName" value="{{ date('l') }}" readonly style="width: 90px;">
                    </div>
                    
                    <div class="field-group">
                        <label>Customer:</label>
                        <div style="position: relative; width: 250px;" id="customerDropdownWrapper">
                            <input type="text" id="customerSearchInput" class="form-control" placeholder="Search customer..." autocomplete="off" style="width: 250px;" onblur="_onCustomerBlur()" oninput="_filterCustomers()">
                            <div id="customerDropList" style="display:none; position:absolute; z-index:99999; top:100%; left:0; width:100%; max-height:220px; overflow-y:auto; background:white; border:1px solid #ccc; box-shadow:0 4px 8px rgba(0,0,0,.15);">
                                @foreach($customers as $customer)
                                <div class="customer-drop-item" data-id="{{ $customer->id }}" data-label="{{ ($customer->code ?? '') . ' - ' . $customer->name }}" style="padding:5px 10px; cursor:pointer; font-size:11px;" onmousedown="_selectCustomerItem(this)">{{ ($customer->code ?? '') }} - {{ $customer->name }}</div>
                                @endforeach
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
                            <input type="text" class="form-control readonly-field" name="trn_no" id="trnNo" value="{{ $nextTrnNo }}" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
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

            
            <!-- Calculation Section (matching Sale module structure) -->
            <div class="bg-white border rounded p-3 mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="d-flex align-items-start gap-3 border rounded p-2" style="font-size: 11px; background: #fafafa;">
                    <!-- Left Section -->
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
                    
                    <!-- Right Side -->
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
            
            <!-- Summary Section (matching Sale module - pink background) -->
            <div class="bg-white border rounded p-2 mb-2" style="background: #ffcccc;">
                <!-- Row 1: 7 fields -->
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
                
                <!-- Row 2: Only Scm.% -->
                <div class="d-flex align-items-center mt-2" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.%</label>
                        <input type="number" class="form-control form-control-sm text-end" id="scm_percent" name="scm_percent" step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>
            </div>
            
            <!-- Detailed Info Section (matching Sale module - orange background) -->
            <div class="bg-white border rounded p-2 mb-2" style="background: #ffe6cc;">
                <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                    <!-- Row 1 -->
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
                    
                    <!-- Row 2 -->
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
                    
                    <!-- Row 3 -->
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
                    
                    <!-- Row 4 -->
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
                <button type="button" class="btn btn-primary btn-sm" id="saveBtn">
                    <i class="bi bi-save"></i> Save
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.reload()">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// ─── Customer custom dropdown ───────────────────────────────────────────────
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

function _openCustomerDrop() {
    _filterCustomers();
}

function _closeCustomerDrop() {
    document.getElementById('customerDropList').style.display = 'none';
}

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
    // Move to Cash after selection
    setTimeout(() => { document.getElementById('is_cash_display').focus(); }, 50);
}

function _onCustomerBlur() {
    setTimeout(_closeCustomerDrop, 150);
}

// ─── Item count for row naming ───────────────────────────────────────────────
let _rowCount = 0;

// ─── Open item modal ─────────────────────────────────────────────────────────
function _openItemModal() {
    if (typeof openItemModal_saleReturnItemModal === 'function') {
        openItemModal_saleReturnItemModal();
    }
}

// ─── Modal bridge callbacks ───────────────────────────────────────────────────
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
    const idx   = _rowCount;

    const expiryRaw     = batch?.expiry_date || '';   // Y-m-d for DB
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
        <td><input type="text"   class="form-control item-name readonly-field" name="items[${idx}][item_name]" value="${escHtml(item.name)}" readonly>
            <input type="hidden" name="items[${idx}][batch_id]"   value="${batch?.id || ''}">
            <input type="hidden" name="items[${idx}][packing]"    value="${escHtml(item.packing || '')}">
            <input type="hidden" name="items[${idx}][company]"    value="${escHtml(item.company_short_name || item.mfg_by || '')}">
            <input type="hidden" name="items[${idx}][mrp]"        value="${mrp}">
            <input type="hidden" name="items[${idx}][unit]"       value="${escHtml(item.unit || '1')}">
            <input type="hidden" name="items[${idx}][cl_qty]"     value="${clQty}">
        </td>
        <td><input type="text"   class="form-control readonly-field" name="items[${idx}][batch_no]"     value="${escHtml(batch?.batch_no || '')}" readonly></td>
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

    _rowCount++;
    _calcTotals();

    // Focus qty of the new row
    setTimeout(() => {
        const qtyInput = tr.querySelector('.qty');
        if (qtyInput) { qtyInput.focus(); qtyInput.select(); }
    }, 60);
}

function escHtml(t) {
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
}

// ─── Row calc (delegated via jQuery, kept for compatibility) ─────────────────
$(document).on('input', '.qty, .sale-rate, .dis-percent', function() {
    const row    = $(this).closest('tr');
    const qty    = parseFloat(row.find('.qty').val())         || 0;
    const rate   = parseFloat(row.find('.sale-rate').val())   || 0;
    const dis    = parseFloat(row.find('.dis-percent').val()) || 0;
    const gross  = qty * rate;
    const disAmt = gross * (dis / 100);
    row.find('.amount').val((gross - disAmt).toFixed(2));
    _calcTotals();
});

$(document).on('click', '.remove-row', function() {
    if ($('#itemsTableBody tr').length > 1) {
        $(this).closest('tr').remove();
    }
    _calcTotals();
});

$('#addRowBtn, #addRowBtn2').click(function() { _openItemModal(); });

function _calcTotals() {
    let total = 0;
    $('#itemsTableBody tr').each(function() {
        total += parseFloat($(this).find('.amount').val()) || 0;
    });
    const scPercent  = parseFloat($('#sc_percent').val())  || 0;
    const taxPercent = parseFloat($('#tax_percent').val()) || 0;
    const disAmt     = parseFloat($('#dis_amt').val())     || 0;
    const scmAmt     = parseFloat($('#scm_amt').val())     || 0;
    const scAmt      = total * (scPercent / 100);
    const taxAmt     = total * (taxPercent / 100);
    const net        = total + scAmt + taxAmt - disAmt - scmAmt;
    $('#nt_amt').val(total.toFixed(2));
    $('#ft_amt').val(total.toFixed(2));
    $('#sc_amt').val(scAmt.toFixed(2));
    $('#tax_amt').val(taxAmt.toFixed(2));
    $('#net_amt').val(net.toFixed(2));
}
calculateTotals = _calcTotals; // expose as global alias

$('#sc_percent, #tax_percent, #dis_amt, #scm_amt').on('input', _calcTotals);

// ─── Day name ────────────────────────────────────────────────────────────────
window.updateDayName = function() {
    const date = new Date($('#trn_date').val());
    $('#dayName').val(date.toLocaleDateString('en-US', { weekday: 'long' }));
};

// ─── Save ────────────────────────────────────────────────────────────────────
function _save() {
    $('#saveBtn').click();
}

$('#saveBtn').click(function() {
    // ── Pre-save validation ──
    const custId = document.getElementById('customer_id').value;
    if (!custId) {
        alert('Please select a customer.');
        document.getElementById('customerSearchInput').focus();
        return;
    }
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length === 0) {
        alert('Please add at least one item.');
        return;
    }
    let hasValidRow = false;
    rows.forEach(r => {
        if (r.querySelector('[name*="[item_id]"]')?.value) hasValidRow = true;
    });
    if (!hasValidRow) {
        alert('Please add at least one item.');
        return;
    }
    // ── Fire ──
    $.ajax({
        url: "{{ route('admin.sale-return-replacement.store') }}",
        method: 'POST',
        data: $('#saleReturnReplacementForm').serialize(),
        success: function(response) {
            if (response.success) {
                alert(response.message);
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            let msg = 'Error saving transaction';
            try {
                const resp = JSON.parse(xhr.responseText);
                if (xhr.status === 422 && resp.errors) {
                    // Laravel validation errors
                    msg = Object.values(resp.errors).flat().join('\n');
                } else {
                    msg = resp.message || resp.error || JSON.stringify(resp);
                }
            } catch(e) {
                msg = xhr.responseText?.substring(0, 300) || msg;
            }
            alert('Error (' + xhr.status + '):\n' + msg);
        }
    });
});

// ─── Cash custom dropdown ─────────────────────────────────────────────────────
let _cashHighlightIdx = -1;

function _toggleCashDrop() {
    const dl = document.getElementById('cashDropList');
    if (dl.style.display === 'none') {
        dl.style.display = 'block';
        // Highlight current value
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

function _closeCashDrop() {
    document.getElementById('cashDropList').style.display = 'none';
}

function _selectCashItem(el) {
    document.getElementById('is_cash_display').value = el.dataset.value;
    document.getElementById('is_cash').value         = el.dataset.value;
    _closeCashDrop();
    _cashHighlightIdx = -1;
    // Move to Fixed Dis
    setTimeout(() => {
        const fd = document.getElementById('fixed_discount');
        fd.focus(); fd.select();
    }, 50);
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
            // Just move forward if already selected
            const fd = document.getElementById('fixed_discount');
            fd.focus(); fd.select();
        }
        return;
    }
    // Press N or Y directly
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

// Close cash drop when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#cashDropdownWrapper')) _closeCashDrop();
});


function _anyModalOpen() {
    return (
        !!document.querySelector('#saleReturnItemModal.show')  ||
        !!document.querySelector('#saleReturnBatchModal.show')
    );
}

// ============================================================
// MASTER KEYBOARD HANDLER — window capture phase
// Flow: Date → Customer → Cash → Fixed Dis → Remarks → Item Modal
// ============================================================
window.addEventListener('keydown', function(e) {

    // ── Cash dropdown arrow/enter/escape (must check BEFORE generic Enter) ──
    const cashList = document.getElementById('cashDropList');
    const cashOpen = cashList && cashList.style.display !== 'none';

    if (cashOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter' || e.key === 'Escape')) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _cashKeydown(e);
        return;
    }

    // ── Customer dropdown arrow/enter/escape ──────────────────
    const dropList = document.getElementById('customerDropList');
    const dropOpen = dropList && dropList.style.display !== 'none';

    if (dropOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter' || e.key === 'Escape')) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

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

    // ── Skip if a modal is open ───────────────────────────────
    if (_anyModalOpen()) return;

    if (e.key === 'Enter') {
        const el = document.activeElement;
        if (!el) return;

        // Date → open customer dropdown
        if (el.id === 'trn_date') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const inp = document.getElementById('customerSearchInput');
            inp.focus();
            inp.select();
            _openCustomerDrop();
            return;
        }

        // Customer search input → open/navigate dropdown
        if (el.id === 'customerSearchInput') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            _openCustomerDrop();
            const visible = [...document.querySelectorAll('#customerDropList .customer-drop-item')]
                                .filter(x => x.style.display !== 'none');
            if (visible.length === 1) {
                _selectCustomerItem(visible[0]);
            } else {
                _customerHighlightIdx = 0;
                _highlightCustomer(0);
            }
            return;
        }

        // Cash → Fixed Dis
        if (el.id === 'is_cash_display') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const fd = document.getElementById('fixed_discount');
            fd.focus(); fd.select();
            return;
        }

        // Fixed Dis → Remarks
        if (el.id === 'fixed_discount') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            document.getElementById('remarks').focus();
            return;
        }

        // Remarks → Open item modal
        if (el.id === 'remarks') {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            _openItemModal();
            return;
        }


        // ── Table row navigation: Qty → F.Qty → Rate → Dis% → FTRate → (next row Qty / Item Modal) ──

        // Helper: focus + select an input
        function _focusInput(inp) { if (inp) { inp.focus(); inp.select(); } }

        if (el.classList.contains('qty')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            _focusInput(el.closest('tr')?.querySelector('.f-qty'));
            return;
        }

        if (el.classList.contains('f-qty')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            _focusInput(el.closest('tr')?.querySelector('.sale-rate'));
            return;
        }

        if (el.classList.contains('sale-rate')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            _focusInput(el.closest('tr')?.querySelector('.dis-percent'));
            return;
        }

        if (el.classList.contains('dis-percent')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            _focusInput(el.closest('tr')?.querySelector('.ft-rate'));
            return;
        }

        if (el.classList.contains('ft-rate')) {
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const nextTr = el.closest('tr')?.nextElementSibling;
            if (nextTr && nextTr.querySelector('.qty')) {
                _focusInput(nextTr.querySelector('.qty'));
            } else {
                _openItemModal();
            }
            return;
        }
    }

    // ── Customer search input: open on focus keystrokes ───────
    if (document.activeElement?.id === 'customerSearchInput' && !dropOpen) {
        if (e.key.length === 1 || e.key === 'Backspace') {
            setTimeout(() => {
                if (document.getElementById('customerSearchInput').value.length >= 0) {
                    document.getElementById('customerDropList').style.display = 'block';
                }
            }, 10);
        }
    }

    // ── Ctrl+S → Save ─────────────────────────────────────────
    if (e.key === 's' && e.ctrlKey && !e.shiftKey && !e.altKey) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
        _save();
        return;
    }

}, true); // capture phase

// ── On page load ──────────────────────────────────────────────────────────────
$(document).ready(function() {
    // Focus date on load
    setTimeout(() => document.getElementById('trn_date')?.focus(), 100);
});
</script>
@endpush
@include('components.modals.item-selection', [
    'id'           => 'saleReturnItemModal',
    'module'       => 'sale-return-replacement',
    'showStock'    => true,
    'rateType'     => 'sale',
    'showCompany'  => true,
    'showHsn'      => false,
    'batchModalId' => 'saleReturnBatchModal',
])

@include('components.modals.batch-selection', [
    'id'                => 'saleReturnBatchModal',
    'module'            => 'sale-return-replacement',
    'showOnlyAvailable' => false,
    'rateType'          => 'sale',
    'showCostDetails'   => false,
])

@endsection