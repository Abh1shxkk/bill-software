<?php $__env->startSection('title', 'Misc. Transaction Book'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow-sm" style="background-color: #f0f0f0;">
        <div class="card-body p-3">
            <form method="GET" id="filterForm" action="<?php echo e(route('admin.reports.misc-transaction.misc-transaction-book')); ?>">
                <div class="row">
                    <!-- Left Column - Transaction Types (Radio Buttons) -->
                    <div class="col-md-6">
                        <div class="row g-1 mb-1">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="sale" id="sale" <?php echo e(request('tran_type', 'sale') == 'sale' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="sale">Sale</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="purchase" id="purchase" <?php echo e(request('tran_type') == 'purchase' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="purchase">Purchase</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="repl_issued" id="repl_issued" <?php echo e(request('tran_type') == 'repl_issued' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="repl_issued">Repl. Issued</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="sale_return" id="sale_return" <?php echo e(request('tran_type') == 'sale_return' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="sale_return">Sale Return</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="repl_received" id="repl_received" <?php echo e(request('tran_type') == 'repl_received' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="repl_received">Repl. Received</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="sample_issued" id="sample_issued" <?php echo e(request('tran_type') == 'sample_issued' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="sample_issued">Sample Issued</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="brk_exp_frm_cust" id="brk_exp_frm_cust" <?php echo e(request('tran_type') == 'brk_exp_frm_cust' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="brk_exp_frm_cust">Brk/Exp Frm Cust.</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="stk_trsfr_outgoing" id="stk_trsfr_outgoing" <?php echo e(request('tran_type') == 'stk_trsfr_outgoing' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="stk_trsfr_outgoing">Stk Trsfr - Outgoing</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="sample_received" id="sample_received" <?php echo e(request('tran_type') == 'sample_received' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="sample_received">Sample Received</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="purchase_return" id="purchase_return" <?php echo e(request('tran_type') == 'purchase_return' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="purchase_return">Purchase Return</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="stk_trsfr_outgoing_ret" id="stk_trsfr_outgoing_ret" <?php echo e(request('tran_type') == 'stk_trsfr_outgoing_ret' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="stk_trsfr_outgoing_ret">Stk. Trsfr. - Outgoing Ret.</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="quotation" id="quotation" <?php echo e(request('tran_type') == 'quotation' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="quotation">Quotation</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="brk_exp_to_supp" id="brk_exp_to_supp" <?php echo e(request('tran_type') == 'brk_exp_to_supp' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="brk_exp_to_supp">Brk/Exp To Supp.</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="stk_trsfr_incoming" id="stk_trsfr_incoming" <?php echo e(request('tran_type') == 'stk_trsfr_incoming' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="stk_trsfr_incoming">Stk. Trsfr. - Incoming</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="gdn_brk_exp_adj" id="gdn_brk_exp_adj" <?php echo e(request('tran_type') == 'gdn_brk_exp_adj' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="gdn_brk_exp_adj">Gdn Brk Exp Adj.</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="sale_on_challan" id="sale_on_challan" <?php echo e(request('tran_type') == 'sale_on_challan' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="sale_on_challan">Sale On Challan</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="stk_trsfr_incoming_ret" id="stk_trsfr_incoming_ret" <?php echo e(request('tran_type') == 'stk_trsfr_incoming_ret' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="stk_trsfr_incoming_ret">Stk. Trsfr. - Incoming Ret.</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="claim_to_supplier" id="claim_to_supplier" <?php echo e(request('tran_type') == 'claim_to_supplier' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="claim_to_supplier">Claim To Supplier</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="purch_on_challan" id="purch_on_challan" <?php echo e(request('tran_type') == 'purch_on_challan' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="purch_on_challan">Purch. On Challan</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="sale_return_repl" id="sale_return_repl" <?php echo e(request('tran_type') == 'sale_return_repl' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="sale_return_repl">Sale Return. Repl.</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="debit_note" id="debit_note" <?php echo e(request('tran_type') == 'debit_note' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="debit_note">Debit Note</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <div class="d-flex align-items-center">
                                    <label class="fw-bold mb-0 me-2">Series :</label>
                                    <input type="text" name="series" class="form-control form-control-sm text-uppercase" value="<?php echo e(request('series', '00')); ?>" style="width: 50px;">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tran_type" value="credit_note" id="credit_note" <?php echo e(request('tran_type') == 'credit_note' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="credit_note">Credit Note</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Filters -->
                    <div class="col-md-6">
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto"><label class="fw-bold mb-0">From :</label></div>
                            <div class="col-auto"><input type="date" name="from_date" class="form-control form-control-sm" value="<?php echo e(request('from_date', date('Y-m-d'))); ?>" style="width: 130px;"></div>
                            <div class="col-auto"><label class="fw-bold mb-0">To :</label></div>
                            <div class="col-auto"><input type="date" name="to_date" class="form-control form-control-sm" value="<?php echo e(request('to_date', date('Y-m-d'))); ?>" style="width: 130px;"></div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto"><label class="fw-bold mb-0">Numbers From :</label></div>
                            <div class="col-auto"><input type="text" name="nos_from" class="form-control form-control-sm" value="<?php echo e(request('nos_from')); ?>" style="width: 80px;"></div>
                            <div class="col-auto"><label class="fw-bold mb-0">To :</label></div>
                            <div class="col-auto"><input type="text" name="nos_to" class="form-control form-control-sm" value="<?php echo e(request('nos_to')); ?>" style="width: 80px;"></div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-12"><label class="fw-bold mb-0">Customer</label></div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col">
                                <select name="customer_id" id="customer_id" class="form-select form-select-sm">
                                    <option value="">-- All Customers --</option>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($customer->id); ?>" <?php echo e(request('customer_id') == $customer->id ? 'selected' : ''); ?>><?php echo e($customer->code); ?> - <?php echo e($customer->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12 text-center">
                                <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm">Ok</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card mt-2">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" id="resultsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">DATE</th>
                            <th style="width: 100px;">TRN.NO.</th>
                            <th>PARTY NAME</th>
                            <th style="width: 100px;" class="text-end">AMOUNT</th>
                            <th style="width: 50px;" class="text-center">TAG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($reportData) && $reportData->count() > 0): ?>
                            <?php $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-id="<?php echo e($item->id ?? $index); ?>">
                                <td><?php echo e($item->date ? date('d-M-y', strtotime($item->date)) : ''); ?></td>
                                <td><?php echo e($item->trn_no ?? ''); ?></td>
                                <td><?php echo e($item->party_name ?? ''); ?></td>
                                <td class="text-end"><?php echo e(number_format($item->amount ?? 0, 2)); ?></td>
                                <td class="text-center tag-cell"><?php echo e($item->tag ?? ''); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <?php for($i = 0; $i < 10; $i++): ?>
                            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer with buttons -->
    <div class="card mt-2">
        <div class="card-body p-2 d-flex justify-content-between align-items-center">
            <div>
                <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="toSingle()">To Single</button>
                <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm me-1" onclick="toIndividual()">To Individual</button>
                <button type="button" class="btn btn-light border px-3 fw-bold shadow-sm" onclick="mailInvoice()">Mail Invoice</button>
            </div>
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">Print (F7)</button>
                <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-3" onclick="closeWindow()">Exit</button>
                <span class="fw-bold">Total : <span id="totalAmount">0.00</span></span>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function closeWindow() { window.location.href = '<?php echo e(route("admin.dashboard")); ?>'; }
function printReport() { window.open('<?php echo e(route("admin.reports.misc-transaction.misc-transaction-book")); ?>?print=1&' + $('#filterForm').serialize(), '_blank'); }
function toSingle() { alert('To Single functionality'); }
function toIndividual() { alert('To Individual functionality'); }
function mailInvoice() { alert('Mail Invoice functionality'); }

$(document).on('keydown', function(e) {
    if (e.key === 'F7') { e.preventDefault(); printReport(); }
});

$('#resultsTable tbody tr').on('click', function() {
    $('#resultsTable tbody tr').removeClass('table-active');
    $(this).addClass('table-active');
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.table th, .table td { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
.form-check-label { font-size: 0.8rem; }
.table-active { background-color: #cce5ff !important; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/reports/misc-transaction-report/misc-transaction-book.blade.php ENDPATH**/ ?>