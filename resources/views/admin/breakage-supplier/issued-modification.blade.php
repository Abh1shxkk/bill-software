@extends('layouts.admin')

@section('title', 'Breakage/Expiry to Supplier - Issued Modification')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Breakage/Expiry to Supplier - Issued Modification</h5>
            <div class="text-muted small">Modify existing issued transactions</div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-info btn-sm text-white" onclick="showLoadInvoiceModal()">
                <i class="bi bi-folder-open me-1"></i> Load Invoice
            </button>
            <a href="{{ route('admin.breakage-supplier.issued-index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list me-1"></i> View All
            </a>
        </div>
    </div>

    <form id="bsiForm" method="POST" autocomplete="off">
        @csrf
        <input type="hidden" id="transaction_id" name="transaction_id">
        
        <!-- Header Section -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-2">
                         <div class="mb-2">
                            <label class="form-label small fw-bold mb-1">Date</label>
                            <div class="input-group input-group-sm">
                                <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="updateDayName()">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold mb-1">Day</label>
                            <input type="text" id="day_name" name="day_name" class="form-control form-control-sm bg-light text-center" value="{{ date('l') }}" readonly>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold mb-1">Trn.No</label>
                            <input type="text" id="trn_no" name="trn_no" class="form-control form-control-sm bg-light" readonly>
                        </div>
                    </div>

                    <div class="col-md-10">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold mb-1">Supplier</label>
                                        <select id="supplier_id" name="supplier_id" class="form-select form-select-sm" onchange="updateSupplierName()">
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers ?? [] as $supplier)
                                            <option value="{{ $supplier->supplier_id }}" data-name="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="supplier_name" name="supplier_name">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Type (R/C)</label>
                                        <select id="note_type" name="note_type" class="form-select form-select-sm">
                                            <option value="C">Credit</option>
                                            <option value="R">Replace</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Tax [Y/N]</label>
                                        <select id="tax_flag" name="tax_flag" class="form-select form-select-sm">
                                            <option value="N">No</option>
                                            <option value="Y">Yes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold mb-1">Narration</label>
                                        <textarea id="narration" name="narration" class="form-control form-control-sm" rows="1"></textarea>
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold mb-1">GST Vno.</label>
                                        <input type="text" id="gst_vno" name="gst_vno" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Brk Count</label>
                                        <input type="number" id="brk_count" name="brk_count" class="form-control form-control-sm bg-white text-end" value="0" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Exp Count</label>
                                        <input type="number" id="exp_count" name="exp_count" class="form-control form-control-sm bg-white text-end" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 350px;">
                    <table class="table table-bordered table-sm table-hover mb-0" style="font-size: 12px;">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:70px;">Code</th>
                                <th>Item Name</th>
                                <th style="width:100px;">Batch</th>
                                <th style="width:80px;">Exp.</th>
                                <th style="width:60px;" class="text-end">Qty</th>
                                <th style="width:60px;" class="text-end">F.Qty</th>
                                <th style="width:80px;" class="text-end">Rate</th>
                                <th style="width:60px;" class="text-end">Dis%</th>
                                <th style="width:60px;" class="text-end">Scm%</th>
                                <th style="width:80px;">Type</th>
                                <th style="width:100px;" class="text-end">Amount</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody"></tbody>
                    </table>
                </div>
                 <div class="p-1 border-top bg-light text-center small">
                    <button type="button" class="btn btn-link text-decoration-none fw-bold text-primary p-0" onclick="showAddItemModal()">
                        <i class="bi bi-plus-circle me-1"></i> Add Item (F2)
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Sections (Consistent with Issued Transaction) -->
        <div class="fixed-bottom-footer" style="padding-bottom: 60px;">
            <!-- SECTION 1: Taxes & Rates -->
            <div class="card border-0 rounded-0 mb-1" style="background-color: #f0f0f0; border-top: 1px solid #ddd !important;">
                <div class="card-body p-1">
                    <div class="row g-1 align-items-center">
                        <div class="col-md-5 d-flex gap-2">
                            <div class="input-group input-group-sm flex-nowrap" style="width: 100px;">
                                <span class="input-group-text px-1 bg-white border-end-0">SC %</span>
                                <input type="number" id="footer_scm_percent" class="form-control text-end px-1 border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 100px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Tax %</span>
                                <input type="number" id="footer_tax_percent" class="form-control text-end px-1 border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 100px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Excise</span>
                                <input type="number" id="footer_excise" class="form-control text-end px-1 border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="border border-danger bg-danger-subtle p-1 d-flex gap-2 rounded align-items-center" style="height: 31px;">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-danger fw-bold small" style="font-size: 11px;">CGST(%):</span>
                                    <input type="number" id="footer_cgst_percent" class="form-control form-control-sm py-0 text-end border-danger text-danger bg-white" style="height: 20px; width: 45px; font-size: 11px;" readonly tabindex="-1">
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-danger fw-bold small" style="font-size: 11px;">SGST(%):</span>
                                    <input type="number" id="footer_sgst_percent" class="form-control form-control-sm py-0 text-end border-danger text-danger bg-white" style="height: 20px; width: 45px; font-size: 11px;" readonly tabindex="-1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 d-flex justify-content-end gap-2">
                             <div class="input-group input-group-sm flex-nowrap" style="width: 120px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Pack</span>
                                <input type="text" id="footer_pack_1" class="form-control px-1 border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="d-flex align-items-center gap-1 bg-white border rounded px-2" style="height: 31px;">
                                <label class="small m-0" for="footer_disallow">Disallow</label>
                                <input type="text" id="footer_disallow_val" class="form-control form-control-sm border-0 py-0 text-center fw-bold" value="N" style="width: 20px; height: 20px;" readonly>
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 120px;">
                                <span class="input-group-text px-1 fw-bold bg-white border-end-0">MRP</span>
                                <input type="number" id="footer_mrp" class="form-control text-end px-1 fw-bold border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 120px;">
                                <span class="input-group-text px-1 bg-white border-end-0">P.Rate</span>
                                <input type="number" id="footer_p_rate" class="form-control text-end px-1 border-start-0" readonly tabindex="-1">
                            </div>
                             <div class="input-group input-group-sm flex-nowrap" style="width: 120px;">
                                <span class="input-group-text px-1 bg-white border-end-0">S.Rate</span>
                                <input type="number" id="footer_s_rate" class="form-control text-end px-1 border-start-0" readonly tabindex="-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Item Details (Blue Panel) -->
            <div class="card border-0 rounded-0 mb-1" style="background-color: #dbeafe; border-top: 1px solid #bfdbfe !important;">
                <div class="card-body p-1">
                    <div class="row g-1 align-items-center">
                        <div class="col-md-5 d-flex gap-2">
                            <div class="input-group input-group-sm flex-nowrap" style="width: 90px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Pack</span>
                                <input type="text" id="footer_pack_2" class="form-control px-1 border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 140px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Comp:</span>
                                <input type="text" id="footer_company" class="form-control px-1 border-start-0" readonly tabindex="-1">
                            </div>
                             <div class="input-group input-group-sm flex-nowrap" style="width: 80px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Unit</span>
                                <input type="text" id="footer_unit" class="form-control px-1 border-start-0" readonly tabindex="-1">
                            </div>
                             <div class="input-group input-group-sm flex-nowrap" style="width: 90px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Bal.</span>
                                <input type="number" id="footer_balance" class="form-control text-end px-1 border-start-0 text-primary fw-bold" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 90px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Srlno.</span>
                                <input type="text" id="footer_srlno" class="form-control px-1 border-start-0" readonly tabindex="-1">
                            </div>
                        </div>
                        <div class="col-md-7 d-flex justify-content-end gap-1 flex-wrap">
                            <div class="d-flex align-items-center gap-1">
                                <label class="small fw-bold mb-0">N.T Amt.</label>
                                <input type="number" id="footer_row_nt_amt" class="form-control form-control-sm text-end" style="width: 80px;" readonly tabindex="-1">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="small fw-bold mb-0">DIS. Amt.</label>
                                <input type="number" id="footer_row_dis_amt" class="form-control form-control-sm text-end" style="width: 70px;" readonly tabindex="-1">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="small fw-bold mb-0">Net Amt.</label>
                                <input type="number" id="footer_row_net_amt" class="form-control form-control-sm text-end fw-bold bg-white" style="width: 80px;" readonly tabindex="-1">
                            </div>
                            <div class="vr mx-1"></div>
                            <div class="d-flex gap-2">
                                <div class="d-flex flex-column justify-content-center">
                                    <div class="d-flex align-items-center gap-1 justify-content-end mb-1">
                                        <label class="small text-muted mb-0" style="font-size: 10px;">P.Scm+</label>
                                        <input type="number" id="footer_p_scm" class="form-control form-control-sm py-0 text-end" style="height: 18px; width: 50px; font-size: 10px;" value="0.00" readonly tabindex="-1">
                                    </div>
                                    <div class="d-flex align-items-center gap-1 justify-content-end">
                                        <label class="small text-muted mb-0" style="font-size: 10px;">S.Scm+</label>
                                        <input type="number" id="footer_s_scm" class="form-control form-control-sm py-0 text-end" style="height: 18px; width: 50px; font-size: 10px;" value="0.00" readonly tabindex="-1">
                                    </div>
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                     <div class="d-flex align-items-center gap-1 justify-content-end mb-1">
                                        <label class="small mb-0" style="font-size: 10px;">Half Scm.</label>
                                        <input type="number" id="footer_row_half_scm" class="form-control form-control-sm py-0 text-end" style="height: 18px; width: 50px; font-size: 10px;" value="0.00" readonly tabindex="-1">
                                    </div>
                                    <div class="d-flex align-items-center gap-1 justify-content-end">
                                        <label class="small mb-0" style="font-size: 10px;">Scm.Amt.</label>
                                        <input type="number" id="footer_row_scm_amt" class="form-control form-control-sm py-0 text-end" style="height: 18px; width: 50px; font-size: 10px;" value="0.00" readonly tabindex="-1">
                                    </div>
                                </div>
                                <div class="d-flex flex-column justify-content-center ms-1">
                                     <div class="d-flex align-items-center gap-1">
                                        <label class="small mb-0">Tax Amt.</label>
                                        <input type="number" id="footer_row_tax_amt" class="form-control form-control-sm text-end" style="width: 50px;" readonly tabindex="-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Totals (Pink Panel) -->
            <div class="card border-0 rounded-0" style="background-color: #ffcccc; border-top: 2px solid #ff9999 !important;">
                <div class="card-body p-2">
                    <div class="row g-2 align-items-center justify-content-end">
                        <div class="col-12 d-flex justify-content-end gap-2 flex-wrap">
                            <div class="input-group input-group-sm flex-nowrap" style="width: 140px;">
                                <span class="input-group-text px-1 fw-bold bg-white border-end-0">N.T AMT</span>
                                <input type="number" id="total_nt_amt" name="total_nt_amt" class="form-control text-end px-1 bg-white border-start-0 fw-bold" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 100px;">
                                <span class="input-group-text px-1 bg-white border-end-0">SC</span>
                                <input type="number" id="total_sc" name="total_sc" class="form-control text-end px-1 bg-white border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 130px;">
                                <span class="input-group-text px-1 bg-white border-end-0">DIS. AMT</span>
                                <input type="number" id="total_dis_amt" name="total_dis_amt" class="form-control text-end px-1 bg-white border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 130px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Scm. AMT</span>
                                <input type="number" id="total_scm_amt" name="total_scm_amt" class="form-control text-end px-1 bg-white border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 130px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Half.Scm.</span>
                                <input type="number" id="total_half_scm" name="total_half_scm" class="form-control text-end px-1 bg-white border-start-0" readonly tabindex="-1">
                            </div>
                             <div class="input-group input-group-sm flex-nowrap" style="width: 120px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Tax</span>
                                <input type="number" id="total_tax" name="total_tax" class="form-control text-end px-1 bg-white border-start-0" readonly tabindex="-1">
                            </div>
                             <div class="input-group input-group-sm flex-nowrap" style="width: 180px;">
                                <span class="input-group-text px-1 fw-bold bg-danger text-white border-danger">INV. AMT</span>
                                <input type="number" id="total_inv_amt" name="total_inv_amt" class="form-control text-end px-1 fw-bold border-danger text-danger bg-white" style="font-size: 1.25em;" readonly tabindex="-1">
                            </div>
                        </div>
                        <div class="col-12 text-center mt-2 d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-success btn-sm px-4" onclick="updateTransaction()" id="updateBtn" disabled>
                                <i class="bi bi-check-lg me-1"></i> Update
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm px-4" onclick="cancelModification()">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-danger btn-sm px-4" onclick="deleteSelectedItem()">
                                <i class="bi bi-trash me-1"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.02); cursor: pointer; }
    .row-selected { background-color: #e8f0fe !important; }
    .row-selected td { background-color: #e8f0fe !important; }
    
    .fixed-bottom-footer {
        position: fixed; bottom: 0; left: 250px; right: 0; z-index: 1030; background: white; box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
    @media (max-width: 992px) { .fixed-bottom-footer { left: 0; } }
    .fixed-bottom-footer .input-group-text { font-size: 11px; padding: 0.25rem 0.5rem; }
    .fixed-bottom-footer .form-control { font-size: 11px; padding: 0.25rem 0.5rem; }
    
    /* Modal Styles */
    .modal-backdrop-custom { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; }
    .modal-backdrop-custom.show { display: block; }
    .custom-modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 800px; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 1055; background-clip: padding-box; outline: 0; }
    .custom-modal.show { display: block; }
    .custom-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1rem; border-bottom: 1px solid #dee2e6; border-top-left-radius: 8px; border-top-right-radius: 8px; background-color: #f8f9fa; }
    .custom-modal-body { position: relative; flex: 1 1 auto; padding: 1rem; max-height: 70vh; overflow-y: auto; }
    .custom-modal-footer { display: flex; flex-wrap: wrap; flex-shrink: 0; align-items: center; justify-content: flex-end; padding: 0.75rem; border-top: 1px solid #dee2e6; border-bottom-right-radius: 8px; border-bottom-left-radius: 8px; background-color: #f8f9fa; }
</style>
@endpush

@push('scripts')
<script>
let rowIndex = 0, allItems = [], selectedRowIndex = null, currentTransactionId = null;

document.addEventListener('DOMContentLoaded', function() { loadItems(); });

function updateDayName() {
    const d = new Date(document.getElementById('transaction_date').value);
    document.getElementById('day_name').value = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d.getDay()];
}

function updateSupplierName() {
    const sel = document.getElementById('supplier_id');
    document.getElementById('supplier_name').value = sel.options[sel.selectedIndex]?.dataset.name || '';
}

function loadItems() {
    fetch('{{ route("admin.breakage-supplier.get-items") }}').then(r => r.json()).then(data => { allItems = data || []; });
}

// ==== LOAD INVOICE MODAL ====
function showLoadInvoiceModal() {
    const html = `
        <div class="modal-backdrop-custom show" id="invoiceModalBackdrop" onclick="closeInvoiceModal()"></div>
        <div class="custom-modal show" id="invoiceModal">
            <div class="custom-modal-header">
                <h5 class="custom-modal-title m-0 fs-6"><i class="bi bi-folder-open me-2"></i>Load Invoice</h5>
                <button type="button" class="btn-close" onclick="closeInvoiceModal()"></button>
            </div>
            <div class="custom-modal-body">
                <input type="text" id="invoiceSearchInput" class="form-control mb-3" placeholder="Search by Trn No or Supplier..." onkeyup="searchInvoices()">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm mb-0 small">
                        <thead class="table-light"><tr><th>Trn No</th><th>Date</th><th>Supplier</th><th class="text-end">Amount</th></tr></thead>
                        <tbody id="invoicesListBody"><tr><td colspan="4" class="text-center py-3">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeInvoiceModal()">Close</button>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    loadInvoices();
}

function loadInvoices(search = '') {
    fetch(`{{ route('admin.breakage-supplier.get-issued-past-invoices') }}?search=${encodeURIComponent(search)}`)
    .then(r => r.json()).then(invoices => {
        const tbody = document.getElementById('invoicesListBody');
        if (!invoices.length) { tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">No invoices found</td></tr>'; return; }
        tbody.innerHTML = invoices.map(inv => `
            <tr style="cursor: pointer;" onclick="loadInvoice(${inv.id})">
                <td><strong>${inv.trn_no}</strong></td>
                <td>${inv.transaction_date ? new Date(inv.transaction_date).toLocaleDateString() : '-'}</td>
                <td>${inv.supplier_name || '-'}</td>
                <td class="text-end">${parseFloat(inv.total_inv_amt || 0).toFixed(2)}</td>
            </tr>`).join('');
    }).catch(() => { document.getElementById('invoicesListBody').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Error loading invoices</td></tr>'; });
}

function searchInvoices() { loadInvoices(document.getElementById('invoiceSearchInput').value); }
function closeInvoiceModal() { document.getElementById('invoiceModal')?.remove(); document.getElementById('invoiceModalBackdrop')?.remove(); }

function loadInvoice(id) {
    fetch(`{{ url('admin/breakage-supplier/issued') }}/${id}`).then(r => r.json()).then(data => { populateForm(data); closeInvoiceModal(); })
    .catch(e => { console.error(e); alert('Error loading invoice'); });
}

function populateForm(data) {
    currentTransactionId = data.id;
    document.getElementById('transaction_id').value = data.id;
    document.getElementById('trn_no').value = data.trn_no;
    document.getElementById('transaction_date').value = data.transaction_date ? data.transaction_date.split('T')[0] : '';
    document.getElementById('supplier_id').value = data.supplier_id || '';
    updateSupplierName();
    document.getElementById('note_type').value = data.note_type || 'C';
    document.getElementById('tax_flag').value = data.tax_flag || 'N';
    document.getElementById('gst_vno').value = data.gst_vno || '';
    document.getElementById('narration').value = data.narration || '';
    
    // Totals
    const set = (id, val) => document.getElementById(id).value = parseFloat(val||0).toFixed(2);
    set('total_nt_amt', data.total_nt_amt);
    set('total_sc', data.total_sc);
    set('total_dis_amt', data.total_dis_amt);
    set('total_scm_amt', data.total_scm_amt);
    set('total_half_scm', data.total_half_scm);
    set('total_tax', data.total_tax);
    set('total_inv_amt', data.total_inv_amt);
    set('brk_count', data.brk_count);
    set('exp_count', data.exp_count);
    
    document.getElementById('itemsTableBody').innerHTML = '';
    rowIndex = 0;
    if (data.items && data.items.length) { data.items.forEach(item => addItemRowFromData(item)); }
    document.getElementById('updateBtn').disabled = false;
    updateDayName();
}

// ==== ITEM LOGIC ====
function addItemRowFromData(item) {
    const idx = rowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${idx}`; row.dataset.rowIndex = idx;
    row.onclick = function(e) { if (!['BUTTON','INPUT','SELECT'].includes(e.target.tagName)) selectRow(idx); };
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][code]" value="${item.item_code || item.item_id}" readonly></td>
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][name]" value="${item.item_name || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][batch]" value="${item.batch_no || ''}" readonly></td>
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][expiry]" value="${item.expiry || ''}" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][qty]" value="${item.qty || 0}" min="0" onchange="calcRow(${idx})" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][free_qty]" value="${item.free_qty || 0}" min="0" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][rate]" value="${item.rate || 0}" step="0.01" onchange="calcRow(${idx})" style="width:80px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][dis_percent]" value="${item.dis_percent || 0}" step="0.01" onchange="calcRow(${idx})" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][scm_percent]" value="${item.scm_percent || 0}" step="0.01" onchange="calcRow(${idx})" style="width:60px;"></td>
        <td><select class="form-select form-select-sm py-0" name="items[${idx}][br_ex_type]" onchange="updateCounts()" style="width:80px;"><option value="BREAKAGE" ${item.br_ex_type === 'BREAKAGE' ? 'selected' : ''}>Brk</option><option value="EXPIRY" ${item.br_ex_type === 'EXPIRY' ? 'selected' : ''}>Exp</option></select></td>
        <td><input type="number" class="form-control form-control-sm text-end border-0 bg-transparent fw-bold py-0" name="items[${idx}][amount]" value="${parseFloat(item.amount || 0).toFixed(2)}" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger p-0" onclick="removeRow(${idx})"><i class="bi bi-trash"></i></button></td>
        
        <input type="hidden" name="items[${idx}][item_id]" value="${item.item_id}">
        <input type="hidden" name="items[${idx}][batch_id]" value="${item.batch_id || ''}">
        <input type="hidden" name="items[${idx}][packing]" value="${item.packing || ''}">
        <input type="hidden" name="items[${idx}][unit]" value="${item.unit || ''}">
        <input type="hidden" name="items[${idx}][company_name]" value="${item.company_name || ''}">
        <input type="hidden" name="items[${idx}][mrp]" value="${item.mrp || 0}">
        <input type="hidden" name="items[${idx}][p_rate]" value="${item.p_rate || 0}">
        <input type="hidden" name="items[${idx}][s_rate]" value="${item.s_rate || 0}">
        <input type="hidden" name="items[${idx}][hsn_code]" value="${item.hsn_code || ''}">
        <input type="hidden" name="items[${idx}][cgst_percent]" value="${item.cgst_percent || 0}">
        <input type="hidden" name="items[${idx}][sgst_percent]" value="${item.sgst_percent || 0}">
        <input type="hidden" name="items[${idx}][cl_qty]" value="${item.cl_qty || 0}">
        <input type="hidden" name="items[${idx}][tax_amount]" class="row-tax-amt" value="0">
        <input type="hidden" name="items[${idx}][dis_amount]" class="row-dis-amt" value="0">
        <input type="hidden" name="items[${idx}][scm_amount]" class="row-scm-amt" value="0">
        `;
    document.getElementById('itemsTableBody').appendChild(row);
    selectRow(idx); calcRow(idx); updateCounts();
}

function showAddItemModal() {
    if (!currentTransactionId) { alert('Please load an invoice first'); return; }
    // ...reuse same modal logic...
    const html = `
        <div class="modal-backdrop-custom show" id="itemModalBackdrop" onclick="closeItemModal()"></div>
        <div class="custom-modal show" id="itemModal">
            <div class="custom-modal-header">
                <h5 class="custom-modal-title m-0 fs-6"><i class="bi bi-box-seam me-2"></i>Select Item</h5>
                <button type="button" class="btn-close" onclick="closeItemModal()"></button>
            </div>
            <div class="custom-modal-body">
                <input type="text" id="itemSearchInput" class="form-control mb-3" placeholder="Type to search..." onkeyup="filterItemsList()">
                <div class="table-responsive"><table class="table table-hover table-bordered table-sm mb-0 small"><tbody id="itemsListBody"></tbody></table></div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput').focus();
    renderItemsList();
}
function filterItemsList() { renderItemsList(document.getElementById('itemSearchInput').value); }
function renderItemsList(filter = '') {
    const filtered = filter ? allItems.filter(i => i.name.toLowerCase().includes(filter.toLowerCase()) || i.id.toString().includes(filter)) : allItems;
    document.getElementById('itemsListBody').innerHTML = filtered.slice(0,50).map(i => `<tr style="cursor:pointer;" onclick="selectItem(${i.id})"><td>${i.id}</td><td>${i.name}</td><td>${i.packing||'-'}</td><td>${parseFloat(i.mrp||0).toFixed(2)}</td></tr>`).join('');
}
function selectItem(id) { const item = allItems.find(i => i.id === id); if (item) { window.selectedItemData = item; closeItemModal(); showBatchModal(item); } }
function closeItemModal() { document.getElementById('itemModal')?.remove(); document.getElementById('itemModalBackdrop')?.remove(); }

function showBatchModal(item) {
     const html = `
        <div class="modal-backdrop-custom show" id="batchModalBackdrop"></div>
        <div class="custom-modal show" id="batchModal">
            <div class="custom-modal-header"><h5 class="custom-modal-title m-0 fs-6">Select Batch</h5><button type="button" class="btn-close" onclick="closeBatchModal()"></button></div>
            <div class="custom-modal-body"><div id="batchContent">Loading...</div></div>
            <div class="custom-modal-footer"><button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Cancel</button></div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    fetch(`{{ url('admin/api/item-batches') }}/${item.id}`).then(r => r.json()).then(data => {
        const batches = data.batches || data || [];
        document.getElementById('batchContent').innerHTML = `
            <table class="table table-hover table-bordered table-sm mb-0 small">
                <thead class="table-light"><tr><th>Batch No</th><th>Exp</th><th class="text-end">Qty</th><th class="text-end">MRP</th></tr></thead>
                <tbody>${batches.map(b => `<tr style="cursor:pointer;" onclick="selectBatch('${b.id}','${b.batch_no}','${b.expiry_display}',${b.qty},${b.mrp},${b.pur_rate},${b.s_rate})"><td>${b.batch_no}</td><td>${b.expiry_display}</td><td class="text-end">${b.qty}</td><td class="text-end">${b.mrp}</td></tr>`).join('')}</tbody>
            </table>`;
    });
}
function selectBatch(batchId, batchNo, expiry, qty, mrp, pRate, sRate) {
    const item = window.selectedItemData;
    addItemRowFromData({
        item_id: item.id, item_code: item.id, item_name: item.name, batch_no: batchNo, expiry: expiry, qty: 1, free_qty: 0,
        rate: pRate, dis_percent: 0, scm_percent: 0, br_ex_type: 'BREAKAGE', amount: 0,
        batch_id: batchId, packing: item.packing, unit: item.unit, company_name: item.company_name, mrp: mrp, p_rate: pRate, s_rate: sRate,
        hsn_code: item.hsn_code, cgst_percent: item.cgst, sgst_percent: item.sgst
    });
    closeBatchModal();
}
function closeBatchModal() { document.getElementById('batchModal')?.remove(); document.getElementById('batchModalBackdrop')?.remove(); }

function selectRow(idx) {
    document.querySelectorAll('#itemsTableBody tr').forEach(tr => tr.classList.remove('row-selected'));
    const row = document.getElementById(`row-${idx}`);
    if (row) { row.classList.add('row-selected'); selectedRowIndex = idx; updateFooter(row); }
}

function updateFooter(row) {
    if (!row) {
        ['footer_pack_1','footer_mrp','footer_p_rate','footer_s_rate','footer_pack_2',
         'footer_company','footer_unit','footer_balance','footer_srlno','footer_row_nt_amt',
         'footer_row_dis_amt','footer_row_net_amt','footer_row_tax_amt','footer_scm_percent',
         'footer_tax_percent','footer_cgst_percent','footer_sgst_percent',
         'footer_row_scm_amt','footer_row_half_scm'
        ].forEach(id => { if(document.getElementById(id)) document.getElementById(id).value = ''; });
        return;
    }
    const get = n => row.querySelector(`input[name*="[${n}]"]`)?.value || '';
    
    document.getElementById('footer_scm_percent').value = get('scm_percent');
    const cgst = parseFloat(get('cgst_percent') || 0);
    const sgst = parseFloat(get('sgst_percent') || 0);
    document.getElementById('footer_tax_percent').value = (cgst + sgst).toFixed(2);
    document.getElementById('footer_cgst_percent').value = cgst;
    document.getElementById('footer_sgst_percent').value = sgst;
    document.getElementById('footer_pack_1').value = get('packing');
    document.getElementById('footer_mrp').value = get('mrp');
    document.getElementById('footer_p_rate').value = get('p_rate');
    document.getElementById('footer_s_rate').value = get('s_rate');
    
    document.getElementById('footer_pack_2').value = get('packing');
    document.getElementById('footer_company').value = get('company_name');
    document.getElementById('footer_unit').value = get('unit');
    document.getElementById('footer_balance').value = get('cl_qty');
    document.getElementById('footer_srlno').value = parseInt(row.dataset.rowIndex) + 1;
    
    // Calc logic reused
    const qty = parseFloat(get('qty')||0);
    const rate = parseFloat(get('rate')||0);
    const gross = qty * rate;
    const disAmt = parseFloat(row.querySelector('.row-dis-amt')?.value||0);
    const scmAmt = parseFloat(row.querySelector('.row-scm-amt')?.value||0);
    const taxAmt = parseFloat(row.querySelector('.row-tax-amt')?.value||0);
    const ntAmt = gross - disAmt - scmAmt;
    
    document.getElementById('footer_row_nt_amt').value = ntAmt.toFixed(2);
    document.getElementById('footer_row_dis_amt').value = disAmt.toFixed(2);
    document.getElementById('footer_row_scm_amt').value = scmAmt.toFixed(2);
    document.getElementById('footer_row_tax_amt').value = taxAmt.toFixed(2);
    document.getElementById('footer_row_net_amt').value = (ntAmt + taxAmt).toFixed(2);
}

function removeRow(idx) { document.getElementById(`row-${idx}`)?.remove(); calcTotals(); updateCounts(); }
function deleteSelectedItem() { if (selectedRowIndex !== null) { removeRow(selectedRowIndex); selectedRowIndex = null; } else alert('Select a row first'); }

function calcRow(idx) {
    const row = document.getElementById(`row-${idx}`); if (!row) return;
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
    const dis = parseFloat(row.querySelector('input[name*="[dis_percent]"]').value) || 0;
    const scm = parseFloat(row.querySelector('input[name*="[scm_percent]"]').value) || 0;
    const cgst = parseFloat(row.querySelector('input[name*="[cgst_percent]"]').value) || 0;
    const sgst = parseFloat(row.querySelector('input[name*="[sgst_percent]"]').value) || 0;
    
    const gross = qty * rate;
    const disAmt = gross * dis / 100;
    const scmAmt = gross * scm / 100;
    const taxable = gross - disAmt - scmAmt;
    const taxAmt = taxable * (cgst + sgst) / 100;
    
    row.querySelector('.row-dis-amt').value = disAmt;
    row.querySelector('.row-scm-amt').value = scmAmt;
    row.querySelector('.row-tax-amt').value = taxAmt;
    row.querySelector('input[name*="[amount]"]').value = (taxable + taxAmt).toFixed(2);
    
    if (selectedRowIndex === idx) updateFooter(row);
    calcTotals();
}

function calcTotals() {
    let nt = 0, dis = 0, scm = 0, tax = 0, inv = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
        const rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
        const d = parseFloat(row.querySelector('.row-dis-amt')?.value) || 0;
        const s = parseFloat(row.querySelector('.row-scm-amt')?.value) || 0;
        const t = parseFloat(row.querySelector('.row-tax-amt')?.value) || 0;
        const gross = qty * rate;
        
        nt += (gross - d - s); 
        dis += d;
        scm += s;
        tax += t;
        inv += (gross - d - s + t);
    });
    
    document.getElementById('total_nt_amt').value = nt.toFixed(2);
    document.getElementById('total_dis_amt').value = dis.toFixed(2);
    document.getElementById('total_scm_amt').value = scm.toFixed(2);
    document.getElementById('total_tax').value = tax.toFixed(2);
    document.getElementById('total_inv_amt').value = inv.toFixed(2);
}

function updateCounts() {
    let brk = 0, exp = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const type = row.querySelector('select[name*="[br_ex_type]"]')?.value;
        if (type === 'BREAKAGE') brk++; else if (type === 'EXPIRY') exp++;
    });
    document.getElementById('brk_count').value = brk;
    document.getElementById('exp_count').value = exp;
}

function updateTransaction() {
    if (!currentTransactionId) { alert('No transaction loaded'); return; }
    if (!document.querySelectorAll('#itemsTableBody tr').length) { alert('Add at least one item'); return; }
    const formData = new FormData(document.getElementById('bsiForm'));
    formData.append('_method', 'PUT');
    fetch(`{{ url('admin/breakage-supplier/issued') }}/${currentTransactionId}`, {
        method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(data => {
        if (data.success) { alert('Updated successfully'); window.location.href = '{{ route("admin.breakage-supplier.issued-index") }}'; }
        else alert('Error: ' + data.message);
    }).catch(() => alert('Error updating transaction'));
}

function cancelModification() {
    if (confirm('Are you sure? Unsaved changes will be lost.')) window.location.href = '{{ route("admin.breakage-supplier.issued-index") }}';
}
</script>
@endpush
@endsection
