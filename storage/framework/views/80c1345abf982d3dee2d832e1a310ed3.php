<?php $__env->startSection('title', 'Voucher Entry - List'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Voucher Entry</h5>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.voucher-entry.transaction')); ?>?type=receipt" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Voucher
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-2">
                <label class="form-label small">Voucher Type</label>
                <select name="voucher_type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="receipt" <?php echo e(request('voucher_type') == 'receipt' ? 'selected' : ''); ?>>Receipt</option>
                    <option value="payment" <?php echo e(request('voucher_type') == 'payment' ? 'selected' : ''); ?>>Payment</option>
                    <option value="contra" <?php echo e(request('voucher_type') == 'contra' ? 'selected' : ''); ?>>Contra</option>
                    <option value="journal" <?php echo e(request('voucher_type') == 'journal' ? 'selected' : ''); ?>>Journal</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="<?php echo e(request('from_date')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="<?php echo e(request('to_date')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Voucher No, Narration..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="<?php echo e(route('admin.voucher-entry.index')); ?>" class="btn btn-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">Voucher No</th>
                        <th style="width: 100px;">Date</th>
                        <th style="width: 100px;">Type</th>
                        <th>Narration</th>
                        <th class="text-end" style="width: 120px;">Debit</th>
                        <th class="text-end" style="width: 120px;">Credit</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($voucher->voucher_no); ?></td>
                        <td><?php echo e($voucher->voucher_date ? $voucher->voucher_date->format('d/m/Y') : '-'); ?></td>
                        <td>
                            <?php
                                $typeBadges = [
                                    'receipt' => 'bg-success',
                                    'payment' => 'bg-danger',
                                    'contra' => 'bg-info',
                                    'journal' => 'bg-warning text-dark',
                                ];
                            ?>
                            <span class="badge <?php echo e($typeBadges[$voucher->voucher_type] ?? 'bg-secondary'); ?>">
                                <?php echo e(ucfirst($voucher->voucher_type)); ?>

                            </span>
                        </td>
                        <td><?php echo e(Str::limit($voucher->narration, 50)); ?></td>
                        <td class="text-end"><?php echo e(number_format($voucher->total_debit, 2)); ?></td>
                        <td class="text-end"><?php echo e(number_format($voucher->total_credit, 2)); ?></td>
                        <td>
                            <?php if($voucher->status == 'cancelled'): ?>
                                <span class="badge bg-danger">Cancelled</span>
                            <?php else: ?>
                                <span class="badge bg-success">Active</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.voucher-entry.show', $voucher->id)); ?>" class="btn btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.voucher-entry.modification')); ?>?voucher_no=<?php echo e($voucher->voucher_no); ?>&type=<?php echo e($voucher->voucher_type); ?>" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteVoucher(<?php echo e($voucher->id); ?>)" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No vouchers found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing <?php echo e($vouchers->firstItem() ?? 0); ?> to <?php echo e($vouchers->lastItem() ?? 0); ?> of <?php echo e($vouchers->total()); ?> entries
            </div>
            <?php echo e($vouchers->withQueryString()->links()); ?>

        </div>
    </div>
</div>

<script>
function deleteVoucher(id) {
    if (confirm('Are you sure you want to delete this voucher?')) {
        fetch(`<?php echo e(url('admin/voucher-entry')); ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(e => {
            console.error('Error:', e);
            alert('Failed to delete voucher');
        });
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/voucher-entry/index.blade.php ENDPATH**/ ?>