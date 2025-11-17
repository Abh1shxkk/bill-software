@extends('layouts.admin')

@section('title', 'Edit Batch')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-pencil me-2"></i> Edit Batch</h4>
        <div class="text-muted small">Update batch information</div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
    <form id="batchEditForm" method="POST" action="{{ route('admin.batches.update', $batch->id ?? $purchaseItem->id) }}">
        @csrf
        @method('PUT')
        
        <!-- Summary Quantities Sectionnn -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fifo Qty.</label>
                        <input type="text" class="form-control" value="{{ number_format($fifoQty, 0) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mst.Qty.</label>
                        <input type="text" class="form-control" value="{{ number_format($mstQty, 0) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Actual</label>
                        <input type="text" class="form-control" value="{{ number_format($actualQty, 0) }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice and Supplier Information -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Inv.No.</label>
                        <input type="text" class="form-control" value="{{ ($batch->transaction ?? $purchaseItem->transaction)->bill_no ?? '---' }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="text" class="form-control" value="{{ ($batch->transaction ?? $purchaseItem->transaction)->bill_date ? ($batch->transaction ?? $purchaseItem->transaction)->bill_date->format('d-M-y') : '---' }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Supplier/Manufacturer</label>
                        <input type="text" class="form-control" value="{{ ($batch->transaction->supplier ?? $purchaseItem->transaction->supplier)->name ?? '---' }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Batch Code</label>
                        <input type="text" class="form-control" value="{{ ($batch->transaction ?? $purchaseItem->transaction)->id ?? '---' }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selected Batch Details -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="mb-3">Batch Details</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Batch <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="batch_no" id="batch_no" value="{{ $batch->batch_no ?? $purchaseItem->batch_no }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty. <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="qty" id="qty" value="{{ $batch->qty ?? $purchaseItem->qty }}" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">T.Qty.</label>
                        <input type="text" class="form-control" value="{{ number_format($totalQty, 0) }}" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">BC</label>
                        <select class="form-select" name="bc" id="bc">
                            <option value="Y" {{ ($batch->bc ?? 'N') == 'Y' ? 'selected' : '' }}>Y</option>
                            <option value="N" {{ ($batch->bc ?? 'N') == 'N' ? 'selected' : '' }}>N</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">DATE</label>
                        <input type="date" class="form-control" name="bill_date" id="bill_date" value="{{ ($batch->transaction ?? $purchaseItem->transaction)->bill_date ? ($batch->transaction ?? $purchaseItem->transaction)->bill_date->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Exp</label>
                        <input type="text" class="form-control" name="expiry_date" id="expiry_date" value="{{ ($batch->expiry_date ?? $purchaseItem->expiry_date) ? ($batch->expiry_date ?? $purchaseItem->expiry_date)->format('m/Y') : '' }}" placeholder="MM/YYYY" maxlength="7" inputmode="numeric">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mfg</label>
                        <input type="text" class="form-control" name="manufacturing_date" id="manufacturing_date" value="{{ ($batch->manufacturing_date ?? null) ? $batch->manufacturing_date->format('m/Y') : '' }}" placeholder="MM/YYYY" maxlength="7" inputmode="numeric">
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing and Financial Details -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="mb-3">Pricing Details</h6>
                
                <!-- Row 1: Sale Rate, P.Rate, MRP, WS.Rate, Spl.Rate -->
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Sale Rate</label>
                        <input type="number" class="form-control" name="s_rate" id="sale_rate" value="{{ $batch->s_rate ?? $purchaseItem->s_rate ?? 0 }}" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">P.Rate <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="pur_rate" id="pur_rate" value="{{ $batch->pur_rate ?? $purchaseItem->pur_rate }}" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">S.C.(Rs)</label>
                        <input type="number" class="form-control" name="sc_amount" id="sc_amount" value="{{ $batch->sc_amount ?? 0 }}" step="0.01">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sale Scheme</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="sale_scheme_plus" id="sale_scheme_plus" 
                                   value="{{ $batch->sale_scheme ? explode('+', $batch->sale_scheme)[0] : '0' }}" 
                                   style="max-width: 60px;" placeholder="0">
                            <span class="input-group-text">+</span>
                            <input type="text" class="form-control" name="sale_scheme_minus" id="sale_scheme_minus" 
                                   value="{{ $batch->sale_scheme && count(explode('+', $batch->sale_scheme)) > 1 ? explode('+', $batch->sale_scheme)[1] : '0' }}" 
                                   style="max-width: 60px;" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Pur.Dis</label>
                        <input type="number" class="form-control" name="dis_percent" id="dis_percent" value="{{ $batch->dis_percent ?? $purchaseItem->dis_percent ?? 0 }}" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Unit</label>
                        <input type="text" class="form-control" value="{{ $batch->unit ?? $purchaseItem->unit ?? '1' }}" readonly>
                    </div>
                </div>
                
                <!-- Row 2: MRP, WS.Rate, Spl.Rate, Inc, N.Rate -->
                <div class="row g-3 mt-2">
                    <div class="col-md-2">
                        <label class="form-label">MRP</label>
                        <input type="number" class="form-control" name="mrp" id="mrp" value="{{ $batch->mrp ?? $purchaseItem->mrp }}" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">W.S.Rate</label>
                        <input type="number" class="form-control" name="ws_rate" id="ws_rate" value="{{ $batch->ws_rate ?? $purchaseItem->ws_rate ?? 0 }}" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Spl.Rate</label>
                        <input type="number" class="form-control" name="spl_rate" id="spl_rate" value="{{ $batch->spl_rate ?? $purchaseItem->spl_rate ?? 0 }}" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Inc.</label>
                        <select class="form-select" name="inc" id="inc">
                            <option value="Y" {{ ($batch->inc ?? 'Y') == 'Y' ? 'selected' : '' }}>Y</option>
                            <option value="N" {{ ($batch->inc ?? 'Y') == 'N' ? 'selected' : '' }}>N</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">N.Rate</label>
                        <input type="number" class="form-control" name="n_rate" id="n_rate" value="{{ $batch->n_rate ?? 0 }}" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Margin %</label>
                        @php
                            $sRate = $batch->s_rate ?? $purchaseItem->s_rate ?? 0;
                            $cost = $batch->cost ?? $purchaseItem->cost ?? 0;
                            $margin = $sRate > 0 ? (($sRate - $cost) / $sRate) * 100 : 0;
                        @endphp
                        <input type="text" class="form-control" id="margin_percent" value="{{ number_format($margin, 2) }}" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">H/B/E</label>
                        <select class="form-select" name="hold_breakage_expiry" id="hold_breakage_expiry">
                            <option value="" {{ ($batch->hold_breakage_expiry ?? '') == '' ? 'selected' : '' }}>-</option>
                            <option value="H" {{ ($batch->hold_breakage_expiry ?? '') == 'H' ? 'selected' : '' }}>H</option>
                            <option value="B" {{ ($batch->hold_breakage_expiry ?? '') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="E" {{ ($batch->hold_breakage_expiry ?? '') == 'E' ? 'selected' : '' }}>E</option>
                        </select>
                    </div>
                </div>
                
                <!-- Row 3: GST Rate, GST PTS, Cost, Cost WFQ, Rate Diff -->
                <div class="row g-3 mt-2">
                    <div class="col-md-2">
                        <label class="form-label text-danger">GST Rate:</label>
                        @php
                            $gstRate = ($batch->cgst_percent ?? 0) + ($batch->sgst_percent ?? 0);
                        @endphp
                        <input type="text" class="form-control text-danger fw-bold" value="{{ number_format($gstRate, 2) }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-danger">GST PTS:</label>
                        <input type="number" class="form-control text-danger fw-bold" name="gst_pts" id="gst_pts" 
                               value="{{ $batch->gst_pts ?? 0 }}" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cost:</label>
                        <input type="number" class="form-control" name="cost" id="cost" value="{{ $batch->cost ?? $purchaseItem->cost ?? 0 }}" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cost WFQ:</label>
                        <input type="number" class="form-control" name="cost_wfq" id="cost_wfq" value="{{ $batch->cost_wfq ?? 0 }}" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rate Diff.:</label>
                        <input type="text" class="form-control" id="rate_diff" value="{{ $batch->rate_diff ?? 0 }}" readonly>
                        <input type="hidden" name="rate_diff" id="rate_diff_hidden" value="{{ $batch->rate_diff ?? 0 }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Save</button>
            <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-danger"><i class="bi bi-x-circle me-2"></i>Cancel</a>
        </div>
    </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('batchEditForm');
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Convert expiry_date from MM/YYYY to YYYY-MM-DD format
        const expiryDate = document.getElementById('expiry_date').value;
        if (expiryDate && expiryDate.includes('/')) {
            const [month, year] = expiryDate.split('/');
            if (month && year && year.length === 4) {
                const fullDate = `${year}-${month.padStart(2, '0')}-01`;
                formData.set('expiry_date', fullDate);
            } else {
                formData.set('expiry_date', '');
            }
        } else if (expiryDate) {
            // If already in YYYY-MM-DD format, use as is
            formData.set('expiry_date', expiryDate);
        } else {
            formData.set('expiry_date', '');
        }
        
        // Convert manufacturing_date from MM/YYYY to YYYY-MM-DD format
        const mfgDate = document.getElementById('manufacturing_date').value;
        if (mfgDate && mfgDate.includes('/')) {
            const [month, year] = mfgDate.split('/');
            if (month && year && year.length === 4) {
                const fullDate = `${year}-${month.padStart(2, '0')}-01`;
                formData.set('manufacturing_date', fullDate);
            } else {
                formData.set('manufacturing_date', '');
            }
        } else if (mfgDate) {
            formData.set('manufacturing_date', mfgDate);
        } else {
            formData.set('manufacturing_date', '');
        }
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Server Error:', text);
                    throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                // Redirect back to the previous page (either Available Batches or All Batches)
                if (document.referrer && document.referrer.includes('/batches')) {
                    window.location.href = document.referrer;
                } else {
                    window.location.href = '{{ route("admin.batches.index") }}';
                }
            } else {
                let errorMsg = data.message || 'Error updating batch';
                if (data.errors) {
                    errorMsg += '\n\nValidation Errors:\n';
                    Object.keys(data.errors).forEach(key => {
                        errorMsg += `- ${key}: ${data.errors[key].join(', ')}\n`;
                    });
                }
                alert('❌ ' + errorMsg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ An error occurred while updating the batch.\n\nError: ' + error.message + '\n\nCheck browser console for details.');
        });
    });
    
    // Format date input (MM/YYYY) with validation
    function formatMonthYearInput(input) {
        if (!input) return;
        
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Auto-format as MM/YYYY
            if (value.length >= 2) {
                let month = value.substring(0, 2);
                let year = value.substring(2, 6);
                
                // Validate month (01-12)
                if (parseInt(month) > 12) {
                    month = '12';
                }
                if (parseInt(month) < 1 && month.length === 2) {
                    month = '01';
                }
                
                value = month + (year ? '/' + year : '');
            }
            
            e.target.value = value;
        });
        
        // Validate on blur
        input.addEventListener('blur', function(e) {
            let value = e.target.value;
            if (value && value.includes('/')) {
                let [month, year] = value.split('/');
                
                // Validate month
                if (month && (parseInt(month) < 1 || parseInt(month) > 12)) {
                    alert('Invalid month! Please enter a month between 01 and 12.');
                    e.target.value = '';
                    return;
                }
                
                // Validate year (should be 4 digits)
                if (year && year.length !== 4) {
                    alert('Please enter a complete 4-digit year (e.g., 2025).');
                    e.target.value = '';
                    return;
                }
            }
        });
    }
    
    // Apply formatting to expiry and manufacturing date inputs
    const expiryInput = document.getElementById('expiry_date');
    const mfgInput = document.getElementById('manufacturing_date');
    
    formatMonthYearInput(expiryInput);
    formatMonthYearInput(mfgInput);
    
    // Auto-calculate Margin %
    function calculateMargin() {
        const sRate = parseFloat(document.getElementById('sale_rate').value) || 0;
        const cost = parseFloat(document.getElementById('cost').value) || 0;
        
        if (sRate > 0) {
            const margin = ((sRate - cost) / sRate) * 100;
            document.getElementById('margin_percent').value = margin.toFixed(2);
        } else {
            document.getElementById('margin_percent').value = '0.00';
        }
    }
    
    // Auto-calculate Rate Diff
    function calculateRateDiff() {
        const sRate = parseFloat(document.getElementById('sale_rate').value) || 0;
        const purRate = parseFloat(document.getElementById('pur_rate').value) || 0;
        const rateDiff = sRate - purRate;
        document.getElementById('rate_diff').value = rateDiff.toFixed(2);
        document.getElementById('rate_diff_hidden').value = rateDiff.toFixed(2);
    }
    
    // Listen to changes on s_rate and cost fields
    const sRateInput = document.getElementById('sale_rate');
    const costInput = document.getElementById('cost');
    const purRateInput = document.getElementById('pur_rate');
    
    if (sRateInput) {
        sRateInput.addEventListener('input', function() {
            calculateMargin();
            calculateRateDiff();
        });
    }
    
    if (costInput) {
        costInput.addEventListener('input', calculateMargin);
    }
    
    if (purRateInput) {
        purRateInput.addEventListener('input', calculateRateDiff);
    }
});
</script>
@endpush

