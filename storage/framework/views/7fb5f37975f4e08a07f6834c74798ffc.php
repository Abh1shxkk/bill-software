<?php $__env->startSection('title', 'Godown Breakage/Expiry'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-box-seam me-2"></i> Godown Breakage/Expiry</h4>
    <div class="text-muted small">Manage godown breakage and expiry transactions</div>
  </div>
  <div>
    <a href="<?php echo e(route('admin.godown-breakage-expiry.create')); ?>" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i> New Transaction
    </a>
  </div>
</div>

<div class="card shadow-sm border-0 rounded">
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="<?php echo e(route('admin.godown-breakage-expiry.index')); ?>" class="row g-3" id="filterForm">
        <div class="col-md-3">
          <label for="filter_by" class="form-label">Filter By</label>
          <select class="form-select" id="filter_by" name="filter_by">
            <option value="trn_no" <?php echo e(request('filter_by', 'trn_no') == 'trn_no' ? 'selected' : ''); ?>>TRN No.</option>
            <option value="narration" <?php echo e(request('filter_by') == 'narration' ? 'selected' : ''); ?>>Narration</option>
          </select>
        </div>
        <div class="col-md-5">
          <label for="search" class="form-label">Search</label>
          <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Enter search term...">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
          </div>
        </div>
        <div class="col-md-2">
          <label for="from_date" class="form-label">Date From</label>
          <input type="date" class="form-control" id="from_date" name="from_date" value="<?php echo e(request('from_date')); ?>">
        </div>
        <div class="col-md-2">
          <label for="to_date" class="form-label">Date To</label>
          <input type="date" class="form-control" id="to_date" name="to_date" value="<?php echo e(request('to_date')); ?>">
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>TRN No.</th>
          <th>Narration</th>
          <th class="text-end">Qty</th>
          <th class="text-end">Total Amount</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $transactions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e(($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration); ?></td>
            <td><?php echo e($transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-'); ?></td>
            <td><strong><?php echo e($transaction->trn_no ?? '-'); ?></strong></td>
            <td><?php echo e(Str::limit($transaction->narration ?? '-', 50)); ?></td>
            <td class="text-end"><?php echo e(number_format($transaction->total_qty ?? 0, 0)); ?></td>
            <td class="text-end">
              <span class="badge bg-danger">₹<?php echo e(number_format($transaction->total_amount ?? 0, 2)); ?></span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-info" href="<?php echo e(route('admin.godown-breakage-expiry.show', $transaction->id)); ?>" title="View">
                <i class="bi bi-eye"></i>
              </a>
              <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('admin.godown-breakage-expiry.modification')); ?>?load=<?php echo e($transaction->id); ?>" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <button type="button" class="btn btn-sm btn-outline-danger delete-gbe" data-id="<?php echo e($transaction->id); ?>" data-trn-no="<?php echo e($transaction->trn_no); ?>" title="Cancel">
                <i class="bi bi-x-circle"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="7" class="text-center text-muted">No transactions found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="card-footer bg-light">
    <div class="d-flex justify-content-between align-items-center">
      <div>Showing <?php echo e($transactions->firstItem() ?? 0); ?>-<?php echo e($transactions->lastItem() ?? 0); ?> of <?php echo e($transactions->total() ?? 0); ?></div>
      <?php echo e($transactions->links()); ?>

    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Cancel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to cancel this transaction?</p>
        <div class="alert alert-warning">
          <strong>TRN No:</strong> <span id="delete-trn-no"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="confirm-delete">Cancel Transaction</button>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.delete-gbe').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.dataset.id;
      const trnNo = this.dataset.trnNo;
      
      document.getElementById('delete-trn-no').textContent = trnNo;
      const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
      modal.show();
      
      document.getElementById('confirm-delete').onclick = function() {
        fetch(`<?php echo e(url('admin/godown-breakage-expiry')); ?>/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            modal.hide();
            location.reload();
          } else {
            alert(data.message || 'Error cancelling transaction');
          }
        });
      };
    });
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/godown-breakage-expiry/index.blade.php ENDPATH**/ ?>