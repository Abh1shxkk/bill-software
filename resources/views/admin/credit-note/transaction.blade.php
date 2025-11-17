@extends('layouts.admin')

@section('title', 'Credit Note Transaction')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-file-earmark-minus me-2"></i> Credit Note Transaction</h4>
        <div class="text-muted small">Create new credit note</div>
    </div>
    <div>
        <a href="{{ route('admin.credit-note.invoices') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list me-1"></i> View All
        </a>
    </div>
</div>

<form id="creditNoteForm" autocomplete="off">
    @csrf
    
    <!-- Header Section -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-primary text-white py-2">
            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Credit Note Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="creditNoteDate" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="creditNoteDate" name="credit_note_date" 
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label for="dayName" class="form-label">Day</label>
                    <input type="text" class="form-control readonly-field" id="dayName" readonly>
                </div>
                <div class="col-md-2">
                    <label for="creditNoteNo" class="form-label">Credit Note No.</label>
                    <input type="text" class="form-control readonly-field" id="creditNoteNo" 
                           value="{{ $nextCreditNoteNo }}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="reason" class="form-label">Reason</label>
                    <select class="form-select" id="reason" name="reason">
                        <option value="">Select Reason</option>
                        <option value="Rate Diff.">Rate Diff.</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Party Details Section -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-success text-white py-2">
            <h6 class="mb-0"><i class="bi bi-people me-2"></i> Party Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Credit Section (Left) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                        <h6 class="text-primary mb-3"><i class="bi bi-arrow-down-circle me-1"></i> Credit (Party)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="credit_party_type" id="partySupplier" value="S" checked>
                                    <label class="btn btn-outline-primary" for="partySupplier">Supplier</label>
                                    <input type="radio" class="btn-check" name="credit_party_type" id="partyCustomer" value="C">
                                    <label class="btn btn-outline-primary" for="partyCustomer">Customer</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="partySelect" class="form-label">Party Name <span class="text-danger">*</span></label>
                                <select class="form-select" id="partySelect" name="credit_party_id">
                                    <option value="">Select Party</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" data-type="S">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="salesmanSelect" class="form-label">Sales Man</label>
                                <select class="form-select" id="salesmanSelect" name="salesman_id">
                                    <option value="">Select Salesman</option>
                                    @foreach($salesmen as $salesman)
                                        <option value="{{ $salesman->id }}">{{ $salesman->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debit Section (Right) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #fff;">
                        <h6 class="text-secondary mb-3"><i class="bi bi-arrow-up-circle me-1"></i> Debit (Account)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Account Type</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="debit_account_type" id="accountPurchase" value="P" checked>
                                    <label class="btn btn-outline-secondary" for="accountPurchase">Purchase</label>
                                    <input type="radio" class="btn-check" name="debit_account_type" id="accountSale" value="S">
                                    <label class="btn btn-outline-secondary" for="accountSale">Sale</label>
                                    <input type="radio" class="btn-check" name="debit_account_type" id="accountGeneral" value="G">
                                    <label class="btn btn-outline-secondary" for="accountGeneral">General</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="accountNo" class="form-label">Account No</label>
                                <input type="text" class="form-control" id="accountNo" name="debit_account_no">
                            </div>
                            <div class="col-md-6">
                                <label for="invRefNo" class="form-label">Inv. Ref. No.</label>
                                <input type="text" class="form-control" id="invRefNo" name="inv_ref_no">
                            </div>
                            <div class="col-md-6">
                                <label for="invoiceDate" class="form-label">Invoice Date</label>
                                <input type="date" class="form-control" id="invoiceDate" name="invoice_date">
                            </div>
                            <div class="col-md-6">
                                <label for="gstVno" class="form-label">GST Vno.</label>
                                <input type="text" class="form-control" id="gstVno" name="gst_vno">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Party Transaction Details -->
                <div class="col-md-12">
                    <div class="row g-2 mt-2">
                        <div class="col-md-3">
                            <label for="partyTrnNo" class="form-label">Party Trn. No.</label>
                            <input type="text" class="form-control" id="partyTrnNo" name="party_trn_no">
                        </div>
                        <div class="col-md-3">
                            <label for="partyTrnDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="partyTrnDate" name="party_trn_date">
                        </div>
                        <div class="col-md-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" value="0.00">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HSN Code Table -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-info text-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i> HSN Details</h6>
            <button type="button" class="btn btn-light btn-sm" onclick="openHsnModal()">
                <i class="bi bi-plus-circle me-1"></i> Insert
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="hsnTable" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 150px;">HSN Code</th>
                            <th style="width: 120px;">Amount</th>
                            <th style="width: 80px;">GST%</th>
                            <th style="width: 80px;">CGST%</th>
                            <th style="width: 100px;">CGST Amt</th>
                            <th style="width: 80px;">SGST%</th>
                            <th style="width: 100px;">SGST Amt</th>
                            <th style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="hsnTableBody">
                        <!-- Rows will be added via Insert button -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Calculation Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-chat-text me-2"></i> Narration</h6>
                </div>
                <div class="card-body">
                    <textarea class="form-control" id="narration" name="narration" rows="3" placeholder="Enter narration..."></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-calculator me-2"></i> Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small">Gross Amount</label>
                            <input type="number" class="form-control form-control-sm readonly-field" id="grossAmount" name="gross_amount" value="0.00" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Total GST</label>
                            <input type="number" class="form-control form-control-sm readonly-field" id="totalGst" name="total_gst" value="0.00" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Net Amount</label>
                            <input type="number" class="form-control form-control-sm readonly-field" id="netAmount" name="net_amount" value="0.00" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">TCS</label>
                            <input type="number" class="form-control form-control-sm" id="tcsAmount" name="tcs_amount" step="0.01" value="0.00" onchange="calculateTotals()">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Round Off</label>
                            <input type="number" class="form-control form-control-sm" id="roundOff" name="round_off" step="0.01" value="0.00" onchange="calculateTotals()">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-success">CN Amount</label>
                            <input type="number" class="form-control form-control-sm fw-bold text-success readonly-field" id="cnAmount" name="cn_amount" value="0.00" readonly style="font-size: 16px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card shadow-sm border-0">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('admin.credit-note.invoices') }}'">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" onclick="saveCreditNote()">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                    <button type="button" class="btn btn-success" onclick="saveCreditNote(true)">
                        <i class="bi bi-printer me-1"></i> Save & Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Hidden field for credit note ID (for modification) -->
<input type="hidden" id="creditNoteId" value="">

<!-- HSN Code Selection Modal - Right Sliding -->
<div id="hsnCodeModal" class="hsn-modal">
    <div class="hsn-modal-content">
        <div class="hsn-modal-header">
            <h5 class="hsn-modal-title">
                <i class="bi bi-upc-scan me-2"></i>Select HSN Code
            </h5>
            <button type="button" class="btn-close-modal" onclick="closeHsnModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="hsn-modal-body" id="hsnModalBody">
            <!-- Search Box -->
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="hsn_modal_search" 
                           placeholder="Search HSN code or description..." autocomplete="off">
                </div>
            </div>
            
            <!-- Loading Spinner -->
            <div id="hsn_modal_loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Loading HSN codes...</div>
            </div>
            
            <!-- HSN Codes List -->
            <div id="hsn_modal_table_container" style="display: none;">
                <table class="table table-hover mb-0">
                    <thead class="sticky-top bg-white">
                        <tr>
                            <th class="fw-semibold text-dark">HSN Code</th>
                            <th class="fw-semibold text-dark">Description</th>
                            <th class="fw-semibold text-dark text-center">GST%</th>
                            <th class="fw-semibold text-dark text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="hsn_codes_list">
                        <!-- HSN codes will be populated here -->
                    </tbody>
                </table>
            </div>
            <div id="hsn_modal_no_results" class="text-center py-5 text-muted" style="display: none;">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                <p class="mb-0">No HSN codes found</p>
            </div>
        </div>
    </div>
</div>
<div id="hsnModalBackdrop" class="hsn-modal-backdrop" onclick="closeHsnModal()"></div>

<!-- Save Options Modal -->
<div id="saveOptionsModal" class="save-options-modal">
    <div class="save-options-modal-content">
        <div class="save-options-modal-header">
            <h5><i class="bi bi-save me-2"></i>Save Credit Note</h5>
            <button type="button" class="save-options-close-btn" onclick="closeSaveOptionsModal()">&times;</button>
        </div>
        <div class="save-options-modal-body">
            <p>How would you like to save this Credit Note?</p>
            <div class="save-options-buttons">
                <button type="button" class="btn btn-secondary" onclick="saveWithoutAdjustment()">
                    <i class="bi bi-file-earmark-check me-1"></i> Save Without Adjustment
                </button>
                <button type="button" class="btn btn-primary" onclick="saveWithAdjustment()">
                    <i class="bi bi-receipt me-1"></i> Save With Adjustment
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let hsnRowCount = 0;
let hsnCodesData = [];

document.addEventListener('DOMContentLoaded', function() {
    updateDayName();
    
    // Date change handler
    document.getElementById('creditNoteDate').addEventListener('change', updateDayName);
    
    // Party type change handler
    document.querySelectorAll('input[name="credit_party_type"]').forEach(radio => {
        radio.addEventListener('change', updatePartyDropdown);
    });
    
    // HSN search handler
    document.getElementById('hsn_modal_search').addEventListener('input', debounce(filterHsnCodes, 300));
    
    // Load HSN codes
    loadHsnCodes();
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeHsnModal();
    });
});

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Update day name based on date
function updateDayName() {
    const dateInput = document.getElementById('creditNoteDate');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    }
}

// Update party dropdown based on type
function updatePartyDropdown() {
    const partyType = document.querySelector('input[name="credit_party_type"]:checked').value;
    const partySelect = document.getElementById('partySelect');
    
    partySelect.innerHTML = '<option value="">Select Party</option>';
    
    if (partyType === 'S') {
        @foreach($suppliers as $supplier)
            partySelect.innerHTML += '<option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>';
        @endforeach
        // Auto-select Purchase in Debit Account Type
        document.getElementById('accountPurchase').checked = true;
    } else {
        @foreach($customers as $customer)
            partySelect.innerHTML += '<option value="{{ $customer->id }}">{{ $customer->name }}</option>';
        @endforeach
        // Auto-select Sale in Debit Account Type
        document.getElementById('accountSale').checked = true;
    }
}

// Load HSN codes from server
function loadHsnCodes() {
    fetch('{{ route("admin.hsn-codes.index") }}?all=1', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        hsnCodesData = data;
        renderHsnCodes(hsnCodesData);
        document.getElementById('hsn_modal_loading').style.display = 'none';
        document.getElementById('hsn_modal_table_container').style.display = 'block';
    })
    .catch(error => {
        console.error('Error loading HSN codes:', error);
        document.getElementById('hsn_modal_loading').innerHTML = '<p class="text-danger">Error loading HSN codes</p>';
    });
}

// Render HSN codes in modal
function renderHsnCodes(codes) {
    const tbody = document.getElementById('hsn_codes_list');
    tbody.innerHTML = '';
    
    if (codes.length === 0) {
        document.getElementById('hsn_modal_table_container').style.display = 'none';
        document.getElementById('hsn_modal_no_results').style.display = 'block';
        return;
    }
    
    document.getElementById('hsn_modal_table_container').style.display = 'block';
    document.getElementById('hsn_modal_no_results').style.display = 'none';
    
    codes.forEach(code => {
        const gstPercent = parseFloat(code.cgst_percent || 0) + parseFloat(code.sgst_percent || 0);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${code.hsn_code}</strong></td>
            <td class="small">${code.description || '-'}</td>
            <td class="text-center"><span class="badge bg-success">${gstPercent.toFixed(2)}%</span></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-primary" onclick="selectHsnCode('${code.hsn_code}', ${code.cgst_percent || 0}, ${code.sgst_percent || 0})">
                    <i class="bi bi-plus-circle"></i> Select
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Filter HSN codes
function filterHsnCodes() {
    const searchTerm = document.getElementById('hsn_modal_search').value.toLowerCase();
    const filtered = hsnCodesData.filter(code => 
        code.hsn_code.toLowerCase().includes(searchTerm) || 
        (code.description && code.description.toLowerCase().includes(searchTerm))
    );
    renderHsnCodes(filtered);
}

// Open HSN modal
function openHsnModal() {
    const modal = document.getElementById('hsnCodeModal');
    const backdrop = document.getElementById('hsnModalBackdrop');
    
    document.getElementById('hsn_modal_search').value = '';
    renderHsnCodes(hsnCodesData);
    
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
        document.getElementById('hsn_modal_search').focus();
    }, 10);
}

// Close HSN modal
function closeHsnModal() {
    const modal = document.getElementById('hsnCodeModal');
    const backdrop = document.getElementById('hsnModalBackdrop');
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
        document.body.style.overflow = '';
    }, 300);
}

// Select HSN code and add row
function selectHsnCode(hsnCode, cgstPercent, sgstPercent) {
    const gstPercent = parseFloat(cgstPercent) + parseFloat(sgstPercent);
    addHsnRowWithData(hsnCode, gstPercent, cgstPercent, sgstPercent);
    closeHsnModal();
}

// Add HSN row with data
function addHsnRowWithData(hsnCode, gstPercent, cgstPercent, sgstPercent) {
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', hsnRowCount);
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm hsn-code readonly-field" name="items[${hsnRowCount}][hsn_code]" value="${hsnCode}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-amount" name="items[${hsnRowCount}][amount]" step="0.01" value="0.00" onchange="calculateGst(${hsnRowCount})" onkeyup="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-gst readonly-field" name="items[${hsnRowCount}][gst_percent]" step="0.01" value="${gstPercent.toFixed(2)}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-percent readonly-field" name="items[${hsnRowCount}][cgst_percent]" step="0.01" value="${cgstPercent.toFixed(2)}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-amount readonly-field" name="items[${hsnRowCount}][cgst_amount]" step="0.01" value="0.00" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-percent readonly-field" name="items[${hsnRowCount}][sgst_percent]" step="0.01" value="${sgstPercent.toFixed(2)}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-amount readonly-field" name="items[${hsnRowCount}][sgst_amount]" step="0.01" value="0.00" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteHsnRow(${hsnRowCount})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    
    // Focus on amount field
    row.querySelector('.hsn-amount').focus();
    
    hsnRowCount++;
}

// Delete HSN row
function deleteHsnRow(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

// Calculate GST for a row
function calculateGst(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (!row) return;
    
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    const cgstPercent = parseFloat(row.querySelector('.hsn-cgst-percent').value) || 0;
    const sgstPercent = parseFloat(row.querySelector('.hsn-sgst-percent').value) || 0;
    
    const cgstAmount = (amount * cgstPercent) / 100;
    const sgstAmount = (amount * sgstPercent) / 100;
    
    row.querySelector('.hsn-cgst-amount').value = cgstAmount.toFixed(2);
    row.querySelector('.hsn-sgst-amount').value = sgstAmount.toFixed(2);
    
    calculateTotals();
}

// Calculate totals
function calculateTotals() {
    let grossAmount = 0;
    let totalGst = 0;
    
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const amount = parseFloat(row.querySelector('.hsn-amount')?.value) || 0;
        const cgstAmount = parseFloat(row.querySelector('.hsn-cgst-amount')?.value) || 0;
        const sgstAmount = parseFloat(row.querySelector('.hsn-sgst-amount')?.value) || 0;
        
        grossAmount += amount;
        totalGst += cgstAmount + sgstAmount;
    });
    
    const tcsAmount = parseFloat(document.getElementById('tcsAmount').value) || 0;
    const roundOff = parseFloat(document.getElementById('roundOff').value) || 0;
    
    const netAmount = grossAmount + totalGst;
    const cnAmount = netAmount + tcsAmount + roundOff;
    
    document.getElementById('grossAmount').value = grossAmount.toFixed(2);
    document.getElementById('totalGst').value = totalGst.toFixed(2);
    document.getElementById('netAmount').value = netAmount.toFixed(2);
    document.getElementById('cnAmount').value = cnAmount.toFixed(2);
}

// Save credit note - Entry point
function saveCreditNote(print = false) {
    window.printAfterSave = print;
    
    const creditNoteDate = document.getElementById('creditNoteDate').value;
    const partyId = document.getElementById('partySelect').value;
    
    if (!creditNoteDate) {
        alert('Please select date');
        return;
    }
    
    if (!partyId) {
        alert('Please select party');
        return;
    }
    
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value;
        const amount = row.querySelector('.hsn-amount')?.value;
        
        if (hsnCode || parseFloat(amount) > 0) {
            items.push({
                hsn_code: hsnCode,
                amount: amount,
                gst_percent: row.querySelector('.hsn-gst')?.value || 0,
                cgst_percent: row.querySelector('.hsn-cgst-percent')?.value || 0,
                cgst_amount: row.querySelector('.hsn-cgst-amount')?.value || 0,
                sgst_percent: row.querySelector('.hsn-sgst-percent')?.value || 0,
                sgst_amount: row.querySelector('.hsn-sgst-amount')?.value || 0,
            });
        }
    });
    
    if (items.length === 0) {
        alert('Please add at least one HSN item');
        return;
    }
    
    // Store CN amount for adjustment
    window.cnAmount = parseFloat(document.getElementById('cnAmount').value || 0);
    
    // Show save options modal
    showSaveOptionsModal();
}

// Show Save Options Modal
function showSaveOptionsModal() {
    const modal = document.getElementById('saveOptionsModal');
    modal.classList.add('show');
    
    document.addEventListener('keydown', handleSaveOptionsEsc);
}

// Close Save Options Modal
function closeSaveOptionsModal() {
    const modal = document.getElementById('saveOptionsModal');
    modal.classList.remove('show');
    document.removeEventListener('keydown', handleSaveOptionsEsc);
}

function handleSaveOptionsEsc(e) {
    if (e.key === 'Escape') closeSaveOptionsModal();
}

// Save Without Adjustment
function saveWithoutAdjustment() {
    closeSaveOptionsModal();
    submitCreditNote(false, []);
}

// Save With Adjustment
function saveWithAdjustment() {
    closeSaveOptionsModal();
    
    const partyId = document.getElementById('partySelect').value;
    const partyType = document.querySelector('input[name="credit_party_type"]:checked').value;
    
    if (!partyId) {
        alert('Please select a party first');
        return;
    }
    
    // Fetch party invoices for adjustment
    fetchPartyInvoices(partyId, partyType);
}

// Fetch Party Invoices for Adjustment
function fetchPartyInvoices(partyId, partyType) {
    fetch('{{ route("admin.credit-note.party-invoices") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            party_id: partyId,
            party_type: partyType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAdjustmentModal(data.invoices, window.cnAmount);
        } else {
            alert(data.message || 'Failed to load invoices');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading invoices');
    });
}

// Show Adjustment Modal
function showAdjustmentModal(invoices, cnAmount) {
    const modalHTML = `
        <div class="adjustment-modal-backdrop" id="adjustmentModalBackdrop"></div>
        <div class="adjustment-modal" id="adjustmentModal">
            <div class="adjustment-modal-content">
                <div class="adjustment-modal-header">
                    <h5 class="adjustment-modal-title"><i class="bi bi-receipt me-2"></i>Credit Note Adjustment</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeAdjustmentModal()"></button>
                </div>
                <div class="adjustment-modal-body">
                    <div style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-bordered table-sm" style="font-size: 12px; margin-bottom: 0;">
                            <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th style="width: 50px; text-align: center;">Sr.</th>
                                    <th style="width: 120px; text-align: center;">Invoice No.</th>
                                    <th style="width: 100px; text-align: center;">Date</th>
                                    <th style="width: 110px; text-align: right;">Bill Amt.</th>
                                    <th style="width: 110px; text-align: center;">Adjusted</th>
                                    <th style="width: 110px; text-align: right;">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${invoices.length > 0 ? invoices.map((invoice, index) => {
                                    const balance = parseFloat(invoice.balance || invoice.bill_amount || 0);
                                    return `
                                    <tr>
                                        <td style="text-align: center;">${index + 1}</td>
                                        <td style="text-align: center;">${invoice.trans_no || invoice.invoice_no || '-'}</td>
                                        <td style="text-align: center;">${invoice.date || '-'}</td>
                                        <td style="text-align: right; font-weight: bold; color: #0d6efd;">₹ ${balance.toFixed(2)}</td>
                                        <td style="text-align: center;">
                                            <input type="number" class="form-control form-control-sm adjustment-input" 
                                                   id="adj_${invoice.id}" 
                                                   data-invoice-id="${invoice.id}"
                                                   data-balance="${balance}"
                                                   value="0.00" 
                                                   min="0" 
                                                   max="${balance}"
                                                   step="0.01"
                                                   onchange="updateAdjustmentBalance()"
                                                   onkeyup="updateAdjustmentBalance()"
                                                   style="width: 90px; text-align: right;">
                                        </td>
                                        <td style="text-align: right;" id="balance_${invoice.id}">
                                            <span style="color: #28a745;">₹ ${balance.toFixed(2)}</span>
                                        </td>
                                    </tr>
                                `}).join('') : '<tr><td colspan="6" class="text-center text-muted py-3">No pending invoices found</td></tr>'}
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 15px; padding: 12px; background: #f8f9fa; border-radius: 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: 500; color: #6c757d;"><kbd>ESC</kbd> to close</span>
                            <span style="font-weight: bold; font-size: 15px; color: #0d6efd;">
                                Amount to Adjust: <span id="adjustmentBalance">₹ ${cnAmount.toFixed(2)}</span>
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="font-weight: 500; color: #495057; white-space: nowrap;">Auto Adjust:</label>
                            <input type="number" id="autoAdjustAmount" class="form-control form-control-sm" 
                                   style="width: 120px;" step="0.01" value="${cnAmount.toFixed(2)}">
                            <button type="button" class="btn btn-info btn-sm" onclick="autoDistributeAmount()">
                                <i class="bi bi-magic me-1"></i>Distribute
                            </button>
                        </div>
                    </div>
                </div>
                <div class="adjustment-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustmentModal()">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="saveAdjustment()">
                        <i class="bi bi-check-circle me-1"></i>Save & Submit
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('adjustmentModal');
    if (existingModal) existingModal.remove();
    const existingBackdrop = document.getElementById('adjustmentModalBackdrop');
    if (existingBackdrop) existingBackdrop.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    window.adjustmentAmount = cnAmount;
    
    setTimeout(() => {
        document.getElementById('adjustmentModalBackdrop').classList.add('show');
        document.getElementById('adjustmentModal').classList.add('show');
    }, 10);
    
    document.addEventListener('keydown', handleAdjustmentEsc);
}

function handleAdjustmentEsc(e) {
    if (e.key === 'Escape') closeAdjustmentModal();
}

// Update adjustment balance
function updateAdjustmentBalance() {
    const inputs = document.querySelectorAll('.adjustment-input');
    let totalAdjusted = 0;
    
    inputs.forEach(input => {
        let adjusted = parseFloat(input.value || 0);
        const invoiceId = input.getAttribute('data-invoice-id');
        const maxBalance = parseFloat(input.getAttribute('data-balance'));
        
        if (adjusted > maxBalance) {
            input.value = maxBalance.toFixed(2);
            adjusted = maxBalance;
        }
        
        totalAdjusted += adjusted;
        
        const newBalance = maxBalance - adjusted;
        const balanceCell = document.getElementById(`balance_${invoiceId}`);
        if (balanceCell) {
            const color = newBalance === 0 ? '#28a745' : (newBalance < 0 ? '#dc3545' : '#28a745');
            balanceCell.innerHTML = `<span style="color: ${color}; font-weight: ${newBalance === 0 ? 'bold' : 'normal'};">₹ ${newBalance.toFixed(2)}</span>`;
        }
    });
    
    const remaining = window.adjustmentAmount - totalAdjusted;
    const balanceEl = document.getElementById('adjustmentBalance');
    balanceEl.textContent = `₹ ${remaining.toFixed(2)}`;
    balanceEl.style.color = remaining === 0 ? '#28a745' : (remaining < 0 ? '#dc3545' : '#0d6efd');
}

// Auto Distribute Amount
function autoDistributeAmount() {
    const totalAmount = parseFloat(document.getElementById('autoAdjustAmount').value || 0);
    
    if (totalAmount <= 0) {
        alert('Please enter a valid amount');
        return;
    }
    
    document.querySelectorAll('.adjustment-input').forEach(input => input.value = '');
    
    const inputs = Array.from(document.querySelectorAll('.adjustment-input'));
    const transactions = inputs.map(input => ({
        input: input,
        balance: parseFloat(input.getAttribute('data-balance'))
    })).filter(t => t.balance > 0).sort((a, b) => b.balance - a.balance);
    
    let remaining = totalAmount;
    
    transactions.forEach(t => {
        if (remaining <= 0) return;
        const adjustAmount = Math.min(remaining, t.balance);
        t.input.value = adjustAmount.toFixed(2);
        remaining -= adjustAmount;
    });
    
    updateAdjustmentBalance();
}

// Close Adjustment Modal
function closeAdjustmentModal() {
    const modal = document.getElementById('adjustmentModal');
    const backdrop = document.getElementById('adjustmentModalBackdrop');
    
    if (modal) modal.classList.remove('show');
    if (backdrop) backdrop.classList.remove('show');
    
    setTimeout(() => {
        if (modal) modal.remove();
        if (backdrop) backdrop.remove();
    }, 300);
    
    document.removeEventListener('keydown', handleAdjustmentEsc);
}

// Save Adjustment
function saveAdjustment() {
    const inputs = document.querySelectorAll('.adjustment-input');
    const adjustments = [];
    
    inputs.forEach(input => {
        const adjusted = parseFloat(input.value || 0);
        if (adjusted > 0) {
            adjustments.push({
                invoice_id: input.getAttribute('data-invoice-id'),
                adjusted_amount: adjusted
            });
        }
    });
    
    const remainingText = document.getElementById('adjustmentBalance').textContent.replace('₹', '').trim();
    const remaining = parseFloat(remainingText);
    
    if (remaining !== 0) {
        if (!confirm(`Balance remaining is ₹${remaining.toFixed(2)}. Continue anyway?`)) {
            return;
        }
    }
    
    closeAdjustmentModal();
    submitCreditNote(true, adjustments);
}

// Submit Credit Note
function submitCreditNote(withAdjustment = false, adjustments = []) {
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value;
        const amount = row.querySelector('.hsn-amount')?.value;
        
        if (hsnCode || parseFloat(amount) > 0) {
            items.push({
                hsn_code: hsnCode,
                amount: amount,
                gst_percent: row.querySelector('.hsn-gst')?.value || 0,
                cgst_percent: row.querySelector('.hsn-cgst-percent')?.value || 0,
                cgst_amount: row.querySelector('.hsn-cgst-amount')?.value || 0,
                sgst_percent: row.querySelector('.hsn-sgst-percent')?.value || 0,
                sgst_amount: row.querySelector('.hsn-sgst-amount')?.value || 0,
            });
        }
    });
    
    const partyType = document.querySelector('input[name="credit_party_type"]:checked').value;
    const partyId = document.getElementById('partySelect').value;
    const partyName = document.getElementById('partySelect').options[document.getElementById('partySelect').selectedIndex].text;
    
    const data = {
        header: {
            credit_note_date: document.getElementById('creditNoteDate').value,
            day_name: document.getElementById('dayName').value,
            credit_party_type: partyType,
            credit_party_id: partyId,
            credit_party_name: partyName,
            debit_account_type: document.querySelector('input[name="debit_account_type"]:checked').value,
            debit_account_no: document.getElementById('accountNo').value,
            inv_ref_no: document.getElementById('invRefNo').value,
            invoice_date: document.getElementById('invoiceDate').value || null,
            gst_vno: document.getElementById('gstVno').value,
            party_trn_no: document.getElementById('partyTrnNo').value,
            party_trn_date: document.getElementById('partyTrnDate').value || null,
            amount: document.getElementById('amount').value,
            salesman_id: document.getElementById('salesmanSelect').value || null,
            reason: document.getElementById('reason').value,
            gross_amount: document.getElementById('grossAmount').value,
            total_gst: document.getElementById('totalGst').value,
            net_amount: document.getElementById('netAmount').value,
            tcs_amount: document.getElementById('tcsAmount').value,
            round_off: document.getElementById('roundOff').value,
            cn_amount: document.getElementById('cnAmount').value,
            narration: document.getElementById('narration').value,
        },
        items: items,
        with_adjustment: withAdjustment,
        adjustments: adjustments
    };
    
    const creditNoteId = document.getElementById('creditNoteId').value;
    const url = creditNoteId 
        ? `{{ url('admin/credit-note') }}/${creditNoteId}`
        : '{{ route("admin.credit-note.store") }}';
    const method = creditNoteId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            if (window.printAfterSave) {
                window.open(`{{ url('admin/credit-note') }}/${result.id}/show`, '_blank');
            }
            window.location.href = '{{ route("admin.credit-note.invoices") }}';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving credit note');
    });
}
</script>

<style>
.readonly-field {
    background-color: #e9ecef !important;
    cursor: not-allowed;
}

/* HSN Modal Styles */
.hsn-modal {
    display: none;
    position: fixed;
    top: 60px;
    right: -450px;
    width: 450px;
    bottom: 45px;
    background: #fff;
    z-index: 1060;
    box-shadow: -5px 0 25px rgba(0,0,0,0.15);
    transition: right 0.3s ease;
    border-radius: 8px 0 0 8px;
    overflow: hidden;
}

.hsn-modal.show {
    right: 0;
}

.hsn-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    z-index: 1055;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.hsn-modal-backdrop.show {
    opacity: 1;
}

.hsn-modal-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.hsn-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #fff;
    border-bottom: 1px solid #e9ecef;
}

.hsn-modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.btn-close-modal {
    background: none;
    border: none;
    font-size: 20px;
    color: #666;
    cursor: pointer;
    padding: 5px;
    line-height: 1;
    transition: color 0.2s;
}

.btn-close-modal:hover {
    color: #dc3545;
}

.hsn-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #fff;
}

.hsn-modal-body .table {
    font-size: 13px;
}

.hsn-modal-body .table th {
    border-top: none;
    padding: 10px 8px;
    background: #f8f9fa;
    font-weight: 600;
}

.hsn-modal-body .table td {
    padding: 10px 8px;
    vertical-align: middle;
}

.hsn-modal-body .table tbody tr:hover {
    background-color: #f8f9fa;
}

.hsn-modal-body .badge {
    font-weight: 500;
    padding: 5px 10px;
}

.hsn-modal-body .btn-sm {
    padding: 4px 12px;
    font-size: 12px;
}

/* Save Options Modal Styles */
.save-options-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1070;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.save-options-modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
}

.save-options-modal-content {
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    width: 420px;
    max-width: 90%;
    transform: scale(0.8);
    transition: transform 0.3s ease;
}

.save-options-modal.show .save-options-modal-content {
    transform: scale(1);
}

.save-options-modal-header {
    padding: 15px 20px;
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 10px 10px 0 0;
}

.save-options-modal-header h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.save-options-close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    line-height: 1;
}

.save-options-modal-body {
    padding: 25px;
    text-align: center;
}

.save-options-modal-body p {
    font-size: 1rem;
    color: #495057;
    margin-bottom: 20px;
}

.save-options-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.save-options-buttons .btn {
    padding: 10px 20px;
    font-weight: 500;
}

/* Adjustment Modal Styles */
.adjustment-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1080;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.adjustment-modal-backdrop.show {
    display: block;
    opacity: 1;
}

.adjustment-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.8);
    width: 700px;
    max-width: 95%;
    z-index: 1085;
    opacity: 0;
    transition: all 0.3s ease;
}

.adjustment-modal.show {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

.adjustment-modal-content {
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.adjustment-modal-header {
    padding: 15px 20px;
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.adjustment-modal-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.adjustment-modal-body {
    padding: 20px;
    background: #fff;
}

.adjustment-modal-footer {
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.adjustment-input {
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.adjustment-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.15);
}
</style>
@endpush
