@extends('layouts.admin')

@section('title', 'Debit Note Modification')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Debit Note Modification</h4>
        <div class="text-muted small">Edit existing debit note</div>
    </div>
    <div>
        <a href="{{ route('admin.debit-note.invoices') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list me-1"></i> View All
        </a>
        <a href="{{ route('admin.debit-note.transaction') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New
        </a>
    </div>
</div>

<!-- Search Section -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="searchDebitNoteNo" class="form-label">Debit Note No.</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchDebitNoteNo" placeholder="Enter Debit Note No." 
                           value="{{ $preloadDebitNoteNo ?? '' }}">
                    <button class="btn btn-danger" type="button" onclick="searchDebitNote()">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-info w-100" type="button" onclick="openDebitNotesModal()">
                    <i class="bi bi-list-ul me-1"></i> Browse
                </button>
            </div>
        </div>
    </div>
</div>

<form id="debitNoteForm" autocomplete="off">
    @csrf
    
    <!-- Header Section -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light py-2 border-bottom">
            <h6 class="mb-0 text-dark"><i class="bi bi-info-circle me-2"></i> Debit Note Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="debitNoteDate" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="debitNoteDate" name="debit_note_date" 
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label for="dayName" class="form-label">Day</label>
                    <input type="text" class="form-control readonly-field" id="dayName" readonly>
                </div>
                <div class="col-md-2">
                    <label for="debitNoteNo" class="form-label">Debit Note No.</label>
                    <input type="text" class="form-control readonly-field" id="debitNoteNo" readonly>
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
        <div class="card-header bg-light py-2 border-bottom">
            <h6 class="mb-0 text-dark"><i class="bi bi-people me-2"></i> Party Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Debit Section (Left) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                        <h6 class="text-dark mb-3"><i class="bi bi-arrow-up-circle me-1"></i> Debit (Party)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="debit_party_type" id="partySupplier" value="S" checked>
                                    <label class="btn btn-outline-secondary" for="partySupplier">Supplier</label>
                                    <input type="radio" class="btn-check" name="debit_party_type" id="partyCustomer" value="C">
                                    <label class="btn btn-outline-secondary" for="partyCustomer">Customer</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="partySelect" class="form-label">Party Name <span class="text-danger">*</span></label>
                                <select class="form-select no-select2" id="partySelect" name="debit_party_id">
                                    <option value="">Type to search...</option>
                                </select>
                                <small class="text-muted">Start typing to search for suppliers</small>
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

                <!-- Credit Section (Right) -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                        <h6 class="text-dark mb-3"><i class="bi bi-arrow-down-circle me-1"></i> Credit (Account)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Account Type</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="credit_account_type" id="accountPurchase" value="P" checked>
                                    <label class="btn btn-outline-secondary" for="accountPurchase">Purchase</label>
                                    <input type="radio" class="btn-check" name="credit_account_type" id="accountSale" value="S">
                                    <label class="btn btn-outline-secondary" for="accountSale">Sale</label>
                                    <input type="radio" class="btn-check" name="credit_account_type" id="accountGeneral" value="G">
                                    <label class="btn btn-outline-secondary" for="accountGeneral">General</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="accountNo" class="form-label">Account No</label>
                                <input type="text" class="form-control" id="accountNo" name="credit_account_no">
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
        <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-dark"><i class="bi bi-table me-2"></i> HSN Details</h6>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addHsnRow()">
                <i class="bi bi-plus-circle me-1"></i> Add Row
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="hsnTable" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 120px;">HSN Code</th>
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
                        <!-- Rows will be added dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Calculation Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light py-2 border-bottom">
                    <h6 class="mb-0 text-dark"><i class="bi bi-chat-text me-2"></i> Narration</h6>
                </div>
                <div class="card-body">
                    <textarea class="form-control" id="narration" name="narration" rows="3" placeholder="Enter narration..."></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light py-2 border-bottom">
                    <h6 class="mb-0 text-dark"><i class="bi bi-calculator me-2"></i> Summary</h6>
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
                            <label class="form-label small fw-bold text-danger">DN Amount</label>
                            <input type="number" class="form-control form-control-sm fw-bold text-danger readonly-field" id="dnAmount" name="dn_amount" value="0.00" readonly style="font-size: 16px;">
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
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('admin.debit-note.invoices') }}'">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="deleteDebitNoteBtn" style="display: none;" onclick="deleteDebitNote()">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" id="updateBtn" onclick="showSaveOptionsModal()" disabled>
                        <i class="bi bi-check-circle me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<input type="hidden" id="debitNoteId" value="">

<!-- Browse Debit Notes Modal - Custom -->
<div class="custom-modal-backdrop" id="debitNotesModalBackdrop" onclick="closeDebitNotesModal()"></div>
<div class="custom-modal" id="debitNotesModal">
    <div class="custom-modal-header" style="background: linear-gradient(135deg, #dc3545, #c82333);">
        <h5><i class="bi bi-list-ul me-2"></i> Select Debit Note</h5>
        <button type="button" class="custom-modal-close" onclick="closeDebitNotesModal()">&times;</button>
    </div>
    <div class="custom-modal-body">
        <!-- Search Box -->
        <div class="mb-3">
            <input type="text" class="form-control" id="modalSearchInput" placeholder="Search by DN No., Party Name..." oninput="searchDebitNotesInModal()">
        </div>
        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
            <table class="table table-hover table-sm">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th>DN No.</th>
                        <th>Date</th>
                        <th>Party</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="debitNotesModalBody">
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Save Options Modal -->
<div class="custom-modal-backdrop" id="saveOptionsModalBackdrop" onclick="closeSaveOptionsModal()"></div>
<div class="custom-modal" id="saveOptionsModal" style="width: 400px;">
    <div class="custom-modal-header" style="background: linear-gradient(135deg, #6c757d, #495057);">
        <h5><i class="bi bi-save me-2"></i> Save Options</h5>
        <button type="button" class="custom-modal-close" onclick="closeSaveOptionsModal()">&times;</button>
    </div>
    <div class="custom-modal-body text-center py-4">
        <p class="mb-4">How would you like to save this debit note?</p>
        <div class="d-grid gap-2">
            <button type="button" class="btn btn-primary btn-lg" onclick="saveWithoutAdjustment()">
                <i class="bi bi-save me-2"></i> Save Without Adjustment
            </button>
            <button type="button" class="btn btn-success btn-lg" onclick="saveWithAdjustment()" id="saveWithAdjustmentBtn">
                <i class="bi bi-sliders me-2"></i> Save With Adjustment
            </button>
        </div>
    </div>
</div>

<!-- Adjustment Modal - Custom -->
<div class="custom-modal-backdrop" id="adjustmentModalBackdrop" onclick="closeAdjustmentModal()"></div>
<div class="custom-modal" id="adjustmentModal" style="width: 850px;">
    <div class="custom-modal-header" style="background: linear-gradient(135deg, #dc3545, #c82333);">
        <h5><i class="bi bi-sliders me-2"></i> Debit Note Adjustment</h5>
        <button type="button" class="custom-modal-close" onclick="closeAdjustmentModal()">&times;</button>
    </div>
    <div class="custom-modal-body">
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-hover table-sm">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 50px;">SR.</th>
                        <th style="width: 80px;">TYPE</th>
                        <th>TRANS NO.</th>
                        <th>DATE</th>
                        <th class="text-end">BALANCE AMT.</th>
                        <th style="width: 120px;">ADJUST</th>
                        <th class="text-end">REMAINING</th>
                    </tr>
                </thead>
                <tbody id="adjustmentModalBody">
                    <tr><td colspan="7" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-3 p-3 bg-light rounded">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-muted">Press ESC to close</span>
                </div>
                <div class="col-md-6 text-end">
                    <strong class="text-danger" style="font-size: 16px;">
                        NET AMOUNT TO ADJUST: ₹ <span id="adjustmentBalance">0.00</span>
                    </strong>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Auto Adjust</span>
                        <input type="number" class="form-control" id="autoAdjustAmount" step="0.01" placeholder="Amount">
                        <button type="button" class="btn btn-info" onclick="autoDistributeAmount()">
                            <i class="bi bi-magic"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="custom-modal-footer" style="padding: 15px 20px; background: #f8f9fa; border-top: 1px solid #e9ecef; display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeAdjustmentModal()">Cancel</button>
        <button type="button" class="btn btn-success btn-sm" onclick="saveAdjustments()">
            <i class="bi bi-check-circle me-1"></i> Save Adjustments
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
let hsnRowCount = 0;
let currentDebitNoteId = null;
let currentPartyType = 'S'; // S = Supplier, C = Customer

document.addEventListener('DOMContentLoaded', function() {
    updateDayName();
    
    // Initialize Select2 AJAX for party dropdown
    initPartySelect2();
    
    document.getElementById('debitNoteDate').addEventListener('change', updateDayName);
    
    document.querySelectorAll('input[name="debit_party_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentPartyType = this.value;
            updatePartyDropdown();
        });
    });
    
    @if($preloadDebitNoteNo)
        searchDebitNote();
    @endif
});

// Initialize Select2 with AJAX for party dropdown
function initPartySelect2() {
    const $partySelect = $('#partySelect');
    
    // Destroy existing Select2 if any
    if ($partySelect.hasClass('select2-hidden-accessible')) {
        $partySelect.select2('destroy');
    }
    
    // Clear the select
    $partySelect.empty().append('<option value="">Type to search...</option>');
    
    const searchUrl = '{{ route("admin.debit-note.search-parties") }}';
    console.log('Initializing Party Select2 with URL:', searchUrl, 'Party Type:', currentPartyType);
    
    $partySelect.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: currentPartyType === 'S' ? 'Search supplier...' : 'Search customer...',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: searchUrl,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                console.log('Making AJAX request with:', params.term, currentPartyType);
                return {
                    q: params.term || '',
                    party_type: currentPartyType,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                console.log('Received results:', data);
                params.page = params.page || 1;
                return {
                    results: data.results || [],
                    pagination: {
                        more: data.pagination ? data.pagination.more : false
                    }
                };
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
            },
            cache: true
        },
        language: {
            searching: function() {
                return 'Searching...';
            },
            noResults: function() {
                return 'No results found';
            },
            loadingMore: function() {
                return 'Loading more...';
            },
            errorLoading: function() {
                return 'Error loading results';
            }
        }
    }).on('select2:select', function(e) {
        const selectedData = e.params.data;
        if (selectedData) {
            console.log('Selected party:', selectedData);
        }
    });
}

function updateDayName() {
    const dateInput = document.getElementById('debitNoteDate');
    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        document.getElementById('dayName').value = days[date.getDay()];
    }
}

function updatePartyDropdown() {
    // Clear current selection
    $('#partySelect').val(null).trigger('change');
    
    // Update help text
    const helpText = document.querySelector('#partySelect + small');
    if (helpText) {
        helpText.textContent = currentPartyType === 'S' ? 'Start typing to search for suppliers' : 'Start typing to search for customers';
    }
    
    // Reinitialize Select2 with updated party type
    initPartySelect2();
}

function searchDebitNote() {
    const debitNoteNo = document.getElementById('searchDebitNoteNo').value.trim();
    if (!debitNoteNo) {
        alert('Please enter Debit Note No.');
        return;
    }
    
    fetch(`{{ url('admin/debit-note/fetch') }}/${encodeURIComponent(debitNoteNo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.debit_note) {
                populateDebitNoteData(data.debit_note);
            } else {
                alert(data.message || 'Debit note not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching debit note');
        });
}

function populateDebitNoteData(dn) {
    console.log('Populating debit note data:', dn);
    
    document.getElementById('debitNoteId').value = dn.id;
    document.getElementById('debitNoteNo').value = dn.debit_note_no;
    document.getElementById('debitNoteDate').value = dn.debit_note_date ? dn.debit_note_date.split('T')[0] : '';
    updateDayName();
    
    // Set party type
    currentPartyType = dn.debit_party_type || 'S';
    if (dn.debit_party_type === 'C') {
        document.getElementById('partyCustomer').checked = true;
    } else {
        document.getElementById('partySupplier').checked = true;
    }
    
    // Initialize Select2 with the current party type
    initPartySelect2();
    
    // Pre-select the party in Select2 - use setTimeout to ensure Select2 is fully initialized
    setTimeout(function() {
        if (dn.debit_party_id && dn.debit_party_name) {
            const $partySelect = $('#partySelect');
            // Clear existing options first
            $partySelect.empty();
            // Create and append the option
            const newOption = new Option(dn.debit_party_name, dn.debit_party_id, true, true);
            $partySelect.append(newOption).trigger('change');
            console.log('Pre-selected party:', dn.debit_party_name, dn.debit_party_id);
        }
        
        // Set salesman and reason using jQuery for Select2 compatibility
        if (dn.salesman_id) {
            $('#salesmanSelect').val(dn.salesman_id).trigger('change');
            console.log('Pre-selected salesman:', dn.salesman_id);
        }
        if (dn.reason) {
            $('#reason').val(dn.reason).trigger('change');
            console.log('Pre-selected reason:', dn.reason);
        }
    }, 100);
    
    if (dn.credit_account_type === 'S') {
        document.getElementById('accountSale').checked = true;
    } else if (dn.credit_account_type === 'G') {
        document.getElementById('accountGeneral').checked = true;
    } else {
        document.getElementById('accountPurchase').checked = true;
    }
    
    document.getElementById('accountNo').value = dn.credit_account_no || '';
    document.getElementById('invRefNo').value = dn.inv_ref_no || '';
    document.getElementById('invoiceDate').value = dn.invoice_date ? dn.invoice_date.split('T')[0] : '';
    document.getElementById('gstVno').value = dn.gst_vno || '';
    document.getElementById('partyTrnNo').value = dn.party_trn_no || '';
    document.getElementById('partyTrnDate').value = dn.party_trn_date ? dn.party_trn_date.split('T')[0] : '';
    document.getElementById('amount').value = dn.amount || 0;
    
    document.getElementById('grossAmount').value = dn.gross_amount || 0;
    document.getElementById('totalGst').value = dn.total_gst || 0;
    document.getElementById('netAmount').value = dn.net_amount || 0;
    document.getElementById('tcsAmount').value = dn.tcs_amount || 0;
    document.getElementById('roundOff').value = dn.round_off || 0;
    document.getElementById('dnAmount').value = dn.dn_amount || 0;
    document.getElementById('narration').value = dn.narration || '';
    
    const tbody = document.getElementById('hsnTableBody');
    tbody.innerHTML = '';
    hsnRowCount = 0;
    
    if (dn.items && dn.items.length > 0) {
        dn.items.forEach((item, index) => {
            addHsnRowWithData(item);
        });
    } else {
        addHsnRow();
    }
    
    document.getElementById('updateBtn').disabled = false;
    document.getElementById('deleteDebitNoteBtn').style.display = 'inline-block';
    
    // Store current debit note ID for adjustments
    currentDebitNoteId = dn.id;
}

function addHsnRow() {
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', hsnRowCount);
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm hsn-code" name="items[${hsnRowCount}][hsn_code]"></td>
        <td><input type="number" class="form-control form-control-sm hsn-amount" name="items[${hsnRowCount}][amount]" step="0.01" value="0.00" onchange="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-gst" name="items[${hsnRowCount}][gst_percent]" step="0.01" value="0.00" onchange="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-percent readonly-field" name="items[${hsnRowCount}][cgst_percent]" step="0.01" value="0.00" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-amount readonly-field" name="items[${hsnRowCount}][cgst_amount]" step="0.01" value="0.00" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-percent readonly-field" name="items[${hsnRowCount}][sgst_percent]" step="0.01" value="0.00" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-amount readonly-field" name="items[${hsnRowCount}][sgst_amount]" step="0.01" value="0.00" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteHsnRow(${hsnRowCount})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    hsnRowCount++;
}

function addHsnRowWithData(item) {
    const tbody = document.getElementById('hsnTableBody');
    const row = document.createElement('tr');
    row.setAttribute('data-row', hsnRowCount);
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm hsn-code" name="items[${hsnRowCount}][hsn_code]" value="${item.hsn_code || ''}"></td>
        <td><input type="number" class="form-control form-control-sm hsn-amount" name="items[${hsnRowCount}][amount]" step="0.01" value="${item.amount || 0}" onchange="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-gst" name="items[${hsnRowCount}][gst_percent]" step="0.01" value="${item.gst_percent || 0}" onchange="calculateGst(${hsnRowCount})"></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-percent readonly-field" name="items[${hsnRowCount}][cgst_percent]" step="0.01" value="${item.cgst_percent || 0}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-cgst-amount readonly-field" name="items[${hsnRowCount}][cgst_amount]" step="0.01" value="${item.cgst_amount || 0}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-percent readonly-field" name="items[${hsnRowCount}][sgst_percent]" step="0.01" value="${item.sgst_percent || 0}" readonly></td>
        <td><input type="number" class="form-control form-control-sm hsn-sgst-amount readonly-field" name="items[${hsnRowCount}][sgst_amount]" step="0.01" value="${item.sgst_amount || 0}" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteHsnRow(${hsnRowCount})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    hsnRowCount++;
}

function deleteHsnRow(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

function calculateGst(rowIndex) {
    const row = document.querySelector(`tr[data-row="${rowIndex}"]`);
    if (!row) return;
    
    const amount = parseFloat(row.querySelector('.hsn-amount').value) || 0;
    const gstPercent = parseFloat(row.querySelector('.hsn-gst').value) || 0;
    
    const cgstPercent = gstPercent / 2;
    const sgstPercent = gstPercent / 2;
    const cgstAmount = (amount * cgstPercent) / 100;
    const sgstAmount = (amount * sgstPercent) / 100;
    
    row.querySelector('.hsn-cgst-percent').value = cgstPercent.toFixed(2);
    row.querySelector('.hsn-cgst-amount').value = cgstAmount.toFixed(2);
    row.querySelector('.hsn-sgst-percent').value = sgstPercent.toFixed(2);
    row.querySelector('.hsn-sgst-amount').value = sgstAmount.toFixed(2);
    
    calculateTotals();
}

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
    const dnAmount = netAmount + tcsAmount + roundOff;
    
    document.getElementById('grossAmount').value = grossAmount.toFixed(2);
    document.getElementById('totalGst').value = totalGst.toFixed(2);
    document.getElementById('netAmount').value = netAmount.toFixed(2);
    document.getElementById('dnAmount').value = dnAmount.toFixed(2);
}

function updateDebitNote() {
    const debitNoteId = document.getElementById('debitNoteId')?.value;
    if (!debitNoteId) {
        alert('Please load a debit note first');
        return;
    }
    
    // Helper function to safely get element value
    const getVal = (id) => document.getElementById(id)?.value || '';
    const getChecked = (name) => document.querySelector(`input[name="${name}"]:checked`)?.value || '';
    
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value || row.querySelector('input[name*="hsn_code"]')?.value;
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
    
    // Get party name from Select2 selected data
    const $partySelect = $('#partySelect');
    const selectedData = $partySelect.select2('data')[0];
    const partyName = selectedData ? selectedData.text : '';
    
    const data = {
        header: {
            debit_note_date: getVal('debitNoteDate'),
            day_name: getVal('dayName'),
            debit_party_type: getChecked('debit_party_type'),
            debit_party_id: getVal('partySelect'),
            debit_party_name: partyName,
            credit_account_type: getChecked('credit_account_type'),
            credit_account_no: getVal('accountNo'),
            inv_ref_no: getVal('invRefNo'),
            invoice_date: getVal('invoiceDate') || null,
            gst_vno: getVal('gstVno'),
            party_trn_no: getVal('partyTrnNo'),
            party_trn_date: getVal('partyTrnDate') || null,
            amount: getVal('amount'),
            salesman_id: getVal('salesmanSelect') || null,
            reason: getVal('reason'),
            gross_amount: getVal('grossAmount'),
            total_gst: getVal('totalGst'),
            net_amount: getVal('netAmount'),
            tcs_amount: getVal('tcsAmount'),
            round_off: getVal('roundOff'),
            dn_amount: getVal('dnAmount'),
            narration: getVal('narration'),
        },
        items: items
    };
    
    fetch(`{{ url('admin/debit-note') }}/${debitNoteId}`, {
        method: 'PUT',
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
            window.location.href = '{{ route("admin.debit-note.invoices") }}';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating debit note');
    });
}

function deleteDebitNote() {
    const debitNoteId = document.getElementById('debitNoteId').value;
    if (!debitNoteId) return;
    
    if (!confirm('Are you sure you want to delete this debit note?')) return;
    
    fetch(`{{ url('admin/debit-note') }}/${debitNoteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            window.location.href = '{{ route("admin.debit-note.invoices") }}';
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting debit note');
    });
}

let allDebitNotes = [];

function openDebitNotesModal() {
    document.getElementById('debitNotesModalBackdrop').classList.add('show');
    document.getElementById('debitNotesModal').classList.add('show');
    
    document.getElementById('modalSearchInput').value = '';
    document.getElementById('debitNotesModalBody').innerHTML = '<tr><td colspan="5" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
    
    // Fetch all debit notes
    fetch('{{ route("admin.debit-note.invoices") }}?all=1', {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.data) {
            allDebitNotes = data.data;
        } else if (Array.isArray(data)) {
            allDebitNotes = data;
        } else {
            allDebitNotes = [];
        }
        renderDebitNotesInModal(allDebitNotes);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('debitNotesModalBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
    });
}

function closeDebitNotesModal() {
    document.getElementById('debitNotesModalBackdrop').classList.remove('show');
    document.getElementById('debitNotesModal').classList.remove('show');
}

function renderDebitNotesInModal(debitNotes) {
    const tbody = document.getElementById('debitNotesModalBody');
    
    if (!debitNotes || debitNotes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No debit notes found</td></tr>';
        return;
    }
    
    tbody.innerHTML = debitNotes.map(dn => `
        <tr>
            <td><strong>${dn.debit_note_no || ''}</strong></td>
            <td>${dn.debit_note_date ? new Date(dn.debit_note_date).toLocaleDateString('en-IN') : ''}</td>
            <td>${dn.debit_party_name || ''}</td>
            <td class="text-end">₹ ${parseFloat(dn.net_amount || 0).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="selectDebitNoteFromModal('${dn.debit_note_no}')">
                    <i class="bi bi-check"></i> Select
                </button>
            </td>
        </tr>
    `).join('');
}

function searchDebitNotesInModal() {
    const searchTerm = document.getElementById('modalSearchInput').value.toLowerCase().trim();
    
    if (!searchTerm) {
        renderDebitNotesInModal(allDebitNotes);
        return;
    }
    
    const filtered = allDebitNotes.filter(dn => {
        const dnNo = (dn.debit_note_no || '').toString().toLowerCase();
        const partyName = (dn.debit_party_name || '').toLowerCase();
        return dnNo.includes(searchTerm) || partyName.includes(searchTerm);
    });
    
    renderDebitNotesInModal(filtered);
}

function selectDebitNoteFromModal(debitNoteNo) {
    // Close modal
    closeDebitNotesModal();
    
    // Set the debit note number and search
    document.getElementById('searchDebitNoteNo').value = debitNoteNo;
    searchDebitNote();
}

// ============ ADJUSTMENT MODAL FUNCTIONS ============
let allInvoices = [];
let existingAdjustments = [];
let dnAmount = 0;

function openAdjustmentModal() {
    const debitNoteId = document.getElementById('debitNoteId').value;
    const partyId = document.getElementById('partySelect').value;
    const partyType = document.querySelector('input[name="debit_party_type"]:checked').value;
    
    if (!debitNoteId || !partyId) {
        alert('Please load a debit note first');
        return;
    }
    
    if (partyType !== 'S') {
        alert('Adjustment is only available for Supplier party type');
        return;
    }
    
    dnAmount = parseFloat(document.getElementById('dnAmount').value) || 0;
    
    document.getElementById('adjustmentModalBackdrop').classList.add('show');
    document.getElementById('adjustmentModal').classList.add('show');
    document.getElementById('adjustmentModalBody').innerHTML = '<tr><td colspan="7" class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';
    document.getElementById('autoAdjustAmount').value = dnAmount.toFixed(2);
    
    // Fetch invoices and existing adjustments
    Promise.all([
        fetch(`{{ url('admin/debit-note/supplier') }}/${partyId}/purchase-invoices`).then(r => r.json()),
        fetch(`{{ url('admin/debit-note/supplier') }}/${partyId}/credit-notes`).then(r => r.json()),
        fetch(`{{ url('admin/debit-note') }}/${debitNoteId}/adjustments`).then(r => r.json())
    ])
    .then(([purchaseData, creditNoteData, adjustmentsData]) => {
        allInvoices = [];
        
        // Add purchase invoices
        if (purchaseData.success && purchaseData.invoices) {
            purchaseData.invoices.forEach(inv => {
                allInvoices.push({
                    id: inv.id,
                    type: 'PURCHASE',
                    trans_no: inv.bill_no || inv.trans_no || '',
                    date: inv.bill_date_formatted || '',
                    bill_amount: parseFloat(inv.inv_amount || 0),
                    balance: parseFloat(inv.balance_amount || inv.inv_amount || 0)
                });
            });
        }
        
        // Add credit notes
        if (creditNoteData.success && creditNoteData.credit_notes) {
            creditNoteData.credit_notes.forEach(cn => {
                allInvoices.push({
                    id: cn.id,
                    type: 'CREDIT_NOTE',
                    trans_no: cn.credit_note_no || '',
                    date: cn.credit_note_date_formatted || '',
                    bill_amount: parseFloat(cn.cn_amount || 0),
                    balance: parseFloat(cn.balance_amount || cn.cn_amount || 0)
                });
            });
        }
        
        if (adjustmentsData.success) {
            existingAdjustments = adjustmentsData.adjustments || [];
        } else {
            existingAdjustments = [];
        }
        
        renderAdjustmentTable();
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('adjustmentModalBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
    });
    
    // ESC key listener
    document.addEventListener('keydown', handleAdjustmentEsc);
}

function handleAdjustmentEsc(e) {
    if (e.key === 'Escape') {
        closeAdjustmentModal();
    }
}

function closeAdjustmentModal() {
    document.getElementById('adjustmentModalBackdrop').classList.remove('show');
    document.getElementById('adjustmentModal').classList.remove('show');
    document.removeEventListener('keydown', handleAdjustmentEsc);
}

function renderAdjustmentTable() {
    const tbody = document.getElementById('adjustmentModalBody');
    
    // Merge invoices with existing adjustments
    const mergedInvoices = [];
    
    // First add invoices that have existing adjustments
    existingAdjustments.forEach(adj => {
        const invType = adj.adjustment_type || (adj.purchase_transaction_id ? 'PURCHASE' : 'CREDIT_NOTE');
        const adjAmount = parseFloat(adj.adjusted_amount || 0);
        // Balance from API = bill_amount - other adjustments (excluding current)
        // So actual available balance for this invoice = balance from API
        // This balance already includes "room" for current adjustment
        const balanceFromApi = parseFloat(adj.balance || adj.bill_amount || adj.invoice_amount || 0);
        
        mergedInvoices.push({
            id: adj.purchase_transaction_id || adj.credit_note_id || adj.invoice_id,
            type: invType,
            trans_no: adj.trans_no || adj.invoice_no || '',
            date: adj.date || adj.invoice_date || '',
            bill_amount: parseFloat(adj.bill_amount || adj.invoice_amount || 0),
            balance: balanceFromApi, // This is the max adjustable amount
            adjusted_amount: adjAmount,
            is_existing: true
        });
    });
    
    // Then add remaining invoices (not already adjusted)
    allInvoices.forEach(inv => {
        const existingAdj = existingAdjustments.find(a => {
            if (inv.type === 'PURCHASE') {
                return a.purchase_transaction_id == inv.id;
            } else {
                return a.credit_note_id == inv.id;
            }
        });
        if (!existingAdj) {
            mergedInvoices.push({
                id: inv.id,
                type: inv.type,
                trans_no: inv.trans_no || '',
                date: inv.date || '',
                bill_amount: parseFloat(inv.bill_amount || 0),
                balance: parseFloat(inv.balance || 0),
                adjusted_amount: 0,
                is_existing: false
            });
        }
    });
    
    if (mergedInvoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No invoices or credit notes found</td></tr>';
        updateAdjustmentBalance();
        return;
    }
    
    tbody.innerHTML = mergedInvoices.map((inv, index) => {
        const typeLabel = inv.type === 'PURCHASE' ? '<span class="badge bg-primary">Purchase</span>' : '<span class="badge bg-warning">CN</span>';
        const remaining = inv.balance - inv.adjusted_amount;
        return `
        <tr class="${inv.is_existing ? 'table-success' : ''}">
            <td class="text-center">${index + 1}</td>
            <td>${typeLabel}</td>
            <td><strong>${inv.trans_no}</strong></td>
            <td>${inv.date}</td>
            <td class="text-end text-primary fw-bold">₹ ${inv.balance.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control form-control-sm adjustment-input" 
                       id="adj_${inv.id}_${inv.type}" 
                       data-invoice-id="${inv.id}"
                       data-invoice-type="${inv.type}"
                       data-balance="${inv.balance}"
                       data-original-adjusted="${inv.adjusted_amount}"
                       value="${inv.adjusted_amount.toFixed(2)}" 
                       min="0" 
                       max="${inv.balance}"
                       step="0.01"
                       onchange="updateAdjustmentBalance()"
                       oninput="updateAdjustmentBalance()"
                       style="width: 100px; text-align: right;">
            </td>
            <td class="text-end" id="balance_${inv.id}_${inv.type}">
                <span class="${remaining > 0 ? 'text-warning' : 'text-success'}">₹ ${remaining.toFixed(2)}</span>
            </td>
        </tr>
    `}).join('');
    
    updateAdjustmentBalance();
}

function updateAdjustmentBalance() {
    let totalAdjusted = 0;
    
    document.querySelectorAll('.adjustment-input').forEach(input => {
        let value = parseFloat(input.value) || 0;
        const balance = parseFloat(input.dataset.balance) || 0;
        const invoiceId = input.dataset.invoiceId;
        const invoiceType = input.dataset.invoiceType;
        
        // Validate - can't be negative or more than balance
        if (value < 0) {
            value = 0;
            input.value = '0.00';
        }
        if (value > balance) {
            value = balance;
            input.value = balance.toFixed(2);
        }
        
        totalAdjusted += value;
        
        // Update remaining display - show (balance - adjusted)
        const remaining = balance - value;
        const balanceEl = document.getElementById(`balance_${invoiceId}_${invoiceType}`);
        if (balanceEl) {
            // Green if fully adjusted (0), orange/warning if partial
            const colorClass = remaining === 0 ? 'text-success' : 'text-warning';
            const fontWeight = remaining === 0 ? 'fw-bold' : '';
            balanceEl.innerHTML = `<span class="${colorClass} ${fontWeight}">₹ ${remaining.toFixed(2)}</span>`;
        }
    });
    
    // Update total remaining DN amount to adjust
    const remaining = dnAmount - totalAdjusted;
    document.getElementById('adjustmentBalance').textContent = remaining.toFixed(2);
    
    // Change color based on remaining
    const balanceSpan = document.getElementById('adjustmentBalance');
    balanceSpan.classList.remove('text-primary', 'text-success', 'text-danger');
    if (remaining < 0) {
        balanceSpan.classList.add('text-danger');
    } else if (remaining === 0) {
        balanceSpan.classList.add('text-success');
    } else {
        balanceSpan.classList.add('text-danger');
    }
}

function autoDistributeAmount() {
    let amountToDistribute = parseFloat(document.getElementById('autoAdjustAmount').value) || 0;
    
    document.querySelectorAll('.adjustment-input').forEach(input => {
        if (amountToDistribute <= 0) {
            input.value = '0.00';
            return;
        }
        
        const balance = parseFloat(input.dataset.balance) || 0;
        const toAdjust = Math.min(amountToDistribute, balance);
        
        input.value = toAdjust.toFixed(2);
        amountToDistribute -= toAdjust;
    });
    
    updateAdjustmentBalance();
}

function saveAdjustments() {
    const debitNoteId = document.getElementById('debitNoteId').value;
    const adjustments = [];
    
    document.querySelectorAll('.adjustment-input').forEach(input => {
        const adjustedAmount = parseFloat(input.value) || 0;
        if (adjustedAmount > 0) {
            adjustments.push({
                invoice_id: input.dataset.invoiceId,
                invoice_type: input.dataset.invoiceType,
                adjusted_amount: adjustedAmount
            });
        }
    });
    
    // Save adjustments
    fetch(`{{ url('admin/debit-note') }}/${debitNoteId}/save-adjustments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ adjustments: adjustments })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message || 'Adjustments saved successfully');
            closeAdjustmentModal();
            // Redirect to invoices list after saving
            window.location.href = '{{ route("admin.debit-note.invoices") }}';
        } else {
            alert('Error: ' + (result.message || 'Failed to save adjustments'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving adjustments');
    });
}

// ============ SAVE OPTIONS MODAL FUNCTIONS ============
function showSaveOptionsModal() {
    // Validate form first
    const debitNoteNo = document.getElementById('debitNoteNo').value;
    const partyId = document.getElementById('partySelect').value;
    const dnAmount = parseFloat(document.getElementById('dnAmount').value) || 0;
    
    if (!debitNoteNo) {
        alert('Please load a debit note first');
        return;
    }
    
    if (!partyId) {
        alert('Please select a party');
        return;
    }
    
    if (dnAmount <= 0) {
        alert('Please add HSN items');
        return;
    }
    
    // Check party type - only show adjustment option for Supplier
    const partyType = document.querySelector('input[name="debit_party_type"]:checked').value;
    const saveWithAdjBtn = document.getElementById('saveWithAdjustmentBtn');
    if (partyType !== 'S') {
        saveWithAdjBtn.style.display = 'none';
    } else {
        saveWithAdjBtn.style.display = 'block';
    }
    
    document.getElementById('saveOptionsModalBackdrop').classList.add('show');
    document.getElementById('saveOptionsModal').classList.add('show');
    
    document.addEventListener('keydown', handleSaveOptionsEsc);
}

function handleSaveOptionsEsc(e) {
    if (e.key === 'Escape') {
        closeSaveOptionsModal();
    }
}

function closeSaveOptionsModal() {
    document.getElementById('saveOptionsModalBackdrop').classList.remove('show');
    document.getElementById('saveOptionsModal').classList.remove('show');
    document.removeEventListener('keydown', handleSaveOptionsEsc);
}

function saveWithoutAdjustment() {
    closeSaveOptionsModal();
    updateDebitNote();
}

function saveWithAdjustment() {
    closeSaveOptionsModal();
    // First update the debit note, then open adjustment modal
    updateDebitNoteAndOpenAdjustment();
}

function updateDebitNoteAndOpenAdjustment() {
    const debitNoteId = document.getElementById('debitNoteId')?.value;
    
    if (!debitNoteId) {
        alert('Please load a debit note first');
        return;
    }
    
    // Helper function to safely get element value
    const getVal = (id) => document.getElementById(id)?.value || '';
    const getChecked = (name) => document.querySelector(`input[name="${name}"]:checked`)?.value || '';
    
    const items = [];
    document.querySelectorAll('#hsnTableBody tr').forEach(row => {
        const hsnCode = row.querySelector('.hsn-code')?.value || row.querySelector('input[name*="hsn_code"]')?.value;
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
    
    const partySelect = document.getElementById('partySelect');
    const partyName = partySelect?.options[partySelect?.selectedIndex]?.text || '';
    
    // Use same data structure as updateDebitNote
    const data = {
        header: {
            debit_note_date: getVal('debitNoteDate'),
            day_name: getVal('dayName'),
            debit_party_type: getChecked('debit_party_type'),
            debit_party_id: getVal('partySelect'),
            debit_party_name: partyName,
            credit_account_type: getChecked('credit_account_type'),
            credit_account_no: getVal('accountNo'),
            inv_ref_no: getVal('invRefNo'),
            invoice_date: getVal('invoiceDate') || null,
            gst_vno: getVal('gstVno'),
            party_trn_no: getVal('partyTrnNo'),
            party_trn_date: getVal('partyTrnDate') || null,
            amount: getVal('amount'),
            salesman_id: getVal('salesmanSelect') || null,
            reason: getVal('reason'),
            gross_amount: getVal('grossAmount'),
            total_gst: getVal('totalGst'),
            net_amount: getVal('netAmount'),
            tcs_amount: getVal('tcsAmount'),
            round_off: getVal('roundOff'),
            dn_amount: getVal('dnAmount'),
            narration: getVal('narration'),
        },
        items: items
    };
    
    // Update debit note first
    fetch(`{{ url('admin/debit-note') }}/${debitNoteId}`, {
        method: 'PUT',
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
            // Now open adjustment modal
            openAdjustmentModal();
        } else {
            alert('Error: ' + (result.message || 'Failed to update debit note'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating debit note');
    });
}
</script>

<style>
.readonly-field {
    background-color: #e9ecef !important;
    cursor: not-allowed;
}

/* Custom Modal Styles */
.custom-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1050;
}

.custom-modal-backdrop.show {
    display: block;
}

.custom-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 700px;
    max-width: 95%;
    max-height: 90vh;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 1060;
    overflow: hidden;
}

.custom-modal.show {
    display: block;
}

.custom-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.custom-modal-header h5 {
    margin: 0;
    font-size: 1.1rem;
}

.custom-modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    line-height: 1;
    padding: 0;
}

.custom-modal-close:hover {
    opacity: 0.8;
}

.custom-modal-body {
    padding: 20px;
    max-height: calc(90vh - 60px);
    overflow-y: auto;
}

.adjustment-input {
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.adjustment-input:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.15);
}

.btn-close-modal {
    background: none;
    border: none;
    font-size: 20px;
    color: #fff;
    cursor: pointer;
    padding: 5px;
    line-height: 1;
    transition: color 0.2s;
}

.btn-close-modal:hover {
    color: rgba(255,255,255,0.7);
}
</style>
@endpush
