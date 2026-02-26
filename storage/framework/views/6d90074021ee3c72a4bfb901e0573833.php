<?php $__env->startSection('title', 'Pending Order Items List'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-list-ul me-2"></i> Pending Order Items</h4>
        <a href="<?php echo e(route('admin.pending-order-item.transaction')); ?>" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Entry
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Action</th>
                            <th class="text-end">Quantity</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration + ($items->currentPage() - 1) * $items->perPage()); ?></td>
                            <td><?php echo e($item->item_code); ?></td>
                            <td><?php echo e($item->item_name); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($item->action_type === 'I' ? 'success' : 'danger'); ?>">
                                    <?php echo e($item->action_type === 'I' ? 'Insert' : 'Delete'); ?>

                                </span>
                            </td>
                            <td class="text-end"><?php echo e(number_format($item->quantity, 2)); ?></td>
                            <td><?php echo e($item->created_at->format('d-M-Y H:i')); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo e($item->id); ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No items found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                <?php echo e($items->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function deleteItem(id) {
    if (!confirm('Are you sure you want to delete this item?')) return;
    
    fetch(`<?php echo e(url('admin/pending-order-item')); ?>/${id}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting item');
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/pending-order-item/index.blade.php ENDPATH**/ ?>