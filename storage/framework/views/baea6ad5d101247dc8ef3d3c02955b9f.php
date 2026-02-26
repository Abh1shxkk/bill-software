<?php $__env->startSection('title', 'Batch Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center">
            <i class="bi bi-box-seam me-2"></i> 
            <?php if(isset($viewType) && $viewType === 'all'): ?>
                All Batches
            <?php else: ?>
                Available Batches
            <?php endif; ?>
        </h4>
        <div class="text-muted small">
            <?php if(isset($viewType) && $viewType === 'all'): ?>
                View all batches including positive, negative, and zero quantities for this item
            <?php else: ?>
                View batches with non-zero quantity (positive or negative only) for this item
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.items.index')); ?>" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Items
        </a>
        <?php
            $currentItemId = $itemId ?? request('item_id');
        ?>
        <?php if(isset($viewType) && $viewType === 'all'): ?>
            <a href="<?php echo e(route('admin.batches.index', ['item_id' => $currentItemId])); ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-check-circle me-1"></i> Available Batches
            </a>
        <?php else: ?>
            <a href="<?php echo e(route('admin.batches.all', ['item_id' => $currentItemId])); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list-ul me-1"></i> All Batches
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <?php
                    $currentItem = isset($itemId) ? \App\Models\Item::find($itemId) : null;
                ?>
                <?php if($currentItem): ?>
                    <div class="fw-semibold"><?php echo e($currentItem->name); ?></div>
                    <div class="text-muted small">Code: <?php echo e($currentItem->bar_code ?? 'N/A'); ?></div>
                <?php else: ?>
                    <div class="text-muted small">Item details not available.</div>
                <?php endif; ?>
            </div>
            <div>
                <button type="button" class="btn btn-primary btn-sm" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <?php if($groupedBatches->count() > 0): ?>
            <?php $__currentLoopData = $groupedBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemId => $batches): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $item = \App\Models\Item::find($itemId);
                    
                    // Skip if item not found
                    if (!$item) {
                        continue;
                    }
                    
                    // Get first batch for item name (only if batches exist)
                    $firstBatch = $batches->isNotEmpty() ? $batches->first() : null;
                    
                    // Filter out batches with null batch_no
                    $validBatches = $batches->filter(function($batch) {
                        return !empty($batch->batch_no);
                    });
                ?>
                
                <?php if($validBatches->isNotEmpty() || $item): ?>
                    <div class="mb-3 p-2 bg-light rounded">
                        <strong><?php echo e($firstBatch->item_name ?? $item->name ?? 'N/A'); ?></strong>
                        <span class="text-muted ms-2">(Packing: <?php echo e($item->packing ?? '1*10'); ?>)</span>
                    </div>
                    
                    <?php if($validBatches->isNotEmpty()): ?>
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr.</th>
                                    <th>Batch</th>
                                    <th>Exp.</th>
                                    <th>Qty.</th>
                                    <th>S.Rate</th>
                                    <th>F.T.Rate</th>
                                    <th>P.Rate</th>
                                    <th>MRP</th>
                                    <th>WS.Rate</th>
                                    <th>Spl.Rate</th>
                                    <th>Scm.</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $validBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        // Calculate values from batch data
                                        $totalQty = $batch->total_qty ?? 0;
                                        $avgRate = $batch->avg_pur_rate ?? 0;
                                        $avgMrp = $batch->avg_mrp ?? 0;
                                        $maxRate = $batch->max_rate ?? 0;
                                        $avgSRate = $batch->avg_s_rate ?? 0;
                                        $avgWsRate = $batch->avg_ws_rate ?? 0;
                                        $avgSplRate = $batch->avg_spl_rate ?? 0;
                                        $avgCgst = $batch->avg_cgst_percent ?? 0;
                                        $avgSgst = $batch->avg_sgst_percent ?? 0;
                                        
                                        $totalGstPercent = $avgCgst + $avgSgst;
                                        
                                        // Calculate F.T.Rate: S.Rate × (1 + GST/100)
                                        $ftRate = $avgSRate > 0 ? ($avgSRate * (1 + ($totalGstPercent / 100))) : 0;
                                        
                                        // Format expiry date as MM/YY
                                        $expiryDisplay = $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('m/y') : '---';
                                        
                                        // Check if expired
                                        $isExpired = $batch->expiry_date && \Carbon\Carbon::parse($batch->expiry_date)->isPast();
                                        
                                        // Check if quantity is zero
                                        $isZero = $totalQty == 0;
                                        
                                        // Check if quantity is negative
                                        $isNegative = $totalQty < 0;
                                        
                                        // Get first batch ID for edit link
                                        $firstBatchId = $batch->first_batch_id ?? null;
                                        
                                        // Determine row class based on status
                                        $rowClass = '';
                                        if ($isExpired) {
                                            $rowClass = 'table-danger'; // Red for expired
                                        } elseif ($isNegative) {
                                            $rowClass = 'table-warning'; // Yellow for negative
                                        } elseif ($isZero) {
                                            $rowClass = 'table-secondary'; // Gray for zero
                                        }
                                    ?>
                                    <tr class="<?php echo e($rowClass); ?>">
                                        <td><?php echo e($index + 1); ?></td>
                                        <td>
                                            <?php echo e($batch->batch_no); ?>

                                            <?php if($isExpired): ?>
                                                <span class="badge bg-danger ms-1">EXPIRED</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="<?php echo e($isExpired ? 'text-danger fw-bold' : ''); ?>"><?php echo e($expiryDisplay); ?></td>
                                        <td class="<?php echo e($isNegative ? 'text-danger fw-bold' : ($isZero ? 'text-muted' : '')); ?>">
                                            <?php echo e(number_format($totalQty, 0)); ?>

                                            <?php if($isNegative): ?>
                                                <span class="badge bg-danger ms-1">NEGATIVE</span>
                                            <?php elseif($isZero): ?>
                                                <span class="badge bg-secondary ms-1">ZERO</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e(number_format($avgSRate, 2)); ?></td>
                                        <td><?php echo e(number_format($ftRate, 2)); ?></td>
                                        <td><?php echo e(number_format($avgRate, 2)); ?></td>
                                        <td><?php echo e(number_format($avgMrp, 2)); ?></td>
                                        <td><?php echo e(number_format($avgWsRate, 2)); ?></td>
                                        <td><?php echo e(number_format($avgSplRate, 2)); ?></td>
                                        <td></td>
                                        <td class="text-end">
                                            <?php if($firstBatchId): ?>
                                                <a href="<?php echo e(route('admin.batches.show', $firstBatchId)); ?>" 
                                                   class="btn btn-sm btn-outline-info me-1" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.batches.edit', $firstBatchId)); ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit Batch">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <p>No batches found for this item.</p>
                        </div>
                    <?php endif; ?>
                    <hr class="my-3">
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-3">No batches found</p>
                <p class="small">Batches will appear here once purchase transactions are created with batch numbers.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Any additional JavaScript can go here
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/batches/index.blade.php ENDPATH**/ ?>