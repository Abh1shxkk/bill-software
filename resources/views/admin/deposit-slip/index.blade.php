@extends('layouts.admin')

@section('title', 'Deposit Slip')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-receipt me-2"></i> Deposit Slip</h4>
        <div class="text-muted small">Manage cheque deposits to bank</div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card-body border-bottom" style="background-color: #f0f0f0;">
        <form class="row g-2 align-items-end" id="filterForm">
            <div class="col-md-2">
                <label class="form-label small mb-1">Deposit / Clearing Date</label>
                <input type="date" class="form-control form-control-sm" id="deposit_date" name="deposit_date" 
                       value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Cheques Upto</label>
                <input type="date" class="form-control form-control-sm" id="cheques_upto" name="cheques_upto" 
                       value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Payin Slip Date</label>
                <input type="date" class="form-control form-control-sm" id="payin_slip_date" name="payin_slip_date" 
                       value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Bank</label>
                <select class="form-select form-select-sm" id="bank_id" name="bank_id">
                    <option value="">Select Bank</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label small mb-1">D/N Wise</label>
                <select class="form-select form-select-sm" id="search_type" name="search_type">
                    <option value="N">N</option>
                    <option value="D">D</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Pay In Slip No</label>
                <input type="number" class="form-control form-control-sm" id="slip_no" name="slip_no" 
                       value="{{ $nextSlipNo }}" readonly>
            </div>
        </form>
    </div>

    <!-- Cheque Table -->
    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
        <table class="table table-sm table-bordered table-hover mb-0" id="chequeTable">
            <thead class="table-primary sticky-top">
                <tr>
                    <th style="width: 60px;">Code</th>
                    <th>Party Name</th>
                    <th style="width: 100px;">Cheque No</th>
                    <th style="width: 90px;">Date</th>
                    <th style="width: 100px;" class="text-end">Amount</th>
                    <th style="width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody id="cheque-table-body">
                @forelse($chequeData as $cheque)
                <tr data-id="{{ $cheque['id'] }}" data-cheque='@json($cheque)' 
                    class="cheque-row {{ $cheque['status'] === 'posted' ? 'table-success' : '' }}"
                    style="cursor: pointer;">
                    <td>{{ $cheque['customer_code'] ?? '---' }}</td>
                    <td class="text-primary fw-bold">{{ $cheque['customer_name'] ?? '-' }}</td>
                    <td>{{ $cheque['cheque_no'] }}</td>
                    <td>{{ $cheque['cheque_date'] }}</td>
                    <td class="text-end">{{ number_format($cheque['amount'], 2) }}</td>
                    <td>
                        @if($cheque['status'] === 'posted')
                            <span class="badge bg-success">POSTED</span>
                        @else
                            <span class="badge bg-warning text-dark">PENDING</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">No cheques found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Summary Row -->
    <div class="card-body border-top border-bottom py-2" style="background-color: #e0e0e0;">
        <div class="row align-items-center">
            <div class="col-md-4">
                <span class="fw-bold">TRN : </span>
                <span id="selected_trn">-</span>
                <span class="ms-3" id="selected_date">-</span>
                <span class="ms-3" id="selected_bank">-</span>
            </div>
            <div class="col-md-4 text-center">
                <span class="fw-bold text-success">TOTAL : </span>
                <span id="total_amount" class="fw-bold">0.00</span>
                <span class="ms-4 text-primary">No. : (<span id="total_count">0</span>)</span>
                <span class="ms-2 text-warning">Un-Posted : <span id="unposted_count">0</span></span>
                <span class="ms-2 text-success">Posted : (<span id="posted_count">0</span>)</span>
            </div>
            <div class="col-md-4 text-end">
                <span class="fw-bold" id="summary_total">0.00</span>
            </div>
        </div>
    </div>

    <!-- Bottom Sections -->
    <div class="row g-0">
        <!-- Outstanding Section -->
        <div class="col-md-6 border-end">
            <div class="p-2" style="background-color: #00ffff;">
                <strong>1 ) Amt. Outstanding &nbsp;&nbsp; Total :</strong>
                <span id="outstanding_total" class="float-end">0.00</span>
            </div>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0" id="outstandingTable">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Ref No</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="outstanding-body">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No outstanding items</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Adjusted Section -->
        <div class="col-md-6">
            <div class="p-2" style="background-color: #00ffff;">
                <strong>2 ) Amt. Adjusted &nbsp;&nbsp; Total :</strong>
                <span id="adjusted_total" class="float-end">0.00</span>
            </div>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0" id="adjustedTable">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Ref No</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="adjusted-body">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No adjusted items</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button type="button" class="btn btn-success btn-sm" id="btn-post">
                    <i class="bi bi-check-circle me-1"></i> Post Selected
                </button>
                <button type="button" class="btn btn-warning btn-sm ms-2" id="btn-unpost">
                    <i class="bi bi-x-circle me-1"></i> Unpost Selected
                </button>
            </div>
            <div>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-refresh">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .cheque-row.selected {
        background-color: #cfe2ff !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.5);
        position: relative;
        z-index: 1;
    }
    .cheque-row.selected td {
        border-top: 2px solid #0d6efd !important;
        border-bottom: 2px solid #0d6efd !important;
    }
    .cheque-row.selected td:first-child {
        border-left: 2px solid #0d6efd !important;
    }
    .cheque-row.selected td:last-child {
        border-right: 2px solid #0d6efd !important;
    }
    .cheque-row:hover {
        background-color: #e9ecef;
    }
    .table-success {
        background-color: #d1e7dd !important;
    }
    .table-success.selected {
        background-color: #b6d4fe !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedCheque = null;
    let chequeData = @json($chequeData);

    // Calculate totals
    function calculateTotals() {
        let total = 0;
        let posted = 0;
        let unposted = 0;

        chequeData.forEach(function(cheque) {
            total += parseFloat(cheque.amount) || 0;
            if (cheque.status === 'posted') {
                posted++;
            } else {
                unposted++;
            }
        });

        document.getElementById('total_amount').textContent = total.toFixed(2);
        document.getElementById('total_count').textContent = chequeData.length;
        document.getElementById('posted_count').textContent = posted;
        document.getElementById('unposted_count').textContent = unposted;
        document.getElementById('summary_total').textContent = total.toFixed(2);
    }

    calculateTotals();

    // Row selection
    document.querySelectorAll('.cheque-row').forEach(function(row) {
        row.addEventListener('click', function() {
            document.querySelectorAll('.cheque-row').forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');
            
            selectedCheque = JSON.parse(this.dataset.cheque);
            
            document.getElementById('selected_trn').textContent = selectedCheque.trn_no || '-';
            document.getElementById('selected_date').textContent = selectedCheque.cheque_date || '-';
            document.getElementById('selected_bank').textContent = selectedCheque.bank_name || '-';
            
            // Display adjustment details
            displayAdjustments(selectedCheque);
        });
    });

    // Function to display adjustments for selected cheque
    function displayAdjustments(cheque) {
        const outstandingBody = document.getElementById('outstanding-body');
        const adjustedBody = document.getElementById('adjusted-body');
        
        let outstandingHtml = '';
        let adjustedHtml = '';
        let outstandingTotal = 0;
        let adjustedTotal = 0;
        
        const adjustments = cheque.adjustments || [];
        
        if (adjustments.length > 0) {
            adjustments.forEach(function(adj) {
                // Outstanding items (balance amount > 0)
                if (adj.balance_amount > 0) {
                    outstandingTotal += adj.balance_amount;
                    outstandingHtml += `
                        <tr>
                            <td>${adj.adjustment_type || '-'}</td>
                            <td>${adj.reference_no || '-'}</td>
                            <td>${adj.reference_date || '-'}</td>
                            <td class="text-end">${parseFloat(adj.balance_amount).toFixed(2)}</td>
                        </tr>
                    `;
                }
                
                // Adjusted items (adjusted amount > 0)
                if (adj.adjusted_amount > 0) {
                    adjustedTotal += adj.adjusted_amount;
                    adjustedHtml += `
                        <tr>
                            <td>${adj.adjustment_type || '-'}</td>
                            <td>${adj.reference_no || '-'}</td>
                            <td>${adj.reference_date || '-'}</td>
                            <td class="text-end">${parseFloat(adj.adjusted_amount).toFixed(2)}</td>
                        </tr>
                    `;
                }
            });
        }
        
        // If unadjusted amount exists, show it in outstanding
        if (cheque.unadjusted && cheque.unadjusted > 0) {
            outstandingTotal += cheque.unadjusted;
            outstandingHtml += `
                <tr>
                    <td>Unadjusted</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="text-end">${parseFloat(cheque.unadjusted).toFixed(2)}</td>
                </tr>
            `;
        }
        
        // Update outstanding section
        if (outstandingHtml) {
            outstandingBody.innerHTML = outstandingHtml;
        } else {
            outstandingBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No outstanding items</td></tr>';
        }
        document.getElementById('outstanding_total').textContent = outstandingTotal.toFixed(2);
        
        // Update adjusted section
        if (adjustedHtml) {
            adjustedBody.innerHTML = adjustedHtml;
        } else {
            adjustedBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No adjusted items</td></tr>';
        }
        document.getElementById('adjusted_total').textContent = adjustedTotal.toFixed(2);
    }

    // Post cheque
    document.getElementById('btn-post').addEventListener('click', function() {
        if (!selectedCheque) {
            alert('Please select a cheque to post');
            return;
        }

        if (selectedCheque.status === 'posted') {
            alert('This cheque is already posted');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Posting...';

        const bankSelect = document.getElementById('bank_id');
        const bankName = bankSelect.options[bankSelect.selectedIndex]?.text || '';

        fetch('{{ route("admin.deposit-slip.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                customer_receipt_item_id: selectedCheque.id,
                slip_no: document.getElementById('slip_no').value,
                deposit_date: document.getElementById('deposit_date').value,
                clearing_date: document.getElementById('deposit_date').value,
                payin_slip_date: document.getElementById('payin_slip_date').value,
                bank_id: document.getElementById('bank_id').value || null,
                bank_name: bankName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Post Selected';
            }
        })
        .catch(error => {
            alert('Error processing request');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Post Selected';
        });
    });

    // Unpost cheque
    document.getElementById('btn-unpost').addEventListener('click', function() {
        if (!selectedCheque) {
            alert('Please select a cheque to unpost');
            return;
        }

        if (selectedCheque.status !== 'posted') {
            alert('This cheque is not posted');
            return;
        }

        if (!selectedCheque.deposit_slip_id) {
            alert('Deposit slip not found');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Unposting...';

        fetch('{{ route("admin.deposit-slip.unpost") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                deposit_slip_id: selectedCheque.deposit_slip_id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Unpost Selected';
            }
        })
        .catch(error => {
            alert('Error processing request');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Unpost Selected';
        });
    });

    // Refresh
    document.getElementById('btn-refresh').addEventListener('click', function() {
        location.reload();
    });

    // Filter by cheques upto date
    document.getElementById('cheques_upto').addEventListener('change', function() {
        filterCheques();
    });

    function filterCheques() {
        const chequesUpto = document.getElementById('cheques_upto').value;
        
        document.querySelectorAll('.cheque-row').forEach(function(row) {
            const cheque = JSON.parse(row.dataset.cheque);
            const chequeDate = cheque.cheque_date_raw;
            
            if (!chequesUpto || !chequeDate || chequeDate <= chequesUpto) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
});
</script>
@endpush
