<?php $__env->startSection('title', 'Replacement Received Invoices'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-box-arrow-in-down me-2"></i> Replacement Received Invoices</h4>
        <div class="text-muted small">View and manage all replacement received transactions</div>
    </div>
    <div>
        <a href="<?php echo e(route('admin.replacement-received.transaction')); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Transaction
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <!-- Filter Section -->
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.replacement-received.index')); ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Filter By</label>
                <select class="form-select" name="filter_by">
                    <option value="supplier_name" <?php echo e(request('filter_by') == 'supplier_name' ? 'selected' : ''); ?>>Supplier Name</option>
                    <option value="rr_no" <?php echo e(request('filter_by', 'rr_no') == 'rr_no' ? 'selected' : ''); ?>>RR No.</option>
                    <option value="total_amount" <?php echo e(request('filter_by') == 'total_amount' ? 'selected' : ''); ?>>Amount</option>
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

    <!-- Table Section -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 100px;">Date</th>
                    <th style="width: 100px;">RR No.</th>
                    <th>Supplier</th>
                    <th style="width: 120px;" class="text-end">Total Amount</th>
                    <th style="width: 80px;">Status</th>
                    <th style="width: 120px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transactions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration + (($transactions->currentPage() - 1) * $transactions->perPage())); ?></td>
                    <td><?php echo e($transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-'); ?></td>
                    <td><strong><?php echo e($transaction->rr_no); ?></strong></td>
                    <td><?php echo e($transaction->supplier ? $transaction->supplier->name : ($transaction->supplier_name ?? '-')); ?></td>
                    <td class="text-end">₹<?php echo e(number_format($transaction->total_amount ?? 0, 2)); ?></td>
                    <td>
                        <span class="badge bg-<?php echo e($transaction->status == 'active' ? 'success' : 'danger'); ?>">
                            <?php echo e(ucfirst($transaction->status ?? 'active')); ?>

                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?php echo e(route('admin.replacement-received.show', $transaction->id)); ?>" class="btn btn-sm btn-outline-info" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?php echo e(route('admin.replacement-received.modification')); ?>?id=<?php echo e($transaction->id); ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="<?php echo e(route('admin.replacement-received.destroy', $transaction->id)); ?>" method="POST" class="d-inline ajax-delete-form">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="button" class="btn btn-sm btn-outline-danger ajax-delete" data-delete-url="<?php echo e(route('admin.replacement-received.destroy', $transaction->id)); ?>" data-delete-message="Delete this transaction?" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No replacement received transactions found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if(isset($transactions) && $transactions->hasPages()): ?>
    <div class="card-footer">
        <?php echo e($transactions->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/replacement-received/index.blade.php ENDPATH**/ ?>