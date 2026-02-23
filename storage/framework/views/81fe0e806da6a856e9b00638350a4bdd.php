<?php $__env->startSection('title','Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="dashboard-container">
    <!-- Welcome Banner -->
    <div class="card mb-3" style="background: #6366f1; color: white; border: none; border-radius: 12px;">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h5 class="mb-1 fw-bold">Welcome back, <?php echo e(auth()->user()->full_name ?? 'Admin'); ?>! 👋</h5>
                    <small class="opacity-75">Here's what's happening with your business today.</small>
                    <?php $user = auth()->user(); ?>
                    <?php if($user->licensed_to || $user->gst_no): ?>
                    <div class="mt-2 small opacity-90">
                        <?php if($user->licensed_to): ?><span class="me-3">Licensed To: <?php echo e($user->licensed_to); ?></span><?php endif; ?>
                        <?php if($user->gst_no): ?><span>GST: <?php echo e($user->gst_no); ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="text-end d-none d-md-block">
                    <div class="small opacity-75"><?php echo e(now()->format('l, F j, Y')); ?></div>
                    <?php if(!$user->isAdmin()): ?>
                    <span class="badge bg-light text-dark mt-1">
                        <i class="bi bi-person-badge me-1"></i>User Account
                    </span>
                    <?php else: ?>
                    <span class="badge bg-warning text-dark mt-1">
                        <i class="bi bi-shield-check me-1"></i>Administrator
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <?php
        $license = auth()->user()->getActiveLicense();
    ?>
    <?php if($license && $license->isExpiringSoon()): ?>
    <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert" style="border-radius: 12px;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>License Expiring!</strong> Your license expires in <?php echo e($license->daysUntilExpiry()); ?> days (<?php echo e($license->expires_at->format('d M Y')); ?>).
            </div>
            <a href="<?php echo e(route('license.status')); ?>" class="btn btn-sm btn-warning">View Details</a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Quick Shortcuts -->
    <div class="card mb-3" style="border-radius: 12px;">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 fw-bold"><i class="bi bi-keyboard me-2"></i>Quick Access</h6>
                <small class="text-muted"><kbd>F1</kbd> for full list</small>
            </div>
            <div class="d-flex gap-2 overflow-auto pb-2" style="scrollbar-width: none;">
                <?php if(auth()->user()->hasPermission('sale', 'view')): ?>
                <a href="<?php echo e(route('admin.sale.transaction')); ?>" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-cart-check me-1"></i>Sale
                </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasPermission('purchase', 'view')): ?>
                <a href="<?php echo e(route('admin.purchase.transaction')); ?>" class="btn btn-info btn-sm px-3 text-white">
                    <i class="bi bi-box-seam me-1"></i>Purchase
                </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasPermission('sale-return', 'view')): ?>
                <a href="<?php echo e(route('admin.sale-return.transaction')); ?>" class="btn btn-warning btn-sm px-3">
                    <i class="bi bi-arrow-return-left me-1"></i>Sale Return
                </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasPermission('customer-receipt', 'view')): ?>
                <a href="<?php echo e(route('admin.customer-receipt.transaction')); ?>" class="btn btn-success btn-sm px-3">
                    <i class="bi bi-cash-stack me-1"></i>Receipt
                </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasPermission('supplier-payment', 'view')): ?>
                <a href="<?php echo e(route('admin.supplier-payment.transaction')); ?>" class="btn btn-danger btn-sm px-3">
                    <i class="bi bi-wallet2 me-1"></i>Payment
                </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasPermission('items', 'view')): ?>
                <a href="<?php echo e(route('admin.items.index')); ?>" class="btn btn-secondary btn-sm px-3">
                    <i class="bi bi-archive me-1"></i>Items
                </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasPermission('customers', 'view')): ?>
                <a href="<?php echo e(route('admin.customers.index')); ?>" class="btn btn-outline-primary btn-sm px-3">
                    <i class="bi bi-people me-1"></i>Customers
                </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasPermission('suppliers', 'view')): ?>
                <a href="<?php echo e(route('admin.suppliers.index')); ?>" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-truck me-1"></i>Suppliers
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Today's Stats -->
    <div class="card mb-3" style="background: #1e293b; color: white; border-radius: 12px;">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge text-warning"></i>
                    <span class="fw-bold">Today's Activity</span>
                </div>
                <div class="d-flex flex-wrap gap-4">
                    <div class="text-center">
                        <div class="fw-bold text-success"><?php echo e($todayStats['sales']); ?></div>
                        <small class="opacity-75">Sales</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-success">₹<?php echo e(number_format($todayStats['sales_amount'])); ?></div>
                        <small class="opacity-75">Sales Amt</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-info"><?php echo e($todayStats['purchases']); ?></div>
                        <small class="opacity-75">Purchases</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-info">₹<?php echo e(number_format($todayStats['purchases_amount'])); ?></div>
                        <small class="opacity-75">Purchase Amt</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="card h-100" style="background: #6366f1; color: white; border: none; border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="small text-uppercase opacity-75">Total Sales</div>
                    <div class="h4 mb-1 fw-bold"><?php echo e(number_format($totalSales)); ?></div>
                    <small><i class="bi bi-arrow-<?php echo e($salesGrowth >= 0 ? 'up' : 'down'); ?>"></i> <?php echo e(abs($salesGrowth)); ?>% from last month</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100" style="background: #10b981; color: white; border: none; border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="small text-uppercase opacity-75">Customers</div>
                    <div class="h4 mb-1 fw-bold"><?php echo e(number_format($totalCustomers)); ?></div>
                    <small><i class="bi bi-people"></i> Active</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100" style="background: #f59e0b; color: white; border: none; border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="small text-uppercase opacity-75">Items in Stock</div>
                    <div class="h4 mb-1 fw-bold"><?php echo e(number_format($totalItems)); ?></div>
                    <small><i class="bi bi-box"></i> In Inventory</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100" style="background: #ef4444; color: white; border: none; border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="small text-uppercase opacity-75">Suppliers</div>
                    <div class="h4 mb-1 fw-bold"><?php echo e(number_format($totalSuppliers)); ?></div>
                    <small><i class="bi bi-truck"></i> Active</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales & Low Stock -->
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card" style="border-radius: 12px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-success"></i>Recent Sales</h6>
                    <a href="<?php echo e(route('admin.sale.invoices')); ?>" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><a href="<?php echo e(route('admin.sale.show', $sale->id)); ?>" class="fw-bold text-primary">#<?php echo e($sale->invoice_no); ?></a></td>
                                    <td><?php echo e(Str::limit($sale->customer->name ?? 'N/A', 20)); ?></td>
                                    <td class="fw-bold">₹<?php echo e(number_format($sale->net_amount, 2)); ?></td>
                                    <td class="text-muted"><?php echo e(\Carbon\Carbon::parse($sale->sale_date)->format('d M')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">No recent sales</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card" style="border-radius: 12px;">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Low Stock <span class="badge bg-danger ms-1"><?php echo e(count($lowStockItems)); ?></span></h6>
                </div>
                <div class="card-body py-2">
                    <?php $__empty_1 = true; $__currentLoopData = $lowStockItems->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="font-size: 0.85rem;">
                        <span class="text-truncate" style="max-width: 150px;"><?php echo e($item->name); ?></span>
                        <span class="badge bg-danger"><?php echo e($item->current_stock); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                        <p class="mb-0 mt-1 text-success small">All items stocked!</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 1rem;
    background: #f1f5f9;
    min-height: 100vh;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>