<?php $__env->startSection('title', 'Organization Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <!-- Organization Profile Card -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>Organization Profile
                    </h5>
                    <?php if(auth()->user()->is_organization_owner || auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('admin.organization.edit-profile')); ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <?php if($organization->logo_path): ?>
                                <img src="<?php echo e(Storage::url($organization->logo_path)); ?>" 
                                     alt="Logo" class="img-fluid rounded" style="max-height: 120px;">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center mx-auto"
                                     style="width: 100px; height: 100px; font-size: 2.5rem;">
                                    <?php echo e(strtoupper(substr($organization->name, 0, 2))); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h4 class="mb-1"><?php echo e($organization->name); ?></h4>
                            <p class="text-muted mb-2">
                                <i class="bi bi-hash me-1"></i><?php echo e($organization->code); ?>

                            </p>
                            <div class="row g-2">
                                <?php if($organization->email): ?>
                                <div class="col-md-6">
                                    <small class="text-muted">Email</small>
                                    <div><?php echo e($organization->email); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if($organization->phone): ?>
                                <div class="col-md-6">
                                    <small class="text-muted">Phone</small>
                                    <div><?php echo e($organization->phone); ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <?php if($organization->address): ?>
                        <div class="col-12">
                            <small class="text-muted">Address</small>
                            <div>
                                <?php echo e($organization->address); ?>

                                <?php if($organization->city || $organization->state || $organization->pin_code): ?>
                                <br><?php echo e(collect([$organization->city, $organization->state, $organization->pin_code])->filter()->join(', ')); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($organization->gst_no): ?>
                        <div class="col-md-4">
                            <small class="text-muted">GST Number</small>
                            <div class="fw-bold"><?php echo e($organization->gst_no); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($organization->pan_no): ?>
                        <div class="col-md-4">
                            <small class="text-muted">PAN Number</small>
                            <div class="fw-bold"><?php echo e($organization->pan_no); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($organization->dl_no): ?>
                        <div class="col-md-4">
                            <small class="text-muted">Drug License</small>
                            <div class="fw-bold"><?php echo e($organization->dl_no); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Usage Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h3 mb-0 text-primary"><?php echo e($stats['users_count']); ?></div>
                                <small class="text-muted">Users</small>
                                <?php if($license): ?>
                                <div class="progress mt-2" style="height: 5px;">
                                    <?php $userPct = min(100, ($stats['users_count'] / $license->max_users) * 100); ?>
                                    <div class="progress-bar <?php echo e($userPct > 90 ? 'bg-danger' : 'bg-primary'); ?>" 
                                         style="width: <?php echo e($userPct); ?>%"></div>
                                </div>
                                <small class="text-muted">of <?php echo e($license->max_users); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h3 mb-0 text-success"><?php echo e(number_format($stats['customers_count'])); ?></div>
                                <small class="text-muted">Customers</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h3 mb-0 text-info"><?php echo e(number_format($stats['items_count'])); ?></div>
                                <small class="text-muted">Items</small>
                                <?php if($license): ?>
                                <div class="progress mt-2" style="height: 5px;">
                                    <?php $itemPct = min(100, ($stats['items_count'] / $license->max_items) * 100); ?>
                                    <div class="progress-bar <?php echo e($itemPct > 90 ? 'bg-danger' : 'bg-info'); ?>" 
                                         style="width: <?php echo e($itemPct); ?>%"></div>
                                </div>
                                <small class="text-muted">of <?php echo e(number_format($license->max_items)); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h3 mb-0 text-warning"><?php echo e(number_format($stats['suppliers_count'])); ?></div>
                                <small class="text-muted">Suppliers</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- License Status -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-key me-2"></i>License</h5>
                    <a href="<?php echo e(route('admin.organization.license')); ?>" class="btn btn-sm btn-outline-primary">
                        Details
                    </a>
                </div>
                <div class="card-body">
                    <?php if($license): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-<?php echo e($license->isValid() ? 'success' : 'danger'); ?> fs-6">
                                <?php echo e($license->isValid() ? 'Active' : ($license->isExpired() ? 'Expired' : 'Inactive')); ?>

                            </span>
                            <span class="badge bg-secondary text-capitalize"><?php echo e($license->plan_type); ?></span>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">Expires</small>
                            <div class="<?php echo e($license->isExpiringSoon() ? 'text-warning fw-bold' : ''); ?>">
                                <?php echo e($license->expires_at?->format('d M Y') ?? 'N/A'); ?>

                                <?php if($license->isExpiringSoon()): ?>
                                    <br><small class="text-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <?php echo e($license->daysUntilExpiry()); ?> days remaining
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="bi bi-key text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">No active license</p>
                            <a href="<?php echo e(route('license.required')); ?>" class="btn btn-primary btn-sm mt-2">
                                Activate License
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Settings</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo e(route('admin.organization.users')); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i>Manage Users
                        <span class="badge bg-primary float-end"><?php echo e($stats['users_count']); ?></span>
                    </a>
                    <a href="<?php echo e(route('admin.organization.license')); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-key me-2"></i>License Details
                    </a>
                    <?php if(auth()->user()->is_organization_owner || auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('admin.organization.edit-profile')); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-pencil me-2"></i>Edit Profile
                    </a>
                    <a href="<?php echo e(route('admin.organization.branding')); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-palette me-2"></i>White-Label Branding
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.audit-logs.index')); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-journal-text me-2"></i>Audit Logs
                    </a>
                </div>
            </div>

            <!-- Organization Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Info</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Created</span>
                        <span><?php echo e($organization->created_at->format('d M Y')); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status</span>
                        <span class="badge bg-<?php echo e($organization->status === 'active' ? 'success' : 'warning'); ?>">
                            <?php echo e(ucfirst($organization->status)); ?>

                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Timezone</span>
                        <span><?php echo e($organization->timezone ?? 'Asia/Kolkata'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bill-software\resources\views/admin/organization/settings.blade.php ENDPATH**/ ?>