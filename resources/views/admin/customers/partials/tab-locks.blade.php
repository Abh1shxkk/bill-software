<div class="card shadow-sm">
    <div class="card-body">
        <h6 class="fw-bold mb-3 text-primary">Credit & Outstanding Limits</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="row g-3">
                    <div class="col-8">
                        <label class="form-label fw-semibold">Maximum O/S Amount</label>
                        <input type="number" step="0.01" class="form-control" name="max_os_amount" value="{{ old('max_os_amount', '0.00') }}" placeholder="0.00">
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">Max Limit On</label>
                        <select class="form-select" name="max_limit_on">
                            <option value="D">Due</option>
                            <option value="L">Ledger</option>
                        </select>
                    </div>
                    <div class="col-8">
                        <label class="form-label fw-semibold">Maximum Inv. Amount</label>
                        <input type="number" step="0.01" class="form-control" name="max_inv_amount" value="{{ old('max_inv_amount', '0.00') }}" placeholder="0.00">
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">Max No. O/S Inv.</label>
                        <input type="number" class="form-control" name="max_no_os_inv" value="{{ old('max_no_os_inv', '0') }}">
                    </div>
                    <div class="col-8">
                        <label class="form-label fw-semibold">Follow Conditions Strictly</label>
                        <select class="form-select" name="follow_conditions_strictly">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">Credit Days Lock</label>
                        <input type="number" class="form-control" name="credit_limit_days_lock" value="{{ old('credit_limit_days_lock', '0') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Open Lock Once</label>
                        <select class="form-select" name="open_lock_once">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border rounded p-3" style="background-color: #faf5ff;">
                    <h6 class="fw-bold mb-3" style="color: #a855f7;">Expiry Locks</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">A(mount) / P(ercentage)</label>
                            <select class="form-select" name="expiry_lock_type">
                                <option value="A">Amount</option>
                                <option value="P">Percentage</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Value</label>
                            <input type="number" step="0.01" class="form-control" name="expiry_lock_value" value="{{ old('expiry_lock_value', '0.00') }}" placeholder="0.00">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">No. of Expiries per Month</label>
                            <input type="number" class="form-control" name="no_of_expiries_per_month" value="{{ old('no_of_expiries_per_month', '0') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <h6 class="fw-bold mb-3 text-primary">Additional License Information</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">TAN No.</label>
                        <input type="text" class="form-control" name="tan_number" value="{{ old('tan_number') }}" placeholder="TAN number">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">MSME License</label>
                        <input type="text" class="form-control" name="msme_license" value="{{ old('msme_license') }}" placeholder="MSME license number">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>