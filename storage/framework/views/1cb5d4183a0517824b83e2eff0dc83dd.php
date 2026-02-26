<?php $__env->startSection('title', 'Sale Return Replacement List'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-arrow-return-left me-2"></i> Sale Return Replacement List</h4>
        <div class="text-muted small">View and manage sale return replacement transactions</div>
    </div>
    <div>
        <a href="<?php echo e(route('admin.sale-return-replacement.transaction')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Transaction
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card mb-4 border-0">
        <div class="card-body">
            <!-- Search Filter -->
             <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                         <input type="text" name="search" class="form-control" placeholder="Search Trn No / Customer" value="<?php echo e(request('search')); ?>">
                         <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo e(request('from_date')); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo e(request('to_date')); ?>">
                </div>
                 <div class="col-md-2 d-flex align-items-end">
                    <a href="<?php echo e(route('admin.sale-return-replacement.index')); ?>" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Series</th>
                    <th>Trn No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th class="text-end">Net Amt</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($trn->id); ?></td>
                    <td><?php echo e($trn->series); ?></td>
                    <td><?php echo e($trn->trn_no); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($trn->trn_date)->format('d/m/Y')); ?></td>
                    <td><?php echo e($trn->customer_name); ?></td>
                    <td class="text-end"><span class="badge bg-success"><?php echo e(number_format($trn->net_amt, 2)); ?></span></td>
                    <td class="text-end">
                        <a href="<?php echo e(route('admin.sale-return-replacement.show', $trn->id)); ?>" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                        <!-- Modification link passing Trn No like Sale module does with invoice_no -->
                        <a href="#" onclick="alert('Please use Modification page and load Trn No: <?php echo e($trn->trn_no); ?>')" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        
                        <form action="<?php echo e(route('admin.sale-return-replacement.destroy', $trn->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No transactions found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
            
    <div class="card-footer bg-light">
        <?php echo e($transactions->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/sale-return-replacement/index.blade.php ENDPATH**/ ?>