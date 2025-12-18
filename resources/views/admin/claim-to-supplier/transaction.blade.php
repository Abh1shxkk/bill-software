@extends('layouts.admin')

@section('title', 'Claim to Supplier Transaction')

@push('styles')
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
    .btn-close-custom {
        background: none; border: none; color: white; font-size: 24px; cursor: pointer; line-height: 1;
    }
    .item-modal-body { padding: 15px 20px; max-height: 60vh; overflow-y: auto; }
    .item-modal-footer { padding: 10px 20px; border-top: 1px solid #dee2e6; text-align: right; }
    
    /* Additional Details Modal Styles */
    .additional-modal {
        display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.7);
        width: 450px; max-height: 90vh; background: #f0f0f0; border: 2px solid #999;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); z-index: 99999; opacity: 0; transition: all 0.3s ease;
    }
    .additional-modal.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .additional-modal-body { padding: 15px 20px; }
    .additional-modal .field-row { display: flex; align-items: center; margin-bottom: 10px; }
    .additional-modal .field-row label { width: 180px; font-weight: 500; }
    .additional-modal .field-row input, .additional-modal .field-row select { 
        border: 1px solid #999; padding: 3px 6px; font-size: 12px; 
    }
    .additional-modal .field-row input[type="text"].small-input { width: 40px; text-align: center; }
    .additional-modal .field-row input[type="date"] { width: 120px; }
    .additional-modal .company-input { width: 120px; }
    .additional-modal .company-name { flex: 1; margin-left: 5px; border: 1px solid #999; padding: 3px 6px; background: #fff; }
    .additional-modal .ok-btn { 
        background: #e0e0e0; border: 2px outset #ccc; padding: 3px 20px; cursor: pointer; font-weight: 500;
    }
    .additional-modal .ok-btn:hover { background: #d0d0d0; }
</style>
@endpush

@section('content')
<section class="cts py-5">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-file-earmark-text me-2"></i> Claim to Supplier Transaction</h4>
                <div class="text-muted small">Create new claim to supplier transaction</div>
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
                                    <input type="date" id="claim_date" name="claim_date" class="form-control" style="width: 140px;" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="field-group mb-2">
                                    <label style="width: 50px;">Day:</label>
                                    <input type="text" id="day_name" class="form-control readonly-field" style="width: 140px;" value="{{ date('l') }}" readonly>
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
                                            <select id="supplier_id" name="supplier_id" class="form-control" required>
                                                <option value="">Select Supplier</option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
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
                            <button type="button" class="btn btn-sm btn-success" onclick="addNewRow()">
                                <i class="fas fa-plus-circle"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-sm btn-primary ms-2" onclick="showAddItemModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add Item
                            </button>
                        </div>
                    </div>

                    <!-- Calculation Section -->
                    <div class="bg-white border rounded p-2 mb-2" style="overflow: hidden;">
                        <div class="d-flex flex-wrap align-items-center gap-3" style="font-size: 11px;">
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
                                <label class="mb-0"><strong>SGST(%)</strong></label>
                                <input type="text" class="form-control readonly-field text-center" id="calc_sgst_percent" readonly style="width: 45px; height: 26px; font-size: 11px;" value="0">
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

                    <!-- Summary Section -->
                    <div class="bg-white border rounded p-2 mb-2">
                        <div class="d-flex align-items-center" style="font-size: 11px; gap: 10px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">N.T AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="nt_amount" id="ntAmount" readonly style="width: 80px; height: 26px; background: #fff3cd;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SC</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="sc_amount" id="scAmount" readonly style="width: 80px; height: 26px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">DIS AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="dis_amount" id="disAmount" readonly style="width: 80px; height: 26px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SCM AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="scm_amount" id="scmAmount" readonly style="width: 80px; height: 26px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Tax</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="tax_amount" id="taxAmount" readonly style="width: 80px; height: 26px;" value="0.00">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">INV. AMT</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" name="inv_amount" id="invAmount" readonly style="width: 80px; height: 26px;" value="0.00">
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-2" style="font-size: 11px; gap: 10px;">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Packing</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="packing" readonly style="width: 80px; height: 26px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Unit</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="unit" readonly style="width: 60px; height: 26px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Cl. Qty</label>
                                <input type="number" class="form-control form-control-sm readonly-field text-end" id="clQty" readonly style="width: 80px; height: 26px;" value="0">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Comp:</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="companyName" readonly style="width: 120px; height: 26px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">Srlno.</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="srlNo" readonly style="width: 60px; height: 26px;">
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="mb-0" style="font-weight: bold;">SCM.</label>
                                <input type="text" class="form-control form-control-sm readonly-field" id="scmField" readonly style="width: 60px; height: 26px;">
                                <span>+</span>
                                <input type="text" class="form-control form-control-sm readonly-field" id="scmField2" readonly style="width: 60px; height: 26px;">
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
@endsection

@push('scripts')
<script>
let rowIndex = 0;
let allItems = [];
let selectedItem = null;

// Additional Details Modal Data
let additionalDetails = {
    blank_statement: 'Y',
    rate_type: 'R', // P = Purchase Rate, S = Sale Rate, R = Rate Diff
    from_date: '{{ date("Y-m-d") }}',
    to_date: '{{ date("Y-m-d") }}',
    company_code: '',
    company_name: '',
    division: '00'
};

// Load companies for dropdown
let allCompanies = [];
function loadCompanies() {
    $.get("{{ route('admin.companies.index') }}", { _format: 'json' }, function(response) {
        // Companies will be loaded when modal opens
    });
}

// Show Additional Details Modal
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
                <div class="field-row additional-field-row">
                    <label>From P(urchase Rate) / S(ale Rate) / R(ate Diff.) :</label>
                    <input type="text" id="add_rate_type" class="small-input additional-field" value="${additionalDetails.rate_type}" maxlength="1" ${disabledAttr} style="${disabledStyle}">
                </div>
                <div class="field-row additional-field-row">
                    <label>From :</label>
                    <input type="date" id="add_from_date" class="additional-field" value="${additionalDetails.from_date}" ${disabledAttr} style="${disabledStyle}">
                    <label style="width: auto; margin-left: 15px; margin-right: 5px;">To :</label>
                    <input type="date" id="add_to_date" class="additional-field" value="${additionalDetails.to_date}" ${disabledAttr} style="${disabledStyle}">
                </div>
                <div class="field-row additional-field-row">
                    <label>Company :</label>
                    <select id="add_company_select" class="additional-field" ${disabledAttr} style="flex: 1; padding: 3px 6px; font-size: 12px; border: 1px solid #999; ${disabledStyle}">
                        <option value="">-- Select Company --</option>
                    </select>
                </div>
                <div class="field-row additional-field-row">
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
    
    // Load companies into dropdown
    loadCompaniesDropdown();
    
    setTimeout(() => { $('#additionalModalBackdrop, #additionalModal').addClass('show'); }, 10);
}

// Load companies into dropdown
function loadCompaniesDropdown() {
    $.ajax({
        url: "{{ route('admin.companies.get-all') }}",
        method: 'GET',
        success: function(response) {
            const companies = response.companies || response;
            const $select = $('#add_company_select');
            
            $select.find('option:not(:first)').remove();
            
            companies.forEach(function(company) {
                const selected = additionalDetails.company_code == company.id ? 'selected' : '';
                $select.append(`<option value="${company.id}" data-name="${company.name}" ${selected}>${company.name}</option>`);
            });
        },
        error: function() {
            console.log('Failed to load companies');
        }
    });
}

// Toggle additional fields based on Blank Statement value
function toggleAdditionalFields() {
    const blankValue = $('#add_blank_statement').val().toUpperCase();
    const isBlankY = blankValue === 'Y';
    
    if (isBlankY) {
        // Disable all additional fields
        $('.additional-field').prop('disabled', true).css({
            'background': '#ccc',
            'cursor': 'not-allowed'
        });
        // Clear values when disabled
        $('#add_rate_type').val('R');
        $('#add_from_date').val('{{ date("Y-m-d") }}');
        $('#add_to_date').val('{{ date("Y-m-d") }}');
        $('#add_company_select').val('');
        $('#add_division').val('00');
    } else {
        // Enable all additional fields
        $('.additional-field').prop('disabled', false).css({
            'background': '#fff',
            'cursor': 'pointer'
        });
    }
}

function closeAdditionalDetailsModal() {
    $('#additionalModalBackdrop, #additionalModal').removeClass('show');
    setTimeout(() => { $('#additionalModal, #additionalModalBackdrop').remove(); }, 300);
}

function saveAdditionalDetails() {
    additionalDetails.blank_statement = $('#add_blank_statement').val().toUpperCase();
    additionalDetails.rate_type = $('#add_rate_type').val().toUpperCase();
    additionalDetails.from_date = $('#add_from_date').val();
    additionalDetails.to_date = $('#add_to_date').val();
    additionalDetails.company_code = $('#add_company_select').val();
    additionalDetails.company_name = $('#add_company_select option:selected').data('name') || '';
    additionalDetails.company_name = $('#add_company_name').val();
    additionalDetails.division = $('#add_division').val();
    
    closeAdditionalDetailsModal();
    console.log('Additional Details saved:', additionalDetails);
}

// Show Add Item Modal (with filters from Additional Details)
function showAddItemModal() {
    const supplierId = $('#supplier_id').val();
    if (!supplierId) {
        alert('Please select a supplier first');
        return;
    }
    
    // Build query params from additional details
    let params = { supplier_id: supplierId };
    if (additionalDetails.company_code) params.company_code = additionalDetails.company_code;
    if (additionalDetails.division && additionalDetails.division !== '00') params.division = additionalDetails.division;
    if (additionalDetails.from_date) params.from_date = additionalDetails.from_date;
    if (additionalDetails.to_date) params.to_date = additionalDetails.to_date;
    
    // Load items with filters
    $.get("{{ route('admin.items.get-all') }}", params, function(data) {
        allItems = data.items || data;
        showItemSelectionModal(allItems);
    }).fail(function() {
        alert('Failed to load items');
    });
}

$(document).ready(function() {
    loadNextTransactionNumber();
    // Don't add row initially - start empty
    
    $('#claim_date').on('change', function() {
        const date = new Date($(this).val());
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $('#day_name').val(days[date.getDay()]);
    });
    
    $('#saveBtn').on('click', saveTransaction);
});

function loadNextTransactionNumber() {
    $.get("{{ route('admin.claim-to-supplier.next-trn-no') }}", function(response) {
        $('#trn_no').val(response.next_trn_no);
    });
}

// Add Row - Opens item selection modal (uses showAddItemModal)
function addNewRow() {
    showAddItemModal();
}

// Show item selection modal
function showItemSelectionModal(items) {
    let itemsHtml = items.map((item, index) => `
        <tr class="item-row" data-item-name="${(item.name || '').toLowerCase()}" data-item-code="${(item.code || '').toLowerCase()}">
            <td style="text-align: center;">${index + 1}</td>
            <td>${item.code || ''}</td>
            <td>${item.name || ''}</td>
            <td>${item.company_name || ''}</td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-sm btn-primary" onclick='selectItem(${JSON.stringify(item).replace(/'/g, "\\'")})'
                    style="font-size: 9px; padding: 2px 8px;">Select</button>
            </td>
        </tr>
    `).join('');
    
    const modalHTML = `
        <div class="modal-backdrop-custom" id="itemModalBackdrop" onclick="closeItemModal()"></div>
        <div class="item-modal" id="itemModal">
            <div class="item-modal-header">
                <h5><i class="bi bi-box-seam me-2"></i>Select Item</h5>
                <button type="button" class="btn-close-custom" onclick="closeItemModal()">&times;</button>
            </div>
            <div class="item-modal-body">
                <div style="margin-bottom: 10px;">
                    <input type="text" id="itemSearchInput" class="form-control form-control-sm" 
                           placeholder="Search by item name or code..." onkeyup="filterItems()" style="font-size: 11px;">
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
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
            </div>
        </div>
    `;
    
    $('#itemModal, #itemModalBackdrop').remove();
    $('body').append(modalHTML);
    setTimeout(() => { $('#itemModalBackdrop, #itemModal').addClass('show'); }, 10);
}

function filterItems() {
    const searchValue = $('#itemSearchInput').val().toLowerCase();
    $('.item-row').each(function() {
        const name = $(this).data('item-name');
        const code = $(this).data('item-code');
        $(this).toggle(name.includes(searchValue) || code.includes(searchValue));
    });
}

function closeItemModal() {
    $('#itemModalBackdrop, #itemModal').removeClass('show');
    setTimeout(() => { $('#itemModal, #itemModalBackdrop').remove(); }, 300);
}

function selectItem(item) {
    if (typeof item === 'string') item = JSON.parse(item);
    selectedItem = item;
    closeItemModal();
    loadBatchesForItem(item.id);
}

function loadBatchesForItem(itemId) {
    const supplierId = $('#supplier_id').val();
    $.get("{{ route('admin.claim-to-supplier.batches') }}", { supplier_id: supplierId, item_id: itemId }, function(response) {
        if (response.batches && response.batches.length > 0) {
            showBatchSelectionModal(response.batches);
        } else {
            alert('No batches found for this item');
        }
    });
}

function showBatchSelectionModal(batches) {
    let batchesHtml = batches.map((batch, index) => `
        <tr class="batch-row">
            <td style="text-align: center;">${index + 1}</td>
            <td>${batch.batch_no || ''}</td>
            <td>${batch.expiry ? new Date(batch.expiry).toLocaleDateString('en-GB', {month: '2-digit', year: '2-digit'}) : ''}</td>
            <td class="text-end">${batch.available_qty || 0}</td>
            <td class="text-end">${parseFloat(batch.purchase_rate || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(batch.mrp || 0).toFixed(2)}</td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-sm btn-success" onclick='selectBatch(${JSON.stringify(batch).replace(/'/g, "\\'")})'
                    style="font-size: 9px; padding: 2px 8px;">Select</button>
            </td>
        </tr>
    `).join('');
    
    const modalHTML = `
        <div class="modal-backdrop-custom" id="batchModalBackdrop" onclick="closeBatchModal()"></div>
        <div class="item-modal" id="batchModal">
            <div class="item-modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <h5><i class="bi bi-layers me-2"></i>Select Batch for: ${selectedItem.name}</h5>
                <button type="button" class="btn-close-custom" onclick="closeBatchModal()">&times;</button>
            </div>
            <div class="item-modal-body">
                <div style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" style="font-size: 10px; margin-bottom: 0;">
                        <thead style="position: sticky; top: 0; background: #28a745; color: white; z-index: 10;">
                            <tr>
                                <th style="width: 35px; text-align: center;">S.N</th>
                                <th style="width: 80px;">Batch No</th>
                                <th style="width: 70px;">Expiry</th>
                                <th style="width: 60px; text-align: right;">Avl Qty</th>
                                <th style="width: 70px; text-align: right;">Rate</th>
                                <th style="width: 70px; text-align: right;">MRP</th>
                                <th style="width: 80px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>${batchesHtml}</tbody>
                    </table>
                </div>
            </div>
            <div class="item-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Close</button>
            </div>
        </div>
    `;
    
    $('#batchModal, #batchModalBackdrop').remove();
    $('body').append(modalHTML);
    setTimeout(() => { $('#batchModalBackdrop, #batchModal').addClass('show'); }, 10);
}

function closeBatchModal() {
    $('#batchModalBackdrop, #batchModal').removeClass('show');
    setTimeout(() => { $('#batchModal, #batchModalBackdrop').remove(); }, 300);
}

function selectBatch(batch) {
    if (typeof batch === 'string') batch = JSON.parse(batch);
    closeBatchModal();
    addItemRow(selectedItem, batch);
}

function addItemRow(item, batch) {
    const expiry = batch.expiry ? new Date(batch.expiry).toLocaleDateString('en-GB', {month: '2-digit', year: '2-digit'}) : '';
    const row = `
        <tr data-row="${rowIndex}">
            <td><input type="text" class="form-control item-code" data-row="${rowIndex}" value="${item.code || ''}" readonly></td>
            <td><input type="text" class="form-control item-name" data-row="${rowIndex}" value="${item.name || ''}" readonly></td>
            <td><input type="text" class="form-control batch-no" data-row="${rowIndex}" value="${batch.batch_no || ''}" readonly></td>
            <td><input type="text" class="form-control expiry" data-row="${rowIndex}" value="${expiry}" readonly></td>
            <td><input type="number" class="form-control qty" data-row="${rowIndex}" value="1" min="0"></td>
            <td><input type="number" class="form-control free-qty" data-row="${rowIndex}" value="0" min="0"></td>
            <td><input type="number" class="form-control rate" data-row="${rowIndex}" value="${batch.purchase_rate || 0}" step="0.01"></td>
            <td><input type="number" class="form-control dis-percent" data-row="${rowIndex}" value="0" step="0.01"></td>
            <td><input type="number" class="form-control amount" data-row="${rowIndex}" value="${batch.purchase_rate || 0}" step="0.01" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
    $('#itemsTableBody').append(row);
    
    const $row = $(`tr[data-row="${rowIndex}"]`);
    $row.data('item_id', item.id);
    $row.data('batch_id', batch.batch_id);
    
    // Update info fields
    $('#packing').val(batch.packing || item.packing || '');
    $('#unit').val(batch.unit || item.unit || '');
    $('#clQty').val(batch.total_cl_qty || 0);
    $('#companyName').val(batch.company_name || item.company_name || '');
    $('#calc_cgst_percent').val(batch.cgst_percent || item.cgst_percent || 0);
    $('#calc_sgst_percent').val(batch.sgst_percent || item.sgst_percent || 0);
    $('#calc_ws_rate').val(batch.ws_rate || 0);
    $('#calc_s_rate').val(batch.s_rate || 0);
    
    rowIndex++;
    calculateTotals();
}

function removeRow(row) {
    $(`tr[data-row="${row}"]`).remove();
    calculateTotals();
}

$(document).on('change', '.qty, .free-qty, .rate, .dis-percent', function() {
    const $row = $(this).closest('tr');
    calculateRowAmount($row);
    calculateTotals();
});

function calculateRowAmount($row) {
    const qty = parseFloat($row.find('.qty').val()) || 0;
    const rate = parseFloat($row.find('.rate').val()) || 0;
    const disPercent = parseFloat($row.find('.dis-percent').val()) || 0;
    
    let amount = qty * rate;
    if (disPercent > 0) {
        amount = amount - (amount * disPercent / 100);
    }
    $row.find('.amount').val(amount.toFixed(2));
}

function calculateTotals() {
    let ntAmount = 0;
    let taxAmount = 0;
    
    $('#itemsTableBody tr').each(function() {
        const amount = parseFloat($(this).find('.amount').val()) || 0;
        ntAmount += amount;
    });
    
    const cgstPercent = parseFloat($('#calc_cgst_percent').val()) || 0;
    const sgstPercent = parseFloat($('#calc_sgst_percent').val()) || 0;
    taxAmount = ntAmount * (cgstPercent + sgstPercent) / 100;
    
    $('#ntAmount').val(ntAmount.toFixed(2));
    $('#taxAmount').val(taxAmount.toFixed(2));
    $('#invAmount').val((ntAmount + taxAmount).toFixed(2));
}

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
        _token: '{{ csrf_token() }}',
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
        items: items
    };
    
    $.ajax({
        url: "{{ route('admin.claim-to-supplier.store') }}",
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
</script>
@endpush
