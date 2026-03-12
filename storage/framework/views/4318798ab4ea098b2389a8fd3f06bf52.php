<?php $__env->startSection('title', 'Party Wise Sale'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-2" style="background-color: #ffc4d0;">
        <div class="card-body py-2 text-center">
            <h4 class="mb-0 text-primary fst-italic fw-bold" style="font-family: 'Times New Roman', serif;">PARTY WISE SALE</h4>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold small">Report Type:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_sale" value="1" <?php echo e(($reportType ?? '1') == '1' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_sale">1. Sale</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_return" value="2" <?php echo e(($reportType ?? '') == '2' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_return">2. Sale Return</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_dn" value="3" <?php echo e(($reportType ?? '') == '3' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_dn">3. Debit Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_cn" value="4" <?php echo e(($reportType ?? '') == '4' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_cn">4. Credit Note</label>
                    
                    <input type="radio" class="btn-check" name="report_type_radio" id="type_consolidated" value="5" <?php echo e(($reportType ?? '') == '5' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-primary" for="type_consolidated">5. Consolidated Sale</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Filters -->
    <div class="card shadow-sm mb-2" style="background-color: #f0f0f0;">
        <div class="card-body py-2">
            <form method="GET" id="filterForm" action="<?php echo e(route('admin.reports.sales.sales-book-party-wise')); ?>">
                <input type="hidden" name="report_type" id="hidden_report_type" value="<?php echo e($reportType ?? '1'); ?>">
                
                <div class="row g-2">
                    <!-- Row 1 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">From</span>
                            <input type="date" name="date_from" class="form-control" value="<?php echo e($dateFrom); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" name="date_to" class="form-control" value="<?php echo e($dateTo); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Series</span>
                            <select name="series" class="form-select">
                                <option value="">All</option>
                                <?php $__currentLoopData = $seriesList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s); ?>" <?php echo e(($series ?? '') == $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Selective</span>
                            <select name="selective" class="form-select text-uppercase">
                                <option value="Y" <?php echo e(($selective ?? 'Y') == 'Y' ? 'selected' : ''); ?>>Y</option>
                                <option value="N" <?php echo e(($selective ?? '') == 'N' ? 'selected' : ''); ?>>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Bill Wise</span>
                            <select name="bill_wise" class="form-select text-uppercase">
                                <option value="Y" <?php echo e(($billWise ?? 'Y') == 'Y' ? 'selected' : ''); ?>>Y</option>
                                <option value="N" <?php echo e(($billWise ?? '') == 'N' ? 'selected' : ''); ?>>N</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">T/R</span>
                            <select name="tax_retail" class="form-select text-uppercase">
                                <option value="">All</option>
                                <option value="T" <?php echo e(($taxRetail ?? '') == 'T' ? 'selected' : ''); ?>>T(ax)</option>
                                <option value="R" <?php echo e(($taxRetail ?? '') == 'R' ? 'selected' : ''); ?>>R(etail)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Tagged</span>
                            <select name="tagged_parties" class="form-select text-uppercase">
                                <option value="N" <?php echo e(($taggedParties ?? 'N') == 'N' ? 'selected' : ''); ?>>N</option>
                                <option value="Y" <?php echo e(($taggedParties ?? '') == 'Y' ? 'selected' : ''); ?>>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Remove Tags</span>
                            <select name="remove_tags" class="form-select text-uppercase">
                                <option value="N" <?php echo e(($removeTags ?? 'N') == 'N' ? 'selected' : ''); ?>>N</option>
                                <option value="Y" <?php echo e(($removeTags ?? '') == 'Y' ? 'selected' : ''); ?>>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Flag</span>
                            <input type="text" name="flag" class="form-control text-uppercase" value="<?php echo e($flag ?? ''); ?>" placeholder="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Party</span>
                            <select name="customer_id" class="form-select">
                                <option value="">All Parties</option>
                                <?php $__currentLoopData = $customers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer->id); ?>" <?php echo e(($customerId ?? '') == $customer->id ? 'selected' : ''); ?>>
                                        <?php echo e($customer->code); ?> - <?php echo e($customer->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Print Addr</span>
                            <select name="print_address" class="form-select text-uppercase">
                                <option value="N" <?php echo e(($printAddress ?? 'N') == 'N' ? 'selected' : ''); ?>>N</option>
                                <option value="Y" <?php echo e(($printAddress ?? '') == 'Y' ? 'selected' : ''); ?>>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Print S.Tax</span>
                            <select name="print_stax" class="form-select text-uppercase">
                                <option value="N" <?php echo e(($printStax ?? 'N') == 'N' ? 'selected' : ''); ?>>N</option>
                                <option value="Y" <?php echo e(($printStax ?? '') == 'Y' ? 'selected' : ''); ?>>Y</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sort By</span>
                            <select name="sort_by" class="form-select text-uppercase">
                                <option value="P" <?php echo e(($sortBy ?? 'P') == 'P' ? 'selected' : ''); ?>>P(arty)</option>
                                <option value="A" <?php echo e(($sortBy ?? '') == 'A' ? 'selected' : ''); ?>>A(mount)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">A/D</span>
                            <select name="asc_desc" class="form-select text-uppercase">
                                <option value="A" <?php echo e(($ascDesc ?? 'A') == 'A' ? 'selected' : ''); ?>>A(sc)</option>
                                <option value="D" <?php echo e(($ascDesc ?? '') == 'D' ? 'selected' : ''); ?>>D(esc)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Amt ></span>
                            <input type="number" name="amount_from" class="form-control" value="<?php echo e($amountFrom ?? 0); ?>" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Amt <</span>
                            <input type="number" name="amount_to" class="form-control" value="<?php echo e($amountTo ?? 0); ?>" placeholder="0">
                        </div>
                    </div>

                    <!-- Row 4 - Checkboxes -->
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center border rounded p-2 bg-light">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="with_vat" id="withVat" value="1" <?php echo e(($withVat ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="withVat">With Vat</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="bill_amount" id="billAmount" value="1" <?php echo e(($billAmount ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="billAmount">Bill Amount</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="gst_summary" id="gstSummary" value="1" <?php echo e(($gstSummary ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="gstSummary">GST Summary</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-2" style="border-top: 2px solid #000; padding-top: 10px;">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="exportToExcel()">
                            <u>E</u>xcel
                        </button>
                        <button type="submit" name="view" value="1" class="btn btn-light border px-4 fw-bold shadow-sm me-2">
                            <u>V</u>iew
                        </button>
                        <button type="button" class="btn btn-light border px-4 fw-bold shadow-sm me-2" onclick="printReport()">
                            <u>P</u>rint
                        </button>
                        <a href="<?php echo e(route('admin.reports.sales')); ?>" class="btn btn-light border px-4 fw-bold shadow-sm">
                            <u>C</u>lose
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table - Only show when view is clicked -->
    <?php if(request()->has('view') && isset($groupedSales) && count($groupedSales) > 0): ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 50vh;">
                <table class="table table-sm table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-center" style="width: 30px;">#</th>
                            <th style="width: 60px;">Code</th>
                            <th>Party Name</th>
                            <th>Area</th>
                            <th class="text-center">Bills</th>
                            <th class="text-end">NT Amount</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $groupedSales ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customerId => $customerSales): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $firstSale = $customerSales->first();
                            $customerTotal = [
                                'nt_amount' => $customerSales->sum('nt_amount'),
                                'dis_amount' => $customerSales->sum('dis_amount'),
                                'tax_amount' => $customerSales->sum('tax_amount'),
                                'net_amount' => $customerSales->sum('net_amount'),
                            ];
                        ?>
                        <tr class="table-info">
                            <td class="text-center"><?php echo e($loop->iteration); ?></td>
                            <td class="fw-bold"><?php echo e($firstSale->customer->code ?? ''); ?></td>
                            <td class="fw-bold"><?php echo e($firstSale->customer->name ?? 'N/A'); ?></td>
                            <td><?php echo e($firstSale->customer->area_name ?? '-'); ?></td>
                            <td class="text-center fw-bold"><?php echo e($customerSales->count()); ?></td>
                            <td class="text-end fw-bold"><?php echo e(number_format($customerTotal['nt_amount'], 2)); ?></td>
                            <td class="text-end text-danger"><?php echo e(number_format($customerTotal['dis_amount'], 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($customerTotal['tax_amount'], 2)); ?></td>
                            <td class="text-end fw-bold text-success"><?php echo e(number_format($customerTotal['net_amount'], 2)); ?></td>
                        </tr>
                        <?php if($billWise ?? true): ?>
                            <?php $__currentLoopData = $customerSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td></td>
                                <td class="text-muted small"><?php echo e($sale->sale_date->format('d-m')); ?></td>
                                <td class="small ps-3">
                                    <a href="<?php echo e(route('admin.sale.show', $sale->id)); ?>" class="text-decoration-none">
                                        <?php echo e($sale->series); ?><?php echo e($sale->invoice_no); ?>

                                    </a>
                                </td>
                                <td></td>
                                <td></td>
                                <td class="text-end small"><?php echo e(number_format($sale->nt_amount ?? 0, 2)); ?></td>
                                <td class="text-end small text-danger"><?php echo e(number_format($sale->dis_amount ?? 0, 2)); ?></td>
                                <td class="text-end small"><?php echo e(number_format($sale->tax_amount ?? 0, 2)); ?></td>
                                <td class="text-end small"><?php echo e(number_format($sale->net_amount ?? 0, 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Select filters and click "View" to generate Party Wise Sale report
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if(isset($totals) && ($totals['count'] ?? 0) > 0): ?>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Grand Total:</td>
                            <td class="text-center"><?php echo e($totals['count'] ?? 0); ?></td>
                            <td class="text-end"><?php echo e(number_format($totals['nt_amount'] ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($totals['dis_amount'] ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($totals['tax_amount'] ?? 0, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($totals['net_amount'] ?? 0, 2)); ?></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Sync report type radio buttons with hidden field
document.querySelectorAll('input[name="report_type_radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('hidden_report_type').value = this.value;
    });
});

function exportToExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    params.set('export', 'excel');
    window.open('<?php echo e(route("admin.reports.sales.sales-book-party-wise")); ?>?' + params.toString(), '_blank');
}

function printReport() {
    window.open('<?php echo e(route("admin.reports.sales.sales-book-party-wise")); ?>?print=1&' + $('#filterForm').serialize(), '_blank');
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    if (e.altKey && e.key.toLowerCase() === 'v') {
        e.preventDefault();
        $('button[name="view"]').click();
    }
    if (e.altKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        printReport();
    }
    if (e.altKey && e.key.toLowerCase() === 'c') {
        e.preventDefault();
        window.location.href = '<?php echo e(route("admin.reports.sales")); ?>';
    }
    if (e.altKey && e.key.toLowerCase() === 'e') {
        e.preventDefault();
        exportToExcel();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.form-control-sm, .form-select-sm { border: 1px solid #aaa; border-radius: 0; }
.card { border-radius: 0; border: 1px solid #ccc; }
.btn { border-radius: 0; }
.input-group-text { font-size: 0.75rem; padding: 0.25rem 0.5rem; min-width: fit-content; border-radius: 0; }
.form-control, .form-select { font-size: 0.8rem; border-radius: 0; }
.table th, .table td { padding: 0.35rem 0.5rem; font-size: 0.8rem; vertical-align: middle; }
.sticky-top { position: sticky; top: 0; z-index: 10; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/reports/sale-report/sales-book-party-wise.blade.php ENDPATH**/ ?>