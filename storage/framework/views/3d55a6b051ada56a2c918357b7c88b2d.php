<?php $__env->startSection('title', 'Supplier Payments'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .table-compact { font-size: 12px; }
    .table-compact th, .table-compact td { padding: 6px 8px; vertical-align: middle; }
    .badge-cash { background-color: #28a745; }
    .badge-cheque { background-color: #17a2b8; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-cash-stack me-2"></i> Supplier Payments</h4>
        <small class="text-muted">Manage payment transactions to suppliers</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.supplier-payment.transaction')); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> New Payment
        </a>
        <a href="<?php echo e(route('admin.supplier-payment.modification')); ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil-square"></i> Modification
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <form method="GET" action="<?php echo e(route('admin.supplier-payment.index')); ?>" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Trn No, Bank..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" value="<?php echo e(request('from_date')); ?>" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm" value="<?php echo e(request('to_date')); ?>" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Search</button>
                <a href="<?php echo e(route('admin.supplier-payment.index')); ?>" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-compact mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Trn No</th>
                        <th style="width: 100px;">Date</th>
                        <th>Bank</th>
                        <th style="width: 100px;">Cash</th>
                        <th style="width: 100px;">Cheque</th>
                        <th style="width: 100px;">Total</th>
                        <th style="width: 80px;">Parties</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center"><?php echo e($payment->trn_no); ?></td>
                        <td><?php echo e($payment->payment_date->format('d/m/Y')); ?></td>
                        <td><?php echo e($payment->bank_name ?? '-'); ?></td>
                        <td class="text-end"><?php echo e(number_format($payment->total_cash, 2)); ?></td>
                        <td class="text-end"><?php echo e(number_format($payment->total_cheque, 2)); ?></td>
                        <td class="text-end fw-bold"><?php echo e(number_format($payment->total_cash + $payment->total_cheque, 2)); ?></td>
                        <td class="text-center"><?php echo e($payment->items->count()); ?></td>
                        <td class="text-center">
                            <a href="<?php echo e(route('admin.supplier-payment.show', $payment->id)); ?>" class="btn btn-info btn-sm" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.supplier-payment.modification')); ?>?trn_no=<?php echo e($payment->trn_no); ?>" class="btn btn-warning btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No payments found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($payments->hasPages()): ?>
    <div class="card-footer">
        <?php echo e($payments->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/supplier-payment/index.blade.php ENDPATH**/ ?>