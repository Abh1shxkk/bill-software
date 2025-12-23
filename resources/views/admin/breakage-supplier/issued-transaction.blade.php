@extends('layouts.admin')

@section('title', 'Breakage/Expiry to Supplier - Issued Transaction')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="bi bi-box-arrow-up me-2"></i> Breakage/Expiry to Supplier - Issued Transaction</h5>
            <div class="text-muted small">Create new issued transaction</div>
        </div>
        <a href="{{ route('admin.breakage-supplier.issued-index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list me-1"></i> View All
        </a>
    </div>

    <form id="bsiForm" method="POST" autocomplete="off">
        @csrf
        <!-- Header Section -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="row g-3">
                    <!-- Date & Trn No -->
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
                            <input type="text" id="trn_no" name="trn_no" class="form-control form-control-sm bg-light" value="{{ $trnNo ?? '477' }}" readonly>
                        </div>
                    </div>

                    <!-- Main Details -->
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
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold mb-1">Narration</label>
                                        <textarea id="narration" name="narration" class="form-control form-control-sm" rows="1" placeholder="Enter remarks..."></textarea>
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
                    <a href="javascript:void(0)" onclick="showAddItemModal()" class="text-decoration-none fw-bold text-primary"><i class="bi bi-plus-circle me-1"></i> Add Item (F2)</a>
                </div>
            </div>
        </div>

        <!-- Footer Sections -->
        <!-- Justification: "Ye teeno alag alag section he" - Implemented as 3 distinct semantic panels -->
        <div class="fixed-bottom-footer" style="padding-bottom: 60px;"> <!-- Padding for bottoms actions -->
            
            <!-- SECTION 1: Taxes & Rates (Grey Panel) -->
            <div class="card border-0 rounded-0 mb-1" style="background-color: #f0f0f0; border-top: 1px solid #ddd !important;">
                <div class="card-body p-1">
                    <div class="row g-1 align-items-center">
                        <!-- Left: Tax info -->
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
                            
                            <!-- Red Box for GST Breakdown -->
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

                        <!-- Right: Rates & Details -->
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

            <!-- SECTION 2: Item Details & Calculations (Blue Panel) -->
            <div class="card border-0 rounded-0 mb-1" style="background-color: #dbeafe; border-top: 1px solid #bfdbfe !important;">
                <div class="card-body p-1">
                    <div class="row g-1 align-items-center">
                         <!-- Left: Item Attributes -->
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

                         <!-- Right: Row Calculations -->
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
                            
                            <!-- Schemes Mini Grid -->
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
                                        <input type="number" id="footer_row_tax_amt" class="form-control form-control-sm text-end" style="width: 60px;" readonly tabindex="-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Transaction Grand Totals (Pink/Red Panel) -->
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
                         <!-- Action Buttons Docked at right bottom or centered -->
                        <div class="col-12 text-center mt-2 d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-primary btn-sm px-4" onclick="saveTransaction()">
                                <i class="bi bi-check-lg me-1"></i> Save
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm px-4" onclick="cancelTransaction()">
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

<!-- Scripts and Styles -->
@push('styles')
<style>
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.02); cursor: pointer; }
    .row-selected { background-color: #e8f0fe !important; }
    .row-selected td { background-color: #e8f0fe !important; }
    
    .fixed-bottom-footer {
        position: fixed;
        bottom: 0;
        left: 250px; /* Adjust based on sidebar width */
        right: 0;
        z-index: 1030;
        background: white;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
    @media (max-width: 992px) { .fixed-bottom-footer { left: 0; } }
    
    /* Improve Input Group Look inside Footer */
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

<script>
let rowIndex = 0, allItems = [], selectedRowIndex = null;

document.addEventListener('DOMContentLoaded', function() { loadItems(); });

function updateDayName() {
    const d = new Date(document.getElementById('transaction_date').value);
    document.getElementById('day_name').value = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d.getDay()];
}

function updateSupplierName() {
    const sel = document.getElementById('supplier_id');
    const name = sel.options[sel.selectedIndex].getAttribute('data-name');
    document.getElementById('supplier_name').value = name || '';
}

function loadItems() {
    fetch('{{ route("admin.breakage-supplier.get-items") }}').then(r => r.json()).then(data => { allItems = data || []; });
}

function showAddItemModal() {
     const html = `
        <div class="modal-backdrop-custom show" id="itemModalBackdrop" onclick="closeItemModal()"></div>
        <div class="custom-modal show" id="itemModal">
            <div class="custom-modal-header">
                <h5 class="custom-modal-title m-0 fs-6"><i class="bi bi-box-seam me-2"></i>Select Item</h5>
                <button type="button" class="btn-close" onclick="closeItemModal()"></button>
            </div>
            <div class="custom-modal-body">
                <input type="text" id="itemSearchInput" class="form-control mb-3" placeholder="Type to search items..." onkeyup="filterItemsList()">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm mb-0 small">
                        <thead class="table-light"><tr><th>Code</th><th>Item Name</th><th>Packing</th><th>Company</th><th class="text-end">MRP</th></tr></thead>
                        <tbody id="itemsListBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput').focus();
    renderItemsList();
}

function renderItemsList(filter = '') {
    const filtered = filter ? allItems.filter(i => i.name.toLowerCase().includes(filter.toLowerCase()) || i.id.toString().includes(filter)) : allItems;
    document.getElementById('itemsListBody').innerHTML = filtered.slice(0,100).map(i => `<tr style="cursor:pointer;" onclick="selectItem(${i.id})"><td>${i.id}</td><td>${i.name}</td><td>${i.packing||'-'}</td><td>${i.company_name||'-'}</td><td class="text-end">${parseFloat(i.mrp||0).toFixed(2)}</td></tr>`).join('');
}

function filterItemsList() { renderItemsList(document.getElementById('itemSearchInput').value); }
function selectItem(id) { const item = allItems.find(i => i.id === id); if (item) { window.selectedItemData = item; closeItemModal(); showBatchModal(item); } }
function closeItemModal() { document.getElementById('itemModal')?.remove(); document.getElementById('itemModalBackdrop')?.remove(); }

function showBatchModal(item) {
     const html = `
        <div class="modal-backdrop-custom show" id="batchModalBackdrop"></div>
        <div class="custom-modal show" id="batchModal">
            <div class="custom-modal-header">
                <h5 class="custom-modal-title m-0 fs-6"><i class="bi bi-stack me-2"></i>Select Batch - ${item.name}</h5>
                <button type="button" class="btn-close" onclick="closeBatchModal()"></button>
            </div>
            <div class="custom-modal-body">
                <div id="batchLoading" class="text-center py-4"><div class="spinner-border text-primary"></div><div class="mt-2">Loading batches...</div></div>
                <div id="batchContent" style="display:none;"></div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeBatchModal()">Cancel</button>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    
    fetch(`{{ url('admin/api/item-batches') }}/${item.id}`).then(r => r.json()).then(data => {
        const batches = data.batches || data || [];
        document.getElementById('batchLoading').style.display = 'none';
        const content = document.getElementById('batchContent');
        content.style.display = 'block';
        if (!batches.length) { content.innerHTML = '<div class="alert alert-warning">No batches available for this item.</div>'; return; }
        content.innerHTML = `
            <table class="table table-hover table-bordered table-sm mb-0 small">
                <thead class="table-light"><tr><th>Batch No</th><th>Expiry</th><th class="text-end">Qty</th><th class="text-end">MRP</th><th class="text-end">P.Rate</th></tr></thead>
                <tbody>${batches.map(b => `<tr style="cursor:pointer;" onclick="selectBatch('${b.id}','${(b.batch_no||'').replace(/'/g,"\\'")}','${(b.expiry_display||b.expiry||'').replace(/'/g,"\\'")}',${b.qty||0},${b.mrp||0},${b.pur_rate||b.cost||0},${b.s_rate||0})"><td>${b.batch_no||'-'}</td><td>${b.expiry_display||'-'}</td><td class="text-end">${b.qty||0}</td><td class="text-end">${parseFloat(b.mrp||0).toFixed(2)}</td><td class="text-end">${parseFloat(b.pur_rate||b.cost||0).toFixed(2)}</td></tr>`).join('')}</tbody>
            </table>`;
    }).catch(() => { document.getElementById('batchLoading').style.display = 'none'; document.getElementById('batchContent').style.display = 'block'; document.getElementById('batchContent').innerHTML = '<div class="alert alert-danger">Error loading batches.</div>'; });
}

function selectBatch(batchId, batchNo, expiry, qty, mrp, pRate, sRate) {
    const item = window.selectedItemData; if (!item) return;
    addItemRow(item, batchId, batchNo, expiry, qty, mrp, pRate, sRate);
    closeBatchModal(); window.selectedItemData = null;
}
function closeBatchModal() { document.getElementById('batchModal')?.remove(); document.getElementById('batchModalBackdrop')?.remove(); }

function addItemRow(item, batchId, batchNo, expiry, batchQty, mrp, pRate, sRate) {
    const idx = rowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${idx}`; row.dataset.rowIndex = idx;
    row.onclick = function(e) { if (!['BUTTON','INPUT','SELECT'].includes(e.target.tagName)) selectRow(idx); };
    
    const cgst = item.cgst || 0;
    const sgst = item.sgst || 0;
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][code]" value="${item.id}" readonly></td>
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][name]" value="${item.name}" readonly></td>
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][batch]" value="${batchNo}" readonly></td>
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][expiry]" value="${expiry}" readonly></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][qty]" value="1" min="0" onchange="calcRow(${idx})" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][free_qty]" value="0" min="0" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][rate]" value="${pRate||0}" step="0.01" onchange="calcRow(${idx})" style="width:80px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][dis_percent]" value="0" step="0.01" onchange="calcRow(${idx})" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][scm_percent]" value="0" step="0.01" onchange="calcRow(${idx})" style="width:60px;"></td>
        <td><select class="form-select form-select-sm py-0" name="items[${idx}][br_ex_type]" style="width:80px;"><option value="BREAKAGE">Brk</option><option value="EXPIRY">Exp</option></select></td>
        <td><input type="number" class="form-control form-control-sm text-end border-0 bg-transparent fw-bold py-0" name="items[${idx}][amount]" value="0.00" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger p-0" onclick="removeRow(${idx})"><i class="bi bi-trash"></i></button></td>
        
        <!-- Hidden Data Fields -->
        <input type="hidden" name="items[${idx}][item_id]" value="${item.id}">
        <input type="hidden" name="items[${idx}][batch_id]" value="${batchId}">
        <input type="hidden" name="items[${idx}][packing]" value="${item.packing||''}">
        <input type="hidden" name="items[${idx}][unit]" value="${item.unit||''}">
        <input type="hidden" name="items[${idx}][company_name]" value="${item.company_name||''}">
        <input type="hidden" name="items[${idx}][mrp]" value="${mrp||0}">
        <input type="hidden" name="items[${idx}][p_rate]" value="${pRate||0}">
        <input type="hidden" name="items[${idx}][s_rate]" value="${sRate||0}">
        <input type="hidden" name="items[${idx}][hsn_code]" value="${item.hsn_code||''}">
        <input type="hidden" name="items[${idx}][cgst_percent]" value="${cgst}">
        <input type="hidden" name="items[${idx}][sgst_percent]" value="${sgst}">
        <input type="hidden" name="items[${idx}][cl_qty]" value="${batchQty||0}">
        
        <input type="hidden" name="items[${idx}][tax_amount]" class="row-tax-amt" value="0">
        <input type="hidden" name="items[${idx}][dis_amount]" class="row-dis-amt" value="0">
        <input type="hidden" name="items[${idx}][scm_amount]" class="row-scm-amt" value="0">
    `;
    document.getElementById('itemsTableBody').appendChild(row);
    selectRow(idx); calcRow(idx);
}

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
    
    const qty = parseFloat(get('qty')||0);
    const rate = parseFloat(get('rate')||0);
    const gross = qty * rate;
    const disAmt = parseFloat(get('dis_amount')||0);
    const scmAmt = parseFloat(get('scm_amount'||0));
    const ntAmt = gross - disAmt - scmAmt;
    const taxAmt = parseFloat(get('tax_amount')||0);
    
    document.getElementById('footer_row_nt_amt').value = ntAmt.toFixed(2);
    document.getElementById('footer_row_dis_amt').value = disAmt.toFixed(2);
    document.getElementById('footer_row_scm_amt').value = scmAmt.toFixed(2);
    document.getElementById('footer_row_tax_amt').value = taxAmt.toFixed(2);
    document.getElementById('footer_row_net_amt').value = (ntAmt + taxAmt).toFixed(2);
}

function removeRow(idx) { document.getElementById(`row-${idx}`)?.remove(); calcTotals(); }
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
        const d = parseFloat(row.querySelector('.row-dis-amt').value) || 0;
        const s = parseFloat(row.querySelector('.row-scm-amt').value) || 0;
        const t = parseFloat(row.querySelector('.row-tax-amt').value) || 0;
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

function saveTransaction() {
    if (!document.querySelectorAll('#itemsTableBody tr').length) { alert('Add at least one item'); return; }
    const formData = new FormData(document.getElementById('bsiForm'));
    fetch('{{ route("admin.breakage-supplier.store") }}', {
        method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(data => {
        if (data.success) { alert('Saved successfully'); window.location.href = '{{ route("admin.breakage-supplier.issued-index") }}'; }
        else alert('Error: ' + data.message);
    }).catch(() => alert('Error saving transaction'));
}

function cancelTransaction() { if (confirm('Cancel? Unsaved data will be lost.')) window.location.href = '{{ route("admin.breakage-supplier.issued-index") }}'; }
</script>
@endsection
