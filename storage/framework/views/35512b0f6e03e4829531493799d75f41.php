<?php $__env->startSection('title', 'Stock Transfer Outgoing'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-box-arrow-right me-2"></i> Stock Transfer Outgoing</h4>
        <div class="text-muted small">View and manage all stock transfer outgoing transactions</div>
    </div>
    <div>
        <a href="<?php echo e(route('admin.stock-transfer-outgoing.transaction')); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Transaction
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.stock-transfer-outgoing.index')); ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Filter By</label>
                <select class="form-select" name="filter_by">
                    <option value="transfer_to" <?php echo e(request('filter_by') == 'transfer_to' ? 'selected' : ''); ?>>Transfer To</option>
                    <option value="sr_no" <?php echo e(request('filter_by') == 'sr_no' ? 'selected' : ''); ?>>SR No.</option>
                    <option value="challan_no" <?php echo e(request('filter_by') == 'challan_no' ? 'selected' : ''); ?>>Challan No.</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="search" value="<?php echo e(request('search')); ?>" placeholder="Enter search term...">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" class="form-control" name="date_from" value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" class="form-control" name="date_to" value="<?php echo e(request('date_to')); ?>">
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>SR No.</th>
                    <th>Transfer To</th>
                    <th>GR No.</th>
                    <th class="text-end">Net Amount</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transactions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($transaction->transaction_date->format('d/m/Y')); ?></td>
                    <td><strong><?php echo e($transaction->sr_no); ?></strong></td>
                    <td><?php echo e($transaction->transfer_to_name ?? '-'); ?></td>
                    <td><?php echo e($transaction->challan_no ?? '-'); ?></td>
                    <td class="text-end">₹<?php echo e(number_format($transaction->net_amount, 2)); ?></td>
                    <td>
                        <span class="badge bg-<?php echo e($transaction->status == 'active' ? 'success' : 'danger'); ?>">
                            <?php echo e(ucfirst($transaction->status)); ?>

                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?php echo e(route('admin.stock-transfer-outgoing.show', $transaction->id)); ?>" class="btn btn-sm btn-outline-info" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?php echo e(route('admin.stock-transfer-outgoing.modification')); ?>?sr_no=<?php echo e($transaction->sr_no); ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="<?php echo e(route('admin.stock-transfer-outgoing.destroy', $transaction)); ?>" method="POST" class="d-inline ajax-delete-form">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" data-delete-url="<?php echo e(route('admin.stock-transfer-outgoing.destroy', $transaction)); ?>" data-delete-message="Delete this transaction?" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No transactions found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(isset($transactions) && $transactions->hasPages()): ?>
    <div class="card-footer">
        <?php echo e($transactions->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/stock-transfer-outgoing/index.blade.php ENDPATH**/ ?>