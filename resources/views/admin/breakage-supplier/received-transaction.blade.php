@extends('layouts.admin')

@section('title', 'Purchase Return Br.Expiry Adjustment')

@push('styles')
<style>
    .bsi-form { font-size: 11px; }
    .bsi-form label { font-weight: 600; font-size: 11px; margin-bottom: 0; }
    .bsi-form input, .bsi-form select { font-size: 11px; padding: 2px 6px; height: 26px; }
    .header-section { background: #fff; border: 1px solid #ccc; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    .field-group { display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }
    .readonly-field { background-color: #e9ecef !important; }
    .inner-card { background: #e8f4f8; border: 1px solid #b8d4e0; padding: 8px; border-radius: 3px; }
    
    /* Claim Section */
    .claim-section { background: #fff; border: 1px solid #ccc; padding: 8px; margin-bottom: 6px; border-radius: 4px; }
    
    /* HSN Table Styles - Brown Header (matching issued-transaction) */
    .hsn-table { font-size: 11px; margin-bottom: 0; border-collapse: collapse; width: 100%; }
    .hsn-table th { background: linear-gradient(180deg, #8B4513 0%, #654321 100%); color: #fff; font-weight: 600; text-align: center; padding: 4px 3px; border: 1px solid #5a3a1a; white-space: nowrap; font-size: 11px; }
    .hsn-table td { padding: 3px; border: 1px solid #ccc; background: #fffacd; }
    .hsn-table input { font-size: 11px; padding: 2px 4px; height: 24px; border: 1px solid #aaa; width: 100%; }
    .hsn-table .row-selected td { background: #cce5ff !important; }
    
    /* Summary Section - Pink */
    .summary-section { background: #ffcccc; padding: 8px; border: 1px solid #cc9999; margin-bottom: 6px; border-radius: 3px; }
    .summary-section label { font-weight: bold; font-size: 11px; }
    .summary-section input { height: 24px; font-size: 11px; }
    
    /* Action Buttons */
    .action-buttons { display: flex; gap: 8px; justify-content: center; margin-top: 10px; }
    .action-buttons .btn { min-width: 80px; }
    
    /* Modal Styles */
    .modal-backdrop-custom { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; }
    .modal-backdrop-custom.show { display: block; }
    .custom-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 700px; background: #fff; border-radius: 6px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); z-index: 1055; }
    .custom-modal.show { display: block; }
    .modal-header-custom { padding: 10px 15px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-radius: 6px 6px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-custom { padding: 12px; max-height: 400px; overflow-y: auto; }
    .modal-footer-custom { padding: 8px 12px; border-top: 1px solid #ddd; text-align: right; }
    .claim-row:hover { background: #e3f2fd !important; cursor: pointer; }
    .claim-row.selected { background: #007bff !important; color: #fff !important; }
</style>
@endpush

@section('content')
<div class="bsi-form">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0"><i class="bi bi-arrow-return-left me-1"></i> Purchase Return Br.Expiry Adjustment</h6>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-info btn-sm py-0" onclick="showClaimModal()">
                <i class="bi bi-file-earmark-text me-1"></i> Load Claim
            </button>
            <a href="{{ route('admin.breakage-supplier.received-modification') }}" class="btn btn-outline-secondary btn-sm py-0">
                <i class="bi bi-list"></i> Modification
            </a>
        </div>
    </div>

    <form id="receivedForm" autocomplete="off">
        @csrf
        <input type="hidden" id="claim_transaction_id" name="claim_transaction_id" value="">
        
        <!-- Header Section Row 1 -->
        <div class="header-section">
            <div class="row g-2">
                <div class="col-md-2">
                    <div class="field-group">
                        <label style="width:35px;">Date</label>
                        <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="field-group">
                        <label style="width:35px;">Trn.No.</label>
                        <input type="text" id="trn_no" name="trn_no" class="form-control readonly-field" value="{{ $trnNo ?? '' }}" readonly style="width:80px;">
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="inner-card">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label style="width:55px;">Supplier</label>
                                    <select id="supplier_id" name="supplier_id" class="form-control" onchange="updateSupplierName()">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers ?? [] as $s)
                                        <option value="{{ $s->supplier_id }}" data-name="{{ $s->name }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="supplier_name" name="supplier_name" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width:70px;">Party Trn No.</label>
                                    <input type="text" id="party_trn_no" name="party_trn_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label style="width:55px;">Party Date</label>
                                    <input type="date" id="party_date" name="party_date" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Claim Section Row 2 -->
        <div class="claim-section">
            <div class="inner-card">
                <div class="row g-2 align-items-center">
                    <div class="col-md-2">
                        <div class="field-group">
                            <label style="width:65px;">O/S.Amount</label>
                            <input type="number" id="os_amount" name="os_amount" class="form-control readonly-field text-end" value="0.00" readonly step="0.01">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="field-group">
                            <label style="width:70px;">Claim [Y/N]</label>
                            <input type="text" id="claim_flag" name="claim_flag" class="form-control text-center" value="Y" maxlength="1" style="width:30px;">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="received_as_debit_note" name="received_as_debit_note">
                            <label class="form-check-label" for="received_as_debit_note">Received as Debit Note</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="field-group">
                            <label style="width:75px;">Claim Amount</label>
                            <input type="number" id="claim_amount" name="claim_amount" class="form-control text-end" value="0.00" step="0.01" onchange="calculateFromClaimAmount()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="field-group">
                            <label style="width:55px; color:red;">Claim Trn.</label>
                            <input type="text" id="claim_trn_no" name="claim_trn_no" class="form-control readonly-field" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HSN Table Section -->
        <div class="bg-white border rounded p-2 mb-2">
            <div class="d-flex justify-content-between mb-2">
                <button type="button" class="btn btn-info btn-sm" onclick="showHsnModal()">
                    <i class="bi bi-plus-circle me-1"></i> Add HSN Code
                </button>
                <div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedRow()">Delete Row</button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="clearAllRows()">Clear All</button>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="hsn-table" id="hsnTable" style="table-layout: fixed; width: 100%;">
                    <thead style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width:120px;">HSN Code</th>
                            <th style="width:100px;">Amount</th>
                            <th style="width:80px;">GST%</th>
                            <th style="width:80px;">IGST %</th>
                            <th style="width:100px;">GST Amount</th>
                            <th style="width:60px;">Qty.</th>
                        </tr>
                    </thead>
                    <tbody id="hsnTableBody">
                        <!-- HSN rows will be added dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="row g-2">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Gross Amt.</label>
                        <input type="number" id="gross_amt" name="gross_amt" class="form-control readonly-field text-end" value="0.00" readonly>
                    </div>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-2">
                    <div class="field-group">
                        <input type="number" id="total_gst_amt" name="total_gst_amt" class="form-control readonly-field text-end" value="0.00" readonly>
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Total GST</label>
                        <input type="number" id="total_gst" name="total_gst" class="form-control readonly-field text-end" value="0.00" readonly>
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Net Amt.</label>
                        <input type="number" id="net_amt" name="net_amt" class="form-control readonly-field text-end fw-bold" value="0.00" readonly>
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Round Off</label>
                        <input type="number" id="round_off" name="round_off" class="form-control text-end" value="0.00" step="0.01">
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-2">
                    <div class="field-group">
                        <label>Amount</label>
                        <input type="number" id="final_amount" name="final_amount" class="form-control readonly-field text-end fw-bold text-success" value="0.00" readonly>
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-8">
                    <div class="field-group">
                        <label>Remarks</label>
                        <input type="text" id="remarks" name="remarks" class="form-control" placeholder="Enter remarks...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="button" class="btn btn-success btn-sm" onclick="saveTransaction()">
                <i class="bi bi-check-lg me-1"></i> Ok
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelTransaction()">
                <i class="bi bi-x-lg me-1"></i> Close
            </button>
        </div>
    </form>
</div>

<!-- Load Claim Modal -->
<div class="modal-backdrop-custom" id="claimModalBackdrop" onclick="closeClaimModal()"></div>
<div class="custom-modal" id="claimModal">
    <div class="modal-header-custom">
        <h6 class="mb-0"><i class="bi bi-file-earmark-text me-1"></i> Load Claim to Supplier Transaction</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeClaimModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="claimSearchInput" class="form-control form-control-sm mb-2" placeholder="Search by Claim No or Supplier..." onkeyup="filterClaims()">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Claim No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th class="text-end">Net Amount</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody id="claimsListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeClaimModal()">Close</button>
    </div>
</div>

<!-- Adjustment Modal -->
<div class="modal-backdrop-custom" id="adjustBackdrop"></div>
<div class="custom-modal" id="adjustModal" style="max-width: 800px;">
    <div class="modal-header-custom" style="background: #6f42c1;">
        <h6 class="mb-0"><i class="bi bi-credit-card me-1"></i> Purchase Adjustment</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeAdjustModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered mb-0" style="font-size: 11px;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>SR.</th>
                        <th>TRANS NO.</th>
                        <th>DATE</th>
                        <th class="text-end">BILL AMT.</th>
                        <th class="text-end">BALANCE</th>
                        <th class="text-end">ADJUST AMT.</th>
                    </tr>
                </thead>
                <tbody id="adjustTableBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom" style="background: #f0f0f0;">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <span class="text-muted small">ESC to close</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span>Total: <strong class="text-success" id="adjustTotalDisplay">₹0.00</strong></span>
                <span>Adjusted: <strong class="text-primary" id="adjustedDisplay">₹0.00</strong></span>
                <span>Balance: <strong class="text-danger" id="adjustBalanceDisplay">₹0.00</strong></span>
            </div>
            <div>
                <button type="button" class="btn btn-success btn-sm" onclick="saveWithAdjustments()">
                    <i class="bi bi-check-circle me-1"></i> Save
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustModal()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- HSN Code Selection Modal -->
<div class="modal-backdrop-custom" id="hsnModalBackdrop" onclick="closeHsnModal()"></div>
<div class="custom-modal" id="hsnModal">
    <div class="modal-header-custom" style="background: linear-gradient(135deg, #28a745, #20c997);">
        <h6 class="mb-0"><i class="bi bi-grid me-1"></i> Select HSN Code</h6>
        <button type="button" class="btn btn-sm btn-light" onclick="closeHsnModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        <input type="text" id="hsnSearchInput" class="form-control form-control-sm mb-2" placeholder="Search by HSN Code or Name..." onkeyup="filterHsnCodes()">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 11px;">
                <thead class="table-success sticky-top">
                    <tr>
                        <th>HSN Code</th>
                        <th>Name</th>
                        <th class="text-end">CGST%</th>
                        <th class="text-end">SGST%</th>
                        <th class="text-end">Total GST%</th>
                    </tr>
                </thead>
                <tbody id="hsnListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeHsnModal()">Close</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
let allClaims = [];
let allHsnCodes = [];
let hsnRowIndex = 0;
let selectedHsnRowIndex = null;
let pendingTransactionData = null;

document.addEventListener('DOMContentLoaded', function() {
    loadClaims();
    loadHsnCodes();
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeClaimModal(); closeAdjustModal(); closeHsnModal(); }
    });
});

// Update supplier name from dropdown
function updateSupplierName() {
    const select = document.getElementById('supplier_id');
    const selectedOption = select.options[select.selectedIndex];
    document.getElementById('supplier_name').value = selectedOption ? selectedOption.dataset.name || '' : '';
}

// Load Claim to Supplier Transactions
function loadClaims() {
    fetch('{{ route("admin.claim-to-supplier.past-claims") }}')
        .then(r => r.json())
        .then(data => {
            console.log('Claims API Response:', data);
            // API returns {success: true, transactions: [...]}
            if (data.success && data.transactions) {
                allClaims = data.transactions;
            } else {
                allClaims = [];
            }
            renderClaimsList(allClaims);
        })
        .catch(e => console.error('Error loading claims:', e));
}

function showClaimModal() {
    document.getElementById('claimModalBackdrop').classList.add('show');
    document.getElementById('claimModal').classList.add('show');
    document.getElementById('claimSearchInput').value = '';
    renderClaimsList(allClaims);
    setTimeout(() => document.getElementById('claimSearchInput').focus(), 100);
}

function closeClaimModal() {
    document.getElementById('claimModalBackdrop').classList.remove('show');
    document.getElementById('claimModal').classList.remove('show');
}

function filterClaims() {
    const search = document.getElementById('claimSearchInput').value.toLowerCase();
    const filtered = allClaims.filter(c => 
        (c.claim_no && c.claim_no.toLowerCase().includes(search)) ||
        (c.supplier_name && c.supplier_name.toLowerCase().includes(search))
    );
    renderClaimsList(filtered);
}

function renderClaimsList(claims) {
    const tbody = document.getElementById('claimsListBody');
    if (!claims || !claims.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No claims found</td></tr>';
        return;
    }
    tbody.innerHTML = claims.map(c => `
        <tr class="claim-row" onclick="selectClaim(${c.id})">
            <td><strong>${c.claim_no || ''}</strong></td>
            <td>${c.claim_date || ''}</td>
            <td>${c.supplier_name || ''}</td>
            <td class="text-end">₹${parseFloat(c.net_amount || c.amount || 0).toFixed(2)}</td>
            <td class="text-end text-primary">₹${parseFloat(c.balance_amount || c.net_amount || c.amount || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function selectClaim(claimId) {
    const claim = allClaims.find(c => c.id === claimId);
    if (!claim) return;
    
    console.log('selectClaim - claim:', claim);
    
    closeClaimModal();
    
    // Populate form fields
    document.getElementById('claim_transaction_id').value = claim.id;
    
    // Set supplier dropdown value - convert to string for comparison
    const supplierSelect = document.getElementById('supplier_id');
    const supplierIdStr = String(claim.supplier_id || '');
    console.log('Setting supplier_id dropdown to:', supplierIdStr);
    
    // Find and select the matching option
    let found = false;
    for (let i = 0; i < supplierSelect.options.length; i++) {
        if (String(supplierSelect.options[i].value) === supplierIdStr) {
            supplierSelect.selectedIndex = i;
            found = true;
            break;
        }
    }
    console.log('Dropdown value after set:', supplierSelect.value, 'Found:', found);
    document.getElementById('supplier_name').value = claim.supplier_name || '';
    
    document.getElementById('claim_trn_no').value = claim.claim_no || '';
    
    const netAmount = parseFloat(claim.net_amount || claim.amount || 0);
    document.getElementById('os_amount').value = netAmount.toFixed(2);
    document.getElementById('claim_amount').value = netAmount.toFixed(2);
    
    // Load HSN data from claim items if available
    if (claim.items && claim.items.length > 0) {
        loadHSNFromClaim(claim.items);
    }
    
    calculateFromClaimAmount();
}

function loadHSNFromClaim(items) {
    // Group items by HSN code
    const hsnGroups = {};
    items.forEach(item => {
        const hsn = item.hsn_code || 'NO-HSN';
        if (!hsnGroups[hsn]) {
            hsnGroups[hsn] = {
                hsn_code: hsn,
                amount: 0,
                gst_percent: (parseFloat(item.cgst_percent || 0) + parseFloat(item.sgst_percent || 0)),
                igst_percent: 0,
                qty: 0
            };
        }
        hsnGroups[hsn].amount += parseFloat(item.ft_amount || 0);
        hsnGroups[hsn].qty += parseFloat(item.qty || 0);
    });
    
    // Clear and populate HSN table
    clearAllRows();
    Object.values(hsnGroups).forEach(hsn => {
        addHSNRowWithData(hsn);
    });
    
    calculateTotals();
}

// HSN Table Functions
function addHSNRowWithData(data) {
    const tbody = document.getElementById('hsnTableBody');
    const idx = hsnRowIndex++;
    
    const gstAmount = (parseFloat(data.amount) * parseFloat(data.gst_percent) / 100).toFixed(2);
    
    const tr = document.createElement('tr');
    tr.id = `hsn_row_${idx}`;
    tr.onclick = function() { selectHsnRow(idx); };
    tr.innerHTML = `
        <td><input type="text" name="hsn[${idx}][hsn_code]" class="form-control readonly-field" value="${data.hsn_code || ''}" readonly></td>
        <td><input type="number" name="hsn[${idx}][amount]" class="form-control text-end" value="${parseFloat(data.amount || 0).toFixed(2)}" step="0.01" onchange="calculateRowGST(${idx}); validateAmount();"></td>
        <td><input type="number" name="hsn[${idx}][gst_percent]" class="form-control text-end readonly-field" value="${parseFloat(data.gst_percent || 0).toFixed(2)}" readonly></td>
        <td><input type="number" name="hsn[${idx}][igst_percent]" class="form-control text-end readonly-field" value="${parseFloat(data.igst_percent || 0).toFixed(2)}" readonly></td>
        <td><input type="number" name="hsn[${idx}][gst_amount]" class="form-control text-end readonly-field" value="${gstAmount}" readonly></td>
        <td><input type="number" name="hsn[${idx}][qty]" class="form-control text-end" value="${parseInt(data.qty || 1)}" step="1"></td>
    `;
    tbody.appendChild(tr);
    selectHsnRow(idx);
    calculateTotals();
    validateAmount();
}

function selectHsnRow(idx) {
    document.querySelectorAll('#hsnTableBody tr').forEach(tr => tr.classList.remove('row-selected'));
    const row = document.getElementById(`hsn_row_${idx}`);
    if (row) {
        row.classList.add('row-selected');
        selectedHsnRowIndex = idx;
    }
}

function calculateRowGST(idx) {
    const row = document.getElementById(`hsn_row_${idx}`);
    if (!row) return;
    
    const amount = parseFloat(row.querySelector('input[name*="[amount]"]').value) || 0;
    const gstPercent = parseFloat(row.querySelector('input[name*="[gst_percent]"]').value) || 0;
    const igstPercent = parseFloat(row.querySelector('input[name*="[igst_percent]"]').value) || 0;
    
    const gstAmount = amount * (gstPercent + igstPercent) / 100;
    row.querySelector('input[name*="[gst_amount]"]').value = gstAmount.toFixed(2);
    
    calculateTotals();
}

function deleteSelectedRow() {
    if (selectedHsnRowIndex !== null) {
        document.getElementById(`hsn_row_${selectedHsnRowIndex}`)?.remove();
        selectedHsnRowIndex = null;
        calculateTotals();
    } else {
        alert('Please select a row first');
    }
}

function clearAllRows() {
    document.getElementById('hsnTableBody').innerHTML = '';
    hsnRowIndex = 0;
    selectedHsnRowIndex = null;
    calculateTotals();
}

// HSN Modal Functions
function loadHsnCodes() {
    fetch('{{ route("admin.sale-voucher.hsn-codes") }}')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.hsn_codes) {
                allHsnCodes = data.hsn_codes;
            }
        })
        .catch(e => console.error('Error loading HSN codes:', e));
}

function showHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.add('show');
    document.getElementById('hsnModal').classList.add('show');
    document.getElementById('hsnSearchInput').value = '';
    renderHsnList(allHsnCodes);
    setTimeout(() => document.getElementById('hsnSearchInput').focus(), 100);
}

function closeHsnModal() {
    document.getElementById('hsnModalBackdrop').classList.remove('show');
    document.getElementById('hsnModal').classList.remove('show');
}

function filterHsnCodes() {
    const search = document.getElementById('hsnSearchInput').value.toLowerCase();
    const filtered = allHsnCodes.filter(h => 
        (h.hsn_code && h.hsn_code.toLowerCase().includes(search)) ||
        (h.name && h.name.toLowerCase().includes(search))
    );
    renderHsnList(filtered);
}

function renderHsnList(hsnCodes) {
    const tbody = document.getElementById('hsnListBody');
    if (!hsnCodes || !hsnCodes.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No HSN codes found</td></tr>';
        return;
    }
    tbody.innerHTML = hsnCodes.map(h => `
        <tr class="claim-row" onclick="selectHsnCode(${h.id})" style="cursor:pointer;">
            <td><strong>${h.hsn_code || ''}</strong></td>
            <td>${h.name || ''}</td>
            <td class="text-end">${parseFloat(h.cgst_percent || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(h.sgst_percent || 0).toFixed(2)}</td>
            <td class="text-end">${parseFloat(h.total_gst_percent || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function selectHsnCode(hsnId) {
    const hsn = allHsnCodes.find(h => h.id === hsnId);
    if (!hsn) return;
    
    closeHsnModal();
    
    // Add HSN row with selected code
    addHSNRowWithData({
        hsn_code: hsn.hsn_code,
        amount: 0,
        gst_percent: parseFloat(hsn.total_gst_percent || 0),
        igst_percent: 0,
        qty: 1
    });
}

// Validation Function
function validateAmount() {
    const osAmount = parseFloat(document.getElementById('os_amount').value) || 0;
    const claimAmount = parseFloat(document.getElementById('claim_amount').value) || 0;
    const finalAmount = parseFloat(document.getElementById('final_amount').value) || 0;
    
    // Calculate reference amount (O/S Amount)
    const referenceAmount = osAmount;
    
    // Check if final amount exceeds O/S Amount
    if (referenceAmount > 0 && finalAmount > referenceAmount) {
        document.getElementById('final_amount').style.color = 'red';
        return false;
    } else {
        document.getElementById('final_amount').style.color = '#28a745';
        return true;
    }
}

function calculateFromClaimAmount() {
    calculateTotals();
    validateAmount();
}

function calculateTotals() {
    let grossAmt = 0;
    let totalGst = 0;
    
    const hsnRows = document.querySelectorAll('#hsnTableBody tr');
    
    // If HSN codes are added, calculate from HSN table
    if (hsnRows.length > 0) {
        hsnRows.forEach(row => {
            const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;
            const gstAmount = parseFloat(row.querySelector('input[name*="[gst_amount]"]')?.value) || 0;
            grossAmt += amount;
            totalGst += gstAmount;
        });
    } else {
        // No HSN - use Claim Amount, if no Claim Amount use O/S Amount
        const claimAmount = parseFloat(document.getElementById('claim_amount').value) || 0;
        const osAmount = parseFloat(document.getElementById('os_amount').value) || 0;
        grossAmt = claimAmount > 0 ? claimAmount : osAmount;
    }
    
    const netAmt = grossAmt + totalGst;
    const roundOff = parseFloat(document.getElementById('round_off').value) || 0;
    const finalAmount = netAmt + roundOff;
    
    document.getElementById('gross_amt').value = grossAmt.toFixed(2);
    document.getElementById('total_gst').value = totalGst.toFixed(2);
    document.getElementById('total_gst_amt').value = totalGst.toFixed(2);
    document.getElementById('net_amt').value = netAmt.toFixed(2);
    document.getElementById('final_amount').value = finalAmount.toFixed(2);
    
    validateAmount();
}

// Save Transaction
function saveTransaction() {
    const supplierId = document.getElementById('supplier_id').value;
    if (!supplierId) {
        alert('Please select a supplier or load a claim first');
        return;
    }
    
    const finalAmount = parseFloat(document.getElementById('final_amount').value) || 0;
    if (finalAmount <= 0) {
        alert('Amount must be greater than 0');
        return;
    }
    
    // Validate: Final amount should not exceed O/S Amount
    const osAmount = parseFloat(document.getElementById('os_amount').value) || 0;
    if (osAmount > 0 && finalAmount > osAmount) {
        alert('Final Amount (₹' + finalAmount.toFixed(2) + ') cannot exceed O/S Amount (₹' + osAmount.toFixed(2) + ')');
        return;
    }
    
    const hsnItems = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        hsnItems.push({
            hsn_code: row.querySelector('input[name*="[hsn_code]"]')?.value || '',
            amount: parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0,
            gst_percent: parseFloat(row.querySelector('input[name*="[gst_percent]"]')?.value) || 0,
            igst_percent: parseFloat(row.querySelector('input[name*="[igst_percent]"]')?.value) || 0,
            gst_amount: parseFloat(row.querySelector('input[name*="[gst_amount]"]')?.value) || 0,
            qty: parseInt(row.querySelector('input[name*="[qty]"]')?.value) || 0
        });
    });
    
    pendingTransactionData = {
        claim_transaction_id: document.getElementById('claim_transaction_id').value,
        transaction_date: document.getElementById('transaction_date').value,
        supplier_id: supplierId,
        supplier_name: document.getElementById('supplier_name').value,
        party_trn_no: document.getElementById('party_trn_no').value,
        party_date: document.getElementById('party_date').value,
        claim_flag: document.getElementById('claim_flag').value,
        received_as_debit_note: document.getElementById('received_as_debit_note').checked,
        claim_amount: parseFloat(document.getElementById('claim_amount').value) || 0,
        gross_amt: parseFloat(document.getElementById('gross_amt').value) || 0,
        total_gst: parseFloat(document.getElementById('total_gst').value) || 0,
        net_amt: parseFloat(document.getElementById('net_amt').value) || 0,
        round_off: parseFloat(document.getElementById('round_off').value) || 0,
        final_amount: finalAmount,
        remarks: document.getElementById('remarks').value,
        hsn_items: hsnItems
    };
    
    showAdjustModal();
}

// Adjustment Modal Functions
function showAdjustModal() {
    const supplierId = pendingTransactionData.supplier_id;
    const totalAmount = pendingTransactionData.final_amount;
    
    console.log('showAdjustModal - supplierId:', supplierId, 'totalAmount:', totalAmount);
    
    const url = `{{ url('admin/breakage-supplier/supplier-purchases') }}/${supplierId}`;
    console.log('Fetching URL:', url);
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            console.log('API Response:', data);
            if (data.success) {
                renderAdjustTable(data.purchases, totalAmount);
                document.getElementById('adjustBackdrop').classList.add('show');
                document.getElementById('adjustModal').classList.add('show');
            } else {
                console.error('API returned success:false', data.message);
                submitTransaction([]);
            }
        })
        .catch(e => {
            console.error('Error:', e);
            submitTransaction([]);
        });
}

function renderAdjustTable(purchases, totalAmount) {
    const tbody = document.getElementById('adjustTableBody');
    
    if (!purchases.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No purchases found for this supplier</td></tr>';
    } else {
        tbody.innerHTML = purchases.map((p, i) => `
            <tr data-purchase-id="${p.id}" data-original-balance="${parseFloat(p.balance_amount || 0)}">
                <td class="text-center">${i + 1}</td>
                <td><strong>${p.purchase_no || ''}</strong></td>
                <td>${p.purchase_date || ''}</td>
                <td class="text-end">₹${parseFloat(p.total_amount || 0).toFixed(2)}</td>
                <td class="text-end text-primary fw-bold balance-cell" data-original="${parseFloat(p.balance_amount || 0)}">${parseFloat(p.balance_amount || 0).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm text-end adjust-input" 
                           data-purchase-id="${p.id}" data-max="${parseFloat(p.balance_amount || 0)}"
                           step="0.01" min="0" value="0" oninput="updateRowBalance(this)" onchange="updateRowBalance(this)">
                </td>
            </tr>
        `).join('');
    }
    
    document.getElementById('adjustTotalDisplay').textContent = '₹' + totalAmount.toFixed(2);
    document.getElementById('adjustedDisplay').textContent = '₹0.00';
    document.getElementById('adjustBalanceDisplay').textContent = '₹' + totalAmount.toFixed(2);
}

// Update row balance instantly when adjustment amount changes
function updateRowBalance(input) {
    const row = input.closest('tr');
    const balanceCell = row.querySelector('.balance-cell');
    const originalBalance = parseFloat(input.dataset.max) || 0;
    const adjustAmount = parseFloat(input.value) || 0;
    
    // Validate amount doesn't exceed original balance
    if (adjustAmount > originalBalance) {
        input.value = originalBalance;
        input.classList.add('is-invalid');
    } else {
        input.classList.remove('is-invalid');
    }
    
    // Update balance cell instantly
    const newBalance = originalBalance - Math.min(adjustAmount, originalBalance);
    balanceCell.textContent = newBalance.toFixed(2);
    
    // Update totals
    updateAdjustTotals();
}

function updateAdjustTotals() {
    const totalAmount = pendingTransactionData.final_amount;
    let totalAdjusted = 0;
    
    document.querySelectorAll('.adjust-input').forEach(input => {
        const amount = parseFloat(input.value) || 0;
        const max = parseFloat(input.dataset.max) || 0;
        if (amount > max) {
            input.value = max;
        }
        totalAdjusted += Math.min(amount, max);
    });
    
    document.getElementById('adjustedDisplay').textContent = '₹' + totalAdjusted.toFixed(2);
    document.getElementById('adjustBalanceDisplay').textContent = '₹' + (totalAmount - totalAdjusted).toFixed(2);
}

function closeAdjustModal() {
    document.getElementById('adjustBackdrop').classList.remove('show');
    document.getElementById('adjustModal').classList.remove('show');
}

function saveWithAdjustments() {
    const adjustments = [];
    document.querySelectorAll('.adjust-input').forEach(input => {
        const amount = parseFloat(input.value) || 0;
        if (amount > 0) {
            adjustments.push({
                purchase_id: input.dataset.purchaseId,
                amount: amount
            });
        }
    });
    
    closeAdjustModal();
    submitTransaction(adjustments);
}

function submitTransaction(adjustments) {
    pendingTransactionData.adjustments = adjustments;
    
    fetch('{{ route("admin.breakage-supplier.store-received") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(pendingTransactionData)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Transaction saved successfully! Trn No: ' + result.trn_no);
            window.location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to save'));
        }
    })
    .catch(e => {
        console.error('Error:', e);
        alert('Error saving transaction');
    });
}

function cancelTransaction() {
    if (confirm('Discard changes?')) {
        window.location.href = '{{ route("admin.breakage-supplier.received-modification") }}';
    }
}

document.getElementById('round_off')?.addEventListener('change', calculateTotals);
</script>
@endpush
