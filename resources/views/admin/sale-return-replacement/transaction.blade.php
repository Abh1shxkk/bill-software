@extends('layouts.admin')

@section('title', 'Sale Return Replacement Transaction')

@section('content')
<style>
    /* Compact form adjustments - matching Sale Module */
    .compact-form {
        font-size: 11px;
        padding: 8px;
        background: #f5f5f5;
    }
    
    .compact-form label {
        font-weight: 600;
        font-size: 11px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .compact-form input,
    .compact-form select {
        font-size: 11px;
        padding: 2px 6px;
        height: 26px;
    }
    
    .header-section {
        background: white;
        border: 1px solid #dee2e6;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 4px;
    }
    
    .header-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 6px;
    }
    
    .field-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .field-group label {
        font-weight: 600;
        font-size: 11px;
        margin-bottom: 0;
        white-space: nowrap;
    }
    
    .field-group input,
    .field-group select {
        font-size: 11px;
        padding: 2px 6px;
        height: 26px;
    }
    
    .inner-card {
        background: #e8f4f8;
        border: 1px solid #b8d4e0;
        padding: 8px;
        border-radius: 3px;
    }
    
    .table-compact {
        font-size: 10px;
        margin-bottom: 0;
    }
    
    .table-compact th,
    .table-compact td {
        padding: 4px;
        vertical-align: middle;
        height: 45px;
    }
    
    .table-compact th {
        background: #e9ecef;
        font-weight: 600;
        text-align: center;
        border: 1px solid #dee2e6;
        height: 40px;
    }
    
    .table-compact input {
        font-size: 10px;
        padding: 2px 4px;
        height: 22px;
        border: 1px solid #ced4da;
        width: 100%;
    }
    
    /* Table container - Shows exactly 6 rows + header */
    #itemsTableContainer {
        max-height: 310px !important;
    }
    
    .readonly-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-arrow-return-left me-2"></i> Sale Return Replacement Transaction</h4>
        <div class="text-muted small">Create new sale return replacement transaction</div>
    </div>
    <div>
        <a href="{{ route('admin.sale-return-replacement.index') }}" class="btn btn-primary">
            <i class="bi bi-receipt-cutoff me-1"></i> View All Transactions
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form id="saleReturnReplacementForm" method="POST" autocomplete="off" onsubmit="return false;">
            @csrf
            
            <!-- Header Section -->
            <div class="header-section">
                <!-- Row 1: Series, Date, Customer -->
                <div class="header-row">
                    <div class="field-group">
                        <label>Series:</label>
                        <input type="text" class="form-control readonly-field" value="RG" readonly style="width: 60px; text-align: center; font-weight: bold; color: red;">
                    </div>
                    
                    <div class="field-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="trn_date" id="trn_date" value="{{ date('Y-m-d') }}" style="width: 140px;" onchange="updateDayName()">
                        <input type="text" class="form-control readonly-field" id="dayName" value="{{ date('l') }}" readonly style="width: 90px;">
                    </div>
                    
                    <div class="field-group">
                        <label>Customer:</label>
                        <select class="form-control" name="customer_id" id="customerSelect" style="width: 250px;" autocomplete="off">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->code ?? '' }} - {{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Row 2: S.R.No, Inner Card -->
                <div class="d-flex gap-3">
                    <!-- Left Side - S.R.No -->
                    <div style="width: 250px;">
                        <div class="field-group mb-2">
                            <label style="width: 70px;">S.R.No.:</label>
                            <input type="text" class="form-control readonly-field" name="trn_no" id="trnNo" value="{{ $nextTrnNo }}" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-success" id="addRowBtn" style="width: 100%;">
                                <i class="bi bi-plus-circle"></i> Add Item Row
                            </button>
                        </div>
                    </div>
                    
                    <!-- Right Side - Inner Card -->
                    <div class="inner-card flex-grow-1">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Cash:</label>
                                    <select class="form-control" name="is_cash" id="is_cash" style="width: 60px;">
                                        <option value="N" selected>N</option>
                                        <option value="Y">Y</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Fixed Dis.:</label>
                                    <input type="number" class="form-control" name="fixed_discount" id="fixed_discount" step="0.01" style="width: 80px;" value="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mt-1">
                            <div class="col-md-12">
                                <div class="field-group">
                                    <label>Remarks:</label>
                                    <input type="text" class="form-control" name="remarks" id="remarks">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Items Table -->
            <div class="bg-white border rounded p-2 mb-2">
                <div class="table-responsive" style="overflow-y: auto;" id="itemsTableContainer">
                    <table class="table table-bordered table-compact">
                        <thead style="position: sticky; top: 0; background: #e9ecef; z-index: 10;">
                            <tr>
                                <th style="width: 50px;">Code</th>
                                <th style="width: 180px;">Item Name</th>
                                <th style="width: 65px;">Batch</th>
                                <th style="width: 55px;">Exp.</th>
                                <th style="width: 45px;">Qty.</th>
                                <th style="width: 45px;">F.Qty</th>
                                <th style="width: 65px;">Rate</th>
                                <th style="width: 45px;">Dis%</th>
                                <th style="width: 60px;">FTRate</th>
                                <th style="width: 70px;">Amount</th>
                                <th style="width: 50px;">Act</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <tr>
                                <td><input type="text" class="form-control item-code" name="items[0][item_code]"></td>
                                <td><input type="text" class="form-control item-name" name="items[0][item_name]"></td>
                                <td><input type="text" class="form-control" name="items[0][batch_no]"></td>
                                <td><input type="text" class="form-control" name="items[0][expiry_date]"></td>
                                <td><input type="number" step="any" class="form-control qty" name="items[0][qty]"></td>
                                <td><input type="number" step="any" class="form-control f-qty" name="items[0][free_qty]"></td>
                                <td><input type="number" step="any" class="form-control sale-rate" name="items[0][sale_rate]"></td>
                                <td><input type="number" step="any" class="form-control dis-percent" name="items[0][discount_percent]"></td>
                                <td><input type="number" step="any" class="form-control ft-rate" name="items[0][ft_rate]"></td>
                                <td><input type="number" step="any" class="form-control amount" name="items[0][amount]" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-x"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Add Row Button -->
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-success" id="addRowBtn2">
                        <i class="bi bi-plus-circle"></i> Add Row
                    </button>
                </div>
            </div>

            
            <!-- Calculation Section (matching Sale module structure) -->
            <div class="bg-white border rounded p-3 mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="d-flex align-items-start gap-3 border rounded p-2" style="font-size: 11px; background: #fafafa;">
                    <!-- Left Section -->
                    <div class="d-flex flex-column gap-2" style="min-width: 200px;">
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>SC %</strong></label>
                            <input type="number" class="form-control" id="sc_percent" name="sc_percent" step="0.01" style="width: 80px; height: 28px;" value="0">
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>Tax %</strong></label>
                            <input type="number" class="form-control" id="tax_percent" name="tax_percent" step="0.01" style="width: 80px; height: 28px;" value="0">
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0" style="min-width: 75px;"><strong>Excise</strong></label>
                            <input type="number" class="form-control readonly-field" id="excise" name="excise" readonly step="0.01" style="width: 80px; height: 28px;" value="0">
                        </div>
                    </div>
                    
                    <!-- Right Side -->
                    <div class="d-flex gap-3">
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0" style="min-width: 60px;"><strong>TSR</strong></label>
                                <input type="number" class="form-control readonly-field" id="tsr" name="tsr" readonly step="0.01" style="width: 80px; height: 28px;" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Section (matching Sale module - pink background) -->
            <div class="bg-white border rounded p-2 mb-2" style="background: #ffcccc;">
                <!-- Row 1: 7 fields -->
                <div class="d-flex align-items-center" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold; white-space: nowrap;">N.T.Amt.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="nt_amt" name="nt_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">SC</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="sc_amt" name="sc_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">F.T.Amt.</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="ft_amt" name="ft_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Dis.</label>
                        <input type="number" class="form-control form-control-sm text-end" id="dis_amt" name="dis_amt" step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.</label>
                        <input type="number" class="form-control form-control-sm text-end" id="scm_amt" name="scm_amt" step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Tax</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="tax_amt" name="tax_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Net</label>
                        <input type="number" class="form-control form-control-sm readonly-field text-end" id="net_amt" name="net_amt" readonly step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>
                
                <!-- Row 2: Only Scm.% -->
                <div class="d-flex align-items-center mt-2" style="font-size: 11px; gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <label class="mb-0" style="font-weight: bold;">Scm.%</label>
                        <input type="number" class="form-control form-control-sm text-end" id="scm_percent" name="scm_percent" step="0.01" style="width: 80px; height: 26px;" value="0.00">
                    </div>
                </div>
            </div>
            
            <!-- Detailed Info Section (matching Sale module - orange background) -->
            <div class="bg-white border rounded p-2 mb-2" style="background: #ffe6cc;">
                <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                    <!-- Row 1 -->
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Packing</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field" id="detailPacking" readonly value="" style="height: 22px; width: 60px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>N.T.Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailNtAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Scm. %</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScmPercent" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Sub.Tot.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailSubTot" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Comp</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field" id="detailCompany" readonly value="" style="height: 22px; width: 100px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Srlno</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="detailSrIno" readonly value="" style="height: 22px; width: 60px;"></td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Unit</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-center" id="detailUnit" readonly value="1" style="height: 22px; width: 60px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>SC Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Scm.Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailScmAmt" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Tax Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailTaxAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>MRP</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field" id="detailMrp" readonly value="" style="height: 22px; width: 100px;"></td>
                        <td colspan="2"></td>
                    </tr>
                    
                    <!-- Row 3 -->
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Cl. Qty</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field text-end" id="detailClQty" readonly value="" style="height: 22px; width: 60px; background: #add8e6;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Dis. Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailDisAmt" readonly value="0.00" style="height: 22px; width: 80px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Net Amt.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailNetAmt" readonly value="0.00" style="height: 22px; width: 70px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Vol.</strong></td>
                        <td style="padding: 3px;"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailVol" readonly value="0" style="height: 22px; width: 80px;"></td>
                        <td colspan="4"></td>
                    </tr>
                    
                    <!-- Row 4 -->
                    <tr>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>Lctn</strong></td>
                        <td style="padding: 3px;"><input type="text" class="form-control form-control-sm readonly-field" id="detailLctn" readonly value="" style="height: 22px; width: 60px;"></td>
                        <td style="padding: 3px; background: #ffe6cc;"><strong>HS Amt.</strong></td>
                        <td style="padding: 3px;" colspan="9"><input type="number" class="form-control form-control-sm readonly-field text-end" id="detailHsAmt" readonly value="0.00" style="height: 22px; width: 100px;"></td>
                    </tr>
                </table>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" id="saveBtn">
                    <i class="bi bi-save"></i> Save
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.reload()">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#customerSelect').select2({ width: '250px' });
    
    // Update Day Name
    window.updateDayName = function() {
        const date = new Date($('#trn_date').val());
        const options = { weekday: 'long' };
        $('#dayName').val(date.toLocaleDateString('en-US', options));
    };

    // Add Row functionality
    function addNewRow() {
        let rowCount = $('#itemsTableBody tr').length;
        let newRow = `<tr>
            <td><input type="text" class="form-control item-code" name="items[${rowCount}][item_code]"></td>
            <td><input type="text" class="form-control item-name" name="items[${rowCount}][item_name]"></td>
            <td><input type="text" class="form-control" name="items[${rowCount}][batch_no]"></td>
            <td><input type="text" class="form-control" name="items[${rowCount}][expiry_date]"></td>
            <td><input type="number" step="any" class="form-control qty" name="items[${rowCount}][qty]"></td>
            <td><input type="number" step="any" class="form-control f-qty" name="items[${rowCount}][free_qty]"></td>
            <td><input type="number" step="any" class="form-control sale-rate" name="items[${rowCount}][sale_rate]"></td>
            <td><input type="number" step="any" class="form-control dis-percent" name="items[${rowCount}][discount_percent]"></td>
            <td><input type="number" step="any" class="form-control ft-rate" name="items[${rowCount}][ft_rate]"></td>
            <td><input type="number" step="any" class="form-control amount" name="items[${rowCount}][amount]" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-x"></i></button></td>
        </tr>`;
        $('#itemsTableBody').append(newRow);
    }

    $('#addRowBtn, #addRowBtn2').click(function() {
        addNewRow();
    });

    // Remove Row
    $(document).on('click', '.remove-row', function() {
        if ($('#itemsTableBody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotals();
        }
    });

    // Calculate row amount and totals
    $(document).on('input', '.qty, .sale-rate, .dis-percent', function() {
        let row = $(this).closest('tr');
        let qty = parseFloat(row.find('.qty').val()) || 0;
        let rate = parseFloat(row.find('.sale-rate').val()) || 0;
        let dis = parseFloat(row.find('.dis-percent').val()) || 0;
        
        let gross = qty * rate;
        let disAmt = gross * (dis / 100);
        let net = gross - disAmt;
        
        row.find('.amount').val(net.toFixed(2));
        calculateTotals();
    });
    
    $('#sc_percent, #tax_percent, #dis_amt, #scm_amt').on('input', function() {
        calculateTotals();
    });

    function calculateTotals() {
        let totalAmount = 0;
        
        $('#itemsTableBody tr').each(function() {
            let row = $(this);
            let amount = parseFloat(row.find('.amount').val()) || 0;
            totalAmount += amount;
        });
        
        $('#nt_amt').val(totalAmount.toFixed(2));
        $('#ft_amt').val(totalAmount.toFixed(2));
        
        // Footer Calcs
        let scPercent = parseFloat($('#sc_percent').val()) || 0;
        let scAmt = totalAmount * (scPercent / 100);
        
        let taxPercent = parseFloat($('#tax_percent').val()) || 0;
        let taxAmt = totalAmount * (taxPercent / 100);
        
        let disAmt = parseFloat($('#dis_amt').val()) || 0;
        let scmAmt = parseFloat($('#scm_amt').val()) || 0;
        
        let net = totalAmount + scAmt + taxAmt - disAmt - scmAmt;
        
        $('#sc_amt').val(scAmt.toFixed(2));
        $('#tax_amt').val(taxAmt.toFixed(2));
        $('#net_amt').val(net.toFixed(2));
    }

    // Save Transaction
    $('#saveBtn').click(function() {
        $.ajax({
            url: "{{ route('admin.sale-return-replacement.store') }}",
            method: "POST",
            data: $('#saleReturnReplacementForm').serialize(),
            success: function(response) {
                if(response.success) {
                    alert(response.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error saving transaction');
            }
        });
    });
});
</script>
@endpush
@endsection
