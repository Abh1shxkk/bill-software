<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-4">
            <!-- LEFT COLUMN -->
            <div class="col-md-6">
                <h6 class="fw-bold mb-3 text-primary">Banking & Pricing</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Bank</label>
                        <input type="text" class="form-control" name="bank" value="{{ old('bank') }}" placeholder="Bank name">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Branch</label>
                        <input type="text" class="form-control" name="branch" value="{{ old('branch') }}" placeholder="Branch name">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Closed On</label>
                        <input type="date" class="form-control" name="closed_on" value="{{ old('closed_on') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Credit Limit</label>
                        <input type="number" step="0.01" class="form-control" name="credit_limit" value="{{ old('credit_limit', '0') }}" placeholder="0.00">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Sale Rate Type</label>
                        <small class="d-block text-muted mb-2">1.Sale / 2.W.Sale / 3.Spl.Rate / 4.Pur.Rate / 5.Cost / 6.MRP / 7.T.Rate / 8.Cost W/O F.Qty.</small>
                        <div class="row g-2">
                            <div class="col-4">
                                <select class="form-select" name="sale_rate_type">
                                    <option value="1">1 - Sale</option>
                                    <option value="2">2 - W.Sale</option>
                                    <option value="3">3 - Spl. Rate</option>
                                    <option value="4">4 - Pur. Rate</option>
                                    <option value="5">5 - Cost</option>
                                    <option value="6">6 - MRP</option>
                                    <option value="7">7 - T.Rate</option>
                                    <option value="8">8 - Cost W/O F.Qty.</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label small">Add %</label>
                            </div>
                            <div class="col-4">
                                <input type="number" step="0.01" class="form-control" name="add_percent" value="{{ old('add_percent', '0') }}" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tax on Br./Expiry</label>
                        <select class="form-select" name="tax_on_br_expiry">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Expiry CN On</label>
                        <select class="form-select" name="expiry_on">
                            <option value="M">MRP</option>
                            <option value="S">Sale Rate</option>
                            <option value="P">Pur. Rate</option>
                            <option value="W">WS Rate</option>
                            <option value="L">Spl. Rate</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Dis. After Scheme</label>
                        <select class="form-select" name="dis_after_scheme">
                            <option value="Y">Yes</option>
                            <option value="N">No</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Expiry RN On</label>
                        <select class="form-select" name="expiry_rn_on">
                            <option value="M">MRP</option>
                            <option value="S">Sale Rate</option>
                            <option value="P">Pur. Rate</option>
                            <option value="W">WS Rate</option>
                        </select>
                    </div>

                    <div class="col-6">
                        <label class="form-label fw-semibold">Dis. On Excise</label>
                        <select class="form-select" name="dis_on_excise">
                            <option value="Y">Yes</option>
                            <option value="N">No</option>
                            <option value="X">X</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Sale Pur. Status</label>
                        <select class="form-select" name="sale_pur_status">
                            <option value="S">Sale</option>
                            <option value="P">Purchase</option>
                            <option value="B">Both</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Scheme Type</label>
                        <select class="form-select" name="scm_type">
                            <option value="F">F</option>
                            <option value="H">H</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Net Rate</label>
                        <select class="form-select" name="net_rate">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">No. of Items In Bill</label>
                        <input type="number" class="form-control" name="no_of_items_in_bill" value="{{ old('no_of_items_in_bill', '0') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Invoice Print Order</label>
                        <small class="d-block text-muted mb-2">0.Default / 1.Company / 2.User Defined / 3.Name</small>
                        <input type="text" class="form-control" name="invoice_print_order" value="{{ old('invoice_print_order') }}" placeholder="Enter order type">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">SR Replacement</label>
                        <select class="form-select" name="sr_replacement">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Cash Sale</label>
                        <select class="form-select" name="cash_sale">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Invoice Format</label>
                        <input type="number" class="form-control" name="invoice_format" value="{{ old('invoice_format', '0') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Fixed Discount</label>
                        <input type="number" step="0.01" class="form-control" name="fixed_discount" value="{{ old('fixed_discount', '0') }}" placeholder="0.00">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold mb-2">Brk./Expiry Dis. On Item With GST</label>
                        <div class="row g-2">
                            <div class="col"><label class="form-label small">5%</label><input type="number" step="0.01" class="form-control" name="gst_5_percent" value="{{ old('gst_5_percent', '0') }}"></div>
                            <div class="col"><label class="form-label small">12%</label><input type="number" step="0.01" class="form-control" name="gst_12_percent" value="{{ old('gst_12_percent', '0') }}"></div>
                            <div class="col"><label class="form-label small">18%</label><input type="number" step="0.01" class="form-control" name="gst_18_percent" value="{{ old('gst_18_percent', '0') }}"></div>
                            <div class="col"><label class="form-label small">28%</label><input type="number" step="0.01" class="form-control" name="gst_28_percent" value="{{ old('gst_28_percent', '0') }}"></div>
                            <div class="col"><label class="form-label small">0%</label><input type="number" step="0.01" class="form-control" name="gst_0_percent" value="{{ old('gst_0_percent', '0') }}"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-secondary w-100">Update Brk./Expiry Dis. to All Customer</button>
                    </div>
                </div>
            </div>
            <!-- RIGHT COLUMN -->
            <div class="col-md-6">
                <h6 class="fw-bold mb-3 text-primary">Tax & Additional Settings</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Reference</label>
                        <input type="text" class="form-control" name="ref" value="{{ old('ref') }}" placeholder="Reference details">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">TDS (Tax Deducted at Source)</label>
                        <select class="form-select" name="tds">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Add Charges with GST</label>
                        <select class="form-select" name="add_charges_with_gst">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">TCS Applicable</label>
                        <select class="form-select" name="tcs_applicable">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                            <option value="#">#</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">BE Incl.</label>
                        <select class="form-select" name="be_incl">
                            <option value="N">No</option>
                            <option value="Y">Yes</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Brk./Expiry Msg. in Sale</label>
                        <select class="form-select" name="brk_expiry_msg_in_sale">
                            <option value="Y">Yes</option>
                            <option value="N">No</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Series Lock</label>
                        <input type="text" class="form-control" name="series_lock" value="{{ old('series_lock') }}" placeholder="Series">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Branch Trf.</label>
                        <input type="text" class="form-control" name="branch_trf" value="{{ old('branch_trf') }}" placeholder="Branch">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Transfer Account</label>
                        <input type="text" class="form-control" name="trnf_account" value="{{ old('trnf_account') }}" placeholder="Account details">
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="fw-bold mb-3">eWay Details</h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Transport</label>
                                <select class="form-select" name="transport_code" id="transport_code">
                                    <option value="">-- Select Transport --</option>
                                    @foreach($transports ?? [] as $transport)
                                        <option value="{{ $transport->id }}" {{ old('transport_code', $customer->transport_code ?? '') == $transport->id ? 'selected' : '' }}>
                                            {{ $transport->alter_code ? $transport->alter_code . ' - ' : '' }}{{ $transport->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Distance (KM)</label>
                                <input type="number" class="form-control" name="distance" value="{{ old('distance') }}" placeholder="Distance in kilometers">
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Expiry - R(epl) / C(redit Note)</label>
                        <select class="form-select" name="expiry_repl_credit">
                            <option value="C">Credit Note</option>
                            <option value="R">Replacement</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>