@extends('layouts.admin')

@section('title', 'Purchase Return Br.Expiry Adjustment - Transaction')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i> Purchase Return Br.Expiry Adjustment</h5>
            <div class="text-muted small">Create new adjustment transaction</div>
        </div>
        <a href="{{ route('admin.breakage-supplier.received-modification') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list me-1"></i> View All
        </a>
    </div>

    <form id="receivedForm" method="POST" autocomplete="off">
        @csrf
        <!-- Header Section -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-2">
                         <div class="mb-2">
                            <label class="form-label small fw-bold mb-1">Date</label>
                            <input type="date" id="transaction_date" name="transaction_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold mb-1">Supplier</label>
                             <input type="text" id="supplier_name" name="supplier_name" class="form-control form-control-sm" placeholder="Supplier Name" list="supplierList">
                             <datalist id="supplierList">
                                 @foreach($suppliers ?? [] as $s)
                                    <option value="{{ $s->name }}">{{ $s->name }}</option>
                                 @endforeach
                             </datalist>
                        </div>
                    </div>

                    <div class="col-md-10">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold mb-1">Party Trn No.</label>
                                        <input type="text" id="party_trn_no" name="party_trn_no" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Party Date</label>
                                        <input type="date" id="party_date" name="party_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Claim [Y/N]</label>
                                        <select id="claim_flag" name="claim_flag" class="form-select form-select-sm">
                                            <option value="Y">Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Claim Amt</label>
                                        <input type="number" id="claim_amount" name="claim_amount" class="form-control form-control-sm text-end" value="0.00">
                                    </div>
                                     <div class="col-md-3 d-flex align-items-center pt-3">
                                        <div class="form-check">
                                            <input type="checkbox" id="received_debit_note" name="received_debit_note" class="form-check-input">
                                            <label class="form-check-label small fw-bold" for="received_debit_note">Debit Note Processing</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">O/S Amount</label>
                                        <input type="number" id="os_amount" name="os_amount" class="form-control form-control-sm text-end" value="0.00" readonly>
                                    </div>
                                     <div class="col-md-10">
                                        <label class="form-label small fw-bold mb-1">Remarks</label>
                                        <input type="text" id="remarks" name="remarks" class="form-control form-control-sm">
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
                                <th style="width:60px;" class="text-end">Free</th>
                                <th style="width:80px;" class="text-end">Rate</th>
                                <th style="width:60px;" class="text-end">Dis%</th>
                                <th style="width:60px;" class="text-end">Tax%</th>
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

        <!-- Comprehensive Footer (Consistent 3-Section) -->
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Item Details (Blue Panel) -->
            <div class="card border-0 rounded-0 mb-1" style="background-color: #dbeafe; border-top: 1px solid #bfdbfe !important;">
                <div class="card-body p-1">
                    <div class="row g-1 align-items-center">
                        <div class="col-md-4 d-flex gap-2">
                            <div class="input-group input-group-sm flex-nowrap" style="width: 140px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Comp:</span>
                                <input type="text" id="footer_company" class="form-control px-1 border-start-0" readonly tabindex="-1">
                            </div>
                             <div class="input-group input-group-sm flex-nowrap" style="width: 80px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Unit</span>
                                <input type="text" id="footer_unit" class="form-control px-1 border-start-0" readonly tabindex="-1">
                            </div>
                        </div>
                        <div class="col-md-8 d-flex justify-content-end gap-1 flex-wrap">
                            <div class="d-flex align-items-center gap-1">
                                <label class="small fw-bold mb-0">N.T Amt.</label>
                                <input type="number" id="footer_row_nt_amt" class="form-control form-control-sm text-end" style="width: 80px;" readonly tabindex="-1">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="small fw-bold mb-0">DIS. Amt.</label>
                                <input type="number" id="footer_row_dis_amt" class="form-control form-control-sm text-end" style="width: 70px;" readonly tabindex="-1">
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="small fw-bold mb-0">Tax Amt.</label>
                                <input type="number" id="footer_row_tax_amt" class="form-control form-control-sm text-end" style="width: 70px;" readonly tabindex="-1">
                            </div>
                            <div class="vr mx-1"></div>
                            <div class="d-flex align-items-center gap-1">
                                <label class="small fw-bold mb-0">Net Amt.</label>
                                <input type="number" id="footer_row_net_amt" class="form-control form-control-sm text-end fw-bold bg-white" style="width: 90px;" readonly tabindex="-1">
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
                                <span class="input-group-text px-1 fw-bold bg-white border-end-0">Gross</span>
                                <input type="number" id="total_gross_amt" name="gross_amt" class="form-control text-end px-1 bg-white border-start-0" readonly tabindex="-1">
                            </div>
                            <div class="input-group input-group-sm flex-nowrap" style="width: 120px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Total Tax</span>
                                <input type="number" id="total_tax_amt" name="total_tax" class="form-control text-end px-1 bg-white border-start-0" readonly tabindex="-1">
                            </div>
                             <div class="input-group input-group-sm flex-nowrap" style="width: 120px;">
                                <span class="input-group-text px-1 bg-white border-end-0">Round Off</span>
                                <input type="number" id="round_off" name="round_off" class="form-control text-end px-1 bg-white border-start-0" value="0.00" step="0.01" onchange="calcTotals()">
                            </div>
                             <div class="input-group input-group-sm flex-nowrap" style="width: 180px;">
                                <span class="input-group-text px-1 fw-bold bg-danger text-white border-danger">NET AMT</span>
                                <input type="number" id="total_net_amt" name="amount" class="form-control text-end px-1 fw-bold border-danger text-danger bg-white" style="font-size: 1.25em;" readonly tabindex="-1">
                            </div>
                        </div>
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

@push('styles')
<style>
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.02); cursor: pointer; }
    .row-selected { background-color: #e8f0fe !important; }
    .row-selected td { background-color: #e8f0fe !important; }
    .fixed-bottom-footer { position: fixed; bottom: 0; left: 250px; right: 0; z-index: 1030; background: white; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); }
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

<script>
let rowIndex = 0, allItems = [], selectedRowIndex = null;
document.addEventListener('DOMContentLoaded', function() { loadItems(); });

function loadItems() { fetch('{{ route("admin.breakage-supplier.get-items") }}').then(r => r.json()).then(data => { allItems = data || []; }); }

function showAddItemModal() {
     const html = `
        <div class="modal-backdrop-custom show" id="itemModalBackdrop" onclick="closeItemModal()"></div>
        <div class="custom-modal show" id="itemModal">
            <div class="custom-modal-header"><h5 class="custom-modal-title m-0 fs-6">Select Item</h5><button type="button" class="btn-close" onclick="closeItemModal()"></button></div>
            <div class="custom-modal-body">
                <input type="text" id="itemSearchInput" class="form-control mb-3" placeholder="Search..." onkeyup="filterItemsList()">
                <div class="table-responsive"><table class="table table-hover table-bordered table-sm mb-0 small"><tbody id="itemsListBody"></tbody></table></div>
            </div>
            <div class="custom-modal-footer"><button type="button" class="btn btn-secondary btn-sm" onclick="closeItemModal()">Close</button></div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.getElementById('itemSearchInput').focus();
    renderItemsList();
}
function filterItemsList() { renderItemsList(document.getElementById('itemSearchInput').value); }
function renderItemsList(filter = '') {
    const filtered = filter ? allItems.filter(i => i.name.toLowerCase().includes(filter.toLowerCase())) : allItems;
    document.getElementById('itemsListBody').innerHTML = filtered.slice(0,50).map(i => `<tr style="cursor:pointer;" onclick="selectItem(${i.id})"><td>${i.id}</td><td>${i.name}</td><td>${i.packing||'-'}</td><td>${parseFloat(i.mrp||0).toFixed(2)}</td></tr>`).join('');
}
function selectItem(id) { const item = allItems.find(i => i.id === id); if (item) { addItemRow(item); closeItemModal(); } }
function closeItemModal() { document.getElementById('itemModal')?.remove(); document.getElementById('itemModalBackdrop')?.remove(); }

function addItemRow(item) {
    const idx = rowIndex++;
    const row = document.createElement('tr');
    row.id = `row-${idx}`; row.dataset.rowIndex = idx;
    row.onclick = function(e) { if (!['BUTTON','INPUT','SELECT'].includes(e.target.tagName)) selectRow(idx); };
    
    // Default dummy data if not present
    const batch = item.batch_no || '-';
    // const expiry = item.expiry || '-';
    const mrp = item.mrp || 0;
    const rate = item.p_rate || 0;
    
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][code]" value="${item.id}" readonly></td>
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][name]" value="${item.name}" readonly></td>
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][batch]" value="${batch}"></td>
        <td><input type="text" class="form-control form-control-sm border-0 bg-transparent py-0" name="items[${idx}][expiry]" value=""></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][qty]" value="1" min="0" onchange="calcRow(${idx})" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][free_qty]" value="0" min="0" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][rate]" value="${rate}" step="0.01" onchange="calcRow(${idx})" style="width:80px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][dis_percent]" value="0" step="0.01" onchange="calcRow(${idx})" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end py-1" name="items[${idx}][tax_percent]" value="${(item.cgst||0)+(item.sgst||0)}" step="0.01" onchange="calcRow(${idx})" style="width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm text-end border-0 bg-transparent fw-bold py-0" name="items[${idx}][amount]" value="0.00" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger p-0" onclick="removeRow(${idx})"><i class="bi bi-trash"></i></button></td>
        <input type="hidden" name="items[${idx}][packing]" value="${item.packing||''}">
        <input type="hidden" name="items[${idx}][unit]" value="${item.unit||''}">
        <input type="hidden" name="items[${idx}][company_name]" value="${item.company_name||''}">
        <input type="hidden" name="items[${idx}][mrp]" value="${mrp}">
        <input type="hidden" name="items[${idx}][p_rate]" value="${item.p_rate||0}">
        <input type="hidden" name="items[${idx}][cgst_percent]" value="${item.cgst||0}">
        <input type="hidden" name="items[${idx}][sgst_percent]" value="${item.sgst||0}">
        <input type="hidden" name="items[${idx}][tax_amount]" class="row-tax-amt" value="0">
        <input type="hidden" name="items[${idx}][dis_amount]" class="row-dis-amt" value="0">
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
        ['footer_pack_1','footer_mrp','footer_p_rate','footer_company','footer_unit',
         'footer_cgst_percent','footer_sgst_percent','footer_tax_percent','footer_row_nt_amt',
         'footer_row_dis_amt','footer_row_tax_amt','footer_row_net_amt'
        ].forEach(id => { if(document.getElementById(id)) document.getElementById(id).value = ''; });
        return;
    }
    const get = n => row.querySelector(`input[name*="[${n}]"]`)?.value || '';
    
    document.getElementById('footer_pack_1').value = get('packing');
    document.getElementById('footer_mrp').value = get('mrp');
    document.getElementById('footer_p_rate').value = get('p_rate');
    
    document.getElementById('footer_company').value = get('company_name');
    document.getElementById('footer_unit').value = get('unit');

    const cgst = parseFloat(get('cgst_percent')||0);
    const sgst = parseFloat(get('sgst_percent')||0);
    document.getElementById('footer_cgst_percent').value = cgst;
    document.getElementById('footer_sgst_percent').value = sgst;
    document.getElementById('footer_tax_percent').value = (cgst+sgst).toFixed(2);
    
    const qty = parseFloat(get('qty')||0);
    const rate = parseFloat(get('rate')||0);
    const gross = qty * rate;
    const dis = parseFloat(row.querySelector('.row-dis-amt')?.value||0);
    const tax = parseFloat(row.querySelector('.row-tax-amt')?.value||0);
    
    document.getElementById('footer_row_nt_amt').value = (gross - dis).toFixed(2);
    document.getElementById('footer_row_dis_amt').value = dis.toFixed(2);
    document.getElementById('footer_row_tax_amt').value = tax.toFixed(2);
    document.getElementById('footer_row_net_amt').value = (gross - dis + tax).toFixed(2);
}

function calcRow(idx) {
    const row = document.getElementById(`row-${idx}`); if (!row) return;
    const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
    const rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
    const disP = parseFloat(row.querySelector('input[name*="[dis_percent]"]').value) || 0;
    const taxP = parseFloat(row.querySelector('input[name*="[tax_percent]"]').value) || 0;
    
    const gross = qty * rate;
    const disAmt = gross * disP / 100;
    const taxable = gross - disAmt;
    const taxAmt = taxable * taxP / 100;
    
    row.querySelector('.row-dis-amt').value = disAmt;
    row.querySelector('.row-tax-amt').value = taxAmt;
    row.querySelector('input[name*="[amount]"]').value = (taxable + taxAmt).toFixed(2);
    
    if (selectedRowIndex === idx) updateFooter(row);
    calcTotals();
}

function removeRow(idx) { document.getElementById(`row-${idx}`)?.remove(); calcTotals(); }
function deleteSelectedItem() { if (selectedRowIndex !== null) { removeRow(selectedRowIndex); selectedRowIndex = null; } else alert('Select a row first'); }

function calcTotals() {
    let grossAll = 0, taxAll = 0;
    document.querySelectorAll('#itemsTableBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
        const rate = parseFloat(row.querySelector('input[name*="[rate]"]').value) || 0;
        const dis = parseFloat(row.querySelector('.row-dis-amt')?.value) || 0;
        const tax = parseFloat(row.querySelector('.row-tax-amt')?.value) || 0;
        
        grossAll += (qty * rate) - dis;
        taxAll += tax;
    });
    
    const round = parseFloat(document.getElementById('round_off').value) || 0;
    
    document.getElementById('total_gross_amt').value = grossAll.toFixed(2);
    document.getElementById('total_tax_amt').value = taxAll.toFixed(2);
    document.getElementById('total_net_amt').value = (grossAll + taxAll + round).toFixed(2);
}

function saveTransaction() {
     if (!document.querySelectorAll('#itemsTableBody tr').length) { alert('Add at least one item'); return; }
     // Implement save logic (AJAX) here
     alert('Save Logic Placeholder - Ready for Implementation');
}
function cancelTransaction() { window.location.href = '{{ route("admin.breakage-supplier.received-modification") }}'; }
</script>
@endsection
