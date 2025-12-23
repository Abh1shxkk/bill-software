@extends('layouts.admin')

@section('title', 'Purchase Return Br.Expiry Adjustment - Modification')

@section('content')
<div class="classic-form-container">
    <!-- Title Header -->
    <div class="form-title-header">
        <h4 class="mb-0 fst-italic">Purchase Return Br.Expiry Adjustment - Modification</h4>
    </div>

    <div class="d-flex justify-content-end mb-2 gap-2">
        <button type="button" class="btn btn-sm btn-classic" onclick="showLoadInvoiceModal()">
            <i class="bi bi-folder-open me-1"></i> Load Invoices
        </button>
        <a href="{{ route('admin.breakage-supplier.received-transaction') }}" class="btn btn-sm btn-classic">
            <i class="bi bi-plus-circle me-1"></i> New
        </a>
    </div>

    <form id="receivedForm" method="POST" autocomplete="off">
        @csrf
        <input type="hidden" id="transaction_id" name="transaction_id">
        @method('PUT')
        
        <!-- Header Section -->
        <div class="form-section">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label mb-0">Date</label>
                    <input type="date" id="transaction_date" name="transaction_date" class="form-control form-control-sm classic-input" value="{{ date('Y-m-d') }}" style="width: 130px;">
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0">Trn.No.</label>
                    <input type="text" id="trn_no" name="trn_no" class="form-control form-control-sm classic-input" style="width: 80px;" readonly>
                </div>
                <div class="col-auto">
                    <input type="text" id="trn_no_suffix" class="form-control form-control-sm classic-input" style="width: 60px;" readonly>
                </div>
                <div class="col-auto ms-3">
                    <label class="form-label mb-0">Supplier</label>
                    <input type="text" id="supplier_code" name="supplier_code" class="form-control form-control-sm classic-input" style="width: 60px;" readonly>
                </div>
                <div class="col">
                    <input type="text" id="supplier_name" name="supplier_name" class="form-control form-control-sm classic-input" placeholder="Supplier Name" readonly>
                </div>
            </div>
            
            <div class="row g-2 align-items-end mt-2">
                <div class="col-auto">
                    <label class="form-label mb-0">Party Trn No.</label>
                    <input type="text" id="party_trn_no" name="party_trn_no" class="form-control form-control-sm classic-input" style="width: 140px;">
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0">Date</label>
                    <input type="date" id="party_date" name="party_date" class="form-control form-control-sm classic-input" value="{{ date('Y-m-d') }}" style="width: 130px;">
                </div>
            </div>
        </div>

        <!-- Second Row - O/S Amount, Claim, Debit Note -->
        <div class="form-section highlight-section">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0">O/S.Amount</label>
                    <input type="number" id="os_amount" name="os_amount" class="form-control form-control-sm classic-input text-end" value="0.00" style="width: 120px;" readonly>
                </div>
                <div class="col-auto ms-3">
                    <label class="form-label mb-0">Claim [ Y / N ]</label>
                    <input type="text" id="claim_flag" name="claim_flag" class="form-control form-control-sm classic-input text-center" value="Y" style="width: 40px;" maxlength="1">
                </div>
                <div class="col-auto">
                    <div class="form-check">
                        <input type="checkbox" id="received_debit_note" name="received_debit_note" class="form-check-input">
                        <label class="form-check-label" for="received_debit_note">Received as Debit Note</label>
                    </div>
                </div>
                <div class="col-auto ms-3">
                    <label class="form-label mb-0">Claim Amount</label>
                    <input type="number" id="claim_amount" name="claim_amount" class="form-control form-control-sm classic-input text-end" value="0.00" style="width: 100px;">
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-auto">
                    <span class="text-danger fw-bold small">Trn.No.</span>
                </div>
            </div>
        </div>

        <!-- HSN Table Section -->
        <div class="form-section p-0">
            <div class="d-flex justify-content-between align-items-center px-2 py-1 bg-light border-bottom">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="generateHSN()">
                    <u>G</u>enerate HSN
                </button>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-danger me-1" onclick="deleteSelectedRow()">
                        <u>D</u>elete Row
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllRows()">
                        Clear <u>A</u>ll
                    </button>
                </div>
            </div>
            
            <div class="table-responsive" style="max-height: 200px;">
                <table class="table table-bordered table-sm mb-0 classic-table">
                    <thead>
                        <tr class="table-header-dark">
                            <th style="width: 120px;">HSN Code</th>
                            <th style="width: 120px;" class="text-end">Amount</th>
                            <th style="width: 80px;" class="text-end">GST%</th>
                            <th style="width: 80px;" class="text-end">IGST %</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="hsnTableBody">
                        <tr class="hsn-row" data-row="0">
                            <td><input type="text" class="form-control form-control-sm border-0 bg-transparent" name="hsn[0][code]"></td>
                            <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[0][amount]" value="0.00"></td>
                            <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[0][gst_percent]" value="0.00"></td>
                            <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[0][igst_percent]" value="0.00"></td>
                            <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[0][tax_amount]" value="0.00" readonly></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Totals Section -->
        <div class="form-section footer-section">
            <div class="row">
                <div class="col-md-4">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="fw-bold" style="width: 100px;">Gross Amt.</td>
                            <td><input type="number" id="gross_amt" name="gross_amt" class="form-control form-control-sm classic-input text-end" value="0.00" readonly></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Total GST</td>
                            <td><input type="number" id="total_gst" name="total_gst" class="form-control form-control-sm classic-input text-end" value="0.00" readonly></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Net Amt.</td>
                            <td><input type="number" id="net_amt" name="net_amt" class="form-control form-control-sm classic-input text-end" value="0.00" readonly></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Round Off</td>
                            <td><input type="number" id="round_off" name="round_off" class="form-control form-control-sm classic-input text-end" value="0.00" step="0.01" onchange="calcTotals()"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Amount</td>
                            <td><input type="number" id="amount" name="amount" class="form-control form-control-sm classic-input text-end fw-bold" value="0.00" readonly></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-end mb-2">
                        <input type="number" id="final_amount" class="form-control form-control-sm classic-input text-end fw-bold" value="0.00" style="width: 150px;" readonly>
                    </div>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-12">
                    <label class="form-label mb-0 fw-bold">Remarks</label>
                    <input type="text" id="remarks" name="remarks" class="form-control form-control-sm classic-input">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="form-section text-end">
            <button type="button" class="btn btn-secondary px-4 me-2" onclick="updateTransaction()" id="updateBtn" disabled>Ok</button>
            <button type="button" class="btn btn-secondary px-4" onclick="cancelTransaction()">Close</button>
        </div>
    </form>
</div>

<!-- Load Invoice Modal -->
<div class="modal-backdrop-custom" id="invoiceModalBackdrop" onclick="closeInvoiceModal()"></div>
<div class="custom-modal" id="invoiceModal">
    <div class="custom-modal-header">
        <h5 class="custom-modal-title m-0 fs-6">Load Invoice</h5>
        <button type="button" class="btn-close" onclick="closeInvoiceModal()"></button>
    </div>
    <div class="custom-modal-body">
        <input type="text" id="invoiceSearchInput" class="form-control mb-3" placeholder="Search..." onkeyup="searchInvoices()">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-sm mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody id="invoicesListBody"></tbody>
            </table>
        </div>
    </div>
    <div class="custom-modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoiceModal()">Close</button>
    </div>
</div>

@push('styles')
<style>
.classic-form-container {
    background: linear-gradient(to bottom, #c5d9c5 0%, #a8c8a8 100%);
    min-height: calc(100vh - 60px);
    padding: 15px;
}

.form-title-header {
    background: linear-gradient(to bottom, #e8f0e8 0%, #d0e0d0 100%);
    border: 2px solid #8a9a8a;
    padding: 8px 15px;
    margin-bottom: 10px;
    text-align: center;
}

.form-title-header h4 {
    color: #2a4a2a;
    font-family: 'Times New Roman', serif;
    font-size: 1.6rem;
    text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
}

.form-section {
    background: #d8e8d8;
    border: 1px solid #8a9a8a;
    padding: 10px;
    margin-bottom: 8px;
}

.highlight-section {
    background: #e8f0e8;
    border: 2px solid #6a8a6a;
}

.classic-input {
    background: #fffff8 !important;
    border: 1px solid #8a9a8a !important;
    border-radius: 0 !important;
    font-size: 12px;
}

.classic-input:focus {
    background: #ffffd0 !important;
    border-color: #4a6a4a !important;
    box-shadow: none !important;
}

.btn-classic {
    background: linear-gradient(to bottom, #e0e0e0 0%, #c0c0c0 100%);
    border: 1px solid #808080;
    color: #000;
    border-radius: 0;
    font-size: 12px;
}

.btn-classic:hover {
    background: linear-gradient(to bottom, #d0d0d0 0%, #b0b0b0 100%);
    color: #000;
}

.classic-table {
    font-size: 12px;
    background: #e0ece0;
}

.classic-table th, .classic-table td {
    border: 1px solid #6a8a6a !important;
    padding: 4px 6px;
    vertical-align: middle;
}

.table-header-dark {
    background: linear-gradient(to bottom, #2a5a2a 0%, #1a4a1a 100%);
    color: #fff;
}

.table-header-dark th {
    font-weight: normal;
    border-color: #1a3a1a !important;
}

.classic-table tbody tr {
    background: #c8dcc8;
}

.classic-table tbody tr:nth-child(even) {
    background: #d8e8d8;
}

.classic-table tbody tr:hover {
    background: #b8d0b8;
}

.classic-table tbody tr.selected {
    background: #a0c8a0 !important;
}

.footer-section {
    background: #d0e0d0;
}

.form-label {
    font-size: 12px;
    font-weight: 500;
    color: #2a4a2a;
}

.btn-secondary {
    background: linear-gradient(to bottom, #e0e0e0 0%, #c0c0c0 100%);
    border: 1px solid #808080;
    color: #000;
    border-radius: 0;
    font-size: 12px;
}

.btn-secondary:hover {
    background: linear-gradient(to bottom, #d0d0d0 0%, #b0b0b0 100%);
    border-color: #606060;
    color: #000;
}

.btn-outline-secondary, .btn-outline-danger {
    border-radius: 0;
    font-size: 11px;
}

.form-check-input {
    border-radius: 0;
}

.hsn-row input {
    background: transparent !important;
}

.hsn-row input:focus {
    background: #ffffd0 !important;
}

/* Modal Styles */
.modal-backdrop-custom {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1050;
}

.modal-backdrop-custom.show {
    display: block;
}

.custom-modal {
    display: none;
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 90%; max-width: 700px;
    background: white;
    border-radius: 4px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    z-index: 1055;
}

.custom-modal.show {
    display: block;
}

.custom-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 15px;
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
}

.custom-modal-body {
    padding: 15px;
    max-height: 60vh;
    overflow-y: auto;
}

.custom-modal-footer {
    display: flex;
    justify-content: flex-end;
    padding: 10px 15px;
    border-top: 1px solid #dee2e6;
    background: #f8f9fa;
}
</style>
@endpush

<script>
let hsnRowIndex = 1;
let selectedHsnRow = null;
let currentTransactionId = null;

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.hsn-row').forEach(row => {
        row.addEventListener('click', function() { selectHsnRow(this); });
    });
    document.querySelectorAll('#hsnTableBody input').forEach(input => {
        input.addEventListener('change', calcTotals);
    });
});

function showLoadInvoiceModal() {
    document.getElementById('invoiceModalBackdrop').classList.add('show');
    document.getElementById('invoiceModal').classList.add('show');
    loadInvoices();
}

function closeInvoiceModal() {
    document.getElementById('invoiceModalBackdrop').classList.remove('show');
    document.getElementById('invoiceModal').classList.remove('show');
}

function loadInvoices(search = '') {
    fetch(`{{ route('admin.breakage-supplier.get-received-past-invoices') }}?search=${encodeURIComponent(search)}`)
        .then(r => r.json())
        .then(invoices => {
            const tbody = document.getElementById('invoicesListBody');
            if (!invoices.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">No invoices found</td></tr>';
                return;
            }
            tbody.innerHTML = invoices.map(inv => `
                <tr style="cursor: pointer;" onclick="loadInvoice(${inv.id})">
                    <td><strong>${inv.id}</strong></td>
                    <td>${inv.transaction_date}</td>
                    <td>${inv.supplier_name || '-'}</td>
                    <td class="text-end">${parseFloat(inv.amount || 0).toFixed(2)}</td>
                </tr>`).join('');
        });
}

function searchInvoices() {
    loadInvoices(document.getElementById('invoiceSearchInput').value);
}

function loadInvoice(id) {
    fetch(`{{ url('admin/breakage-supplier/received') }}/${id}`)
        .then(r => r.json())
        .then(data => {
            populateForm(data);
            closeInvoiceModal();
        })
        .catch(console.error);
}

function populateForm(data) {
    currentTransactionId = data.id;
    document.getElementById('transaction_id').value = data.id;
    document.getElementById('transaction_date').value = data.transaction_date;
    document.getElementById('trn_no').value = data.id;
    document.getElementById('supplier_name').value = data.supplier_name || '';
    document.getElementById('party_trn_no').value = data.party_trn_no || '';
    document.getElementById('party_date').value = data.party_date || data.transaction_date;
    document.getElementById('claim_flag').value = data.claim_flag || 'Y';
    document.getElementById('claim_amount').value = data.claim_amount || 0;
    document.getElementById('received_debit_note').checked = !!data.received_debit_note;
    document.getElementById('os_amount').value = data.os_amount || 0;
    document.getElementById('remarks').value = data.remarks || '';
    document.getElementById('round_off').value = data.round_off || 0;
    document.getElementById('gross_amt').value = data.gross_amt || 0;
    document.getElementById('total_gst').value = data.total_gst || 0;
    document.getElementById('amount').value = data.amount || 0;
    document.getElementById('final_amount').value = data.amount || 0;
    document.getElementById('net_amt').value = (parseFloat(data.gross_amt || 0) + parseFloat(data.total_gst || 0)).toFixed(2);
    
    document.getElementById('updateBtn').disabled = false;
}

function selectHsnRow(row) {
    document.querySelectorAll('.hsn-row').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    selectedHsnRow = row;
}

function generateHSN() {
    const tbody = document.getElementById('hsnTableBody');
    const newRow = document.createElement('tr');
    newRow.className = 'hsn-row';
    newRow.dataset.row = hsnRowIndex;
    newRow.innerHTML = `
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent" name="hsn[${hsnRowIndex}][code]"></td>
        <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[${hsnRowIndex}][amount]" value="0.00" onchange="calcTotals()"></td>
        <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[${hsnRowIndex}][gst_percent]" value="0.00" onchange="calcRowTax(${hsnRowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[${hsnRowIndex}][igst_percent]" value="0.00" onchange="calcRowTax(${hsnRowIndex})"></td>
        <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[${hsnRowIndex}][tax_amount]" value="0.00" readonly></td>
    `;
    newRow.addEventListener('click', function() { selectHsnRow(this); });
    tbody.appendChild(newRow);
    hsnRowIndex++;
}

function calcRowTax(idx) {
    const row = document.querySelector(`.hsn-row[data-row="${idx}"]`);
    if (!row) return;
    const amount = parseFloat(row.querySelector('input[name*="[amount]"]').value) || 0;
    const gstP = parseFloat(row.querySelector('input[name*="[gst_percent]"]').value) || 0;
    const igstP = parseFloat(row.querySelector('input[name*="[igst_percent]"]').value) || 0;
    const taxAmt = amount * (gstP + igstP) / 100;
    row.querySelector('input[name*="[tax_amount]"]').value = taxAmt.toFixed(2);
    calcTotals();
}

function deleteSelectedRow() {
    if (selectedHsnRow && document.querySelectorAll('.hsn-row').length > 1) {
        selectedHsnRow.remove();
        selectedHsnRow = null;
        calcTotals();
    } else {
        alert('Cannot delete the last row');
    }
}

function clearAllRows() {
    if (confirm('Clear all rows?')) {
        const tbody = document.getElementById('hsnTableBody');
        tbody.innerHTML = `
            <tr class="hsn-row" data-row="0">
                <td><input type="text" class="form-control form-control-sm border-0 bg-transparent" name="hsn[0][code]"></td>
                <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[0][amount]" value="0.00" onchange="calcTotals()"></td>
                <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[0][gst_percent]" value="0.00" onchange="calcRowTax(0)"></td>
                <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[0][igst_percent]" value="0.00" onchange="calcRowTax(0)"></td>
                <td><input type="number" class="form-control form-control-sm border-0 bg-transparent text-end" name="hsn[0][tax_amount]" value="0.00" readonly></td>
            </tr>
        `;
        hsnRowIndex = 1;
        selectedHsnRow = null;
        calcTotals();
    }
}

function calcTotals() {
    let grossAmt = 0, totalGst = 0;
    document.querySelectorAll('.hsn-row').forEach(row => {
        const amount = parseFloat(row.querySelector('input[name*="[amount]"]').value) || 0;
        const taxAmt = parseFloat(row.querySelector('input[name*="[tax_amount]"]').value) || 0;
        grossAmt += amount;
        totalGst += taxAmt;
    });
    const roundOff = parseFloat(document.getElementById('round_off').value) || 0;
    const netAmt = grossAmt + totalGst;
    const finalAmt = netAmt + roundOff;
    
    document.getElementById('gross_amt').value = grossAmt.toFixed(2);
    document.getElementById('total_gst').value = totalGst.toFixed(2);
    document.getElementById('net_amt').value = netAmt.toFixed(2);
    document.getElementById('amount').value = finalAmt.toFixed(2);
    document.getElementById('final_amount').value = finalAmt.toFixed(2);
}

function updateTransaction() {
    if (!currentTransactionId) {
        alert('Please load an invoice first');
        return;
    }
    const formData = new FormData(document.getElementById('receivedForm'));
    fetch(`{{ url('admin/breakage-supplier/received') }}/${currentTransactionId}`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Transaction updated successfully!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(() => alert('Error updating transaction'));
}

function cancelTransaction() {
    window.location.href = '{{ route("admin.breakage-supplier.received-transaction") }}';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'F2') {
        e.preventDefault();
        generateHSN();
    }
});
</script>
@endsection
