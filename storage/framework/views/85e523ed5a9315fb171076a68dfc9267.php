<?php $__env->startSection('title', 'Stock Transfer Incoming Return'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-box-arrow-up-right me-2"></i> Stock Transfer Incoming Return</h4>
    <div class="text-muted small">Manage incoming stock transfer returns (Stock REDUCED)</div>
  </div>
  <div>
    <a href="<?php echo e(route('admin.stock-transfer-incoming-return.transaction')); ?>" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i> New Transaction
    </a>
    <a href="<?php echo e(route('admin.stock-transfer-incoming-return.modification')); ?>" class="btn btn-warning btn-sm">
      <i class="bi bi-pencil-square me-1"></i> Modification
    </a>
  </div>
</div>

<div class="card shadow-sm border-0 rounded">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="<?php echo e(route('admin.stock-transfer-incoming-return.index')); ?>" class="row g-3" id="filterForm">
        <div class="col-md-3">
          <label for="filter_by" class="form-label">Filter By</label>
          <select class="form-select" id="filter_by" name="filter_by">
            <option value="trn_no" <?php echo e(request('filter_by', 'trn_no') == 'trn_no' ? 'selected' : ''); ?>>Trn No.</option>
            <option value="name" <?php echo e(request('filter_by') == 'name' ? 'selected' : ''); ?>>Name</option>
            <option value="net_amount" <?php echo e(request('filter_by') == 'net_amount' ? 'selected' : ''); ?>>Net Amount</option>
          </select>
        </div>
        <div class="col-md-5">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" 
                   value="<?php echo e(request('search')); ?>" placeholder="Enter search term..." autocomplete="off">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-search"></i> Search
            </button>
          </div>
        </div>
        <div class="col-md-2">
          <label for="date_from" class="form-label">Date From</label>
          <input type="date" class="form-control" id="date_from" name="date_from" 
                 value="<?php echo e(request('date_from')); ?>" autocomplete="off">
        </div>
        <div class="col-md-2">
          <label for="date_to" class="form-label">Date To</label>
          <input type="date" class="form-control" id="date_to" name="date_to" 
                 value="<?php echo e(request('date_to')); ?>" autocomplete="off">
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <a href="<?php echo e(route('admin.stock-transfer-incoming-return.index')); ?>" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-clockwise"></i> Clear All
          </a>
        </div>
      </form>
    </div>
  </div>

  <!-- Table Section -->
  <div class="table-responsive" style="min-height: 400px;">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Name</th>
          <th>Trn No.</th>
          <th>GR No.</th>
          <th>GR Date</th>
          <th class="text-end">Net Amount</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $transactions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e(($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration); ?></td>
            <td><?php echo e($transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-'); ?></td>
            <td><?php echo e($transaction->name ?? '-'); ?></td>
            <td><strong><?php echo e($transaction->trn_no ?? '-'); ?></strong></td>
            <td><?php echo e($transaction->gr_no ?? '-'); ?></td>
            <td><?php echo e($transaction->gr_date ? (is_string($transaction->gr_date) ? $transaction->gr_date : $transaction->gr_date->format('d/m/Y')) : '-'); ?></td>
            <td class="text-end">
              <span class="badge bg-danger">₹<?php echo e(number_format($transaction->net_amount ?? 0, 2)); ?></span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-info" href="<?php echo e(route('admin.stock-transfer-incoming-return.show', $transaction->id)); ?>" title="View Details">
                <i class="bi bi-eye"></i>
              </a>
              <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('admin.stock-transfer-incoming-return.modification')); ?>?id=<?php echo e($transaction->id); ?>" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTransaction(<?php echo e($transaction->id); ?>)" title="Delete">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="bi bi-inbox display-4"></i>
              <p class="mt-2">No transactions found</p>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <?php if(isset($transactions) && $transactions->hasPages()): ?>
  <div class="card-footer bg-white">
    <?php echo e($transactions->withQueryString()->links()); ?>

  </div>
  <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function deleteTransaction(id) {
    if (confirm('Are you sure you want to delete this transaction? Stock quantities will be RESTORED.')) {
        fetch(`<?php echo e(url('admin/stock-transfer-incoming-return')); ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Error deleting transaction');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting transaction');
        });
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/stock-transfer-incoming-return/index.blade.php ENDPATH**/ ?>