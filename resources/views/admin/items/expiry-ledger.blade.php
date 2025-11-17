@extends('layouts.admin')

@section('title', 'Expiry Ledger - ' . $item->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center">
            <i class="bi bi-calendar-x me-2"></i> Expiry Ledger - {{ $item->name }}
        </h4>
        <div class="text-muted small">Item: {{ $item->name }} | Company: {{ $item->company->name ?? 'N/A' }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Items
        </a>
        <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list me-1"></i> Items List
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-3 g-2" id="summaryCards">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Received</small>
                        <h5 class="mb-0 text-success" id="totalReceived">0</h5>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="bi bi-arrow-down-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Issued</small>
                        <h5 class="mb-0 text-danger" id="totalIssued">0</h5>
                    </div>
                    <div class="text-danger opacity-50">
                        <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Balance Qty</small>
                        <h5 class="mb-0 text-primary" id="balanceQty">0</h5>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.items.expiry-ledger', $item->id) }}" class="row g-3">
                <div class="col-md-4">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}">
                </div>
                <div class="col-md-4">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>S.No</th>
                    <th>Date</th>
                    <th>Trans. No</th>
                    <th>Type</th>
                    <th>Party Name</th>
                    <th>Batch</th>
                    <th class="text-center">Rcvd</th>
                    <th class="text-center">Issued</th>
                    <th>Balance</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="ledgerTableBody">
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 mb-0">Loading ledger data...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

<!-- Transaction Details Modal -->
@section('modals')
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="transactionDetailsModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Breakage/Expiry Transaction Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Transaction Header -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-2"></i>Transaction Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold">SR No:</label>
                                        <div id="modal_sr_no">---</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold">Series:</label>
                                        <div id="modal_series">---</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold">Date:</label>
                                        <div id="modal_date">---</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold">Customer:</label>
                                        <div id="modal_customer">---</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold">Salesman:</label>
                                        <div id="modal_salesman">---</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold">GST VNo:</label>
                                        <div id="modal_gst_vno">---</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-success text-white">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="bi bi-currency-rupee me-2"></i>Amount Summary</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Gross Amount:</span>
                                    <strong id="modal_gross_amount">₹0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax Amount:</span>
                                    <strong id="modal_tax_amount">₹0.00</strong>
                                </div>
                                <hr class="bg-white">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Net Amount:</span>
                                    <strong class="fs-5" id="modal_net_amount">₹0.00</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Items Table -->
                <div class="card border-0">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Transaction Items</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                        <th>Batch</th>
                                        <th>Expiry</th>
                                        <th>Type</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">MRP</th>
                                        <th class="text-end">Rate</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="modal_items_tbody">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const itemId = {{ $item->id }};
    const fromDate = '{{ $fromDate }}';
    const toDate = '{{ $toDate }}';

    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadLedgerData();
    });

    // Load ledger data
    function loadLedgerData() {
        const tbody = document.getElementById('ledgerTableBody');
        
        // Show loading
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Loading ledger data...</p>
                </td>
            </tr>
        `;

        // Fetch data
        fetch(`{{ route('admin.items.expiry-ledger.data') }}?item_id=${itemId}&from_date=${fromDate}&to_date=${toDate}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayLedgerData(data.transactions);
                    displaySummary(data.summary);
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" class="text-center text-danger py-4">
                                <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">${data.message || 'Error loading data'}</p>
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Error loading ledger data. Please try again.</p>
                        </td>
                    </tr>
                `;
            });
    }

    // Display ledger data
    function displayLedgerData(transactions) {
        const tbody = document.getElementById('ledgerTableBody');

        if (!transactions || transactions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2 mb-0">No breakage/expiry transactions found for this item</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        let balance = 0;

        transactions.forEach((trans, index) => {
            const rcvd = parseFloat(trans.rcvd || 0);
            const issued = parseFloat(trans.issued || 0);
            balance += rcvd - issued;

            html += `
                <tr class="table-warning">
                    <td>${index + 1}</td>
                    <td>${formatDate(trans.transaction_date)}</td>
                    <td>${trans.trans_no || '---'}</td>
                    <td>${trans.type || '---'}</td>
                    <td>${trans.party_name || '---'}</td>
                    <td>${trans.batch_no || '---'}</td>
                    <td class="text-center">${rcvd > 0 ? rcvd : '-'}</td>
                    <td class="text-center">${issued > 0 ? issued : '-'}</td>
                    <td>${balance.toFixed(2)}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary" 
                            onclick="viewTransactionDetails(${trans.id})" 
                            title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // Display summary
    function displaySummary(summary) {
        if (!summary) return;

        document.getElementById('totalReceived').textContent = summary.total_received || '0';
        document.getElementById('totalIssued').textContent = summary.total_issued || '0';
        document.getElementById('balanceQty').textContent = summary.balance || '0';
    }

    // View transaction details
    function viewTransactionDetails(transactionId) {
        fetch(`{{ url('admin/breakage-expiry/transaction') }}/${transactionId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateTransactionModal(data.transaction, data.items);
                    const modal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
                    modal.show();
                } else {
                    alert('Error loading transaction details: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading transaction details. Please try again.');
            });
    }

    // Populate transaction modal with data
    function populateTransactionModal(transaction, items) {
        // Transaction Information
        document.getElementById('modal_sr_no').textContent = transaction.sr_no || '---';
        document.getElementById('modal_series').textContent = transaction.series || 'BE';
        document.getElementById('modal_date').textContent = formatDate(transaction.transaction_date);
        document.getElementById('modal_customer').textContent = transaction.customer_name || '---';
        document.getElementById('modal_salesman').textContent = transaction.salesman_name || '---';
        document.getElementById('modal_gst_vno').textContent = transaction.gst_vno || '---';
        
        // Amount Summary
        const grossAmount = parseFloat(transaction.gross_amount || 0);
        const taxAmount = parseFloat(transaction.tax_amount || 0);
        const netAmount = parseFloat(transaction.net_amount || 0);
        
        document.getElementById('modal_gross_amount').textContent = '₹' + grossAmount.toFixed(2);
        document.getElementById('modal_tax_amount').textContent = '₹' + taxAmount.toFixed(2);
        document.getElementById('modal_net_amount').textContent = '₹' + netAmount.toFixed(2);
        
        // Items Table
        const tbody = document.getElementById('modal_items_tbody');
        if (!items || items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No items found</td></tr>';
            return;
        }
        
        let html = '';
        items.forEach((item, index) => {
            const qty = parseFloat(item.qty || 0);
            const mrp = parseFloat(item.mrp || 0);
            const rate = parseFloat(item.s_rate || item.rate || 0);
            const amount = parseFloat(item.amount || 0);
            
            // Determine badge color based on br_ex value
            const badgeClass = item.br_ex === 'E' ? 'bg-warning' : 'bg-danger';
            const typeText = item.br_ex === 'E' ? 'Expiry' : 'Breakage';
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.item_name || '---'}</td>
                    <td>${item.batch_no || '-'}</td>
                    <td>${item.expiry || '-'}</td>
                    <td>
                        <span class="badge ${badgeClass}">
                            ${typeText}
                        </span>
                    </td>
                    <td class="text-end">${qty.toFixed(2)}</td>
                    <td class="text-end">₹${mrp.toFixed(2)}</td>
                    <td class="text-end">₹${rate.toFixed(2)}</td>
                    <td class="text-end">₹${amount.toFixed(2)}</td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    // Format date
    function formatDate(dateString) {
        if (!dateString) return '---';
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }
</script>
@endpush
