@extends('layouts.admin')
@section('title', 'Stock Ledger - ' . $item->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center">
            <i class="bi bi-graph-up me-2"></i> Stock Ledger (F10) - {{ $item->name }}
        </h4>
        <div class="text-muted small">Item: {{ $item->name }} | Company: {{ $item->company_short_name }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Items
        </a>
        <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list me-1"></i>Items List
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-3 g-2">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Received</small>
                        <h5 class="mb-0 text-success" id="total-received">{{ number_format($totalReceived, 0) }}</h5>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="bi bi-arrow-down-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Issued</small>
                        <h5 class="mb-0 text-danger" id="total-issued">{{ number_format($totalIssued, 0) }}</h5>
                    </div>
                    <div class="text-danger opacity-50">
                        <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Balance Qty</small>
                        <h5 class="mb-0 text-primary" id="balance-qty">{{ number_format($balance, 0) }}</h5>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Records</small>
                        <h5 class="mb-0 text-info" id="total-records">{{ $ledgers->total() }}</h5>
                    </div>
                    <div class="text-info opacity-50">
                        <i class="bi bi-list-check" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm border-0 rounded">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.items.stock-ledger-complete', $item->id) }}" class="row g-3">
                <div class="col-md-3">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}">
                </div>
                <div class="col-md-3">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}">
                </div>
                <div class="col-md-4">
                    <label for="party_id" class="form-label">Party Name</label>
                    <select class="form-select" id="party_id" name="party_id">
                        <option value="">All Parties</option>
                        <optgroup label="Customers">
                            @foreach($customers as $customer)
                                <option value="C{{ $customer->id }}" {{ $selectedPartyId == 'C'.$customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->code }})
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Suppliers">
                            @foreach($suppliers as $supplier)
                                <option value="S{{ $supplier->supplier_id }}" {{ $selectedPartyId == 'S'.$supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->name }} ({{ $supplier->code }})
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i>Filter
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
                    <th>Trans No.</th>
                    <th>Date</th>
                    <th>Party Name</th>
                    <th>Batch</th>
                    <th colspan="2" class="text-center">Received</th>
                    <th colspan="2" class="text-center">Issued</th>
                    <th>Balance</th>
                    <th class="text-end">Actions</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Free</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Free</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="ledger-table-body">
                @forelse($ledgers as $ledger)
                    <tr class="{{ 
                        $ledger['type'] === 'SALE_RETURN' ? 'table-success' : 
                        ($ledger['type'] === 'PURCHASE' ? 'table-info' : 
                        ($ledger['type'] === 'STOCK_ADJUSTMENT' ? 'table-secondary' : 
                        ($ledger['type'] === 'REPLACEMENT_NOTE' ? 'table-danger' :
                        ($ledger['type'] === 'REPLACEMENT_RECEIVED' ? 'table-primary' : 
                        ($ledger['type'] === 'STOCK_TRANSFER_INCOMING' ? 'table-success' :
                        ($ledger['type'] === 'STOCK_TRANSFER_INCOMING_RETURN' ? 'table-warning' :
                        ($ledger['type'] === 'SAMPLE_ISSUED' ? 'table-danger' :
                        ($ledger['type'] === 'STOCK_TRANSFER_OUTGOING' ? 'table-warning' :
                        ($ledger['type'] === 'STOCK_TRANSFER_OUTGOING_RETURN' ? 'table-info' : 'table-warning'))))))))) 
                    }}">
                        <td>
                            @if($ledger['type'] === 'SALE_RETURN')
                                <i class="bi bi-arrow-return-left text-success me-1" title="Sale Return"></i>
                            @elseif($ledger['type'] === 'PURCHASE')
                                <i class="bi bi-arrow-down-circle text-info me-1" title="Purchase"></i>
                            @elseif($ledger['type'] === 'STOCK_ADJUSTMENT')
                                <i class="bi bi-sliders text-secondary me-1" title="Stock Adjustment"></i>
                            @elseif($ledger['type'] === 'REPLACEMENT_NOTE')
                                <i class="bi bi-arrow-repeat text-danger me-1" title="Replacement Note (Out)"></i>
                            @elseif($ledger['type'] === 'REPLACEMENT_RECEIVED')
                                <i class="bi bi-arrow-down-left-circle text-primary me-1" title="Replacement Received (In)"></i>
                            @elseif($ledger['type'] === 'STOCK_TRANSFER_INCOMING')
                                <i class="bi bi-box-arrow-in-down text-success me-1" title="Stock Transfer Incoming"></i>
                            @elseif($ledger['type'] === 'STOCK_TRANSFER_INCOMING_RETURN')
                                <i class="bi bi-box-arrow-up text-warning me-1" title="Stock Transfer Incoming Return"></i>
                            @elseif($ledger['type'] === 'SAMPLE_ISSUED')
                                <i class="bi bi-gift text-danger me-1" title="Sample Issued"></i>
                            @elseif($ledger['type'] === 'STOCK_TRANSFER_OUTGOING')
                                <i class="bi bi-box-arrow-right text-warning me-1" title="Stock Transfer Outgoing"></i>
                            @elseif($ledger['type'] === 'STOCK_TRANSFER_OUTGOING_RETURN')
                                <i class="bi bi-box-arrow-in-left text-info me-1" title="Stock Transfer Outgoing Return"></i>
                            @else
                                <i class="bi bi-arrow-up-circle text-warning me-1" title="Sale"></i>
                            @endif
                            {{ $ledger['trans_no'] }}
                        </td>
                        <td>{{ $ledger['date'] }}</td>
                        <td>{{ $ledger['party_name'] }}</td>
                        <td>{{ $ledger['batch'] }}</td>
                        <td class="text-center">{{ $ledger['received_qty'] > 0 ? number_format($ledger['received_qty'], 0) : '-' }}</td>
                        <td class="text-center">{{ $ledger['received_free'] > 0 ? number_format($ledger['received_free'], 0) : '-' }}</td>
                        <td class="text-center">{{ $ledger['issued_qty'] > 0 ? number_format($ledger['issued_qty'], 0) : '-' }}</td>
                        <td class="text-center">{{ $ledger['issued_free'] > 0 ? number_format($ledger['issued_free'], 0) : '-' }}</td>
                        <td>{{ number_format($ledger['balance'], 0) }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" 
                                onclick="viewStockLedgerDetails('{{ $ledger['type'] }}', '{{ $ledger['transaction_id'] }}')"
                                title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No stock movements found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer with Load More -->
    <div class="card-footer bg-light d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>Showing {{ $ledgers->firstItem() ?? 0 }}-{{ $ledgers->lastItem() ?? 0 }} of {{ $ledgers->total() }}</div>
            <div class="text-muted">Page {{ $ledgers->currentPage() }} of {{ $ledgers->lastPage() }}</div>
        </div>
        @if($ledgers->hasMorePages())
            <div class="d-flex align-items-center justify-content-center gap-2">
                <div id="ledger-spinner" class="spinner-border text-primary d-none" style="width: 2rem; height: 2rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span id="ledger-load-text" class="text-muted" style="font-size: 0.9rem;">Scroll for more</span>
            </div>
            <div id="ledger-sentinel" data-next-url="{{ $ledgers->appends(request()->query())->nextPageUrl() }}" style="height: 1px;"></div>
        @endif
    </div>
</div>

<!-- Stock Ledger Details Modal -->
<div id="stockLedgerDetailsModal" class="stock-ledger-modal">
    <div class="stock-ledger-modal-content">
        <div class="stock-ledger-modal-header">
            <h5 class="stock-ledger-modal-title">
                <i class="bi bi-info-circle me-2"></i>Transaction Details
            </h5>
            <button type="button" class="btn-close-modal" onclick="closeStockLedgerModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="stock-ledger-modal-body" id="stockLedgerModalBody">
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2">Loading details...</div>
            </div>
        </div>
    </div>
</div>

<div id="stockLedgerModalBackdrop" class="stock-ledger-modal-backdrop"></div>

@endsection

@push('scripts')
<style>
    /* Ensure content area is positioned relatively for modal */
    .content {
        position: relative;
        /* overflow: hidden; */
    }

    /* Stock Ledger Modal Styles */
    .stock-ledger-modal {
        position: absolute;
        top: 0;
        right: 0;
        width: 500px;
        height: 100%;
        max-height: calc(100vh - 120px);
        background: white;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.15);
        z-index: 1060;
        display: none;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    .stock-ledger-modal.show {
        transform: translateX(0);
    }

    .stock-ledger-modal-content {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .stock-ledger-modal-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stock-ledger-modal-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #fff;
    }

    .btn-close-modal {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        color: rgba(255, 255, 255, 0.9);
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .btn-close-modal:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .stock-ledger-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        background: #f8f9fa;
    }

    .stock-ledger-modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stock-ledger-modal-backdrop.show {
        opacity: 1;
    }

    /* Responsive for smaller screens */
    @media (max-width: 768px) {
        .stock-ledger-modal {
            width: 100%;
        }
    }

    /* Detail Cards Styling */
    .stock-ledger-modal .card {
        margin-bottom: 1rem;
        border: none;
    }

    .stock-ledger-modal .card-header {
        padding: 0.75rem 1rem;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
    }

    .stock-ledger-modal .card-header h6 {
        margin: 0;
        font-size: 1rem;
    }

    .stock-ledger-modal .card-body {
        padding: 1.25rem;
        background: #fff;
    }

    .stock-ledger-modal .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .stock-ledger-modal .detail-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .stock-ledger-modal .detail-row:first-child {
        padding-top: 0;
    }

    .stock-ledger-modal .detail-label {
        font-weight: 500;
        color: #6c757d;
        font-size: 0.875rem;
        flex-shrink: 0;
        margin-right: 1rem;
    }

    .stock-ledger-modal .detail-value {
        font-weight: 600;
        color: #212529;
        text-align: right;
        font-size: 0.9rem;
    }
</style>
@endpush

@push('scripts')
<script>
// Cache for storing transaction details
const stockLedgerCache = new Map();

// View stock ledger transaction details
function viewStockLedgerDetails(transactionType, transactionId) {
    const modal = document.getElementById('stockLedgerDetailsModal');
    const backdrop = document.getElementById('stockLedgerModalBackdrop');
    const modalBody = document.getElementById('stockLedgerModalBody');

    backdrop.style.display = 'block';
    modal.style.display = 'flex';

    setTimeout(() => {
        backdrop.classList.add('show');
        modal.classList.add('show');
    }, 10);

    // Create cache key
    const cacheKey = `${transactionType}_${transactionId}`;

    // Check if data is already cached
    if (stockLedgerCache.has(cacheKey)) {
        populateStockLedgerData(stockLedgerCache.get(cacheKey), transactionType);
        return;
    }

    // Show loading spinner
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2 small">Loading details...</div>
        </div>
    `;

    // Determine the endpoint based on transaction type
    let endpoint = '';
    if (transactionType === 'PURCHASE') {
        endpoint = `/bill-software/admin/purchase-transactions/${transactionId}/details`;
    } else if (transactionType === 'SALE') {
        endpoint = `/bill-software/admin/sale-transactions/${transactionId}/details`;
    } else if (transactionType === 'SALE_RETURN') {
        endpoint = `/bill-software/admin/sale-return/${transactionId}/details`;
    } else if (transactionType === 'STOCK_ADJUSTMENT') {
        endpoint = `/bill-software/admin/stock-adjustment/${transactionId}/details`;
    } else if (transactionType === 'REPLACEMENT_NOTE') {
        endpoint = `/bill-software/admin/replacement-note/details/${transactionId}`;
    } else if (transactionType === 'REPLACEMENT_RECEIVED') {
        endpoint = `/bill-software/admin/replacement-received/details/${transactionId}`;
    } else if (transactionType === 'STOCK_TRANSFER_INCOMING') {
        endpoint = `/bill-software/admin/stock-transfer-incoming/${transactionId}/details`;
    } else if (transactionType === 'STOCK_TRANSFER_INCOMING_RETURN') {
        endpoint = `/bill-software/admin/stock-transfer-incoming-return/${transactionId}/details`;
    } else if (transactionType === 'SAMPLE_ISSUED') {
        endpoint = `/bill-software/admin/sample-issued/${transactionId}`;
    } else if (transactionType === 'STOCK_TRANSFER_OUTGOING') {
        endpoint = `/bill-software/admin/stock-transfer-outgoing/${transactionId}/details`;
    } else if (transactionType === 'STOCK_TRANSFER_OUTGOING_RETURN') {
        endpoint = `/bill-software/admin/stock-transfer-outgoing-return/${transactionId}/details`;
    } else {
        showErrorInStockLedgerModal('Invalid transaction type');
        return;
    }

    // Fetch transaction details
    fetch(endpoint, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            showErrorInStockLedgerModal(data.error);
        } else {
            // Cache the data
            stockLedgerCache.set(cacheKey, data);
            populateStockLedgerData(data, transactionType);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorInStockLedgerModal('Error loading transaction details. Please try again.');
    });
}

// Close stock ledger modal
function closeStockLedgerModal() {
    const modal = document.getElementById('stockLedgerDetailsModal');
    const backdrop = document.getElementById('stockLedgerModalBackdrop');

    modal.classList.remove('show');
    backdrop.classList.remove('show');

    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
    }, 300);
}

// Populate stock ledger modal with data
function populateStockLedgerData(data, transactionType) {
    const modalBody = document.getElementById('stockLedgerModalBody');
    let html = '';

    if (transactionType === 'PURCHASE') {
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Purchase Transaction</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Bill No:</span>
                        <span class="detail-value">${data.bill_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Bill Date:</span>
                        <span class="detail-value">${data.bill_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Supplier:</span>
                        <span class="detail-value">${data.supplier_name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">${data.address || '-'}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Item Details</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Item Code:</span>
                        <span class="detail-value">${data.code || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Purchase Rate:</span>
                        <span class="detail-value">₹${parseFloat(data.pur_rate || 0).toFixed(2)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">MRP:</span>
                        <span class="detail-value">₹${parseFloat(data.mrp || 0).toFixed(2)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Discount %:</span>
                        <span class="detail-value">${parseFloat(data.discount_percent || 0).toFixed(2)}%</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>User Information</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">User ID:</span>
                        <span class="detail-value">${data.user_id || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Salesman:</span>
                        <span class="detail-value">${data.salesman || '-'}</span>
                    </div>
                </div>
            </div>
        `;
    } else if (transactionType === 'SALE') {
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Sale Transaction</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Invoice No:</span>
                        <span class="detail-value">${data.invoice_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sale Date:</span>
                        <span class="detail-value">${data.sale_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Customer:</span>
                        <span class="detail-value">${data.customer_name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">${data.address || '-'}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Item Details</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Item Code:</span>
                        <span class="detail-value">${data.code || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sale Rate:</span>
                        <span class="detail-value">₹${parseFloat(data.rate || 0).toFixed(2)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">MRP:</span>
                        <span class="detail-value">₹${parseFloat(data.mrp || 0).toFixed(2)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Discount %:</span>
                        <span class="detail-value">${parseFloat(data.discount_percent || 0).toFixed(2)}%</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>User Information</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">User ID:</span>
                        <span class="detail-value">${data.user_id || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Salesman:</span>
                        <span class="detail-value">${data.salesman || '-'}</span>
                    </div>
                </div>
            </div>
        `;
    } else if (transactionType === 'SALE_RETURN') {
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i>Sale Return Transaction</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Return No:</span>
                        <span class="detail-value">${data.return_no || data.trn_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Return Date:</span>
                        <span class="detail-value">${data.return_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Customer:</span>
                        <span class="detail-value">${data.customer_name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Reason:</span>
                        <span class="detail-value">${data.reason || '-'}</span>
                    </div>
                </div>
            </div>
        `;
    } else if (transactionType === 'STOCK_ADJUSTMENT') {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
            itemsHtml = `
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Batch</th>
                            <th>Type</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.items.map(item => `
                            <tr>
                                <td>${item.item_name}</td>
                                <td>${item.batch_no}</td>
                                <td><span class="badge ${item.adjustment_type === 'Shortage' ? 'bg-danger' : 'bg-success'}">${item.adjustment_type}</span></td>
                                <td class="text-end">${parseFloat(item.qty).toFixed(0)}</td>
                                <td class="text-end">₹${parseFloat(item.amount).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
        
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-sliders me-2"></i>Stock Adjustment</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Trn No:</span>
                        <span class="detail-value">${data.trn_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${data.adjustment_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Remarks:</span>
                        <span class="detail-value">${data.remarks || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value fw-bold">₹${data.total_amount || '0.00'}</span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items (${data.total_items || 0})</h6>
                </div>
                <div class="card-body p-0">
                    ${itemsHtml}
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Summary</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Shortage Items:</span>
                        <span class="detail-value text-danger">${data.shortage_items || 0}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Excess Items:</span>
                        <span class="detail-value text-success">${data.excess_items || 0}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Created By:</span>
                        <span class="detail-value">${data.created_by || '-'}</span>
                    </div>
                </div>
            </div>
        `;
    } else if (transactionType === 'REPLACEMENT_NOTE') {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
            itemsHtml = `
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Batch</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Free</th>
                            <th class="text-end">MRP</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.items.map(item => `
                            <tr>
                                <td>${item.item_name || '-'}</td>
                                <td>${item.batch_no || '-'}</td>
                                <td class="text-end">${parseFloat(item.qty || 0).toFixed(0)}</td>
                                <td class="text-end">${parseFloat(item.free_qty || 0).toFixed(0)}</td>
                                <td class="text-end">₹${parseFloat(item.mrp || 0).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
        
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i>Replacement Note (OUT)</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">RN No:</span>
                        <span class="detail-value">${data.transaction?.rn_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${data.transaction?.transaction_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Supplier:</span>
                        <span class="detail-value">${data.transaction?.supplier_name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value fw-bold">₹${parseFloat(data.transaction?.total_amount || 0).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items (${data.items?.length || 0})</h6>
                </div>
                <div class="card-body p-0">
                    ${itemsHtml}
                </div>
            </div>
        `;
    } else if (transactionType === 'REPLACEMENT_RECEIVED') {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
            itemsHtml = `
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Batch</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Free</th>
                            <th class="text-end">MRP</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.items.map(item => `
                            <tr>
                                <td>${item.item_name || '-'}</td>
                                <td>${item.batch_no || '-'}</td>
                                <td class="text-end">${parseFloat(item.qty || 0).toFixed(0)}</td>
                                <td class="text-end">${parseFloat(item.free_qty || 0).toFixed(0)}</td>
                                <td class="text-end">₹${parseFloat(item.mrp || 0).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
        
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-arrow-down-left-circle me-2"></i>Replacement Received (IN)</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">RR No:</span>
                        <span class="detail-value">${data.transaction?.rr_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${data.transaction?.transaction_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Supplier:</span>
                        <span class="detail-value">${data.transaction?.supplier_name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value fw-bold">₹${parseFloat(data.transaction?.total_amount || 0).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items (${data.items?.length || 0})</h6>
                </div>
                <div class="card-body p-0">
                    ${itemsHtml}
                </div>
            </div>
        `;
    } else if (transactionType === 'STOCK_TRANSFER_INCOMING') {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
            itemsHtml = `
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Batch</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.items.map(item => `
                            <tr>
                                <td>${item.item_name || '-'}</td>
                                <td>${item.batch_no || '-'}</td>
                                <td class="text-end">${parseFloat(item.qty || 0).toFixed(0)}</td>
                                <td class="text-end">₹${parseFloat(item.rate || 0).toFixed(2)}</td>
                                <td class="text-end">₹${parseFloat(item.amount || 0).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
        
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-box-arrow-in-down me-2"></i>Stock Transfer Incoming</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">TRF No:</span>
                        <span class="detail-value">${data.trf_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${data.transaction_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">From:</span>
                        <span class="detail-value">${data.supplier_name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value fw-bold">₹${parseFloat(data.total_amount || 0).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items (${data.items?.length || 0})</h6>
                </div>
                <div class="card-body p-0">
                    ${itemsHtml}
                </div>
            </div>
        `;
    } else if (transactionType === 'STOCK_TRANSFER_INCOMING_RETURN') {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
            itemsHtml = `
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Batch</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.items.map(item => `
                            <tr>
                                <td>${item.item_name || '-'}</td>
                                <td>${item.batch_no || '-'}</td>
                                <td class="text-end">${parseFloat(item.qty || 0).toFixed(0)}</td>
                                <td class="text-end">₹${parseFloat(item.rate || 0).toFixed(2)}</td>
                                <td class="text-end">₹${parseFloat(item.amount || 0).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
        
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-box-arrow-up me-2"></i>Stock Transfer Incoming Return</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">TRN No:</span>
                        <span class="detail-value">${data.trn_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${data.transaction_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">To:</span>
                        <span class="detail-value">${data.name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value fw-bold">₹${parseFloat(data.net_amount || 0).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items (${data.items?.length || 0})</h6>
                </div>
                <div class="card-body p-0">
                    ${itemsHtml}
                </div>
            </div>
        `;
    } else if (transactionType === 'SAMPLE_ISSUED') {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
            itemsHtml = `
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Batch</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.items.map(item => `
                            <tr>
                                <td>${item.item_name || '-'}</td>
                                <td>${item.batch_no || '-'}</td>
                                <td class="text-end">${parseFloat(item.qty || 0).toFixed(0)}</td>
                                <td class="text-end">₹${parseFloat(item.rate || 0).toFixed(2)}</td>
                                <td class="text-end">₹${parseFloat(item.amount || 0).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
        
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="bi bi-gift me-2"></i>Sample Issued</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">TRN No:</span>
                        <span class="detail-value">${data.trn_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${data.transaction_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Party:</span>
                        <span class="detail-value">${data.party_name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Party Type:</span>
                        <span class="detail-value">${data.party_type || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value fw-bold">₹${parseFloat(data.net_amount || 0).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items (${data.items?.length || 0})</h6>
                </div>
                <div class="card-body p-0">
                    ${itemsHtml}
                </div>
            </div>
        `;
    } else if (transactionType === 'STOCK_TRANSFER_OUTGOING') {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
            itemsHtml = `
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Batch</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.items.map(item => `
                            <tr>
                                <td>${item.item_name || '-'}</td>
                                <td>${item.batch_no || '-'}</td>
                                <td class="text-end">${parseFloat(item.qty || 0).toFixed(0)}</td>
                                <td class="text-end">₹${parseFloat(item.rate || 0).toFixed(2)}</td>
                                <td class="text-end">₹${parseFloat(item.amount || 0).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
        
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-box-arrow-right me-2"></i>Stock Transfer Outgoing</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">TRF No:</span>
                        <span class="detail-value">${data.trf_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${data.transaction_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">To:</span>
                        <span class="detail-value">${data.transfer_to_name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value fw-bold">₹${parseFloat(data.total_amount || 0).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items (${data.items?.length || 0})</h6>
                </div>
                <div class="card-body p-0">
                    ${itemsHtml}
                </div>
            </div>
        `;
    } else if (transactionType === 'STOCK_TRANSFER_OUTGOING_RETURN') {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
            itemsHtml = `
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Batch</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.items.map(item => `
                            <tr>
                                <td>${item.item_name || '-'}</td>
                                <td>${item.batch_no || '-'}</td>
                                <td class="text-end">${parseFloat(item.qty || 0).toFixed(0)}</td>
                                <td class="text-end">₹${parseFloat(item.rate || 0).toFixed(2)}</td>
                                <td class="text-end">₹${parseFloat(item.amount || 0).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
        
        html = `
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-box-arrow-in-left me-2"></i>Stock Transfer Outgoing Return</h6>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">TRN No:</span>
                        <span class="detail-value">${data.trn_no || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${data.transaction_date || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">From:</span>
                        <span class="detail-value">${data.transfer_from_name || '-'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value fw-bold">₹${parseFloat(data.net_amount || 0).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items (${data.items?.length || 0})</h6>
                </div>
                <div class="card-body p-0">
                    ${itemsHtml}
                </div>
            </div>
        `;
    }

    document.getElementById('stockLedgerModalBody').innerHTML = html;
}

// Show error in modal
function showErrorInStockLedgerModal(message) {
    const modalBody = document.getElementById('stockLedgerModalBody');
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="text-danger mb-3">
                <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
            </div>
            <p class="text-danger">${message}</p>
            <button class="btn btn-sm btn-outline-secondary mt-3" onclick="closeStockLedgerModal()">
                Close
            </button>
        </div>
    `;
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('stockLedgerDetailsModal');
    const backdrop = document.getElementById('stockLedgerModalBackdrop');
    
    if (event.target === backdrop) {
        closeStockLedgerModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('stockLedgerDetailsModal');
        if (modal && modal.style.display === 'flex') {
            closeStockLedgerModal();
        }
    }
});
</script>

<script>
// Global variables for stock ledger
let isLoadingLedger = false;
let observerLedger = null;
let itemId = {{ $item->id }};

// Global infinite scroll function
window.initStockLedgerInfiniteScroll = function() {
    // Disconnect previous observer if exists
    if(observerLedger) {
        observerLedger.disconnect();
    }

    const sentinel = document.getElementById('ledger-sentinel');
    const spinner = document.getElementById('ledger-spinner');
    const loadText = document.getElementById('ledger-load-text');
    const tbody = document.getElementById('ledger-table-body');
    const filterForm = document.querySelector('form[action*="stock-ledger-complete"]');
    
    if(!sentinel || !tbody) return;
    
    isLoadingLedger = false;
    
    async function loadMore(){
        if(isLoadingLedger) return;
        const nextUrl = sentinel.getAttribute('data-next-url');
        if(!nextUrl) return;
        
        isLoadingLedger = true;
        spinner && spinner.classList.remove('d-none');
        loadText && (loadText.textContent = 'Loading...');
        
        try{
            // Add current filter params to nextUrl
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            const url = new URL(nextUrl, window.location.origin);
            
            // Merge current filter params with pagination URL
            params.forEach((value, key) => {
                if(value) url.searchParams.set(key, value);
            });
            
            const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newRows = doc.querySelectorAll('#ledger-table-body tr');
            
            // Filter out empty rows
            const realRows = Array.from(newRows).filter(tr => {
                const tds = tr.querySelectorAll('td');
                return !(tds.length === 1 && tr.querySelector('td[colspan]'));
            });
            
            realRows.forEach(tr => tbody.appendChild(tr));
            
            // Update summary cards
            const newSummary = doc.querySelector('.row.mb-3.g-2');
            const currentSummary = document.querySelector('.row.mb-3.g-2');
            if(newSummary && currentSummary) {
                currentSummary.innerHTML = newSummary.innerHTML;
            }
            
            const newSentinel = doc.querySelector('#ledger-sentinel');
            if(newSentinel){
                sentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url'));
                spinner && spinner.classList.add('d-none');
                loadText && (loadText.textContent = 'Scroll for more');
                isLoadingLedger = false;
            } else {
                observerLedger.disconnect();
                sentinel.remove();
                spinner && spinner.remove();
                loadText && loadText.remove();
            }
        }catch(e){
            console.error('Error loading more records:', e);
            spinner && spinner.classList.add('d-none');
            loadText && (loadText.textContent = 'Error loading');
            isLoadingLedger = false;
        }
    }
    
    observerLedger = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting && !isLoadingLedger){
                loadMore();
            }
        });
    }, { rootMargin: '100px' });
    
    observerLedger.observe(sentinel);
};

document.addEventListener('DOMContentLoaded', function(){
    // Initialize infinite scroll
    window.initStockLedgerInfiniteScroll();
    
    // Handle filter form submission
    const filterForm = document.querySelector('form[action*="stock-ledger-complete"]');
    if(filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            
            fetch(`/bill-software/admin/items/${itemId}/stock-ledger-complete?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newRows = doc.querySelectorAll('#ledger-table-body tr');
                
                // Clear and update table
                const tbody = document.getElementById('ledger-table-body');
                tbody.innerHTML = '';
                if(newRows.length) {
                    newRows.forEach(tr => tbody.appendChild(tr));
                } else {
                    tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No stock movements found</td></tr>';
                }
                
                // Update summary cards
                const newSummary = doc.querySelector('.row.mb-3.g-2');
                const currentSummary = document.querySelector('.row.mb-3.g-2');
                if(newSummary && currentSummary) {
                    currentSummary.innerHTML = newSummary.innerHTML;
                }
                
                // Update footer and reinitialize infinite scroll
                const newFooter = doc.querySelector('.card-footer');
                const currentFooter = document.querySelector('.card-footer');
                if(newFooter && currentFooter) {
                    currentFooter.innerHTML = newFooter.innerHTML;
                    window.initStockLedgerInfiniteScroll();
                }
            })
            .catch(error => {
                console.error('Error filtering records:', error);
                alert('Error filtering records. Please try again.');
            });
        });
    }
});

</script>
@endpush


